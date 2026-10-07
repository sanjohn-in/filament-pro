<?php

namespace App\Filament\Admin\Resources\Themes\Pages;

use App\Filament\Admin\Resources\Themes\ThemeResource;
use App\Models\Admin\MainCategory;
use App\Models\Admin\Theme;
use App\Models\Admin\UserThemePurchase;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
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

        // 1. Prioritize default_theme_id from the main category in the database
        $selectedThemeId = $this->mainCategory->default_theme_id;

        // 2. Fall back to user category data if mainCategory default_theme_id is empty
        if (blank($selectedThemeId)) {
            $userData = Auth::user()->getUserCategoryData($mainCategoryId);
            $selectedThemeId = $userData['selected_theme_id'] ?? null;
        }

        $themeMap = collect($this->themes)->keyBy('id');

        if (filled($selectedThemeId)) {
            $theme = $themeMap->get($selectedThemeId);
            $canSelect = filled($theme) && ((bool) ($theme['is_free'] ?? false) || in_array($selectedThemeId, $this->userPurchases));

            $this->userSelectedTheme = $canSelect ? (int) $selectedThemeId : null;
            $this->user = Auth::user();
            return;
        }

        $candidateThemeId = $themeMap->keys()->first();
        if (filled($candidateThemeId)) {
            $candidate = $themeMap->get($candidateThemeId);
            $canSelectCandidate = filled($candidate) && ((bool) ($candidate['is_free'] ?? false) || in_array($candidateThemeId, $this->userPurchases));

            $this->userSelectedTheme = $canSelectCandidate ? (int) $candidateThemeId : null;
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
        $themeId = (int) $themeId;
        $theme = Theme::findOrFail($themeId);
        $mainCategoryId = $this->mainCategory->id;

        if (! $theme->is_active) {
            Notification::make()
                ->title(__('messages.theme_not_available') ?? 'Theme not available')
                ->danger()
                ->send();

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => __('messages.theme_not_available'),
            ]);

            return;
        }

        if (! $theme->is_free && ! in_array($themeId, $this->userPurchases)) {
            Notification::make()
                ->title(__('messages.theme_not_purchased') ?? 'Theme not purchased')
                ->danger()
                ->send();

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => __('messages.theme_not_purchased'),
            ]);
            return;
        }

        // 1. Update the database column default_theme_id on main_categories
        $this->mainCategory->update([
            'default_theme_id' => $themeId,
        ]);
        $this->mainCategory->refresh();

        // 2. Also keep user-specific selection updated
        Auth::user()->updateUserCategoryData($mainCategoryId, [
            'selected_theme_id' => $themeId,
        ]);

        // 3. Update Livewire property for reactive re-render
        $this->userSelectedTheme = $themeId;

        // 4. Send native Filament notification toast
        Notification::make()
            ->title(__('messages.theme_selected') ?? 'Theme selected successfully')
            ->success()
            ->send();

        // 5. Dispatch Livewire event for any listeners
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
