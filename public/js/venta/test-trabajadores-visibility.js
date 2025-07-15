// Script de prueba para verificar la visibilidad de trabajadores
document.addEventListener('DOMContentLoaded', function() {
    // Ejecutar después de que todo esté listo
    setTimeout(() => {
        console.log("INICIANDO PRUEBA DE VISIBILIDAD DE TRABAJADORES");

        // Elementos importantes
        const $trabajadoresContainer = $('#trabajadores-carwash-container');
        const $articuloSelect = $('#articulo');

        console.log("Contenedor de trabajadores:", {
            element: $trabajadoresContainer[0],
            visible: $trabajadoresContainer.is(':visible'),
            cssDisplay: $trabajadoresContainer.css('display')
        });

        // Verificar las opciones en el select de artículos
        const opciones = $articuloSelect.find('option');
        console.log(`Encontradas ${opciones.length} opciones en el select de artículos`);

        // Buscar opciones de servicio y producto
        let opcionServicio = null;
        let opcionProducto = null;

        opciones.each(function() {
            const $option = $(this);
            const tipo = $option.data('tipo');
            if (tipo === 'servicio' && !opcionServicio) {
                opcionServicio = $option;
            } else if (tipo !== 'servicio' && !opcionProducto) {
                opcionProducto = $option;
            }

            // Si ya tenemos ambos, salimos del bucle
            if (opcionServicio && opcionProducto) return false;
        });

        // Mostrar información sobre las opciones encontradas
        console.log("Opción de servicio encontrada:", opcionServicio ? {
            id: opcionServicio.val(),
            text: opcionServicio.text(),
            tipo: opcionServicio.data('tipo')
        } : "No encontrada");

        console.log("Opción de producto encontrada:", opcionProducto ? {
            id: opcionProducto.val(),
            text: opcionProducto.text(),
            tipo: opcionProducto.data('tipo')
        } : "No encontrada");

        // Si tenemos ambas opciones, probar seleccionar cada una y ver el comportamiento
        if (opcionServicio && opcionProducto) {
            // Primero seleccionamos un producto
            console.log("SELECCIONANDO PRODUCTO PARA PRUEBA");
            $articuloSelect.val(opcionProducto.val()).trigger('change');

            // Esperar un momento y verificar estado
            setTimeout(() => {
                console.log("DESPUÉS DE SELECCIONAR PRODUCTO:", {
                    trabajadoresVisible: $trabajadoresContainer.is(':visible'),
                    cssDisplay: $trabajadoresContainer.css('display')
                });                // Ahora seleccionamos un servicio
                console.log("SELECCIONANDO SERVICIO PARA PRUEBA");
                $articuloSelect.val(opcionServicio.val()).trigger('change');

                // Verificar estado múltiples veces para ver si eventualmente cambia
                function checkVisibilidad(intento) {
                    console.log(`VERIFICACIÓN #${intento} DESPUÉS DE SELECCIONAR SERVICIO:`, {
                        trabajadoresVisible: $trabajadoresContainer.is(':visible'),
                        cssDisplay: $trabajadoresContainer.css('display'),
                        esServicio: $articuloSelect.find('option:selected').data('tipo') === 'servicio'
                    });

                    // Si no está visible y debería estarlo, intentar forzarlo
                    if (!$trabajadoresContainer.is(':visible') &&
                        $articuloSelect.find('option:selected').data('tipo') === 'servicio') {
                        console.log("FORZANDO VISIBILIDAD MANUALMENTE");
                        $trabajadoresContainer.show();
                        $trabajadoresContainer.css('display', 'block');
                    }

                    if (intento < 5) {
                        setTimeout(() => checkVisibilidad(intento + 1), 200);
                    } else {
                        // Al finalizar la prueba, limpiar la selección
                        console.log("PRUEBA FINALIZADA - Limpiando selección de artículo");
                        $articuloSelect.val(null).trigger('change');
                    }
                }

                // Iniciar verificación
                setTimeout(() => checkVisibilidad(1), 100);
            }, 500);
        } else {
            console.log("No se pueden realizar pruebas automáticas, faltan opciones de servicio o producto");
        }
    }, 1000);
});
