<?php

namespace App\Filament\Admin\Resources\MainCategories\Pages;

use App\Filament\Admin\Resources\MainCategories\MainCategoryResource;
use App\Models\Admin\Theme;
use Filament\Resources\Pages\CreateRecord;

class CreateMainCategory extends CreateRecord
{
    protected static string $resource = MainCategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Default to the first active theme (typically the free one).
        $data['default_theme_id'] = $data['default_theme_id'] ?? Theme::query()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->value('id');

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var \App\Models\Admin\MainCategory $record */
        $record = $this->record;

        $activeThemes = Theme::query()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get(['id', 'display_order']);

        // Attach all active themes so the category has a selectable theme list.
        $pivotData = [];
        foreach ($activeThemes as $theme) {
            $pivotData[$theme->id] = [
                'display_order' => $theme->display_order,
            ];
        }

        $record->themes()->sync($pivotData);

        // Ensure `default_theme_id` is consistent with the attached themes.
        if (blank($record->default_theme_id) && $activeThemes->isNotEmpty()) {
            $record->default_theme_id = $activeThemes->first()->id;
            $record->save();
        }
    }
}
