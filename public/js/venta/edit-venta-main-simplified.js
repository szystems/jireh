document.addEventListener('DOMContentLoaded', function () {
    console.log('Edit venta: Inicializando JavaScript principal...');
    
    // Variables para control de estado
    window.nuevoDetalleCount = 0;
    window.hayCambios = false;
    window.hayCambiosTrabajadores = {};

    // Acceder a la configuración global proporcionada por Blade
    window.APP_CURRENCY_SYMBOL = (window.jirehVentaConfig && typeof window.jirehVentaConfig.currencySymbol !== 'undefined') ? window.jirehVentaConfig.currencySymbol : 'Q';
    window.APP_VEHICULO_ID_ORIGINAL = (window.jirehVentaConfig && typeof window.jirehVentaConfig.vehiculoIdOriginal !== 'undefined') ? window.jirehVentaConfig.vehiculoIdOriginal : '';
    window.APP_CSRF_TOKEN = (window.jirehVentaConfig && typeof window.jirehVentaConfig.csrfToken !== 'undefined') ? window.jirehVentaConfig.csrfToken : '';

    // Función para formatear moneda
    window.formatCurrency = function(value) {
        const val = parseFloat(value);
        if (isNaN(val)) return window.APP_CURRENCY_SYMBOL + ' 0.00';
        return window.APP_CURRENCY_SYMBOL + ' ' + val.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    // Función para marcar cambios
    window.marcarCambio = function() {
        window.hayCambios = true;
    }

    // Inicialización de Select2
    $('.select2').select2({
        language: {
            noResults: () => "No se encontraron resultados",
            searching: () => "Buscando..."
        },
        width: '100%',
        selectOnClose: true
    });

    $('.select2-modal').each(function() {
        $(this).select2({
            dropdownParent: $(this).closest('.modal'),
            width: '100%',
            closeOnSelect: false,
            language: {
                noResults: () => "No se encontraron resultados",
                searching: () => "Buscando..."
            }
        });
    });

    // Función mejorada para actualizar el total de la venta
    window.actualizarTotalVenta = function() {
        const $totalElement = $('#total-venta');
        if ($totalElement.length === 0) {
            console.warn('Elemento #total-venta no encontrado');
            return;
        }

        const currencySymbol = window.APP_CURRENCY_SYMBOL;
        let total = 0;
        let elementosEncontrados = 0;

        // Buscar todos los inputs de subtotal
        const todosLosInputsSubtotal = $('input[name*="sub_total"], input.subtotal-input, input[name*="[sub_total]"]');

        todosLosInputsSubtotal.each(function() {
            const $input = $(this);
            const $row = $input.closest('tr');
            const inputValue = parseFloat($input.val()) || 0;
            
            // Verificar si debe contarse
            let debeContarse = true;

            // Verificar si está marcado para eliminar
            const $eliminarInput = $row.find('input[name*="[eliminar]"]');
            if ($eliminarInput.length > 0 && $eliminarInput.val() === '1') {
                debeContarse = false;
            }

            // Verificar si la fila está oculta
            if ($row.hasClass('detalle-oculto-por-eliminacion') || $row.is(':hidden')) {
                debeContarse = false;
            }

            // Contar si debe contarse y tiene valor válido
            if (debeContarse && !isNaN(inputValue) && inputValue > 0) {
                total += inputValue;
                elementosEncontrados++;
            }
        });

        // Formatear el total
        const totalFormateado = total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        const nuevoTextoTotal = `Total: ${currencySymbol}.${totalFormateado}`;
        
        $totalElement.html(nuevoTextoTotal);
        console.log(`Total actualizado: ${nuevoTextoTotal} (${elementosEncontrados} elementos)`);
    }

    // Calcular total inicial una sola vez
    setTimeout(function() {
        if (typeof window.actualizarTotalVenta === 'function') {
            const inputsSubtotal = $('input[name*="sub_total"], input.subtotal-input, input[name*="[sub_total]"]');
            if (inputsSubtotal.length > 0) {
                console.log('Calculando total inicial...');
                window.actualizarTotalVenta();
            }
        }
    }, 500);

    console.log('Edit venta: JavaScript principal inicializado correctamente');

    // ========================================
    // MANEJO DEL FORMULARIO DE EDICIÓN
    // ========================================
    
    // Configurar el evento submit del formulario
    const $form = $('#forma-editar-venta');
    if ($form.length > 0) {
        console.log('Formulario encontrado, configurando evento submit');
        
        $form.on('submit', function(e) {
            console.log('🚀 EVENTO SUBMIT INTERCEPTADO');
            
            // Validaciones básicas
            const clienteId = $('#cliente_id').val();
            const vehiculoId = $('#vehiculo_id').val();
            const fecha = $('#fecha').val();
            
            console.log('Validando campos:', { clienteId, vehiculoId, fecha });
            
            if (!clienteId || clienteId === '') {
                console.error('❌ Cliente no seleccionado');
                alert('Debe seleccionar un cliente');
                e.preventDefault();
                return false;
            }
            
            if (!vehiculoId || vehiculoId === '') {
                console.error('❌ Vehículo no seleccionado');
                alert('Debe seleccionar un vehículo');
                e.preventDefault();
                return false;
            }
            
            if (!fecha || fecha === '') {
                console.error('❌ Fecha no seleccionada');
                alert('Debe ingresar una fecha');
                e.preventDefault();
                return false;
            }
            
            console.log('✅ Validaciones pasadas');
            
            // Deshabilitar botón para prevenir doble envío
            const $btnGuardar = $('#btn-guardar-cambios');
            $btnGuardar.prop('disabled', true).html('<i class="bi bi-hourglass"></i> Guardando...');
            $('#mensaje-guardando').removeClass('d-none');
            
            // Log de datos del formulario
            const formData = new FormData(this);
            console.log('📤 Datos del formulario:');
            for (let [key, value] of formData.entries()) {
                console.log(`  ${key}: ${value}`);
            }
            
            console.log('🚀 Permitiendo envío del formulario');
            return true; // Permitir el envío
        });
        
        console.log('✅ Evento submit configurado correctamente');
    } else {
        console.error('❌ Formulario #forma-editar-venta no encontrado');
    }
    
    // ========================================
    // MANEJO DE ELIMINACIÓN DE DETALLES
    // ========================================
    
    // NOTA: El manejo de eliminación de detalles se hace en edit.blade.php
    // usando SweetAlert para una mejor experiencia de usuario.
    // No duplicar la lógica aquí para evitar múltiples cuadros de confirmación.
});
