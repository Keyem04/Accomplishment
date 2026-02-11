<?php

namespace App\Filament\Resources\AccomplishmentHeaders\RelationManagers;

use Filament\Tables\Table;

use Illuminate\Support\Str;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use App\Models\ProgramAndProject;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\RelationManagers\RelationManager;
use App\Filament\Resources\AccomplishmentHeaders\AccomplishmentHeaderResource;

class AccomplishmentDetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'accomplishmentdetails';

    protected static ?string $label = 'Accomplishment Details';
    protected static bool $isLazy = false;

    protected function getTableHeading(): ?string
    {
        return 'Accomplishment Details';
    }
    
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Select::make('ppa_id')
                //     ->label('PPA')
                //     ->options(ProgramAndProject::whereNotNull('paps_desc')
                //         ->orderBy('paps_desc')
                //         ->pluck('paps_desc', 'id')
                //         ->toArray())
                //     ->searchable()
                //     ->preload()
                //     ->required()
                //     ->columnSpanFull(),

                Select::make('ppa_id')
                    ->label('PPA')
                    ->options(function () {
                        $userDeptCode = auth()->user()->department_code;

                        return ProgramAndProject::whereNotNull('paps_desc')
                            ->where('department_code', $userDeptCode) // filter by user's department
                            ->orderBy('paps_desc')
                            ->pluck('paps_desc', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),

                DatePicker::make('date')
                    ->label('Date')
                    ->live() // Make it reactive
                    ->locale('en-US') // Force US format
                    ->native(false)   // Use JS picker instead of browser native
                    ->displayFormat('m/d/Y') 
                    ->placeholder('mm/dd/yyyy') 
                    ->required()
                    ->rule(function ($get) {
                        $reportingMonth = $this->ownerRecord->reporting_month;
                        $reportingYear = $this->ownerRecord->reporting_year;

                        return function ($attribute, $value, $fail) use ($reportingMonth, $reportingYear) {
                            if (date('m', strtotime($value)) != $reportingMonth || date('Y', strtotime($value)) != $reportingYear) {
                                $fail("The date must be within the reporting period month and year.");
                            }
                        };
                    }),

                TextInput::make('title_of_accomplishment')
                    ->label('Title of Accomplishment')
                    ->required()
                    ->maxLength(255),

                Textarea::make('brief_description')
                    ->label('Brief Description')
                    ->required()
                    ->rows(3),

                Textarea::make('scope')
                    ->label('Beneficiaries / Scope')
                    ->required()
                    ->rows(3),

                Textarea::make('results')
                    ->label('Impact / Results (Quantifiable)')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),

                FileUpload::make('mov')
                    ->label('Mode of Verification (MOV) 1-2 pictures only per accomplishment')
                    ->image()
                    ->multiple()
                    ->maxFiles(2)
                    ->maxSize(5120) // 5MB
                    ->directory(fn () => 'accomplishments/' . now()->format('Y/F'))
                    ->columnSpanFull()
                    ->downloadable()
                    ->openable()
                    ->previewable()
                    ->rules([
                        fn () => function (string $attribute, $value, $fail) {
                            $originalName = strtolower($value->getClientOriginalName());
                            
                            // 1. Block the word "php" anywhere in the name (prevents .php.jpg)
                            if (Str::contains($originalName, 'php')) {
                                $fail("Security error: Filename contains restricted keywords.");
                            }

                            // 2. Double-check the actual extension
                            $extension = $value->getClientOriginalExtension();
                            if (in_array(strtolower($extension), ['php', 'php5', 'phtml', 'phar'])) {
                                $fail("Direct PHP extension uploads are strictly prohibited.");
                            }
                        },
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table

            ->columns([
                TextColumn::make('date')
                    ->label('Date')
                    ->date(),
                TextColumn::make('title_of_accomplishment')
                    ->label('Title of Accomplishment')
                    ->searchable()
                    ->limit(100)
                    ->wrap()
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        // Only render the tooltip if the column contents exceeds the length limit.
                        return $state;
                    }),
                TextColumn::make('brief_description')
                    ->label('Brief Description')
                    ->limit(100)
                    ->wrap()
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        // Only render the tooltip if the column contents exceeds the length limit.
                        return $state;
                    }),
                TextColumn::make('scope')
                    ->label('Beneficiaries / Scope')
                    ->limit(100)
                    ->wrap()
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        // Only render the tooltip if the column contents exceeds the length limit.
                        return $state;
                    }),
                TextColumn::make('results')
                    ->label('Impact / Results (Quantifiable)')
                    ->limit(100)
                    ->wrap()
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        // Only render the tooltip if the column contents exceeds the length limit.
                        return $state;
                    }),
                ImageColumn::make('mov')
                    ->label('Mode of Verification (MOV)')
                    ->limit(100)
                    ->wrap(),
                TextColumn::make('ppa.paps_desc')
                    ->label('PPA')
                    ->wrap()
                    ->limit(100)
                    ->searchable()
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        // Only render the tooltip if the column contents exceeds the length limit.
                        return $state;
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Filter by PPA
                SelectFilter::make('ppa_id')
                    ->label('PPA')
                    ->options(function () {
                        $userDeptCode = auth()->user()->department_code;

                        return ProgramAndProject::whereNotNull('paps_desc')
                            ->where('department_code', $userDeptCode)
                            ->orderBy('paps_desc')
                            ->pluck('paps_desc', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload(),

                // Filter by Date Range
                Filter::make('date')
                    ->form([
                        DatePicker::make('from')->label('From'),
                        DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date) =>
                                    $query->whereDate('date', '>=', $date)
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date) =>
                                    $query->whereDate('date', '<=', $date)
                            );
                    }),
            ])

            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => $this->ownerRecord->status !== 'submitted'),
            ])
             ->recordActions([
                ViewAction::make() // Filament built-in view
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->visible(fn () => $this->ownerRecord->status === 'submitted'), // only visible if submitted
                EditAction::make()
                   ->visible(fn () => $this->ownerRecord->status !== 'submitted'),
                DeleteAction::make()
                    ->visible(fn () => $this->ownerRecord->status !== 'submitted'),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->visible(fn ($records) => collect($records)->contains(fn ($record) => $this->ownerRecord->status !== 'submitted')), // only show if there are draft records

            ]);
    }
}
