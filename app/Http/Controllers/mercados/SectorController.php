<?php

namespace App\Http\Controllers\mercados;

use App\Http\Controllers\Controller;
use App\Models\mercados\Sector;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SectorController extends Controller
{
    // Busqueda con data tables
    function indexPOST()
    {

        return datatables()->eloquent(Sector::query())->filter(function ($query) {
            if (request()->has('search') && request('search')) {
                $searchTerm = request('search');
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('sector', 'like', '%' . $searchTerm . '%')
                        ->orWhere('descripcion', 'like', '%' . $searchTerm . '%');
                });
            }
        })->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // 1.-Recoge datos por post
        $params = (object) $request->all(); // Devuelve un objeto

        $validate = Validator::make($request->all(), [

            'mercado' => 'required',
            'sector' => 'required',
            'descripcion' => 'required',
            'usuario_id' => 'required'

        ]);

        // 3.- SI LA VALIDACION FUE CORRECTA
        // Comprobar si los datos son validos
        if ($validate->fails()) { // en caso si los datos fallan la validacion

            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Los datos enviados no son correctos.',
                'errors' => $validate->errors()
            );
            return response()->json($data, $data['code']);
        } else {

            // Iniciar una transacción
            DB::beginTransaction();

            try {

                // Crear el usuario y guardarlo en la base de datos
                $sector = new Sector();
                $sector->mercado = $params->mercado;
                $sector->sector = $params->sector;
                $sector->descripcion = $params->descripcion;
                $sector->usuario_id = $params->usuario_id;
                $sector->save();

                // Commit de la transacción
                DB::commit();

                // Retornar una respuesta exitosa
                $data = array(
                    'status' => 'success',
                    'code' => 201,
                    'message' => 'Sector registrado correctamente',
                    'sector' => $sector
                );
            } catch (Exception $e) {
                // Rollback de la transacción en caso de error
                DB::rollBack();

                // Manejo de excepciones
                $data = array(
                    'status' => 'Error',
                    'code' => 500,
                    'message' => 'Error al registrar al sector',
                    'error' => $e->getMessage()
                );
            }
            return response()->json($data, $data['code']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Titular  $titular
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $sector = Sector::findOrFail($id);

            $data = array(
                'code' => 200,
                'status' => 'success',
                'sector' => $sector
            );
        } catch (ModelNotFoundException $e) {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'Sector de mercado no encontrado'
            );
        } catch (Exception $e) {
            $data = array(
                'code' => 500,
                'status' => 'error',
                'message' => 'Ocurrió un error al intentar recuperar el sector de mercado',
                'error' => $e->getMessage()
            );
        }
        return response()->json($data, $data['code']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Titular  $titular
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Recoge datos por PUT o PATCH
        $params = (object) $request->all(); // Devuelve un objeto

        $validate = Validator::make($request->all(), [

            'mercado' => 'required',
            'sector' => 'required',
            'descripcion' => 'required',
            'estado' => 'required'
        ]);

        // 3.- SI LA VALIDACION FUE CORRECTA
        // Comprobar si los datos son validos
        if ($validate->fails()) { // en caso si los datos fallan la validacion

            $data = array(
                'status' => 'error',
                'code' => 400,
                'message' => 'Los datos enviados no son correctos.',
                'errors' => $validate->errors()
            );
            return response()->json($data, $data['code']);
        } else {

            // Iniciar una transacción
            DB::beginTransaction();

            try {


                // Buscar la persona en la base de datos
                $sector = Sector::findOrFail($id);
                // Actualizar los datos de la seccion
                $sector->mercado = $params->mercado;
                $sector->sector = $params->sector;
                $sector->descripcion = $params->descripcion;
                $sector->estado = $params->estado;
                $sector->save();

                // Commit de la transacción
                DB::commit();

                // Retornar una respuesta exitosa
                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El sector se actualizo correctamente',
                    'sector' => $sector
                );
            } catch (ModelNotFoundException $e) {
                // Rollback de la transacción en caso de que no se encuentre la persona
                DB::rollBack();

                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'Ocurrio un error al intentar actualizar el sector del mercado',
                );
            } catch (Exception $e) {
                // Rollback de la transacción en caso de cualquier otro error
                DB::rollBack();

                $data = array(
                    'status' => 'error',
                    'code' => 500,
                    'message' => 'Ocurrió un error al actualizar el sector',
                    'error' => $e->getMessage()
                );
            }
            return response()->json($data, $data['code']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Titular  $titular
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Iniciar una transacción
        DB::beginTransaction();

        try {
            // Buscar la persona en la base de datos
            $sector = Sector::findOrFail($id);

            // Actualizar el estado de la seccion a "inactivo"
            $sector->estado = 0;
            $sector->save();

            // Commit de la transacción
            DB::commit();

            // Retornar una respuesta exitosa
            $data = array(
                'status' => 'success',
                'code' => 200,
                'message' => 'Se desactivó correctamente',
                'sector' => $sector
            );
        } catch (ModelNotFoundException $e) {
            // Rollback de la transacción en caso de que no se encuentre la persona
            DB::rollBack();

            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'sector no encontrado',
            );
        } catch (Exception $e) {
            // Rollback de la transacción en caso de cualquier otro error
            DB::rollBack();
            $data = array(
                'status' => 'error',
                'code' => 500,
                'message' => 'Ocurrió un error al desactivar el sector del mercado',
                'error' => $e->getMessage()
            );
        }
        return response()->json($data, $data['code']);
    }
}
