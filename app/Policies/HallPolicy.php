<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Hall;
use Illuminate\Auth\Access\HandlesAuthorization;

class HallPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Hall');
    }

    public function view(AuthUser $authUser, Hall $hall): bool
    {
        return $authUser->can('View:Hall');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Hall');
    }

    public function update(AuthUser $authUser, Hall $hall): bool
    {
        return $authUser->can('Update:Hall');
    }

    public function delete(AuthUser $authUser, Hall $hall): bool
    {
        return $authUser->can('Delete:Hall');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Hall');
    }

    public function restore(AuthUser $authUser, Hall $hall): bool
    {
        return $authUser->can('Restore:Hall');
    }

    public function forceDelete(AuthUser $authUser, Hall $hall): bool
    {
        return $authUser->can('ForceDelete:Hall');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Hall');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Hall');
    }

    public function replicate(AuthUser $authUser, Hall $hall): bool
    {
        return $authUser->can('Replicate:Hall');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Hall');
    }

}