<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Admin\MainCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function mainCategories()
    {
        return $this->hasMany(MainCategory::class);
    }
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => in_array($this->role, ['admin', 'super_admin']),
            'app' => true,
            default => false,
        };
    }
    /**
     * Get user's preferences for a specific category
     */
    public function getUserCategoryData($mainCategoryId): array
    {
        $data = json_decode($this->category_preferences ?? '{}', true);
        return $data[$mainCategoryId] ?? [];
    }

    /**
     * Update user's category preferences (selected theme, etc)
     */
    public function updateUserCategoryData($mainCategoryId, array $data): void
    {
        $preferences = json_decode($this->category_preferences ?? '{}', true);
        $preferences[$mainCategoryId] = array_merge(
            $preferences[$mainCategoryId] ?? [],
            $data
        );
        $this->update(['category_preferences' => json_encode($preferences)]);
    }

    /**
     * Get all theme purchases for user
     */
    public function themePurchases(): HasMany
    {
        return $this->hasMany(\App\Models\Admin\UserThemePurchase::class);
    }
}
