<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    public function index()
    {
        $appSettings = AppSetting::getInstance();
        
        $settings = [
            'general' => [
                'app_name' => $appSettings->app_name,
                'app_timezone' => $appSettings->app_timezone,
                'app_language' => $appSettings->app_language,
                'admin_email' => $appSettings->admin_email,
            ],
            'attendance' => [
                'attendance_start_time' => $appSettings->attendance_start_time,
                'attendance_end_time' => $appSettings->attendance_end_time,
                'attendance_late_grace_period' => $appSettings->attendance_late_grace_period,
                'location_required' => $appSettings->location_required,
                'photo_required' => $appSettings->photo_required,
                'location_latitude' => $appSettings->location_latitude,
                'location_longitude' => $appSettings->location_longitude,
                'location_radius' => $appSettings->location_radius,
            ],
            'notification' => [
                'email_notification' => $appSettings->email_notification,
                'late_notification' => $appSettings->late_notification,
            ],
            'appearance' => [
                'primary_color' => $appSettings->primary_color,
                'accent_color' => $appSettings->accent_color,
            ],
        ];

        return view('settings.index', compact('appSettings', 'settings'));
    }

    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_timezone' => 'required|string',
            'app_language' => 'required|string|max:10',
            'admin_email' => 'nullable|email|max:255',
        ]);

        $appSettings = AppSetting::getInstance();
        $appSettings->fill($validated);
        $appSettings->save();

        // Update settings table as well
        Setting::set('app_name', $validated['app_name'], 'string', 'general', 'Nama aplikasi');
        Setting::set('app_timezone', $validated['app_timezone'], 'string', 'general', 'Zona waktu');
        Setting::set('app_language', $validated['app_language'], 'string', 'general', 'Bahasa aplikasi');

        return back()->with('success', 'Pengaturan umum berhasil diperbarui!');
    }

    public function updateAttendance(Request $request)
    {
        $validated = $request->validate([
            'attendance_start_time' => 'required',
            'attendance_end_time' => 'required',
            'attendance_late_grace_period' => 'required|integer|min:0|max:120',
            'location_latitude' => 'nullable|numeric|between:-90,90',
            'location_longitude' => 'nullable|numeric|between:-180,180',
            'location_radius' => 'required|integer|min:10|max:5000',
        ]);

        $appSettings = AppSetting::getInstance();
        $appSettings->attendance_start_time = $validated['attendance_start_time'];
        $appSettings->attendance_end_time = $validated['attendance_end_time'];
        $appSettings->attendance_late_grace_period = $validated['attendance_late_grace_period'];
        $appSettings->location_latitude = $validated['location_latitude'] ?? $appSettings->location_latitude;
        $appSettings->location_longitude = $validated['location_longitude'] ?? $appSettings->location_longitude;
        $appSettings->location_radius = $validated['location_radius'];
        
        // Handle booleans from checkbox
        $appSettings->location_required = $request->has('location_required');
        $appSettings->photo_required = $request->has('photo_required');
        
        $appSettings->save();

        // Sync to settings table
        Setting::set('attendance_start_time', $validated['attendance_start_time'], 'string', 'attendance');
        Setting::set('attendance_end_time', $validated['attendance_end_time'], 'string', 'attendance');
        Setting::set('attendance_late_grace_period', $validated['attendance_late_grace_period'], 'integer', 'attendance');

        Artisan::call('config:clear');

        return back()->with('success', 'Pengaturan presensi berhasil diperbarui!');
    }

    public function updateAppearance(Request $request)
    {
        $request->validate([
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'app_favicon' => 'nullable|file|mimes:ico,png,jpg,jpeg|max:512',
            'primary_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $appSettings = AppSetting::getInstance();

        // Handle Logo Upload
        if ($request->hasFile('app_logo')) {
            if ($appSettings->app_logo) {
                Storage::disk('public')->delete($appSettings->app_logo);
            }
            $appSettings->app_logo = $request->file('app_logo')->store('logos', 'public');
        }

        // Handle Favicon Upload
        if ($request->hasFile('app_favicon')) {
            if ($appSettings->app_favicon) {
                Storage::disk('public')->delete($appSettings->app_favicon);
            }
            $appSettings->app_favicon = $request->file('app_favicon')->store('favicons', 'public');
        }

        $appSettings->primary_color = $request->primary_color;
        $appSettings->accent_color = $request->accent_color;
        $appSettings->save();

        return back()->with('success', 'Tampilan aplikasi berhasil diperbarui!');
    }

    public function updateNotification(Request $request)
    {
        $validated = $request->validate([
            'email_notification' => 'boolean',
            'late_notification' => 'boolean',
            'admin_email' => 'nullable|email|max:255',
        ]);

        $appSettings = AppSetting::getInstance();
        $appSettings->update([
            'email_notification' => $request->has('email_notification'),
            'late_notification' => $request->has('late_notification'),
            'admin_email' => $validated['admin_email'] ?? null,
        ]);

        return back()->with('success', 'Pengaturan notifikasi berhasil diperbarui!');
    }

    public function resetSettings()
    {
        AppSetting::query()->delete();
        Setting::query()->delete();
        
        // Recreate default settings
        AppSetting::getInstance();
        
        return back()->with('success', 'Pengaturan berhasil direset ke default!');
    }
}