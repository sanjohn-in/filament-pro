<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Theme extends Model
{
    protected $table = 'themes';

    protected $fillable = [
        'name',
        'description',
        'image_url',
        'preview_image_url',
        'price',
        'is_free',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
    ];

    /**
     * Get the main categories this theme belongs to
     */
    public function mainCategories(): BelongsToMany
    {
        return $this->belongsToMany(
            MainCategory::class,
            'main_category_theme',
            'theme_id',
            'main_category_id'
        );
    }

    /**
     * Get the user purchases for this theme
     */
    public function userPurchases()
    {
        return $this->hasMany(UserThemePurchase::class);
    }

    /**
     * Check if user has purchased this theme
     */
    public function isPurchasedByUser($userId)
    {
        if ($this->is_free) {
            return true;
        }

        return $this->userPurchases()
            ->where('user_id', $userId)
            ->exists();
    }
}
