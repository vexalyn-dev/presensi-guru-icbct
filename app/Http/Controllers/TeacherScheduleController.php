<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TeacherSchedule;
use Illuminate\Http\Request;

class TeacherScheduleController extends Controller
{
    public function index()
    {
        $teachers = User::where('role', 'guru')
            ->where('is_active', true)
            ->with('schedules')
            ->get();

        return view('schedules.index', compact('teachers'));
    }

    public function edit(User $teacher)
    {
        $schedules = TeacherSchedule::where('user_id', $teacher->id)
            ->orderBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');

        return view('schedules.edit', compact('teacher', 'schedules'));
    }

    public function update(Request $request, User $teacher)
    {
        $schedules = $request->input('schedules', []);

        // Hapus jadwal lama
        TeacherSchedule::where('user_id', $teacher->id)->delete();

        // Simpan jadwal baru (hanya yang aktif)
        foreach ($schedules as $schedule) {
            if (($schedule['is_active'] ?? '0') == '1') {
                TeacherSchedule::create([
                    'user_id' => $teacher->id,
                    'day_of_week' => $schedule['day_of_week'],
                    'start_time' => $schedule['start_time'],
                    'end_time' => $schedule['end_time'],
                    'is_active' => true,
                ]);
            }
        }

        return redirect()->route('schedules.index')
            ->with('success', 'Jadwal guru berhasil diperbarui');
    }
}
