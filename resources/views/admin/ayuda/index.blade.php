@extends('layouts.admin')

@section('title', 'Centro de Ayuda - Sistema Jireh')

@section('content')
<div class="container-fluid">
    <!-- Header del Centro de Ayuda -->
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <div class="page-title">
                    <h3><i class="bi bi-question-circle text-primary"></i> Centro de Ayuda</h3>
                    <p class="text-muted">Guías completas para usar el Sistema Jireh de manera eficiente</p>
                </div>
                <div class="page-header-actions">
                    <span class="badge bg-{{ $isAdmin ? 'danger' : 'success' }} fs-6">
                        <i class="bi bi-{{ $isAdmin ? 'shield-lock-fill' : 'person-badge-fill' }}"></i>
                        {{ $isAdmin ? 'Administrador' : 'Vendedor' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @php
        $ayudaTab = request('tab', 'primeros-pasos');
        $ayudaTabsValidas = ['primeros-pasos', 'modulos', 'faq', 'soporte'];
        if (!in_array($ayudaTab, $ayudaTabsValidas, true)) {
            $ayudaTab = 'primeros-pasos';
        }
    @endphp

    <div class="row mb-3">
        <div class="col-12">
            <label class="form-label" for="ayudaBuscar">Buscar en la ayuda</label>
            <input type="search" class="form-control" id="ayudaBuscar" placeholder="Ejemplo: departamento, cotización, comisión, contraseña">
            <div id="ayudaSinResultados" class="alert alert-warning mt-2 d-none mb-0">
                No hay coincidencias. Prueba con otra palabra, como departamento o cotización.
            </div>
        </div>
    </div>

    <!-- Tabs principales del Centro de Ayuda -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="ayudaTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $ayudaTab === 'primeros-pasos' ? 'active' : '' }}" id="primeros-pasos-tab" data-bs-toggle="tab" 
                                    data-bs-target="#primeros-pasos" type="button" role="tab">
                                <i class="bi bi-rocket-takeoff"></i> Primeros Pasos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $ayudaTab === 'modulos' ? 'active' : '' }}" id="modulos-tab" data-bs-toggle="tab" 
                                    data-bs-target="#modulos" type="button" role="tab">
                                <i class="bi bi-grid-3x3-gap"></i> Módulos
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $ayudaTab === 'faq' ? 'active' : '' }}" id="faq-tab" data-bs-toggle="tab" 
                                    data-bs-target="#faq" type="button" role="tab">
                                <i class="bi bi-question-circle"></i> FAQ
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $ayudaTab === 'soporte' ? 'active' : '' }}" id="soporte-tab" data-bs-toggle="tab" 
                                    data-bs-target="#soporte" type="button" role="tab">
                                <i class="bi bi-headset"></i> Soporte
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content" id="ayudaTabsContent">
                        <!-- Tab 1: Primeros Pasos -->
                        <div class="tab-pane fade {{ $ayudaTab === 'primeros-pasos' ? 'show active' : '' }}" id="primeros-pasos" role="tabpanel">
                            @include('admin.ayuda.sections.primeros-pasos')
                        </div>

                        <!-- Tab 2: Módulos -->
                        <div class="tab-pane fade {{ $ayudaTab === 'modulos' ? 'show active' : '' }}" id="modulos" role="tabpanel">
                            @include('admin.ayuda.sections.modulos')
                        </div>

                        <!-- Tab 3: FAQ -->
                        <div class="tab-pane fade {{ $ayudaTab === 'faq' ? 'show active' : '' }}" id="faq" role="tabpanel">
                            @include('admin.ayuda.sections.faq')
                        </div>

                        <!-- Tab 4: Soporte -->
                        <div class="tab-pane fade {{ $ayudaTab === 'soporte' ? 'show active' : '' }}" id="soporte" role="tabpanel">
                            @include('admin.ayuda.sections.soporte')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.help-section {
    margin-bottom: 2rem;
}

.help-card {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
}

.help-card:hover {
    border-color: #007bff;
    box-shadow: 0 4px 8px rgba(0,123,255,0.1);
}

.step-number {
    width: 30px;
    height: 30px;
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    flex-shrink: 0;
}

.config-flow {
    position: relative;
}

.config-flow::after {
    content: '';
    position: absolute;
    top: 40px;
    left: 15px;
    width: 2px;
    height: calc(100% - 60px);
    background: linear-gradient(to bottom, #007bff, #28a745);
    z-index: 1;
}

.config-step {
    position: relative;
    z-index: 2;
    background: white;
    padding: 1rem;
    margin-left: 45px;
    border-left: 3px solid #e9ecef;
    margin-bottom: 1rem;
}

.config-step.completed {
    border-left-color: #28a745;
}

.config-step.active {
    border-left-color: #007bff;
    background: #f8f9fa;
}

.accordion-button:not(.collapsed) {
    background-color: #e7f1ff;
    color: #0069d9;
}

.badge-role {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
}

.quick-access {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.quick-access-item {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 1rem;
    text-align: center;
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
}

.quick-access-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    text-decoration: none;
    color: inherit;
}

.module-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    color: #007bff;
}

@media (max-width: 768px) {
    .config-flow::after {
        display: none;
    }
    
    .config-step {
        margin-left: 0;
        border-left: none;
        border-top: 3px solid #e9ecef;
        padding-top: 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    var pestanasValidas = ['primeros-pasos', 'modulos', 'faq', 'soporte'];

    function abrirPestana(id) {
        var boton = document.getElementById(id + '-tab');
        if (!boton || typeof bootstrap === 'undefined') {
            return;
        }
        bootstrap.Tab.getOrCreateInstance(boton).show();
    }

    function abrirAcordeon(target) {
        var collapse = target.classList.contains('accordion-collapse')
            ? target
            : (target.querySelector('.accordion-collapse') || target.closest('.accordion-collapse'));
        if (collapse && typeof bootstrap !== 'undefined') {
            bootstrap.Collapse.getOrCreateInstance(collapse, { toggle: false }).show();
        }
    }

    var hash = (window.location.hash || '').replace('#', '');
    if (pestanasValidas.indexOf(hash) !== -1) {
        abrirPestana(hash);
    }

    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var id = this.getAttribute('href');
            if (!id || id === '#') {
                return;
            }
            var target = document.querySelector(id);
            if (!target) {
                return;
            }
            e.preventDefault();
            var pane = target.closest('.tab-pane');
            if (pane) {
                abrirPestana(pane.id);
            }
            abrirAcordeon(target);
            setTimeout(function () {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }, 150);
        });
    });

    var buscador = document.getElementById('ayudaBuscar');
    var aviso = document.getElementById('ayudaSinResultados');
    if (buscador) {
        buscador.addEventListener('input', function () {
            var consulta = buscador.value.trim().toLowerCase();
            var coincidencias = 0;
            var primera = null;

            document.querySelectorAll('#ayudaTabsContent .accordion-item').forEach(function (item) {
                var coincide = consulta === '' || item.innerText.toLowerCase().indexOf(consulta) !== -1;
                item.classList.toggle('d-none', !coincide);
                if (coincide) {
                    coincidencias++;
                    if (!primera) {
                        primera = item;
                    }
                    if (consulta !== '') {
                        abrirAcordeon(item);
                    }
                }
            });

            if (consulta !== '' && primera) {
                var pane = primera.closest('.tab-pane');
                if (pane) {
                    abrirPestana(pane.id);
                }
            }

            if (aviso) {
                aviso.classList.toggle('d-none', coincidencias > 0 || consulta === '');
            }
        });
    }
});
</script>
@endsection