<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MainCategory extends Model
{
    protected $table = 'main_categories';
    protected $fillable = [
        'user_id',
        'type',
        'bride_name',
        'groom_name',
        'slug',
        'date',
        'adress',
        'google_map',
        'cover_image',
        'qr_code',
        'is_visible',
        'time',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }
}
