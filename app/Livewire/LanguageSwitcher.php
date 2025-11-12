<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LanguageSwitcher extends Component
{
    /**
     * Current locale
     *
     * @var string
     */
    public string $currentLocale;

    /**
     * Initialize component
     *
     * @return void
     */
    public function mount(): void
    {
        $this->currentLocale = App::getLocale();
    }

    /**
     * Switch application language
     *
     * @param string $locale
     * @return void
     */
    public function switchLanguage(string $locale): void
    {
        // Validate locale
        if (!in_array($locale, config('app.available_locales', ['en', 'ar']))) {
            return;
        }

        // Update session
        Session::put('locale', $locale);

        // Update user preference if authenticated
        if ($user = Auth::user()) {
            $user->setPreferredLanguage($locale);
        }

        // Set application locale
        App::setLocale($locale);
        $this->currentLocale = $locale;

        // Redirect to refresh the page with new locale
        $this->redirect(request()->header('Referer'));
    }

    /**
     * Render the component
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.language-switcher', [
            'locales' => config('app.available_locales', ['en', 'ar']),
            'localeNames' => config('app.locale_names', [
                'en' => 'English',
                'ar' => 'العربية',
            ]),
        ]);
    }
}
