<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
 
class Caja extends Model
{
    protected $table = 'caja';
    protected $primaryKey = 'id_caja';
    public $timestamps = false;
 
    protected $fillable = [
        'fecha_apertura',
        'fecha_cierre',
        'saldo_inicial',
        'saldo_actual',
        'saldo_final',
        'estado',
        'id_empleado',
        'observacion',
    ];
 
    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre'   => 'datetime',
        'saldo_inicial'  => 'decimal:2',
        'saldo_actual'   => 'decimal:2',
        'saldo_final'    => 'decimal:2',
    ];
 
    // ── Relaciones ─────────────────────────────────────────
 
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }
 
    public function movimientos()
    {
        return $this->hasMany(MovimientoCaja::class, 'id_caja');
    }
 
    public function compras()
    {
        return $this->hasMany(Compra::class, 'id_caja');
    }
 
    // ── Scopes ─────────────────────────────────────────────
 
    public function scopeAbierta($query)
    {
        return $query->where('estado', 'abierta');
    }
 
    // ── Helpers ────────────────────────────────────────────
 
    /**
     * Devuelve la caja abierta actualmente, o null si no hay ninguna
     */
    public static function cajaActual(): ?self
    {
        return static::where('estado', 'abierta')
                     ->orderByDesc('fecha_apertura')
                     ->first();
    }
 
    public function estaAbierta(): bool
    {
        return $this->estado === 'abierta';
    }
 
    /**
     * Suma de todos los ingresos de esta caja
     */
    public function getTotalIngresosAttribute(): float
    {
        return (float) $this->movimientos()
            ->whereIn('tipo', ['ingreso'])
            ->sum('monto');
    }
 
    /**
     * Suma de todos los egresos de esta caja
     */
    public function getTotalEgresosAttribute(): float
    {
        return (float) $this->movimientos()
            ->whereIn('tipo', ['egreso'])
            ->sum('monto');
    }
}
