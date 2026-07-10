<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $teacher = Teacher::where('user_id', auth()->id())->first();
        return view('teacher.profile', compact('teacher'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update user
        $user->update([
            'name' => $validated['name'],
        ]);

        // Update teacher
        if ($teacher) {
            $teacherData = [
                'name' => $validated['name'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ];

            // Handle photo upload
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($teacher->photo) {
                    Storage::disk('public')->delete($teacher->photo);
                }
                $teacherData['photo'] = $request->file('photo')->store('teachers', 'public');
            }

            $teacher->update($teacherData);
        }

        return back()->with('success', 'Profil berhasil diperbarui');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        // Check current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password berhasil diubah');
    }
}