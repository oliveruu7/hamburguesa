<?php
// app/Models/Cliente.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $table = 'cliente';
    protected $primaryKey = 'idcliente';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'telefono',
        'ci',
        'estado',
    ];

    /**
     * Un cliente puede tener muchas ventas.
     */
    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'idcliente');
    }
}
