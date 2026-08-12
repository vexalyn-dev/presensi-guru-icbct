<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeveloperUpdate extends Model
{
    protected $fillable = ['version', 'title', 'content', 'type', 'show_modal', 'is_active'];

    protected $casts = [
        'show_modal' => 'boolean',
        'is_active'  => 'boolean',
    ];

    public static function latest_active()
    {
        return static::where('is_active', true)->orderByDesc('id')->first();
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'feature' => 'purple',
            'fix'     => 'green',
            'hotfix'  => 'red',
            'update'  => 'blue',
            default   => 'gray',
        };
    }
}
