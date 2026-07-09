@extends('layouts.app')

@section('title', 'Financial Visualization')

@section('styles')
<style>
:root {
    --c-omt:       #f59e0b;
    --c-medical:   #ec4899;
    --c-paid:      #22c55e;
    --c-pending:   #f59e0b;
    --c-partial:   #3b82f6;
    --c-overdue:   #ef4444;
    --c-primary:   #2563eb;
    --radius:      12px;
}

.viz-page { padding: 1.5rem; background:#f1f5f9; min-height:100vh; }

/* ── Page selector ─────────────────────────────────────── */
.page-selector {
    display:flex; gap:.6rem; flex-wrap:wrap; margin-bottom:1.5rem;
}
.ps-btn {
    padding:.45rem 1.1rem; border-radius:999px; font-size:.82rem;
    font-weight:600; cursor:pointer; border:2px solid transparent;
    transition:all .2s; text-decoration:none; color:#64748b;
    background:#fff; box-shadow:0 1px 3px rgba(0,0,0,.08);
}
.ps-btn:hover  { border-color:var(--c-primary); color:var(--c-primary); }
.ps-btn.active { background:var(--c-primary); color:#fff; border-color:var(--c-primary); }
.ps-btn.omt.active      { background:var(--c-omt);    border-color:var(--c-omt);    color:#fff; }
.ps-btn.medicine.active { background:#3b82f6;          border-color:#3b82f6;         color:#fff; }
.ps-btn.hospital.active { background:#ef4444;          border-color:#ef4444;         color:#fff; }

/* ── KPI cards ─────────────────────────────────────────── */
.kpi-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:1rem; margin-bottom:1.5rem; }
.kpi-card {
    background:#fff; border-radius:var(--radius); padding:1rem 1.2rem;
    box-shadow:0 1px 4px rgba(0,0,0,.08);
    border-top:4px solid var(--c-primary);
}
.kpi-card .label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#64748b; }
.kpi-card .value { font-size:1.5rem; font-weight:800; color:#1e293b; margin:.25rem 0 .1rem; }
.kpi-card .sub   { font-size:.75rem; color:#94a3b8; }

/* ── Chart panels ──────────────────────────────────────── */
.charts-grid { display:grid; gap:1.2rem; grid-template-columns:repeat(auto-fill,minmax(360px,1fr)); }
.chart-panel {
    background:#fff; border-radius:var(--radius); padding:1.2rem 1.4rem;
    box-shadow:0 1px 4px rgba(0,0,0,.08);
}
.chart-panel.full { grid-column:1/-1; }
.chart-title { font-size:.88rem; font-weight:700; color:#334155; margin-bottom:1rem; display:flex; align-items:center; gap:.4rem; }
.chart-title i { color:var(--c-primary); }

/* ── Section header ────────────────────────────────────── */
.section-badge {
    display:inline-flex; align-items:center; gap:.5rem;
    background:var(--c-primary); color:#fff; border-radius:8px;
    padding:.35rem .9rem; font-size:.8rem; font-weight:700;
    margin-bottom:1.2rem; text-transform:uppercase; letter-spacing:.04em;
}

.sub-section-title {
    font-size:.82rem; font-weight:700; color:#64748b; text-transform:uppercase;
    letter-spacing:.05em; margin:1.5rem 0 .75rem; border-left:3px solid var(--c-primary);
    padding-left:.6rem;
}

/* hidden pages */
.viz-section { display:none; }
.viz-section.active { display:block; }

/* back link */
.back-link { color:var(--c-primary); text-decoration:none; font-size:.85rem; font-weight:600; }
.back-link:hover { text-decoration:underline; }

.empty-note { color:#94a3b8; font-size:.85rem; padding:1rem 0; }
</style>
@endsection

@section('content')
@php
    // ── OMT breakdown ────────────────────────────────────
    $omtLabels = array_map(fn($k) => ucwords(str_replace('_',' ',$k)), array_keys($omtBreakdown));
    $omtVals   = array_values($omtBreakdown);

    // ── OMT per activity ─────────────────────────────────
    $omtActLabels  = $omtByActivity->pluck('activity')->map(fn($a)=>$a??'Unknown')->toArray();
    $omtActTotals  = $omtByActivity->pluck('total')->map(fn($v)=>(float)$v)->toArray();
    $omtActCounts  = $omtByActivity->pluck('cnt')->map(fn($v)=>(int)$v)->toArray();

    // ── Medicine disease ─────────────────────────────────
    $medDiseaseLabels = $medicineByDisease->pluck('disease')->map(fn($d)=>$d??'Unknown')->toArray();
    $medDiseaseTotals = $medicineByDisease->pluck('total')->map(fn($v)=>(float)$v)->toArray();

    // ── Medicine cost JSONB fields ────────────────────────
    $medCostLabels = array_map(fn($k) => ucwords(str_replace('_',' ',$k)), array_keys($medicineCostBreakdown));
    $medCostVals   = array_values($medicineCostBreakdown);

    // ── Hospital ops ─────────────────────────────────────
    $hospOpLabels = $hospitalByOperation->pluck('op')->map(fn($d)=>$d??'Unknown')->toArray();
    $hospOpTotals = $hospitalByOperation->pluck('total')->map(fn($v)=>(float)$v)->toArray();

    // ── Hospital cost JSONB fields ────────────────────────
    $hospCostLabels = array_map(fn($k) => ucwords(str_replace('_',' ',$k)), array_keys($hospitalCostBreakdown));
    $hospCostVals   = array_values($hospitalCostBreakdown);
@endphp

<div class="viz-page">

    {{-- Header ─────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 style="font-weight:800;color:#1e293b;margin:0;">
                <i class="fas fa-chart-pie me-2" style="color:var(--c-primary);"></i>Financial Visualization
            </h4>
            <p style="color:#64748b;font-size:.85rem;margin:.25rem 0 0;">Analytics dashboard</p>
        </div>
        <a href="{{ route('financials.index') }}" class="back-link">
            <i class="fas fa-arrow-left me-1"></i> Back to Financials
        </a>
    </div>

    {{-- Page Selector ──────────────────────────────────── --}}
    <div class="page-selector">
        <button class="ps-btn omt active"  data-target="omt">
            <i class="fas fa-dollar-sign me-1"></i> OMT
        </button>
        <button class="ps-btn medicine"    data-target="medicine">
            <i class="fas fa-pills me-1"></i> Medical – Medicine
        </button>
        <button class="ps-btn hospital"    data-target="hospital">
            <i class="fas fa-hospital me-1"></i> Medical – Hospital
        </button>
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- SECTION: OMT ─────────────────────────────────────────  --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="viz-section active" id="section-omt">

        <div class="section-badge" style="background:#b45309;"><i class="fas fa-dollar-sign"></i> OMT Financials</div>

        <div class="kpi-grid">
            <div class="kpi-card" style="border-color:#b45309;">
                <div class="label">OMT Total</div>
                <div class="value">${{ number_format($kpis['omt_total'],2) }}</div>
                <div class="sub">{{ $byType->get('omt')->cnt ?? 0 }} records</div>
            </div>
            @forelse(array_slice($omtBreakdown, 0, 5, true) as $field => $val)
            <div class="kpi-card" style="border-color:#f59e0b;">
                <div class="label">{{ ucwords(str_replace('_',' ',$field)) }}</div>
                <div class="value">${{ number_format($val,2) }}</div>
            </div>
            @empty
            @endforelse
        </div>

        @if(count($omtLabels) > 0)
        <div class="charts-grid">
            <div class="chart-panel">
                <div class="chart-title"><i class="fas fa-chart-pie"></i> Cost Category Breakdown</div>
                <canvas id="chartOmtDonut" height="220"></canvas>
            </div>
            <div class="chart-panel">
                <div class="chart-title"><i class="fas fa-chart-bar"></i> Cost Categories (Bar)</div>
                <canvas id="chartOmtBar" height="220"></canvas>
            </div>
        </div>
        @else
        <p class="empty-note"><i class="fas fa-info-circle me-1"></i>No OMT financial data available yet.</p>
        @endif

        @if(count($omtActLabels) > 0)
        <div class="sub-section-title">Amount Sent per Activity</div>
        <div class="charts-grid">
            <div class="chart-panel full">
                <div class="chart-title"><i class="fas fa-chart-bar"></i> Total Sent per Activity</div>
                <canvas id="chartOmtByActivity" height="{{ max(120, count($omtActLabels) * 36) }}"></canvas>
            </div>
        </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- SECTION: MEDICINE ───────────────────────────────────── --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="viz-section" id="section-medicine">

        <div class="section-badge" style="background:#2563eb;"><i class="fas fa-pills"></i> Medical – Medicine</div>

        <div class="kpi-grid">
            <div class="kpi-card" style="border-color:#2563eb;">
                <div class="label">Medicine Records</div>
                <div class="value">{{ $medicineByDisease->sum('cnt') }}</div>
            </div>
            <div class="kpi-card" style="border-color:#2563eb;">
                <div class="label">Medicine Total</div>
                <div class="value">${{ number_format($medicineByDisease->sum('total'),2) }}</div>
            </div>
            <div class="kpi-card" style="border-color:#2563eb;">
                <div class="label">Disease Types</div>
                <div class="value">{{ $medicineByDisease->count() }}</div>
            </div>
            @foreach(array_slice($medicineCostBreakdown, 0, 4, true) as $field => $val)
            <div class="kpi-card" style="border-color:#60a5fa;">
                <div class="label">{{ ucwords(str_replace('_',' ',$field)) }}</div>
                <div class="value">${{ number_format($val,2) }}</div>
            </div>
            @endforeach
        </div>

        @if(count($medDiseaseLabels) > 0)
        <div class="sub-section-title">Breakdown by Disease Type</div>
        <div class="charts-grid">
            <div class="chart-panel">
                <div class="chart-title"><i class="fas fa-chart-pie"></i> Amount by Disease Type</div>
                <canvas id="chartMedDiseaseDonut" height="220"></canvas>
            </div>
            <div class="chart-panel">
                <div class="chart-title"><i class="fas fa-chart-bar"></i> Top Diseases by Amount</div>
                <canvas id="chartMedDiseaseBar" height="220"></canvas>
            </div>
        </div>
        @else
        <p class="empty-note"><i class="fas fa-info-circle me-1"></i>No disease type data available.</p>
        @endif

        @if(count($medCostLabels) > 0)
        <div class="sub-section-title">Breakdown by JSONB Cost Fields</div>
        <div class="charts-grid">
            <div class="chart-panel">
                <div class="chart-title"><i class="fas fa-chart-pie"></i> Cost Field Distribution</div>
                <canvas id="chartMedCostDonut" height="220"></canvas>
            </div>
            <div class="chart-panel">
                <div class="chart-title"><i class="fas fa-chart-bar"></i> Cost Fields Comparison</div>
                <canvas id="chartMedCostBar" height="220"></canvas>
            </div>
        </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════ --}}
    {{-- SECTION: HOSPITAL ───────────────────────────────────── --}}
    {{-- ═══════════════════════════════════════════════════════ --}}
    <div class="viz-section" id="section-hospital">

        <div class="section-badge" style="background:#dc2626;"><i class="fas fa-hospital"></i> Medical – Hospital</div>

        <div class="kpi-grid">
            <div class="kpi-card" style="border-color:#dc2626;">
                <div class="label">Hospital Records</div>
                <div class="value">{{ $hospitalByOperation->sum('cnt') }}</div>
            </div>
            <div class="kpi-card" style="border-color:#dc2626;">
                <div class="label">Hospital Total</div>
                <div class="value">${{ number_format($hospitalByOperation->sum('total'),2) }}</div>
            </div>
            <div class="kpi-card" style="border-color:#dc2626;">
                <div class="label">Operation Types</div>
                <div class="value">{{ $hospitalByOperation->count() }}</div>
            </div>
            @foreach(array_slice($hospitalCostBreakdown, 0, 4, true) as $field => $val)
            <div class="kpi-card" style="border-color:#fca5a5;">
                <div class="label">{{ ucwords(str_replace('_',' ',$field)) }}</div>
                <div class="value">${{ number_format($val,2) }}</div>
            </div>
            @endforeach
        </div>

        @if(count($hospOpLabels) > 0)
        <div class="sub-section-title">Breakdown by Operation Type</div>
        <div class="charts-grid">
            <div class="chart-panel">
                <div class="chart-title"><i class="fas fa-chart-pie"></i> Amount by Operation Type</div>
                <canvas id="chartHospOpDonut" height="220"></canvas>
            </div>
            <div class="chart-panel">
                <div class="chart-title"><i class="fas fa-chart-bar"></i> Top Operations by Amount</div>
                <canvas id="chartHospOpBar" height="220"></canvas>
            </div>
        </div>
        @else
        <p class="empty-note"><i class="fas fa-info-circle me-1"></i>No operation type data available.</p>
        @endif

        @if(count($hospCostLabels) > 0)
        <div class="sub-section-title">Breakdown by JSONB Cost Fields</div>
        <div class="charts-grid">
            <div class="chart-panel">
                <div class="chart-title"><i class="fas fa-chart-pie"></i> Cost Field Distribution</div>
                <canvas id="chartHospCostDonut" height="220"></canvas>
            </div>
            <div class="chart-panel">
                <div class="chart-title"><i class="fas fa-chart-bar"></i> Cost Fields Comparison</div>
                <canvas id="chartHospCostBar" height="220"></canvas>
            </div>
        </div>
        @endif
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function palette(n) {
    const base = ['#2563eb','#ec4899','#10b981','#f59e0b','#6366f1','#ef4444','#0ea5e9','#a855f7','#f97316','#14b8a6'];
    return Array.from({length:n}, (_,i) => base[i % base.length]);
}
const fmt = v => '$' + parseFloat(v).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});

const donutDefaults = { plugins:{ legend:{ position:'bottom', labels:{font:{size:11}} } } };
const barDefaults   = { plugins:{ legend:{ display:false } }, scales:{ y:{ ticks:{ callback: v=>fmt(v) } } } };

document.querySelectorAll('.ps-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.ps-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('.viz-section').forEach(s => s.classList.remove('active'));
        document.getElementById('section-' + btn.dataset.target).classList.add('active');
    });
});

// ── OMT: dynamic JSONB cost breakdown ────────────────────────────────────
@if(count($omtLabels) > 0)
const omtLabels = @json($omtLabels);
const omtVals   = @json($omtVals);
const omtColors = palette(omtLabels.length);
new Chart(document.getElementById('chartOmtDonut'), {
    type:'doughnut',
    data:{ labels:omtLabels, datasets:[{ data:omtVals, backgroundColor:omtColors, hoverOffset:6 }] },
    options:{ ...donutDefaults, plugins:{ ...donutDefaults.plugins, tooltip:{callbacks:{label:ctx=>fmt(ctx.raw)}} } }
});
new Chart(document.getElementById('chartOmtBar'), {
    type:'bar',
    data:{ labels:omtLabels, datasets:[{ data:omtVals, backgroundColor:omtColors, borderRadius:6 }] },
    options:{ ...barDefaults }
});
@endif

// ── OMT: amount sent per activity ─────────────────────────────────────────
@if(count($omtActLabels) > 0)
const omtActLabels = @json($omtActLabels);
const omtActTotals = @json($omtActTotals);
const omtActCounts = @json($omtActCounts);
const omtActColors = palette(omtActLabels.length).map((_,i)=>['#b45309','#f59e0b','#fbbf24','#fcd34d','#fde68a','#fffbeb'][i%6]);
new Chart(document.getElementById('chartOmtByActivity'), {
    type:'bar',
    data:{
        labels: omtActLabels,
        datasets:[{
            label:'Total Amount',
            data: omtActTotals,
            backgroundColor: omtActColors,
            borderRadius: 6
        }]
    },
    options:{
        indexAxis:'y',
        plugins:{
            legend:{ display:false },
            tooltip:{
                callbacks:{
                    label: ctx => fmt(ctx.raw) + '  (' + omtActCounts[ctx.dataIndex] + ' records)'
                }
            }
        },
        scales:{ x:{ ticks:{ callback: v=>fmt(v) } } }
    }
});
@endif

// ── Medicine: disease type breakdown ─────────────────────────────────────
@if(count($medDiseaseLabels) > 0)
const medDLbls = @json($medDiseaseLabels);
const medDVals = @json($medDiseaseTotals);
const medDClrs = palette(medDLbls.length);
new Chart(document.getElementById('chartMedDiseaseDonut'), {
    type:'doughnut',
    data:{ labels:medDLbls, datasets:[{ data:medDVals, backgroundColor:medDClrs, hoverOffset:6 }] },
    options:{ ...donutDefaults, plugins:{ ...donutDefaults.plugins, tooltip:{callbacks:{label:ctx=>fmt(ctx.raw)}} } }
});
new Chart(document.getElementById('chartMedDiseaseBar'), {
    type:'bar',
    data:{ labels:medDLbls, datasets:[{ data:medDVals, backgroundColor:medDClrs, borderRadius:6 }] },
    options:{ ...barDefaults, indexAxis:'y' }
});
@endif

// ── Medicine: JSONB cost fields breakdown ─────────────────────────────────
@if(count($medCostLabels) > 0)
const medCLbls = @json($medCostLabels);
const medCVals = @json($medCostVals);
const medCClrs = palette(medCLbls.length).map((_,i)=>['#2563eb','#3b82f6','#60a5fa','#93c5fd','#bfdbfe','#dbeafe'][i%6]);
new Chart(document.getElementById('chartMedCostDonut'), {
    type:'doughnut',
    data:{ labels:medCLbls, datasets:[{ data:medCVals, backgroundColor:medCClrs, hoverOffset:6 }] },
    options:{ ...donutDefaults, plugins:{ ...donutDefaults.plugins, tooltip:{callbacks:{label:ctx=>fmt(ctx.raw)}} } }
});
new Chart(document.getElementById('chartMedCostBar'), {
    type:'bar',
    data:{ labels:medCLbls, datasets:[{ data:medCVals, backgroundColor:medCClrs, borderRadius:6 }] },
    options:{ ...barDefaults }
});
@endif

// ── Hospital: operation type breakdown ────────────────────────────────────
@if(count($hospOpLabels) > 0)
const hospLbls = @json($hospOpLabels);
const hospVals = @json($hospOpTotals);
const hospClrs = palette(hospLbls.length).map((_,i)=>['#ef4444','#f97316','#dc2626','#b91c1c','#7f1d1d','#fca5a5'][i%6]);
new Chart(document.getElementById('chartHospOpDonut'), {
    type:'doughnut',
    data:{ labels:hospLbls, datasets:[{ data:hospVals, backgroundColor:hospClrs, hoverOffset:6 }] },
    options:{ ...donutDefaults, plugins:{ ...donutDefaults.plugins, tooltip:{callbacks:{label:ctx=>fmt(ctx.raw)}} } }
});
new Chart(document.getElementById('chartHospOpBar'), {
    type:'bar',
    data:{ labels:hospLbls, datasets:[{ data:hospVals, backgroundColor:hospClrs, borderRadius:6 }] },
    options:{ ...barDefaults, indexAxis:'y' }
});
@endif

// ── Hospital: JSONB cost fields breakdown ─────────────────────────────────
@if(count($hospCostLabels) > 0)
const hospCLbls = @json($hospCostLabels);
const hospCVals = @json($hospCostVals);
const hospCClrs = palette(hospCLbls.length).map((_,i)=>['#ef4444','#f97316','#dc2626','#b91c1c','#7f1d1d','#fca5a5'][i%6]);
new Chart(document.getElementById('chartHospCostDonut'), {
    type:'doughnut',
    data:{ labels:hospCLbls, datasets:[{ data:hospCVals, backgroundColor:hospCClrs, hoverOffset:6 }] },
    options:{ ...donutDefaults, plugins:{ ...donutDefaults.plugins, tooltip:{callbacks:{label:ctx=>fmt(ctx.raw)}} } }
});
new Chart(document.getElementById('chartHospCostBar'), {
    type:'bar',
    data:{ labels:hospCLbls, datasets:[{ data:hospCVals, backgroundColor:hospCClrs, borderRadius:6 }] },
    options:{ ...barDefaults }
});
@endif
</script>
@endsection
