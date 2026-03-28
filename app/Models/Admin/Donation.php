<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    protected $fillable = [
        'main_category_id',
        'guest_id',
        'amount_usd',
        'amount_khr',
        'payment_method',
        'cash_method',
        'note',
    ];

    protected $casts = [
        'amount_usd' => 'decimal:2',
        'amount_khr' => 'decimal:2',
    ];

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }
    public function mainCategory(): BelongsTo
    {
        return $this->belongsTo(MainCategory::class);
    }
}
