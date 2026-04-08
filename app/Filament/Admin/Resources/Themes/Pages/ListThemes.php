<?php

namespace App\Filament\Admin\Resources\Themes\Pages;

use App\Filament\Admin\Resources\Themes\ThemeResource;
use App\Models\Admin\MainCategory;
use App\Models\Admin\Theme;
use App\Models\Admin\UserThemePurchase;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListThemes extends ListRecords
{
    protected static string $resource = ThemeResource::class;
    protected  string $view = 'filament.admin.pages.browse-themes';

    public MainCategory $mainCategory;
    public array $themes = [];
    public array $userPurchases = [];
    public $userSelectedTheme = null;
    public $user = null;
    

    public function mount(): void
    {
        $mainCategoryId = session('main_category_id');

        if (! $mainCategoryId) {
            redirect()->back()->with('error', __('messages.no_category_selected'));
        }

        $this->mainCategory = MainCategory::findOrFail($mainCategoryId);

        $this->themes = Theme::query()
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get()
            ->toArray();
        $userId = Auth::id();
        $this->userPurchases = UserThemePurchase::where('user_id', $userId)
            ->where('main_category_id', $mainCategoryId)
            ->pluck('theme_id')
            ->toArray();

        $userData = Auth::user()->getUserCategoryData($mainCategoryId);
        $selectedThemeId = $userData['selected_theme_id'] ?? null;

        $themeMap = collect($this->themes)->keyBy('id');

        if (filled($selectedThemeId)) {
            $theme = $themeMap->get($selectedThemeId);
            $canSelect = filled($theme) && ((bool) ($theme['is_free'] ?? false) || in_array($selectedThemeId, $this->userPurchases));

            $this->userSelectedTheme = $canSelect ? $selectedThemeId : null;
            return;
        }

        $candidateThemeId = $this->mainCategory->default_theme_id;
        if (blank($candidateThemeId) && $themeMap->isNotEmpty()) {
            $candidateThemeId = $themeMap->keys()->first();
        }

        if (filled($candidateThemeId)) {
            $candidate = $themeMap->get($candidateThemeId);
            $canSelectCandidate = filled($candidate) && ((bool) ($candidate['is_free'] ?? false) || in_array($candidateThemeId, $this->userPurchases));

            $this->userSelectedTheme = $canSelectCandidate ? $candidateThemeId : null;
        }
        $this->user = Auth::user();
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->model(Theme::class)
                ->label(__('messages.create_theme'))
                ->visible(fn () => auth()->check() && auth()->user()->email === 'admin@gmail.com'),
          
        ];
    }

    public function selectTheme($themeId): void
    {
        $theme = Theme::findOrFail($themeId);
        $mainCategoryId = $this->mainCategory->id;

        if (! $theme->is_active) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => __('messages.theme_not_available'),
            ]);

            return;
        }

        if (! $theme->is_free && ! in_array($themeId, $this->userPurchases)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => __('messages.theme_not_purchased'),
            ]);
            return;
        }

        Auth::user()->updateUserCategoryData($mainCategoryId, [
            'selected_theme_id' => $themeId,
        ]);

        $this->userSelectedTheme = $themeId;

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('messages.theme_selected'),
        ]);
    }

    public function purchaseTheme($themeId): void
    {
        $theme = Theme::findOrFail($themeId);

        if (! $theme->is_active) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => __('messages.theme_not_available'),
            ]);

            return;
        }

        if ($theme->is_free) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => __('messages.theme_already_free'),
            ]);
            return;
        }

        if (in_array($themeId, $this->userPurchases)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => __('messages.theme_already_purchased'),
            ]);
            return;
        }

        UserThemePurchase::create([
            'user_id' => Auth::id(),
            'theme_id' => $themeId,
            'main_category_id' => $this->mainCategory->id,
            'purchase_date' => now(),
            'amount_paid' => $theme->price,
        ]);

        $this->userPurchases[] = $themeId;

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => __('messages.theme_purchased'),
        ]);
    }
}
