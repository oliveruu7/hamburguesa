<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    /* ----- Config básica ----- */
    protected $table      = 'receta';
    protected $primaryKey = 'idreceta';
    public    $timestamps = false;

    protected $fillable = [
        'idhamburguesa',
        'idinsumo',
        'cantidad_necesaria',
    ];

    /* ----- Relaciones ----- */
    // 1 receta pertenece a UNA hamburguesa
    public function hamburguesa()
    {
        return $this->belongsTo(Product::class, 'idhamburguesa');
    }

    // 1 receta pertenece a UN insumo
    public function insumo()
    {
        return $this->belongsTo(Insumo::class, 'idinsumo');
    }
}
