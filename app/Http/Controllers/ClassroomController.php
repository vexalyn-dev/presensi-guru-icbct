<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClassroomController extends Controller
{
    public function index()
    {
        $classrooms = Classroom::withCount('teachingSchedules')
            ->orderBy('class_level')
            ->orderBy('name')
            ->get()
            ->groupBy('class_level');

        return view('classrooms.index', compact('classrooms'));
    }

    public function create()
    {
        return view('classrooms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'class_level' => 'required|in:X,XI,XII',
            'major_code'  => 'required|string|max:50',
            'is_active'   => 'nullable|boolean',
        ]);

        // Auto-generate code: X-RPL, XI-TKJ, XII-FAR
        $classCode = strtoupper($validated['class_level'] . '-' . $validated['major_code']);

        // Check uniqueness: same class_level + same generated code
        $exists = Classroom::where('class_level', $validated['class_level'])
            ->where('code', $classCode)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['major_code' => 'Kelas dengan jurusan ini sudah ada di tingkat ' . $validated['class_level']])
                ->withInput();
        }

        $validated['code']      = $classCode;
        $validated['qr_token']  = Str::uuid()->toString();
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        unset($validated['major_code']); // not a DB column

        Classroom::create($validated);

        return redirect()->route('classrooms.index')
            ->with('success', 'Kelas berhasil ditambahkan dan QR Code telah digenerate');
    }

    public function edit(Classroom $classroom)
    {
        return view('classrooms.edit', compact('classroom'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'class_level' => 'required|in:X,XI,XII',
            'major_code'  => 'required|string|max:50',
            'is_active'   => 'nullable|boolean',
        ]);

        // Auto-generate code
        $classCode = strtoupper($validated['class_level'] . '-' . $validated['major_code']);

        // Check uniqueness (exclude current classroom)
        $exists = Classroom::where('id', '!=', $classroom->id)
            ->where('class_level', $validated['class_level'])
            ->where('code', $classCode)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['major_code' => 'Kelas dengan jurusan ini sudah ada di tingkat ' . $validated['class_level']])
                ->withInput();
        }

        $validated['code']      = $classCode;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        unset($validated['major_code']); // not a DB column

        $classroom->update($validated);

        return redirect()->route('classrooms.index')
            ->with('success', 'Kelas berhasil diperbarui');
    }

    public function destroy(Classroom $classroom)
    {
        $classroom->delete();

        return redirect()->route('classrooms.index')
            ->with('success', 'Kelas berhasil dihapus');
    }

    public function qrCode(Classroom $classroom)
    {
        return view('classrooms.qr', compact('classroom'));
    }
}