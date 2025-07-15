$(document).ready(function() {
    const $form = $('#forma-editar-venta');
    const $btnGuardarCambios = $('#btn-guardar-cambios');
    const $mensajeGuardando = $('#mensaje-guardando');

    $btnGuardarCambios.on('click', function(e) {
        e.preventDefault();

        // Comprobar si realmente hay cambios antes de enviar
        const currentFormData = $form.serialize();
        let cambiosReales = window.hayCambios; // Considera cambios marcados por interacciones

        // Si no se marcaron cambios por interacción, comparar el formulario
        // Esto es un fallback, idealmente window.hayCambios debería ser suficiente
        if (!cambiosReales && window.originalFormData === currentFormData) {
            // Verificar si hay cambios en los trabajadores de detalles existentes
            // que no se reflejan directamente en la serialización del formulario principal
            // hasta que se reconstruyen los inputs en el backend.
            if (Object.keys(window.hayCambiosTrabajadores || {}).length === 0) {
                Swal.fire('Sin cambios', 'No se han detectado modificaciones para guardar.', 'info');
                return;
            }
        }

        $btnGuardarCambios.prop('disabled', true).find('i').removeClass('bi-check-circle').addClass('bi-hourglass-split');
        $mensajeGuardando.removeClass('d-none');
        window.hayCambios = false; // Resetear flag para la advertencia de salida

        // Preparar datos del formulario, incluyendo los detalles nuevos y existentes
        let formData = $form.serializeArray();

        // Asegurarnos de que se incluya el campo _method=PUT
        let hasMethodField = false;
        for (let i = 0; i < formData.length; i++) {
            if (formData[i].name === '_method') {
                formData[i].value = 'PUT';
                hasMethodField = true;
                break;
            }
        }

        if (!hasMethodField) {
            formData.push({
                name: '_method',
                value: 'PUT'
            });
        }

        // Asegurarse de que los detalles marcados para eliminar se envíen correctamente.
        // El script de detalles existentes ya maneja el input `detalles[id][eliminar]=1`.

        // Los nuevos detalles ya están siendo construidos con names como `nuevos_detalles[index][propiedad]`
        // y los inputs de trabajadores para nuevos detalles también se añaden al form.

        $.ajax({
            url: window.jirehVentaConfig.rutaGuardarVenta || $form.attr('action'),
            type: 'POST', // Usamos POST para que Laravel interprete _method como PUT
            data: $.param(formData),
            headers: {
                'X-CSRF-TOKEN': window.jirehVentaConfig.csrfToken
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: response.message || 'Los cambios se han guardado correctamente.',
                        timer: 2000,
                        timerProgressBar: true,
                        willClose: () => {
                            window.location.href = response.redirect_url || '{{ url("ventas") }}'; // Usar redirect_url si se proporciona
                        }
                    });
                } else {
                    Swal.fire('Error', response.message || 'Ocurrió un error al guardar los cambios.', 'error');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error en la solicitud AJAX:', textStatus, errorThrown, jqXHR.responseText);
                let errorMessage = 'Ocurrió un error inesperado.';
                if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                    errorMessage = jqXHR.responseJSON.message;
                    if (jqXHR.responseJSON.errors) {
                        const errors = jqXHR.responseJSON.errors;
                        const errorMessages = Object.values(errors).flat();
                        errorMessage += ':<br><ul><li>' + errorMessages.join('</li><li>') + '</li></ul>';
                    }
                }
                Swal.fire('Error de Servidor', errorMessage, 'error');
            },
            complete: function() {
                $btnGuardarCambios.prop('disabled', false).find('i').removeClass('bi-hourglass-split').addClass('bi-check-circle');
                $mensajeGuardando.addClass('d-none');
            }
        });
    });
});
