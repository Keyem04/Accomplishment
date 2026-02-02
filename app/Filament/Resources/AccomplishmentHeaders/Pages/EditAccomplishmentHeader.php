<?php

namespace App\Filament\Resources\AccomplishmentHeaders\Pages;

use App\Filament\Resources\AccomplishmentHeaders\AccomplishmentHeaderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAccomplishmentHeader extends EditRecord
{
    protected static string $resource = AccomplishmentHeaderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
