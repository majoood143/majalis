<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EditProfile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static string $view = 'filament.pages.edit-profile';

    protected static ?int $navigationSort = 100;

    public ?array $data = [];

    /**
     * Mount the component
     */
    public function mount(): void
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        $user = Auth::user();

        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'language_preference' => $user->language_preference ?? config('app.locale', 'en'),
        ]);
    }

    /**
     * Define the form schema
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('Profile Information'))
                    ->description(__('Update your profile information and language preference.'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament.forms.name'))
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label(__('filament.forms.email'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Select::make('language_preference')
                            ->label(__('Language Preference'))
                            ->options([
                                'en' => 'English',
                                'ar' => 'العربية',
                            ])
                            ->required()
                            ->native(false)
                            ->helperText(__('Select your preferred language for the admin panel'))
                            ->reactive()
                            ->afterStateUpdated(fn($state) => $this->updateLanguagePreference($state)),
                    ]),

                Section::make(__('Update Password'))
                    ->description(__('Ensure your account is using a long, random password.'))
                    ->schema([
                        TextInput::make('current_password')
                            ->label(__('Current Password'))
                            ->password()
                            ->required(fn($get) => filled($get('new_password')))
                            ->currentPassword(),

                        TextInput::make('new_password')
                            ->label(__('New Password'))
                            ->password()
                            ->nullable()
                            ->minLength(8)
                            ->confirmed(),

                        TextInput::make('new_password_confirmation')
                            ->label(__('Confirm Password'))
                            ->password()
                            ->requiredWith('new_password'),
                    ]),
            ])
            ->statePath('data');
    }

    /**
     * Update language preference immediately
     */
    protected function updateLanguagePreference(string $language): void
    {
        session()->put('locale', $language);
        app()->setLocale($language);
    }

    /**
     * Save profile changes
     */
    public function save(): void
    {
        $data = $this->form->getState();

        $user = Auth::user();

        if (!$user) {
            Notification::make()
                ->title(__('Error: User not authenticated'))
                ->danger()
                ->send();
            return;
        }

        // Update basic info
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'language_preference' => $data['language_preference'],
        ]);

        // Update password if provided
        if (filled($data['new_password'])) {
            $user->update([
                'password' => Hash::make($data['new_password']),
            ]);
        }

        Notification::make()
            ->title(__('Profile updated successfully'))
            ->success()
            ->send();

        // Redirect to refresh page with new language if changed
        if ($user->wasChanged('language_preference')) {
            $this->redirect(static::getUrl());
        }
    }

    /**
     * Get the page title
     */
    public static function getNavigationLabel(): string
    {
        return __('Profile');
    }

    /**
     * Define form actions
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament.actions.save'))
                ->action('save'),
        ];
    }
}
