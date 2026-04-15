<?php

namespace App\Policies;

use App\Models\CaseTask;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'attorney', 'paralegal']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CaseTask $task): bool
    {
        // Admins can view all tasks
        if ($user->role === 'admin') {
            return true;
        }

        // Attorneys can view tasks from cases they are assigned to
        if ($user->role === 'attorney' && $task->case && $task->case->assigned_attorney_id === $user->id) {
            return true;
        }

        // Paralegals can view tasks assigned to them or from cases they are assigned to
        if ($user->role === 'paralegal') {
            return $task->assigned_to === $user->id ||
                   ($task->case && $task->case->assigned_attorney_id === $user->id);
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
    public function update(User $user, CaseTask $task): bool
    {
        // Admins can update all tasks
        if ($user->role === 'admin') {
            return true;
        }

        // Attorneys can update tasks from cases they are assigned to
        if ($user->role === 'attorney' && $task->case && $task->case->assigned_attorney_id === $user->id) {
            return true;
        }

        // Paralegals can update tasks assigned to them
        if ($user->role === 'paralegal' && $task->assigned_to === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CaseTask $task): bool
    {
        // Only admins and attorneys can delete tasks
        return in_array($user->role, ['admin', 'attorney']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CaseTask $task): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CaseTask $task): bool
    {
        return false;
    }
}
