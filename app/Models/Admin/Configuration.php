<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Configuration extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'link', 'type', 'value', 'is_visible',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        // 'value' => 'string'
    ];

    protected static function booted()
    {
        // Clean up storage when a file is replaced, or when type switches from file to text
        static::updating(function (Configuration $model) {
            if ($model->isDirty('value')) {
                $oldType = $model->getOriginal('type');
                $oldValue = $model->getOriginal('value');

                // We only delete if the PREVIOUS state was a file-based type
                if (in_array($oldType, ['image', 'music']) && $oldValue) {
                    if (Storage::disk('public')->exists($oldValue)) {
                        Storage::disk('public')->delete($oldValue);
                    }
                }
            }
        });

        // Clean up storage when the configuration record is deleted
        static::deleting(function (Configuration $model) {
            if (in_array($model->type, ['image', 'music']) && $model->value) {
                if (Storage::disk('public')->exists($model->value)) {
                    Storage::disk('public')->delete($model->value);
                }
            }
        });

        static::saved(function (Configuration $configuration) {
            if ($configuration->slug === 'exchange-rate-kh') {
                Cache::forget('exchange_rate_khr');
            }
        });
    }
}
