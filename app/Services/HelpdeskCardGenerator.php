<?php

namespace App\Services;

use App\Models\SupportTicket;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Generate PNG card laporan Pusat Bantuan menggunakan PHP GD.
 *
 * Template:
 * public/images/card-laporan.png
 *
 * Ukuran desain utama:
 * 612 × 408 px
 *
 * Generator menggunakan koordinat berbasis 612 × 408,
 * tetapi tetap mendukung template dengan ukuran berbeda
 * melalui automatic scaling.
 */
class HelpdeskCardGenerator
{
    /*
    |--------------------------------------------------------------------------
    | BASE TEMPLATE SIZE
    |--------------------------------------------------------------------------
    |
    | Semua koordinat desain dibuat berdasarkan ukuran ini.
    | Kalau template card-laporan.png memiliki ukuran 612x408,
    | scale = 1.
    |
    | Kalau suatu saat template berubah menjadi 1224x816,
    | generator otomatis melakukan scale 2x.
    |
    */

    private const BASE_WIDTH = 612;
    private const BASE_HEIGHT = 408;

    /*
    |--------------------------------------------------------------------------
    | TEXT POSITIONS
    |--------------------------------------------------------------------------
    |
    | Semua koordinat di bawah dibuat khusus untuk template
    | card-laporan.png ukuran 612 × 408.
    |
    | x/y adalah posisi teks.
    | maxW = batas lebar teks.
    | maxH = batas tinggi untuk teks multiline.
    |
    */

