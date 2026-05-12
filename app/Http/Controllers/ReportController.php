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
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-t'));
        $selectedTeacher = $request->input('teacher_id', '');

        $teachers = User::where('role', 'guru')->get();

        $query = Attendance::join('users', 'attendances.user_id', '=', 'users.id')
            ->select('attendances.*', 'users.name', 'users.email')
            ->whereBetween('date', [$startDate, $endDate]);

        if ($selectedTeacher) {
            $query->where('user_id', $selectedTeacher);
        }

        // Stats
        $total = $query->count();
        $hadir = (clone $query)->where('status', 'Hadir')->count();
        $terlambat = (clone $query)->where('status', 'Terlambat')->count();
        $izin = (clone $query)->where('status', 'Izin')->count();
        $alpha = (clone $query)->where('status', 'Alpha')->count();

        $stats = (object)[
            'total' => $total,
            'hadir' => $hadir,
            'terlambat' => $terlambat,
            'izin' => $izin,
            'alpha' => $alpha,
        ];

        $attendances = $query->orderBy('date', 'desc')->paginate(10)->withQueryString();

        return view('reports.index', compact('startDate', 'endDate', 'selectedTeacher', 'teachers', 'stats', 'attendances'));
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