<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class HallOwnerPolicy
{
    /**
     * Perform pre-authorization check
     */
    public function before(User $user, string $ability): ?bool
    {
        // Super admins can do anything (optional)
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Must be an owner to access owner panel resources
        if (!$this->isOwner($user)) {
            return false;
        }

        return null; // Continue to specific checks
    }

    /**
     * Determine whether the user can view any models
     */
    public function viewAny(User $user): bool
    {
        return $this->isOwner($user);
    }

    /**
     * Determine whether the user can view the model
     */
    public function view(User $user, Model $model): bool
    {
        return $this->ownsRecord($user, $model);
    }

    /**
     * Determine whether the user can create models
     */
    public function create(User $user): bool
    {
        return $this->isOwner($user);
    }

    /**
     * Determine whether the user can update the model
     */
    public function update(User $user, Model $model): bool
    {
        return $this->ownsRecord($user, $model);
    }

    /**
     * Determine whether the user can delete the model
     */
    public function delete(User $user, Model $model): bool
    {
        return $this->ownsRecord($user, $model);
    }

    /**
     * Check if user is an owner
     */
    protected function isOwner(User $user): bool
    {
        return $user->user_type === 'owner'
            || $user->hasRole('hall_owner')
            || $user->halls()->exists();
    }

    /**
     * Check if user owns the record
     */
    protected function ownsRecord(User $user, Model $model): bool
    {
        // Check different ownership patterns
        if (isset($model->owner_id)) {
            return $model->owner_id === $user->id;
        }

        if (isset($model->user_id)) {
            return $model->user_id === $user->id;
        }

        if (method_exists($model, 'hall') && $model->hall) {
            return $model->hall->owner_id === $user->id;
        }

        return false;
    }
}
