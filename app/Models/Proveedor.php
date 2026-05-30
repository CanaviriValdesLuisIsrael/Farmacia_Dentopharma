<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedor';
    protected $primaryKey = 'id_proveedor';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'nro_contacto',
        'correo',
        'direccion',
        'avatar'
    ];

    public function lotes()
    {
        return $this->hasMany(LoteProducto::class, 'id_proveedor');
    }
}