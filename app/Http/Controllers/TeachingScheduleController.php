<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\TeachingSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeachingScheduleController extends Controller
{
    public function index()
    {
        $teachers = User::where('role', 'guru')
            ->where('is_active', true)
            ->with(['teachingSchedules.classroom', 'teachingSchedules.subject'])
            ->get();

        return view('teaching-schedules.index', compact('teachers'));
    }

    public function edit(User $teacher)
    {
        $classrooms = Classroom::where('is_active', true)
            ->orderByRaw("CASE WHEN class_level = 'X' THEN 1 WHEN class_level = 'XI' THEN 2 WHEN class_level = 'XII' THEN 3 ELSE 4 END")
            ->orderBy('code')
            ->get();
        $subjects = Subject::orderBy('name')->get();

        $schedules = TeachingSchedule::where('user_id', $teacher->id)
            ->with(['classroom', 'subject'])
            ->orderBy('day_of_week')
            ->orderBy('period')
            ->get();

        return view('teaching-schedules.edit', compact('teacher', 'classrooms', 'subjects', 'schedules'));
    }

    public function update(Request $request, User $teacher)
    {
        // Validasi manual karena key-nya dinamis (new_0, new_1, edit_1, dll)
        $schedules = $request->input('schedules', []);
        $deleteSchedules = $request->input('delete_schedules', []);

        // Validasi delete_schedules
        if (!empty($deleteSchedules)) {
            foreach ($deleteSchedules as $id) {
                if (!TeachingSchedule::where('id', $id)->where('user_id', $teacher->id)->exists()) {
                    return back()->withErrors(['delete_schedules' => 'Jadwal tidak valid'])->withInput();
                }
            }
        }

        // Validasi setiap schedule
        foreach ($schedules as $key => $schedule) {
            $validator = \Validator::make($schedule, [
                'day_of_week' => 'required|integer|min:0|max:6',
                'classroom_id' => 'required|exists:classrooms,id',
                'subject_id' => 'nullable|exists:subjects,id',
                'period' => 'required|integer|min:1|max:15',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i',
            ]);

            if ($validator->fails()) {
                return back()
                    ->withErrors(["schedules.{$key}" => $validator->errors()->first()])
                    ->withInput();
            }

            // Validasi jam pulang > jam masuk
            if (strtotime($schedule['end_time']) <= strtotime($schedule['start_time'])) {
                return back()
                    ->withErrors(["schedules.{$key}.end_time" => "Jam pulang harus lebih besar dari jam masuk."])
                    ->withInput();
            }
        }

        DB::beginTransaction();
        try {
            // 1. Hapus jadwal yang di-mark for delete
            if (!empty($deleteSchedules)) {
                TeachingSchedule::whereIn('id', $deleteSchedules)
                    ->where('user_id', $teacher->id)
                    ->delete();
            }

            // 2. Proses semua schedules dari form
            foreach ($schedules as $key => $schedule) {
                // Skip jika jadwal ini sudah dihapus
                if (!empty($deleteSchedules) && isset($schedule['schedule_id']) && in_array($schedule['schedule_id'], $deleteSchedules)) {
                    continue;
                }

                // Cek apakah ini edit jadwal existing atau tambah baru
                if (isset($schedule['schedule_id']) && $schedule['schedule_id']) {
                    // UPDATE jadwal existing
                    TeachingSchedule::where('id', $schedule['schedule_id'])
                        ->where('user_id', $teacher->id)
                        ->update([
                            'day_of_week' => $schedule['day_of_week'],
                            'classroom_id' => $schedule['classroom_id'],
                            'subject_id' => $schedule['subject_id'] ?? null,
                            'period' => $schedule['period'],
                            'start_time' => $schedule['start_time'],
                            'end_time' => $schedule['end_time'],
                        ]);
                } else {
                    // CREATE jadwal baru
                    TeachingSchedule::create([
                        'user_id' => $teacher->id,
                        'day_of_week' => $schedule['day_of_week'],
                        'classroom_id' => $schedule['classroom_id'],
                        'subject_id' => $schedule['subject_id'] ?? null,
                        'period' => $schedule['period'],
                        'start_time' => $schedule['start_time'],
                        'end_time' => $schedule['end_time'],
                        'is_active' => true,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('teaching-schedules.index')
                ->with('success', 'Jadwal mengajar berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }
}