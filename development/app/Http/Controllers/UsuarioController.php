<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Responses\ApiErrorResponse;
use App\Responses\ApiSuccessResponse;


class UsuarioController extends Controller
{
    
    public function store(Request $request){
        
        // Reglas de validación
        $reglas = [
            'nombre' => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
            'apellido1' => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
            'apellido2' => 'required|string|max:255|regex:/^[\pL\s\-]+$/u',
            'email' => 'required|string|email|max:255|unique:users',
            'dni' => 'required|string|regex:/^[0-9]{8}[A-Za-z]$/|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8', // Asumiendo que deseas al menos 8 caracteres
        ];

        // Mensajes de error personalizados
        $mensajes = [
            'required' => 'El campo :attribute es obligatorio.',
            'string' => 'El campo :attribute debe ser una cadena de texto.',
            'max' => 'El campo :attribute no debe tener más de :max caracteres.',
            'email' => 'El campo :attribute debe ser una dirección de correo electrónico válida.',
            'unique' => 'El :attribute ya está en uso.',
            'regex' => 'El formato del :attribute es inválido. Solo se permiten caracteres alfabéticos.',
            'min' => 'El campo :attribute debe tener al menos :min caracteres.',
        ];
        
        
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), $reglas, $mensajes);
        
        if ($validator->fails()) {

            $errorResponse = new ApiErrorResponse(
                'No se ha podido crear el usuario debido a un error semántico', // message
                $validator->errors(), // errors
                422, // code
                $request->url() //endpoint
            );
            // Si la validación falla, devuelve un error 422 con los mensajes de error
            return response()->json($errorResponse->toArray(), $errorResponse->code);

        }
        
        // si la validación es exitosa recogemos los datos
        $datos = array (
            "nombre"=>$request->input("nombre"),
            "apellido1"=>$request->input("apellido1"),
            "apellido2"=>$request->input("apellido2"),
            "email"=>$request->input("email"),
            "dni"=>$request->input("dni"),
            "username"=>$request->input("username"),
            "password"=>$request->input("password"),
        );
        
        try {
            // Inicia una transacción de base de datos
            DB::beginTransaction();
        
            $id_usuario = Hash::make($datos['nombre'].$datos['apellido1'].$datos['email']);
            $clave_usuario = Hash::make($datos['email'].$datos['apellido1'].$datos['nombre'],['rounds'=>12]);
            
            $user = new User;
            $user->nombre = $datos['nombre'];
            $user->apellido1 = $datos['apellido1'];
            $user->apellido2 = $datos['apellido2'];
            $user->username = $datos['username'];
            $user->email = $datos['email'];
            $user->dni = $datos['dni'];
            $user->password = $datos['password']; // Es recomendable encriptar la contraseña antes de guardarla, se puede usar bcrypt() por ejemplo
            $user->id_usuario = $id_usuario; 
            $user->clave_usuario = $clave_usuario; 
            $user->save();
        
            // Si todo ha ido bien, confirma la transacción
            DB::commit();
        
            // Devuelve una respuesta exitosa
            return response()->json(['message' => 'Usuario creado con éxito. Aquí tienes tus credenciales', 'id_usuario'=>$id_usuario, 'clave'=>$clave_usuario], 201);

        } catch (QueryException $exception) {

            // Si algo sale mal, se revierte la transaccion y devolvemos un error
            DB::rollBack();        
            return response()->json(['error' => 'No se pudo crear el usuario debido a un error en la base de datos.', 'details' => $exception->getMessage()], 500);
        
        } catch (\Exception $exception) {

            DB::rollBack();        
            return response()->json(['error' => 'Ocurrió un error inesperado.', 'details' => $exception->getMessage()], 500);
        }
        
    }
    public function getAllUsers(){
        
        $users = User::all();
        $userJson = json_encode($users);

        return response()->json(['details' => 'Consulta a getAllUsers realizada con exito.', 'data'=>$users], 200);

    }
}
