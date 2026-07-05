<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Attendance;
use App\Models\AppSetting;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class QrCodeController extends Controller
{
    /**
     * Process QR Code scan for attendance
     */
    public function processScan(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'qr_data' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            // Decode QR data - Handle both JSON and plain ID
            $qrData = json_decode($validated['qr_data'], true);

            // If not JSON, assume it's teacher_id
            if (!$qrData || !is_array($qrData)) {
                $qrData = [
                    'teacher_id' => $validated['qr_data'],
                    'token' => null
                ];
            }

            if (!isset($qrData['teacher_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code tidak valid - Teacher ID tidak ditemukan'
                ], 400);
            }

            // Find teacher
            $teacher = User::where('id', $qrData['teacher_id'])
                ->where('role', 'guru')
                ->first();

            // If token exists in QR, validate it
            if (isset($qrData['token']) && $teacher) {
                $teacher = User::where('id', $qrData['teacher_id'])
                    ->where('qr_token', $qrData['token'])
                    ->where('role', 'guru')
                    ->first();
            }

            if (!$teacher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Guru tidak ditemukan. QR Code mungkin sudah tidak valid.'
                ], 404);
            }

            if (!$teacher->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun guru tidak aktif. Hubungi admin.'
                ], 403);
            }

            // Check if already attended today
            $today = Carbon::today();
            $existingAttendance = Attendance::where('user_id', $teacher->id)
                ->where('date', $today)
                ->first();

            if ($existingAttendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah presensi hari ini pada jam ' . $existingAttendance->check_in
                ], 400);
            }

            // Determine status
            $now = Carbon::now();
            $status = 'Hadir';

            $appSettings = AppSetting::getInstance();
            $endTime = $appSettings->attendance_end_time;

            if ($now->format('H:i') >= $endTime) {
                $status = 'Terlambat';
            }

            // Create attendance record
            DB::transaction(function () use ($teacher, $today, $now, $status, $request) {
                Attendance::create([
                    'user_id' => $teacher->id,
                    'date' => $today,
                    'check_in' => $now->format('H:i:s'),
                    'status' => $status,
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'scan_method' => 'qr_code',
                ]);
            });

            // Send notification to admins
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                /** @var User $admin */
                $admin->notify(new SystemNotification(
                    "Guru {$teacher->name} telah absen ({$status})",
                    'success',
                    route('attendance.history')
                ));
            }

            return response()->json([
                'success' => true,
                'message' => 'Presensi berhasil!',
                'teacher_name' => $teacher->name,
                'status' => $status,
                'time' => $now->format('H:i:s')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid'
            ], 422);
        } catch (\Exception $e) {
            Log::error('QR Scan Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show teacher's QR Code
     */
    public function show(User $teacher)
    {
        if ($teacher->role !== 'guru') {
            abort(403);
        }

        // Generate QR if not exists
        if (empty($teacher->qr_code)) {
            $teacher->generateQrCode();
        }

        return view('teachers.qr', compact('teacher'));
    }

    /**
     * Download teacher's QR Code
     */
    public function download(User $teacher)
    {
        if ($teacher->role !== 'guru') {
            abort(403);
        }
        // If QR code does not exist, generate it first
        if (empty($teacher->qr_code)) {
            $teacher->generateQrCode();
        }

        $path = storage_path('app/public/' . $teacher->qr_code);

        session()->flash('success', 'QR Code berhasil diunduh: ' . $teacher->name);

        return response()->download($path, 'QR_' . str_replace(' ', '_', $teacher->name) . '.jpg');
    }

    /**
     * Regenerate QR Code
     */
    public function regenerate(User $teacher)
    {
        if ($teacher->role !== 'guru') {
            abort(403);
        }

        try {
            // Delete old QR
            if ($teacher->qr_code) {
                Storage::disk('public')->delete($teacher->qr_code);
            }

            $teacher->generateQrCode();

            return back()->with('success', 'QR Code berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui QR Code: ' . $e->getMessage());
        }
    }

    /**
     * Display QR code scanner page
     */
    public function scan()
    {
        return view('attendance.scan');
    }
}