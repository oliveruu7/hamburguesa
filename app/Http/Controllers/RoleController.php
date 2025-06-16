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
 
         $rol = RolUsuario::create($r->only('nombre','descripcion') + ['estado'=>1]);
 
         $ids = $r->full_access === 'yes'
                  ? Permiso::pluck('idpermiso')->all()
                  : ($r->permisos ?? []);
 
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
 
     public function update(Request $r, $id)
{
    $rol = RolUsuario::with('usuarios')->findOrFail($id);
    $this->validar($r, $id);

    $usuarioActual = auth()->user();

    // Obtener los nuevos permisos asignados
    $ids = $r->full_access === 'yes'
        ? Permiso::pluck('idpermiso')->all()
        : ($r->permisos ?? []);

    // ❌ Evitar rol sin permisos
    if (empty($ids)) {
        return back()->with('error', 'Debes asignar al menos un permiso.');
    }

    // ❌ No dejar quitar el permiso de roles al propio rol
    $permisoRolesID = Permiso::where('nombre', 'roles.index')->value('idpermiso');
    if ($usuarioActual->idrol === $rol->idrol && !in_array($permisoRolesID, $ids)) {
        return back()->with('error', 'No puedes quitar el permiso del módulo de roles a tu propio rol.');
    }

    // ❌ No dejar su propio rol sin permisos
    if ($usuarioActual->idrol === $rol->idrol && $r->full_access !== 'yes' && empty($r->permisos)) {
        return back()->with('error', 'No puedes dejar tu propio rol sin permisos mientras estás logueado.');
    }

    // ❌ Proteger rol Administrador (opcional)
    $esAdmin = strtolower($rol->nombre) === 'admin' || strtolower($rol->nombre) === 'administrador';
    if ($esAdmin && $r->full_access !== 'yes' && count($ids) < 3) {
        return back()->with('error', 'El rol Administrador debe mantener permisos esenciales.');
    }

    // ❌ Si el rol tiene usuarios, debe conservar al menos 2 permisos
    if ($rol->usuarios->count() > 0 && count($ids) < 2) {
        return back()->with('error', 'Este rol tiene usuarios asignados. No puede tener menos de 2 permisos.');
    }

    // ✅ Actualizar y guardar
    $rol->update($r->only('nombre', 'descripcion'));
    $rol->syncPermisos($ids);

    return redirect()->route('roles.index')->with('success', 'Rol actualizado.');
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
      'nombre.max' => 'El nombre del rol no debe exceder los 30 caracteres.',
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
 