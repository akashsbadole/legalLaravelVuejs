<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Client;
use App\Models\LegalCase;
use App\Observers\ClientObserver;
use App\Observers\CaseObserver;

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
        Client::observe(ClientObserver::class);
        LegalCase::observe(CaseObserver::class);
    }
}
