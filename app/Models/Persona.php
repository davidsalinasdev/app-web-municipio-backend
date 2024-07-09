<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    // 1.- indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'personas';


    // RELACIONES

    /**
     * Obtener los usuarios para la persona.
     * Saca todos los usuarios relacionados con la persona
     */
    public function users()
    {
        return $this->hasMany(User::class); // se dirige hacia users
    }
}
