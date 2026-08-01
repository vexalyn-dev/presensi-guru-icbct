<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class MaintenanceModeController extends Controller
{
    public function toggle(Request $request)
    {
        $setting = AppSetting::getInstance();
        $request->validate([
            'maintenance_mode'    => 'required|boolean',
            'maintenance_message' => 'nullable|string|max:500',
        ]);

        $setting->update([
            'maintenance_mode'    => (bool) $request->maintenance_mode,
            'maintenance_message' => $request->maintenance_message,
        ]);

        $status = $request->maintenance_mode ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Mode maintenance berhasil {$status}.");
    }
}
