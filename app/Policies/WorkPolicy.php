<?php

namespace App\Policies;

use App\Enums\Visibility;
use App\Models\User;
use App\Models\Work;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_work');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Work $work): bool
    {
        if ($user->hasRole('student')) {
            return $user->can('view_work') && ($user->id === $work->user_id || $work->visibility === Visibility::Public);
        }

        return $user->can('view_work');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_work');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Work $work): bool
    {
        return $user->hasRole('teacher')
            ? $user->can('update_work') && $user->id === $work->group->owner_id
            : $user->can('update_work') && $user->id === $work->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Work $work): bool
    {
        return $user->hasRole('teacher')
            ? $user->can('delete_work') && $user->id === $work->group->owner_id
            : $user->can('delete_work');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_work');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Work $work): bool
    {
        return $user->can('{{ ForceDelete }}');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('{{ ForceDeleteAny }}');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Work $work): bool
    {
        return $user->can('{{ Restore }}');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('{{ RestoreAny }}');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Work $work): bool
    {
        return $user->can('replicate_work');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_work');
    }
}
