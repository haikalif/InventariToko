<?php

namespace App\Policies;

use App\Models\ModelSupliers;
use App\Models\User;

class SuplierPolicy
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
    public function view(User $user, ModelSupliers $modelSupliers): bool
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
    public function update(User $user, ModelSupliers $modelSupliers): bool
    {
        return in_array($user->role, ['superadmin', 'admin', 'staff']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ModelSupliers $modelSupliers): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ModelSupliers $modelSupliers): bool
    {
        return in_array($user->role, ['superadmin', 'admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ModelSupliers $modelSupliers): bool
    {
        return $user->role === 'superadmin';
    }
}
