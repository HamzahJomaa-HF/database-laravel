@extends('layouts.app')

@section('title', 'Analytics')

@section('styles')
<style>
:root {
    --a-blue:    #2563eb;
    --a-indigo:  #4f46e5;
    --a-pink:    #ec4899;
    --a-green:   #10b981;
    --a-amber:   #f59e0b;
    --a-red:     #ef4444;
    --a-purple:  #8b5cf6;
    --a-teal:    #14b8a6;
    --a-orange:  #f97316;
    --a-sky:     #0ea5e9;
    --radius: 12px;
}

/* ── Layout ──────────────────────────────────────────── */
.an-page { padding: 1.5rem; background: #f1f5f9; min-height: 100vh; }

/* ── Module selector ─────────────────────────────────── */
.mod-bar {
    display: flex; gap: .5rem; flex-wrap: wrap;
    background: #fff; border-radius: var(--radius);
    padding: .75rem 1rem; margin-bottom: 1.5rem;
    box-shadow: 0 1px 4px rgba(0,0,0,.07);
}
.mod-btn {
    padding: .4rem 1rem; border-radius: 999px; font-size: .8rem;
    font-weight: 700; cursor: pointer; border: 2px solid transparent;
    background: #f1f5f9; color: #64748b; transition: all .18s;
    white-space: nowrap;
}
.mod-btn:hover { border-color: var(--a-blue); color: var(--a-blue); }
.mod-btn.active { color: #fff; }
.mod-btn[data-m="overview"].active   { background: var(--a-indigo); border-color: var(--a-indigo); }
.mod-btn[data-m="activities"].active { background: var(--a-blue);   border-color: var(--a-blue);   }
.mod-btn[data-m="users"].active      { background: var(--a-purple);  border-color: var(--a-purple);  }
.mod-btn[data-m="participation"].active { background: var(--a-teal); border-color: var(--a-teal);  }
.mod-btn[data-m="financials"].active { background: var(--a-pink);   border-color: var(--a-pink);   }
.mod-btn[data-m="programs"].active   { background: var(--a-orange);  border-color: var(--a-orange);  }
.mod-btn[data-m="cops"].active       { background: var(--a-green);   border-color: var(--a-green);   }

/* ── Sections ────────────────────────────────────────── */
.mod-section { display: none; }
.mod-section.active { display: block; }

/* ── KPI grid ────────────────────────────────────────── */
.kpi-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px,1fr)); gap: 1rem; margin-bottom: 1.5rem; }
.kpi-card {
    background: #fff; border-radius: var(--radius);
    padding: 1rem 1.2rem; box-shadow: 0 1px 4px rgba(0,0,0,.07);
    border-top: 4px solid var(--a-blue);
}
.kpi-card .kl { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #64748b; }
.kpi-card .kv { font-size: 1.55rem; font-weight: 800; color: #1e293b; margin: .2rem 0 .1rem; }
.kpi-card .ks { font-size: .73rem; color: #94a3b8; }

/* ── Charts grid ─────────────────────────────────────── */
.chart-grid { display: grid; gap: 1.2rem; grid-template-columns: repeat(auto-fill, minmax(340px,1fr)); }
.chart-panel {
    background: #fff; border-radius: var(--radius);
    padding: 1.2rem 1.4rem; box-shadow: 0 1px 4px rgba(0,0,0,.07);
}
.chart-panel.wide { grid-column: 1/-1; }
.chart-panel.half { grid-column: span 1; }
.chart-title {
    font-size: .85rem; font-weight: 700; color: #334155;
    margin-bottom: .9rem; display: flex; align-items: center; gap: .35rem;
}
.chart-title i { font-size: 1rem; }

/* ── Section badge ───────────────────────────────────── */
.sec-badge {
    display: inline-flex; align-items: center; gap: .5rem;
    color: #fff; border-radius: 8px; padding: .3rem .9rem;
    font-size: .78rem; font-weight: 700; margin-bottom: 1.1rem;
    text-transform: uppercase; letter-spacing: .05em;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
@endsection

@section('content')
@php
/* ── colour palette helper ─────────────────────────── */
$pal = ['#2563eb','#ec4899','#10b981','#f59e0b','#6366f1','#ef4444','#0ea5e9','#a855f7','#f97316','#14b8a6','#84cc16','#f43f5e'];
$clr = fn($n) => array_map(fn($i) => $pal[$i % count($pal)], range(0, $n-1));

/* ── prep helpers ───────────────────────────────────── */
$labels  = fn($col) => $col->pluck('label')->map(fn($v) => $v ?? 'Unknown')->toArray();
$counts  = fn($col) => $col->pluck('cnt')->map(fn($v) => (int)$v)->toArray();
$totals  = fn($col) => $col->pluck('total')->map(fn($v) => (float)$v)->toArray();

/* ── Users ──────────────────────────────────────────── */
$ugLabels = $labels($usersByGender);   $ugCnts = $counts($usersByGender);
$usLabels = $labels($usersBySector);   $usCnts = $counts($usersBySector);
$uoLabels = $labels($usersByOrgType);  $uoCnts = $counts($usersByOrgType);
$ueLabels = $labels($usersByEmployment);$ueCnts = $counts($usersByEmployment);
$umLabels = $labels($usersByMarital);  $umCnts = $counts($usersByMarital);
$umthLabels = $usersByMonth->pluck('month')->toArray();
$umthCnts   = $usersByMonth->pluck('cnt')->map(fn($v)=>(int)$v)->toArray();

/* ── Activities ─────────────────────────────────────── */
$atLabels  = $labels($activitiesByType);    $atCnts = $counts($activitiesByType);
$anLabels  = $labels($activitiesByNetwork); $anCnts = $counts($activitiesByNetwork);
$amthLbls  = $activitiesByMonth->pluck('month')->toArray();
$amthCnts  = $activitiesByMonth->pluck('cnt')->map(fn($v)=>(int)$v)->toArray();
$topActLbls= $topActivitiesByParticipants->pluck('title')->toArray();
$topActCnts= $topActivitiesByParticipants->pluck('cnt')->map(fn($v)=>(int)$v)->toArray();

/* ── Participation ──────────────────────────────────── */
$pcLabels  = $labels($participationByCop);  $pcCnts = $counts($participationByCop);
$ptLabels  = $labels($participationByType); $ptCnts = $counts($participationByType);

/* ── Financials ─────────────────────────────────────── */
$ftLabels  = $financialsByType->pluck('label')->map(fn($v)=>ucfirst($v))->toArray();
$ftAmts    = $totals($financialsByType);
$fsLabels  = $financialsByStatus->pluck('label')->map(fn($v)=>ucfirst($v))->toArray();
$fsAmts    = $totals($financialsByStatus);
$fmLbls    = $financialsByMonth->pluck('month')->toArray();
$fmAmts    = $financialsByMonth->pluck('total')->map(fn($v)=>(float)$v)->toArray();
$omtLbls   = array_map(fn($k)=>ucwords(str_replace('_',' ',$k)), array_keys($omtBreakdown));
$omtVals   = array_values($omtBreakdown);
$mdLabels  = $labels($medicineByDisease);    $mdAmts = $totals($medicineByDisease);
$hoLabels  = $labels($hospitalByOperation);  $hoAmts = $totals($hospitalByOperation);
$elLabels  = $labels($educationByLevel);     $elAmts = $totals($educationByLevel);
$eiLabels  = $labels($educationByInstitution);$eiAmts = $totals($educationByInstitution);

/* ── Programs & Projects ────────────────────────────── */
$pgLabels  = $labels($programsByType);        $pgCnts = $counts($programsByType);
$prLabels  = $labels($projectsPerProgram);    $prCnts = $counts($projectsPerProgram);

/* ── COPs & Portfolios ──────────────────────────────── */
$copLabels = $activityPerCop->pluck('label')->toArray();
$copActs   = $activityPerCop->pluck('activities')->map(fn($v)=>(int)$v)->toArray();
$copParts  = $activityPerCop->pluck('participants')->map(fn($v)=>(int)$v)->toArray();
$pfLabels  = $labels($activityPerPortfolio);  $pfCnts = $counts($activityPerPortfolio);
@endphp

<div class="an-page">

    {{-- Header ─────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 style="font-weight:800;color:#1e293b;margin:0;">
                <i class="bi bi-graph-up-arrow me-2" style="color:var(--a-indigo);"></i>Analytics Dashboard
            </h4>
            <p style="color:#64748b;font-size:.82rem;margin:.2rem 0 0;">
                Cross-module PowerBI-style insights · All data live
            </p>
        </div>
        <span style="font-size:.78rem;color:#94a3b8;">Last updated: {{ now()->format('d M Y, H:i') }}</span>
    </div>

    {{-- Module selector ─────────────────────────────────── --}}
    <div class="mod-bar">
        <button class="mod-btn active" data-m="overview">
            <i class="bi bi-grid-1x2 me-1"></i> Overview
        </button>
        <button class="mod-btn" data-m="activities">
            <i class="bi bi-calendar-event me-1"></i> Activities
        </button>
        <button class="mod-btn" data-m="users">
            <i class="bi bi-people me-1"></i> Users
        </button>
        <button class="mod-btn" data-m="participation">
            <i class="bi bi-person-check me-1"></i> Participation
        </button>
        <button class="mod-btn" data-m="financials">
            <i class="bi bi-coin me-1"></i> Financials
        </button>
        <button class="mod-btn" data-m="programs">
            <i class="bi bi-diagram-3 me-1"></i> Programs & Projects
        </button>
        <button class="mod-btn" data-m="cops">
            <i class="bi bi-people-fill me-1"></i> COPs & Portfolios
        </button>
    </div>

    {{-- ════════════════════════════════════════════ --}}
    {{-- OVERVIEW ──────────────────────────────────── --}}
    {{-- ════════════════════════════════════════════ --}}
    <div class="mod-section active" id="mod-overview">
        <div class="sec-badge" style="background:var(--a-indigo);">
            <i class="bi bi-grid-1x2"></i> Overview
        </div>

        <div class="kpi-grid">
            <div class="kpi-card" style="border-color:var(--a-purple);">
                <div class="kl">Users</div>
                <div class="kv">{{ number_format($overview['total_users']) }}</div>
                <div class="ks">Total beneficiaries</div>
            </div>
            <div class="kpi-card" style="border-color:var(--a-blue);">
                <div class="kl">Activities</div>
                <div class="kv">{{ number_format($overview['total_activities']) }}</div>
                <div class="ks">Total activities</div>
            </div>
            <div class="kpi-card" style="border-color:var(--a-pink);">
                <div class="kl">Financials</div>
                <div class="kv">${{ number_format($overview['total_financials_amt'], 0) }}</div>
                <div class="ks">Total disbursed</div>
            </div>
            <div class="kpi-card" style="border-color:var(--a-teal);">
                <div class="kl">Participation</div>
                <div class="kv">{{ number_format($overview['total_participation']) }}</div>
                <div class="ks">Activity-user records</div>
            </div>
            <div class="kpi-card" style="border-color:var(--a-orange);">
                <div class="kl">Programs</div>
                <div class="kv">{{ number_format($overview['total_programs']) }}</div>
            </div>
            <div class="kpi-card" style="border-color:var(--a-amber);">
                <div class="kl">Projects</div>
                <div class="kv">{{ number_format($overview['total_projects']) }}</div>
            </div>
            <div class="kpi-card" style="border-color:var(--a-green);">
                <div class="kl">Portfolios</div>
                <div class="kv">{{ number_format($overview['total_portfolios']) }}</div>
            </div>
            <div class="kpi-card" style="border-color:var(--a-sky);">
                <div class="kl">COPs</div>
                <div class="kv">{{ number_format($overview['total_cops']) }}</div>
            </div>
        </div>

        <div class="chart-grid">
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-pie-chart" style="color:var(--a-indigo);"></i> Users by Gender</div>
                <canvas id="ov-gender" height="200"></canvas>
            </div>
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-bar-chart" style="color:var(--a-blue);"></i> Activities by Type</div>
                <canvas id="ov-act-type" height="200"></canvas>
            </div>
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-pie-chart" style="color:var(--a-pink);"></i> Financials by Type</div>
                <canvas id="ov-fin-type" height="200"></canvas>
            </div>
            <div class="chart-panel wide">
                <div class="chart-title"><i class="bi bi-graph-up" style="color:var(--a-teal);"></i> Monthly Activity Trend</div>
                <canvas id="ov-act-monthly" height="80"></canvas>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════ --}}
    {{-- ACTIVITIES ─────────────────────────────────── --}}
    {{-- ════════════════════════════════════════════ --}}
    <div class="mod-section" id="mod-activities">
        <div class="sec-badge" style="background:var(--a-blue);">
            <i class="bi bi-calendar-event"></i> Activities
        </div>

        <div class="kpi-grid">
            <div class="kpi-card" style="border-color:var(--a-blue);">
                <div class="kl">Total Activities</div>
                <div class="kv">{{ number_format($overview['total_activities']) }}</div>
            </div>
            <div class="kpi-card" style="border-color:var(--a-blue);">
                <div class="kl">Activity Types</div>
                <div class="kv">{{ count($atLabels) }}</div>
                <div class="ks">Distinct types</div>
            </div>
            <div class="kpi-card" style="border-color:var(--a-sky);">
                <div class="kl">Networks</div>
                <div class="kv">{{ count($anLabels) }}</div>
                <div class="ks">Content networks</div>
            </div>
        </div>

        <div class="chart-grid">
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-pie-chart" style="color:var(--a-blue);"></i> By Activity Type</div>
                <canvas id="act-type-donut" height="220"></canvas>
            </div>
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-pie-chart" style="color:var(--a-sky);"></i> By Content Network</div>
                <canvas id="act-network-donut" height="220"></canvas>
            </div>
            <div class="chart-panel wide">
                <div class="chart-title"><i class="bi bi-graph-up" style="color:var(--a-blue);"></i> Monthly Activity Count</div>
                <canvas id="act-monthly-line" height="90"></canvas>
            </div>
            <div class="chart-panel wide">
                <div class="chart-title"><i class="bi bi-bar-chart-horizontal" style="color:var(--a-indigo);"></i> Top Activities by Participant Count</div>
                <canvas id="act-top-bar" height="120"></canvas>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════ --}}
    {{-- USERS ──────────────────────────────────────── --}}
    {{-- ════════════════════════════════════════════ --}}
    <div class="mod-section" id="mod-users">
        <div class="sec-badge" style="background:var(--a-purple);">
            <i class="bi bi-people"></i> Users
        </div>

        <div class="kpi-grid">
            <div class="kpi-card" style="border-color:var(--a-purple);">
                <div class="kl">Total Users</div>
                <div class="kv">{{ number_format($overview['total_users']) }}</div>
            </div>
            <div class="kpi-card" style="border-color:var(--a-red);">
                <div class="kl">High Profile</div>
                <div class="kv">{{ number_format($usersHighProfile['high']) }}</div>
                <div class="ks">{{ $overview['total_users'] > 0 ? round($usersHighProfile['high']/$overview['total_users']*100,1) : 0 }}% of total</div>
            </div>
            <div class="kpi-card" style="border-color:var(--a-green);">
                <div class="kl">Sectors</div>
                <div class="kv">{{ count($usLabels) }}</div>
                <div class="ks">Distinct sectors</div>
            </div>
            <div class="kpi-card" style="border-color:var(--a-amber);">
                <div class="kl">Org Types</div>
                <div class="kv">{{ count($uoLabels) }}</div>
                <div class="ks">Distinct org types</div>
            </div>
        </div>

        <div class="chart-grid">
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-pie-chart" style="color:var(--a-purple);"></i> By Gender</div>
                <canvas id="usr-gender-donut" height="220"></canvas>
            </div>
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-pie-chart" style="color:var(--a-amber);"></i> By Marital Status</div>
                <canvas id="usr-marital-donut" height="220"></canvas>
            </div>
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-bar-chart-horizontal" style="color:var(--a-teal);"></i> By Employment Status</div>
                <canvas id="usr-employ-bar" height="220"></canvas>
            </div>
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-pie-chart" style="color:var(--a-red);"></i> High Profile vs Normal</div>
                <canvas id="usr-highprofile-donut" height="220"></canvas>
            </div>
            <div class="chart-panel wide">
                <div class="chart-title"><i class="bi bi-bar-chart-horizontal" style="color:var(--a-indigo);"></i> Top Sectors</div>
                <canvas id="usr-sector-bar" height="120"></canvas>
            </div>
            <div class="chart-panel wide">
                <div class="chart-title"><i class="bi bi-bar-chart-horizontal" style="color:var(--a-orange);"></i> By Organization Type</div>
                <canvas id="usr-orgtype-bar" height="110"></canvas>
            </div>
            <div class="chart-panel wide">
                <div class="chart-title"><i class="bi bi-graph-up" style="color:var(--a-purple);"></i> Monthly User Registrations</div>
                <canvas id="usr-monthly-line" height="80"></canvas>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════ --}}
    {{-- PARTICIPATION ──────────────────────────────── --}}
    {{-- ════════════════════════════════════════════ --}}
    <div class="mod-section" id="mod-participation">
        <div class="sec-badge" style="background:var(--a-teal);">
            <i class="bi bi-person-check"></i> Participation
        </div>

        <div class="kpi-grid">
            <div class="kpi-card" style="border-color:var(--a-teal);">
                <div class="kl">Total Records</div>
                <div class="kv">{{ number_format($participationOverview['total']) }}</div>
            </div>
            <div class="kpi-card" style="border-color:var(--a-green);">
                <div class="kl">Attended</div>
                <div class="kv">{{ number_format($participationOverview['attended']) }}</div>
                <div class="ks">
                    @if($participationOverview['total'] > 0)
                        {{ round($participationOverview['attended']/$participationOverview['total']*100,1) }}% rate
                    @endif
                </div>
            </div>
            <div class="kpi-card" style="border-color:var(--a-amber);">
                <div class="kl">Invited</div>
                <div class="kv">{{ number_format($participationOverview['invited']) }}</div>
            </div>
            <div class="kpi-card" style="border-color:var(--a-purple);">
                <div class="kl">Leads</div>
                <div class="kv">{{ number_format($participationOverview['leads']) }}</div>
                <div class="ks">Activity leads</div>
            </div>
        </div>

        <div class="chart-grid">
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-pie-chart" style="color:var(--a-teal);"></i> Attendance Rate</div>
                <canvas id="par-attend-donut" height="220"></canvas>
            </div>
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-pie-chart" style="color:var(--a-purple);"></i> By Participant Type</div>
                <canvas id="par-type-donut" height="220"></canvas>
            </div>
            <div class="chart-panel wide">
                <div class="chart-title"><i class="bi bi-bar-chart-horizontal" style="color:var(--a-sky);"></i> Participation by COP</div>
                <canvas id="par-cop-bar" height="130"></canvas>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════ --}}
    {{-- FINANCIALS ─────────────────────────────────── --}}
    {{-- ════════════════════════════════════════════ --}}
    <div class="mod-section" id="mod-financials">
        <div class="sec-badge" style="background:var(--a-pink);">
            <i class="bi bi-coin"></i> Financials
        </div>

        @php
        $finGrand = $financialsByType->sum('total');
        $finPaid  = $financialsByStatus->where('label','paid')->first()?->total ?? 0;
        $finPend  = $financialsByStatus->where('label','pending')->first()?->total ?? 0;
        $finOver  = $financialsByStatus->where('label','overdue')->first()?->total ?? 0;
        @endphp

        <div class="kpi-grid">
            <div class="kpi-card" style="border-color:var(--a-pink);">
                <div class="kl">Grand Total</div>
                <div class="kv">${{ number_format($finGrand, 0) }}</div>
            </div>
            <div class="kpi-card" style="border-color:var(--a-green);">
                <div class="kl">Paid</div>
                <div class="kv">${{ number_format($finPaid, 0) }}</div>
            </div>
            <div class="kpi-card" style="border-color:var(--a-amber);">
                <div class="kl">Pending</div>
                <div class="kv">${{ number_format($finPend, 0) }}</div>
            </div>
            <div class="kpi-card" style="border-color:var(--a-red);">
                <div class="kl">Overdue</div>
                <div class="kv">${{ number_format($finOver, 0) }}</div>
            </div>
        </div>

        <div class="chart-grid">
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-pie-chart" style="color:var(--a-pink);"></i> Amount by Type</div>
                <canvas id="fin-type-donut" height="220"></canvas>
            </div>
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-bar-chart" style="color:var(--a-amber);"></i> Amount by Payment Status</div>
                <canvas id="fin-status-bar" height="220"></canvas>
            </div>
            <div class="chart-panel wide">
                <div class="chart-title"><i class="bi bi-graph-up" style="color:var(--a-pink);"></i> Monthly Financial Trend</div>
                <canvas id="fin-monthly-line" height="80"></canvas>
            </div>

            {{-- OMT --}}
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-pie-chart" style="color:var(--a-amber);"></i> OMT Cost Categories</div>
                <canvas id="fin-omt-donut" height="220"></canvas>
            </div>
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-bar-chart-horizontal" style="color:var(--a-amber);"></i> OMT Breakdown (Bar)</div>
                <canvas id="fin-omt-bar" height="220"></canvas>
            </div>

            {{-- Medicine --}}
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-pie-chart" style="color:var(--a-blue);"></i> Medicine by Disease</div>
                <canvas id="fin-med-disease-donut" height="220"></canvas>
            </div>
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-bar-chart-horizontal" style="color:var(--a-blue);"></i> Disease Amounts (Bar)</div>
                <canvas id="fin-med-disease-bar" height="220"></canvas>
            </div>

            {{-- Hospital --}}
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-pie-chart" style="color:var(--a-red);"></i> Hospital by Operation Type</div>
                <canvas id="fin-hosp-donut" height="220"></canvas>
            </div>
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-bar-chart-horizontal" style="color:var(--a-red);"></i> Operation Amounts (Bar)</div>
                <canvas id="fin-hosp-bar" height="220"></canvas>
            </div>

            {{-- Education --}}
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-pie-chart" style="color:var(--a-green);"></i> Education by Level</div>
                <canvas id="fin-edu-level-donut" height="220"></canvas>
            </div>
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-bar-chart-horizontal" style="color:var(--a-green);"></i> Top Institutions</div>
                <canvas id="fin-edu-inst-bar" height="220"></canvas>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════ --}}
    {{-- PROGRAMS & PROJECTS ────────────────────────── --}}
    {{-- ════════════════════════════════════════════ --}}
    <div class="mod-section" id="mod-programs">
        <div class="sec-badge" style="background:var(--a-orange);">
            <i class="bi bi-diagram-3"></i> Programs & Projects
        </div>

        <div class="kpi-grid">
            <div class="kpi-card" style="border-color:var(--a-orange);">
                <div class="kl">Programs</div>
                <div class="kv">{{ number_format($overview['total_programs']) }}</div>
            </div>
            <div class="kpi-card" style="border-color:var(--a-amber);">
                <div class="kl">Projects</div>
                <div class="kv">{{ number_format($overview['total_projects']) }}</div>
            </div>
            <div class="kpi-card" style="border-color:var(--a-orange);">
                <div class="kl">Program Types</div>
                <div class="kv">{{ count($pgLabels) }}</div>
            </div>
        </div>

        <div class="chart-grid">
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-pie-chart" style="color:var(--a-orange);"></i> Programs by Type</div>
                <canvas id="pg-type-donut" height="220"></canvas>
            </div>
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-bar-chart" style="color:var(--a-amber);"></i> Programs by Type (Bar)</div>
                <canvas id="pg-type-bar" height="220"></canvas>
            </div>
            <div class="chart-panel wide">
                <div class="chart-title"><i class="bi bi-bar-chart-horizontal" style="color:var(--a-orange);"></i> Projects per Program</div>
                <canvas id="pg-projects-bar" height="120"></canvas>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════ --}}
    {{-- COPs & PORTFOLIOS ──────────────────────────── --}}
    {{-- ════════════════════════════════════════════ --}}
    <div class="mod-section" id="mod-cops">
        <div class="sec-badge" style="background:var(--a-green);">
            <i class="bi bi-people-fill"></i> COPs & Portfolios
        </div>

        <div class="kpi-grid">
            <div class="kpi-card" style="border-color:var(--a-green);">
                <div class="kl">COPs</div>
                <div class="kv">{{ number_format($overview['total_cops']) }}</div>
            </div>
            <div class="kpi-card" style="border-color:var(--a-teal);">
                <div class="kl">Portfolios</div>
                <div class="kv">{{ number_format($overview['total_portfolios']) }}</div>
            </div>
        </div>

        <div class="chart-grid">
            <div class="chart-panel wide">
                <div class="chart-title"><i class="bi bi-bar-chart" style="color:var(--a-green);"></i> Activities per COP</div>
                <canvas id="cop-act-bar" height="100"></canvas>
            </div>
            <div class="chart-panel wide">
                <div class="chart-title"><i class="bi bi-bar-chart" style="color:var(--a-sky);"></i> Participants per COP</div>
                <canvas id="cop-part-bar" height="100"></canvas>
            </div>
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-pie-chart" style="color:var(--a-teal);"></i> Activities per Portfolio</div>
                <canvas id="pf-donut" height="220"></canvas>
            </div>
            <div class="chart-panel">
                <div class="chart-title"><i class="bi bi-bar-chart-horizontal" style="color:var(--a-teal);"></i> Portfolio Activity Count</div>
                <canvas id="pf-bar" height="220"></canvas>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── helpers ──────────────────────────────────────────────
