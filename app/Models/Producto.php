<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'producto';
    protected $primaryKey = 'id_producto';
    public $timestamps = false;

    protected $fillable = [
        'nombre_comercial',
        'descripcion',
        'concentracion',
        'forma_farmaceutica',
        'precio_referencia',
        'stock_min',
        'avatar',
        'id_categoria',
        'id_laboratorio'
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }
    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class, 'id_laboratorio');
    }

    public function lotes()
    {
        return $this->hasMany(LoteProducto::class, 'id_producto');
    }
    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'id_producto');
    }
}
