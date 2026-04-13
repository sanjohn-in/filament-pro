<?php

namespace App\Helpers;

use App\Models\Admin\Configuration;
use Illuminate\Support\Facades\Cache;

class ExchangeRate
{
    /**
     * Get KHR to USD rate (e.g. 4000 means 1 USD = 4000 KHR)
     * Cached for 60 minutes to avoid repeated DB hits.
     */
    public static function khr(): float
    {
        return Cache::remember('exchange_rate_khr', now()->addMinutes(60), function () {
            return (float) Configuration::where('slug', 'exhange-rate-kh')->value('value') ?? 4000;
        });
    }

    /**
     * Convert KHR amount to USD.
     */
    public static function khrToUsd(float $khr): float
    {
        return $khr / self::khr();
    }

    /**
     * Convert USD amount to KHR.
     */
    public static function usdToKhr(float $usd): float
    {
        return $usd * self::khr();
    }
}