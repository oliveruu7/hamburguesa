<?php
 
 namespace App\Http\Controllers;
 
 use App\Models\RolUsuario;
 use App\Models\Permiso;
 use Illuminate\Http\Request;
 use App\Http\Middleware\CheckPermission as Perm; 
 
 class RoleController extends Controller
 {

    public function __construct()
    {
        $this->middleware(Perm::class . ':roles.index')->only('index');
        $this->middleware(Perm::class . ':roles.create')->only(['create','store']);
        $this->middleware(Perm::class . ':roles.edit')->only(['edit','update']);
        $this->middleware(Perm::class . ':roles.delete')->only('destroy');
    }

     /* ===== LISTAR ===== */
     public function index()
     {
         $roles = RolUsuario::orderBy('nombre')->get();
         return view('roles.index', compact('roles'));
     }
 
     /* ===== FORM CREAR ===== */
     public function create()
     {
         return view('roles.create', [
             'rol'    => new RolUsuario(),
             'grupos' => $this->getGruposPermisos()
         ]);
     }
 
     /* ===== GUARDAR ===== */
public function store(Request $r)
{
    $this->validar($r);

    $ids = $r->full_access === 'yes'
        ? Permiso::pluck('idpermiso')->all()
        : ($r->permisos ?? []);

    // ❌ Si no se asignó ningún permiso
    if (empty($ids)) {
        return back()
            ->withInput()
            ->with('error', 'Debes seleccionar al menos un permiso.');
    }

    $rol = RolUsuario::create($r->only('nombre','descripcion') + ['estado'=>1]);
    $rol->syncPermisos($ids);

    return redirect()->route('roles.index')->with('success','Rol creado.');
}

 
     /* ===== FORM EDITAR ===== */
     public function edit($id)
     {
         return view('roles.edit', [
             'rol'    => RolUsuario::with('permisos')->findOrFail($id),
             'grupos' => $this->getGruposPermisos()
         ]);
     }
 
        /* ===== ACTUALIZAR ===== */
        public function update(Request $r, $id)
{
    $rol = RolUsuario::with(['usuarios', 'permisos'])->findOrFail($id);
    $this->validar($r, $id);

    $usuarioActual = auth()->user();

    /* ---------- Permisos enviados ---------- */
    $ids = $r->full_access === 'yes'
        ? Permiso::pluck('idpermiso')->all()
        : ($r->permisos ?? []);                 // array vacía si ningún checkbox

    /* ---------- 1. No se permite dejar el rol sin permisos ---------- */
    if (empty($ids)) {
        return back()->with('error', 'Debes asignar al menos un permiso.');
    }

    /* ---------- 2. Proteger «Administrador» contra pérdida de permisos ---------- */
    $esAdmin = in_array(strtolower($rol->nombre), ['admin', 'administrador']);
    if ($esAdmin) {
        $permisosOriginales = $rol->permisos->pluck('idpermiso')->toArray();
        $perdidos = array_diff($permisosOriginales, $ids);   // los que intenta quitar

        if (!empty($perdidos)) {
            return back()->with('error',
                'No puedes quitar permisos existentes al rol Administrador; solo es permitido añadir nuevos.');
        }
    }

    /* ---------- 3. Protección extra: no quitar tu propio permiso de roles ---------- */
    $permisoRolesID = Permiso::where('nombre', 'roles.index')->value('idpermiso');
    if ($usuarioActual->idrol === $rol->idrol && !in_array($permisoRolesID, $ids)) {
        return back()->with('error',
            'No puedes quitar el permiso del módulo de roles a tu propio rol.');
    }

    
/* ---------- 4. Verificar si realmente hay cambios ---------- */
$nombreIgual      = trim($r->nombre)      === trim($rol->nombre);
$descripcionIgual = trim($r->descripcion) === trim($rol->descripcion);

$permisosActuales = $rol->permisos->pluck('idpermiso')->toArray();
sort($permisosActuales);          // ordena
$idsOrdenados = $ids;             // $ids viene de la petición
sort($idsOrdenados);

$sinCambiosPermisos = ($permisosActuales === $idsOrdenados);

if ($nombreIgual && $descripcionIgual && $sinCambiosPermisos) {
    return back()->with('info', 'No se realizaron cambios.');
}


    /* ---------- 5. Actualizar ---------- */
    $rol->update($r->only('nombre', 'descripcion'));
    $rol->syncPermisos($ids);

    return redirect()
           ->route('roles.index')
           ->with('success', 'Rol actualizado correctamente.');
}

 
     /* ===== ELIMINAR ===== */
     public function destroy($id)
     {
         $rol = RolUsuario::findOrFail($id);
 
         if ($rol->usuarios()->exists()) {
             return back()->with('error','No se puede eliminar: hay usuarios con este rol.');
         }
 
         $rol->delete();           // o $rol->update(['estado'=>0]);
         return back()->with('success','Rol eliminado.');
     }
 
     /* ===== VALIDACIÓN ===== */
     private function validar(Request $r, $id = null): void
     {
         $r->validate([
           'nombre' => [
           'required',
           'min:3',
           'max:30',
           'unique:rol_usuario,nombre' . ($id ? ",$id,idrol" : '')
      ],
       'descripcion' => 'nullable|max:100',
       ], [
      'nombre.required' => 'El nombre del rol es obligatorio.',
      'nombre.min' => 'El nombre del rol debe tener al menos 3 caracteres.',
      'nombre.max' => 'El nombre del rol no debe exceder los 15 caracteres.',
      'nombre.unique' => 'Ya existe un rol con ese nombre.',
      'descripcion.max' => 'La descripción no debe exceder los 100 caracteres.',
   ]);

     }
     
 
     /* ===== AGRUPAR PERMISOS ===== */
     private function getGruposPermisos(): array
     {
         $out = [];
         foreach (Permiso::orderBy('nombre')->get() as $p) {
             [$prefijo] = explode('.',$p->nombre,2);
             $out[$prefijo][] = $p;
         }
         return $out;
     }
 }
 