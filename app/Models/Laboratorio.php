<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laboratorio extends Model
{
    protected $table = 'laboratorio'; // nombre exacto de la tabla

    protected $primaryKey = 'id_laboratorio'; // clave primaria

    public $timestamps = false; // no tienes created_at ni updated_at

    protected $fillable = [
        'nombre',
        'telefono',
        'correo',
        'direccion',
        'avatar'
    ];
    public function lotes()
    {
        return $this->hasMany(LoteProducto::class, 'id_laboratorio');
    }
    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_laboratorio');
    }
}