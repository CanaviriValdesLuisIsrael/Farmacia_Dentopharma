<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class MovimientoCaja extends Model
{
    protected $table = 'movimiento_caja';
    protected $primaryKey = 'id_movimiento';
    public $timestamps = false;
 
    protected $fillable = [
        'id_caja',
        'tipo',
        'monto',
        'descripcion',
        'fecha',
        'hora',
        'id_empleado',
        'id_venta',
        'id_compra',
    ];
 
    protected $casts = [
        'monto' => 'decimal:2',
        'fecha' => 'date',
    ];
 
    // ── Constantes de tipo ──────────────────────────────────
 
    const TIPO_INGRESO  = 'ingreso';
    const TIPO_EGRESO   = 'egreso';
    const TIPO_APERTURA = 'apertura';
    const TIPO_CIERRE   = 'cierre';
 
    // ── Relaciones ─────────────────────────────────────────
 
    public function caja()
    {
        return $this->belongsTo(Caja::class, 'id_caja');
    }
 
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'id_empleado');
    }
 
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'id_venta');
    }
 
    public function compra()
    {
        return $this->belongsTo(Compra::class, 'id_compra');
    }
 
    // ── Scopes ─────────────────────────────────────────────
 
    public function scopeIngresos($query)
    {
        return $query->where('tipo', self::TIPO_INGRESO);
    }
 
    public function scopeEgresos($query)
    {
        return $query->where('tipo', self::TIPO_EGRESO);
    }
 
    public function scopeDelDia($query, $fecha = null)
    {
        return $query->where('fecha', $fecha ?? now()->toDateString());
    }
 
    // ── Factory methods ─────────────────────────────────────
 
    /**
     * Registra un movimiento y actualiza el saldo_actual de la caja
     */
    public static function registrar(Caja $caja, array $datos): self
    {
        $movimiento = static::create(array_merge($datos, [
            'id_caja' => $caja->id_caja,
            'fecha'   => $datos['fecha'] ?? now()->toDateString(),
            'hora'    => $datos['hora']  ?? now()->toTimeString(),
        ]));
 
        // Actualizar saldo_actual de la caja
        if ($datos['tipo'] === self::TIPO_INGRESO) {
            $caja->increment('saldo_actual', $datos['monto']);
        } elseif ($datos['tipo'] === self::TIPO_EGRESO) {
            $caja->decrement('saldo_actual', $datos['monto']);
        }
 
        return $movimiento;
    }
}
