<?php

namespace App\Filament\Admin\Resources\Guests\Widgets;

use App\Models\Admin\Donation as AdminDonation;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DonationChartWidget extends ChartWidget  // ← fix this

{  

    public function getHeading(): ?string
    {
        return __('messages.donation_by_guest');
    }

    protected function getData(): array
    {
        $data = AdminDonation::select('guest_id', DB::raw('count(*) as total'))
            ->with('guest')
            ->groupBy('guest_id')
            ->where('main_category_id', session('main_category_id'))
            ->get();

        if ($data->isEmpty()) {
            return [
                'datasets' => [
                    [
                        'label'           => __('messages.no_data'),
                        'data'            => [1],
                        'backgroundColor' => ['rgba(200,200,200,0.5)'],
                        'borderWidth'     => 0,
                    ],
                ],
                'labels' => [__('messages.no_data')],
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
    // ← This method only exists in ChartWidget not StatsOverviewWidget
    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['position' => 'bottom'],
            ],
            'cutout' => '65%',
        ];
    }
}