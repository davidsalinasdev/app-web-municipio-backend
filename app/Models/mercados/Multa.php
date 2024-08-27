<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Multa extends Model
{
    use HasFactory;


    // 1.- indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'multas';

    // RELACIONES
    /**
     * Recibe a Puesto.
     */
    public function puesto()
    {
        return $this->belongsTo(Puesto::class); // Recibe a puesto
    }
}
