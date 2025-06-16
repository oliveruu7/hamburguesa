<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Authenticatable
{
    use HasFactory;

    /* ───────── Config básica ───────── */
    protected $table      = 'usuario';
    protected $primaryKey = 'idusuario';
    public    $timestamps = false;

    protected $fillable = [
        'idrol',        // FK al rol
        'nombre',
        'direccion',
        'telefono',
        'email',
        'password',
        'perfil_link',
        'estado',
    ];

    /* opcional: esconder password cuando serialices el modelo */
    protected $hidden = ['password'];

    /* ───────── Relaciones ───────── */

    /** inversa uno-a-muchos : el usuario pertenece a un rol */
    public function rol()
    {
        return $this->belongsTo(RolUsuario::class, 'idrol', 'idrol');
    }

    /** permisos agregados a través del rol (helper simple) */
    public function permisos()
    {
        return $this->rol ? $this->rol->permisos : collect();
    }
}
