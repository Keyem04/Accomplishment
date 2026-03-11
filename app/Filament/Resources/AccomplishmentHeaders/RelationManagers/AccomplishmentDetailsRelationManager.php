<?php

namespace App\Filament\Resources\AccomplishmentHeaders\RelationManagers;

use App\Filament\Resources\AccomplishmentHeaders\AccomplishmentHeaderResource;
use App\Models\ProgramAndProject;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

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
                    ->options(function (Get $get) {
                        $userDeptCode = auth()->user()->department_code;
                        $currentPpaId = $get('ppa_id');

                        return ProgramAndProject::whereNotNull('paps_desc')
                            ->where(function ($query) use ($userDeptCode, $currentPpaId) {
                                $query->where('department_code', $userDeptCode);

                                // Always include the current record's PPA even if it's from another dept
                                if ($currentPpaId) {
                                    $query->orWhere('id', $currentPpaId);
                                }
                            })
                            ->orderBy('paps_desc')
                            ->pluck('paps_desc', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('title_of_accomplishment')
                    ->label('Title of Accomplishment')
                    ->required()
                    ->maxLength(255),
                    
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
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->multiple()
                    ->maxFiles(2)
                    ->maxSize(5120) // 5MB
                    ->directory(fn () => 'accomplishments/' . now()->format('Y/F'))
                    ->columnSpanFull()
                    ->downloadable()
                    ->openable()
                    ->previewable()
                    ->getUploadedFileNameForStorageUsing(
                        function (\Illuminate\Http\UploadedFile $file): string {
                            $extension = strtolower($file->getClientOriginalExtension());
                            $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                            // Sanitize original name
                            $safe = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);

                            // UUID hash prefix + sanitized original name
                            // e.g: 550e8400-e29b-41d4_bp_logo.jpg
                            return (string) \Illuminate\Support\Str::uuid() . '_' . $safe . '.' . $extension;
                        }
                    )
                    ->storeFileNamesIn('mov_original_names')
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
                    ->date()
                    ->sortable(),
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
                
                BadgeColumn::make('include_in_print')
                    ->label('Print')
                    ->formatStateUsing(fn (bool $state) => $state ? 'Included' : 'Excluded')
                    ->colors([
                        'success' => fn ($state) => $state,  // green for included
                        'danger'  => fn ($state) => ! $state, // red for excluded
                    ]),
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

                SelectFilter::make('created_by')
                    ->label('Created By')
                    ->options(function () {
                        $userDept = auth()->user()->department_code;

                        // Get all users who belong to this department AND have created accomplishments
                        return \App\Models\User::where('department_code', $userDept)
                            ->whereIn('recid', \App\Models\AccomplishmentDetail::pluck('created_by')->filter()->toArray())
                            ->orderBy('FullName')
                            ->pluck('FullName', 'recid')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload(),
            ])
            // ->filtersLayout(FiltersLayout::AboveContent)
            
            ->headerActions([
                // CreateAction::make()
                //     ->mutateFormDataUsing(function (array $data): array {
                //         $data['created_by'] = auth()->user()->recid;
                //         return $data;
                //     })
                //     ->visible(fn () => $this->ownerRecord->status !== 'submitted'),
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['created_by'] = auth()->user()->recid;
                        return $data;
                    })
                    ->visible(function () {
                        $user = auth()->user();
                        $record = $this->ownerRecord; // AccomplishmentHeader parent record

                        $canViewAll   = $user->can('ViewAllDepartmentsAccomplishments:AccomplishmentHeader');
                        $canViewOwn   = $user->can('ViewWithinDepartmentsAccomplishments:AccomplishmentHeader');
                        $canEditOther = $user->can('EditOtherDepartmentAccomplishment:AccomplishmentHeader');
                        $canEditOwn   = $user->can('EditWithinDepartmentsAccomplishments:AccomplishmentHeader');
                        $isOwnRecord  = (int) $record->department_id === (int) $user->department_code;
                        $isSubmitted  = $record->status === 'submitted';

                        // Rule 1: ViewAll + EditOther = can create on ANY record including submitted
                        if ($canViewAll && $canEditOther) {
                            return true;
                        }

                        // All rules below: cannot create on submitted records
                        if ($isSubmitted) {
                            return false;
                        }

                        // Rule 2: ViewAll + EditWithin = own department only (non-submitted)
                        if ($canViewAll && $canEditOwn) {
                            return $isOwnRecord;
                        }

                        // Rule 3: ViewWithin + EditWithin = own department only (non-submitted)
                        if ($canViewOwn && $canEditOwn) {
                            return $isOwnRecord;
                        }

                        return false;
                    }),
            ])
             ->recordActions([
                // Action::make('togglePrint')
                //     ->label(fn ($record) => $record->include_in_print ? 'Exclude' : 'Include')
                //     ->icon('heroicon-o-printer')
                //     ->action(function ($record) {
                //         $record->update([
                //             'include_in_print' => ! $record->include_in_print,
                //         ]);
                //     })
                //     ->color(fn ($record) => $record->include_in_print ? 'danger' : 'success'),
                ViewAction::make() // Filament built-in view
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->visible(fn () => $this->ownerRecord->status === 'submitted'), // only visible if submitted
                // EditAction::make()
                //    ->visible(fn () => $this->ownerRecord->status !== 'submitted'),
                // DeleteAction::make()
                //     ->visible(fn () => $this->ownerRecord->status !== 'submitted'),
                EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['created_by'] = auth()->user()->recid;
                        return $data;
                    })
                    ->visible(function () {
                        $user = auth()->user();
                        $record = $this->ownerRecord;

                        $canViewAll   = $user->can('ViewAllDepartmentsAccomplishments:AccomplishmentHeader');
                        $canViewOwn   = $user->can('ViewWithinDepartmentsAccomplishments:AccomplishmentHeader');
                        $canEditOther = $user->can('EditOtherDepartmentAccomplishment:AccomplishmentHeader');
                        $canEditOwn   = $user->can('EditWithinDepartmentsAccomplishments:AccomplishmentHeader');
                        $isOwnRecord  = (int) $record->department_id === (int) $user->department_code;
                        $isSubmitted  = $record->status === 'submitted';

                        // Rule 1: ViewAll + EditOther = can edit ANY record including submitted
                        if ($canViewAll && $canEditOther) {
                            return true;
                        }

                        // All rules below: cannot edit submitted records
                        if ($isSubmitted) {
                            return false;
                        }

                        // Rule 2: ViewAll + EditWithin = own department only (non-submitted)
                        if ($canViewAll && $canEditOwn) {
                            return $isOwnRecord;
                        }

                        // Rule 3: ViewWithin + EditWithin = own department only (non-submitted)
                        if ($canViewOwn && $canEditOwn) {
                            return $isOwnRecord;
                        }

                        return false;
                    }),

                DeleteAction::make()
                    ->visible(function () {
                        $user = auth()->user();
                        $record = $this->ownerRecord;

                        $canViewAll     = $user->can('ViewAllDepartmentsAccomplishments:AccomplishmentHeader');
                        $canViewOwn     = $user->can('ViewWithinDepartmentsAccomplishments:AccomplishmentHeader');
                        $canDeleteOther = $user->can('DeleteOtherDepartmentAccomplishment:AccomplishmentHeader');
                        $canDeleteOwn   = $user->can('DeleteWithinDepartmentsAccomplishments:AccomplishmentHeader');
                        $isOwnRecord    = (int) $record->department_id === (int) $user->department_code;
                        $isSubmitted    = $record->status === 'submitted';

                        // Rule 1: ViewAll + DeleteOther = can delete ANY record including submitted
                        if ($canViewAll && $canDeleteOther) {
                            return true;
                        }

                        // All rules below: cannot delete submitted records
                        if ($isSubmitted) {
                            return false;
                        }

                        // Rule 2: ViewAll + DeleteWithin = own department only (non-submitted)
                        if ($canViewAll && $canDeleteOwn) {
                            return $isOwnRecord;
                        }

                        // Rule 3: ViewWithin + DeleteWithin = own department only (non-submitted)
                        if ($canViewOwn && $canDeleteOwn) {
                            return $isOwnRecord;
                        }

                        return false;
                    }),
            ])
            ->toolbarActions([
                BulkAction::make('includeSelected')
                    ->label('Include in Print')
                    ->icon('heroicon-o-printer')
                    ->action(fn ($records) =>
                        $records->each->update(['include_in_print' => true])
                    )
                    ->color('success'),
                BulkAction::make('excludeSelected')
                    ->label('Exclude from Print')
                    ->icon('heroicon-o-printer')
                    ->action(fn ($records) =>
                        $records->each->update(['include_in_print' => false])
                    ),
                // DeleteBulkAction::make()
                //     ->disabled(fn ($records) => collect($records)->contains(fn ($record) => $this->ownerRecord->status === 'submitted')), // only show if there are draft records
                
                DeleteBulkAction::make()
                    ->visible(function () {
                        $user = auth()->user();
                        $record = $this->ownerRecord;

                        $canViewAll     = $user->can('ViewAllDepartmentsAccomplishments:AccomplishmentHeader');
                        $canViewOwn     = $user->can('ViewWithinDepartmentsAccomplishments:AccomplishmentHeader');
                        $canDeleteOther = $user->can('DeleteOtherDepartmentAccomplishment:AccomplishmentHeader');
                        $canDeleteOwn   = $user->can('DeleteWithinDepartmentsAccomplishments:AccomplishmentHeader');
                        $isOwnRecord    = (int) $record->department_id === (int) $user->department_code;
                        $isSubmitted    = $record->status === 'submitted';

                        // Rule 1: ViewAll + DeleteOther = can delete ANY record including submitted
                        if ($canViewAll && $canDeleteOther) {
                            return true;
                        }

                        // All rules below: cannot delete submitted records
                        if ($isSubmitted) {
                            return false;
                        }

                        // Rule 2: ViewAll + DeleteWithin = own department only (non-submitted)
                        if ($canViewAll && $canDeleteOwn) {
                            return $isOwnRecord;
                        }

                        // Rule 3: ViewWithin + DeleteWithin = own department only (non-submitted)
                        if ($canViewOwn && $canDeleteOwn) {
                            return $isOwnRecord;
                        }

                        return false;
                    })
                    ->disabled(fn ($records) => collect($records)->contains(
                        fn ($record) => $this->ownerRecord->status === 'submitted'
                    )),
            ]);
    }
}
