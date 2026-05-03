<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\HallAvailability;
use Illuminate\Auth\Access\HandlesAuthorization;

class HallAvailabilityPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HallAvailability');
    }

    public function view(AuthUser $authUser, HallAvailability $hallAvailability): bool
    {
        return $authUser->can('View:HallAvailability');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HallAvailability');
    }

    public function update(AuthUser $authUser, HallAvailability $hallAvailability): bool
    {
        return $authUser->can('Update:HallAvailability');
    }

    public function delete(AuthUser $authUser, HallAvailability $hallAvailability): bool
    {
        return $authUser->can('Delete:HallAvailability');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:HallAvailability');
    }

    public function restore(AuthUser $authUser, HallAvailability $hallAvailability): bool
    {
        return $authUser->can('Restore:HallAvailability');
    }

    public function forceDelete(AuthUser $authUser, HallAvailability $hallAvailability): bool
    {
        return $authUser->can('ForceDelete:HallAvailability');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HallAvailability');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HallAvailability');
    }

    public function replicate(AuthUser $authUser, HallAvailability $hallAvailability): bool
    {
        return $authUser->can('Replicate:HallAvailability');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HallAvailability');
    }

}