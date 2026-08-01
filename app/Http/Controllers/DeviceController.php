<?php

namespace App\Http\Controllers;

use App\Models\TeacherDevice;
use App\Services\DeviceService;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function register(Request $request)
    {
        $user        = $request->user();
        $deviceCount = $user->devices()->where('is_active', true)->count();

        return view('devices.register', compact('user', 'deviceCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'device_name'  => 'required|string|max:50',
            'device_token' => 'required|string|max:100',
        ], [
            'device_name.required'  => 'Nama perangkat wajib diisi',
            'device_token.required' => 'Token perangkat tidak ditemukan. Coba refresh halaman.',
        ]);

        try {
            app(DeviceService::class)->registerDevice(
                $request->user(),
                $request->device_name,
                $request->device_token,
                $request->userAgent()
            );

            // Set cookie 30 hari
            cookie()->queue(
                cookie('device_token', $request->device_token, 60 * 24 * 30, '/', null, true, true)
            );

            return redirect()->route('teacher.dashboard')
                ->with('success', 'Perangkat "' . $request->device_name . '" berhasil didaftarkan!');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function manage()
    {
        $devices  = auth()->user()->devices()->orderByDesc('last_used_at')->get();
        $maxDevices = DeviceService::MAX_DEVICES;
        return view('devices.manage', compact('devices', 'maxDevices'));
    }

    public function destroy(TeacherDevice $device)
    {
        abort_if($device->user_id !== auth()->id(), 403, 'Akses ditolak.');

        $activeCount = auth()->user()->devices()->where('is_active', true)->count();

        if ($activeCount <= 1) {
            return back()->with('error', 'Minimal 1 perangkat aktif harus tetap terdaftar.');
        }

        $device->update(['is_active' => false]);

        return back()->with('success', 'Perangkat "' . $device->device_name . '" berhasil dinonaktifkan.');
    }
}
