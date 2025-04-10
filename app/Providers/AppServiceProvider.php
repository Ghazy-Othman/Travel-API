<?php

namespace App\Providers;

use App\Models\Travel;
use App\Observers\TravelObserve;
use Illuminate\Support\ServiceProvider;

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
        //
        Travel::observe(TravelObserve::class);
    }
}
