<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with(['user:id,name,email,teacher_code,photo_url']);

        // Filter kategori
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter tipe
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        // Stats
        $stats = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'attendance' => ActivityLog::where('category', 'attendance')->whereDate('created_at', today())->count(),
            'auth' => ActivityLog::where('category', 'auth')->whereDate('created_at', today())->count(),
        ];

        // Kategori untuk filter
        $categories = [
            'attendance' => ['label' => 'Presensi', 'icon' => 'scan-line', 'color' => 'green'],
            'auth' => ['label' => 'Autentikasi', 'icon' => 'log-in', 'color' => 'blue'],
            'settings' => ['label' => 'Pengaturan', 'icon' => 'settings', 'color' => 'purple'],
            'teacher' => ['label' => 'Data Guru', 'icon' => 'users', 'color' => 'indigo'],
            'classroom' => ['label' => 'Kelas', 'icon' => 'school', 'color' => 'amber'],
            'system' => ['label' => 'Sistem', 'icon' => 'server', 'color' => 'slate'],
        ];

        // List guru untuk filter
        $teachers = User::where('role', 'guru')->orderBy('name')->get(['id', 'name', 'teacher_code']);

        return view('admin.activity-logs.index', compact('logs', 'stats', 'categories', 'teachers'));
    }

    public function show(ActivityLog $log)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $log->id,
                'user' => $log->user ? [
                    'name' => $log->user->name,
                    'email' => $log->user->email,
                    'teacher_code' => $log->user->teacher_code,
                    'photo_url' => $log->user->photo_url,
                ] : null,
                'type' => $log->type,
                'category' => $log->category,
                'description' => $log->description,
                'ip_address' => $log->ip_address,
                'user_agent' => $log->user_agent,
                'device_info' => $log->device,
                'properties' => $log->properties,
                'created_at' => $log->created_at->format('d M Y H:i:s'),
            ],
        ]);
    }

    public function destroyOld(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:7|max:365',
        ]);

        $days    = $request->days;
        $deleted = ActivityLog::where('created_at', '<', now()->subDays($days))->delete();

        return back()->with('success', "Berhasil menghapus {$deleted} log yang lebih dari {$days} hari.");
    }

    // Alias untuk route 'cleanup'
    public function cleanup(Request $request)
    {
        return $this->destroyOld($request);
    }

    public function export(Request $request)
    {
        $query = ActivityLog::with('user:id,name,email,teacher_code');

        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);

        $logs = $query->orderBy('created_at', 'desc')->limit(5000)->get();

        // Generate Excel menggunakan native PHP
        $filename = 'activity_logs_' . date('Y-m-d_His') . '.xlsx';

        // Create PHPExcel object
        $phpExcel = new \PHPExcel();
        $sheet = $phpExcel->getActiveSheet();
        $sheet->setTitle('Activity Logs');

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(12);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(35);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(12);
        $sheet->getColumnDimension('J')->setWidth(15);

        // Add headers
        $headers = ['ID', 'Tanggal', 'User', 'Kategori', 'Tipe', 'Deskripsi', 'IP Address', 'Device', 'Browser', 'OS'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        // Style header
        $headerStyle = new \PHPExcel_Style();
        $headerStyle->getFont()->setBold(true)->setSize(11)->setColor(new \PHPExcel_Style_Color('FFFFFF'));
        $headerStyle->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->setStartColor(new \PHPExcel_Style_Color('1F2937'));
        $headerStyle->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $headerStyle->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);

        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle->exportArray());

        // Add data rows
        $row = 2;
        foreach ($logs as $log) {
            $device = $log->device;
            $sheet->setCellValue('A' . $row, $log->id);
            $sheet->setCellValue('B' . $row, $log->created_at->format('d/m/Y H:i:s'));
            $sheet->setCellValue('C' . $row, $log->user?->name ?? 'System');
            $sheet->setCellValue('D' . $row, $log->category);
            $sheet->setCellValue('E' . $row, $log->type);
            $sheet->setCellValue('F' . $row, $log->description);
            $sheet->setCellValue('G' . $row, $log->ip_address ?? '-');
            $sheet->setCellValue('H' . $row, $device['device'] ?? '-');
            $sheet->setCellValue('I' . $row, $device['browser'] ?? '-');
            $sheet->setCellValue('J' . $row, $device['os'] ?? '-');
            $row++;
        }

        // Auto filter
        $sheet->setAutoFilter('A1:J' . ($row - 1));

        // Freeze header row
        $sheet->freezePane('A2');

        // Save
        $writer = \PHPExcel_IOFactory::createWriter($phpExcel, 'Excel2007');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $writer->save('php://output');
        exit;
    }
}