#!/bin/bash

echo "=== LIMPIEZA DE ARCHIVOS VACÍOS EN LA RAÍZ ==="
echo "Eliminando archivos duplicados y vacíos..."

# Archivos .md vacíos
rm -f CORRECCION_COMPLETA_FORMULARIO_FINAL.md
rm -f CORRECCION_DIALOGO_UNICO_FINALIZADA.md
rm -f CORRECCION_DUPLICACION_DETALLES.md
rm -f CORRECCION_EDIT_VENTA_FINALIZADA.md
rm -f CORRECCION_EDIT_VENTA_NUEVA_SESION.md
rm -f CORRECCION_ERROR_DASHBOARD_PRO.md
rm -f CORRECCION_ERROR_SINTAXIS_VISTA_REPORTE.md
rm -f CORRECCION_ERROR_TYPEERROR_LOGS.md
rm -f CORRECCION_ERROR_VISTA_REPORTE.md
rm -f CORRECCION_EVENTOS_JAVASCRIPT_FINAL.md
rm -f CORRECCION_FORMULARIO_AGREGAR_DETALLE.md
rm -f CORRECCION_JAVASCRIPT_EDIT_FINALIZADA.md
rm -f CORRECCION_MODAL_TRABAJADORES_FINAL.md
rm -f CORRECCION_MODAL_TRABAJADORES_FINALIZADA.md
rm -f CORRECCION_MODAL_TRABAJADORES_SELECT2.md
rm -f CORRECCION_MODULO_TRABAJADORES.md
rm -f CORRECCION_PDF_VENTA.md
rm -f CORRECCION_RUTA_VEHICULOS_FINALIZADA.md
rm -f CORRECCION_SIDEBAR_LAYOUT.md
rm -f CORRECCION_TRABAJADORES_DEBUGGING_FINAL.md
rm -f CORRECCION_TRABAJADORES_SISTEMA_UNIFICADO_FINAL.md
rm -f CORRECCION_VALIDACION_STOCK_DETALLADA.md
rm -f CORRECCION_VISTA_SHOW.md
rm -f DIAGNOSTICO_PROBLEMA_RECARGA_COMPLETO.md
rm -f ESTADO_FINAL_SISTEMA.md
rm -f GUIA_DEBUGGING_COMPLETA.md
rm -f GUIA_INTEGRACION_SISTEMA_PREVENCION.md
rm -f IMPLEMENTACION_CORRECCION_MANUAL_AUDITORIA.md
rm -f INSTRUCCIONES_DEBUGGING_FORMULARIO.md
rm -f MEJORA_VISUAL_REPORTES_AUDITORIA.md
rm -f MEJORA_VISUAL_SWEETALERT_FINALIZADA.md
rm -f ORGANIZACION_PROYECTO.md
rm -f PRUEBA_FILTRADO_METAS.md
rm -f RESUMEN_CONSOLIDACION_MIGRACIONES_TRABAJADORES.md
rm -f RESUMEN_FINAL_CORRECCION_STOCK.md
rm -f SISTEMA_AUDITORIA_COMPLETO.md
rm -f SISTEMA_CARWASH_FUNCIONAL.md
rm -f SISTEMA_DASHBOARD_EJECUTIVO_COMPLETO.md
rm -f SISTEMA_PREVENCION_INCONSISTENCIAS_COMPLETO.md
rm -f SOLUCION_COMPLETA_FORMULARIO_VENTA.md

echo "✓ Archivos .md eliminados"

