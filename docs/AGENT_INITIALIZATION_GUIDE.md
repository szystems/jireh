# 🤖 Guía para Inicializar Agentes - Sistema Jireh

## 🎯 Objetivo
Esta guía te ayuda a inicializar correctamente cualquier agente de IA para trabajar en el Sistema Jireh, asegurando que cargue todo el contexto necesario desde el primer momento.

---

## ⚡ Prompt de Inicialización Rápida

### **Copia y pega este prompt al iniciar un nuevo agente:**

```
SISTEMA JIREH v1.7.0 - INICIALIZACIÓN COMPLETA

Soy desarrollador del Sistema Jireh (Laravel 8 + MySQL + Bootstrap 5).

CARGAR CONTEXTO OBLIGATORIO:
1. ✅ Lee .copilot-context - Estado completo del proyecto  
2. ✅ Revisa estructura: app/Http/Controllers/Admin/, resources/views/admin/
3. ✅ Identifica versión actual: v1.7.0 (Centro de Ayuda implementado)
4. ✅ Entiende convenciones: PSR-4, snake_case BD, CamelCase clases
5. ✅ Revisa 7 módulos: Admin, Inventario, Ventas, Personal, Finanzas, Cotizaciones, Centro de Ayuda

PROTOCOLO DE VERSIONES: .copilot-context/VERSION_MANAGEMENT.md

TAREA: [DESCRIBE AQUÍ LO QUE NECESITAS HACER]

Confirma que has cargado el contexto correctamente y procede con la tarea manteniendo todas las convenciones establecidas.
```

---

## 🔍 Verificación de Contexto Cargado

**Después del prompt inicial, el agente debe poder responder:**

1. **¿Cuál es la versión actual?** → v1.7.0
2. **¿Cuántos módulos principales hay?** → 7 módulos
3. **¿Cuál es el stack tecnológico?** → Laravel 8 + MySQL + Bootstrap 5  
4. **¿Cuál fue la última funcionalidad agregada?** → Centro de Ayuda (Sep 18, 2025)
5. **¿Dónde está el protocolo de versiones?** → .copilot-context/VERSION_MANAGEMENT.md

**Si el agente puede responder correctamente, el contexto está cargado ✅**

---

## 📋 Prompts Especializados

### **Para Desarrollo Frontend:**
```
Contexto Jireh v1.7.0 cargado ✓

Trabajaré en frontend. Revisar:
- Layout: resources/views/layouts/admin.blade.php
- Centro de Ayuda: resources/views/admin/ayuda/ (referencia v1.7.0)
- Stack: Bootstrap 5 + DataTables + jQuery

Mantener consistencia con diseño existente.
```

### **Para Desarrollo Backend:**
```
Contexto Jireh v1.7.0 cargado ✓

Trabajaré en backend. Revisar:
- Controllers: app/Http/Controllers/Admin/
- Models: app/Models/
- Routes: routes/web.php (middleware 'auth')

Seguir patrones de controllers existentes.
```

### **Para Actualizar Documentación:**
```
Contexto Jireh v1.7.0 cargado ✓

Actualizar documentación para [MÓDULO].
Revisar Centro de Ayuda existente como referencia.
Seguir formato y estructura establecida.
```

---

## 🎯 Casos de Uso Frecuentes

### **1. Agregar Nueva Funcionalidad:**
```
Jireh v1.7.0 ✓ - Implementar [FUNCIONALIDAD]
Seguir arquitectura existente y convenciones del proyecto.
```

### **2. Corregir Bug:**
```
Sistema Jireh v1.7.0 ✓ - Problema: [DESCRIPCIÓN]
Analizar con contexto completo del proyecto.
```

### **3. Actualizar Versión:**
```
Jireh ✓ - Funcionalidad completada: [DESCRIPCIÓN]
Aplicar protocolo VERSION_MANAGEMENT.md para nueva versión.
```

### **4. Mejorar Módulo Existente:**
```
Contexto Jireh v1.7.0 ✓ - Mejorar módulo [NOMBRE]
Revisar implementación actual antes de modificar.
```

---

## 📁 Archivos de Referencia Críticos

### **Contexto del Proyecto:**
- **.copilot-context** → Estado completo y actualizado
- **.copilot-context/VERSION_MANAGEMENT.md** → Protocolo de versiones
- **.copilot-context/QUICK_START_PROMPTS.md** → Prompts resumidos

### **Documentación:**
- **docs/VERSION_MANAGEMENT_PROTOCOL.md** → Protocolo detallado
- **docs/CENTRO_AYUDA_v1.7.0.md** → Doc del Centro de Ayuda

### **Código Clave:**
- **app/Http/Controllers/Admin/** → Controladores principales
- **resources/views/admin/** → Vistas del sistema  
- **resources/views/admin/ayuda/** → Centro de Ayuda (ejemplo v1.7.0)

---

## ⚠️ Errores Comunes

### **❌ NO hagas esto:**
- Usar prompts genéricos: "Ayúdame con Laravel"
- Omitir la carga de contexto
- No mencionar la versión del proyecto
- Trabajar sin revisar convenciones existentes

### **✅ SÍ haz esto:**
- Siempre usar prompt de inicialización completo
- Mencionar "Sistema Jireh v1.7.0"
- Referir arquitectura y convenciones existentes
- Verificar que el agente entienda el contexto

---

## 🚀 Flujo Recomendado

1. **Usar prompt de inicialización** completo
2. **Verificar contexto** con preguntas de validación
3. **Describir tarea específica** con contexto cargado
4. **Proceder con desarrollo** manteniendo convenciones
5. **Actualizar versiones** si es necesario (protocolo)

---

## 💡 Tips Adicionales

### **Para Continuidad:**
- "Continúa con contexto Jireh v1.7.0 previamente cargado"

### **Para Cambio de Enfoque:**
- "Mantén contexto Jireh - Ahora trabajar en [NUEVO ÁREA]"

### **Para Verificación Final:**
- "Verificar consistencia con arquitectura Sistema Jireh"

---

**🎯 Resultado:** Con estos prompts, cualquier agente cargará el contexto completo del Sistema Jireh en menos de 2 minutos y trabajará eficientemente manteniendo todas las convenciones y patrones establecidos.

---
**Última actualización:** 18 de septiembre, 2025  
**Versión del sistema:** v1.7.0  
**Creado para:** Desarrolladores y agentes del Sistema Jireh