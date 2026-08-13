<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassAttendance;
use App\Models\TeachingSchedule;
use App\Models\Classroom;
use App\Models\ScanLog;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Helpers\NotificationHelper;
use App\Services\ActivityLogService;
use Illuminate\Support\Facades\Log;

class ClassAttendanceController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // index
    // ──────────────────────────────────────────────────────────────────────────

    public function index()
    {
        $user      = auth()->user();
        $today     = Carbon::today();
        $dayOfWeek = $today->dayOfWeek;

        $schedules = TeachingSchedule::where('user_id', $user->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->with(['classroom', 'subject', 'classAttendances' => function ($q) use ($user, $today) {
                $q->where('user_id', $user->id)
                  ->whereDate('date', $today);
            }])
            ->orderBy('start_time')
            ->get();

        $totalClasses      = $schedules->count();
        $completedClasses  = $schedules->filter(fn ($s) => $s->classAttendances->first()?->isComplete())->count();
        $inProgressClasses = $schedules->filter(
            fn ($s) => $s->classAttendances->first()?->check_in_time && !$s->classAttendances->first()?->check_out_time
        )->count();

        return view('teacher.class-attendance.index', compact(
            'schedules', 'totalClasses', 'completedClasses', 'inProgressClasses'
        ));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // refreshData — AJAX endpoint for live schedule + stats refresh
    // ──────────────────────────────────────────────────────────────────────────

    public function refreshData()
    {
        $user      = auth()->user();
        $today     = Carbon::today();
        $now       = Carbon::now();
        $dayOfWeek = $today->dayOfWeek;

        $schedules = TeachingSchedule::where('user_id', $user->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->with(['classroom', 'subject', 'classAttendances' => function ($q) use ($user, $today) {
                $q->where('user_id', $user->id)
                  ->whereDate('date', $today);
            }])
            ->orderBy('start_time')
            ->get();

        $totalClasses      = $schedules->count();
        $completedClasses  = $schedules->filter(fn ($s) => $s->classAttendances->first()?->isComplete())->count();
        $inProgressClasses = $schedules->filter(
            fn ($s) => $s->classAttendances->first()?->check_in_time && !$s->classAttendances->first()?->check_out_time
        )->count();

        // Build schedule items with status
        $items = $schedules->map(function ($schedule) use ($now) {
            $startTime = Carbon::parse($schedule->start_time);
            $endTime   = Carbon::parse($schedule->end_time);
            $att       = $schedule->classAttendances->first();
            $graceEnd  = $endTime->copy()->addMinutes(3);

            if ($now->greaterThan($graceEnd)) {
                $badge = 'Berakhir'; $theme = 'red';
            } elseif ($att && $att->check_in_time) {
                if ($att->check_out_time) {
                    $badge = 'Selesai'; $theme = 'green';
                } elseif ($att->status === 'Terlambat') {
                    $badge = 'Terlambat'; $theme = 'yellow';
                } else {
                    $badge = 'Hadir'; $theme = 'green';
                }
            } elseif ($now->greaterThanOrEqualTo($startTime)) {
                $badge = 'Berlangsung'; $theme = 'blue';
            } else {
                $badge = 'Belum'; $theme = 'slate';
            }

            return [
                'id'        => $schedule->id,
                'classroom' => $schedule->classroom->code
                    ? strtoupper(str_replace('-', ' ', $schedule->classroom->code))
                    : ($schedule->classroom->name ?? '-'),
                'subject'   => $schedule->subject->name ?? '-',
                'period'    => $schedule->period,
                'start'     => $startTime->format('H:i'),
                'end'       => $endTime->format('H:i'),
                'badge'     => $badge,
                'theme'     => $theme,
                'hasCheckIn'  => (bool) $att?->check_in_time,
                'hasCheckOut' => (bool) $att?->check_out_time,
            ];
        });

        // Build reminders
        $reminders = [];

        // Check class scans
        $unscannedClasses = $schedules->filter(fn ($s) => !$s->classAttendances->first()?->check_in_time);
        foreach ($unscannedClasses as $s) {
            $className = $s->classroom->code
                ? strtoupper(str_replace('-', ' ', $s->classroom->code))
                : ($s->classroom->name ?? '-');
            $reminders[] = "Anda belum scan masuk kelas {$className}";
        }

        // Check pending check-outs
        $pendingOuts = $schedules->filter(
            fn ($s) => $s->classAttendances->first()?->check_in_time && !$s->classAttendances->first()?->check_out_time
        );
        foreach ($pendingOuts as $s) {
            $className = $s->classroom->code
                ? strtoupper(str_replace('-', ' ', $s->classroom->code))
                : ($s->classroom->name ?? '-');
            $reminders[] = "Anda belum scan keluar kelas {$className}";
        }

        return response()->json([
            'stats' => [
                'total'      => $totalClasses,
                'completed'  => $completedClasses,
                'inProgress' => $inProgressClasses,
            ],
            'schedules' => $items,
            'reminders' => $reminders,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // scan — entry point utama dari view teacher
    // ──────────────────────────────────────────────────────────────────────────

    public function scan(Request $request)
    {
        $request->validate([
            'qr_data'    => 'required|string',
            'mode'       => 'required|in:in,out',
            'schedule_id' => 'nullable|integer',
        ]);

        $user      = auth()->user();
        $now       = Carbon::now();
        $today     = $now->toDateString();
        $dayOfWeek = $now->dayOfWeek;
        $mode      = $request->mode;
        $raw       = trim($request->qr_data);

        // ── Parse QR data ────────────────────────────────────────────────────
        [$classroomId, $qrToken] = $this->parseQrData($raw);
        $decodedQrData = json_decode($raw, true);
        $timestamp = is_array($decodedQrData) && isset($decodedQrData['timestamp']) ? (int) $decodedQrData['timestamp'] : null;

        if (!$classroomId) {
            $this->logScan($user, null, $mode, 'failed', 'Format QR Code tidak valid: ' . $raw, $request);
            return response()->json(['success' => false, 'message' => 'Format QR Code tidak valid.'], 422);
        }

        // ── Cari kelas ───────────────────────────────────────────────────────
        $classroom = Classroom::find($classroomId);
        if (!$classroom) {
            $this->logScan($user, null, $mode, 'failed', "Kelas tidak ditemukan (ID: {$classroomId})", $request);
            return response()->json(['success' => false, 'message' => 'Kelas tidak ditemukan.'], 404);
        }

        // ── Validasi waktu QR (30 detik) ───────────────────────────────────
        if ($timestamp && ($now->timestamp - $timestamp) > 30) {
            $this->logScan($user, $classroom, $mode, 'failed',
                'QR Code telah kadaluarsa (30 detik)', $request);
            return response()->json([
                'success' => false,
                'message' => 'QR Code telah kadaluarsa (30 detik)',
                'debug' => [
                    'message' => 'Silakan scan ulang QR Code yang baru dibuat.',
                    'elapsed' => ($now->timestamp - $timestamp) . ' detik',
                    'current' => $now->timestamp,
                    'qr' => $timestamp,
                ],
            ], 403);
        }

        // ── Validasi GPS (radius 5 meter) ─────────────────────────────────
        $userLat = $request->input('latitude');
        $userLng = $request->input('longitude');

        if ($userLat !== null && $userLng !== null && $classroomId) {
            $classroom = Classroom::find($classroomId);

            if ($classroom && $classroom->latitude && $classroom->longitude) {
                $distance = $this->calculateDistance(
                    (float) $userLat,
                    (float) $userLng,
                    (float) $classroom->latitude,
                    (float) $classroom->longitude
                );

                if ($distance > 5) {
                    $this->logScan($user, $classroom, $mode, 'failed',
                        'Anda tidak berada di lokasi kelas (radius 5m)', $request);
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak berada di lokasi kelas (radius 5m)',
                        'debug' => [
                            'message' => 'Pastikan Anda berada di area kelas yang sama.',
                            'distance' => round($distance, 2) . 'm',
                            'classroom' => $classroom->name,
                        ],
                    ], 403);
                }
            }
        }

        // ── Validasi token ───────────────────────────────────────────────────
        // Token wajib ada dan cocok dengan qr_token di DB.
        // Jika QR dari format pipe lama (bukan UUID), token-nya tidak valid → tolak.
        if ($qrToken !== null) {
            if ($classroom->qr_token !== $qrToken) {
                $this->logScan($user, $classroom, $mode, 'failed',
                    "QR token tidak cocok untuk kelas {$classroom->name}", $request);
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code tidak valid atau sudah kedaluwarsa. Minta admin regenerate QR kelas ini.',
                ], 422);
            }
        } else {
            // Tidak ada token sama sekali — QR lama tanpa token, izinkan tapi catat warning
            Log::warning('Teacher scan: QR tanpa token digunakan', [
                'classroom_id' => $classroomId,
                'user_id'      => $user->id,
                'raw_qr'       => $raw,
            ]);
        }

        // ── Shared space? ────────────────────────────────────────────────────
        $isSharedSpace = (bool) $classroom->is_shared || $classroom->type === 'shared';

        if ($isSharedSpace) {
            // Scan pertama: belum ada pilihan kelas → tampilkan prompt (selalu, untuk in maupun out)
            if (!$request->filled('selected_classroom_id')) {
                return $this->handleSharedSpacePrompt($classroom, $user, $mode);
            }

            // Scan lanjutan: sudah ada pilihan dari modal → proses masuk atau keluar
            if ($mode === 'out') {
                return $this->handleSharedSpaceCheckOut($classroom, $user, $now, $today, $request);
            }

            // mode === 'in' dengan selected_classroom_id → langsung ke saveSharedSpaceAttendance
            // tapi flow ini datang dari submitSharedSpaceAttendance() di JS, bukan scan() langsung
            // Jika sampai sini, berarti ada payload lengkap → proses in
            return $this->handleSharedSpaceCheckIn($classroom, $user, $now, $today, $request);
        }

        // ── Cari jadwal hari ini ─────────────────────────────────────────────
        $isMultiClassroom = ($classroom->major && (str_contains($classroom->major, ',') || str_contains($classroom->major, ' ')))
            || str_contains($classroom->name, ',')
            || str_contains($classroom->name, '&');

        $schedulesQuery = TeachingSchedule::where('user_id', $user->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->with(['classroom', 'subject']);

        if (!$isSharedSpace) {
            if ($isMultiClassroom) {
                // Jika kelas ber-jurusan banyak / gabungan (misal: RPL, FARMASI),
                // cari jadwal guru hari ini yang classroom_id nya sesuai ATAU berada di tingkat yang cocok
                $schedulesQuery->where(function ($q) use ($classroomId, $classroom) {
                    $q->where('classroom_id', $classroomId);
                    if ($classroom->level) {
                        $q->orWhereHas('classroom', function ($cQ) use ($classroom) {
                            $cQ->where('level', $classroom->level);
                        });
                    }
                });
            } else {
                $schedulesQuery->where('classroom_id', $classroomId);
            }
        }

        $schedules = $schedulesQuery->get();

        if ($schedules->isEmpty()) {
            // Fallback: cari semua jadwal aktif guru hari ini agar guru tetap dapat memilih
            $allTodaySchedules = TeachingSchedule::where('user_id', $user->id)
                ->where('day_of_week', $dayOfWeek)
                ->where('is_active', true)
                ->with(['classroom', 'subject'])
                ->get();

            if ($allTodaySchedules->isNotEmpty()) {
                $schedules = $allTodaySchedules;
            } else {
                $this->logScan($user, $classroom, $mode, 'failed',
                    'Tidak ada jadwal mengajar di lokasi ini hari ini', $request);
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki jadwal mengajar hari ini.',
                ], 422);
            }
        }

        // Guru memilih jadwal spesifik dari pilihan multiple
        if ($request->filled('schedule_id')) {
            $picked = $schedules->where('id', $request->schedule_id)->first();
            if (!$picked) {
                $picked = TeachingSchedule::where('user_id', $user->id)
                    ->where('id', $request->schedule_id)
                    ->where('is_active', true)
                    ->with(['classroom', 'subject'])
                    ->first();
            }
            if (!$picked) {
                return response()->json(['success' => false, 'message' => 'Jadwal yang dipilih tidak valid.'], 422);
            }
            $res = $this->processAttendanceForSchedule($picked, $user, $now, $mode, $classroom, $request);
            return response()->json($res, $res['success'] ? 200 : 422);
        }

        // Multiple jadwal atau kelas gabungan (multi-classroom) → minta guru memilih
        if (($schedules->count() > 1 || $isMultiClassroom) && !$request->filled('schedule_id')) {
            return response()->json([
                'success'   => true,
                'message'   => $classroom->name,
                'schedules' => $schedules->map(fn ($s) => [
                    'id'             => $s->id,
                    'classroom_name' => $s->classroom->name ?? '-',
                    'subject'        => $s->subject->name ?? '-',
                    'period'         => $s->period,
                    'start_time'     => Carbon::parse($s->start_time)->format('H:i'),
                    'end_time'       => Carbon::parse($s->end_time)->format('H:i'),
                ])->values(),
            ]);
        }

        // Single jadwal — langsung proses tanpa batas waktu
        $single = $schedules->first();
        $res    = $this->processAttendanceForSchedule($single, $user, $now, $mode, $classroom, $request);
        return response()->json($res, $res['success'] ? 200 : 422);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // saveSharedSpace — endpoint langsung dari modal ON-DEMAND (tanpa QR scan ulang)
    // ──────────────────────────────────────────────────────────────────────────

    public function saveSharedSpace(Request $request)
    {
        $mode = $request->input('mode', 'in');

        // Validasi dinamis: mode out+attendance_id tidak wajib selected_classroom_id
        $rules = [
            'classroom_id'  => 'required|exists:classrooms,id',
            'mode'          => 'nullable|in:in,out',
            'attendance_id' => 'nullable|integer|exists:class_attendances,id',
        ];

        if ($mode === 'in' || !$request->filled('attendance_id')) {
            $rules['selected_classroom_id'] = 'required|exists:classrooms,id';
            $rules['subject_id']            = 'required|exists:subjects,id';
            $rules['period']                = 'required|integer|min:1|max:12';
        }

        $request->validate($rules);

        $user      = auth()->user();
        $now       = Carbon::now();
        $today     = $now->toDateString();
        $classroom = Classroom::find($request->classroom_id);

        if (!$classroom || (!$classroom->is_shared && $classroom->type !== 'shared')) {
            return response()->json(['success' => false, 'message' => 'Ruangan bersama tidak valid.'], 422);
        }

        if ($mode === 'out') {
            return $this->handleSharedSpaceCheckOut($classroom, $user, $now, $today, $request);
        }

        return $this->handleSharedSpaceCheckIn($classroom, $user, $now, $today, $request);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ──────────────────────────────────────────────────────────────────────────

    private function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c * 1000;
    }

    /**
     * Parse QR data string ke [classroomId, token|null].
     * Format didukung:
     *   1. JSON baru : {"type":"classroom","classroom_id":1,"token":"uuid"}
     *   2. JSON lama : {"classroom_id":1,"token":"uuid"} atau {"classroom_id":1}
     *   3. Pipe lama : "1|X-RPL"  → token NULL (karena kode bukan UUID)
     *
     * @return array{0: int|null, 1: string|null}
     */
    private function parseQrData(string $raw): array
    {
        $decoded = json_decode($raw, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $classroomId = $decoded['classroom_id'] ?? null;
            $token       = $decoded['token'] ?? null;

            if (!$classroomId) {
                return [null, null];
            }

            return [(int) $classroomId, $token ?: null];
        }

        // Fallback: pipe format "id|code" — code bukan token UUID, set null
        $parts       = explode('|', $raw);
        $classroomId = $parts[0] ?? null;

        if ($classroomId && is_numeric($classroomId)) {
            return [(int) $classroomId, null];
        }

        return [null, null];
    }

    /**
     * Return shared space room list + subjects untuk guru pilih.
     * Subjects: SEMUA mapel aktif (bukan hanya yang ada di jadwal guru),
     * sesuai spesifikasi ON-DEMAND — guru bebas pilih mapel apapun.
     */
    private function handleSharedSpacePrompt(Classroom $classroom, User $user, string $mode)
    {
        // Semua kelas reguler aktif
        $allClasses = Classroom::where('is_active', true)
            ->where(function ($q) {
                $q->where('is_shared', false)->orWhereNull('is_shared');
            })
            ->where(function ($q) {
                $q->where('type', 'regular')->orWhereNull('type');
            })
            ->orderBy('level')
            ->orderBy('name')
            ->get();

        // Semua mata pelajaran aktif — ON-DEMAND, tidak terikat jadwal guru
        $subjects = Subject::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Untuk mode OUT: cari sesi yang sedang berlangsung (in tapi belum out)
        // agar guru tidak perlu isi ulang dari awal
        $activeSessions = [];
        if ($mode === 'out') {
            $activeSessions = ClassAttendance::where('user_id', $user->id)
                ->where('classroom_id', $classroom->id)
                ->whereDate('date', now()->toDateString())
                ->whereNotNull('check_in_time')
                ->whereNull('check_out_time')
                ->with(['selectedClassroom', 'subject'])
                ->get()
                ->map(function ($a) {
                    $checkInStr = $a->check_in_time ? Carbon::parse($a->check_in_time)->format('H:i:s') : '00:00:00';
                    $dateStr    = $a->date ? Carbon::parse($a->date)->toDateString() : now()->toDateString();
                    $checkIn    = Carbon::parse("{$dateStr} {$checkInStr}");
                    $duration   = (int) max(0, round($checkIn->diffInMinutes(now())));

                    return [
                        'id'               => $a->id,
                        'classroom_name'   => $a->selectedClassroom?->name ?? '-',
                        'classroom_id'     => $a->selected_classroom_id,
                        'subject_name'     => $a->subject?->name ?? '-',
                        'subject_id'       => $a->subject_id,
                        'period'           => $a->period,
                        'check_in_time'    => Carbon::parse($a->check_in_time)->format('H:i'),
                        'duration_minutes' => $duration,
                    ];
                })
                ->values()
                ->toArray();
        }

        return response()->json([
            'success'          => true,
            'is_shared_space'  => true,
            'classroom'        => [
                'id'   => $classroom->id,
                'name' => $classroom->name,
                'code' => $classroom->code,
            ],
            'all_classes'      => $allClasses->map(fn ($c) => [
                'id'   => $c->id,
                'name' => $c->name,
                'code' => $c->code,
            ])->values(),
            'subjects'         => $subjects->map(fn ($s) => [
                'id'   => $s->id,
                'name' => $s->name,
            ])->values(),
            'active_sessions'  => $activeSessions,   // untuk mode out: langsung pilih sesi
            'message'          => $mode === 'in' ? 'Pilih kelas yang sedang diajar' : 'Pilih sesi yang ingin diselesaikan',
        ]);
    }

    /**
     * Proses scan MASUK shared space dengan payload lengkap dari modal.
     */
    private function handleSharedSpaceCheckIn(Classroom $classroom, User $user, Carbon $now, string $today, Request $request)
    {
        $request->validate([
            'selected_classroom_id' => 'required|exists:classrooms,id',
            'subject_id'            => 'required|exists:subjects,id',
            'period'                => 'required|integer|min:1|max:12',
        ]);

        $selectedClassroom = Classroom::find($request->selected_classroom_id);
        $subject           = Subject::find($request->subject_id);

        // Cek duplikat: periode + kelas + lokasi yang sama hari ini
        $existing = ClassAttendance::where('user_id', $user->id)
            ->where('classroom_id', $classroom->id)
            ->where('selected_classroom_id', $request->selected_classroom_id)
            ->where('subject_id', $request->subject_id)
            ->where('period', $request->period)
            ->whereDate('date', $today)
            ->first();

        if ($existing) {
            $msg = $existing->check_out_time
                ? "Presensi jam ke-{$request->period} untuk kelas {$selectedClassroom->name} sudah selesai hari ini."
                : "Anda sudah scan masuk jam ke-{$request->period} untuk kelas {$selectedClassroom->name}. Scan keluar dulu.";
            return response()->json(['success' => false, 'message' => $msg], 409);
        }

        $attendance = ClassAttendance::create([
            'user_id'               => $user->id,
            'classroom_id'          => $classroom->id,
            'selected_classroom_id' => $request->selected_classroom_id,
            'subject_id'            => $request->subject_id,
            'period'                => $request->period,
            'date'                  => $today,
            'check_in_time'         => $now,
            'status'                => 'Hadir',
            'scan_method'           => 'qr_shared_space',
            'notes'                 => "Mengajar {$selectedClassroom->name} - {$subject->name} di {$classroom->name}",
        ]);

        $this->logScan($user, $classroom, 'in', 'success',
            "Scan masuk di {$classroom->name} untuk kelas {$selectedClassroom->name} jam ke-{$request->period}", $request);

        NotificationHelper::send(
            $user,
            'class_attendance',
            '✅ Presensi Kelas Masuk',
            "Berhasil scan masuk di {$classroom->name} — Kelas: {$selectedClassroom->name}, Mapel: {$subject->name}, Jam ke-{$request->period} ({$now->format('H:i')} WIB)",
            route('teacher.class-attendance'),
            'log-in',
            'bg-emerald-100 text-emerald-600'
        );

        return response()->json([
            'success' => true,
            'message' => "✅ Presensi masuk berhasil!\nLokasi: {$classroom->name}\nKelas: {$selectedClassroom->name}\nMapel: {$subject->name}\nJam ke-{$request->period} • {$now->format('H:i')} WIB",
            'data'    => [
                'location'      => $classroom->name,
                'classroom'     => $selectedClassroom->name,
                'subject'       => $subject->name,
                'period'        => $request->period,
                'check_in_time' => $now->format('H:i'),
            ],
        ]);
    }

    /**
     * Proses scan keluar untuk shared space.
     * Support 2 cara:
     *   a) attendance_id langsung (dari active_sessions di prompt) — paling akurat
     *   b) selected_classroom_id + subject_id + period — fallback manual
     */
    private function handleSharedSpaceCheckOut(Classroom $classroom, User $user, Carbon $now, string $today, Request $request)
    {
        // Cara a: guru memilih sesi aktif langsung dari daftar
        if ($request->filled('attendance_id')) {
            $attendance = ClassAttendance::where('id', $request->attendance_id)
                ->where('user_id', $user->id)
                ->where('classroom_id', $classroom->id)
                ->whereDate('date', $today)
                ->whereNotNull('check_in_time')
                ->whereNull('check_out_time')
                ->first();

            if (!$attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi presensi tidak ditemukan atau sudah diselesaikan.',
                ], 422);
            }
        } else {
            // Cara b: match by selected_classroom_id + subject_id + period
            $request->validate([
                'selected_classroom_id' => 'required|exists:classrooms,id',
                'subject_id'            => 'required|exists:subjects,id',
                'period'                => 'required|integer|min:1|max:12',
            ]);

            $attendance = ClassAttendance::where('user_id', $user->id)
                ->where('classroom_id', $classroom->id)
                ->where('selected_classroom_id', $request->selected_classroom_id)
                ->where('subject_id', $request->subject_id)
                ->where('period', $request->period)
                ->whereDate('date', $today)
                ->whereNotNull('check_in_time')
                ->whereNull('check_out_time')
                ->first();

            if (!$attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada presensi masuk yang ditemukan. Pastikan kelas, mapel, dan jam ke- sudah sesuai.',
                ], 422);
            }
        }

        $checkInStr = $attendance->check_in_time ? Carbon::parse($attendance->check_in_time)->format('H:i:s') : '00:00:00';
        $dateStr    = $attendance->date ? Carbon::parse($attendance->date)->toDateString() : $now->toDateString();
        $checkIn    = Carbon::parse("{$dateStr} {$checkInStr}");
        $duration   = (int) max(0, round($checkIn->diffInMinutes($now)));


        $attendance->check_out_time = $now;
        $attendance->save();

        $selectedClassroom = $attendance->selectedClassroom;
        $subject           = $attendance->subject;

        $this->logScan($user, $classroom, 'out', 'success',
            "Scan keluar di {$classroom->name} untuk kelas " . ($selectedClassroom?->name ?? '-') . ", durasi {$duration} menit", $request);

        NotificationHelper::send(
            $user,
            'class_attendance',
            '✅ Presensi Kelas Selesai',
            "Berhasil scan keluar dari {$classroom->name} — Kelas: " . ($selectedClassroom?->name ?? '-') . ", Mapel: " . ($subject?->name ?? '-') . ". Durasi mengajar: {$duration} menit.",
            route('teacher.class-attendance'),
            'log-out',
            'bg-blue-100 text-blue-600'
        );

        return response()->json([
            'success' => true,
            'message' => "✅ Scan keluar berhasil!\nDurasi mengajar: {$duration} menit",
            'data'    => [
                'location'  => $classroom->name,
                'classroom' => $selectedClassroom?->name ?? '-',
                'subject'   => $subject?->name ?? '-',
                'duration'  => $duration . ' menit',
                'check_in'  => Carbon::parse($attendance->check_in_time)->format('H:i'),
                'check_out' => $now->format('H:i'),
            ],
        ]);
    }

    /**
     * Proses presensi (masuk/keluar) untuk satu jadwal yang sudah dipilih.
     */
    private function processAttendanceForSchedule(
        TeachingSchedule $schedule,
        User $user,
        Carbon $now,
        string $mode,
        ?Classroom $scannedClassroom,
        Request $request
    ): array {
        $today                 = $now->toDateString();
        $scheduleClassroomName = $schedule->classroom->name ?? '-';
        $scannedName           = $scannedClassroom?->name ?? '-';

        $attendance = ClassAttendance::firstOrNew([
            'user_id'               => $user->id,
            'classroom_id'          => $schedule->classroom_id,
            'teaching_schedule_id'  => $schedule->id,
            'period'                => $schedule->period,
            'date'                  => $today,
        ]);

        // ── Mode IN ──────────────────────────────────────────────────────────
        if ($mode === 'in') {
            if ($attendance->exists && $attendance->check_in_time) {
                $this->logScan($user, $scannedClassroom, 'in', 'duplicate',
                    "Sudah scan masuk di kelas {$scheduleClassroomName}", $request);
                return [
                    'success' => false,
                    'message' => "Anda sudah scan masuk di kelas {$scheduleClassroomName}!",
                ];
            }

            $scheduleStart = Carbon::parse($schedule->start_time);
            $isLate        = $now->gt($scheduleStart);

            $attendance->check_in_time = $now;
            $attendance->status        = $isLate ? 'Terlambat' : 'Hadir';
            $attendance->scan_method   = 'qr_in';
            $attendance->save();

            $this->logScan($user, $scannedClassroom, 'in', 'success',
                "Scan masuk berhasil di kelas {$scheduleClassroomName} ({$attendance->status}) via {$scannedName}", $request);

            // ActivityLog
            try {
                ActivityLogService::scanIn($user, $scannedClassroom ?? $schedule->classroom, $schedule, [
                    'latitude'  => $request->input('latitude'),
                    'longitude' => $request->input('longitude'),
                ]);
            } catch (\Exception $e) { Log::warning('ActivityLog scanIn failed: ' . $e->getMessage()); }

            return [
                'success' => true,
                'message' => "✅ Scan masuk berhasil di kelas {$scheduleClassroomName}",
                'data'    => [
                    'classroom'     => $scheduleClassroomName,
                    'subject'       => $schedule->subject->name ?? '-',
                    'period'        => $schedule->period,
                    'check_in_time' => $now->format('H:i'),
                    'status'        => $attendance->status,
                    'message'       => $isLate
                        ? 'Terlambat ' . $now->diffInMinutes($scheduleStart) . ' menit'
                        : 'Tepat waktu',
                ],
            ];
        }

        // ── Mode OUT ─────────────────────────────────────────────────────────
        if ($mode === 'out') {
            if (!$attendance->exists || !$attendance->check_in_time) {
                $this->logScan($user, $scannedClassroom, 'out', 'failed',
                    "Belum scan masuk di kelas {$scheduleClassroomName}", $request);
                return [
                    'success' => false,
                    'message' => "Anda belum scan masuk di kelas {$scheduleClassroomName}!",
                ];
            }

            if ($attendance->check_out_time) {
                $this->logScan($user, $scannedClassroom, 'out', 'duplicate',
                    "Sudah scan keluar di kelas {$scheduleClassroomName}", $request);
                return [
                    'success' => false,
                    'message' => "Anda sudah scan keluar di kelas {$scheduleClassroomName}!",
                ];
            }

            $checkInStr = $attendance->check_in_time ? Carbon::parse($attendance->check_in_time)->format('H:i:s') : '00:00:00';
            $dateStr    = $attendance->date ? Carbon::parse($attendance->date)->toDateString() : $now->toDateString();
            $checkIn    = Carbon::parse("{$dateStr} {$checkInStr}");
            $duration   = (int) max(0, round($checkIn->diffInMinutes($now)));


            $attendance->check_out_time = $now;
            // Tandai kelas sangat singkat (< 10 menit)
            if ($duration < 10) {
                $attendance->is_short_class = true;
                if (!$attendance->notes) {
                    $attendance->notes = "Kelas singkat: {$duration} menit";
                }
            }
            $attendance->save();

            $this->logScan($user, $scannedClassroom, 'out', 'success',
                "Scan keluar berhasil di kelas {$scheduleClassroomName}, durasi {$duration} menit via {$scannedName}", $request);

            // ActivityLog
            try {
                ActivityLogService::scanOut($user, $scannedClassroom ?? $schedule->classroom, $schedule, [
                    'latitude'  => $request->input('latitude'),
                    'longitude' => $request->input('longitude'),
                ]);
            } catch (\Exception $e) { Log::warning('ActivityLog scanOut failed: ' . $e->getMessage()); }

            // Kirim notifikasi in-app
            try {
                \App\Helpers\NotificationHelper::send(
                    $user,
                    'success',
                    'Presensi Kelas Selesai',
                    "Anda telah menyelesaikan mengajar di kelas {$scheduleClassroomName} selama {$duration} menit",
                    route('teacher.class-attendance'),
                    'check-circle',
                    'bg-green-100 text-green-600'
                );
            } catch (\Exception $e) {
                Log::warning('Notifikasi presensi kelas gagal: ' . $e->getMessage());
            }

            return [
                'success' => true,
                'message' => "✅ Scan keluar berhasil untuk kelas {$scheduleClassroomName}!",
                'data'    => [
                    'classroom' => $scheduleClassroomName,
                    'subject'   => $schedule->subject->name ?? '-',
                    'duration'  => $duration . ' menit',
                    'check_in'  => Carbon::parse($attendance->check_in_time)->format('H:i'),
                    'check_out' => $now->format('H:i'),
                    'status'    => $attendance->status,
                ],
            ];
        }

        return ['success' => false, 'message' => 'Mode tidak valid.'];
    }

    /**
     * Catat audit trail setiap scan.
     */
    private function logScan(
        User $user,
        ?Classroom $classroom,
        string $mode,
        string $status,
        string $message,
        Request $request
    ): void {
        try {
            ScanLog::create([
                'user_id'      => $user->id,
                'classroom_id' => $classroom?->id,
                'mode'         => $mode,
                'status'       => $status,
                'message'      => $message,
                'ip_address'   => $request->ip(),
                'user_agent'   => $request->userAgent(),
                'latitude'     => $request->input('latitude'),
                'longitude'    => $request->input('longitude'),
                'device_info'  => $request->header('User-Agent'),
                'metadata'     => [
                    'qr_data'         => $request->input('qr_data'),
                    'schedule_period' => $request->input('period'),
                    'schedule_id'     => $request->input('schedule_id'),
                ],
                'scanned_at'   => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('ScanLog gagal disimpan: ' . $e->getMessage());
        }
    }
}
