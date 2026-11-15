<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Services\HolidayScraperService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

    public function fetchNationalHolidays(Request $request, HolidayScraperService $scraper)
    {
        $validated = $request->validate([
            'year' => 'nullable|integer|between:2000,2100',
        ]);

        $year = (int) ($validated['year'] ?? Carbon::now()->year);

        try {
            $holidays = Cache::remember(
                "national_holidays_{$year}",
                now()->addDay(),
                fn () => $scraper->fetchYear($year)
            );
        } catch (\Throwable $e) {
            Log::error('fetchNationalHolidays error: '.$e->getMessage());

            return back()->with('error', 'Gagal mengambil data libur nasional dari publicholidays.co.id. Coba lagi nanti.');
        }

        $count = 0;

        foreach ($holidays as $date => $name) {
            $holiday = Holiday::whereDate('date', $date)
                ->where('type', 'national')
                ->first();

            if (! $holiday) {
                Holiday::create([
                    'date' => $date,
                    'name' => $name,
                    'type' => 'national',
                    'is_recurring' => false,
                ]);

                $count++;
            }
        }

        if ($count > 0) {
            return back()->with('success', "Berhasil menambahkan {$count} hari libur nasional tahun {$year}.");
        }

        $existingCount = Holiday::where('type', 'national')->whereYear('date', $year)->count();

        return back()->with('success', "Data libur nasional {$year} sudah ada ({$existingCount} hari). Tidak ada data baru.");
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