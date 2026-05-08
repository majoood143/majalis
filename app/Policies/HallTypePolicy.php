<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\HallType;
use Illuminate\Auth\Access\HandlesAuthorization;

class HallTypePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HallType');
    }

    public function view(AuthUser $authUser, HallType $hallType): bool
    {
        return $authUser->can('View:HallType');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HallType');
    }

    public function update(AuthUser $authUser, HallType $hallType): bool
    {
        return $authUser->can('Update:HallType');
    }

    public function delete(AuthUser $authUser, HallType $hallType): bool
    {
        return $authUser->can('Delete:HallType');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:HallType');
    }

    public function restore(AuthUser $authUser, HallType $hallType): bool
    {
        return $authUser->can('Restore:HallType');
    }

    public function forceDelete(AuthUser $authUser, HallType $hallType): bool
    {
        return $authUser->can('ForceDelete:HallType');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HallType');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HallType');
    }

    public function replicate(AuthUser $authUser, HallType $hallType): bool
    {
        return $authUser->can('Replicate:HallType');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HallType');
    }

}