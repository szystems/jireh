<?php
// Script para simular diferentes tipos de errores y probar el manejo en el sistema

// Redirigir después de X segundos (simulando proceso)
$redirect = isset($_REQUEST['redirect']) ? (int)$_REQUEST['redirect'] : 0;

// Tipo de respuesta a simular
$tipo = $_REQUEST['tipo'] ?? 'json'; // json, html

// Tipo de error a simular
$error = $_REQUEST['error'] ?? 'validation'; // validation, server, no_existe, html, exception

// Código de estado a simular
$codigo = $_REQUEST['codigo'] ?? 500;

// Simulamos la validación recibida desde AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_REQUEST['simular_post'])) {

    // Enviamos header según el tipo de respuesta
    if ($tipo === 'json') {
        header('Content-Type: application/json');
    } else {
        header('Content-Type: text/html');
    }

    // Enviamos el código de estado
    http_response_code((int)$codigo);

    // Generamos la respuesta según el tipo de error
    switch ($error) {
        case 'validation':
            if ($tipo === 'json') {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error de validación',
                    'errors' => [
                        'cliente_id' => ['El campo cliente es obligatorio.'],
                        'vehiculo_id' => ['El campo vehículo es obligatorio.'],
                        'fecha' => ['El campo fecha es obligatorio.']
                    ]
                ]);
            } else {
                echo '<div class="alert alert-danger">Error de validación. Falta cliente, vehículo y fecha</div>';
            }
            break;

        case 'server':
            if ($tipo === 'json') {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error interno del servidor',
                ]);
            } else {
                echo '<h1>Error 500</h1><p>Error interno del servidor</p>';
            }
            break;

        case 'no_existe':
            http_response_code(404);
            if ($tipo === 'json') {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'No existe el recurso solicitado',
                ]);
            } else {
                echo '<h1>Error 404</h1><p>No existe el recurso solicitado</p>';
            }
            break;

        case 'html':
            // Devolver un HTML completo como si fuera una página de error
            echo '<!DOCTYPE html>
                <html>
                <head>
                    <title>Error Inesperado</title>
                    <style>body{font-family:sans-serif;}</style>
                </head>
                <body>
                    <h1>Error inesperado</h1>
                    <div class="alert alert-danger">
                        <p>Ha ocurrido un error en el servidor.</p>
                        <p>DEBUG: Call to undefined method App\Models\Venta::undefined_method() in VentaController.php:127</p>
                    </div>
                    <div>
                        <p>Stack trace:</p>
                        <pre>
#0 VentaController.php(45): App\Http\Controllers\Admin\VentaController->procesarDetalles()
#1 [internal function]: App\Http\Controllers\Admin\VentaController->update()
#2 vendor/laravel/framework/src/Illuminate/Routing/Controller.php(54): call_user_func_array()
                        </pre>
                    </div>
                </body>
                </html>';
            break;

        case 'exception':
            // Simular una excepción de PHP
            throw new Exception('Excepción simulada para pruebas');
            break;
    }

    // Si hay que redirigir, esperar primero
    if ($redirect > 0) {
        sleep($redirect);
        header('Location: /find-ventas-para-test.php');
        exit;
    }

    exit;
}

