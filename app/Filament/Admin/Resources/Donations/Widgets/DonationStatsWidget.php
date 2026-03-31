<?php

namespace App\Filament\Admin\Resources\Donations\Widgets;

use App\Models\Admin\Donation;
use App\Models\Admin\Guest;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class DonationStatsWidget extends BaseStatsOverviewWidget
{
    public function getHeading(): ?string
    {
        return __('messages.donation_status_widget');
    }

    protected function getStats(): array
    {
        $mainCategoryId = session('main_category_id');

        // Count total guests
        $totalGuests = Guest::count();

        // Count guests who donated
        $donatedGuests = Donation::where('main_category_id', $mainCategoryId)
            ->distinct('guest_id')
            ->count('guest_id');

        // Count guests who didn't donate
        $nonDonatedGuests = $totalGuests - $donatedGuests;

        // Get donation totals
        $data = Donation::select(
            DB::raw('SUM(amount_usd) as total_usd'),
            DB::raw('SUM(amount_khr) as total_khr')
        )
            ->where('main_category_id', $mainCategoryId)
            ->first();

        $rate = config('app.khr_to_usd_rate', 4100);
        $totalUsd = floatval($data->total_usd ?? 0);
        $totalKhr = floatval($data->total_khr ?? 0);
        $grandTotalUsd = $totalUsd + ($totalKhr / $rate);
        $grandTotalKhr = $totalKhr + ($totalUsd * $rate);

        return [
            // Donated vs Total
            Stat::make(__('messages.guests_donated'), "{$donatedGuests}/{$totalGuests}")
                ->description(__('messages.guests_donated_description'))
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            // Not Donated
            Stat::make(__('messages.guests_not_donated'), $nonDonatedGuests)
                ->description(__('messages.guests_not_donated_description'))
                ->color('danger')
                ->icon('heroicon-o-x-circle'),

            // Total USD
            Stat::make(__('messages.total_usd'), '$' . number_format($totalUsd, 2))
                ->color('info')
                ->icon('heroicon-o-currency-dollar'),

            // Total KHR
            Stat::make(__('messages.total_khr'), number_format($totalKhr, 0) . ' ៛')
                ->color('warning')
                ->icon('heroicon-o-currency-dollar'),

            // Grand Total USD
            Stat::make(__('messages.grand_total_usd'), '$' . number_format($grandTotalUsd, 2))
                ->description(__('messages.combined_total'))
                ->color('primary')
                ->icon('heroicon-o-sparkles'),

            // Grand Total KHR
            Stat::make(__('messages.grand_total_khr'), number_format($grandTotalKhr, 0) . ' ៛')
                ->description(__('messages.combined_total'))
                ->color('primary')
                ->icon('heroicon-o-sparkles'),
        ];
    }
}