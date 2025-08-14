<?php
// Script para encontrar la primera venta activa con detalles para hacer pruebas
require_once __DIR__.'/../vendor/autoload.php';
use Illuminate\Support\Facades\DB;
$app = require_once __DIR__.'/../bootstrap/app.php';

// Inicializar la aplicación
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Consultar ventas activas con detalles
$ventasConDetalles = DB::select('
    SELECT v.id, COUNT(dv.id) as num_detalles, v.cliente_id, v.fecha, v.numero_factura
    FROM ventas v
    LEFT JOIN detalle_ventas dv ON v.id = dv.venta_id
    WHERE v.estado = 1
    GROUP BY v.id, v.cliente_id, v.fecha, v.numero_factura
    HAVING num_detalles > 0
    LIMIT 5
');

// Variables para el HTML
$htmlContent = '';

// Comprobar si hay resultados
if (count($ventasConDetalles) > 0) {
    $htmlContent .= '<div class="alert alert-success mb-4"><i class="bi bi-check-circle-fill"></i> Se encontraron ' . count($ventasConDetalles) . ' ventas activas con detalles.</div>';

    $htmlContent .= '<table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Fecha</th>
                            <th>Factura</th>
                            <th>Detalles</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>';

    foreach ($ventasConDetalles as $venta) {
        // Obtener nombre de cliente
        $cliente = DB::table('clientes')->where('id', $venta->cliente_id)->first();
        $nombreCliente = $cliente ? $cliente->nombre : 'Desconocido';

        // Formatear fecha
        $fecha = date('d/m/Y', strtotime($venta->fecha));

        $htmlContent .= '<tr>
                        <td>' . $venta->id . '</td>
                        <td>' . $nombreCliente . '</td>
                        <td>' . $fecha . '</td>
                        <td>' . ($venta->numero_factura ?: 'Sin factura') . '</td>
                        <td>' . $venta->num_detalles . ' detalles</td>
                        <td>
                            <a href="/test-form-venta.php?venta_id=' . $venta->id . '" class="btn btn-sm btn-primary" target="_blank">Test Form</a>
                            <a href="/test-validacion-venta.php?venta_id=' . $venta->id . '" class="btn btn-sm btn-info" target="_blank">Test Validación</a>
                            <a href="/edit-venta/' . $venta->id . '" class="btn btn-sm btn-success" target="_blank">Editar</a>
                        </td>
                    </tr>';
    }

    $htmlContent .= '</tbody></table>';

    // Agregar una sección para el ID recomendado
    $ventaId = $ventasConDetalles[0]->id;
    $htmlContent .= '<div class="alert alert-primary">
                        <h5>Sugerencia</h5>
                        <p>Use <strong>venta_id=' . $ventaId . '</strong> para sus pruebas</p>
                        <hr>
                        <p>Enlaces rápidos:</p>
                        <a href="/test-form-venta.php?venta_id=' . $ventaId . '" class="btn btn-primary mb-2" target="_blank">
                            <i class="bi bi-clipboard-check"></i> Ejecutar test-form-venta.php
                        </a><br>
                        <a href="/test-validacion-venta.php?venta_id=' . $ventaId . '" class="btn btn-info mb-2" target="_blank">
                            <i class="bi bi-check-square"></i> Ejecutar test-validacion-venta.php
                        </a><br>
                        <a href="/edit-venta/' . $ventaId . '" class="btn btn-success" target="_blank">
                            <i class="bi bi-pencil-square"></i> Ver formulario de edición
                        </a>
                    </div>';
} else {
    $htmlContent .= '<div class="alert alert-warning">
                        <h5><i class="bi bi-exclamation-triangle-fill"></i> No se encontraron ventas activas con detalles.</h5>
                        <p>Cree una venta con detalles primero para poder realizar las pruebas.</p>
                        <a href="/ventas/create" class="btn btn-primary" target="_blank">Crear nueva venta</a>
                    </div>';
}

// Mostrar HTML mejorado (con estilos de Bootstrap)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Búsqueda de Ventas para Pruebas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        body { padding: 20px; }
        .table { margin-top: 20px; }
        .btn { margin-right: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="bi bi-search"></i> Búsqueda de Ventas para Pruebas</h1>
        <p class="lead">Aquí puede encontrar ventas activas con detalles para ejecutar las pruebas.</p>

        <div class="card mb-4">
            <div class="card-body">
                <?php echo $htmlContent; ?>
            </div>
        </div>

        <div class="mt-4">
            <h3>Instrucciones para pruebas</h3>
            <ul>
                <li>Seleccione una venta con detalles para obtener resultados más completos en las pruebas.</li>
                <li>La primera venta de la lista es la recomendada para pruebas.</li>
                <li>Use el ID de venta en los parámetros de URL para los scripts de prueba.</li>
                <li>Si no hay ventas activas con detalles, cree una primero.</li>
            </ul>
        </div>

        <div class="mt-4">
            <h3>Otras herramientas de prueba</h3>
            <div class="list-group">
                <a href="/error-simulador.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Simulador de Errores</h5>
                        <p class="mb-1">Simule diferentes tipos de errores para probar el manejo en el sistema.</p>
                    </div>
                    <span class="badge bg-primary rounded-pill"><i class="bi bi-arrow-right"></i></span>
                </a>
            </div>
        </div>

        <footer class="mt-5 text-muted">
            <p>Generado el: <?= date('d/m/Y H:i:s') ?></p>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
