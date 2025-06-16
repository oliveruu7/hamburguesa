<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Compra extends Model
{
    protected $table = 'compra';
    protected $primaryKey = 'idcompra';
    public $timestamps = false;

    protected $fillable = [
        'idproveedor', 'idusuario', 'fecha', 'total', 'estado',
    ];

    /* Relaciones */
    public function proveedor(): BelongsTo   { return $this->belongsTo(Proveedor::class, 'idproveedor'); }
    public function usuario():    BelongsTo   { return $this->belongsTo(Usuario::class,   'idusuario');  }
    public function detalles():   HasMany     { return $this->hasMany(DetalleCompraInsumo::class, 'idcompra'); }
}
