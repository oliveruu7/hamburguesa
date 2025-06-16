<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    protected $table      = 'venta';
    protected $primaryKey = 'idventa';
    public    $timestamps = false;

    protected $fillable = [
        'idcliente','idusuario','fecha_hora','total','estado',
    ];

    /* Relaciones */
    public function cliente(): BelongsTo      { return $this->belongsTo(Cliente::class, 'idcliente'); }
    public function usuario():  BelongsTo      { return $this->belongsTo(Usuario::class,  'idusuario'); }
    public function detalles(): HasMany        { return $this->hasMany(DetalleVenta::class,'idventa'); }
}
