<?php

namespace App\Filament\Resources\AccomplishmentHeaders\Pages;

use App\Filament\Resources\AccomplishmentHeaders\AccomplishmentHeaderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAccomplishmentHeader extends EditRecord
{
    protected static string $resource = AccomplishmentHeaderResource::class;

   public function getHeading(): string
    {
        $monthName = date('F', mktime(0, 0, 0, $this->record->reporting_month, 1));
        
        $action = $this->record->status === 'submitted' ? 'View' : 'Edit';

        return "{$action} {$monthName} {$this->record->reporting_year} Accomplishments";
    }



    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn () => $this->record->status !== 'submitted'),
        ];
    }
}
