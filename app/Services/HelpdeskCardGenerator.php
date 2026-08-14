<?php

namespace App\Services;

use App\Models\SupportTicket;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Generate PNG card laporan Pusat Bantuan menggunakan PHP GD.
 * Template: public/images/card-laporan.png
 */
class HelpdeskCardGenerator
{
    // ─── Konfigurasi posisi teks di atas template ──────────────────────────
    // Semua koordinat dalam piksel, relatif terhadap pojok kiri atas gambar.
    // Ukuran template asli: 666 × 375 px (landscape)
    private const POSITIONS = [
        'ticket_id' => [
            'x'    => 470,
            'y'    => 45,
            'maxW' => 160,
            'size' => 11,
            'color'=> 'navy',
            'bold' => true,
        ],
        'pelapor' => [
            'x'    => 82,
            'y'    => 128,
            'maxW' => 115,
            'size' => 10,
            'color'=> 'dark',
            'bold' => true,
        ],
        'role' => [
            'x'    => 82,
            'y'    => 143,
            'maxW' => 115,
            'size' => 8,
            'color'=> 'gray',
            'bold' => false,
        ],
        'subjek' => [
            'x'    => 82,
            'y'    => 178,
            'maxW' => 115,
            'size' => 9,
            'color'=> 'dark',
            'bold' => false,
        ],
        'prioritas' => [
            'x'    => 82,
            'y'    => 224,
            'maxW' => 100,
            'size' => 9,
            'color'=> 'priority',
            'bold' => true,
        ],
        'waktu_dibuat' => [
            'x'    => 82,
            'y'    => 268,
            'maxW' => 115,
            'size' => 8,
            'color'=> 'dark',
            'bold' => false,
        ],
        'status' => [
            'x'    => 82,
            'y'    => 313,
            'maxW' => 115,
            'size' => 9,
            'color'=> 'dark',
            'bold' => false,
        ],
        'detail' => [
            'x'    => 208,
            'y'    => 125,
            'maxW' => 440,
            'maxH' => 185,
            'size' => 9,
            'color'=> 'dark',
            'bold' => false,
            'lineH'=> 15,
            'wrap' => true,
        ],
        'footer_time' => [
            'x'    => 318,
            'y'    => 360,
            'maxW' => 100,
            'size' => 8,
            'color'=> 'white',
            'bold' => false,
        ],
    ];

    // Palet warna teks
    private const COLORS = [
        'navy'   => [0x10, 0x37, 0x6C],   // navy gelap
        'dark'   => [0x1E, 0x29, 0x3B],   // slate-800
        'gray'   => [0x64, 0x74, 0x8B],   // slate-500
        'white'  => [0xFF, 0xFF, 0xFF],
        'red'    => [0xDC, 0x26, 0x26],
        'orange' => [0xEA, 0x58, 0x0C],
        'amber'  => [0xD9, 0x77, 0x06],
        'green'  => [0x16, 0xA3, 0x4A],
    ];

    // Path template
    private string $templatePath;

    // Folder output (storage/app/public/helpdesk/)
    private string $outputDir = 'helpdesk';

    public function __construct()
    {
        $this->templatePath = public_path('images/card-laporan.png');
    }

