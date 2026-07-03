<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_out',
        'check_out_status',
        'status',
        'photo_in',
        'photo_out',
        'latitude',
        'longitude',
        'location_name',
        'location_address',
        'scan_method',
        'notes',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}