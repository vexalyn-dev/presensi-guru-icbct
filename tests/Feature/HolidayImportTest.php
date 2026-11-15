<?php

namespace Tests\Feature;

use App\Models\Holiday;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class HolidayImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_import_national_holidays_and_reuse_cache(): void
    {
        Cache::flush();

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
            <td>17 Agustus</td>
            <td>Senin</td>
            <td><span class="summary">Hari Kemerdekaan</span></td>
        </tr>
    </tbody>
</table>
</body>
</html>
HTML;

        Http::fake([
            config('services.holiday.source') => Http::response($html, 200),
        ]);

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('holidays.fetch-national'), ['year' => 2026])
            ->assertRedirect();

        $this->assertDatabaseHas('holidays', [
            'date' => '2026-01-01 00:00:00',
            'name' => 'Tahun Baru Masehi',
            'type' => 'national',
        ]);

        $this->actingAs($admin)
            ->post(route('holidays.fetch-national'), ['year' => 2026])
            ->assertRedirect();

        $this->assertSame(2, Holiday::where('type', 'national')->count());
        Http::assertSentCount(1);
    }
}
