# Centro de Ayuda y Gestión de Versiones

## 📚 Centro de Ayuda - Versión 1.7.0

### 🎯 Funcionalidades Implementadas

El Centro de Ayuda es una funcionalidad completa agregada al Sistema Jireh que proporciona:

#### ✅ **Características Principales:**
- **Navegación por Tabs:** 4 secciones organizadas (Primeros Pasos, Módulos, FAQ, Soporte)
- **Contenido Diferenciado por Roles:** Administradores vs Vendedores
- **Diseño Responsivo:** Compatible con dispositivos móviles
- **Documentación Completa:** Todos los módulos del sistema documentados
- **Estructura Organizada:** Acordeones y categorías claras

#### 🗂️ **Estructura de Archivos:**

```
app/Http/Controllers/Admin/
└── AyudaController.php

resources/views/admin/ayuda/
├── index.blade.php
└── sections/
    ├── primeros-pasos.blade.php
    ├── modulos.blade.php
    ├── faq.blade.php
    └── soporte.blade.php

routes/web.php (rutas agregadas)
resources/views/layouts/incadmin/sidebar.blade.php (navegación)
```

#### 🔗 **Rutas Implementadas:**
- `/ayuda` - Página principal
- `/ayuda/primeros-pasos` - Guía de introducción
- `/ayuda/modulos` - Documentación de módulos
- `/ayuda/faq` - Preguntas frecuentes
- `/ayuda/soporte` - Contacto y soporte técnico

#### 🎨 **Tecnologías Utilizadas:**
- **Laravel 8:** Framework backend
- **Bootstrap 5:** Framework CSS
- **Blade Templates:** Motor de plantillas
- **JavaScript:** Navegación de tabs
- **Responsive Design:** Para dispositivos móviles

### 📈 **Contenido Especializado:**

#### 🛠️ **Artículos y Servicios - Documentación Mejorada:**
- **Unidades de Medida:** Diferenciación entre enteros y decimales
- **Servicios de Mecánico:** Configuración de comisiones específicas
- **Servicios de Car Wash:** Asignación durante la venta
- **Componentes de Servicios:** Gestión obligatoria de componentes

#### 👥 **Diferenciación por Roles:**
- **Administradores:** Acceso completo a toda la documentación
- **Vendedores:** Contenido adaptado a sus permisos y funciones

### 🔄 **Protocolo de Mantenimiento:**

Para mantener actualizada la documentación del Centro de Ayuda:

1. **Nuevas Funcionalidades:** Documentar en la sección de Módulos correspondiente
2. **Preguntas Frecuentes:** Agregar nuevas FAQ basadas en consultas de usuarios
3. **Versiones:** Actualizar historial en la sección de Soporte
4. **Contactos:** Mantener información de Szystems actualizada

### 🎓 **Guía de Uso para Desarrolladores:**

#### **Agregar Nueva Sección a un Módulo:**
1. Editar archivo correspondiente en `sections/`
2. Seguir estructura de `help-card`
3. Usar iconos Bootstrap apropiados
4. Mantener diferenciación por roles con `@if($isAdmin)`

#### **Agregar Nueva FAQ:**
1. Editar `faq.blade.php`
2. Agregar nuevo item al acordeón
3. Usar estructura consistente con alertas

#### **Actualizar Información de Contacto:**
1. Editar `soporte.blade.php`
2. Actualizar datos de Szystems
3. Verificar enlaces funcionales

---

## 🔄 Gestión de Versiones

### 📍 **Ubicaciones Críticas para Actualizar:**

1. **Sidebar:** `resources/views/layouts/incadmin/sidebar.blade.php`
2. **Centro de Ayuda:** `resources/views/admin/ayuda/sections/soporte.blade.php`

### 📋 **Checklist de Actualización:**
- [ ] Incrementar versión en sidebar
- [ ] Actualizar información del sistema
- [ ] Agregar entrada al historial
- [ ] Documentar cambios principales
- [ ] Verificar fechas correctas

---
**Documento creado:** 18 de septiembre, 2025  
**Versión del sistema:** 1.7.0  
**Desarrollado por:** Szystems