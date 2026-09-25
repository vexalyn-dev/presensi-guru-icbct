<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@smkicb.sch.id'],
            [
                'name' => 'Admin ICB CT',
                'password' => Hash::make('Adminicb123'),
                'role' => 'admin',
                'is_active' => true,
                'teacher_code' => 'ADM-001',
                'phone' => '087770077887',
            ]
        );
    }
}