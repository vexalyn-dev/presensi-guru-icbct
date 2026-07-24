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

        // Clone base query BEFORE applying status filter for accurate statistics cards
        $statsBaseQuery = clone $query;

        $stats = [
            'total'     => (clone $statsBaseQuery)->count(),
            'hadir'     => (clone $statsBaseQuery)->whereIn('status', ['Hadir', 'Tepat Waktu'])->count(),
            'terlambat' => (clone $statsBaseQuery)->where('status', 'Terlambat')->count(),
            'alpha'     => (clone $statsBaseQuery)->where('status', 'Alpha')->count(),
            'izin'      => (clone $statsBaseQuery)->whereIn('status', ['Izin', 'Sakit', 'Cuti'])->count(),
        ];

        // Apply status filter to data list
        if ($request->filled('status')) {
            if ($request->status === 'Hadir') {
                $query->whereIn('status', ['Hadir', 'Tepat Waktu']);
            } elseif ($request->status === 'Izin') {
                $query->whereIn('status', ['Izin', 'Sakit', 'Cuti']);
            } else {
                $query->where('status', $request->status);
            }
        }

        // Get paginated data with relationships
        $attendances = $query->with('user')
            ->orderBy('date', 'desc')
            ->orderBy('check_in', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Transform items consistently
        $items = $attendances->getCollection()->map(function ($att) {
            return [
                'id'         => $att->id,
                'user_id'    => $att->user_id,
                'date'       => $att->date ? Carbon::parse($att->date)->toDateString() : '',
                'check_in'   => $att->check_in ? Carbon::parse($att->check_in)->format('H:i') . ' WIB' : '-',
                'check_out'  => $att->check_out ? Carbon::parse($att->check_out)->format('H:i') . ' WIB' : '-',
                'status'     => $att->status ?? 'Hadir',
                'notes'      => $att->notes ?? '',
                'user'       => $att->user ? [
                    'id'        => $att->user->id,
                    'name'      => $att->user->name,
                    'photo_url' => $att->user->photo_url,
                ] : null,
            ];
        });

        // Get teachers and statuses for filters
        $teachers = User::where('role', 'guru')->orderBy('name')->get(['id', 'name']);
        $statuses = ['Hadir', 'Terlambat', 'Izin', 'Alpha'];

        if ($request->ajax() || $request->wantsJson()) {
            $paginationData = $attendances->toArray();
            $paginationData['data'] = $items;
            $paginationData['stats'] = $stats;

            return response()->json($paginationData);
        }

        return view('attendance.history', [
            'attendances'      => $attendances,
            'transformedItems' => $items,
            'stats'            => $stats,
            'teachers'         => $teachers,
            'statuses'         => $statuses,
        ]);
    }

    public function export(Request $request)
    {
        $query = Attendance::with('user')->orderBy('date', 'desc')->orderBy('check_in', 'desc');

        if ($request->filled('start_date')) {
            $query->where('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('date', '<=', $request->end_date);
        }
        if ($request->filled('teacher_id')) {
            $query->where('user_id', $request->teacher_id);
        }
        if ($request->filled('status')) {
            if ($request->status === 'Hadir') {
                $query->whereIn('status', ['Hadir', 'Tepat Waktu']);
            } elseif ($request->status === 'Izin') {
                $query->whereIn('status', ['Izin', 'Sakit', 'Cuti']);
            } else {
                $query->where('status', $request->status);
            }
        }

        $attendances = $query->get();

        $filename = 'laporan_absensi_guru_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($attendances) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['Tanggal', 'Nama Guru', 'Check In', 'Check Out', 'Status', 'Keterangan']);

            foreach ($attendances as $att) {
                $checkIn = $att->check_in ? Carbon::parse($att->check_in)->format('H:i') . ' WIB' : '-';
                $checkOut = $att->check_out ? Carbon::parse($att->check_out)->format('H:i') . ' WIB' : '-';
                $dateFormatted = Carbon::parse($att->date)->format('d/m/Y');
                fputcsv($file, [
                    $dateFormatted,
                    $att->user->name ?? '-',
                    $checkIn,
                    $checkOut,
                    $att->status,
                    $att->notes ?? '-'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}