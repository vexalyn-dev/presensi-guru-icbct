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
        'check_in_latitude',
        'check_in_longitude',
        'check_out_latitude',
        'check_out_longitude',
        'location_name',
        'location_address',
        'scan_method',
        'notes',
    ];
    
    protected $casts = [
        'check_in' => 'datetime:H:i:s',
        'check_out' => 'datetime:H:i:s',
        'check_in_latitude' => 'decimal:7',
        'check_in_longitude' => 'decimal:7',
        'check_out_latitude' => 'decimal:7',
        'check_out_longitude' => 'decimal:7',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}