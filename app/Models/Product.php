<?php
 namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'hamburguesa'; // <-- Tabla real en BD
    protected $primaryKey = 'idhamburguesa'; // <-- Campo PK real
    public $timestamps = false;

    protected $fillable = [
        'idcategoria',
        'nombre',
        'tipo',
        'precio_unitario',
        'descripcion',
        'imagenUrl',
        'estado'
    ];

    /* ---------- Relaciones ---------- */
    // (1) hamburguesa pertenece a una categoría
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'idcategoria');
    }

    // (2) hamburguesa tiene muchas recetas (relación 1-N)
    public function recetas()
    {
        return $this->hasMany(Receta::class, 'idhamburguesa');
    }

    // (3) hamburguesa ↔ insumos (relación N-N vía ‘receta’)
    public function insumos()
    {
        return $this->belongsToMany(
            Insumo::class,
            'receta',             // tabla pivote
            'idhamburguesa',      // FK local en pivote
            'idinsumo'            // FK del otro modelo
        )->withPivot('cantidad_necesaria');
    }

    /* ---------- Scopes útiles (opcional) ---------- */
    public function scopeActivas($q)
    {
        return $q->where('estado', 1);
    }
}
