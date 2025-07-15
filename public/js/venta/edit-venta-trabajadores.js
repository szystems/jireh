// Inicializar la variable global para almacenar trabajadores
if (!window.trabajadoresPorDetalle) {
    window.trabajadoresPorDetalle = {};
    console.log("Variable global trabajadoresPorDetalle inicializada");
}

$(document).ready(function() {
    let currentEditDetalleId = null;
    let isEditingNuevoDetalleTrabajadores = false; // true si se editan trabajadores de un nuevo detalle, false para existente
    let $modal = $('#editar-trabajadores-modal');
    let $selectTrabajadoresEdit = $('#trabajadores-carwash-edit'); // El select dentro del modal

    // Inicializar Select2 para el selector de trabajadores en el MODAL
    if ($selectTrabajadoresEdit.length) {
        $selectTrabajadoresEdit.select2({
            dropdownParent: $modal.find('.modal-body'), // Contenedor del modal
            width: '100%',
            placeholder: 'Seleccione trabajadores'
        });
    }

    // Abrir modal para editar trabajadores de DETALLES EXISTENTES
    $('#tabla-detalles-existentes').on('click', '.editar-trabajadores', function() {
        currentEditDetalleId = $(this).data('detalle-id');
        isEditingNuevoDetalleTrabajadores = false;
        const articuloNombre = $(this).data('articulo-nombre') || 'Servicio';
        $modal.find('#servicio-nombre').text(articuloNombre);
        $modal.find('.modal-title').text('Asignar Trabajadores (Existente)');

        // IMPORTANTE: Vamos a usar una variable global para almacenar la lista de trabajadores por detalle
        // Esta estrategia evita depender de los inputs en el DOM que pueden ser difíciles de localizar
        if (!window.trabajadoresPorDetalle) {
            window.trabajadoresPorDetalle = {};
        }

        // IMPORTANTE: Primero consultamos los inputs ocultos actuales (que contienen los cambios más recientes)
        let trabajadoresActuales = [];

        // Si tenemos trabajadores almacenados para este detalle, los usamos
        if (window.trabajadoresPorDetalle[currentEditDetalleId]) {
            console.log("Usando trabajadores de la memoria:", window.trabajadoresPorDetalle[currentEditDetalleId]);
            trabajadoresActuales = window.trabajadoresPorDetalle[currentEditDetalleId];
        } else {
            // Debuggear el contenedor para ver si existe y qué contiene
            console.log(`Buscando trabajadores en contenedor #trabajadores-${currentEditDetalleId}`);
            const $contenedor = $(`#trabajadores-${currentEditDetalleId}`);
            console.log("Contenedor encontrado:", $contenedor.length);

            // Usamos un selector más general para asegurarnos de encontrar los inputs
            // sin importar su nombre exacto
            $contenedor.find('input').each(function() {
                console.log("Input encontrado:", $(this).attr('name'), "valor:", $(this).val());
                trabajadoresActuales.push($(this).val());
            });

        // Si no encontramos inputs, intentamos directamente con el selector de nombre esperado
        if (trabajadoresActuales.length === 0) {
            $(`input[name^="trabajadores_carwash[${currentEditDetalleId}]"]`).each(function() {
                console.log("Input por nombre encontrado:", $(this).attr('name'), "valor:", $(this).val());
                trabajadoresActuales.push($(this).val());
            });
        }

            // Si seguimos sin encontrar trabajadores, usamos los datos originales
            if (trabajadoresActuales.length === 0) {
                const detallesOriginales = window.jirehVentaConfig.detallesOriginales || {};
                const detalleActual = detallesOriginales[currentEditDetalleId] || {};
                const trabajadoresOriginales = detalleActual.trabajadores_asignados || [];
                console.log("Usando trabajadores originales:", trabajadoresOriginales);
                trabajadoresActuales.push(...trabajadoresOriginales);
            }
        }

        console.log("Cargando trabajadores finales para detalle #" + currentEditDetalleId, trabajadoresActuales);
        $selectTrabajadoresEdit.val(trabajadoresActuales).trigger('change');
        $modal.modal('show');
    });

    // Abrir modal para editar trabajadores de NUEVOS DETALLES (desde la tabla de nuevos)
    $('#nuevos-detalles').on('click', '.editar-trabajadores-nuevo-btn', function() {
        currentEditDetalleId = $(this).data('nuevo-detalle-indice'); // Índice del nuevo detalle
        isEditingNuevoDetalleTrabajadores = true;
        const articuloNombre = $(this).data('articulo-nombre') || 'Servicio';
        $modal.find('#servicio-nombre').text(articuloNombre);
        $modal.find('.modal-title').text('Asignar Trabajadores (Nuevo)');

        // IMPORTANTE: Consultar primero la memoria global para nuevos detalles también
        let trabajadoresActuales = [];

        // Usar los trabajadores guardados en memoria si existen
        if (window.trabajadoresPorDetalle['nuevo-' + currentEditDetalleId]) {
            console.log("Usando trabajadores de memoria para nuevo detalle:", window.trabajadoresPorDetalle['nuevo-' + currentEditDetalleId]);
            trabajadoresActuales = window.trabajadoresPorDetalle['nuevo-' + currentEditDetalleId];
        } else {
            // Si no están en memoria, buscar en los inputs del DOM
            $(`input[name^="nuevos_detalles[${currentEditDetalleId}][trabajadores_carwash]"]`).each(function() {
                console.log("Input nuevo detalle encontrado:", $(this).attr('name'), "valor:", $(this).val());
                trabajadoresActuales.push($(this).val());
            });
        }

        $selectTrabajadoresEdit.val(trabajadoresActuales).trigger('change');
        $modal.modal('show');
    });

    // Aplicar cambios de trabajadores desde el modal
    $('#guardar-trabajadores').on('click', function() { // ID del botón "Aplicar cambios" en el modal
        const selectedTrabajadoresIds = $selectTrabajadoresEdit.val() || [];
        const detalleIdOrIndex = currentEditDetalleId;

        // IMPORTANTE: Almacenar los trabajadores en la memoria global para persistencia
        if (isEditingNuevoDetalleTrabajadores) {
            // Para nuevos detalles, usamos un prefijo para no confundirlos con los existentes
            const memoryKey = 'nuevo-' + detalleIdOrIndex;
            window.trabajadoresPorDetalle[memoryKey] = selectedTrabajadoresIds;
            console.log("Guardando en memoria global para el NUEVO detalle", memoryKey, selectedTrabajadoresIds);

            // Para nuevos detalles, actualizar los inputs ocultos y la tabla visual
            const $filaNuevoDetalle = $(`#nuevo-detalle-row-${detalleIdOrIndex}`);

            // Eliminar inputs de trabajadores anteriores para este nuevo detalle
            $(`input[name^="nuevos_detalles[${detalleIdOrIndex}][trabajadores_carwash]"]`).remove();

            let trabajadoresNombres = [];
            if (selectedTrabajadoresIds.length > 0) {
                selectedTrabajadoresIds.forEach(id => {
                    // Añadir input oculto al formulario (fuera de la tabla, directamente en el form o en un div contenedor)
                    $('#forma-editar-venta').append(`<input type="hidden" name="nuevos_detalles[${detalleIdOrIndex}][trabajadores_carwash][]" value="${id}">`);
                    trabajadoresNombres.push($selectTrabajadoresEdit.find(`option[value="${id}"]`).text().split(' (')[0]); // Tomar solo el nombre
                });
            }

            let textoTrabajadores = '<span class="badge bg-warning">Sin asignar</span>';
            if (trabajadoresNombres.length > 0) {
                textoTrabajadores = trabajadoresNombres.join(', ');
            }

            // Actualizar la celda de texto en la tabla de nuevos detalles
            $filaNuevoDetalle.find(`#trabajadores-texto-nuevo-${detalleIdOrIndex}`).html(
                `${textoTrabajadores}
                 <button type="button" class="btn btn-link btn-sm p-0 editar-trabajadores-nuevo-btn" data-nuevo-detalle-indice="${detalleIdOrIndex}" data-articulo-nombre="${$modal.find('#servicio-nombre').text()}"><i class="bi bi-pencil-square"></i></button>`
            );
        } else {
            // Para detalles existentes, actualizar los inputs ocultos y la tabla visual
            window.trabajadoresPorDetalle[detalleIdOrIndex] = selectedTrabajadoresIds;
            console.log("Guardando en memoria global para el detalle existente", detalleIdOrIndex, selectedTrabajadoresIds);

            const $filaDetalleExistente = $(`#detalle-row-${detalleIdOrIndex}`);

            // IMPORTANTE: Verificar y depurar dónde estamos guardando los trabajadores
            console.log("Guardando trabajadores para el detalle:", detalleIdOrIndex);
            console.log("Trabajadores seleccionados:", selectedTrabajadoresIds);

            // Limpiar el contenedor de los inputs de trabajadores
            let $trabajadoresContainer = $(`#trabajadores-${detalleIdOrIndex}`);
            console.log("Contenedor de trabajadores encontrado:", $trabajadoresContainer.length);

            // Si el contenedor no existe, lo creamos
            if ($trabajadoresContainer.length === 0) {
                console.log("Creando contenedor de trabajadores");
                $filaDetalleExistente.find('td:first').append(`<div id="trabajadores-${detalleIdOrIndex}" class="trabajadores-container"></div>`);
                // Actualizamos la referencia al contenedor
                $trabajadoresContainer = $(`#trabajadores-${detalleIdOrIndex}`);
            }

            $trabajadoresContainer.empty();
            console.log("Contenedor limpiado");

            let trabajadoresNombres = [];
            if (selectedTrabajadoresIds.length > 0) {
                selectedTrabajadoresIds.forEach(id => {
                    // Añadir input oculto al contenedor específico para los trabajadores
                    $trabajadoresContainer.append(`<input type="hidden" name="trabajadores_carwash[${detalleIdOrIndex}][]" value="${id}">`);
                    console.log(`Input añadido: trabajadores_carwash[${detalleIdOrIndex}][] = ${id}`);

                    // Guardar el nombre para mostrar en la interfaz
                    const optionText = $selectTrabajadoresEdit.find(`option[value="${id}"]`).text();
                    const nombreCompleto = optionText.split(' (')[0];
                    trabajadoresNombres.push(nombreCompleto);
                });
            }

            let badgeHtml = '';
            let nombresHtml = '';
            if (trabajadoresNombres.length > 0) {
                badgeHtml = `<span class="badge bg-info">${trabajadoresNombres.length} trabajador(es)</span>`;
                if (trabajadoresNombres.length <= 2) {
                    nombresHtml = trabajadoresNombres.join(', ');
                } else {
                    nombresHtml = `${trabajadoresNombres[0]}, ${trabajadoresNombres[1]} y ${trabajadoresNombres.length - 2} más`;
                }
                $filaDetalleExistente.find(`#trabajadores-text-${detalleIdOrIndex}`).html(`${badgeHtml}<div class="small mt-1">${nombresHtml}</div>`);
            } else {
                $filaDetalleExistente.find(`#trabajadores-text-${detalleIdOrIndex}`).html('<span class="badge bg-warning">Sin asignar</span>');
            }
            // Marcar que hubo un cambio específico en los trabajadores de este detalle existente
            window.hayCambiosTrabajadores[detalleIdOrIndex] = true;
        }

        // Antes de ocultar el modal, guardamos una referencia al elemento que debería recibir el foco
        const $focusAfterClose = $(`.editar-trabajadores[data-detalle-id="${detalleIdOrIndex}"]`);

        $modal.modal('hide');

        // Transferimos el foco después de un pequeño retraso para asegurarnos de que Bootstrap haya terminado
        setTimeout(function() {
            if ($focusAfterClose.length) {
                $focusAfterClose.focus();
            } else {
                // Si no encontramos el botón específico, enfocamos el body como fallback
                $('body').attr('tabindex', '-1').focus().removeAttr('tabindex');
            }
        }, 100);

        window.marcarCambio(); // Marcar cambio general para la advertencia de salida
        // No es necesario llamar a calcularComisiones aquí, se hará en el backend al guardar.
    });

    // Manejo del evento de cierre del modal - solo reiniciamos variables, pero NO limpiamos la selección
    // para no interferir con la persistencia de datos
    $modal.on('hidden.bs.modal', function () {
        // No limpiamos el select para evitar problemas con la persistencia de datos
        // $selectTrabajadoresEdit.val(null).trigger('change');

        // Al cerrar el modal, aseguramos que ningún elemento dentro de él tenga el foco
        // Esto evita problemas de accesibilidad con aria-hidden
        if (document.activeElement && $.contains($modal[0], document.activeElement)) {
            $('body').attr('tabindex', '-1').focus().removeAttr('tabindex');
        }

        currentEditDetalleId = null;
        isEditingNuevoDetalleTrabajadores = false;
        $modal.find('#servicio-nombre').text('');
    });
});