const pal = ['#2563eb','#ec4899','#10b981','#f59e0b','#6366f1','#ef4444','#0ea5e9','#a855f7','#f97316','#14b8a6','#84cc16','#f43f5e'];
const clr  = n  => Array.from({length:n},(_,i)=>pal[i%pal.length]);
const fmtD = v  => '$' + parseFloat(v).toLocaleString(undefined,{minimumFractionDigits:0,maximumFractionDigits:0});
const fmtN = v  => parseFloat(v).toLocaleString();

const donut = (id, labels, data, colors) => new Chart(document.getElementById(id), {
    type:'doughnut',
    data:{ labels, datasets:[{ data, backgroundColor:colors||clr(data.length), hoverOffset:6 }] },
    options:{ plugins:{ legend:{ position:'bottom', labels:{font:{size:11},boxWidth:12} } } }
});
const donutFmt = (id, labels, data, colors, fmt) => new Chart(document.getElementById(id), {
    type:'doughnut',
    data:{ labels, datasets:[{ data, backgroundColor:colors||clr(data.length), hoverOffset:6 }] },
    options:{ plugins:{
        legend:{ position:'bottom', labels:{font:{size:11},boxWidth:12} },
        tooltip:{ callbacks:{ label: ctx => fmt(ctx.raw) } }
    }}
});
const barH = (id, labels, data, colors) => new Chart(document.getElementById(id), {
    type:'bar',
    data:{ labels, datasets:[{ data, backgroundColor:colors||clr(data.length), borderRadius:5 }] },
    options:{ indexAxis:'y', plugins:{ legend:{ display:false } }, scales:{ x:{ ticks:{ callback:fmtN } } } }
});
const barV = (id, labels, data, colors, fmtFn) => new Chart(document.getElementById(id), {
    type:'bar',
    data:{ labels, datasets:[{ data, backgroundColor:colors||clr(data.length), borderRadius:5 }] },
    options:{ plugins:{ legend:{ display:false } }, scales:{ y:{ ticks:{ callback:fmtFn||fmtN } } } }
});
const line = (id, labels, data, color, fmtFn) => new Chart(document.getElementById(id), {
    type:'line',
    data:{ labels, datasets:[{
        data, fill:true, tension:.35,
        borderColor: color||'#2563eb',
        backgroundColor: (color||'#2563eb').replace(')',', .08)').replace('rgb','rgba'),
        pointRadius:3, pointHoverRadius:5, pointBackgroundColor: color||'#2563eb'
    }] },
    options:{ plugins:{ legend:{ display:false } }, scales:{ y:{ ticks:{ callback: fmtFn||fmtN } } } }
});

