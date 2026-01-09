<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add deleted_at to tables that don't have it
        $tables = [
            'action_plans',
            'activities',
            'activity_focal_points',
            'activity_users',
            'answers',
            'cops',
            'diploma',
            'employees',
            'nationality',
            'portfolio_activities',
            'portfolios',
            'programs',
            'project_activities',
            'project_employees',
            'project_portfolios',
            'projects',
            'questions',
            'responses',
            'roles',
            'rp_activity_focalpoints',
            'rp_activity_indicators',
            'rp_activity_mappings',
            'survey_questions',
            'surveys',
            'users',
            'users_diploma',
            'users_nationality',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $tableObj) {
                    $tableObj->timestamp('deleted_at')->nullable();
                });
            }
        }
    }

    public function down()
    {
        // Remove deleted_at from tables
        $tables = [
            'action_plans',
            'activities',
            'activity_focal_points',
            'activity_users',
            'answers',
            'cops',
            'diploma',
            'employees',
            'nationality',
            'portfolio_activities',
            'portfolios',
            'programs',
            'project_activities',
            'project_employees',
            'project_portfolios',
            'projects',
            'questions',
            'responses',
            'roles',
            'rp_activity_focalpoints',
            'rp_activity_indicators',
            'rp_activity_mappings',
            'survey_questions',
            'surveys',
            'users',
            'users_diploma',
            'users_nationality',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $tableObj) {
                    $tableObj->dropColumn('deleted_at');
                });
            }
        }
    }
};