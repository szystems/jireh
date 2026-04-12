# Plan de Corrección - Quejas del Cliente v1.7.5

**Fecha:** 11 de abril de 2026  
**Reportado por:** Emilio Rodriguez (cliente)  
**Diagnóstico realizado por:** Agente IA  
**Base de datos analizada:** Copia local de producción (dbjirehapp)

---

## Resumen de Quejas

1. *"Borré varios items de productos y servicios y siguen apareciendo"*
2. *"Las comisiones no las puedo calcular"*
3. *"Tiene muchos clavos"*

---

## Hallazgos Confirmados

### 🔴 BUG #1 — Artículos/servicios eliminados siguen apareciendo en formularios [CRÍTICO]

**Descripción:** La eliminación de artículos cambia `estado` de 1 a 0 (`ArticuloController@destroy`). El listado principal de inventario filtra correctamente por `estado=1`. Sin embargo, **7 consultas en controladores** cargan todos los artículos SIN filtrar por estado, haciendo que los artículos eliminados sigan apareciendo en dropdowns de ventas, cotizaciones e ingresos.

**Datos BD:**
- Artículos activos: 1,254
- Artículos eliminados (estado=0): 25
- Detalles de venta que referencian artículos eliminados: 372

**Archivos afectados y líneas exactas:**

| # | Archivo | Método | Línea | Query actual | Fix |
|---|---------|--------|-------|-------------|-----|
| 1 | `app/Http/Controllers/Admin/VentaController.php` | `create` | 89 | `Articulo::with('unidad')->get()` | Agregar `->where('estado', 1)` |
| 2 | `app/Http/Controllers/Admin/CotizacionController.php` | `create` | 96 | `Articulo::with('unidad')->get()` | Agregar `->where('estado', 1)` |
| 3 | `app/Http/Controllers/Admin/CotizacionController.php` | `edit` | 271 | `Articulo::with('unidad')->get()` | Agregar `->where('estado', 1)` |
| 4 | `app/Http/Controllers/Admin/IngresoController.php` | `create` | 67 | `Articulo::with('unidad')->get()` | Agregar `->where('estado', 1)` |
| 5 | `app/Http/Controllers/Admin/IngresoController.php` | `edit` | 141 | `Articulo::with('unidad')->get()` | Agregar `->where('estado', 1)` |
| 6 | `app/Http/Controllers/Admin/ArticuloController.php` | `edit` | 321 | `Articulo::where('tipo', 'articulo')->get()` | Agregar `->where('estado', 1)` |
| 7 | `app/Http/Controllers/Admin/InventarioController.php` | `buscarArticulos` | ~319 | Query AJAX sin filtro estado | Agregar `->where('estado', 1)` |

**Nota:** `VentaController@edit` (L254) ya filtra correctamente con `->where('estado', 1)`.

**Estado:** ✅ CONFIRMADO — Listo para corregir

---

### 🔴 BUG #2 — Estadísticas de comisiones muestran valores incorrectos [CRÍTICO]

**Descripción:** En `ComisionController@calcularEstadisticas`, se reutiliza el mismo objeto `$query` de Eloquent sin clonarlo. Los `where()` se van acumulando en la misma instancia, provocando que:

- `$total` (primera operación) → correcto
- `$totalPagadas` → agrega `where('estado', 'pagado')` al query → correcto
- `$totalPendientes` → agrega `where('estado', 'pendiente')` AL MISMO query que ya tiene `where('estado', 'pagado')` → **siempre retorna 0** (imposible cumplir ambas condiciones)
- `$cantidadTotal` → hereda ambos where previos → **siempre 0**
- Las siguientes operaciones siguen acumulando → **todo 0**

**Archivo:** `app/Http/Controllers/Admin/ComisionController.php` líneas 398-420

