<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Puesto extends Model
{
    use HasFactory;


    // 1.- indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'puestos';

    // RELACIONES
    /**
     * Obtiene el usuario al que pertenece este puesto.
     */
    public function user() // Cambié "users" a "user" porque pertenece a un solo usuario
    {
        return $this->belongsTo(User::class); // El puesto registrado pertenece a un usuario.(Recibe)
    }


    /**
     * Se dirige hacia PAGO
     */
    public function pago()
    {
        return $this->hasMany(Pago::class); //Se dirige Pago
    }


    /**
     * Se dirige hacia Multa
     */
    public function multa()
    {
        return $this->hasMany(Multa::class); //Se dirige Multa
    }
}
