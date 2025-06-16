<?php
// app/Http/Controllers/SalidaInsumoController.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalidaInsumo extends Model
{
    protected $table      = 'salida_insumo';
    protected $primaryKey = 'idsalida';
    public    $timestamps = false;

    protected $fillable = ['fecha','idusuario','observacion'];

    public function usuario(): BelongsTo        { return $this->belongsTo(Usuario::class,'idusuario'); }
    public function detalles(): HasMany         { return $this->hasMany(DetalleSalidaInsumo::class,'idsalida'); }
}
