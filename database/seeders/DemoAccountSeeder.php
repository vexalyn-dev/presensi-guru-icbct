<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Operator (full access = admin)
        User::updateOrCreate(
            ['email' => 'operator@smkicb.sch.id'],
            [
                'name'         => 'Operator ICB CT',
                'email'        => 'operator@smkicb.sch.id',
                'password'     => Hash::make('operator123'),
                'role'         => 'operator',
                'teacher_code' => 'OPR-001',
                'is_active'    => true,
                'phone'        => '081234567890',
            ]
        );

        // Akun Guru Piket (akses terbatas)
        User::updateOrCreate(
            ['email' => 'piket@smkicb.sch.id'],
            [
                'name'         => 'Guru Piket ICB CT',
                'email'        => 'piket@smkicb.sch.id',
                'password'     => Hash::make('piket123'),
                'role'         => 'guru_piket',
                'teacher_code' => 'PKT-001',
                'is_active'    => true,
                'phone'        => '081234567891',
            ]
        );

        $this->command->info('');
        $this->command->info('✅ Demo accounts created:');
        $this->command->table(
            ['Role', 'Email', 'Password', 'Akses'],
            [
                ['Operator',    'operator@smkicb.sch.id', 'operator123', 'Full access (seperti admin)'],
                ['Guru Piket',  'piket@smkicb.sch.id',   'piket123',    'Presensi, Izin, Jadwal, Manual Presensi'],
            ]
        );
    }
}
