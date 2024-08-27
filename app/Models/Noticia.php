<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    use HasFactory;

    // 1.- indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'noticias';

    // RELACIONES
    /**
     * Obtiene la sección de noticias a la que pertenece esta noticia.
     */
    public function seccion_noticia()
    {
        return $this->belongsTo(SeccionNoticia::class); // Esta noticia pertenece a una sección de noticias.
    }
}
