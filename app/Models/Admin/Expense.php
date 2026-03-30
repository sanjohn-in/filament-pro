<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'main_category_id',
        'user_id',
        'name',
        'description',
        'amount_usd',
        'amount_khr',
        'date',
        'status',
        'receipt',
    ];

    protected $casts = [
        'date'       => 'date',
        'amount_usd' => 'decimal:2',
        'amount_khr' => 'decimal:2',
    ];

    public function mainCategory(): BelongsTo
    {
        return $this->belongsTo(MainCategory::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}