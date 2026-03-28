<?php

namespace App\Filament\Admin\Resources\Configurations\Pages;

use App\Filament\Admin\Resources\Configurations\ConfigurationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateConfiguration extends CreateRecord
{
    protected static string $resource = ConfigurationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->mergeValueField($data);
    }

    private function mergeValueField(array $data): array
    {
        $type = $data['type'] ?? 'text';

        $data['value'] = match ($type) {
            'text'        => $data['value_text'] ?? null,
            'image'       => is_array($data['value_image'] ?? null)
                                ? array_values($data['value_image'])[0] ?? null
                                : $data['value_image'] ?? null,
            'text-editor' => $data['value_editor'] ?? null,
            default       => null,
        };

        // Remove temp fields
        unset($data['value_text'], $data['value_image'], $data['value_editor']);

        return $data;
    }
    
}
