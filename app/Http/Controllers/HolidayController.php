<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::orderBy('date', 'desc')->paginate(20);
        
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $upcomingHolidays = Holiday::whereYear('date', $currentYear)
            ->whereMonth('date', '>=', $currentMonth)
            ->orderBy('date')
            ->take(10)
            ->get();
        
        return view('holidays.index', compact('holidays', 'upcomingHolidays'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'name' => 'required|string|max:255',
            'type' => 'required|in:national,school',
            'is_recurring' => 'boolean',
        ]);

        Holiday::create($validated);

        return back()->with('success', 'Hari libur berhasil ditambahkan');
    }

    public function update(Request $request, Holiday $holiday)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'name' => 'required|string|max:255',
            'type' => 'required|in:national,school',
            'is_recurring' => 'boolean',
        ]);

        $holiday->update($validated);

        return back()->with('success', 'Hari libur berhasil diupdate');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return back()->with('success', 'Hari libur berhasil dihapus');
    }

    public function fetchNationalHolidays()
    {
        try {
            $year = Carbon::now()->year;

            // ── Strategi: coba 3 endpoint kemendesa.link secara berurutan ──

            $holidays = null;
            $errorMsg = '';

            // 1. Coba endpoint per tahun (paling spesifik)
            try {
                $res = Http::timeout(15)
                    ->withHeaders(['Accept' => 'application/json'])
                    ->get("https://api.kemendesa.link/libur-nasional/api/holidays/{$year}.json");

                if ($res->successful()) {
                    $body = $res->json();
                    // Format: { "data": [...] } atau langsung array
                    if (isset($body['data']) && is_array($body['data'])) {
                        $holidays = $body['data'];
                    } elseif (is_array($body) && !empty($body)) {
                        $holidays = $body;
                    }
                }
            } catch (\Exception $e) {
                $errorMsg .= 'yearly: ' . $e->getMessage() . '; ';
            }

            // 2. Fallback: endpoint latest
            if (empty($holidays)) {
                try {
                    $res = Http::timeout(15)
                        ->withHeaders(['Accept' => 'application/json'])
                        ->get('https://api.kemendesa.link/libur-nasional/api/holidays/latest');

                    if ($res->successful()) {
                        $body = $res->json();
                        if (isset($body['data']) && is_array($body['data'])) {
                            $holidays = $body['data'];
                        } elseif (is_array($body) && !empty($body)) {
                            $holidays = $body;
                        }
                    }
                } catch (\Exception $e) {
                    $errorMsg .= 'latest: ' . $e->getMessage() . '; ';
                }
            }

            if (empty($holidays)) {
                \Log::warning('fetchNationalHolidays: semua endpoint gagal. ' . $errorMsg);
                return back()->with('error', 'Gagal mengambil data libur dari API. Coba lagi nanti.');
            }

            $count = 0;
            foreach ($holidays as $holiday) {
                try {
                    // Normalisasi field — kemendesa.link bisa pakai 'date'/'tanggal' dan 'name'/'nama'/'holiday_name'
                    $date = $holiday['date']         ?? $holiday['tanggal']      ?? null;
                    $name = $holiday['name']         ?? $holiday['holiday_name'] ?? $holiday['nama'] ?? null;

                    if (!$date || !$name) continue;

                    $parsedDate = Carbon::parse($date)->toDateString();

                    // Validasi tahun — pastikan hanya tahun yang diminta
                    if (Carbon::parse($parsedDate)->year !== $year) continue;

                    $exists = Holiday::where('date', $parsedDate)->where('type', 'national')->exists();
                    if (!$exists) {
                        Holiday::create([
                            'date'         => $parsedDate,
                            'name'         => $name,
                            'type'         => 'national',
                            'is_recurring' => false,
                        ]);
                        $count++;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            if ($count > 0) {
                return back()->with('success', "Berhasil menambahkan {$count} hari libur nasional tahun {$year}.");
            }

            $existingCount = Holiday::where('type', 'national')->whereYear('date', $year)->count();
            if ($existingCount > 0) {
                return back()->with('success', "Data libur nasional {$year} sudah ada ({$existingCount} hari). Tidak ada data baru.");
            }

            return back()->with('error', 'Tidak ada data libur baru yang bisa ditambahkan.');

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return back()->with('error', 'Koneksi ke API gagal. Periksa koneksi internet server.');
        } catch (\Exception $e) {
            \Log::error('fetchNationalHolidays error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan: ' . substr($e->getMessage(), 0, 100));
        }
    }

    /**
     * Cek apakah sebuah tanggal merupakan hari libur via API kemendesa.link
     * Endpoint: GET /api/is-holiday?date=2026-08-17
     */
    public function checkIsHoliday(Request $request)
    {
        $date = $request->input('date', now()->toDateString());

        try {
            $validated = Carbon::parse($date)->toDateString();

            // Cek DB lokal dulu
            $localHoliday = Holiday::where('date', $validated)->first();
            if ($localHoliday) {
                return response()->json([
                    'is_holiday' => true,
                    'source'     => 'local',
                    'name'       => $localHoliday->name,
                    'date'       => $validated,
                ]);
            }

            // Cek via API kemendesa.link
            $res = Http::timeout(10)
                ->withHeaders(['Accept' => 'application/json'])
                ->get('https://api.kemendesa.link/libur-nasional/api/is-holiday', [
                    'date' => $validated,
                ]);

            if ($res->successful()) {
                $body = $res->json();
                return response()->json([
                    'is_holiday' => $body['is_holiday'] ?? false,
                    'source'     => 'api',
                    'name'       => $body['holiday_name'] ?? $body['name'] ?? null,
                    'date'       => $validated,
                ]);
            }

            return response()->json(['is_holiday' => false, 'source' => 'api_error', 'date' => $validated]);

        } catch (\Exception $e) {
            return response()->json(['is_holiday' => false, 'error' => $e->getMessage(), 'date' => $date]);
        }
    }
}