<?php

use Illuminate\Console\Command;
use App\Models\User;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateQr extends Command
{
    protected $signature = 'qr:generate';
    protected $description = 'Generate QR Codes';

    public function handle()
    {
        $users = User::all();

        foreach ($users as $user) {
            $path = storage_path('app/public/qrcodes/' . $user->id . '.png');

            QrCode::format('png')
                ->size(300)
                ->generate($user->name, $path);

            $this->info("QR Generated: {$user->name}");
        }
    }
}