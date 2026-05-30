<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Empleado extends Model
{
    protected $table = 'empleado';
    
    protected $primaryKey = 'id_empleado'; // Clave primaria real
    public $incrementing = true;           // Si es autoincremental
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'ci',
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'sexo',
        'direccion',
        'nro_contacto',
        'cargo',
        'turno',
        'salario',
        
    ];
    //  Calcular la edad automáticamente
    public function getEdadAttribute()
    {
        if (!$this->fecha_nacimiento) {
            return null;
        }

        return Carbon::parse($this->fecha_nacimiento)->age;
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id_empleado', 'id_empleado');
    }
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'id_empleado');
    }

    public function compras()
    {
        return $this->hasMany(Compra::class, 'id_empleado');
    }
}


