@extends('layouts.app')

@section('title', 'Dashboard — Hariri Foundation')

@section('styles')
<style>
/* ─── Global ─── */
.dash-pg { display:flex; flex-direction:column; gap:1.1rem; }

/* ─── Welcome Bar ─── */
.dash-wb {
    background:linear-gradient(120deg,#1a237e 0%,#1565c0 55%,#0288d1 100%);
    border-radius:14px; padding:.95rem 1.5rem;
    color:#fff; display:flex; align-items:center; justify-content:space-between; gap:1rem;
    overflow:hidden; position:relative;
}
.dash-wb::before {
    content:''; position:absolute; right:-50px; top:-70px;
    width:210px; height:210px; border-radius:50%;
    background:rgba(255,255,255,.05); pointer-events:none;
}
.wb-av {
    width:40px; height:40px; border-radius:50%;
    background:rgba(255,255,255,.15); border:1.5px solid rgba(255,255,255,.22);
    display:flex; align-items:center; justify-content:center; font-size:1.05rem; flex-shrink:0;
}
.wb-name { font-size:.95rem; font-weight:700; line-height:1.25; }
.wb-meta { font-size:.7rem; opacity:.72; display:flex; align-items:center; gap:.4rem; flex-wrap:wrap; margin-top:.12rem; }
.wb-super {
    display:inline-flex; align-items:center; gap:.2rem;
    background:linear-gradient(135deg,#f59e0b,#fcd34d);
    color:#78350f; font-size:.58rem; font-weight:700;
    padding:.12rem .48rem; border-radius:20px; letter-spacing:.04em; margin-left:.35rem;
}
.wb-clock { text-align:right; flex-shrink:0; }
.wb-clock-v { font-size:1.05rem; font-weight:800; line-height:1; }
.wb-clock-l { font-size:.58rem; opacity:.55; text-transform:uppercase; letter-spacing:.06em; }

/* ─── KPI Card ─── */
.kc {
    background:#fff; border:1px solid #f0f0f5; border-radius:14px;
    padding:1.4rem 1.5rem 1.1rem; height:100%;
    box-shadow:0 1px 3px rgba(0,0,0,.05),0 4px 16px rgba(0,0,0,.04);
    position:relative; overflow:hidden;
    transition:box-shadow .2s, transform .2s;
}
.kc:hover { box-shadow:0 6px 24px rgba(0,0,0,.1); transform:translateY(-2px); }
.kc-featured {
    background:linear-gradient(135deg,#0ea5e9 0%,#1d4ed8 100%);
    border-color:transparent; color:#fff;
}
.kc-lbl {
    font-size:.65rem; font-weight:700; text-transform:uppercase;
    letter-spacing:.09em; color:#9ca3af;
}
.kc-featured .kc-lbl { color:rgba(255,255,255,.72); }
.kc-val {
    font-size:3rem; font-weight:900; line-height:1.05;
    margin:.3rem 0 .22rem; letter-spacing:-.03em;
}
.kc-blue  .kc-val { color:#1d4ed8; }
.kc-green .kc-val { color:#16a34a; }
.kc-featured .kc-val { color:#fff; }
.kc-sub { font-size:.7rem; color:#9ca3af; display:flex; align-items:center; gap:.3rem; margin-top:.1rem; }
.kc-featured .kc-sub { color:rgba(255,255,255,.65); }
.kc-ico {
    position:absolute; right:1.2rem; top:1.1rem;
    width:42px; height:42px; border-radius:11px;
    display:flex; align-items:center; justify-content:center; font-size:1.15rem;
}
.kc-blue  .kc-ico { background:#eff6ff; color:#1d4ed8; }
.kc-green .kc-ico { background:#f0fdf4; color:#15803d; }
.kc-featured .kc-ico { background:rgba(255,255,255,.18); color:#fff; }

/* ─── Chart Card ─── */
.cc {
    background:#fff; border:1px solid #f0f0f5; border-radius:14px;
    padding:1.2rem 1.35rem; height:100%;
    box-shadow:0 1px 3px rgba(0,0,0,.05),0 4px 16px rgba(0,0,0,.04);
}
.cc-head {
    display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;
}
.cc-title { font-size:.82rem; font-weight:700; color:#111827; }
.cc-dots { color:#d1d5db; font-size:.9rem; letter-spacing:.08em; cursor:default; }
.cc-legend { display:flex; flex-wrap:wrap; gap:.4rem .8rem; margin-bottom:.65rem; }
.cc-leg-item { display:flex; align-items:center; gap:.3rem; font-size:.68rem; color:#6b7280; }
.cc-leg-dot { width:9px; height:9px; border-radius:3px; flex-shrink:0; }

/* ─── Module Nav List ─── */
.mn-item {
    display:flex; align-items:center; gap:.65rem;
    padding:.5rem .7rem; border-radius:9px;
    background:#f9fafb; border:1px solid #f0f0f5;
    text-decoration:none; color:inherit;
    transition:background .15s, box-shadow .15s, transform .15s;
}
.mn-item:hover { background:#fff; box-shadow:0 2px 10px rgba(0,0,0,.08); transform:translateY(-1px); color:inherit; }
.mn-ico {
    width:28px; height:28px; border-radius:7px;
    display:flex; align-items:center; justify-content:center; font-size:.78rem; flex-shrink:0;
}
.mn-info { flex:1; min-width:0; }
.mn-lbl { font-size:.76rem; font-weight:700; color:#1f2937; line-height:1.2; }
.mn-cnt { font-size:.67rem; color:#9ca3af; }
.mn-arr { color:#d1d5db; font-size:.7rem; flex-shrink:0; }

/* ─── Colour helpers ─── */
.ic-blue   { background:#eff6ff;  color:#1d4ed8; }
.ic-indigo { background:#eef2ff;  color:#4338ca; }
.ic-violet { background:#f5f3ff;  color:#6d28d9; }
.ic-green  { background:#f0fdf4;  color:#15803d; }
.ic-teal   { background:#f0fdfa;  color:#0f766e; }
.ic-cyan   { background:#ecfeff;  color:#0e7490; }
.ic-orange { background:#fff7ed;  color:#c2410c; }
.ic-amber  { background:#fffbeb;  color:#b45309; }
.ic-rose   { background:#fff1f2;  color:#be123c; }
.ic-red    { background:#fef2f2;  color:#b91c1c; }
.ic-slate  { background:#f8fafc;  color:#475569; }

/* ─── Buttons ─── */
.btn-dash-ghost {
    font-size:.7rem; font-weight:600; padding:.25rem .7rem;
    background:#f9fafb; border:1px solid #e5e7eb; color:#374151;
    border-radius:7px; text-decoration:none;
    display:inline-flex; align-items:center; gap:.25rem;
    transition:background .15s;
}
.btn-dash-ghost:hover { background:#f3f4f6; color:#111; }
.btn-dash-primary {
    font-size:.7rem; font-weight:600; padding:.25rem .7rem;
    background:#0ea5e9; border:none; color:#fff;
    border-radius:7px; text-decoration:none;
    display:inline-flex; align-items:center; gap:.25rem;
    transition:background .15s;
}
.btn-dash-primary:hover { background:#0284c7; color:#fff; }

/* ─── No access ─── */
.dash-na {
    background:#fff; border:1px solid #f0f0f5; border-radius:14px;
    padding:3rem; text-align:center;
    box-shadow:0 1px 3px rgba(0,0,0,.05);
}

@media(max-width:575px){ .wb-clock { display:none!important; } }
</style>
@endsection

@section('content')
@php
/* ── Build chart data ── */
$donutLabels = []; $donutValues = []; $donutColors = [];
$barLabels   = []; $barValues   = []; $barColors   = [];

if ($can['users'] && !is_null($counts['users'])) {
    $donutLabels[]='Users';         $donutValues[]=(int)$counts['users'];         $donutColors[]='#3b82f6';
    $barLabels[]='Users';           $barValues[]=(int)$counts['users'];           $barColors[]='#3b82f6';
}
if ($can['employees'] && !is_null($counts['employees'])) {
    $donutLabels[]='Employees';     $donutValues[]=(int)$counts['employees'];      $donutColors[]='#6366f1';
    $barLabels[]='Employees';       $barValues[]=(int)$counts['employees'];        $barColors[]='#6366f1';
}
if ($can['programs'] && !is_null($counts['programs'])) {
    $donutLabels[]='Programs';      $donutValues[]=(int)$counts['programs'];       $donutColors[]='#8b5cf6';
    $barLabels[]='Programs';        $barValues[]=(int)$counts['programs'];         $barColors[]='#8b5cf6';
}
if ($can['projects'] && !is_null($counts['projects'])) {
    $donutLabels[]='Projects';      $donutValues[]=(int)$counts['projects'];       $donutColors[]='#f97316';
    $barLabels[]='Projects';        $barValues[]=(int)$counts['projects'];         $barColors[]='#f97316';
}
if ($can['portfolios'] && !is_null($counts['portfolios'])) {
    $donutLabels[]='Portfolios';    $donutValues[]=(int)$counts['portfolios'];     $donutColors[]='#14b8a6';
    $barLabels[]='Portfolios';      $barValues[]=(int)$counts['portfolios'];       $barColors[]='#14b8a6';
}
if ($can['cops'] && !is_null($counts['cops'])) {
    $donutLabels[]='COPs';          $donutValues[]=(int)$counts['cops'];           $donutColors[]='#06b6d4';
    $barLabels[]='COPs';            $barValues[]=(int)$counts['cops'];             $barColors[]='#06b6d4';
}
if ($can['activities'] && !is_null($counts['activities'])) {
    $donutLabels[]='Activities';    $donutValues[]=(int)$counts['activities'];     $donutColors[]='#22c55e';
    $barLabels[]='Activities';      $barValues[]=(int)$counts['activities'];       $barColors[]='#22c55e';
}
if ($can['activityUsers'] && !is_null($counts['activityUsers'])) {
    $donutLabels[]='Act. Users';    $donutValues[]=(int)$counts['activityUsers'];  $donutColors[]='#64748b';
    $barLabels[]='Act. Users';      $barValues[]=(int)$counts['activityUsers'];    $barColors[]='#64748b';
}
if ($can['actionPlans'] && !is_null($counts['actionPlans'])) {
    $donutLabels[]='Action Plans';  $donutValues[]=(int)$counts['actionPlans'];    $donutColors[]='#a855f7';
    $barLabels[]='Action Plans';    $barValues[]=(int)$counts['actionPlans'];      $barColors[]='#a855f7';
}

$donutTotal = array_sum($donutValues);

/* ── KPI cards ── */
$kpi1 = null; $kpi2 = null; $kpi3 = null;

if ($can['users'])
    $kpi1 = ['lbl'=>'User Directory',    'val'=>(int)$counts['users'],        'sub'=>'Registered users',    'ico'=>'bi-people-fill',         'cls'=>'kc-blue'];
elseif ($can['employees'])
    $kpi1 = ['lbl'=>'Employees',         'val'=>(int)$counts['employees'],    'sub'=>'Staff members',       'ico'=>'bi-person-badge-fill',   'cls'=>'kc-blue'];
elseif ($can['programs'])
    $kpi1 = ['lbl'=>'Programs',          'val'=>(int)$counts['programs'],     'sub'=>'Total programs',      'ico'=>'bi-diagram-3-fill',      'cls'=>'kc-blue'];

if ($can['activities'])
    $kpi2 = ['lbl'=>'Activities',        'val'=>(int)$counts['activities'],   'sub'=>'All activities',      'ico'=>'bi-calendar-event-fill', 'cls'=>'kc-green'];
elseif ($can['activityUsers'])
    $kpi2 = ['lbl'=>'Activity Users',    'val'=>(int)$counts['activityUsers'],'sub'=>'Participant records', 'ico'=>'bi-person-check-fill',   'cls'=>'kc-green'];
elseif ($can['projects'])
    $kpi2 = ['lbl'=>'Projects',          'val'=>(int)$counts['projects'],     'sub'=>'Total projects',      'ico'=>'bi-kanban-fill',         'cls'=>'kc-green'];

if ($can['financials'])
    $kpi3 = ['lbl'=>'Financial Records', 'val'=>(int)$counts['financials'],   'sub'=>'OMT + Medical',       'ico'=>'bi-coin',                'cls'=>'kc-featured'];
elseif ($can['roles'])
    $kpi3 = ['lbl'=>'Roles',             'val'=>(int)$counts['roles'],        'sub'=>'System roles',        'ico'=>'bi-shield-shaded',       'cls'=>'kc-featured'];
elseif ($can['employees'])
    $kpi3 = ['lbl'=>'Employees',         'val'=>(int)$counts['employees'],    'sub'=>'Staff members',       'ico'=>'bi-person-badge-fill',   'cls'=>'kc-featured'];

$anyVisible = $kpi1 || $kpi2 || $kpi3 || count($donutValues) > 0;
@endphp

<div class="dash-pg">

{{-- ── Welcome Bar ── --}}
<div class="dash-wb">
    <div class="d-flex align-items-center gap-3">
        <div class="wb-av"><i class="bi bi-person-fill"></i></div>
        <div>
            <div class="wb-name">
                {{ $employee->first_name }} {{ $employee->last_name }}
                @if($hasFullAccess)<span class="wb-super"><i class="bi bi-stars"></i> Super Admin</span>@endif
            </div>
            <div class="wb-meta">
                <i class="bi bi-shield-check"></i> {{ $employee->role->role_name ?? 'No Role' }}
                <span style="opacity:.3">·</span>
                <i class="bi bi-calendar3"></i> {{ now()->format('D, d M Y') }}
            </div>
        </div>
    </div>
    <div class="wb-clock">
        <div class="wb-clock-v" id="dash-clock">{{ now()->format('H:i') }}</div>
        <div class="wb-clock-l">live</div>
    </div>
</div>

@if(!$anyVisible)
<div class="dash-na">
    <i class="bi bi-lock-fill fs-4 text-warning mb-2 d-block"></i>
    <p class="fw-bold mb-0" style="font-size:.85rem;">No module access assigned.</p>
    <p class="text-muted mb-0" style="font-size:.78rem;">Contact your administrator to request permissions.</p>
</div>
@else

{{-- ── KPI Row ── --}}
@if($kpi1 || $kpi2 || $kpi3)
<div class="row g-3">
    @foreach(array_filter([$kpi1, $kpi2, $kpi3]) as $k)
    <div class="col-sm-6 col-lg-4">
        <div class="kc {{ $k['cls'] }}">
            <div class="kc-ico"><i class="bi {{ $k['ico'] }}"></i></div>
            <div class="kc-lbl">{{ $k['lbl'] }}</div>
            <div class="kc-val">{{ number_format($k['val']) }}</div>
            <div class="kc-sub"><i class="bi bi-dot" style="font-size:.95rem;margin:-2px;"></i> {{ $k['sub'] }}</div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- ── Charts Row: Donut + Horizontal Bars ── --}}
@if(count($donutValues) > 0)
<div class="row g-3">

    {{-- Donut ── --}}
    <div class="col-lg-5">
        <div class="cc">
            <div class="cc-head">
                <span class="cc-title">System Overview</span>
                <span class="cc-dots">···</span>
            </div>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <div style="position:relative;width:150px;height:150px;flex-shrink:0;margin:0 auto;">
                    <canvas id="donutChart" width="150" height="150"></canvas>
                    <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;pointer-events:none;">
                        <span style="font-size:1.7rem;font-weight:900;color:#111;line-height:1;letter-spacing:-.03em;">{{ number_format($donutTotal) }}</span>
                        <span style="font-size:.6rem;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;">Total</span>
                    </div>
                </div>
                <div style="flex:1;min-width:110px;">
                    @foreach($donutLabels as $i => $lbl)
                    <div class="d-flex align-items-center justify-content-between mb-1" style="gap:.5rem;">
                        <div class="d-flex align-items-center gap-1">
                            <span style="width:9px;height:9px;border-radius:3px;background:{{ $donutColors[$i] }};display:inline-block;flex-shrink:0;"></span>
                            <span style="font-size:.7rem;color:#374151;">{{ $lbl }}</span>
                        </div>
                        <span style="font-size:.7rem;font-weight:700;color:#111827;">{{ number_format($donutValues[$i]) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Horizontal Bar ── --}}
    <div class="col-lg-7">
        <div class="cc">
            <div class="cc-head">
                <span class="cc-title">Record Distribution</span>
                <span class="cc-dots">···</span>
            </div>
            <div style="height:{{ min(count($barLabels) * 30 + 20, 260) }}px;">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>

</div>
@endif

{{-- ── Bottom Row: Financial Chart + Quick Navigation ── --}}
<div class="row g-3">

    @if($can['financials'])
    <div class="col-lg-6">
        <div class="cc">
            <div class="cc-head">
                <span class="cc-title">Financial Breakdown</span>
                <span class="cc-dots">···</span>
            </div>
            <div class="cc-legend">
                <div class="cc-leg-item">
                    <span class="cc-leg-dot" style="background:#f59e0b;"></span>
                    OMT — {{ number_format((int)($counts['omt'] ?? 0)) }}
                </div>
                <div class="cc-leg-item">
                    <span class="cc-leg-dot" style="background:#f43f5e;"></span>
                    Medical — {{ number_format((int)($counts['medical'] ?? 0)) }}
                </div>
            </div>
            <div style="height:175px;">
                <canvas id="finChart"></canvas>
            </div>
            <div class="d-flex gap-2 mt-3 flex-wrap">
                <a href="{{ route('financials.index') }}" class="btn-dash-ghost"><i class="bi bi-list-ul"></i> View Records</a>
                <a href="{{ route('financials.visualization') }}" class="btn-dash-ghost"><i class="bi bi-bar-chart"></i> Charts</a>
                @if($can['financialsCreate'])
                <a href="{{ route('financials.import.form') }}" class="btn-dash-primary"><i class="bi bi-upload"></i> Import</a>
                @endif
            </div>
        </div>
    </div>
    @endif

    <div class="{{ $can['financials'] ? 'col-lg-6' : 'col-12' }}">
        <div class="cc">
            <div class="cc-head">
                <span class="cc-title">Quick Navigation</span>
                <span class="cc-dots">···</span>
            </div>
            <div class="d-flex flex-column gap-2">

                @if($can['users'])
                <a href="{{ route('users.index') }}" class="mn-item">
                    <div class="mn-ico ic-blue"><i class="bi bi-people-fill"></i></div>
                    <div class="mn-info">
                        <div class="mn-lbl">User Directory</div>
                        <div class="mn-cnt">{{ number_format((int)$counts['users']) }} users</div>
                    </div>
                    <i class="bi bi-chevron-right mn-arr"></i>
                </a>
                @endif

                @if($can['employees'])
                <a href="{{ route('employees.index') }}" class="mn-item">
                    <div class="mn-ico ic-indigo"><i class="bi bi-person-badge-fill"></i></div>
                    <div class="mn-info">
                        <div class="mn-lbl">Employees</div>
                        <div class="mn-cnt">{{ number_format((int)$counts['employees']) }} staff</div>
                    </div>
                    <i class="bi bi-chevron-right mn-arr"></i>
                </a>
                @endif

                @if($can['programs'])
                <a href="{{ route('programs.index') }}" class="mn-item">
                    <div class="mn-ico ic-violet"><i class="bi bi-diagram-3-fill"></i></div>
                    <div class="mn-info">
                        <div class="mn-lbl">Programs</div>
                        <div class="mn-cnt">{{ number_format((int)$counts['programs']) }} total</div>
                    </div>
                    <i class="bi bi-chevron-right mn-arr"></i>
                </a>
                @endif

                @if($can['projects'])
                <a href="{{ route('projects.index') }}" class="mn-item">
                    <div class="mn-ico ic-orange"><i class="bi bi-kanban-fill"></i></div>
                    <div class="mn-info">
                        <div class="mn-lbl">Projects</div>
                        <div class="mn-cnt">{{ number_format((int)$counts['projects']) }} total</div>
                    </div>
                    <i class="bi bi-chevron-right mn-arr"></i>
                </a>
                @endif

                @if($can['portfolios'])
                <a href="{{ route('portfolios.index') }}" class="mn-item">
                    <div class="mn-ico ic-teal"><i class="bi bi-briefcase-fill"></i></div>
                    <div class="mn-info">
                        <div class="mn-lbl">Portfolios</div>
                        <div class="mn-cnt">{{ number_format((int)$counts['portfolios']) }} total</div>
                    </div>
                    <i class="bi bi-chevron-right mn-arr"></i>
                </a>
                @endif

                @if($can['cops'])
                <a href="{{ route('cops.index') }}" class="mn-item">
                    <div class="mn-ico ic-cyan"><i class="bi bi-people-fill"></i></div>
                    <div class="mn-info">
                        <div class="mn-lbl">Communities of Practice</div>
                        <div class="mn-cnt">{{ number_format((int)$counts['cops']) }} total</div>
                    </div>
                    <i class="bi bi-chevron-right mn-arr"></i>
                </a>
                @endif

                @if($can['activities'])
                <a href="{{ route('activities.index') }}" class="mn-item">
                    <div class="mn-ico ic-green"><i class="bi bi-calendar-event-fill"></i></div>
                    <div class="mn-info">
                        <div class="mn-lbl">Activities</div>
                        <div class="mn-cnt">{{ number_format((int)$counts['activities']) }} total</div>
                    </div>
                    <i class="bi bi-chevron-right mn-arr"></i>
                </a>
                @endif

                @if($can['activityUsers'])
                <a href="{{ route('activity-users.index') }}" class="mn-item">
                    <div class="mn-ico ic-slate"><i class="bi bi-person-check-fill"></i></div>
                    <div class="mn-info">
                        <div class="mn-lbl">Activity Users</div>
                        <div class="mn-cnt">{{ number_format((int)$counts['activityUsers']) }} records</div>
                    </div>
                    <i class="bi bi-chevron-right mn-arr"></i>
                </a>
                @endif

                @if($can['roles'])
                <a href="{{ route('roles.index') }}" class="mn-item">
                    <div class="mn-ico ic-red"><i class="bi bi-shield-shaded"></i></div>
                    <div class="mn-info">
                        <div class="mn-lbl">Roles & Permissions</div>
                        <div class="mn-cnt">{{ number_format((int)$counts['roles']) }} roles</div>
                    </div>
                    <i class="bi bi-chevron-right mn-arr"></i>
                </a>
                @endif

                @if($can['actionPlans'])
                <a href="{{ route('action-plans.index') }}" class="mn-item">
                    <div class="mn-ico ic-slate"><i class="bi bi-journal-check"></i></div>
                    <div class="mn-info">
                        <div class="mn-lbl">Action Plans</div>
                        <div class="mn-cnt">{{ number_format((int)$counts['actionPlans']) }} plans</div>
                    </div>
                    <i class="bi bi-chevron-right mn-arr"></i>
                </a>
                @endif

            </div>
        </div>
    </div>

</div>

@endif {{-- end anyVisible --}}
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
<script>
// Live clock
(function tick() {
    var el = document.getElementById('dash-clock');
    if (el) {
        var n = new Date();
        el.textContent = n.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
    }
    setTimeout(tick, 1000);
})();

Chart.defaults.font.family = 'inherit';
Chart.defaults.color = '#9ca3af';

// ── Donut ──
var donutEl = document.getElementById('donutChart');
if (donutEl) {
    new Chart(donutEl, {
        type: 'doughnut',
        data: {
            labels: @json($donutLabels),
            datasets: [{
                data: @json($donutValues),
                backgroundColor: @json($donutColors),
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 5,
            }]
        },
        options: {
            cutout: '74%',
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            var total = ctx.dataset.data.reduce(function(a,b){ return a+b; }, 0);
                            var pct   = total > 0 ? Math.round(ctx.parsed / total * 100) : 0;
                            return ' ' + ctx.label + ': ' + ctx.parsed.toLocaleString() + ' (' + pct + '%)';
                        }
                    }
                }
            }
        }
    });
}

// ── Horizontal Bar ──
var barEl = document.getElementById('barChart');
if (barEl) {
    var bLabels = @json($barLabels);
    var bValues = @json($barValues);
    var bColors = @json($barColors);
    var maxVal  = Math.max.apply(null, bValues);
    new Chart(barEl, {
        type: 'bar',
        plugins: [ChartDataLabels],
        data: {
            labels: bLabels,
            datasets: [{
                data: bValues,
                backgroundColor: bColors.map(function(c) { return c + '28'; }),
                borderColor: bColors,
                borderWidth: 1.5,
                borderRadius: 5,
                borderSkipped: false,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false },
                datalabels: {
                    anchor: 'end',
                    align: 'end',
                    formatter: function(value) { return value.toLocaleString(); },
                    color: function(ctx) { return bColors[ctx.dataIndex]; },
                    font: { size: 11, weight: '700' },
                    padding: { left: 4 },
                }
            },
            scales: {
                x: {
                    grid: { color: '#f5f5f7' },
                    ticks: { font: { size: 10 } },
                    max: Math.ceil(maxVal * 1.22),
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { size: 11, weight: '600' }, color: '#374151' }
                }
            }
        }
    });
}

// ── Financial Bar ──
var finEl = document.getElementById('finChart');
if (finEl) {
    new Chart(finEl, {
        type: 'bar',
        data: {
            labels: ['OMT', 'Medical'],
            datasets: [{
                label: 'Records',
                data: [@json((int)($counts['omt'] ?? 0)), @json((int)($counts['medical'] ?? 0))],
                backgroundColor: ['#f59e0b28', '#f43f5e28'],
                borderColor: ['#f59e0b', '#f43f5e'],
                borderWidth: 1.5,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: { label: function(ctx) { return ' ' + ctx.parsed.y.toLocaleString() + ' records'; } }
                }
            },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11, weight: '600' }, color: '#374151' } },
                y: { grid: { color: '#f5f5f7' }, ticks: { font: { size: 10 } } }
            }
        }
    });
}
</script>
@endsection
