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
    /**
     * Verifikasi secret key dari URL vs config.
     * Pakai config() bukan env() agar tetap bekerja saat config:cache aktif.
     */
    private function verifySecret(string $secret): bool
    {
        // Harus login + role developer + secret key cocok
        $user = auth()->user();
        if (!$user || !$user->isDeveloper()) {
            abort(403);
        }
        $key = config('app.developer_secret_key', '');
        return $key !== '' && hash_equals($key, $secret);
    }

    // ─────────────────────────────────────────────
    // DASHBOARD
    // ─────────────────────────────────────────────

    public function dashboard(string $secret)
    {
        if (!$this->verifySecret($secret)) abort(404);

        $appSetting = $this->getApkSetting();

        $stats = [
            'total_users'     => User::count(),
            'total_teachers'  => User::where('role', 'guru')->count(),
            'total_operators' => User::whereIn('role', ['admin', 'operator'])->count(),
            'pending_leaves'  => LeaveRequest::where('status', 'pending')->count(),
            'php_version'     => PHP_VERSION,
            'laravel_version' => app()->version(),
            'env'             => config('app.env'),
            'debug'           => config('app.debug'),
            'app_url'         => config('app.url'),
        ];

        $latestUpdate = null;
        try { $latestUpdate = DeveloperUpdate::latest_active(); } catch (\Throwable $e) {}

        $updates = collect();
        try { $updates = DeveloperUpdate::orderByDesc('id')->take(10)->get(); } catch (\Throwable $e) {}

        return view('developer.dashboard', compact('appSetting', 'stats', 'secret', 'latestUpdate', 'updates'));
    }

    // ─────────────────────────────────────────────
    // APK MANAGER
    // ─────────────────────────────────────────────

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
            catch (\Throwable $e) {
                $meta = ['apk_size' => $file->getSize(), 'apk_version' => null, 'apk_min_android' => null, 'apk_name' => null];
            }

            $path = $file->storeAs('apk', $file->getClientOriginalName(), 'public');

            $data = [
                'apk_file'        => $path,
                'apk_size'        => $file->getSize(),
                'apk_uploaded_at' => now(),
                'apk_version'     => $request->input('apk_version') ?: ($meta['apk_version'] ?? '1.0.0'),
                'apk_min_android' => $request->input('apk_min_android') ?: ($meta['apk_min_android'] ?? 'Android 8.0+'),
                'apk_name'        => $request->input('apk_name') ?: ($meta['apk_name'] ?? 'ICB CT Presensi'),
            ];

            Setting::set('apk_file_path',    $path);
            Setting::set('apk_name',         $data['apk_name']);
            Setting::set('apk_version',      $data['apk_version']);
            Setting::set('apk_min_android',  $data['apk_min_android']);
            Setting::set('apk_size',         $data['apk_size'], 'number');
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
            $apkSetting->update([
                'apk_file' => null, 'apk_name' => null, 'apk_version' => null,
                'apk_min_android' => null, 'apk_size' => null,
                'apk_uploaded_at' => null, 'apk_changelog' => null,
            ]);
        } catch (\Throwable $e) {}

        foreach (['apk_file_path', 'apk_name', 'apk_version', 'apk_min_android', 'apk_size', 'apk_download_url', 'apk_changelog'] as $k) {
            Setting::set($k, '');
        }

        return back()->with('success', '✅ APK berhasil dihapus.');
    }

    // ─────────────────────────────────────────────
    // MAINTENANCE
    // ─────────────────────────────────────────────

    public function toggleMaintenance(string $secret, Request $request)
    {
        if (!$this->verifySecret($secret)) abort(404);

        $request->validate([
            'maintenance_mode'    => 'required|boolean',
            'maintenance_message' => 'nullable|string|max:500',
        ]);

        AppSetting::getInstance()->update([
            'maintenance_mode'    => (bool) $request->maintenance_mode,
            'maintenance_message' => $request->maintenance_message,
        ]);

        $status = $request->maintenance_mode ? 'AKTIF' : 'NONAKTIF';
        return back()->with('success', "Mode maintenance sekarang: {$status}");
    }

    // ─────────────────────────────────────────────
    // RELEASES / CHANGELOG
    // ─────────────────────────────────────────────

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
            return back()->with('error', 'Gagal menyimpan update.');
        }

        return back()->with('success', '✅ Update v' . $request->version . ' berhasil ditambahkan!');
    }

    public function deleteUpdate(string $secret, int $id)
    {
        if (!$this->verifySecret($secret)) abort(404);
        try { DeveloperUpdate::findOrFail($id)->delete(); } catch (\Throwable $e) {}
        return back()->with('success', '✅ Update berhasil dihapus.');
    }

    // ─────────────────────────────────────────────
    // SYSTEM TOOLS — semua dikonsolidasi di sini
    // ─────────────────────────────────────────────

    /**
     * Clear semua cache (config, route, view, application cache).
     * Dulu dikenal sebagai "sapu jagat".
     */
    public function clearCache(string $secret)
    {
        if (!$this->verifySecret($secret)) abort(404);

        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        Artisan::call('event:clear');

        return back()->with('success', '🧹 Semua cache berhasil dibersihkan (config, route, view, app cache).');
    }

    /**
     * Jalankan database migration.
     */
    public function migrate(string $secret)
    {
        if (!$this->verifySecret($secret)) abort(404);

        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = trim(Artisan::output()) ?: 'Tidak ada migration baru.';
            // Ambil max 300 char agar tidak membanjiri flash message
            $output = strlen($output) > 300 ? substr($output, 0, 300) . '...' : $output;
        } catch (\Throwable $e) {
            return back()->with('error', '❌ Migration gagal. Cek log server untuk detail.');
        }

        return back()->with('success', '✅ Migration selesai: ' . $output);
    }

    /**
     * Optimize & rebuild semua cache (kebalikan clearCache).
     */
    public function optimize(string $secret)
    {
        if (!$this->verifySecret($secret)) abort(404);

        Artisan::call('optimize');
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');

        return back()->with('success', '⚡ Optimasi selesai — config, route, view sudah di-cache.');
    }

    /**
     * Deploy: git pull → composer → migrate → optimize → clear cache.
     */
    public function deploy(string $secret)
    {
        if (!$this->verifySecret($secret)) abort(404);

        $steps = [];

        // Git pull
        chdir(base_path());
        exec('git stash 2>&1', $stashOut, $stashCode);
        exec('git pull origin main 2>&1', $pullOut, $pullCode);
        if ($pullCode !== 0) {
            return back()->with('error', '❌ Git pull gagal: ' . implode(' ', array_slice($pullOut, -2)));
        }
        $steps[] = '✅ Git pull';

        // Composer
        exec('php composer.phar install --no-dev --optimize-autoloader 2>&1', $composerOut, $composerCode);
        $steps[] = $composerCode === 0 ? '✅ Composer install' : '⚠️ Composer warning';

        // Migrate
        try {
            Artisan::call('migrate', ['--force' => true]);
            $steps[] = '✅ Migrate';
        } catch (\Throwable $e) {
            $steps[] = '⚠️ Migrate skip';
        }

        // Cache
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('optimize');
        $steps[] = '✅ Cache rebuilt';

        return back()->with('success', implode(' → ', $steps));
    }

    // ─────────────────────────────────────────────
    // CARD PREVIEW (support)
    // ─────────────────────────────────────────────

    public function cardPreview(string $secret, ?int $ticketId = null)
    {
        if (!$this->verifySecret($secret)) abort(404);

        if (ob_get_length()) ob_clean();

        try {
            $ticket = $ticketId
                ? \App\Models\SupportTicket::with('user')->find($ticketId)
                : \App\Models\SupportTicket::with('user')->latest()->first();

            if (!$ticket) {
                $ticket = new \App\Models\SupportTicket([
                    'ticket_id'   => 'HD-PREVIEW-001',
                    'type'        => 'question',
                    'title'       => 'Tidak bisa melakukan presensi',
                    'description' => 'QR Code kelas tidak terbaca.',
                    'priority'    => 'critical',
                    'status'      => 'new',
                ]);
                $ticket->id = 0;
                $ticket->setRelation('user', new User(['name' => 'Vexalyn Dev', 'role' => 'guru']));
                $ticket->created_at = now();
            }

            return view('developer.helpdesk-card', compact('ticket'));
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Gagal generate card.'], 500);
        }
    }

    // ─────────────────────────────────────────────
    // PRIVATE HELPER
    // ─────────────────────────────────────────────

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
