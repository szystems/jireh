<?php
/**
 * Herramienta de prueba para trabajadores en ventas
 * 
 * Este script permite:
 * 1. Ver los trabajadores asignados a los detalles de venta
 * 2. Probar la asignación manual de trabajadores
 * 3. Verificar la integridad de los datos
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Models\DetalleVenta;
use App\Models\Trabajador;
use App\Models\Venta;
use App\Models\TrabajadorDetalleVenta;
use Illuminate\Database\Capsule\Manager as DB;

// Iniciar aplicación Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

// Configuración
$ventaId = $_GET['venta_id'] ?? 1;
$detalleId = $_GET['detalle_id'] ?? null;
$accion = $_GET['accion'] ?? 'ver';

// Obtener datos
$venta = Venta::with(['detalleVentas.articulo', 'detalleVentas.trabajadoresCarwash'])
    ->find($ventaId);

// Si no se encontró la venta, mostrar mensaje de error
if (!$venta) {
    echo "<div style='background-color: #ffcccc; padding: 15px; border-radius: 5px; margin: 20px;'>";
    echo "<h2>Error: Venta no encontrada</h2>";
    echo "<p>La venta con ID {$ventaId} no existe o ha sido eliminada.</p>";
    echo "<p><a href='find-ventas-para-test.php'>Buscar otra venta</a></p>";
    echo "</div>";
    exit;
}

// Obtener todos los trabajadores para listas desplegables
$trabajadores = Trabajador::where('estado', 1)
    ->whereHas('tipoTrabajador', function($query) {
        $query->where('nombre', 'Car Wash');
    })->get();

// Título y estilo base
echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Prueba de Trabajadores - Venta #{$ventaId}</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        .card { margin-bottom: 20px; }
        .status-badge { font-size: 90%; }
        .actions { margin-top: 15px; }
        .debug-info { font-family: monospace; font-size: 12px; background: #f8f9fa; padding: 10px; border-radius: 4px; }
        .id-badge { background: #e2e3e5; color: #212529; padding: 2px 6px; border-radius: 3px; font-size: 85%; }
    </style>
</head>
<body>
    <div class='container-fluid py-4'>
        <h1 class='mb-4'>
            Prueba de Trabajadores 
            <small class='text-muted'>Venta #<span class='badge bg-secondary'>{$ventaId}</span></small>
        </h1>";

// Barra de navegación superior
echo "<div class='card mb-4'>
        <div class='card-body'>
            <div class='row'>
                <div class='col-md-8'>
                    <h5>Herramientas de prueba:</h5>
                    <a href='find-ventas-para-test.php' class='btn btn-outline-primary me-2 mt-2'>Buscar ventas</a>
                    <a href='find-ventas-activas.php' class='btn btn-outline-success me-2 mt-2'>Listar ventas activas</a>
                    <a href='test-form-venta.php' class='btn btn-outline-info me-2 mt-2'>Probar formulario</a>
                    <a href='test-validacion-venta.php' class='btn btn-outline-warning me-2 mt-2'>Probar validación</a>
                    <a href='error-simulador.php' class='btn btn-outline-danger me-2 mt-2'>Simulador de errores</a>
                </div>
                <div class='col-md-4 text-end'>
                    <h5>Acceder a la venta:</h5>
                    <a href='../admin/ventas' class='btn btn-primary me-2 mt-2' target='_blank'>Panel ventas</a>
                    <a href='../show-venta/{$ventaId}' class='btn btn-success me-2 mt-2' target='_blank'>Ver venta</a>
                    <a href='../edit-venta/{$ventaId}' class='btn btn-warning me-2 mt-2' target='_blank'>Editar venta</a>
                </div>
            </div>
        </div>
    </div>";

// Información de la venta
echo "<div class='card'>
        <div class='card-header'>
            <h3>Información de Venta</h3>
        </div>
        <div class='card-body'>
            <div class='row'>
                <div class='col-md-4'>
                    <p><strong>ID:</strong> {$venta->id}</p>
                    <p><strong>Factura:</strong> " . ($venta->numero_factura ?: 'Sin factura') . "</p>
                    <p><strong>Fecha:</strong> {$venta->fecha}</p>
                </div>
                <div class='col-md-4'>
                    <p><strong>Cliente:</strong> " . ($venta->cliente ? $venta->cliente->nombre : 'Sin cliente') . "</p>
                    <p><strong>Vehículo:</strong> " . ($venta->vehiculo ? "{$venta->vehiculo->marca} {$venta->vehiculo->modelo} ({$venta->vehiculo->placa})" : 'Sin vehículo') . "</p>
                    <p><strong>Tipo:</strong> {$venta->tipo_venta}</p>
                </div>
                <div class='col-md-4'>
                    <p><strong>Estado:</strong> <span class='badge " . ($venta->estado ? 'bg-success' : 'bg-danger') . "'>" . ($venta->estado ? 'Activa' : 'Inactiva') . "</span></p>
                    <p><strong>Pago:</strong> <span class='badge " . ($venta->estado_pago == 'pagado' ? 'bg-success' : 'bg-warning') . "'>{$venta->estado_pago}</span></p>
                    <p><strong>Total:</strong> $" . number_format($venta->detalleVentas->sum('sub_total'), 2) . "</p>
                </div>
            </div>
        </div>
    </div>";

// Mostrar mensajes de procesamiento si hay alguna acción
if ($accion == 'asignar' && $detalleId) {
    $detalle = DetalleVenta::find($detalleId);
    
    if ($detalle) {
        // Procesar la asignación de trabajadores
        if (isset($_POST['trabajadores']) && !empty($_POST['trabajadores'])) {
            // Limpiar asignaciones existentes
            $detalle->trabajadoresCarwash()->detach();
            
            // Asignar nuevos trabajadores
            $trabajadoresIds = $_POST['trabajadores'];
            $comision = $_POST['comision'] ?: 0;
            
            $result = $detalle->asignarTrabajadores($trabajadoresIds, $comision);
            
            echo "<div class='alert alert-success'>
                    <h4 class='alert-heading'>Trabajadores asignados correctamente</h4>
                    <p>Se han asignado " . count($result) . " trabajadores al detalle #{$detalleId}</p>
                    <hr>
                    <p class='mb-0'>Comisión asignada: $" . number_format($comision, 2) . "</p>
                  </div>";
        }
    }
}

if ($accion == 'eliminar' && $detalleId) {
    $detalle = DetalleVenta::find($detalleId);
    
    if ($detalle) {
        // Eliminar todas las asignaciones
        $count = $detalle->trabajadoresCarwash()->count();
        $detalle->trabajadoresCarwash()->detach();
        
        echo "<div class='alert alert-warning'>
                <h4 class='alert-heading'>Trabajadores desvinculados</h4>
                <p>Se han eliminado {$count} asignaciones de trabajadores del detalle #{$detalleId}</p>
              </div>";
    }
}

// Tabla de detalles con sus trabajadores
echo "<div class='card'>
        <div class='card-header'>
            <h3>Detalles de la Venta</h3>
        </div>
        <div class='card-body'>
            <div class='table-responsive'>
                <table class='table table-striped table-hover'>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Artículo</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Trabajadores Asignados</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>";

foreach ($venta->detalleVentas as $detalle) {
    $esServicio = $detalle->articulo && $detalle->articulo->tipo == 'servicio';
    $servicioClass = $esServicio ? 'table-info' : '';
    $trabajadoresAsignados = $detalle->trabajadoresCarwash;
    
    echo "<tr class='{$servicioClass}'>
            <td><span class='id-badge'>{$detalle->id}</span></td>
            <td>" . ($detalle->articulo ? $detalle->articulo->nombre : 'Artículo no disponible') . 
                 ($esServicio ? " <span class='badge bg-info'>Servicio</span>" : "") . "
            </td>
            <td>{$detalle->cantidad}</td>
            <td>$" . number_format($detalle->sub_total, 2) . "</td>
            <td>";
    
    if ($esServicio) {
        if ($trabajadoresAsignados->count() > 0) {
            echo "<span class='badge bg-success'>{$trabajadoresAsignados->count()} trabajador(es)</span>
                  <ul class='mt-2 mb-0'>";
            
            foreach ($trabajadoresAsignados as $trabajador) {
                echo "<li>{$trabajador->nombre_completo} <small class='text-muted'>[$" . 
                      number_format($trabajador->pivot->monto_comision, 2) . "]</small></li>";
            }
            
            echo "</ul>";
        } else {
            echo "<span class='badge bg-warning'>Sin trabajadores</span>";
        }
    } else {
        echo "<span class='text-muted'>No aplica</span>";
    }
    
    echo "</td>
          <td>";
    
    // Mostrar opciones solo para servicios
    if ($esServicio) {
        echo "<div class='btn-group'>
                <a href='?venta_id={$ventaId}&detalle_id={$detalle->id}&accion=ver' class='btn btn-sm btn-outline-primary'>Ver</a>
                <a href='?venta_id={$ventaId}&detalle_id={$detalle->id}&accion=eliminar' class='btn btn-sm btn-outline-danger' 
                   onclick='return confirm(\"¿Seguro que desea eliminar todos los trabajadores?\")'>Eliminar</a>
              </div>";
    } else {
        echo "<span class='text-muted'>No aplica</span>";
    }
    
    echo "</td>
        </tr>";
}

echo "      </tbody>
                </table>
            </div>
        </div>
    </div>";

// Si se seleccionó un detalle específico y es una acción ver o asignar, mostrar el formulario
if ($detalleId && ($accion == 'ver' || $accion == 'asignar')) {
    $detalle = DetalleVenta::find($detalleId);
    
    if ($detalle && $detalle->articulo && $detalle->articulo->tipo == 'servicio') {
        $trabajadoresAsignados = $detalle->trabajadoresCarwash->pluck('id')->toArray();
        
        echo "<div class='card'>
                <div class='card-header'>
                    <h3>Editar Trabajadores - Detalle #{$detalleId}</h3>
                </div>
                <div class='card-body'>
                    <div class='row'>
                        <div class='col-md-6'>
                            <p><strong>Artículo:</strong> {$detalle->articulo->nombre}</p>
                            <p><strong>Tipo:</strong> {$detalle->articulo->tipo}</p>
                            <p><strong>Cantidad:</strong> {$detalle->cantidad}</p>
                        </div>
                        <div class='col-md-6'>
                            <p><strong>Subtotal:</strong> $" . number_format($detalle->sub_total, 2) . "</p>
                            <p><strong>Trabajadores asignados:</strong> " . count($trabajadoresAsignados) . "</p>
                            <p><strong>Comisión:</strong> $" . number_format($detalle->articulo->comision_carwash, 2) . "</p>
                        </div>
                    </div>
                    
                    <form action='?venta_id={$ventaId}&detalle_id={$detalleId}&accion=asignar' method='post' class='mt-4'>
                        <div class='row'>
                            <div class='col-md-6 mb-3'>
                                <label class='form-label'><strong>Seleccionar Trabajadores:</strong></label>
                                <select name='trabajadores[]' class='form-select' multiple size='10'>";
        
        foreach ($trabajadores as $trabajador) {
            $selected = in_array($trabajador->id, $trabajadoresAsignados) ? 'selected' : '';
            echo "<option value='{$trabajador->id}' {$selected}>{$trabajador->nombre_completo}</option>";
        }
        
        echo "              </select>
                                <small class='text-muted'>Mantenga presionada la tecla Ctrl para seleccionar múltiples trabajadores</small>
                            </div>
                            <div class='col-md-6 mb-3'>
                                <label class='form-label'><strong>Comisión por trabajador:</strong></label>
                                <input type='number' name='comision' class='form-control' step='0.01' min='0' value='{$detalle->articulo->comision_carwash}'>
                                <small class='text-muted'>Esta comisión se aplicará a cada trabajador seleccionado</small>
                                
                                <div class='mt-4'>
                                    <h5>Trabajadores actualmente asignados:</h5>
                                    <ul class='list-group'>";
        
        if (count($trabajadoresAsignados) > 0) {
            foreach ($detalle->trabajadoresCarwash as $trabajador) {
                echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
                        {$trabajador->nombre_completo}
                        <span class='badge bg-primary'>$" . number_format($trabajador->pivot->monto_comision, 2) . "</span>
                      </li>";
            }
        } else {
            echo "<li class='list-group-item text-center text-muted'>No hay trabajadores asignados</li>";
        }
        
        echo "              </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class='mt-4'>
                            <button type='submit' class='btn btn-primary'>Guardar Asignaciones</button>
                            <a href='?venta_id={$ventaId}' class='btn btn-secondary'>Cancelar</a>
                        </div>
                    </form>
                    
                    <div class='mt-5'>
                        <h5>Información Técnica</h5>
                        <div class='debug-info'>
                            <p>Detalle ID: {$detalleId}</p>
                            <p>Trabajadores asignados IDs: " . implode(', ', $trabajadoresAsignados) . "</p>
                            <p>Registros en tabla pivot: " . TrabajadorDetalleVenta::where('detalle_venta_id', $detalleId)->count() . "</p>
                            <p>
                                <strong>SQL para verificar asignaciones:</strong><br>
                                SELECT * FROM trabajador_detalle_venta WHERE detalle_venta_id = {$detalleId};
                            </p>
                        </div>
                    </div>
                </div>
            </div>";
    } else {
        echo "<div class='alert alert-danger'>El detalle seleccionado no existe o no es un servicio que pueda tener trabajadores asignados.</div>";
    }
}

// Cerrar la página HTML
echo "
    </div>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
