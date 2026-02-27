<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\Office;
use Dom\Text;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
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
                    ->sortable()
                    ->wrap(),

                TextColumn::make('UserName')
                    ->label('Username')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                BooleanColumn::make('is_active')
                    ->label('Active')
                    ->sortable(),
                
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('department_code')
                    ->label('Department Code')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('Designation')
                    ->sortable()
                    ->searchable()
                    ->wrap(),

                TextColumn::make('officeRelation.office')
                    ->label('Office')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->officeRelation
                            ? $record->officeRelation->office .
                                ($record->officeRelation->short_name
                                    ? ' (' . $record->officeRelation->short_name . ')'
                                    : '')
                            : null;
                    })
                    ->sortable()
                    ->searchable()
                    ->wrap(),

                TextColumn::make('cats')
                    ->label('Cats number')
                    ->searchable(),
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
                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->boolean(),
                SelectFilter::make('department_code')
                    ->label('Department Code')
                    ->options(
                        Office::query()
                            ->pluck('department_code', 'department_code')
                            ->toArray()
                    )
                    ->searchable(),
                SelectFilter::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->searchable()
                    ->preload(),
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
