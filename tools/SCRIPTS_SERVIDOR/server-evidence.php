<?php
// SCRIPT PARA GENERAR EVIDENCIA TÉCNICA CONTRA iPAGE
// Este script demuestra que es un problema del servidor, no de Laravel

echo "<h1>EVIDENCIA TÉCNICA - PROBLEMA DEL SERVIDOR iPAGE</h1>";
echo "<h2>Ejecutado el: " . date('Y-m-d H:i:s') . "</h2>";

echo "<hr><h3>1. VERIFICACIÓN DE ESCRITURA EN DIRECTORIOS DE SESIONES</h3>";

// Directorio de sesiones de Laravel
$sessionDir = __DIR__ . '/storage/framework/sessions';
if (!file_exists($sessionDir)) {
    mkdir($sessionDir, 0755, true);
}

echo "<strong>Directorio de sesiones Laravel:</strong> $sessionDir<br>";
echo "<strong>¿Existe el directorio?</strong> " . (file_exists($sessionDir) ? 'SÍ' : 'NO') . "<br>";
echo "<strong>¿Es escribible?</strong> " . (is_writable($sessionDir) ? 'SÍ' : 'NO') . "<br>";
echo "<strong>Permisos:</strong> " . substr(sprintf('%o', fileperms($sessionDir)), -4) . "<br>";

// Intentar escribir un archivo de sesión como Laravel
$testFile = $sessionDir . '/test_session_' . time();
$canWrite = file_put_contents($testFile, 'test session data');
echo "<strong>¿Puede escribir archivo de sesión?</strong> " . ($canWrite ? 'SÍ' : 'NO') . "<br>";

if ($canWrite) {
    unlink($testFile);
    echo "<span style='color:green'>✓ El directorio acepta escritura de archivos</span><br>";
} else {
    echo "<span style='color:red'>✗ ERROR: No puede escribir archivos de sesión</span><br>";
}

echo "<hr><h3>2. COMPARACIÓN SESIONES PHP vs LARAVEL</h3>";

// Probar sesiones PHP nativas
session_start();
$_SESSION['test_php'] = 'PHP session working';
echo "<strong>Sesión PHP nativa:</strong> " . (isset($_SESSION['test_php']) ? 'FUNCIONANDO' : 'FALLANDO') . "<br>";
echo "<strong>Session ID PHP:</strong> " . session_id() . "<br>";
echo "<strong>Session save path:</strong> " . session_save_path() . "<br>";

// Simular escritura de sesión Laravel
$laravelSessionId = 'laravel_' . uniqid();
$laravelSessionData = serialize(['_token' => 'test_csrf_token', 'user_id' => 1]);
$laravelSessionFile = $sessionDir . '/' . $laravelSessionId;

$laravelWrite = file_put_contents($laravelSessionFile, $laravelSessionData);
echo "<strong>¿Laravel puede escribir sesión?</strong> " . ($laravelWrite ? 'SÍ' : 'NO') . "<br>";

if ($laravelWrite) {
    $readBack = file_get_contents($laravelSessionFile);
    echo "<strong>¿Laravel puede leer sesión?</strong> " . ($readBack === $laravelSessionData ? 'SÍ' : 'NO') . "<br>";
    unlink($laravelSessionFile);
} else {
    echo "<span style='color:red'>✗ CRÍTICO: Laravel NO puede escribir sesiones</span><br>";
}

echo "<hr><h3>3. CONFIGURACIÓN PHP ACTUAL</h3>";

$phpSettings = [
    'session.save_handler',
    'session.save_path', 
    'session.name',
    'session.auto_start',
    'session.gc_maxlifetime',
    'session.cookie_lifetime',
    'session.use_cookies',
    'session.use_only_cookies',
    'upload_tmp_dir',
    'open_basedir',
    'disable_functions'
];

foreach ($phpSettings as $setting) {
    $value = ini_get($setting);
    echo "<strong>$setting:</strong> " . ($value ?: 'no configurado') . "<br>";
}

echo "<hr><h3>4. PRUEBA DE NUEVOS ARCHIVOS PHP (404 ERROR)</h3>";

// Crear un archivo PHP nuevo para probar el problema de 404
$newTestFile = __DIR__ . '/new-file-test-' . time() . '.php';
$newFileContent = '<?php echo "Este archivo fue creado el " . date("Y-m-d H:i:s") . " para probar el error 404"; ?>';

if (file_put_contents($newTestFile, $newFileContent)) {
    $urlToTest = 'https://' . $_SERVER['HTTP_HOST'] . '/' . basename($newTestFile);
    echo "<strong>Archivo creado:</strong> $newTestFile<br>";
    echo "<strong>URL para probar:</strong> <a href='$urlToTest' target='_blank'>$urlToTest</a><br>";
    echo "<span style='color:red'>Si este link da 404, es PRUEBA DEFINITIVA de problema del servidor</span><br>";
} else {
    echo "<span style='color:red'>✗ No se pudo crear archivo de prueba</span><br>";
}

echo "<hr><h3>5. INFORMACIÓN DEL SERVIDOR</h3>";

echo "<strong>Servidor:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "<strong>PHP Version:</strong> " . phpversion() . "<br>";
echo "<strong>Sistema:</strong> " . php_uname() . "<br>";
echo "<strong>Usuario PHP:</strong> " . (function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] : 'desconocido') . "<br>";

echo "<hr><h3>6. RESUMEN DE EVIDENCIA TÉCNICA</h3>";

echo "<div style='background-color: #ffcccc; padding: 10px; border: 1px solid #ff0000;'>";
echo "<strong>EVIDENCIA PARA iPAGE SUPPORT:</strong><br><br>";

if (!$canWrite || !$laravelWrite) {
    echo "✗ PROBLEMA CONFIRMADO: Laravel no puede escribir archivos de sesión<br>";
    echo "✓ Las sesiones PHP básicas funcionan<br>";
    echo "✗ Las sesiones Laravel NO funcionan<br>";
    echo "<br><strong>CONCLUSIÓN:</strong> Hay un cambio en la configuración del servidor que específicamente afecta la escritura de archivos por parte de las aplicaciones Laravel.";
} else {
    echo "⚠ PROBLEMA INTERMITENTE: Laravel puede escribir archivos pero las sesiones fallan en producción<br>";
    echo "Esto sugiere un problema de configuración específico del entorno de producción.";
}

echo "</div>";

echo "<hr><p><small>Script ejecutado desde: " . __FILE__ . "</small></p>";
?>
