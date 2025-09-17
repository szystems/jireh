<?php
/*
|--------------------------------------------------------------------------
| SCRIPT DE LIMPIEZA Y OPTIMIZACIÓN PARA IPAGE
|--------------------------------------------------------------------------
*/

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>LIMPIEZA Y OPTIMIZACIÓN PARA IPAGE</h2>";
echo "<hr>";

// Función para eliminar directorio recursivamente
function eliminarDirectorio($dir) {
    if (!is_dir($dir)) {
        return false;
    }
    
    $archivos = array_diff(scandir($dir), ['.', '..']);
    
    foreach ($archivos as $archivo) {
        $ruta = $dir . DIRECTORY_SEPARATOR . $archivo;
        if (is_dir($ruta)) {
            eliminarDirectorio($ruta);
        } else {
            unlink($ruta);
        }
    }
    
    return rmdir($dir);
}

// 1. LIMPIAR CACHE DE VIEWS
echo "<h3>1. Limpiando cache de views...</h3>";
$views_cache = '../storage/framework/views';
if (is_dir($views_cache)) {
    $archivos = glob($views_cache . '/*');
    foreach ($archivos as $archivo) {
        if (is_file($archivo) && pathinfo($archivo, PATHINFO_EXTENSION) === 'php') {
            unlink($archivo);
        }
    }
    echo "✓ Cache de views limpiado<br>";
} else {
    echo "✗ Directorio de cache de views no encontrado<br>";
}

// 2. LIMPIAR CACHE DE CONFIGURACIÓN
echo "<h3>2. Limpiando cache de configuración...</h3>";
$config_cache = '../bootstrap/cache/config.php';
if (file_exists($config_cache)) {
    unlink($config_cache);
    echo "✓ Cache de configuración eliminado<br>";
} else {
    echo "- No hay cache de configuración<br>";
}

// 3. LIMPIAR CACHE DE RUTAS
echo "<h3>3. Limpiando cache de rutas...</h3>";
$routes_cache = '../bootstrap/cache/routes-v7.php';
if (file_exists($routes_cache)) {
    unlink($routes_cache);
    echo "✓ Cache de rutas eliminado<br>";
} else {
    echo "- No hay cache de rutas<br>";
}

// 4. LIMPIAR CACHE DE SERVICIOS
echo "<h3>4. Limpiando cache de servicios...</h3>";
$services_cache = '../bootstrap/cache/services.php';
if (file_exists($services_cache)) {
    unlink($services_cache);
    echo "✓ Cache de servicios eliminado<br>";
} else {
    echo "- No hay cache de servicios<br>";
}

// 5. LIMPIAR CACHE DE APLICACIÓN
echo "<h3>5. Limpiando cache de aplicación...</h3>";
$app_cache = '../storage/framework/cache';
if (is_dir($app_cache)) {
    $subdirs = ['data'];
    foreach ($subdirs as $subdir) {
        $path = $app_cache . '/' . $subdir;
        if (is_dir($path)) {
            $archivos = glob($path . '/*');
            foreach ($archivos as $archivo) {
                if (is_file($archivo)) {
                    unlink($archivo);
                }
            }
        }
    }
    echo "✓ Cache de aplicación limpiado<br>";
} else {
    echo "✗ Directorio de cache de aplicación no encontrado<br>";
}

// 6. VERIFICAR/CREAR DIRECTORIOS NECESARIOS
echo "<h3>6. Verificando directorios necesarios...</h3>";
$directorios = [
    '../storage/app',
    '../storage/app/public',
    '../storage/framework',
    '../storage/framework/cache',
    '../storage/framework/cache/data',
    '../storage/framework/sessions',
    '../storage/framework/views',
    '../storage/logs',
    '../bootstrap/cache'
];

foreach ($directorios as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✓ Directorio creado: $dir<br>";
        } else {
            echo "✗ Error creando directorio: $dir<br>";
        }
    } else {
        echo "- Directorio existe: $dir<br>";
    }
}

// 7. VERIFICAR PERMISOS
echo "<h3>7. Verificando permisos...</h3>";
$directorios_permisos = [
    '../storage',
    '../storage/app',
    '../storage/framework',
    '../storage/framework/cache',
    '../storage/framework/sessions',
    '../storage/framework/views',
    '../storage/logs',
    '../bootstrap/cache'
];

foreach ($directorios_permisos as $dir) {
    if (is_dir($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        $escribible = is_writable($dir) ? 'SÍ' : 'NO';
        echo "Directorio: $dir - Permisos: $perms - Escribible: $escribible<br>";
        
        // Intentar establecer permisos si no son correctos
        if (!is_writable($dir)) {
            chmod($dir, 0755);
            echo "- Intentando establecer permisos 755<br>";
        }
    }
}

// 8. CREAR ARCHIVO .htaccess EN STORAGE (SEGURIDAD)
echo "<h3>8. Configurando seguridad...</h3>";
$htaccess_storage = '../storage/.htaccess';
if (!file_exists($htaccess_storage)) {
    $contenido_htaccess = "Options -Indexes\nDeny from all";
    file_put_contents($htaccess_storage, $contenido_htaccess);
    echo "✓ Archivo .htaccess de seguridad creado en storage<br>";
} else {
    echo "- Archivo .htaccess ya existe en storage<br>";
}

// 9. OPTIMIZAR AUTOLOADER
echo "<h3>9. Optimizando autoloader...</h3>";
if (file_exists('../composer.json')) {
    echo "- Para optimizar ejecute: composer dump-autoload -o<br>";
    echo "- Este comando debe ejecutarse en el servidor<br>";
} else {
    echo "✗ composer.json no encontrado<br>";
}

echo "<hr>";
echo "<h3>RESUMEN DE OPTIMIZACIÓN</h3>";
echo "✓ Cache limpiado completamente<br>";
echo "✓ Directorios verificados/creados<br>";
echo "✓ Permisos verificados<br>";
echo "✓ Seguridad configurada<br>";
echo "<br>";
echo "<strong>PRÓXIMOS PASOS:</strong><br>";
echo "1. Subir archivos al servidor iPage<br>";
echo "2. Renombrar .env_ipage_optimizado a .env<br>";
echo "3. Renombrar .htaccess_ipage_basico a .htaccess<br>";
echo "4. Ejecutar crear_tabla_sesiones.php<br>";
echo "5. Probar la aplicación<br>";

echo "<hr>";
echo "<p><strong>Optimización completada - " . date('Y-m-d H:i:s') . "</strong></p>";
?>