# Archivos .php vacíos
rm -f CORRECCION_PRESERVACION_VEHICULO_FINALIZADA.php
rm -f CORRECCION_VALIDACION_SOLAPAMIENTO_FINALIZADA.php
rm -f corregir_permisos_cache.php
rm -f crear_metas_ejemplo.php
rm -f crear_venta_prueba_comisiones.php
rm -f debug_comisiones.php
rm -f debug_problema_agregar_detalle.php
rm -f debug_problema_recarga_persistente.php
rm -f debug_recarga_formulario_edit_venta.php
rm -f debug_regeneracion.php
rm -f debug_sistema_completo.php
rm -f debug_trabajadores.php
rm -f debug_trabajadores_carwash.php
rm -f encontrar_venta_para_pruebas.php
rm -f resolver_problema_stock.php
rm -f test_correcciones_delicadas.php
rm -f test_correcciones_edit_venta.php
rm -f test_correccion_bd.php
rm -f test_correccion_ruta_vehiculos.php
rm -f test_correccion_setselectionrange.php
rm -f test_dashboard_comisiones.php
rm -f test_duplicacion_corregida.php
rm -f test_edicion_manual.php
rm -f test_edit_venta.php
rm -f test_edit_venta_completo.php
rm -f test_eventos_edit_venta.php
rm -f test_formulario_edit_final.php
rm -f test_logs_corregidos.php
rm -f test_metas_sistema.php
rm -f test_pdf_venta.php
rm -f test_preservacion_vehiculo.php
rm -f test_problema_recarga_especifico.php
rm -f test_sistema_auditoria.php
rm -f test_solucion_final_edit_venta.php
rm -f test_solucion_simplificada.php
rm -f test_trabajadores_directo.php
rm -f test_trabajador_sin_direccion.php
rm -f test_validacion_navegador.php
rm -f test_validacion_solapamiento.php
rm -f test_validacion_stock_mejorada.php
rm -f test_vista_show.php
rm -f validar_calculos_costos.php
rm -f verificacion_correccion_stock.php
rm -f verificacion_dialogo_unico.php
rm -f verificacion_diseno_sweetalert.php
rm -f verificacion_final.php
rm -f verificacion_final_edit.php
rm -f verificacion_final_sistema.php
rm -f verificacion_funcionalidades_edit.php

echo "✓ Archivos .php eliminados"

# Archivos .html vacíos
rm -f debug_formulario_agregar_detalle.html
rm -f debug_modal_trabajadores.html
rm -f debug_solucion_trabajadores.html
rm -f debug_trabajadores_completo_final.html
rm -f debug_trabajadores_inputs_completo.html
rm -f debug_trabajadores_persistencia.html
rm -f debugging-simplificado.html
rm -f test_agregar_detalle.html
rm -f test-inmediato-formulario.html

echo "✓ Archivos .html eliminados"

# Archivos .txt vacíos
rm -f resumen_correccion.txt
rm -f resumen_correccion_final.txt

echo "✓ Archivos .txt eliminados"

# Eliminar carpetas vacías si existen
if [ -d "CORRECCIONES_HISTORIAL" ] && [ -z "$(ls -A CORRECCIONES_HISTORIAL)" ]; then
    rmdir CORRECCIONES_HISTORIAL
    echo "✓ Carpeta CORRECCIONES_HISTORIAL vacía eliminada"
fi

if [ -d "RESUMEN_TRABAJO" ] && [ -z "$(ls -A RESUMEN_TRABAJO)" ]; then
    rmdir RESUMEN_TRABAJO
    echo "✓ Carpeta RESUMEN_TRABAJO vacía eliminada"
fi

if [ -d "TESTING_DESARROLLO" ] && [ -z "$(ls -A TESTING_DESARROLLO)" ]; then
    rmdir TESTING_DESARROLLO
    echo "✓ Carpeta TESTING_DESARROLLO vacía eliminada"
fi

if [ -d "DOCUMENTACION_PROYECTO" ] && [ -z "$(ls -A DOCUMENTACION_PROYECTO)" ]; then
    rmdir DOCUMENTACION_PROYECTO
    echo "✓ Carpeta DOCUMENTACION_PROYECTO vacía eliminada"
fi

echo ""
echo "🎉 LIMPIEZA COMPLETADA"
echo "Todos los archivos duplicados y vacíos han sido eliminados."
echo "Los archivos originales están organizados en la carpeta tools/"
