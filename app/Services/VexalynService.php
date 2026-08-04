<?php

namespace App\Services;

use App\Models\SupportTicket;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VexalynService
{
    private string $apiUrl;
    private string $apiKey;
    private string $projectId;
    private string $projectName;
    private string $webhookSecret;
    private string $githubToken;
    private string $githubRepo;

    public function __construct()
    {
        $this->apiUrl        = config('vexalyn.api_url',      'https://api.vexalyn.dev/v1');
        $this->apiKey        = config('vexalyn.api_key',      '');
        $this->projectId     = config('vexalyn.project_id',   'icb-ct-absensi-guru');
        $this->projectName   = config('vexalyn.project_name', 'ICB CT - Absensi Guru');
        $this->webhookSecret = config('vexalyn.webhook_secret', '');

        // Baca token & repo dari .env langsung (bukan config file)
        $this->githubToken = env('GITHUB_TOKEN', '');
        $this->githubRepo  = env('GITHUB_REPO',  'vexalyn-dev/presensi-guru-icbct');
    }

    // -------------------------------------------------------------------------
    // PUBLIC: Kirim tiket ke Vexalyn + GitHub Issues
    // -------------------------------------------------------------------------

    /**
     * Kirim tiket ke Vexalyn Dev Center & otomatis buat GitHub Issue.
     */
    public function sendTicket(SupportTicket $ticket): array
    {
        $timestamp = now()->toIso8601String();
        $payload   = $this->buildPayload($ticket, $timestamp);
        $signature = $this->generateSignature($payload, $timestamp);

        // 2. Buat GitHub Issue (terlepas dari hasil Vexalyn)
        $this->sendToGitHub($ticket);

        try {
            // 1. Kirim ke API Vexalyn utama
            $response = Http::withHeaders([
                'Authorization'     => 'Bearer ' . $this->apiKey,
                'X-Vexalyn-Project' => $this->projectId,
                'X-Timestamp'       => $timestamp,
                'X-Signature'       => $signature,
                'Content-Type'      => 'application/json',
                'Accept'            => 'application/json',
            ])
            ->timeout(15)
            ->post($this->apiUrl . '/tickets', $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success'   => true,
                    'ticket_id' => $data['ticket_id'] ?? $data['id'] ?? null,
                    'data'      => $data,
                ];
            }

            Log::warning('Vexalyn API error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'API Error: ' . $response->status(),
                'body'    => $response->body(),
            ];

        } catch (\Exception $e) {
            Log::error('Vexalyn Service exception: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    // -------------------------------------------------------------------------
    // PRIVATE: GitHub Issues
    // -------------------------------------------------------------------------

    /**
     * Buat GitHub Issue dari laporan support.
     */
    private function sendToGitHub(SupportTicket $ticket): void
    {
        if (empty($this->githubToken) || empty($this->githubRepo)) {
            Log::warning('GitHub Issue dilewati: GITHUB_TOKEN atau GITHUB_REPO belum diset di .env');
            return;
        }

        try {
            $meta = is_array($ticket->metadata) ? $ticket->metadata : [];

            // Upload lampiran ke GitHub — jika gagal, issue tetap dibuat tanpa lampiran
            $attachmentLinks = [];
            try {
                $attachmentLinks = $this->uploadAttachmentsToGitHub($ticket);
            } catch (\Exception $uploadEx) {
                Log::warning('Upload attachment ke GitHub gagal, issue tetap dibuat tanpa lampiran: ' . $uploadEx->getMessage(), [
                    'ticket_id' => $ticket->id,
                ]);
            }

            // Susun body issue dalam format Markdown
            $body = $this->buildGitHubBody($ticket, $meta, $attachmentLinks);

            // Label: tipe tiket + prioritas
            $labels = array_values(array_filter([
                $ticket->type,
                $ticket->priority ?? 'medium',
            ]));

            $issueTitle = '[' . strtoupper($ticket->type) . '] ' . $ticket->title;

            $response = Http::withHeaders([
                'Authorization'        => 'Bearer ' . $this->githubToken,
                'Accept'               => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
                'Content-Type'         => 'application/json',
            ])
            ->timeout(15)
            ->post("https://api.github.com/repos/{$this->githubRepo}/issues", [
                'title'  => $issueTitle,
                'body'   => $body,
                'labels' => $labels,
            ]);

            if ($response->successful()) {
                $issueUrl = $response->json('html_url') ?? '(URL tidak tersedia)';
                Log::info("GitHub Issue berhasil dibuat: {$issueUrl} — Tiket #{$ticket->id}");
            } else {
                Log::error('GitHub API Error: ' . $response->body(), [
                    'status'    => $response->status(),
                    'ticket_id' => $ticket->id,
                    'repo'      => $this->githubRepo,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Gagal mengirim issue ke GitHub: ' . $e->getMessage(), [
                'ticket_id' => $ticket->id,
            ]);
        }
    }

    /**
     * Upload file lampiran ke GitHub repo (Contents API) dan kembalikan
     * array of ['name' => ..., 'url' => ..., 'is_image' => bool].
     */
    private function uploadAttachmentsToGitHub(SupportTicket $ticket): array
    {
        $attachments = is_array($ticket->attachments) ? $ticket->attachments : [];
        if (empty($attachments)) return [];

        $results = [];

        foreach ($attachments as $file) {
            try {
                $fileUrl  = $file['url']  ?? null;
                $fileName = $file['name'] ?? basename((string) $fileUrl);
                $mimeType = $file['type'] ?? '';

                if (!$fileUrl) continue;

                $fileContent = $this->readAttachmentContent($fileUrl);

                if (empty($fileContent)) {
                    Log::warning("GitHub upload: tidak bisa membaca konten file '{$fileName}'", [
                        'url' => $fileUrl, 'ticket_id' => $ticket->id,
                    ]);
                    continue;
                }

                $base64Content = base64_encode($fileContent);
                $isImage       = str_starts_with($mimeType, 'image/')
                              || (bool) preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $fileName);

                // Path di repo: support-attachments/ticket-{id}/{timestamp}_{filename}
                $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
                $repoPath = 'support-attachments/ticket-' . $ticket->id . '/'
                          . now()->format('YmdHis') . '_' . $safeName;

                $uploadResponse = Http::withHeaders([
                    'Authorization'        => 'Bearer ' . $this->githubToken,
                    'Accept'               => 'application/vnd.github+json',
                    'X-GitHub-Api-Version' => '2022-11-28',
                    'Content-Type'         => 'application/json',
                ])
                ->timeout(30)
                ->put("https://api.github.com/repos/{$this->githubRepo}/contents/{$repoPath}", [
                    'message' => "chore: upload attachment for support ticket #{$ticket->id}",
                    'content' => $base64Content,
                ]);

                if ($uploadResponse->successful()) {
                    $rawUrl = $uploadResponse->json('content.download_url')
                           ?? "https://raw.githubusercontent.com/{$this->githubRepo}/main/{$repoPath}";

                    $results[] = [
                        'name'     => $fileName,
                        'url'      => $rawUrl,
                        'is_image' => $isImage,
                    ];
                    Log::info("GitHub attachment uploaded: {$rawUrl}");
                } else {
                    Log::warning('GitHub attachment upload gagal: ' . $uploadResponse->body(), [
                        'status' => $uploadResponse->status(), 'file' => $fileName, 'ticket_id' => $ticket->id,
                    ]);
                }

            } catch (\Exception $e) {
                Log::warning('Exception upload attachment: ' . $e->getMessage(), [
                    'file' => $file['name'] ?? '?', 'ticket_id' => $ticket->id,
                ]);
            }
        }

        return $results;
    }

    /**
     * Baca konten file attachment — coba dari disk storage dulu, fallback ke HTTP fetch.
     */
    private function readAttachmentContent(string $fileUrl): string
    {
        // Coba baca dari storage disk public langsung (lebih cepat, tidak butuh HTTP)
        try {
            $publicDiskUrl = rtrim(\Storage::disk('public')->url(''), '/');
            if (str_starts_with($fileUrl, $publicDiskUrl)) {
                $relativePath = ltrim(substr($fileUrl, strlen($publicDiskUrl)), '/');
                if (\Storage::disk('public')->exists($relativePath)) {
                    return \Storage::disk('public')->get($relativePath);
                }
            }

            // Fallback: coba path relatif dari APP_URL
            $appUrl = rtrim(config('app.url', ''), '/');
            $storagePubUrl = $appUrl . '/storage/';
            if (str_starts_with($fileUrl, $storagePubUrl)) {
                $relativePath = ltrim(substr($fileUrl, strlen($storagePubUrl)), '/');
                if (\Storage::disk('public')->exists($relativePath)) {
                    return \Storage::disk('public')->get($relativePath);
                }
            }
        } catch (\Exception $e) {
            Log::debug('readAttachmentContent disk read failed: ' . $e->getMessage());
        }

        // Last resort: fetch via HTTP
        return $this->fetchFileContent($fileUrl);
    }

    /**
     * Susun konten Markdown untuk body GitHub Issue.
     *
     * @param array $attachmentLinks  Output dari uploadAttachmentsToGitHub()
     */
    private function buildGitHubBody(SupportTicket $ticket, array $meta, array $attachmentLinks = []): string
    {
        $extra = is_array($ticket->extra_fields) ? $ticket->extra_fields : [];

        $lines = [];

        $lines[] = '## Deskripsi Masalah';
        $lines[] = $ticket->description;
        $lines[] = '';

        // Informasi sistem pengguna
        $lines[] = '---';
        $lines[] = '## Informasi Sistem Pengguna';
        $lines[] = '| Field | Value |';
        $lines[] = '|-------|-------|';
        $lines[] = '| **OS** | ' . ($meta['os'] ?? 'Unknown') . ' |';
        $lines[] = '| **Browser** | ' . ($meta['browser'] ?? 'Unknown') . ' |';
        $lines[] = '| **Device** | ' . ($meta['device'] ?? 'Unknown') . ' |';
        $lines[] = '| **Resolusi** | ' . ($meta['resolution'] ?? 'Unknown') . ' |';
        $lines[] = '| **IP Address** | ' . ($meta['ip_address'] ?? 'Unknown') . ' |';
        $lines[] = '| **URL** | ' . ($meta['url'] ?? 'Unknown') . ' |';
        $lines[] = '| **Kategori** | ' . ($ticket->category ?? 'Umum') . ' |';
        $lines[] = '| **Prioritas** | ' . ($ticket->priority ?? 'medium') . ' |';
        $lines[] = '| **Dilaporkan pada** | ' . ($meta['submitted_at'] ?? now()->toIso8601String()) . ' |';
        $lines[] = '';

        // Extra fields khusus per tipe
        if ($ticket->type === 'bug') {
            $lines[] = '---';
            $lines[] = '## Detail Bug';

            if (!empty($extra['steps_to_reproduce'])) {
                $lines[] = '### Langkah Reproduksi';
                $lines[] = $extra['steps_to_reproduce'];
            }
            if (!empty($extra['expected_result'])) {
                $lines[] = '### Hasil yang Diharapkan';
                $lines[] = $extra['expected_result'];
            }
            if (!empty($extra['actual_result'])) {
                $lines[] = '### Hasil Aktual';
                $lines[] = $extra['actual_result'];
            }
            if (!empty($extra['affected_module'])) {
                $lines[] = '**Modul Terdampak:** ' . $extra['affected_module'];
            }
            if (!empty($extra['impact_level'])) {
                $lines[] = '**Tingkat Dampak:** ' . $extra['impact_level'];
            }
            if (!empty($extra['extra_notes'])) {
                $lines[] = '**Catatan Tambahan:** ' . $extra['extra_notes'];
            }
        }

        if ($ticket->type === 'feature') {
            $lines[] = '---';
            $lines[] = '## Detail Permintaan Fitur';
            if (!empty($extra['purpose'])) {
                $lines[] = '**Tujuan:** ' . $extra['purpose'];
            }
            if (!empty($extra['benefit'])) {
                $lines[] = '**Manfaat:** ' . $extra['benefit'];
            }
        }

        if ($ticket->type === 'maintenance') {
            $lines[] = '---';
            $lines[] = '## Detail Maintenance';
            if (!empty($extra['maintenance_type'])) {
                $lines[] = '**Jenis Maintenance:** ' . $extra['maintenance_type'];
            }
            if (!empty($extra['preferred_schedule'])) {
                $lines[] = '**Jadwal Diinginkan:** ' . $extra['preferred_schedule'];
            }
        }

        $lines[] = '';
        $lines[] = '---';

        // Section lampiran
        if (!empty($attachmentLinks)) {
            $lines[] = '## Lampiran';
            foreach ($attachmentLinks as $att) {
                if ($att['is_image']) {
                    // Embed gambar langsung di Markdown
                    $lines[] = '**' . $att['name'] . '**';
                    $lines[] = '![](' . $att['url'] . ')';
                } else {
                    // Link download untuk non-gambar
                    $lines[] = '📎 [' . $att['name'] . '](' . $att['url'] . ')';
                }
                $lines[] = '';
            }
            $lines[] = '---';
        }

        $lines[] = '*Issue ini dibuat otomatis dari Pusat Bantuan ICB CT Absensi Guru.*';

        return implode("\n", $lines);
    }

    // -------------------------------------------------------------------------
    // PRIVATE: Vexalyn helpers
    // -------------------------------------------------------------------------

    private function buildPayload(SupportTicket $ticket, string $timestamp): array
    {
        $meta  = is_array($ticket->metadata)    ? $ticket->metadata    : [];
        $extra = is_array($ticket->extra_fields) ? $ticket->extra_fields : [];

        return [
            'project_id'   => $this->projectId,
            'project_name' => $this->projectName,
            'timestamp'    => $timestamp,
            'ticket'       => [
                'id'          => $ticket->id,
                'type'        => $ticket->type,
                'title'       => $ticket->title,
                'description' => $ticket->description,
                'category'    => $ticket->category,
                'priority'    => $ticket->priority,
                'status'      => $ticket->status,
                'extra'       => $extra,
                'metadata'    => $meta,
                'attachments' => $ticket->attachments ?? [],
                'created_at'  => $ticket->created_at?->toIso8601String(),
            ],
            'reporter'     => [
                'id'    => $ticket->user?->id,
                'name'  => $ticket->user?->name,
                'email' => $ticket->user?->email,
                'role'  => $ticket->user?->role,
            ],
        ];
    }

    private function generateSignature(array $payload, string $timestamp): string
    {
        if (empty($this->webhookSecret)) {
            return '';
        }

        $raw = $timestamp . '.' . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return hash_hmac('sha256', $raw, $this->webhookSecret);
    }

    // -------------------------------------------------------------------------
    // PUBLIC: Query tiket dari Vexalyn
    // -------------------------------------------------------------------------

    public function getTickets(string $userId, int $page = 1): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization'     => 'Bearer ' . $this->apiKey,
                'X-Vexalyn-Project' => $this->projectId,
                'Accept'            => 'application/json',
            ])
            ->timeout(10)
            ->get($this->apiUrl . '/tickets', [
                'user_id' => $userId,
                'page'    => $page,
            ]);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            return ['success' => false, 'data' => []];

        } catch (\Exception $e) {
            Log::error('Vexalyn getTickets exception: ' . $e->getMessage());
            return ['success' => false, 'data' => []];
        }
    }

    public function getTicket(string $ticketId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization'     => 'Bearer ' . $this->apiKey,
                'X-Vexalyn-Project' => $this->projectId,
                'Accept'            => 'application/json',
            ])
            ->timeout(10)
            ->get($this->apiUrl . '/tickets/' . $ticketId);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }

            return ['success' => false, 'data' => null];

        } catch (\Exception $e) {
            Log::error('Vexalyn getTicket exception: ' . $e->getMessage());
            return ['success' => false, 'data' => null];
        }
    }
}
