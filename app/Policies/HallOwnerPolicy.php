<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\HallOwner;
use Illuminate\Auth\Access\HandlesAuthorization;

class HallOwnerPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HallOwner');
    }

    public function view(AuthUser $authUser, HallOwner $hallOwner): bool
    {
        return $authUser->can('View:HallOwner');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HallOwner');
    }

    public function update(AuthUser $authUser, HallOwner $hallOwner): bool
    {
        return $authUser->can('Update:HallOwner');
    }

    public function delete(AuthUser $authUser, HallOwner $hallOwner): bool
    {
        return $authUser->can('Delete:HallOwner');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:HallOwner');
    }

    public function restore(AuthUser $authUser, HallOwner $hallOwner): bool
    {
        return $authUser->can('Restore:HallOwner');
    }

    public function forceDelete(AuthUser $authUser, HallOwner $hallOwner): bool
    {
        return $authUser->can('ForceDelete:HallOwner');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HallOwner');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HallOwner');
    }

    public function replicate(AuthUser $authUser, HallOwner $hallOwner): bool
    {
        return $authUser->can('Replicate:HallOwner');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HallOwner');
    }

}