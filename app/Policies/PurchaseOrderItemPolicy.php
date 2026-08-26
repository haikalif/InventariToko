<?php

namespace App\Policies;

use App\Models\ModelPurchaseOrdersItems;
use App\Models\User;

class PurchaseOrderItemPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['superadmin', 'admin', 'staff']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ModelPurchaseOrdersItems $modelPurchaseOrdersItems): bool
    {
        return in_array($user->role, ['superadmin', 'admin', 'staff']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['superadmin', 'admin', 'staff']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ModelPurchaseOrdersItems $modelPurchaseOrdersItems): bool
    {
        return in_array($user->role, ['superadmin', 'admin', 'staff']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ModelPurchaseOrdersItems $modelPurchaseOrdersItems): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }
}
