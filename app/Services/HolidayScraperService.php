<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class HolidayScraperService
{
    private const MONTHS = [
        'januari' => 1,
        'februari' => 2,
        'maret' => 3,
        'april' => 4,
        'mei' => 5,
        'juni' => 6,
        'juli' => 7,
        'agustus' => 8,
        'september' => 9,
        'oktober' => 10,
        'november' => 11,
        'desember' => 12,
    ];

    public function fetchYear(int $year): array
    {
        $response = Http::timeout(20)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0 Safari/537.36',
                'Accept-Language' => 'id-ID,id;q=0.9,en;q=0.8',
            ])
            ->get(config('services.holiday.source'));

        if (! $response->successful()) {
            throw new RuntimeException('Gagal mengambil halaman sumber libur nasional.');
        }

        return $this->parseYear($response->body(), $year);
    }

    public function parseYear(string $html, int $year): array
    {
        $dom = new \DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">'.$html);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $xpath = new \DOMXPath($dom);
        $heading = $xpath->query(sprintf('//h2[contains(normalize-space(.), "Libur Nasional %d")]', $year))->item(0);

        if (! $heading) {
            throw new RuntimeException("Data libur nasional tahun {$year} tidak ditemukan di sumber.");
        }

        $table = null;
        for ($node = $heading->nextSibling; $node; $node = $node->nextSibling) {
            if ($node instanceof \DOMElement && strtolower($node->nodeName) === 'table') {
                $table = $node;
                break;
            }
        }

        if (! $table) {
            $table = $xpath->query('following::table[contains(@class, "publicholidays")][1]', $heading)->item(0);
        }

        if (! $table) {
            throw new RuntimeException("Tabel libur nasional tahun {$year} tidak ditemukan.");
        }

        $holidays = [];

        foreach ($xpath->query('.//tbody/tr', $table) as $row) {
            $cells = $xpath->query('./td', $row);

            if ($cells->length < 3) {
                continue;
            }

            $dateText = $this->normalize($cells->item(0)->textContent);
            $nameNode = $xpath->query('.//span[contains(@class, "summary")]', $cells->item(2))->item(0);
            $name = $this->normalize($nameNode ? $nameNode->textContent : $cells->item(2)->textContent);

            if ($dateText === '' || $name === '') {
                continue;
            }

            foreach ($this->expandDate($dateText, $year) as $date) {
                $holidays[$date] = isset($holidays[$date])
                    ? $holidays[$date].' / '.$name
                    : $name;
            }
        }

        if ($holidays === []) {
            throw new RuntimeException("Tidak ada data libur nasional tahun {$year} yang bisa dibaca.");
        }

        ksort($holidays);

        return $holidays;
    }

    private function expandDate(string $text, int $year): array
    {
        $text = str_ireplace([' s/d ', ' sampai ', ' – ', ' — ', ' - '], ' to ', $text);
        $pattern = '/^(\d{1,2})\s+([A-Za-z]+)(?:\s+to\s+(\d{1,2})\s+([A-Za-z]+))?$/i';

        if (! preg_match($pattern, $text, $matches)) {
            return [];
        }

        $startMonth = $this->month($matches[2]);
        $endMonth = isset($matches[4]) && $matches[4] !== ''
            ? $this->month($matches[4])
            : $startMonth;
        $endDay = isset($matches[3]) && $matches[3] !== ''
            ? (int) $matches[3]
            : (int) $matches[1];

        if (! $startMonth || ! $endMonth || ! checkdate($startMonth, (int) $matches[1], $year) || ! checkdate($endMonth, $endDay, $year)) {
            return [];
        }

        $start = Carbon::create($year, $startMonth, (int) $matches[1])->startOfDay();
        $end = Carbon::create($year, $endMonth, $endDay)->startOfDay();

        if ($end->lt($start)) {
            $end->addYear();
        }

        $dates = [];

        for ($cursor = $start->copy(); $cursor->lte($end); $cursor->addDay()) {
            $dates[] = $cursor->toDateString();
        }

        return $dates;
    }

    private function month(string $name): ?int
    {
        return self::MONTHS[strtolower(trim($name))] ?? null;
    }

    private function normalize(string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    }
}
