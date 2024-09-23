<?php

namespace App\Models\mercados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detalle_factura extends Model
{
    use HasFactory;

    // 1.- Indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'detalle_facturas';

    // RELACIONES
    /**
     * Recibe a Factura.
     */
    public function factura()
    {
        return $this->belongsTo(Factura::class); // Recibe a factura
    }

    public function puesto()
    {
        return $this->belongsTo(Puesto::class); // Recibe a puesto
    }
}
