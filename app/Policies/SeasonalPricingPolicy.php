<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SeasonalPricing;
use Illuminate\Auth\Access\HandlesAuthorization;

class SeasonalPricingPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SeasonalPricing');
    }

    public function view(AuthUser $authUser, SeasonalPricing $seasonalPricing): bool
    {
        return $authUser->can('View:SeasonalPricing');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SeasonalPricing');
    }

    public function update(AuthUser $authUser, SeasonalPricing $seasonalPricing): bool
    {
        return $authUser->can('Update:SeasonalPricing');
    }

    public function delete(AuthUser $authUser, SeasonalPricing $seasonalPricing): bool
    {
        return $authUser->can('Delete:SeasonalPricing');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SeasonalPricing');
    }

    public function restore(AuthUser $authUser, SeasonalPricing $seasonalPricing): bool
    {
        return $authUser->can('Restore:SeasonalPricing');
    }

    public function forceDelete(AuthUser $authUser, SeasonalPricing $seasonalPricing): bool
    {
        return $authUser->can('ForceDelete:SeasonalPricing');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SeasonalPricing');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SeasonalPricing');
    }

    public function replicate(AuthUser $authUser, SeasonalPricing $seasonalPricing): bool
    {
        return $authUser->can('Replicate:SeasonalPricing');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SeasonalPricing');
    }

}