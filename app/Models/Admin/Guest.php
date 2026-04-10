<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;



class Guest extends Model
{
    use HasFactory;

    protected $fillable = [
        'main_category_id',
        'name',
        'phone',
        'note',
        'tag',
        'link',
        'is_attending',
        'table_group_id',
        'lang',
    ];
    
    // 👇 Add this
    protected static function booted()
    {
        static::deleting(function ($guest) {
            $guest->donations()->delete();
        });
    }

    public function mainCategory(): BelongsTo
    {
        return $this->belongsTo(MainCategory::class);
    }
    // ✅ Replace with
    public function donation(): HasOne
    {
        return $this->hasOne(Donation::class);
    }
    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class, 'guest_id');
    }
    public function tableGroup(): BelongsTo
    {
        return $this->belongsTo(TableGroup::class, 'table_group_id');
    }
    
}
