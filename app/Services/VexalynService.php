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

    public function __construct()
    {
        $this->apiUrl      = config('vexalyn.api_url',      'https://api.vexalyn.dev/v1');
        $this->apiKey      = config('vexalyn.api_key',      '');
        $this->projectId   = config('vexalyn.project_id',   'icb-ct-absensi-guru');
        $this->projectName = config('vexalyn.project_name', 'ICB CT - Absensi Guru');
        $this->webhookSecret = config('vexalyn.webhook_secret', '');
    }

    /**
     * Kirim tiket ke Vexalyn Dev Center
     */
    public function sendTicket(SupportTicket $ticket): array
    {
        $timestamp = now()->toIso8601String();
        $payload   = $this->buildPayload($ticket, $timestamp);
        $signature = $this->generateSignature($payload, $timestamp);

        try {
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

    /**
     * Ambil riwayat tiket user dari Vexalyn
     */
    public function getTickets(string $userId, int $page = 1): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization'     => 'Bearer ' . $this->apiKey,
                'X-Vexalyn-Project' => $this->projectId,
            ])
            ->timeout(10)
            ->get($this->apiUrl . '/tickets', [
                'project_id' => $this->projectId,
                'reporter_id' => $userId,
                'page'       => $page,
                'per_page'   => 20,
            ]);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }
        } catch (\Exception $e) {
            Log::warning('Vexalyn getTickets failed: ' . $e->getMessage());
        }

        return ['success' => false, 'data' => []];
    }

    /**
     * Ambil detail satu tiket
     */
    public function getTicket(string $ticketId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization'     => 'Bearer ' . $this->apiKey,
                'X-Vexalyn-Project' => $this->projectId,
            ])
            ->timeout(10)
            ->get($this->apiUrl . '/tickets/' . $ticketId);

            if ($response->successful()) {
                return ['success' => true, 'data' => $response->json()];
            }
        } catch (\Exception $e) {
            Log::warning('Vexalyn getTicket failed: ' . $e->getMessage());
        }

        return ['success' => false, 'data' => null];
    }

    private function buildPayload(SupportTicket $ticket, string $timestamp): array
    {
        $user = $ticket->user;
        return [
            'project_id'   => $this->projectId,
            'project_name' => $this->projectName,
            'type'         => $ticket->type,
            'title'        => $ticket->title,
            'description'  => $ticket->description,
            'priority'     => $ticket->priority,
            'category'     => $ticket->category,
            'extra_fields' => $ticket->extra_fields ?? [],
            'metadata'     => $ticket->metadata ?? [],
            'attachments'  => $ticket->attachments ?? [],
            'reporter'     => $user ? [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role ?? 'guru',
            ] : null,
            'timestamp'    => $timestamp,
        ];
    }

    private function generateSignature(array $payload, string $timestamp): string
    {
        $body = json_encode($payload);
        return hash_hmac('sha256', $timestamp . '.' . $body, $this->webhookSecret);
    }
}
