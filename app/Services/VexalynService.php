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

            // Susun body issue dalam format Markdown
            $body = $this->buildGitHubBody($ticket, $meta);

            // Label: tipe tiket + prioritas
            $labels = array_filter([
                $ticket->type,
                $ticket->priority ?? 'medium',
            ]);

            $issueTitle = '[' . strtoupper($ticket->type) . '] ' . $ticket->title;

            $response = Http::withHeaders([
                'Authorization'      => 'Bearer ' . $this->githubToken,
                'Accept'             => 'application/vnd.github+json',
                'X-GitHub-Api-Version' => '2022-11-28',
                'Content-Type'       => 'application/json',
            ])
            ->timeout(15)
            ->post("https://api.github.com/repos/{$this->githubRepo}/issues", [
                'title'  => $issueTitle,
                'body'   => $body,
                'labels' => array_values($labels),
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
     * Susun konten Markdown untuk body GitHub Issue.
     */
    private function buildGitHubBody(SupportTicket $ticket, array $meta): string
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
