<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\NotificationHelper;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveApprovalController extends Controller
{
    public function index()
    {
        $leaves = LeaveRequest::with('user', 'approvedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $pendingCount  = LeaveRequest::where('status', 'pending')->count();
        $approvedCount = LeaveRequest::where('status', 'approved')->count();
        $rejectedCount = LeaveRequest::where('status', 'rejected')->count();

        return view('admin.leave-approval.index', compact(
            'leaves', 'pendingCount', 'approvedCount', 'rejectedCount'
        ));
    }

    public function approve(LeaveRequest $leaveRequest)
    {
        $leaveRequest->update([
            'status'      => 'approved',
            'admin_notes' => request('admin_notes'),
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        NotificationHelper::send(
            $leaveRequest->user,
            'success',
            'Pengajuan ' . ucfirst($leaveRequest->type) . ' Disetujui',
            'Pengajuan ' . $leaveRequest->type . ' Anda dari tanggal ' . optional($leaveRequest->start_date)->format('d M Y') . ' s/d ' . optional($leaveRequest->end_date)->format('d M Y') . ' telah disetujui.',
            route('teacher.leave.show', ['leaveRequest' => $leaveRequest->id]),
            'check-circle',
            'bg-green-100 text-green-600'
        );

        return back()->with('success', 'Pengajuan izin berhasil disetujui');
    }

    public function reject(LeaveRequest $leaveRequest)
    {
        $leaveRequest->update([
            'status'      => 'rejected',
            'admin_notes' => request('admin_notes'),
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        NotificationHelper::send(
            $leaveRequest->user,
            'error',
            'Pengajuan ' . ucfirst($leaveRequest->type) . ' Ditolak',
            'Pengajuan ' . $leaveRequest->type . ' Anda ditolak. Alasan: ' . (request('admin_notes') ?? '-'),
            route('teacher.leave.show', ['leaveRequest' => $leaveRequest->id]),
            'x-circle',
            'bg-red-100 text-red-600'
        );

        return back()->with('success', 'Pengajuan izin ditolak');
    }
}
