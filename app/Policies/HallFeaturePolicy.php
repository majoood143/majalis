<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\HallFeature;
use Illuminate\Auth\Access\HandlesAuthorization;

class HallFeaturePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HallFeature');
    }

    public function view(AuthUser $authUser, HallFeature $hallFeature): bool
    {
        return $authUser->can('View:HallFeature');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HallFeature');
    }

    public function update(AuthUser $authUser, HallFeature $hallFeature): bool
    {
        return $authUser->can('Update:HallFeature');
    }

    public function delete(AuthUser $authUser, HallFeature $hallFeature): bool
    {
        return $authUser->can('Delete:HallFeature');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:HallFeature');
    }

    public function restore(AuthUser $authUser, HallFeature $hallFeature): bool
    {
        return $authUser->can('Restore:HallFeature');
    }

    public function forceDelete(AuthUser $authUser, HallFeature $hallFeature): bool
    {
        return $authUser->can('ForceDelete:HallFeature');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HallFeature');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HallFeature');
    }

    public function replicate(AuthUser $authUser, HallFeature $hallFeature): bool
    {
        return $authUser->can('Replicate:HallFeature');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HallFeature');
    }

}