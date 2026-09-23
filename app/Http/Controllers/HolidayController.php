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

            // Menggunakan API baru dari kemendesa.link
            $response = Http::timeout(15)->get("https://api.kemendesa.link/libur-nasional/api/holidays/{$year}.json");

            if (!$response->successful()) {
                return back()->with('error', 'Gagal mengakses API libur nasional. Status: ' . $response->status());
            }

            $result = $response->json();

            if (!isset($result['data']) || !is_array($result['data'])) {
                return back()->with('error', 'Format data libur tidak valid dari API');
            }

            $holidays = $result['data'];

            $count = 0;
            foreach ($holidays as $holiday) {
                try {
                    $date = $holiday['date'] ?? null;
                    $name = $holiday['name'] ?? null;

                    if (!$date || !$name) {
                        continue;
                    }

                    $parsedDate = Carbon::parse($date)->toDateString();

                    $exists = Holiday::where('date', $parsedDate)
                        ->where('type', 'national')
                        ->exists();

                    if (!$exists) {
                        Holiday::create([
                            'date' => $parsedDate,
                            'name' => $name,
                            'type' => 'national',
                            'is_recurring' => false,
                        ]);
                        $count++;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            if ($count > 0) {
                return back()->with('success', "Berhasil menambahkan {$count} hari libur nasional tahun {$year}");
            } else {
                $existingCount = Holiday::where('type', 'national')
                    ->whereYear('date', $year)
                    ->count();

                if ($existingCount > 0) {
                    return back()->with('success', "Data libur nasional tahun {$year} sudah ada ({$existingCount} hari). Tidak ada data baru.");
                }

                return back()->with('error', 'Tidak ada data libur baru yang ditambahkan.');
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return back()->with('error', 'Koneksi ke API gagal. Periksa koneksi internet Anda.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}