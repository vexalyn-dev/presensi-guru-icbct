<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service untuk mengirim pesan WhatsApp via Fonnte API.
 * Docs: https://fonnte.com/docs
 */
class FonnteService
{
    private string $token;
    private string $apiUrl;

    public function __construct()
    {
        $this->token  = config('services.fonnte.token', env('FONNTE_TOKEN', ''));
        $this->apiUrl = config('services.fonnte.url',   env('FONNTE_URL', 'https://api.fonnte.com/send'));
    }

    /**
     * Kirim pesan teks biasa ke nomor WA.
     *
     * @param  string  $to      Nomor tujuan (format: 628xxx)
     * @param  string  $message Teks pesan
     * @return array{success: bool, response: mixed}
     */
    public function sendText(string $to, string $message): array
    {
        return $this->send([
            'target'  => $to,
            'message' => $message,
        ]);
    }

    /**
     * Kirim gambar ke nomor WA via URL publik (harus bisa diakses dari internet).
     *
     * @param  string  $to       Nomor tujuan (format: 628xxx)
     * @param  string  $imageUrl URL publik gambar
     * @param  string  $caption  Caption di bawah gambar
     * @return array{success: bool, response: mixed}
     */
    public function sendImage(string $to, string $imageUrl, string $caption = ''): array
    {
        return $this->send([
            'target'  => $to,
            'message' => $caption,
            'url'     => $imageUrl,
        ]);
    }

    /**
     * Kirim gambar ke nomor WA langsung dari path file lokal (multipart upload).
     * Metode ini TIDAK memerlukan URL publik — cocok untuk localhost & shared hosting.
     *
     * @param  string  $to              Nomor tujuan (format: 628xxx)
     * @param  string  $absoluteFilePath Path absolut file di server
     * @param  string  $caption          Caption di bawah gambar
     * @return array{success: bool, response: mixed}
     */
    public function sendImageFile(string $to, string $absoluteFilePath, string $caption = ''): array
    {
        if (empty($this->token)) {
            Log::warning('FonnteService: FONNTE_TOKEN belum dikonfigurasi di .env');
            return ['success' => false, 'response' => 'Token tidak dikonfigurasi'];
        }

        if (!file_exists($absoluteFilePath)) {
            Log::warning('FonnteService: File tidak ditemukan untuk dikirim', ['path' => $absoluteFilePath]);
            return ['success' => false, 'response' => 'File tidak ditemukan'];
        }

        try {
            $filename = basename($absoluteFilePath);
            $mimeType = mime_content_type($absoluteFilePath) ?: 'image/png';
            $fileContent = file_get_contents($absoluteFilePath);

            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])
            ->timeout(30)
            ->attach('file', $fileContent, $filename, ['Content-Type' => $mimeType])
            ->post($this->apiUrl, [
                'target'  => $to,
                'message' => $caption,
            ]);

            $body = $response->json();

            if ($response->successful() && ($body['status'] ?? false)) {
                Log::info('FonnteService: Gambar berhasil dikirim via file upload', [
                    'target' => $to,
                    'file'   => $filename,
                ]);
                return ['success' => true, 'response' => $body];
            }

            Log::warning('FonnteService: Gagal kirim gambar via file upload', [
                'status'   => $response->status(),
                'response' => $body,
            ]);
            return ['success' => false, 'response' => $body];

        } catch (\Throwable $e) {
            Log::error('FonnteService: Exception saat kirim file', [
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'response' => $e->getMessage()];
        }
    }

    /**
     * Kirim request ke Fonnte API via form data biasa.
     */
    private function send(array $payload): array
    {
        if (empty($this->token)) {
            Log::warning('FonnteService: FONNTE_TOKEN belum dikonfigurasi di .env');
            return ['success' => false, 'response' => 'Token tidak dikonfigurasi'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])
            ->timeout(15)
            ->asForm()
            ->post($this->apiUrl, $payload);

            $body = $response->json();

            if ($response->successful() && ($body['status'] ?? false)) {
                Log::info('FonnteService: Pesan berhasil dikirim', [
                    'target' => $payload['target'],
                ]);
                return ['success' => true, 'response' => $body];
            }

            Log::warning('FonnteService: Gagal kirim pesan', [
                'status'   => $response->status(),
                'response' => $body,
            ]);
            return ['success' => false, 'response' => $body];

        } catch (\Throwable $e) {
            Log::error('FonnteService: Exception saat kirim pesan', [
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'response' => $e->getMessage()];
        }
    }
}
