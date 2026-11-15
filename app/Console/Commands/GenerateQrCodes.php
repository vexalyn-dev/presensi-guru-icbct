<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateQrCodes extends Command
{
    protected $signature = 'qr:generate';
    protected $description = 'Generate QR Codes for all teachers';

    public function handle()
    {
        $this->info('Generating QR Codes for all teachers...');
        
        $teachers = User::whereIn('role', ['guru', 'guru_piket'])->get();
        
        foreach ($teachers as $teacher) {
            if (empty($teacher->qr_code) || empty($teacher->qr_token)) {
                $teacher->generateQrCode();
                $this->line("✓ QR Code generated for: {$teacher->name}");
            } else {
                $this->line("○ Already has QR Code: {$teacher->name}");
            }
        }
        
        $this->info('All QR Codes generated successfully!');
        
        return 0;
    }
}