<?php

namespace App\Filament\Resources\AccomplishmentHeaders\Schemas;

use App\Models\Office;
use App\Models\ProgramAndProject;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Operation;
use Illuminate\Validation\Rule;

class AccomplishmentHeaderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
        
            ->components([

                Section::make('Report Information')
                    ->icon('heroicon-o-document-text')
                    ->schema([

                    // 🔹 Header fields
                    // Select::make('department_id')
                    //     ->label('Department')
                    //     ->options(Office::orderBy('office')->pluck('office', 'id'))
                    //     ->searchable()
                    //     ->preload()
                    //     ->required()
                    //     ->columnSpanFull(),

                    Select::make('department_id')
                        ->label('Department')
                        ->options(function ($record) {
                            if ($record) {
                                // ✅ Editing: load the office of the record being viewed, not the logged-in user's
                                return Office::where('id', $record->department_id)
                                            ->pluck('office', 'id')
                                            ->toArray();
                            }

                            // ✅ Creating: load the logged-in user's own department
                            $userDeptCode = auth()->user()->department_code;
                            return Office::where('department_code', $userDeptCode)
                                        ->pluck('office', 'id')
                                        ->toArray();
                        })
                        ->default(function () {
                            // Default for new records = logged-in user's office id
                            return Office::where('department_code', auth()->user()->department_code)
                                        ->value('id');
                        })
                        ->searchable()
                        ->preload()
                        ->required()
                        ->disabled()
                        ->dehydrated()
                        ->columnSpanFull(),

                    Select::make('reporting_month')
                        ->label('Reporting Month')
                        // ->disabled(fn ($record) => $record?->status === 'submitted')
                        ->disabled(fn ($record) => $record?->exists)
                        ->options(fn () => collect(range(1,12))
                            ->mapWithKeys(fn($m) => [$m => date('F', mktime(0,0,0,$m,1))])
                            ->toArray())
                        ->required()
                        ->rule(function ($get, $record) {
                            return Rule::unique('accomplishment_headers', 'reporting_month')
                                ->where(function ($query) use ($get) {
                                    return $query
                                        ->where('reporting_year', $get('reporting_year'))
                                        ->where('department_id', $get('department_id')); // 👈 scoped to their department
                                })
                                ->ignore($record?->id);
                        }),

                    Select::make('reporting_year')
                        ->label('Reporting Year')
                        // ->disabled(fn ($record) => $record?->status === 'submitted')
                        ->disabled(fn ($record) => $record?->exists)
                        ->options([
                            now()->year => now()->year,
                        ])
                        ->default(now()->year)
                        ->required(),

                    ])
                        ->columns(2)
                        ->columnSpanFull(),


                Section::make('Signatories')
                    ->schema([

                        TextInput::make('prepared_by')
                            ->label('Prepared By')
                            ->prefixIcon('heroicon-o-user')
                            ->helperText('This name will appear in the printed report')
                            ->required()
                            ->maxLength(150),
                            // ->disabled(fn ($record) => $record?->status === 'submitted'),

                        TextInput::make('noted_by')
                            ->label('Noted By')
                            ->prefixIcon('heroicon-o-user')
                            ->helperText('This name will appear in the printed report')
                            ->required()
                            ->maxLength(150)
                            // ->disabled(fn ($record) => $record?->status === 'submitted'),

                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
