<?php

namespace App\Models\mercados;

use App\Models\Multa;
use App\Models\Pago;
use App\Models\User;
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
    public function usuario() // Cambié "users" a "user" porque pertenece a un solo usuario
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


    /**
     * Recibe a titular
     */
    public function titular()
    {
        return $this->belongsTo(Titular::class); // Recibe a Titular
    }

    /**
     * Recibe a sector
     */
    public function sector()
    {
        return $this->belongsTo(Sector::class); // Recibe a Sector
    }
}
