# Scripts de Despliegue

Este directorio contiene scripts y herramientas para el despliegue del proyecto Jireh en diferentes entornos.

## 📁 Archivos incluidos:

### 🔧 Scripts PowerShell
- `configurar.ps1` - Script de configuración general del proyecto
- `preparar_para_ipage.ps1` - Script de preparación específica para despliegue en iPage

### 🐘 Scripts PHP
- `preparar-ipage.php` - Script PHP para preparación de archivos para iPage
- `diagnostico-ipage.php` - Script de diagnóstico del entorno iPage

## 📋 Uso:

### Scripts PowerShell (.ps1):
```powershell
# Ejecutar desde la raíz del proyecto
.\tools\SCRIPTS_DESPLIEGUE\configurar.ps1
.\tools\SCRIPTS_DESPLIEGUE\preparar_para_ipage.ps1
```

### Scripts PHP:
```bash
# Ejecutar desde la raíz del proyecto
php tools/SCRIPTS_DESPLIEGUE/preparar-ipage.php
php tools/SCRIPTS_DESPLIEGUE/diagnostico-ipage.php
```

## ⚙️ Descripción de Scripts:

### `configurar.ps1`
- **Propósito**: Configuración general del entorno de desarrollo
- **Funcionalidad**: Configura permisos, directorios y dependencias
- **Uso**: Ejecutar después de clonar el repositorio

### `preparar_para_ipage.ps1`
- **Propósito**: Preparación específica para servidor iPage
- **Funcionalidad**: Ajusta configuraciones y archivos para el hosting
- **Uso**: Ejecutar antes de subir archivos a iPage

### `preparar-ipage.php`
- **Propósito**: Preparación de archivos PHP para iPage
- **Funcionalidad**: Modifica rutas y configuraciones específicas
- **Uso**: Ejecutar en el servidor o localmente antes del despliegue

### `diagnostico-ipage.php`
- **Propósito**: Diagnóstico del entorno iPage
- **Funcionalidad**: Verifica configuraciones y conexiones
- **Uso**: Ejecutar en el servidor para validar el entorno

## ⚠️ Requisitos:

### Para Scripts PowerShell:
- Windows PowerShell 5.1 o superior
- Permisos de ejecución habilitados
- Acceso de escritura al directorio del proyecto

### Para Scripts PHP:
- PHP CLI 7.4 o superior
- Extensiones PHP requeridas por Laravel
- Acceso a base de datos (para scripts de diagnóstico)

## 🔐 Precauciones:

1. **Backup**: Siempre haz backup antes de ejecutar scripts de preparación
2. **Testing**: Prueba los scripts en entorno de desarrollo primero
3. **Permisos**: Verifica que tienes los permisos necesarios
4. **Configuración**: Revisa que los archivos .env estén correctamente configurados

## 📝 Mantenimiento:

- Mantén los scripts actualizados con los cambios del proyecto
- Documenta cualquier modificación en los scripts
- Prueba los scripts después de cambios importantes en el proyecto
- Mantén versiones de backup de scripts que funcionan correctamente

---
*Organizado: Septiembre 16, 2025*  
*Ubicación anterior: Raíz del proyecto*