// ── Module selector ──────────────────────────────────────
document.querySelectorAll('.mod-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.mod-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('.mod-section').forEach(s => s.classList.remove('active'));
        document.getElementById('mod-' + btn.dataset.m).classList.add('active');
    });
});

// ═══════════════════════════════════════════════════════
// OVERVIEW
// ═══════════════════════════════════════════════════════
donut('ov-gender',    @json($ugLabels), @json($ugCnts));
barV('ov-act-type',   @json($atLabels), @json($atCnts));
donutFmt('ov-fin-type', @json($ftLabels), @json($ftAmts), null, fmtD);
line('ov-act-monthly', @json($amthLbls), @json($amthCnts), '#2563eb');

// ═══════════════════════════════════════════════════════
// ACTIVITIES
// ═══════════════════════════════════════════════════════
donut('act-type-donut',    @json($atLabels), @json($atCnts));
donut('act-network-donut', @json($anLabels), @json($anCnts));
line('act-monthly-line',   @json($amthLbls), @json($amthCnts), '#2563eb');
barH('act-top-bar',        @json($topActLbls), @json($topActCnts));

// ═══════════════════════════════════════════════════════
// USERS
// ═══════════════════════════════════════════════════════
donut('usr-gender-donut',  @json($ugLabels), @json($ugCnts));
donut('usr-marital-donut', @json($umLabels), @json($umCnts));
barH('usr-employ-bar',     @json($ueLabels), @json($ueCnts));
donut('usr-highprofile-donut',
    ['High Profile','Normal'],
    [{{ $usersHighProfile['high'] }}, {{ $usersHighProfile['normal'] }}],
    ['#ef4444','#10b981']
);
barH('usr-sector-bar',  @json($usLabels), @json($usCnts));
barH('usr-orgtype-bar', @json($uoLabels), @json($uoCnts));
line('usr-monthly-line', @json($umthLabels), @json($umthCnts), '#8b5cf6');

