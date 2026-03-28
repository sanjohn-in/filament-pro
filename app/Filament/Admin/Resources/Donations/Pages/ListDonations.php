<?php

namespace App\Filament\Admin\Resources\Donations\Pages;

use App\Filament\Admin\Resources\Donations\DonationResource;
use App\Filament\Admin\Resources\Donations\Widgets\DonationStatsWidget;
use App\Filament\Admin\Resources\Guests\Widgets\DonationChartWidget;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDonations extends ListRecords
{
    protected static string $resource = DonationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            // DonationStatsWidget::class,
            // DonationChartWidget::class,
        ];
    }
}
