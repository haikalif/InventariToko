<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\ModelSales;
use App\Policies\SalePolicy;
use App\Models\ModelStockMovements;
use App\Policies\StockMovementPolicy;
use App\Models\Product;
use App\Policies\ProductPolicy;
use App\Models\PurchaseOrder;
use App\Policies\PurchaseOrderPolicy;

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
        Gate::policy(ModelSales::class, SalePolicy::class);
        Gate::policy(ModelStockMovements::class, StockMovementPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(PurchaseOrder::class, PurchaseOrderPolicy::class);
    }
}
