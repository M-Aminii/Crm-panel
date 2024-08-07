<?php

namespace App\Providers;

use App\Observers\InvoiceObserver;
use Cmixin\BusinessDay;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Invoice::observe(InvoiceObserver::class);
    }
}
