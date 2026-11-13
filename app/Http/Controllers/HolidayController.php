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

            // ── API upset.dev/tanggalmerah ──
            $res = Http::timeout(15)
                ->withHeaders(['Accept' => 'application/json'])
                ->get('https://upset.dev/api/holidays', [
                    'year' => $year,
                ]);

            if (!$res->successful()) {
                return back()->with('error', 'Gagal mengambil data libur dari API. Coba lagi nanti.');
            }

            $body     = $res->json();
            // Response envelope: { "data": [...], ... }
            $holidays = $body['data'] ?? (is_array($body) ? $body : []);

            if (empty($holidays)) {
                return back()->with('error', 'API tidak mengembalikan data hari libur untuk tahun ' . $year . '.');
            }

            $count = 0;
            foreach ($holidays as $holiday) {
                try {
                    // Format upset.dev: { "date": "YYYY-MM-DD", "name": "...", "type": "holiday|leave" }
                    $date = $holiday['date'] ?? null;
                    $name = $holiday['name'] ?? null;

                    if (!$date || !$name) continue;

                    $parsedDate = Carbon::parse($date)->toDateString();

                    // Pastikan hanya tahun yang diminta
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
     * Cek apakah sebuah tanggal merupakan hari libur via API upset.dev/tanggalmerah
     * Endpoint: GET /api/check?date=2026-08-17
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

            // Cek via API upset.dev — GET /api/check?date=YYYY-MM-DD
            $res = Http::timeout(10)
                ->withHeaders(['Accept' => 'application/json'])
                ->get('https://upset.dev/api/check', [
                    'date' => $validated,
                ]);

            if ($res->successful()) {
                $body = $res->json();
                // Response: { "is_off_day": true/false, "name": "...", ... }
                $isOff = $body['is_off_day'] ?? false;
                return response()->json([
                    'is_holiday' => $isOff,
                    'source'     => 'api',
                    'name'       => $body['name'] ?? null,
                    'date'       => $validated,
                ]);
            }

            return response()->json(['is_holiday' => false, 'source' => 'api_error', 'date' => $validated]);

        } catch (\Exception $e) {
            return response()->json(['is_holiday' => false, 'error' => $e->getMessage(), 'date' => $date]);
        }
    }
}