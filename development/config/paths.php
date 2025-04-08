<?php

return [
    'user_images' => env('USER_IMAGES_PATH', storage_path('C:/Users/admin/Documents/drawing-numbers/')), // ! CHECK: Aquí hay una ruta absoluta del servidor, esto habría que modificar en producción
    'temp_files' => env('TEMP_FILES_PATH', storage_path('app/temp')),
];
