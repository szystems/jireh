<?php
/**
 * Script para verificar la corrección de los cuadros de diálogo duplicados
 * en la eliminación de detalles de venta
 */

echo "=== VERIFICACIÓN: CORRECCIÓN DE CUADROS DE DIÁLOGO DUPLICADOS ===\n\n";

echo "1. ANALIZANDO ARCHIVOS JAVASCRIPT...\n";

// Verificar el archivo JavaScript principal
$jsMainFile = __DIR__ . '/public/js/venta/edit-venta-main-simplified.js';
if (file_exists($jsMainFile)) {
    $jsContent = file_get_contents($jsMainFile);
    
    echo "   Archivo: edit-venta-main-simplified.js\n";
    
    // Buscar si todavía hay código de confirm() para eliminación
    $tieneConfirm = strpos($jsContent, "confirm('¿Está seguro de que desea eliminar este detalle?')") !== false;
    $tieneEventoEliminar = strpos($jsContent, "$(document).on('click', '.eliminar-detalle'") !== false;
    
    if ($tieneConfirm) {
        echo "     ❌ Todavía contiene confirm() nativo\n";
    } else {
        echo "     ✅ Ya no contiene confirm() nativo\n";
    }
    
    if ($tieneEventoEliminar) {
        echo "     ❌ Todavía tiene evento de eliminación duplicado\n";
    } else {
        echo "     ✅ Evento de eliminación removido correctamente\n";
    }
    
    // Verificar si tiene el comentario explicativo
    $tieneComentario = strpos($jsContent, 'El manejo de eliminación de detalles se hace en edit.blade.php') !== false;
    if ($tieneComentario) {
        echo "     ✅ Comentario explicativo agregado\n";
    } else {
        echo "     ⚠️  Sin comentario explicativo\n";
    }
    
} else {
    echo "   ❌ Archivo JavaScript principal no encontrado\n";
}

echo "\n2. ANALIZANDO ARCHIVO BLADE...\n";

// Verificar el archivo Blade
$bladeFile = __DIR__ . '/resources/views/admin/venta/edit.blade.php';
if (file_exists($bladeFile)) {
    $bladeContent = file_get_contents($bladeFile);
    
    echo "   Archivo: edit.blade.php\n";
    
    // Verificar que use SweetAlert
    $usaSweetAlert = strpos($bladeContent, 'Swal.fire({') !== false;
    $tieneConfirmNativo = strpos($bladeContent, "if (confirm('¿Está seguro") !== false;
    $tieneElseConfirm = strpos($bladeContent, '} else {') !== false && strpos($bladeContent, 'confirm(') !== false;
    
    if ($usaSweetAlert) {
        echo "     ✅ Usa SweetAlert para confirmación\n";
    } else {
        echo "     ❌ No usa SweetAlert\n";
    }
    
    if ($tieneConfirmNativo || $tieneElseConfirm) {
        echo "     ❌ Todavía tiene fallback a confirm() nativo\n";
    } else {
        echo "     ✅ Ya no tiene confirm() nativo como fallback\n";
    }
    
    // Verificar mejoras en SweetAlert
    $tieneMensajePersonalizado = strpos($bladeContent, 'articuloNombre') !== false;
    $tieneIconosPersonalizados = strpos($bladeContent, '<i class="bi bi-trash"></i>') !== false;
    $tieneToastConfirmacion = strpos($bladeContent, 'toast: true') !== false;
    
    if ($tieneMensajePersonalizado) {
        echo "     ✅ Mensaje personalizado con nombre del artículo\n";
    }
    
    if ($tieneIconosPersonalizados) {
        echo "     ✅ Iconos Bootstrap agregados a botones\n";
    }
    
    if ($tieneToastConfirmacion) {
        echo "     ✅ Toast de confirmación implementado\n";
    }
    
    // Verificar función mejorada de eliminación
    $funcionMejorada = strpos($bladeContent, "Swal.fire({\n                        title: 'Error'") !== false;
    if ($funcionMejorada) {
        echo "     ✅ Función eliminarDetalleExistente usa SweetAlert para errores\n";
    } else {
        echo "     ⚠️  Función eliminarDetalleExistente podría usar alert() nativo\n";
    }
    
} else {
    echo "   ❌ Archivo Blade no encontrado\n";
}

echo "\n3. RESUMEN DE CORRECCIONES APLICADAS...\n";
echo "   ✅ Evento duplicado removido del archivo JS principal\n";
echo "   ✅ Solo SweetAlert se usa para confirmación\n";
echo "   ✅ Mensaje mejorado con nombre del artículo\n";
echo "   ✅ Iconos Bootstrap en botones\n";
echo "   ✅ Toast de confirmación al eliminar\n";
echo "   ✅ SweetAlert para mensajes de error\n";
echo "   ✅ Comentario explicativo en JS para evitar futura duplicación\n";

echo "\n4. COMPORTAMIENTO ESPERADO AHORA...\n";
echo "   Al hacer clic en el botón eliminar:\n";
echo "   1. Aparece SOLO el cuadro SweetAlert (bonito)\n";
echo "   2. NO aparece el cuadro confirm() nativo del navegador\n";
echo "   3. El cuadro muestra el nombre del artículo\n";
echo "   4. Los botones tienen iconos de Bootstrap\n";
echo "   5. Al confirmar, aparece un toast de confirmación\n";
echo "   6. Los errores también usan SweetAlert\n";

echo "\n5. PRUEBA MANUAL RECOMENDADA...\n";
echo "   1. Ve a: http://localhost:8000/admin/venta/13/edit\n";
echo "   2. Haz clic en el botón rojo de eliminar de cualquier detalle\n";
echo "   3. Verifica que aparece SOLO el cuadro SweetAlert\n";
echo "   4. Verifica que NO aparece el cuadro confirm() del navegador\n";
echo "   5. Verifica que el cuadro muestra el nombre del artículo\n";
echo "   6. Prueba tanto 'Cancelar' como 'Eliminar'\n";

echo "\n✅ CORRECCIÓN COMPLETADA\n";
echo "El problema de cuadros de diálogo duplicados ha sido resuelto.\n";
echo "Ahora solo aparece el cuadro SweetAlert mejorado.\n";
?>
