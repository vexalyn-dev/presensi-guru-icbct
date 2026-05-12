<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TeacherSeeder extends Seeder
{
    public function run()
    {
        $subjects = ['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'IPA', 'IPS', 'Informatika', 'Seni Budaya', 'PJOK', 'PAI', 'PKN'];
        
        $faker = \Faker\Factory::create('id_ID');

        for ($i = 0; $i < 10; $i++) {
            DB::transaction(function () use ($faker, $subjects) {
                $teacher = User::create([
                    'name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'password' => Hash::make('password'),
                    'role' => 'guru',
                    'phone' => $faker->phoneNumber,
                    'address' => $faker->address,
                    'subject' => $faker->randomElement($subjects),
                    'is_active' => true,
                ]);

                // Also generate QR Code token
                $teacher->generateQrCode();
            });
        }
    }
}