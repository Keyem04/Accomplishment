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
                TextColumn::make('recid')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('FullName')
                    ->label('Full Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('UserName')
                    ->label('Username')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Designation')
                    ->sortable(),

                TextColumn::make('department_code')
                    ->label('Department Code')
                    ->sortable(),

                TextColumn::make('UserType')
                    ->label('User Type')
                    ->sortable(),

                BooleanColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),

                TextColumn::make('passworddate')
                    ->label('Password Set Date')
                    ->sortable(),

                TextColumn::make('password_expiry')
                    ->label('Password Expiry')
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])

            ->recordActions([
                EditAction::make(),
                Impersonate::make(),
            ])

            

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
