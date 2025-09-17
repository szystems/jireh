# INSTRUCCIONES DE DESPLIEGUE EN iPAGE
# =====================================

## PASOS A SEGUIR EN EL SERVIDOR:

### 1. SUBIR ARCHIVOS
- Subir TODA la aplicación EXCEPTO:
  - node_modules/ (si existe)
  - .git/
  - .env (usar el .env preparado)
  - storage/logs/* (limpiar logs)

### 2. CONFIGURAR .ENV
- Usar el archivo .env preparado
- Reemplazar valores [CONFIGURAR_XXX] con datos reales de iPage:
  
  DB_HOST=[tu_host_ipage].ipagemysql.com
  DB_DATABASE=[nombre_bd_real]
  DB_USERNAME=[usuario_bd_real]
  DB_PASSWORD=[password_bd_real]
  APP_URL=https://[tu_dominio_real].com
  SESSION_DOMAIN=.[tu_dominio_real].com

### 3. CONFIGURAR PERMISOS
chmod 755 bootstrap/cache/
chmod 755 storage/
chmod -R 755 storage/app/
chmod -R 755 storage/framework/
chmod -R 755 storage/logs/

### 4. INSTALAR DEPENDENCIAS
composer install --optimize-autoloader --no-dev

### 5. MIGRAR BASE DE DATOS
php artisan migrate --force

### 6. OPTIMIZAR (opcional, recomendado)
php artisan config:cache
php artisan route:cache
php artisan view:cache

### 7. PROBAR APLICACIÓN
- Acceder a la URL principal
- Probar login
- Verificar que no hay error 419

## SOLUCIÓN DE PROBLEMAS:

### Si aparece error 500:
tail -f storage/logs/laravel.log

### Si aparece error 419:
- Verificar que SESSION_DRIVER=database en .env
- Ejecutar: php artisan migrate --force

### Si no cargan estilos:
- Verificar APP_URL en .env
- Verificar permisos de public/

2025-09-04 23:08:12