**Código actual (incorrecto):**
```php
$query = Comision::query();
$this->aplicarFiltrosAvanzados($query, $request);
$total = $query->sum('monto');                                    // OK
$totalPagadas = $query->where('estado', 'pagado')->sum('monto');  // OK pero contamina $query
$totalPendientes = $query->where('estado', 'pendiente')->sum('monto'); // ❌ SIEMPRE 0
$cantidadTotal = $query->count();                                  // ❌ SIEMPRE 0
$cantidadPagadas = $query->where('estado', 'pagado')->count();    // ❌ SIEMPRE 0
$cantidadPendientes = $query->where('estado', 'pendiente')->count(); // ❌ SIEMPRE 0
```

**Fix:** Usar `(clone $query)` antes de cada operación con filtro.

**Datos BD:**
- Total comisiones: 3,231
- Pendientes: 3,207
- Canceladas: 24
- Pagadas: 0
- Lotes de pago creados: 0

**Estado:** ✅ CONFIRMADO — Listo para corregir

---

### 🟡 BUG #3 — Clientes eliminados aparecen en formularios [MEDIO]

**Descripción:** Similar al Bug #1, los formularios de venta y cotización cargan clientes con `Cliente::all()` sin filtrar por `estado=1`. Hay 17 clientes eliminados que siguen apareciendo.

**Archivos afectados:**

| # | Archivo | Método | Línea |
|---|---------|--------|-------|
| 1 | `app/Http/Controllers/Admin/VentaController.php` | `index` | 78 |
| 2 | `app/Http/Controllers/Admin/VentaController.php` | `create` | 90 |
| 3 | `app/Http/Controllers/Admin/VentaController.php` | `refacturar` | 724 |
| 4 | `app/Http/Controllers/Admin/CotizacionController.php` | `index` | 82 |
| 5 | `app/Http/Controllers/Admin/CotizacionController.php` | `create` | 97 |
| 6 | `app/Http/Controllers/Admin/CotizacionController.php` | `edit` | 272 |

**Nota:** `VentaController@edit` (L257) ya filtra correctamente con `Cliente::where('estado', 1)`.

**Estado:** ✅ CONFIRMADO — Listo para corregir

---

### 🟡 BUG #4 — Error de favicon en show-articulo [MEDIO]

**Descripción:** Los logs muestran repetidamente: `Error al mostrar artículo: No query results for model [App\Models\Articulo] favicon.ico`. La ruta `show-articulo/{id}` acepta cualquier string como `{id}`, incluyendo `favicon.ico`.

**Archivo:** `app/Http/Controllers/Admin/ArticuloController.php` método `show` (L278)

**Fix:** Agregar constraint numérico en la ruta o validar `$id` en el controlador.

**Estado:** ✅ CONFIRMADO — Listo para corregir

---

### 🟡 HALLAZGO #5 — Metas de venta no configuradas [MEDIO]

**Descripción:** La tabla `metas_ventas` está vacía (0 registros). El sistema de comisiones por metas para vendedores (`tipo_comision = 'venta_meta'`) no puede funcionar sin metas configuradas. Esto explica por qué no existen comisiones de tipo `venta_meta` en la BD.

**Impacto:** Las comisiones de mecánicos (676 pendientes / Q29,565) y carwash (2,531 pendientes / Q27,950) SÍ se generan automáticamente. Pero las comisiones por metas de vendedores NO, por falta de configuración.

**Acción:** Informar al cliente que debe configurar metas en el módulo correspondiente para activar las comisiones de vendedores.

**Estado:** ✅ CONFIRMADO — Requiere acción del cliente

---

### 🟡 BUG #6 — Campo `fecha_pago` inexistente en pagarIndividual [BAJO]

**Descripción:** En `ComisionController@pagarIndividual`, se hace `$comision->update(['estado' => 'pagado', 'fecha_pago' => now()])` pero la columna `fecha_pago` no existe en la tabla `comisiones` ni en `$fillable`.

