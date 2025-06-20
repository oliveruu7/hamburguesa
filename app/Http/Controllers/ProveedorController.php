<?php

namespace App\Http\Controllers;

use App\Http\Middleware\CheckPermission as Perm;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProveedorController extends Controller
{
    public function __construct()
    {
        $this->middleware(Perm::class.':proveedores.index')  ->only('index');
        $this->middleware(Perm::class.':proveedores.create')->only(['create','store']);
        $this->middleware(Perm::class.':proveedores.edit')   ->only(['edit','update']);
        $this->middleware(Perm::class.':proveedores.delete') ->only('destroy');
    }

    /* ========== LISTAR ========== */
    public function index(Request $request)
    {
        $proveedores = Proveedor::when($request->filled('q'), function ($q) use ($request) {
                                $txt = trim($request->q);
                                $q->where('nombre','like',"%$txt%")
                                  ->orWhere('email','like',"%$txt%");
                            })
                            ->orderBy('nombre')
                            ->paginate(10);

        return view('proveedores.index', compact('proveedores'));
    }

    /* ========== FORM CREAR ========== */
    public function create()
    {
        return view('proveedores.create', ['proveedor'=>new Proveedor()]);
    }

    /* ========== GUARDAR ========== */
    public function store(Request $request)
    {
        $data = $this->validar($request);

        Proveedor::create($data);

        return redirect()->route('proveedores.index')
                         ->with('success','Proveedor registrado correctamente.');
    }

    /* ========== FORM EDITAR ========== */
    public function edit(Proveedor $proveedor)
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    /* ========== ACTUALIZAR ========== */
    public function update(Request $request, Proveedor $proveedor)
    {
        $data = $this->validar($request, $proveedor->idproveedor);

        // si no cambió nada
        $proveedor->fill($data);
        if (!$proveedor->isDirty()) {
            return back()->with('info','No se realizaron cambios.');
        }

        $proveedor->save();

        return redirect()->route('proveedores.index')
                         ->with('success','Proveedor actualizado correctamente.');
    }

    /* ========== ELIMINAR ========== */
    public function destroy(Proveedor $proveedor)
    {
        // Si existe al menos una compra ligada al proveedor, no permitir borrado
        $tieneCompras = $proveedor->compras()->exists();   // asume relación compras() en modelo
        if ($tieneCompras) {
            return back()->with('error',
                'No se puede eliminar: el proveedor está ligado a registros de compra.');
        }

        $proveedor->delete();

        return back()->with('success','Proveedor eliminado correctamente.');
    }

    /* ========== VALIDACIÓN CENTRAL ========== */
    private function validar(Request $request, $id = null): array
    {
        return $request->validate([
            'nombre'   => [
                'required',
                'string',
                'max:20',                          // ≤ 20 caracteres
                Rule::unique('proveedor','nombre')
                    ->ignore($id,'idproveedor'),   // nombre único
            ],
            'telefono' => [
                'nullable',
                'regex:/^\d{7,8}$/',               // sólo dígitos, 7-8 caracteres
            ],
            'email'    => [
                'nullable',
                'email',
                'max:50',
            ],
        ],[
            'nombre.required'   => 'El nombre es obligatorio.',
            'nombre.max'        => 'Máx. 20 caracteres.',
            'nombre.unique'     => 'Ese proveedor ya existe.',
            'telefono.regex'    => 'El teléfono debe tener 7 u 8 dígitos.',
        ]);
    }
}
