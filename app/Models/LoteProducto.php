<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoteProducto extends Model
{
    //
    protected $table = 'lote_producto';
    protected $primaryKey = 'id_lote';
    public $timestamps = false;

    protected $fillable = [
        'numero_lote',
        'fecha_vencimiento',
        'cantidad_por_caja',
        'id_producto',
        'id_compra',
        'id_detallecompra',
        'id_proveedor'
    ];
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'id_proveedor');
    }
    public function compra()
    {
        return $this->belongsTo(Compra::class, 'id_compra');
    }
    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'id_lote');
    }
}
