$(document).ready(function() {
    // Verificación defensiva para comprobar si la función overlayScrollbars está disponible
    if (typeof $.fn.overlayScrollbars === 'undefined') {
        console.warn('OverlayScrollbars library is not loaded. Skipping scroll customization.');
        return;
    }

    // El resto del código sólo se ejecutará si overlayScrollbars está disponible
    try {
        // Configuración para body-scroll
        $('.body-scroll').overlayScrollbars({
            // opciones existentes...
        });
        
        // Configuración para content-scroll
        $('.content-scroll').overlayScrollbars({
            // opciones existentes...
        });
        
        // Configuración para content-wrapper-scroll
        $('.content-wrapper-scroll').overlayScrollbars({
            // opciones existentes...
        });
        
        // Configuración para dropdown-scroll
        $('.dropdown-scroll .dropdown-menu').overlayScrollbars({
            // opciones existentes...
        });
        
        // Configuración para dropdown-menu-scroll
        $('.dropdown-menu-scroll').overlayScrollbars({
            // opciones existentes...
        });
    } catch (error) {
        console.error('Error initializing OverlayScrollbars:', error);
    }
});
