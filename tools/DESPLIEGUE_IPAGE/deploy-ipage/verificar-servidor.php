<?php
// SCRIPT DE VERIFICACIÓN EN EL SERVIDOR iPAGE
echo "🔍 VERIFICANDO INSTALACIÓN EN iPAGE\n";
echo "===================================\n\n";

// 1. Verificar PHP
echo "PHP Version: " . phpversion() . "\n";

// 2. Verificar extensiones necesarias
$extensions = ["pdo", "mbstring", "openssl", "tokenizer", "xml", "ctype", "json"];
foreach ($extensions as $ext) {
    echo "Extensión $ext: " . (extension_loaded($ext) ? "✅" : "❌") . "\n";
}

// 3. Verificar permisos
$paths = ["storage/", "bootstrap/cache/"];
foreach ($paths as $path) {
    $writeable = is_writable($path);
    echo "Permisos $path: " . ($writeable ? "✅" : "❌") . "\n";
}

// 4. Verificar base de datos
try {
    $pdo = new PDO("mysql:host=" . $_ENV["DB_HOST"] . ";dbname=" . $_ENV["DB_DATABASE"], $_ENV["DB_USERNAME"], $_ENV["DB_PASSWORD"]);
    echo "Conexión BD: ✅\n";
} catch (Exception $e) {
    echo "Conexión BD: ❌ " . $e->getMessage() . "\n";
}

// 5. Verificar configuración
if (file_exists(".env")) {
    echo "Archivo .env: ✅\n";
} else {
    echo "Archivo .env: ❌\n";
}

echo "\n🎯 SIGUIENTE PASO: Ejecutar migraciones\n";
echo "php artisan migrate --force\n";
?>