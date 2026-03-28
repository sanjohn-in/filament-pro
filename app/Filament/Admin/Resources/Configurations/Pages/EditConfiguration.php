<?php

namespace App\Filament\Admin\Resources\Configurations\Pages;

use App\Filament\Admin\Resources\Configurations\ConfigurationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditConfiguration extends EditRecord
{
    protected static string $resource = ConfigurationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeSave(array $data): array
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

        unset($data['value_text'], $data['value_image'], $data['value_editor']);

        return $data;
    }
}
