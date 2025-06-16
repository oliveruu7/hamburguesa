<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Usuario;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        // El panel requiere el permiso main.menu.view
        $this->middleware(\App\Http\Middleware\CheckPermission::class . ':main.menu.view');
    }

    public function index()
    {
        return view('panel.admin', [          // ⬅️  aquí cambió
            'totalProducts' => Product::count(),
            'ventasHoy'     => Sale::whereDate('created_at', Carbon::today())->count(),
            'totalUsuarios' => Usuario::count(),
        ]);
    }
}
