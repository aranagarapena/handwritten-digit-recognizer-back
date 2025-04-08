<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Responses\ApiErrorResponse;
use App\Responses\ApiSuccessResponse;
use App\Models\Drawing;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;


class NumberDrawerController extends Controller
{
    public function storeNumber(Request $request){

        Validator::extend('base64_image', function ($attribute, $value, $parameters, $validator) {
            if (preg_match('/^data:image\/(\w+);base64,/', $value, $type)) {
                $data = substr($value, strpos($value, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif
                
                if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                    return false;
                }
    
                $data = base64_decode($data);
                if ($data === false) {
                    return false;
                }
                return true;
            }
    
            return false;
        });

        // Reglas de validación
        $rules = [
            'metadata' => 'required|array',
            'image' => 'required|base64_image'
        ];

        // Mensajes de error personalizados
        $messages = [
            'image.base64_image' => 'El campo imagen debe ser una imagen base64 válida.',
        ];

        // Crear el validador
        $validator = Validator::make($request->all(), $rules, $messages);

        // Verificar si la validación falló
        if ($validator->fails()) {
            // return response()->json($validator->errors(), 422);
            $errorResponse = new ApiErrorResponse(
                'No sé ha podido descifrar la imagen debido a un error en el contenido de la misma', // message
                $validator->errors(), // errors
                422, // code
                $request->url() //endpoint
            );
            // Si la validación falla, devuelve un error 422 con los mensajes de error
            return response()->json($errorResponse->toArray(), $errorResponse->code);
        }

        try {
            DB::beginTransaction();

            // recogemos la imagen del request
            $imageData = $request->image;
            $metadata = $request->metadata;

            list(, $imageData) = explode(',', $imageData);
            $imageData = base64_decode($imageData);

            // creamos el directorio de almacenamiento en caso de que no exista
            $directory = 'images';  // Ruta relativa dentro de storage/app
            if (!Storage::disk('local')->exists($directory)) {
                Storage::disk('local')->makeDirectory($directory);
            }
            
            // guardamos la imagen en el directorio
            $filename = 'images/' . Carbon::now()->format(config('formats.image_name')) . '.png'; 
            Storage::disk('local')->put($filename, $imageData);

            $drawing = new Drawing([
                'image_name' => $filename,
                'label' => $metadata['label'],
                'user_id' => $metadata['userId'] ?? null
            ]);
            $drawing->save();
            DB::commit();

            self::escribirCSV($drawing);

            return response()->json(['message' => 'Imagen guardada con exito'], 201);

        } 
        catch (\Exception $exception) {
            DB::rollBack();        
            return response()->json(['error' => 'Ocurrió un error inesperado.', 'details' => $exception->getMessage()], 500);
        }
        
    }

    // escribimos los datos de la imagen en un fichero
    private function escribirCSV($drawing){
        
        // Especificar la ruta del archivo CSV 
        $csvPath = storage_path('app/images/drawings.csv');

        // Abrir el archivo CSV o crearlo si no existe
        $fileHandle = fopen($csvPath, 'a'); // 'a' es para modo append

        // Encabezados para el CSV, solo añadir si el archivo está vacío
        if (fstat($fileHandle)['size'] === 0) {
            fputcsv($fileHandle, ['id', 'image_name', 'label', 'user_id', 'created_at']);
        }

        // Datos para añadir al CSV
        $data = [
            $drawing->id,
            $drawing->image_name,
            $drawing->label,
            $drawing->user_id,
            $drawing->created_at
        ];

        // Escribir la línea en el CSV
        fputcsv($fileHandle, $data);

        // Cerrar el archivo
        fclose($fileHandle);
    }
}
