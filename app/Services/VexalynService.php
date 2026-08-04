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
        $this->apiUrl      = config('vexalyn.api_url',      'https://api.vexalyn.dev/v1');
        $this->apiKey      = config('vexalyn.api_key',      '');
        $this->projectId   = config('vexalyn.project_id',   'icb-ct-absensi-guru');
        $this->projectName = config('vexalyn.project_name', 'ICB CT - Absensi Guru');
        $this->webhookSecret = config('vexalyn.webhook_secret', '');
        
        // Konfigurasi GitHub
        $this->githubToken = env('GITHUB_TOKEN', '');
        $this->githubRepo  = env('GITHUB_REPO', 'vexalyn-dev/presensi-guru-icbct');
    }

    /**
     * Kirim tiket ke Vexalyn Dev Center & Otomatis ke GitHub Issues
     */
    public function sendTicket(SupportTicket $ticket): array
    {
        $timestamp = now()->toIso8601String();
        $payload   = $this->buildPayload($ticket, $timestamp);
        $signature = $this->generateSignature($payload, $timestamp);

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

            // 2. KIRIM OTOMATIS KE GITHUB ISSUES (PROJECTS)
            $this->sendToGitHub($ticket);

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

    /**
     * Kirim data laporan ke GitHub Issues agar masuk GitHub Projects
     */
    private function sendToGitHub(SupportTicket $ticket): void
    {
        if (empty($this->githubToken) || empty($this->githubRepo)) {
            return; // Lewati jika token belum diset
        }

        try {
            $meta = $ticket->metadata ?? [];
            $body = "### Deskripsi Masalah\n" . $ticket->description . "\n\n" .
                    "---\n" .
                    "**Info Sistem Pengguna:**\n" .
                    "- **OS:** " . ($meta['os'] ?? 'Unknown') . "\n" .
                    "- **Browser:** " . ($meta['browser'] ?? 'Unknown') . "\n" .
                    "- **Device:** " . ($meta['device'] ?? 'Unknown') . "\n" .
                    "- **Kategori:** " . ($ticket->category ?? 'Umum') . "\n" .
                    "- **IP Address:** " . ($meta['ip_address'] ?? 'Unknown');

            Http::withToken($this->githubToken)
                ->withHeaders([
                    'Accept' => 'application/vnd.github+json',
                    'X-GitHub-Api-Version' => '2022-11-28'
                ])
                ->timeout(10)
                ->post("https://api.github.com/repos/{$this->githubRepo}/issues", [
                    'title'  => '[' . strtoupper($ticket->type) . '] ' . $ticket->title,
                    'body'   => $body,
                    'labels' => [$ticket->type, $ticket->priority ?? 'medium']
                ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengirim issue ke GitHub: ' . $e->getMessage());
        }
    }

    // (Fungsi getTickets, getTicket, buildPayload, generateSignature tetap sama seperti sebelumnya...)
    
    public function getTickets(string $userId, int $page = 1): array { /* ... */ return ['success' => false, 'data' => []]; }
    public function getTicket(string $ticketId): array { /* ... */ return ['success' => false, 'data' => null]; }
    private function buildPayload(SupportTicket $ticket, string $timestamp): array { /* ... */ return []; }
    private function generateSignature(array $payload, string $timestamp): string { /* ... */ return ''; }
}