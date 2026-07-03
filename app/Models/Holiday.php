<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Holiday extends Model
{
    protected $fillable = [
        'date',
        'name',
        'type',
        'is_recurring',
    ];

    protected $casts = [
        'date' => 'date',
        'is_recurring' => 'boolean',
    ];

    public static function isHoliday($date)
    {
        $date = Carbon::parse($date);
        
        if ($date->isWeekend()) {
            return true;
        }
        
        $holiday = self::where('date', $date->toDateString())->first();
        
        if ($holiday) {
            return true;
        }
        
        $recurringHoliday = self::where('is_recurring', true)
            ->whereMonth('date', $date->month)
            ->whereDay('date', $date->day)
            ->first();
        
        return $recurringHoliday !== null;
    }

    public static function getHolidayName($date)
    {
        $date = Carbon::parse($date);
        
        if ($date->isWeekend()) {
            return $date->dayName;
        }
        
        $holiday = self::where('date', $date->toDateString())->first();
        
        if ($holiday) {
            return $holiday->name;
        }
        
        $recurringHoliday = self::where('is_recurring', true)
            ->whereMonth('date', $date->month)
            ->whereDay('date', $date->day)
            ->first();
        
        return $recurringHoliday ? $recurringHoliday->name : null;
    }
}