<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedor';
    protected $primaryKey = 'idproveedor';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'telefono',
        'email'
    ];

    public function compras()
    {
        return $this->hasMany(Compra::class, 'idproveedor');
    }
}
