<?php
// Script para probar el formulario de edición de ventas

require_once __DIR__.'/../vendor/autoload.php';
use Illuminate\Support\Facades\DB;

// Ruta base
$baseUrl = 'http://localhost:8000';

// ID de una venta existente para pruebas
$ventaId = $_GET['venta_id'] ?? 1; // Obtener el ID de la URL o usar 1 como valor predeterminado

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

// Casos de prueba
$tests = [
    'carga_pagina' => [
        'url' => "{$baseUrl}/edit-venta/{$ventaId}",
        'method' => 'GET',
        'description' => 'Verifica que la página de edición carga correctamente'
    ],
    'editar_datos_basicos' => [
        'url' => "{$baseUrl}/update-venta/{$ventaId}",
        'method' => 'POST',
        'data' => [
            '_method' => 'PUT',
            '_token' => '', // Se rellenará con JS
            'cliente_id' => 1,
            'vehiculo_id' => 1,
            'numero_factura' => 'TEST-' . rand(1000, 9999),
            'fecha' => date('Y-m-d'),
            'tipo_venta' => 'Car Wash',
            'estado_pago' => 'pendiente',
            'estado' => 1
        ],
        'description' => 'Prueba editar sólo datos básicos sin cambiar detalles'
    ],
    'eliminar_detalle' => [
        'description' => 'Prueba eliminar un detalle existente',
        'js' => "
            // Este código simula un clic en el botón eliminar de un detalle
            // y luego envía el formulario
            console.log('Probando eliminación de un detalle');
            const eliminarBtn = document.querySelector('.eliminar-detalle');
            if (eliminarBtn) {
                eliminarBtn.click();
                console.log('Botón eliminar clickeado');
                setTimeout(() => {
                    document.getElementById('forma-editar-venta').submit();
                }, 500);
            } else {
                console.error('No se encontró ningún botón de eliminar');
            }
        "
    ],
    'agregar_detalle' => [
        'description' => 'Prueba agregar un nuevo detalle',
        'js' => "
            console.log('Probando agregar un nuevo detalle');
            // Seleccionar el primer artículo disponible
            const articuloSelect = document.getElementById('articulo');
            if (articuloSelect && articuloSelect.options.length > 1) {
                // Seleccionar segunda opción (índice 1) para evitar la opción vacía
                articuloSelect.selectedIndex = 1;
                // Disparar evento change para que se actualicen los datos
                const event = new Event('change');
                articuloSelect.dispatchEvent(event);

                // Establecer cantidad
                document.getElementById('cantidad-nuevo').value = '1';

                // Clic en botón de agregar
                setTimeout(() => {
                    document.getElementById('agregar-detalle').click();
                    console.log('Artículo agregado');

                    // Enviar formulario después de agregar
                    setTimeout(() => {
                        document.getElementById('forma-editar-venta').submit();
                    }, 500);
                }, 500);
            } else {
                console.error('No se encontró el selector de artículos o no tiene opciones');
            }
        "
    ],
    'editar_trabajadores' => [
        'description' => 'Prueba editar trabajadores de un servicio',
        'js' => "
            console.log('Probando editar trabajadores');
            const editarTrabajadoresBtn = document.querySelector('.editar-trabajadores');
            if (editarTrabajadoresBtn) {
                editarTrabajadoresBtn.click();
                console.log('Botón editar trabajadores clickeado');

                // Esperar a que el modal se abra
                setTimeout(() => {
                    // Seleccionar todos los trabajadores disponibles
                    const trabajadoresSelect = document.getElementById('trabajadores-carwash-edit');
                    if (trabajadoresSelect) {
                        for (let i = 0; i < trabajadoresSelect.options.length; i++) {
                            trabajadoresSelect.options[i].selected = true;
                        }

                        // Guardar trabajadores
                        setTimeout(() => {
                            document.getElementById('guardar-trabajadores').click();
                            console.log('Trabajadores guardados');

                            // Enviar formulario
                            setTimeout(() => {
                                document.getElementById('forma-editar-venta').submit();
                            }, 500);
                        }, 500);
                    } else {
                        console.error('No se encontró el selector de trabajadores');
                    }
                }, 500);
            } else {
                console.error('No se encontró ningún botón de editar trabajadores');
            }
        "
    ],
    'modificar_cantidad' => [
        'description' => 'Prueba modificar cantidad de un detalle existente',
        'js' => "
            console.log('Probando modificar cantidad');
            const cantidadInput = document.querySelector('.cantidad-input');
            if (cantidadInput) {
                // Guardar valor original
                const valorOriginal = cantidadInput.value;
                // Cambiar a un valor diferente
                cantidadInput.value = parseInt(valorOriginal) + 1;
                // Disparar evento change
                cantidadInput.dispatchEvent(new Event('change'));
                        console.log('Cantidad modificada de ' + valorOriginal + ' a ' + cantidadInput.value);

                // Enviar formulario
                setTimeout(() => {
                    document.getElementById('forma-editar-venta').submit();
                }, 500);
            } else {
                console.error('No se encontró ningún input de cantidad');
            }
        "
    ]
];

