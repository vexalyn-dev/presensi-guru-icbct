<?php

use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$today = '2026-04-30';
$teachers = User::where('role', 'guru')->get();

foreach ($teachers as $teacher) {
    // Check if already attended today to avoid duplicates
    if (Attendance::where('user_id', $teacher->id)->where('date', $today)->exists()) {
        continue;
    }

    $rand = rand(1, 100);
    
    if ($rand <= 60) {
        // Tepat Waktu (60% chance)
        $hour = 6;
        $minute = rand(0, 59);
        $status = 'Hadir';
    } elseif ($rand <= 90) {
        // Terlambat (30% chance)
        $hour = rand(7, 8);
        $minute = rand(1, 59);
        $status = 'Terlambat';
    } else {
        // Izin/Sakit (10% chance)
        $status = rand(0, 1) ? 'Izin' : 'Sakit';
        $hour = null;
    }

    $attendance = new Attendance();
    $attendance->user_id = $teacher->id;
    $attendance->date = $today;
    
    if ($hour !== null) {
        $checkIn = Carbon::createFromFormat('Y-m-d H:i:s', "$today " . sprintf('%02d:%02d:00', $hour, $minute));
        $attendance->check_in = $checkIn->toTimeString();
        $attendance->status = $status;
        $attendance->latitude = '-6.9147'; // ICB location approx
        $attendance->longitude = '107.6098';
        $attendance->location_name = 'SMK ICB Cinta Teknika';
        $attendance->scan_method = 'QR Code';
    } else {
        $attendance->status = $status;
        $attendance->notes = 'Keterangan ' . $status;
    }

    $attendance->save();
}

echo "Inserted attendance for " . count($teachers) . " teachers for today ($today).\n";
