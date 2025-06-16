<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckPermission as Perm;
use App\Models\{Compra, DetalleCompraInsumo, Proveedor, Insumo};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CompraController extends Controller
{
    public function __construct()
    {
        $this->middleware(Perm::class.':compras.index')->only('index');
        $this->middleware(Perm::class.':compras.create')->only(['create','store']);
        $this->middleware(Perm::class.':compras.edit')->only(['edit','update']);
        $this->middleware(Perm::class.':compras.delete')->only('destroy');
    }

    /* ===== LISTAR ===== */
public function index()
{
    $compras = Compra::with(['proveedor','usuario'])
                     ->where('estado', 'Registrada')  
                     ->orderByDesc('fecha')
                     ->paginate(10);

    return view('compras.index', compact('compras'));
}


    /* ===== FORMULARIO NUEVA COMPRA ===== */
    public function create()
    {
        return view('compras.create', [
            'proveedores' => Proveedor::all(),
            'insumos'     => Insumo::all(),
        ]);
    }

    /* ===== GUARDAR ===== */
    public function store(Request $request)
    {
        $data = $request->validate([
            'idproveedor'            => 'required|exists:proveedor,idproveedor',
            'detalles'               => 'required|array|min:1',
            'detalles.*.idinsumo'    => 'required|exists:insumo,idinsumo|distinct',
            'detalles.*.cantidad'    => 'required|numeric|min:0.01',
            'detalles.*.precio'      => 'required|numeric|min:0.01',
        ]);

        DB::beginTransaction();
        try {
            /* Total */
            $total = collect($data['detalles'])
                     ->sum(fn($d) => $d['cantidad'] * $d['precio']);

            $compra = Compra::create([
                'idproveedor' => $data['idproveedor'],
                'idusuario'   => Auth::id(),
                'fecha'       => now(),
                'total'       => $total,
            ]);

            foreach ($data['detalles'] as $detalle) {
                DetalleCompraInsumo::create([
                    'idcompra' => $compra->idcompra,
                    'idinsumo' => $detalle['idinsumo'],
                    'cantidad' => $detalle['cantidad'],
                    'precio'   => $detalle['precio'],
                ]);
            }

            DB::commit();
            return redirect()->route('compras.index')
                             ->with('success','Compra registrada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error','Error: '.$e->getMessage())->withInput();
        }
    }

    /* ===== FORMULARIO DE EDICIÓN ===== */
public function edit(Compra $compra)
{
    // Sólo permitir edición si NO está anulada
    if ($compra->estado === 'Anulada') {
        return back()->with('info','No se puede editar una compra anulada.');
    }

    return view('compras.edit', [
        'compra'      => $compra->load('detalles.insumo', 'proveedor'),
        'proveedores' => Proveedor::all(),
        'insumos'     => Insumo::all(),
    ]);
}

 public function update(Request $request, Compra $compra)
{
    if ($compra->estado === 'Anulada') {
        return back()->with('info','No se puede editar una compra anulada.');
    }

    $data = $request->validate([
        'idproveedor'            => 'required|exists:proveedor,idproveedor',
        'detalles'               => 'required|array|min:1',
        'detalles.*.idinsumo'    => 'required|exists:insumo,idinsumo|distinct',
        'detalles.*.cantidad'    => 'required|numeric|min:0.01',
        'detalles.*.precio'      => 'required|numeric|min:0.01',
    ]);

    DB::beginTransaction();
    try {
        /* 1. Elimina detalles viejos ->
              el trigger AFTER DELETE resta stock automáticamente */
        $compra->detalles()->delete();

        /* 2. Calcula total y actualiza encabezado */
        $total = collect($data['detalles'])
                 ->sum(fn($d)=>$d['cantidad']*$d['precio']);

        $compra->update([
            'idproveedor' => $data['idproveedor'],
            'total'       => $total,
            'fecha'       => now(),
        ]);

        /* 3. Inserta nuevos detalles ->
              el trigger AFTER INSERT suma stock automáticamente */
        foreach ($data['detalles'] as $d) {
            DetalleCompraInsumo::create([
                'idcompra' => $compra->idcompra,
                'idinsumo' => $d['idinsumo'],
                'cantidad' => $d['cantidad'],
                'precio'   => $d['precio'],
            ]);
        }

        DB::commit();
        return redirect()->route('compras.index')
                         ->with('success','Compra actualizada.');
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->with('error','Error: '.$e->getMessage())->withInput();
    }
}


    /*Eliminar compra y sus detalles */
    public function destroy(Compra $compra)
{
    if ($compra->estado === 'Anulada') {
        return back()->with('info', 'La compra ya está anulada.');
    }

    DB::beginTransaction();
    try {
        /** @var DetalleCompraInsumo $detalle */
        foreach ($compra->detalles as $detalle) {
            // Restar del stock lo que había sumado esa compra
            $detalle->insumo()->update([
                'stock_actual' => DB::raw('GREATEST(0, stock_actual - '.$detalle->cantidad.')')
            ]);
        }

        $compra->update(['estado' => 'Anulada']);

        DB::commit();
        return back()->with('success', 'Compra anulada y stock revertido.');
    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->with('error', 'Error al anular: '.$e->getMessage());
    }
}
}
