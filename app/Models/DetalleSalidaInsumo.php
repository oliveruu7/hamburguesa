<?php
// app/Http/Controllers/SalidaInsumoController.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleSalidaInsumo extends Model
{
    protected $table      = 'detalle_salida_insumo';
    protected $primaryKey = 'iddetalle_salida';
    public    $timestamps = false;

    protected $fillable = ['idsalida','idinsumo','cantidad'];

    public function insumo(): BelongsTo { return $this->belongsTo(Insumo::class,'idinsumo'); }
    public function salida(): BelongsTo { return $this->belongsTo(SalidaInsumo::class,'idsalida'); }
}
