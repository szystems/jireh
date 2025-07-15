@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <div class="card mt-4">
        <div class="card-header">
            <h4>Controlador de Pruebas</h4>
        </div>
        <div class="card-body">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> <strong>Atención:</strong> Esta sección es solo para pruebas de funcionamiento del sistema.
                Las operaciones realizadas aquí pueden afectar a los datos reales.
            </div>

            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5>Datos de Prueba</h5>
                        </div>
                        <div class="card-body">
                            <p>Genera datos iniciales para realizar pruebas: categorías, artículos, trabajadores, clientes, etc.</p>
                            <button type="button" class="btn btn-primary" onclick="crearDatosPrueba()">
                                <i class="fas fa-database"></i> Generar Datos de Prueba
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Pruebas de Ventas y Comisiones</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header bg-primary text-white">
                                            <h5 class="mb-0">Prueba de Venta con Servicio</h5>
                                        </div>
                                        <div class="card-body">
                                            <p>Esta prueba crea una venta con un servicio y asigna trabajadores, verificando la generación de comisiones para:</p>
                                            <ul>
                                                <li>Trabajadores (mecánicos o car wash)</li>
                                                <li>Vendedor (por meta de ventas)</li>
                                            </ul>
                                        </div>
                                        <div class="card-footer">
                                            <button type="button" class="btn btn-primary" onclick="ejecutarPrueba('venta-con-servicio')">
                                                <i class="fas fa-play"></i> Ejecutar Prueba
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-header bg-success text-white">
                                            <h5 class="mb-0">Prueba de Venta Completa</h5>
                                        </div>
                                        <div class="card-body">
                                            <p>Esta prueba es más completa y crea una venta con:</p>
                                            <ul>
                                                <li>Servicio de mecánica (con comisiones para mecánicos)</li>
                                                <li>Servicio de car wash (con comisiones para trabajadores)</li>
                                                <li>Producto (sin comisiones específicas)</li>
                                                <li>Comisión para el vendedor por meta de ventas</li>
                                            </ul>
                                        </div>
                                        <div class="card-footer">
                                            <button type="button" class="btn btn-success" onclick="ejecutarPrueba('venta-completa')">
                                                <i class="fas fa-play"></i> Ejecutar Prueba
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header bg-info text-white">
                                            <h5 class="mb-0">Resultado de la Prueba</h5>
                                        </div>
                                        <div class="card-body">
                                            <div id="resultadoPrueba" class="p-3 bg-light rounded">
                                                <div class="text-center text-muted">
                                                    <i class="fas fa-info-circle"></i> No se ha ejecutado ninguna prueba aún.
                                                    Haga clic en uno de los botones de prueba para comenzar.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Otras Acciones de Prueba</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-header bg-danger text-white">
                                            <h5 class="mb-0">Eliminar Venta de Prueba</h5>
                                        </div>
                                        <div class="card-body">
                                            <p>Elimina una venta y verifica que se eliminen las comisiones asociadas.</p>
                                            <div class="input-group mb-3">
                                                <input type="number" id="ventaId" class="form-control" placeholder="ID de la venta a eliminar">
                                                <button class="btn btn-danger" type="button" onclick="eliminarVenta()">
                                                    <i class="fas fa-trash"></i> Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div class="card">
                                        <div class="card-header bg-warning">
                                            <h5 class="mb-0">Ver Comisiones de una Venta</h5>
                                        </div>
                                        <div class="card-body">
                                            <p>Muestra las comisiones generadas para una venta específica.</p>
                                            <div class="input-group mb-3">
                                                <input type="number" id="ventaIdComisiones" class="form-control" placeholder="ID de la venta">
                                                <button class="btn btn-warning" type="button" onclick="verComisiones()">
                                                    <i class="fas fa-search"></i> Ver Comisiones
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function ejecutarPrueba(tipoPrueba) {
        // Mostrar spinner mientras se ejecuta la prueba
        document.getElementById('resultadoPrueba').innerHTML = `
            <div class="text-center p-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Ejecutando prueba...</span>
                </div>
                <div class="mt-2">Ejecutando prueba, por favor espere...</div>
            </div>
        `;

        // Realizar la petición AJAX
        fetch(`/test/${tipoPrueba}`)
            .then(response => response.json())
            .then(data => {
                let resultadoHTML = '';

                if (data.success) {
                    resultadoHTML = `
                        <div class="alert alert-success">
                            <h5><i class="fas fa-check-circle"></i> ${data.message}</h5>
                        </div>
                    `;

                    // Si es la prueba completa, mostrar detalles
                    if (tipoPrueba === 'venta-completa' && data.resultados) {
                        const res = data.resultados;

                        resultadoHTML += `
                            <div class="mb-3">
                                <h6>Información de la Venta:</h6>
                                <ul class="list-group">
                                    <li class="list-group-item">ID Venta: ${res.venta.id}</li>
                                    <li class="list-group-item">Factura: ${res.venta.numero_factura}</li>
                                    <li class="list-group-item">Cliente: ${res.cliente.nombre}</li>
                                    <li class="list-group-item">Vehículo: ${res.cliente.vehiculo.placa} (${res.cliente.vehiculo.modelo})</li>
                                    <li class="list-group-item">Total: Q. ${res.venta.total}</li>
                                </ul>
                            </div>

                            <div class="mb-3">
                                <h6>Artículos Incluidos:</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Artículo</th>
                                                <th>Precio</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Servicio Mecánica</td>
                                                <td>${res.articulos.servicio_mecanica.nombre}</td>
                                                <td>Q. ${res.articulos.servicio_mecanica.precio}</td>
                                            </tr>
                                            <tr>
                                                <td>Servicio Car Wash</td>
                                                <td>${res.articulos.servicio_carwash.nombre}</td>
                                                <td>Q. ${res.articulos.servicio_carwash.precio}</td>
                                            </tr>
                                            <tr>
                                                <td>Producto</td>
                                                <td>${res.articulos.producto.nombre}</td>
                                                <td>Q. ${res.articulos.producto.precio}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="mb-3">
                                <h6>Comisiones Generadas (${res.comisiones.total}):</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Tipo</th>
                                                <th>Receptor</th>
                                                <th>Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${res.comisiones.detalle.map(c => `
                                                <tr>
                                                    <td>${c.id}</td>
                                                    <td>${c.tipo}</td>
                                                    <td>${c.receptor_tipo}: ${c.receptor_nombre}</td>
                                                    <td>Q. ${c.monto}</td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="alert ${res.verificacion.todos_tipos_generados ? 'alert-success' : 'alert-warning'}">
                                <h6>Verificación de Comisiones:</h6>
                                <ul>
                                    <li>Comisiones para Mecánicos: ${res.verificacion.comisiones_mecanicos}</li>
                                    <li>Comisiones para Car Wash: ${res.verificacion.comisiones_carwash}</li>
                                    <li>Comisiones para Vendedor: ${res.verificacion.comisiones_vendedor}</li>
                                    <li>Estado: ${res.verificacion.todos_tipos_generados ? 'TODOS LOS TIPOS DE COMISIONES GENERADOS CORRECTAMENTE' : 'ALGÚN TIPO DE COMISIÓN NO SE GENERÓ'}</li>
                                </ul>
                            </div>

                            <div class="mt-3">
                                <a href="/comisiones" class="btn btn-primary">
                                    <i class="fas fa-list"></i> Ver Todas las Comisiones
                                </a>
                                <a href="/comisiones/show/${res.comisiones.detalle[0]?.id}" class="btn btn-info">
                                    <i class="fas fa-eye"></i> Ver Detalle de Comisión
                                </a>
                            </div>
                        `;
                    } else if (tipoPrueba === 'venta-con-servicio') {
                        // Para la prueba simple
                        resultadoHTML += `
                            <div class="mb-3">
                                <h6>Información Generada:</h6>
                                <ul class="list-group">
                                    <li class="list-group-item">ID Venta: ${data.venta_id}</li>
                                    <li class="list-group-item">ID Detalle: ${data.detalle_id}</li>
                                    <li class="list-group-item">Asignaciones de Trabajadores: ${data.asignaciones}</li>
                                    <li class="list-group-item">Total de Comisiones: ${data.comisiones}</li>
                                </ul>
                            </div>

                            <div class="mb-3">
                                <h6>Comisión del Vendedor:</h6>
                                <div class="alert alert-info">
                                    ${typeof data.comision_vendedor === 'object'
                                        ? `ID: ${data.comision_vendedor.id}, Monto: Q. ${data.comision_vendedor.monto}`
                                        : data.comision_vendedor}
                                </div>
                            </div>

                            <div class="mt-3">
                                <a href="/comisiones" class="btn btn-primary">
                                    <i class="fas fa-list"></i> Ver Todas las Comisiones
                                </a>
                                <a href="/test/ver-comisiones/${data.venta_id}" class="btn btn-info" target="_blank">
                                    <i class="fas fa-eye"></i> Ver Comisiones de esta Venta (JSON)
                                </a>
                            </div>
                        `;
                    }
                } else {
                    resultadoHTML = `
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-triangle"></i> Error en la prueba</h5>
                            <p>${data.error}</p>
                            <p>Archivo: ${data.file}, Línea: ${data.line}</p>
                        </div>
                    `;
                }

                document.getElementById('resultadoPrueba').innerHTML = resultadoHTML;
            })
            .catch(error => {
                document.getElementById('resultadoPrueba').innerHTML = `
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-triangle"></i> Error al ejecutar la prueba</h5>
                        <p>${error.message}</p>
                    </div>
                `;
            });
    }

    function eliminarVenta() {
        const ventaId = document.getElementById('ventaId').value;

        if (!ventaId) {
            alert('Por favor, ingrese un ID de venta válido');
            return;
        }

        if (!confirm(`¿Está seguro que desea eliminar la venta #${ventaId}? Esta acción es irreversible.`)) {
            return;
        }

        document.getElementById('resultadoPrueba').innerHTML = `
            <div class="text-center p-3">
                <div class="spinner-border text-danger" role="status">
                    <span class="visually-hidden">Eliminando venta...</span>
                </div>
                <div class="mt-2">Eliminando venta #${ventaId}, por favor espere...</div>
            </div>
        `;

        fetch(`/test/eliminar-venta/${ventaId}`)
            .then(response => response.json())
            .then(data => {
                let resultadoHTML = '';

                if (data.success) {
                    resultadoHTML = `
                        <div class="alert alert-success">
                            <h5><i class="fas fa-check-circle"></i> ${data.message}</h5>
                        </div>

                        <div class="mb-3">
                            <h6>Informe de Eliminación:</h6>
                            <div class="alert alert-info">
                                <ul>
                                    <li>Comisiones antes de eliminar: ${data.informe.comisiones.length}</li>
                                    <li>Comisiones después de eliminar: ${data.verificacion.comisiones_despues}</li>
                                    <li>Estado: ${data.verificacion.comisiones_eliminadas_correctamente ? 'TODAS LAS COMISIONES ELIMINADAS CORRECTAMENTE' : 'ALGUNAS COMISIONES NO SE ELIMINARON'}</li>
                                </ul>
                            </div>
                        </div>
                    `;
                } else {
                    resultadoHTML = `
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-triangle"></i> Error al eliminar la venta</h5>
                            <p>${data.message}</p>
                        </div>
                    `;
                }

                document.getElementById('resultadoPrueba').innerHTML = resultadoHTML;
            })
            .catch(error => {
                document.getElementById('resultadoPrueba').innerHTML = `
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-triangle"></i> Error al eliminar la venta</h5>
                        <p>${error.message}</p>
                    </div>
                `;
            });
    }

    function verComisiones() {
        const ventaId = document.getElementById('ventaIdComisiones').value;

        if (!ventaId) {
            alert('Por favor, ingrese un ID de venta válido');
            return;
        }

        document.getElementById('resultadoPrueba').innerHTML = `
            <div class="text-center p-3">
                <div class="spinner-border text-warning" role="status">
                    <span class="visually-hidden">Consultando comisiones...</span>
                </div>
                <div class="mt-2">Consultando comisiones de la venta #${ventaId}, por favor espere...</div>
            </div>
        `;

        fetch(`/test/ver-comisiones/${ventaId}`)
            .then(response => response.json())
            .then(data => {
                let resultadoHTML = '';

                if (data.comisiones && data.comisiones.length > 0) {
                    resultadoHTML = `
                        <div class="alert alert-success">
                            <h5><i class="fas fa-check-circle"></i> Comisiones encontradas</h5>
                            <p>Se encontraron ${data.comisiones.length} comisiones para la venta #${ventaId}</p>
                        </div>

                        <div class="mb-3">
                            <h6>Listado de Comisiones:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Tipo</th>
                                            <th>Receptor</th>
                                            <th>Monto</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${data.comisiones.map(c => `
                                            <tr>
                                                <td>${c.id}</td>
                                                <td>${c.tipo_comision}</td>
                                                <td>${c.commissionable_type.split('\\').pop()}: ${c.commissionable_id}</td>
                                                <td>Q. ${c.monto}</td>
                                                <td>
                                                    ${c.estado === 'pendiente' ? '<span class="badge bg-warning">Pendiente</span>' : ''}
                                                    ${c.estado === 'pagado' ? '<span class="badge bg-success">Pagado</span>' : ''}
                                                    ${c.estado === 'cancelado' ? '<span class="badge bg-danger">Cancelado</span>' : ''}
                                                </td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                <a href="/comisiones" class="btn btn-primary">
                                    <i class="fas fa-list"></i> Ver Todas las Comisiones
                                </a>
                            </div>
                        </div>
                    `;
                } else {
                    resultadoHTML = `
                        <div class="alert alert-warning">
                            <h5><i class="fas fa-exclamation-triangle"></i> No se encontraron comisiones</h5>
                            <p>No existen comisiones asociadas a la venta #${ventaId}</p>
                        </div>
                    `;
                }

                document.getElementById('resultadoPrueba').innerHTML = resultadoHTML;
            })
            .catch(error => {
                document.getElementById('resultadoPrueba').innerHTML = `
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-triangle"></i> Error al consultar comisiones</h5>
                        <p>${error.message}</p>
                    </div>
                `;
            });
    }

    function crearDatosPrueba() {
        document.getElementById('resultadoPrueba').innerHTML = `
            <div class="text-center p-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Generando datos de prueba...</span>
                </div>
                <div class="mt-2">Generando datos de prueba, por favor espere...</div>
            </div>
        `;

        fetch('/test/crear-datos-prueba')
            .then(response => response.json())
            .then(data => {
                let resultadoHTML = '';

                if (data.success) {
                    const res = data.resultados;
                    resultadoHTML = `
                        <div class="alert alert-success">
                            <h5><i class="fas fa-check-circle"></i> ${data.message}</h5>
                        </div>

                        <div class="accordion" id="acordeonDatosPrueba">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingCategorias">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCategorias" aria-expanded="true" aria-controls="collapseCategorias">
                                        Categorías (${res.categorias.length})
                                    </button>
                                </h2>
                                <div id="collapseCategorias" class="accordion-collapse collapse show" aria-labelledby="headingCategorias" data-bs-parent="#acordeonDatosPrueba">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            ${res.categorias.map(c => `
                                                <li class="list-group-item ${c.nuevo ? 'list-group-item-success' : ''}">
                                                    <i class="fas ${c.nuevo ? 'fa-plus-circle text-success' : 'fa-check-circle text-info'}"></i>
                                                    ID: ${c.id} - ${c.nombre} ${c.nuevo ? '<span class="badge bg-success">Nuevo</span>' : '<span class="badge bg-info">Existente</span>'}
                                                </li>
                                            `).join('')}
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingArticulos">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseArticulos" aria-expanded="false" aria-controls="collapseArticulos">
                                        Artículos (${res.articulos.length})
                                    </button>
                                </h2>
                                <div id="collapseArticulos" class="accordion-collapse collapse" aria-labelledby="headingArticulos" data-bs-parent="#acordeonDatosPrueba">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            ${res.articulos.map(a => `
                                                <li class="list-group-item ${a.nuevo ? 'list-group-item-success' : ''}">
                                                    <i class="fas ${a.nuevo ? 'fa-plus-circle text-success' : 'fa-check-circle text-info'}"></i>
                                                    ID: ${a.id} - ${a.nombre} (${a.tipo}) - Q. ${a.precio}
                                                    ${a.nuevo ? '<span class="badge bg-success">Nuevo</span>' : '<span class="badge bg-info">Existente</span>'}
                                                </li>
                                            `).join('')}
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTrabajadores">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTrabajadores" aria-expanded="false" aria-controls="collapseTrabajadores">
                                        Trabajadores (${res.trabajadores.length})
                                    </button>
                                </h2>
                                <div id="collapseTrabajadores" class="accordion-collapse collapse" aria-labelledby="headingTrabajadores" data-bs-parent="#acordeonDatosPrueba">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            ${res.trabajadores.map(t => `
                                                <li class="list-group-item ${t.nuevo ? 'list-group-item-success' : ''}">
                                                    <i class="fas ${t.nuevo ? 'fa-plus-circle text-success' : 'fa-check-circle text-info'}"></i>
                                                    ID: ${t.id} - ${t.nombre} (${t.tipo})
                                                    ${t.nuevo ? '<span class="badge bg-success">Nuevo</span>' : '<span class="badge bg-info">Existente</span>'}
                                                </li>
                                            `).join('')}
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingClientes">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseClientes" aria-expanded="false" aria-controls="collapseClientes">
                                        Clientes y Vehículos
                                    </button>
                                </h2>
                                <div id="collapseClientes" class="accordion-collapse collapse" aria-labelledby="headingClientes" data-bs-parent="#acordeonDatosPrueba">
                                    <div class="accordion-body">
                                        <h6>Clientes (${res.clientes.length}):</h6>
                                        <ul class="list-group mb-3">
                                            ${res.clientes.map(c => `
                                                <li class="list-group-item ${c.nuevo ? 'list-group-item-success' : ''}">
                                                    <i class="fas ${c.nuevo ? 'fa-plus-circle text-success' : 'fa-check-circle text-info'}"></i>
                                                    ID: ${c.id} - ${c.nombre}
                                                    ${c.nuevo ? '<span class="badge bg-success">Nuevo</span>' : '<span class="badge bg-info">Existente</span>'}
                                                </li>
                                            `).join('')}
                                        </ul>

                                        <h6>Vehículos (${res.vehiculos.length}):</h6>
                                        <ul class="list-group">
                                            ${res.vehiculos.map(v => `
                                                <li class="list-group-item ${v.nuevo ? 'list-group-item-success' : ''}">
                                                    <i class="fas ${v.nuevo ? 'fa-plus-circle text-success' : 'fa-check-circle text-info'}"></i>
                                                    ID: ${v.id} - ${v.placa} (${v.modelo}) - Cliente: ${v.cliente}
                                                    ${v.nuevo ? '<span class="badge bg-success">Nuevo</span>' : '<span class="badge bg-info">Existente</span>'}
                                                </li>
                                            `).join('')}
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingUsuarios">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUsuarios" aria-expanded="false" aria-controls="collapseUsuarios">
                                        Usuario y Meta de Venta
                                    </button>
                                </h2>
                                <div id="collapseUsuarios" class="accordion-collapse collapse" aria-labelledby="headingUsuarios" data-bs-parent="#acordeonDatosPrueba">
                                    <div class="accordion-body">
                                        <h6>Usuario:</h6>
                                        <ul class="list-group mb-3">
                                            <li class="list-group-item ${res.usuario.nuevo ? 'list-group-item-success' : ''}">
                                                <i class="fas ${res.usuario.nuevo ? 'fa-plus-circle text-success' : 'fa-check-circle text-info'}"></i>
                                                ID: ${res.usuario.id} - ${res.usuario.nombre}
                                                ${res.usuario.nuevo ? '<span class="badge bg-success">Nuevo</span>' : '<span class="badge bg-info">Existente</span>'}
                                            </li>
                                        </ul>

                                        <h6>Meta de Venta:</h6>
                                        <ul class="list-group">
                                            <li class="list-group-item ${res.meta_venta.nuevo ? 'list-group-item-success' : ''}">
                                                <i class="fas ${res.meta_venta.nuevo ? 'fa-plus-circle text-success' : 'fa-check-circle text-info'}"></i>
                                                ID: ${res.meta_venta.id} - Usuario: ${res.meta_venta.usuario_id} - Comisión: ${res.meta_venta.porcentaje_comision}%
                                                ${res.meta_venta.nuevo ? '<span class="badge bg-success">Nuevo</span>' : '<span class="badge bg-info">Existente</span>'}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> ¡Datos de prueba generados correctamente! Ahora puede ejecutar las pruebas de venta y comisiones.
                            </div>
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-primary" onclick="ejecutarPrueba('venta-con-servicio')">
                                    <i class="fas fa-play"></i> Ejecutar Prueba de Venta con Servicio
                                </button>
                                <button class="btn btn-success" onclick="ejecutarPrueba('venta-completa')">
                                    <i class="fas fa-play"></i> Ejecutar Prueba de Venta Completa
                                </button>
                            </div>
                        </div>
                    `;
                } else {
                    resultadoHTML = `
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-triangle"></i> Error al generar datos de prueba</h5>
                            <p>${data.message}</p>
                            <p>${data.error}</p>
                        </div>
                    `;
                }

                document.getElementById('resultadoPrueba').innerHTML = resultadoHTML;
            })
            .catch(error => {
                document.getElementById('resultadoPrueba').innerHTML = `
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-triangle"></i> Error al generar datos de prueba</h5>
                        <p>${error.message}</p>
                    </div>
                `;
            });
    }
</script>
@endsection
