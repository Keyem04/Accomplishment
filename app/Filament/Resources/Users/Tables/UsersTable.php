<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use STS\FilamentImpersonate\Actions\Impersonate;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('recid')
                //     ->label('ID')
                //     ->sortable(),

                TextColumn::make('FullName')
                    ->label('Full Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('UserName')
                    ->label('Username')
                    ->searchable()
                    ->sortable(),

                BooleanColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),
                
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('department_code')
                    ->label('Department Code')
                    ->sortable(),

                TextColumn::make('Designation')
                    ->sortable(),

                // TextColumn::make('UserType')
                //     ->label('User Type')
                //     ->sortable(),

                // TextColumn::make('passworddate')
                //     ->label('Password Set Date')
                //     ->sortable(),

                // TextColumn::make('password_expiry')
                //     ->label('Password Expiry')
                //     ->sortable(),

                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state) => collect([
                        'primary',
                        'success',
                        'warning',
                        'danger',
                        'info',
                        'gray',
                    ])[abs(crc32($state)) % 6]),
            ])
            ->filters([
                //
            ])

            ->recordActions([
                EditAction::make(),
                Impersonate::make()
                    ->successRedirectUrl(url('/accomplishment-headers')),
            ])

            

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
