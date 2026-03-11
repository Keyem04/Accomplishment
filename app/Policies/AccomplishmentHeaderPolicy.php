<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AccomplishmentHeader;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccomplishmentHeaderPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AccomplishmentHeader');
    }

    public function view(AuthUser $authUser, AccomplishmentHeader $accomplishmentHeader): bool
    {
        return $authUser->can('View:AccomplishmentHeader');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AccomplishmentHeader');
    }

    public function update(AuthUser $authUser, AccomplishmentHeader $accomplishmentHeader): bool
    {
        return $authUser->can('Update:AccomplishmentHeader');
    }

    public function delete(AuthUser $authUser, AccomplishmentHeader $accomplishmentHeader): bool
    {
        return $authUser->can('Delete:AccomplishmentHeader');
    }

    public function restore(AuthUser $authUser, AccomplishmentHeader $accomplishmentHeader): bool
    {
        return $authUser->can('Restore:AccomplishmentHeader');
    }

    public function forceDelete(AuthUser $authUser, AccomplishmentHeader $accomplishmentHeader): bool
    {
        return $authUser->can('ForceDelete:AccomplishmentHeader');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AccomplishmentHeader');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AccomplishmentHeader');
    }

    public function replicate(AuthUser $authUser, AccomplishmentHeader $accomplishmentHeader): bool
    {
        return $authUser->can('Replicate:AccomplishmentHeader');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AccomplishmentHeader');
    }

    public function viewAllDepartmentsAccomplishments(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAllDepartmentsAccomplishments:AccomplishmentHeader');
    }

    public function viewWithinDepartmentsAccomplishments(AuthUser $authUser): bool
    {
        return $authUser->can('ViewWithinDepartmentsAccomplishments:AccomplishmentHeader');
    }

    public function deleteOtherDepartmentAccomplishment(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteOtherDepartmentAccomplishment:AccomplishmentHeader');
    }

    public function deleteWithinDepartmentsAccomplishments(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteWithinDepartmentsAccomplishments:AccomplishmentHeader');
    }

    public function editOtherDepartmentAccomplishment(AuthUser $authUser): bool
    {
        return $authUser->can('EditOtherDepartmentAccomplishment:AccomplishmentHeader');
    }

    public function editWithinDepartmentsAccomplishments(AuthUser $authUser): bool
    {
        return $authUser->can('EditWithinDepartmentsAccomplishments:AccomplishmentHeader');
    }

}