// ═══════════════════════════════════════════════════════
// PARTICIPATION – initial data (all activities)
// ═══════════════════════════════════════════════════════
const parBaseUrl = '{{ url("/analytics/participation") }}';

@php
$pgLabels2 = $participationByGender->pluck('label')->toArray();
$pgCnts2   = $participationByGender->pluck('cnt')->map(fn($v)=>(int)$v)->toArray();
@endphp

let parCharts = {};

function destroyPar() {
    Object.values(parCharts).forEach(c => c?.destroy());
    parCharts = {};
}

function renderPar(data) {
    destroyPar();
    const total    = data.kpis.total;
    const attended = data.kpis.attended;
    const invited  = data.kpis.invited;
    const leads    = data.kpis.leads;

    // KPIs
    document.getElementById('kpi-total').textContent    = total.toLocaleString();
    document.getElementById('kpi-attended').textContent  = attended.toLocaleString();
    document.getElementById('kpi-invited').textContent   = invited.toLocaleString();
    document.getElementById('kpi-leads').textContent     = leads.toLocaleString();
    document.getElementById('kpi-attend-rate').textContent = total > 0
        ? (attended/total*100).toFixed(1) + '% rate' : '';

    const typeL  = data.by_type.map(r => r.label);
    const typeCn = data.by_type.map(r => +r.cnt);
    const copL   = data.by_cop.map(r => r.label);
    const copCn  = data.by_cop.map(r => +r.cnt);
    const genL   = data.by_gender.map(r => r.label);
    const genCn  = data.by_gender.map(r => +r.cnt);
    const secL   = data.by_sector ? data.by_sector.map(r => r.label) : [];
    const secCn  = data.by_sector ? data.by_sector.map(r => +r.cnt) : [];

    parCharts.attend = new Chart(document.getElementById('par-attend-donut'), {
        type:'doughnut',
        data:{ labels:['Attended','Not Attended','Leads'],
               datasets:[{ data:[attended, total-attended, leads],
                           backgroundColor:['#10b981','#e2e8f0','#8b5cf6'], hoverOffset:6 }] },
        options:{ plugins:{ legend:{ position:'bottom', labels:{font:{size:11},boxWidth:12} } } }
    });
    parCharts.type = donut('par-type-donut', typeL, typeCn);
    parCharts.gender = donut('par-gender-donut', genL, genCn);
    parCharts.cop  = barH('par-cop-bar', copL, copCn);
    parCharts.sec  = barH('par-sector-bar', secL, secCn);
}

