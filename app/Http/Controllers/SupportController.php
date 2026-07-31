<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Services\VexalynService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SupportController extends Controller
{
    private ?VexalynService $vexalyn = null;

    private function vexalyn(): VexalynService
    {
        if (!$this->vexalyn) {
            $this->vexalyn = new VexalynService();
        }
        return $this->vexalyn;
    }

    /** Halaman utama Pusat Bantuan */
    public function index(Request $request)
    {
        $type = $request->get('type', 'bug');
        return view('support.index', [
            'activeType' => $type,
            'typeLabels' => SupportTicket::typeLabels(),
        ]);
    }

    /** Simpan laporan baru */
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
            'feature' => [
                'purpose'  => 'nullable|string|max:1000',
                'benefit'  => 'nullable|string|max:1000',
            ],
            'maintenance' => [
                'maintenance_type'     => 'nullable|string|max:100',
                'preferred_schedule'   => 'nullable|string|max:200',
            ],
            default => [],
        };

        $fileRules = [
            'attachments'   => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:png,jpg,jpeg,webp,pdf,mp4|max:10240',
        ];

        $validated = $request->validate(array_merge($baseRules, $extraRules, $fileRules), [
            'title.required'       => 'Judul wajib diisi',
            'title.min'            => 'Judul minimal 5 karakter',
            'description.required' => 'Deskripsi wajib diisi',
            'description.min'      => 'Deskripsi minimal 10 karakter',
            'priority.required'    => 'Prioritas wajib dipilih',
            'category.required'    => 'Kategori wajib dipilih',
        ]);

        // Upload attachments
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

        // Kumpulkan extra fields
        $extraFields = match ($type) {
            'bug' => [
                'steps_to_reproduce' => $request->steps_to_reproduce,
                'expected_result'    => $request->expected_result,
                'actual_result'      => $request->actual_result,
                'impact_level'       => $request->impact_level,
                'affected_module'    => $request->affected_module,
                'extra_notes'        => $request->extra_notes,
            ],
            'feature' => [
                'purpose' => $request->purpose,
                'benefit' => $request->benefit,
            ],
            'maintenance' => [
                'maintenance_type'   => $request->maintenance_type,
                'preferred_schedule' => $request->preferred_schedule,
            ],
            default => [],
        };

        // Metadata dari client (dikirim via hidden input)
        $metadata = [
            'browser'     => $request->input('meta_browser', 'Unknown'),
            'os'          => $request->input('meta_os', 'Unknown'),
            'device'      => $request->input('meta_device', 'Unknown'),
            'resolution'  => $request->input('meta_resolution', 'Unknown'),
            'timezone'    => $request->input('meta_timezone', 'Unknown'),
            'language'    => $request->input('meta_language', 'Unknown'),
            'url'         => $request->input('meta_url', url()->current()),
            'user_agent'  => $request->input('meta_user_agent', $request->userAgent()),
            'submitted_at'=> now()->toIso8601String(),
            'ip_address'  => $request->ip(),
        ];

        // Simpan ke database lokal (wrapped try-catch jika tabel belum ada)
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
            return redirect()->route('teacher.support')
                ->with('warning', '⚠️ Laporan gagal disimpan. Hubungi admin untuk menjalankan migrasi database.');
        }

        // Kirim ke Vexalyn
        $result = $this->vexalyn()->sendTicket($ticket);

        if ($result['success']) {
            $ticket->update([
                'ticket_id'        => $result['ticket_id'],
                'vexalyn_sent_at'  => now(),
                'vexalyn_response' => json_encode($result['data']),
            ]);

            return redirect()->route('support.history')
                ->with('success', '✅ Laporan berhasil dikirim! Nomor tiket: ' . ($result['ticket_id'] ?? $ticket->id));
        }

        // Gagal kirim ke Vexalyn — tetap simpan lokal
        $ticket->update(['vexalyn_response' => json_encode($result)]);

        return redirect()->route('support.history')
            ->with('warning', '⚠️ Laporan tersimpan namun gagal dikirim ke server. Tim kami akan segera menindaklanjuti.');
    }

    /** Riwayat tiket */
    public function history(Request $request)
    {
        try {
            $tickets = SupportTicket::where('user_id', auth()->id())
                ->orderByDesc('created_at')
                ->paginate(15);
        } catch (\Exception $e) {
            // Tabel belum ada — tampilkan empty state
            $tickets = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
        }

        return view('support.history', [
            'tickets'        => $tickets,
            'typeLabels'     => SupportTicket::typeLabels(),
            'statusLabels'   => SupportTicket::statusLabels(),
            'priorityLabels' => SupportTicket::priorityLabels(),
        ]);
    }

    /** Detail tiket (ambil dari Vexalyn jika ada) */
    public function show(SupportTicket $ticket)
    {
        abort_if($ticket->user_id !== auth()->id(), 403);

        $vexalynData = null;
        if ($ticket->ticket_id) {
            $result = $this->vexalyn()->getTicket($ticket->ticket_id);
            if ($result['success']) $vexalynData = $result['data'];
        }

        return view('support.show', [
            'ticket'       => $ticket,
            'vexalynData'  => $vexalynData,
            'typeLabels'   => SupportTicket::typeLabels(),
            'statusLabels' => SupportTicket::statusLabels(),
            'priorityLabels' => SupportTicket::priorityLabels(),
        ]);
    }
}
