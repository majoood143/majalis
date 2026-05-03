<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ExtraService;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExtraServicePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ExtraService');
    }

    public function view(AuthUser $authUser, ExtraService $extraService): bool
    {
        return $authUser->can('View:ExtraService');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ExtraService');
    }

    public function update(AuthUser $authUser, ExtraService $extraService): bool
    {
        return $authUser->can('Update:ExtraService');
    }

    public function delete(AuthUser $authUser, ExtraService $extraService): bool
    {
        return $authUser->can('Delete:ExtraService');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ExtraService');
    }

    public function restore(AuthUser $authUser, ExtraService $extraService): bool
    {
        return $authUser->can('Restore:ExtraService');
    }

    public function forceDelete(AuthUser $authUser, ExtraService $extraService): bool
    {
        return $authUser->can('ForceDelete:ExtraService');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ExtraService');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ExtraService');
    }

    public function replicate(AuthUser $authUser, ExtraService $extraService): bool
    {
        return $authUser->can('Replicate:ExtraService');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ExtraService');
    }

}