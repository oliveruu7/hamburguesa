<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolUsuario extends Model
{
    /* ───────── Config básica ───────── */
    protected $table      = 'rol_usuario';
    protected $primaryKey = 'idrol';
    public    $timestamps = false;
    protected $fillable   = ['nombre','descripcion','estado'];

    /* ───────── Relaciones ───────── */

    /** uno-a-muchos : un rol tiene muchos usuarios */
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'idrol', 'idrol');
    }

    /** muchos-a-muchos : un rol tiene muchos permisos */
    public function permisos()
    {
        return $this->belongsToMany(
            Permiso::class,
            'permiso_rol_usuario',
            'idrol',
            'idpermiso'
        );
    }

    /* ───────── Helper para sincronizar permisos ───────── */
    public function syncPermisos(array $ids)
    {
        // deja solo los IDs indicados en la pivote
        return $this->permisos()->sync($ids);
    }
}
