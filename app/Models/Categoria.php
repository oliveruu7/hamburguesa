<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    /* ---------- Config básica ---------- */
    protected $table      = 'categoria';     // nombre real
    protected $primaryKey = 'idcategoria';   // PK real
    public    $timestamps = false;           // la tabla no tiene created_at / updated_at

    /* Campos que se pueden asignar en masa */
    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    /* Casts (BIT → boolean) */
    protected $casts = [
        'estado' => 'boolean',
    ];

    /* ---------- Relaciones ---------- */
    /** una categoría tiene muchos productos */
    public function productos()
    {
        return $this->hasMany(Product::class, 'idcategoria');
    }

    /* ---------- Scopes ---------- */
    /** solo categorías activas */
    public function scopeActivas($query)
    {
        return $query->where('estado', 1);
    }
}
