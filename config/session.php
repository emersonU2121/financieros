<?php

use Illuminate\Support\Str;

return [

    // 🔹 OLVIDATE DEL ENV. Forzamos archivo.
    'driver' => 'file',

    'lifetime' => env('SESSION_LIFETIME', 120),
    'expire_on_close' => false,
    'encrypt' => env('SESSION_ENCRYPT', false),

    'files' => storage_path('framework/sessions'),

    'connection' => null,   // 👈 muy importante, sin conexión a BD
    'table' => 'sessions',
    'store' => null,

    'lottery' => [2, 100],

    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug((string) env('APP_NAME', 'laravel'), '_').'_session'
    ),

    'path' => env('SESSION_PATH', '/'),
    'domain' => env('SESSION_DOMAIN'),

    'secure' => env('SESSION_SECURE_COOKIE'),
    'http_only' => true,

    'same_site' => 'lax',

];
