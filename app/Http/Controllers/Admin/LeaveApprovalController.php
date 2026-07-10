<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\NotificationHelper;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveApprovalController extends Controller
{
    public function approve(LeaveRequest $leaveRequest)
    {
        $leaveRequest->update([
            'status' => 'approved',
            'admin_notes' => request('admin_notes'),
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Kirim notifikasi ke guru
        NotificationHelper::send(
            $leaveRequest->user,
            'success',
            'Pengajuan ' . ucfirst($leaveRequest->type) . ' Disetujui',
            'Pengajuan ' . $leaveRequest->type . ' Anda dari tanggal ' . $leaveRequest->start_date->format('d M Y') . ' s/d ' . $leaveRequest->end_date->format('d M Y') . ' telah disetujui oleh admin.',
            route('teacher.leave.show', $leaveRequest),
            'check-circle',
            'bg-green-100 text-green-600'
        );

        return back()->with('success', 'Pengajuan izin berhasil disetujui');
    }

    public function reject(LeaveRequest $leaveRequest)
    {
        $leaveRequest->update([
            'status' => 'rejected',
            'admin_notes' => request('admin_notes'),
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Kirim notifikasi ke guru
        NotificationHelper::send(
            $leaveRequest->user,
            'error',
            'Pengajuan ' . ucfirst($leaveRequest->type) . ' Ditolak',
            'Pengajuan ' . $leaveRequest->type . ' Anda dari tanggal ' . $leaveRequest->start_date->format('d M Y') . ' s/d ' . $leaveRequest->end_date->format('d M Y') . ' ditolak. Alasan: ' . (request('admin_notes') ?? '-'),
            route('teacher.leave.show', $leaveRequest),
            'x-circle',
            'bg-red-100 text-red-600'
        );

        return back()->with('success', 'Pengajuan izin ditolak');
    }
}