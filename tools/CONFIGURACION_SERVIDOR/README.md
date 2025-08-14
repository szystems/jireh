# Configuración del Servidor

Este directorio contiene archivos de configuración relacionados con el servidor web y despliegue.

## Archivos incluidos:

### CONFIGURACIONES_HTACCESS.md
- **Descripción:** Diferentes configuraciones de .htaccess para diversos entornos
- **Uso:** Referencia para configurar redirecciones HTTPS y dominio específico
- **Origen:** Movido desde la raíz del proyecto para mejor organización

## Instrucciones de uso:

1. **Desarrollo local:** Usar configuración básica sin redirecciones
2. **Staging:** Activar redirección HTTPS
3. **Producción:** Activar todas las redirecciones

## Notas importantes:
- Siempre probar configuraciones en entorno de staging antes de producción
- Verificar que SSL esté configurado antes de activar redirecciones HTTPS
- Las reglas de redirección pueden causar loops si no están bien configuradas
