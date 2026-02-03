<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('FullName')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(50),

                TextInput::make('Designation')
                    ->label('Designation')
                    ->maxLength(50)
                    ->nullable(),

                TextInput::make('UserName')
                    ->label('Username')
                    ->required()
                    ->maxLength(30)
                    ->unique(
                        table: User::class,
                        column: 'UserName',
                        ignorable: fn ($record) => $record
                    ),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(100)
                    ->nullable(),

                TextInput::make('department_code')
                    ->label('Department Code')
                    ->maxLength(3)
                    ->nullable(),

                Select::make('UserType')
                    ->label('User Type')
                    ->options([
                        'ADMIN' => 'Admin',
                        'USER'  => 'User',
                    ])
                    ->nullable(),

                Select::make('is_active')
                    ->label('Account Status')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ])
                    ->default(1)
                    ->required(),

                TextInput::make('UserPassword')
                    ->label('Password')
                    ->password()
                    ->required(fn ($record) => ! $record)
                    ->dehydrateStateUsing(fn ($state) => md5($state))
                    ->dehydrated(fn ($state) => filled($state)),

                TextInput::make('laravel_password')
                    ->label('Laravel Password (Optional)')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                    ->dehydrated(fn ($state) => filled($state)),

                DatePicker::make('passworddate')
                    ->label('Password Date')
                    ->nullable(),

                DatePicker::make('password_expiry')
                    ->label('Password Expiry')
                    ->nullable(),
            ]);
    }
}
