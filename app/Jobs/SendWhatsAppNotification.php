<?php

namespace App\Jobs;

use App\Services\FonnteService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendWhatsAppNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $phone,
        public string $message,
        public ?string $imageUrl = null
    ) {}

    public function handle(FonnteService $fonnte): void
    {
        try {
            if ($this->imageUrl) {
                $fonnte->sendImage($this->phone, $this->imageUrl, $this->message);
            } else {
                $fonnte->sendText($this->phone, $this->message);
            }
        } catch (\Throwable $e) {
            Log::error('SendWhatsAppNotification gagal: ' . $e->getMessage(), [
                'phone' => $this->phone,
            ]);
        }
    }
}
