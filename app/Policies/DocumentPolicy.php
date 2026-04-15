<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
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
    public function view(User $user, Document $document): bool
    {
        // Admins can view all documents
        if ($user->role === 'admin') {
            return true;
        }

        // Attorneys can view documents from cases they are assigned to
        if ($user->role === 'attorney' && $document->case && $document->case->assigned_attorney_id === $user->id) {
            return true;
        }

        // Clients can view documents from cases they are associated with
        if ($user->role === 'client' && $document->case && $document->case->client_id === $user->id) {
            return true;
        }

        // Paralegals can view documents from cases they are assigned to or participate in
        if ($user->role === 'paralegal' && $document->case) {
            return $document->case->participants()->where('user_id', $user->id)->exists() ||
                   $document->case->assigned_attorney_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->role, ['admin', 'attorney', 'paralegal']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Document $document): bool
    {
        // Admins can update all documents
        if ($user->role === 'admin') {
            return true;
        }

        // Attorneys can update documents from cases they are assigned to
        if ($user->role === 'attorney' && $document->case && $document->case->assigned_attorney_id === $user->id) {
            return true;
        }

        // Paralegals can update documents from cases they participate in
        if ($user->role === 'paralegal' && $document->case && $document->case->participants()->where('user_id', $user->id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Document $document): bool
    {
        // Only admins and attorneys can delete documents
        return in_array($user->role, ['admin', 'attorney']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Document $document): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Document $document): bool
    {
        return false;
    }
}
