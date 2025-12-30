<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;

class EnsureUserIsOwner
{
    /**
     * Handle an incoming request.
     * Ensures only hall owners can access the owner panel
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the authenticated user
        $user = Auth::user();

        // If no user, redirect to login
        if (!$user) {
            return redirect()->route('filament.owner.auth.login');
        }

        // Check if user is a hall owner (either by role or by having halls)
        $isOwner = $this->checkIfUserIsOwner($user);

        if (!$isOwner) {
            // Log unauthorized access attempt
            activity()
                ->causedBy($user)
                ->event('unauthorized_owner_access')
                ->log('User attempted to access owner panel without authorization');

            // Show notification
            Notification::make()
                ->title(__('owner.errors.unauthorized_title'))
                ->body(__('owner.errors.unauthorized_body'))
                ->danger()
                ->persistent()
                ->send();

            // Logout and redirect
            Auth::logout();
            return redirect()->route('filament.owner.auth.login');
        }

        // Check if owner account is active
        if (!$this->checkIfOwnerIsActive($user)) {
            Notification::make()
                ->title(__('owner.errors.account_suspended_title'))
                ->body(__('owner.errors.account_suspended_body'))
                ->danger()
                ->persistent()
                ->send();

            Auth::logout();
            return redirect()->route('filament.owner.auth.login');
        }

        // Check for required profile completion
        if (!$this->checkProfileCompletion($user)) {
            // Allow access but show persistent notification
            Notification::make()
                ->title(__('owner.warnings.complete_profile_title'))
                ->body(__('owner.warnings.complete_profile_body'))
                ->warning()
                ->persistent()
                ->actions([
                    \Filament\Notifications\Actions\Action::make('complete')
                        ->label(__('owner.actions.complete_profile'))
                        ->url(route('filament.owner.pages.profile'))
                ])
                ->send();
        }

        return $next($request);
    }

    /**
     * Check if user is a hall owner
     */
    protected function checkIfUserIsOwner($user): bool
    {
        // Method 1: Check by role (if using Filament Shield)
        if (method_exists($user, 'hasRole') && $user->hasRole('hall_owner')) {
            return true;
        }

        // Method 2: Check by panel access (Filament 3.3+)
        if (method_exists($user, 'canAccessPanel')) {
            $ownerPanel = Filament::getPanel('owner');
            if ($user->canAccessPanel($ownerPanel)) {
                return true;
            }
        }

        // Method 3: Check if user owns any halls
        if ($user->halls()->exists()) {
            return true;
        }

        // Method 4: Check user type field
        if (isset($user->user_type) && $user->user_type === 'owner') {
            return true;
        }

        return false;
    }

    /**
     * Check if owner account is active
     */
    protected function checkIfOwnerIsActive($user): bool
    {
        // Check suspension status
        if ($user->is_suspended ?? false) {
            return false;
        }

        // Check active status
        if (isset($user->is_active) && !$user->is_active) {
            return false;
        }

        // Check if all owner's halls are inactive (optional)
        $hasActiveHall = $user->halls()
            ->where('is_active', true)
            ->exists();

        if (!$hasActiveHall) {
            // Owner has no active halls - you might want to allow access
            // but show a warning instead of blocking
            return true; // Change to false if you want to block
        }

        return true;
    }

    /**
     * Check if owner has completed their profile
     */
    protected function checkProfileCompletion($user): bool
    {
        $requiredFields = [
            'phone',
            'email',
            'commercial_registration_number',
            'bank_account_name',
            'bank_account_number',
            'bank_name',
        ];

        foreach ($requiredFields as $field) {
            if (empty($user->{$field})) {
                return false;
            }
        }

        return true;
    }
}
