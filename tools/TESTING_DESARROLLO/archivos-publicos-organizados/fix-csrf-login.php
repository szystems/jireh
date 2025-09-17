<?php
/**
 * Script de reparación: Solucionar problema de sesión expirada en login
 * Instrucciones: Sube este archivo a public/ y visita: tudominio.com/fix-csrf-login.php
 */

// Verificar que el usuario tenga permisos (opcional)
$password = 'Reparar2025!'; // CAMBIA ESTA CONTRASEÑA por una de tu elección
if (!isset($_GET['password']) || $_GET['password'] !== $password) {
    die('❌ Acceso denegado. Usa: ?password=' . $password);
}

echo '<h1>🔒 Solucionar Problema de Sesión Expirada</h1>';
echo '<pre>';

try {
    // Cambiar al directorio raíz de Laravel
    $currentDir = getcwd();
    $searchDir = $currentDir;
    $maxLevels = 5;
    $level = 0;
    
    while ($level < $maxLevels && !file_exists($searchDir . '/composer.json')) {
        $parentDir = dirname($searchDir);
        if ($parentDir === $searchDir) break;
        $searchDir = $parentDir;
        $level++;
    }
    
    if (file_exists($searchDir . '/composer.json')) {
        $rootPath = $searchDir;
        chdir($rootPath);
        echo "✅ Encontrado Laravel en: $rootPath\n";
    } else {
        echo "❌ No se encontró Laravel\n";
        $rootPath = dirname(__DIR__);
        chdir($rootPath);
    }
    
    echo "📁 Directorio actual: " . getcwd() . "\n\n";
    
    // 1. Verificar y actualizar VerifyCsrfToken.php
    echo "🔍 Verificando middleware CSRF...\n";
    $csrfFile = 'app/Http/Middleware/VerifyCsrfToken.php';
    
    if (file_exists($csrfFile)) {
        $csrfContent = file_get_contents($csrfFile);
        echo "✅ Archivo VerifyCsrfToken.php encontrado\n";
        
        // Verificar si ya tiene manejo de TokenMismatchException
        if (strpos($csrfContent, 'TokenMismatchException') !== false) {
            echo "✅ Middleware ya tiene manejo de excepciones\n";
        } else {
            echo "🔧 Actualizando middleware CSRF...\n";
            
            // Crear respaldo
            $backupFile = $csrfFile . '.backup.' . date('Y-m-d_H-i-s');
            copy($csrfFile, $backupFile);
            echo "💾 Respaldo creado: $backupFile\n";
            
            // Nuevo contenido del middleware
            $newCsrfContent = '<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
    ];
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (TokenMismatchException $exception) {
            // Si es una petición AJAX, devolver JSON
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    \'message\' => \'Sesión expirada. Por favor, recarga la página e intenta de nuevo.\',
                    \'reload\' => true
                ], 419);
            }
            
            // Para peticiones normales, redirigir con mensaje
            return redirect()->back()
                ->withInput($request->except([\'_token\', \'password\']))
                ->withErrors([
                    \'csrf\' => \'Tu sesión ha expirado. Por favor intenta de nuevo.\'
                ]);
        }
    }
}';
            
            if (file_put_contents($csrfFile, $newCsrfContent)) {
                echo "✅ Middleware CSRF actualizado exitosamente\n";
            } else {
                echo "❌ Error actualizando middleware CSRF\n";
            }
        }
    } else {
        echo "❌ Archivo VerifyCsrfToken.php no encontrado\n";
    }
    
    // 2. Verificar configuración de sesiones una vez más
    echo "\n📋 Verificando configuración final de sesiones...\n";
    if (file_exists('.env')) {
        $envContent = file_get_contents('.env');
        
        // Configuraciones críticas para login
        $criticalConfigs = [
            'SESSION_DRIVER' => 'database',
            'SESSION_LIFETIME' => '480',
            'SESSION_SAME_SITE' => 'lax',
            'APP_KEY' => null
        ];
        
        foreach ($criticalConfigs as $key => $expectedValue) {
            if (preg_match("/$key=(.+)/", $envContent, $matches)) {
                $value = trim($matches[1]);
                if ($key === 'APP_KEY') {
                    if (empty($value) || $value === 'null') {
                        echo "❌ $key está vacío - ESTO CAUSA PROBLEMAS DE SESIÓN\n";
                    } else {
                        echo "✅ $key configurado correctamente\n";
                    }
                } else {
                    echo "✅ $key = $value\n";
                }
            } else {
                echo "❌ $key no definido\n";
            }
        }
        
        // Verificar si APP_KEY está vacío
        if (preg_match('/APP_KEY=(.*)/', $envContent, $matches)) {
            $appKey = trim($matches[1]);
            if (empty($appKey) || $appKey === 'null' || $appKey === '') {
                echo "\n🚨 ¡PROBLEMA CRÍTICO ENCONTRADO!\n";
                echo "APP_KEY está vacío - esto causa que las sesiones no funcionen correctamente\n";
                echo "📝 SOLUCIÓN: Genera una nueva APP_KEY\n";
                
                // Generar nueva APP_KEY
                $newKey = 'base64:' . base64_encode(random_bytes(32));
                $envContent = preg_replace('/APP_KEY=.*/', "APP_KEY=$newKey", $envContent);
                
                if (file_put_contents('.env', $envContent)) {
                    echo "✅ Nueva APP_KEY generada y guardada: $newKey\n";
                } else {
                    echo "❌ Error guardando nueva APP_KEY\n";
                }
            }
        }
    }
    
    // 3. Limpiar todo el cache
    echo "\n🧹 Limpiando todos los caches...\n";
    
    $cacheFiles = [
        'bootstrap/cache/config.php' => 'Cache de configuración',
        'bootstrap/cache/routes.php' => 'Cache de rutas',
        'bootstrap/cache/services.php' => 'Cache de servicios',
        'storage/framework/cache/data' => 'Directorio de cache'
    ];
    
    foreach ($cacheFiles as $path => $description) {
        if (is_file($path)) {
            unlink($path);
            echo "✅ Eliminado: $description\n";
        } elseif (is_dir($path)) {
            $files = glob("$path/*");
            $count = 0;
            foreach ($files as $file) {
                if (is_file($file) && basename($file) !== '.gitignore') {
                    unlink($file);
                    $count++;
                }
            }
            echo "✅ Limpiado: $description ($count archivos)\n";
        }
    }
    
    // 4. Verificar tabla sessions una última vez
    echo "\n🗄️ Verificación final de sesiones en BD...\n";
    if (file_exists('.env')) {
        $envContent = file_get_contents('.env');
        
        preg_match('/DB_HOST=(.+)/', $envContent, $hostMatch);
        preg_match('/DB_DATABASE=(.+)/', $envContent, $dbMatch);
        preg_match('/DB_USERNAME=(.+)/', $envContent, $userMatch);
        preg_match('/DB_PASSWORD=(.*)/', $envContent, $passMatch);
        
        $host = isset($hostMatch[1]) ? trim($hostMatch[1]) : '';
        $database = isset($dbMatch[1]) ? trim($dbMatch[1]) : '';
        $username = isset($userMatch[1]) ? trim($userMatch[1]) : '';
        $password_db = isset($passMatch[1]) ? trim($passMatch[1]) : '';
        
        if ($host && $database && $username) {
            try {
                $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password_db);
                
                // Limpiar sesiones expiradas
                $now = time();
                $stmt = $pdo->prepare("DELETE FROM sessions WHERE last_activity < ?");
                $stmt->execute([$now - 28800]); // 8 horas atrás
                $deletedRows = $stmt->rowCount();
                
                echo "🧹 Limpiadas $deletedRows sesiones expiradas\n";
                
                // Contar sesiones actuales
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM sessions");
                $result = $stmt->fetch();
                echo "📊 Sesiones activas: {$result['count']}\n";
                
            } catch (Exception $e) {
                echo "⚠️ Error verificando BD: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n🎉 Proceso completado!\n";
    echo "🎯 AHORA PRUEBA:\n";
    echo "1. Ve a https://software.burotributario.com/login\n";
    echo "2. Usa Ctrl+F5 para refrescar completamente la página\n";
    echo "3. Intenta hacer login de nuevo\n";
    echo "4. Si persiste el problema, el issue es más profundo\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo '</pre>';
echo '<p><strong>🎯 Próximo paso:</strong> <a href="https://software.burotributario.com/login" target="_blank">Probar Login (Ctrl+F5 primero)</a></p>';
?>
