$(document).ready(function() {
    // Asegurarnos de que la configuración global existe y tiene los campos necesarios
    if (!window.jirehVentaConfig) {
        window.jirehVentaConfig = {};
    }

    // Asegurarnos de que el símbolo de moneda existe
    if (!window.jirehVentaConfig.currencySymbol) {
        window.jirehVentaConfig.currencySymbol = window.jirehVentaConfig.currency_simbol || '$';
    }

    // Logging para depuración de la configuración
    console.log("jirehVentaConfig disponible:", !!window.jirehVentaConfig);
    console.log("Artículos precargados:", window.jirehVentaConfig && window.jirehVentaConfig.articulos ? window.jirehVentaConfig.articulos.length : 0);
    console.log("Ruta de artículos:", window.jirehVentaConfig && window.jirehVentaConfig.rutaObtenerArticulosParaVenta);
    console.log("Símbolo de moneda:", window.jirehVentaConfig.currencySymbol);

    const $articuloSelect = $('#articulo');
    const $stockInput = $('#stock');
    const $unidadAbreviaturaSpan = $('#unidad-abreviatura');
    const $unidadCantidadSpan = $('#unidad-cantidad'); // Nuevo elemento para mostrar la unidad en el input de cantidad
    const $cantidadNuevoInput = $('#cantidad-nuevo');
    const $descuentoNuevoSelect = $('#descuento-nuevo');
    const $trabajadoresCarwashContainer = $('#trabajadores-carwash-container');
    const $trabajadoresCarwashNuevoSelect = $('#trabajadores-carwash-nuevo');
    const $agregarDetalleBtn = $('#agregar-detalle');
    const $nuevosDetallesContainer = $('#nuevos-detalles-container');
    const $tablaNuevosDetallesBody = $('#nuevos-detalles');
    const $formaEditarVenta = $('#forma-editar-venta');

    // UTILIZAR LAS OPCIONES YA CARGADAS EN EL HTML
    console.log("Opciones existentes en el select:", $articuloSelect.find('option').length);

    // Asegurarnos de que ninguna opción esté seleccionada inicialmente
    $articuloSelect.find('option:selected').prop('selected', false);
    $articuloSelect.val('');

    // Configurar Select2 para trabajar con las opciones existentes
    $articuloSelect.select2({
        dropdownParent: $('#articulo').parent(), // Asegurar que el dropdown se muestre correctamente
        language: {
            noResults: () => "No se encontraron resultados",
            searching: () => "Buscando...",
            inputTooShort: args => `Por favor ingrese ${args.minimum - args.input.length} o más caracteres`
        },
        width: '100%',
        selectOnClose: false, // CAMBIO: false para evitar selección automática
        selectionCssClass: 'select2-articulo-selection', // Para poder identificar fácilmente
        placeholder: {
            id: '', // Asegurar que el placeholder tiene un id vacío
            text: 'Seleccione un artículo'
        },
        allowClear: true, // Permitir borrar la selección
        templateResult: function(data) {
            if (data.loading) return data.text;
            if (!data.id) return data.text;

            // Intentar obtener datos adicionales
            let stock = "N/A";
            let unidad = "";
            let tipo = null;

            // Intentar conseguir el elemento option original que contiene los data attributes
            const $option = $(data.element);
            if ($option && $option.length) {
                // Verificar los datos tanto con .data() como con .attr() para diagnóstico
                const stockFromData = $option.data('stock');
                const stockFromAttr = $option.attr('data-stock');
                const unidadFromData = $option.data('unidad-abreviatura');
                const unidadFromAttr = $option.attr('data-unidad-abreviatura');
                const tipoFromData = $option.data('tipo');
                const tipoFromAttr = $option.attr('data-tipo');

                console.log("Diagnóstico de atributos para opción " + data.id + ":", {
                    stockData: stockFromData,
                    stockAttr: stockFromAttr,
                    unidadData: unidadFromData,
                    unidadAttr: unidadFromAttr,
                    tipoData: tipoFromData,
                    tipoAttr: tipoFromAttr
                });

                // Usar el mejor valor disponible
                stock = stockFromData !== undefined ? stockFromData : (stockFromAttr !== undefined ? stockFromAttr : "N/A");
                unidad = unidadFromData || unidadFromAttr || "";
                tipo = tipoFromData || tipoFromAttr || "producto"; // Predeterminado a producto si no se especifica

                // Asegurar que el tipo y es_servicio se establezcan correctamente
                data.tipo = tipo;
                data.es_servicio = (tipo === 'servicio');

                console.log("Estableciendo es_servicio en templateResult:", data.es_servicio, "tipo:", tipo);
            }

            // Añadir un identificador visual a los servicios para depuración
            let stockText = `Stock: ${stock !== null ? stock : 'N/A'} ${unidad || ''}`;
            if (data.es_servicio) {
                stockText += ' (SERVICIO)';
            }

            return $('<span>').text(data.text).append(` <small class="text-muted">(${stockText})</small>`);
        }
    }).on('select2:select', function(e) {
        const selectedData = e.params.data;
        console.log("EVENTO select2:select ACTIVADO con datos:", selectedData);
        if (!selectedData || !selectedData.id) {
            // Limpiar campos si no hay data (aunque Select2 usualmente no permite seleccionar "nada" así)
            $stockInput.val('');
            $unidadAbreviaturaSpan.text('');
            $unidadCantidadSpan.text(''); // Limpiar la unidad en el input de cantidad
            $cantidadNuevoInput.val('1').attr('step', '0.01').attr('min', '0.01');
            $trabajadoresCarwashContainer.hide();
            $trabajadoresCarwashNuevoSelect.val(null).trigger('change');
            return;
        }

        // Verificar los datos raw del elemento option seleccionado
        const $selectedOption = $(selectedData.element);
        console.log("Datos raw del option seleccionado:", {
            tipo: $selectedOption.attr('data-tipo'),
            precio: $selectedOption.attr('data-precio'),
            stock: $selectedOption.attr('data-stock'),
            unidad: $selectedOption.attr('data-unidad'),
            unidadAbreviatura: $selectedOption.attr('data-unidad-abreviatura'),
            unidadTipo: $selectedOption.attr('data-unidad-tipo')
        });

        // Asegurar que esServicio esté correctamente establecido basado en el option seleccionado
        // Obtenemos el tipo tanto con .data() como con .attr() para diagnóstico
        const tipoFromData = $selectedOption.data('tipo');
        const tipoFromAttr = $selectedOption.attr('data-tipo');

        // Obtener datos de unidad explícitamente del option
        const unidadAbreviaturaAttr = $selectedOption.attr('data-unidad-abreviatura');
        const unidadTipoAttr = $selectedOption.attr('data-unidad-tipo');

        // Asignar el tipo usando la mejor fuente disponible
        selectedData.tipo = tipoFromData || tipoFromAttr || selectedData.tipo || 'producto';
        
        // Asegurar que la información de unidad esté disponible
        selectedData.unidad_abreviatura = unidadAbreviaturaAttr || '';
        selectedData.unidad_tipo = unidadTipoAttr || 'decimal';

        // Establecer es_servicio basado en el tipo
        const esServicio = (selectedData.tipo === 'servicio');
        selectedData.es_servicio = esServicio;

        console.log("DATOS VERIFICADOS Y CORREGIDOS:", {
            id: selectedData.id,
            tipo: selectedData.tipo,
            tipoFromData: tipoFromData,
            tipoFromAttr: tipoFromAttr,
            es_servicio: selectedData.es_servicio
        });

        $stockInput.val(selectedData.stock_disponible !== null ? selectedData.stock_disponible : 'N/A');
        $unidadAbreviaturaSpan.text(selectedData.unidad_abreviatura || '');
        $unidadCantidadSpan.text(selectedData.unidad_abreviatura || ''); // Mostrar la unidad en el input de cantidad

        if (selectedData.unidad_tipo === 'unidad') {
            $cantidadNuevoInput.attr('step', '1').attr('min', '1');
            // Si el valor actual no es un entero o es menor que 1, establecerlo a 1
            if (parseFloat($cantidadNuevoInput.val()) % 1 !== 0 || parseFloat($cantidadNuevoInput.val()) < 1) {
                $cantidadNuevoInput.val('1');
            }
        } else {
            $cantidadNuevoInput.attr('step', '0.01').attr('min', '0.01');
            if (parseFloat($cantidadNuevoInput.val()) < 0.01) {
                $cantidadNuevoInput.val('0.01');
            }
        }
        // Forzar validación de cantidad al seleccionar artículo
        $cantidadNuevoInput.trigger('input');        // Implementación robusta para mostrar/ocultar el contenedor de trabajadores
        console.log(`Cambiando visibilidad del contenedor de trabajadores: ${esServicio ? 'mostrar' : 'ocultar'}`);

        // Hacemos el cambio inmediato con dos métodos para mayor seguridad
        if (esServicio) {
            // Mostrar para servicios - aplicar múltiples métodos para asegurar visibilidad
            $trabajadoresCarwashContainer.css('display', 'block').show();
            console.log("Contenedor mostrado con CSS y show()");

            // Quitar cualquier estilo que pueda estar ocultándolo
            $trabajadoresCarwashContainer.css('visibility', 'visible');
            $trabajadoresCarwashContainer.css('opacity', '1');
            $trabajadoresCarwashContainer.css('height', 'auto');

            // Para asegurar que Select2 dentro se renderice correctamente
            if ($trabajadoresCarwashNuevoSelect.data('select2')) {
                setTimeout(() => $trabajadoresCarwashNuevoSelect.select2('destroy').select2(), 10);
            }
        } else {
            // Ocultar para productos regulares
            $trabajadoresCarwashContainer.css('display', 'none').hide();
            // Limpiar selección para evitar envío de datos innecesarios
            $trabajadoresCarwashNuevoSelect.val(null).trigger('change');
            console.log("Contenedor ocultado y selección limpiada");
        }

        // Verificación post-cambio
        setTimeout(() => {
            const visibilidadActual = $trabajadoresCarwashContainer.is(':visible');
            console.log(`Verificación: Contenedor ${visibilidadActual ? 'visible' : 'oculto'} (esperado: ${esServicio})`);

            // Forzar corrección si hay discrepancia
            if (esServicio && !visibilidadActual) {
                console.log("⚠️ Corrigiendo visibilidad forzosamente");
                $trabajadoresCarwashContainer.attr('style', 'display: block !important');
            }
        }, 50);

        console.log("Estado del contenedor de trabajadores DESPUÉS del cambio:", {
            esServicio: esServicio,
            containerVisible: $trabajadoresCarwashContainer.is(':visible'),
            cssDisplay: $trabajadoresCarwashContainer.css('display')
        });
        window.marcarCambio();
    }).on('select2:unselect', function (e) {
        $stockInput.val('');
        $unidadAbreviaturaSpan.text('');
        $unidadCantidadSpan.text(''); // Limpiar la unidad en el input de cantidad
        $cantidadNuevoInput.val('1').attr('step', '0.01').attr('min', '0.01');
        $trabajadoresCarwashContainer.hide();
        $trabajadoresCarwashNuevoSelect.val(null).trigger('change');
        window.marcarCambio();
    });

    // Validación del input de cantidad para nuevos detalles
    $cantidadNuevoInput.on('input', function() {
        const articuloSeleccionado = $articuloSelect.select2('data')[0];
        if (!articuloSeleccionado || !articuloSeleccionado.id) return;

        const stock = parseFloat(articuloSeleccionado.stock_disponible);
        const unidadTipo = articuloSeleccionado.unidad_tipo;
        const esServicio = articuloSeleccionado.es_servicio;
        let valor = $(this).val();

        // Validación específica según tipo de unidad
        if (unidadTipo === 'unidad') {
            // Para unidades, solo permitir números enteros
            valor = valor.replace(/[^0-9]/g, '');
            if (valor === '' || parseInt(valor) < 1) valor = '1';
            // Forzar a que sea un entero
            valor = parseInt(valor).toString();
        } else {
            // Para decimales, permitir punto decimal
            valor = valor.replace(/[^0-9.]/g, '');
            const parts = valor.split('.');
            if (parts.length > 2) valor = parts[0] + '.' + parts.slice(1).join('');
            if (parts[1] && parts[1].length > 2) valor = parseFloat(valor).toFixed(2);
            if (valor === '' || parseFloat(valor) < 0.01) valor = '0.01';
        }

        if (!esServicio && !isNaN(stock) && stock >= 0 && parseFloat(valor) > stock) {
            Swal.fire('Stock insuficiente', `La cantidad (${valor}) no puede exceder el stock disponible: ${stock}`, 'warning');
            valor = stock.toString();
        }
        $(this).val(valor);
        window.marcarCambio();
    });

    // Obtener nombres de los trabajadores seleccionados
    function getTrabajadoresNombres(ids, $select) {
        if (!ids || !ids.length) return [];

        // Verificar que cada ID es válido y obtener su nombre
        return ids.map(id => {
            const $option = $select.find(`option[value="${id}"]`);
            if ($option.length) {
                return $option.text();
            }
            return null; // ID inválido
        }).filter(nombre => nombre !== null); // Filtrar IDs inválidos
    }

    // Agregar nuevo detalle a la tabla
    $agregarDetalleBtn.on('click', function() {
        const articuloSeleccionado = $articuloSelect.select2('data')[0];
        const cantidad = parseFloat($cantidadNuevoInput.val());
        const descuentoId = $descuentoNuevoSelect.val();
        const descuentoOption = $descuentoNuevoSelect.find('option:selected');
        const descuentoPorcentaje = descuentoOption.data('porcentaje') ? parseFloat(descuentoOption.data('porcentaje')) : 0;
        const descuentoNombre = descuentoId ? descuentoOption.text() : 'Sin descuento';
        const trabajadoresSeleccionados = $trabajadoresCarwashNuevoSelect.val() || [];
        const trabajadoresNombres = getTrabajadoresNombres(trabajadoresSeleccionados, $trabajadoresCarwashNuevoSelect);

        if (!articuloSeleccionado || !articuloSeleccionado.id) {
            Swal.fire('Error', 'Debe seleccionar un artículo.', 'error');
            return;
        }
        if (isNaN(cantidad) || cantidad <= 0) {
            Swal.fire('Error', 'La cantidad debe ser mayor a cero.', 'error');
            return;
        }

        // Validar que la cantidad sea entera para artículos con unidad de tipo "unidad"
        const unidadTipo = $(articuloSeleccionado.element).data('unidad-tipo') || 'decimal';
        if (unidadTipo === 'unidad' && cantidad % 1 !== 0) {
            Swal.fire('Error', 'La cantidad debe ser un número entero para este tipo de artículo.', 'error');
            $cantidadNuevoInput.val(Math.floor(cantidad)); // Redondear hacia abajo
            return;
        }

        // Debugging de los datos del artículo
        console.log("Artículo seleccionado para agregar:", articuloSeleccionado);

        const stock = parseFloat(articuloSeleccionado.stock_disponible);
        // No cambiamos esta lógica ya que la validación de stock sigue igual:
        // Los servicios no tienen stock que validar, los productos regulares sí
        if (!articuloSeleccionado.es_servicio && !isNaN(stock) && stock >= 0 && cantidad > stock) {
            Swal.fire('Stock insuficiente', `La cantidad (${cantidad}) no puede exceder el stock disponible: ${stock}`, 'error');
            return;
        }

        // Obtener el precio directamente del option seleccionado para evitar problemas
        const $selectedOption = $(articuloSeleccionado.element);
        const precioUnitario = parseFloat($selectedOption.data('precio-venta') || $selectedOption.data('precio') || 0);

        if (isNaN(precioUnitario) || precioUnitario <= 0) {
            console.error("Error: No se pudo obtener el precio del artículo", {
                articuloId: articuloSeleccionado.id,
                precioAttr: $selectedOption.data('precio-venta'),
                precioAlternativo: $selectedOption.data('precio')
            });
        }

        let subtotalSinDescuento = cantidad * precioUnitario;
        let montoDescuento = subtotalSinDescuento * (descuentoPorcentaje / 100);
        let subtotalFinal = subtotalSinDescuento - montoDescuento;

        const detalleIndex = window.nuevoDetalleCount++;

        let trabajadoresHtml = '<span class="text-muted">No aplica</span>';
        let inputsTrabajadoresHtml = '';
        // CORREGIDO NUEVAMENTE: Mostramos trabajadores SOLO para servicios, no para productos regulares
        if (articuloSeleccionado.es_servicio) {
            // Obtener directamente los valores seleccionados del multiselect
            const trabajadoresIds = $trabajadoresCarwashNuevoSelect.val() || [];
            const trabajadoresTextos = [];

            // Depuración de trabajadores
            console.log("DETALLE DE TRABAJADORES SELECCIONADOS:");
            console.log("- IDs:", trabajadoresIds);
            console.log("- Select element:", $trabajadoresCarwashNuevoSelect[0]);

            // Recopilar los nombres de los trabajadores
            trabajadoresIds.forEach(id => {
                const $option = $trabajadoresCarwashNuevoSelect.find(`option[value="${id}"]`);
                if ($option.length > 0) {
                    trabajadoresTextos.push($option.text());
                }
            });

            // Guardar trabajadores en memoria global para este detalle
            if (window.trabajadoresPorDetalle) {
                // Usamos un ID único para este nuevo detalle: "nuevo-" + índice
                window.trabajadoresPorDetalle['nuevo-' + detalleIndex] = trabajadoresIds;
                console.log("Guardando trabajadores para nuevo detalle:", {
                    id: 'nuevo-' + detalleIndex,
                    trabajadores: trabajadoresIds
                });
            }

            if (trabajadoresIds.length > 0) {            // Generar HTML para mostrar los trabajadores
                trabajadoresHtml = `<span class="badge bg-info">${trabajadoresIds.length} trabajador(es)</span>`;

                if (trabajadoresTextos.length > 0) {
                    // Limitar a mostrar máximo 2 nombres y un contador para el resto
                    if (trabajadoresTextos.length <= 2) {
                        trabajadoresHtml += `<div class="small mt-1">${trabajadoresTextos.join(', ')}</div>`;
                    } else {
                        trabajadoresHtml += `<div class="small mt-1">${trabajadoresTextos[0]}, ${trabajadoresTextos[1]} y ${trabajadoresTextos.length - 2} más</div>`;
                    }
                }

                // Generar inputs ocultos para envío al servidor
                trabajadoresIds.forEach(trabId => {
                    inputsTrabajadoresHtml += `<input type="hidden" name="nuevos_detalles[${detalleIndex}][trabajadores_carwash][]" value="${trabId}">`;
                });
            } else {
                trabajadoresHtml = '<span class="badge bg-warning">Sin asignar</span>';
            }

            // Botón para editar trabajadores en nuevos detalles para productos regulares
            trabajadoresHtml += ` <button type="button" class="btn btn-link btn-sm p-0 editar-trabajadores-nuevo-btn" data-nuevo-detalle-indice="${detalleIndex}" data-articulo-nombre="${articuloSeleccionado.text}"><i class="bi bi-pencil-square"></i></button>`;
        }

        // Obtener el símbolo de moneda correcto de la configuración
        const currencySymbol = window.jirehVentaConfig.currencySymbol || window.jirehVentaConfig.currency_simbol || '$';

        // Verificar que tenemos valores numéricos válidos
        const precioFormateado = !isNaN(precioUnitario) ? precioUnitario.toFixed(2) : '0.00';
        const subtotalFormateado = !isNaN(subtotalFinal) ? subtotalFinal.toFixed(2) : '0.00';

        console.log("Generando fila de detalle con valores:", {
            articulo: articuloSeleccionado.text,
            precio: precioFormateado,
            subtotal: subtotalFormateado,
            currency: currencySymbol,
            trabajadores: trabajadoresHtml
        });

        const nuevaFilaHtml = `
            <tr id="nuevo-detalle-row-${detalleIndex}">
                <td>
                    ${articuloSeleccionado.text}
                    <input type="hidden" name="nuevos_detalles[${detalleIndex}][articulo_id]" value="${articuloSeleccionado.id}">
                    <input type="hidden" name="nuevos_detalles[${detalleIndex}][precio_unitario]" value="${precioFormateado}">
                    ${inputsTrabajadoresHtml}
                </td>
                <td>
                    ${cantidad}
                    <input type="hidden" name="nuevos_detalles[${detalleIndex}][cantidad]" value="${cantidad}">
                </td>
                <td>${currencySymbol} ${precioFormateado}</td>
                <td>
                    ${descuentoNombre}
                    <input type="hidden" name="nuevos_detalles[${detalleIndex}][descuento_id]" value="${descuentoId || ''}">
                </td>
                <td class="subtotal-nuevo-detalle">
                    ${currencySymbol} ${subtotalFormateado}
                    <input type="hidden" name="nuevos_detalles[${detalleIndex}][sub_total]" value="${subtotalFinal}" class="subtotal-input">
                </td>
                <td id="trabajadores-texto-nuevo-${detalleIndex}">${trabajadoresHtml}</td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm eliminar-nuevo-detalle" data-detalle-index="${detalleIndex}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>`;

        $tablaNuevosDetallesBody.append(nuevaFilaHtml);
        $nuevosDetallesContainer.show();

        // Limpiar campos del formulario de nuevo detalle
        $articuloSelect.val(null).trigger('change');
        $cantidadNuevoInput.val('1'); // O el valor por defecto que prefieras
        $descuentoNuevoSelect.val('').trigger('change');
        $trabajadoresCarwashNuevoSelect.val(null).trigger('change');
        $trabajadoresCarwashContainer.hide();
        $stockInput.val('');
        $unidadAbreviaturaSpan.text('');

        window.actualizarTotalVenta();
        window.marcarCambio();
    });

    // Eliminar nuevo detalle de la tabla
    $tablaNuevosDetallesBody.on('click', '.eliminar-nuevo-detalle', function() {
        $(this).closest('tr').remove();
        // Si no quedan nuevos detalles, ocultar el contenedor
        if ($tablaNuevosDetallesBody.find('tr').length === 0) {
            $nuevosDetallesContainer.hide();
        }
        window.actualizarTotalVenta();
        window.marcarCambio();
    });

    // Inicializar Select2 para descuentos y trabajadores en el formulario de nuevo detalle
    if ($descuentoNuevoSelect.length) {
        $descuentoNuevoSelect.select2({
            dropdownParent: $descuentoNuevoSelect.parent(),
            width: '100%',
            placeholder: 'Sin descuento'
        });
    }
    if ($trabajadoresCarwashNuevoSelect.length) {
        $trabajadoresCarwashNuevoSelect.select2({
            dropdownParent: $trabajadoresCarwashNuevoSelect.parent(),
            width: '100%',
            placeholder: 'Seleccione trabajadores'
        });

        // Asegurar que el contenedor de trabajadores esté oculto inicialmente
        $trabajadoresCarwashContainer.hide();
        console.log("Ocultado explícitamente el contenedor de trabajadores al inicializar");
    }
});
