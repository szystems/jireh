/**
 * Script de debugging integrado para el formulario de edición de ventas
 * Se ejecuta automáticamente cuando se carga la página
 */

(function() {
    'use strict';
    
    console.log('🔍 DEBUGGING INTEGRADO ACTIVADO - Formulario Edit Venta');
    
    // Función para logging con timestamp
    function debugLog(message, data = null) {
        const timestamp = new Date().toLocaleTimeString();
        console.log(`[${timestamp}] 🔍 FORM DEBUG:`, message);
        if (data) {
            console.log('📊 Datos:', data);
        }
    }
    
    // Verificar que estamos en la página correcta
    if (!document.querySelector('#forma-editar-venta')) {
        debugLog('❌ Formulario #forma-editar-venta no encontrado - Script no aplicable');
        return;
    }
    
    debugLog('✅ Formulario encontrado, iniciando debugging...');
    
    // 1. Verificar elementos del formulario
    function verificarElementosFormulario() {
        debugLog('=== VERIFICACIÓN DE ELEMENTOS ===');
        
        const form = document.querySelector('#forma-editar-venta');
        const submitBtn = document.querySelector('#btn-guardar-cambios');
        const method = form?.querySelector('input[name="_method"]');
        const token = form?.querySelector('input[name="_token"]');
        
        debugLog('Formulario:', form ? '✅ Encontrado' : '❌ No encontrado');
        debugLog('Botón submit:', submitBtn ? '✅ Encontrado' : '❌ No encontrado');
        debugLog('Method field:', method ? `✅ Valor: ${method.value}` : '❌ No encontrado');
        debugLog('CSRF Token:', token ? `✅ Valor: ${token.value.substring(0, 10)}...` : '❌ No encontrado');
        
        if (form) {
            debugLog('Action del formulario:', form.action);
            debugLog('Method del formulario:', form.method);
        }
    }
    
    // 2. Interceptar eventos del formulario
    function interceptarEventos() {
        debugLog('=== INTERCEPTANDO EVENTOS ===');
        
        const form = document.querySelector('#forma-editar-venta');
        if (!form) return;
        
        // Interceptar submit
        form.addEventListener('submit', function(e) {
            debugLog('🚨 EVENTO SUBMIT DETECTADO');
            debugLog('Event target:', e.target);
            debugLog('Event type:', e.type);
            debugLog('Default prevented:', e.defaultPrevented);
            
            // Verificar si hay otros listeners
            const listeners = getEventListeners ? getEventListeners(form) : 'No disponible';
            debugLog('Event listeners en el formulario:', listeners);
            
            // Permitir que continue normalmente por ahora
            debugLog('⏳ Permitiendo que el submit continúe...');
        }, true); // Usar capture para ejecutar antes que otros
        
        // Interceptar clicks en el botón
        const submitBtn = document.querySelector('#btn-guardar-cambios');
        if (submitBtn) {
            submitBtn.addEventListener('click', function(e) {
                debugLog('🖱️ CLICK EN BOTÓN SUBMIT DETECTADO');
                debugLog('Button type:', submitBtn.type);
                debugLog('Button disabled:', submitBtn.disabled);
                debugLog('Event prevented:', e.defaultPrevented);
            });
        }
    }
    
    // 3. Monitorear cambios en el DOM
    function monitorearCambios() {
        debugLog('=== MONITOREANDO CAMBIOS EN DOM ===');
        
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1 && node.tagName === 'FORM') {
                            debugLog('📝 Nuevo formulario detectado en DOM');
                        }
                    });
                }
            });
        });
        
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
    
    // 4. Verificar conflictos de JavaScript
    function verificarConflictos() {
        debugLog('=== VERIFICANDO CONFLICTOS ===');
        
        // Verificar jQuery
        if (window.jQuery) {
            debugLog('jQuery versión:', $.fn.jquery);
            
            // Verificar eventos jQuery en el formulario
            const form = $('#forma-editar-venta');
            if (form.length) {
                const events = $._data(form[0], 'events');
                debugLog('Eventos jQuery en formulario:', events);
            }
        }
        
        // Verificar otros frameworks
        debugLog('Bootstrap:', typeof window.bootstrap !== 'undefined' ? '✅' : '❌');
        debugLog('Select2:', typeof window.select2 !== 'undefined' ? '✅' : '❌');
        
        // Verificar errores globales
        window.addEventListener('error', function(e) {
            debugLog('🚨 ERROR JAVASCRIPT GLOBAL:', e.error);
        });
    }
    
    // 5. Test de envío de formulario
    function testEnvioFormulario() {
        debugLog('=== PREPARANDO TEST DE ENVÍO ===');
        
        // Crear función global para test manual
        window.testFormSubmit = function() {
            debugLog('🧪 INICIANDO TEST MANUAL DE ENVÍO');
            
            const form = document.querySelector('#forma-editar-venta');
            if (!form) {
                debugLog('❌ Formulario no encontrado para test');
                return;
            }
            
            // Verificar datos del formulario
            const formData = new FormData(form);
            debugLog('📋 Datos del formulario:');
            for (let [key, value] of formData.entries()) {
                console.log(`  ${key}: ${value}`);
            }
            
            // Simular envío AJAX para ver qué pasa
            debugLog('📤 Simulando envío AJAX...');
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                debugLog('📥 Respuesta recibida:', {
                    status: response.status,
                    statusText: response.statusText,
                    url: response.url
                });
                return response.text();
            })
            .then(data => {
                debugLog('📄 Contenido de respuesta (primeros 200 chars):', data.substring(0, 200));
            })
            .catch(error => {
                debugLog('❌ Error en envío AJAX:', error);
            });
        };
        
        debugLog('✅ Función testFormSubmit() creada. Puedes ejecutarla en consola.');
    }
    
    // Ejecutar todas las verificaciones cuando el DOM esté listo
    function iniciarDebugging() {
        debugLog('🚀 INICIANDO DEBUGGING COMPLETO');
        
        verificarElementosFormulario();
        interceptarEventos();
        monitorearCambios();
        verificarConflictos();
        testEnvioFormulario();
        
        debugLog('✅ Debugging configurado completamente');
        debugLog('💡 Para test manual, ejecuta: testFormSubmit()');
    }
    
    // Ejecutar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', iniciarDebugging);
    } else {
        iniciarDebugging();
    }
    
})();
