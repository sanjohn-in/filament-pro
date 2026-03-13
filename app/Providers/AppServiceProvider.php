<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
            // URL::forceRootUrl('https://sambath.tovna24.com');
        } 
    
        // Force Livewire update URL to HTTPS directly
        Livewire::setUpdateRoute(function ($handle) {
            return Route::post('/livewire/update', $handle)
                ->middleware('web');
        });
    
       
        // LanguageSwitch
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en', 'km'])       // English + Khmer
                ->labels([
                    'en' => 'English',
                    'km' => 'ភាសាខ្មែរ',        // Khmer label
                ])
                ->displayLocale('en')
                ->circular();                 
        });
        FilamentView::registerRenderHook(
            PanelsRenderHook::TOPBAR_LOGO_BEFORE,
            fn (): string => Blade::render('
                <a href="/admin/clear-category"
                   class="mr-3 inline-flex items-center text-gray-700 hover:text-gray-900">
                    <x-heroicon-o-arrow-left style="width:24px"/>
                </a>
            ')
        );
    }
}
