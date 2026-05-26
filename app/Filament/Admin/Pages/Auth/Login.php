<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages\Auth;

use Filament\Schemas\Schema;
use MarcoGermani87\FilamentCaptcha\Forms\Components\CaptchaField;

class Login extends \Filament\Auth\Pages\Login
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
                CaptchaField::make('captcha'),
            ])
            ->statePath('data');
    }
}
