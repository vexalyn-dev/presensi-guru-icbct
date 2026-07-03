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

        $subjects = $query->with('teachers')->orderBy('name')->paginate(15)->withQueryString();

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
        $teachers = \App\Models\User::where('role', 'guru')->orderBy('name')->get();
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
            'teacher_id' => 'nullable|integer|exists:users,id',
        ]);

        // Auto-generate code
        $lastSubject = Subject::orderBy('id', 'desc')->first();
        $nextId = $lastSubject ? $lastSubject->id + 1 : 1;
        $validated['code'] = 'MP-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        $validated['category'] = 'Umum'; // Set default category
        
        $validated['is_active'] = $request->has('is_active');

        $subject = Subject::create($validated);

        // Assign teacher if selected
        if ($request->filled('teacher_id')) {
            $teacher = \App\Models\User::find($request->teacher_id);
            if ($teacher) {
                $teacher->update(['subject' => $subject->name]);
            }
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
        $teachers = \App\Models\User::where('role', 'guru')->orderBy('name')->get();
        $currentTeacher = $subject->teachers->first();
        return view('subjects.edit', compact('subject', 'teachers', 'currentTeacher'));
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
            'teacher_id' => 'nullable|integer|exists:users,id',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['category'] = 'Umum'; // Set default category

        $oldName = $subject->name;
        $subject->update($validated);

        // Sync teacher subject names if name changed
        if ($oldName !== $subject->name) {
            \App\Models\User::where('role', 'guru')
                ->where('subject', $oldName)
                ->update(['subject' => $subject->name]);
        }

        // Clear current teachers for this subject
        \App\Models\User::where('role', 'guru')
            ->where('subject', $subject->name)
            ->update(['subject' => null]);

        // Assign new teacher if selected
        if ($request->filled('teacher_id')) {
            $teacher = \App\Models\User::find($request->teacher_id);
            if ($teacher) {
                $teacher->update(['subject' => $subject->name]);
            }
        }

        return redirect()->route('subjects.index')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Subject $subject)
    {
        // Clear assigned teachers' subject
        \App\Models\User::where('role', 'guru')
            ->where('subject', $subject->name)
            ->update(['subject' => null]);

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
