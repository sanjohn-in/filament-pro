<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

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
        'music_id',
        'bride_name_en',
        'groom_name_en',
    ];

    protected $casts = [
        'portfolios' => 'array',
        'schedules'  => 'array',
    ];

    protected static function booted()
    {
        // Handle cleanup when a record is updated (file replaced or removed)
        static::updating(function (MainCategory $model) {
            // Cleanup single files (cover_image and qr_code)
            foreach (['cover_image', 'qr_code'] as $field) {
                if ($model->isDirty($field)) {
                    $oldFile = $model->getOriginal($field);
                    if ($oldFile && Storage::disk('public')->exists($oldFile)) {
                        Storage::disk('public')->delete($oldFile);
                    }
                }
            }

            // Cleanup array of files (portfolios)
            if ($model->isDirty('portfolios')) {
                $oldFiles = $model->getOriginal('portfolios') ?? [];
                $newFiles = $model->portfolios ?? [];
                $toDelete = array_diff($oldFiles, $newFiles);

                foreach ($toDelete as $file) {
                    Storage::disk('public')->delete($file);
                }
            }
        });

        // Handle cleanup when the entire record is deleted
        static::deleting(function (MainCategory $model) {
            Storage::disk('public')->delete(array_merge(
                [$model->cover_image, $model->qr_code],
                $model->portfolios ?? []
            ));
        });
    }

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
