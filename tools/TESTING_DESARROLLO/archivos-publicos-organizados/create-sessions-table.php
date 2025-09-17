<?php
/**
 * Script de reparación: Crear tabla sessions manualmente
 * Instrucciones: Sube este archivo a public/ y visita: tudominio.com/create-sessions-table.php
 */

// Verificar que el usuario tenga permisos (opcional)
$password = 'Reparar2025!'; // CAMBIA ESTA CONTRASEÑA por una de tu elección
if (!isset($_GET['password']) || $_GET['password'] !== $password) {
    die('❌ Acceso denegado. Usa: ?password=' . $password);
}

echo '<h1>🗄️ Crear Tabla Sessions Manualmente</h1>';
echo '<pre>';

try {
    // Cambiar al directorio raíz de Laravel (buscar hacia arriba hasta encontrar composer.json)
    $currentDir = getcwd();
    $searchDir = $currentDir;
    $maxLevels = 5; // Máximo 5 niveles hacia arriba
    $level = 0;
    
    while ($level < $maxLevels && !file_exists($searchDir . '/composer.json')) {
        $parentDir = dirname($searchDir);
        if ($parentDir === $searchDir) break; // Llegamos a la raíz del sistema
        $searchDir = $parentDir;
        $level++;
    }
    
    if (file_exists($searchDir . '/composer.json')) {
        $rootPath = $searchDir;
        chdir($rootPath);
        echo "✅ Encontrado Laravel en: $rootPath\n";
    } else {
        echo "❌ No se encontró Laravel (composer.json) en los niveles superiores\n";
        $rootPath = dirname(__DIR__);
        chdir($rootPath);
        echo "ℹ️ Usando directorio padre por defecto: $rootPath\n";
    }
    
    echo "📁 Directorio actual: " . getcwd() . "\n\n";
    
    // Leer configuración de base de datos desde .env
    echo "📋 Leyendo configuración de base de datos...\n";
    if (!file_exists('.env')) {
        throw new Exception("Archivo .env no encontrado");
    }
    
    $envContent = file_get_contents('.env');
    
    preg_match('/DB_HOST=(.+)/', $envContent, $hostMatch);
    preg_match('/DB_DATABASE=(.+)/', $envContent, $dbMatch);
    preg_match('/DB_USERNAME=(.+)/', $envContent, $userMatch);
    preg_match('/DB_PASSWORD=(.*)/', $envContent, $passMatch);
    
    $host = isset($hostMatch[1]) ? trim($hostMatch[1]) : '';
    $database = isset($dbMatch[1]) ? trim($dbMatch[1]) : '';
    $username = isset($userMatch[1]) ? trim($userMatch[1]) : '';
    $password_db = isset($passMatch[1]) ? trim($passMatch[1]) : '';
    
    echo "🏠 Host: $host\n";
    echo "📊 Base de datos: $database\n";
    echo "👤 Usuario: $username\n\n";
    
    if (!$host || !$database || !$username) {
        throw new Exception("Configuración de base de datos incompleta en .env");
    }
    
    // Conectar a base de datos
    echo "🔌 Conectando a base de datos...\n";
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password_db, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    echo "✅ Conexión exitosa\n\n";
    
    // Verificar si la tabla ya existe
    echo "🔍 Verificando si tabla sessions existe...\n";
    $stmt = $pdo->query("SHOW TABLES LIKE 'sessions'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabla 'sessions' ya existe\n";
        
        // Mostrar estructura existente
        $stmt = $pdo->query("DESCRIBE sessions");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "📋 Columnas existentes: " . implode(', ', $columns) . "\n";
    } else {
        echo "📝 Tabla 'sessions' no existe, creando...\n";
        
        // SQL para crear tabla sessions
        $createTableSQL = "
            CREATE TABLE `sessions` (
                `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                `user_id` bigint unsigned DEFAULT NULL,
                `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                `user_agent` text COLLATE utf8mb4_unicode_ci,
                `payload` text COLLATE utf8mb4_unicode_ci NOT NULL,
                `last_activity` int NOT NULL,
                PRIMARY KEY (`id`),
                KEY `sessions_user_id_index` (`user_id`),
                KEY `sessions_last_activity_index` (`last_activity`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        // Ejecutar creación de tabla
        $pdo->exec($createTableSQL);
        echo "✅ Tabla 'sessions' creada exitosamente\n\n";
        
        // Verificar creación
        $stmt = $pdo->query("DESCRIBE sessions");
        $columns = $stmt->fetchAll();
        
        echo "📋 Estructura de tabla creada:\n";
        foreach ($columns as $column) {
            echo "   - {$column['Field']}: {$column['Type']}\n";
        }
    }
    
    // Verificar configuración de sesiones en .env
    echo "\n⚙️ Verificando configuración de sesiones en .env...\n";
    if (strpos($envContent, 'SESSION_DRIVER=database') !== false) {
        echo "✅ SESSION_DRIVER configurado como database\n";
    } else {
        echo "❌ SESSION_DRIVER no está configurado como database\n";
    }
    
    // Contar registros de sesiones actuales (si existen)
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM sessions");
        $result = $stmt->fetch();
        echo "📊 Sesiones actuales en BD: {$result['count']}\n";
    } catch (Exception $e) {
        echo "⚠️ No se pudieron contar sesiones: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎉 Proceso completado!\n";
    echo "📝 IMPORTANTE: Prueba tu aplicación ahora. El error de sesiones debería estar resuelto.\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "📋 Trace: " . $e->getTraceAsString() . "\n";
}

echo '</pre>';
echo '<p><a href="diagnosis.php?password=Reparar2025!">🔍 Ver Diagnóstico</a> | <a href="fix-cache.php?password=Reparar2025!">🧹 Limpiar Cache</a> | <a href="/">🏠 Inicio</a></p>';
?>
