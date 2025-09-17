<?php
echo "<h1>MYSQL DIAGNOSTIC</h1>";
echo "<p>Test: " . date('Y-m-d H:i:s') . "</p>";

// Test básico de conexión MySQL
$host = 'szclinicascom.ipagemysql.com';
$db = 'dbburo';
$user = 'sz';
$pass = '%@$SZystems888';

try {
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        echo "<p style='color:red'>Connection failed: " . $conn->connect_error . "</p>";
    } else {
        echo "<p style='color:green'>✓ MySQL Connection OK</p>";
        
        $version = $conn->query("SELECT VERSION()")->fetch_row()[0];
        echo "<p><strong>MySQL Version:</strong> $version</p>";
        
        $sqlmode = $conn->query("SELECT @@sql_mode")->fetch_row()[0];
        echo "<p><strong>SQL Mode:</strong></p>";
        echo "<div style='background:#f0f0f0;padding:10px;font-family:monospace'>$sqlmode</div>";
        
        if (strpos($sqlmode, 'ONLY_FULL_GROUP_BY') !== false) {
            echo "<div style='background:#ffcccc;padding:10px;border:2px solid red;margin:10px 0'>";
            echo "<h3>PROBLEM FOUND!</h3>";
            echo "<p>ONLY_FULL_GROUP_BY mode is active and breaking Laravel views</p>";
            echo "<p><strong>SOLUTION for Savio:</strong></p>";
            echo "<p>Configure MySQL with: <code>sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER'</code></p>";
            echo "</div>";
        } else {
            echo "<p style='color:green'>SQL mode looks compatible</p>";
        }
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
?>
