<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GitHubWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Verifikasi signature GitHub
        $secret    = env('GITHUB_WEBHOOK_SECRET', '');
        $signature = $request->header('X-Hub-Signature-256', '');

        if ($secret && $signature) {
            $expected = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);
            if (!hash_equals($expected, $signature)) {
                Log::warning('GitHub Webhook: signature tidak valid');
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        }

        $event   = $request->header('X-GitHub-Event', '');
        $payload = $request->json()->all();

        Log::info("GitHub Webhook received: event={$event}", [
            'action' => $payload['action'] ?? null,
        ]);

        // Hanya proses event "issues"
        if ($event === 'issues') {
            $this->handleIssueEvent($payload);
        }

        return response()->json(['ok' => true]);
    }

    private function handleIssueEvent(array $payload): void
    {
        $action = $payload['action'] ?? null;
        $issue  = $payload['issue'] ?? [];
        $title  = $issue['title'] ?? 'Unknown';
        $url    = $issue['html_url'] ?? null;
        $number = $issue['number'] ?? null;

        // Map action GitHub ke pesan yang readable
        $actionMap = [
            'opened'      => ['label' => 'Laporan Baru Dibuka',      'icon' => 'git-pull-request', 'color' => 'bg-blue-100 text-blue-600'],
            'closed'      => ['label' => 'Laporan Ditutup / Selesai', 'icon' => 'check-circle-2',   'color' => 'bg-green-100 text-green-600'],
            'reopened'    => ['label' => 'Laporan Dibuka Kembali',    'icon' => 'rotate-ccw',        'color' => 'bg-amber-100 text-amber-600'],
            'assigned'    => ['label' => 'Laporan Ditugaskan',        'icon' => 'user-check',        'color' => 'bg-indigo-100 text-indigo-600'],
            'labeled'     => ['label' => 'Label Laporan Diperbarui',  'icon' => 'tag',               'color' => 'bg-purple-100 text-purple-600'],
            'unlabeled'   => ['label' => 'Label Laporan Dihapus',     'icon' => 'tag',               'color' => 'bg-slate-100 text-slate-600'],
        ];

        // Tangkap juga event project card move (kanban drag) — ini datang sebagai 'edited' dengan changes.body
        if ($action === 'edited' && isset($payload['changes'])) {
            $actionMap['edited'] = ['label' => 'Laporan Diperbarui', 'icon' => 'pencil', 'color' => 'bg-amber-100 text-amber-600'];
        }

        $cfg = $actionMap[$action] ?? null;
        if (!$cfg) return; // Abaikan action yang tidak relevan

        // Cari tiket lokal berdasarkan github_issue_url
        $ticket = $url ? SupportTicket::where('github_issue_url', $url)->first() : null;

        $notifTitle   = "GitHub Issue #{$number}: {$cfg['label']}";
        $notifMessage = "\"{$title}\"";

        if ($ticket) {
            $notifMessage .= " — laporan dari {$ticket->user?->name}";
        }

        // Kirim notifikasi ke reporter tiket (kalau ada)
        if ($ticket && $ticket->user) {
            try {
                NotificationHelper::send(
                    $ticket->user,
                    'info',
                    $notifTitle,
                    $notifMessage . " sudah {$cfg['label']} oleh developer.",
                    $url,
                    $cfg['icon'],
                    $cfg['color']
                );
            } catch (\Throwable $e) {
                Log::warning('GitHubWebhook: gagal notif ke reporter: ' . $e->getMessage());
            }
        }

        // Kirim notifikasi ke semua admin & piket
        $admins = User::whereIn('role', ['admin', 'operator', 'guru_piket'])->get();
        foreach ($admins as $admin) {
            // Jangan double-notif kalau si admin adalah reporter tiket
            if ($ticket && $ticket->user_id === $admin->id) continue;

            try {
                NotificationHelper::send(
                    $admin,
                    'info',
                    $notifTitle,
                    $notifMessage,
                    $url,
                    $cfg['icon'],
                    $cfg['color']
                );
            } catch (\Throwable $e) {
                Log::warning('GitHubWebhook: gagal notif ke admin #' . $admin->id . ': ' . $e->getMessage());
            }
        }

        Log::info("GitHub Webhook: notifikasi dikirim untuk issue #{$number} action={$action}");
    }
}
