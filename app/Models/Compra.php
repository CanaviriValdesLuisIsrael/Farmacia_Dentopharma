<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'compra';
    protected $primaryKey = 'id_compra';
    public $timestamps = false;

    protected $fillable = [
        'fecha_compra',
        'total_compra',
        'descuento',
        'id_empleado',
        'id_caja'
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'id_caja');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class, 'id_compra');
    }
}