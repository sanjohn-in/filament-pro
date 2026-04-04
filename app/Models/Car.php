<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_id',
        'owner_id',
        'price',
        'interest',
        'contract',
        'start_date',
        'end_date',
        'year',
        'is_active',
        'note',
        'pay_date',
    ];
    protected $casts = [
        'end_date' => 'date',
        'start_date' => 'date',
        'price' => 'decimal:2',
        'interest' => 'decimal:2',
        'is_active' => 'boolean',
        'contract' => 'integer', 
    ];
    public function carModel(): BelongsTo
    {
        return $this->belongsTo(CarModel::class, 'model_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }
}