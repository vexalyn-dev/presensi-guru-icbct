<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

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
                'users.photo',
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

        // Transform the collection to include photo_url
        $attendances->getCollection()->transform(function ($attendance) {
            $attendance->photo_url = $attendance->photo 
                ? asset('storage/' . $attendance->photo) 
                : asset('images/default-teacher.png');
            return $attendance;
        });

        // Calculate stats
        $stats = (object) [
            'total'     => $attendances->total(),
            'hadir'     => (clone $query)->where('attendances.status', 'Hadir')->count(),
            'terlambat' => (clone $query)->where('attendances.status', 'Terlambat')->count(),
            'izin'      => (clone $query)->where('attendances.status', 'Izin')->count(),
            'alpha'     => (clone $query)->where('attendances.status', 'Alpha')->count(),
        ];

        // Get teachers for dropdown
        $teachers = User::where('role', 'guru')->orderBy('name')->get(['id', 'name', 'email']);

        // Weekly tardiness recap
        $weeklyRecap = $this->buildWeeklyRecap($request);

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'data'          => $attendances->items(),
                'stats'         => $stats,
                'current_page'  => $attendances->currentPage(),
                'last_page'     => $attendances->lastPage(),
                'prev_page_url' => $attendances->previousPageUrl(),
                'next_page_url' => $attendances->nextPageUrl(),
                'links'         => $attendances->toArray()['links'] ?? [],
                'total'         => $attendances->total(),
            ]);
        }

        return view('reports.index', compact('attendances', 'stats', 'teachers', 'weeklyRecap'));
    }

    // ─── Weekly Tardiness Recap ─────────────────────────────────────────────────
    private function buildWeeklyRecap(Request $request): array
    {
        $teachers    = User::where('role', 'guru')->where('is_active', true)->orderBy('name')->get();
        $weeklyRecap = [];

        foreach ($teachers as $teacher) {
            $q = Attendance::where('user_id', $teacher->id)->where('status', 'Terlambat');

            if ($request->filled('start_date')) $q->where('date', '>=', $request->start_date);
            if ($request->filled('end_date'))   $q->where('date', '<=', $request->end_date);

            $lateRecords = $q->get();

            $week1 = $lateRecords->filter(fn($r) => Carbon::parse($r->date)->day <= 7)->count();
            $week2 = $lateRecords->filter(fn($r) => Carbon::parse($r->date)->day > 7  && Carbon::parse($r->date)->day <= 14)->count();
            $week3 = $lateRecords->filter(fn($r) => Carbon::parse($r->date)->day > 14 && Carbon::parse($r->date)->day <= 21)->count();
            $week4 = $lateRecords->filter(fn($r) => Carbon::parse($r->date)->day > 21)->count();
            $total = $week1 + $week2 + $week3 + $week4;

            if ($total > 0) {
                $weeklyRecap[] = [
                    'id'        => $teacher->id,
                    'name'      => $teacher->name,
                    'email'     => $teacher->email,
                    'photo_url' => $teacher->photo_url,
                    'week1'     => $week1,
                    'week2'     => $week2,
                    'week3'     => $week3,
                    'week4'     => $week4,
                    'total'     => $total,
                ];
            }
        }

        usort($weeklyRecap, fn($a, $b) => $b['total'] <=> $a['total']);
        return $weeklyRecap;
    }

    // ─── Export Excel (PhpSpreadsheet) ─────────────────────────────────────────
    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate   = $request->input('end_date', date('Y-m-t'));
        $teacherId = $request->input('teacher_id');

        $query = User::where('role', 'guru')->orderBy('name');
        if ($teacherId) $query->where('id', $teacherId);
        $teachers = $query->get();

        // Count working days in range
        $workingDays = 0;
        for ($d = Carbon::parse($startDate)->copy(); $d <= Carbon::parse($endDate); $d->addDay()) {
            if ($d->isWeekday()) $workingDays++;
        }

        // ─── Spreadsheet setup ────────────────────────────────────────────────
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Absensi');

        $navy      = '1E3A5F';
        $navyLight = '2D4A6E';
        $gold      = 'F59E0B';
        $white     = 'FFFFFF';
        $stripe    = 'F8FAFC';

        // ─── Row 1: Title ─────────────────────────────────────────────────────
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'LAPORAN KEHADIRAN GURU');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => $white], 'name' => 'Calibri'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $navy]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(42);

        // ─── Row 2: School ────────────────────────────────────────────────────
        $sheet->mergeCells('A2:H2');
        $sheet->setCellValue('A2', 'SMK ICB CINTA TEKNIKA');
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => $white], 'name' => 'Calibri'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $navy]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(24);

        // ─── Row 3: Period info ───────────────────────────────────────────────
        $sheet->mergeCells('A3:H3');
        $periodText = 'Periode: ' . Carbon::parse($startDate)->locale('id')->isoFormat('D MMMM YYYY')
            . ' s/d ' . Carbon::parse($endDate)->locale('id')->isoFormat('D MMMM YYYY')
            . '   |   Dicetak: ' . now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm') . ' WIB';
        $sheet->setCellValue('A3', $periodText);
        $sheet->getStyle('A3')->applyFromArray([
            'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => 'CBD5E1'], 'name' => 'Calibri'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $navyLight]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(20);

        // ─── Row 4: Spacer ────────────────────────────────────────────────────
        $sheet->getRowDimension(4)->setRowHeight(10);

        // ─── Row 5: Table Header ──────────────────────────────────────────────
        $headers = ['No', 'Nama Guru', 'Email', 'Hari Kerja', 'Hadir', 'Terlambat', 'Izin/Sakit', 'Alpha'];
        $cols    = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        foreach ($cols as $i => $col) {
            $sheet->setCellValue("{$col}5", $headers[$i]);
        }
        $sheet->getStyle('A5:H5')->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1E293B'], 'name' => 'Calibri'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $gold]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
        ]);
        $sheet->getRowDimension(5)->setRowHeight(30);

        // ─── Data Rows ────────────────────────────────────────────────────────
        $row = 6;
        $no  = 1;
        foreach ($teachers as $teacher) {
            $atts      = Attendance::where('user_id', $teacher->id)->whereBetween('date', [$startDate, $endDate])->get();
            $hadir     = $atts->where('status', 'Hadir')->count();
            $terlambat = $atts->where('status', 'Terlambat')->count();
            $izin      = $atts->whereIn('status', ['Izin', 'Sakit', 'Cuti'])->count();
            $alpha     = max(0, $workingDays - $hadir - $terlambat - $izin);
            $bgColor   = ($no % 2 === 0) ? $stripe : $white;

            $sheet->setCellValue("A{$row}", $no++);
            $sheet->setCellValue("B{$row}", $teacher->name);
            $sheet->setCellValue("C{$row}", $teacher->email);
            $sheet->setCellValue("D{$row}", $workingDays);
            $sheet->setCellValue("E{$row}", $hadir);
            $sheet->setCellValue("F{$row}", $terlambat);
            $sheet->setCellValue("G{$row}", $izin);
            $sheet->setCellValue("H{$row}", $alpha);

            $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                'font'      => ['size' => 10, 'name' => 'Calibri'],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
            ]);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Highlight Alpha > 0 red
            if ($alpha > 0) {
                $sheet->getStyle("H{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'DC2626']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF2F2']],
                ]);
            }
            // Highlight Terlambat > 3 amber
            if ($terlambat > 3) {
                $sheet->getStyle("F{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'D97706']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFBEB']],
                ]);
            }
            // Highlight all-hadir green
            if ($hadir >= $workingDays && $workingDays > 0) {
                $sheet->getStyle("E{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '16A34A']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDF4']],
                ]);
            }

            $sheet->getRowDimension($row)->setRowHeight(22);
            $row++;
        }

        // ─── Outer border around table ────────────────────────────────────────
        $lastDataRow = $row - 1;
        $sheet->getStyle("A5:H{$lastDataRow}")->getBorders()->getOutline()
            ->setBorderStyle(Border::BORDER_MEDIUM)
            ->setColor(new Color($navy));

        // ─── Spacer row ───────────────────────────────────────────────────────
        $row++;

        // ─── Summary header ───────────────────────────────────────────────────
        $sheet->mergeCells("A{$row}:H{$row}");
        $sheet->setCellValue("A{$row}", 'RINGKASAN');
        $sheet->getStyle("A{$row}")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => $white], 'name' => 'Calibri'],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $navy]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(26);
        $row++;

        $ids = $teachers->pluck('id');
        $summaryData = [
            ['Total Guru',          $teachers->count()],
            ['Hari Kerja',          $workingDays],
            ['Total Hadir',         Attendance::whereIn('user_id', $ids)->whereBetween('date', [$startDate, $endDate])->where('status', 'Hadir')->count()],
            ['Total Terlambat',     Attendance::whereIn('user_id', $ids)->whereBetween('date', [$startDate, $endDate])->where('status', 'Terlambat')->count()],
            ['Total Izin / Sakit',  Attendance::whereIn('user_id', $ids)->whereBetween('date', [$startDate, $endDate])->whereIn('status', ['Izin', 'Sakit', 'Cuti'])->count()],
            ['Total Alpha',         Attendance::whereIn('user_id', $ids)->whereBetween('date', [$startDate, $endDate])->where('status', 'Alpha')->count()],
        ];

        foreach ($summaryData as [$label, $value]) {
            $sheet->mergeCells("A{$row}:C{$row}");
            $sheet->mergeCells("D{$row}:H{$row}");
            $sheet->setCellValue("A{$row}", $label);
            $sheet->setCellValue("D{$row}", $value);
            $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                'font'      => ['size' => 10, 'name' => 'Calibri'],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $stripe]],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
            ]);
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            $sheet->getStyle("D{$row}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getRowDimension($row)->setRowHeight(20);
            $row++;
        }

        // ─── Column widths ────────────────────────────────────────────────────
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(28);
        $sheet->getColumnDimension('C')->setWidth(34);
        $sheet->getColumnDimension('D')->setWidth(14);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(13);
        $sheet->getColumnDimension('G')->setWidth(13);
        $sheet->getColumnDimension('H')->setWidth(10);

        // ─── Freeze pane below header ─────────────────────────────────────────
        $sheet->freezePane('A6');

        // ─── Print settings ───────────────────────────────────────────────────
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1);

        // ─── Stream download ──────────────────────────────────────────────────
        $filename = 'Laporan_Kehadiran_' . $startDate . '_sd_' . $endDate . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: 0');
        header('Pragma: public');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}