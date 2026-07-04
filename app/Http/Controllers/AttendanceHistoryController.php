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

        // Clone BEFORE paginate for accurate stats
        $statsQuery = clone $query;

        // Calculate stats from the unmodified query clone
        $stats = [
            'total'     => (clone $statsQuery)->count(),
            'hadir'     => (clone $statsQuery)->where('status', 'Hadir')->count(),
            'terlambat' => (clone $statsQuery)->where('status', 'Terlambat')->count(),
            'alpha'     => (clone $statsQuery)->where('status', 'Alpha')->count(),
            'izin'      => (clone $statsQuery)->whereIn('status', ['Izin', 'Sakit', 'Cuti'])->count(),
        ];

        // Get paginated data with relationships
        $attendances = $query->with('user')
            ->orderBy('date', 'desc')
            ->orderBy('check_in', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Get teachers and statuses for filters
        $teachers = User::where('role', 'guru')->orderBy('name')->get(['id', 'name']);
        $statuses = ['Hadir', 'Terlambat', 'Izin', 'Alpha'];

        // Return JSON for AJAX requests — use toArray() so frontend gets
        // the full Laravel pagination structure: links[], prev_page_url, next_page_url, etc.
        if ($request->ajax() || $request->wantsJson()) {
            $paginationData = $attendances->toArray();
            $paginationData['stats'] = $stats;

            // Ensure user relation is serialised properly in items
            $paginationData['data'] = $attendances->getCollection()->map(function ($att) {
                $arr = $att->toArray();
                $arr['user'] = $att->user ? [
                    'id'        => $att->user->id,
                    'name'      => $att->user->name,
                    'photo_url' => $att->user->photo_url,
                ] : null;
                return $arr;
            })->values()->all();

            return response()->json($paginationData);
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