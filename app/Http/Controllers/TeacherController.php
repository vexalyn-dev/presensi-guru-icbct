<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\SystemNotification;
// use App\Models\ClassRoom;
// use App\Models\TeacherSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'guru');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $teachers = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $stats = [
            'total' => User::where('role', 'guru')->count(),
            'active' => User::where('role', 'guru')->where('is_active', true)->count(),
            'inactive' => User::where('role', 'guru')->where('is_active', false)->count(),
            'today_checkin' => User::where('role', 'guru')
                ->whereHas('attendances', function($q) {
                    $q->where('date', today());
                })->count(),
        ];

        return view('teachers.index', compact('teachers', 'stats'));
    }

    public function create()
    {
        $subjects = \App\Models\Subject::where('is_active', true)->orderBy('name')->get();

        return view('teachers.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'address' => ['nullable', 'string', 'max:500'],
            'bio' => ['nullable', 'string', 'max:500'],
            'subject' => ['nullable', 'string', 'max:255'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('profiles', 'public');
        }

        DB::transaction(function () use ($validated, $photoPath) {
            $teacher = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'guru',
                'photo' => $photoPath,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'bio' => $validated['bio'] ?? null,
                'subject' => $validated['subject'] ?? null,
                'start_time' => $validated['start_time'] ?? '07:30',
                'end_time' => $validated['end_time'] ?? '16:00',
                'is_active' => true,
            ]);

            $teacher->generateQrCode();
        });

        // Send notification
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            /** @var User $admin */
            $admin->notify(new SystemNotification(
                "Data guru baru ({$validated['name']}) berhasil ditambahkan.",
                'info',
                route('teachers.index')
            ));
        }

        return redirect()->route('teachers.index')->with('success', 'Guru berhasil ditambahkan!');
    }

    public function show(User $teacher, Request $request)
    {
        if ($teacher->role !== 'guru') {
            abort(403);
        }

        // $teacher->load(['activeSubjects' => function($query) {
        //     $query->withPivot('class_id', 'semester', 'academic_year', 'hours_per_week');
        // }]);
        
        $attendances = $teacher->attendances()
            ->latest('date')
            ->latest('check_in')
            ->paginate(5);
        
        $stats = [
            'total_hadir' => $teacher->attendances()->where('status', 'Hadir')->count(),
            'total_terlambat' => $teacher->attendances()->where('status', 'Terlambat')->count(),
            'total_izin' => $teacher->attendances()->where('status', 'Izin')->count(),
            'total_alpha' => $teacher->attendances()->where('status', 'Alpha')->count(),
        ];

        // Fetch subject details for description
        $subjectDetails = null;
        if ($teacher->subject) {
            $subjectDetails = \App\Models\Subject::where('name', $teacher->subject)->first();
        }

        // If AJAX request, return only the attendance body and pagination
        if ($request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'body' => view('teachers.partials.attendance-body', compact('attendances'))->render(),
                'pagination' => view('teachers.partials.attendance-pagination', compact('attendances'))->render(),
            ]);
        }

        return view('teachers.show', compact('teacher', 'attendances', 'stats', 'subjectDetails'));
    }

    public function edit(User $teacher)
    {
        if ($teacher->role !== 'guru') {
            abort(403);
        }

        // Fetch active subjects dynamically
        $subjects = \App\Models\Subject::where('is_active', true)->orderBy('name')->get();
        
        $teacherSubject = $teacher->subject;
        
        return view('teachers.edit', compact('teacher', 'subjects', 'teacherSubject'));
    }

    public function update(Request $request, User $teacher)
    {
        if ($teacher->role !== 'guru') {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teacher->id,
            'password' => 'nullable|min:6',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'subject_id' => 'nullable|exists:subjects,id',
            'is_active' => 'nullable|boolean',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('photo')) {
            if ($teacher->photo) {
                Storage::disk('public')->delete($teacher->photo);
            }
            $updateData['photo'] = $request->file('photo')->store('profiles', 'public');
        }

        $teacher->update($updateData);

        return redirect()->route('teachers.index')
            ->with('success', 'Data guru berhasil diperbarui');
    }

    public function destroy(User $teacher)
    {
        if ($teacher->role !== 'guru') {
            abort(403);
        }

        DB::transaction(function () use ($teacher) {
            if ($teacher->photo) {
                Storage::disk('public')->delete($teacher->photo);
            }
            
            // TeacherSubject::where('user_id', $teacher->id)->delete();
            $teacher->attendances()->delete();
            $teacher->delete();
        });

        // Send notification
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            /** @var User $admin */
            $admin->notify(new SystemNotification(
                "Data guru ({$teacher->name}) telah dihapus dari sistem.",
                'warning'
            ));
        }

        return redirect()->route('teachers.index')->with('success', 'Guru berhasil dihapus!');
    }

    public function toggleStatus(User $teacher)
    {
        if ($teacher->role !== 'guru') {
            abort(403);
        }

        DB::transaction(function () use ($teacher) {
            $teacher->update(['is_active' => !$teacher->is_active]);
        });
        
        $status = $teacher->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Guru berhasil {$status}!");
    }

    public function bulkToggleStatus(Request $request)
    {
        $validated = $request->validate([
            'teacher_ids' => ['required', 'array', 'min:1'],
            'teacher_ids.*' => ['integer', 'exists:users,id'],
            'status' => ['required', 'in:0,1'],
        ]);

        $ids = array_unique($validated['teacher_ids']);
        $isActive = $validated['status'] === '1';

        $count = User::where('role', 'guru')
            ->whereIn('id', $ids)
            ->update(['is_active' => $isActive]);

        if ($count === 0) {
            return redirect()->route('teachers.index')->with('error', 'Tidak ada guru yang valid untuk diperbarui.');
        }

        $label = $isActive ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('teachers.index')->with('success', "{$count} guru berhasil {$label}.");
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'teacher_ids' => ['required', 'array', 'min:1'],
            'teacher_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $ids = array_unique($validated['teacher_ids']);
        $teachers = User::where('role', 'guru')->whereIn('id', $ids)->get();

        if ($teachers->isEmpty()) {
            return redirect()->route('teachers.index')->with('error', 'Tidak ada guru yang valid untuk dihapus.');
        }

        DB::transaction(function () use ($teachers) {
            foreach ($teachers as $teacher) {
                /** @var User $teacher */
                if ($teacher->photo) {
                    Storage::disk('public')->delete($teacher->photo);
                }
                // TeacherSubject::where('user_id', $teacher->id)->delete();
                $teacher->attendances()->delete();
                $teacher->delete();
            }
        });

        $n = $teachers->count();

        return redirect()->route('teachers.index')->with('success', "{$n} guru berhasil dihapus.");
    }

    public function getData(User $teacher)
    {
        if ($teacher->role !== 'guru') {
            return response()->json(['error' => 'Not a teacher'], 403);
        }

        return response()->json([
            'id' => $teacher->id,
            'name' => $teacher->name,
            'email' => $teacher->email,
            'subject' => $teacher->subject,
        ]);
    }
}