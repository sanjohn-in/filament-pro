<?php

namespace App\Filament\App\Resources\Cars\Widgets;

use App\Models\Car; // adjust to your actual model namespace
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CarsStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $cars = Car::all();

        $total        = $cars->count();
        $expired      = 0;
        $expiringSoon = 0;
        $active       = 0;

        foreach ($cars as $car) {
            if (blank($car->end_date)) {
                $active++;
                continue;
            }

            $endDate  = Carbon::parse($car->end_date)->startOfDay();
            $today    = Carbon::today();
            $daysLeft = $today->diffInDays($endDate, false); // signed

            if ($daysLeft <= 0) {
                $expired++;
            } elseif ($daysLeft <= 6) {
                $expiringSoon++;
            } else {
                $active++;
            }
        }

        return [
            Stat::make(__('messages.stats_total_cars'), $total)
                ->description(__('messages.stats_total_cars_desc'))
                ->descriptionIcon('heroicon-m-truck')
                ->color('info'),

            Stat::make(__('messages.stats_active'), $active)
                ->description(__('messages.stats_active_desc'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make(__('messages.stats_expiring_soon'), $expiringSoon)
                ->description(__('messages.stats_expiring_soon_desc'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(__('messages.stats_expired'), $expired)
                ->description(__('messages.stats_expired_desc'))
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}