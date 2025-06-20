<?php

namespace App\Http\Controllers;

use App\Models\{Venta, Product, Usuario};     // ← tus modelos Eloquent
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /* -----------------------------------------------------------------
     | 1)   PANEL PRINCIPAL  →  /admin
     |------------------------------------------------------------------
     | Devuelve la vista Blade (`resources/views/panel/admin.blade.php`)
     | con algunos contadores básicos para mostrar inmediatamente.
     *-----------------------------------------------------------------*/
    public function index()
    {
        return view('panel.admin', [
            'totalProducts' => Product::count(),
            'ventasHoy'     => Venta::whereDate('fecha_hora', today())->count(),
            'totalUsuarios' => Usuario::count(),
        ]);
    }

    /* -----------------------------------------------------------------
     | 2)   MÉTRICAS PARA GRÁFICAS  →  /dashboard/metrics
     |------------------------------------------------------------------
     | Devuelve JSON con:
     |   • Ventas últimos 7 días  (date  => total Bs)
     |   • Top-5 hamburguesas     (array de nombres)
     *-----------------------------------------------------------------*/
    public function metrics()
    {
        try {
            /* --- Ventas de los últimos 7 días ----------------------- */
            $salesLast7 = Venta::selectRaw('DATE(fecha_hora) AS d, SUM(total) AS t')
                              ->where('fecha_hora', '>=', Carbon::now()->subDays(6))
                              ->groupBy('d')
                              ->orderBy('d')
                              ->pluck('t', 'd');   // →  {"2025-06-12":580, "2025-06-13":430,…}

            /* --- Top 5 hamburguesas más vendidas último mes -------- */
            $topProducts = DB::table('detalle_venta AS dv')
                             ->join('venta AS v', 'v.idventa', '=', 'dv.idventa')
                             ->join('hamburguesa AS h', 'h.idhamburguesa', '=', 'dv.idhamburguesa')
                             ->where('v.fecha_hora', '>=', Carbon::now()->subMonth())
                             ->groupBy('h.idhamburguesa', 'h.nombre')
                             ->orderByRaw('SUM(dv.cantidad) DESC')
                             ->limit(5)
                             ->pluck('h.nombre');   // → ["Zombie Doble", "Clásica", …]

            return response()->json([
                'salesLast7'  => $salesLast7,
                'topProducts' => $topProducts,
            ]);

        } catch (\Throwable $e) {
            Log::error('Dashboard metrics error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
            ]);
            return response()->json(['error' => 'server'], 500);
        }
    }

    /* -----------------------------------------------------------------
     | 3)   KPIs NUMÉRICOS  →  /dashboard/kpis
     |------------------------------------------------------------------
     | Devuelve JSON con indicadores simples (tarjetas).
     *-----------------------------------------------------------------*/
    public function kpis()
    {
        try {
            return response()->json([
                'ventasHoy'  => Venta::whereDate('fecha_hora', today())->sum('total'),
                'productos'  => Product::count(),
                'usuarios'   => Usuario::count(),
            ]);

        } catch (\Throwable $e) {
            Log::error('Dashboard KPI error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
            ]);
            return response()->json(['error' => 'server'], 500);
        }
    }
}
