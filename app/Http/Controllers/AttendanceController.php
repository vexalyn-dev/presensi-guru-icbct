<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'qr_data' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        // Parse QR data (JSON format)
        try {
            $qrData = json_decode($validated['qr_data'], true);
            
            if (!isset($qrData['teacher_id'], $qrData['token'])) {
                return back()->with('error', 'QR code tidak valid.');
            }
            
            $teacher = User::find($qrData['teacher_id']);
            
            if (!$teacher || $teacher->role !== 'guru' || $teacher->qr_token !== $qrData['token']) {
                return back()->with('error', 'QR code tidak valid atau sudah kadaluarsa.');
            }
            
            if (!$teacher->is_active) {
                return back()->with('error', 'Guru ini tidak aktif.');
            }
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses QR code.');
        }

        // Check if already attended today
        $existing = Attendance::where('user_id', $teacher->id)
            ->where('date', today())
            ->first();
        
        if ($existing) {
            return back()->with('error', 'Anda sudah absen hari ini.');
        }

        // Determine status based on time
        $now = now();
        $startTime = Carbon::parse(config('app.attendance_start_time', '07:30'));
        $endTime = Carbon::parse(config('app.attendance_end_time', '08:00'));
        
        $currentTime = Carbon::parse($now->format('H:i'));
        
        if ($currentTime->greaterThan($endTime)) {
            $status = 'Terlambat';
        } else {
            $status = 'Hadir';
        }

        // Create attendance record
        $attendance = Attendance::create([
            'user_id' => $teacher->id,
            'date' => today(),
            'check_in' => $now->format('H:i:s'),
            'status' => $status,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'scan_method' => 'qr_code',
        ]);

        // Notify Admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            /** @var \App\Models\User $admin */
            $admin->notify(new \App\Notifications\SystemNotification(
                "Guru {$teacher->name} telah absen ({$status})",
                'success',
                route('attendance.history')
            ));
        }

        return redirect()->route('dashboard')->with('success', 'Absensi berhasil! Status: ' . $status);
    }
}