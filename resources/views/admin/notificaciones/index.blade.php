@extends('layouts.admin')

@section('content')
<style>
/* Estilos para notificaciones */
.notification-item {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
    margin-bottom: 10px;
    border-radius: 8px;
}

.notification-item.unread {
    background-color: #f8f9fa;
    border-left-color: #007bff;
    box-shadow: 0 2px 4px rgba(0,123,255,0.1);
}

.notification-item.read {
    background-color: #f1f3f4;
    border-left-color: #28a745;
    opacity: 0.8;
    transition: all 0.3s ease;
}

.notification-item.read .notification-title {
    color: #6c757d;
    text-decoration: line-through;
}

.notification-item.read .notification-message {
    color: #6c757d;
}

.notification-content {
    padding: 15px;
}

.notification-icon {
    margin-right: 15px;
    font-size: 1.2rem;
}

.notification-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 8px;
}

.notification-title {
    font-weight: 600;
    margin: 0;
    color: #333;
}

.notification-meta {
    display: flex;
    gap: 10px;
    align-items: center;
}

.notification-message {
    margin: 8px 0;
    color: #555;
}

.notification-actions {
    margin-top: 10px;
    display: flex;
    gap: 10px;
}

.notification-time {
    font-size: 0.85rem;
    color: #888;
}

