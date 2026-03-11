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
            // DeleteAction::make()
            //     ->visible(fn () => $this->record->status !== 'submitted'),
            DeleteAction::make()
                    ->visible(function ($record) {
                        $user = auth()->user();

                        $canViewAll     = $user->can('ViewAllDepartmentsAccomplishments:AccomplishmentHeader');
                        $canViewOwn     = $user->can('ViewWithinDepartmentsAccomplishments:AccomplishmentHeader');
                        $canDeleteOther = $user->can('DeleteOtherDepartmentAccomplishment:AccomplishmentHeader');
                        $canDeleteOwn   = $user->can('DeleteWithinDepartmentsAccomplishments:AccomplishmentHeader');
                        $isOwnRecord    = (int) $record->department_id === (int) $user->department_code; // ← fix
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
        ];
    }
}
