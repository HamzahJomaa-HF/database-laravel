<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\Employee;

class ActivityPolicy
{
    /**
     * Employees can view/update/delete a specific activity if they can see
     * all Activities records (super admin, or a role granted 'full' access
     * to the Activities module), or if they're a focal point on it.
     */
    public function view(Employee $employee, Activity $activity): bool
    {
        return $activity->isVisibleTo($employee);
    }

    public function update(Employee $employee, Activity $activity): bool
    {
        return $activity->isVisibleTo($employee);
    }

    public function delete(Employee $employee, Activity $activity): bool
    {
        return $activity->isVisibleTo($employee);
    }
}