.badge-alta { background-color: #dc3545; }
.badge-media { background-color: #ffc107; color: #212529; }
.badge-baja { background-color: #28a745; }

/* Efecto hover */
.notification-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Estilos para filtros */
.form-select.filter-active {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.alert-info {
    border-left: 4px solid #0dcaf0;
}

.alert-warning {
    border-left: 4px solid #ffc107;
}

#indicador-resultados {
    margin-bottom: 1rem;
}
</style>

<div class="content-wrapper">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="page-title mb-0">
                        <i class="bi bi-bell me-2"></i>
                        Centro de Notificaciones
                    </h3>
                    <p class="text-muted mb-0">Gestión de alertas y notificaciones del sistema</p>
                </div>
                <div>
                    <button class="btn btn-sm btn-primary" onclick="marcarTodasComoLeidas()">
                        <i class="bi bi-check-all me-1"></i>
                        Marcar todas como leídas
                    </button>
                    <button class="btn btn-sm btn-outline-secondary ms-2" onclick="limpiarNotificacionesLeidas()">
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        Reset notificaciones
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary-transparent">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-primary-transparent rounded-circle">
                            <i class="bi bi-bell text-primary"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0">Total Notificaciones</h6>
                            <h4 class="mb-0" id="total-notificaciones">{{ $notificaciones->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning-transparent">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-warning-transparent rounded-circle">
                            <i class="bi bi-exclamation-triangle text-warning"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0">Prioridad Alta</h6>
                            <h4 class="mb-0" id="prioridad-alta">{{ $notificaciones->where('prioridad', 'alta')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger-transparent">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-danger-transparent rounded-circle">
                            <i class="bi bi-exclamation-circle text-danger"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0">Stock Crítico</h6>
                            <h4 class="mb-0" id="stock-critico">{{ $notificaciones->where('tipo', 'stock_critico')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success-transparent">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md bg-success-transparent rounded-circle">
                            <i class="bi bi-check-circle text-success"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0">Ventas Importantes</h6>
                            <h4 class="mb-0" id="ventas-importantes">{{ $notificaciones->where('tipo', 'venta_importante')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <select class="form-select" id="filtro-tipo" onchange="aplicarFiltros()">
                                <option value="">Todos los tipos</option>
                                <option value="stock_critico">Stock Crítico</option>
                                <option value="stock_bajo">Stock Bajo</option>
                                <option value="venta_importante">Ventas Importantes</option>
                                <option value="cliente_nuevo">Clientes Nuevos</option>
                                <option value="comisiones_vencidas">Comisiones Vencidas</option>
                                <option value="metas_incumplidas">Metas Incumplidas</option>
                                <option value="objetivo_alcanzado">Objetivos Alcanzados</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filtro-prioridad" onchange="aplicarFiltros()">
                                <option value="">Todas las prioridades</option>
                                <option value="alta">Alta</option>
                                <option value="media">Media</option>
                                <option value="baja">Baja</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filtro-estado" onchange="aplicarFiltros()">
                                <option value="">Todos los estados</option>
                                <option value="no_leida">No leídas</option>
                                <option value="leida">Leídas</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary" onclick="aplicarFiltros()">
                                <i class="bi bi-funnel me-1"></i>
                                Aplicar Filtros
                            </button>
                            <button class="btn btn-outline-secondary ms-2" onclick="limpiarFiltros()">
                                <i class="bi bi-x-circle me-1"></i>
                                Limpiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Notificaciones -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Lista de Notificaciones</h5>
                </div>
                <div class="card-body">
                    <div class="notifications-container" id="notifications-container">
                        @foreach($notificaciones as $notificacion)
                        <div class="notification-item {{ $notificacion['leida'] ? 'read' : 'unread' }}" 
                             data-id="{{ $notificacion['id'] }}"
                             data-tipo="{{ $notificacion['tipo'] }}" 
                             data-prioridad="{{ $notificacion['prioridad'] }}"
                             data-estado="{{ $notificacion['leida'] ? 'leida' : 'no_leida' }}">
                            <div class="notification-content">
                                <div class="notification-icon">
                                    @switch($notificacion['tipo'])
                                        @case('stock_critico')
                                            <i class="bi bi-exclamation-circle text-danger"></i>
                                            @break
                                        @case('stock_bajo')
                                            <i class="bi bi-exclamation-triangle text-warning"></i>
                                            @break
                                        @case('venta_importante')
                                            <i class="bi bi-cash-coin text-success"></i>
                                            @break
                                        @case('cliente_nuevo')
                                            <i class="bi bi-person-plus text-info"></i>
                                            @break
                                        @case('comisiones_vencidas')
                                            <i class="bi bi-clock-history text-danger"></i>
                                            @break
                                        @case('metas_incumplidas')
                                            <i class="bi bi-graph-down-arrow text-warning"></i>
                                            @break
                                        @case('objetivo_alcanzado')
                                            <i class="bi bi-trophy text-primary"></i>
                                            @break
                                        @default
                                            <i class="bi bi-info-circle text-secondary"></i>
                                    @endswitch
                                </div>
                                <div class="notification-details">
                                    <div class="notification-header">
                                        <h6 class="notification-title">{{ $notificacion['titulo'] }}</h6>
                                        <div class="notification-meta">
                                            <span class="badge badge-{{ $notificacion['prioridad'] }}">
                                                {{ ucfirst($notificacion['prioridad']) }}
                                            </span>
                                            <span class="notification-time">
                                                {{ $notificacion['fecha']->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                    <p class="notification-message">{{ $notificacion['mensaje'] }}</p>
                                    @if(isset($notificacion['accion']))
                                    <div class="notification-actions">
                                        <a href="{{ $notificacion['accion']['url'] }}" class="btn btn-sm btn-outline-primary">
                                            {{ $notificacion['accion']['texto'] }}
                                        </a>
                                        @if($notificacion['leida'])
                                            <button class="btn btn-sm btn-success" disabled>
                                                <i class="bi bi-check-circle"></i>
                                                Leída
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary" onclick="marcarComoLeida('{{ $notificacion['id'] }}')">
                                                <i class="bi bi-check"></i>
                                                Marcar como leída
                                            </button>
                                        @endif
                                    </div>
                                    @else
                                    <div class="notification-actions">
                                        @if($notificacion['leida'])
                                            <button class="btn btn-sm btn-success" disabled>
                                                <i class="bi bi-check-circle"></i>
                                                Leída
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary" onclick="marcarComoLeida('{{ $notificacion['id'] }}')">
                                                <i class="bi bi-check"></i>
                                                Marcar como leída
                                            </button>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.notification-item {
    border-left: 4px solid transparent;
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.notification-item.unread {
    background-color: #f8f9fa;
    border-left-color: #007bff;
}

.notification-item.read {
    background-color: #ffffff;
    border-left-color: #e9ecef;
    opacity: 0.8;
}

.notification-content {
    display: flex;
    align-items: flex-start;
}

.notification-icon {
    font-size: 1.5rem;
    margin-right: 1rem;
    margin-top: 0.25rem;
}

.notification-details {
    flex: 1;
}

.notification-header {
    display: flex;
    justify-content: between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.notification-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    flex: 1;
}

.notification-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.notification-time {
    font-size: 0.85rem;
    color: #6c757d;
}

.notification-message {
    color: #495057;
    font-size: 0.9rem;
    margin-bottom: 0.75rem;
}

.notification-actions {
    display: flex;
    gap: 0.5rem;
}

.badge-alta {
    background-color: #dc3545;
    color: white;
}

.badge-media {
    background-color: #ffc107;
    color: #212529;
}

.badge-baja {
    background-color: #6c757d;
    color: white;
}

.notifications-container {
    max-height: 600px;
    overflow-y: auto;
}

.notification-item:hover {
    background-color: #f1f3f4;
    cursor: pointer;
}
</style>

<script>
function marcarComoLeida(id) {
    console.log('Marcando como leída la notificación:', id);
    
    fetch(`/api/notificaciones/marcar-leida/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        console.log('Respuesta recibida:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Datos recibidos:', data);
        if (data.success) {
            const notification = document.querySelector(`[data-id="${id}"]`);
            if (notification) {
                notification.classList.remove('unread');
                notification.classList.add('read');
                notification.setAttribute('data-estado', 'leida');
                
                // Actualizar el botón
                const button = notification.querySelector('.notification-actions button:last-child');
                if (button) {
                    button.innerHTML = '<i class="bi bi-check-circle"></i> Leída';
                    button.disabled = true;
                    button.classList.add('btn-success');
                    button.classList.remove('btn-outline-secondary');
                }
                
                console.log('Notificación marcada como leída exitosamente');
            }
            actualizarContadores();
        } else {
            console.error('Error en respuesta:', data.message);
            alert('Error al marcar la notificación como leída');
        }
    })
    .catch(error => {
        console.error('Error de red:', error);
        alert('Error de conexión al marcar la notificación: ' + error.message);
    });
}

function marcarTodasComoLeidas() {
    console.log('Marcando todas las notificaciones como leídas');
    
    fetch('/api/notificaciones/marcar-todas-leidas', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => {
        console.log('Respuesta recibida:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Datos recibidos:', data);
        if (data.success) {
            // Marcar todas las notificaciones visualmente
            const notifications = document.querySelectorAll('.notification-item');
            notifications.forEach(notification => {
                notification.classList.remove('unread');
                notification.classList.add('read');
                notification.setAttribute('data-estado', 'leida');
                
                // Actualizar el botón (buscar el botón correcto)
                const actionButton = notification.querySelector('.notification-actions button:last-child');
                if (actionButton && !actionButton.disabled) {
                    actionButton.innerHTML = '<i class="bi bi-check-circle"></i> Leída';
                    actionButton.disabled = true;
                    actionButton.classList.add('btn-success');
                    actionButton.classList.remove('btn-outline-secondary');
                }
            });
            
            actualizarContadores();
            alert(`${data.cantidad} notificaciones marcadas como leídas`);
        } else {
            console.error('Error en respuesta:', data.message);
            alert('Error al marcar las notificaciones como leídas');
        }
    })
    .catch(error => {
        console.error('Error de red:', error);
        alert('Error de conexión al marcar las notificaciones');
    });
}

function aplicarFiltros() {
    const tipo = document.getElementById('filtro-tipo').value;
    const prioridad = document.getElementById('filtro-prioridad').value;
    const estado = document.getElementById('filtro-estado').value;
    
    // Guardar filtros en localStorage
    localStorage.setItem('notificaciones_filtro_tipo', tipo);
    localStorage.setItem('notificaciones_filtro_prioridad', prioridad);
    localStorage.setItem('notificaciones_filtro_estado', estado);
    
    // Aplicar estilos visuales a filtros activos
    document.getElementById('filtro-tipo').classList.toggle('filter-active', tipo !== '');
    document.getElementById('filtro-prioridad').classList.toggle('filter-active', prioridad !== '');
    document.getElementById('filtro-estado').classList.toggle('filter-active', estado !== '');
    
    const notificaciones = document.querySelectorAll('.notification-item');
    let visibles = 0;
    
    notificaciones.forEach(item => {
        let mostrar = true;
        
        if (tipo && item.dataset.tipo !== tipo) mostrar = false;
        if (prioridad && item.dataset.prioridad !== prioridad) mostrar = false;
        if (estado && item.dataset.estado !== estado) mostrar = false;
        
        if (mostrar) {
            item.style.display = 'block';
            visibles++;
        } else {
            item.style.display = 'none';
        }
    });
    
    // Mostrar contador de resultados
    console.log(`Filtros aplicados: ${visibles} notificaciones visibles`);
    
    // Actualizar indicador visual si no hay resultados
    actualizarIndicadorResultados(visibles);
}

function limpiarFiltros() {
    // Limpiar selecciones
    document.getElementById('filtro-tipo').value = '';
    document.getElementById('filtro-prioridad').value = '';
    document.getElementById('filtro-estado').value = '';
    
    // Remover estilos visuales de filtros activos
    document.getElementById('filtro-tipo').classList.remove('filter-active');
    document.getElementById('filtro-prioridad').classList.remove('filter-active');
    document.getElementById('filtro-estado').classList.remove('filter-active');
    
    // Limpiar localStorage
    localStorage.removeItem('notificaciones_filtro_tipo');
    localStorage.removeItem('notificaciones_filtro_prioridad');
    localStorage.removeItem('notificaciones_filtro_estado');
    
    // Mostrar todas las notificaciones
    const notificaciones = document.querySelectorAll('.notification-item');
    notificaciones.forEach(item => {
        item.style.display = 'block';
    });
    
    // Actualizar indicador
    actualizarIndicadorResultados(notificaciones.length);
}

function cargarFiltrosGuardados() {
    // Restaurar filtros desde localStorage
    const tipoGuardado = localStorage.getItem('notificaciones_filtro_tipo');
    const prioridadGuardada = localStorage.getItem('notificaciones_filtro_prioridad');
    const estadoGuardado = localStorage.getItem('notificaciones_filtro_estado');
    
    if (tipoGuardado) {
        document.getElementById('filtro-tipo').value = tipoGuardado;
        document.getElementById('filtro-tipo').classList.add('filter-active');
    }
    if (prioridadGuardada) {
        document.getElementById('filtro-prioridad').value = prioridadGuardada;
        document.getElementById('filtro-prioridad').classList.add('filter-active');
    }
    if (estadoGuardado) {
        document.getElementById('filtro-estado').value = estadoGuardado;
        document.getElementById('filtro-estado').classList.add('filter-active');
    }
    
    // Aplicar filtros si hay alguno guardado
    if (tipoGuardado || prioridadGuardada || estadoGuardado) {
        aplicarFiltros();
    }
}

function actualizarIndicadorResultados(cantidad) {
    // Buscar o crear indicador de resultados
    let indicador = document.getElementById('indicador-resultados');
    if (!indicador) {
        indicador = document.createElement('div');
        indicador.id = 'indicador-resultados';
        indicador.className = 'alert alert-info mt-2';
        document.querySelector('#notifications-container').parentNode.insertBefore(indicador, document.querySelector('#notifications-container'));
    }
    
    if (cantidad === 0) {
        indicador.innerHTML = '<i class="bi bi-info-circle me-2"></i>No se encontraron notificaciones con los filtros aplicados.';
        indicador.className = 'alert alert-warning mt-2';
        indicador.style.display = 'block';
    } else {
        const total = document.querySelectorAll('.notification-item').length;
        if (cantidad < total) {
            indicador.innerHTML = `<i class="bi bi-funnel me-2"></i>Mostrando ${cantidad} de ${total} notificaciones.`;
            indicador.className = 'alert alert-info mt-2';
            indicador.style.display = 'block';
        } else {
            indicador.style.display = 'none';
        }
    }
}

function actualizarContadores() {
    // Actualizar contadores en tiempo real
    fetch('/api/notificaciones/resumen')
        .then(response => response.json())
        .then(data => {
            // Actualizar contadores en la página de notificaciones
            document.getElementById('total-notificaciones').textContent = data.total;
            document.getElementById('prioridad-alta').textContent = data.por_prioridad.alta;
            document.getElementById('stock-critico').textContent = data.por_tipo.stock_critico;
            document.getElementById('ventas-importantes').textContent = data.por_tipo.venta_importante;
            
            // Actualizar el badge del sidebar usando la función global
            if (typeof window.actualizarBadgeNotificaciones === 'function') {
                window.actualizarBadgeNotificaciones();
            } else {
                // Fallback: actualizar directamente
                const sidebarBadge = document.getElementById('notification-count');
                if (sidebarBadge) {
                    sidebarBadge.textContent = data.no_leidas;
                    console.log('Badge del sidebar actualizado a:', data.no_leidas);
                } else {
                    console.warn('Badge del sidebar no encontrado');
                }
            }
        })
        .catch(error => console.error('Error:', error));
}

function limpiarNotificacionesLeidas() {
    if (confirm('¿Estás seguro de que quieres resetear todas las notificaciones como no leídas?')) {
        fetch('/api/notificaciones/limpiar-leidas', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Recargar para mostrar todas como no leídas
            } else {
                alert('Error al limpiar las notificaciones');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión');
        });
    }
}

// Inicializar contadores cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    console.log('Página cargada, inicializando contadores y filtros...');
    actualizarContadores();
    cargarFiltrosGuardados();
});

// Actualizar notificaciones cada 60 segundos sin recargar la página
setInterval(function() {
    console.log('Actualizando notificaciones automáticamente...');
    actualizarContadores();
}, 60000);
</script>
@endsection
