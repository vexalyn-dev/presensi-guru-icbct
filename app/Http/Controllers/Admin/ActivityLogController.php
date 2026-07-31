<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with(['user:id,name,email,teacher_code,photo_url']);

        // Filter kategori
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter tipe
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter tanggal
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%");
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);

        // Stats
        $stats = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'attendance' => ActivityLog::where('category', 'attendance')->whereDate('created_at', today())->count(),
            'auth' => ActivityLog::where('category', 'auth')->whereDate('created_at', today())->count(),
        ];

        // Kategori untuk filter
        $categories = [
            'attendance' => ['label' => 'Presensi', 'icon' => 'scan-line', 'color' => 'green'],
            'auth' => ['label' => 'Autentikasi', 'icon' => 'log-in', 'color' => 'blue'],
            'settings' => ['label' => 'Pengaturan', 'icon' => 'settings', 'color' => 'purple'],
            'teacher' => ['label' => 'Data Guru', 'icon' => 'users', 'color' => 'indigo'],
            'classroom' => ['label' => 'Kelas', 'icon' => 'school', 'color' => 'amber'],
            'system' => ['label' => 'Sistem', 'icon' => 'server', 'color' => 'slate'],
        ];

        // List guru untuk filter
        $teachers = User::where('role', 'guru')->orderBy('name')->get(['id', 'name', 'teacher_code']);

        return view('admin.activity-logs.index', compact('logs', 'stats', 'categories', 'teachers'));
    }

    public function show(ActivityLog $log)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $log->id,
                'user' => $log->user ? [
                    'name' => $log->user->name,
                    'email' => $log->user->email,
                    'teacher_code' => $log->user->teacher_code,
                    'photo_url' => $log->user->photo_url,
                ] : null,
                'type' => $log->type,
                'category' => $log->category,
                'description' => $log->description,
                'ip_address' => $log->ip_address,
                'user_agent' => $log->user_agent,
                'device_info' => $log->device,
                'properties' => $log->properties,
                'created_at' => $log->created_at->format('d M Y H:i:s'),
            ],
        ]);
    }

    public function destroyOld(Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:7|max:365',
        ]);

        $days    = $request->days;
        $deleted = ActivityLog::where('created_at', '<', now()->subDays($days))->delete();

        return back()->with('success', "Berhasil menghapus {$deleted} log yang lebih dari {$days} hari.");
    }

    // Alias untuk route 'cleanup'
    public function cleanup(Request $request)
    {
        return $this->destroyOld($request);
    }

    public function export(Request $request)
    {
        $query = ActivityLog::with('user:id,name,email,teacher_code');

        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to')) $query->whereDate('created_at', '<=', $request->date_to);

        $logs = $query->orderBy('created_at', 'desc')->limit(1000)->get();

        // Generate CSV
        $filename = 'activity_logs_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'ID', 'Tanggal', 'User', 'Kategori', 'Tipe', 'Deskripsi',
                'IP Address', 'Device', 'Browser', 'OS', 'Latitude', 'Longitude'
            ]);

            // Data
            foreach ($logs as $log) {
                $device = $log->device;
                fputcsv($file, [
                    $log->id,
                    $log->created_at->format('d/m/Y H:i:s'),
                    $log->user?->name ?? 'System',
                    $log->category,
                    $log->type,
                    $log->description,
                    $log->ip_address,
                    $device['device'] ?? '-',
                    $device['browser'] ?? '-',
                    $device['os'] ?? '-',
                    $log->properties['latitude'] ?? '-',
                    $log->properties['longitude'] ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}