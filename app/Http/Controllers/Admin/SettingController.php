<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\AppSetting;
use App\Services\ApkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    private function syncToAppSetting(): void
    {
        try {
            $appSetting = AppSetting::getInstance();
            $appSetting->update([
                'app_name' => Setting::get('app_name', 'ICB CT - Absensi Guru'),
                'app_timezone' => Setting::get('app_timezone', 'Asia/Jakarta'),
                'app_language' => Setting::get('app_language', 'id'),
                'admin_email' => Setting::get('admin_email', ''),
                'attendance_start_time' => Setting::get('attendance_start_time', '06:30'),
                'attendance_end_time' => Setting::get('attendance_end_time', '16:00'),
                'attendance_late_grace_period' => (int) Setting::get('attendance_late_grace_period', 5),
                'location_required' => Setting::get('gps_validation_status', 'on') === 'on',
                'location_latitude' => (float) Setting::get('school_latitude', -6.9142403),
                'location_longitude' => (float) Setting::get('school_longitude', 107.6458618),
                'location_radius' => (int) Setting::get('location_radius', 50),
                'email_notification' => (bool) Setting::get('email_notification', true),
                'late_notification' => (bool) Setting::get('late_notification', true),
                'primary_color' => Setting::get('primary_color', '#0F172A'),
                'accent_color' => Setting::get('accent_color', '#FACC15'),
                'app_logo' => Setting::get('app_logo', ''),
                'app_favicon' => Setting::get('app_favicon', ''),
            ]);
        } catch (\Exception $e) {
            // Ignore error if app_settings table sync fails
        }
    }

    public function index()
    {
        // Load semua settings ke array
        $settings = [
            'general' => [
                'app_name' => Setting::get('app_name', 'ICB CT - Absensi Guru'),
                'app_timezone' => Setting::get('app_timezone', 'Asia/Jakarta'),
                'app_language' => Setting::get('app_language', 'id'),
                'admin_email' => Setting::get('admin_email', ''),
            ],
            'attendance' => [
                'attendance_start_time' => Setting::get('attendance_start_time', '06:30'),
                'attendance_end_time' => Setting::get('attendance_end_time', '16:00'),
                'attendance_late_grace_period' => Setting::get('attendance_late_grace_period', 5),
                'gps_validation_status' => Setting::get('gps_validation_status', 'on'),
                'qr_expiration' => Setting::get('qr_expiration', 30),
                'auto_logout' => Setting::get('auto_logout', 'off'),
            ],
            'appearance' => [
                'primary_color' => Setting::get('primary_color', '#0F172A'),
                'accent_color' => Setting::get('accent_color', '#FACC15'),
                'app_logo' => Setting::get('app_logo', ''),
                'app_favicon' => Setting::get('app_favicon', ''),
            ],
            'notification' => [
                'email_notification' => Setting::get('email_notification', true),
                'late_notification' => Setting::get('late_notification', true),
                'alert_email' => Setting::get('alert_email', ''),
            ],
            'maps' => [
                'school_latitude' => Setting::get('school_latitude', -6.9142403),
                'school_longitude' => Setting::get('school_longitude', 107.6458618),
                'location_radius' => Setting::get('location_radius', 50),
            ],
        ];

        $apkSetting = null;
        try {
            $appSet = AppSetting::getInstance();
            // Cek apakah kolom apk_file sudah ada di database
            $hasApkColumns = \Illuminate\Support\Facades\Schema::hasColumn('app_settings', 'apk_file');
            if (!$hasApkColumns) {
                // Migration belum jalan — baca dari Setting key-value
                $appSet->apk_file        = Setting::get('apk_file_path') ?: null;
                $appSet->apk_name        = Setting::get('apk_name') ?: null;
                $appSet->apk_version     = Setting::get('apk_version') ?: null;
                $appSet->apk_min_android = Setting::get('apk_min_android') ?: null;
                $appSet->apk_size        = (int) Setting::get('apk_size', 0);
                $appSet->apk_changelog   = Setting::get('apk_changelog') ?: null;
                $appSet->apk_uploaded_at = null;
            } elseif (!$appSet->apk_file && Setting::get('apk_file_path')) {
                // Kolom ada tapi kosong — sync dari Setting
                $appSet->apk_file        = Setting::get('apk_file_path');
                $appSet->apk_name        = Setting::get('apk_name');
                $appSet->apk_version     = Setting::get('apk_version');
                $appSet->apk_min_android = Setting::get('apk_min_android');
                $appSet->apk_size        = (int) Setting::get('apk_size', 0);
            }
            $apkSetting = $appSet;
        } catch (\Throwable $e) {
            $apkSetting = null;
        }

        return view('settings.index', compact('settings', 'apkSetting'));
    }

    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_timezone' => 'required|string|max:100',
            'app_language' => 'required|string|max:50',
            'admin_email' => 'nullable|email',
        ]);

        Setting::set('app_name', $validated['app_name']);
        Setting::set('app_timezone', $validated['app_timezone']);
        Setting::set('app_language', $validated['app_language']);
        Setting::set('admin_email', $validated['admin_email'] ?? '');

        $this->syncToAppSetting();

        return back()->with('success', 'Pengaturan umum berhasil disimpan!');
    }

    public function updateAttendance(Request $request)
    {
        $validated = $request->validate([
            'attendance_start_time' => 'required|date_format:H:i',
            'attendance_end_time' => 'required|date_format:H:i|after:attendance_start_time',
            'attendance_late_grace_period' => 'required|integer|min:0|max:60',
            'gps_validation_status' => 'required|in:on,off',
            'qr_expiration' => 'required|integer|in:15,30,45,60',
            'auto_logout' => 'required|in:off,5,10,15,30,60,120',
        ]);

        Setting::set('attendance_start_time', $validated['attendance_start_time']);
        Setting::set('attendance_end_time', $validated['attendance_end_time']);
        Setting::set('attendance_late_grace_period', $validated['attendance_late_grace_period'], 'number');
        Setting::set('gps_validation_status', $validated['gps_validation_status'], 'string');
        Setting::set('qr_expiration', $validated['qr_expiration'], 'number');
        Setting::set('auto_logout', $validated['auto_logout'], 'string');

        $this->syncToAppSetting();

        return back()->with('success', 'Aturan presensi berhasil disimpan!');
    }

    public function updateAppearance(Request $request)
    {
        $validated = $request->validate([
            'primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'app_favicon' => 'nullable|image|mimes:jpeg,png,jpg,ico|max:1024',
        ]);

        // Handle logo upload
        if ($request->hasFile('app_logo')) {
            $oldLogo = Setting::get('app_logo', '');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            
            $path = $request->file('app_logo')->store('settings', 'public');
            Setting::set('app_logo', $path);
        }

        // Handle favicon upload
        if ($request->hasFile('app_favicon')) {
            $oldFavicon = Setting::get('app_favicon', '');
            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }
            
            $path = $request->file('app_favicon')->store('settings', 'public');
            Setting::set('app_favicon', $path);
        }

        Setting::set('primary_color', $validated['primary_color']);
        Setting::set('accent_color', $validated['accent_color']);

        $this->syncToAppSetting();

        return back()->with('success', 'Tampilan berhasil diperbarui!');
    }

    public function updateNotification(Request $request)
    {
        $validated = $request->validate([
            'email_notification' => 'nullable|boolean',
            'late_notification' => 'nullable|boolean',
            'alert_email' => 'nullable|email',
        ]);

        Setting::set('email_notification', $request->has('email_notification'), 'boolean');
        Setting::set('late_notification', $request->has('late_notification'), 'boolean');
        Setting::set('alert_email', $validated['alert_email'] ?? '');

        $this->syncToAppSetting();

        return back()->with('success', 'Pengaturan notifikasi berhasil disimpan!');
    }

    public function updateMaps(Request $request)
    {
        $validated = $request->validate([
            'school_latitude' => 'required|numeric|between:-90,90',
            'school_longitude' => 'required|numeric|between:-180,180',
            'location_radius' => 'nullable|integer|min:10|max:1000',
        ]);

        Setting::set('school_latitude', $validated['school_latitude'], 'number');
        Setting::set('school_longitude', $validated['school_longitude'], 'number');
        Setting::set('location_radius', $validated['location_radius'] ?? 50, 'number');

        $this->syncToAppSetting();

        return back()->with('success', 'Lokasi sekolah berhasil diperbarui!');
    }

    public function updateApk(Request $request)
    {
        $request->validate([
            'apk_file'        => 'nullable|file|mimes:apk|max:102400',
            'apk_name'        => 'nullable|string|max:100',
            'apk_version'     => 'nullable|string|max:20',
            'apk_min_android' => 'nullable|string|max:50',
            'apk_changelog'   => 'nullable|string|max:1000',
        ]);

        try {
            $apkSetting = AppSetting::getInstance();
            $data = [];

            if ($request->hasFile('apk_file')) {
                // Hapus file lama
                if ($apkSetting->apk_file && Storage::disk('public')->exists($apkSetting->apk_file)) {
                    Storage::disk('public')->delete($apkSetting->apk_file);
                }

                $file = $request->file('apk_file');
                $meta = ApkService::extractMetadata($file);
                $path = $file->storeAs('apk', $file->getClientOriginalName(), 'public');

                $data['apk_file']        = $path;
                $data['apk_size']        = $meta['apk_size'];
                $data['apk_uploaded_at'] = now();
                $data['apk_version']     = $meta['apk_version']     ?? $request->input('apk_version', $apkSetting->apk_version);
                $data['apk_min_android'] = $meta['apk_min_android'] ?? $request->input('apk_min_android', $apkSetting->apk_min_android);
                $data['apk_name']        = $request->input('apk_name') ?: ($meta['apk_name'] ?? $apkSetting->apk_name);

                // Simpan juga ke Setting key-value sebagai backup
                Setting::set('apk_file_path', $path);
                Setting::set('apk_name', $data['apk_name'] ?? 'ICB CT Presensi');
                Setting::set('apk_version', $data['apk_version'] ?? '1.0.0');
                Setting::set('apk_min_android', $data['apk_min_android'] ?? 'Android 8.0+');
                Setting::set('apk_size', $meta['apk_size'] ?? 0, 'number');
                Setting::set('apk_download_url', asset('storage/' . $path));
            } else {
                if ($request->filled('apk_name'))        { $data['apk_name'] = $request->apk_name; Setting::set('apk_name', $request->apk_name); }
                if ($request->filled('apk_version'))     { $data['apk_version'] = $request->apk_version; Setting::set('apk_version', $request->apk_version); }
                if ($request->filled('apk_min_android')) { $data['apk_min_android'] = $request->apk_min_android; Setting::set('apk_min_android', $request->apk_min_android); }
            }

            if ($request->filled('apk_changelog')) {
                $data['apk_changelog'] = $request->apk_changelog;
                Setting::set('apk_changelog', $request->apk_changelog);
            }

            if (!empty($data)) {
                $apkSetting->update($data);
            }

        } catch (\Throwable $e) {
            // Fallback: simpan ke Setting key-value saja jika kolom AppSetting belum ada
            if ($request->hasFile('apk_file')) {
                $file = $request->file('apk_file');
                $path = $file->storeAs('apk', $file->getClientOriginalName(), 'public');
                $size = $file->getSize();

                Setting::set('apk_file_path', $path);
                Setting::set('apk_name', $request->input('apk_name', 'ICB CT Presensi'));
                Setting::set('apk_version', $request->input('apk_version', '1.0.0'));
                Setting::set('apk_min_android', $request->input('apk_min_android', 'Android 8.0+'));
                Setting::set('apk_size', $size, 'number');
                Setting::set('apk_download_url', asset('storage/' . $path));
                Setting::set('apk_changelog', $request->input('apk_changelog', ''));
            } else {
                if ($request->filled('apk_name'))        Setting::set('apk_name', $request->apk_name);
                if ($request->filled('apk_version'))     Setting::set('apk_version', $request->apk_version);
                if ($request->filled('apk_min_android')) Setting::set('apk_min_android', $request->apk_min_android);
                if ($request->filled('apk_changelog'))   Setting::set('apk_changelog', $request->apk_changelog);
            }
        }

        return back()->with('success', 'Pengaturan APK berhasil disimpan!')->with('active_tab', 'apk');
    }

    public function deleteApk()
    {
        try {
            $apkSetting = AppSetting::getInstance();
            if ($apkSetting->apk_file && Storage::disk('public')->exists($apkSetting->apk_file)) {
                Storage::disk('public')->delete($apkSetting->apk_file);
            }
            $apkSetting->update([
                'apk_file' => null, 'apk_name' => null, 'apk_version' => null,
                'apk_min_android' => null, 'apk_size' => null,
                'apk_uploaded_at' => null, 'apk_changelog' => null,
            ]);
        } catch (\Throwable $e) {
            // ignore
        }

        // Hapus dari Setting key-value juga
        $path = Setting::get('apk_file_path', '');
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
        foreach (['apk_file_path','apk_name','apk_version','apk_min_android','apk_size','apk_download_url','apk_changelog'] as $key) {
            Setting::set($key, '');
        }

        return back()->with('success', 'APK berhasil dihapus.')->with('active_tab', 'apk');
    }

    public function reset()
    {
        // Reset semua settings ke default
        $defaults = [
            'app_name' => ['value' => 'ICB CT - Absensi Guru', 'type' => 'string'],
            'app_timezone' => ['value' => 'Asia/Jakarta', 'type' => 'string'],
            'app_language' => ['value' => 'id', 'type' => 'string'],
            'admin_email' => ['value' => '', 'type' => 'string'],
            'attendance_start_time' => ['value' => '06:30', 'type' => 'string'],
            'attendance_end_time' => ['value' => '16:00', 'type' => 'string'],
            'attendance_late_grace_period' => ['value' => '5', 'type' => 'number'],
            'gps_validation_status' => ['value' => 'on', 'type' => 'string'],
            'qr_expiration' => ['value' => '30', 'type' => 'number'],
            'auto_logout' => ['value' => 'off', 'type' => 'string'],
            'primary_color' => ['value' => '#0F172A', 'type' => 'string'],
            'accent_color' => ['value' => '#FACC15', 'type' => 'string'],
            'email_notification' => ['value' => '1', 'type' => 'boolean'],
            'late_notification' => ['value' => '1', 'type' => 'boolean'],
            'alert_email' => ['value' => '', 'type' => 'string'],
            'school_latitude' => ['value' => '-6.9142403', 'type' => 'number'],
            'school_longitude' => ['value' => '107.6458618', 'type' => 'number'],
            'location_radius' => ['value' => '50', 'type' => 'number'],
        ];

        foreach ($defaults as $key => $data) {
            Setting::updateOrCreate(
                ['key' => $key],
                $data
            );
        }

        $this->syncToAppSetting();

        return back()->with('success', 'Semua pengaturan berhasil direset ke default!');
    }
}