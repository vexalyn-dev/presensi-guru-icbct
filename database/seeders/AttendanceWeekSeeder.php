<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceWeekSeeder extends Seeder
{
    public function run()
    {
        $teacherIds = [56, 57, 58, 59, 60, 61, 62, 63, 65];

        // Monday 22 - Sunday 28 June 2026
        $dates = [
            '2026-06-22', // Mon
            '2026-06-23', // Tue
            '2026-06-24', // Wed
            '2026-06-25', // Thu
            '2026-06-26', // Fri
            '2026-06-27', // Sat
            '2026-06-28', // Sun
        ];

        // Remove existing attendance for this week to avoid duplicates
        DB::table('attendances')
            ->whereIn('user_id', $teacherIds)
            ->whereBetween('date', ['2026-06-22', '2026-06-28'])
            ->delete();

        $records = [];

        // ============================
        // SENIN 22 JUNI - Full Hadir
        // ============================
        $records[] = ['user_id' => 56, 'date' => '2026-06-22', 'check_in' => '06:28:00', 'check_out' => '14:30:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 57, 'date' => '2026-06-22', 'check_in' => '06:31:00', 'check_out' => '14:45:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 58, 'date' => '2026-06-22', 'check_in' => '06:25:00', 'check_out' => '14:20:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 59, 'date' => '2026-06-22', 'check_in' => '06:33:00', 'check_out' => '15:00:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 60, 'date' => '2026-06-22', 'check_in' => '06:29:00', 'check_out' => '14:35:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 61, 'date' => '2026-06-22', 'check_in' => '06:27:00', 'check_out' => '14:50:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 62, 'date' => '2026-06-22', 'check_in' => '06:34:00', 'check_out' => '14:40:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 63, 'date' => '2026-06-22', 'check_in' => '06:22:00', 'check_out' => '14:15:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 65, 'date' => '2026-06-22', 'check_in' => '06:30:00', 'check_out' => '14:25:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];

        // ============================
        // SELASA 23 JUNI - 7 Hadir, 1 Terlambat, 1 Izin
        // ============================
        $records[] = ['user_id' => 56, 'date' => '2026-06-23', 'check_in' => '06:26:00', 'check_out' => '14:30:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 57, 'date' => '2026-06-23', 'check_in' => '06:32:00', 'check_out' => '14:50:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 58, 'date' => '2026-06-23', 'check_in' => '06:29:00', 'check_out' => '14:40:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 59, 'date' => '2026-06-23', 'check_in' => '06:35:00', 'check_out' => '15:10:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 60, 'date' => '2026-06-23', 'check_in' => '06:28:00', 'check_out' => '14:20:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 61, 'date' => '2026-06-23', 'check_in' => '06:48:00', 'check_out' => '14:45:00', 'check_out_status' => 'on_time', 'status' => 'Terlambat', 'scan_method' => 'qr_code']; // Late
        $records[] = ['user_id' => 62, 'date' => '2026-06-23', 'check_in' => '06:30:00', 'check_out' => '14:35:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 63, 'date' => '2026-06-23', 'check_in' => '06:24:00', 'check_out' => '14:10:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 65, 'date' => '2026-06-23', 'check_in' => null, 'check_out' => null, 'check_out_status' => null, 'status' => 'Izin', 'scan_method' => null, 'notes' => 'Izin urusan keluarga']; // Izin

        // ============================
        // RABU 24 JUNI - 6 Hadir, 1 Terlambat, 1 Cuti, 1 Alpha
        // ============================
        $records[] = ['user_id' => 56, 'date' => '2026-06-24', 'check_in' => '06:27:00', 'check_out' => '14:35:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 57, 'date' => '2026-06-24', 'check_in' => '06:52:00', 'check_out' => '14:50:00', 'check_out_status' => 'on_time', 'status' => 'Terlambat', 'scan_method' => 'qr_code']; // Late
        $records[] = ['user_id' => 58, 'date' => '2026-06-24', 'check_in' => '06:31:00', 'check_out' => '14:45:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 59, 'date' => '2026-06-24', 'check_in' => '06:28:00', 'check_out' => '14:30:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 60, 'date' => '2026-06-24', 'check_in' => null, 'check_out' => null, 'check_out_status' => null, 'status' => 'Alpha', 'scan_method' => null]; // Alpha - no check in/out
        $records[] = ['user_id' => 61, 'date' => '2026-06-24', 'check_in' => '06:33:00', 'check_out' => '14:55:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 62, 'date' => '2026-06-24', 'check_in' => '06:25:00', 'check_out' => '14:20:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 63, 'date' => '2026-06-24', 'check_in' => null, 'check_out' => null, 'check_out_status' => null, 'status' => 'Cuti', 'scan_method' => null, 'notes' => 'Cuti melahirkan']; // Cuti
        $records[] = ['user_id' => 65, 'date' => '2026-06-24', 'check_in' => '06:30:00', 'check_out' => '14:40:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];

        // ============================
        // KAMIS 25 JUNI - 5 Hadir, 2 Terlambat, 1 Sakit, 1 Alpha
        // ============================
        $records[] = ['user_id' => 56, 'date' => '2026-06-25', 'check_in' => '06:29:00', 'check_out' => '14:40:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 57, 'date' => '2026-06-25', 'check_in' => '06:34:00', 'check_out' => '14:50:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 58, 'date' => '2026-06-25', 'check_in' => '06:46:00', 'check_out' => '14:30:00', 'check_out_status' => 'on_time', 'status' => 'Terlambat', 'scan_method' => 'qr_code']; // Late
        $records[] = ['user_id' => 59, 'date' => '2026-06-25', 'check_in' => null, 'check_out' => null, 'check_out_status' => null, 'status' => 'Sakit', 'scan_method' => null, 'notes' => 'Sakit demam']; // Sakit
        $records[] = ['user_id' => 60, 'date' => '2026-06-25', 'check_in' => '06:31:00', 'check_out' => '14:25:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 61, 'date' => '2026-06-25', 'check_in' => '06:50:00', 'check_out' => '14:55:00', 'check_out_status' => 'on_time', 'status' => 'Terlambat', 'scan_method' => 'qr_code']; // Late
        $records[] = ['user_id' => 62, 'date' => '2026-06-25', 'check_in' => null, 'check_out' => null, 'check_out_status' => null, 'status' => 'Alpha', 'scan_method' => null]; // Alpha
        $records[] = ['user_id' => 63, 'date' => '2026-06-25', 'check_in' => '06:27:00', 'check_out' => '14:15:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 65, 'date' => '2026-06-25', 'check_in' => '06:32:00', 'check_out' => '14:35:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];

        // ============================
        // JUMAT 26 JUNI - 7 Hadir, 1 Terlambat, 1 Izin
        // ============================
        $records[] = ['user_id' => 56, 'date' => '2026-06-26', 'check_in' => '06:25:00', 'check_out' => '11:30:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code']; // Friday shorter
        $records[] = ['user_id' => 57, 'date' => '2026-06-26', 'check_in' => '06:30:00', 'check_out' => '11:35:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 58, 'date' => '2026-06-26', 'check_in' => '06:28:00', 'check_out' => '11:25:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 59, 'date' => '2026-06-26', 'check_in' => '06:33:00', 'check_out' => '11:40:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 60, 'date' => '2026-06-26', 'check_in' => '06:47:00', 'check_out' => '11:30:00', 'check_out_status' => 'on_time', 'status' => 'Terlambat', 'scan_method' => 'qr_code']; // Late
        $records[] = ['user_id' => 61, 'date' => '2026-06-26', 'check_in' => '06:29:00', 'check_out' => '11:20:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 62, 'date' => '2026-06-26', 'check_in' => '06:31:00', 'check_out' => '11:35:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];
        $records[] = ['user_id' => 63, 'date' => '2026-06-26', 'check_in' => null, 'check_out' => null, 'check_out_status' => null, 'status' => 'Izin', 'scan_method' => null, 'notes' => 'Izin acara keluarga']; // Izin
        $records[] = ['user_id' => 65, 'date' => '2026-06-26', 'check_in' => '06:26:00', 'check_out' => '11:30:00', 'check_out_status' => 'on_time', 'status' => 'Hadir', 'scan_method' => 'qr_code'];

        // ============================
        // SABTU 27 JUNI - Weekend (no school)
        // ============================
        // No attendance records for Saturday

        // ============================
        // MINGGU 28 JUNI - Weekend (no school)
        // ============================
        // No attendance records for Sunday

        // Add timestamps and normalize keys
        $now = Carbon::now();
        $defaults = ['check_in' => null, 'check_out' => null, 'check_out_status' => null, 'scan_method' => null, 'notes' => null];
        foreach ($records as &$record) {
            $record = array_merge($defaults, $record);
            $record['created_at'] = $now;
            $record['updated_at'] = $now;
        }

        DB::table('attendances')->insert($records);

        $this->command->info('✓ Seeded ' . count($records) . ' attendance records for 22-28 June 2026');

        // Print summary
        $this->command->table(
            ['Date', 'Hadir', 'Terlambat', 'Izin/Cuti', 'Alpha'],
            collect($records)->groupBy('date')->sortKeys()->map(function ($dayRecords, $date) {
                return [
                    Carbon::parse($date)->locale('id')->isoFormat('dddd, D MMM'),
                    $dayRecords->where('status', 'Hadir')->count(),
                    $dayRecords->where('status', 'Terlambat')->count(),
                    $dayRecords->whereIn('status', ['Izin', 'Sakit', 'Cuti'])->count(),
                    $dayRecords->where('status', 'Alpha')->count(),
                ];
            })->values()->toArray()
        );
    }
}
