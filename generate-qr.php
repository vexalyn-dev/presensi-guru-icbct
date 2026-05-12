<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

echo "Generating QR Codes for all teachers...\n\n";

$teachers = User::where('role', 'guru')->get();

foreach ($teachers as $teacher) {
    echo "Processing: {$teacher->name}\n";
    
    if (empty($teacher->qr_token)) {
        $teacher->qr_token = Str::uuid()->toString();
        $teacher->save();
        echo "  ✓ Token generated\n";
    }
    
    $qrData = json_encode([
        'teacher_id' => $teacher->id,
        'token' => $teacher->qr_token,
        'name' => $teacher->name,
        'email' => $teacher->email,
    ]);
    
    // HIGH error correction - bisa scan meski QR terpotong/rusak
    $qrCode = QrCode::format('svg')
        ->size(400)                         // Lebih besar = lebih mudah discan
        ->errorCorrection('H')              // HIGH = bisa scan meski 30% rusak
        ->margin(2)
        ->generate($qrData);
    
    $filename = 'qr_' . $teacher->id . '_' . time() . '.svg';
    $path = 'qrcodes/' . $filename;
    
    Storage::disk('public')->makeDirectory('qrcodes');
    Storage::disk('public')->put($path, $qrCode);
    
    $teacher->update(['qr_code' => $path]);
    
    echo "  ✓ QR Code saved: {$path}\n\n";
}

echo "All QR Codes generated successfully!\n";