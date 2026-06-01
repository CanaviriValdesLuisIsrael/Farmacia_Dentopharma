<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaCuarentena extends Model
{
    protected $table = 'area_cuarentena';

    protected $primaryKey = 'id_area';

    public $timestamps = false;

    protected $fillable = [
        'fecha_ingreso',
        'cantidad',
        'id_lote'
    ];

    /**
     * Relación con lote
     */
    public function lote()
    {
        return $this->belongsTo(LoteProducto::class, 'id_lote', 'id_lote');
    }
}
