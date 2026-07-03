<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Holiday;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendAttendanceReminder extends Command
{
    protected $signature = 'reminder:attendance {type? : checkin atau checkout}';
    protected $description = 'Mengirim reminder absensi via Email dan WhatsApp';

    public function handle()
    {
        $type = $this->argument('type') ?? 'checkin';
        
        // 1. CEK HARI LIBUR DULU (PENTING!)
        // Kalau hari ini libur (Sabtu/Minggu/Nasional), batal kirim!
        if (Holiday::isHoliday(now())) {
            $this->info('Hari ini libur. Reminder dibatalkan.');
            return;
        }

        // 2. AMBIL SEMUA GURU AKTIF
        $teachers = User::where('role', 'guru')->where('is_active', true)->get();
        $count = 0;

        foreach ($teachers as $teacher) {
            // Cek apakah guru ini sudah absen hari ini?
            $hasAttended = Attendance::where('user_id', $teacher->id)
                ->whereDate('date', today())
                ->exists();

            // Kalau BELUM absen, kita kirim reminder
            if (!$hasAttended) {
                $this->sendNotification($teacher, $type);
                $count++;
            }
        }

        $this->info("Berhasil mengirim reminder ke {$count} guru.");
    }

    private function sendNotification($teacher, $type)
    {
        $time = now()->format('H:i');
        
        if ($type === 'checkin') {
            $message = "Yth. Bapak/Ibu {$teacher->name},\n\nJangan lupa lakukan Presensi Masuk hari ini sebelum terlambat!\n\nTerima kasih,\nAdmin ICB CT";
            $subject = "Reminder Presensi Masuk";
        } else {
            $message = "Yth. Bapak/Ibu {$teacher->name},\n\nJangan lupa lakukan Presensi Pulang hari ini sebelum meninggalkan sekolah.\n\nTerima kasih,\nAdmin ICB CT";
            $subject = "Reminder Presensi Pulang";
        }

        // A. KIRIM EMAIL
        try {
            Mail::raw($message, function ($mail) use ($teacher, $subject) {
                $mail->to($teacher->email)
                     ->subject($subject);
            });
            $this->info("Email terkirim ke: {$teacher->email}");
        } catch (\Exception $e) {
            $this->error("Gagal kirim email ke {$teacher->email}: " . $e->getMessage());
        }

        // B. KIRIM WHATSAPP
        if ($teacher->phone) {
            try {
                // Format nomor HP (08xx -> 628xx)
                $phone = preg_replace('/^08/', '628', $teacher->phone);

                Http::withHeaders([
                    'Authorization' => env('FONNTE_TOKEN'),
                ])->post(env('FONNTE_URL'), [
                    'target' => $phone,
                    'message' => $message,
                ]);
                
                $this->info("WA terkirim ke: {$phone}");
            } catch (\Exception $e) {
                $this->error("Gagal kirim WA ke {$phone}: " . $e->getMessage());
            }
        }
    }
}