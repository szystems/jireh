# Archivos Públicos Organizados

Este directorio contiene archivos que anteriormente estaban en la carpeta `/public` pero que son herramientas de desarrollo, testing y diagnóstico, no archivos públicos web.

## 📁 Archivos incluidos:

### 🔧 Scripts de Reparación (fix-*)
- `fix-autoloader.php` - Reparación del autoloader de Composer
- `fix-cache.php` - Limpieza y reparación de cache de Laravel
- `fix-csrf-login.php` - Reparación de problemas de CSRF en login
- `fix-session-config.php` - Corrección de configuración de sesiones
- `fix-storage.php` - Reparación de permisos y enlaces de storage

### 🗄️ Scripts de Base de Datos
- `crear_tabla_sesiones.php` - Creación de tabla de sesiones (versión ES)
- `create-sessions-table.php` - Creación de tabla de sesiones (versión EN)
- `migrate-sessions.php` - Migración de sesiones a base de datos

### 🔍 Scripts de Diagnóstico
- `diagnosis.php` - Diagnóstico general del sistema
- `diagnostico_avanzado.php` - Diagnóstico avanzado con más detalle
- `diagnostico_ipage.php` - Diagnóstico específico para servidor iPage

### 🧪 Scripts de Testing
- `test-auth.php` - Testing de sistema de autenticación
- `test-login.php` - Testing de proceso de login
- `test-login-specific.php` - Testing específico de login con casos particulares

### ⚙️ Scripts de Configuración
- `habilitar_debug.php` - Activación de modo debug
- `optimizar_ipage.php` - Optimizaciones específicas para iPage
- `restaurar_env.php` - Restauración de archivo de configuración .env

### 🔐 Scripts de Seguridad
- `reset-password.php` - Reset de contraseñas de usuarios

### 📄 Archivos de Backup
- `index.php.backup` - Backup del archivo index.php principal
- `index_ipage.php` - Versión específica de index.php para iPage

## 📋 Clasificación por Uso:

### 🚨 Scripts de Emergencia
Usar cuando el sistema tiene problemas críticos:
- `fix-autoloader.php`
- `fix-cache.php`
- `fix-storage.php`
- `restaurar_env.php`

### 🔧 Scripts de Mantenimiento
Para mantenimiento regular y optimización:
- `optimizar_ipage.php`
- `migrate-sessions.php`
- `crear_tabla_sesiones.php`

### 🔍 Scripts de Diagnóstico
Para identificar problemas y estado del sistema:
- `diagnosis.php`
- `diagnostico_avanzado.php`
- `diagnostico_ipage.php`

### 🧪 Scripts de Testing
Para pruebas de funcionalidad:
- `test-auth.php`
- `test-login.php`
- `test-login-specific.php`

## ⚠️ Importante - Seguridad:

### ❌ Estos archivos NO deben estar en /public porque:
1. **Exponen información sensible** del sistema
2. **Pueden ser ejecutados por usuarios no autorizados**
3. **Contienen herramientas de diagnóstico** que revelan configuraciones
4. **Incluyen scripts de reparación** que pueden modificar el sistema
5. **Son herramientas de desarrollo**, no funcionalidad de usuario final

### ✅ Ubicación correcta - /tools:
- **Acceso controlado** solo para desarrolladores
- **No expuestos públicamente** via web
- **Organizados por categoría** para fácil mantenimiento
- **Documentados apropiadamente** para uso correcto

## 📋 Cómo usar estos scripts:

### Desde línea de comandos:
```bash
# Ejecutar desde la raíz del proyecto
php tools/TESTING_DESARROLLO/archivos-publicos-organizados/diagnosis.php
php tools/TESTING_DESARROLLO/archivos-publicos-organizados/fix-cache.php
```

### Para emergencias en servidor:
1. Subir el archivo específico temporalmente
2. Ejecutar via web browser (SOLO en emergencias)
3. **ELIMINAR inmediatamente** después del uso
4. **NUNCA dejar** estos archivos en /public permanentemente

## 🔄 Mantenimiento:

- **Revisar periódicamente** si los scripts siguen siendo necesarios
- **Actualizar scripts** cuando cambien las configuraciones del proyecto
- **Eliminar scripts obsoletos** que ya no se usen
- **Documentar nuevos scripts** que se agreguen
- **Validar funcionamiento** después de cambios en Laravel

## 📝 Notas de Desarrollo:

- Estos archivos fueron **movidos desde /public** en Septiembre 16, 2025
- **Razón del movimiento**: Seguridad y organización
- **Estado**: Archivos activos de desarrollo, usar con precaución
- **Acceso**: Solo desarrolladores autorizados

---
*Organizado: Septiembre 16, 2025*  
*Ubicación anterior: /public (INSEGURO)*  
*Ubicación actual: /tools/TESTING_DESARROLLO/archivos-publicos-organizados (SEGURO)*