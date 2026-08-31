<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Helpers\ImageOptimizer;
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
        if (!$teacher) {
            return redirect()->route('dashboard')->with('error', 'Profil guru tidak ditemukan.');
        }
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

        $photoPath = null;
        $croppedData = $request->input('cropped_photo_data');

        if ($croppedData === 'DELETE') {
            // Delete existing photos
            if ($teacher && $teacher->photo) {
                Storage::disk('public')->delete($teacher->photo);
            }
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $photoPath = null;
            $deletePhoto = true;
        } elseif ($croppedData && str_starts_with($croppedData, 'data:image')) {
            // Decode base64 and save as JPEG
            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $croppedData);
            $imageData = base64_decode($imageData);
            $filename = 'teachers/' . uniqid('photo_', true) . '.jpg';
            // Delete old photos
            if ($teacher && $teacher->photo) {
                Storage::disk('public')->delete($teacher->photo);
            }
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            Storage::disk('public')->put($filename, $imageData);
            $photoPath = $filename;
            $deletePhoto = false;
        } elseif ($request->hasFile('photo')) {
            // Regular file upload
            if ($teacher && $teacher->photo) {
                Storage::disk('public')->delete($teacher->photo);
            }
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $photoPath = ImageOptimizer::replace($request->file('photo'), $teacher?->photo ?? null, 'teachers/' . $request->file('photo')->hashName());
            $deletePhoto = false;
        } else {
            $deletePhoto = false;
        }

        // Update user
        $userData = ['name' => $validated['name']];
        if (isset($deletePhoto) && $deletePhoto) {
            $userData['photo'] = null;
        } elseif ($photoPath) {
            $userData['photo'] = $photoPath;
        }
        $user->update($userData);

        // Update teacher
        if ($teacher) {
            $teacherData = [
                'name'    => $validated['name'],
                'phone'   => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
            ];
            if (isset($deletePhoto) && $deletePhoto) {
                $teacherData['photo'] = null;
            } elseif ($photoPath) {
                $teacherData['photo'] = $photoPath;
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

    public function updateEmail(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'email'            => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'email_password'   => ['required'],
        ], [
            'email.required'        => 'Email tidak boleh kosong.',
            'email.email'           => 'Format email tidak valid.',
            'email.unique'          => 'Email sudah digunakan akun lain.',
            'email_password.required' => 'Masukkan password untuk konfirmasi.',
        ]);

        // Konfirmasi password
        if (!Hash::check($request->email_password, $user->password)) {
            return back()
                ->withErrors(['email_password' => 'Password tidak sesuai.'])
                ->withInput()
                ->with('open_email_form', true);
        }

        $oldEmail = $user->email;
        $newEmail = $request->email;

        // Tidak ada perubahan
        if ($oldEmail === $newEmail) {
            return back()->with('success', 'Email tidak ada perubahan.')->with('open_email_form', true);
        }

        // Update email & langsung verified (verifikasi email tidak digunakan di sistem ini)
        $user->update([
            'email'             => $newEmail,
            'email_verified_at' => now(),
        ]);

        return back()->with('success', 'Email berhasil diubah ke ' . $newEmail . '. Silakan verifikasi email baru Anda.');
    }
}