<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('user')
            ->latest('date')
            ->latest('check_in');

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }

        // Filter by teacher
        if ($request->filled('teacher_id')) {
            $query->where('user_id', $request->teacher_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->paginate(15);
        $teachers = User::where('role', 'guru')->where('is_active', true)->get();
        $statuses = ['Hadir', 'Terlambat', 'Izin', 'Alpha'];

        // Stats for selected period
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth();
        
        $stats = [
            'total' => Attendance::whereBetween('date', [$startDate, $endDate])->count(),
            'hadir' => Attendance::whereBetween('date', [$startDate, $endDate])->where('status', 'Hadir')->count(),
            'terlambat' => Attendance::whereBetween('date', [$startDate, $endDate])->where('status', 'Terlambat')->count(),
            'alpha' => Attendance::whereBetween('date', [$startDate, $endDate])->where('status', 'Alpha')->count(),
        ];

        return view('attendance.history', compact('attendances', 'teachers', 'statuses', 'stats'));
    }

    public function export(Request $request)
    {
        // Simple CSV export implementation
        $query = Attendance::with('user')->latest('date');
        
        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }

        $attendances = $query->get();

        $filename = 'laporan_absensi_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($attendances) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Tanggal', 'Nama Guru', 'Email', 'Check In', 'Check Out', 'Status', 'Keterangan']);
            
            foreach ($attendances as $att) {
                fputcsv($file, [
                    $att->date,
                    $att->user->name,
                    $att->user->email,
                    $att->check_in,
                    $att->check_out,
                    $att->status,
                    $att->notes
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}