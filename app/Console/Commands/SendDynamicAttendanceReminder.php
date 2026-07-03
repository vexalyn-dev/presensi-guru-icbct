<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Holiday;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SendDynamicAttendanceReminder extends Command
{
    protected $signature = 'reminder:dynamic {--test : Mode testing}';
    protected $description = 'Reminder absensi via WA (Masuk, Terlambat, Pulang)';

    public function handle()
    {
        // 1. MATI TOTAL KALO HARI LIBUR
        if (Holiday::isHoliday(now())) return;

        // Mode Test
        if ($this->option('test')) {
            $teachers = User::where('role', 'guru')->where('is_active', true)->get();
            foreach ($teachers as $teacher) {
                if ($teacher->phone) {
                    $this->sendWA($teacher->phone, "TEST: Ini adalah pesan uji coba sistem reminder ICB CT.");
                    $this->info("✅ Test terkirim ke {$teacher->name}");
                }
            }
            return;
        }

        // Logika Normal
        $teachers = User::where('role', 'guru')->where('is_active', true)->whereNotNull('start_time')->get();
        $nowStr = now()->format('H:i'); 
        $sentCount = 0;

        foreach ($teachers as $teacher) {
            $attendance = Attendance::where('user_id', $teacher->id)->whereDate('date', today())->first();
            $hasCheckedIn = $attendance && !is_null($attendance->check_in);
            $hasCheckedOut = $attendance && !is_null($attendance->check_out);

            $startTime = Carbon::parse($teacher->start_time)->format('H:i');
            $endTime = Carbon::parse($teacher->end_time)->format('H:i');
            $lateInTime = Carbon::parse($teacher->start_time)->addMinutes(5)->format('H:i'); // +5 Menit

            // 1. Reminder Masuk (Tepat Waktu)
            if ($nowStr === $startTime && !$hasCheckedIn) {
                if ($this->sendOnce($teacher, 'checkin_exact')) {
                    $msg = "Assalamualaikum Warahmatullahi Wabarakatuh.\n\n"
                        . "Yth. Bapak/Ibu {$teacher->name},\n\n"
                        . "Dengan hormat, kami mengingatkan bahwa jadwal presensi *MASUK* Anda hari ini adalah pukul *{$startTime}* WIB.\n\n"
                        . "Mohon untuk segera melakukan scan QR Code presensi sebelum waktu yang ditentukan untuk menghindari status terlambat.\n\n"
                        . "Terima kasih atas perhatian dan kerjasamanya.\n\n"
                        . "Wassalamualaikum Warahmatullahi Wabarakatuh.\n\n"
                        . "Hormat kami,\n*Operator SMK ICB CT*";
                    $this->sendWA($teacher->phone, $msg);
                    $sentCount++;
                }
            }

            // 2. Reminder Terlambat Masuk (+5 Menit)
            if ($nowStr === $lateInTime && !$hasCheckedIn) {
                if ($this->sendOnce($teacher, 'checkin_late')) {
                    $msg = "Assalamualaikum Warahmatullahi Wabarakatuh.\n\n"
                        . "Yth. Bapak/Ibu {$teacher->name},\n\n"
                        . "Dengan hormat, kami sampaikan bahwa waktu presensi *MASUK* Anda adalah pukul *{$startTime}* WIB. Berdasarkan sistem, Anda belum melakukan presensi dan tercatat *TERLAMBAT*.\n\n"
                        . "Mohon untuk segera melakukan presensi masuk untuk menghindari status Alpha. Apabila ada kendala, silakan hubungi operator.\n\n"
                        . "Terima kasih atas perhatian dan kerjasamanya.\n\n"
                        . "Wassalamualaikum Warahmatullahi Wabarakatuh.\n\n"
                        . "Hormat kami,\n*Operator SMK ICB CT*";
                    $this->sendWA($teacher->phone, $msg);
                    $sentCount++;
                }
            }

            // 3. Reminder Pulang (Tepat Waktu)
            if ($nowStr === $endTime && !$hasCheckedOut) {
                if ($this->sendOnce($teacher, 'checkout_exact')) {
                    $msg = "Assalamualaikum Warahmatullahi Wabarakatuh.\n\n"
                        . "Yth. Bapak/Ibu {$teacher->name},\n \n"
                        . "Dengan hormat, kami mengingatkan bahwa jadwal presensi *PULANG* Anda hari ini adalah pukul *{$endTime}* WIB.\n\n"
                        . "Mohon untuk tidak lupa melakukan scan QR Code presensi pulang sebelum meninggalkan area sekolah.\n\n"
                        . "Terima kasih atas dedikasi dan kerja keras Anda hari ini.\n\n"
                        . "Wassalamualaikum Warahmatullahi Wabarakatuh.\n\n"
                        . "Hormat kami,\n*Operator SMK ICB CT*";
                    $this->sendWA($teacher->phone, $msg);
                    $sentCount++;
                }
            }
        }

        if ($sentCount > 0) $this->info("✅ {$sentCount} reminder terkirim.");
    }

    private function sendOnce(User $teacher, string $type) {
        $key = "rem_{$teacher->id}_{$type}_" . now()->toDateString();
        return Cache::add($key, true, now()->addDay()->endOfDay());
    }

    private function sendWA(string $phone, string $message) {
        if (!$phone) return;
        $phone = preg_replace('/^08/', '628', $phone);
        try {
            Http::withHeaders(['Authorization' => env('FONNTE_TOKEN')])
                ->post(env('FONNTE_URL'), ['target' => $phone, 'message' => $message]);
        } catch (\Exception $e) {
            $this->error("WA gagal: {$phone}");
        }
    }
}