// Initial render from server-side data
renderPar({
    kpis: {
        total:    {{ $participationOverview['total'] }},
        attended: {{ $participationOverview['attended'] }},
        invited:  {{ $participationOverview['invited'] }},
        leads:    {{ $participationOverview['leads'] }},
    },
    by_type:   @json($participationByType->map(fn($r)=>['label'=>$r->label,'cnt'=>$r->cnt])),
    by_cop:    @json($participationByCop->map(fn($r)=>['label'=>$r->label,'cnt'=>$r->cnt])),
    by_gender: @json($participationByGender->map(fn($r)=>['label'=>$r->label,'cnt'=>$r->cnt])),
    by_sector: [],
});

// Activity selector – fetch data via AJAX
document.getElementById('parActivitySelect').addEventListener('change', async function() {
    const actId  = this.value;
    const banner = document.getElementById('parActivityBanner');
    const spin   = document.getElementById('parLoadingSpinner');

    if (!actId) {
        banner.style.display = 'none';
        renderPar({
            kpis: {
                total:    {{ $participationOverview['total'] }},
                attended: {{ $participationOverview['attended'] }},
                invited:  {{ $participationOverview['invited'] }},
                leads:    {{ $participationOverview['leads'] }},
            },
            by_type:   @json($participationByType->map(fn($r)=>['label'=>$r->label,'cnt'=>$r->cnt])),
            by_cop:    @json($participationByCop->map(fn($r)=>['label'=>$r->label,'cnt'=>$r->cnt])),
            by_gender: @json($participationByGender->map(fn($r)=>['label'=>$r->label,'cnt'=>$r->cnt])),
            by_sector: [],
        });
        return;
    }

    spin.style.display = 'inline';
    try {
        const res  = await fetch(`${parBaseUrl}/${actId}`);
        const data = await res.json();
        spin.style.display = 'none';

        // Show banner
        document.getElementById('parActivityTitle').textContent = data.activity.title;
        document.getElementById('parActivityDate').textContent  = data.activity.date
            ? '· ' + new Date(data.activity.date).toLocaleDateString() : '';
        banner.style.display = 'block';

        renderPar(data);
    } catch(e) {
        spin.style.display = 'none';
        console.error('Participation data fetch failed', e);
    }
});

