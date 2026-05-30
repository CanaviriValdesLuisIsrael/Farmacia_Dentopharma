<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'cliente';
    protected $primaryKey = 'id_cliente';
    public $timestamps = false;

    protected $fillable = [
        'ci',
        'nombre',
        'apellido',
        'nro_contacto',
        'tipo_cliente'
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_cliente');
    }
}
