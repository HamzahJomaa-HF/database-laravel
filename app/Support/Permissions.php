<?php

namespace App\Support;

class Permissions
{
    /**
     * Whether the logged-in employee can perform a specific button-level action.
     * Holding the module's "manage" or "full" checkbox always grants every other
     * action in that module, matching the fallback already used by route middleware
     * (e.g. "hasPermission:Employees.export,Employees.manage,Employees.full").
     */
    public static function check(string $module, string $action): bool
    {
        $employee = auth()->guard('employee')->user();

        if (!$employee) {
            return false;
        }

        if ($employee->hasFullAccess()) {
            return true;
        }

        return $employee->hasPermission($module, $action)
            || $employee->hasPermission($module, 'manage')
            || $employee->hasPermission($module, 'full');
    }
}
