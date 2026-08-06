<?php

namespace App\Services;

use App\Models\SupportTicket;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GitHubService
{
    private string $token;
    private string $repo;

    private array $headers;

    public function __construct()
    {
        $this->token = env('GITHUB_TOKEN', '');
        $this->repo  = env('GITHUB_REPO',  'vexalyn-dev/presensi-guru-icbct');

        $this->headers = [
            'Authorization'        => 'Bearer ' . $this->token,
            'Accept'               => 'application/vnd.github+json',
            'X-GitHub-Api-Version' => '2022-11-28',
            'Content-Type'         => 'application/json',
        ];
    }

    // -------------------------------------------------------------------------
    // PUBLIC
    // -------------------------------------------------------------------------

    /**
     * Buat GitHub Issue dari tiket support.
     * Return ['success' => bool, 'issue_url' => string|null, 'issue_number' => int|null]
     */
    public function createIssue(SupportTicket $ticket): array
    {
        if (empty($this->token) || empty($this->repo)) {
            Log::warning('GitHubService: GITHUB_TOKEN atau GITHUB_REPO belum diset di .env');
            return ['success' => false, 'issue_url' => null, 'issue_number' => null];
        }

        try {
            $meta  = is_array($ticket->metadata)    ? $ticket->metadata    : [];

            // Upload lampiran dulu — tidak blokir pembuatan issue kalau gagal
            $attachmentLinks = [];
            try {
                $attachmentLinks = $this->uploadAttachments($ticket);
            } catch (\Exception $e) {
                Log::warning('GitHubService: upload attachment gagal, issue tetap dibuat. ' . $e->getMessage());
            }

            $body   = $this->buildBody($ticket, $meta, $attachmentLinks);
            $labels = array_values(array_filter([$ticket->type, $ticket->priority ?? 'medium']));
            $title  = '[' . strtoupper($ticket->type) . '] ' . $ticket->title;

            $response = Http::withHeaders($this->headers)
                ->timeout(15)
                ->post("https://api.github.com/repos/{$this->repo}/issues", [
                    'title'  => $title,
                    'body'   => $body,
                    'labels' => $labels,
                ]);

            if ($response->successful()) {
                $issueUrl    = $response->json('html_url');
                $issueNumber = $response->json('number');
                Log::info("GitHub Issue #{$issueNumber} dibuat: {$issueUrl}");
                return ['success' => true, 'issue_url' => $issueUrl, 'issue_number' => $issueNumber];
            }

            Log::error('GitHub API Error: ' . $response->body(), [
                'status'    => $response->status(),
                'ticket_id' => $ticket->id,
            ]);
            return ['success' => false, 'issue_url' => null, 'issue_number' => null];

        } catch (\Exception $e) {
            Log::error('GitHubService::createIssue exception: ' . $e->getMessage(), ['ticket_id' => $ticket->id]);
            return ['success' => false, 'issue_url' => null, 'issue_number' => null];
        }
    }

    // -------------------------------------------------------------------------
    // PRIVATE — attachment upload
    // -------------------------------------------------------------------------

    private function uploadAttachments(SupportTicket $ticket): array
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

                $content = $this->readFile($fileUrl);
                if (empty($content)) {
                    Log::warning("GitHubService: tidak bisa baca file '{$fileName}'");
                    continue;
                }

                $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $fileName);
                $repoPath = 'support-attachments/ticket-' . $ticket->id . '/'
                          . now()->format('YmdHis') . '_' . $safeName;

                $resp = Http::withHeaders($this->headers)
                    ->timeout(30)
                    ->put("https://api.github.com/repos/{$this->repo}/contents/{$repoPath}", [
                        'message' => "chore: attachment untuk tiket #{$ticket->id}",
                        'content' => base64_encode($content),
                    ]);

                if ($resp->successful()) {
                    $rawUrl = $resp->json('content.download_url')
                           ?? "https://raw.githubusercontent.com/{$this->repo}/main/{$repoPath}";

                    $isImage = str_starts_with($mimeType, 'image/')
                            || (bool) preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $fileName);

                    $results[] = ['name' => $fileName, 'url' => $rawUrl, 'is_image' => $isImage];
                    Log::info("GitHub attachment uploaded: {$rawUrl}");
                } else {
                    Log::warning('GitHub attachment gagal: ' . $resp->body(), [
                        'file' => $fileName, 'status' => $resp->status(),
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('GitHubService attachment exception: ' . $e->getMessage(), [
                    'file' => $file['name'] ?? '?',
                ]);
            }
        }

        return $results;
    }

    /**
     * Baca konten file: coba dari storage dulu, fallback HTTP.
     */
    private function readFile(string $url): string
    {
        // Coba strip ke path relatif storage public
        try {
            $publicBase = rtrim(Storage::disk('public')->url(''), '/');
            if (str_starts_with($url, $publicBase)) {
                $rel = ltrim(substr($url, strlen($publicBase)), '/');
                if (Storage::disk('public')->exists($rel)) {
                    return Storage::disk('public')->get($rel);
                }
            }

            $appStorage = rtrim(config('app.url', ''), '/') . '/storage/';
            if (str_starts_with($url, $appStorage)) {
                $rel = ltrim(substr($url, strlen($appStorage)), '/');
                if (Storage::disk('public')->exists($rel)) {
                    return Storage::disk('public')->get($rel);
                }
            }
        } catch (\Exception $e) {
            // lanjut ke HTTP
        }

        // HTTP fallback
        try {
            $resp = Http::withoutVerifying()->timeout(10)->get($url);
            return $resp->successful() ? $resp->body() : '';
        } catch (\Exception $e) {
            return '';
        }
    }

    // -------------------------------------------------------------------------
    // PRIVATE — body builder
    // -------------------------------------------------------------------------

    private function buildBody(SupportTicket $ticket, array $meta, array $attachments = []): string
    {
        $extra = is_array($ticket->extra_fields) ? $ticket->extra_fields : [];
        $lines = [];

        $lines[] = '## Deskripsi';
        $lines[] = $ticket->description;
        $lines[] = '';
        $lines[] = '---';
        $lines[] = '## Informasi Sistem';
        $lines[] = '| Field | Value |';
        $lines[] = '|---|---|';
        $lines[] = '| **OS** | ' . ($meta['os'] ?? 'Unknown') . ' |';
        $lines[] = '| **Browser** | ' . ($meta['browser'] ?? 'Unknown') . ' |';
        $lines[] = '| **Device** | ' . ($meta['device'] ?? 'Unknown') . ' |';
        $lines[] = '| **Resolusi** | ' . ($meta['resolution'] ?? 'Unknown') . ' |';
        $lines[] = '| **IP Address** | ' . ($meta['ip_address'] ?? 'Unknown') . ' |';
        $lines[] = '| **URL** | ' . ($meta['url'] ?? 'Unknown') . ' |';
        $lines[] = '| **Kategori** | ' . ($ticket->category ?? 'Umum') . ' |';
        $lines[] = '| **Prioritas** | ' . ($ticket->priority ?? 'medium') . ' |';
        $lines[] = '| **Dilaporkan** | ' . ($meta['submitted_at'] ?? now()->toIso8601String()) . ' |';
        $lines[] = '| **Reporter** | ' . ($ticket->user?->name ?? 'Unknown') . ' |';
        $lines[] = '';

        if ($ticket->type === 'bug') {
            $lines[] = '---';
            $lines[] = '## Detail Bug';
            if (!empty($extra['steps_to_reproduce'])) { $lines[] = '### Langkah Reproduksi'; $lines[] = $extra['steps_to_reproduce']; }
            if (!empty($extra['expected_result']))    { $lines[] = '### Hasil Diharapkan';   $lines[] = $extra['expected_result']; }
            if (!empty($extra['actual_result']))      { $lines[] = '### Hasil Aktual';        $lines[] = $extra['actual_result']; }
            if (!empty($extra['affected_module']))    { $lines[] = '**Modul:** ' . $extra['affected_module']; }
            if (!empty($extra['impact_level']))       { $lines[] = '**Dampak:** ' . $extra['impact_level']; }
            if (!empty($extra['extra_notes']))        { $lines[] = '**Catatan:** ' . $extra['extra_notes']; }
            $lines[] = '';
        }

        if ($ticket->type === 'feature') {
            $lines[] = '---';
            $lines[] = '## Detail Fitur';
            if (!empty($extra['purpose'])) { $lines[] = '**Tujuan:** ' . $extra['purpose']; }
            if (!empty($extra['benefit'])) { $lines[] = '**Manfaat:** ' . $extra['benefit']; }
            $lines[] = '';
        }

        if ($ticket->type === 'maintenance') {
            $lines[] = '---';
            $lines[] = '## Detail Maintenance';
            if (!empty($extra['maintenance_type']))   { $lines[] = '**Jenis:** ' . $extra['maintenance_type']; }
            if (!empty($extra['preferred_schedule'])) { $lines[] = '**Jadwal:** ' . $extra['preferred_schedule']; }
            $lines[] = '';
        }

        // Lampiran
        if (!empty($attachments)) {
            $lines[] = '---';
            $lines[] = '## Lampiran';
            foreach ($attachments as $att) {
                if ($att['is_image']) {
                    $lines[] = '**' . $att['name'] . '**';
                    $lines[] = '![](' . $att['url'] . ')';
                } else {
                    $lines[] = '📎 [' . $att['name'] . '](' . $att['url'] . ')';
                }
                $lines[] = '';
            }
        }

        $lines[] = '---';
        $lines[] = '*Dibuat otomatis dari Pusat Bantuan ICB CT Absensi Guru.*';

        return implode("\n", $lines);
    }
}
