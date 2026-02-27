<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\URL;
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
        URL::forceScheme('https');
        URL::forceRootUrl('https://sambath.tovna24.com');
    
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
    }
}
