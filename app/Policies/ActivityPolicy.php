<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\Employee;

class ActivityPolicy
{
    /**
     * Viewing a specific activity is allowed if the employee can see all
     * Activities records (super admin, a role granted 'full' access to
     * Activities, or the read-only 'view_all' override), or if they're a
     * focal point on it.
     */
    public function view(Employee $employee, Activity $activity): bool
    {
        return $activity->isVisibleTo($employee);
    }

    /**
     * Updating/deleting a specific activity is stricter: the read-only
     * 'view_all' override does not apply here, only 'full'/super admin or
     * being a focal point on this activity does.
     */
    public function update(Employee $employee, Activity $activity): bool
    {
        return $activity->isEditableBy($employee);
    }

    public function delete(Employee $employee, Activity $activity): bool
    {
        return $activity->isEditableBy($employee);
    }
}
