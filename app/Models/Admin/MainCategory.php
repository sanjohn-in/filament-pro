<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'address',
        'google_map',
        'cover_image',
        'qr_code',
        'is_visible',
        'time',
        'default_theme_id',
        'portfolios',
        'theme_color',
        'bg_color',
        'schedules',
        'music_id'
    ];

    protected $casts = [
        'portfolios' => 'array',
        'schedules'  => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class);
    }
   /**
     * Get the themes for this main category
     */
    public function themes(): BelongsToMany
    {
        return $this->belongsToMany(
            Theme::class,
            'main_category_theme',
            'main_category_id',
            'theme_id'
        )
        // Qualify column to avoid ambiguity with `themes.display_order`.
        ->orderBy('main_category_theme.display_order');
    }
 
    /**
     * Get the default theme
     */
    public function defaultTheme()
    {
        return $this->belongsTo(Theme::class, 'default_theme_id');
    }
    public function music(): HasOne
    {
        return $this->hasOne(Configuration::class, 'id', 'music_id');
    }
    public function musics(): BelongsTo
    {
        return $this->belongsTo(Configuration::class, 'music_id')
            ->where('type', 'music');
    }
}
