<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\HallCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class HallCategoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:HallCategory');
    }

    public function view(AuthUser $authUser, HallCategory $hallCategory): bool
    {
        return $authUser->can('View:HallCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:HallCategory');
    }

    public function update(AuthUser $authUser, HallCategory $hallCategory): bool
    {
        return $authUser->can('Update:HallCategory');
    }

    public function delete(AuthUser $authUser, HallCategory $hallCategory): bool
    {
        return $authUser->can('Delete:HallCategory');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:HallCategory');
    }

    public function restore(AuthUser $authUser, HallCategory $hallCategory): bool
    {
        return $authUser->can('Restore:HallCategory');
    }

    public function forceDelete(AuthUser $authUser, HallCategory $hallCategory): bool
    {
        return $authUser->can('ForceDelete:HallCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:HallCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:HallCategory');
    }

    public function replicate(AuthUser $authUser, HallCategory $hallCategory): bool
    {
        return $authUser->can('Replicate:HallCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:HallCategory');
    }

}