<?php

/*
|--------------------------------------------------------------------------
| INDEX.PHP OPTIMIZADO PARA IPAGE
|--------------------------------------------------------------------------
| Versión simplificada para resolver problemas en servidores compartidos
*/

// Manejo de errores robusto
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
ini_set('display_errors', 0); // Desactivado para producción
ini_set('log_errors', 1);

// Definir el tiempo de inicio
define('LARAVEL_START', microtime(true));

// Verificar si la aplicación está en mantenimiento
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Verificar que el autoloader existe
$autoloader_path = __DIR__.'/../vendor/autoload.php';
if (!file_exists($autoloader_path)) {
    http_response_code(500);
    die('Error: No se encontró el autoloader de Composer. Ejecute "composer install".');
}

// Cargar el autoloader
require $autoloader_path;

// Verificar que el bootstrap existe
$bootstrap_path = __DIR__.'/../bootstrap/app.php';
if (!file_exists($bootstrap_path)) {
    http_response_code(500);
    die('Error: No se encontró el archivo bootstrap de Laravel.');
}

try {
    // Cargar la aplicación Laravel
    $app = require_once $bootstrap_path;

    // Crear el kernel HTTP
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    // Capturar la petición
    $request = Illuminate\Http\Request::capture();

    // Procesar la petición
    $response = $kernel->handle($request);

    // Enviar la respuesta
    $response->send();

    // Terminar el kernel
    $kernel->terminate($request, $response);

} catch (Exception $e) {
    // Log del error
    error_log('Laravel Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    
    // Respuesta de error genérica para producción
    http_response_code(500);
    echo '<!DOCTYPE html>
<html>
<head>
    <title>Error del Servidor</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
        .error { color: #e74c3c; }
    </style>
</head>
<body>
    <h1>Error del Servidor</h1>
    <p class="error">La aplicación no pudo iniciarse correctamente.</p>
    <p>Por favor, contacte al administrador del sistema.</p>
</body>
</html>';
}
?>
