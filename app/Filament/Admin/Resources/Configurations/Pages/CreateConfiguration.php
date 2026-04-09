<?php

namespace App\Filament\Admin\Resources\Configurations\Pages;

use App\Filament\Admin\Resources\Configurations\ConfigurationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateConfiguration extends CreateRecord
{
    protected static string $resource = ConfigurationResource::class;

    // protected function mutateFormDataBeforeCreate(array $data): array
    // {
    //     return $this->mergeValueField($data);
    // }
    
    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     return $this->mergeValueField($data);
    // }
    
    // private function mergeValueField(array $data): array
    // {
    //     $type = $data['type'] ?? 'text';
    
    //     $data['value'] = match ($type) {
    //         'text'  => $data['text_value'] ?? null,
    
    //         // FileUpload returns an array — grab the first file path
    //         'image' => collect($data['image_value'] ?? [])->first(),
    //         'music' => collect($data['music_value'] ?? [])->first(),
    
    //         default => null,
    //     };
    
    //     // Clean up the virtual fields — they don't exist in the DB
    //     unset($data['text_value'], $data['image_value'], $data['music_value']);
    
    //     return $data;
    // }
    
}
