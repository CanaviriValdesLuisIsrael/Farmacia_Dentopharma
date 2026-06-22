<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class LoteProducto extends Model
{
    protected $table = 'lote_producto';
    protected $primaryKey = 'id_lote';
    public $timestamps = false;
 
    protected $fillable = [
        'numero_lote',
        'fecha_vencimiento',
        'cantidad_por_caja',
        'stock',
        'id_producto',
        'id_compra',
        'id_proveedor',
    ];
 
    protected $casts = [
        'fecha_vencimiento' => 'date',
    ];
 
    // ── Relaciones ─────────────────────────────────────────
 
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
 
    public function cuarentenas()
    {
        return $this->hasMany(AreaCuarentena::class, 'id_lote', 'id_lote');
    }
 
    // ── Scopes ─────────────────────────────────────────────
 
    public function scopeVigentes($query)
    {
        return $query->where('fecha_vencimiento', '>', now());
    }
 
    public function scopeConStock($query)
    {
        return $query->where('stock', '>', 0);
    }
 
    // ── Accesores ──────────────────────────────────────────
 
    public function getEstaVencidoAttribute(): bool
    {
        return $this->fecha_vencimiento && $this->fecha_vencimiento->isPast();
    }
 
    public function getDiasParaVencerAttribute(): int
    {
        return $this->fecha_vencimiento
            ? (int) now()->diffInDays($this->fecha_vencimiento, false)
            : 0;
    }
}