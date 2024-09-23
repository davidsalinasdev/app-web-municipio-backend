<?php

namespace App\Models\mercados;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periodo_pagado extends Model
{
    use HasFactory;

    // 1.- indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'periodo_pagados';

    /**
     * Recibe a Pago.
     */
    public function pago()
    {
        return $this->belongsTo(Pago::class); // Recibe a pago
    }
}
