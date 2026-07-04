<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\TeachingSchedule;
use Illuminate\Http\Request;

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
        $classrooms = Classroom::where('is_active', true)->orderBy('name')->get();
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
        $validated = $request->validate([
            'schedules' => 'required|array',
            'schedules.*.classroom_id' => 'required|exists:classrooms,id',
            'schedules.*.subject_id' => 'nullable|exists:subjects,id',
            'schedules.*.day_of_week' => 'required|integer|min:0|max:6',
            'schedules.*.period' => 'required|integer|min:1|max:15',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
        ]);

        // Hapus jadwal lama
        TeachingSchedule::where('user_id', $teacher->id)->delete();

        // Simpan jadwal baru
        foreach ($validated['schedules'] as $schedule) {
            TeachingSchedule::create([
                'user_id' => $teacher->id,
                'classroom_id' => $schedule['classroom_id'],
                'subject_id' => $schedule['subject_id'] ?? null,
                'day_of_week' => $schedule['day_of_week'],
                'period' => $schedule['period'],
                'start_time' => $schedule['start_time'],
                'end_time' => $schedule['end_time'],
                'is_active' => true,
            ]);
        }

        return redirect()->route('teaching-schedules.index')
            ->with('success', 'Jadwal mengajar berhasil diperbarui');
    }
}