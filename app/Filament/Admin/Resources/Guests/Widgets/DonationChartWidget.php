<?php

namespace App\Filament\Admin\Resources\Guests\Widgets;

use App\Models\Admin\Donation as AdminDonation;
use App\Models\Admin\Guest;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class DonationChartWidget extends ChartWidget
{
    public function getHeading(): ?string
    {
        return __('messages.donation_by_guest');
    }

    protected function getData(): array
    {
        $mainCategoryId = session('main_category_id');

        // Count total guests
        $totalGuests = Guest::count();

        // Count guests who donated
        $donatedCount = AdminDonation::where('main_category_id', $mainCategoryId)
            ->distinct('guest_id')
            ->count('guest_id');

        // Count guests who didn't donate
        $nonDonatedCount = $totalGuests - $donatedCount;

        // Get donation totals
        $data = AdminDonation::select(
            DB::raw('SUM(amount_usd) as total_usd'),
            DB::raw('SUM(amount_khr) as total_khr')
        )
            ->where('main_category_id', $mainCategoryId)
            ->first();

        $rate = config('app.khr_to_usd_rate', 4100);
        $totalUsd = floatval($data->total_usd ?? 0);
        $totalKhr = floatval($data->total_khr ?? 0);
        $grandTotalUsd = $totalUsd + ($totalKhr / $rate);

        // Handle empty data
        if ($donatedCount == 0 && $nonDonatedCount == 0) {
            return [
                'datasets' => [
                    [
                        'label'           => __('messages.no_data'),
                        'data'            => [1],
                        'backgroundColor' => ['rgba(200, 200, 200, 0.3)'],
                        'borderColor'     => ['rgba(200, 200, 200, 0.5)'],
                        'borderWidth'     => 1,
                    ],
                ],
                'labels' => [__('messages.no_guests')],
            ];
        }

        return [
            'datasets' => [
                [
                    'label'           => __('messages.donation_status'),
                    'data'            => [$donatedCount, $nonDonatedCount],
                    'backgroundColor' => ['rgba(16, 185, 129, 0.8)', 'rgba(239, 68, 68, 0.8)'],
                    'borderWidth'     => 2,
                    'borderColor'     => '#ffffff',
                ],
            ],
            'labels' => [
                __('messages.guests_donated') . " ({$donatedCount}/{$totalGuests}) - $" . number_format($grandTotalUsd, 2),
                __('messages.guests_not_donated') . " ({$nonDonatedCount}/{$totalGuests})",
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        $mainCategoryId = session('main_category_id');
        $isEmpty = AdminDonation::where('main_category_id', $mainCategoryId)->doesntExist();

        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'display'  => true,
                ],
                'tooltip' => [
                    'enabled' => !$isEmpty,
                    'callbacks' => [
                        'label' => "function(context) {
                            return context.label;
                        }",
                    ],
                ],
            ],
            'animation' => [
                'animateRotate' => true,
                'animateScale'  => true,
            ],
        ];
    }
}