<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DeveloperAccountSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('DEV_EMAIL', 'dev@vexalyndev.my.id')],
            [
                'name'         => 'Vexalyn Dev',
                'email'        => env('DEV_EMAIL', 'dev@vexalyndev.my.id'),
                'password'     => Hash::make(env('DEV_PASSWORD', 'VexalynDev2026!')),
                'role'         => 'developer',
                'is_active'    => true,
                'teacher_code' => 'DEV-001',
            ]
        );
    }
}
