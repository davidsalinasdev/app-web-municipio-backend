<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * El error 403 THIS ACTION IS UNAUTHORIZED se debe a que el método authorize()
     * en tu LoginRequest está devolviendo false. Este método determina si el 
     * usuario está autorizado para realizar la solicitud, y al devolver false, 
     * estás indicando que ninguna solicitud está autorizada.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Reglas de validación
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ];
    }
}
