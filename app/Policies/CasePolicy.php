<?php

namespace App\Policies;

use App\Models\LegalCase;
use App\Models\User;

class CasePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'attorney', 'paralegal', 'client']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LegalCase $legalCase): bool
    {
        // Admins can view all cases
        if ($user->role === 'admin') {
            return true;
        }

        // Attorneys can view cases they are assigned to
        if ($user->role === 'attorney' && $legalCase->assigned_attorney_id === $user->id) {
            return true;
        }

        // Clients can view cases they are associated with
        if ($user->role === 'client' && $legalCase->client_id === $user->id) {
            return true;
        }

        // Paralegals can view cases they are assigned to or participate in
        if ($user->role === 'paralegal') {
            return $legalCase->participants()->where('user_id', $user->id)->exists() ||
                   $legalCase->assigned_attorney_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'attorney']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LegalCase $legalCase): bool
    {
        // Admins can update all cases
        if ($user->role === 'admin') {
            return true;
        }

        // Attorneys can update cases they are assigned to
        if ($user->role === 'attorney' && $legalCase->assigned_attorney_id === $user->id) {
            return true;
        }

        // Paralegals can update cases they participate in (limited permissions)
        if ($user->role === 'paralegal' && $legalCase->participants()->where('user_id', $user->id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LegalCase $legalCase): bool
    {
        // Only admins can delete cases
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LegalCase $legalCase): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LegalCase $legalCase): bool
    {
        return false;
    }
}