    private const POSITIONS = [

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        'ticket_id' => [
            'x' => 465,
            'y' => 63,
            'maxW' => 112,
            'size' => 10,
            'color' => 'navy',
            'bold' => true,
        ],

        /*
        |--------------------------------------------------------------------------
        | LEFT PANEL - PELAPOR
        |--------------------------------------------------------------------------
        */

        'pelapor' => [
            'x' => 79,
            'y' => 122,
            'maxW' => 125,
            'size' => 10,
            'color' => 'dark',
            'bold' => true,
        ],

        'role' => [
            'x' => 80,
            'y' => 138,
            'maxW' => 120,
            'size' => 7,
            'color' => 'gray',
            'bold' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | LEFT PANEL - SUBJEK
        |--------------------------------------------------------------------------
        */

        'subjek' => [
            'x' => 79,
            'y' => 178,
            'maxW' => 125,
            'size' => 8,
            'color' => 'dark',
            'bold' => true,
        ],

        /*
        |--------------------------------------------------------------------------
        | LEFT PANEL - PRIORITAS
        |--------------------------------------------------------------------------
        |
        | Template sudah menyediakan pill kosong.
        | Kita hanya mengisi teksnya.
        |
        */

        'prioritas' => [
            'x' => 84,
            'y' => 222,
            'maxW' => 90,
            'size' => 7,
            'color' => 'priority',
            'bold' => true,
        ],

        /*
        |--------------------------------------------------------------------------
        | LEFT PANEL - WAKTU DIBUAT
        |--------------------------------------------------------------------------
        */

        'waktu_dibuat' => [
            'x' => 80,
            'y' => 268,
            'maxW' => 125,
            'size' => 7,
            'color' => 'dark',
            'bold' => false,
        ],

        /*
        |--------------------------------------------------------------------------
        | LEFT PANEL - STATUS
        |--------------------------------------------------------------------------
        |
        | Template sudah menyediakan pill kosong.
        |
        */

        'status' => [
            'x' => 84,
            'y' => 316,
            'maxW' => 105,
            'size' => 7,
            'color' => 'dark',
            'bold' => true,
        ],

        /*
        |--------------------------------------------------------------------------
        | RIGHT PANEL - DETAIL
        |--------------------------------------------------------------------------
        */

        'detail' => [
            'x' => 244,
            'y' => 132,
            'maxW' => 325,
            'maxH' => 185,
            'size' => 8,
            'color' => 'dark',
            'bold' => false,
            'lineH' => 15,
            'wrap' => true,
        ],

        /*
        |--------------------------------------------------------------------------
        | FOOTER TIME
        |--------------------------------------------------------------------------
        |
        | Template footer memiliki:
        |
        | clock icon → dotted line → [TIME] → WIB
        |
        | Kita hanya mengisi bagian TIME.
        |
        */

        'footer_time' => [
            'x' => 409,
            'y' => 385,
            'maxW' => 40,
            'size' => 6,
            'color' => 'navy',
            'bold' => false,
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | COLORS
    |--------------------------------------------------------------------------
    */

    private const COLORS = [

        'navy' => [
            0x10,
            0x37,
            0x6C,
        ],

        'dark' => [
            0x1E,
            0x29,
            0x3B,
        ],

        'gray' => [
            0x64,
            0x74,
            0x8B,
        ],

        'white' => [
            0xFF,
            0xFF,
            0xFF,
        ],

        'red' => [
            0xDC,
            0x26,
            0x26,
        ],

        'orange' => [
            0xEA,
            0x58,
            0x0C,
        ],

        'amber' => [
            0xD9,
            0x77,
            0x06,
        ],

        'green' => [
            0x16,
            0xA3,
            0x4A,
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | PATHS
    |--------------------------------------------------------------------------
    */

    private string $templatePath;

    /**
     * Folder output:
     *
     * storage/app/public/helpdesk/
     */
    private string $outputDir = 'helpdesk';

    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR
    |--------------------------------------------------------------------------
    */

    public function __construct()
    {
        $this->templatePath = public_path('images/card-laporan.png');
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE
    |--------------------------------------------------------------------------
    */

    /**
     * Generate card untuk satu tiket.
     *
     * Return:
     * helpdesk/{filename}.png
     *
     * atau null jika gagal.
     */
    public function generate(SupportTicket $ticket): ?string
    {
        /*
        |--------------------------------------------------------------------------
        | CHECK TEMPLATE
        |--------------------------------------------------------------------------
        */

        if (!file_exists($this->templatePath)) {

            Log::error(
                'HelpdeskCardGenerator: template tidak ditemukan',
                [
                    'path' => $this->templatePath,
                ]
            );

            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK GD
        |--------------------------------------------------------------------------
        */

        if (!function_exists('imagecreatefrompng')) {

            Log::error(
                'HelpdeskCardGenerator: PHP GD extension tidak aktif'
            );

            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | OUTPUT FILENAME
        |--------------------------------------------------------------------------
        */

        $filename = 'helpdesk-'
            . ($ticket->ticket_id ?: 'T' . $ticket->id)
            . '.png';

        $relPath = $this->outputDir . '/' . $filename;

        /*
        |--------------------------------------------------------------------------
        | USE EXISTING IMAGE
        |--------------------------------------------------------------------------
        |
        | Jangan generate ulang kalau file sudah tersedia.
        |
        */

        if (Storage::disk('public')->exists($relPath)) {
            return $relPath;
        }

        /*
        |--------------------------------------------------------------------------
        | RENDER
        |--------------------------------------------------------------------------
        */

        try {

            return $this->render(
                $ticket,
                $relPath
            );

        } catch (\Throwable $e) {

            Log::error(
                'HelpdeskCardGenerator: gagal generate card',
                [
                    'ticket_id' => $ticket->id,
                    'reason' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]
            );

            return null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | REGENERATE
    |--------------------------------------------------------------------------
    */

    /**
     * Hapus image lama lalu generate ulang.
     */
    public function regenerate(SupportTicket $ticket): ?string
    {
        $filename = 'helpdesk-'
            . ($ticket->ticket_id ?: 'T' . $ticket->id)
            . '.png';

        $relPath = $this->outputDir . '/' . $filename;

        if (Storage::disk('public')->exists($relPath)) {

            Storage::disk('public')->delete(
                $relPath
            );
        }

        return $this->generate($ticket);
    }

    /*
    |--------------------------------------------------------------------------
    | RENDER
    |--------------------------------------------------------------------------
    */

    private function render(
        SupportTicket $ticket,
        string $relPath
    ): ?string {

        /*
        |--------------------------------------------------------------------------
        | LOAD TEMPLATE
        |--------------------------------------------------------------------------
        */

        $tmpImg = imagecreatefrompng(
            $this->templatePath
        );

        if (!$tmpImg) {

            Log::error(
                'HelpdeskCardGenerator: gagal membaca template PNG'
            );

            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | DETECT TEMPLATE SIZE
        |--------------------------------------------------------------------------
        */

        $templateWidth = imagesx($tmpImg);
        $templateHeight = imagesy($tmpImg);

        /*
        |--------------------------------------------------------------------------
        | AUTOMATIC SCALE
        |--------------------------------------------------------------------------
        |
        | Desain dibuat berdasarkan 612 × 408.
        |
        | Kalau actual template:
        |
        | 612 × 408
        | scale = 1
        |
        | Kalau:
        |
        | 1224 × 816
        | scale = 2
        |
        */

        $scaleX = $templateWidth / self::BASE_WIDTH;
        $scaleY = $templateHeight / self::BASE_HEIGHT;

        /*
        |--------------------------------------------------------------------------
        | TRUE COLOR CANVAS
        |--------------------------------------------------------------------------
        */

        $img = imagecreatetruecolor(
            $templateWidth,
            $templateHeight
        );

        /*
        |--------------------------------------------------------------------------
        | ALPHA
        |--------------------------------------------------------------------------
        */

        imagealphablending(
            $img,
            false
        );

        imagesavealpha(
            $img,
            true
        );

        /*
        |--------------------------------------------------------------------------
        | WHITE BACKGROUND
        |--------------------------------------------------------------------------
        */

        $white = imagecolorallocate(
            $img,
            255,
            255,
            255
        );

        imagefill(
            $img,
            0,
            0,
            $white
        );

        /*
        |--------------------------------------------------------------------------
        | COPY TEMPLATE
        |--------------------------------------------------------------------------
        */

        imagealphablending(
            $img,
            true
        );

        imagecopy(
            $img,
            $tmpImg,
            0,
            0,
            0,
            0,
            $templateWidth,
            $templateHeight
        );

        imagedestroy(
            $tmpImg
        );

        /*
        |--------------------------------------------------------------------------
        | FONT
        |--------------------------------------------------------------------------
        */

        $fontDir = public_path('fonts');

        $fontBold = $fontDir . '/Inter-Bold.ttf';
        $fontReg = $fontDir . '/Inter-Regular.ttf';

        $hasTTF =
            function_exists('imagettftext')
            && function_exists('imagettfbbox')
            && file_exists($fontBold)
            && file_exists($fontReg);

        /*
        |--------------------------------------------------------------------------
        | PREPARE DATA
        |--------------------------------------------------------------------------
        */

        $data = $this->prepareData(
            $ticket
        );

        /*
        |--------------------------------------------------------------------------
        | RENDER DYNAMIC FIELDS
        |--------------------------------------------------------------------------
        */

        foreach (self::POSITIONS as $field => $cfg) {

            if (
                !array_key_exists(
                    $field,
                    $data
                )
            ) {
                continue;
            }

            $text = (string) $data[$field];

            /*
            |--------------------------------------------------------------------------
            | SCALE POSITION
            |--------------------------------------------------------------------------
            */

            $x = (int) round(
                $cfg['x'] * $scaleX
            );

            $y = (int) round(
                $cfg['y'] * $scaleY
            );

            /*
            |--------------------------------------------------------------------------
            | SCALE WIDTH
            |--------------------------------------------------------------------------
            */

            $maxW = isset($cfg['maxW'])
                ? (int) round(
                    $cfg['maxW'] * $scaleX
                )
                : 9999;

            /*
            |--------------------------------------------------------------------------
            | SCALE HEIGHT
            |--------------------------------------------------------------------------
            */

            $maxH = isset($cfg['maxH'])
                ? (int) round(
                    $cfg['maxH'] * $scaleY
                )
                : 9999;

            /*
            |--------------------------------------------------------------------------
            | FONT SIZE
            |--------------------------------------------------------------------------
            */

            $averageScale = (
                $scaleX + $scaleY
            ) / 2;

            $size = max(
                6,
                (int) round(
                    $cfg['size'] * $averageScale
                )
            );

            /*
            |--------------------------------------------------------------------------
            | LINE HEIGHT
            |--------------------------------------------------------------------------
            */

            $lineH = isset($cfg['lineH'])
                ? (int) round(
                    $cfg['lineH'] * $scaleY
                )
                : (int) round(
                    $size * 1.6
                );

            /*
            |--------------------------------------------------------------------------
            | COLOR
            |--------------------------------------------------------------------------
            */

            $color = $this->resolveColor(
                $img,
                $cfg['color'] ?? 'dark',
                $ticket->priority ?? 'medium'
            );

            /*
            |--------------------------------------------------------------------------
            | TTF RENDERING
            |--------------------------------------------------------------------------
            */

            if ($hasTTF) {

                $font = (
                    $cfg['bold'] ?? false
                )
                    ? $fontBold
                    : $fontReg;

                /*
                |--------------------------------------------------------------------------
                | MULTI-LINE DETAIL
                |--------------------------------------------------------------------------
                */

                if ($cfg['wrap'] ?? false) {

                    $this->drawWrappedText(
                        $img,
                        $text,
                        $font,
                        $size,
                        $x,
                        $y,
                        $maxW,
                        $maxH,
                        $lineH,
                        $color
                    );

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | SINGLE LINE
                |--------------------------------------------------------------------------
                */

                $text = $this->truncateText(
                    $text,
                    $font,
                    $size,
                    $maxW
                );

                imagettftext(
                    $img,
                    $size,
                    0,
                    $x,
                    $y,
                    $color,
                    $font,
                    $text
                );

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | FALLBACK GD BITMAP FONT
            |--------------------------------------------------------------------------
            */

            $gdSize = max(
                1,
                min(
                    5,
                    (int) round($size / 4)
                )
            );

            $text = $this->truncateBitmap(
                $text,
                $gdSize,
                $maxW
            );

            imagestring(
                $img,
                $gdSize,
                $x,
                max(0, $y - 10),
                $text,
                $color
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SAVE OUTPUT DIRECTORY
        |--------------------------------------------------------------------------
        */

        $dir = Storage::disk(
            'public'
        )->path(
                $this->outputDir
            );

        if (!is_dir($dir)) {

            mkdir(
                $dir,
                0755,
                true
            );
        }

        /*
        |--------------------------------------------------------------------------
        | OUTPUT PATH
        |--------------------------------------------------------------------------
        */

        $outPath = Storage::disk(
            'public'
        )->path(
                $relPath
            );

        /*
        |--------------------------------------------------------------------------
        | SAVE PNG
        |--------------------------------------------------------------------------
        */

        $saved = imagepng(
            $img,
            $outPath,
            9
        );

        /*
        |--------------------------------------------------------------------------
        | CLEANUP
        |--------------------------------------------------------------------------
        */

        imagedestroy(
            $img
        );

        /*
        |--------------------------------------------------------------------------
        | SAVE FAILED
        |--------------------------------------------------------------------------
        */

        if (!$saved) {

            Log::error(
                'HelpdeskCardGenerator: gagal menyimpan PNG',
                [
                    'path' => $outPath,
                ]
            );

            return null;
        }

        /*
        |--------------------------------------------------------------------------
        | LOG SUCCESS
        |--------------------------------------------------------------------------
        */

        Log::info(
            'HelpdeskCardGenerator: card berhasil dibuat',
            [
                'ticket' => $ticket->id,
                'path' => $relPath,
                'width' => $templateWidth,
                'height' => $templateHeight,
            ]
        );

        return $relPath;
    }

    /*
    |--------------------------------------------------------------------------
    | PREPARE DATA
    |--------------------------------------------------------------------------
    */

    /**
     * Siapkan semua data yang akan dirender.
     */
    private function prepareData(
        SupportTicket $ticket
    ): array {

        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */

        $user = $ticket->user;

        /*
        |--------------------------------------------------------------------------
        | PRIORITY LABEL
        |--------------------------------------------------------------------------
        */

        $priorityLabel =
            SupportTicket::priorityLabels()[
                $ticket->priority
            ]['label']
            ?? strtoupper(
                $ticket->priority
            );

        /*
        |--------------------------------------------------------------------------
        | STATUS LABEL
        |--------------------------------------------------------------------------
        */

        $statusLabel =
            SupportTicket::statusLabels()[
                $ticket->status
            ]['label']
            ?? ucfirst(
                $ticket->status
            );

        /*
        |--------------------------------------------------------------------------
        | ROLE LABEL
        |--------------------------------------------------------------------------
        */

        $roleLabel = match (
        $user?->role ?? ''
        ) {

            'admin',
            'operator'
            => 'Operator',

            'guru_piket'
            => 'Guru Piket',

            'guru'
            => 'Guru',

            default
            => ucfirst(
                $user?->role
                ?? 'Pengguna'
            ),
        };

        /*
        |--------------------------------------------------------------------------
        | CREATED AT
        |--------------------------------------------------------------------------
        |
        | Gunakan waktu tiket dibuat.
        |
        */

        $createdAt = $ticket->created_at
            ? \Carbon\Carbon::parse(
                $ticket->created_at
            )->setTimezone(
                    'Asia/Jakarta'
                )
            : now()->setTimezone(
                'Asia/Jakarta'
            );

        /*
        |--------------------------------------------------------------------------
        | TIME FORMAT
        |--------------------------------------------------------------------------
        |
        | Kiri:
        |
        | 14 Agu 2026 • 16:46
        |
        | Footer:
        |
        | 16:46
        |
        */

        $waktu = $createdAt
            ->locale('id')
            ->isoFormat('D MMM YYYY')
            . ' • '
            . $createdAt->format('H:i');

        $footerTime =
            $createdAt->format('H:i');

        /*
        |--------------------------------------------------------------------------
        | TICKET ID
        |--------------------------------------------------------------------------
        */

        $ticketId = $ticket->ticket_id
            ?: 'HD-'
            . $createdAt->format('md')
            . '-'
            . str_pad(
                (string) $ticket->id,
                3,
                '0',
                STR_PAD_LEFT
            );

        /*
        |--------------------------------------------------------------------------
        | RETURN DATA
        |--------------------------------------------------------------------------
        */

        return [

            'ticket_id' =>
                $this->sanitize(
                    $ticketId,
                    100
                ),

            'pelapor' =>
                $this->sanitize(
                    $user?->name
                    ?? 'Pengguna',
                    100
                ),

            'role' =>
                $this->sanitize(
                    $roleLabel,
                    50
                ),

            'subjek' =>
                $this->sanitize(
                    $ticket->title
                    ?? 'Tanpa subjek',
                    300
                ),

            'prioritas' =>
                strtoupper(
                    $this->sanitize(
                        $priorityLabel,
                        50
                    )
                ),

            'waktu_dibuat' =>
                $this->sanitize(
                    $waktu,
                    100
                ),

            'status' =>
                $this->sanitize(
                    $statusLabel,
                    100
                ),

            'detail' =>
                $this->sanitize(
                    $ticket->description
                    ?? '',
                    2000
                ),

            'footer_time' =>
                $this->sanitize(
                    $footerTime,
                    20
                ),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | SANITIZE
    |--------------------------------------------------------------------------
    */

    /**
     * Sanitasi teks sebelum dirender ke image.
     */
    private function sanitize(
        string $text,
        int $maxLen = 2000
    ): string {

        /*
        |--------------------------------------------------------------------------
        | REMOVE HTML
        |--------------------------------------------------------------------------
        */

        $text = strip_tags(
            $text
        );

        /*
        |--------------------------------------------------------------------------
        | DECODE HTML ENTITY
        |--------------------------------------------------------------------------
        */

        $text = html_entity_decode(
            $text,
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );

        /*
        |--------------------------------------------------------------------------
        | REMOVE CONTROL CHARACTERS
        |--------------------------------------------------------------------------
        |
        | Newline dan carriage return tetap dipertahankan.
        |
        */

        $text = preg_replace(
            '/[\x00-\x08\x0B-\x0C\x0E-\x1F\x7F]/',
            '',
            $text
        );

        /*
        |--------------------------------------------------------------------------
        | NORMALIZE WHITESPACE
        |--------------------------------------------------------------------------
        */

        $text = preg_replace(
            '/[ \t]+/u',
            ' ',
            $text
        );

        /*
        |--------------------------------------------------------------------------
        | TRIM
        |--------------------------------------------------------------------------
        */

        $text = trim(
            $text
        );

        /*
        |--------------------------------------------------------------------------
        | LIMIT LENGTH
        |--------------------------------------------------------------------------
        */

        return mb_substr(
            $text,
            0,
            $maxLen,
            'UTF-8'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RESOLVE COLOR
    |--------------------------------------------------------------------------
    */

    /**
     * Resolve warna teks.
     */
    private function resolveColor(
        $img,
        string $colorName,
        string $priority
    ): int {

        /*
        |--------------------------------------------------------------------------
        | PRIORITY COLOR
        |--------------------------------------------------------------------------
        */

        if ($colorName === 'priority') {

            $c = match (
            strtolower($priority)
            ) {

                'critical',
                'kritis'
                => self::COLORS['red'],

                'high',
                'tinggi'
                => self::COLORS['orange'],

                'medium',
                'normal'
                => self::COLORS['amber'],

                'low',
                'rendah'
                => self::COLORS['green'],

                default
                => self::COLORS['navy'],
            };

        } else {

            $c =
                self::COLORS[$colorName]
                ?? self::COLORS['dark'];
        }

        return imagecolorallocate(
            $img,
            $c[0],
            $c[1],
            $c[2]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | DRAW WRAPPED TEXT
    |--------------------------------------------------------------------------
    */

    /**
     * Render text multiline dengan word wrapping.
     *
     * Tidak akan keluar dari area detail.
     */
    private function drawWrappedText(
        $img,
        string $text,
        string $font,
        float $size,
        int $x,
        int $y,
        int $maxW,
        int $maxH,
        float $lineH,
        int $color
    ): void {

        /*
        |--------------------------------------------------------------------------
        | NORMALIZE NEWLINES
        |--------------------------------------------------------------------------
        */

        $text = str_replace(
            ["\r\n", "\r"],
            "\n",
            $text
        );

        /*
        |--------------------------------------------------------------------------
        | SPLIT PARAGRAPH
        |--------------------------------------------------------------------------
        */

        $paragraphs = preg_split(
            "/\n/u",
            $text
        );

        $lines = [];

        /*
        |--------------------------------------------------------------------------
        | BUILD LINES
        |--------------------------------------------------------------------------
        */

        foreach (
            $paragraphs as $paragraph
        ) {

            $paragraph = trim(
                $paragraph
            );

            /*
            |--------------------------------------------------------------------------
            | EMPTY LINE
            |--------------------------------------------------------------------------
            */

            if ($paragraph === '') {

                $lines[] = '';

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | SPLIT WORDS
            |--------------------------------------------------------------------------
            */

            $words = preg_split(
                '/\s+/u',
                $paragraph,
                -1,
                PREG_SPLIT_NO_EMPTY
            );

            $current = '';

            /*
            |--------------------------------------------------------------------------
            | WORD WRAP
            |--------------------------------------------------------------------------
            */

            foreach (
                $words as $word
            ) {

                $test =
                    $current === ''
                    ? $word
                    : $current . ' ' . $word;

                $box = imagettfbbox(
                    $size,
                    0,
                    $font,
                    $test
                );

                $tw = abs(
                    $box[4] - $box[0]
                );

                /*
                |--------------------------------------------------------------------------
                | NEW LINE
                |--------------------------------------------------------------------------
                */

                if (
                    $tw > $maxW
                    && $current !== ''
                ) {

                    $lines[] =
                        $current;

                    $current =
                        $word;

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | WORD ITSELF TOO LONG
                |--------------------------------------------------------------------------
                */

                if (
                    $tw > $maxW
                    && $current === ''
                ) {

                    $lines[] =
                        $this->fitLongWord(
                            $word,
                            $font,
                            $size,
                            $maxW
                        );

                    $current = '';

                    continue;
                }

                $current =
                    $test;
            }

            /*
            |--------------------------------------------------------------------------
            | FINAL CURRENT LINE
            |--------------------------------------------------------------------------
            */

            if ($current !== '') {

                $lines[] =
                    $current;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | MAX LINES
        |--------------------------------------------------------------------------
        */

        $maxLines = max(
            1,
            (int) floor(
                $maxH / $lineH
            )
        );

        /*
        |--------------------------------------------------------------------------
        | TRUNCATE IF TOO MANY LINES
        |--------------------------------------------------------------------------
        */

        if (
            count($lines)
            > $maxLines
        ) {

            $lines = array_slice(
                $lines,
                0,
                $maxLines
            );

            /*
            |--------------------------------------------------------------------------
            | ADD ELLIPSIS
            |--------------------------------------------------------------------------
            */

            $lastIndex =
                $maxLines - 1;

            $last =
                $lines[$lastIndex];

            $lines[$lastIndex] =
                $this->fitEllipsis(
                    $last,
                    $font,
                    $size,
                    $maxW
                );
        }

        /*
        |--------------------------------------------------------------------------
        | DRAW LINES
        |--------------------------------------------------------------------------
        */

        $curY = $y;

        foreach (
            $lines as $line
        ) {

            /*
            |--------------------------------------------------------------------------
            | STOP IF OUTSIDE AREA
            |--------------------------------------------------------------------------
            */

            if (
                ($curY - $y)
                > $maxH
            ) {
                break;
            }

            /*
            |--------------------------------------------------------------------------
            | EMPTY LINE
            |--------------------------------------------------------------------------
            */

            if ($line === '') {

                $curY += $lineH;

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | DRAW
            |--------------------------------------------------------------------------
            */

            imagettftext(
                $img,
                $size,
                0,
                $x,
                (int) $curY,
                $color,
                $font,
                $line
            );

            $curY += $lineH;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | FIT LONG WORD
    |--------------------------------------------------------------------------
    */

    /**
     * Memotong kata yang sendiri lebih panjang dari area.
     */
    private function fitLongWord(
        string $text,
        string $font,
        float $size,
        int $maxW
    ): string {

        $result = '';

        $length = mb_strlen(
            $text,
            'UTF-8'
        );

        for (
            $i = 0;
            $i < $length;
            $i++
        ) {

            $char =
                mb_substr(
                    $text,
                    $i,
                    1,
                    'UTF-8'
                );

            $test =
                $result . $char;

            $box = imagettfbbox(
                $size,
                0,
                $font,
                $test
            );

            $width = abs(
                $box[4] - $box[0]
            );

            if ($width > $maxW) {
                break;
            }

            $result = $test;
        }

        return $result !== ''
            ? $result
            : '...';
    }

    /*
    |--------------------------------------------------------------------------
    | FIT ELLIPSIS
    |--------------------------------------------------------------------------
    */

    /**
     * Pastikan "..." juga tetap berada dalam max width.
     */
    private function fitEllipsis(
        string $text,
        string $font,
        float $size,
        int $maxW
    ): string {

        $ellipsis = '...';

        /*
        |--------------------------------------------------------------------------
        | ALREADY FIT
        |--------------------------------------------------------------------------
        */

        $box = imagettfbbox(
            $size,
            0,
            $font,
            $text . $ellipsis
        );

        if (
            abs(
                $box[4] - $box[0]
            ) <= $maxW
        ) {

            return $text . $ellipsis;
        }

        /*
        |--------------------------------------------------------------------------
        | REMOVE CHARACTER UNTIL FIT
        |--------------------------------------------------------------------------
        */

        while (
            mb_strlen(
                $text,
                'UTF-8'
            ) > 0
        ) {

            $text =
                mb_substr(
                    $text,
                    0,
                    -1,
                    'UTF-8'
                );

            $candidate =
                rtrim($text)
                . $ellipsis;

            $box = imagettfbbox(
                $size,
                0,
                $font,
                $candidate
            );

            if (
                abs(
                    $box[4] - $box[0]
                ) <= $maxW
            ) {

                return $candidate;
            }
        }

        return $ellipsis;
    }

    /*
    |--------------------------------------------------------------------------
    | TRUNCATE SINGLE LINE
    |--------------------------------------------------------------------------
    */

    /**
     * Truncate text satu baris.
     */
    private function truncateText(
        string $text,
        string $font,
        float $size,
        int $maxW
    ): string {

        /*
        |--------------------------------------------------------------------------
        | EMPTY
        |--------------------------------------------------------------------------
        */

        if ($text === '') {
            return '';
        }

        /*
        |--------------------------------------------------------------------------
        | CHECK WIDTH
        |--------------------------------------------------------------------------
        */

        $box = imagettfbbox(
            $size,
            0,
            $font,
            $text
        );

        $width = abs(
            $box[4] - $box[0]
        );

        if ($width <= $maxW) {
            return $text;
        }

        /*
        |--------------------------------------------------------------------------
        | TRUNCATE
        |--------------------------------------------------------------------------
        */

        while (
            mb_strlen(
                $text,
                'UTF-8'
            ) > 0
        ) {

            $text =
                mb_substr(
                    $text,
                    0,
                    -1,
                    'UTF-8'
                );

            $candidate =
                rtrim($text)
                . '...';

            $box = imagettfbbox(
                $size,
                0,
                $font,
                $candidate
            );

            $width = abs(
                $box[4] - $box[0]
            );

            if ($width <= $maxW) {
                return $candidate;
            }
        }

        return '...';
    }

    /*
    |--------------------------------------------------------------------------
    | TRUNCATE BITMAP
    |--------------------------------------------------------------------------
    */

    /**
     * Fallback ketika font TTF tidak tersedia.
     */
    private function truncateBitmap(
        string $text,
        int $gdSize,
        int $maxW
    ): string {

        /*
        |--------------------------------------------------------------------------
        | APPROXIMATE CHARACTER WIDTH
        |--------------------------------------------------------------------------
        */

        $charW = [
            1 => 6,
            2 => 7,
            3 => 8,
            4 => 9,
            5 => 10,
        ][$gdSize] ?? 8;

        /*
        |--------------------------------------------------------------------------
        | MAX CHARACTERS
        |--------------------------------------------------------------------------
        */

        $max =
            max(
                3,
                (int) floor(
                    $maxW / $charW
                )
            );

        /*
        |--------------------------------------------------------------------------
        | ALREADY FIT
        |--------------------------------------------------------------------------
        */

        if (
            mb_strlen(
                $text,
                'UTF-8'
            ) <= $max
        ) {

            return $text;
        }

        /*
        |--------------------------------------------------------------------------
        | TRUNCATE
        |--------------------------------------------------------------------------
        */

        return mb_substr(
            $text,
            0,
            max(1, $max - 3),
            'UTF-8'
        ) . '...';
    }
}