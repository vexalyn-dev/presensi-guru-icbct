<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_general_settings_with_extended_locale_values(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        $response = $this->post(route('settings.general'), [
            'app_name' => 'ICB CT - Absensi Guru',
            'app_timezone' => 'Asia/Tokyo',
            'app_language' => 'en',
            'admin_email' => 'admin@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Pengaturan umum berhasil disimpan!');
        $this->assertSame('Asia/Tokyo', Setting::get('app_timezone'));
        $this->assertSame('en', Setting::get('app_language'));
    }
}
