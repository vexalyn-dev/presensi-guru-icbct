<?php

namespace Tests\Feature;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectTest extends TestCase
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

    public function test_admin_can_view_subjects_index()
    {
        Subject::create([
            'name' => 'Fisika',
            'code' => 'MP-001',
            'category' => 'Umum',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('subjects.index'));

        $response->assertStatus(200);
        $response->assertViewIs('subjects.index');
        $response->assertViewHas('subjects');
    }

    public function test_admin_can_create_subject()
    {
        $response = $this->actingAs($this->admin)->post(route('subjects.store'), [
            'name' => 'Matematika Dasar',
            'category' => 'Umum',
            'is_active' => true,
            'description' => 'Mata pelajaran matematika dasar',
        ]);

        $response->assertRedirect(route('subjects.index'));
        $this->assertDatabaseHas('subjects', [
            'name' => 'Matematika Dasar',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_update_subject()
    {
        $subject = Subject::create([
            'name' => 'Biologi',
            'code' => 'MP-002',
            'category' => 'Umum',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('subjects.update', $subject), [
            'name' => 'Biologi Lanjut',
            'category' => 'Umum',
            'is_active' => true,
            'description' => 'Biologi lanjutan',
        ]);

        $response->assertRedirect(route('subjects.index'));
        $this->assertDatabaseHas('subjects', [
            'id' => $subject->id,
            'name' => 'Biologi Lanjut',
        ]);
    }

    public function test_admin_can_delete_subject()
    {
        $subject = Subject::create([
            'name' => 'Kimia',
            'code' => 'MP-003',
            'category' => 'Umum',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('subjects.destroy', $subject));

        $response->assertRedirect(route('subjects.index'));
        $this->assertDatabaseMissing('subjects', [
            'id' => $subject->id,
        ]);
    }
}
