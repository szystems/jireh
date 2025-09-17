<?php
/**
 * DIAGNÓSTICO DE COMPATIBILIDAD PARA iPAGE
 * =======================================
 * 
 * Este script verifica que tu aplicación Laravel esté lista para iPage
 * Ejecutar ANTES de subir a producción
 */

echo "<h1>🔍 DIAGNÓSTICO DE COMPATIBILIDAD iPAGE</h1>";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.success { color: green; font-weight: bold; }
.warning { color: orange; font-weight: bold; }
.error { color: red; font-weight: bold; }
.info { color: blue; font-weight: bold; }
.section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
.check { margin: 10px 0; padding: 10px; background: #f9f9f9; border-radius: 3px; }
</style>";

echo "<div class='section'>";
echo "<h2>📋 RESUMEN EJECUTIVO</h2>";

$errors = 0;
$warnings = 0;
$success = 0;

// =============================================
// 1. VERIFICAR ARCHIVOS CRÍTICOS
// =============================================
echo "<div class='check'>";
echo "<h3>1. 📁 ARCHIVOS CRÍTICOS</h3>";

// Verificar .env.production
if (file_exists('.env.production')) {
    echo "<span class='success'>✅ .env.production existe</span><br>";
    $success++;
} else {
    echo "<span class='error'>❌ .env.production NO EXISTE - CRÍTICO</span><br>";
    $errors++;
}

// Verificar .htaccess
if (file_exists('public/.htaccess')) {
    echo "<span class='success'>✅ public/.htaccess existe</span><br>";
    $success++;
} else {
    echo "<span class='error'>❌ public/.htaccess NO EXISTE - CRÍTICO</span><br>";
    $errors++;
}

// Verificar composer.json
if (file_exists('composer.json')) {
    echo "<span class='success'>✅ composer.json existe</span><br>";
    $success++;
} else {
    echo "<span class='error'>❌ composer.json NO EXISTE - CRÍTICO</span><br>";
    $errors++;
}

echo "</div>";

// =============================================
// 2. VERIFICAR CONFIGURACIÓN DE SESIONES
// =============================================
echo "<div class='check'>";
echo "<h3>2. 🔐 CONFIGURACIÓN DE SESIONES (Anti-Error 419)</h3>";

if (file_exists('.env.production')) {
    $envContent = file_get_contents('.env.production');
    
    if (strpos($envContent, 'SESSION_DRIVER=database') !== false) {
        echo "<span class='success'>✅ SESSION_DRIVER configurado como database</span><br>";
        $success++;
    } else {
        echo "<span class='warning'>⚠️ SESSION_DRIVER no está configurado como database</span><br>";
        $warnings++;
    }
    
    if (strpos($envContent, 'SESSION_LIFETIME=480') !== false) {
        echo "<span class='success'>✅ SESSION_LIFETIME configurado (480 min)</span><br>";
        $success++;
    } else {
        echo "<span class='warning'>⚠️ SESSION_LIFETIME no está optimizado</span><br>";
        $warnings++;
    }
} else {
    echo "<span class='error'>❌ No se puede verificar - falta .env.production</span><br>";
    $errors++;
}

echo "</div>";

// =============================================
// 3. VERIFICAR MIGRACIONES DE SESIONES
// =============================================
echo "<div class='check'>";
echo "<h3>3. 🗄️ MIGRACIÓN DE SESIONES</h3>";

$sessionMigrationExists = false;
$migrationFiles = glob('database/migrations/*_create_sessions_table.php');

if (!empty($migrationFiles)) {
    echo "<span class='success'>✅ Migración de sessions existe</span><br>";
    $success++;
    $sessionMigrationExists = true;
} else {
    echo "<span class='error'>❌ Migración de sessions NO EXISTE - Ejecutar: php artisan session:table</span><br>";
    $errors++;
}

echo "</div>";

// =============================================
// 4. VERIFICAR CONFIGURACIÓN DE BASE DE DATOS
// =============================================
echo "<div class='check'>";
echo "<h3>4. 🗄️ CONFIGURACIÓN DE BASE DE DATOS</h3>";

if (file_exists('.env.production')) {
    $envContent = file_get_contents('.env.production');
    
    if (strpos($envContent, 'DB_HOST=') !== false && strpos($envContent, 'ipage') !== false) {
        echo "<span class='success'>✅ DB_HOST configurado para iPage</span><br>";
        $success++;
    } else {
        echo "<span class='warning'>⚠️ DB_HOST no está configurado para iPage</span><br>";
        $warnings++;
    }
    
    if (strpos($envContent, 'DB_DATABASE=') !== false) {
        echo "<span class='success'>✅ DB_DATABASE configurado</span><br>";
        $success++;
    } else {
        echo "<span class='error'>❌ DB_DATABASE no configurado</span><br>";
        $errors++;
    }
} else {
    echo "<span class='error'>❌ No se puede verificar - falta .env.production</span><br>";
    $errors++;
}

echo "</div>";

// =============================================
// 5. VERIFICAR CONFIGURACIÓN DE MAIL
// =============================================
echo "<div class='check'>";
echo "<h3>5. 📧 CONFIGURACIÓN DE EMAIL</h3>";

if (file_exists('.env.production')) {
    $envContent = file_get_contents('.env.production');
    
    if (strpos($envContent, 'MAIL_HOST=smtp.ipage.com') !== false) {
        echo "<span class='success'>✅ MAIL_HOST configurado para iPage</span><br>";
        $success++;
    } else {
        echo "<span class='warning'>⚠️ MAIL_HOST no está configurado para iPage</span><br>";
        $warnings++;
    }
} else {
    echo "<span class='error'>❌ No se puede verificar - falta .env.production</span><br>";
    $errors++;
}

echo "</div>";

// =============================================
// 6. VERIFICAR SEGURIDAD
// =============================================
echo "<div class='check'>";
echo "<h3>6. 🔒 CONFIGURACIÓN DE SEGURIDAD</h3>";

if (file_exists('.env.production')) {
    $envContent = file_get_contents('.env.production');
    
    if (strpos($envContent, 'APP_DEBUG=false') !== false) {
        echo "<span class='success'>✅ APP_DEBUG=false (modo producción)</span><br>";
        $success++;
    } else {
        echo "<span class='error'>❌ APP_DEBUG debe ser false en producción</span><br>";
        $errors++;
    }
    
    if (strpos($envContent, 'APP_ENV=production') !== false) {
        echo "<span class='success'>✅ APP_ENV=production</span><br>";
        $success++;
    } else {
        echo "<span class='error'>❌ APP_ENV debe ser production</span><br>";
        $errors++;
    }
    
    if (strpos($envContent, 'APP_KEY=') !== false && strlen(trim(str_replace('APP_KEY=', '', strstr($envContent, 'APP_KEY=')))) > 10) {
        echo "<span class='success'>✅ APP_KEY configurado</span><br>";
        $success++;
    } else {
        echo "<span class='error'>❌ APP_KEY no configurado o inválido</span><br>";
        $errors++;
    }
} else {
    echo "<span class='error'>❌ No se puede verificar - falta .env.production</span><br>";
    $errors++;
}

echo "</div>";

// =============================================
// 7. VERIFICAR RUTAS CRÍTICAS
// =============================================
echo "<div class='check'>";
echo "<h3>7. 🛣️ RUTAS CRÍTICAS</h3>";

if (file_exists('routes/web.php')) {
    $routesContent = file_get_contents('routes/web.php');
    
    if (strpos($routesContent, 'refresh-csrf') !== false) {
        echo "<span class='success'>✅ Ruta refresh-csrf existe (anti-error 419)</span><br>";
        $success++;
    } else {
        echo "<span class='warning'>⚠️ Ruta refresh-csrf no encontrada</span><br>";
        $warnings++;
    }
} else {
    echo "<span class='error'>❌ routes/web.php no existe</span><br>";
    $errors++;
}

echo "</div>";

// =============================================
// 8. VERIFICAR DEPENDENCIAS COMPOSER
// =============================================
echo "<div class='check'>";
echo "<h3>8. 📦 DEPENDENCIAS COMPOSER</h3>";

if (file_exists('composer.json')) {
    $composerContent = file_get_contents('composer.json');
    $composer = json_decode($composerContent, true);
    
    if (isset($composer['require']['php'])) {
        echo "<span class='success'>✅ Requisitos de PHP definidos: " . $composer['require']['php'] . "</span><br>";
        $success++;
    } else {
        echo "<span class='warning'>⚠️ Requisitos de PHP no definidos</span><br>";
        $warnings++;
    }
    
    if (file_exists('vendor/autoload.php')) {
        echo "<span class='success'>✅ Vendor directory existe</span><br>";
        $success++;
    } else {
        echo "<span class='error'>❌ Vendor directory NO EXISTE - Ejecutar: composer install</span><br>";
        $errors++;
    }
} else {
    echo "<span class='error'>❌ composer.json no existe</span><br>";
    $errors++;
}

echo "</div>";

// =============================================
// RESUMEN FINAL
// =============================================
echo "</div>";

echo "<div class='section'>";
echo "<h2>📊 RESUMEN FINAL</h2>";

echo "<div style='display: flex; gap: 20px; margin: 20px 0;'>";
echo "<div style='background: green; color: white; padding: 15px; border-radius: 5px; text-align: center;'>";
echo "<h3>✅ EXITOSO</h3>";
echo "<h2>$success</h2>";
echo "</div>";

echo "<div style='background: orange; color: white; padding: 15px; border-radius: 5px; text-align: center;'>";
echo "<h3>⚠️ ADVERTENCIAS</h3>";
echo "<h2>$warnings</h2>";
echo "</div>";

echo "<div style='background: red; color: white; padding: 15px; border-radius: 5px; text-align: center;'>";
echo "<h3>❌ ERRORES</h3>";
echo "<h2>$errors</h2>";
echo "</div>";
echo "</div>";

// RECOMENDACIONES
echo "<h3>🎯 RECOMENDACIONES PARA iPAGE:</h3>";

if ($errors > 0) {
    echo "<div class='error'>";
    echo "<h4>🚨 CRÍTICO - DEBE SOLUCIONARSE ANTES DE SUBIR:</h4>";
    echo "<ul>";
    echo "<li>Crear archivo .env.production con configuración de producción</li>";
    echo "<li>Verificar que APP_DEBUG=false y APP_ENV=production</li>";
    echo "<li>Configurar base de datos con credenciales de iPage</li>";
    echo "<li>Ejecutar: php artisan session:table && php artisan migrate</li>";
    echo "</ul>";
    echo "</div>";
}

if ($warnings > 0) {
    echo "<div class='warning'>";
    echo "<h4>⚠️ RECOMENDACIONES:</h4>";
    echo "<ul>";
    echo "<li>Configurar SESSION_DRIVER=database para evitar error 419</li>";
    echo "<li>Configurar MAIL_HOST=smtp.ipage.com</li>";
    echo "<li>Verificar configuración de dominio en SESSION_DOMAIN</li>";
    echo "</ul>";
    echo "</div>";
}

echo "<div class='info'>";
echo "<h4>📝 PASOS FINALES ANTES DE SUBIR A iPAGE:</h4>";
echo "<ol>";
echo "<li>Copiar .env.production como .env en el servidor</li>";
echo "<li>Ejecutar: composer install --optimize-autoloader --no-dev</li>";
echo "<li>Ejecutar: php artisan migrate --force</li>";
echo "<li>Ejecutar: php artisan config:cache</li>";
echo "<li>Ejecutar: php artisan route:cache</li>";
echo "<li>Ejecutar: php artisan view:cache</li>";
echo "<li>Configurar .htaccess para redirecciones HTTPS</li>";
echo "</ol>";
echo "</div>";

echo "</div>";

// ESTADO GENERAL
if ($errors == 0 && $warnings == 0) {
    echo "<div style='background: green; color: white; padding: 20px; border-radius: 10px; text-align: center; margin: 20px 0;'>";
    echo "<h2>🎉 ¡APLICACIÓN LISTA PARA iPAGE!</h2>";
    echo "<p>Todas las verificaciones pasaron exitosamente</p>";
    echo "</div>";
} elseif ($errors == 0) {
    echo "<div style='background: orange; color: white; padding: 20px; border-radius: 10px; text-align: center; margin: 20px 0;'>";
    echo "<h2>⚠️ APLICACIÓN CASI LISTA</h2>";
    echo "<p>Hay algunas advertencias que deberías revisar</p>";
    echo "</div>";
} else {
    echo "<div style='background: red; color: white; padding: 20px; border-radius: 10px; text-align: center; margin: 20px 0;'>";
    echo "<h2>🚨 APLICACIÓN NO LISTA</h2>";
    echo "<p>Hay errores críticos que DEBEN solucionarse</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><small>Diagnóstico generado el " . date('Y-m-d H:i:s') . "</small></p>";
?>
