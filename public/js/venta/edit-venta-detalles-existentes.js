$(document).ready(function() {
    const tablaDetallesExistentes = $('#tabla-detalles-existentes'); // Asumiendo que la tabla de detalles existentes tiene este ID

    // Función para recalcular el subtotal de un detalle existente
    function recalcularSubtotalExistente(detalleId) {
        const fila = $(`#detalle-row-${detalleId}`);
        const cantidadInput = fila.find('.cantidad-input');
        const cantidad = parseFloat(cantidadInput.val());
        const precioUnitario = parseFloat(cantidadInput.data('precio'));

        const descuentoSelect = fila.find('.descuento-select');
        const descuentoId = descuentoSelect.val();
        const descuentoPorcentaje = descuentoId ? parseFloat(descuentoSelect.find('option:selected').data('porcentaje')) / 100 : 0;

        if (isNaN(cantidad) || isNaN(precioUnitario)) {
            console.warn(`Cantidad (${cantidad}) o precio unitario (${precioUnitario}) no válidos para detalle existente ${detalleId}`);
            // Podrías querer poner el subtotal a 0 o mostrar un error
            $(`#subtotal-${detalleId}`).html(window.formatCurrency(0));
            fila.find('input.subtotal-input').val(0);
            setTimeout(() => {
                console.log(`🔄 Recalculando total después de error en detalle ${detalleId}`);
                window.actualizarTotalVenta();
            }, 10);
            return;
        }

        let subtotalSinDescuento = cantidad * precioUnitario;
        let montoDescuento = subtotalSinDescuento * descuentoPorcentaje;
        let subtotalFinal = subtotalSinDescuento - montoDescuento;

        // Actualizar solo el input del subtotal y luego actualizar el texto visible
        fila.find('input.subtotal-input').val(subtotalFinal.toFixed(2));
        
        // Actualizar el texto visible sin afectar los inputs hidden
        const subtotalCell = $(`#subtotal-${detalleId}`);
        const textoExistente = subtotalCell.html();
        const inputsHidden = subtotalCell.find('input').detach(); // Separar los inputs
        subtotalCell.html(window.formatCurrency(subtotalFinal)); // Actualizar solo el texto
        subtotalCell.append(inputsHidden); // Volver a agregar los inputs

        // Verificar que el input se actualizó correctamente antes de recalcular
        const inputActualizado = fila.find('input.subtotal-input');
        console.log(`🔄 Detalle ${detalleId} actualizado: name="${inputActualizado.attr('name')}", value="${inputActualizado.val()}"`);

        // Usar timeout para asegurar que el DOM esté completamente actualizado antes del recálculo
        setTimeout(() => {
            console.log(`🔄 Recalculando total después de actualizar detalle ${detalleId}`);
            window.actualizarTotalVenta();
        }, 10);
    }

    // Evento input para la cantidad en detalles existentes
    tablaDetallesExistentes.on('input', '.cantidad-input', function() {
        const detalleId = $(this).data('detalle-id');
        // Leer el stock original desde el atributo data-stock-original
        const stockOriginal = parseFloat($(this).data('stock-original'));
        const unidadTipo = $(this).data('unidad-tipo');
        const articuloTipo = $(this).data('articulo-tipo'); // Leer el tipo de artículo
        let valor = $(this).val();

        if (unidadTipo === 'unidad') {
            valor = valor.replace(/[^0-9]/g, ''); // Solo enteros
            if (valor === '' || parseInt(valor) < 1) valor = '1'; // Mínimo 1 para unidades
        } else { // Decimal
            valor = valor.replace(/[^0-9.]/g, ''); // Permitir números y punto
            const parts = valor.split('.');
            if (parts.length > 2) valor = parts[0] + '.' + parts.slice(1).join(''); // Solo un punto decimal
            if (parts[1] && parts[1].length > 2) valor = parseFloat(valor).toFixed(2); // Máximo 2 decimales
            if (valor === '' || parseFloat(valor) < 0.01) valor = '0.01'; // Mínimo 0.01 para decimales
        }

        // Validar contra stock original solo si es un 'articulo' (no 'servicio')
        // y si el stock original es un número válido y mayor o igual a cero.
        if (articuloTipo === 'articulo' && !isNaN(stockOriginal) && stockOriginal >= 0 && parseFloat(valor) > stockOriginal) {
            Swal.fire(
                'Stock insuficiente',
                `La cantidad (${valor}) no puede exceder el stock original disponible para este ítem: ${stockOriginal}. Si necesita más, agregue un nuevo detalle.`,
                'warning'
            );
            valor = stockOriginal.toString();
        }

        $(this).val(valor);
        recalcularSubtotalExistente(detalleId);
        window.marcarCambio();
    });

    // Evento change para el select de descuento en detalles existentes
    tablaDetallesExistentes.on('change', '.descuento-select', function() {
        const detalleId = $(this).data('detalle-id');
        recalcularSubtotalExistente(detalleId);
        window.marcarCambio();
    });

    // Eliminar detalle existente (marcar para eliminación)
    tablaDetallesExistentes.on('click', '.eliminar-detalle', function(e) { // Añadir parámetro 'e' para el evento
        e.preventDefault(); // Prevenir cualquier acción por defecto del botón
        const detalleId = $(this).data('detalle-id');
        const row = $(`#detalle-row-${detalleId}`);

        // Marcar el input hidden 'eliminar' que ya existe en la fila
        $(`input[name="detalles[${detalleId}][eliminar]"]`).val('1');

        // Opcional: Añadir un input global para el backend si prefiere procesar una lista de IDs a eliminar
        // if ($(`input[name="detalles_a_eliminar[]"][value="${detalleId}"]`).length === 0) {
        //     $('#forma-editar-venta').append(`<input type="hidden" name="detalles_a_eliminar[]" value="${detalleId}">`);
        // }

        row.addClass('detalle-oculto-por-eliminacion').hide(); // Ocultar la fila y añadir clase para recálculo de total

        const confirmRowId = `confirm-row-${detalleId}`;
        if ($(`#${confirmRowId}`).length === 0) { // Evitar duplicados
            const cells = row.find('td').length;
            const articuloNombre = row.find('td:first').text().trim().split('\n')[0] || 'Artículo'; // Tomar solo el nombre
            const confirmRowHtml = `
                <tr id="${confirmRowId}" class="table-danger detalle-eliminado-confirmacion">
                    <td colspan="${cells}">
                        "${articuloNombre}" será eliminado.
                        <button type="button" class="btn btn-sm btn-outline-secondary restaurar-detalle-existente-btn" data-detalle-id="${detalleId}">Restaurar</button>
                    </td>
                </tr>`;
            row.after(confirmRowHtml);
        }

        setTimeout(() => {
            console.log(`🔄 Recalculando total después de eliminar detalle ${detalleId}`);
            window.actualizarTotalVenta();
        }, 10);
        window.marcarCambio();
    });

    // Restaurar detalle existente (desmarcar para eliminación)
    // Usar delegación de eventos en un contenedor estático (la tabla o el documento)
    tablaDetallesExistentes.on('click', '.restaurar-detalle-existente-btn', function(e) { // Añadir parámetro 'e' para el evento
        e.preventDefault(); // Prevenir cualquier acción por defecto del botón
        const detalleId = $(this).data('detalle-id');

        $(`input[name="detalles[${detalleId}][eliminar]"]`).val('0');
        // if ($(`input[name="detalles_a_eliminar[]"][value="${detalleId}"]`).length > 0) {
        //     $(`input[name="detalles_a_eliminar[]"][value="${detalleId}"]`).remove();
        // }

        $(`#detalle-row-${detalleId}`).removeClass('detalle-oculto-por-eliminacion').show();
        $(`#confirm-row-${detalleId}`).remove();

        setTimeout(() => {
            console.log(`🔄 Recalculando total después de restaurar detalle ${detalleId}`);
            window.actualizarTotalVenta();
        }, 10);
        window.marcarCambio();
    });

    // NO recalcular subtotales existentes al cargar la página
    // Los subtotales ya están correctos desde el servidor
    // Solo marcar que no necesitan inicialización
    console.log('Detalles existentes cargados - conservando subtotales del servidor');
});
