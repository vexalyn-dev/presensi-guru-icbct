<?php

use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$today = '2026-04-30';
$attendances = Attendance::where('date', $today)->where('status', '!=', 'Izin')->where('status', '!=', 'Sakit')->get();

foreach ($attendances as $attendance) {
    if ($attendance->check_out) continue;

    // Only 40% have checked out (simulating mid-day or late afternoon)
    if (rand(1, 100) <= 40) {
        $hour = rand(15, 17);
        $minute = rand(0, 59);
        $attendance->check_out = sprintf('%02d:%02d:00', $hour, $minute);
        $attendance->photo_out = 'dummy_photo_out.jpg';
        $attendance->save();
    }
}

echo "Updated check-outs for today.\n";
