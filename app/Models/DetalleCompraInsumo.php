<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleCompraInsumo extends Model
{
    protected $table = 'detalle_compra_insumo';
    protected $primaryKey = 'iddetalle_compra';
    public $timestamps = false;

    protected $fillable = [
        'idcompra','idinsumo','cantidad','precio',
    ];

    public function insumo():  BelongsTo { return $this->belongsTo(Insumo::class,  'idinsumo'); }
    public function compra():  BelongsTo { return $this->belongsTo(Compra::class,  'idcompra'); }
}
