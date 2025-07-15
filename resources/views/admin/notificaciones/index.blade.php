@extends('layouts.admin')

@section('content')
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
                            <select class="form-select" id="filtro-tipo">
                                <option value="">Todos los tipos</option>
                                <option value="stock_critico">Stock Crítico</option>
                                <option value="stock_bajo">Stock Bajo</option>
                                <option value="venta_importante">Ventas Importantes</option>
                                <option value="cliente_nuevo">Clientes Nuevos</option>
                                <option value="objetivo_alcanzado">Objetivos</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filtro-prioridad">
                                <option value="">Todas las prioridades</option>
                                <option value="alta">Alta</option>
                                <option value="media">Media</option>
                                <option value="baja">Baja</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filtro-estado">
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
                                        <button class="btn btn-sm btn-outline-secondary" onclick="marcarComoLeida('{{ $notificacion['id'] }}')">
                                            <i class="bi bi-check"></i>
                                            Marcar como leída
                                        </button>
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
    fetch(`/api/notificaciones/marcar-leida/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const notification = document.querySelector(`[data-id="${id}"]`);
            if (notification) {
                notification.classList.remove('unread');
                notification.classList.add('read');
            }
            actualizarContadores();
        }
    })
    .catch(error => console.error('Error:', error));
}

function marcarTodasComoLeidas() {
    fetch('/api/notificaciones/marcar-todas-leidas', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelectorAll('.notification-item.unread').forEach(item => {
                item.classList.remove('unread');
                item.classList.add('read');
            });
            actualizarContadores();
        }
    })
    .catch(error => console.error('Error:', error));
}

function aplicarFiltros() {
    const tipo = document.getElementById('filtro-tipo').value;
    const prioridad = document.getElementById('filtro-prioridad').value;
    const estado = document.getElementById('filtro-estado').value;
    
    const notificaciones = document.querySelectorAll('.notification-item');
    
    notificaciones.forEach(item => {
        let mostrar = true;
        
        if (tipo && item.dataset.tipo !== tipo) mostrar = false;
        if (prioridad && item.dataset.prioridad !== prioridad) mostrar = false;
        if (estado && item.dataset.estado !== estado) mostrar = false;
        
        item.style.display = mostrar ? 'block' : 'none';
    });
}

function actualizarContadores() {
    // Actualizar contadores en tiempo real
    fetch('/api/notificaciones/resumen')
        .then(response => response.json())
        .then(data => {
            document.getElementById('total-notificaciones').textContent = data.total;
            document.getElementById('prioridad-alta').textContent = data.por_prioridad.alta;
            document.getElementById('stock-critico').textContent = data.por_tipo.stock_critico;
            document.getElementById('ventas-importantes').textContent = data.por_tipo.venta_importante;
        })
        .catch(error => console.error('Error:', error));
}

// Actualizar notificaciones cada 30 segundos
setInterval(function() {
    location.reload();
}, 30000);
</script>
@endsection
