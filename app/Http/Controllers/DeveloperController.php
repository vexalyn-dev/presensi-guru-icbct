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
            'upload_max'      => get_cfg_var('upload_max_filesize') ?: 'N/A',
            'post_max'        => get_cfg_var('post_max_size') ?: 'N/A',
            'memory_limit'    => get_cfg_var('memory_limit') ?: 'N/A',
            'max_exec_time'   => get_cfg_var('max_execution_time') ?: 'N/A',
            'disk_total'      => disk_total_space(storage_path()),
            'disk_free'       => disk_free_space(storage_path()),
            'disk_percent'    => disk_total_space(storage_path()) > 0
                ? round((1 - disk_free_space(storage_path()) / disk_total_space(storage_path())) * 100, 1)
                : 0,
            'cache_config'    => file_exists(base_path('bootstrap/cache/config.php')) ? 'CACHED' : 'NOT',
            'cache_route'     => file_exists(base_path('bootstrap/cache/routes-v7.php')) ? 'CACHED' : 'NOT',
            'cache_view'      => count(glob(storage_path('framework/views/*.php')) ?: []) > 0 ? 'CACHED' : 'NOT',
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'server_port'     => $_SERVER['SERVER_PORT'] ?? 'N/A',
            'server_addr'     => $_SERVER['SERVER_ADDR'] ?? 'N/A',
            'remote_addr'     => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
            'db_driver'       => config('database.default'),
            'db_host'         => config('database.connections.' . config('database.default') . '.host') ?: 'N/A',
            'db_name'         => config('database.connections.' . config('database.default') . '.database') ?: 'N/A',
            'db_user'         => config('database.connections.' . config('database.default') . '.username') ?: 'N/A',
            'session_driver'  => config('session.driver', 'file'),
            'session_lifetime'=> config('session.lifetime', 120),
            'timezone'        => config('app.timezone', 'UTC'),
        ];

        $latestUpdate = null;
        try { $latestUpdate = DeveloperUpdate::latest_active(); } catch (\Throwable $e) {}

        $updates = collect();
        try { $updates = DeveloperUpdate::orderByDesc('id')->take(10)->get(); } catch (\Throwable $e) {}

        return view('developer.dashboard', compact('appSetting', 'stats', 'secret', 'latestUpdate', 'updates'));
    }

    public function serverInfo(string $secret)
    {
        if (!$this->verifySecret($secret)) abort(404);

        $phpIni = php_ini_loaded_file() ?? 'Tidak ditemukan';
        $uploadMax = get_cfg_var('upload_max_filesize') ?? 'N/A';
        $postMax = get_cfg_var('post_max_size') ?? 'N/A';
        $memoryLimit = get_cfg_var('memory_limit') ?? 'N/A';
        $maxExecTime = get_cfg_var('max_execution_time') ?? 'N/A';

        $diskTotal = disk_total_space(storage_path());
        $diskFree  = disk_free_space(storage_path());
        $diskUsed  = $diskTotal - $diskFree;
        $diskPercent = $diskTotal > 0 ? round(($diskUsed / $diskTotal) * 100, 1) : 0;

        $cacheStatus = [
            'config'  => file_exists(base_path('bootstrap/cache/config.php')) ? 'CACHED' : 'NOT CACHED',
            'route'   => file_exists(base_path('bootstrap/cache/routes-v7.php')) ? 'CACHED' : 'NOT CACHED',
            'view'    => count(glob(storage_path('framework/views/*.php')) ?: []) > 0 ? 'CACHED' : 'NOT CACHED',
            'events'  => file_exists(base_path('bootstrap/cache/events.php')) ? 'CACHED' : 'NOT CACHED',
        ];

        $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown');
        $serverPort = $_SERVER['SERVER_PORT'] ?? 'N/A';
        $serverProtocol = $_SERVER['SERVER_PROTOCOL'] ?? 'N/A';
        $serverAddr = $_SERVER['SERVER_ADDR'] ?? 'N/A';
        $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? 'N/A';

        $pdoDsn = config('database.connections.' . config('database.default') . '.dsn', 'N/A');
        $dbHost = config('database.connections.' . config('database.default') . '.host', 'N/A');
        $dbName = config('database.connections.' . config('database.default') . '.database', 'N/A');
        $dbUser = config('database.connections.' . config('database.default') . '.username', 'N/A');

        try {
            $dbConnected = \DB::connection()->getPdo() !== null;
        } catch (\Throwable $e) {
            $dbConnected = false;
        }

        $phpExtensions = phpinfo(INFO_MODULES);
        preg_match_all('/<tr[^>]*><td[^>]*>([A-Za-z_]+)<\/td><td[^>]*>([^<]+)<\/td>/i', $phpExtensions, $extMatches);
        $extList = [];
        foreach ($extMatches[1] as $i => $name) {
            $extList[] = $name;
        }
        sort($extList);

        $sessionDriver = config('session.driver', 'file');
        $sessionLifetime = config('session.lifetime', 120);
        $sessionPath = config('session.path', '/tmp');

        $laravelVersion = app()->version();
        $timezone = config('app.timezone', 'UTC');

        return view('developer.server-info', compact(
            'secret', 'phpVersion', 'phpIni', 'uploadMax', 'postMax', 'memoryLimit', 'maxExecTime',
            'diskTotal', 'diskFree', 'diskUsed', 'diskPercent', 'cacheStatus',
            'serverSoftware', 'serverPort', 'serverProtocol', 'serverAddr', 'remoteAddr',
            'pdoDsn', 'dbHost', 'dbName', 'dbUser', 'dbConnected',
            'extList', 'sessionDriver', 'sessionLifetime', 'sessionPath',
            'laravelVersion', 'timezone'
        ));
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
            try {
                $apkSetting->update($data);
            } catch (\Throwable $e) {
                \Log::error('APK update failed: ' . $e->getMessage());
                return back()->with('error', '❌ Gagal menyimpan metadata APK: ' . substr($e->getMessage(), 0, 100));
            }
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
    // DEVELOPER PROFILE
    // ─────────────────────────────────────────────

    public function updateProfile(string $secret, Request $request)
    {
        if (!$this->verifySecret($secret)) abort(404);

        $user = auth()->user();

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? $user->phone,
        ];

        if ($request->hasFile('photo')) {
            // Hapus foto lama
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $path = $request->file('photo')->store('profiles', 'public');
            $data['photo'] = $path;
        }

        $user->update($data);

        return back()->with('success', '✅ Profil berhasil diperbarui!')->with('active_tab_dev', 'profile');
    }

    public function updatePassword(string $secret, Request $request)
    {
        if (!$this->verifySecret($secret)) abort(404);

        $request->validate([
            'current_password'      => 'required',
            'password'              => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $user = auth()->user();

        if (!\Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Password lama tidak sesuai.'])
                ->with('active_tab_dev', 'profile');
        }

        $user->update(['password' => \Hash::make($request->password)]);

        return back()->with('success', '✅ Password berhasil diubah!')->with('active_tab_dev', 'profile');
    }

    // ─────────────────────────────────────────────
    // iOS MANAGER
    // ─────────────────────────────────────────────

    public function updateIos(string $secret, Request $request)
    {
        if (!$this->verifySecret($secret)) abort(404);

        $request->validate([
            'ios_file'        => 'nullable|file|max:204800', // max 200MB (IPA lebih besar dari APK)
            'ios_name'        => 'nullable|string|max:100',
            'ios_version'     => 'nullable|string|max:20',
            'ios_min_version' => 'nullable|string|max:50',
            'ios_changelog'   => 'nullable|string|max:1000',
        ]);

        if ($request->hasFile('ios_file')) {
            $ext = strtolower($request->file('ios_file')->getClientOriginalExtension());
            if (!in_array($ext, ['ipa', 'zip'])) {
                return back()->withErrors(['ios_file' => 'File harus berekstensi .ipa atau .zip']);
            }
        }

        $setting = AppSetting::getInstance();
        $data    = [];

        if ($request->hasFile('ios_file')) {
            // Hapus file lama
            if ($setting->ios_file && Storage::disk('public')->exists($setting->ios_file)) {
                Storage::disk('public')->delete($setting->ios_file);
            }

            $file = $request->file('ios_file');
            $path = $file->storeAs('ios', $file->getClientOriginalName(), 'public');

            $data = [
                'ios_file'        => $path,
                'ios_size'        => $file->getSize(),
                'ios_uploaded_at' => now(),
                'ios_version'     => $request->input('ios_version') ?: '1.0.0',
                'ios_min_version' => $request->input('ios_min_version') ?: 'iOS 14.0+',
                'ios_name'        => $request->input('ios_name') ?: ($setting->apk_name ?? 'ICB CT Presensi'),
            ];

            Setting::set('ios_file_path',    $path);
            Setting::set('ios_name',         $data['ios_name']);
            Setting::set('ios_version',      $data['ios_version']);
            Setting::set('ios_min_version',  $data['ios_min_version']);
            Setting::set('ios_size',         $data['ios_size'], 'number');
            Setting::set('ios_download_url', asset('storage/' . $path));
        } else {
            if ($request->filled('ios_name'))        { $data['ios_name']        = $request->ios_name;        Setting::set('ios_name',        $request->ios_name); }
            if ($request->filled('ios_version'))     { $data['ios_version']     = $request->ios_version;     Setting::set('ios_version',     $request->ios_version); }
            if ($request->filled('ios_min_version')) { $data['ios_min_version'] = $request->ios_min_version; Setting::set('ios_min_version', $request->ios_min_version); }
        }

        if ($request->filled('ios_changelog')) {
            $data['ios_changelog'] = $request->ios_changelog;
            Setting::set('ios_changelog', $request->ios_changelog);
        }

        if (!empty($data)) {
            try {
                $setting->update($data);
            } catch (\Throwable $e) {
                \Log::error('iOS update failed: ' . $e->getMessage());
                return back()->with('error', '❌ Gagal menyimpan metadata iOS: ' . substr($e->getMessage(), 0, 100));
            }
        }

        return back()->with('success', '✅ iOS berhasil disimpan!');
    }

    public function deleteIos(string $secret)
    {
        if (!$this->verifySecret($secret)) abort(404);

        $setting = AppSetting::getInstance();
        try {
            if ($setting->ios_file && Storage::disk('public')->exists($setting->ios_file)) {
                Storage::disk('public')->delete($setting->ios_file);
            }
            $setting->update([
                'ios_file' => null, 'ios_name' => null, 'ios_version' => null,
                'ios_min_version' => null, 'ios_size' => null,
                'ios_uploaded_at' => null, 'ios_changelog' => null,
            ]);
        } catch (\Throwable $e) {}

        foreach (['ios_file_path', 'ios_name', 'ios_version', 'ios_min_version', 'ios_size', 'ios_download_url', 'ios_changelog'] as $k) {
            Setting::set($k, '');
        }

        return back()->with('success', '✅ iOS berhasil dihapus.');
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
     */
    public function clearCache(string $secret)
    {
        if (!$this->verifySecret($secret)) abort(404);

        // Simpan session sebelum clear, urutan: view & route dulu, config terakhir
        // supaya session terenkripsi tidak putus di tengah jalan
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('event:clear');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');  // config paling akhir

        return redirect()->route('developer.dashboard', $secret)
            ->with('success', '🧹 Semua cache berhasil dibersihkan (config, route, view, app cache).');
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

        // config:cache duluan agar APP_KEY terbaca sebelum route/view di-cache
        Artisan::call('config:cache');
        Artisan::call('optimize');
        Artisan::call('route:cache');
        Artisan::call('view:cache');

        return redirect()->route('developer.dashboard', $secret)
            ->with('success', '⚡ Optimasi selesai — config, route, view sudah di-cache.');
    }

    /**
     * Toggle APP_DEBUG via env file edit + config cache rebuild.
     */
    public function toggleDebug(string $secret, Request $request)
    {
        if (!$this->verifySecret($secret)) abort(404);

        $target = (bool) $request->input('debug');
        $envPath = base_path('.env');

        try {
            $envContent = file_get_contents($envPath);
            $envContent = preg_replace(
                '/^APP_DEBUG=.*/m',
                'APP_DEBUG=' . ($target ? 'true' : 'false'),
                $envContent
            );
            file_put_contents($envPath, $envContent);
        } catch (\Throwable $e) {
            return back()->with('error', '❌ Gagal menulis .env: ' . $e->getMessage());
        }

        try {
            Artisan::call('config:clear');
            Artisan::call('config:cache');
            Artisan::call('view:clear');
            Artisan::call('route:clear');
        } catch (\Throwable $e) {
            return back()->with('error', '❌ Gagal rebuild cache: ' . $e->getMessage());
        }

        return back()->with('success', $target
            ? '⚠️ Debug mode AKTIFkan — refresh halaman untuk melihat perubahan.'
            : '✅ Debug mode MATIKAN — refresh halaman untuk melihat perubahan.');
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
        return AppSetting::getInstance();
    }

    /**
     * Public endpoint: jalankan migration dengan verifikasi secret key.
     * GET /run-migrate-secret?key={secret}
     */
    public function runMigrateSecret(Request $request)
    {
        $key = (string) $request->input('key', '');
        $developerKey = config('app.developer_secret_key', '');
        $deployKey    = config('app.deploy_secret_key', '');
        $allowed = $developerKey !== '' && hash_equals($developerKey, $key);
        if (!$allowed && $deployKey !== '' && hash_equals($deployKey, $key)) {
            $allowed = true;
        }
        if (!$allowed) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = trim(Artisan::output());
            return response()->json([
                'success' => true,
                'message' => 'Migration selesai.',
                'output'  => $output ?: 'Tidak ada migration baru.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error'   => 'Migration gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Public endpoint: clear semua cache & compiled views via secret key.
     * GET /clear-all-cache?key={secret}
     */
    public function clearAllCacheSecret(Request $request)
    {
        $key = (string) $request->input('key', '');
        $developerKey = config('app.developer_secret_key', '');
        $deployKey    = config('app.deploy_secret_key', '');
        $allowed = $developerKey !== '' && hash_equals($developerKey, $key);
        if (!$allowed && $deployKey !== '' && hash_equals($deployKey, $key)) {
            $allowed = true;
        }
        if (!$allowed) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        Artisan::call('view:clear');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('event:clear');

        $viewsDir = storage_path('framework/views');
        if (is_dir($viewsDir)) {
            $files = glob($viewsDir . '/*.php');
            foreach ($files as $file) { @unlink($file); }
        }

        return response()->json([
            'success' => true,
            'message' => 'Semua cache & compiled views berhasil dibersihkan.',
        ]);
    }

    /**
     * Public endpoint: jalankan database seeder.
     * GET /run-seeder?key={secret}
     */
    public function runSeeder(Request $request)
    {
        $key = (string) $request->input('key', '');
        $developerKey = config('app.developer_secret_key', '');
        $deployKey    = config('app.deploy_secret_key', '');
        $allowed = $developerKey !== '' && hash_equals($developerKey, $key);
        if (!$allowed && $deployKey !== '' && hash_equals($deployKey, $key)) {
            $allowed = true;
        }
        if (!$allowed) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            Artisan::call('db:seed', ['--force' => true]);
            $output = trim(Artisan::output());
            // Clear route & view cache so new routes take effect immediately
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            return response()->json([
                'success' => true,
                'message' => 'Seeder selesai + cache dibersihkan.',
                'output'  => $output ?: 'Database berhasil di-seed.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error'   => 'Seeder gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Run seeder via dev-panel (authenticated).
     */
    public function runSeederPanel(string $secret)
    {
        Artisan::call('db:seed', ['--force' => true]);
        $output = trim(Artisan::output());
        return redirect()->back()->with('success', 'Seeder berhasil dijalankan.' . ($output ? ' Output: ' . $output : ''));
    }

    /**
     * Clear route cache — untuk register route baru tanpa deploy penuh.
     */
    public function clearRoutes(string $secret)
    {
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        return redirect()->back()->with('success', '✅ Route & view cache dibersihkan. Refresh halaman.');
    }
}
