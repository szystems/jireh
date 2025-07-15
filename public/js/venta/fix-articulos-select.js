// Script para arreglar el select de artículos
$(document).ready(function() {
    // Comprobar si el select existe
    const $articuloSelect = $('#articulo');
    const $stockInput = $('#stock');
    const $unidadAbreviaturaSpan = $('#unidad-abreviatura');

    if (!$articuloSelect.length) {
        console.error("No se encontró el select de artículos (#articulo) en el DOM");
        return;
    }

    // Crear un observador para debugging
    const checkStockInterval = setInterval(() => {
        const selectedData = $articuloSelect.select2('data')[0];
        if (selectedData && selectedData.id) {
            const $selectedOption = $(selectedData.element);
            const stock = $selectedOption.data('stock');
            const unidadAbreviatura = $selectedOption.data('unidad-abreviatura');

            if ($stockInput.val() === '' && stock) {
                console.log("Corrección automática de stock vacío");
                $stockInput.val(stock);
            }

            if ($unidadAbreviaturaSpan.text() === '' && unidadAbreviatura) {
                console.log("Corrección automática de unidad vacía");
                $unidadAbreviaturaSpan.text(unidadAbreviatura);
            }
        }
    }, 1000);

    console.log("Fix-articulos-select: Inicializando select de artículos. Opciones actuales:", $articuloSelect.find('option').length);

    // Destruir cualquier instancia previa de Select2
    if ($articuloSelect.data('select2')) {
        $articuloSelect.select2('destroy');
    }

    // No restauramos ningún valor seleccionado para evitar preselección de artículos
    const valorSeleccionado = null; // Forzar a que no haya valor preseleccionado

    // Remover cualquier atributo selected en las opciones antes de inicializar
    $articuloSelect.find('option:selected').prop('selected', false);
    $articuloSelect.find('option[selected]').removeAttr('selected');

    // Inicializar Select2 con las opciones existentes - configuración mejorada
    $articuloSelect.select2({
        dropdownParent: $articuloSelect.parent(),
        language: {
            noResults: () => "No se encontraron resultados",
            searching: () => "Buscando...",
        },
        width: '100%',
        selectOnClose: false, // Evitar selección automática al cerrar dropdown
        allowClear: true, // Permitir borrar la selección
        placeholder: {
            id: '', // Asegurarnos que el placeholder tiene id vacío
            text: 'Seleccione un artículo'
        },
        // Configuración para evitar selección automática/inicial
        minimumResultsForSearch: 0,
        minimumInputLength: 0,
        closeOnSelect: true,
        // Desactivar seleccionar primero por defecto
        selectionCssClass: 'no-auto-selection',
        templateResult: function(data) {
            if (data.loading) return data.text;
            if (!data.id) return data.text;

            // Intentar obtener datos adicionales
            let stock = "N/A";
            let unidad = "";

            // Intentar conseguir el elemento option original que contiene los data attributes
            const $option = $(data.element);
            if ($option && $option.data) {
                stock = $option.data('stock') !== undefined ? $option.data('stock') : "N/A";
                unidad = $option.data('unidad-abreviatura') || "";
            }

            return $('<span>').text(data.text).append(` <small class="text-muted">(Stock: ${stock !== null ? stock : 'N/A'} ${unidad || ''})</small>`);
        },
        templateSelection: function(data) {
            // Asegurar que los datos se mantengan incluso cuando esté seleccionado
            if (!data.id) return data.text;

            // Store data for later use
            const $option = $(data.element);
            data.stock_disponible = $option.data('stock');
            data.unidad_abreviatura = $option.data('unidad-abreviatura');
            data.unidad_tipo = $option.data('unidad-tipo');
            data.precio_venta = parseFloat($option.data('precio-venta') || $option.data('precio'));
            data.tipo = $option.data('tipo');
            data.es_servicio = ($option.data('tipo') === 'servicio');

            console.log("Datos guardados en artículo seleccionado:", {
                id: data.id,
                text: data.text,
                precio_venta: data.precio_venta,
                stock_disponible: data.stock_disponible,
                unidad_abreviatura: data.unidad_abreviatura,
                unidad_tipo: data.unidad_tipo,
                tipo: data.tipo,
                es_servicio: data.es_servicio
            });

            return data.text;
        }
    });

    // Función para actualizar el stock y la unidad del artículo
    function actualizarStockYUnidad(selectedArticulo) {
        // Asegurarnos que los elementos existen
        const $stockInput = $('#stock');
        const $unidadAbreviaturaSpan = $('#unidad-abreviatura');

        if (!$stockInput.length) {
            console.error("No se encontró el campo de stock (#stock)");
        }

        if (!$unidadAbreviaturaSpan.length) {
            console.error("No se encontró el span de unidad (#unidad-abreviatura)");
        }

        if (!selectedArticulo || !selectedArticulo.id) {
            // Si no hay artículo seleccionado, limpiar los campos
            $stockInput.val('');
            $unidadAbreviaturaSpan.text('');
            return;
        }

        // Obtener los datos adicionales del option seleccionado
        const $selectedOption = $(selectedArticulo.element);
        const stock = $selectedOption.data('stock');
        const unidadAbreviatura = $selectedOption.data('unidad-abreviatura');        // Actualizar los campos visibles - forzar actualización con jQuery
        $stockInput.val(stock !== undefined && stock !== null ? stock : 'N/A');
        $unidadAbreviaturaSpan.text(unidadAbreviatura || '');

        // Forzar la actualización de la vista con setTimeout
        setTimeout(() => {
            // Doble actualización para asegurar que los cambios se apliquen
            if ($stockInput.val() !== (stock !== undefined && stock !== null ? stock : 'N/A')) {
                $stockInput.val(stock !== undefined && stock !== null ? stock : 'N/A');
            }
            if ($unidadAbreviaturaSpan.text() !== (unidadAbreviatura || '')) {
                $unidadAbreviaturaSpan.text(unidadAbreviatura || '');
            }

            // Usar el atributo value directamente también (como respaldo)
            if ($stockInput[0]) {
                $stockInput[0].value = stock !== undefined && stock !== null ? stock : 'N/A';
            }

            // Debug para verificar la actualización
            console.log("Stock actualizado a:", $stockInput.val());
            console.log("Unidad actualizada a:", $unidadAbreviaturaSpan.text());
        }, 10);

        // Asegurar que el precio esté disponible en el objeto del artículo para otros scripts
        if (!selectedArticulo.precio_venta || isNaN(selectedArticulo.precio_venta)) {
            selectedArticulo.precio_venta = parseFloat($selectedOption.data('precio-venta') || $selectedOption.data('precio') || 0);
            console.log("Corrigiendo precio del artículo:", {
                id: selectedArticulo.id,
                text: selectedArticulo.text,
                precio_original: selectedArticulo.precio_venta,
                precio_corregido: parseFloat($selectedOption.data('precio-venta') || $selectedOption.data('precio') || 0)
            });
        }

        // Debug para verificar los valores
        console.log("Actualizando stock y unidad:", {
            articulo: selectedArticulo.text,
            stock: stock,
            stockValue: $stockInput.val(),
            stockElement: $stockInput[0],
            unidadAbreviatura: unidadAbreviatura,
            spanText: $unidadAbreviaturaSpan.text(),
            spanElement: $unidadAbreviaturaSpan[0],
            precio: selectedArticulo.precio_venta
        });
    }

    // Evento cuando se selecciona un artículo
    $articuloSelect.on('select2:select', function(e) {
        const selectedData = e.params.data;
        if (!selectedData || !selectedData.id) {
            actualizarStockYUnidad(null);
            return;
        }

        // Obtener los datos adicionales del option seleccionado
        const $selectedOption = $(selectedData.element);
        const stock = $selectedOption.data('stock');
        const unidadAbreviatura = $selectedOption.data('unidad-abreviatura');
        const unidadTipo = $selectedOption.data('unidad-tipo');
        const tipoArticulo = $selectedOption.data('tipo');
        const precioVenta = $selectedOption.data('precio-venta');
        const esServicio = tipoArticulo === 'servicio';

        console.log("Artículo seleccionado:", {
            id: selectedData.id,
            text: selectedData.text,
            stock,
            unidadAbreviatura,
            unidadTipo,
            tipoArticulo,
            precioVenta,
            esServicio
        });

        // Actualizar directamente los campos aquí también (doble seguridad)
        const $stockInput = $('#stock');
        const $unidadAbreviaturaSpan = $('#unidad-abreviatura');

        $stockInput.val(stock !== undefined && stock !== null ? stock : 'N/A');
        $unidadAbreviaturaSpan.text(unidadAbreviatura || '');

        // También actualizar mediante la función centralizada
        actualizarStockYUnidad(selectedData);

        // Debug: Mostrar información sobre el stock y unidad en consola
        console.log("Actualizando stock y unidad desde select event:", {
            stock: stock,
            stockValue: $('#stock').val(),
            unidadAbreviatura: unidadAbreviatura,
            spanText: $('#unidad-abreviatura').text()
        });

        // Manejar tipo de unidad
        const $cantidadNuevoInput = $('#cantidad-nuevo');
        if (unidadTipo === 'unidad') {
            $cantidadNuevoInput.attr('step', '1').attr('min', '1');
            if (parseFloat($cantidadNuevoInput.val()) % 1 !== 0 || parseFloat($cantidadNuevoInput.val()) < 1) {
                $cantidadNuevoInput.val('1');
            }
        } else {
            $cantidadNuevoInput.attr('step', '0.01').attr('min', '0.01');
            if (parseFloat($cantidadNuevoInput.val()) < 0.01) {
                $cantidadNuevoInput.val('0.01');
            }
        }

        // Implementación robusta y mejorada para mostrar/ocultar el contenedor de trabajadores
        const $trabajadoresCarwashContainer = $('#trabajadores-carwash-container');
        const $trabajadoresCarwashNuevoSelect = $('#trabajadores-carwash-nuevo');

        // Control de visibilidad basado en el tipo de artículo
        console.log(`Procesando visibilidad de trabajadores: ${esServicio ? 'Es servicio' : 'No es servicio'}`);

        if (esServicio) {
            // Para los servicios: mostrar el contenedor usando múltiples métodos
            $trabajadoresCarwashContainer.css('display', 'block');
            $trabajadoresCarwashContainer.show();
            $trabajadoresCarwashContainer.attr('style', 'display: block !important');

            // Asegurar que el multiselect se actualice correctamente
            if ($trabajadoresCarwashNuevoSelect.data('select2')) {
                setTimeout(() => {
                    // Refrescar Select2 para asegurar renderizado correcto
                    $trabajadoresCarwashNuevoSelect.select2('destroy').select2();
                }, 10);
            }

            console.log("Contenedor de trabajadores MOSTRADO para servicio");
        } else {
            // Para productos: ocultar el contenedor y limpiar selección
            $trabajadoresCarwashContainer.hide();
            $trabajadoresCarwashContainer.css('display', 'none');

            // Limpiar selección para evitar datos residuales
            if ($trabajadoresCarwashNuevoSelect.length) {
                $trabajadoresCarwashNuevoSelect.val(null).trigger('change');
            }

            console.log("Contenedor de trabajadores OCULTADO para producto");
        }

        // Verificar estado después de aplicar cambios
        setTimeout(() => {
            console.log("Estado del contenedor de trabajadores:", {
                esServicio: esServicio,
                containerVisible: $trabajadoresCarwashContainer.is(':visible'),
                displayCSS: $trabajadoresCarwashContainer.css('display')
            });
        }, 50);
    });

    // Evento cuando se deselecciona un artículo
    $articuloSelect.on('select2:unselect', function (e) {
        actualizarStockYUnidad(null);

        // Marcar cambios
        if (window.marcarCambio) {
            window.marcarCambio();
        }
    });

    // Ya no intentamos restaurar ningún valor seleccionado
    // para evitar problemas con la visualización del multiselect de trabajadores

    // Función mejorada para evitar preselección automática
    function resetarSelect() {
        console.log("Ejecutando reseteo completo del select de artículos");

        // 1. Eliminar cualquier atributo selected de todas las opciones en HTML
        $articuloSelect.find('option:selected').prop('selected', false);
        $articuloSelect.find('option[selected]').removeAttr('selected');

        // 2. Asegurarse que la opción vacía sea la primera y esté seleccionada
        let $emptyOption = $articuloSelect.find('option[value=""]');
        if ($emptyOption.length === 0) {
            $articuloSelect.prepend('<option value="">Seleccione un artículo</option>');
            $emptyOption = $articuloSelect.find('option[value=""]');
        }

        // Asegurar que la opción vacía está seleccionada
        $emptyOption.prop('selected', true);

        // 3. Resetear el valor de múltiples formas para asegurar que ninguna quede seleccionada
        try {
            // Método 1: Con jQuery val (null y string vacía para cubrir todos los casos)
            $articuloSelect.val('');

            // Método 2: Con select2 directamente - múltiples enfoques
            if ($articuloSelect.data('select2')) {
                $articuloSelect.select2('val', null);
                $articuloSelect.select2('val', '');
                $articuloSelect.val(null).select2('val', null);
            }

            // Método 3: Disparar cambio después de todos los resets
            $articuloSelect.trigger('change');

        } catch(e) {
            console.warn("Error al resetear select:", e);
        }

        // 4. Limpiar otros campos relacionados
        $stockInput.val('');
        $unidadAbreviaturaSpan.text('');

        console.log("Select resetado. Valor actual:", $articuloSelect.val());

        // 5. Verificar estado del contenedor de trabajadores
        const $trabajadoresContainer = $('#trabajadores-carwash-container');
        if ($trabajadoresContainer.length && $trabajadoresContainer.is(':visible')) {
            $trabajadoresContainer.hide();
            console.log("Contenedor de trabajadores ocultado durante reseteo");
        }
    }

    // Hacer múltiples intentos de reseteo
    setTimeout(resetarSelect, 100);
    setTimeout(resetarSelect, 250);
    setTimeout(resetarSelect, 500);

    // Se removió el botón de refrescar valores que no era necesario

    // Forzar opciones correctas en el HTML del select
    setTimeout(() => {
        // Verificar si todavía hay alguna opción seleccionada
        if ($articuloSelect.val()) {
            console.log("¡ALERTA! El select aún tiene valor:", $articuloSelect.val());
            console.log("Aplicando corrección de emergencia...");

            // Recrear el select desde cero
            const optionsHtml = $articuloSelect.html();
            $articuloSelect.html('<option value="">Seleccione un artículo</option>' +
                                  optionsHtml.replace(/selected="selected"/g, ''));

            // Forzar que no haya selección
            $articuloSelect.val(null).trigger('change');
        }
    }, 1000);
});
