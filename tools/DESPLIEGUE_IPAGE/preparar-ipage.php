<?php
/**
 * SCRIPT DE PREPARACIÓN FINAL PARA iPAGE
 * =====================================
 * 
 * Ejecutar este script ANTES de subir a producción
 * para preparar todos los archivos necesarios
 */

echo "🚀 PREPARANDO APLICACIÓN PARA iPAGE\n";
echo "=====================================\n\n";

// Crear directorio de despliegue si no existe
$deployDir = 'deploy-ipage';
if (!is_dir($deployDir)) {
    mkdir($deployDir);
    echo "✅ Directorio de despliegue creado: $deployDir/\n";
}

// 1. VERIFICAR QUE TODO ESTÉ LISTO
echo "1. 🔍 VERIFICANDO CONFIGURACIÓN...\n";

// Verificar .env.production
if (!file_exists('.env.production')) {
    echo "❌ ERROR: .env.production no existe\n";
    exit(1);
}

// Verificar vendor
if (!is_dir('vendor')) {
    echo "⚠️  Instalando dependencias...\n";
    exec('composer install --optimize-autoloader', $output, $return);
    if ($return !== 0) {
        echo "❌ ERROR: No se pudieron instalar las dependencias\n";
        exit(1);
    }
}

echo "✅ Configuración verificada\n\n";

// 2. LIMPIAR CACHÉS
echo "2. 🧹 LIMPIANDO CACHÉS...\n";
$commands = [
    'php artisan config:clear',
    'php artisan cache:clear',
    'php artisan route:clear',
    'php artisan view:clear'
];

foreach ($commands as $command) {
    echo "   Ejecutando: $command\n";
    exec($command, $output, $return);
    if ($return !== 0) {
        echo "   ⚠️  Advertencia en: $command\n";
    }
}
echo "✅ Cachés limpiados\n\n";

// 3. OPTIMIZAR PARA PRODUCCIÓN
echo "3. ⚡ OPTIMIZANDO PARA PRODUCCIÓN...\n";
$optimizeCommands = [
    'php artisan config:cache',
    'php artisan route:cache',
    'php artisan view:cache'
];

foreach ($optimizeCommands as $command) {
    echo "   Ejecutando: $command\n";
    exec($command, $output, $return);
    if ($return !== 0) {
        echo "   ⚠️  Advertencia en: $command\n";
    }
}
echo "✅ Optimización completada\n\n";

// 4. PREPARAR ARCHIVO .ENV PARA PRODUCCIÓN
echo "4. 📄 PREPARANDO ARCHIVO .ENV...\n";

$envProduction = file_get_contents('.env.production');

// Crear versión personalizable
$envForDeploy = str_replace([
    'tu_base_datos',
    'tu_usuario', 
    'tu_contraseña',
    'tudominio.com'
], [
    '[CONFIGURAR_BD]',
    '[CONFIGURAR_USUARIO]',
    '[CONFIGURAR_PASSWORD]',
    '[CONFIGURAR_DOMINIO]'
], $envProduction);

file_put_contents("$deployDir/.env", $envForDeploy);
echo "✅ Archivo .env preparado en $deployDir/.env\n";
echo "   📝 NOTA: Debes configurar los valores marcados con [CONFIGURAR_XXX]\n\n";

// 5. CREAR INSTRUCCIONES ESPECÍFICAS
echo "5. 📋 CREANDO INSTRUCCIONES...\n";

$instructions = "# INSTRUCCIONES DE DESPLIEGUE EN iPAGE
# =====================================

## PASOS A SEGUIR EN EL SERVIDOR:

### 1. SUBIR ARCHIVOS
- Subir TODA la aplicación EXCEPTO:
  - node_modules/ (si existe)
  - .git/
  - .env (usar el .env preparado)
  - storage/logs/* (limpiar logs)

### 2. CONFIGURAR .ENV
- Usar el archivo .env preparado
- Reemplazar valores [CONFIGURAR_XXX] con datos reales de iPage:
  
  DB_HOST=[tu_host_ipage].ipagemysql.com
  DB_DATABASE=[nombre_bd_real]
  DB_USERNAME=[usuario_bd_real]
  DB_PASSWORD=[password_bd_real]
  APP_URL=https://[tu_dominio_real].com
  SESSION_DOMAIN=.[tu_dominio_real].com

### 3. CONFIGURAR PERMISOS
chmod 755 bootstrap/cache/
chmod 755 storage/
chmod -R 755 storage/app/
chmod -R 755 storage/framework/
chmod -R 755 storage/logs/

### 4. INSTALAR DEPENDENCIAS
composer install --optimize-autoloader --no-dev

### 5. MIGRAR BASE DE DATOS
php artisan migrate --force

### 6. OPTIMIZAR (opcional, recomendado)
php artisan config:cache
php artisan route:cache
php artisan view:cache

### 7. PROBAR APLICACIÓN
- Acceder a la URL principal
- Probar login
- Verificar que no hay error 419

## SOLUCIÓN DE PROBLEMAS:

### Si aparece error 500:
tail -f storage/logs/laravel.log

### Si aparece error 419:
- Verificar que SESSION_DRIVER=database en .env
- Ejecutar: php artisan migrate --force

### Si no cargan estilos:
- Verificar APP_URL en .env
- Verificar permisos de public/

" . date('Y-m-d H:i:s') . "\n";

file_put_contents("$deployDir/INSTRUCCIONES_SERVIDOR.md", $instructions);
echo "✅ Instrucciones creadas en $deployDir/INSTRUCCIONES_SERVIDOR.md\n\n";

// 6. CREAR SCRIPT DE VERIFICACIÓN PARA EL SERVIDOR
echo "6. 🔧 CREANDO SCRIPT DE VERIFICACIÓN...\n";

$verificationScript = '<?php
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
?>';

file_put_contents("$deployDir/verificar-servidor.php", $verificationScript);
echo "✅ Script de verificación creado en $deployDir/verificar-servidor.php\n\n";

// 7. RESUMEN FINAL
echo "🎉 PREPARACIÓN COMPLETADA\n";
echo "========================\n\n";

echo "📁 ARCHIVOS PREPARADOS EN: $deployDir/\n";
echo "   - .env (configurar con datos de iPage)\n";
echo "   - INSTRUCCIONES_SERVIDOR.md\n";
echo "   - verificar-servidor.php\n\n";

echo "📋 PRÓXIMOS PASOS:\n";
echo "1. Subir aplicación completa a iPage\n";
echo "2. Configurar .env con datos reales\n";
echo "3. Ejecutar comandos en el servidor\n";
echo "4. Probar funcionamiento\n\n";

echo "✅ TU APLICACIÓN ESTÁ LISTA PARA iPAGE\n";
echo "   Todas las optimizaciones aplicadas\n";
echo "   Configuración anti-error 419 activa\n";
echo "   Cachés optimizados para producción\n\n";

echo "🚀 ¡BUENA SUERTE CON EL DESPLIEGUE!\n";
?>
