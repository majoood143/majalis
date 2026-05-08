<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use Guava\FilamentKnowledgeBase\Models\FlatfileNode;
use Illuminate\Auth\Access\HandlesAuthorization;

class FlatfileNodePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FlatfileNode');
    }

    public function view(AuthUser $authUser, FlatfileNode $flatfileNode): bool
    {
        return $authUser->can('View:FlatfileNode');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FlatfileNode');
    }

    public function update(AuthUser $authUser, FlatfileNode $flatfileNode): bool
    {
        return $authUser->can('Update:FlatfileNode');
    }

    public function delete(AuthUser $authUser, FlatfileNode $flatfileNode): bool
    {
        return $authUser->can('Delete:FlatfileNode');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:FlatfileNode');
    }

    public function restore(AuthUser $authUser, FlatfileNode $flatfileNode): bool
    {
        return $authUser->can('Restore:FlatfileNode');
    }

    public function forceDelete(AuthUser $authUser, FlatfileNode $flatfileNode): bool
    {
        return $authUser->can('ForceDelete:FlatfileNode');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FlatfileNode');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FlatfileNode');
    }

    public function replicate(AuthUser $authUser, FlatfileNode $flatfileNode): bool
    {
        return $authUser->can('Replicate:FlatfileNode');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FlatfileNode');
    }

}