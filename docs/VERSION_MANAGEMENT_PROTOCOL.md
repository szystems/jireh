# 📋 Protocolo de Gestión de Versiones - Sistema Jireh

## 🎯 Objetivo
Mantener un control riguroso de las versiones del Sistema Jireh y asegurar que todos los archivos relevantes se actualicen correctamente cuando se agreguen nuevas funcionalidades.

## 🔢 Versión Actual: **1.7.0**

### 📅 Fecha de Última Actualización: 18 de septiembre, 2025

---

## 📍 Archivos que SIEMPRE Deben Actualizarse

### 1. **Sidebar Principal**
- **Archivo:** `resources/views/layouts/incadmin/sidebar.blade.php`
- **Buscar:** `Szystems v1.7.0`
- **Ubicación:** Sección footer del sidebar (~línea 281)
- **Formato:** `<b>Szystems vX.Y.Z</b>`

### 2. **Centro de Ayuda - Información del Sistema**
- **Archivo:** `resources/views/admin/ayuda/sections/soporte.blade.php`
- **Buscar:** `Sistema Jireh v1.7.0`
- **Ubicación:** Sección "Información del Sistema"
- **Actualizar:** Versión y fecha de actualización

### 3. **Centro de Ayuda - Historial de Versiones**
- **Archivo:** `resources/views/admin/ayuda/sections/soporte.blade.php`
- **Ubicación:** Tabla del historial de versiones
- **Acción:** Agregar nueva fila con detalles de la versión

---

## 🎲 Esquema de Versionado Semántico

```
MAJOR.MINOR.PATCH
  |     |     |
  |     |     └── Correcciones de bugs, mejoras menores
  |     └────────── Nuevas funcionalidades, módulos
  └──────────────── Cambios de arquitectura, breaking changes
```

### 📊 Ejemplos:
- **1.7.0 → 1.7.1:** Corrección de bug en Centro de Ayuda
- **1.7.0 → 1.8.0:** Nuevo módulo de Reportes Avanzados  
- **1.7.0 → 2.0.0:** Migración a Laravel 9

---

## 📝 Template para Nueva Versión

### **Historial de Versiones (HTML):**
```html
<tr class="table-success">
    <td><strong>X.Y.Z</strong></td>
    <td>Mes DD, YYYY</td>
    <td>
        <strong>Nombre de la funcionalidad:</strong>
        <ul class="mb-0">
            <li>Característica específica 1</li>
            <li>Característica específica 2</li>
            <li>Mejora o corrección importante</li>
        </ul>
    </td>
</tr>
```

### **Notas importantes:**
- La versión más reciente siempre tiene clase `table-success`
- Remover `table-success` de la versión anterior
- Mantener orden cronológico descendente (más reciente arriba)

---

## 🗓️ Historial Completo de Versiones

| Versión | Fecha | Descripción | Desarrollador |
|---------|-------|-------------|---------------|
| **1.7.0** | Sep 18, 2025 | Centro de Ayuda completo | GitHub Copilot |
| 1.6.0 | Sep 2025 | Módulo de Cotizaciones | Szystems |
| 1.5.x | Ago 2025 | Sistema de comisiones y pagos | Szystems |
| 1.4.x | Jul 2025 | Mejoras en ventas y reportes | Szystems |
| 1.3.x | Jun 2025 | Sistema de inventario avanzado | Szystems |

---

## 🔄 Proceso Paso a Paso

### **Antes de Implementar:**
1. [ ] Definir número de versión según tipo de cambio
2. [ ] Identificar archivos a actualizar
3. [ ] Preparar descripción de cambios

### **Durante la Implementación:**
1. [ ] Desarrollar la funcionalidad
2. [ ] Probar completamente
3. [ ] Documentar en Centro de Ayuda (si aplica)

### **Después de Implementar:**
1. [ ] Actualizar versión en sidebar
2. [ ] Actualizar información del sistema
3. [ ] Agregar entrada al historial
4. [ ] Limpiar cachés (`php artisan view:clear`)
5. [ ] Probar que las versiones se muestren correctamente

---

## ⚠️ Recordatorios Críticos

### 🚨 **NUNCA OLVIDAR:**
- Actualizar **AMBAS** ubicaciones de versión (sidebar + centro de ayuda)
- Mantener formato consistente en fechas
- Verificar que los enlaces funcionen correctamente
- Documentar cambios de manera clara y concisa

### 🎯 **Buenas Prácticas:**
- Usar fechas en formato: "Sep 18, 2025"
- Describir funcionalidades en lenguaje claro para usuarios finales
- Mantener historial limitado a últimas 8-10 versiones para legibilidad
- Respaldar documentación antes de cambios mayores

---

## 📞 Contacto para Dudas

**Soporte Técnico:** oszarata@szystems.com  
**Desarrollo:** Szystems  
**Documentación:** Sistema automatizado

---
**Última revisión:** 18 de septiembre, 2025  
**Próxima revisión:** Con cada actualización del sistema