// Si no es POST, mostrar un formulario para probar los diferentes tipos de error
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulador de Errores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Simulador de Errores para Pruebas</h1>
        <p>Esta herramienta permite simular diferentes tipos de errores para probar el manejo en el sistema.</p>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Configurar Simulación de Error</h5>
            </div>
            <div class="card-body">
                <form method="get" action="error-simulador.php" id="simulacion-form">
                    <input type="hidden" name="simular_post" value="1">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Respuesta:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo" id="tipo-json" value="json" checked>
                                <label class="form-check-label" for="tipo-json">JSON (para AJAX)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo" id="tipo-html" value="html">
                                <label class="form-check-label" for="tipo-html">HTML (para redirecciones)</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Código de Estado:</label>
                            <select class="form-control" name="codigo">
                                <option value="400">400 - Bad Request</option>
                                <option value="401">401 - Unauthorized</option>
                                <option value="403">403 - Forbidden</option>
                                <option value="404">404 - Not Found</option>
                                <option value="422">422 - Unprocessable Entity</option>
                                <option value="500" selected>500 - Internal Server Error</option>
                                <option value="503">503 - Service Unavailable</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipo de Error:</label>
                        <select class="form-control" name="error">
                            <option value="validation">Validación (errores en campos)</option>
                            <option value="server">Error de Servidor</option>
                            <option value="no_existe">Recurso No Existe (404)</option>
                            <option value="html">HTML de Error (página)</option>
                            <option value="exception">Excepción PHP</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Redirigir después de (segundos):</label>
                        <input type="number" class="form-control" name="redirect" value="0" min="0" max="10">
                        <div class="form-text">0 para no redirigir</div>
                    </div>

                    <button type="submit" class="btn btn-primary">Simular Error</button>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">Probar con AJAX</h5>
            </div>
            <div class="card-body">
                <p>Prueba una llamada AJAX con los parámetros configurados arriba:</p>
                <button id="test-ajax" class="btn btn-info">Probar con AJAX</button>
                <div class="alert alert-light mt-3" id="ajax-result">
                    <code>Los resultados se mostrarán aquí...</code>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">URLs para Pruebas en Formulario</h5>
            </div>
            <div class="card-body">
                <p>Para probar directamente en el formulario de ventas, puedes modificar la URL de destino del formulario:</p>

                <div id="url-examples" class="border p-3 bg-light mb-3">
                    <!-- Se rellena con JavaScript -->
                </div>

                <p><strong>Nota:</strong> Estas pruebas son para simular errores en desarrollo. No usar en producción.</p>
            </div>
        </div>
    </div>

    <script>
        // Generar ejemplos de URL en tiempo real
        function updateUrlExamples() {
            const form = document.getElementById('simulacion-form');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();

            document.getElementById('url-examples').innerHTML = `
                <p><strong>URL para probar:</strong><br>
                <code>${window.location.origin}/error-simulador.php?${params}</code></p>

                <p><strong>Código para modificar destino del formulario:</strong><br>
                <code>document.getElementById('forma-editar-venta').action = '${window.location.origin}/error-simulador.php?${params}';</code></p>
            `;
        }

        // Actualizar ejemplos al cargar y cuando cambia el formulario
        updateUrlExamples();
        document.getElementById('simulacion-form').addEventListener('change', updateUrlExamples);
        document.getElementById('simulacion-form').addEventListener('submit', function(e) {
            if (!e.submitter || e.submitter.id !== 'test-ajax') {
                return; // Solo interceptar si no es el botón de AJAX
            }
            e.preventDefault();
            updateUrlExamples();
        });

        // Función para probar con AJAX
        document.getElementById('test-ajax').addEventListener('click', function() {
            const form = document.getElementById('simulacion-form');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();

            document.getElementById('ajax-result').innerHTML =
                `<div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                 </div> Enviando solicitud...`;

            fetch(`error-simulador.php?${params}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                const result = document.getElementById('ajax-result');
                result.innerHTML = `
                    <p><strong>Código de estado:</strong> ${response.status} ${response.statusText}</p>
                    <p><strong>Tipo de contenido:</strong> ${response.headers.get('content-type')}</p>
                `;

                // Intentar obtener el cuerpo de la respuesta
                return response.text().then(text => {
                    try {
                        // Intentar parsear como JSON
                        const json = JSON.parse(text);
                        result.innerHTML += `
                            <p><strong>Respuesta (JSON):</strong></p>
                            <pre>${JSON.stringify(json, null, 2)}</pre>
                        `;
                    } catch (e) {
                        // Si no es JSON, mostrar como HTML
                        result.innerHTML += `
                            <p><strong>Respuesta (HTML):</strong></p>
                            <div class="border p-2 bg-light">${text}</div>
                        `;
                    }
                });
            })
            .catch(error => {
                document.getElementById('ajax-result').innerHTML = `
                    <div class="alert alert-danger">
                        <p><strong>Error:</strong> ${error.message}</p>
                    </div>
                `;
            });
        });
    </script>
</body>
</html>
