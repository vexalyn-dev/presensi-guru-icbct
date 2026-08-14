<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Setting;
use App\Models\User;
use App\Models\LeaveRequest;
use App\Models\DeveloperUpdate;
use App\Services\ApkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class DeveloperController extends Controller
{
    const SECRET_KEY = 'vexalyn-dev-2026';

    private function verifySecret(string $secret): bool
    {
        return $secret === self::SECRET_KEY;
    }

    public function dashboard(string $secret)
    {
        if (!$this->verifySecret($secret)) abort(404);

        $appSetting = $this->getApkSetting();

        $stats = [
            'total_users'    => User::count(),
            'total_teachers' => User::where('role', 'guru')->count(),
            'total_operators'=> User::whereIn('role', ['admin','operator'])->count(),
            'pending_leaves' => LeaveRequest::where('status','pending')->count(),
            'php_version'    => PHP_VERSION,
            'laravel_version'=> app()->version(),
            'env'            => config('app.env'),
            'debug'          => config('app.debug'),
            'app_url'        => config('app.url'),
        ];

        $latestUpdate = null;
        try {
            $latestUpdate = DeveloperUpdate::latest_active();
        } catch (\Throwable $e) {}

        $updates = [];
        try {
            $updates = DeveloperUpdate::orderByDesc('id')->take(10)->get();
        } catch (\Throwable $e) {}

        return view('developer.dashboard', compact('appSetting', 'stats', 'secret', 'latestUpdate', 'updates'));
    }

    public function updateApk(string $secret, Request $request)
    {
        if (!$this->verifySecret($secret)) abort(404);

        $request->validate([
            'apk_file'        => 'nullable|file|max:102400',
            'apk_name'        => 'nullable|string|max:100',
            'apk_version'     => 'nullable|string|max:20',
            'apk_min_android' => 'nullable|string|max:50',
            'apk_changelog'   => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('apk_file')) {
            $ext = strtolower($request->file('apk_file')->getClientOriginalExtension());
            if ($ext !== 'apk') {
                return back()->withErrors(['apk_file' => 'File harus berekstensi .apk']);
            }
        }

        $apkSetting = $this->getApkSetting();
        $data = [];

        if ($request->hasFile('apk_file')) {
            if ($apkSetting->apk_file && Storage::disk('public')->exists($apkSetting->apk_file)) {
                Storage::disk('public')->delete($apkSetting->apk_file);
            }

            $file = $request->file('apk_file');
            try { $meta = ApkService::extractMetadata($file); }
            catch (\Throwable $e) { $meta = ['apk_size' => $file->getSize(), 'apk_version' => null, 'apk_min_android' => null, 'apk_name' => null]; }

            $path = $file->storeAs('apk', $file->getClientOriginalName(), 'public');

            $data = [
                'apk_file'        => $path,
                'apk_size'        => $file->getSize(),
                'apk_uploaded_at' => now(),
                'apk_version'     => $request->input('apk_version') ?: ($meta['apk_version'] ?? '1.0.0'),
                'apk_min_android' => $request->input('apk_min_android') ?: ($meta['apk_min_android'] ?? 'Android 8.0+'),
                'apk_name'        => $request->input('apk_name') ?: ($meta['apk_name'] ?? 'ICB CT Presensi'),
            ];

            Setting::set('apk_file_path', $path);
            Setting::set('apk_name',        $data['apk_name']);
            Setting::set('apk_version',     $data['apk_version']);
            Setting::set('apk_min_android', $data['apk_min_android']);
            Setting::set('apk_size',        $data['apk_size'], 'number');
            Setting::set('apk_download_url', asset('storage/' . $path));
        } else {
            if ($request->filled('apk_name'))        { $data['apk_name']        = $request->apk_name;        Setting::set('apk_name',        $request->apk_name); }
            if ($request->filled('apk_version'))     { $data['apk_version']     = $request->apk_version;     Setting::set('apk_version',     $request->apk_version); }
            if ($request->filled('apk_min_android')) { $data['apk_min_android'] = $request->apk_min_android; Setting::set('apk_min_android', $request->apk_min_android); }
        }

        if ($request->filled('apk_changelog')) {
            $data['apk_changelog'] = $request->apk_changelog;
            Setting::set('apk_changelog', $request->apk_changelog);
        }

        if (!empty($data)) {
            try { $apkSetting->update($data); } catch (\Throwable $e) {}
        }

        return back()->with('success', '✅ APK berhasil disimpan!');
    }

    public function deleteApk(string $secret)
    {
        if (!$this->verifySecret($secret)) abort(404);

        $apkSetting = $this->getApkSetting();
        try {
            if ($apkSetting->apk_file && Storage::disk('public')->exists($apkSetting->apk_file)) {
                Storage::disk('public')->delete($apkSetting->apk_file);
            }
            $apkSetting->update(['apk_file'=>null,'apk_name'=>null,'apk_version'=>null,'apk_min_android'=>null,'apk_size'=>null,'apk_uploaded_at'=>null,'apk_changelog'=>null]);
        } catch (\Throwable $e) {}

        foreach (['apk_file_path','apk_name','apk_version','apk_min_android','apk_size','apk_download_url','apk_changelog'] as $k) {
            Setting::set($k, '');
        }

        return back()->with('success', '✅ APK berhasil dihapus.');
    }

    public function toggleMaintenance(string $secret, Request $request)
    {
        if (!$this->verifySecret($secret)) abort(404);

        $request->validate([
            'maintenance_mode'    => 'required|boolean',
            'maintenance_message' => 'nullable|string|max:500',
        ]);

        $setting = AppSetting::getInstance();
        $setting->update([
            'maintenance_mode'    => (bool) $request->maintenance_mode,
            'maintenance_message' => $request->maintenance_message,
        ]);

        $status = $request->maintenance_mode ? 'AKTIF' : 'NONAKTIF';
        return back()->with('success', "Mode maintenance sekarang: {$status}");
    }

    public function storeUpdate(string $secret, Request $request)
    {
        if (!$this->verifySecret($secret)) abort(404);

        $request->validate([
            'version'    => 'required|string|max:20',
            'title'      => 'required|string|max:200',
            'content'    => 'required|string|max:3000',
            'type'       => 'required|in:feature,fix,update,hotfix',
            'show_modal' => 'nullable|boolean',
        ]);

        try {
            DeveloperUpdate::create([
                'version'    => $request->version,
                'title'      => $request->title,
                'content'    => $request->input('content'),
                'type'       => $request->type,
                'show_modal' => $request->boolean('show_modal', true),
                'is_active'  => true,
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menyimpan update: ' . $e->getMessage());
        }

        return back()->with('success', '✅ Update v' . $request->version . ' berhasil ditambahkan!');
    }

    public function deleteUpdate(string $secret, int $id)
    {
        if (!$this->verifySecret($secret)) abort(404);
        try { DeveloperUpdate::findOrFail($id)->delete(); } catch (\Throwable $e) {}
        return back()->with('success', '✅ Update berhasil dihapus.');
    }

    public function cardPreview(string $secret, ?int $ticketId = null)
    {
        if (!$this->verifySecret($secret)) abort(404);

        if (ob_get_length()) {
            ob_clean();
        }

        try {
            $ticket = null;
            if ($ticketId) {
                $ticket = \App\Models\SupportTicket::with('user')->find($ticketId);
            } else {
                $ticket = \App\Models\SupportTicket::with('user')->latest()->first();
            }

            if (!$ticket) {
                // Buat dummy ticket untuk preview
                $ticket = new \App\Models\SupportTicket([
                    'ticket_id'   => 'HD-PREVIEW-001',
                    'type'        => 'question',
                    'title'       => 'Tidak bisa melakukan presensi',
                    'description' => 'Saya tidak bisa melakukan presensi karena QR Code kelas tidak terbaca. Sudah dicoba beberapa kali tapi tetap gagal.',
                    'priority'    => 'critical',
                    'status'      => 'new',
                ]);
                $ticket->id = 0;
                $ticket->setRelation('user', new \App\Models\User([
                    'name' => 'Vexalyn Dev',
                    'role' => 'guru'
                ]));
                $ticket->created_at = now();
            }

            return view('developer.helpdesk-card', compact('ticket'));

        } catch (\Throwable $e) {
            return response('Error: ' . $e->getMessage(), 500);
        }
        } catch (\Throwable $e) {
            return response()->json([
                'error'   => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'message' => 'Gagal generate card. Hubungi tim developer.'
            ], 500);
        }
    }

    public function clearCache(string $secret)
    {
        if (!$this->verifySecret($secret)) abort(404);
        Artisan::call('optimize:clear');
        return back()->with('success', ' Cache berhasil dibersihkan.');
    }

    private function getApkSetting(): AppSetting
    {
        $s = AppSetting::getInstance();
        $hasCol = Schema::hasColumn('app_settings', 'apk_file');
        if (!$hasCol || (!$s->apk_file && Setting::get('apk_file_path'))) {
            $s->apk_file        = Setting::get('apk_file_path');
            $s->apk_name        = Setting::get('apk_name');
            $s->apk_version     = Setting::get('apk_version');
            $s->apk_min_android = Setting::get('apk_min_android');
            $s->apk_size        = (int) Setting::get('apk_size', 0);
            $s->apk_changelog   = Setting::get('apk_changelog');
        }
        return $s;
    }
}
