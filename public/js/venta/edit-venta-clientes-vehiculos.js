$(document).ready(function() {
    // Cargar vehículos cuando se selecciona un cliente
    $('#cliente_id').on('change', function() {
        const clienteId = $(this).val();
        const vehiculoSelect = $('#vehiculo_id');
        // Usar la variable global definida en edit-venta-main.js a través de jirehVentaConfig
        const originalVehiculoId = window.APP_VEHICULO_ID_ORIGINAL;

        vehiculoSelect.empty().append('<option value="">Seleccione un vehículo</option>');

        if (!clienteId) {
            vehiculoSelect.val("").trigger('change'); // Asegurar que Select2 se actualice
            return;
        }

        $.ajax({
            url: `/api/clientes/${clienteId}/vehiculos`,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data && data.length > 0) {
                    data.forEach(function(vehiculo) {
                        vehiculoSelect.append(new Option(`${vehiculo.marca} ${vehiculo.modelo} - ${vehiculo.placa}`, vehiculo.id, false, false));
                    });
                }
                // Intentar seleccionar el vehículo original si el cliente es el mismo o si es la carga inicial
                // y el vehículo original pertenece al cliente recién seleccionado.
                // Esta lógica puede necesitar ajustarse si el $venta->cliente_id cambia y $venta->vehiculo_id ya no es válido.
                if (originalVehiculoId) {
                     // Comprobar si el vehículo original está en la lista cargada
                    if (vehiculoSelect.find(`option[value="${originalVehiculoId}"]`).length > 0) {
                        vehiculoSelect.val(originalVehiculoId);
                    } else {
                        // Si el vehículo original no está en la lista, significa que no pertenece al cliente seleccionado
                        // o que el cliente ha cambiado y el vehículo ya no es relevante. Se resetea.
                        vehiculoSelect.val("");
                    }
                }
                vehiculoSelect.trigger('change'); // Notificar a Select2 del cambio
                window.marcarCambio();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error al cargar vehículos:', textStatus, errorThrown, jqXHR.responseText);
                Swal.fire('Error', 'No se pudieron cargar los vehículos del cliente.', 'error');
                vehiculoSelect.trigger('change'); // Asegurar que Select2 se actualice incluso en error
            }
        });
    });

    // Disparar evento change si ya hay un cliente seleccionado al cargar la página
    // para cargar los vehículos correspondientes y seleccionar el vehículo original si existe.
    if ($('#cliente_id').val()) {
        $('#cliente_id').trigger('change');
    } else {
        // Si no hay cliente seleccionado, asegurar que el select de vehículo esté vacío y Select2 actualizado.
        $('#vehiculo_id').empty().append('<option value="">Seleccione un vehículo</option>').trigger('change');
    }
});
