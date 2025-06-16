<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckPermission as Perm;
use App\Models\Compra;
use App\Models\Insumo;
use App\Models\DetalleCompraInsumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetalleCompraController extends Controller
{
    public function __construct()
    {
        $this->middleware(Perm::class.':compras.edit'); // el mismo permiso que editar
    }

    /* ========= FORMULARIO ========= */
    public function create(Compra $compra)
    {
        $insumos  = Insumo::orderBy('nombre')->get();
        $detalles = $compra->load('detalles.insumo')->detalles; // eager

        return view('compras.detalle', compact('compra','insumos','detalles'));
    }

    /* ========= AGREGAR FILA ========= */
    public function store(Request $r, Compra $compra)
    {
        $data = $r->validate([
            'idinsumo'        => 'required|exists:insumo,idinsumo',
            'cantidad'        => 'required|numeric|min:0.01|max:9999.99',
            'precio_unitario' => 'required|numeric|min:0.01|max:999999.99',
        ]);

        /* No repetir insumo */
        if ($compra->detalles()->where('idinsumo',$r->idinsumo)->exists()) {
            return back()->with('error','Ese insumo ya está en la compra.');
        }

        DB::transaction(function() use ($compra,$data) {

            /* 1. Crear fila detalle */
            $fila = $compra->detalles()->create($data);

            /* 2. Incrementar stock del insumo */
            $fila->insumo->increment('stock_actual', $data['cantidad']);

            /* 3. Recalcular total de la compra */
            $nuevoTotal = $compra->detalles()
                                 ->sum(DB::raw('cantidad * precio_unitario'));
            $compra->update(['total' => $nuevoTotal]);
        });

        return back()->with('success','Insumo añadido.');
    }

    /* ========= QUITAR FILA ========= */
    public function destroy(Compra $compra, DetalleCompraInsumo $fila)
    {
        abort_unless($fila->idcompra == $compra->idcompra,404);

        DB::transaction(function() use ($compra,$fila) {

            /* 1. Devolver stock */
            $fila->insumo->decrement('stock_actual', $fila->cantidad);

            /* 2. Eliminar fila */
            $fila->delete();

            /* 3. Recalcular total */
            $nuevoTotal = $compra->detalles()
                                 ->sum(DB::raw('cantidad * precio_unitario'));
            $compra->update(['total' => $nuevoTotal]);
        });

        return back()->with('success','Insumo quitado de la compra.');
    }
}
