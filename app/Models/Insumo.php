<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Insumo extends Model
{
    /* ---------- Config básica ---------- */
    protected $table      = 'insumo';
    protected $primaryKey = 'idinsumo';
    public    $timestamps = false;

    /* ---------- Asignación en masa ---------- */
    protected $fillable = [
        'nombre',
        'unidad',
        'descripcion',
        'stock_actual',
    ];

    /* ---------- Relaciones ---------- */
    /** 1 insumo puede aparecer en muchas recetas */
    public function detalleCompras()   { return $this->hasMany(DetalleCompraInsumo::class,'idinsumo'); }
    public function detallesSalidas()  { return $this->hasMany(DetalleSalidaInsumo::class,'idinsumo'); }
    public function recetas()          { return $this->hasMany(Receta::class,'idinsumo'); }

    /** Relación N-a-N con hamburguesas, a través de la tabla pivote `receta`
     *  Acceso:
     *      $insumo->hamburguesas   → colección de Product
     *      $insumo->hamburguesas->first()->pivot->cantidad_necesaria
     */
    public function hamburguesas()
    {
        return $this->belongsToMany(
            Product::class,     // modelo relacionado
            'receta',           // tabla pivote
            'idinsumo',         // FK de este modelo en la pivote
            'idhamburguesa'     // FK del otro modelo en la pivote
        )->withPivot('idreceta', 'cantidad_necesaria');
    }
}
