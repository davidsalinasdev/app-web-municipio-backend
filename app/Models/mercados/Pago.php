<?php

namespace App\Models\mercados;

use App\Models\mercados\Periodo_pagado;
use App\Models\mercados\Puesto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;


    // 1.- indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'pagos';

    // RELACIONES
    /**
     * Recibe a Factura.
     */
    public function factura()
    {
        return $this->belongsTo(Factura::class); // Recibe a factura
    }


    /**
     * Recibe a puesto
     */
    public function puesto()
    {
        return $this->belongsTo(Puesto::class); // Recibe a Puesto
    }

    /**
     * Recibe a multa
     */
    public function multa()
    {
        return $this->belongsTo(Multa::class); // Recibe a Multa
    }

    /**
     * Recibe a user
     */
    public function user()
    {
        return $this->belongsTo(User::class); // Recibe a usuario
    }


    /**
     * Se dirige periodoPagado
     */
    public function periodoPagado()
    {
        return $this->hasMany(Periodo_pagado::class); // Se dirige a periodo_pagado
    }
}
