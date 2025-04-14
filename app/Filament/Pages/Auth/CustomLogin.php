<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Form;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseAuth;
use Illuminate\Validation\ValidationException;
 
class Login extends BaseAuth
{
    
   public function form(Form $form): Form
   {
       return $form
           ->schema([
                $this->getUsernameFormComponent(), 
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
           ])
            ->statePath('data');
   }

    protected function getUsernameFormComponent(): Component 
    {
        return TextInput::make('login')
            ->label('Username')
            ->required()
            ->placeholder('Masukkan username...')
            ->prefixIcon('heroicon-o-user')
            ->autofocus()
            ->extraInputAttributes([
                'tabindex' => 1,
            ]);
    } 


   protected function getCredentialsFromFormData(array $data): array
    {
        $login_type = filter_var($data['login'], FILTER_VALIDATE_EMAIL ) ? 'email' : 'username';
 
        return [
            $login_type => $data['login'],
            'password'  => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}