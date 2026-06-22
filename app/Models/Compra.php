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
        'tipo_pago',
        'estado',
        'estado_pago',
        'fecha_pago',
        'nota',
        'id_empleado',
        'id_caja',
    ];
 
    protected $casts = [
        'fecha_compra' => 'date',
        'fecha_pago'   => 'date',
        'total_compra' => 'decimal:2',
        'descuento'    => 'decimal:2',
    ];
 
    // ── Helpers de estado de pago ───────────────────────────
 
    public function estaPendiente(): bool
    {
        return $this->tipo_pago === 'credito' && $this->estado_pago === 'pendiente';
    }
 
    // ── Relaciones ─────────────────────────────────────────
 
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
 
    public function movimientosCaja()
    {
        return $this->hasMany(MovimientoCaja::class, 'id_compra');
    }
 
    public function lotes()
    {
        return $this->hasMany(LoteProducto::class, 'id_compra');
    }
 
    // ── Scopes ─────────────────────────────────────────────
 
    public function scopeRegistradas($query)
    {
        return $query->where('estado', 'registrada');
    }
 
    public function scopeDelPeriodo($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha_compra', [$desde, $hasta]);
    }
 
    // ── Accesores ──────────────────────────────────────────
 
    public function getTotalConDescuentoAttribute(): float
    {
        return (float) $this->total_compra - (float) $this->descuento;
    }
}