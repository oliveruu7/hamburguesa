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
      $this->middleware(Perm::class.':sales.index' ) ->only('index');
      $this->middleware(Perm::class.':sales.create')->only(['create','store']);
      $this->middleware(Perm::class.':sales.edit'  )->only(['edit','update']);    
      $this->middleware(Perm::class.':sales.delete')->only('destroy');
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
    $request->validate([
        'idcliente'                   => ['required','exists:cliente,idcliente'],
        'productos'                   => ['required','array','min:1'],
        'productos.*.idhamburguesa'   => ['required','exists:hamburguesa,idhamburguesa'],
        'productos.*.cantidad'        => ['required','integer','min:1'],
        'productos.*.precio_unitario' => ['required','numeric','min:0'],
    ]);

    try {
        DB::transaction(function () use ($request) {

            $total = collect($request->productos)
                     ->sum(fn($p) => $p['cantidad'] * $p['precio_unitario']);

            $venta = Venta::create([
                'idcliente' => $request->idcliente,
                'idusuario' => Auth::id(),
                'fecha_hora'=> now(),
                'total'     => $total,
                'estado'    => 1,
            ]);

            foreach ($request->productos as $p) {
                DetalleVenta::create([
                    'idventa'        => $venta->idventa,
                    'idhamburguesa'  => $p['idhamburguesa'],
                    'cantidad'       => $p['cantidad'],
                    'precio_unitario'=> $p['precio_unitario'],
                    'subtotal'       => $p['cantidad'] * $p['precio_unitario'],
                ]);
            }
        });

        return redirect()->route('sales.index')
                         ->with('success','Venta registrada correctamente.');

    } catch (\Illuminate\Database\QueryException $e) {
        // Trigger 1644 => stock insuficiente
        if ($e->errorInfo[1] == 1644) {
            return back()->withInput()->with('error', $e->errorInfo[2]);
        }
        return back()->withInput()->with('error', 'Error SQL: '.$e->getMessage());
    } catch (\Throwable $e) {
        return back()->withInput()->with('error', 'Error: '.$e->getMessage());
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
