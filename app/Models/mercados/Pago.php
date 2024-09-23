<?php

namespace App\Models\mercados;

use App\Models\mercados\Periodo_pagado;
use App\Models\mercados\Puesto;
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
        return $this->belongsTo(Puesto::class); // Recibe a factura
    }

    /**
     * Se dirige periodoPagado
     */
    public function periodoPagado()
    {
        return $this->hasMany(Periodo_pagado::class); // Se dirige a periodo_pagado
    }
}
