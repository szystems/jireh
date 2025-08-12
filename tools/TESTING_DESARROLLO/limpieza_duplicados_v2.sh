#!/bin/bash

echo "=== SEGUNDA LIMPIEZA DE ARCHIVOS DUPLICADOS EN LA RAÍZ ==="
echo "Eliminando archivos duplicados y vacíos (fecha: $(date))..."

# Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    echo "❌ Error: No se encuentra el archivo artisan. Asegúrate de estar en la raíz del proyecto Laravel."
    exit 1
fi

echo "✓ Confirmado: estamos en la raíz del proyecto Laravel"
echo ""

# Función para eliminar archivos de forma segura
eliminar_archivos() {
    local categoria="$1"
    shift
    local archivos=("$@")
    local eliminados=0
    
    echo "📁 Eliminando archivos de categoría: $categoria"
    
    for archivo in "${archivos[@]}"; do
        if [ -f "$archivo" ] && [ ! -s "$archivo" ]; then
            rm -f "$archivo"
            echo "  ✓ $archivo (vacío)"
            ((eliminados++))
        elif [ -f "$archivo" ]; then
            echo "  ⚠️  $archivo (no está vacío - NO eliminado)"
        fi
    done
    
    echo "  → $eliminados archivos eliminados en $categoria"
    echo ""
}

# 1. Archivos de correcciones (CORRECCION_*.md y .php)
archivos_correcciones=(
    "CORRECCION_COMPLETA_FORMULARIO_FINAL.md"
    "CORRECCION_DIALOGO_UNICO_FINALIZADA.md"
    "CORRECCION_DUPLICACION_DETALLES.md"
    "CORRECCION_EDIT_VENTA_FINALIZADA.md"
    "CORRECCION_EDIT_VENTA_NUEVA_SESION.md"
    "CORRECCION_ERROR_DASHBOARD_PRO.md"
    "CORRECCION_ERROR_SINTAXIS_VISTA_REPORTE.md"
    "CORRECCION_ERROR_TYPEERROR_LOGS.md"
    "CORRECCION_ERROR_VISTA_REPORTE.md"
    "CORRECCION_EVENTOS_JAVASCRIPT_FINAL.md"
    "CORRECCION_FORMULARIO_AGREGAR_DETALLE.md"
    "CORRECCION_JAVASCRIPT_EDIT_FINALIZADA.md"
    "CORRECCION_MODAL_TRABAJADORES_FINAL.md"
    "CORRECCION_MODAL_TRABAJADORES_FINALIZADA.md"
    "CORRECCION_MODAL_TRABAJADORES_SELECT2.md"
    "CORRECCION_MODULO_TRABAJADORES.md"
    "CORRECCION_PDF_VENTA.md"
    "CORRECCION_PRESERVACION_VEHICULO_FINALIZADA.php"
    "CORRECCION_RUTA_VEHICULOS_FINALIZADA.md"
    "CORRECCION_SIDEBAR_LAYOUT.md"
    "CORRECCION_TRABAJADORES_DEBUGGING_FINAL.md"
    "CORRECCION_TRABAJADORES_SISTEMA_UNIFICADO_FINAL.md"
    "CORRECCION_VALIDACION_SOLAPAMIENTO_FINALIZADA.php"
    "CORRECCION_VALIDACION_STOCK_DETALLADA.md"
    "CORRECCION_VISTA_SHOW.md"
)

eliminar_archivos "CORRECCIONES" "${archivos_correcciones[@]}"

# 2. Archivos de debug
archivos_debug=(
    "debug_comisiones.php"
    "debug_formulario_agregar_detalle.html"
    "debug_modal_trabajadores.html"
    "debug_problema_agregar_detalle.php"
    "debug_problema_recarga_persistente.php"
    "debug_recarga_formulario_edit_venta.php"
    "debug_regeneracion.php"
    "debug_sistema_completo.php"
    "debug_solucion_trabajadores.html"
    "debug_trabajadores.php"
    "debug_trabajadores_carwash.php"
    "debug_trabajadores_completo_final.html"
    "debug_trabajadores_inputs_completo.html"
    "debug_trabajadores_persistencia.html"
    "debugging-simplificado.html"
)

eliminar_archivos "DEBUG" "${archivos_debug[@]}"

