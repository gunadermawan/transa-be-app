<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\User;

class BusinessPolicy
{
    /**
     * Determine if the user can view any businesses.
     */
    public function viewAny(User $user): bool
    {
        // super_admin: can see all businesses
        // business_owner: can see their own business (will be filtered in resource)
        return in_array($user->role->name, ['super_admin', 'business_owner']);
    }

    /**
     * Determine if the user can view the business.
     */
    public function view(User $user, Business $business): bool
    {
        // super_admin: can view any business
        if ($user->role->name === 'super_admin') {
            return true;
        }

        // business_owner: can only view their own business
        if ($user->role->name === 'business_owner') {
            return $business->id === $user->business_id;
        }

        return false;
    }

    /**
     * Determine if the user can create businesses.
     */
    public function create(User $user): bool
    {
        // Only super_admin can create businesses
        return $user->role->name === 'super_admin';
    }

    /**
     * Determine if the user can update the business.
     */
    public function update(User $user, Business $business): bool
    {
        // super_admin: can update any business
        if ($user->role->name === 'super_admin') {
            return true;
        }

        // business_owner: can only update their own business
        if ($user->role->name === 'business_owner') {
            return $business->id === $user->business_id;
        }

        return false;
    }

    /**
     * Determine if the user can delete the business.
     */
    public function delete(User $user, Business $business): bool
    {
        // Only super_admin can delete businesses
        return $user->role->name === 'super_admin';
    }

    /**
     * Determine if the user can restore the business.
     */
    public function restore(User $user, Business $business): bool
    {
        return $user->role->name === 'super_admin';
    }

    /**
     * Determine if the user can permanently delete the business.
     */
    public function forceDelete(User $user, Business $business): bool
    {
        return $user->role->name === 'super_admin';
    }
}
