<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_view_teachers_index()
    {
        User::factory()->count(3)->create(['role' => 'guru']);

        $response = $this->actingAs($this->admin)->get(route('teachers.index'));

        $response->assertStatus(200);
        $response->assertViewIs('teachers.index');
        $response->assertViewHas('teachers');
    }

    public function test_admin_can_create_teacher()
    {
        $response = $this->actingAs($this->admin)->post(route('teachers.store'), [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'phone' => '08123456789',
        ]);

        $response->assertRedirect(route('teachers.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'role' => 'guru',
        ]);
    }

    public function test_admin_can_update_teacher()
    {
        $teacher = User::factory()->create([
            'role' => 'guru',
            'name' => 'Andi',
        ]);

        $response = $this->actingAs($this->admin)->put(route('teachers.update', $teacher), [
            'name' => 'Andi Wijaya',
            'email' => $teacher->email,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('teachers.show', $teacher));
        $this->assertDatabaseHas('users', [
            'id' => $teacher->id,
            'name' => 'Andi Wijaya',
        ]);
    }

    public function test_admin_can_delete_teacher()
    {
        $teacher = User::factory()->create([
            'role' => 'guru',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('teachers.destroy', $teacher));

        $response->assertRedirect(route('teachers.index'));
        $this->assertDatabaseMissing('users', [
            'id' => $teacher->id,
        ]);
    }
}
