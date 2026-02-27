<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Office;
use App\Models\Permission;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('FullName')
                    ->required(fn ($livewire, $record) => !$record),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(fn ($livewire, $record) => !$record)
                    ->required(fn ($livewire, $record) => !$record),

                Select::make('is_active')
                    ->label('Active')
                    ->options([
                        1 => 'Yes',
                        0 => 'No',
                    ])
                    ->required(fn ($livewire, $record) => !$record),

                TextInput::make('cats')
                    ->required(fn ($livewire, $record) => !$record),

                // Select::make('department_code')
                //     ->label('Department Code')
                //     ->options(fn () => Office::pluck('department_code', 'department_code'))
                //     ->searchable()
                //     ->reactive()
                //     ->afterStateUpdated(function (callable $set, $state) {
                //         $office = Office::where('department_code', $state)->first();
                //         $set('office', $office?->office);
                //     }),

                // Select::make('department_code')
                //     ->label('Office')
                //     ->options(
                //         Office::orderBy('office')
                //             ->get()
                //             ->mapWithKeys(function ($office) {
                //                 $label = $office->office;

                //                 if (!empty($office->short_name)) {
                //                     $label .= ' (' . $office->short_name . ')';
                //                 }

                //                 return [
                //                     $office->department_code => $label
                //                 ];
                //             })
                //             ->toArray()
                //     )
                //     ->searchable()
                //     ->preload(),
                
                Select::make('department_code')
                    ->label('Office')
                    ->relationship('officeRelation', 'office')
                    ->getOptionLabelFromRecordUsing(fn ($record) =>
                        $record->office .
                        ($record->short_name ? ' (' . $record->short_name . ')' : '')
                    )
                    ->searchable()
                    ->preload()
                    ->required(fn ($livewire, $record) => !$record),
                TextInput::make('UserName')
                    ->required(fn ($livewire, $record) => !$record),
                TextInput::make('password_input')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->required(fn ($livewire, $record) => !$record)
                    ->dehydrated(true) // IMPORTANT
                    ->dehydrateStateUsing(fn ($state) => $state ?: null)
                    ->afterStateHydrated(fn ($component) => $component->state('')),
                Select::make('roles')
                    ->label('Roles')
                    ->multiple()
                    ->relationship('roles', 'name')
                    ->preload()
                    ->searchable()
                    ->afterStateUpdated(function (callable $set, $state) {
                        // When roles change, update the permissions list
                        $rolePermissions = Permission::whereHas('roles', function ($q) use ($state) {
                            $q->whereIn('roles.id', $state);
                        })->pluck('id')->toArray();

                        $set('permissions', $rolePermissions);
                    })
                    ->columns(1),
            ]);
    }
}
