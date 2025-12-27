<?php

namespace App\Policies;

use App\Models\Outlet;
use App\Models\User;

class OutletPolicy
{
    /**
     * Determine if the user can view any outlets.
     */
    public function viewAny(User $user): bool
    {
        // super_admin and business_owner can view outlets
        return in_array($user->role->name, ['super_admin', 'business_owner', 'manager']);
    }

    /**
     * Determine if the user can view the outlet.
     */
    public function view(User $user, Outlet $outlet): bool
    {
        // super_admin: can view any outlet
        if ($user->role->name === 'super_admin') {
            return true;
        }

        // business_owner/manager: can only view outlets in their business
        if (in_array($user->role->name, ['business_owner', 'manager'])) {
            return $outlet->business_id === $user->business_id;
        }

        return false;
    }

    /**
     * Determine if the user can create outlets.
     */
    public function create(User $user): bool
    {
        // super_admin and business_owner can create outlets
        return in_array($user->role->name, ['super_admin', 'business_owner']);
    }

    /**
     * Determine if the user can update the outlet.
     */
    public function update(User $user, Outlet $outlet): bool
    {
        // super_admin: can update any outlet
        if ($user->role->name === 'super_admin') {
            return true;
        }

        // business_owner: can only update outlets in their business
        if ($user->role->name === 'business_owner') {
            return $outlet->business_id === $user->business_id;
        }

        return false;
    }

    /**
     * Determine if the user can delete the outlet.
     */
    public function delete(User $user, Outlet $outlet): bool
    {
        // super_admin: can delete any outlet
        if ($user->role->name === 'super_admin') {
            return true;
        }

        // business_owner: can only delete outlets in their business
        if ($user->role->name === 'business_owner') {
            return $outlet->business_id === $user->business_id;
        }

        return false;
    }

    /**
     * Determine if the user can restore the outlet.
     */
    public function restore(User $user, Outlet $outlet): bool
    {
        return $this->update($user, $outlet);
    }

    /**
     * Determine if the user can permanently delete the outlet.
     */
    public function forceDelete(User $user, Outlet $outlet): bool
    {
        return $this->delete($user, $outlet);
    }
}
