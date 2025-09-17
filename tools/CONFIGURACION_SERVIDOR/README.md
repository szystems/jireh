# Configuración del Servidor

Este directorio contiene archivos de configuración relacionados con el servidor web, entornos y despliegue del proyecto Jireh.

## 📁 Archivos incluidos:

### 🔧 Configuraciones de Entorno (.env)
- `.env.backup` - Respaldo de configuración de entorno
- `.env.production` - Configuración para entorno de producción
- `.env_debug_ipage` - Configuración para debug en servidor iPage
- `.env_ipage_optimizado` - Configuración optimizada para servidor iPage
- `.env_jirehapp` - Configuración para aplicación JirehApp
- `.env_jirehapp_corregido` - Configuración corregida para JirehApp
- `.env_local` - Configuración para desarrollo local

### ⚙️ Configuraciones de Servidor Web
- `.htaccess_ipage_basico` - Configuración básica de Apache para iPage
- `CONFIGURACIONES_HTACCESS.md` - Diferentes configuraciones de .htaccess para diversos entornos

## 📋 Instrucciones de uso:

### Para Configurar un Entorno:
1. Copia el archivo de configuración apropiado desde esta carpeta
2. Renómbralo como `.env` en la raíz del proyecto
3. Ajusta las variables según las necesidades específicas del entorno
4. Verifica que todas las credenciales sean correctas

### Entornos Disponibles:
- **Local**: Usa `.env_local` para desarrollo en tu máquina
- **iPage**: Usa `.env_ipage_optimizado` para el servidor de producción
- **JirehApp**: Usa `.env_jirehapp_corregido` para la aplicación específica
- **Debug**: Usa `.env_debug_ipage` para debugging en iPage

### Configuraciones de Servidor Web:
1. **Desarrollo local:** Usar configuración básica sin redirecciones
2. **Staging:** Activar redirección HTTPS
3. **Producción:** Activar todas las redirecciones

## ⚠️ Notas importantes:
- **Seguridad**: Estos archivos .env contienen información sensible como credenciales de base de datos
- **NUNCA** subas archivos .env al repositorio público
- Siempre probar configuraciones en entorno de staging antes de producción
- Verificar que SSL esté configurado antes de activar redirecciones HTTPS
- Las reglas de redirección pueden causar loops si no están bien configuradas
- Mantén backups de las configuraciones que funcionan correctamente

## 🔄 Mantenimiento:
- Actualiza las configuraciones cuando cambien las credenciales
- Mantén un backup actualizado de las configuraciones que funcionan
- Documenta cualquier cambio importante en las configuraciones

---
*Actualizado: Septiembre 16, 2025*  
*Archivos movidos desde: Raíz del proyecto y /public*
