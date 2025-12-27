<?php

namespace App\Policies;

use App\Models\BusinessSetting;
use App\Models\User;

class BusinessSettingPolicy
{
    /**
     * Determine if the user can view any business settings.
     */
    public function viewAny(User $user): bool
    {
        // super_admin and business_owner can view settings
        return in_array($user->role->name, ['super_admin', 'business_owner']);
    }

    /**
     * Determine if the user can view the business setting.
     */
    public function view(User $user, BusinessSetting $businessSetting): bool
    {
        // super_admin: can view any setting
        if ($user->role->name === 'super_admin') {
            return true;
        }

        // business_owner: can only view settings in their business
        if ($user->role->name === 'business_owner') {
            return $businessSetting->business_id === $user->business_id;
        }

        return false;
    }

    /**
     * Determine if the user can create business settings.
     */
    public function create(User $user): bool
    {
        // super_admin and business_owner can create settings
        return in_array($user->role->name, ['super_admin', 'business_owner']);
    }

    /**
     * Determine if the user can update the business setting.
     */
    public function update(User $user, BusinessSetting $businessSetting): bool
    {
        // super_admin: can update any setting
        if ($user->role->name === 'super_admin') {
            return true;
        }

        // business_owner: can only update settings in their business
        if ($user->role->name === 'business_owner') {
            return $businessSetting->business_id === $user->business_id;
        }

        return false;
    }

    /**
     * Determine if the user can delete the business setting.
     */
    public function delete(User $user, BusinessSetting $businessSetting): bool
    {
        // super_admin: can delete any setting
        if ($user->role->name === 'super_admin') {
            return true;
        }

        // business_owner: can only delete settings in their business
        if ($user->role->name === 'business_owner') {
            return $businessSetting->business_id === $user->business_id;
        }

        return false;
    }

    /**
     * Determine if the user can restore the business setting.
     */
    public function restore(User $user, BusinessSetting $businessSetting): bool
    {
        return $this->update($user, $businessSetting);
    }

    /**
     * Determine if the user can permanently delete the business setting.
     */
    public function forceDelete(User $user, BusinessSetting $businessSetting): bool
    {
        return $this->delete($user, $businessSetting);
    }
}
