<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Reminder Check-in (Jam 07:00 Pagi)
Schedule::command('reminder:attendance checkin')
    ->dailyAt('07:00')
    ->withoutOverlapping();

// Reminder Check-out (Jam 15:30 Sore)
Schedule::command('reminder:attendance checkout')
    ->dailyAt('15:30')
    ->withoutOverlapping();

// Dynamic Reminder (Jalankan setiap 1 menit agar presisi)
Schedule::command('reminder:dynamic')->everyMinute()->withoutOverlapping();
