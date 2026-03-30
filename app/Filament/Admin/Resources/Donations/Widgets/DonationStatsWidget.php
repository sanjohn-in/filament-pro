<?php

namespace App\Filament\Admin\Resources\Donations\Widgets;

use App\Models\Admin\Donation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class DonationStatsWidget extends ChartWidget
{
 

    public function getHeading(): ?string
    {
        return __('messages.donation_status_widget');
    }

    protected function getData(): array
    {
        $data = Donation::select('guest_id', DB::raw('count(*) as total'))
            ->with('guest')
            ->groupBy('guest_id')
            ->where('main_category_id', session('main_category_id'))
            ->get();

        // ← Handle empty data
        if ($data->isEmpty()) {
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
                'labels' => [__('messages.no_donations_yet')],
            ];
        }

        $labels = $data->map(fn ($d) =>
            $d->guest?->name ?? __('messages.anonymous')
        )->toArray();

        $values = $data->pluck('total')->toArray();

        $colors = [
            'rgba(59, 130, 246, 0.8)',
            'rgba(16, 185, 129, 0.8)',
            'rgba(245, 158, 11, 0.8)',
            'rgba(239, 68, 68, 0.8)',
            'rgba(139, 92, 246, 0.8)',
            'rgba(236, 72, 153, 0.8)',
            'rgba(20, 184, 166, 0.8)',
        ];

        return [
            'datasets' => [
                [
                    'label'           => __('messages.donations'),
                    'data'            => $values,
                    'backgroundColor' => array_slice($colors, 0, count($values)),
                    'borderWidth'     => 2,
                    'borderColor'     => '#ffffff',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        $isEmpty = Donation::where('main_category_id', session('main_category_id'))->doesntExist();

        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'display'  => true,
                ],
                // ← Show "No data" text in center when empty
                'tooltip' => [
                    'enabled' => !$isEmpty,
                ],
            ],
            'animation' => [
                'animateRotate' => true,
                'animateScale'  => true,
            ],
        ];
    }
}