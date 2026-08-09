<?php

namespace App\Services;

use App\Models\SupportTicket;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClickUpService
{
    private string $apiToken;
    private string $listId;
    private string $baseUrl = 'https://api.clickup.com/api/v2';

    public function __construct()
    {
        $this->apiToken = config('services.clickup.api_token', '');
        $this->listId   = config('services.clickup.list_id', '');
    }

    /** Apakah ClickUp diaktifkan dan terkonfigurasi */
    public function isEnabled(): bool
    {
        return config('services.clickup.enabled', false)
            && !empty($this->apiToken)
            && !empty($this->listId);
    }

    /** Buat task baru di ClickUp dari SupportTicket */
    public function createTask(SupportTicket $ticket): array
    {
        if (!$this->isEnabled()) {
            return ['success' => false, 'message' => 'ClickUp tidak diaktifkan', 'task_url' => null];
        }

        try {
            $payload = $this->buildPayload($ticket);

            $response = Http::withHeaders([
                'Authorization' => $this->apiToken,
                'Content-Type'  => 'application/json',
            ])->timeout(15)->post("{$this->baseUrl}/list/{$this->listId}/task", $payload);

            if ($response->successful()) {
                $data    = $response->json();
                $taskUrl = $data['url'] ?? "https://app.clickup.com/t/{$data['id']}";

                Log::info('ClickUp task created', [
                    'ticket_id' => $ticket->id,
                    'task_id'   => $data['id'],
                    'task_url'  => $taskUrl,
                ]);

                return ['success' => true, 'task_url' => $taskUrl, 'task_id' => $data['id']];
            }

            Log::warning('ClickUp task creation failed', [
                'ticket_id' => $ticket->id,
                'status'    => $response->status(),
                'body'      => $response->body(),
            ]);

            return ['success' => false, 'task_url' => null, 'message' => $response->body()];

        } catch (\Throwable $e) {
            Log::error('ClickUpService exception', [
                'ticket_id' => $ticket->id,
                'error'     => $e->getMessage(),
            ]);
            return ['success' => false, 'task_url' => null, 'message' => $e->getMessage()];
        }
    }

    /** Bangun payload task ClickUp dari data tiket */
    private function buildPayload(SupportTicket $ticket): array
    {
        $user     = $ticket->user;
        $userName = $user?->name ?? 'Unknown';
        $userRole = $user?->role_name ?? '-';
        $meta     = $ticket->metadata ?? [];
        $extra    = $ticket->extra_fields ?? [];

        // Priority mapping
        $priority = match ($ticket->priority) {
            'critical' => 1, // urgent
            'high'     => 2, // high
            'medium'   => 3, // normal
            'low'      => 4, // low
            default    => 3,
        };

        // Status/tag mapping
        $typeLabel = match ($ticket->type) {
            'bug'         => '🐛 Bug Report',
            'feature'     => '✨ Feature Request',
            'maintenance' => '🔧 Maintenance',
            'question'    => '❓ Pertanyaan',
            default       => ucfirst($ticket->type),
        };

        // Description markdown
        $desc  = "## 📋 Detail Laporan\n\n";
        $desc .= "**Tipe:** {$typeLabel}\n";
        $desc .= "**Prioritas:** {$ticket->priority}\n";
        $desc .= "**Pengirim:** {$userName} ({$userRole})\n";
        $desc .= "**Email:** " . ($user?->email ?? '-') . "\n";
        $desc .= "**Dikirim:** " . $ticket->created_at->format('d M Y H:i') . " WIB\n\n";

        $desc .= "---\n\n## 📝 Deskripsi\n\n{$ticket->description}\n\n";

        // Extra fields per type
        if ($ticket->type === 'bug') {
            if (!empty($extra['steps_to_reproduce'])) {
                $desc .= "---\n\n## 🔁 Langkah Reproduksi\n\n{$extra['steps_to_reproduce']}\n\n";
            }
            if (!empty($extra['expected_result'])) {
                $desc .= "**Hasil yang diharapkan:** {$extra['expected_result']}\n\n";
            }
            if (!empty($extra['actual_result'])) {
                $desc .= "**Hasil aktual:** {$extra['actual_result']}\n\n";
            }
            if (!empty($extra['affected_module'])) {
                $desc .= "**Modul terdampak:** {$extra['affected_module']}\n\n";
            }
            if (!empty($extra['impact_level'])) {
                $desc .= "**Tingkat dampak:** {$extra['impact_level']}\n\n";
            }
            if (!empty($extra['extra_notes'])) {
                $desc .= "**Catatan tambahan:** {$extra['extra_notes']}\n\n";
            }
        }

        if ($ticket->type === 'feature') {
            if (!empty($extra['purpose']))  $desc .= "**Tujuan:** {$extra['purpose']}\n\n";
            if (!empty($extra['benefit']))  $desc .= "**Manfaat:** {$extra['benefit']}\n\n";
        }

        if ($ticket->type === 'maintenance') {
            if (!empty($extra['maintenance_type']))      $desc .= "**Jenis maintenance:** {$extra['maintenance_type']}\n\n";
            if (!empty($extra['preferred_schedule']))    $desc .= "**Jadwal diinginkan:** {$extra['preferred_schedule']}\n\n";
        }

        // System metadata
        $desc .= "---\n\n## 💻 Info Sistem\n\n";
        $desc .= "| Key | Value |\n|---|---|\n";
        foreach (['browser','os','device','resolution','timezone','ip_address'] as $key) {
            if (!empty($meta[$key])) {
                $desc .= "| " . ucfirst(str_replace('_',' ',$key)) . " | {$meta[$key]} |\n";
            }
        }

        // Attachments
        if (!empty($ticket->attachments)) {
            $desc .= "\n---\n\n## 📎 Lampiran\n\n";
            foreach ($ticket->attachments as $att) {
                $desc .= "- [{$att['name']}]({$att['url']})\n";
            }
        }

        $desc .= "\n---\n_Laporan ini dikirim otomatis dari **ICB CT Presensi Guru** · Ticket ID: #{$ticket->id}_";

        return [
            'name'        => "[{$typeLabel}] {$ticket->title}",
            'description' => $desc,
            'priority'    => $priority,
            'tags'        => [$ticket->type, $ticket->priority, 'icb-ct'],
            'due_date'    => null,
            'notify_all'  => true,
        ];
    }
}
