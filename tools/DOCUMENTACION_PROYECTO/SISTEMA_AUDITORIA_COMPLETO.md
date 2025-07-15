# 🔍 Sistema de Auditoría de Ventas e Inventario - Jireh Automotriz

## 📋 Descripción General

El Sistema de Auditoría de Ventas e Inventario es una solución robusta implementada para **Jireh Automotriz** que garantiza la integridad de los datos de ventas e inventario, previene inconsistencias y proporciona herramientas de corrección automática y manual.

## ✨ Características Principales

### 🛡️ Prevención de Inconsistencias
- **Validación de Stock en Tiempo Real**: Uso del trait `StockValidation` que valida disponibilidad antes de cada venta
- **Locks de Base de Datos**: Prevención de condiciones de carrera en operaciones concurrentes
- **Actualizaciones Seguras**: Validación de stock negativo y trazabilidad completa

### 🔍 Detección de Problemas
- **Stock Negativo**: Detección automática de stock por debajo de cero
- **Inconsistencias de Stock**: Comparación entre stock actual y stock teórico basado en movimientos
- **Ventas Duplicadas**: Identificación de posibles ventas duplicadas por cliente, fecha y artículos
- **Detalles Sospechosos**: Detección de cantidades anómalas o datos incorrectos

### 🔧 Corrección Automática y Manual
- **Corrección Automática**: Ajuste de stock basado en movimientos registrados
- **Ajustes Manuales**: Interface para correcciones administrativas con justificación
- **Fusión de Ventas**: Herramienta para consolidar ventas duplicadas
- **Eliminación Segura**: Eliminación de detalles o ventas con devolución automática de stock

### 📊 Reportes y Dashboards
- **Dashboard Principal**: Vista general con estadísticas y alertas
- **Stock en Tiempo Real**: Monitoreo continuo del inventario
- **Alertas de Stock**: Notificaciones de stock bajo y crítico
- **Inconsistencias de Ventas**: Listado detallado de problemas detectados
- **Reportes de Auditoría**: Informes JSON con detalles completos

## 🚀 Cómo Usar el Sistema

### 1. Acceso al Dashboard
1. Navegar a **Ventas > Auditoría de Ventas** en el menú principal
2. El dashboard muestra un resumen general del estado del sistema

### 2. Ejecutar Auditoría Manual
1. En el dashboard, hacer clic en **"Ejecutar Auditoría Completa"**
2. Configurar parámetros:
   - **Días hacia atrás**: 7, 15, 30, 60 o 90 días
   - **Artículo específico**: Opcional, para auditar un artículo en particular
   - **Aplicar correcciones**: ⚠️ **USAR CON PRECAUCIÓN** - Modifica datos automáticamente
3. Los resultados se muestran en tiempo real

### 3. Monitorear Stock en Tiempo Real
1. Navegar a **Stock en Tiempo Real**
2. Ver estado actual de todos los artículos con:
   - Stock actual vs stock teórico
   - Nivel de consistencia
   - Última venta registrada
3. Filtrar por categoría, estado o buscar artículos específicos
4. Exportar reportes en Excel o PDF

### 4. Gestionar Alertas de Stock
1. Ir a **Alertas de Stock**
2. Ver alertas clasificadas por severidad:
   - **Críticas**: Stock negativo
   - **Advertencias**: Stock bajo (≤10 unidades)
3. Acciones disponibles:
   - **Ver Historial**: Movimientos recientes del artículo
   - **Corregir Stock**: Ajuste automático basado en ventas
   - **Ajuste Manual**: Corrección administrativa con justificación
   - **Reabastecer**: Agregar unidades al inventario

### 5. Corregir Inconsistencias de Ventas
1. Acceder a **Inconsistencias de Ventas**
2. Revisar problemas detectados:
   - **Detalles Sospechosos**: Cantidades anómalas
   - **Ventas Duplicadas**: Posibles duplicaciones
3. Acciones de corrección:
   - **Comparar Ventas**: Ver detalles lado a lado
   - **Corregir Detalle**: Modificar cantidad con justificación
   - **Fusionar Ventas**: Consolidar ventas duplicadas
   - **Eliminar**: Remover detalles o ventas problemáticas

## 🤖 Auditorías Automáticas

### Comandos Disponibles

#### Auditoría Manual
```bash
# Auditoría básica
php artisan ventas:auditoria

# Auditoría con parámetros
php artisan ventas:auditoria --dias=30 --fix --articulo=123

# Auditoría automática
php artisan auditoria:automatica --dias=7 --enviar-alertas
```

#### Parámetros
- `--dias=N`: Cantidad de días hacia atrás para auditar
- `--fix`: Aplicar correcciones automáticas (⚠️ usar con precaución)
- `--articulo=ID`: Auditar solo un artículo específico
- `--enviar-alertas`: Enviar notificaciones por inconsistencias críticas

### Programación Automática
El sistema ejecuta automáticamente:
- **Auditoría Diaria**: Todos los días a las 06:00
- **Auditoría Semanal**: Domingos a las 02:00  
- **Verificación de Stock**: Cada 4 horas

## 📁 Archivos y Ubicaciones

