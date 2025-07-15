# Guía de Migraciones Consolidadas para Trabajadores

## Resumen de Cambios
Este documento describe las migraciones consolidadas para las tablas `trabajadors` y `tipo_trabajadors` para mejorar la estructura del proyecto, eliminando migraciones redundantes y organizando los cambios en un conjunto coherente de archivos.

## Archivos de Migración Importantes
- **2025_03_04_164717_create_trabajadors_table.php**: Crea tanto la tabla `tipo_trabajadors` como `trabajadors` con la estructura completa y actualizada.
- **2025_05_15_121720_insert_default_tipo_trabajadors.php**: Inserta los datos predeterminados para los tipos de trabajadores.

## Archivos Obsoletos (No ejecutar)
Los siguientes archivos han sido marcados como obsoletos y no deben ejecutarse:
- obsoleto_2025_05_15_000001_unificar_relaciones_tipo_trabajador.php
- obsoleto_2025_05_15_114912_change_tipo_field_to_integer_in_trabajadors.php
- obsoleto_2025_05_15_114912_change_tipo_field_to_integer_in_trabajadors_v2.php
- obsoleto_2026_05_01_000000_recreate_tipo_trabajadors_table.php
- obsoleto_2026_05_15_000001_add_commission_config_to_tipo_trabajadors_table.php

## Estructura de las Tablas

### tipo_trabajadors
```
- id (bigint, primary key)
- nombre (string, unique)
- descripcion (text, nullable)
- aplica_comision (boolean, default: false)
- requiere_asignacion (boolean, default: false)
- tipo_comision (string, nullable)
- valor_comision (decimal, nullable)
- porcentaje_comision (decimal, nullable)
- permite_multiples_trabajadores (boolean, default: false)
- configuracion_adicional (json, nullable)
- estado (enum: 'activo'/'inactivo', default: 'activo')
- timestamps (created_at, updated_at)
```

### trabajadors
```
- id (bigint, primary key)
- nombre (string)
- apellido (string)
- telefono (string, 20)
- direccion (string)
- email (string, nullable)
- nit (string, nullable)
- dpi (string, nullable)
- tipo (unsignedBigInteger, nullable) - Este campo se mantiene por compatibilidad
- tipo_trabajador_id (bigint, foreign key to tipo_trabajadors.id, nullable)
- estado (boolean, default: 1)
- timestamps (created_at, updated_at)
```

## Instrucciones para Ejecutar

Para aplicar estas migraciones:

1. Asegúrese de que la base de datos esté configurada correctamente en el archivo `.env`
2. Ejecute el comando:
   ```bash
   php artisan migrate
   ```
3. Si hay problemas, puede forzar una reinstalación completa con:
   ```bash
   php artisan migrate:fresh
   ```
   **ADVERTENCIA**: Esto eliminará y recreará todas las tablas.

## Importante
- El campo `tipo` en la tabla `trabajadors` se mantiene para compatibilidad con el código existente
- El modelo `Trabajador` ha sido actualizado con un mutador para mantener sincronizados los campos `tipo` y `tipo_trabajador_id`
- Si existen datos en producción, considere crear una migración adicional para copiar los valores del campo `tipo` a `tipo_trabajador_id`
