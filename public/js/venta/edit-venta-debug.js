// Script para depuración de problemas en edit-venta
$(document).ready(function() {
    // Debug para artículos
    console.log("=== DEBUG INFORMACIÓN ===");
    console.log("jirehVentaConfig disponible:", !!window.jirehVentaConfig);
    if (window.jirehVentaConfig) {
        console.log("Rutas disponibles:", {
            rutaObtenerArticulosParaVenta: window.jirehVentaConfig.rutaObtenerArticulosParaVenta,
            rutaGuardarVenta: window.jirehVentaConfig.rutaGuardarVenta
        });
        console.log("Artículos precargados:",
            window.jirehVentaConfig.articulos ?
            window.jirehVentaConfig.articulos.length + " artículos" :
            "No hay artículos precargados");

        // Mostrar los primeros 2 artículos para diagnóstico
        if (window.jirehVentaConfig.articulos && window.jirehVentaConfig.articulos.length > 0) {
            console.log("Muestra de los primeros 2 artículos:",
                window.jirehVentaConfig.articulos.slice(0, 2));
        }
        
        // Debugging de AJAX requests
        $(document).ajaxSend(function(event, jqXHR, settings) {
            console.log('Enviando solicitud AJAX:', {
                url: settings.url,
                type: settings.type,
                method: settings.method || settings.type,
                contentType: settings.contentType,
                dataType: settings.dataType
            });
        });
        
        $(document).ajaxComplete(function(event, jqXHR, settings) {
            console.log('Solicitud AJAX completada:', {
                url: settings.url,
                status: jqXHR.status,
                statusText: jqXHR.statusText
            });
            
            if (jqXHR.status >= 400) {
                console.error('Error en la solicitud AJAX:', {
                    url: settings.url,
                    status: jqXHR.status,
                    responseText: jqXHR.responseText.substring(0, 200) + '...'
                });
            }
        });

        // Verificar si el select de artículos está inicializado correctamente
        const $articulo = $('#articulo');
        if ($articulo.length) {
            console.log("Select de artículos encontrado en el DOM");
            console.log("Select2 inicializado:", !!$articulo.data('select2'));

            // Intentar cargar opciones manualmente si no hay opciones
            if ($articulo.find('option').length === 0 && window.jirehVentaConfig.articulos) {
                console.log("No hay opciones en el select, intentando cargar manualmente");
                window.jirehVentaConfig.articulos.forEach(function(item) {
                    const option = new Option(item.text, item.id, false, false);
                    $articulo.append(option);
                });
                $articulo.trigger('change');
            }
        } else {
            console.log("Select de artículos NO encontrado en el DOM");
        }
    }

    // Debug para el método PUT
    const $form = $('#forma-editar-venta');
    if ($form.length) {
        console.log("Formulario encontrado:", $form.attr('id'));
        console.log("Método del formulario:", $form.attr('method'));
        console.log("Action del formulario:", $form.attr('action'));
        console.log("Token CSRF en form:", !!$form.find('input[name="_token"]').val());
        console.log("Method override en form:", $form.find('input[name="_method"]').val());

        // Ver si hay algún handler de submit
        const events = $._data($form[0], "events");
        console.log("Hay eventos de submit en el formulario:", !!events && !!events.submit);
    } else {
        console.log("Formulario NO encontrado en el DOM");
    }
});
