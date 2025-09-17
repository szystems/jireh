<?php
/*
|--------------------------------------------------------------------------
| SCRIPT PARA CREAR TABLA DE SESIONES EN IPAGE
|--------------------------------------------------------------------------
*/

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>CREAR/VERIFICAR TABLA DE SESIONES</h2>";

// Leer configuración del .env
$env_path = '../.env';
if (!file_exists($env_path)) {
    die("Error: No se encontró el archivo .env");
}

$env_content = file_get_contents($env_path);

// Extraer configuración de DB
preg_match('/DB_HOST=(.*)/', $env_content, $host_match);
preg_match('/DB_DATABASE=(.*)/', $env_content, $db_match);
preg_match('/DB_USERNAME=(.*)/', $env_content, $user_match);
preg_match('/DB_PASSWORD=(.*)/', $env_content, $pass_match);

if (!$host_match || !$db_match || !$user_match || !$pass_match) {
    die("Error: No se pudo leer la configuración de la base de datos");
}

$host = trim($host_match[1]);
$database = trim($db_match[1]);
$username = trim($user_match[1]);
$password = trim($pass_match[1]);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Conectado a la base de datos<br>";
    
    // Verificar si la tabla existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'sessions'");
    if ($stmt->fetch()) {
        echo "✓ La tabla 'sessions' ya existe<br>";
        
        // Mostrar estructura
        $stmt = $pdo->query("DESCRIBE sessions");
        echo "<h3>Estructura actual de la tabla sessions:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value ?? '') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        
    } else {
        echo "La tabla 'sessions' no existe. Creándola...<br>";
        
        // SQL para crear la tabla de sesiones
        $sql = "CREATE TABLE sessions (
            id varchar(255) NOT NULL,
            user_id bigint(20) unsigned DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            user_agent text,
            payload longtext NOT NULL,
            last_activity int(11) NOT NULL,
            PRIMARY KEY (id),
            KEY sessions_user_id_index (user_id),
            KEY sessions_last_activity_index (last_activity)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        echo "✓ Tabla 'sessions' creada exitosamente<br>";
    }
    
    // Verificar permisos en la tabla
    try {
        $pdo->exec("INSERT INTO sessions (id, payload, last_activity) VALUES ('test_session', 'test_payload', " . time() . ")");
        echo "✓ Permisos de escritura: OK<br>";
        
        $stmt = $pdo->query("SELECT * FROM sessions WHERE id = 'test_session'");
        if ($stmt->fetch()) {
            echo "✓ Permisos de lectura: OK<br>";
        }
        
        $pdo->exec("DELETE FROM sessions WHERE id = 'test_session'");
        echo "✓ Permisos de eliminación: OK<br>";
        
    } catch (Exception $e) {
        echo "✗ Error de permisos: " . $e->getMessage() . "<br>";
    }
    
} catch (PDOException $e) {
    echo "✗ Error de conexión: " . $e->getMessage() . "<br>";
}

echo "<br><strong>Proceso completado</strong>";
?>
