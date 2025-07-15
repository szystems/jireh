// Script de inicialización final mejorado
// Este script se ejecuta después de todos los otros para garantizar
// que el select de artículos esté vacío y el multiselect de trabajadores oculto.
$(document).ready(function() {
    console.log("🚀 Inicializando final-init.js - Configuración final del formulario");

    // Ejecución inmediata con verificación de estado
    inicializacionFinal();

    // Múltiples intentos para combatir cualquier inicialización tardía
    setTimeout(() => inicializacionFinal(), 200);
    setTimeout(() => inicializacionFinal(), 500);
    setTimeout(() => inicializacionFinal(), 1000);
    setTimeout(() => inicializacionFinal(), 2000); // Última verificación    // Función principal de inicialización final
    function inicializacionFinal() {
        console.log("⚙️ Aplicando configuraciones finales del formulario");

        // Referencias a elementos clave
        const $articuloSelect = $('#articulo');
        const $trabajadoresContainer = $('#trabajadores-carwash-container');
        const $stockInput = $('#stock');
        const $unidadAbreviaturaSpan = $('#unidad-abreviatura');
        const $cantidadInput = $('#cantidad-nuevo');

        // 1. Resetear y configurar correctamente el select de artículos
        if ($articuloSelect.length) {
            console.log("Iniciando configuración final del select de artículos");

            // 1.1 Verificar si hay una selección automática o de prueba
            const valorActual = $articuloSelect.val();
            // Si hay una selección activa que no fue realizada explícitamente por el usuario
            if (valorActual && !window.seleccionArticuloExplicita) {
                console.warn(`⚠️ Detectada selección no explícita: ${valorActual} - Aplicando corrección`);

                // Procedimiento de corrección avanzado
                try {
                    // Eliminar atributos selected en todas las opciones
                    $articuloSelect.find('option:selected').prop('selected', false);
                    $articuloSelect.find('option[selected]').removeAttr('selected');

                    // Asegurar que la opción vacía esté seleccionada
                    const $emptyOption = $articuloSelect.find('option[value=""]');
                    if ($emptyOption.length) {
                        $emptyOption.prop('selected', true);
                    } else {
                        // Si no existe opción vacía, crear una
                        $articuloSelect.prepend('<option value="" selected>Seleccione un artículo</option>');
                    }

                    // Reinicializar Select2 con configuración óptima
                    if ($articuloSelect.data('select2')) {
                        $articuloSelect.select2('destroy');

                        // Inicializar con opciones que previenen selección automática
                        $articuloSelect.select2({
                            dropdownParent: $articuloSelect.parent(),
                            placeholder: {
                                id: '',
                                text: 'Seleccione un artículo'
                            },
                            allowClear: true,
                            selectOnClose: false,
                            closeOnSelect: true,
                            selectionCssClass: 'no-auto-selection'
                        });

                        // Forzar valor vacío después de inicialización
                        $articuloSelect.val(null).trigger('change');
                    }
                } catch(e) {
                    console.error("Error al corregir select2:", e);
                }
            } else {
                console.log("✅ Select sin preselección - Estado correcto");
            }
        }

        // 2. Asegurar visibilidad correcta del contenedor de trabajadores
        if ($trabajadoresContainer.length) {
            // Verificar si hay algún artículo seleccionado
            const articuloSeleccionado = $articuloSelect.val();
            if (!articuloSeleccionado) {
                // Si no hay artículo, ocultar contenedor
                if ($trabajadoresContainer.is(':visible')) {
                    $trabajadoresContainer.hide();
                    console.log("Ocultando contenedor de trabajadores (no hay artículo seleccionado)");
                }
            } else {
                // Si hay artículo, verificar su tipo
                const tipoArticulo = $articuloSelect.find('option:selected').data('tipo');
                const esServicio = (tipoArticulo === 'servicio');

                // Ajustar visibilidad según tipo
                if (esServicio && !$trabajadoresContainer.is(':visible')) {
                    $trabajadoresContainer.show();
                    console.log("Mostrando contenedor de trabajadores para servicio");
                } else if (!esServicio && $trabajadoresContainer.is(':visible')) {
                    $trabajadoresContainer.hide();
                    console.log("Ocultando contenedor de trabajadores para producto");
                }
            }
        }

        // 3. Resetear campos adicionales si no hay artículo seleccionado
        if (!$articuloSelect.val()) {
            if ($stockInput.length) $stockInput.val('');
            if ($unidadAbreviaturaSpan.length) $unidadAbreviaturaSpan.text('');
            if ($cantidadInput.length) $cantidadInput.val('1');
        }

        // 4. Configuración global de Select2 para prevenir selección automática
        if ($.fn.select2 && $.fn.select2.defaults) {
            try {
                // Aplicar configuración global óptima
                $.fn.select2.defaults.set('selectOnClose', false);
                $.fn.select2.defaults.set('allowClear', true);
                console.log("Configuración global de Select2 optimizada");
            } catch (e) {
                console.error("Error al modificar configuración global:", e);
            }
        }

        console.log("✅ Inicialización final completada");
    }

    // Monitorear cambios en el select de artículos para diagnóstico
    $('#articulo').on('change', function() {
        const valor = $(this).val();
        const esServicio = $(this).find('option:selected').data('tipo') === 'servicio';
        console.log(`📢 Cambio en select de artículos: ${valor || 'vacío'} (${esServicio ? 'Servicio' : 'Producto'})`);
    });
});
