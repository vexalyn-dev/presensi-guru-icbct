<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\ClassAttendance;
use App\Models\TeachingSchedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ClassAttendanceController extends Controller
{
    /**
     * Halaman scan QR kelas
     */
    public function scan()
    {
        $todaySchedules = TeachingSchedule::getTodaySchedules(auth()->id());
        $todayAttendances = ClassAttendance::where('user_id', auth()->id())
            ->where('date', today())
            ->with('classroom')
            ->get()
            ->keyBy(function ($att) {
                return $att->classroom_id . '_' . $att->period;
            });

        return view('class-attendance.scan', compact('todaySchedules', 'todayAttendances'));
    }

    /**
     * Proses scan QR kelas
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'qr_data' => 'required|string',
        ]);

        // Parse QR data
        try {
            $qrData = json_decode($validated['qr_data'], true);

            if (!isset($qrData['type']) || $qrData['type'] !== 'classroom') {
                return back()->with('error', 'QR Code tidak valid. Ini bukan QR kelas.');
            }

            $classroom = Classroom::where('id', $qrData['classroom_id'])
                ->where('qr_token', $qrData['token'])
                ->first();

            if (!$classroom) {
                return back()->with('error', 'QR Code kelas tidak dikenali.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Format QR Code tidak valid.');
        }

        $now = Carbon::now();
        $currentTime = $now->format('H:i:s');
        $today = today();

        // Cari jadwal yang cocok
        $schedule = TeachingSchedule::findMatchingSchedule(
            auth()->id(),
            $classroom->id
        );

        if (!$schedule) {
            return back()->with(
                'error',
                "Anda tidak memiliki jadwal mengajar di kelas {$classroom->name} pada waktu ini."
            );
        }

        // Cek apakah sudah scan masuk untuk periode ini
        $existingAttendance = ClassAttendance::where('user_id', auth()->id())
            ->where('classroom_id', $classroom->id)
            ->where('date', $today)
            ->where('period', $schedule->period)
            ->first();

        // Tentukan status (tepat waktu atau terlambat)
        $scheduleStart = Carbon::parse($schedule->start_time);
        $status = $now->greaterThan($scheduleStart) ? 'Terlambat' : 'Hadir';

        if (!$existingAttendance) {
            // SCAN MASUK KELAS
            ClassAttendance::create([
                'user_id' => auth()->id(),
                'classroom_id' => $classroom->id,
                'teaching_schedule_id' => $schedule->id,
                'date' => $today,
                'period' => $schedule->period,
                'check_in_time' => $currentTime,
                'status' => $status,
            ]);

            return back()->with(
                'success',
                "✅ Presensi MASUK kelas {$classroom->name} berhasil!\n" .
                "Jam Pelajaran: {$schedule->period}\n" .
                "Waktu: {$now->format('H:i')} WIB\n" .
                "Status: {$status}"
            );

        } elseif ($existingAttendance->check_in_time && !$existingAttendance->check_out_time) {
            // SCAN KELUAR KELAS
            $existingAttendance->update([
                'check_out_time' => $currentTime,
            ]);

            return back()->with(
                'success',
                "✅ Presensi KELUAR kelas {$classroom->name} berhasil!\n" .
                "Jam Pelajaran: {$schedule->period}\n" .
                "Waktu: {$now->format('H:i')} WIB"
            );

        } else {
            return back()->with(
                'error',
                "Anda sudah menyelesaikan presensi untuk kelas {$classroom->name} jam pelajaran ke-{$schedule->period}."
            );
        }
    }

    /**
     * Riwayat presensi kelas guru
     */
    public function history()
    {
        $attendances = ClassAttendance::where('user_id', auth()->id())
            ->with(['classroom', 'teachingSchedule.subject'])
            ->orderBy('date', 'desc')
            ->orderBy('period', 'desc')
            ->paginate(20);

        return view('class-attendance.history', compact('attendances'));
    }
}