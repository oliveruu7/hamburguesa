<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckPermission as Perm;
use App\Models\Insumo;
use Illuminate\Http\Request;

class InsumoController extends Controller
{
    public function __construct()
    {
        $this->middleware(Perm::class . ':insumos.index')->only('index');
        $this->middleware(Perm::class . ':insumos.create')->only(['create','store']);
        $this->middleware(Perm::class . ':insumos.edit')->only(['edit','update']);
        $this->middleware(Perm::class . ':insumos.delete')->only('destroy');
    }

    /* ===== LISTAR ===== */
    public function index(Request $request)
    {
        $insumos = Insumo::when($request->filled('q'), function ($q) use ($request) {
                            $txt = trim($request->q);
                            $q->where('nombre','LIKE',"%$txt%")
                              ->orWhere('unidad','LIKE',"%$txt%");
                        })
                        ->orderBy('nombre')
                        ->paginate(10);

        return view('insumos.index', compact('insumos'));
    }

    /* ===== CREAR ===== */
    public function create()
    {
        return view('insumos.create', ['insumo' => new Insumo()]);
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        $data['stock_actual'] = 0.00;

        Insumo::create($data);

        return redirect()->route('insumos.index')
                         ->with('success', 'Insumo registrado exitosamente.');
    }

    /* ===== EDITAR ===== */
    public function edit(Insumo $insumo)
    {
        return view('insumos.edit', compact('insumo'));
    }

    public function update(Request $request, Insumo $insumo)
    {
        $data = $this->validar($request, $insumo->idinsumo);

        /* 1.  Rellenar el modelo con los nuevos datos */
        $insumo->fill($data);

        /* 2. ¿Realmente cambió algo? */
        if (!$insumo->isDirty()) {
            return back()->with('info', 'No se realizaron cambios.');
        }

        $insumo->save();

        return redirect()->route('insumos.index')
                         ->with('success', 'Insumo actualizado correctamente.');
    }

    /* ===== ELIMINAR ===== */
    public function destroy(Insumo $insumo)
    {
        $usado = $insumo->detalleCompras()->exists()
              || $insumo->detallesSalidas()->exists()
              || $insumo->recetas()->exists();

        if ($usado) {
            return back()->with('error',
                'No puedes eliminar este insumo porque ya fue utilizado en registros de compra o receta.');
        }

        $insumo->delete();
        return back()->with('success', 'Insumo eliminado.');
    }

    /* ===== VALIDAR ===== */
    /* ===== VALIDAR ===== */
private function validar(Request $request, $id = null): array
{
    /* -------- Reglas -------- */
    $rules = [
        'nombre'      => [
            'required','string','max:50',
            // único (ignora al propio registro en update)
            'unique:insumo,nombre' . ($id ? ",$id,idinsumo" : '')
        ],
        'unidad'      => 'required|string|max:20',
        'descripcion' => 'nullable|string|max:255',
    ];

    /* -------- Mensajes -------- */
    $messages = [
        'nombre.required' => 'El nombre es obligatorio.',
        'nombre.max'      => 'El nombre no debe superar 50 caracteres.',
        'nombre.unique'   => 'Ya existe un insumo con ese nombre.',   // 👈 aquí tu mensaje
        'unidad.required' => 'La unidad es obligatoria.',
        'unidad.max'      => 'La unidad no debe superar 20 caracteres.',
        'descripcion.max' => 'La descripción no debe superar 255 caracteres.',
    ];

    return $request->validate($rules, $messages);
}

}
