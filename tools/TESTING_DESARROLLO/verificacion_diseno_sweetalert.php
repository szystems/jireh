<?php
/**
 * Script de verificación del diseño mejorado de SweetAlert
 * Verifica que los estilos CSS personalizados se hayan aplicado correctamente
 */

require_once __DIR__ . '/vendor/autoload.php';

echo "=== VERIFICACIÓN DE DISEÑO SWEETALERT - JIREH AUTOMOTRIZ ===\n";
echo "Fecha: " . date('Y-m-d H:i:s') . "\n\n";

// Verificar archivo de vista
$archivoVista = __DIR__ . '/resources/views/admin/venta/edit.blade.php';

if (!file_exists($archivoVista)) {
    echo "❌ ERROR: No se encontró el archivo de vista: $archivoVista\n";
    exit(1);
}

echo "📄 Verificando archivo de vista...\n";
$contenidoVista = file_get_contents($archivoVista);

// Verificar presencia de estilos CSS personalizados
$verificaciones = [
    'seccion_estilos' => [
        'nombre' => 'Sección de estilos CSS',
        'patron' => '/<!-- Estilos personalizados para SweetAlert -->/',
        'descripcion' => 'Comentario de identificación de estilos'
    ],
    'espaciado_botones' => [
        'nombre' => 'Espaciado entre botones',
        'patron' => '/\.swal2-actions\s*\{[^}]*gap:\s*20px\s*!important/',
        'descripcion' => 'Gap de 20px entre botones'
    ],
    'contraste_cancelar' => [
        'nombre' => 'Contraste botón cancelar',
        'patron' => '/\.swal2-styled\.btn-secondary[^}]*background-color:\s*#6c757d\s*!important/',
        'descripcion' => 'Color de fondo mejorado para botón cancelar'
    ],
    'hover_cancelar' => [
        'nombre' => 'Efecto hover cancelar',
        'patron' => '/\.swal2-styled\.btn-secondary:hover[^}]*background-color:\s*#545b62\s*!important/',
        'descripcion' => 'Efecto hover para botón cancelar'
    ],
    'min_width_botones' => [
        'nombre' => 'Ancho mínimo botones',
        'patron' => '/\.swal2-actions\s+button[^}]*min-width:\s*120px\s*!important/',
        'descripcion' => 'Ancho mínimo de 120px para botones'
    ],
    'transiciones' => [
        'nombre' => 'Transiciones suaves',
        'patron' => '/\.swal2-styled[^}]*transition:\s*all\s+0\.2s\s+ease\s*!important/',
        'descripcion' => 'Transiciones suaves en botones'
    ],
    'sombras_botones' => [
        'nombre' => 'Sombras en botones',
        'patron' => '/box-shadow:\s*0\s+2px\s+4px/',
        'descripcion' => 'Efectos de sombra en botones'
    ]
];

$resultados = [];
$todoOk = true;

echo "\n🎨 VERIFICANDO ESTILOS CSS PERSONALIZADOS:\n";
echo str_repeat("-", 60) . "\n";

foreach ($verificaciones as $clave => $verificacion) {
    $encontrado = preg_match($verificacion['patron'], $contenidoVista);
    $resultados[$clave] = $encontrado;
    
    $icono = $encontrado ? "✅" : "❌";
    echo sprintf("%-30s %s %s\n", 
        $verificacion['nombre'] . ":", 
        $icono, 
        $verificacion['descripcion']
    );
    
    if (!$encontrado) {
        $todoOk = false;
    }
}

echo "\n📋 VERIFICANDO CONFIGURACIÓN SWEETALERT:\n";
echo str_repeat("-", 60) . "\n";

// Verificar configuración de SweetAlert
$configuracionesSweetAlert = [
    'showCancelButton' => 'Botón cancelar habilitado',
    'confirmButtonColor' => 'Color botón confirmar configurado',
    'cancelButtonColor' => 'Color botón cancelar configurado',
    'confirmButtonText' => 'Texto personalizado botón confirmar',
    'cancelButtonText' => 'Texto personalizado botón cancelar',
    'customClass' => 'Clases CSS personalizadas',
    'buttonsStyling: false' => 'Estilos propios habilitados'
];

foreach ($configuracionesSweetAlert as $config => $descripcion) {
    $encontrado = strpos($contenidoVista, $config) !== false;
    $icono = $encontrado ? "✅" : "❌";
    echo sprintf("%-30s %s %s\n", $config . ":", $icono, $descripcion);
    
    if (!$encontrado) {
        $todoOk = false;
    }
}

echo "\n🔍 VERIFICANDO ELEMENTOS ESPECÍFICOS:\n";
echo str_repeat("-", 60) . "\n";

// Verificar elementos específicos del diseño
$elementosEspecificos = [
    'bi bi-trash' => 'Icono trash en botón confirmar',
    'bi bi-x-circle' => 'Icono x-circle en botón cancelar',
    'btn btn-danger' => 'Clase Bootstrap para botón confirmar',
    'btn btn-secondary' => 'Clase Bootstrap para botón cancelar',
    'gap: 20px' => 'Espaciado de 20px entre botones',
    'min-width: 120px' => 'Ancho mínimo de botones',
    'border-radius: 6px' => 'Bordes redondeados',
    'transform: translateY(-1px)' => 'Efecto de elevación en hover'
];

foreach ($elementosEspecificos as $elemento => $descripcion) {
    $encontrado = strpos($contenidoVista, $elemento) !== false;
    $icono = $encontrado ? "✅" : "❌";
    echo sprintf("%-30s %s %s\n", $elemento . ":", $icono, $descripcion);
}

echo "\n" . str_repeat("=", 60) . "\n";

if ($todoOk) {
    echo "🎉 VERIFICACIÓN EXITOSA: Todos los estilos CSS personalizados están presentes\n";
    echo "✅ El diseño de SweetAlert ha sido mejorado correctamente\n";
    echo "✅ Espaciado entre botones: 20px\n";
    echo "✅ Contraste mejorado en botón cancelar\n";
    echo "✅ Efectos hover y transiciones implementados\n";
    echo "✅ Sombras y efectos visuales agregados\n";
} else {
    echo "⚠️  ADVERTENCIA: Algunos estilos pueden estar faltando\n";
    echo "🔧 Revise la implementación de los estilos CSS\n";
}

echo "\n📝 INSTRUCCIONES PARA PROBAR:\n";
echo "1. Abra la página de edición de venta\n";
echo "2. Haga clic en 'Eliminar' en cualquier detalle\n";
echo "3. Verifique:\n";
echo "   - Los botones tienen mayor espaciado (20px)\n";
echo "   - El botón 'Cancelar' tiene mejor contraste\n";
echo "   - Los botones tienen efectos hover suaves\n";
echo "   - Ancho mínimo de 120px en botones\n";
echo "   - Sombras y efectos visuales\n";

echo "\n🎨 MEJORAS IMPLEMENTADAS:\n";
echo "• Espaciado aumentado entre botones (gap: 20px)\n";
echo "• Mejor contraste en botón cancelar (#6c757d)\n";
echo "• Efectos hover con elevación (translateY(-1px))\n";
echo "• Sombras suaves en botones\n";
echo "• Transiciones de 0.2s para suavidad\n";
echo "• Ancho mínimo de 120px para consistencia\n";
echo "• Bordes redondeados de 6px\n";

echo "\nFin de la verificación: " . date('Y-m-d H:i:s') . "\n";
