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
            ->orderBy('name')
            ->get();

        return view('classrooms.index', compact('classrooms'));
    }

    public function create()
    {
        return view('classrooms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:classrooms',
            'building' => 'nullable|string|max:255',
            'floor' => 'nullable|integer|min:0',
        ]);

        $validated['qr_token'] = Str::uuid()->toString();
        Classroom::create($validated);

        return redirect()->route('classrooms.index')
            ->with('success', 'Kelas berhasil ditambahkan');
    }

    public function edit(Classroom $classroom)
    {
        return view('classrooms.edit', compact('classroom'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:classrooms,code,' . $classroom->id,
            'building' => 'nullable|string|max:255',
            'floor' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

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

    /**
     * Generate QR Code untuk kelas
     */
    public function qrCode(Classroom $classroom)
    {
        return view('classrooms.qr', compact('classroom'));
    }
}