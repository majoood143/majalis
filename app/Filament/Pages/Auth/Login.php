<?php

declare(strict_types=1);

namespace App\Filament\Owner\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Forms\Form;
use Illuminate\Validation\ValidationException;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;

class Login extends BaseLogin
{
    /**
     * Get the title for the login page
     */
    public function getTitle(): string|Htmlable
    {
        return __('owner.auth.login_title');
    }

    /**
     * Get the heading for the login page
     */
    public function getHeading(): string|Htmlable
    {
        return __('owner.auth.login_heading');
    }

    /**
     * Get the subheading for the login page
     */
    public function getSubheading(): string|Htmlable|null
    {
        return __('owner.auth.login_subheading');
    }

    /**
     * Define the login form schema
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent()
                    ->label(__('owner.auth.email'))
                    ->placeholder(__('owner.auth.email_placeholder'))
                    ->helperText(__('owner.auth.email_helper'))
                    ->required()
                    ->autofocus()
                    ->extraInputAttributes(['tabindex' => 1]),

                $this->getPasswordFormComponent()
                    ->label(__('owner.auth.password'))
                    ->placeholder(__('owner.auth.password_placeholder'))
                    ->helperText(__('owner.auth.password_helper'))
                    ->required()
                    ->extraInputAttributes(['tabindex' => 2]),

                $this->getRememberFormComponent()
                    ->label(__('owner.auth.remember_me')),
            ])
            ->statePath('data');
    }

    /**
     * Authenticate the owner
     */
    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/login.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/login.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/login.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }

        $data = $this->form->getState();

        // Attempt authentication
        if (!Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            // Log failed attempt
            activity()
                ->event('owner_login_failed')
                ->withProperties(['email' => $data['email']])
                ->log('Failed owner login attempt');

            throw ValidationException::withMessages([
                'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
            ]);
        }

        $user = Filament::auth()->user();

        // Additional validation for owner accounts
        if (!$this->validateOwnerAccount($user)) {
            Filament::auth()->logout();

            throw ValidationException::withMessages([
                'data.email' => __('owner.auth.not_authorized'),
            ]);
        }

        // Log successful login
        activity()
            ->causedBy($user)
            ->event('owner_login_success')
            ->log('Owner logged in successfully');

        // Update last login timestamp
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);

        session()->regenerate();

        return app(LoginResponse::class);
    }

    /**
     * Validate that the user is an authorized owner
     */
    protected function validateOwnerAccount($user): bool
    {
        // Check if user is a hall owner
        if (!$user->halls()->exists() && !$user->hasRole('hall_owner')) {
            return false;
        }

        // Check if account is active
        if ($user->is_suspended ?? false) {
            Notification::make()
                ->title(__('owner.auth.account_suspended'))
                ->danger()
                ->persistent()
                ->send();
            return false;
        }

        return true;
    }

    /**
     * Get the credentials from form data
     */
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'email' => $data['email'],
            'password' => $data['password'],
        ];
    }
}
