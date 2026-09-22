<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'bio' => 'nullable|string|max:500',
        ]);

        $croppedData = $request->input('cropped_photo_data');

        if ($croppedData === 'DELETE') {
            // Delete existing photo
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $validated['photo'] = null;
        } elseif ($croppedData && str_starts_with($croppedData, 'data:image')) {
            // Validate MIME type from data URI header
            if (!preg_match('/^data:image\/(jpeg|png|gif|webp);base64,/', $croppedData)) {
                return back()->with('error', 'Format gambar tidak didukung. Gunakan JPEG, PNG, GIF, atau WebP.');
            }
            $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $croppedData);
            $imageData = base64_decode($imageData);
            // Verify it's actually an image by checking magic bytes
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $detectedType = finfo_buffer($finfo, $imageData);
            finfo_close($finfo);
            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($detectedType, $allowedMimes, true)) {
                return back()->with('error', 'File bukan gambar yang valid.');
            }
            $filename = 'profiles/' . uniqid('photo_', true) . '.jpg';
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            Storage::disk('public')->put($filename, $imageData);
            $validated['photo'] = $filename;
        } elseif ($request->hasFile('photo')) {
            // Regular file upload
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $validated['photo'] = ImageOptimizer::store($request->file('photo'), 'profiles/' . $request->file('photo')->hashName());
        } else {
            // No photo change — remove from validated so it's not overwritten
            unset($validated['photo']);
        }

        $user->update($validated);

        // Regenerate session to reflect changes
        session()->put('auth.user', $user);

        return redirect()->route('dashboard')->with('success', 'Profile berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password berhasil diubah!');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password tidak sesuai.']);
        }

        if ($user->photo) {
            Storage::disk('public')->delete($user->photo);
        }

        Auth::logout();
        $user->delete();

        return redirect('/')->with('success', 'Akun berhasil dihapus.');
    }
}