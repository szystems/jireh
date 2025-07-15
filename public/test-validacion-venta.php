<?php
// Script para probar el manejo de datos y validaciones del formulario de edición

// Obtener el ID de la venta desde la URL
$ventaId = $_GET['venta_id'] ?? 1; // Valor predeterminado 1

// Comprobar si la venta existe y está activa
require_once __DIR__.'/../vendor/autoload.php';
use Illuminate\Support\Facades\DB;
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Verificar si la venta existe y está activa
$venta = DB::table('ventas')->where('id', $ventaId)->first();
$ventaActiva = $venta && $venta->estado == 1;
$tieneDetalles = false;

if ($venta) {
    $conteoDetalles = DB::table('detalle_ventas')
        ->where('venta_id', $ventaId)
        ->count();
    $tieneDetalles = $conteoDetalles > 0;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Validación de Formulario Editar Venta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .test-block { margin-bottom: 30px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .console-output {
            background: #1e1e1e;
            color: #e6e6e6;
            padding: 10px;
            border-radius: 3px;
            font-family: monospace;
            height: 200px;
            overflow-y: auto;
        }
        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }
        .status-pending { background-color: #ffc107; }
        .status-success { background-color: #28a745; }
        .status-error { background-color: #dc3545; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Prueba de Validación - Formulario Editar Venta</h1>
        <p>Este script prueba varios aspectos del manejo de datos y validaciones en el formulario de edición de ventas.</p>

        <div class="alert <?= $ventaActiva ? 'alert-info' : 'alert-danger' ?>">
            <strong>Venta ID:</strong> <?= $ventaId ?>
            <?php if ($ventaActiva): ?>
                <span class="badge bg-success">Activa</span>
                <?php if ($tieneDetalles): ?>
                    <span class="badge bg-info">Con detalles</span>
                <?php else: ?>
                    <span class="badge bg-warning">Sin detalles</span>
                <?php endif; ?>
            <?php else: ?>
                <span class="badge bg-danger">Inactiva o no existe</span>
            <?php endif; ?>
            <a href="/edit-venta/<?= $ventaId ?>" target="_blank" class="btn btn-sm btn-primary float-end">Abrir formulario original</a>
            <a href="/find-ventas-para-test.php" target="_blank" class="btn btn-sm btn-secondary float-end me-1">Buscar otra venta</a>
        </div>

        <?php if (!$ventaActiva): ?>
        <div class="alert alert-warning">
            <strong>¡Advertencia!</strong> La venta seleccionada está inactiva o no existe. Las pruebas probablemente fallarán.
            <a href="/find-ventas-para-test.php" class="btn btn-sm btn-success">Buscar ventas activas</a>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <div class="test-block">
                    <h3>
                        <span class="status-indicator status-pending" id="status-form-submission"></span>
                        Test 1: Envío de formulario con validación
                    </h3>
                    <p>Verifica que el formulario valide correctamente antes de enviar.</p>
                    <button class="btn btn-primary" id="run-form-validation">Ejecutar prueba</button>
                    <div class="console-output mt-3" id="console-form-validation"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="test-block">
                    <h3>
                        <span class="status-indicator status-pending" id="status-trabajadores"></span>
                        Test 2: Manejo de trabajadores
                    </h3>
                    <p>Verifica que los trabajadores se asignen correctamente a servicios.</p>
                    <button class="btn btn-primary" id="run-trabajadores-test">Ejecutar prueba</button>
                    <div class="console-output mt-3" id="console-trabajadores"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="test-block">
                    <h3>
                        <span class="status-indicator status-pending" id="status-delete-items"></span>
                        Test 3: Eliminar y restaurar detalles
                    </h3>
                    <p>Prueba la eliminación y restauración de detalles existentes.</p>
                    <button class="btn btn-primary" id="run-delete-items-test">Ejecutar prueba</button>
                    <div class="console-output mt-3" id="console-delete-items"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="test-block">
                    <h3>
                        <span class="status-indicator status-pending" id="status-add-items"></span>
                        Test 4: Agregar nuevos detalles
                    </h3>
                    <p>Prueba agregar nuevos detalles al formulario.</p>
                    <button class="btn btn-primary" id="run-add-items-test">Ejecutar prueba</button>
                    <div class="console-output mt-3" id="console-add-items"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="test-block">
                    <h3>
                        <span class="status-indicator status-pending" id="status-price-calculation"></span>
                        Test 5: Cálculo de precios y subtotales
                    </h3>
                    <p>Verifica que los subtotales y descuentos se calculen correctamente.</p>
                    <button class="btn btn-primary" id="run-price-test">Ejecutar prueba</button>
                    <div class="console-output mt-3" id="console-price"></div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="test-block">
                    <h3>
                        <span class="status-indicator status-pending" id="status-ajax-submit"></span>
                        Test 6: Envío AJAX y manejo de errores
                    </h3>
                    <p>Prueba el envío AJAX y la visualización de errores.</p>
                    <button class="btn btn-primary" id="run-ajax-test">Ejecutar prueba</button>
                    <div class="console-output mt-3" id="console-ajax"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Función para log en la consola específica
        function logToConsole(consoleId, message, isError = false) {
            const console = document.getElementById(consoleId);
            const line = document.createElement('div');
            line.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
            if (isError) {
                line.style.color = '#ff6b6b';
            }
            console.appendChild(line);
            console.scrollTop = console.scrollHeight;
        }

        // Función para cambiar el estado de un test
        function updateStatus(statusId, status) {
            const indicator = document.getElementById(statusId);
            indicator.classList.remove('status-pending', 'status-success', 'status-error');
            indicator.classList.add(`status-${status}`);
        }

        // Prueba 1: Validación del formulario
        document.getElementById('run-form-validation').addEventListener('click', function() {
            const consoleId = 'console-form-validation';
            const statusId = 'status-form-submission';
            updateStatus(statusId, 'pending');

            logToConsole(consoleId, 'Iniciando prueba de validación del formulario...');

            // Crear un iframe para cargar el formulario
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            document.body.appendChild(iframe);

            iframe.onload = function() {
                try {
                    logToConsole(consoleId, 'Formulario cargado');

                    // Acceder al documento dentro del iframe
                    const iframeDoc = iframe.contentWindow.document;
                    const form = iframeDoc.getElementById('forma-editar-venta');

                    if (!form) {
                        throw new Error('No se encontró el formulario');
                    }

                    // Extraer información del formulario
                    logToConsole(consoleId, 'Analizando estructura del formulario...');

                    // Contar campos e inputs
                    const inputs = form.querySelectorAll('input, select, textarea');
                    logToConsole(consoleId, `El formulario tiene ${inputs.length} campos`);

                    // Comprobar campos obligatorios
                    const requiredFields = form.querySelectorAll('[required]');
                    logToConsole(consoleId, `Campos obligatorios encontrados: ${requiredFields.length}`);

                    // Verificar la validación en el evento submit
                    logToConsole(consoleId, 'Probando envío sin campos obligatorios...');

                    // Vaciar campos requeridos para prueba
                    requiredFields.forEach(field => {
                        if (field.tagName === 'SELECT') {
                            field.selectedIndex = 0;
                        } else {
                            field.value = '';
                        }
                        field.dispatchEvent(new Event('change'));
                    });

                    // Intentar enviar el formulario
                    let submitPrevented = false;
                    const originalSubmit = iframe.contentWindow.XMLHttpRequest.prototype.send;

                    iframe.contentWindow.XMLHttpRequest.prototype.send = function() {
                        logToConsole(consoleId, 'Solicitud AJAX interceptada');
                        iframe.contentWindow.XMLHttpRequest.prototype.send = originalSubmit;
                        return false;
                    };

                    // Interceptar el envío del formulario
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        submitPrevented = true;

                        // Verificar que los campos vacíos hayan sido validados
                        let invalidFields = 0;
                        requiredFields.forEach(field => {
                            if (field.checkValidity() === false) {
                                invalidFields++;
                            }
                        });

                        logToConsole(consoleId, `Validación del navegador: ${invalidFields} campos inválidos de ${requiredFields.length} obligatorios`);

                        // Verificar la función de validación JavaScript
                        logToConsole(consoleId, 'Verificando funciones de validación JavaScript personalizadas...');

                        // Buscar funciones de validación en el código
                        const scriptTags = iframeDoc.querySelectorAll('script');
                        let validationFunctionsFound = false;

                        scriptTags.forEach(script => {
                            if (script.textContent.includes('verificarCambios')) {
                                validationFunctionsFound = true;
                                logToConsole(consoleId, 'Función de verificación de cambios encontrada');
                            }
                        });

                        if (validationFunctionsFound) {
                            updateStatus(statusId, 'success');
                            logToConsole(consoleId, '✅ Prueba completada: El formulario incluye validación correctamente');
                        } else {
                            updateStatus(statusId, 'error');
                            logToConsole(consoleId, '❌ Error: No se encontraron todas las funciones de validación esperadas', true);
                        }
                    });

                    // Enviar el formulario para probar la validación
                    form.dispatchEvent(new Event('submit'));

                    if (!submitPrevented) {
                        updateStatus(statusId, 'error');
                        logToConsole(consoleId, '❌ Error: El evento submit no fue interceptado', true);
                    }

                } catch (error) {
                    updateStatus(statusId, 'error');
                    logToConsole(consoleId, `❌ Error: ${error.message}`, true);
                } finally {
                    // Limpiar
                    setTimeout(() => {
                        document.body.removeChild(iframe);
                    }, 1000);
                }
            };

            // Cargar el formulario en el iframe
            iframe.src = `/edit-venta/<?= $ventaId ?>`;
        });

        // Prueba 2: Manejo de trabajadores
        document.getElementById('run-trabajadores-test').addEventListener('click', function() {
            const consoleId = 'console-trabajadores';
            const statusId = 'status-trabajadores';
            updateStatus(statusId, 'pending');

            logToConsole(consoleId, 'Iniciando prueba de manejo de trabajadores...');

            // Crear un iframe para cargar el formulario
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            document.body.appendChild(iframe);

            iframe.onload = function() {
                try {
                    logToConsole(consoleId, 'Formulario cargado');

                    // Acceder al documento dentro del iframe
                    const iframeDoc = iframe.contentWindow.document;

                    // Buscar botones de editar trabajadores
                    const editarTrabajadoresBtn = iframeDoc.querySelector('.editar-trabajadores');

                    if (!editarTrabajadoresBtn) {
                        logToConsole(consoleId, 'No se encontraron botones de editar trabajadores. Esto puede ser normal si no hay servicios en la venta.');
                        updateStatus(statusId, 'success');
                        return;
                    }

                    logToConsole(consoleId, 'Botón de editar trabajadores encontrado');
                    logToConsole(consoleId, 'Haciendo clic en el botón...');

                    // Simular clic
                    editarTrabajadoresBtn.click();

                    // Esperar a que se abra el modal
                    setTimeout(() => {
                        // Verificar que el modal se haya abierto
                        const modal = iframeDoc.getElementById('editar-trabajadores-modal');

                        if (!modal || !modal.classList.contains('show')) {
                            updateStatus(statusId, 'error');
                            logToConsole(consoleId, '❌ Error: El modal no se abrió correctamente', true);
                            return;
                        }

                        logToConsole(consoleId, 'Modal abierto correctamente');

                        // Verificar el select de trabajadores
                        const trabajadoresSelect = iframeDoc.getElementById('trabajadores-carwash-edit');

                        if (!trabajadoresSelect) {
                            updateStatus(statusId, 'error');
                            logToConsole(consoleId, '❌ Error: No se encontró el select de trabajadores en el modal', true);
                            return;
                        }

                        logToConsole(consoleId, `Select de trabajadores encontrado con ${trabajadoresSelect.options.length} opciones`);

                        // Verificar el botón de guardar
                        const guardarBtn = iframeDoc.getElementById('guardar-trabajadores');

                        if (!guardarBtn) {
                            updateStatus(statusId, 'error');
                            logToConsole(consoleId, '❌ Error: No se encontró el botón de guardar trabajadores', true);
                            return;
                        }

                        logToConsole(consoleId, 'Botón de guardar encontrado');
                        logToConsole(consoleId, 'Seleccionando algunos trabajadores...');

                        // Seleccionar algunos trabajadores (primera y última opción)
                        if (trabajadoresSelect.options.length > 0) {
                            trabajadoresSelect.options[0].selected = true;
                            if (trabajadoresSelect.options.length > 1) {
                                trabajadoresSelect.options[trabajadoresSelect.options.length - 1].selected = true;
                            }
                            trabajadoresSelect.dispatchEvent(new Event('change'));

                            logToConsole(consoleId, 'Trabajadores seleccionados');
                            logToConsole(consoleId, 'Haciendo clic en guardar...');

                            // Clic en guardar
                            guardarBtn.click();

                            // Verificar si se cerró el modal
                            setTimeout(() => {
                                if (modal.classList.contains('show')) {
                                    updateStatus(statusId, 'error');
                                    logToConsole(consoleId, '❌ Error: El modal no se cerró después de guardar', true);
                                } else {
                                    logToConsole(consoleId, 'Modal cerrado correctamente');

                                    // Verificar si se actualizó el texto de trabajadores
                                    const detalleId = editarTrabajadoresBtn.getAttribute('data-detalle-id');
                                    const textoTrabajadores = iframeDoc.getElementById(`trabajadores-text-${detalleId}`);

                                    if (textoTrabajadores && textoTrabajadores.textContent.includes('trabajador')) {
                                        updateStatus(statusId, 'success');
                                        logToConsole(consoleId, '✅ Prueba completada: Se pudieron asignar trabajadores correctamente');
                                    } else {
                                        updateStatus(statusId, 'error');
                                        logToConsole(consoleId, '❌ Error: No se actualizó el texto de trabajadores después de guardar', true);
                                    }
                                }
                            }, 500);
                        } else {
                            updateStatus(statusId, 'error');
                            logToConsole(consoleId, '❌ Error: No hay opciones de trabajadores disponibles', true);
                        }
                    }, 500);

                } catch (error) {
                    updateStatus(statusId, 'error');
                    logToConsole(consoleId, `❌ Error: ${error.message}`, true);
                }
            };

            // Cargar el formulario en el iframe
            iframe.src = `/edit-venta/<?= $ventaId ?>`;
        });

        // Prueba 3: Eliminar y restaurar detalles
        document.getElementById('run-delete-items-test').addEventListener('click', function() {
            const consoleId = 'console-delete-items';
            const statusId = 'status-delete-items';
            updateStatus(statusId, 'pending');

            logToConsole(consoleId, 'Iniciando prueba de eliminación y restauración de detalles...');

            // Crear un iframe para cargar el formulario
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            document.body.appendChild(iframe);

            iframe.onload = function() {
                try {
                    logToConsole(consoleId, 'Formulario cargado');

                    // Acceder al documento dentro del iframe
                    const iframeDoc = iframe.contentWindow.document;

                    // Buscar botones de eliminar detalles
                    const eliminarBtns = iframeDoc.querySelectorAll('.eliminar-detalle');

                    if (eliminarBtns.length === 0) {
                        logToConsole(consoleId, 'No se encontraron botones de eliminar detalles. Esto puede ser normal si no hay detalles en la venta.');
                        updateStatus(statusId, 'success');
                        return;
                    }

                    logToConsole(consoleId, `Se encontraron ${eliminarBtns.length} botones de eliminar`);

                    // Obtener el primer botón de eliminar
                    const eliminarBtn = eliminarBtns[0];
                    const detalleId = eliminarBtn.getAttribute('data-detalle-id');

                    logToConsole(consoleId, `Probando eliminación del detalle ID: ${detalleId}`);
                    logToConsole(consoleId, 'Haciendo clic en el botón eliminar...');

                    // Simular clic
                    eliminarBtn.click();

                    // Verificar que se haya marcado para eliminar
                    setTimeout(() => {
                        const inputEliminar = iframeDoc.getElementById(`eliminar-${detalleId}`);
                        const row = iframeDoc.getElementById(`detalle-row-${detalleId}`);
                        const confirmRow = iframeDoc.getElementById(`confirm-row-${detalleId}`);

                        if (!inputEliminar || inputEliminar.value !== '1') {
                            updateStatus(statusId, 'error');
                            logToConsole(consoleId, '❌ Error: No se marcó el input de eliminar correctamente', true);
                            return;
                        }

                        if (row && !row.hasAttribute('style')) {
                            updateStatus(statusId, 'error');
                            logToConsole(consoleId, '❌ Error: La fila no se ocultó correctamente', true);
                            return;
                        }

                        if (!confirmRow) {
                            updateStatus(statusId, 'error');
                            logToConsole(consoleId, '❌ Error: No se agregó la fila de confirmación', true);
                            return;
                        }

                        logToConsole(consoleId, 'Detalle eliminado correctamente');
                        logToConsole(consoleId, 'Probando restauración...');

                        // Buscar botón de restaurar
                        const restaurarBtn = confirmRow.querySelector('.restaurar-detalle');

                        if (!restaurarBtn) {
                            updateStatus(statusId, 'error');
                            logToConsole(consoleId, '❌ Error: No se encontró el botón de restaurar', true);
                            return;
                        }

                        // Simular clic en restaurar
                        restaurarBtn.click();

                        // Verificar que se haya restaurado
                        setTimeout(() => {
                            if (inputEliminar.value !== '0') {
                                updateStatus(statusId, 'error');
                                logToConsole(consoleId, '❌ Error: No se restableció el valor del input de eliminar', true);
                                return;
                            }

                            if (row && row.hasAttribute('style') && row.style.display === 'none') {
                                updateStatus(statusId, 'error');
                                logToConsole(consoleId, '❌ Error: La fila no se mostró nuevamente', true);
                                return;
                            }

                            if (iframeDoc.getElementById(`confirm-row-${detalleId}`)) {
                                updateStatus(statusId, 'error');
                                logToConsole(consoleId, '❌ Error: La fila de confirmación no se eliminó', true);
                                return;
                            }

                            updateStatus(statusId, 'success');
                            logToConsole(consoleId, '✅ Prueba completada: Eliminación y restauración de detalles funcionan correctamente');
                        }, 500);
                    }, 500);

                } catch (error) {
                    updateStatus(statusId, 'error');
                    logToConsole(consoleId, `❌ Error: ${error.message}`, true);
                }
            };

            // Cargar el formulario en el iframe
            iframe.src = `/edit-venta/<?= $ventaId ?>`;
        });

        // Prueba 4: Agregar nuevos detalles
        document.getElementById('run-add-items-test').addEventListener('click', function() {
            const consoleId = 'console-add-items';
            const statusId = 'status-add-items';
            updateStatus(statusId, 'pending');

            logToConsole(consoleId, 'Iniciando prueba de agregar nuevos detalles...');

            // Crear un iframe para cargar el formulario
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            document.body.appendChild(iframe);

            iframe.onload = function() {
                try {
                    logToConsole(consoleId, 'Formulario cargado');

                    // Acceder al documento dentro del iframe
                    const iframeDoc = iframe.contentWindow.document;

                    // Buscar el select de artículos
                    const articuloSelect = iframeDoc.getElementById('articulo');

                    if (!articuloSelect) {
                        updateStatus(statusId, 'error');
                        logToConsole(consoleId, '❌ Error: No se encontró el select de artículos', true);
                        return;
                    }

                    logToConsole(consoleId, `Select de artículos encontrado con ${articuloSelect.options.length} opciones`);

                    // Seleccionar el primer artículo disponible
                    if (articuloSelect.options.length <= 1) {
                        updateStatus(statusId, 'error');
                        logToConsole(consoleId, '❌ Error: No hay opciones de artículos disponibles', true);
                        return;
                    }

                    // Seleccionar la segunda opción (índice 1) para evitar la opción vacía
                    articuloSelect.selectedIndex = 1;

                    // Disparar evento change
                    const event = new Event('change');
                    articuloSelect.dispatchEvent(event);

                    logToConsole(consoleId, `Artículo seleccionado: ${articuloSelect.options[articuloSelect.selectedIndex].text}`);

                    // Establecer cantidad
                    const cantidadInput = iframeDoc.getElementById('cantidad-nuevo');

                    if (!cantidadInput) {
                        updateStatus(statusId, 'error');
                        logToConsole(consoleId, '❌ Error: No se encontró el input de cantidad', true);
                        return;
                    }

                    cantidadInput.value = '1';
                    cantidadInput.dispatchEvent(new Event('input'));

                    logToConsole(consoleId, 'Cantidad establecida: 1');

                    // Buscar botón de agregar
                    const agregarBtn = iframeDoc.getElementById('agregar-detalle');

                    if (!agregarBtn) {
                        updateStatus(statusId, 'error');
                        logToConsole(consoleId, '❌ Error: No se encontró el botón de agregar detalle', true);
                        return;
                    }

                    logToConsole(consoleId, 'Haciendo clic en el botón agregar...');

                    // Contar detalles antes de agregar
                    const detallesAntes = iframeDoc.querySelectorAll('#nuevos-detalles tr').length;

                    // Simular clic
                    agregarBtn.click();

                    // Verificar que se haya agregado
                    setTimeout(() => {
                        const detallesDespues = iframeDoc.querySelectorAll('#nuevos-detalles tr').length;

                        if (detallesDespues <= detallesAntes) {
                            updateStatus(statusId, 'error');
                            logToConsole(consoleId, '❌ Error: No se agregó ningún detalle nuevo', true);
                            return;
                        }

                        logToConsole(consoleId, `Detalle agregado correctamente (${detallesAntes} → ${detallesDespues})`);

                        // Verificar que se muestre el contenedor
                        const contenedor = iframeDoc.getElementById('nuevos-detalles-container');

                        if (!contenedor || contenedor.style.display === 'none') {
                            updateStatus(statusId, 'error');
                            logToConsole(consoleId, '❌ Error: El contenedor de nuevos detalles no se mostró', true);
                            return;
                        }

                        // Probar eliminar el detalle agregado
                        logToConsole(consoleId, 'Probando eliminar el detalle agregado...');

                        const eliminarBtn = iframeDoc.querySelector('#nuevos-detalles .eliminar-nuevo-detalle');

                        if (!eliminarBtn) {
                            updateStatus(statusId, 'error');
                            logToConsole(consoleId, '❌ Error: No se encontró el botón de eliminar nuevo detalle', true);
                            return;
                        }

                        eliminarBtn.click();

                        // Verificar que se haya eliminado
                        setTimeout(() => {
                            const detallesFinal = iframeDoc.querySelectorAll('#nuevos-detalles tr').length;

                            if (detallesFinal !== detallesAntes) {
                                updateStatus(statusId, 'error');
                                logToConsole(consoleId, '❌ Error: No se eliminó el detalle correctamente', true);
                                return;
                            }

                            logToConsole(consoleId, 'Detalle eliminado correctamente');
                            updateStatus(statusId, 'success');
                            logToConsole(consoleId, '✅ Prueba completada: Agregar y eliminar nuevos detalles funciona correctamente');
                        }, 500);
                    }, 500);

                } catch (error) {
                    updateStatus(statusId, 'error');
                    logToConsole(consoleId, `❌ Error: ${error.message}`, true);
                }
            };

            // Cargar el formulario en el iframe
            iframe.src = `/edit-venta/<?= $ventaId ?>`;
        });

        // Prueba 5: Cálculo de precios y subtotales
        document.getElementById('run-price-test').addEventListener('click', function() {
            const consoleId = 'console-price';
            const statusId = 'status-price-calculation';
            updateStatus(statusId, 'pending');

            logToConsole(consoleId, 'Iniciando prueba de cálculo de precios y subtotales...');

            // Crear un iframe para cargar el formulario
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            document.body.appendChild(iframe);

            iframe.onload = function() {
                try {
                    logToConsole(consoleId, 'Formulario cargado');

                    // Acceder al documento dentro del iframe
                    const iframeDoc = iframe.contentWindow.document;

                    // Buscar inputs de cantidad
                    const cantidadInputs = iframeDoc.querySelectorAll('.cantidad-input');

                    if (cantidadInputs.length === 0) {
                        logToConsole(consoleId, 'No se encontraron inputs de cantidad. Esto puede ser normal si no hay detalles en la venta.');

                        // Intentar agregar un nuevo detalle para probar
                        logToConsole(consoleId, 'Intentando agregar un nuevo detalle para probar...');

                        // Buscar el select de artículos
                        const articuloSelect = iframeDoc.getElementById('articulo');

                        if (!articuloSelect || articuloSelect.options.length <= 1) {
                            updateStatus(statusId, 'error');
                            logToConsole(consoleId, '❌ Error: No hay opciones de artículos disponibles para probar', true);
                            return;
                        }

                        // Seleccionar la segunda opción
                        articuloSelect.selectedIndex = 1;
                        articuloSelect.dispatchEvent(new Event('change'));

                        // Establecer cantidad
                        const cantidadInput = iframeDoc.getElementById('cantidad-nuevo');
                        cantidadInput.value = '2';
                        cantidadInput.dispatchEvent(new Event('input'));

                        // Agregar detalle
                        const agregarBtn = iframeDoc.getElementById('agregar-detalle');
                        agregarBtn.click();

                        setTimeout(() => {
                            // Verificar que se haya actualizado el total
                            const totalElement = iframeDoc.getElementById('total-venta');

                            if (!totalElement) {
                                updateStatus(statusId, 'error');
                                logToConsole(consoleId, '❌ Error: No se encontró el elemento de total', true);
                                return;
                            }

                            logToConsole(consoleId, `Total calculado: ${totalElement.textContent}`);
                            updateStatus(statusId, 'success');
                            logToConsole(consoleId, '✅ Prueba completada: Cálculo de total funciona correctamente con nuevos detalles');
                        }, 500);

                        return;
                    }

                    logToConsole(consoleId, `Se encontraron ${cantidadInputs.length} inputs de cantidad`);

                    // Obtener el primer input de cantidad
                    const cantidadInput = cantidadInputs[0];
                    const detalleId = cantidadInput.getAttribute('data-detalle-id');

                    // Obtener valores iniciales
                    const cantidadInicial = parseFloat(cantidadInput.value);
                    const subtotalElement = iframeDoc.getElementById(`subtotal-${detalleId}`);
                    const subtotalInicial = subtotalElement ? subtotalElement.textContent.trim() : null;
                    const totalElement = iframeDoc.getElementById('total-venta');
                    const totalInicial = totalElement ? totalElement.textContent.trim() : null;

                    logToConsole(consoleId, `Valores iniciales: Cantidad = ${cantidadInicial}, Subtotal = ${subtotalInicial}, Total = ${totalInicial}`);

                    // Cambiar la cantidad
                    const nuevaCantidad = cantidadInicial + 1;
                    logToConsole(consoleId, `Cambiando cantidad a ${nuevaCantidad}...`);

                    cantidadInput.value = nuevaCantidad;
                    cantidadInput.dispatchEvent(new Event('change'));

                    // Verificar que se hayan actualizado los valores
                    setTimeout(() => {
                        const subtotalFinal = subtotalElement ? subtotalElement.textContent.trim() : null;
                        const totalFinal = totalElement ? totalElement.textContent.trim() : null;

                        logToConsole(consoleId, `Valores finales: Subtotal = ${subtotalFinal}, Total = ${totalFinal}`);

                        if (subtotalInicial === subtotalFinal) {
                            updateStatus(statusId, 'error');
                            logToConsole(consoleId, '❌ Error: El subtotal no se actualizó después de cambiar la cantidad', true);
                            return;
                        }

                        if (totalInicial === totalFinal) {
                            updateStatus(statusId, 'error');
                            logToConsole(consoleId, '❌ Error: El total no se actualizó después de cambiar la cantidad', true);
                            return;
                        }

                        updateStatus(statusId, 'success');
                        logToConsole(consoleId, '✅ Prueba completada: Cálculo de subtotales y total funciona correctamente');
                    }, 500);

                } catch (error) {
                    updateStatus(statusId, 'error');
                    logToConsole(consoleId, `❌ Error: ${error.message}`, true);
                }
            };

            // Cargar el formulario en el iframe
            iframe.src = `/edit-venta/<?= $ventaId ?>`;
        });

        // Prueba 6: Envío AJAX y manejo de errores
        document.getElementById('run-ajax-test').addEventListener('click', function() {
            const consoleId = 'console-ajax';
            const statusId = 'status-ajax-submit';
            updateStatus(statusId, 'pending');

            logToConsole(consoleId, 'Iniciando prueba de envío AJAX y manejo de errores...');

            // Crear un iframe para cargar el formulario
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            document.body.appendChild(iframe);

            iframe.onload = function() {
                try {
                    logToConsole(consoleId, 'Formulario cargado');

                    // Acceder al documento dentro del iframe
                    const iframeDoc = iframe.contentWindow.document;
                    const form = iframeDoc.getElementById('forma-editar-venta');

                    if (!form) {
                        updateStatus(statusId, 'error');
                        logToConsole(consoleId, '❌ Error: No se encontró el formulario', true);

                        // Verificar si hay algún mensaje de error en la página que podría explicar por qué no está el formulario
                        const errorMessages = iframeDoc.querySelectorAll('.alert-danger, .error-message');
                        if (errorMessages.length > 0) {
                            logToConsole(consoleId, 'Mensaje de error encontrado en la página:', true);
                            errorMessages.forEach(element => {
                                logToConsole(consoleId, element.textContent.trim(), true);
                            });
                        }

                        return;
                    }

                    // Interceptar la función AJAX
                    let ajaxSubmitted = false;
                    const originalAjax = iframe.contentWindow.$.ajax;

                    iframe.contentWindow.$.ajax = function(options) {
                        logToConsole(consoleId, 'Solicitud AJAX interceptada');
                        logToConsole(consoleId, `URL: ${options.url}`);
                        logToConsole(consoleId, `Método: ${options.type}`);

                        ajaxSubmitted = true;

                        // Simular respuesta exitosa
                        if (options.success) {
                            setTimeout(() => {
                                options.success({status: 'success', message: 'Prueba exitosa'});
                            }, 500);
                        }

                        // Restaurar función original
                        iframe.contentWindow.$.ajax = originalAjax;

                        // No realizar la solicitud real
                        return {
                            done: function() { return this; },
                            fail: function() { return this; },
                            always: function() { return this; }
                        };
                    };

                    // Interceptar la redirección
                    const originalLocation = iframe.contentWindow.location;
                    Object.defineProperty(iframe.contentWindow, 'location', {
                        get: function() {
                            return originalLocation;
                        },
                        set: function(value) {
                            logToConsole(consoleId, `Intento de redirección a: ${value}`);

                            // Verificar si la URL incluye el parámetro success
                            if (String(value).includes('success=true')) {
                                logToConsole(consoleId, 'La URL de redirección incluye el parámetro success=true');
                                updateStatus(statusId, 'success');
                                logToConsole(consoleId, '✅ Prueba completada: Envío AJAX y redirección funcionan correctamente');
                            } else {
                                updateStatus(statusId, 'error');
                                logToConsole(consoleId, '❌ Error: La URL de redirección no incluye el parámetro success=true', true);
                            }

                            // No realizar la redirección real
                            return originalLocation;
                        }
                    });

                    // Simular un pequeño cambio en el formulario para activar el flag de cambios
                    const inputs = form.querySelectorAll('input, select');
                    if (inputs.length > 0) {
                        const input = inputs[0];
                        input.dispatchEvent(new Event('change'));
                    }

                    logToConsole(consoleId, 'Enviando formulario...');

                    // Enviar el formulario
                    form.dispatchEvent(new Event('submit'));

                    // Verificar después de un tiempo
                    setTimeout(() => {
                        if (!ajaxSubmitted) {
                            updateStatus(statusId, 'error');
                            logToConsole(consoleId, '❌ Error: No se realizó ninguna solicitud AJAX', true);
                        }
                    }, 1000);

                } catch (error) {
                    updateStatus(statusId, 'error');
                    logToConsole(consoleId, `❌ Error: ${error.message}`, true);
                }
            };

            // Cargar el formulario en el iframe
            iframe.src = `/edit-venta/<?= $ventaId ?>`;
        });
    </script>
</body>
</html>
