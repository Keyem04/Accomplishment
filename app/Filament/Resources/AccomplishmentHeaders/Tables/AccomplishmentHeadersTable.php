<?php

namespace App\Filament\Resources\AccomplishmentHeaders\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use App\Models\AccomplishmentHeader;
use Filament\Actions\BulkActionGroup;
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
           
            ->columns([
                // TextColumn::make('id')->label('ID')->sortable(),
                
                // TextColumn::make('department.office')
                //     ->label('Department')
                //     ->sortable()
                //     ->searchable(),
            
                TextColumn::make('reporting_month')
                    ->label('Month')
                    ->formatStateUsing(fn($state) => date('F', mktime(0,0,0,$state,1)))
                    ->sortable(),

                TextColumn::make('reporting_year')
                    ->label('Year')
                    ->sortable(),
                

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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
