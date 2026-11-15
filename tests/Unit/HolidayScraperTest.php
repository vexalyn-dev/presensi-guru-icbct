<?php

namespace Tests\Unit;

use App\Services\HolidayScraperService;
use RuntimeException;
use Tests\TestCase;

class HolidayScraperTest extends TestCase
{
    public function test_it_parses_single_and_range_holidays(): void
    {
        $html = <<<'HTML'
<html>
<body>
<h2>Libur Nasional 2026</h2>
<table class="publicholidays phgtable">
    <tbody>
        <tr>
            <td>1 Januari</td>
            <td>Kamis</td>
            <td><span class="summary">Tahun Baru Masehi</span></td>
        </tr>
        <tr>
            <td>21 Maret to 22 Maret</td>
            <td>Sabtu to Minggu</td>
            <td><span class="summary">Hari Raya Idul Fitri</span></td>
        </tr>
        <tr>
            <td> </td>
            <td> </td>
            <td> </td>
        </tr>
    </tbody>
</table>
<h2>Libur Nasional 2027</h2>
<table class="publicholidays phgtable">
    <tbody>
        <tr>
            <td>1 Januari</td>
            <td>Jumat</td>
            <td><span class="summary">Tahun Baru Masehi</span></td>
        </tr>
    </tbody>
</table>
</body>
</html>
HTML;

        $result = (new HolidayScraperService())->parseYear($html, 2026);

        $this->assertSame([
            '2026-01-01' => 'Tahun Baru Masehi',
            '2026-03-21' => 'Hari Raya Idul Fitri',
            '2026-03-22' => 'Hari Raya Idul Fitri',
        ], $result);
    }

    public function test_it_rejects_missing_year(): void
    {
        $this->expectException(RuntimeException::class);

        (new HolidayScraperService())->parseYear('<html><body></body></html>', 2026);
    }
}
