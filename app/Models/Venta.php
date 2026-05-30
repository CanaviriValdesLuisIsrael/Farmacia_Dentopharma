<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'venta';
    protected $primaryKey = 'id_venta';
    public $timestamps = false;

    protected $fillable = [
        'fecha_venta',
        'total_venta',
        'metodo_pago',
        'id_empleado',
        'id_cliente'
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'id_venta');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }
}
