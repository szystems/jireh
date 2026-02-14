# 📝 Historial de Cambios - Sistema Jireh

Todos los cambios notables en este proyecto están documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/) y este proyecto sigue [Versionado Semántico](https://semver.org/lang/es/).

---

## [v1.7.4] - 2026-01-05

### 🐛 Corrección Crítica: Precios Históricos en Ventas
- **Bug corregido**: Las ventas ahora muestran el precio al momento de la venta, no el precio actual del artículo
- **Patrón implementado**: `$detalle->precio_venta > 0 ? $detalle->precio_venta : (fallback)`
- **Compatibilidad legacy**: Fallback automático para registros antiguos sin precio guardado

### 🎯 Archivos Corregidos (29 correcciones en 12 archivos)

#### Vistas de Ventas
- `resources/views/admin/venta/index.blade.php` - 10 correcciones
- `resources/views/admin/venta/show.blade.php` - 1 corrección
- `resources/views/admin/venta/single_pdf.blade.php` - 1 corrección
- `resources/views/admin/venta/edit.blade.php` - 1 corrección

#### Vistas de Reportes
- `resources/views/admin/reportearticulo/index.blade.php` - 1 corrección
- `resources/views/admin/reportearticulo/pdf.blade.php` - 2 correcciones

#### Vistas de Cotizaciones
- `resources/views/admin/cotizacion/index.blade.php` - 1 corrección

#### Controladores
- `app/Http/Controllers/Admin/VentaController.php` - 1 corrección (calcularTotalesVenta)
- `app/Http/Controllers/Admin/ReporteArticuloController.php` - 8 correcciones
- `app/Http/Controllers/Admin/PagoController.php` - 1 corrección

#### Validaciones
- `app/Http/Requests/PagoFormRequest.php` - 1 corrección

#### Scripts de Testing
- `tools/TESTING_DESARROLLO/validar_calculos_costos.php` - 1 corrección

### 🔍 Problema Resuelto
```
ANTES (incorrecto):
$precioUnitario = $detalle->articulo->precio_venta;
// Mostraba precio ACTUAL del artículo

DESPUÉS (correcto):
$precioUnitario = $detalle->precio_venta > 0 
    ? $detalle->precio_venta 
    : ($detalle->articulo ? $detalle->articulo->precio_venta : ($detalle->sub_total / $detalle->cantidad));
// Muestra precio HISTÓRICO guardado al momento de la venta
```

### ✅ Verificaciones Realizadas
- Auditoría completa del código fuente
- Verificación de exports (VentasExport.php ya era correcto)
- Verificación de modelos (Venta.php usa sum('sub_total') correctamente)
- Verificación de vistas de cotización (ya usaban precio histórico)

---

## [v1.7.3] - 2025-09-22

### 🧮 Corrección de Cálculos de Rentabilidad
- **Lógica corregida**: Precio de venta incluye IVA por defecto
- **Cálculo IVA**: Sobre precio base sin IVA (precio_venta ÷ 1.12)
- **Costo total**: precio_compra + IVA + comisiones 
- **Ganancia real**: precio_venta - costo_total
- **Margen**: ganancia_real ÷ costo_total × 100

### 🎯 Archivos Modificados
- `public/js/articulo-script.js` - Función calcularMargen() corregida
- `resources/views/admin/articulo/show.blade.php` - Análisis rentabilidad
- `resources/views/admin/articulo/add.blade.php` - Preview rentabilidad 
- `resources/views/admin/articulo/edit.blade.php` - Preview rentabilidad
- `app/Http/Controllers/Admin/ArticuloController.php` - Export PDF

### 📊 Mejoras Visuales
- **Nueva fila**: "Precio base sin IVA" en todas las vistas
- **Etiquetas actualizadas**: "Precio de venta (incluye IVA)"
- **IVA clarificado**: Etiqueta "IVA" en lugar de "Impuesto"
- **Cálculos precisos**: Coinciden exactamente con especificaciones del cliente

### 🔍 Ejemplo de Corrección
```
Antes (INCORRECTO):
Ganancia: Q 260.96, Margen: 37.28%

Después (CORRECTO):  
Ganancia: Q 275.00, Margen: 33.66%
```

---

## [v1.7.2] - 2025-09-18

### ✨ Validaciones No Intrusivas y UX Mejorada
- **Validaciones no intrusivas** para campos de cantidad en Ingresos y Ventas
- **Separador decimal punto (.)** nativo en lugar de coma (,) para mejor UX
- **Tabla responsive** con scroll horizontal en listado de ventas
- **Corrección de roles** de usuario en navegación (Administrador/Vendedor)

### 🎯 Módulos Actualizados
- **Ingresos (create/edit)**: Inputs cantidad/precio con validaciones quirúrgicas
- **Ventas (create/edit)**: Campos cantidad con validaciones no intrusivas  
- **Navegación**: Roles de usuario corregidos según lógica del sistema

### 🔧 Mejoras Técnicas
- **type='text' + inputmode='decimal'** para punto decimal nativo
- **Validaciones blur** no intrusivas basadas en step/min del input
- **Preserva funcionalidad Select2** y auto-población de campos
- **table-responsive** con CSS personalizado para móviles

### 📱 UX Mejorada
- **Escritura libre** sin interrupciones durante entrada de datos
- **Auto-corrección inteligente** al perder foco según tipo de unidad
- **Tabla de ventas navegable** completamente en dispositivos móviles
- **Scrollbar visible** y scroll suave en iOS/Android

### 🛡️ Validaciones Backend
- **IngresoController**: Validación cantidades según unidad.tipo
- **Integridad de datos** con validaciones duales (frontend + backend)
- **Compatibilidad total** con funcionalidad existente

### 🗂️ Archivos Modificados
- `app/Http/Controllers/Admin/IngresoController.php`
- `resources/views/admin/ingreso/create.blade.php`
- `resources/views/admin/ingreso/edit.blade.php`
- `resources/views/admin/venta/create.blade.php`
- `resources/views/admin/venta/edit.blade.php`
- `resources/views/admin/venta/index.blade.php`
- `resources/views/layouts/incadmin/nav.blade.php`

---

## [v1.7.1] - 2025-09-18

### 🔓 Expansión de Permisos de Vendedores
- **Acceso completo a módulo de Compras** (Ingresos y Proveedores)
- **Acceso completo a módulo de Inventario/Catálogos** (Artículos, Categorías, Unidades)
- **Acceso completo a módulo de Trabajadores** (Gestión de Personal)
- **Restricción de eliminación** mantenida para seguridad de datos

### 🛡️ Seguridad Implementada
- **SuperAdminMiddleware** creado para proteger operaciones de eliminación
- **Protección dual**: UI (botones ocultos) + Servidor (middleware)
- **Validación @if(Auth::user()->role_as != 1)** en todas las vistas con botones de eliminar

### 🔄 Cambiado
- **Sidebar navegación** actualizado para vendedores
- **Centro de Ayuda** completamente actualizado con nuevos permisos
- **FAQ** reescrita con detalles de permisos expandidos
- **Documentación de módulos** diferenciada por rol

### 🛠️ Técnico
- **6 vistas actualizadas** con protección de botones eliminar:
  - `ingreso/index.blade.php`
  - `proveedor/index.blade.php` 
  - `trabajador/index.blade.php`
  - `categoria/index.blade.php`
  - `unidad/index.blade.php`
  - `articulo/index.blade.php`
- **Middleware registrado** en `app/Http/Kernel.php`
- **6 rutas protegidas** con middleware `superAdmin`
- **Centro de Ayuda v1.7.1** con documentación actualizada

---

## [v1.7.0] - 2025-09-18

### ✨ Añadido
- **Centro de Ayuda completo** con sistema de documentación integrado
- **4 secciones principales**: Primeros Pasos, Módulos, FAQ, Soporte
- **Contenido diferenciado por roles** (Administrador/Vendedor)
- **Icono de ayuda en navegación** principal para acceso rápido
- **Documentación detallada** de todos los módulos del sistema
- **Guías paso a paso** para configuración de artículos y servicios
- **Información de contacto actualizada** (oszarata@szystems.com)
- **Arquitectura de vistas organizadas** en resources/views/admin/ayuda/

### 🔄 Cambiado
- **Navegación principal** actualizada con icono de ayuda
- **Sidebar** con enlace directo al Centro de Ayuda
- **Documentación del proyecto** completamente actualizada
- **Versión del sistema** mostrada en todas las interfaces

### 🛠️ Técnico
- **AyudaController** implementado con middleware de autenticación
- **Rutas configuradas** para módulo de ayuda (/admin/ayuda/*)
- **Vistas Blade** responsivas con Bootstrap 5
- **Detección automática de roles** para contenido específico

---

## [v1.6.0] - 2025-09-16

### ✨ Añadido
- **Módulo de Cotizaciones completo** con funcionalidades avanzadas
- **Estados inteligentes**: Generado → Aprobado con vigencia automática
- **5 pestañas especializadas**: Todas, Generadas, Vigentes, Vencidas, Aprobadas
- **Regeneración inteligente** de cotizaciones aprobadas
- **PDF optimizado** con configuración de moneda dinámica
- **DataTables** con filtros automáticos por estado

### 🔄 Cambiado
- **Dashboard principal** con acceso directo a cotizaciones
- **Sistema de navegación** optimizado para nuevos módulos

### 🛠️ Técnico
- **CotizacionController** completo con CRUD + PDF + estados
- **Modelos**: Cotizacion y DetalleCotizacion con relaciones
- **Migraciones** para tablas de cotizaciones
- **Middleware** y rutas configuradas

---

## [v1.5.0] - 2025-09-10

### ✨ Añadido
- **Sistema de Personal** completo
- **Gestión de Trabajadores** con tipos y comisiones
- **Cálculo automático** de comisiones por ventas
- **Sistema de Pagos** para sueldos y comisiones
- **Lotes de pago** para procesamiento masivo

### 🔧 Corregido
- **Cálculos de stock** optimizados
- **Reportes de inventario** mejorados
- **Performance** en consultas de base de datos

---

## [v1.4.0] - 2025-09-05

### ✨ Añadido
- **Módulo de Finanzas** con control de pagos
- **Reportes financieros** detallados
- **Sistema de configuración** centralizado
- **Gestión de descuentos** en ventas

### 🔄 Cambiado
- **Interface de usuario** mejorada con Bootstrap 5
- **Dashboard** con métricas visuales

---

## [v1.3.0] - 2025-08-28

### ✨ Añadido
- **Sistema de Ventas** completo
- **Gestión de Clientes** y Vehículos
- **Facturación** con PDF
- **Reportes de ventas** por período

### 🛠️ Técnico
- **Arquitectura MVC** optimizada
- **Relaciones de base de datos** establecidas
- **Validaciones** robustas en formularios

---

## [v1.2.0] - 2025-08-20

### ✨ Añadido
- **Módulo de Inventario** funcional
- **Gestión de Artículos** con categorías
- **Control de Stock** en tiempo real
- **Movimientos de inventario** auditados

### 🔧 Corregido
- **Problemas de autenticación** resueltos
- **Validaciones** de formularios mejoradas

---

## [v1.1.0] - 2025-08-15

### ✨ Añadido
- **Sistema de Autenticación** con Laravel UI
- **Gestión de Usuarios** básica
- **Dashboard** administrativo
- **Estructura base** del proyecto

### 🛠️ Técnico
- **Laravel 8** configurado
- **MySQL** como base de datos
- **Bootstrap 5** para UI
- **Arquitectura** de carpetas establecida

---

## [v1.0.0] - 2025-08-10

### ✨ Añadido
- **Proyecto inicial** creado
- **Configuración** básica de Laravel
- **Estructura** de base de datos
- **Documentación** inicial

---

## Tipos de Cambios

- **✨ Añadido** para nuevas funcionalidades
- **🔄 Cambiado** para cambios en funcionalidades existentes  
- **🔧 Corregido** para corrección de bugs
- **🛠️ Técnico** para cambios técnicos sin impacto visible
- **🗑️ Eliminado** para funcionalidades removidas
- **🔒 Seguridad** para correcciones de vulnerabilidades

---

**Información del Documento:**
- **Mantenido por**: Equipo de Desarrollo Jireh
- **Última actualización**: 18 de septiembre, 2025
- **Formato**: Keep a Changelog v1.0.0
- **Versionado**: Semantic Versioning v2.0.0