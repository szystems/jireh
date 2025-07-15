// Script para prevenir la selección automática de artículos
// Versión simplificada que no interfiere con el cálculo de totales
$(document).ready(function() {
    console.log("🚫 PREVENT-AUTO-SELECTION: Script iniciado (versión simplificada)");

    // Elementos principales
    const $articuloSelect = $('#articulo');
    const $trabajadoresContainer = $('#trabajadores-carwash-container');

    // Función simple para prevenir selección automática
    function prevenirSeleccionAutomatica() {
        if (!$articuloSelect.length) {
            console.warn("🚫 PREVENT-AUTO-SELECTION: No se encontró el select de artículos");
            return;
        }

        // Solo resetear si hay algo seleccionado automáticamente
        const valorActual = $articuloSelect.val();
        if (valorActual) {
            console.log("🚫 PREVENT-AUTO-SELECTION: Removiendo selección automática:", valorActual);
            $articuloSelect.val(''); // Sin triggear eventos
        }

        // Ocultar contenedor de trabajadores
        if ($trabajadoresContainer.length && $trabajadoresContainer.is(':visible')) {
            $trabajadoresContainer.hide();
            console.log("✅ PREVENT-AUTO-SELECTION: Contenedor de trabajadores ocultado");
        }

        console.log("✅ PREVENT-AUTO-SELECTION: Prevención completada");
    }

    // Ejecutar una sola vez al cargar
    prevenirSeleccionAutomatica();

    console.log("✅ PREVENT-AUTO-SELECTION: Configuración completada");
});
