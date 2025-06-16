<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckPermission as Perm;
use App\Models\Product;   // hamburguesa
use App\Models\Insumo;
use App\Models\Receta;
use Illuminate\Http\Request;

class RecetaController extends Controller
{
    public function __construct()
    {
        $this->middleware(Perm::class . ':recetas.index')->only('index');
        $this->middleware(Perm::class . ':recetas.create')->only(['create', 'store']);
        $this->middleware(Perm::class . ':recetas.edit')->only(['edit', 'update']);
        $this->middleware(Perm::class . ':recetas.delete')->only('destroy');
    }

    /* ---------- LISTAR TODAS LAS RECETAS ---------- */
    public function index()
    {
        $recetas = Receta::with(['hamburguesa', 'insumo'])
                         ->orderBy('idhamburguesa')
                         ->orderBy('idreceta')
                         ->get();

        return view('recetas.index', compact('recetas'));
    }

    /* ---------- FORMULARIO CREAR ---------- */
    public function create()
    {
        $hamburguesas = Product::orderBy('nombre')->get();
        $insumos = Insumo::orderBy('nombre')->get();

        return view('recetas.create', compact('hamburguesas', 'insumos'));
    }

    /* ---------- GUARDAR RECETA ---------- */
    public function store(Request $request)
    {
        $data = $request->validate([
            'idhamburguesa'      => 'required|exists:hamburguesa,idhamburguesa',
            'idinsumo'           => 'required|exists:insumo,idinsumo',
            'cantidad_necesaria' => 'required|numeric|min:0.01'
        ]);

        Receta::create($data);

        return redirect()->route('recetas.index')->with('success', 'Receta registrada correctamente.');
    }

    /* ---------- FORMULARIO EDITAR ---------- */
    public function edit(Receta $receta)
{
    $hamburguesas = \App\Models\Product::orderBy('nombre')->get();
    $insumos = \App\Models\Insumo::orderBy('nombre')->get();

    return view('recetas.edit', compact('receta', 'hamburguesas', 'insumos'));
}


    /* ---------- ACTUALIZAR ---------- */
    public function update(Request $request, Receta $receta)
    {
        $data = $request->validate([
            'cantidad_necesaria' => 'required|numeric|min:0.01|max:9999.99'
        ]);

        $receta->update($data);

        return redirect()->route('recetas.index')->with('success', 'Receta actualizada correctamente.');
    }

    /* ---------- ELIMINAR ---------- */
    public function destroy(Receta $receta)
    {
        $receta->delete();

        return back()->with('success', 'Receta eliminada.');
    }
}