**Impacto:** Laravel ignora silenciosamente el campo (no causa error), pero el dato nunca se guarda.

**Archivo:** `app/Http/Controllers/Admin/ComisionController.php` método `pagarIndividual`

**Estado:** ✅ CONFIRMADO — Corregir eliminando el campo o agregando migración

---

## Resumen de Datos de la BD (Copia Local)

| Entidad | Activos | Eliminados |
|---------|---------|-----------|
| Artículos | 1,254 | 25 |
| Clientes | 742 | 17 |
| Comisiones pendientes | 3,207 | — |
| Comisiones canceladas | 24 | — |
| Comisiones pagadas | 0 | — |
| Lotes de pago | 0 | — |
| Metas de venta | 0 | — |
| Usuarios | 6 (2 admin, 4 gerentes) | — |

---

## Plan de Trabajo Priorizado

### Fase 1 — Correcciones Críticas (Bugs que afectan operación diaria)

- [x] **1.1** Fix Bug #1: Agregar `->where('estado', 1)` en 7 queries de artículos ✅
  - `VentaController@create` L89
  - `CotizacionController@create` L96
  - `CotizacionController@edit` L271
  - `IngresoController@create` L67
  - `IngresoController@edit` L141
  - `ArticuloController@edit` L321
  - `InventarioController@buscarArticulos` L319

- [x] **1.2** Fix Bug #2: Corregir query acumulativo en `calcularEstadisticas` ✅
  - `ComisionController.php` L398-420: Usar `(clone $query)` para cada cálculo

### Fase 2 — Correcciones Medias

- [x] **2.1** Fix Bug #3: Agregar `->where('estado', 1)` en 6 queries de clientes ✅
  - `VentaController` L78, L90, L724
  - `CotizacionController` L82, L97, L272

- [x] **2.2** Fix Bug #4: Validar ID numérico en rutas de artículos ✅
  - Agregado `->where('id', '[0-9]+')` en 4 rutas

- [x] **2.3** Fix Bug #6: Eliminar `fecha_pago` en 4 métodos de pago ✅
  - `pagarIndividual`, `pagarMultiples`, `pagarTrabajador`, `pagarVendedor`

### Fase 3 — Comunicación con el Cliente

- [ ] **3.1** Informar que debe configurar Metas de Venta para activar comisiones de vendedores
- [ ] **3.2** Explicar flujo correcto de pago de comisiones (Gestión → Lotes de Pago)

---

## Resultados de Verificación (Post-Fix)

**Fecha:** 11 de abril de 2026

| Verificación | Resultado |
|---|---|
| Artículos filtrados correctamente | ✅ 1,254 activos / 25 eliminados excluidos |
| Clientes filtrados correctamente | ✅ 742 activos / 17 eliminados excluidos |
| Estadísticas comisiones con `clone` | ✅ Pendientes: 3,207 (Q57,515) — Total: 3,231 (Q58,255) |
| Bug sin `clone` reproduce error | ✅ Confirmado: pendientes=0, cantidad=0 (bug original) |
| Columna `fecha_pago` no existe en comisions | ✅ Confirmado — referencias eliminadas |
| Tabla `metas_ventas` vacía | ✅ 0 registros — requiere configuración del cliente |
| Rutas con constraint numérico | ✅ 4 rutas protegidas con `[0-9]+` |

---

## Criterios de Verificación

Después de aplicar los fixes:

1. **Artículos eliminados:** Verificar que los 25 artículos con `estado=0` NO aparezcan en ningún dropdown de ventas, cotizaciones ni ingresos
2. **Clientes eliminados:** Verificar que los 17 clientes con `estado=0` NO aparezcan en formularios
3. **Estadísticas comisiones:** Verificar que `calcularEstadisticas` retorne valores correctos (3,207 pendientes, Q57,515 total)
4. **Favicon error:** Verificar que `/show-articulo/favicon.ico` no genere error en logs
