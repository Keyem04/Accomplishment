<?php

namespace App\Filament\Resources\AccomplishmentHeaders\Tables;

use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use App\Models\AccomplishmentHeader;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class AccomplishmentHeadersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = Auth::user();
                
                // Filter: user's department_code must equal header's department_id
                $query->where('department_id', $user->department_code);
            })
            ->columns([
                // TextColumn::make('id')->label('ID')->sortable(),
                
                // TextColumn::make('department.office')
                //     ->label('Department')
                //     ->sortable()
                //     ->searchable(),
            
                TextColumn::make('reporting_month')
                    ->label('Month')
                    ->formatStateUsing(fn($state) => date('F', mktime(0,0,0,$state,1)))
                    ->sortable()
                    ->searchable(query: function ($query, $search) {
                        $months = [
                            'january' => 1,
                            'february' => 2,
                            'march' => 3,
                            'april' => 4,
                            'may' => 5,
                            'june' => 6,
                            'july' => 7,
                            'august' => 8,
                            'september' => 9,
                            'october' => 10,
                            'november' => 11,
                            'december' => 12,
                        ];

                        $searchLower = strtolower($search);

                        if (isset($months[$searchLower])) {
                            $query->where('reporting_month', $months[$searchLower]);
                        } else {
                            $query->where('reporting_month', 'like', "%{$search}%");
                        }
                    }),

                TextColumn::make('reporting_year')
                    ->label('Year')
                    ->sortable()
                    ->searchable(),
                

                // TextColumn::make('month_year')
                //     ->label('Reporting Period Month & Year')
                //     ->getStateUsing(fn($record) => 
                //         date('F Y', mktime(0, 0, 0, $record->reporting_month, 1, $record->reporting_year))
                //     )
                //     ->sortable([
                //         'reporting_year' => 'asc',
                //         'reporting_month' => 'asc',
                //     ]),

            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // // Filter by Department (relationship)
                // SelectFilter::make('department_id')
                //     ->label('Department')
                //     ->relationship('department', 'office')
                //     ->searchable()
                //     ->preload(),

                // Filter by Reporting Month
                SelectFilter::make('reporting_month')
                    ->label('Month')
                    ->options([
                        1 => 'January',
                        2 => 'February',
                        3 => 'March',
                        4 => 'April',
                        5 => 'May',
                        6 => 'June',
                        7 => 'July',
                        8 => 'August',
                        9 => 'September',
                        10 => 'October',
                        11 => 'November',
                        12 => 'December',
                    ]),

                // Filter by Reporting Year
                SelectFilter::make('reporting_year')
                    ->label('Year')
                    ->options(
                        fn () => AccomplishmentHeader::query()
                            ->select('reporting_year')
                            ->distinct()
                            ->orderBy('reporting_year', 'desc')
                            ->pluck('reporting_year', 'reporting_year')
                            ->toArray()
                    ),
            ])
            
            ->recordActions([
                Action::make('submit')
                    ->label(fn ($record) => $record->status === 'submitted' ? 'Submitted' : 'Submit')
                    ->modalHeading('Confirm Submission') // Title of the confirmation modal
                    ->modalDescription('Once submitted, this record cannot be edited. Are you sure you want to submit?') // Custom message
                    ->icon('heroicon-o-lock-closed')
                    ->color(fn ($record) => $record->status === 'submitted' ? 'gray' : 'success')
                    ->disabled(fn ($record) => $record->status === 'submitted')
                    ->requiresConfirmation(fn ($record) => $record->status !== 'submitted')
                    ->action(function ($record) {
                        if ($record->status !== 'submitted') {
                            $record->update([
                                'status' => 'submitted',
                            ]);
                        }
                    })
                    ->after(function ($record, $livewire) {
                        // Refresh the table automatically after action
                        $livewire->refresh();
                    }),

                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    // ->url(fn ($record) => route('api.accomplishments.print', [
                    //     'department_id' => $record->department_id,
                    //     'year' => $record->reporting_year,
                    //     'month' => $record->reporting_month,
                    // ]))
                    ->modalContent(fn($record) => view('filament.print', [
                        'department_id' => $record->department_id,
                        'year' => $record->reporting_year,
                        'month' => $record->reporting_month,
                    ]))
                    ->slideOver()
                    ->disabled(fn ($record) => $record->accomplishmentdetails()->count() === 0),
                EditAction::make() 
                    ->visible(fn ($record) => $record->status !== 'submitted'),
                DeleteAction::make()
                    ->visible(fn ($record) => $record->status !== 'submitted'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function ($records) {
                            // Only delete draft/not-submitted records
                            foreach ($records as $record) {
                                if ($record->status !== 'submitted') {
                                    $record->delete();
                                }
                            }
                        }),
                ]),
            ])
            ->selectable(function ($record) {
                return $record?->status !== 'submitted'; // only allow selection if status is NOT submitted
            })
            ;


    }
}
