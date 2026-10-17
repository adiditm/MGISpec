<?php

namespace App\Filament\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form->schema([
            $this->getNameFormComponent(),
            TextInput::make('username')
                ->label('Username')
                ->helperText('Dipakai untuk login. Huruf kecil, angka, titik, strip atau garis bawah.')
                ->required()
                ->minLength(3)
                ->maxLength(50)
                ->regex('/^[a-z0-9._-]+$/')
                ->unique(ignoreRecord: true),
            $this->getEmailFormComponent()
                ->helperText('Dipakai untuk reset password.'),
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),
        ]);
    }
}
