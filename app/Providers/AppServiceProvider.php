<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;

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
