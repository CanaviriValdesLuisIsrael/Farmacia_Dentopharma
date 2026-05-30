<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    protected $table = 'detalle_venta';
    public $incrementing = true;
    protected $primaryKey = 'id_detalleventa';
    public $timestamps = false;

    protected $fillable = [
        'id_venta',
        'id_lote',        
        'id_producto',    
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta');
    }
    public function lote()
    {
        return $this->belongsTo(LoteProducto::class, 'id_lote');
    }
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
}
