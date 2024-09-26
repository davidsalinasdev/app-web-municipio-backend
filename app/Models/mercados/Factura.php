<?php

namespace App\Models\mercados;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    // 1.- Indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'facturas';


    /**
     * Se dirige detalle_facturas
     */
    public function detalleFactura()
    {
        return $this->hasMany(Detalle_factura::class); // Se dirige a Detalle_factura
    }

    /**
     * Se dirige pagos
     */
    public function pago()
    {
        return $this->hasMany(Pago::class); // Se dirige a pagos
    }

    /**
     * Recibe a puesto
     */
    public function puesto()
    {
        return $this->belongsTo(Puesto::class); // Recibe a puesto
    }
}
