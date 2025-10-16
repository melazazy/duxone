<?php

namespace App\Providers;

use App\Modules\Accounting\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Modules\Accounting\Repositories\Eloquent\EloquentInvoiceRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Accounting Module Service Provider
 * 
 * Registers bindings and configurations for the Accounting module,
 * following the Repository Pattern and Dependency Injection principles.
 */
class AccountingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        // Bind Invoice Repository Interface to Eloquent Implementation
        $this->app->bind(InvoiceRepositoryInterface::class, EloquentInvoiceRepository::class);

        // You can add other repository bindings here as the module grows
        // Example:
        // $this->app->bind(ClientRepositoryInterface::class, EloquentClientRepository::class);
        // $this->app->bind(PaymentRepositoryInterface::class, EloquentPaymentRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Load module routes if needed
        // $this->loadRoutesFrom(module_path('Accounting', 'routes/web.php'));

        // Load module views if needed
        // $this->loadViewsFrom(module_path('Accounting', 'resources/views'), 'accounting');

        // Load module translations if needed
        // $this->loadTranslationsFrom(module_path('Accounting', 'lang'), 'accounting');

        // Load module migrations if needed
        // $this->loadMigrationsFrom(module_path('Accounting', 'database/migrations'));

        // Publish module assets if needed
        // $this->publishes([
        //     module_path('Accounting', 'resources/assets') => public_path('vendor/accounting'),
        // ], 'accounting-assets');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            InvoiceRepositoryInterface::class,
        ];
    }
}
