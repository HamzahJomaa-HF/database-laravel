<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\ActivityUser;
use App\Models\ActivityFinancial;
use App\Models\User;
use App\Models\Program;
use App\Models\Project;
use App\Models\Portfolio;
use App\Models\Cop;
use App\Models\PortfolioActivity;

class AnalyticsController extends Controller
{
    public function index()
    {
        // ── OVERVIEW KPIs ──────────────────────────────────────────
        $overview = [
            'total_users'          => User::count(),
            'total_activities'     => Activity::count(),
            'total_financials_amt' => (float) ActivityFinancial::sum('amount'),
            'total_participation'  => ActivityUser::count(),
            'total_programs'       => Program::count(),
            'total_projects'       => Project::count(),
            'total_portfolios'     => Portfolio::count(),
            'total_cops'           => Cop::count(),
        ];

        // ── USERS ──────────────────────────────────────────────────
        $usersByGender = User::selectRaw("COALESCE(gender,'Unknown') as label, COUNT(*) as cnt")
            ->groupBy('label')->orderByDesc('cnt')->get();

        $usersBySector = User::selectRaw("COALESCE(NULLIF(sector,''),'Unknown') as label, COUNT(*) as cnt")
            ->groupBy('label')->orderByDesc('cnt')->limit(12)->get();

        $usersByOrgType = User::selectRaw("COALESCE(NULLIF(organization_type_1,''),'Unknown') as label, COUNT(*) as cnt")
            ->groupBy('label')->orderByDesc('cnt')->limit(10)->get();

        $usersByEmployment = User::selectRaw("COALESCE(NULLIF(employment_status,''),'Unknown') as label, COUNT(*) as cnt")
            ->groupBy('label')->orderByDesc('cnt')->get();

        $usersByMarital = User::selectRaw("COALESCE(NULLIF(marital_status,''),'Unknown') as label, COUNT(*) as cnt")
            ->groupBy('label')->orderByDesc('cnt')->get();

        $usersHighProfile = [
            'high'   => User::where('is_high_profile', true)->count(),
            'normal' => User::where('is_high_profile', false)->orWhereNull('is_high_profile')->count(),
        ];

        $usersByMonth = User::selectRaw("TO_CHAR(created_at,'YYYY-MM') as month, COUNT(*) as cnt")
            ->whereNotNull('created_at')
            ->groupBy('month')->orderBy('month')->get();

        // ── ACTIVITIES ─────────────────────────────────────────────
        $activitiesByType = Activity::selectRaw("COALESCE(NULLIF(activity_type,''),'Unknown') as label, COUNT(*) as cnt")
            ->groupBy('label')->orderByDesc('cnt')->limit(12)->get();

        $activitiesByNetwork = Activity::selectRaw("COALESCE(NULLIF(content_network,''),'Unknown') as label, COUNT(*) as cnt")
            ->groupBy('label')->orderByDesc('cnt')->get();

        $activitiesByMonth = Activity::selectRaw("TO_CHAR(start_date,'YYYY-MM') as month, COUNT(*) as cnt")
            ->whereNotNull('start_date')
            ->groupBy('month')->orderBy('month')->get();

        $topActivitiesByParticipants = Activity::selectRaw("
                activities.activity_id,
                COALESCE(activities.activity_title_en, activities.folder_name, 'Untitled') as title,
                COUNT(activity_users.activity_user_id) as cnt
            ")
            ->leftJoin('activity_users', 'activities.activity_id', '=', 'activity_users.activity_id')
            ->groupBy('activities.activity_id', 'title')
            ->orderByDesc('cnt')
            ->limit(10)
            ->get();

        // ── PARTICIPATION (ActivityUsers) ───────────────────────────
        $participationOverview = [
            'total'    => ActivityUser::count(),
            'attended' => ActivityUser::where('attended', true)->count(),
            'invited'  => ActivityUser::where('invited', true)->count(),
            'leads'    => ActivityUser::where('is_lead', true)->count(),
        ];

        $participationByCop = ActivityUser::selectRaw("
                cops.cop_name as label, COUNT(activity_users.activity_user_id) as cnt
            ")
            ->leftJoin('cops', 'activity_users.cop_id', '=', 'cops.cop_id')
            ->groupBy('cops.cop_name')
            ->orderByDesc('cnt')
            ->limit(10)
            ->get();

        $participationByType = ActivityUser::selectRaw("COALESCE(NULLIF(type,''),'Unknown') as label, COUNT(*) as cnt")
            ->groupBy('label')->orderByDesc('cnt')->limit(10)->get();

        // ── FINANCIALS ─────────────────────────────────────────────
        $financialsByType = ActivityFinancial::selectRaw(
            "financial_type as label, COUNT(*) as cnt, COALESCE(SUM(amount),0) as total"
        )->groupBy('financial_type')->get();

        $financialsByStatus = ActivityFinancial::selectRaw(
            "payment_status as label, COUNT(*) as cnt, COALESCE(SUM(amount),0) as total"
        )->groupBy('payment_status')->get();

        $financialsByMonth = ActivityFinancial::selectRaw(
            "TO_CHAR(tx_date,'YYYY-MM') as month, COALESCE(SUM(amount),0) as total, COUNT(*) as cnt"
        )->whereNotNull('tx_date')->groupBy('month')->orderBy('month')->get();

        $omtCostFields = ['operational_cost','personnel_cost','travel_cost','equipment_cost','supplies_cost','training_cost','communication_cost'];
        $omtBreakdown = [];
        foreach ($omtCostFields as $f) {
            $omtBreakdown[$f] = (float) ActivityFinancial::where('financial_type','omt')
                ->selectRaw("COALESCE(SUM((financial_data->>'$f')::numeric),0) as val")->value('val');
        }

        $medicineByDisease = ActivityFinancial::where('financial_type','medical')
            ->whereRaw("financial_data->>'medication_type' = 'medicine'")
            ->selectRaw("financial_data->>'disease_type' as label, COUNT(*) as cnt, COALESCE(SUM(amount),0) as total")
            ->groupBy('label')->orderByDesc('total')->limit(10)->get();

        $hospitalByOperation = ActivityFinancial::where('financial_type','medical')
            ->whereRaw("financial_data->>'medication_type' = 'hospital'")
            ->selectRaw("financial_data->>'operation_type' as label, COUNT(*) as cnt, COALESCE(SUM(amount),0) as total")
            ->groupBy('label')->orderByDesc('total')->limit(10)->get();

        $educationByLevel = ActivityFinancial::where('financial_type','education')
            ->selectRaw("financial_data->>'education_level' as label, COUNT(*) as cnt, COALESCE(SUM(amount),0) as total")
            ->groupBy('label')->orderByDesc('total')->get();

        $educationByInstitution = ActivityFinancial::where('financial_type','education')
            ->selectRaw("financial_data->>'institution_name' as label, COUNT(*) as cnt, COALESCE(SUM(amount),0) as total")
            ->groupBy('label')->orderByDesc('total')->limit(10)->get();

        // ── PROGRAMS & PROJECTS ────────────────────────────────────
        $programsByType = Program::selectRaw("COALESCE(NULLIF(program_type,''),'Unknown') as label, COUNT(*) as cnt")
            ->groupBy('label')->orderByDesc('cnt')->get();

        $projectsPerProgram = Project::selectRaw("
                programs.name as label, COUNT(projects.project_id) as cnt
            ")
            ->leftJoin('programs', 'projects.program_id', '=', 'programs.program_id')
            ->groupBy('programs.name')
            ->orderByDesc('cnt')
            ->limit(10)
            ->get();

        // ── COPs & PORTFOLIOS ──────────────────────────────────────
        $activityPerCop = Cop::selectRaw("
                cops.cop_name as label,
                COUNT(DISTINCT activity_users.activity_id) as activities,
                COUNT(DISTINCT activity_users.user_id) as participants
            ")
            ->leftJoin('activity_users', 'cops.cop_id', '=', 'activity_users.cop_id')
            ->groupBy('cops.cop_name')
            ->orderByDesc('activities')
            ->limit(12)
            ->get();

        $activityPerPortfolio = Portfolio::selectRaw("
                portfolios.name as label,
                COUNT(portfolio_activities.activity_id) as cnt
            ")
            ->leftJoin('portfolio_activities', 'portfolios.portfolio_id', '=', 'portfolio_activities.portfolio_id')
            ->groupBy('portfolios.name')
            ->orderByDesc('cnt')
            ->limit(10)
            ->get();

        return view('analytics.index', compact(
            'overview',
            'usersByGender','usersBySector','usersByOrgType',
            'usersByEmployment','usersByMarital','usersHighProfile','usersByMonth',
            'activitiesByType','activitiesByNetwork','activitiesByMonth','topActivitiesByParticipants',
            'participationOverview','participationByCop','participationByType',
            'financialsByType','financialsByStatus','financialsByMonth',
            'omtBreakdown','medicineByDisease','hospitalByOperation',
            'educationByLevel','educationByInstitution',
            'programsByType','projectsPerProgram',
            'activityPerCop','activityPerPortfolio'
        ));
    }
}
