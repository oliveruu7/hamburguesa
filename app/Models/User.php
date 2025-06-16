<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table = 'usuario'; // Nombre de la tabla en la base de datos
    protected $primaryKey = 'idusuario'; // Clave primaria de la tabla

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'email',
        'perfil_link',
        'password',
        'idrol',
        'estado',
    ];

    // Relación con la tabla de roles
    public function rol()
    {
        return $this->belongsTo(RolUsuario::class, 'idrol', 'idrol');
    }
}