// Script para detectar interacciones explícitas del usuario con el select de artículos
$(document).ready(function() {
    console.log("🔍 Iniciando detector de interacciones explícitas");

    // Variable global para rastrear si la selección fue explícita
    window.seleccionArticuloExplicita = false;

    // Referencias a elementos
    const $articuloSelect = $('#articulo');
    if (!$articuloSelect.length) {
        console.warn("No se encontró el select de artículos");
        return;
    }

    // Funciones para detectar interacciones explícitas del usuario
    function marcarInteraccionExplicita() {
        window.seleccionArticuloExplicita = true;
        console.log("✅ Interacción explícita del usuario detectada con el select de artículos");
    }

    // Detectar clicks directos en el select o en su contenedor de Select2
    $articuloSelect.on('mousedown', marcarInteraccionExplicita);
    $(document).on('mousedown', '.select2-container--open .select2-results__option', marcarInteraccionExplicita);

    // Detectar interacción con teclado
    $articuloSelect.on('keydown', marcarInteraccionExplicita);
    $(document).on('keydown', '.select2-search__field', marcarInteraccionExplicita);

    // Detectar cuando el usuario interactúa con el dropdown
    $(document).on('mouseenter', '.select2-results__option', function() {
        window.articuloHoverDetectado = true;
    });

    // Cuando cambia el valor, verificar si fue por interacción o programáticamente
    $articuloSelect.on('change', function(e) {
        const valor = $(this).val();

        if (window.articuloHoverDetectado || window.seleccionArticuloExplicita) {
            console.log(`📝 Selección explícita de artículo: ${valor || 'vacío'}`);
            // Resetear flag de hover para detecciones futuras
            window.articuloHoverDetectado = false;
        } else {
            console.log(`🤖 Selección programática de artículo: ${valor || 'vacío'}`);
        }
    });

    console.log("✅ Detector de interacciones explícitas inicializado");
});
