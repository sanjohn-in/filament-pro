<?php

namespace App\Filament\Admin\Resources\Donations\Pages;

use App\Filament\Admin\Resources\Donations\DonationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDonation extends CreateRecord
{
    protected static string $resource = DonationResource::class;
}
