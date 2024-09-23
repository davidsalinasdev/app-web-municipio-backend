<?php

namespace App\Models\mercados;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Generar extends Model
{
    // 1.- indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'generars';

    // RELACIONES
    /**
     * Recibe a User.
     */
    public function user()
    {
        return $this->belongsTo(User::class); // Recibe a user
    }
}
