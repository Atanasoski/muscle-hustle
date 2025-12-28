<?php

namespace App\Policies;

use App\Models\Partner;
use App\Models\User;

class PartnerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admins can view all partners, partner admins can view their own
        return $user->hasAnyRole(['admin', 'partner_admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Partner $partner): bool
    {
        // Admins can view any partner
        if ($user->hasRole('admin')) {
            return true;
        }

        // Partner admins can only view their own partner
        if ($user->hasRole('partner_admin')) {
            return $user->partner_id === $partner->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only system admins can create new partners
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Partner $partner): bool
    {
        // Admins can update any partner
        if ($user->hasRole('admin')) {
            return true;
        }

        // Partner admins can only update their own partner
        if ($user->hasRole('partner_admin')) {
            return $user->partner_id === $partner->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Partner $partner): bool
    {
        // Only system admins can delete partners
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Partner $partner): bool
    {
        // Only system admins can restore partners
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Partner $partner): bool
    {
        // Only system admins can permanently delete partners
        return $user->hasRole('admin');
    }
}
