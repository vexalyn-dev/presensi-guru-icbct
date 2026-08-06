<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Models\User;
use App\Services\GitHubService;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupportController extends Controller
{
    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function routePrefix(): string
    {
        $user = auth()->user();
        if (!$user) return 'teacher';

        return match (true) {
            $user->canAccessAdmin() => 'admin',
            $user->isGuruPiket()    => 'piket',
            default                 => 'teacher',
        };
    }

    private function supportRoute(string $name, mixed $params = []): string
    {
        return route($this->routePrefix() . '.support.' . $name, $params);
    }

    // -------------------------------------------------------------------------
    // Actions
    // -------------------------------------------------------------------------

    /** Halaman utama Pusat Bantuan */
    public function index(Request $request)
    {
        return view('support.index', [
            'activeType' => $request->get('type', 'bug'),
            'typeLabels' => SupportTicket::typeLabels(),
        ]);
    }

    /** Simpan & kirim laporan ke GitHub */
    public function store(Request $request)
    {
        $type = $request->input('type', 'bug');

        $baseRules = [
            'type'        => 'required|in:bug,feature,maintenance,question',
            'title'       => 'required|string|min:5|max:200',
            'description' => 'required|string|min:10|max:5000',
            'priority'    => 'required|in:low,medium,high,critical',
        ];

        $extraRules = match ($type) {
            'bug' => [
                'category'           => 'required|string|max:100',
                'steps_to_reproduce' => 'nullable|string|max:3000',
                'expected_result'    => 'nullable|string|max:1000',
                'actual_result'      => 'nullable|string|max:1000',
                'impact_level'       => 'nullable|string|max:100',
                'affected_module'    => 'nullable|string|max:200',
                'extra_notes'        => 'nullable|string|max:1000',
            ],
            'feature'     => ['purpose' => 'nullable|string|max:1000', 'benefit'  => 'nullable|string|max:1000'],
            'maintenance' => ['maintenance_type' => 'nullable|string|max:100', 'preferred_schedule' => 'nullable|string|max:200'],
            default       => [],
        };

        $validated = $request->validate(array_merge($baseRules, $extraRules, [
            'attachments'   => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:png,jpg,jpeg,webp,pdf,mp4|max:10240',
        ]), [
            'title.required'       => 'Judul wajib diisi',
            'title.min'            => 'Judul minimal 5 karakter',
            'description.required' => 'Deskripsi wajib diisi',
            'description.min'      => 'Deskripsi minimal 10 karakter',
            'priority.required'    => 'Prioritas wajib dipilih',
            'category.required'    => 'Kategori wajib dipilih',
        ]);

        // Upload file lampiran ke storage lokal
        $uploadedFiles = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('support-attachments/' . now()->format('Y/m'), 'public');
                $uploadedFiles[] = [
                    'name' => $file->getClientOriginalName(),
                    'url'  => Storage::disk('public')->url($path),
                    'type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ];
            }
        }

        // Extra fields per tipe
        $extraFields = match ($type) {
            'bug'         => array_intersect_key($request->only(['steps_to_reproduce','expected_result','actual_result','impact_level','affected_module','extra_notes']), array_flip(['steps_to_reproduce','expected_result','actual_result','impact_level','affected_module','extra_notes'])),
            'feature'     => $request->only(['purpose', 'benefit']),
            'maintenance' => $request->only(['maintenance_type', 'preferred_schedule']),
            default       => [],
        };

        // Metadata sistem dari client
        $metadata = [
            'browser'      => $request->input('meta_browser',    'Unknown'),
            'os'           => $request->input('meta_os',         'Unknown'),
            'device'       => $request->input('meta_device',     'Unknown'),
            'resolution'   => $request->input('meta_resolution', 'Unknown'),
            'timezone'     => $request->input('meta_timezone',   'Unknown'),
            'language'     => $request->input('meta_language',   'Unknown'),
            'url'          => $request->input('meta_url',         url()->current()),
            'user_agent'   => $request->input('meta_user_agent', $request->userAgent()),
            'submitted_at' => now()->toIso8601String(),
            'ip_address'   => $request->ip(),
        ];

        // Simpan ke database
        try {
            $ticket = SupportTicket::create([
                'user_id'     => auth()->id(),
                'type'        => $type,
                'title'       => $validated['title'],
                'description' => $validated['description'],
                'category'    => $validated['category'] ?? null,
                'priority'    => $validated['priority'],
                'status'      => 'new',
                'metadata'    => $metadata,
                'attachments' => $uploadedFiles,
                'extra_fields'=> $extraFields,
            ]);
        } catch (\Exception $e) {
            \Log::error('SupportTicket create failed: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan laporan.'], 500);
            }
            return redirect()->to($this->supportRoute('index'))
                ->with('error', 'Laporan gagal disimpan. Hubungi admin.');
        }

        // Kirim ke GitHub Issues
        $github = new GitHubService();
        $result = $github->createIssue($ticket);

        if ($result['success']) {
            $ticket->update(['github_issue_url' => $result['issue_url']]);
        }

        // Kirim notifikasi ke semua admin & guru_piket
        $this->notifyAdmins($ticket);

        // Selalu sukses (data sudah tersimpan lokal, GitHub best-effort)
        $successMsg = '✅ Laporan berhasil dikirim! ID lokal: #' . $ticket->id
                    . ($result['issue_url'] ? ' · GitHub: ' . $result['issue_url'] : '');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => '✅ Laporan berhasil dikirim!',
                'redirect' => $this->supportRoute('history'),
            ]);
        }

        return redirect()->to($this->supportRoute('history'))->with('success', $successMsg);
    }

    /** Kirim notifikasi laporan baru ke semua admin & guru_piket */
    private function notifyAdmins(SupportTicket $ticket): void
    {
        $typeMap = [
            'bug'         => ['icon' => 'bug',          'color' => 'bg-red-100 text-red-600'],
            'feature'     => ['icon' => 'lightbulb',    'color' => 'bg-amber-100 text-amber-600'],
            'maintenance' => ['icon' => 'wrench',        'color' => 'bg-blue-100 text-blue-600'],
            'question'    => ['icon' => 'help-circle',   'color' => 'bg-purple-100 text-purple-600'],
        ];

        $cfg      = $typeMap[$ticket->type] ?? $typeMap['bug'];
        $reporter = $ticket->user?->name ?? 'Seseorang';
        $typeText = SupportTicket::typeLabels()[$ticket->type]['label'] ?? ucfirst($ticket->type);

        $title   = "Laporan Baru: {$typeText}";
        $message = "{$reporter} baru aja kirim laporan \"{$ticket->title}\". Cek deh!";

        $recipients = User::whereIn('role', ['admin', 'operator', 'guru_piket'])->get();

        foreach ($recipients as $user) {
            // Tentukan URL show tiket sesuai role penerima
            $prefix = match (true) {
                in_array($user->role, ['admin', 'operator']) => 'admin',
                $user->role === 'guru_piket'                 => 'piket',
                default                                      => 'admin',
            };

            try {
                $url = route("{$prefix}.support.show", $ticket);
                NotificationHelper::send($user, 'warning', $title, $message, $url, $cfg['icon'], $cfg['color']);
            } catch (\Throwable $e) {
                \Log::warning('notifyAdmins gagal untuk user #' . $user->id . ': ' . $e->getMessage());
            }
        }
    }

    /** Riwayat tiket */
    public function history()
    {
        try {
            $tickets = SupportTicket::where('user_id', auth()->id())
                ->orderByDesc('created_at')
                ->paginate(15);
        } catch (\Exception $e) {
            $tickets = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
        }

        return view('support.history', [
            'tickets'        => $tickets,
            'typeLabels'     => SupportTicket::typeLabels(),
            'statusLabels'   => SupportTicket::statusLabels(),
            'priorityLabels' => SupportTicket::priorityLabels(),
        ]);
    }

    /** Hapus tiket */
    public function destroy(SupportTicket $ticket)
    {
        abort_if($ticket->user_id !== auth()->id(), 403);

        if (!empty($ticket->attachments)) {
            foreach ($ticket->attachments as $file) {
                if (!empty($file['url'])) {
                    $path = str_replace(Storage::disk('public')->url(''), '', $file['url']);
                    if (Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->delete($path);
                    }
                }
            }
        }

        $ticket->delete();

        return redirect()->to($this->supportRoute('history'))
            ->with('success', '✅ Laporan berhasil dihapus.');
    }

    /** Detail tiket */
    public function show(SupportTicket $ticket)
    {
        $user = auth()->user();
        // Pemilik tiket selalu boleh, admin & piket juga boleh
        if ((int) $ticket->user_id !== (int) $user->id && !$user->canAccessAdmin() && !$user->isGuruPiket()) {
            abort(403);
        }

        return view('support.show', [
            'ticket'         => $ticket,
            'typeLabels'     => SupportTicket::typeLabels(),
            'statusLabels'   => SupportTicket::statusLabels(),
            'priorityLabels' => SupportTicket::priorityLabels(),
        ]);
    }
}
