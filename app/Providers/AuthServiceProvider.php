<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Customer;
use App\Models\Invoice;
use App\Policies\CustomerPolicy;
use App\Policies\GlassLayerPolicy;
use App\Policies\InvoicePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Customer::class => CustomerPolicy::class,
        Invoice::class => InvoicePolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        Gate::define('viewAny', [CustomerPolicy::class, 'viewAny']);
        Gate::define('view', [CustomerPolicy::class, 'view']);
        Gate::define('update', [CustomerPolicy::class, 'update']);

        Gate::define('createInvoice', [InvoicePolicy::class, 'create']);
        Gate::define('viewInvoice', [InvoicePolicy::class, 'view']);
        Gate::define('viewAnyInvoice', [InvoicePolicy::class, 'viewAny']);

    }
}
