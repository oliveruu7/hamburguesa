<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\RolUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Http\Middleware\CheckPermission as Perm; 

class UserController extends Controller
{
    # Middleware para controlar el acceso
    public function __construct()
    {
        $this->middleware(Perm::class . ':users.index')->only('index');
        $this->middleware(Perm::class . ':users.create')->only(['create','store']);
        $this->middleware(Perm::class . ':users.edit')->only(['edit','update']);
        $this->middleware(Perm::class . ':users.delete')->only('destroy');
    }

    /* ------------------------------------------------------------------
       LISTADO + BUSCADOR
       -----------------------------------------------------------------*/
    public function index(Request $request)
    {
        $q = Usuario::with('rol')->where('estado', 1);             // solo activos

        /* — filtro opcional — */
        if ($request->filled('buscar')) {
            // máx. 25 caracteres, quitamos cualquier símbolo raro
            $term = substr(trim($request->buscar), 0, 25);
            $term = preg_replace('/[^A-Za-zÁÉÍÓÚáéíóúÑñ0-9@._\- ]/u', '', $term);

            $q->where(function ($sub) use ($term) {
                $sub->where('nombre', 'like', "%$term%")
                    ->orWhere('email',  'like', "%$term%");
            });
        }

        $usuarios = $q->orderBy('nombre')->paginate(10);
        return view('usuarios.index', compact('usuarios'));
    }

    /* ------------------------------------------------------------------
       CREAR
       -----------------------------------------------------------------*/
    public function create()
    {
        $roles = RolUsuario::all();
        return view('usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->validateRequest($request, 'store');

        Usuario::create([
            'nombre'      => $request->nombre,
            'email'       => $request->email,
            'telefono'    => $request->telefono,
            'direccion'   => $request->direccion,
            'perfil_link' => $request->perfil_link,
            'password'    => Hash::make($request->password),
            'idrol'       => $request->idrol,
            'estado'      => 1,                      // activo por defecto
        ]);

        return redirect()
               ->route('usuarios.index')
               ->with('success', 'Usuario registrado correctamente.');
    }

    /* ------------------------------------------------------------------
       EDITAR
       -----------------------------------------------------------------*/
    public function edit($id)
    {
        $usuario = Usuario::findOrFail($id);
        $roles   = RolUsuario::all();

        return view('usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $this->validateRequest($request, 'update', $usuario->idusuario);

        /* Asignación masiva segura */
        $usuario->fill($request->only([
            'nombre', 'email', 'telefono', 'direccion',
            'perfil_link', 'idrol'
        ]));

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }

        $usuario->save();

        return redirect()
               ->route('usuarios.index')
               ->with('success', 'Usuario actualizado correctamente.');
    }

    /* ------------------------------------------------------------------
       BORRADO LÓGICO
       -----------------------------------------------------------------*/
    public function destroy($id)
    {
        if ($id == Auth::id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }
        $usuario         = Usuario::findOrFail($id);
        $usuario->estado = 0;                 // pasa a inactivo
        $usuario->save();

        return back()->with('success', 'Usuario desactivado correctamente.');
    }

    /* ------------------------------------------------------------------
       SHOW
       -----------------------------------------------------------------*/
    public function show($id)
    {
        $usuario = Usuario::with('rol')->findOrFail($id);
        return view('usuarios.show', compact('usuario'));
    }

    /* ==================================================================
       VALIDACIÓN CENTRALIZADA
       =================================================================*/
    private function validateRequest(Request $request, string $modo, int $id = null): void
    {
        /* --------------- Reglas --------------- */
        $rules = [
            'nombre' => [
                'required', 'max:25',
                // solo letras y espacios
                'regex:/^[A-Za-zÁÉÍÓÚáéíóúÑñ ]+$/u',
            ],

            'email'  => [
                'required', 'email', 'max:30',
                // 1‑25 caracteres + @gmail.com
                'regex:/^[A-Za-z0-9._%+-]{1,25}@gmail\.com$/',
                // único (ignora al propio registro en update)
                "unique:usuario,email" . ($modo === 'update' ? ",$id,idusuario" : ''),
            ],

            'password' => $modo === 'store'
                          ? ['required', 'min:6']
                          : ['nullable', 'min:6'],

            'telefono'    => ['nullable', 'regex:/^\d{7,8}$/'],   // solo 7‑8 números
            'direccion'   => ['nullable', 'max:70'],
            'perfil_link' => ['nullable', 'url', 'min:10'],
            'idrol'       => ['required', 'exists:rol_usuario,idrol'],
        ];

        /* --------------- Mensajes --------------- */
        $messages = [
            // nombre
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.regex'    => 'Solo se permiten letras y espacios (sin números ni símbolos).',
            'nombre.max'      => 'Máximo 25 caracteres.',

            // email
            'email.required' => 'El correo es obligatorio.',
            'email.regex'    => 'Debe ser un correo Gmail válido, sin espacios ni símbolos extra.',
            'email.unique'   => 'Este correo ya está registrado.',
            'email.max'      => 'Máximo 30 caracteres en total.',

            // password
            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'La contraseña debe tener al menos 6 caracteres.',

            // teléfono
            'telefono.regex' => 'El teléfono debe contener solo números (7‑8 dígitos).',

            // perfil
            'perfil_link.url' => 'El enlace no es válido.',
            'perfil_link.min' => 'El enlace debe tener al menos 10 caracteres.',

            // rol
            'idrol.required' => 'Seleccione un rol.',
            'idrol.exists'   => 'Rol inválido.',
        ];

        $request->validate($rules, $messages);
    }
}
