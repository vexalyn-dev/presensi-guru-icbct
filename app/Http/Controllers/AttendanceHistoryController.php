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
        $query = Attendance::query();
        
        // Date range filter
        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }
        
        // Teacher filter
        if ($request->filled('teacher_id')) {
            $query->where('user_id', $request->teacher_id);
        }
        
        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Get data with relationships
        $attendances = $query->with('user')->orderBy('date', 'desc')->orderBy('check_in', 'desc')->paginate(15);
        
        // Calculate stats
        $stats = [
            'total' => $attendances->total(),
            'hadir' => $query->clone()->where('status', 'Hadir')->count(),
            'terlambat' => $query->clone()->where('status', 'Terlambat')->count(),
            'alpha' => $query->clone()->where('status', 'Alpha')->count(),
            'izin' => $query->clone()->whereIn('status', ['Izin', 'Sakit', 'Cuti'])->count(),
        ];
        
        // Get teachers and statuses for filters
        $teachers = User::where('role', 'guru')->orderBy('name')->get(['id', 'name']);
        $statuses = ['Hadir', 'Terlambat', 'Izin', 'Alpha'];
        
        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'data' => $attendances->items(),
                'stats' => $stats,
                'links' => [
                    'first' => $attendances->url(1),
                    'last' => $attendances->url($attendances->lastPage()),
                    'prev' => $attendances->previousPageUrl(),
                    'next' => $attendances->nextPageUrl(),
                ],
                'current_page' => $attendances->currentPage(),
                'last_page' => $attendances->lastPage(),
                'total' => $attendances->total(),
            ]);
        }
        
        return view('attendance.history', compact('attendances', 'stats', 'teachers', 'statuses'));
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