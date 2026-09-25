<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Stat presensi hari ini
        $hadirHariIni    = Attendance::whereDate('date', $today)->where('status', 'Hadir')->count();
        $terlambatHariIni = Attendance::whereDate('date', $today)->where('status', 'Terlambat')->count();
        $totalGuru       = User::whereIn('role', ['guru', 'guru_piket'])->where('is_active', true)->count();
        $belumAbsen      = max(0, $totalGuru - $hadirHariIni - $terlambatHariIni);

        // Pengajuan izin pending
        $pendingLeaves = LeaveRequest::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        $pendingCount = LeaveRequest::where('status', 'pending')->count();

        // Presensi terbaru hari ini
        $recentAttendances = Attendance::with('user')
            ->whereDate('date', $today)
            ->orderBy('check_in', 'desc')
            ->take(8)
            ->get();

        return view('piket.dashboard', compact(
            'hadirHariIni', 'terlambatHariIni', 'totalGuru', 'belumAbsen',
            'pendingLeaves', 'pendingCount', 'recentAttendances'
        ));
    }

    public function data()
    {
        $today = Carbon::today();

        $hadir = Attendance::whereDate('date', $today)->where('status', 'Hadir')->count();
        $terlambat = Attendance::whereDate('date', $today)->where('status', 'Terlambat')->count();
        $totalGuru = User::whereIn('role', ['guru', 'guru_piket'])->where('is_active', true)->count();
        $belumAbsen = max(0, $totalGuru - $hadir - $terlambat);
        $pendingCount = LeaveRequest::where('status', 'pending')->count();

        $recent = Attendance::with('user')
            ->whereDate('date', $today)
            ->orderBy('check_in', 'desc')
            ->take(8)
            ->get()
            ->map(function ($att) {
                $photo = $att->user?->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($att->user?->name ?? 'G').'&background=0F172A&color=fff&size=64';
                return [
                    'name'      => $att->user?->name ?? '-',
                    'photo'     => $photo,
                    'check_in'  => $att->check_in ? Carbon::parse($att->check_in)->format('H:i') : '-',
                    'check_out' => $att->check_out ? Carbon::parse($att->check_out)->format('H:i') : null,
                    'status'    => $att->status ?? 'Hadir',
                ];
            });

        $pending = LeaveRequest::with('user')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($leave) {
                $photo = $leave->user?->photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($leave->user?->name ?? 'G').'&background=0F172A&color=fff&size=64';
                return [
                    'id'        => $leave->id,
                    'name'      => $leave->user?->name ?? '-',
                    'photo'     => $photo,
                    'type'      => ucfirst($leave->type),
                    'start'     => optional($leave->start_date)->format('d M'),
                    'end'       => optional($leave->end_date)->format('d M Y'),
                    'route'     => route('piket.leave-approval.show', $leave),
                ];
            });

        return response()->json([
            'stats'          => compact('hadir', 'terlambat', 'belumAbsen', 'totalGuru', 'pendingCount'),
            'recent'         => $recent,
            'pending'        => $pending,
        ]);
    }
}

