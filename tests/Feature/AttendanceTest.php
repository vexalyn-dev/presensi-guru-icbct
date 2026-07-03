<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected $teacher;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->teacher = User::factory()->create([
            'role' => 'guru',
            'is_active' => true,
        ]);
    }

    public function test_teacher_can_check_in()
    {
        $this->teacher->update(['qr_token' => 'test-token-123']);

        $qrData = json_encode([
            'teacher_id' => $this->teacher->id,
            'token' => 'test-token-123',
        ]);

        $response = $this->actingAs($this->teacher)->post(route('attendance.store'), [
            'qr_data' => $qrData,
            'latitude' => -6.200000,
            'longitude' => 106.816666,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->teacher->id,
        ]);
    }
}
