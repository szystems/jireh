<?php
// ==========================================
// SCRIPT DE EMERGENCIA - HABILITAR DEBUG
// ==========================================

echo "<h2>HABILITANDO DEBUG TEMPORAL</h2>";
echo "<hr>";

// 1. Crear backup del .env actual
if (file_exists('../.env')) {
    copy('../.env', '../.env.backup.emergency');
    echo "✓ Backup del .env actual creado<br>";
}

// 2. Crear .env con debug habilitado
$env_debug = '# CONFIGURACION TEMPORAL DE DEBUG
APP_NAME="Jireh Automotriz"
APP_ENV=production
APP_KEY=base64:xiIpo1QczF5Kb/rviPdZ4H4mAhXCLXjOPZbPligtx0M=
APP_DEBUG=true
APP_URL=https://szystems.com/jirehsoftware/public

LOG_CHANNEL=single
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=szclinicascom.ipagemysql.com
DB_PORT=3306
DB_DATABASE=dbjirehappnuevo
DB_USERNAME=sz
DB_PASSWORD=SPP7007aaa@@@

SESSION_DRIVER=database
SESSION_LIFETIME=480
SESSION_CONNECTION=mysql
SESSION_COOKIE=jireh_session
SESSION_DOMAIN=
SESSION_SECURE_COOKIE=false
SESSION_SAME_SITE=lax
SESSION_ENCRYPT=false

CACHE_DRIVER=file
BROADCAST_DRIVER=log
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=sync

MAIL_MAILER=smtp
MAIL_HOST=smtp.ipage.com
MAIL_PORT=465
MAIL_USERNAME=ventas@jirehautomotriz.com
MAIL_PASSWORD=JIREHventas2020@123
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=ventas@jirehautomotriz.com
MAIL_FROM_NAME="${APP_NAME}"
';

file_put_contents('../.env', $env_debug);
echo "✓ Archivo .env con debug habilitado<br>";

echo "<br><strong>DEBUG HABILITADO</strong><br>";
echo "Ahora ve a: <a href='../'>https://szystems.com/jirehsoftware/public/</a><br>";
echo "Deberías ver el error específico.<br>";
echo "<br>";
echo "<strong>IMPORTANTE:</strong><br>";
echo "Después de ver el error, ejecuta: <a href='restaurar_env.php'>restaurar_env.php</a> para volver a la configuración segura.<br>";
?>
