<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_id', 'owner_id', 'price',
        'price', 'contract', 'start_date', 'end_date',
        'year', 'is_active', 'note',
    ];
    protected $casts = [
        'end_date'   => 'date',
        'start_date' => 'date',
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