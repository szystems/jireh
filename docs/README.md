# 📚 Documentación del Proyecto Jireh (BuroSoft)

**Sistema de Gestión Empresarial Laravel 8**

## � Estado Actual (Septiembre 16, 2025)

### ✅ **Módulos Completados**
- **📋 Cotizaciones** ⭐ **100% FUNCIONAL** - Sistema avanzado implementado
- **👥 Trabajadores** - Gestión completa 
- **🚗 Vehículos** - Funcional
- **⚙️ Configuración** - Sistema centralizado

### 🚧 **En Desarrollo**
- Otros módulos según prioridades del negocio

## �🏗️ Arquitectura del Proyecto

### Stack Tecnológico
- **Framework**: Laravel 8.x
- **PHP**: ^7.3|^8.0
- **Base de Datos**: MySQL (compatible con múltiples entornos)
- **Frontend**: Blade Templates + Laravel UI + Bootstrap 5
- **Autenticación**: Laravel Sanctum + Laravel UI Auth
- **PDF Generation**: DomPDF (optimizado)
- **Tablas Interactivas**: DataTables + jQuery

### Dependencias Principales
- **PDF Generation**: barryvdh/laravel-dompdf
- **Excel Export/Import**: maatwebsite/excel
- **CORS**: fruitcake/laravel-cors
- **Database**: doctrine/dbal

## 🏢 Módulos del Sistema

### 1. **Admin** (Administración)
- Gestión de usuarios y permisos
- Configuraciones del sistema
- Dashboard administrativo

### 2. **📋 Cotizaciones** ⭐ **MÓDULO COMPLETO**
- **CRUD Completo**: Crear, editar, ver, eliminar cotizaciones
- **Estados Avanzados**: Sistema dual Estado (Generado/Aprobado) + Vigencia automática
- **Dashboard Especializado**: 5 pestañas (Todas, Generadas, Vigentes, Vencidas, Aprobadas)
- **Regeneración Inteligente**: Reactivar cotizaciones con 15 días frescos
- **PDF Optimizado**: Generación profesional con monedas dinámicas
- **UX Moderna**: DataTables, AJAX, validaciones robustas

### 3. **Inventario**
- Gestión de artículos y categorías
- Control de stock y movimientos
- Proveedores y unidades

### 4. **Ventas**
- Gestión de ventas y clientes
- Detalles de venta y facturación
- Reportes de ventas

### 5. **Trabajadores y Comisiones**
- Gestión de trabajadores
- Cálculo y pago de comisiones
- Metas de ventas
- Pagos de sueldos

### 6. **Finanzas**
- Gestión de pagos
- Lotes de pago
- Control financiero

## 📁 Estructura de Directorios

```
jireh/
├── app/
│   ├── Console/           # Comandos Artisan
│   ├── Exceptions/        # Manejo de excepciones
│   ├── Exports/          # Clases de exportación Excel
│   ├── Helpers/          # Funciones helper
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/    # Controladores administrativos
│   │   │   ├── Api/      # Controladores API
│   │   │   └── Auth/     # Controladores autenticación
│   │   ├── Middleware/   # Middlewares
│   │   └── Requests/     # Form Requests
│   ├── Models/           # Modelos Eloquent
│   ├── Providers/        # Service Providers
│   ├── Services/         # Lógica de negocio
│   └── Traits/           # Traits reutilizables
├── database/
│   ├── factories/        # Model Factories
│   ├── migrations/       # Migraciones de BD
│   └── seeders/         # Seeders de datos
├── docs/                # Documentación del proyecto
│   ├── core/           # Documentación técnica
│   └── visual/         # Diagramas y visuales
├── resources/
│   ├── views/          # Vistas Blade
│   ├── js/             # JavaScript
│   └── css/            # Estilos
└── tools/              # Herramientas de desarrollo
```

## 🗄️ Modelos Principales

### Gestión de Inventario
- `Articulo` - Productos del sistema
- `Categoria` - Categorías de productos
- `Unidad` - Unidades de medida
- `Proveedor` - Proveedores de productos
- `MovimientoStock` - Movimientos de inventario

### Gestión Comercial
- `Cliente` - Clientes del sistema
- `Vehiculo` - Vehículos de clientes
- `Venta` - Ventas realizadas
- `DetalleVenta` - Items de cada venta
- `Pago` - Pagos recibidos

### Gestión de Personal
- `Trabajador` - Empleados del sistema
- `TipoTrabajador` - Tipos de trabajadores
- `Comision` - Comisiones por ventas
- `PagoComision` - Pagos de comisiones
- `PagoSueldo` - Pagos de sueldos
- `DetallePagoSueldo` - Detalles de pagos
- `LotePago` - Lotes de pagos agrupados

### Sistema Core
- `User` - Usuarios del sistema
- `Config` - Configuraciones
- `MetaVenta` - Metas de ventas

## 🛡️ Autenticación y Permisos

### Sistema de Autenticación
- Laravel UI Auth con Bootstrap
- Middleware de autenticación en rutas admin
- Sesiones almacenadas en base de datos

### Estructura de Permisos
- Sistema basado en roles (implícito por controladores Admin)
- Middleware auth aplicado a rutas sensibles
- Separación clara entre frontend y admin

## 🗃️ Base de Datos

### Migraciones Principales (30+ tablas)
- **Sistema Core**: users, sessions, configs
- **Inventario**: articulos, categorias, unidads, proveedors
- **Ventas**: ventas, detalle_ventas, clientes, vehiculos
- **Personal**: trabajadors, tipo_trabajadors, comisiones
- **Pagos**: pagos, pagos_comisiones, pagos_sueldos, lotes_pago

### Características BD
- Uso de foreign keys y relaciones
- Timestamps automáticos
- Soft deletes en modelos críticos
- Índices optimizados para consultas

## 🚀 Comandos de Desarrollo

```bash
# Instalación inicial
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed

# Desarrollo
php artisan serve
php artisan route:list
php artisan tinker

# Testing
php artisan test
vendor/bin/phpunit

# Cache y optimización
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📍 Rutas Principales

### Admin Routes
- `/admin` - Dashboard administrativo
- `/admin/users` - Gestión de usuarios
- `/admin/articulos` - Gestión de artículos
- `/admin/ventas` - Gestión de ventas
- `/admin/trabajadores` - Gestión de trabajadores
- `/admin/comisiones` - Gestión de comisiones

### Auth Routes
- `/login` - Página de login
- `/register` - Registro de usuarios
- `/logout` - Cerrar sesión

## 🔧 Configuración de Entornos

### Desarrollo Local
- Base de datos MySQL local
- Debug habilitado
- Logs detallados

### Producción (iPage)
- Configuraciones optimizadas
- Cache habilitado
- Logs mínimos
- HTTPS forzado

## 📖 Documentación de Referencia

### Archivos Core
- `docs/core/ARCHITECTURE.md` - Arquitectura detallada
- `docs/core/PRD.md` - Product Requirements Document
- `docs/core/API.md` - Documentación de API

### Herramientas de Desarrollo
- `tools/` - Scripts y herramientas
- Configuraciones de entorno organizadas
- Scripts de despliegue automatizados

---

**Última actualización**: Septiembre 16, 2025  
**Versión Laravel**: 8.x  
**Estado**: Producción activa