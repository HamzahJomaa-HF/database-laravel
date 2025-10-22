<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. COP → Program (COP belongs to a Program)
        Schema::table('cops', function (Blueprint $table) {
            if (!Schema::hasColumn('cops', 'program_id')) {
                $table->unsignedBigInteger('program_id');
            }
            $table->foreign('program_id', 'FK_COPS_PROGRAM_ID')
                  ->references('program_id')->on('programs')
                  ->onDelete('cascade');
        });

        // 2. ProjectCenter → Program (ProjectCenter belongs to a Program)
        Schema::table('project_centers', function (Blueprint $table) {
            if (!Schema::hasColumn('project_centers', 'program_id')) {
                $table->unsignedBigInteger('program_id');
            }
            $table->foreign('program_id', 'FK_PROJECT_CENTERS_PROGRAM_ID')
                  ->references('program_id')->on('programs')
                  ->onDelete('cascade');
        });

        // 3. ProjectActivity → ProjectCenter, Activities (Junction table)
        Schema::table('project_activities', function (Blueprint $table) {
            if (!Schema::hasColumn('project_activities', 'project_center_id')) {
                $table->unsignedBigInteger('project_center_id');
            }
            if (!Schema::hasColumn('project_activities', 'activity_id')) {
                $table->unsignedBigInteger('activity_id');
            }
            $table->foreign('project_center_id', 'FK_PROJECT_ACTIVITIES_PROJECT_CENTER_ID')
                  ->references('project_center_id')->on('project_centers')
                  ->onDelete('cascade');
            $table->foreign('activity_id', 'FK_PROJECT_ACTIVITIES_ACTIVITY_ID')
                  ->references('activity_id')->on('activities')
                  ->onDelete('cascade');
        });

        // 4. Activities → Parent Activity (self), Target COP
        Schema::table('activities', function (Blueprint $table) {
            if (!Schema::hasColumn('activities', 'parent_activity')) {
                $table->unsignedBigInteger('parent_activity')->nullable();
            }
            if (!Schema::hasColumn('activities', 'target_cop')) {
                $table->unsignedBigInteger('target_cop')->nullable();
            }
            $table->foreign('parent_activity', 'FK_ACTIVITIES_PARENT_ACTIVITY')
                  ->references('activity_id')->on('activities')
                  ->onDelete('set null');
            $table->foreign('target_cop', 'FK_ACTIVITIES_TARGET_COP')
                  ->references('cop_id')->on('cops')
                  ->onDelete('set null');
        });

        // 5. activity_users → user, activity, cop (Junction table)
        Schema::table('activity_users', function (Blueprint $table) {
            if (!Schema::hasColumn('activity_users', 'user_id')) {
                $table->unsignedBigInteger('user_id');
            }
            if (!Schema::hasColumn('activity_users', 'activity_id')) {
                $table->unsignedBigInteger('activity_id');
            }
            if (!Schema::hasColumn('activity_users', 'cop_id')) {
                $table->unsignedBigInteger('cop_id')->nullable();
            }
            
            $table->foreign('user_id', 'FK_ACTIVITY_USERS_USER_ID')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
            $table->foreign('activity_id', 'FK_ACTIVITY_USERS_ACTIVITY_ID')
                  ->references('activity_id')->on('activities')
                  ->onDelete('cascade');
            $table->foreign('cop_id', 'FK_ACTIVITY_USERS_COP_ID')
                  ->references('cop_id')->on('cops')
                  ->onDelete('set null');
        });

        // 6. users → role (FIXED - Only add foreign key, don't create column)
        Schema::table('users', function (Blueprint $table) {
            // Check if user_role exists and rename it to role_id
            if (Schema::hasColumn('users', 'user_role') && !Schema::hasColumn('users', 'role_id')) {
                $table->renameColumn('user_role', 'role_id');
            }
            
            // Only add foreign key constraint if role_id column exists
            if (Schema::hasColumn('users', 'role_id')) {
                $table->foreign('role_id', 'FK_USERS_ROLE_ID')
                      ->references('role_id')->on('roles')
                      ->onDelete('set null');
            }
        });

        // 7. Survey → Activity
        Schema::table('surveys', function (Blueprint $table) {
            if (!Schema::hasColumn('surveys', 'activity_id')) {
                $table->unsignedBigInteger('activity_id');
            }
            $table->foreign('activity_id', 'FK_SURVEYS_ACTIVITY_ID')
                  ->references('activity_id')->on('activities')
                  ->onDelete('cascade');
        });

        // 8. SurveyQuestions → Survey, Question (Junction table)
        Schema::table('survey_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('survey_questions', 'survey_id')) {
                $table->unsignedBigInteger('survey_id');
            }
            if (!Schema::hasColumn('survey_questions', 'question_id')) {
                $table->unsignedBigInteger('question_id');
            }
            $table->foreign('survey_id', 'FK_SURVEY_QUESTIONS_SURVEY_ID')
                  ->references('survey_id')->on('surveys')
                  ->onDelete('cascade');
            $table->foreign('question_id', 'FK_SURVEY_QUESTIONS_QUESTION_ID')
                  ->references('question_id')->on('questions')
                  ->onDelete('cascade');
        });

        // 9. Questions → Survey
        Schema::table('questions', function (Blueprint $table) {
            if (!Schema::hasColumn('questions', 'survey_id')) {
                $table->unsignedBigInteger('survey_id')->nullable();
            }
            $table->foreign('survey_id', 'FK_QUESTIONS_SURVEY_ID')
                  ->references('survey_id')->on('surveys')
                  ->onDelete('set null');
        });

        // 10. Responses → user, survey
        Schema::table('responses', function (Blueprint $table) {
            if (!Schema::hasColumn('responses', 'user_id')) {
                $table->unsignedBigInteger('user_id');
            }
            if (!Schema::hasColumn('responses', 'survey_id')) {
                $table->unsignedBigInteger('survey_id');
            }
            $table->foreign('user_id', 'FK_RESPONSES_USER_ID')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
            $table->foreign('survey_id', 'FK_RESPONSES_SURVEY_ID')
                  ->references('survey_id')->on('surveys')
                  ->onDelete('cascade');
        });

        // 11. Answers → response, question, survey_question
        Schema::table('answers', function (Blueprint $table) {
            if (!Schema::hasColumn('answers', 'response_id')) {
                $table->unsignedBigInteger('response_id');
            }
            if (!Schema::hasColumn('answers', 'question_id')) {
                $table->unsignedBigInteger('question_id');
            }
            if (!Schema::hasColumn('answers', 'survey_question_id')) {
                $table->unsignedBigInteger('survey_question_id');
            }
            $table->foreign('response_id', 'FK_ANSWERS_RESPONSE_ID')
                  ->references('response_id')->on('responses')
                  ->onDelete('cascade');
            $table->foreign('question_id', 'FK_ANSWERS_QUESTION_ID')
                  ->references('question_id')->on('questions')
                  ->onDelete('cascade');
            $table->foreign('survey_question_id', 'FK_ANSWERS_SURVEY_QUESTION_ID')
                  ->references('survey_question_id')->on('survey_questions')
                  ->onDelete('cascade');
        });

        // 12. ProjectEmployee → Program, Employee (Junction table)
        Schema::table('project_employees', function (Blueprint $table) {
            if (!Schema::hasColumn('project_employees', 'program_id')) {
                $table->unsignedBigInteger('program_id');
            }
            if (!Schema::hasColumn('project_employees', 'employee_id')) {
                $table->unsignedBigInteger('employee_id');
            }
            $table->foreign('program_id', 'FK_PROJECT_EMPLOYEES_PROGRAM_ID')
                  ->references('program_id')->on('programs')
                  ->onDelete('cascade');
            $table->foreign('employee_id', 'FK_PROJECT_EMPLOYEES_EMPLOYEE_ID')
                  ->references('employee_id')->on('employees')
                  ->onDelete('cascade');
        });

        // 13. Employee → Role, ProjectCenter
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'role_id')) {
                $table->unsignedBigInteger('role_id')->nullable();
            }
            if (!Schema::hasColumn('employees', 'project_id')) {
                $table->unsignedBigInteger('project_id')->nullable();
            }
            $table->foreign('role_id', 'FK_EMPLOYEES_ROLE_ID')
                  ->references('role_id')->on('roles')
                  ->onDelete('set null');
            $table->foreign('project_id', 'FK_EMPLOYEES_PROJECT_ID')
                  ->references('project_center_id')->on('project_centers')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        // Drop foreign keys in reverse order
        Schema::table('employees', function (Blueprint $t) {
            $t->dropForeign('FK_EMPLOYEES_ROLE_ID');
            $t->dropForeign('FK_EMPLOYEES_PROJECT_ID');
        });
        Schema::table('project_employees', function (Blueprint $t) {
            $t->dropForeign('FK_PROJECT_EMPLOYEES_PROGRAM_ID');
            $t->dropForeign('FK_PROJECT_EMPLOYEES_EMPLOYEE_ID');
        });
        Schema::table('answers', function (Blueprint $t) {
            $t->dropForeign('FK_ANSWERS_RESPONSE_ID');
            $t->dropForeign('FK_ANSWERS_QUESTION_ID');
            $t->dropForeign('FK_ANSWERS_SURVEY_QUESTION_ID');
        });
        Schema::table('responses', function (Blueprint $t) {
            $t->dropForeign('FK_RESPONSES_USER_ID');
            $t->dropForeign('FK_RESPONSES_SURVEY_ID');
        });
        Schema::table('questions', fn(Blueprint $t) => $t->dropForeign('FK_QUESTIONS_SURVEY_ID'));
        Schema::table('survey_questions', function (Blueprint $t) {
            $t->dropForeign('FK_SURVEY_QUESTIONS_SURVEY_ID');
            $t->dropForeign('FK_SURVEY_QUESTIONS_QUESTION_ID');
        });
        Schema::table('surveys', fn(Blueprint $t) => $t->dropForeign('FK_SURVEYS_ACTIVITY_ID'));
        Schema::table('users', fn(Blueprint $t) => $t->dropForeign('FK_USERS_ROLE_ID'));
        Schema::table('activity_users', function (Blueprint $t) {
            $t->dropForeign('FK_ACTIVITY_USERS_USER_ID');
            $t->dropForeign('FK_ACTIVITY_USERS_ACTIVITY_ID');
            $t->dropForeign('FK_ACTIVITY_USERS_COP_ID');
        });
        Schema::table('activities', function (Blueprint $t) {
            $t->dropForeign('FK_ACTIVITIES_PARENT_ACTIVITY');
            $t->dropForeign('FK_ACTIVITIES_TARGET_COP');
        });
        Schema::table('project_activities', function (Blueprint $t) {
            $t->dropForeign('FK_PROJECT_ACTIVITIES_PROJECT_CENTER_ID');
            $t->dropForeign('FK_PROJECT_ACTIVITIES_ACTIVITY_ID');
        });
        Schema::table('project_centers', fn(Blueprint $t) => $t->dropForeign('FK_PROJECT_CENTERS_PROGRAM_ID'));
        Schema::table('cops', fn(Blueprint $t) => $t->dropForeign('FK_COPS_PROGRAM_ID'));
    }
};