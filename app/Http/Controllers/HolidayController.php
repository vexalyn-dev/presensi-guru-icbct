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
            
            // ✅ Menggunakan API libur.deno.dev
            $response = Http::timeout(15)->get("https://libur.deno.dev/api?year={$year}");
            
            if (!$response->successful()) {
                return back()->with('error', 'Gagal mengakses API libur. Status: ' . $response->status());
            }
            
            $holidays = $response->json();
            
            // Validasi response
            if (!is_array($holidays) || empty($holidays)) {
                return back()->with('error', 'Data libur kosong atau format tidak valid');
            }
            
            $count = 0;
            $errors = [];
            
            foreach ($holidays as $holiday) {
                try {
                    // ✅ Flexible parsing - support multiple field names
                    $date = $holiday['holiday_date'] 
                        ?? $holiday['date'] 
                        ?? $holiday['tanggal'] 
                        ?? null;
                        
                    $name = $holiday['holiday_name'] 
                        ?? $holiday['name'] 
                        ?? $holiday['nama'] 
                        ?? $holiday['description'] 
                        ?? null;
                    
                    // Skip jika data tidak valid
                    if (!$date || !$name) {
                        continue;
                    }
                    
                    // Parse date ke format Y-m-d
                    try {
                        $parsedDate = Carbon::parse($date)->toDateString();
                    } catch (\Exception $e) {
                        continue;
                    }
                    
                    // Cek apakah sudah ada di database
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
                    $errors[] = "Error parsing: " . json_encode($holiday);
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
                
                return back()->with('error', 'Tidak ada data libur baru yang ditambahkan. Response API: ' . json_encode($holidays));
            }
            
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return back()->with('error', 'Koneksi ke API gagal. Periksa koneksi internet Anda.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}