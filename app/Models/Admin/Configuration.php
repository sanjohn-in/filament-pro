<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Configuration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'link', 'type', 'value', 'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'value' => 'string'
    ];


}
