<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Http\Request;

class AttendanceReportController extends Controller
{
    public function index(Request $request, ReportService $service)
    {
        $month  = (int) $request->input('month', now()->month);
        $year   = (int) $request->input('year',  now()->year);
        $userId = $request->input('user_id') ?: null;

        $report = $service->getAttendanceReport($month, $year, $userId ? (int) $userId : null);
        $users  = User::where('role', 'guru')->orderBy('name')->get(['id', 'name']);

        return view('reports.attendance', compact('report', 'users', 'month', 'year', 'userId'));
    }

    public function export(Request $request, ReportService $service)
    {
        $month  = (int) $request->input('month', now()->month);
        $year   = (int) $request->input('year',  now()->year);
        $userId = $request->input('user_id') ?: null;
        $report = $service->getAttendanceReport($month, $year, $userId ? (int) $userId : null);

        $monthNames = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $periodLabel = $monthNames[$month] . ' ' . $year;

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Presensi');

        // ── JUDUL ────────────────────────────────────────
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'LAPORAN PRESENSI — ' . strtoupper(\App\Models\AppSetting::getInstance()->app_name ?? 'SMK ICB CT'));
        $sheet->getStyle('A1')->applyFromArray(['font' => ['bold'=>true,'size'=>14,'color'=>['rgb'=>'FFFFFF']], 'fill' => ['fillType'=>'solid','startColor'=>['rgb'=>'1E3A5F']], 'alignment' => ['horizontal'=>'center','vertical'=>'center']]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->mergeCells('A2:H2');
        $sheet->setCellValue('A2', 'Periode: ' . $periodLabel . '  |  Dicetak: ' . now()->locale('id')->isoFormat('dddd, D MMMM YYYY HH:mm') . ' WIB');
        $sheet->getStyle('A2')->applyFromArray(['font'=>['size'=>9,'italic'=>true,'color'=>['rgb'=>'64748B']],'fill'=>['fillType'=>'solid','startColor'=>['rgb'=>'F8FAFC']],'alignment'=>['horizontal'=>'center']]);
        $sheet->getRowDimension(2)->setRowHeight(18);

        // ── HEADER ───────────────────────────────────────
        $headers = ['No', 'Nama Guru', 'Kode', 'Total Hari', 'Hadir / Tepat Waktu', 'Terlambat', 'Izin / Sakit', 'Alpha', 'Scan Tidak Lengkap', 'Ketepatan Waktu (%)'];
        $cols = range('A', 'J');
        foreach ($headers as $i => $h) {
            $sheet->setCellValue($cols[$i] . '3', $h);
        }
        $sheet->getStyle('A3:J3')->applyFromArray(['font'=>['bold'=>true,'size'=>10,'color'=>['rgb'=>'FFFFFF']],'fill'=>['fillType'=>'solid','startColor'=>['rgb'=>'1E3A5F']],'alignment'=>['horizontal'=>'center','vertical'=>'center'],'borders'=>['allBorders'=>['borderStyle'=>'thin','color'=>['rgb'=>'FFFFFF']]]]);
        $sheet->getRowDimension(3)->setRowHeight(22);

        // ── DATA ─────────────────────────────────────────
        $pctColors = ['green'=>'166534','amber'=>'92400E','red'=>'991B1B'];
        $row = 4;
        foreach ($report as $i => $r) {
            $pct  = $r['persentase_ketepatan'];
            $pctC = $pct >= 90 ? $pctColors['green'] : ($pct >= 70 ? $pctColors['amber'] : $pctColors['red']);
            $even = ($i % 2 === 0);

            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $r['user']->name ?? '-');
            $sheet->setCellValue("C{$row}", $r['user']->teacher_code ?? '-');
            $sheet->setCellValue("D{$row}", $r['total']);
            $sheet->setCellValue("E{$row}", $r['hadir']);
            $sheet->setCellValue("F{$row}", $r['telat']);
            $sheet->setCellValue("G{$row}", $r['izin_sakit']);
            $sheet->setCellValue("H{$row}", $r['alpha']);
            $sheet->setCellValue("I{$row}", $r['incomplete_scans']);
            $sheet->setCellValue("J{$row}", $pct . '%');

            $sheet->getStyle("A{$row}:J{$row}")->applyFromArray(['fill'=>['fillType'=>'solid','startColor'=>['rgb'=>$even?'FFFFFF':'F8FAFC']],'font'=>['size'=>9],'alignment'=>['vertical'=>'center'],'borders'=>['allBorders'=>['borderStyle'=>'thin','color'=>['rgb'=>'E2E8F0']]]]);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal('center');
            $sheet->getStyle("D{$row}:I{$row}")->getAlignment()->setHorizontal('center');
            $sheet->getStyle("J{$row}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color($pctC))->setBold(true);
            $sheet->getStyle("E{$row}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('166534'))->setBold(true);

            $sheet->getRowDimension($row)->setRowHeight(18);
            $row++;
        }

        // ── SUMMARY ROW ──────────────────────────────────
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->setCellValue("A{$row}", 'TOTAL / RATA-RATA');
        $sheet->setCellValue("D{$row}", array_sum(array_column($report, 'total')));
        $sheet->setCellValue("E{$row}", array_sum(array_column($report, 'hadir')));
        $sheet->setCellValue("F{$row}", array_sum(array_column($report, 'telat')));
        $sheet->setCellValue("G{$row}", array_sum(array_column($report, 'izin_sakit')));
        $sheet->setCellValue("H{$row}", array_sum(array_column($report, 'alpha')));
        $sheet->setCellValue("I{$row}", array_sum(array_column($report, 'incomplete_scans')));
        $avgPct = count($report) > 0 ? round(array_sum(array_column($report, 'persentase_ketepatan')) / count($report), 1) : 0;
        $sheet->setCellValue("J{$row}", $avgPct . '%');
        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray(['font'=>['bold'=>true,'size'=>9,'color'=>['rgb'=>'FFFFFF']],'fill'=>['fillType'=>'solid','startColor'=>['rgb'=>'334155']],'alignment'=>['horizontal'=>'center','vertical'=>'center']]);
        $sheet->getRowDimension($row)->setRowHeight(20);

        // ── KOLOM WIDTH ──────────────────────────────────
        $widths = ['A'=>5,'B'=>25,'C'=>12,'D'=>10,'E'=>18,'F'=>12,'G'=>12,'H'=>10,'I'=>18,'J'=>18];
        foreach ($widths as $c => $w) $sheet->getColumnDimension($c)->setWidth($w);

        $sheet->freezePane('A4');
        $sheet->setAutoFilter('A3:J' . ($row - 1));

        $filename = 'laporan_presensi_' . str_pad($month, 2, '0', STR_PAD_LEFT) . '_' . $year . '.xlsx';
        $writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }
}
