<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    /* ───────── Config básica ───────── */
    protected $table      = 'permiso';      // nombre real de la tabla
    protected $primaryKey = 'idpermiso';    // PK real
    public    $timestamps = false;          // no tiene created_at/updated_at
    protected $fillable   = ['nombre','descripcion'];

    /* ───────── Relaciones ───────── */

    /** muchos-a-muchos : un permiso pertenece a varios roles */
    public function roles()
    {
        return $this->belongsToMany(
            RolUsuario::class,     // modelo destino
            'permiso_rol_usuario', // tabla pivote
            'idpermiso',           // FK actual (en la pivote)
            'idrol'                // FK del otro modelo
        );
    }
}
