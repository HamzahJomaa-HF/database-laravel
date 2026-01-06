<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Activities table foreign keys
        Schema::table('activities', function (Blueprint $table) {
            $table->foreign('parent_activity')
                  ->references('activity_id')
                  ->on('activities')
                  ->onDelete('set null');
                  
            $table->foreign('target_cop')
                  ->references('cop_id')
                  ->on('cops')
                  ->onDelete('set null');
        });

        // Activity focal points foreign keys
        Schema::table('activity_focal_points', function (Blueprint $table) {
            $table->foreign('activity_id')
                  ->references('activity_id')
                  ->on('activities')
                  ->onDelete('cascade');
                  
            $table->foreign('rp_focalpoints_id')
                  ->references('rp_focalpoints_id')
                  ->on('rp_focalpoints')
                  ->onDelete('cascade');
        });

        // Activity users foreign keys
        Schema::table('activity_users', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('activity_id')
                  ->references('activity_id')
                  ->on('activities')
                  ->onDelete('cascade');
                  
            $table->foreign('cop_id')
                  ->references('cop_id')
                  ->on('cops')
                  ->onDelete('set null');
        });

        // Answers foreign keys
        Schema::table('answers', function (Blueprint $table) {
            $table->foreign('response_id')
                  ->references('response_id')
                  ->on('responses')
                  ->onDelete('cascade');
                  
            $table->foreign('question_id')
                  ->references('question_id')
                  ->on('questions')
                  ->onDelete('cascade');
                  
            $table->foreign('survey_question_id')
                  ->references('survey_question_id')
                  ->on('survey_questions')
                  ->onDelete('cascade');
        });

        // Cops foreign key
        Schema::table('cops', function (Blueprint $table) {
            $table->foreign('program_id')
                  ->references('program_id')
                  ->on('programs')
                  ->onDelete('cascade');
        });

        // Employees foreign keys
        Schema::table('employees', function (Blueprint $table) {
            $table->foreign('role_id')
                  ->references('role_id')
                  ->on('roles')
                  ->onDelete('set null');
                  
            $table->foreign('project_id')
                  ->references('project_id')
                  ->on('projects')
                  ->onDelete('set null');
        });

        // Portfolio activities foreign keys
        Schema::table('portfolio_activities', function (Blueprint $table) {
            $table->foreign('portfolio_id')
                  ->references('portfolio_id')
                  ->on('portfolios')
                  ->onDelete('cascade');
                  
            $table->foreign('activity_id')
                  ->references('activity_id')
                  ->on('activities')
                  ->onDelete('cascade');
        });

        // Programs foreign key
        Schema::table('programs', function (Blueprint $table) {
            $table->foreign('parent_program_id')
                  ->references('program_id')
                  ->on('programs')
                  ->onDelete('set null');
        });

        // Project activities foreign keys
        Schema::table('project_activities', function (Blueprint $table) {
            $table->foreign('project_id')
                  ->references('project_id')
                  ->on('projects')
                  ->onDelete('cascade');
                  
            $table->foreign('activity_id')
                  ->references('activity_id')
                  ->on('activities')
                  ->onDelete('cascade');
        });

        // Project employees foreign keys
        Schema::table('project_employees', function (Blueprint $table) {
            $table->foreign('program_id')
                  ->references('program_id')
                  ->on('programs')
                  ->onDelete('cascade');
                  
            $table->foreign('employee_id')
                  ->references('employee_id')
                  ->on('employees')
                  ->onDelete('cascade');
        });

        // Project portfolios foreign keys
        Schema::table('project_portfolios', function (Blueprint $table) {
            $table->foreign('project_id')
                  ->references('project_id')
                  ->on('projects')
                  ->onDelete('cascade');
                  
            $table->foreign('portfolio_id')
                  ->references('portfolio_id')
                  ->on('portfolios')
                  ->onDelete('cascade');
        });

        // Projects foreign keys
        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('program_id')
                  ->references('program_id')
                  ->on('programs')
                  ->onDelete('cascade');
                  
            $table->foreign('parent_project_id')
                  ->references('project_id')
                  ->on('projects')
                  ->onDelete('set null');
        });

        // Questions foreign key
        Schema::table('questions', function (Blueprint $table) {
            $table->foreign('survey_id')
                  ->references('survey_id')
                  ->on('surveys')
                  ->onDelete('set null');
        });

        // Responses foreign keys
        Schema::table('responses', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('survey_id')
                  ->references('survey_id')
                  ->on('surveys')
                  ->onDelete('cascade');
        });

       

        // Survey questions foreign keys
        Schema::table('survey_questions', function (Blueprint $table) {
            $table->foreign('survey_id')
                  ->references('survey_id')
                  ->on('surveys')
                  ->onDelete('cascade');
                  
            $table->foreign('question_id')
                  ->references('question_id')
                  ->on('questions')
                  ->onDelete('cascade');
        });

        // Surveys foreign key
        Schema::table('surveys', function (Blueprint $table) {
            $table->foreign('activity_id')
                  ->references('activity_id')
                  ->on('activities')
                  ->onDelete('cascade');
        });

        // Users foreign key
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('default_cop_id')
                  ->references('cop_id')
                  ->on('cops')
                  ->onDelete('set null');
        });

        // Users diploma foreign keys
        Schema::table('users_diploma', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('diploma_id')
                  ->references('diploma_id')
                  ->on('diploma')
                  ->onDelete('cascade');
        });

        // Users nationality foreign keys
        Schema::table('users_nationality', function (Blueprint $table) {
            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('nationality_id')
                  ->references('nationality_id')
                  ->on('nationality')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Drop foreign keys in reverse order
        $tables = [
            'users_nationality',
            'users_diploma',
            'users',
            'surveys',
            'survey_questions',
            'sessions',
            'responses',
            'questions',
            'projects',
            'project_portfolios',
            'project_employees',
            'project_activities',
            'programs',
            'portfolio_activities',
            'employees',
            'cops',
            'answers',
            'activity_users',
            'activity_focal_points',
            'activities'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $foreignKeys = Schema::getConnection()
                    ->getDoctrineSchemaManager()
                    ->listTableForeignKeys($table->getTable());
                    
                foreach ($foreignKeys as $foreignKey) {
                    $table->dropForeign($foreignKey->getName());
                }
            });
        }
    }
};