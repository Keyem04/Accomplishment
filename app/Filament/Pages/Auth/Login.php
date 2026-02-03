<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Schemas\Schema;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Validation\ValidationException;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;

class Login extends BaseLogin
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                $this->getUsernameFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ]);
    }

    protected function getUsernameFormComponent(): Component
    {
        return TextInput::make('UserName')
            ->label('Username')
            ->required()
            ->autocomplete('username')
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('UserPassword')
            ->label('Password')
            ->required()
            ->password();
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'UserName' => $data['UserName'],
            'UserPassword' => $data['UserPassword'],
        ];
    }

   public function authenticate(): ?LoginResponse
    {
        try {
            $data = $this->form->getState();

            // 1. Fetch user from custom connection/table
            $user = User::where('UserName', $data['UserName'])->first();

            // 2. Validate existence and custom password (MD5)
            if (! $user || md5($data['UserPassword']) !== $user->UserPassword) {
                throw ValidationException::withMessages([
                    'UserName' => __('filament-panels::pages/auth/login.messages.failed'),
                ]);
            }

            // 3. Check if account is active
            if (! $user->is_active) {
                Notification::make()
                    ->title('Account Inactive')
                    ->danger()
                    ->body('Please contact your administrator.')
                    ->send();

                return null;
            }

            // 4. Log the user in using Filament's facade
            Filament::auth()->login($user, $data['remember'] ?? false);

            // 5. Regenerate session to prevent fixation attacks
            session()->regenerate();

            return app(LoginResponse::class);

        } catch (ValidationException $e) {
            // Use generic message for security
            throw $e;
        }
    }


    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.UserName' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }

    protected function getRedirectUrl(): string
    {
        return filament()->getUrl();
    }
}