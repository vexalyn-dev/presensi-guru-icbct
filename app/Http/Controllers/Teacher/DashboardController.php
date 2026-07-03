<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AppSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $user = Auth::user();

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Mengambil data statistik untuk guru ini (Bulan Ini)
        $totalHadir = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->where('status', 'Hadir')
            ->count();
            
        $totalTerlambat = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->where('status', 'Terlambat')
            ->count();

        $totalIzinSakit = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->whereIn('status', ['Izin', 'Sakit'])
            ->count();

        $totalAlpha = Attendance::where('user_id', $user->id)
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->where('status', 'Alpha')
            ->count();

        // Mengambil riwayat absensi terbaru (dengan relasi user)
        $recentAttendances = Attendance::with('user')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // Mengambil data untuk grafik (7 hari terakhir)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $count = Attendance::where('user_id', $user->id)
                ->whereDate('date', $date)
                ->whereIn('status', ['Hadir', 'Terlambat'])
                ->count();
            $chartData[] = $count;
        }

        // Mengambil pengaturan jam (untuk jam masuk/pulang)
        $appSettings = AppSetting::getInstance();

        return view('teacher.dashboard', compact(
            'totalHadir',
            'totalTerlambat',
            'totalIzinSakit',
            'totalAlpha',
            'recentAttendances',
            'appSettings',
            'chartData'
        ));
    }

    public function schedule() { 
        return view('teacher.schedule'); 
    }
    
    public function attendance() { 
        $attendances = Attendance::where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->paginate(10);
        return view('teacher.attendance', compact('attendances')); 
    }
    
    public function profile() { 
        $user = Auth::user();
        return view('teacher.profile', compact('user')); 
    }
    
    public function updateProfile(Request $request) { 
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $data = $request->only(['name', 'phone', 'address']);
        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->update($data);
        return back()->with('success', 'Profil berhasil diperbarui.');
    }
    
    public function leaves() { 
        $leaves = \App\Models\Leave::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('teacher.leaves', compact('leaves')); 
    }
    
    public function createLeaveRequest() {
        return view('teacher.leaves-create');
    }
    
    public function storeLeaveRequest(Request $request) { 
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:Izin,Sakit',
            'reason' => 'required|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $data['status'] = 'Pending';

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('leaves', 'public');
        }

        \App\Models\Leave::create($data);
        return redirect()->route('teacher.leaves')->with('success', 'Pengajuan izin berhasil dikirim.');
    }
    
    public function updateTodayNotes() { 
        return back()->with('error', 'Fitur Catatan sedang dalam tahap pengembangan.'); 
    }
}
