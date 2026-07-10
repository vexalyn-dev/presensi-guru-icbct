<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\Teacher;
use App\Models\TeacherSchedule;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $viewMode = $request->input('view_mode', 'weekly');
        $search = $request->input('search', '');

        if ($viewMode === 'daily') {
            $startDate = $request->input('start_date') ?: Carbon::today()->toDateString();
            $endDate = $startDate;
        } elseif ($viewMode === 'weekly') {
            $baseDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now();
            $startDate = $baseDate->copy()->startOfWeek()->toDateString();
            $endDate = $baseDate->copy()->endOfWeek()->toDateString();
        } elseif ($viewMode === 'monthly') {
            $baseDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now();
            $startDate = $baseDate->copy()->startOfMonth()->toDateString();
            $endDate = $baseDate->copy()->endOfMonth()->toDateString();
        } else {
            $startDate = $request->input('start_date') ?: Carbon::now()->startOfWeek()->toDateString();
            $endDate = $request->input('end_date') ?: Carbon::now()->endOfWeek()->toDateString();
        }

        // Get teachers
        $teachersQuery = User::where('role', 'guru')->where('is_active', true);
        if ($search) {
            $teachersQuery->where('name', 'like', "%{$search}%");
        }
        $teachers = $teachersQuery->with('teacher')->orderBy('name')->get();

        // Generate dates
        $dates = [];
        $period = CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $date) {
            $dates[] = $date->copy();
        }

        // Get attendances
        $attendances = Attendance::whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy(fn($att) => $att->user_id . '_' . $att->date);

        // Get approved leaves
        $leaves = LeaveRequest::where('status', 'approved')
            ->where('end_date', '>=', $startDate)
            ->where('start_date', '<=', $endDate)
            ->get();

        // Get holidays
        $holidays = Holiday::whereBetween('date', [$startDate, $endDate])->pluck('date')->toArray();

        // Build report data
        $reportData = [];
        $totalStats = ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0];

        foreach ($teachers as $teacher) {
            $teacherData = [
                'user' => $teacher,
                'teacher' => $teacher->teacher,
                'days' => [],
                'summary' => ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0, 'T' => 0],
            ];

            foreach ($dates as $date) {
                $dayOfWeek = $date->dayOfWeek;
                $dateStr = $date->toDateString();

                // Check if weekend (Saturday=6, Sunday=0)
                $isWeekend = in_array($dayOfWeek, [0, 6]);

                // Check if holiday
                $isHoliday = in_array($dateStr, $holidays);

                // Check if teacher has schedule
                $hasSchedule = TeacherSchedule::where('user_id', $teacher->id)
                    ->where('day_of_week', $dayOfWeek)
                    ->where('is_active', true)
                    ->exists();

                // Libur = weekend OR holiday OR no schedule
                if ($isWeekend || $isHoliday || !$hasSchedule) {
                    $teacherData['days'][$dateStr] = [
                        'status' => 'libur',
                        'code' => '-',
                    ];
                    continue;
                }

                // Check attendance
                $attKey = $teacher->id . '_' . $dateStr;
                $attendance = $attendances->get($attKey)?->first();

                if ($attendance) {
                    $code = match ($attendance->status) {
                        'Hadir' => 'H',
                        'Terlambat' => 'T',
                        'Izin' => 'I',
                        'Sakit' => 'S',
                        'Alpha' => 'A',
                        default => 'A',
                    };
                    $teacherData['days'][$dateStr] = [
                        'status' => strtolower($attendance->status),
                        'code' => $code,
                        'check_in' => $attendance->check_in,
                        'check_out' => $attendance->check_out,
                    ];
                    $teacherData['summary'][$code]++;
                    $statusKey = strtolower($attendance->status);
                    if (isset($totalStats[$statusKey])) {
                        $totalStats[$statusKey]++;
                    }
                } else {
                    // Check approved leave
                    $onLeave = $leaves->first(fn($l) => $l->user_id === $teacher->id
                        && $l->start_date->toDateString() <= $dateStr
                        && $l->end_date->toDateString() >= $dateStr);

                    if ($onLeave) {
                        $code = $onLeave->type === 'sakit' ? 'S' : 'I';
                        $teacherData['days'][$dateStr] = [
                            'status' => $onLeave->type,
                            'code' => $code,
                        ];
                        $teacherData['summary'][$code]++;
                        $totalStats[$onLeave->type === 'sakit' ? 'sakit' : 'izin']++;
                    } else {
                        // Alpha
                        $teacherData['days'][$dateStr] = [
                            'status' => 'alpha',
                            'code' => 'A',
                        ];
                        $teacherData['summary']['A']++;
                        $totalStats['alpha']++;
                    }
                }
            }

            $reportData[] = $teacherData;
        }

        $totalAbsensi = array_sum($totalStats);
        $totalHadir = $totalStats['hadir'];
        $kehadiranRate = $totalAbsensi > 0 ? round(($totalHadir / $totalAbsensi) * 100) : 0;

        return view('reports.index', compact(
            'reportData',
            'dates',
            'totalStats',
            'totalAbsensi',
            'kehadiranRate',
            'startDate',
            'endDate',
            'viewMode',
            'search'
        ));
    }

    public function export(Request $request)
    {
        $viewMode = $request->input('view_mode', 'weekly');
        $search = $request->input('search', '');

        if ($viewMode === 'daily') {
            $startDate = $request->input('start_date') ?: Carbon::today()->toDateString();
            $endDate = $startDate;
        } elseif ($viewMode === 'weekly') {
            $baseDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now();
            $startDate = $baseDate->copy()->startOfWeek()->toDateString();
            $endDate = $baseDate->copy()->endOfWeek()->toDateString();
        } elseif ($viewMode === 'monthly') {
            $baseDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now();
            $startDate = $baseDate->copy()->startOfMonth()->toDateString();
            $endDate = $baseDate->copy()->endOfMonth()->toDateString();
        } else {
            $startDate = $request->input('start_date') ?: Carbon::now()->startOfWeek()->toDateString();
            $endDate = $request->input('end_date') ?: Carbon::now()->endOfWeek()->toDateString();
        }

        $teachersQuery = User::where('role', 'guru')->where('is_active', true);
        if ($search) {
            $teachersQuery->where('name', 'like', "%{$search}%");
        }
        $teachers = $teachersQuery->with('teacher')->orderBy('name')->get();

        $dates = [];
        $period = CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $date) {
            $dates[] = $date->copy();
        }

        $attendances = Attendance::whereBetween('date', [$startDate, $endDate])->get()
            ->groupBy(fn($att) => $att->user_id . '_' . $att->date);

        $leaves = LeaveRequest::where('status', 'approved')
            ->where('end_date', '>=', $startDate)
            ->where('start_date', '<=', $endDate)
            ->get();

        $holidays = Holiday::whereBetween('date', [$startDate, $endDate])->pluck('date')->toArray();

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Absensi');

        // Premium Styling Defaults
        $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);
        $sheet->setShowGridlines(false); // Clean look

        // Calculate columns
        $lastColIndex = 4 + count($dates) + 5; 
        $lastColStr = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColIndex);

        // Title Area Styling (Navy Background)
        $titleRange = 'B2:' . $lastColStr . '4';
        $sheet->getStyle($titleRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0F172A');
        
        // Title
        $sheet->setCellValue('B2', 'LAPORAN ABSENSI GURU');
        $sheet->mergeCells('B2:' . $lastColStr . '2');
        $sheet->getStyle('B2')->getFont()->setBold(true)->setSize(16)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

        // Subtitle
        $sheet->setCellValue('B3', 'ICB CINTA TEKNIKA');
        $sheet->mergeCells('B3:' . $lastColStr . '3');
        $sheet->getStyle('B3')->getFont()->setBold(true)->setSize(12)->getColor()->setARGB('FFEAB308'); // Gold

        // Periode
        $periodText = \Carbon\Carbon::parse($startDate)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($endDate)->format('d M Y');
        $sheet->setCellValue('B4', 'Periode: ' . $periodText);
        $sheet->mergeCells('B4:' . $lastColStr . '4');
        $sheet->getStyle('B4')->getFont()->setItalic(true)->getColor()->setARGB('FF94A3B8'); // Slate-400
        
        $sheet->getStyle('B3:' . $lastColStr . '4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        
        $sheet->getRowDimension(2)->setRowHeight(25);
        $sheet->getRowDimension(3)->setRowHeight(20);
        $sheet->getRowDimension(4)->setRowHeight(20);

        // Table Headers
        $headerRow = 6;
        $sheet->getRowDimension($headerRow)->setRowHeight(25);
        $sheet->setCellValue('B' . $headerRow, 'No');
        $sheet->setCellValue('C' . $headerRow, 'Nama Guru');
        $sheet->setCellValue('D' . $headerRow, 'Mapel');

        $col = 5;
        foreach ($dates as $date) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->setCellValue($colLetter . $headerRow, $date->format('d/m'));
            $col++;
        }

        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $headerRow, 'H');
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $headerRow, 'I');
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $headerRow, 'S');
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $headerRow, 'A');
        $sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $headerRow, 'T');

        // Style headers (Gold background, Navy text)
        $headerRange = 'B' . $headerRow . ':' . $lastColStr . $headerRow;
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FF0F172A']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEAB308']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'bottom' => ['borderStyle' => Border::BORDER_THICK, 'color' => ['argb' => 'FF0F172A']]
            ]
        ]);

        // Data rows
        $row = $headerRow + 1;
        $no = 1;
        foreach ($teachers as $teacher) {
            $sheet->getRowDimension($row)->setRowHeight(22);
            
            // Explicit White or Slate-100 background to prevent Dark Mode bugs
            $bgColor = ($no % 2 === 0) ? 'FFF1F5F9' : 'FFFFFFFF';
            $rowRange = 'B' . $row . ':' . $lastColStr . $row;
            $sheet->getStyle($rowRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($bgColor);
            $sheet->getStyle($rowRange)->getFont()->getColor()->setARGB('FF0F172A'); // Force Navy text

            $sheet->setCellValue('B' . $row, $no);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            $sheet->setCellValue('C' . $row, $teacher->name);
            $sheet->getStyle('C' . $row)->getFont()->setBold(true);
            
            $sheet->setCellValue('D' . $row, $teacher->subject ?? '-');

            $summary = ['H' => 0, 'I' => 0, 'S' => 0, 'A' => 0, 'T' => 0];
            $col = 5;

            foreach ($dates as $date) {
                $dateStr = $date->toDateString();
                $dayOfWeek = $date->dayOfWeek;
                $isWeekend = in_array($dayOfWeek, [0, 6]);
                $isHoliday = in_array($dateStr, $holidays);
                $hasSchedule = TeacherSchedule::where('user_id', $teacher->id)
                    ->where('day_of_week', $dayOfWeek)
                    ->where('is_active', true)
                    ->exists();

                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                
                if ($isWeekend || $isHoliday || !$hasSchedule) {
                    $sheet->setCellValue($colLetter . $row, '-');
                    $sheet->getStyle($colLetter . $row)->getFont()->getColor()->setARGB('FF94A3B8'); // slate-400
                } else {
                    $attKey = $teacher->id . '_' . $dateStr;
                    $attendance = $attendances->get($attKey)?->first();

                    if ($attendance) {
                        $code = match ($attendance->status) {
                            'Hadir' => 'H', 'Terlambat' => 'T', 'Izin' => 'I', 'Sakit' => 'S', default => 'A'
                        };
                        $sheet->setCellValue($colLetter . $row, $code);
                        $summary[$code]++;
                    } else {
                        $onLeave = $leaves->first(fn($l) => $l->user_id === $teacher->id
                            && $l->start_date->toDateString() <= $dateStr
                            && $l->end_date->toDateString() >= $dateStr);

                        if ($onLeave) {
                            $code = $onLeave->type === 'sakit' ? 'S' : 'I';
                            $sheet->setCellValue($colLetter . $row, $code);
                            $summary[$code]++;
                        } else {
                            $sheet->setCellValue($colLetter . $row, 'A');
                            $summary['A']++;
                        }
                    }
                    
                    // Color coding for attendance codes
                    $color = match ($sheet->getCell($colLetter . $row)->getValue()) {
                        'H' => 'FF16A34A', // green-600
                        'I' => 'FF2563EB', // blue-600
                        'S' => 'FF0891B2', // cyan-600
                        'A' => 'FFDC2626', // red-600
                        'T' => 'FFD97706', // amber-600
                        default => 'FF0F172A'
                    };
                    $sheet->getStyle($colLetter . $row)->getFont()->setBold(true)->getColor()->setARGB($color);
                }
                
                $sheet->getStyle($colLetter . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $col++;
            }

            // Summary Colors
            $summaryCodes = ['H' => 'FF16A34A', 'I' => 'FF2563EB', 'S' => 'FF0891B2', 'A' => 'FFDC2626', 'T' => 'FFD97706'];
            
            foreach (['H', 'I', 'S', 'A', 'T'] as $type) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                $sheet->setCellValue($colLetter . $row, $summary[$type]);
                $sheet->getStyle($colLetter . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                if ($summary[$type] > 0) {
                    $sheet->getStyle($colLetter . $row)->getFont()->setBold(true)->getColor()->setARGB($summaryCodes[$type]);
                } else {
                    $sheet->getStyle($colLetter . $row)->getFont()->getColor()->setARGB('FF94A3B8'); // slate-400
                }
                $col++;
            }

            $row++;
            $no++;
        }

        // Auto-size columns, add padding
        $sheet->getColumnDimension('A')->setWidth(3); // Empty margin
        $sheet->getColumnDimension('B')->setWidth(5);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        
        // Date columns and summary columns fixed width
        for ($c = 5; $c <= $lastColIndex; $c++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
            $sheet->getColumnDimension($colLetter)->setWidth(7);
        }

        // Borders
        $dataRange = 'B' . $headerRow . ':' . $lastColStr . ($row - 1);
        // Inner borders light slate
        $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFCBD5E1');
        // Outer border thick navy
        $sheet->getStyle($dataRange)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_MEDIUM)->getColor()->setARGB('FF0F172A');
        
        // Vertical align middle for all cells
        $sheet->getStyle($dataRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        // Output
        $filename = 'Laporan_Absensi_' . $startDate . '_' . $endDate . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}