<?php

namespace App\Models\mercados;

use App\Models\Puesto;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    use HasFactory;

    // 1.- indicamos la tabla que va a utilizar de la base de datos
    protected $table = 'sectors';


    // RELACIONES
    /**
     * Obtiene el usuario al que pertenece este titular de puesto.
     */
    public function user() // Cambié "users" a "user" porque pertenece a un solo usuario
    {
        return $this->belongsTo(User::class); // El titular registrado pertenece a un usuario.(Recibe)
    }


    /**
     * Obtiene todas los puestos asociadas  al titular.
     */
    public function puesto()
    {
        return $this->hasMany(Puesto::class); // Se dirige hacia puestos (Envia);
    }
}
