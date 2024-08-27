<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeccionNoticia extends Model
{
    use HasFactory;
    // 1.- indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'seccion_noticias';


    protected $fillable = [
        'seccion',
        'descripcion'
    ];

    // RELACIONES
    /**
     * Obtiene el usuario al que pertenece esta sección de noticias.
     */
    public function user() // Cambié "users" a "user" porque pertenece a un solo usuario
    {
        return $this->belongsTo(User::class); // La sección de noticias pertenece a un usuario.(Recibe)
    }

    /**
     * Obtiene todas las noticias asociadas con esta sección de noticias.
     */
    public function noticias()
    {
        return $this->hasMany(Noticia::class); // La sección de noticias tiene muchas noticias.(Envia)
    }
}
