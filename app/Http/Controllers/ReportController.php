<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::query()
            ->join('users', 'attendances.user_id', '=', 'users.id')
            ->select(
                'users.id as user_id',
                'users.name',
                'users.email',
                'attendances.id',
                'attendances.date',
                'attendances.check_in',
                'attendances.check_out',
                'attendances.status'
            );
        
        // Date range filter
        if ($request->filled('start_date')) {
            $query->where('attendances.date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('attendances.date', '<=', $request->end_date);
        }
        
        // Teacher filter
        if ($request->filled('teacher_id')) {
            $query->where('users.id', $request->teacher_id);
        }
        
        $attendances = $query->orderBy('attendances.date', 'desc')->paginate(15);
        
        // Calculate stats
        $stats = (object) [
            'total' => $attendances->total(),
            'hadir' => (clone $query)->where('attendances.status', 'Hadir')->count(),
            'terlambat' => (clone $query)->where('attendances.status', 'Terlambat')->count(),
            'izin' => (clone $query)->where('attendances.status', 'Izin')->count(),
            'alpha' => (clone $query)->where('attendances.status', 'Alpha')->count(),
        ];
        
        // Get teachers for dropdown
        $teachers = User::where('role', 'guru')->orderBy('name')->get(['id', 'name', 'email']);
        
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
        
        return view('reports.index', compact('attendances', 'stats', 'teachers'));
    }

    public function exportCsv(Request $request)
    {
        $startDateStr = $request->input('start_date', date('Y-m-01'));
        $endDateStr = $request->input('end_date', date('Y-m-t'));
        $teacherId = $request->input('teacher_id', '');

        $startDate = Carbon::parse($startDateStr);
        $endDate = Carbon::parse($endDateStr);

        $query = User::where('role', 'guru');
        if ($teacherId) {
            $query->where('id', $teacherId);
        }
        $teachers = $query->get();

        $workingDays = 0;
        for ($d = $startDate->copy(); $d <= $endDate; $d->addDay()) {
            if ($d->isWeekday()) $workingDays++;
        }

        $fileName = 'Laporan_Kehadiran_' . $startDateStr . '_sd_' . $endDateStr . '.csv';

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Nama Guru', 'Email', 'Total Hadir', 'Terlambat', 'Izin', 'Alpha', 'Persentase');

        $callback = function() use($teachers, $columns, $startDateStr, $endDateStr, $workingDays) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($teachers as $teacher) {
                $attendances = Attendance::where('user_id', $teacher->id)
                    ->whereBetween('date', [$startDateStr, $endDateStr])
                    ->get();

                $hadir = $attendances->where('status', 'Hadir')->count();
                $terlambat = $attendances->where('status', 'Terlambat')->count();
                $izin = $attendances->where('status', 'Izin')->count();
                $alpha = max(0, $workingDays - $hadir - $terlambat - $izin);
                $percentage = $workingDays > 0 ? round((($hadir + $terlambat) / $workingDays) * 100) : 0;

                fputcsv($file, array($teacher->name, $teacher->email, $hadir, $terlambat, $izin, $alpha, $percentage . '%'));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}