// ═══════════════════════════════════════════════════════
// FINANCIALS
// ═══════════════════════════════════════════════════════
donutFmt('fin-type-donut',  @json($ftLabels), @json($ftAmts), null, fmtD);
barV('fin-status-bar',      @json($fsLabels), @json($fsAmts), null, fmtD);
line('fin-monthly-line',    @json($fmLbls), @json($fmAmts), '#ec4899', fmtD);
donutFmt('fin-omt-donut',   @json($omtLbls), @json($omtVals), null, fmtD);
barH('fin-omt-bar',         @json($omtLbls), @json($omtVals));
donutFmt('fin-med-disease-donut', @json($mdLabels), @json($mdAmts), null, fmtD);
barH('fin-med-disease-bar',       @json($mdLabels), @json($mdAmts));
donutFmt('fin-hosp-donut',        @json($hoLabels), @json($hoAmts), null, fmtD);
barH('fin-hosp-bar',              @json($hoLabels), @json($hoAmts));
donutFmt('fin-edu-level-donut',   @json($elLabels), @json($elAmts), null, fmtD);
barH('fin-edu-inst-bar',          @json($eiLabels), @json($eiAmts));

// ═══════════════════════════════════════════════════════
// PROGRAMS & PROJECTS
// ═══════════════════════════════════════════════════════
donut('pg-type-donut', @json($pgLabels), @json($pgCnts));
barV('pg-type-bar',    @json($pgLabels), @json($pgCnts));
barH('pg-projects-bar',@json($prLabels), @json($prCnts));

// ═══════════════════════════════════════════════════════
// COPs & PORTFOLIOS
// ═══════════════════════════════════════════════════════
barV('cop-act-bar',  @json($copLabels), @json($copActs));
barV('cop-part-bar', @json($copLabels), @json($copParts), ['#0ea5e9']);
donut('pf-donut',    @json($pfLabels), @json($pfCnts));
barH('pf-bar',       @json($pfLabels), @json($pfCnts));
</script>
@endsection
