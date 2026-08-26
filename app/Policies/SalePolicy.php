<?php

namespace App\Policies;

use App\Models\ModelSales;
use App\Models\User;

class SalePolicy
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
    public function view(User $user, ModelSales $modelSales): bool
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
    public function update(User $user, ModelSales $modelSales): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ModelSales $modelSales): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ModelSales $modelSales): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ModelSales $modelSales): bool
    {
        return $user->role === 'superadmin';
    }
}
