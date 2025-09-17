<?php
// DIAGNÓSTICO ESPECÍFICO PARA PROBLEMA DE VISTAS MYSQL 5.7
echo "<h1>DIAGNÓSTICO MYSQL 5.7 - PROBLEMA DE VISTAS LARAVEL</h1>";
echo "<h2>Ejecutado el: " . date('Y-m-d H:i:s') . "</h2>";

// Configuración directa de base de datos desde .env
$config = [
    'host' => 'szclinicascom.ipagemysql.com',
    'database' => 'dbburo',
    'username' => 'sz',
    'password' => '%@$SZystems888'
];

try {
    
    echo "<h3>1. CONFIGURACIÓN DE BASE DE DATOS</h3>";
    echo "<strong>Host:</strong> " . $config['host'] . "<br>";
    echo "<strong>Database:</strong> " . $config['database'] . "<br>";
    echo "<strong>Username:</strong> " . $config['username'] . "<br>";
    
    // Conexión PDO para pruebas específicas
    $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "<span style='color:green'>✓ Conexión a base de datos exitosa</span><br>";
    
    echo "<h3>2. CONFIGURACIÓN MYSQL 5.7</h3>";
    
    // Verificar versión MySQL
    $version = $pdo->query("SELECT VERSION()")->fetchColumn();
    echo "<strong>Versión MySQL:</strong> $version<br>";
    
    // Verificar SQL_MODE - CRÍTICO para MySQL 5.7
    $sqlMode = $pdo->query("SELECT @@sql_mode")->fetchColumn();
    echo "<strong>SQL Mode actual:</strong> $sqlMode<br>";
    
    // Verificar si SQL_MODE está causando problemas
    $problematicModes = ['ONLY_FULL_GROUP_BY', 'STRICT_TRANS_TABLES', 'NO_ZERO_DATE', 'NO_ZERO_IN_DATE'];
    $currentModes = explode(',', $sqlMode);
    
    echo "<h4>ANÁLISIS SQL_MODE:</h4>";
    foreach ($problematicModes as $mode) {
        $hasMode = in_array($mode, $currentModes);
        $color = $hasMode ? 'red' : 'green';
        $status = $hasMode ? '⚠ PRESENTE (puede causar problemas)' : '✓ No presente';
        echo "<span style='color:$color'>$mode: $status</span><br>";
    }
    
    echo "<h3>3. PRUEBA DE CONSULTAS BÁSICAS</h3>";
    
    // Probar algunas consultas típicas de Laravel
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<strong>Tablas encontradas:</strong> " . count($tables) . "<br>";
    
    // Listar primeras 5 tablas
    echo "<strong>Primeras tablas:</strong> " . implode(', ', array_slice($tables, 0, 5)) . "<br>";
    
    // Probar una consulta SELECT simple en una tabla común
    $commonTables = ['users', 'migrations', 'sessions'];
    foreach ($commonTables as $table) {
        if (in_array($table, $tables)) {
            try {
                $count = $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
                echo "<span style='color:green'>✓ Tabla $table: $count registros</span><br>";
            } catch (Exception $e) {
                echo "<span style='color:red'>✗ Error en tabla $table: " . $e->getMessage() . "</span><br>";
            }
            break;
        }
    }
    
    echo "<h3>4. CONFIGURACIÓN RECOMENDADA PARA MYSQL 5.7</h3>";
    
    echo "<div style='background-color: #ffffcc; padding: 10px; border: 1px solid #ffcc00;'>";
    echo "<strong>SOLUCIÓN SUGERIDA:</strong><br><br>";
    
    if (in_array('ONLY_FULL_GROUP_BY', $currentModes)) {
        echo "1. El modo ONLY_FULL_GROUP_BY está causando problemas con consultas Laravel<br>";
        echo "2. Necesitas configurar MySQL para compatibilidad con Laravel<br><br>";
        
        echo "<strong>Configuración recomendada para MySQL 5.7:</strong><br>";
        echo "<code>sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER'</code><br><br>";
        
        echo "Esta configuración mantiene la seguridad de MySQL 5.7 pero permite que Laravel funcione correctamente.";
    } else {
        echo "El SQL_MODE parece compatible. El problema puede estar en:<br>";
        echo "- Consultas específicas en el código Laravel<br>";
        echo "- Configuración de charset/collation<br>";
        echo "- Problemas de memoria PHP<br>";
    }
    
    echo "</div>";
    
} catch (Exception $e) {
    echo "<span style='color:red'>✗ Error de conexión: " . $e->getMessage() . "</span><br>";
    echo "<p>No se pudo conectar a la base de datos. Verifica la configuración en el archivo .env</p>";
}

echo "<h3>5. PASOS INMEDIATOS PARA EL CLIENTE</h3>";

echo "<div style='background-color: #ffcccc; padding: 10px; border: 1px solid #ff0000;'>";
echo "<strong>PARA SAVIO (iPAGE SUPPORT):</strong><br><br>";
echo "El problema de las vistas que no cargan está relacionado con la actualización MySQL 5.6 → 5.7<br><br>";
echo "MySQL 5.7 introdujo el modo ONLY_FULL_GROUP_BY que rompe muchas consultas Laravel<br><br>";
echo "<strong>SOLUCIÓN:</strong> Configurar MySQL con un sql_mode compatible con Laravel:<br>";
echo "<code>sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER'</code><br><br>";
echo "Esta configuración permite que Laravel funcione mientras mantiene la seguridad de MySQL 5.7";
echo "</div>";

echo "<hr><p><small>Diagnóstico ejecutado desde: " . __FILE__ . "</small></p>";
?>
