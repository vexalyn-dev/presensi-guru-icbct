<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Setting;
use App\Models\TeacherSchedule;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-11-16 07:00:00'));

        $this->teacher = User::factory()->create([
            'role' => 'guru',
            'is_active' => true,
        ]);

        TeacherSchedule::create([
            'user_id' => $this->teacher->id,
            'day_of_week' => Carbon::now()->dayOfWeek,
            'start_time' => '07:00',
            'end_time' => '16:00',
            'is_active' => true,
        ]);

        Setting::set('gps_validation_status', 'off');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_teacher_can_check_in()
    {
        $this->teacher->update(['qr_token' => 'test-token-123']);

        $qrData = json_encode([
            'teacher_id' => $this->teacher->id,
            'token' => 'test-token-123',
        ]);

        $response = $this->actingAs($this->teacher)->postJson(route('teacher.attendance.store'), [
            'qr_data' => $qrData,
            'mode' => 'masuk',
            'latitude' => -6.200000,
            'longitude' => 106.816666,
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->teacher->id,
            'date' => '2026-11-16',
        ]);
        $this->assertSame(1, Attendance::where('user_id', $this->teacher->id)->count());
    }

    public function test_check_in_rejects_qr_without_token()
    {
        $this->teacher->update(['qr_token' => 'test-token-123']);

        $response = $this->actingAs($this->teacher)->postJson(route('teacher.attendance.store'), [
            'qr_data' => json_encode(['teacher_id' => $this->teacher->id]),
            'mode' => 'masuk',
        ]);

        $response->assertUnprocessable()->assertJson(['success' => false]);
        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_check_in_rejects_other_teachers_token()
    {
        $this->teacher->update(['qr_token' => 'test-token-123']);

        $response = $this->actingAs($this->teacher)->postJson(route('teacher.attendance.store'), [
            'qr_data' => json_encode([
                'teacher_id' => $this->teacher->id,
                'token' => 'wrong-token',
            ]),
            'mode' => 'masuk',
        ]);

        $response->assertUnprocessable()->assertJson(['success' => false]);
        $this->assertDatabaseCount('attendances', 0);
    }
}
