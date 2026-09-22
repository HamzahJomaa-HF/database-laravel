<?php

namespace App\Policies;

use App\Models\ActivityUser;
use App\Models\Employee;

class ActivityUserPolicy
{
    /**
     * Visibility of an activity-user row follows its parent activity's
     * focal-point rules (see ActivityPolicy).
     */
    public function view(Employee $employee, ActivityUser $activityUser): bool
    {
        return $this->canAccess($employee, $activityUser);
    }

    public function update(Employee $employee, ActivityUser $activityUser): bool
    {
        return $this->canAccess($employee, $activityUser);
    }

    public function delete(Employee $employee, ActivityUser $activityUser): bool
    {
        return $this->canAccess($employee, $activityUser);
    }

    protected function canAccess(Employee $employee, ActivityUser $activityUser): bool
    {
        if (!$activityUser->activity) {
            return $employee->canSeeAllFor('Activities');
        }

        return $activityUser->activity->isVisibleTo($employee);
    }
}
