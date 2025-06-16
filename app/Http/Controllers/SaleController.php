<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckPermission as Perm;
use App\Models\{Venta, DetalleVenta, Cliente, Product};   // Product == tabla hamburguesa
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware(Perm::class . ':sales.index' )->only('index');
        $this->middleware(Perm::class . ':sales.create')->only(['create','store']);
        $this->middleware(Perm::class . ':sales.delete')->only('destroy');
    }

    /* ===== LISTAR ===== */
    public function index()
    {
        $ventas = Venta::with(['cliente','usuario'])
                       ->orderByDesc('fecha_hora')
                       ->paginate(10);

        return view('sales.index', compact('ventas'));
    }

    /* ===== FORM NUEVA VENTA ===== */
    public function create()
    {
        return view('sales.create', [
            'clientes'  => Cliente::orderBy('nombre')->get(),
            'productos' => Product::where('estado',1)->get()   // trae hamburguesas activas
        ]);
    }

    /* ===== GUARDAR ===== */
    public function store(Request $request)
    {
        $items = $request->input('productos', []);

        /* Validaciones básicas */
        if (!$items) {
            return back()->withInput()->with('error','Debe agregar al menos un producto.');
        }

        foreach ($items as $i => $it) {
            if (!isset($it['idhamburguesa'],$it['cantidad'],$it['precio_unitario'])) {
                return back()->withInput()->with('error',"Fila #".($i+1)." incompleta.");
            }
            if ($it['cantidad'] < 1)
                return back()->withInput()->with('error',"Cantidad inválida en fila #".($i+1).".");
        }

        DB::beginTransaction();
        try {
            $total = collect($items)->sum(fn($it)=>$it['cantidad']*$it['precio_unitario']);

            $venta = Venta::create([
                'idcliente' => $request->idcliente,
                'idusuario' => Auth::id(),
                'fecha_hora'=> now(),
                'total'     => $total,
                'estado'    => 1,
            ]);

            foreach ($items as $it) {
                DetalleVenta::create([
                    'idventa'        => $venta->idventa,
                    'idhamburguesa'  => $it['idhamburguesa'],
                    'cantidad'       => $it['cantidad'],
                    'precio_unitario'=> $it['precio_unitario'],
                    'subtotal'       => $it['cantidad']*$it['precio_unitario'],
                ]);
            }

            DB::commit();
            return redirect()->route('sales.index')
                             ->with('success','Venta registrada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()
                         ->with('error','Error al registrar la venta: '.$e->getMessage());
        }
    }

    /* ===== DETALLE ===== */
    public function show(Venta $sale)   // Route-model binding usa parámetro 'sale'
    {
        $sale->load(['cliente','usuario','detalles.hamburguesa']);
        return view('sales.show', ['venta'=>$sale]);
    }

    /* ===== ELIMINAR / ANULAR ===== */
    public function destroy(Venta $sale)
    {
        $sale->delete();
        return back()->with('success','Venta eliminada.');
    }
}
