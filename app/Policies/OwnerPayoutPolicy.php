<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\OwnerPayout;
use Illuminate\Auth\Access\HandlesAuthorization;

class OwnerPayoutPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:OwnerPayout');
    }

    public function view(AuthUser $authUser, OwnerPayout $ownerPayout): bool
    {
        return $authUser->can('View:OwnerPayout');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:OwnerPayout');
    }

    public function update(AuthUser $authUser, OwnerPayout $ownerPayout): bool
    {
        return $authUser->can('Update:OwnerPayout');
    }

    public function delete(AuthUser $authUser, OwnerPayout $ownerPayout): bool
    {
        return $authUser->can('Delete:OwnerPayout');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:OwnerPayout');
    }

    public function restore(AuthUser $authUser, OwnerPayout $ownerPayout): bool
    {
        return $authUser->can('Restore:OwnerPayout');
    }

    public function forceDelete(AuthUser $authUser, OwnerPayout $ownerPayout): bool
    {
        return $authUser->can('ForceDelete:OwnerPayout');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:OwnerPayout');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:OwnerPayout');
    }

    public function replicate(AuthUser $authUser, OwnerPayout $ownerPayout): bool
    {
        return $authUser->can('Replicate:OwnerPayout');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:OwnerPayout');
    }

}