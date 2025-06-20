@extends('layouts.admin')
@section('title','Panel Principal')

{{-- ---------- 1) Carga de Chart.js  ---------- --}}
@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush


@section('content')
{{-- ---------- KPI Cards ---------- --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4">
        <div class="card border-success shadow-sm kpi-card">
            <div class="card-body d-flex justify-content-between align-items-center text-success">
                <div>
                    <div class="kpi-value" id="kpiVentas">0</div>
                    <small>Ventas hoy (Bs)</small>
                </div>
                <i class="bi bi-cash-coin fs-2"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-primary shadow-sm kpi-card">
            <div class="card-body d-flex justify-content-between align-items-center text-primary">
                <div>
                    <div class="kpi-value" id="kpiProductos">0</div>
                    <small>Productos</small>
                </div>
                <i class="bi bi-box-seam fs-2"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card border-dark shadow-sm kpi-card">
            <div class="card-body d-flex justify-content-between align-items-center text-dark">
                <div>
                    <div class="kpi-value" id="kpiUsuarios">0</div>
                    <small>Usuarios</small>
                </div>
                <i class="bi bi-people-fill fs-2"></i>
            </div>
        </div>
    </div>
</div>

{{-- ---------- Gráficas ---------- --}}
<div class="row g-4">
    <div class="col-12 col-lg-6">
        <canvas id="ventasChart" height="120"></canvas>
    </div>
    <div class="col-12 col-lg-6">
        <canvas id="topChart" height="120"></canvas>
    </div>
</div>

{{-- Contenedor de error --}}
<div id="dashError" class="alert alert-danger mt-4 d-none"></div>
@endsection



{{-- ---------- 2) Script del dashboard ---------- --}}
@push('js')
<script>
const METRICS_URL = '{{ route('dashboard.metrics') }}';
const KPIS_URL    = '{{ route('dashboard.kpis') }}';

let ventasChart, topChart;

/*--------- Render de KPIs ----------*/
function renderKpis({ ventasHoy = 0, productos = 0, usuarios = 0 }) {
    document.getElementById('kpiVentas').textContent   = (+ventasHoy).toLocaleString('es-BO', {minimumFractionDigits: 2});
    document.getElementById('kpiProductos').textContent= productos;
    document.getElementById('kpiUsuarios').textContent = usuarios;
}

/*--------- Render de gráficas ----------*/
function renderCharts({ salesLast7 = {}, topProducts = [] }) {

    /* --- Line Chart: Ventas últimos 7 días --- */
    const vCtx = document.getElementById('ventasChart');
    const labels = Object.keys(salesLast7);
    const valores = Object.values(salesLast7);

    if (ventasChart) ventasChart.destroy();
    ventasChart = new Chart(vCtx, {
        type: 'line',
        data: { labels, datasets:[{ label:'Ventas (Bs)', data:valores, tension:.3, fill:false }] },
        options:{ responsive:true, scales:{ y:{ beginAtZero:true } } }
    });

    /* --- Bar Chart: Top 5 productos --- */
    const tCtx = document.getElementById('topChart');
    if (topChart) topChart.destroy();
    topChart = new Chart(tCtx, {
        type: 'bar',
        data: {
            labels: topProducts,
            datasets: [{ label:'Top 5 (último mes)', data: Array(topProducts.length).fill(1) }]
        },
        options:{ responsive:true, scales:{ y:{ ticks:{ stepSize:1, precision:0 } } } }
    });
}

/*--------- Cargar datos vía AJAX ----------*/
async function loadDashboard(){
   try {
       const [mRes, kRes] = await Promise.all([fetch(METRICS_URL), fetch(KPIS_URL)]);
       if (!mRes.ok || !kRes.ok) throw new Error(`HTTP ${mRes.status}/${kRes.status}`);

       const metrics = await mRes.json();
       const kpis    = await kRes.json();

       renderCharts(metrics);
       renderKpis(kpis);
       document.getElementById('dashError').classList.add('d-none');

   } catch (e) {
       console.error(e);
       const errBox = document.getElementById('dashError');
       errBox.textContent = 'Error al cargar métricas del dashboard.';
       errBox.classList.remove('d-none');
   }
}

/*--------- Inicial y refresco ----------*/
loadDashboard();
setInterval(loadDashboard, 60000);   // refrescar cada minuto
</script>
@endpush



{{-- ---------- 3) Pequeños estilos ---------- --}}
@push('css')
<style>
    .kpi-card   { min-height:90px }
    .kpi-value  { font-size:1.4rem; font-weight:600 }
</style>
@endpush
