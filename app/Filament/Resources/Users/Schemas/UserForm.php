<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(User::class, 'email')
                    ->maxLength(255),
                TextInput::make('department_code')
                    ->label('Department Code')
                    ->maxLength(10),
                TextInput::make('password')
                    ->password()
                    ->required(fn ($record) => !$record) // required only for new users
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state)),
                DateTimePicker::make('email_verified_at')
                    ->label('Email Verified At')
                    ->nullable(),
            ]);
    }
}
