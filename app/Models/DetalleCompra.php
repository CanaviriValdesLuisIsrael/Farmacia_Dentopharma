<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class DetalleCompra extends Model
{
    protected $table = 'detalle_compra';
    // PK compuesta
    protected $primaryKey = 'id_detallecompra';
    public $incrementing = true;
    public $timestamps = false;
 
    protected $fillable = [
        'id_compra',
        'id_producto',
        'tipo_unidad',
        'cantidad',
        'unidades_por_paquete',
        'precio_unitario',
        'costo_por_paquete',
        'subtotal',
    ];
 
    protected $casts = [
        'precio_unitario'     => 'decimal:2',
        'costo_por_paquete'   => 'decimal:2',
        'subtotal'            => 'decimal:2',
        'cantidad'            => 'integer',
        'unidades_por_paquete'=> 'integer',
    ];
 
    // ── Relaciones ─────────────────────────────────────────
 
    public function compra()
    {
        return $this->belongsTo(Compra::class, 'id_compra');
    }
 
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto');
    }
 
    // ── Accesores ──────────────────────────────────────────
 
    /**
     * Total de unidades que ingresan al stock
     * (equivalente a la columna generada total_unidades en BD)
     */
    public function getTotalUnidadesAttribute(): int
    {
        return (int) $this->cantidad * (int) $this->unidades_por_paquete;
    }
 
    /**
     * Descripción legible del tipo de compra
     * Ej: "3 cajas × 10 u = 30 unidades"
     */
    public function getDescripcionUnidadAttribute(): string
    {
        if ($this->tipo_unidad === 'unidad') {
            return "{$this->cantidad} unidades";
        }
        return "{$this->cantidad} {$this->tipo_unidad}(s) × {$this->unidades_por_paquete} u = {$this->total_unidades} unidades";
    }
}