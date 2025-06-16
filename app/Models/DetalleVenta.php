<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleVenta extends Model
{
    protected $table      = 'detalle_venta';
    protected $primaryKey = 'iddetalle_venta';
    public    $timestamps = false;

    protected $fillable = [
        'idventa','idhamburguesa','cantidad','precio_unitario','subtotal',
    ];

    public function venta():       BelongsTo { return $this->belongsTo(Venta::class,'idventa'); }
    public function hamburguesa(): BelongsTo { return $this->belongsTo(Hamburguesa::class,'idhamburguesa'); }
}
