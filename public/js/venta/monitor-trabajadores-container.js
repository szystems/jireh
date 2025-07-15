// Script para monitorear y asegurar la visibilidad correcta del contenedor
// de trabajadores de Car Wash en el formulario de ventas
$(document).ready(function() {
    console.log("🔍 Iniciando monitor-trabajadores-container.js");

    // Verificar si la API MutationObserver está disponible
    if (!window.MutationObserver) {
        console.warn("MutationObserver no disponible. La monitorización avanzada no funcionará.");
        return;
    }

    // Referencias a elementos principales
    const $articuloSelect = $('#articulo');
    const $trabajadoresContainer = $('#trabajadores-carwash-container');

    // Si algún elemento no existe, salir
    if (!$articuloSelect.length || !$trabajadoresContainer.length) {
        console.warn("Elementos necesarios no encontrados.");
        return;
    }

    // Función para determinar la visibilidad correcta del contenedor
    function determinarVisibilidadCorrecta() {
        const articuloSeleccionado = $articuloSelect.val();
        if (!articuloSeleccionado) {
            return false; // Sin artículo seleccionado, debería estar oculto
        }

        const tipoArticulo = $articuloSelect.find('option:selected').data('tipo');
        return (tipoArticulo === 'servicio'); // Visible solo para servicios
    }

    // Función para forzar la visibilidad correcta
    function forzarVisibilidadCorrecta() {
        const deberiaEstarVisible = determinarVisibilidadCorrecta();
        const estaVisible = $trabajadoresContainer.is(':visible');

        if (deberiaEstarVisible && !estaVisible) {
            console.log("🔄 Forzando visibilidad del contenedor de trabajadores (estaba oculto)");
            $trabajadoresContainer.css('display', 'block').show();
            $trabajadoresContainer.attr('style', 'display: block !important');

            // Recargar Select2 dentro
            const $trabajadoresSelect = $('#trabajadores-carwash-nuevo');
            if ($trabajadoresSelect.length && $trabajadoresSelect.data('select2')) {
                setTimeout(() => $trabajadoresSelect.select2('destroy').select2(), 10);
            }
        } else if (!deberiaEstarVisible && estaVisible) {
            console.log("🔄 Forzando ocultamiento del contenedor de trabajadores (estaba visible)");
            $trabajadoresContainer.css('display', 'none').hide();
            $trabajadoresContainer.attr('style', 'display: none !important');
        }
    }

    // Configurar el observador para detectar cambios de visibilidad
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' &&
                (mutation.attributeName === 'style' || mutation.attributeName === 'class')) {

                const estaVisible = $trabajadoresContainer.is(':visible');
                const deberiaEstarVisible = determinarVisibilidadCorrecta();

                console.log(`📊 Cambio detectado en contenedor. Visible: ${estaVisible}, Debería ser: ${deberiaEstarVisible}`);

                // Si hay discrepancia, corregir
                if (estaVisible !== deberiaEstarVisible) {
                    forzarVisibilidadCorrecta();
                }
            }
        });
    });

    // Iniciar observación
    observer.observe($trabajadoresContainer[0], {
        attributes: true,
        attributeFilter: ['style', 'class']
    });

    // También monitorear cambios en el select de artículos
    $articuloSelect.on('change select2:select select2:unselect', function() {
        // Esperar un poco antes de actualizar la visibilidad
        setTimeout(forzarVisibilidadCorrecta, 50);

        // Hacer una segunda verificación un poco más tarde
        setTimeout(forzarVisibilidadCorrecta, 150);
    });

    // También detectar cuando se limpie el select
    $articuloSelect.on('select2:clear', function() {
        // Si se limpia la selección, asegurarnos que el contenedor se oculta
        console.log("🔄 Limpieza de select detectada, ocultando contenedor de trabajadores");
        $trabajadoresContainer.hide();
    });

    // Verificar estado inicial y hacer verificaciones periódicas
    setTimeout(forzarVisibilidadCorrecta, 200);
    setTimeout(forzarVisibilidadCorrecta, 500);
    setTimeout(forzarVisibilidadCorrecta, 1000);

    // Una verificación adicional cuando termine todo el proceso de inicialización
    setTimeout(function() {
        // Revisar el valor actual del select
        const currentValue = $articuloSelect.val();
        // Si no hay valor seleccionado, asegurarnos que el contenedor está oculto
        if (!currentValue && $trabajadoresContainer.is(':visible')) {
            console.log("🔄 Verificación final: Ocultando contenedor de trabajadores (sin selección)");
            $trabajadoresContainer.hide();
        }
    }, 2000);

    console.log("✅ Monitor de contenedor de trabajadores iniciado");
});
