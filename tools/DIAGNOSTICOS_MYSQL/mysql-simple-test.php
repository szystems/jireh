<?php
// DIAGNÓSTICO MYSQL SIMPLE PARA LARAVEL
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>DIAGNÓSTICO MYSQL 5.7 - PROBLEMA DE VISTAS LARAVEL</h1>";
echo "<h2>Ejecutado el: " . date('Y-m-d H:i:s') . "</h2>";

echo "<h3>1. PRUEBA DE CONEXIÓN MYSQL</h3>";

// Configuración de base de datos
$host = 'szclinicascom.ipagemysql.com';
$database = 'dbburo';
$username = 'sz';
$password = '%@$SZystems888';

echo "<strong>Host:</strong> $host<br>";
echo "<strong>Database:</strong> $database<br>";
echo "<strong>Username:</strong> $username<br>";
echo "<strong>Password:</strong> [OCULTO]<br><br>";

try {
    // Intentar conexión con mysqli (más simple)
    $mysqli = new mysqli($host, $username, $password, $database);
    
    if ($mysqli->connect_error) {
        throw new Exception("Error de conexión: " . $mysqli->connect_error);
    }
    
    echo "<span style='color:green; font-size:16px;'>✓ CONEXIÓN MYSQL EXITOSA</span><br><br>";
    
    // Obtener versión MySQL
    $version = $mysqli->query("SELECT VERSION()")->fetch_row()[0];
    echo "<strong>Versión MySQL:</strong> <span style='color:blue'>$version</span><br>";
    
    // Obtener SQL_MODE - ESTA ES LA CLAVE
    $result = $mysqli->query("SELECT @@sql_mode");
    $sqlMode = $result->fetch_row()[0];
    echo "<strong>SQL Mode actual:</strong><br>";
    echo "<div style='background-color: #f0f0f0; padding: 10px; margin: 5px 0; font-family: monospace;'>";
    echo $sqlMode;
    echo "</div>";
    
    // Analizar modos problemáticos
    $modes = explode(',', $sqlMode);
    $problemModes = [];
    
    echo "<h3>2. ANÁLISIS SQL_MODE</h3>";
    
    $problematicModes = [
        'ONLY_FULL_GROUP_BY' => 'Rompe consultas Laravel con GROUP BY',
        'STRICT_TRANS_TABLES' => 'Puede causar problemas con campos opcionales',
        'NO_ZERO_DATE' => 'Problemas con fechas 0000-00-00',
        'NO_ZERO_IN_DATE' => 'Problemas con fechas parciales'
    ];
    
    foreach ($problematicModes as $mode => $description) {
        $isActive = in_array($mode, $modes);
        $color = $isActive ? 'red' : 'green';
        $status = $isActive ? '⚠ ACTIVO - PROBLEMÁTICO' : '✓ No activo';
        
        echo "<div style='margin: 5px 0;'>";
        echo "<strong>$mode:</strong> <span style='color:$color'>$status</span><br>";
        echo "<small>$description</small>";
        echo "</div>";
        
        if ($isActive) {
            $problemModes[] = $mode;
        }
    }
    
    // Mostrar solución
    echo "<h3>3. SOLUCIÓN TÉCNICA</h3>";
    
    if (!empty($problemModes)) {
        echo "<div style='background-color: #ffcccc; padding: 15px; border: 2px solid #ff0000; margin: 10px 0;'>";
        echo "<h4 style='color: red; margin-top: 0;'>PROBLEMA CONFIRMADO</h4>";
        echo "<p><strong>Modos problemáticos detectados:</strong> " . implode(', ', $problemModes) . "</p>";
        
        echo "<h4>SOLUCIÓN PARA SAVIO (iPAGE):</h4>";
        echo "<p>Configurar MySQL con el siguiente sql_mode compatible con Laravel:</p>";
        
        echo "<div style='background-color: #000; color: #0f0; padding: 10px; font-family: monospace;'>";
        echo "sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER'";
        echo "</div>";
        
        echo "<p><strong>Ubicación:</strong> my.cnf sección [mysqld] o configuración MySQL del servidor</p>";
        echo "<p><strong>Efecto:</strong> Permitirá que las vistas de módulos Laravel carguen correctamente</p>";
        
    } else {
        echo "<div style='background-color: #ccffcc; padding: 15px; border: 2px solid #00ff00;'>";
        echo "<h4 style='color: green; margin-top: 0;'>SQL_MODE PARECE COMPATIBLE</h4>";
        echo "<p>El problema puede estar en otro lugar. Revisa logs de Laravel para errores específicos.</p>";
        echo "</div>";
    }
    
    // Probar una consulta simple
    echo "<h3>4. PRUEBA DE CONSULTA</h3>";
    $result = $mysqli->query("SHOW TABLES");
    if ($result) {
        $tableCount = $result->num_rows;
        echo "<span style='color:green'>✓ Consulta básica exitosa - $tableCount tablas encontradas</span><br>";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "<div style='background-color: #ffcccc; padding: 15px; border: 2px solid #ff0000;'>";
    echo "<h4 style='color: red;'>ERROR DE CONEXIÓN</h4>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Verifica las credenciales de base de datos.</p>";
    echo "</div>";
}

echo "<hr>";
echo "<h3>5. RESUMEN PARA CLIENTE</h3>";
echo "<p>Una vez que Savio configure MySQL correctamente:</p>";
echo "<ul>";
echo "<li>✅ El login seguirá funcionando (ya funciona)</li>";
echo "<li>🔧 Las vistas de módulos deberían cargar (problema actual)</li>";
echo "<li>✅ Todas las funcionalidades Laravel se restaurarán</li>";
echo "</ul>";

echo "<p><small>Diagnóstico ejecutado: " . date('Y-m-d H:i:s') . "</small></p>";
?>
