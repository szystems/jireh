# 📁 ORGANIZACIÓN DE HERRAMIENTAS Y DOCUMENTACIÓN
## Última actualización: 4 de septiembre de 2025

Esta carpeta contiene todas las herramientas, scripts, documentación y archivos auxiliares del proyecto Jireh, organizados por categorías para facilitar su gestión.

---

## 📂 ESTRUCTURA DE CARPETAS

### 🗄️ BASE_DATOS/
**Archivos relacionados con la base de datos**
- `dbjirehapp_inicial.sql` - Estructura inicial de la base de datos

### 🔍 DIAGNOSTICOS_MYSQL/
**Scripts de diagnóstico y pruebas de MySQL**
- `mysql-fix-simple.php` - Script de corrección de problemas MySQL
- `mysql-simple-test.php` - Pruebas básicas de conexión MySQL
- `mysql-views-diagnosis.php` - Diagnóstico de vistas MySQL
- `test-mysql.php` - Test de funcionalidad MySQL

### 🚀 DESPLIEGUE_IPAGE/
**Todo lo relacionado con el despliegue en iPage**
- `diagnostico-ipage.php` - Script de diagnóstico de compatibilidad
- `preparar-ipage.php` - Script de preparación para despliegue
- `GUIA_DESPLIEGUE_IPAGE.md` - Guía completa de despliegue
- `SOLUCION_ERROR_419_IPAGE.md` - Solución específica para error 419
- `IPAGE_SUPPORT_REQUEST_ENGLISH.txt` - Solicitud de soporte técnico
- `REPORTE_IPAGE_SOPORTE.txt` - Reporte de problemas con iPage
- `CONFIGURACIONES_HTACCESS.md` - Configuraciones de .htaccess
- `INSTRUCCIONES_CORRECCION_LOGIN.txt` - Instrucciones de corrección de login
- `deploy-ipage/` - Archivos preparados para despliegue

### 📊 REPORTES_PROYECTO/
**Reportes y documentación del proyecto**
- `PRD_PROYECTO_JIREH.md` - Documento de requisitos del proyecto
- `PLAN_TRABAJO_SUELDOS_Y_PERMISOS.md` - Plan de trabajo y permisos
- `REPORTE_ENTREGA_CLIENTE_JIREH_2025.md` - Reporte de entrega al cliente

### 🖥️ SCRIPTS_SERVIDOR/
**Scripts para pruebas y configuración del servidor**
- `server.php` - Script del servidor de desarrollo
- `server-evidence.php` - Script de evidencia del servidor
- `phpinfo.php` - Información de PHP del servidor

### 📝 DOCUMENTACION_PROYECTO/
**Documentación general del proyecto**
- Documentos de especificaciones
- Manuales de usuario
- Guías técnicas

### 🔧 CONFIGURACION_SERVIDOR/
**Configuraciones específicas del servidor**
- Archivos de configuración
- Scripts de instalación
- Configuraciones de entorno

### 🧪 TESTING_DESARROLLO/
**Archivos de pruebas y desarrollo**
- Scripts de testing
- Pruebas unitarias auxiliares
- Datos de prueba

### 📋 CORRECCIONES_HISTORIAL/
**Historial de correcciones y cambios**
- Documentos de correcciones aplicadas
- Historial de bugs solucionados
- Changelog detallado

### 📈 RESUMEN_TRABAJO/
**Resúmenes y reportes de trabajo**
- Reportes de progreso
- Resúmenes de funcionalidades
- Métricas del proyecto

---

## 🎯 PROPÓSITO DE LA ORGANIZACIÓN

### ✅ VENTAJAS:
1. **Raíz limpia**: Solo archivos esenciales de Laravel en la raíz
2. **Fácil navegación**: Cada tipo de archivo en su lugar apropiado
3. **Mejor organización**: Estructura lógica por categorías
4. **Mantenimiento**: Fácil encontrar y actualizar documentación
5. **Despliegue**: Raíz preparada para producción

### 📁 ARCHIVOS QUE PERMANECEN EN LA RAÍZ:
- `artisan` - CLI de Laravel
- `server.php` - Servidor de desarrollo de Laravel
- `composer.json` / `composer.lock` - Dependencias
- `package.json` - Dependencias de Node.js
- `webpack.mix.js` - Configuración de Mix
- `phpunit.xml` - Configuración de pruebas
- `README.md` - Documentación principal
- `.env*` - Archivos de configuración de entorno
- `.git*` - Archivos de Git
- Carpetas principales de Laravel: `app/`, `config/`, `database/`, etc.

---

## 🚀 COMANDOS ÚTILES

### Para encontrar archivos específicos:
```bash
# Buscar en diagnósticos MySQL
ls tools/DIAGNOSTICOS_MYSQL/

# Buscar herramientas de iPage
ls tools/DESPLIEGUE_IPAGE/

# Ver reportes del proyecto
ls tools/REPORTES_PROYECTO/
```

### Para agregar nuevos archivos:
```bash
# Colocar en la carpeta apropiada según su propósito
# Actualizar este README si es necesario
```

---

## 📞 REFERENCIAS RÁPIDAS

- **Para despliegue en iPage**: Ver `DESPLIEGUE_IPAGE/`
- **Para problemas de MySQL**: Ver `DIAGNOSTICOS_MYSQL/`
- **Para información del proyecto**: Ver `REPORTES_PROYECTO/`
- **Para pruebas de servidor**: Ver `SCRIPTS_SERVIDOR/`

---

*Organización completada el 4 de septiembre de 2025*  
*Raíz del proyecto limpia y optimizada para producción* ✅
