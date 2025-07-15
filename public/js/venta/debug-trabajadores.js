// Script para depurar el comportamiento de la selección de trabajadores
document.addEventListener('DOMContentLoaded', function() {
    // Esperar un poco para asegurarse de que todos los scripts se han cargado
    setTimeout(() => {
        console.log("DEBUG TRABAJADORES CARGADO");

        // Verificar la existencia del contenedor de trabajadores
        const $trabajadoresContainer = $('#trabajadores-carwash-container');
        console.log("Contenedor de trabajadores encontrado:", $trabajadoresContainer.length > 0);
        if ($trabajadoresContainer.length) {
            console.log("Estado inicial (display):", $trabajadoresContainer.css('display'));
        }

        // Verificar los atributos data de las opciones
        console.log("VERIFICANDO ATRIBUTOS DATA DE LAS OPCIONES:");
        const $articuloSelect = $('#articulo');
        const options = $articuloSelect.find('option');
        console.log(`Total de opciones: ${options.length}`);

        // Imprimir información sobre algunas opciones
        let serviciosCount = 0;
        let productosCount = 0;

        options.each(function(index) {
            const $option = $(this);
            const tipo = $option.data('tipo');

            if (tipo === 'servicio') {
                serviciosCount++;
            } else if (tipo) {
                productosCount++;
            }

            // Solo mostrar hasta 5 de cada tipo para evitar spam en la consola
            if ((tipo === 'servicio' && serviciosCount <= 5) ||
                (tipo && tipo !== 'servicio' && productosCount <= 5)) {
                console.log(`Opción ${index}: id=${$option.val()}, tipo=${tipo}, texto=${$option.text()}`);
            }
        });

        console.log(`Total servicios: ${serviciosCount}, Total productos: ${productosCount}`);

        // Escuchar los cambios en la selección de artículos
        $('#articulo').on('select2:select', function(e) {
            const selectedData = e.params.data;
            console.log("EVENTO SELECT ARTÍCULO DEPURACIÓN:");
            console.log("- ID:", selectedData.id);
            console.log("- Texto:", selectedData.text);
            console.log("- Tipo:", selectedData.tipo);
            console.log("- es_servicio:", selectedData.es_servicio);
            console.log("- Option data-tipo:", $(selectedData.element).data('tipo'));

            // Verificar el estado del contenedor después de la selección
            setTimeout(() => {
                console.log("Estado del contenedor después de selección:", $trabajadoresContainer.css('display'));
            }, 100);
        });
    }, 1000);
});