// HTML para mostrar los resultados de las pruebas
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pruebas del Formulario de Edición de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .test-card { margin-bottom: 15px; }
        .btn-test { margin-right: 5px; }
        .results { margin-top: 20px; background: #f8f9fa; padding: 15px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Pruebas del Formulario de Edición de Ventas</h1>
        <p>Este script permite probar las diferentes funcionalidades del formulario de edición de ventas.</p>

        <div class="alert <?= $ventaActiva ? 'alert-info' : 'alert-danger' ?> mb-3">
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

        <?php if ($ventaActiva && !$tieneDetalles): ?>
        <div class="alert alert-warning">
            <strong>¡Advertencia!</strong> Esta venta no tiene detalles. Algunas pruebas que requieran detalles existentes fallarán.
        </div>
        <?php endif; ?>

        <div class="row">
            <?php foreach ($tests as $key => $test): ?>
            <div class="col-md-4">
                <div class="card test-card">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($test['description']) ?></h5>
                        <button class="btn btn-primary btn-test" data-test="<?= $key ?>">Ejecutar prueba</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="results">
            <h3>Resultados:</h3>
            <div id="test-results"></div>
        </div>

        <iframe id="test-frame" style="width: 100%; height: 500px; border: 1px solid #ccc; display: none;"></iframe>
    </div>

    <script>
        // Token CSRF para las solicitudes POST
        const csrfToken = '{{ csrf_token() }}';

        // Función para ejecutar pruebas
        document.querySelectorAll('.btn-test').forEach(button => {
            button.addEventListener('click', function() {
                const testKey = this.getAttribute('data-test');
                const test = <?= json_encode($tests) ?>[testKey];

                document.getElementById('test-results').innerHTML =
                    `<div class="alert alert-info">Ejecutando prueba: ${test.description}...</div>`;

                if (test.method === 'GET') {
                    // Para pruebas GET, simplemente navegar a la URL
                    const iframe = document.getElementById('test-frame');
                    iframe.style.display = 'block';
                    iframe.src = test.url;

                    iframe.onload = function() {
                        document.getElementById('test-results').innerHTML +=
                            `<div class="alert alert-success">Página cargada correctamente</div>`;
                    };
                } else if (test.js) {
                    // Para pruebas que requieren JavaScript
                    const iframe = document.getElementById('test-frame');
                    iframe.style.display = 'block';

                    // Cargar primero la página de edición
                    iframe.src = '<?= "{$baseUrl}/edit-venta/{$ventaId}" ?>';

                    iframe.onload = function() {
                        document.getElementById('test-results').innerHTML +=
                            `<div class="alert alert-info">Página cargada, ejecutando JavaScript...</div>`;

                        // Ejecutar el JavaScript en el iframe
                        try {
                            iframe.contentWindow.eval(test.js);
                            document.getElementById('test-results').innerHTML +=
                                `<div class="alert alert-success">JavaScript ejecutado correctamente</div>`;
                        } catch (error) {
                            document.getElementById('test-results').innerHTML +=
                                `<div class="alert alert-danger">Error ejecutando JavaScript: ${error.message}</div>`;
                        }
                    };
                } else if (test.method === 'POST') {
                    // Para pruebas POST, crear un formulario y enviarlo
                    const form = document.createElement('form');
                    form.method = test.method;
                    form.action = test.url;
                    form.target = 'test-frame';

                    // Agregar campos del formulario
                    for (const [key, value] of Object.entries(test.data)) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = key;
                        input.value = key === '_token' ? csrfToken : value;
                        form.appendChild(input);
                    }

                    // Agregar el formulario al documento y enviarlo
                    document.body.appendChild(form);

                    const iframe = document.getElementById('test-frame');
                    iframe.style.display = 'block';

                    form.submit();
                    document.body.removeChild(form);
                }
            });
        });
    </script>
</body>
</html>
