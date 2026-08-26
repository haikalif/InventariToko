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
use App\Models\Category;
use App\Policies\CategoryPolicy;
use App\Models\ModelSupliers;
use App\Policies\SuplierPolicy;
use App\Models\User;
use App\Policies\UserPolicy;
use App\Models\ModelSalesItems;
use App\Policies\SaleItemPolicy;
use App\Models\ModelPurchaseOrdersItems;
use App\Policies\PurchaseOrderItemPolicy;

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
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(ModelSupliers::class, SuplierPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(ModelSalesItems::class, SaleItemPolicy::class);
        Gate::policy(ModelPurchaseOrdersItems::class, PurchaseOrderItemPolicy::class);
    }
}

