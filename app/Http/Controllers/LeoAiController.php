<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassAttendance;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LeoAiController extends Controller
{
    /**
     * Ambil data real-time dari database untuk dijadikan konteks AI.
     */
    private function getRealtimeContext(): string
    {
        try {
            $today    = Carbon::today();
            $todayStr = $today->translatedFormat('l, d F Y');
            $now      = Carbon::now()->format('H:i');

            $totalGuruAktif = User::where('role', 'guru')->where('is_active', true)->count();
            $totalGuruAll   = User::where('role', 'guru')->count();
            $totalGuruTidakAktif = User::where('role', 'guru')->where('is_active', false)->count();

            $hadirCount = Attendance::whereDate('date', $today)
                ->whereIn('status', [
                    User::STATUS_HADIR,
                    User::STATUS_TEPAT_WAKTU,
                    User::STATUS_TERLAMBAT,
                ])->count();

            $terlambatCount  = Attendance::whereDate('date', $today)->where('status', User::STATUS_TERLAMBAT)->count();
            $tepatWaktuCount = Attendance::whereDate('date', $today)->where('status', User::STATUS_TEPAT_WAKTU)->count();
            $alphaCount      = Attendance::whereDate('date', $today)->where('status', User::STATUS_ALPHA)->count();
            $izinCount       = Attendance::whereDate('date', $today)->where('status', User::STATUS_IZIN)->count();
            $sakitCount      = Attendance::whereDate('date', $today)->where('status', User::STATUS_SAKIT)->count();

            $belumPresensiCount = max(0, $totalGuruAktif - $hadirCount - $alphaCount - $izinCount - $sakitCount);

            $sedangMengajar  = ClassAttendance::whereDate('date', $today)->whereNotNull('check_in_time')->whereNull('check_out_time')->count();
            $selesaiMengajar = ClassAttendance::whereDate('date', $today)->whereNotNull('check_in_time')->whereNotNull('check_out_time')->count();
            $totalSesiKelas  = ClassAttendance::whereDate('date', $today)->count();

            $pendingLeaveCount = LeaveRequest::where('status', 'pending')->count();
            $pendingLeaves = LeaveRequest::with('user')
                ->where('status', 'pending')
                ->whereHas('user')
                ->latest()
                ->take(5)
                ->get()
                ->map(fn($l) => "- {$l->user->name} ({$l->type_text}, mulai {$l->start_date->format('d/m/Y')})")
                ->join("\n");

            $recentAttendances = Attendance::with('user')
                ->whereDate('date', $today)
                ->whereHas('user')
                ->whereNotNull('check_in')
                ->latest('check_in')
                ->take(5)
                ->get()
                ->map(fn($a) => "- {$a->user->name} ({$a->status}, masuk {$a->check_in->format('H:i')})")
                ->join("\n");

            $sudahPresensiIds = Attendance::whereDate('date', $today)->pluck('user_id');
            $belumPresensiList = User::where('role', 'guru')
                ->where('is_active', true)
                ->whereNotIn('id', $sudahPresensiIds)
                ->take(10)
                ->pluck('name')
                ->map(fn($n) => "- {$n}")
                ->join("\n");

            $belumPresensiNote = $belumPresensiCount > 10
                ? "\n(dan " . ($belumPresensiCount - 10) . " guru lainnya)"
                : "";

            $izinBulanIni = LeaveRequest::whereMonth('start_date', $today->month)
                ->whereYear('start_date', $today->year)
                ->where('status', 'approved')
                ->count();

            $izinDitolakBulanIni = LeaveRequest::whereMonth('start_date', $today->month)
                ->whereYear('start_date', $today->year)
                ->where('status', 'rejected')
                ->count();

            return <<<CONTEXT
=== DATA REAL-TIME APLIKASI (diperbarui otomatis) ===
Tanggal & Waktu: {$todayStr}, pukul {$now}

STATISTIK KEHADIRAN HARI INI:
- Total guru aktif: {$totalGuruAktif} orang (dari total {$totalGuruAll} guru, {$totalGuruTidakAktif} tidak aktif)
- Sudah hadir: {$hadirCount} guru (tepat waktu: {$tepatWaktuCount}, terlambat: {$terlambatCount})
- Belum presensi: {$belumPresensiCount} guru
- Izin: {$izinCount} guru | Sakit: {$sakitCount} guru | Alpha: {$alphaCount} guru

PRESENSI KELAS HARI INI:
- Sedang mengajar: {$sedangMengajar} sesi | Selesai: {$selesaiMengajar} sesi | Total: {$totalSesiKelas} sesi

PENGAJUAN IZIN/SAKIT:
- Pending: {$pendingLeaveCount} pengajuan
{$pendingLeaves}
- Disetujui bulan ini: {$izinBulanIni} | Ditolak: {$izinDitolakBulanIni}

5 GURU TERAKHIR PRESENSI:
{$recentAttendances}

GURU BELUM PRESENSI (maks 10):
{$belumPresensiList}{$belumPresensiNote}
=== AKHIR DATA REAL-TIME ===
CONTEXT;
        } catch (\Throwable $e) {
            Log::warning('Leo AI: gagal ambil data real-time', ['error' => $e->getMessage()]);
            return '=== DATA REAL-TIME: Tidak tersedia saat ini ===';
        }
    }

    /**
     * System prompt untuk Leo AI.
     */
    private function systemPrompt(): string
    {
        return <<<PROMPT
Kamu adalah Leo AI, asisten cerdas untuk aplikasi Presensi Guru ICB CT milik SMK ICB Cinta Teknika.
Tugasmu adalah membantu operator, admin, guru piket, dan developer dalam menggunakan aplikasi
dan menjawab pertanyaan tentang data kehadiran, izin, serta statistik guru secara real-time.

Kamu akan diberikan DATA REAL-TIME dari database. Gunakan data itu untuk menjawab pertanyaan seperti:
- "Berapa guru yang hadir hari ini?"
- "Siapa saja yang belum presensi?"
- "Ada berapa izin yang pending?"
- dll.

Fitur aplikasi: Dashboard, Live Monitoring, Data Guru, Data Kelas, Mata Pelajaran, Jadwal Kerja,
Jadwal Mengajar, Riwayat Presensi, Manual Presensi Kelas, Izin & Sakit, Laporan Umum,
Laporan Presensi, Kinerja & Analitik, Log Aktivitas, Pusat Bantuan, Kalender Libur, Pengaturan.

Aturan:
- Jawab ramah, sopan, Bahasa Indonesia.
- Gunakan data real-time untuk pertanyaan statistik.
- Tolak pertanyaan di luar konteks aplikasi dengan sopan.
- Jawaban singkat, gunakan poin jika perlu.
- Jangan ekspos data sensitif (password, token, API key).
PROMPT;
    }

    /**
     * Test koneksi ke Leo AI API — khusus developer.
     * Akses: GET /leo-ai/test
     */
    public function testConnection()
    {
        $user = auth()->user();
        if (! $user || $user->role !== 'developer') {
            abort(403, 'Hanya developer yang bisa mengakses halaman ini.');
        }

        $apiKey  = config('services.nubi_ai.api_key');
        $baseUrl = rtrim(config('services.nubi_ai.base_url', 'https://apihub.agnes-ai.com/v1'), '/');
        $model   = config('services.nubi_ai.model', 'agnes-3.0-flash');

        $result = [
            'config' => [
                'api_key_set'    => ! empty($apiKey),
                'api_key_prefix' => ! empty($apiKey) ? substr($apiKey, 0, 8) . '...' : '(kosong)',
                'base_url'       => $baseUrl,
                'model'          => $model,
            ],
            'env_check' => [
                'LEO_AI_API_KEY'  => ! empty(env('LEO_AI_API_KEY')) ? 'SET' : 'TIDAK SET',
                'LEO_AI_BASE_URL' => env('LEO_AI_BASE_URL', '(pakai default)'),
                'LEO_AI_MODEL'    => env('LEO_AI_MODEL', '(pakai default)'),
            ],
        ];

        if (empty($apiKey)) {
            $result['status']  = 'GAGAL';
            $result['message'] = 'API key kosong. Jalankan: php artisan config:clear';
            return response()->json($result, 500);
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(15)
                ->connectTimeout(8)
                ->post("{$baseUrl}/chat/completions", [
                    'model'      => $model,
                    'messages'   => [['role' => 'user', 'content' => 'ping']],
                    'max_tokens' => 10,
                ]);

            $result['http_status'] = $response->status();
            $result['response']    = $response->json();

            if ($response->successful()) {
                $reply = data_get($response->json(), 'choices.0.message.content', '');
                $result['status']  = 'OK';
                $result['message'] = 'Koneksi berhasil! Reply: ' . $reply;
            } else {
                $result['status']  = 'GAGAL';
                $result['message'] = 'API mengembalikan status ' . $response->status();
            }
        } catch (\Throwable $e) {
            $result['status']    = 'ERROR';
            $result['message']   = $e->getMessage();
            $result['exception'] = get_class($e);
        }

        return response()->json($result, $result['status'] === 'OK' ? 200 : 500);
    }

    /**
     * Handle pesan chat dari user ke Leo AI.
     */
    public function chat(Request $request)
    {
        $user = auth()->user();
        $allowedRoles = ['admin', 'operator', 'guru_piket', 'developer'];
        if (! $user || ! in_array($user->role, $allowedRoles, true)) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'message'           => 'required|string|max:1000',
            'history'           => 'nullable|array|max:10',
            'history.*.role'    => 'required|in:user,assistant',
            'history.*.content' => 'required|string|max:2000',
        ]);

        $apiKey  = config('services.nubi_ai.api_key');
        $baseUrl = rtrim(config('services.nubi_ai.base_url', 'https://apihub.agnes-ai.com/v1'), '/');
        $model   = config('services.nubi_ai.model', 'agnes-3.0-flash');

        if (empty($apiKey)) {
            Log::error('Leo AI: API key kosong. Set LEO_AI_API_KEY di .env dan jalankan config:clear.');
            return response()->json(['error' => 'Leo AI belum dikonfigurasi. Hubungi administrator.'], 500);
        }

        $history = collect($request->input('history', []))
            ->takeLast(10)
            ->map(fn($h) => ['role' => $h['role'], 'content' => $h['content']])
            ->values()
            ->toArray();

        $messages = array_merge(
            [['role' => 'system', 'content' => $this->systemPrompt() . "\n\n" . $this->getRealtimeContext()]],
            $history,
            [['role' => 'user', 'content' => $request->input('message')]]
        );

        try {
            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->connectTimeout(10)
                ->post("{$baseUrl}/chat/completions", [
                    'model'       => $model,
                    'messages'    => $messages,
                    'max_tokens'  => 700,
                    'temperature' => 0.5,
                ]);

            if ($response->failed()) {
                Log::warning('Leo AI API error', [
                    'status'         => $response->status(),
                    'body'           => $response->json() ?? [],
                    'api_key_prefix' => substr($apiKey, 0, 8) . '...',
                ]);

                $msg = match ($response->status()) {
                    401     => 'Leo AI: API key tidak valid. Hubungi administrator.',
                    402     => 'Leo AI: Kuota API habis. Hubungi administrator.',
                    429     => 'Leo AI sedang sibuk. Tunggu sebentar lalu coba lagi.',
                    default => 'Leo AI sedang tidak tersedia. Silakan coba lagi.',
                };

                return response()->json(['error' => $msg], 503);
            }

            $reply = data_get($response->json(), 'choices.0.message.content');
            if (! is_string($reply) || trim($reply) === '') {
                Log::warning('Leo AI: invalid response', ['body' => $response->json()]);
                return response()->json(['error' => 'Leo AI mengirim jawaban yang tidak valid. Silakan coba lagi.'], 503);
            }

            return response()->json(['reply' => $reply]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Leo AI connection failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Koneksi ke Leo AI gagal. Periksa koneksi internet dan coba lagi.'], 503);
        }
    }
}
