<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\GuestSession;
use Illuminate\Auth\Access\HandlesAuthorization;

class GuestSessionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:GuestSession');
    }

    public function view(AuthUser $authUser, GuestSession $guestSession): bool
    {
        return $authUser->can('View:GuestSession');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:GuestSession');
    }

    public function update(AuthUser $authUser, GuestSession $guestSession): bool
    {
        return $authUser->can('Update:GuestSession');
    }

    public function delete(AuthUser $authUser, GuestSession $guestSession): bool
    {
        return $authUser->can('Delete:GuestSession');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:GuestSession');
    }

    public function restore(AuthUser $authUser, GuestSession $guestSession): bool
    {
        return $authUser->can('Restore:GuestSession');
    }

    public function forceDelete(AuthUser $authUser, GuestSession $guestSession): bool
    {
        return $authUser->can('ForceDelete:GuestSession');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:GuestSession');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:GuestSession');
    }

    public function replicate(AuthUser $authUser, GuestSession $guestSession): bool
    {
        return $authUser->can('Replicate:GuestSession');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:GuestSession');
    }

}