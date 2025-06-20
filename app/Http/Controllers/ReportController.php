<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckPermission as Perm;
use App\Models\{Venta, Compra};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __construct()
    {
       $this->middleware(Perm::class . ':reports.view')->only('index');
       $this->middleware(Perm::class . ':reports.sales')->only('sales');
       $this->middleware(Perm::class . ':reports.purchases')->only('purchases');

    }

    /* ───────── Dashboard de reportes ───────── */
    public function index()
    {
        return view('reportes.index');
    }

    /* ───────── Reporte de Ventas ───────── */
    public function sales(Request $request)
    {
        /* 1)  Validación de fechas (opcional = hoy) */
        $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date|after_or_equal:desde',
        ]);

        $desde = $request->filled('desde') ? $request->desde : Carbon::today()->toDateString();
        $hasta = $request->filled('hasta') ? $request->hasta : Carbon::today()->toDateString();

        /* 2)  Dataset paginado */
        $ventas = Venta::with(['cliente','usuario'])
            ->whereBetween(DB::raw('DATE(fecha_hora)'), [$desde, $hasta])
            ->where('estado', 1)              // solo completadas
            ->orderByDesc('fecha_hora')
            ->paginate(15)
            ->withQueryString();              // conserva filtros

        /* 3)  Resumen */
        $resumen = Venta::selectRaw('COUNT(*) as total_registros, SUM(total) as total_bs')
            ->whereBetween(DB::raw('DATE(fecha_hora)'), [$desde, $hasta])
            ->where('estado', 1)
            ->first();

        return view('reportes.sales', compact('ventas', 'desde', 'hasta', 'resumen'));
    }

    /* ───────── Reporte de Compras (opcional) ───────── */
    public function purchases(Request $request)
    {
        $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date|after_or_equal:desde',
        ]);

        $desde = $request->filled('desde') ? $request->desde : Carbon::today()->toDateString();
        $hasta = $request->filled('hasta') ? $request->hasta : Carbon::today()->toDateString();

        $compras = Compra::with(['proveedor','usuario'])
            ->whereBetween(DB::raw('DATE(fecha)'), [$desde, $hasta])
            ->orderByDesc('fecha')
            ->paginate(15)
            ->withQueryString();

        $resumen = Compra::selectRaw('COUNT(*) as total_registros, SUM(total) as total_bs')
            ->whereBetween(DB::raw('DATE(fecha)'), [$desde, $hasta])
            ->first();

        return view('reportes.purchases', compact('compras', 'desde', 'hasta', 'resumen'));
    }
}