    /**
     * Generate card untuk satu tiket.
     * Kembalikan storage-relative path (helpdesk/{filename}.png) atau null jika gagal.
     */
    public function generate(SupportTicket $ticket): ?string
    {
        // ── Cek template ada ────────────────────────────────────────────────
        if (!file_exists($this->templatePath)) {
            Log::error('HelpdeskCardGenerator: template tidak ditemukan', [
                'path' => $this->templatePath,
            ]);
            return null;
        }

        // ── Cek GD tersedia ─────────────────────────────────────────────────
        if (!function_exists('imagecreatefrompng')) {
            Log::error('HelpdeskCardGenerator: PHP GD extension tidak aktif');
            return null;
        }

        // ── Jika sudah digenerate sebelumnya, kembalikan path lama ──────────
        $filename  = 'helpdesk-' . ($ticket->ticket_id ?: 'T' . $ticket->id) . '.png';
        $relPath   = $this->outputDir . '/' . $filename;

        if (Storage::disk('public')->exists($relPath)) {
            return $relPath;
        }

        try {
            return $this->render($ticket, $relPath);
        } catch (\Throwable $e) {
            Log::error('HelpdeskCardGenerator: gagal generate card', [
                'ticket_id' => $ticket->id,
                'reason'    => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    /**
     * Generate ulang (hapus cache lama dan buat baru).
     */
    public function regenerate(SupportTicket $ticket): ?string
    {
        $filename = 'helpdesk-' . ($ticket->ticket_id ?: 'T' . $ticket->id) . '.png';
        $relPath  = $this->outputDir . '/' . $filename;

        if (Storage::disk('public')->exists($relPath)) {
            Storage::disk('public')->delete($relPath);
        }

        return $this->generate($ticket);
    }

    // ────────────────────────────────────────────────────────────────────────
    // PRIVATE
    // ────────────────────────────────────────────────────────────────────────

    private function render(SupportTicket $ticket, string $relPath): ?string
    {
        // Load template
        $img = imagecreatefrompng($this->templatePath);
        if (!$img) {
            Log::error('HelpdeskCardGenerator: gagal membaca template PNG');
            return null;
        }

        // Aktifkan alpha blending agar teks tidak menghancurkan transparansi
        imagealphablending($img, true);
        imagesavealpha($img, true);

        // Load font (gunakan built-in GD font jika tidak ada TTF)
        $fontDir  = public_path('fonts');
        $fontBold = $fontDir . '/Inter-Bold.ttf';
        $fontReg  = $fontDir . '/Inter-Regular.ttf';

        $hasTTF = function_exists('imagettftext')
            && file_exists($fontBold)
            && file_exists($fontReg);

        // Persiapan data
        $data = $this->prepareData($ticket);

        // ── Tulis setiap field ───────────────────────────────────────────────
        foreach (self::POSITIONS as $field => $cfg) {
            if (!isset($data[$field])) continue;

            $text  = (string) $data[$field];
            $size  = $cfg['size'];
            $bold  = $cfg['bold'] ?? false;
            $color = $this->resolveColor($img, $cfg['color'], $ticket->priority ?? 'medium');

            if ($hasTTF) {
                $font = $bold ? $fontBold : $fontReg;
                if ($cfg['wrap'] ?? false) {
                    $this->drawWrappedText(
                        $img, $text, $font, $size,
                        $cfg['x'], $cfg['y'],
                        $cfg['maxW'], $cfg['maxH'] ?? 9999,
                        $cfg['lineH'] ?? ($size * 1.6),
                        $color
                    );
                } else {
                    $text = $this->truncateText($text, $font, $size, $cfg['maxW']);
                    imagettftext($img, $size, 0, $cfg['x'], $cfg['y'], $color, $font, $text);
                }
            } else {
                // Fallback GD bitmap font (ukuran 1-5)
                $gdSize = max(1, min(5, (int) round($size / 4)));
                $text   = $this->truncateBitmap($text, $gdSize, $cfg['maxW']);
                imagestring($img, $gdSize, $cfg['x'], $cfg['y'] - 10, $text, $color);
            }
        }

        // ── Simpan ke storage ─────────────────────────────────────────────
        $dir = Storage::disk('public')->path($this->outputDir);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $outPath = Storage::disk('public')->path($relPath);
        $saved   = imagepng($img, $outPath, 9);
        imagedestroy($img);

        if (!$saved) {
            Log::error('HelpdeskCardGenerator: gagal menyimpan PNG', ['path' => $outPath]);
            return null;
        }

        Log::info('HelpdeskCardGenerator: card berhasil dibuat', [
            'ticket'  => $ticket->id,
            'path'    => $relPath,
        ]);

        return $relPath;
    }

    /**
     * Siapkan data yang akan dirender dari SupportTicket.
     */
    private function prepareData(SupportTicket $ticket): array
    {
        $user          = $ticket->user;
        $priorityLabel = SupportTicket::priorityLabels()[$ticket->priority]['label'] ?? strtoupper($ticket->priority);
        $statusLabel   = SupportTicket::statusLabels()[$ticket->status]['label']   ?? ucfirst($ticket->status);
        $roleLabel     = match($user?->role ?? '') {
            'admin', 'operator' => 'Operator',
            'guru_piket'        => 'Guru Piket',
            'guru'              => 'Guru',
            default             => ucfirst($user?->role ?? 'Pengguna'),
        };

        $waktu = now()->setTimezone('Asia/Jakarta')
                      ->locale('id')
                      ->isoFormat('D MMMM YYYY • HH:mm') . ' WIB';

        $ticketId = $ticket->ticket_id
            ?? ('#' . str_pad($ticket->id, 6, '0', STR_PAD_LEFT));

        return [
            'ticket_id'    => $ticketId,
            'pelapor'      => $this->sanitize($user?->name ?? 'Pengguna'),
            'role'         => $roleLabel,
            'subjek'       => $this->sanitize($ticket->title),
            'prioritas'    => strtoupper($priorityLabel),
            'waktu_dibuat' => $waktu,
            'status'       => $statusLabel,
            'detail'       => $this->sanitize($ticket->description),
            'footer_time'  => now()->setTimezone('Asia/Jakarta')->format('H:i') . ' WIB',
        ];
    }

    /**
     * Sanitize teks agar aman untuk dirender (hapus tag, strip HTML, batasi panjang).
     */
    private function sanitize(string $text, int $maxLen = 2000): string
    {
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        // Hapus karakter kontrol kecuali newline
        $text = preg_replace('/[\x00-\x08\x0B-\x0C\x0E-\x1F\x7F]/', '', $text);
        return mb_substr($text, 0, $maxLen);
    }

    /**
     * Resolve warna GD dari nama alias atau warna dinamis prioritas.
     */
    private function resolveColor($img, string $colorName, string $priority): int
    {
        if ($colorName === 'priority') {
            $c = match($priority) {
                'critical' => self::COLORS['red'],
                'high'     => self::COLORS['orange'],
                'medium'   => self::COLORS['amber'],
                default    => self::COLORS['green'],
            };
        } else {
            $c = self::COLORS[$colorName] ?? self::COLORS['dark'];
        }

        return imagecolorallocate($img, $c[0], $c[1], $c[2]);
    }

    /**
     * Gambar teks dengan word-wrap otomatis dalam area tertentu.
     * Jika melebihi tinggi, potong dengan "...".
     */
    private function drawWrappedText(
        $img, string $text, string $font, float $size,
        int $x, int $y, int $maxW, int $maxH, float $lineH, int $color
    ): void {
        $words   = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $lines   = [];
        $current = '';

        foreach ($words as $word) {
            $test = $current === '' ? $word : "$current $word";
            $box  = imagettfbbox($size, 0, $font, $test);
            $tw   = abs($box[4] - $box[0]);

            if ($tw > $maxW && $current !== '') {
                $lines[]  = $current;
                $current  = $word;
            } else {
                $current = $test;
            }
        }
        if ($current !== '') $lines[] = $current;

        // Hitung berapa baris yang muat
        $maxLines = (int) floor($maxH / $lineH);
        if (count($lines) > $maxLines) {
            $lines = array_slice($lines, 0, $maxLines);
            // Tambah "..." di akhir baris terakhir
            $last  = $lines[$maxLines - 1];
            while ($last !== '' && ($tw = abs(
                (function() use ($font, $size, $last) {
                    $b = imagettfbbox($size, 0, $font, $last . '...');
                    return abs($b[4] - $b[0]);
                })()
            )) > $maxW) {
                $last = mb_substr($last, 0, -1, 'UTF-8');
            }
            $lines[$maxLines - 1] = $last . '...';
        }

        $curY = $y;
        foreach ($lines as $line) {
            imagettftext($img, $size, 0, $x, (int) $curY, $color, $font, $line);
            $curY += $lineH;
        }
    }

    /**
     * Truncate single-line text agar tidak melebihi maxW pixel.
     */
    private function truncateText(string $text, string $font, float $size, int $maxW): string
    {
        $box = imagettfbbox($size, 0, $font, $text);
        if (abs($box[4] - $box[0]) <= $maxW) return $text;

        while (mb_strlen($text, 'UTF-8') > 0) {
            $text = mb_substr($text, 0, -1, 'UTF-8');
            $box  = imagettfbbox($size, 0, $font, $text . '...');
            if (abs($box[4] - $box[0]) <= $maxW) {
                return $text . '...';
            }
        }
        return '...';
    }

    /**
     * Truncate untuk bitmap font GD (perkiraan lebar per karakter).
     */
    private function truncateBitmap(string $text, int $gdSize, int $maxW): string
    {
        $charW = [1=>6, 2=>7, 3=>8, 4=>9, 5=>10][$gdSize] ?? 8;
        $max   = (int) floor($maxW / $charW);
        if (mb_strlen($text, 'UTF-8') <= $max) return $text;
        return mb_substr($text, 0, $max - 3, 'UTF-8') . '...';
    }
}
