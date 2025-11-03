<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 0. Programs → Parent Program (self-referencing for hierarchy)
        Schema::table('programs', function (Blueprint $table) {
            if (!Schema::hasColumn('programs', 'parent_program_id')) {
                $table->uuid('parent_program_id')->nullable()->after('program_id');

                $table->foreign('parent_program_id', 'FK_PROGRAMS_PARENT_PROGRAM')
                      ->references('program_id')
                      ->on('programs')
                      ->onDelete('cascade');
            }
        });

        // 1. COP → Program
        Schema::table('cops', function (Blueprint $table) {
            if (!Schema::hasColumn('cops', 'program_id')) {
                $table->uuid('program_id');
            }
            $table->foreign('program_id', 'FK_COPS_PROGRAM_ID')
                  ->references('program_id')->on('programs')
                  ->onDelete('cascade');
        });

        // 2. Project → Program
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'program_id')) {
                $table->uuid('program_id');
            }
            $table->foreign('program_id', 'FK_project_PROGRAM_ID')
                  ->references('program_id')->on('programs')
                  ->onDelete('cascade');

            if (!Schema::hasColumn('projects', 'parent_project_id')) {
                $table->uuid('parent_project_id')->nullable()->after('project_id');

                $table->foreign('parent_project_id', 'FK_project_PARENT')
                      ->references('project_id')
                      ->on('projects')
                      ->onDelete('set null');
            }
        });

        // 3. ProjectActivity → Project, Activities
        Schema::table('project_activities', function (Blueprint $table) {
            if (!Schema::hasColumn('project_activities', 'project_id')) {
                $table->uuid('project_id');
            }
            if (!Schema::hasColumn('project_activities', 'activity_id')) {
                $table->uuid('activity_id');
            }
            $table->foreign('project_id', 'FK_PROJECT_ACTIVITIES_project_id')
                  ->references('project_id')->on('projects')
                  ->onDelete('cascade');
            $table->foreign('activity_id', 'FK_PROJECT_ACTIVITIES_ACTIVITY_ID')
                  ->references('activity_id')->on('activities')
                  ->onDelete('cascade');
        });

        // 4. Activities → Parent Activity, Target COP
        Schema::table('activities', function (Blueprint $table) {
            if (!Schema::hasColumn('activities', 'parent_activity')) {
                $table->uuid('parent_activity')->nullable();
            }
            if (!Schema::hasColumn('activities', 'target_cop')) {
                $table->uuid('target_cop')->nullable();
            }
            $table->foreign('parent_activity', 'FK_ACTIVITIES_PARENT_ACTIVITY')
                  ->references('activity_id')->on('activities')
                  ->onDelete('set null');
            $table->foreign('target_cop', 'FK_ACTIVITIES_TARGET_COP')
                  ->references('cop_id')->on('cops')
                  ->onDelete('set null');
        });

        // 5. activity_users → user, activity, cop
        Schema::table('activity_users', function (Blueprint $table) {
            if (!Schema::hasColumn('activity_users', 'user_id')) {
                $table->uuid('user_id');
            }
            if (!Schema::hasColumn('activity_users', 'activity_id')) {
                $table->uuid('activity_id');
            }
            if (!Schema::hasColumn('activity_users', 'cop_id')) {
                $table->uuid('cop_id')->nullable();
            }

            $table->foreign('user_id', 'FK_ACTIVITY_USERS_USER_ID')
                  ->references('user_id')->on('users')
                  ->onDelete('cascade');
            $table->foreign('activity_id', 'FK_ACTIVITY_USERS_ACTIVITY_ID')
                  ->references('activity_id')->on('activities')
                  ->onDelete('cascade');
            $table->foreign('cop_id', 'FK_ACTIVITY_USERS_COP_ID')
                  ->references('cop_id')->on('cops')
                  ->onDelete('set null');
        });

        // 6. Survey → Activity
        Schema::table('surveys', function (Blueprint $table) {
            if (!Schema::hasColumn('surveys', 'activity_id')) {
                $table->uuid('activity_id');
            }
            $table->foreign('activity_id', 'FK_SURVEYS_ACTIVITY_ID')
                  ->references('activity_id')->on('activities')
                  ->onDelete('cascade');
        });

        // 7. SurveyQuestions → Survey, Question
        Schema::table('survey_questions', function (Blueprint $table) {
            if (!Schema::hasColumn('survey_questions', 'survey_id')) {
                $table->uuid('survey_id');
            }
            if (!Schema::hasColumn('survey_questions', 'question_id')) {
                $table->uuid('question_id');
            }
            $table->foreign('survey_id', 'FK_SURVEY_QUESTIONS_SURVEY_ID')
                  ->references('survey_id')->on('surveys')
                  ->onDelete('cascade');
            $table->foreign('question_id', 'FK_SURVEY_QUESTIONS_QUESTION_ID')
                  ->references('question_id')->on('questions')
                  ->onDelete('cascade');
        });

        // 8. Questions → Survey
        Schema::table('questions', function (Blueprint $table) {
            if (!Schema::hasColumn('questions', 'survey_id')) {
                $table->uuid('survey_id')->nullable();
            }
            $table->foreign('survey_id', 'FK_QUESTIONS_SURVEY_ID')
                  ->references('survey_id')->on('surveys')
                  ->onDelete('set null');
        });

        // 9. Responses → user, survey
        Schema::table('responses', function (Blueprint $table) {
            if (!Schema::hasColumn('responses', 'user_id')) {
                $table->uuid('user_id');
            }
            if (!Schema::hasColumn('responses', 'survey_id')) {
                $table->uuid('survey_id');
            }
            $table->foreign('user_id', 'FK_RESPONSES_USER_ID')
                  ->references('user_id')->on('users')
                  ->onDelete('cascade');
            $table->foreign('survey_id', 'FK_RESPONSES_SURVEY_ID')
                  ->references('survey_id')->on('surveys')
                  ->onDelete('cascade');
        });

        // 10. Answers → response, question, survey_question
        Schema::table('answers', function (Blueprint $table) {
            if (!Schema::hasColumn('answers', 'response_id')) {
                $table->uuid('response_id');
            }
            if (!Schema::hasColumn('answers', 'question_id')) {
                $table->uuid('question_id');
            }
            if (!Schema::hasColumn('answers', 'survey_question_id')) {
                $table->uuid('survey_question_id');
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

        // 11. ProjectEmployee → Program, Employee
        Schema::table('project_employees', function (Blueprint $table) {
            if (!Schema::hasColumn('project_employees', 'program_id')) {
                $table->uuid('program_id');
            }
            if (!Schema::hasColumn('project_employees', 'employee_id')) {
                $table->uuid('employee_id');
            }
            $table->foreign('program_id', 'FK_PROJECT_EMPLOYEES_PROGRAM_ID')
                  ->references('program_id')->on('programs')
                  ->onDelete('cascade');
            $table->foreign('employee_id', 'FK_PROJECT_EMPLOYEES_EMPLOYEE_ID')
                  ->references('employee_id')->on('employees')
                  ->onDelete('cascade');
        });

        // 12. Employee → Role, Project
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'role_id')) {
                $table->uuid('role_id')->nullable();
            }
            if (!Schema::hasColumn('employees', 'project_id')) {
                $table->uuid('project_id')->nullable();
            }
            $table->foreign('role_id', 'FK_EMPLOYEES_ROLE_ID')
                  ->references('role_id')->on('roles')
                  ->onDelete('set null');
            $table->foreign('project_id', 'FK_EMPLOYEES_PROJECT_ID')
                  ->references('project_id')->on('projects')
                  ->onDelete('set null');
        });

        // 13. Portfolio_Activities → Portfolio, Activities
        Schema::table('portfolio_activities', function (Blueprint $table) {
            if (!Schema::hasColumn('portfolio_activities', 'portfolio_id')) {
                $table->uuid('portfolio_id');
            }
            if (!Schema::hasColumn('portfolio_activities', 'activity_id')) {
                $table->uuid('activity_id');
            }

            $table->foreign('portfolio_id', 'FK_PORTFOLIO_ACTIVITIES_PORTFOLIO_ID')
                  ->references('portfolio_id')->on('portfolios')
                  ->onDelete('cascade');

            $table->foreign('activity_id', 'FK_PORTFOLIO_ACTIVITIES_ACTIVITY_ID')
                  ->references('activity_id')->on('activities')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('portfolio_activities', function (Blueprint $table) {
            $table->dropForeign('FK_PORTFOLIO_ACTIVITIES_PORTFOLIO_ID');
            $table->dropForeign('FK_PORTFOLIO_ACTIVITIES_ACTIVITY_ID');
        });
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
            $t->dropForeign('FK_PROJECT_ACTIVITIES_project_id');
            $t->dropForeign('FK_PROJECT_ACTIVITIES_ACTIVITY_ID');
        });
        Schema::table('projects', function (Blueprint $t) {
            $t->dropForeign('FK_project_PARENT');
            $t->dropForeign('FK_project_PROGRAM_ID');
            $t->dropColumn('parent_project_id');
        });
        Schema::table('cops', fn(Blueprint $t) => $t->dropForeign('FK_COPS_PROGRAM_ID'));
        Schema::table('programs', fn(Blueprint $t) => $t->dropForeign('FK_PROGRAMS_PARENT_PROGRAM'));
    }
};
