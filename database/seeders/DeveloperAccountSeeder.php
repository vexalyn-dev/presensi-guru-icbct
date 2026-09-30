<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DeveloperAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil valid roles dari ENUM kolom role di DB
        $enumValues = DB::select("SHOW COLUMNS FROM users LIKE 'role'")[0]->Type ?? '';
        preg_match_all("/'([^']+)'/", $enumValues, $matches);
        $validRoles = $matches[1] ?? ['admin', 'guru'];

        $role = in_array('developer', $validRoles, true) ? 'developer' : 'admin';

        User::updateOrCreate(
            ['email' => env('DEV_EMAIL')],
            [
                'name'         => env('DEV_NAME', 'Developer'),
                'email'        => env('DEV_EMAIL'),
                'password'     => Hash::make(env('DEV_PASSWORD')),
                'role'         => $role,
                'is_active'    => true,
                'teacher_code' => env('DEV_TEACHER_CODE', 'DEV-001'),
            ]
        );
    }
}