# 3. Archivos de testing
archivos_testing=(
    "test_agregar_detalle.html"
    "test_correcciones_delicadas.php"
    "test_correcciones_edit_venta.php"
    "test_correccion_bd.php"
    "test_correccion_ruta_vehiculos.php"
    "test_correccion_setselectionrange.php"
    "test_dashboard_comisiones.php"
    "test_duplicacion_corregida.php"
    "test_edicion_manual.php"
    "test_edit_venta.php"
    "test_edit_venta_completo.php"
    "test_eventos_edit_venta.php"
    "test_formulario_edit_final.php"
    "test_logs_corregidos.php"
    "test_metas_sistema.php"
    "test_pdf_venta.php"
    "test_preservacion_vehiculo.php"
    "test_problema_recarga_especifico.php"
    "test_sistema_auditoria.php"
    "test_solucion_final_edit_venta.php"
    "test_solucion_simplificada.php"
    "test_trabajadores_directo.php"
    "test_trabajador_sin_direccion.php"
    "test_validacion_navegador.php"
    "test_validacion_solapamiento.php"
    "test_validacion_stock_mejorada.php"
    "test_vista_show.php"
    "test-inmediato-formulario.html"
)

eliminar_archivos "TESTING" "${archivos_testing[@]}"

# 4. Archivos de documentación y sistemas
archivos_documentacion=(
    "DIAGNOSTICO_PROBLEMA_RECARGA_COMPLETO.md"
    "ESTADO_FINAL_SISTEMA.md"
    "GUIA_DEBUGGING_COMPLETA.md"
    "GUIA_INTEGRACION_SISTEMA_PREVENCION.md"
    "IMPLEMENTACION_CORRECCION_MANUAL_AUDITORIA.md"
    "INSTRUCCIONES_DEBUGGING_FORMULARIO.md"
    "MEJORA_VISUAL_REPORTES_AUDITORIA.md"
    "MEJORA_VISUAL_SWEETALERT_FINALIZADA.md"
    "ORGANIZACION_PROYECTO.md"
    "PRUEBA_FILTRADO_METAS.md"
    "RESUMEN_CONSOLIDACION_MIGRACIONES_TRABAJADORES.md"
    "RESUMEN_FINAL_CORRECCION_STOCK.md"
    "SISTEMA_AUDITORIA_COMPLETO.md"
    "SISTEMA_CARWASH_FUNCIONAL.md"
    "SISTEMA_DASHBOARD_EJECUTIVO_COMPLETO.md"
    "SISTEMA_PREVENCION_INCONSISTENCIAS_COMPLETO.md"
    "SOLUCION_COMPLETA_FORMULARIO_VENTA.md"
)

eliminar_archivos "DOCUMENTACIÓN" "${archivos_documentacion[@]}"

# 5. Archivos de utilidades y validación
archivos_utilidades=(
    "corregir_permisos_cache.php"
    "crear_metas_ejemplo.php"
    "crear_venta_prueba_comisiones.php"
    "encontrar_venta_para_pruebas.php"
    "resolver_problema_stock.php"
    "validar_calculos_costos.php"
    "verificacion_correccion_stock.php"
    "verificacion_dialogo_unico.php"
    "verificacion_diseno_sweetalert.php"
    "verificacion_final.php"
    "verificacion_final_edit.php"
    "verificacion_final_sistema.php"
    "verificacion_funcionalidades_edit.php"
)

eliminar_archivos "UTILIDADES" "${archivos_utilidades[@]}"

# 6. Archivos de resumen
archivos_resumen=(
    "resumen_correccion.txt"
    "resumen_correccion_final.txt"
)

eliminar_archivos "RESUMEN" "${archivos_resumen[@]}"

# 7. Eliminar carpetas duplicadas vacías si existen
echo "📁 Verificando carpetas duplicadas..."
carpetas_a_verificar=("CORRECCIONES_HISTORIAL" "DOCUMENTACION_PROYECTO" "RESUMEN_TRABAJO" "TESTING_DESARROLLO")

for carpeta in "${carpetas_a_verificar[@]}"; do
    if [ -d "$carpeta" ]; then
        if [ -z "$(ls -A "$carpeta" 2>/dev/null)" ]; then
            rmdir "$carpeta"
            echo "  ✓ Carpeta vacía eliminada: $carpeta"
        else
            echo "  ⚠️  Carpeta no vacía, no eliminada: $carpeta"
        fi
    fi
done

echo ""
echo "🎉 SEGUNDA LIMPIEZA COMPLETADA"
echo "✅ Todos los archivos duplicados y vacíos han sido eliminados"
echo "✅ Los archivos originales permanecen seguros en tools/"
echo ""
echo "📊 Estado final de la raíz del proyecto:"
ls -la | grep -E '^d|artisan|composer|package|README' | head -10
