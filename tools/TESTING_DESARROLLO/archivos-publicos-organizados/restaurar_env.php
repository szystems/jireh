<?php
// ==========================================
// SCRIPT PARA RESTAURAR CONFIGURACIÓN SEGURA
// ==========================================

echo "<h2>RESTAURANDO CONFIGURACIÓN SEGURA</h2>";
echo "<hr>";

// Restaurar desde backup
if (file_exists('../.env.backup.emergency')) {
    copy('../.env.backup.emergency', '../.env');
    echo "✓ Configuración original restaurada<br>";
    unlink('../.env.backup.emergency');
    echo "✓ Archivo de backup eliminado<br>";
} else {
    // Crear configuración segura por defecto
    $env_seguro = '# CONFIGURACION SEGURA PARA IPAGE
APP_NAME="Jireh Automotriz"
APP_ENV=production
APP_KEY=base64:xiIpo1QczF5Kb/rviPdZ4H4mAhXCLXjOPZbPligtx0M=
APP_DEBUG=false
APP_URL=https://szystems.com/jirehsoftware/public

LOG_CHANNEL=single
LOG_LEVEL=error

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
    
    file_put_contents('../.env', $env_seguro);
    echo "✓ Configuración segura por defecto aplicada<br>";
}

echo "<br><strong>CONFIGURACIÓN SEGURA RESTAURADA</strong><br>";
echo "APP_DEBUG=false activado<br>";
echo "Ya puedes probar la aplicación de nuevo.<br>";
?>
