<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LeaveController extends Controller
{
    public function index()
    {
        $query = Leave::with(['user', 'approver']);
        
        if (auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }
        
        $leaves = $query->orderBy('created_at', 'desc')->paginate(15);

        if (auth()->user()->isAdmin()) {
            $stats = [
                'total' => Leave::count(),
                'pending' => Leave::where('status', 'Pending')->count(),
                'approved' => Leave::where('status', 'Approved')->count(),
                'rejected' => Leave::where('status', 'Rejected')->count(),
            ];
        } else {
            $uid = auth()->id();
            $stats = [
                'total' => Leave::where('user_id', $uid)->count(),
                'pending' => Leave::where('user_id', $uid)->where('status', 'Pending')->count(),
                'approved' => Leave::where('user_id', $uid)->where('status', 'Approved')->count(),
                'rejected' => Leave::where('user_id', $uid)->where('status', 'Rejected')->count(),
            ];
        }
        
        return view('leaves.index', compact('leaves', 'stats'));
    }

    public function create()
    {
        if (! auth()->user()->isAdmin()) {
            return redirect()->to(route('teacher.dashboard') . '#izin');
        }

        return view('leaves.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:Sakit,Izin,Dinas,Cuti',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'Pending';

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('leaves', 'public');
        }

        $leave = Leave::create($validated);

        // Notify Admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\SystemNotification(
                "Guru " . auth()->user()->name . " mengajukan " . $leave->type,
                'info',
                route('leaves.show', $leave)
            ));
        }

        if (auth()->user()->isAdmin()) {
            return redirect()->route('leaves.index')->with('success', 'Pengajuan izin berhasil dikirim!');
        }

        return redirect()->route('teacher.leaves')->with('success', 'Pengajuan izin berhasil dikirim!');
    }

    public function show(Leave $leave)
    {
        if (! auth()->user()->isAdmin() && (int) $leave->user_id !== (int) auth()->id()) {
            abort(403);
        }

        $leave->load(['user', 'approver']);

        return view('leaves.show', compact('leave'));
    }

    public function approve(Request $request, Leave $leave)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $leave->approve(auth()->id(), $validated['notes'] ?? null);

        // Notify Teacher
        $leave->user->notify(new \App\Notifications\SystemNotification(
            "Pengajuan " . $leave->type . " Anda telah DISETUJUI",
            'success',
            route('leaves.show', $leave)
        ));

        return redirect()->route('leaves.index')->with('success', 'Pengajuan izin disetujui!');
    }

    public function reject(Request $request, Leave $leave)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        $leave->reject(auth()->id(), $validated['notes'] ?? 'Ditolak oleh admin');

        // Notify Teacher
        $leave->user->notify(new \App\Notifications\SystemNotification(
            "Pengajuan " . $leave->type . " Anda telah DITOLAK",
            'error',
            route('leaves.show', $leave)
        ));

        return redirect()->route('leaves.index')->with('success', 'Pengajuan izin ditolak!');
    }

    public function myLeaves()
    {
        $leaves = Leave::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('leaves.my-leaves', compact('leaves'));
    }
}