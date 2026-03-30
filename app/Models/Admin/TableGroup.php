<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TableGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'main_category_id',
        'user_id',
        'name',
        'status',
        'note',
    ];
    protected $casts = [];

    public function mainCategory(): BelongsTo
    {
        return $this->belongsTo(MainCategory::class);
    }

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class, 'table_group_id');
    }
 
}
