<?php

namespace App\Policies;

use App\Models\ModelSalesItems;
use App\Models\User;

class SaleItemPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ModelSalesItems $modelSalesItems): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ModelSalesItems $modelSalesItems): bool
    {
        return true; // Kasir biasanya boleh update item di keranjang sebelum checkout
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ModelSalesItems $modelSalesItems): bool
    {
        return true; // Kasir biasanya boleh hapus item di keranjang sebelum checkout
    }
}
