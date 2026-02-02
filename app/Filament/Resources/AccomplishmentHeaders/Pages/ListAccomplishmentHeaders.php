<?php

namespace App\Filament\Resources\AccomplishmentHeaders\Pages;

use App\Filament\Resources\AccomplishmentHeaders\AccomplishmentHeaderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAccomplishmentHeaders extends ListRecords
{
    protected static string $resource = AccomplishmentHeaderResource::class;
    protected static ?string $title = 'Accomplishments';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
