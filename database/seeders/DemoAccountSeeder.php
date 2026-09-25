<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoAccountSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Admin (full access)
        User::updateOrCreate(
            ['email' => 'admin@smkicb.sch.id'],
            [
                'name'         => 'Admin ICB CT',
                'email'        => 'admin@smkicb.sch.id',
                'password'     => Hash::make('Adminicb123'),
                'role'         => 'admin',
                'teacher_code' => 'ADM-001',
                'is_active'    => true,
                'phone'        => '087770077887',
            ]
        );

        // Akun Operator (full access = admin)
        User::updateOrCreate(
            ['email' => 'operator@smkicb.sch.id'],
            [
                'name'         => 'Operator ICB CT',
                'email'        => 'operator@smkicb.sch.id',
                'password'     => Hash::make('Operatoricb123'),
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
                'password'     => Hash::make('Piketicb123'),
                'role'         => 'guru_piket',
                'teacher_code' => 'PKT-001',
                'is_active'    => true,
                'phone'        => '081234567891',
            ]
        );

        // Akun Guru (guru biasa)
        User::updateOrCreate(
            ['email' => 'guru@smkicb.sch.id'],
            [
                'name'         => 'Guru ICB CT',
                'email'        => 'guru@smkicb.sch.id',
                'password'     => Hash::make('Guruicb123'),
                'role'         => 'guru',
                'teacher_code' => 'GRU-001',
                'is_active'    => true,
                'phone'        => '081234567892',
            ]
        );

        $this->command->info('');
        $this->command->info('✅ Demo accounts created:');
        $this->command->table(
            ['Role', 'Email', 'Password', 'Akses'],
            [
                ['Admin',     'admin@smkicb.sch.id',     'Adminicb123',  'Full access semua fitur'],
                ['Operator',  'operator@smkicb.sch.id',  'Operatoricb123', 'Full access (seperti admin)'],
                ['Guru Piket','piket@smkicb.sch.id',     'Piketicb123',  'Presensi, Izin, Jadwal, Manual Presensi'],
                ['Guru',      'guru@smkicb.sch.id',      'Guruicb123',   'Presensi Harian & Kelas, Jadwal, Izin'],
            ]
        );
    }
}
