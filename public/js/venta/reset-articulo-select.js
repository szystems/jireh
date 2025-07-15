// Script para asegurar que el select de artículos no tenga selección inicial
// y que el multiselect de trabajadores se muestre cuando sea necesario
$(document).ready(function() {
    console.log("🔄 RESET-ARTICULO-SELECT: Script iniciado");
    
    // Solo ejecutar inmediatamente, sin múltiples timeouts que puedan interferir
    resetearSelect();

    // Función principal de reseteo - SOLO para el select de artículos nuevos
    function resetearSelect() {
        console.log("🔄 RESET-ARTICULO-SELECT: Inicio de ejecución");

        // 1. Obtener referencias a los elementos
        const $articuloSelect = $('#articulo');
        const $trabajadoresContainer = $('#trabajadores-carwash-container');

        if (!$articuloSelect.length) {
            console.error("❌ RESET-ARTICULO-SELECT: No se encontró el select de artículos");
            return;
        }

        // 2. Comprobar si hay alguna selección actual
        const valorSeleccionado = $articuloSelect.val();
        console.log("🔄 RESET-ARTICULO-SELECT: Valor actual del select:", valorSeleccionado);

        // IMPORTANTE: NO llamar a actualizarTotalVenta() desde aquí
        // porque no debe afectar el total de los detalles existentes

        if (valorSeleccionado) {
            // Hay un artículo seleccionado, vamos a verificar si es un servicio
            const $opcionSeleccionada = $articuloSelect.find('option:selected');
            const tipoArticulo = $opcionSeleccionada.data('tipo');
            const esServicio = (tipoArticulo === 'servicio');

            console.log("🔄 RESET-ARTICULO-SELECT: Artículo seleccionado:", {
                id: valorSeleccionado,
                tipo: tipoArticulo,
                esServicio: esServicio
            });

            // Manejar visibilidad de trabajadores sin resetear el select
            if (esServicio && $trabajadoresContainer.length) {
                if (!$trabajadoresContainer.is(':visible')) {
                    $trabajadoresContainer.show();
                    console.log("✅ RESET-ARTICULO-SELECT: Mostrando contenedor de trabajadores para servicio");
                }
            } else {
                if ($trabajadoresContainer.is(':visible')) {
                    $trabajadoresContainer.hide();
                    console.log("✅ RESET-ARTICULO-SELECT: Ocultando contenedor de trabajadores para no-servicio");
                }
            }

            // Si hay algo seleccionado intencionalmente, NO resetearlo
            return;
        }

        // 3. Solo resetear si realmente es necesario (select vacío)
        console.log("🔄 RESET-ARTICULO-SELECT: Ejecutando reseteo completo del select");

        // 3.1 Limpiar cualquier opción seleccionada en el HTML
        $articuloSelect.find('option:selected').prop('selected', false);
        $articuloSelect.find('option[selected]').removeAttr('selected');

        // 3.2 Asegurar que hay una primera opción vacía seleccionable
        const $primeraOpcion = $articuloSelect.find('option').first();
        if ($primeraOpcion.val() !== '') {
            $articuloSelect.prepend('<option value="" selected>Seleccione un artículo</option>');
            console.log("✅ RESET-ARTICULO-SELECT: Se añadió opción vacía al inicio");
        }

        // 3.3 Establecer valor vacío SIN triggear eventos que puedan llamar actualizarTotalVenta
        $articuloSelect.val('');
        console.log("✅ RESET-ARTICULO-SELECT: Reset de valor directo (sin events)");

        // 4. Asegurar que el contenedor de trabajadores esté oculto
        if ($trabajadoresContainer.length && $trabajadoresContainer.is(':visible')) {
            $trabajadoresContainer.hide();
            console.log("✅ RESET-ARTICULO-SELECT: Ocultando contenedor de trabajadores");
        }

        console.log("✅ RESET-ARTICULO-SELECT: Reseteo completo finalizado");
    }

    // Monitoreando los cambios de valor en el select, para depuración
    $('#articulo').on('change', function() {
        const valor = $(this).val();
        const tipoArticulo = $(this).find('option:selected').data('tipo');
        console.log("🔄 RESET-ARTICULO-SELECT: Cambio detectado en select:", {
            valor: valor,
            tipo: tipoArticulo,
            esServicio: (tipoArticulo === 'servicio')
        });
    });
});
