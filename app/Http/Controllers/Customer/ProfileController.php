<?php

declare(strict_types=1);

/**
 * Customer Profile Controller
 *
 * Handles customer profile management including:
 * - Viewing profile
 * - Updating profile information
 * - Changing password
 * - Deleting account
 *
 * @package App\Http\Controllers\Customer
 * @version 1.0.0
 * @author Majalis Development Team
 */

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the customer's profile.
     *
     * @return View
     */
    public function show(): View
    {
        $user = Auth::user();

        return view('customer.profile', compact('user'));
    }

    /**
     * Update the customer's profile information.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Validate the request
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
        ], [
            'name.required' => __('The name field is required.'),
            'email.required' => __('The email field is required.'),
            'email.email' => __('Please enter a valid email address.'),
            'email.unique' => __('This email is already taken.'),
        ]);

        // Check if email changed
        $emailChanged = $user->email !== $validated['email'];

        // Update user
        $user->fill($validated);

        // If email changed, reset verification
        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Log the update
        Log::info('Customer profile updated', [
            'user_id' => $user->id,
            'email_changed' => $emailChanged,
        ]);

        // Send verification email if email changed
        if ($emailChanged && method_exists($user, 'sendEmailVerificationNotification')) {
            $user->sendEmailVerificationNotification();
        }

        return redirect()
            ->route('customer.profile')
            ->with('success', __('Profile updated successfully.'));
    }

    /**
     * Update the customer's password.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Validate the request
        $validated = $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers(),
            ],
        ], [
            'current_password.required' => __('Please enter your current password.'),
            'current_password.current_password' => __('The current password is incorrect.'),
            'password.required' => __('Please enter a new password.'),
            'password.confirmed' => __('Password confirmation does not match.'),
            'password.min' => __('Password must be at least 8 characters.'),
        ]);

        // Update password
        $user->password = Hash::make($validated['password']);
        $user->save();

        // Log the password change
        Log::info('Customer password changed', [
            'user_id' => $user->id,
        ]);

        return redirect()
            ->route('customer.profile')
            ->with('success', __('Password updated successfully.'));
    }

    /**
     * Delete the customer's account.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Validate password
        $request->validate([
            'password' => ['required', 'string', 'current_password'],
        ], [
            'password.required' => __('Please enter your password to confirm.'),
            'password.current_password' => __('The password is incorrect.'),
        ]);

        // Check for active bookings
        $activeBookings = $user->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('booking_date', '>=', now())
            ->count();

        if ($activeBookings > 0) {
            return redirect()
                ->route('customer.profile')
                ->with('error', __('You cannot delete your account while you have active bookings. Please cancel your bookings first.'));
        }

        // Log the account deletion
        Log::warning('Customer account deleted', [
            'user_id' => $user->id,
            'email' => $user->email,
            'deleted_at' => now(),
        ]);

        // Logout the user
        Auth::logout();

        // Delete the user
        $user->delete();

        // Invalidate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', __('Your account has been deleted successfully.'));
    }
}
