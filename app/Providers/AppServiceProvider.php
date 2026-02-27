<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Illuminate\Support\Facades\URL;

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
        // Force HTTPS
        URL::forceScheme('https');
        URL::forceRootUrl(config('app.url')); 
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
