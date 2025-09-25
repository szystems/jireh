# GitHub Copilot Instructions - Sistema Jireh

## Información del Proyecto

**Sistema Jireh v1.7.3** es una plataforma integral de gestión empresarial para PyMEs desarrollada en Laravel 8, con MySQL, Bootstrap 5 y jQuery. El sistema maneja 7 módulos principales: Admin, Inventario, Ventas, Personal, Finanzas, Cotizaciones y Centro de Ayuda.

## Convenciones de Código

### Nomenclatura y Estructura
- **Controladores**: `CamelCase` en namespace `App\Http\Controllers\Admin\`
- **Modelos**: `CamelCase` con relaciones Eloquent (ej: `Articulo`, `Cotizacion`, `DetalleVenta`)
- **Vistas**: `snake_case` en `resources/views/admin/[modulo]/`
- **Rutas**: Agrupadas por middleware `admin` en `routes/web.php`
- **Base de datos**: Tablas y campos en `snake_case`

### Autenticación y Roles
- Sistema basado en `role_as`: 0=Admin, 1=Gerente, 2=Vendedor
- Middleware personalizado para control de acceso por rol
- Verificación con `@if (Auth::user()->role_as == 0)` en vistas

### Frontend Patterns
- **Bootstrap 5** como framework CSS principal
- **DataTables** para tablas con `#datatable` ID estándar
- **Select2** para campos de selección múltiple
- **SweetAlert2** para confirmaciones y notificaciones
- Validaciones JavaScript no intrusivas (validar al abandonar campo)

## Arquitectura Clave

### Estructura de Controladores
```
app/Http/Controllers/Admin/
├── DashboardController.php     # Dashboard principal
├── ArticuloController.php      # Gestión de productos/inventario  
├── VentaController.php         # Sistema de ventas
├── CotizacionController.php    # Cotizaciones con estados automáticos
├── TrabajadorController.php    # Gestión de personal
├── ComisionController.php      # Cálculo de comisiones
└── AyudaController.php         # Centro de ayuda integrado
```

### Flujos de Datos Críticos
1. **Inventario**: `Articulo` → `DetalleIngreso` → `MovimientoStock`
2. **Ventas**: `Venta` → `DetalleVenta` → actualización automática de stock
3. **Comisiones**: `DetalleVenta` → `TrabajadorDetalleVenta` → `Comision` → `PagoComision`
4. **Cotizaciones**: Estados automáticos (pendiente → aprobado → facturado)

### Validaciones Inteligentes (v1.7.2+)
- Campos de cantidad respetan tipo de unidad (entera/decimal)
- Separador decimal nativo: punto (.)
- Validación solo al abandonar campo (`blur` event)
- Preserva funcionalidad Select2 y auto-completado

## Comandos de Desarrollo

### Entorno Local
```bash
# Servidor de desarrollo
php artisan serve

# Migraciones y seeders
php artisan migrate:fresh --seed

# Cache y optimización
php artisan config:cache
php artisan view:cache
```

### Base de Datos
- Configuración local: `config/database_local.php`
- Configuración producción: `config/database_ipage.php`
- Migraciones en `database/migrations/`

## Patrones de Implementación

### CRUD Estándar
- Métodos: `index`, `create`, `store`, `show`, `edit`, `update`, `destroy`
- Validación en Request classes cuando es compleja
- Respuestas JSON para AJAX con `->response()->json()`

### Relaciones Eloquent
- `hasMany`/`belongsTo` siguiendo convenciones Laravel
- Carga eager loading con `with()` para optimizar consultas
- Soft deletes en modelos críticos

### Centro de Ayuda (v1.7.0+)
- Documentación integrada en `/admin/ayuda`
- Secciones: Inicio, Módulos, Funciones, Soporte
- Contenido en vistas Blade separadas por sección

## Gestión de Versiones

### Archivos a Actualizar con Nueva Versión
1. `resources/views/layouts/incadmin/sidebar.blade.php` - Footer version
2. `resources/views/admin/ayuda/sections/soporte.blade.php` - Info sistema
3. `README.md` - Badge de versión
4. `docs/PROJECT_STATUS.md` - Estado actual

### Documentación Obligatoria
- Leer `docs/AGENT_INITIALIZATION_GUIDE.md` al iniciar
- Seguir `docs/VERSION_MANAGEMENT_PROTOCOL.md` para versiones
- Consultar `docs/CHANGELOG.md` para historial completo