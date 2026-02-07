<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WatermarkSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'font_family',
        'font_size',
        'font_size_unit',
        'position',
        'opacity',
        'color',
        'shadow',
        'angle',
        'is_active',
    ];

    protected $casts = [
        'shadow' => 'boolean',
        'is_active' => 'boolean',
        'font_size' => 'integer',
        'opacity' => 'integer',
        'angle' => 'integer',
    ];
}