### Rutas de Archivos
- **Reportes JSON**: `storage/app/auditorias/`
- **Configuración**: `config/auditoria.php`
- **Logs**: `storage/logs/laravel.log`

### Archivos Principales
- **AuditoriaController**: `app/Http/Controllers/Admin/AuditoriaController.php`
- **AuditoriaVentas Command**: `app/Console/Commands/AuditoriaVentas.php`
- **StockValidation Trait**: `app/Traits/StockValidation.php`
- **VentaController**: `app/Http/Controllers/Admin/VentaController.php` (con validaciones integradas)

## 🛠️ Configuración

### Archivo `config/auditoria.php`
```php
'stock' => [
    'limite_stock_bajo' => 10,      // Unidades para considerar stock bajo
    'limite_stock_critico' => 0,    // Límite para stock crítico
],

'auditoria' => [
    'dias_por_defecto' => 30,       // Días por defecto para auditorías
    'auto_correccion_habilitada' => false, // Habilitar corrección automática
],
```

## 🔒 Seguridad y Permisos

### Control de Acceso
- Solo usuarios con rol de **Administrador** (`role_as != 1`) pueden acceder al sistema completo
- Las correcciones automáticas requieren confirmación explícita
- Todas las operaciones se registran en logs con trazabilidad completa

### Trazabilidad
Cada operación registra:
- Usuario responsable
- Timestamp de la operación
- Valores anteriores y nuevos
- Motivo de la corrección (en ajustes manuales)

## 📈 Interpretación de Resultados

### Estados de Stock
- **🟢 Consistente**: Stock actual coincide con movimientos registrados
- **🟡 Inconsistente**: Diferencia entre stock actual y teórico
- **🔴 Crítico**: Stock negativo o inconsistencias graves

### Severidad de Inconsistencias
- **CRÍTICA**: Stock negativo o problemas que afectan ventas
- **ADVERTENCIA**: Stock bajo o inconsistencias menores
- **INFORMACIÓN**: Discrepancias detectadas que requieren revisión

### Tipos de Problemas
- **STOCK_NEGATIVO**: Artículos con stock por debajo de cero
- **STOCK_INCONSISTENTE**: Diferencia entre stock actual y teórico
- **VENTA_DUPLICADA**: Posibles ventas duplicadas por mismo cliente/fecha
- **DETALLE_SOSPECHOSO**: Cantidades anómalas o artículos no válidos

## 🆘 Solución de Problemas

### Stock Negativo
1. **Identificar causa**: Revisar ventas recientes en el historial
2. **Verificar datos**: Comparar con inventario físico
3. **Corregir**: Usar "Corregir Stock" o "Ajuste Manual"
4. **Prevenir**: El sistema ahora valida antes de cada venta

### Ventas Duplicadas
1. **Comparar ventas**: Usar herramienta de comparación
2. **Verificar legitimidad**: Confirmar si son ventas reales separadas
3. **Fusionar o eliminar**: Según corresponda
4. **Ajustar stock**: El sistema ajusta automáticamente

### Inconsistencias Persistentes
1. **Ejecutar auditoría completa**: Con mayor rango de días
2. **Revisar logs**: Buscar patrones en `storage/logs/laravel.log`
3. **Contactar soporte**: Si los problemas persisten

## 📞 Soporte y Mantenimiento

### Logs del Sistema
```bash
# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Buscar eventos de auditoría
grep "auditoria\|Stock\|Venta" storage/logs/laravel.log
```

### Comandos de Diagnóstico
```bash
# Verificar comandos disponibles
php artisan list | grep auditoria

# Estado de tareas programadas
php artisan schedule:list

# Limpiar cache si hay problemas
php artisan cache:clear
php artisan config:clear
```

### Mantenimiento Preventivo
- **Ejecutar auditorías regulares**: Al menos semanalmente
- **Monitorear alertas**: Revisar dashboard diariamente
- **Backup de reportes**: Los JSON se almacenan automáticamente
- **Limpieza de logs**: Rotar logs periódicamente

## 🎯 Mejores Prácticas

### Para Operadores
1. **Verificar stock antes de ventas grandes**: Especialmente para artículos con stock bajo
2. **Revisar alertas diariamente**: Especialmente las críticas
3. **Justificar ajustes manuales**: Siempre incluir motivo detallado
4. **Reportar inconsistencias persistentes**: No intentar corregir problemas complejos

### Para Administradores
1. **Ejecutar auditorías semanales**: Con revisión manual de resultados
2. **Configurar notificaciones**: Para recibir alertas de problemas críticos
3. **Revisar reportes históricos**: Identificar patrones y tendencias
4. **Mantener backups**: De la base de datos antes de correcciones masivas

### Para Desarrolladores
1. **Usar StockValidation trait**: En cualquier operación que afecte inventario
2. **Registrar operaciones**: Usar logs detallados para trazabilidad
3. **Validar entrada**: Siempre validar datos antes de modificar stock
4. **Implementar tests**: Para nuevas funcionalidades relacionadas

---

**🏁 El sistema está ahora completamente implementado y funcional. Proporciona una solución robusta para mantener la integridad de datos en Jireh Automotriz, con herramientas tanto para prevención como para corrección de inconsistencias.**
