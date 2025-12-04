<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Rutas de vistas
    |--------------------------------------------------------------------------
    |
    | Aquí Laravel buscará las vistas Blade de tu aplicación.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Ruta de vistas compiladas
    |--------------------------------------------------------------------------
    |
    | Aquí se almacenan las vistas Blade compiladas. Normalmente es
    | storage/framework/views. Si este valor es null, los comandos
    | view:clear / optimize:clear revienten con "View path not found".
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

];
