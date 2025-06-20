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

    /* -------- 1. validar -------- */
    $data = $request->validate([
        'idproveedor'            => 'required|exists:proveedor,idproveedor',
        'detalles'               => 'required|array|min:1',
        'detalles.*.idinsumo'    => 'required|exists:insumo,idinsumo|distinct',
        'detalles.*.cantidad'    => 'required|numeric|min:0.01',
        'detalles.*.precio'      => 'required|numeric|min:0.01',
    ]);

    DB::beginTransaction();
    try {

        /* -------- 2. Mapear detalles actuales -------- */
        /** @var \Illuminate\Support\Collection $viejos */
        $viejos = $compra->detalles->keyBy('iddetalle_compra_insumo');   // agrupa por PK
        $usados = [];   // ids que seguiremos usando

        /* -------- 3. Recorrer nuevos detalles -------- */
        foreach ($data['detalles'] as $idx => $nuevo) {

            // ¿Existe una fila con mismo índice visual? -> obtenemos su PK
            $detalleViejo = $compra->detalles[$idx] ?? null;

            if ($detalleViejo) {
                // ---------- a) el detalle ya existía ----------
                $usados[] = $detalleViejo->iddetalle_compra_insumo;

                // a1) si cambió de insumo, revertir viejo y sumar nuevo
                if ($detalleViejo->idinsumo != $nuevo['idinsumo']) {
                    $detalleViejo->insumo()->decrement('stock_actual', $detalleViejo->cantidad);
                    Insumo::where('idinsumo', $nuevo['idinsumo'])
                          ->increment('stock_actual', $nuevo['cantidad']);
                } else {
                    // a2) mismo insumo, ajustar por diferencia de cantidad
                    $delta = $nuevo['cantidad'] - $detalleViejo->cantidad;
                    if ($delta != 0) {
                        $detalleViejo->insumo()->increment('stock_actual', $delta);
                    }
                }

                // a3) actualizar fila detalle
                $detalleViejo->update([
                    'idinsumo' => $nuevo['idinsumo'],
                    'cantidad' => $nuevo['cantidad'],
                    'precio'   => $nuevo['precio'],
                ]);

            } else {
                // ---------- b) detalle completamente nuevo ----------
                $nuevoDet = DetalleCompraInsumo::create([
                    'idcompra' => $compra->idcompra,
                    'idinsumo' => $nuevo['idinsumo'],
                    'cantidad' => $nuevo['cantidad'],
                    'precio'   => $nuevo['precio'],
                ]);
                $usados[] = $nuevoDet->iddetalle_compra_insumo;

                // sumar al stock
                $nuevoDet->insumo()->increment('stock_actual', $nuevo['cantidad']);
            }
        }

        /* -------- 4. Eliminar detalles que ya no existen -------- */
        $paraBorrar = $compra->detalles()
                             ->whereNotIn('iddetalle_compra_insumo', $usados)
                             ->get();

        foreach ($paraBorrar as $det) {
            $det->insumo()->decrement('stock_actual', $det->cantidad);
            $det->delete();
        }

        /* -------- 5. Recalcular total -------- */
        $total = $compra->detalles()->sum(DB::raw('cantidad * precio'));

        /* -------- 6. Detectar “sin cambios” -------- */
        $sinCambiosProveedor = $compra->idproveedor == $data['idproveedor'];
        $sinCambiosTotal     = bccomp($total, $compra->total, 2) == 0;
        if ($sinCambiosProveedor && $paraBorrar->isEmpty() && empty(array_diff($compra->detalles->pluck('iddetalle_compra_insumo')->toArray(), $usados)) && $sinCambiosTotal) {
            DB::rollBack();
            return back()->with('info', 'No se realizaron cambios.');
        }

        /* -------- 7. Actualizar cabecera -------- */
        $compra->update([
            'idproveedor' => $data['idproveedor'],
            'total'       => $total,
            'fecha'       => now(),
        ]);

        DB::commit();
        return redirect()->route('compras.index')->with('success','Compra actualizada correctamente.');

    } catch (\Throwable $e) {
        DB::rollBack();
        return back()->with('error','Error: '.$e->getMessage())->withInput();
    }
}

}
