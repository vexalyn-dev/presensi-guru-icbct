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
        $query = ActivityLog::with(['user:id,name,email,teacher_code,photo']);

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
            'settings' => ['label' => 'Pengaturan Sistem', 'icon' => 'settings', 'color' => 'purple'],
            'teacher' => ['label' => 'Data Guru', 'icon' => 'users', 'color' => 'indigo'],
            'classroom' => ['label' => 'Data Kelas', 'icon' => 'school', 'color' => 'amber'],
            'system' => ['label' => 'Sistem', 'icon' => 'server', 'color' => 'slate'],
        ];

        // List guru untuk filter
        $teachers = User::where('role', 'guru')->orderBy('name')->get(['id', 'name', 'teacher_code']);

        return view('admin.activity-logs.index', compact('logs', 'stats', 'categories', 'teachers'));
    }

    public function show(ActivityLog $log)
    {
        $categoryLabels = [
            'attendance' => 'Presensi',
            'auth'       => 'Autentikasi',
            'settings'   => 'Pengaturan Sistem',
            'teacher'    => 'Data Guru',
            'classroom'  => 'Data Kelas',
            'system'     => 'Sistem',
        ];
        $typeLabels = [
            'scan_in_daily'   => 'Scan Masuk Harian',
            'scan_out_daily'  => 'Scan Keluar Harian',
            'scan_in'         => 'Scan Masuk Kelas',
            'scan_out'        => 'Scan Keluar Kelas',
            'login'           => 'Login ke Sistem',
            'logout'          => 'Logout dari Sistem',
            'teacher_created' => 'Tambah Data Guru',
            'teacher_updated' => 'Ubah Data Guru',
            'teacher_deleted' => 'Hapus Data Guru',
            'settings_change' => 'Ubah Pengaturan',
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $log->id,
                'user' => $log->user ? [
                    'name'         => $log->user->name,
                    'email'        => $log->user->email,
                    'teacher_code' => $log->user->teacher_code,
                    'photo_url'    => $log->user->photo ? asset('storage/' . $log->user->photo) : null,
                ] : null,
                'type'        => $typeLabels[$log->type] ?? ucfirst(str_replace('_', ' ', $log->type)),
                'category'    => $categoryLabels[$log->category] ?? ucfirst($log->category),
                'description' => $log->description,
                'ip_address'  => $log->ip_address,
                'user_agent'  => $log->user_agent,
                'device_info' => $log->device,
                'properties'  => $log->properties,
                'created_at'  => $log->created_at->format('d M Y H:i:s'),
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
        if ($request->filled('date_to'))   $query->whereDate('created_at', '<=', $request->date_to);
        $logs = $query->orderBy('created_at', 'desc')->limit(5000)->get();

        $categoryLabels = [
            'attendance' => 'Presensi',    'auth' => 'Autentikasi',
            'settings'   => 'Pengaturan',  'teacher' => 'Data Guru',
            'classroom'  => 'Data Kelas',  'system' => 'Sistem',
        ];
        $typeLabels = [
            'scan_in_daily'   => 'Scan Masuk Harian',
            'scan_out_daily'  => 'Scan Keluar Harian',
            'scan_in'         => 'Scan Masuk Kelas',
            'scan_out'        => 'Scan Keluar Kelas',
            'login'           => 'Login ke Sistem',
            'logout'          => 'Logout dari Sistem',
            'teacher_created' => 'Tambah Data Guru',
            'teacher_updated' => 'Ubah Data Guru',
            'teacher_deleted' => 'Hapus Data Guru',
            'settings_change' => 'Ubah Pengaturan',
        ];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Log Aktivitas');

        // ── JUDUL ──────────────────────────────────────────────
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'LOG AKTIVITAS SISTEM — ' . strtoupper(\App\Models\AppSetting::getInstance()->app_name ?? 'SMK ICB CT'));
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(32);

        // ── SUB JUDUL (tanggal cetak) ───────────────────────────
        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', 'Dicetak pada: ' . now()->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') . ' WIB');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '64748B']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F8FAFC']],
            'alignment' => ['horizontal' => 'center'],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);

        // ── HEADER KOLOM ────────────────────────────────────────
        $headers = ['No', 'Tanggal & Waktu', 'Nama Guru', 'Kategori', 'Jenis Aktivitas',
                    'Keterangan', 'IP Address', 'Perangkat', 'Browser', 'Sistem Operasi'];
        $cols = range('A', 'J');
        foreach ($headers as $i => $h) {
            $cell = $cols[$i] . '3';
            $sheet->setCellValue($cell, $h);
        }
        $sheet->getStyle('A3:J3')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => false],
            'borders'   => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'FFFFFF']]],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(22);

        // ── DATA ROWS ───────────────────────────────────────────
        $row = 4;
        foreach ($logs as $i => $log) {
            $device  = $log->device ?? [];
            $isEven  = ($i % 2 === 0);
            $bgColor = $isEven ? 'FFFFFF' : 'F1F5F9';

            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $log->created_at->format('d/m/Y H:i'));
            $sheet->setCellValue("C{$row}", $log->user?->name ?? 'Sistem');
            $sheet->setCellValue("D{$row}", $categoryLabels[$log->category] ?? ucfirst($log->category));
            $sheet->setCellValue("E{$row}", $typeLabels[$log->type] ?? ucfirst(str_replace('_', ' ', $log->type)));
            $sheet->setCellValue("F{$row}", $log->description);
            $sheet->setCellValue("G{$row}", $log->ip_address ?? '-');
            $sheet->setCellValue("H{$row}", $device['device'] ?? '-');
            $sheet->setCellValue("I{$row}", $device['browser'] ?? '-');
            $sheet->setCellValue("J{$row}", $device['os'] ?? '-');

            // Row style
            $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => $bgColor]],
                'font'      => ['size' => 9],
                'alignment' => ['vertical' => 'center'],
                'borders'   => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'E2E8F0']]],
            ]);
            // Kolom No center
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal('center');
            // Kolom kategori — warna sesuai tipe
            $catColors = ['Presensi' => '166534', 'Autentikasi' => '1D4ED8', 'Data Guru' => '92400E',
                          'Pengaturan' => '6D28D9', 'Data Kelas' => 'B45309', 'Sistem' => '475569'];
            $catLabel = $categoryLabels[$log->category] ?? '';
            if (isset($catColors[$catLabel])) {
                $sheet->getStyle("D{$row}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color($catColors[$catLabel]))->setBold(true);
            }

            $sheet->getRowDimension($row)->setRowHeight(18);
            $row++;
        }

        // ── SUMMARY ROW ─────────────────────────────────────────
        $sheet->mergeCells("A{$row}:E{$row}");
        $sheet->setCellValue("A{$row}", "Total: {$logs->count()} aktivitas");
        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '334155']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(20);

        // ── COLUMN WIDTHS ────────────────────────────────────────
        $widths = ['A'=>6,'B'=>18,'C'=>20,'D'=>14,'E'=>22,'F'=>40,'G'=>15,'H'=>12,'I'=>16,'J'=>14];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        // Freeze header + filter
        $sheet->freezePane('A4');
        $sheet->setAutoFilter("A3:J" . ($row - 1));

        // ── OUTPUT ──────────────────────────────────────────────
        $filename = 'log_aktivitas_' . date('Y-m-d_His') . '.xlsx';
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function getCategoryLabel($category)
    {
        $labels = [
            'attendance' => 'Presensi',
            'auth' => 'Autentikasi',
            'settings' => 'Pengaturan',
            'teacher' => 'Data Guru',
            'classroom' => 'Kelas',
            'system' => 'Sistem',
        ];
        return $labels[$category] ?? ucfirst($category);
    }

    private function getTypeLabel($type)
    {
        $labels = [
            'scan_in_daily' => 'Scan Masuk (Harian)',
            'scan_out_daily' => 'Scan Keluar (Harian)',
            'scan_in' => 'Scan Masuk (Kelas)',
            'scan_out' => 'Scan Keluar (Kelas)',
            'login' => 'Login Sistem',
            'logout' => 'Logout Sistem',
            'teacher_created' => 'Tambah Guru',
            'teacher_updated' => 'Ubah Guru',
            'teacher_deleted' => 'Hapus Guru',
            'settings_change' => 'Ubah Pengaturan',
        ];
        return $labels[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }
}