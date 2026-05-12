<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    public function run()
    {
        $subjects = ['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'IPA', 'IPS', 'Informatika', 'Seni Budaya', 'PJOK', 'PAI', 'PKN'];
        
        foreach($subjects as $i => $s) {
            Subject::updateOrCreate(
                ['name' => $s],
                [
                    'code' => 'MP-'.str_pad($i+1, 3, '0', STR_PAD_LEFT),
                    'category' => 'Umum',
                    'credits' => 2,
                    'is_active' => true,
                ]
            );
        }
    }
}
