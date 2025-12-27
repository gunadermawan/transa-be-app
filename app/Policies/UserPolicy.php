<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        // super_admin: can see all users
        // business_owner: can see users in their business
        // others: no access to user management
        return in_array($user->role->name, ['super_admin', 'business_owner']);
    }

    /**
     * Determine if the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // super_admin: can view any user
        if ($user->role->name === 'super_admin') {
            return true;
        }

        // business_owner: can only view users in their business
        if ($user->role->name === 'business_owner') {
            return $model->business_id === $user->business_id;
        }

        return false;
    }

    /**
     * Determine if the user can create users.
     */
    public function create(User $user): bool
    {
        // super_admin and business_owner can create users
        return in_array($user->role->name, ['super_admin', 'business_owner']);
    }

    /**
     * Determine if the user can update the user.
     */
    public function update(User $user, User $model): bool
    {
        // super_admin: can update any user
        if ($user->role->name === 'super_admin') {
            return true;
        }

        // business_owner: can only update users in their business
        // but CANNOT update other business_owners or super_admins
        if ($user->role->name === 'business_owner') {
            if ($model->business_id !== $user->business_id) {
                return false;
            }

            // Cannot edit super_admin or other business_owners
            if (in_array($model->role->name, ['super_admin', 'business_owner'])) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Determine if the user can delete the user.
     */
    public function delete(User $user, User $model): bool
    {
        // super_admin: can delete any user
        if ($user->role->name === 'super_admin') {
            return true;
        }

        // business_owner: can only delete users in their business
        // but CANNOT delete other business_owners or super_admins
        if ($user->role->name === 'business_owner') {
            if ($model->business_id !== $user->business_id) {
                return false;
            }

            // Cannot delete super_admin or other business_owners
            if (in_array($model->role->name, ['super_admin', 'business_owner'])) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Determine if the user can restore the user.
     */
    public function restore(User $user, User $model): bool
    {
        return $this->update($user, $model);
    }

    /**
     * Determine if the user can permanently delete the user.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return $this->delete($user, $model);
    }
}
