<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\HallImage;
use Illuminate\Auth\Access\HandlesAuthorization;

class HallImagePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HallImage');
    }

    public function view(AuthUser $authUser, HallImage $hallImage): bool
    {
        return $authUser->can('View:HallImage');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HallImage');
    }

    public function update(AuthUser $authUser, HallImage $hallImage): bool
    {
        return $authUser->can('Update:HallImage');
    }

    public function delete(AuthUser $authUser, HallImage $hallImage): bool
    {
        return $authUser->can('Delete:HallImage');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:HallImage');
    }

    public function restore(AuthUser $authUser, HallImage $hallImage): bool
    {
        return $authUser->can('Restore:HallImage');
    }

    public function forceDelete(AuthUser $authUser, HallImage $hallImage): bool
    {
        return $authUser->can('ForceDelete:HallImage');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HallImage');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HallImage');
    }

    public function replicate(AuthUser $authUser, HallImage $hallImage): bool
    {
        return $authUser->can('Replicate:HallImage');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HallImage');
    }

}