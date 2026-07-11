<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Subject::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('teachers', function($tQuery) use ($search) {
                      $tQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Category Filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $subjects = $query->with(['teachers' => function($q) {
            $q->select('teachers.id', 'teachers.name', 'teachers.email');
        }])->orderBy('name')->paginate(15)->withQueryString();

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'data'         => $subjects->items(),
                'links'        => [
                    'first' => $subjects->url(1),
                    'last'  => $subjects->url($subjects->lastPage()),
                    'prev'  => $subjects->previousPageUrl(),
                    'next'  => $subjects->nextPageUrl(),
                ],
                'current_page' => $subjects->currentPage(),
                'last_page'    => $subjects->lastPage(),
                'total'        => $subjects->total(),
            ]);
        }

        return view('subjects.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teachers = \App\Models\Teacher::orderBy('name')->get();
        return view('subjects.create', compact('teachers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string',
            'teacher_ids' => 'nullable|array',
            'teacher_ids.*' => 'exists:teachers,id',
        ]);

        // Auto-generate code
        $lastSubject = Subject::orderBy('id', 'desc')->first();
        $nextId = $lastSubject ? $lastSubject->id + 1 : 1;
        $validated['code'] = 'MP-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        $validated['category'] = 'Umum'; // Set default category
        
        $validated['is_active'] = $request->has('is_active');

        $subject = Subject::create($validated);

        // Sync multiple teachers
        if ($request->filled('teacher_ids')) {
            $subject->teachers()->sync($request->teacher_ids);
        }

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subject $subject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        $teachers = \App\Models\Teacher::with('user')->orderBy('name')->get();
        $subject->load('teachers');
        $selectedTeacherIds = $subject->teachers->pluck('id')->toArray();
        
        return view('subjects.edit', compact('subject', 'teachers', 'selectedTeacherIds'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string',
            'teacher_ids' => 'nullable|array',
            'teacher_ids.*' => 'exists:teachers,id',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['category'] = 'Umum'; // Set default category

        $subject->update($validated);

        // Get old teacher IDs before sync
        $oldTeacherIds = $subject->teachers->pluck('id')->toArray();

        // Sync multiple teachers (replace all existing with new selection)
        if ($request->has('teacher_ids')) {
            $subject->teachers()->sync($request->teacher_ids);
            $newTeacherIds = $request->teacher_ids;
        } else {
            $subject->teachers()->sync([]);
            $newTeacherIds = [];
        }

        // Update major_specialty for teachers that were removed from this subject
        $removedTeacherIds = array_diff($oldTeacherIds, $newTeacherIds);
        foreach ($removedTeacherIds as $teacherId) {
            $teacher = \App\Models\Teacher::find($teacherId);
            if ($teacher) {
                // Check if teacher still has other subjects
                $otherSubjects = $teacher->subjects()->where('subjects.id', '!=', $subject->id)->get();
                if ($otherSubjects->isEmpty()) {
                    // No other subjects, clear major_specialty
                    $teacher->update(['major_specialty' => null]);
                } else {
                    // Set to first other subject
                    $teacher->update(['major_specialty' => $otherSubjects->first()->name]);
                }
            }
        }

        // Update major_specialty for newly assigned teachers
        foreach ($newTeacherIds as $teacherId) {
            $teacher = \App\Models\Teacher::find($teacherId);
            if ($teacher) {
                // Update to this subject name
                $teacher->update(['major_specialty' => $subject->name]);
            }
        }

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Subject $subject)
    {
        // Detach all teachers (akan otomatis terhapus di pivot table karena cascade)
        $subject->teachers()->detach();
        
        $subject->delete();

        // Return JSON for AJAX/fetch requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Mata pelajaran berhasil dihapus.',
            ]);
        }

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
