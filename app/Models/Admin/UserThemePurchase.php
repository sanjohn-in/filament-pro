<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserThemePurchase extends Model
{
    protected $table = 'user_theme_purchases';

    protected $fillable = [
        'user_id',
        'theme_id',
        'main_category_id',
        'purchase_date',
        'amount_paid',
    ];

    protected $casts = [
        'purchase_date' => 'datetime',
        'amount_paid' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }

    public function mainCategory(): BelongsTo
    {
        return $this->belongsTo(MainCategory::class);
    }
}
