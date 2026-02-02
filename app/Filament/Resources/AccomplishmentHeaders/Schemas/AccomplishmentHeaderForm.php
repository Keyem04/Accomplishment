<?php

namespace App\Filament\Resources\AccomplishmentHeaders\Schemas;

use App\Models\Office;
use Filament\Schemas\Schema;
use App\Models\ProgramAndProject;
use Filament\Forms\Components\Select;
use Filament\Support\Enums\Operation;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;

class AccomplishmentHeaderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    ->options(function () {
                        // Get only the department of the logged-in user
                        $userDeptCode = auth()->user()->department_code;

                        return Office::where('department_code', $userDeptCode)
                                    ->pluck('office', 'id')
                                    ->toArray();
                    })
                    ->default(fn () => auth()->user()->department_code) // sets default selection
                    ->searchable()
                    ->preload()
                    ->required()
                    ->disabled()
                    ->dehydrated()
                    ->columnSpanFull(),

                Select::make('reporting_month')
                    ->label('Reporting Month')
                    ->options(fn () => collect(range(1,12))
                        ->mapWithKeys(fn($m) => [$m => date('F', mktime(0,0,0,$m,1))])
                        ->toArray())
                    ->required(),

                Select::make('reporting_year')
                    ->label('Reporting Year')
                    ->options(function () {
                        $options = [];
                        for ($year = now()->year - 5; $year <= now()->year + 1; $year++) {
                            $options[$year] = $year;
                        }
                        return $options;
                    })
                    ->required(),
            ]);
    }
}
