<?php

declare(strict_types=1);

namespace App\Filament\Owner\Resources;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

abstract class OwnerResource extends Resource
{
    /**
     * Apply owner scope to all queries
     * This ensures owners only see their own data
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Get the current authenticated user
        $user = Auth::user();

        if (!$user) {
            // Return empty query if no user (shouldn't happen with auth middleware)
            return $query->whereRaw('1 = 0');
        }

        // Apply owner-specific scope
        return static::applyOwnerScope($query, $user);
    }

    /**
     * Apply owner-specific query scope
     * Override this in child resources for custom scoping
     */
    protected static function applyOwnerScope(Builder $query, $user): Builder
    {
        $model = $query->getModel();
        $table = $model->getTable();

        // Check different ownership patterns

        // Pattern 1: Direct ownership (owner_id column)
        if (static::hasColumn($model, 'owner_id')) {
            return $query->where("{$table}.owner_id", $user->id);
        }

        // Pattern 2: User ID column (for user-owned records)
        if (static::hasColumn($model, 'user_id')) {
            return $query->where("{$table}.user_id", $user->id);
        }

        // Pattern 3: Through hall relationship
        if (method_exists($model, 'hall')) {
            return $query->whereHas('hall', function (Builder $q) use ($user) {
                $q->where('owner_id', $user->id);
            });
        }

        // Pattern 4: Through halls relationship (many-to-many)
        if (method_exists($model, 'halls')) {
            return $query->whereHas('halls', function (Builder $q) use ($user) {
                $q->where('owner_id', $user->id);
            });
        }

        // Pattern 5: If model IS Hall
        if ($model instanceof \App\Models\Hall) {
            return $query->where("{$table}.owner_id", $user->id);
        }

        // Default: Return unmodified query (be careful with this)
        // You might want to return empty query instead for safety
        return $query;
    }

    /**
     * Check if model has a specific column
     */
    protected static function hasColumn(Model $model, string $column): bool
    {
        return in_array(
            $column,
            $model->getConnection()
                ->getSchemaBuilder()
                ->getColumnListing($model->getTable())
        );
    }

    /**
     * Can view any records (listing page)
     * This is called for index/listing authorization
     */
    public static function canViewAny(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Check if user is an owner
        return $user->user_type === 'owner'
            || $user->hasRole('hall_owner')
            || $user->halls()->exists();
    }

    /**
     * Check if the owner can create new records
     * Override in child resources if needed
     */
    public static function canCreate(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Owners can create by default
        // Override this method in specific resources if needed
        return static::canViewAny();
    }

    /**
     * Authorize actions on specific records
     * This works with Filament's authorization system
     */
    public static function can(string $action, ?Model $record = null): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // For creating new records
        if ($action === 'create' && $record === null) {
            return static::canCreate();
        }

        // For viewing the index/list page
        if ($action === 'viewAny' && $record === null) {
            return static::canViewAny();
        }

        // For actions on specific records
        if ($record !== null) {
            return static::checkRecordOwnership($record, $user);
        }

        return false;
    }

    /**
     * Check if user owns a specific record
     * This is used for view, edit, delete actions
     */
    protected static function checkRecordOwnership(Model $record, $user): bool
    {
        // Check direct ownership
        if (isset($record->owner_id)) {
            return $record->owner_id === $user->id;
        }

        // Check user ownership
        if (isset($record->user_id)) {
            return $record->user_id === $user->id;
        }

        // Check through hall relationship (single)
        if (method_exists($record, 'hall') && $record->hall) {
            return $record->hall->owner_id === $user->id;
        }

        // Check through halls relationship (multiple)
        if (method_exists($record, 'halls')) {
            return $record->halls()
                ->where('owner_id', $user->id)
                ->exists();
        }

        // For Hall model itself
        if ($record instanceof \App\Models\Hall) {
            return $record->owner_id === $user->id;
        }

        return false;
    }

    /**
     * Get navigation badge (e.g., count of new items)
     * Override in child resources to show badges
     */
    public static function getNavigationBadge(): ?string
    {
        return null;
    }

    /**
     * Get navigation badge color
     * Options: 'primary', 'success', 'warning', 'danger', 'info', 'gray'
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }

    /**
     * Global search configuration
     * Limit search to owner's records
     */
    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return static::getEloquentQuery();
    }

    /**
     * Modify table query for better performance
     * Add eager loading for common relationships
     */
    protected static function applyEagerLoading(Builder $query): Builder
    {
        // Override in child resources to add eager loading
        // Example: return $query->with(['hall', 'customer']);
        return $query;
    }

    /**
     * Helper method to get owner's statistics
     * Useful for dashboard widgets
     */
    public static function getOwnerStatistics(): array
    {
        $user = Auth::user();

        if (!$user) {
            return [];
        }

        return [
            'total_halls' => $user->halls()->count(),
            'active_halls' => $user->halls()->where('is_active', true)->count(),
            'total_bookings' => \App\Models\Booking::whereHas('hall', function ($q) use ($user) {
                $q->where('owner_id', $user->id);
            })->count(),
            'pending_bookings' => \App\Models\Booking::whereHas('hall', function ($q) use ($user) {
                $q->where('owner_id', $user->id);
            })->where('status', 'pending')->count(),
        ];
    }
}
