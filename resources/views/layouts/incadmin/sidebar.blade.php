<!-- Sidebar wrapper start -->
<nav class="sidebar-wrapper" id="sidebar">
    <!-- Sidebar header starts -->
    <div class="sidebar-header">
        {{-- <div class="sidebar-logo">
            <a href="{{ url('/dashboard') }}">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" class="img-fluid" width="120">
            </a>
        </div> --}}
        {{-- <br>
        <div class="sidebar-toggle d-none d-md-flex">
            <button type="button" id="sidebarCollapse" class="btn btn-sm btn-light">
                <i class="bi bi-arrow-left-right"></i>
            </button>
        </div> --}}
    </div>
    <!-- Sidebar header ends -->

    <!-- Sidebar menu starts -->
    <div class="sidebar-menu">
        <div class="sidebarMenuScroll">
            <ul>
                {{-- @if (Auth::user()->role_as == 0) --}}
                <li class="active">
                    <a href="{{ url('show-user/'.Auth::user()->id) }}">
                        <span class="avatar">
                            @if (Auth::user()->fotografia != null)
                                <img src="{{ asset('assets/imgs/users/'.Auth::user()->fotografia) }}" alt="Usuario" class="img-thumbnail rounded-4 border-success m-2 img-fluid" style="height: 40px;"/>
                            @else
                                <img src="{{ asset('assets/imgs/users/usericon4.png') }}" alt="Usuario" class="img-thumbnail rounded-4 border-success m-2 img-fluid" style="height: 40px;"/>
                            @endif
                            <span class="status online"></span>
                        </span>
                        @php
                            $usuario = Auth::user()->name;
                            $nombre = explode(' ', trim($usuario));
                        @endphp
                        <span class="menu-text"><u><strong> {{ ucwords($nombre[0]) }}</strong></u></span>
                    </a>
                </li>
                <li class="menu-separator">
                    <hr>
                </li>
                {{-- @endif --}}

                {{-- <li class="{{ Request::is('dashboard') ? 'active-page-link':''  }}">
                    <a href="{{ url('/dashboard') }}">
                        <i class="bi bi-house"></i>
                        <span class="menu-text">Panel de Control</span>
                    </a>
                </li> --}}
                
                <!-- Dashboard Mejorado -->
                <li class="{{ Request::is('dashboard-pro') ? 'active-page-link':''  }}">
                    <a href="{{ url('/dashboard-pro') }}">
                        <i class="bi bi-speedometer2"></i>
                        <span class="menu-text">Dashboard</span>
                        {{-- <span class="badge bg-primary-transparent ms-auto">Nuevo</span> --}}
                    </a>
                </li>
                
                <!-- Notificaciones (solo para admins y otros, no vendedores) -->
                @if(Auth::user()->role_as != 1)
                <li class="{{ Request::is('notificaciones') ? 'active-page-link':''  }}">
                    <a href="{{ url('/notificaciones') }}">
                        <i class="bi bi-bell"></i>
                        <span class="menu-text">Notificaciones</span>
                        <span class="badge bg-danger text-white ms-auto" id="notification-count">0</span>
                    </a>
                </li>
                @endif

                <!-- Gestión de Clientes y Vehículos -->
                <li class="menu-category">Gestión de Clientes</li>
                <li class="{{ Request::is('clientes','show-cliente/*','add-cliente','edit-cliente/*') ? 'active-page-link':''  }}">
                    <a href="{{ url('clientes') }}">
                        <i class="bi bi-person-video2"></i>
                        <span class="menu-text">Clientes</span>
                    </a>
                </li>
                <li class="{{ Request::is('vehiculos','show-vehiculo/*','add-vehiculo','edit-vehiculo/*') ? 'active-page-link':''  }}">
                    <a href="{{ url('vehiculos') }}">
                        <i class="bi bi-car-front"></i>
                        <span class="menu-text">Vehículos</span>
                    </a>
                </li>

                <!-- Almacén -->
                @if(Auth::user()->role_as != 1)
                    <li class="menu-category">Inventario y Catálogos</li>
                    <li class="sidebar-dropdown">
                        <a href="#" class="{{ Request::is('categorias','articulos','unidades','tipo-comisiones', 'show-categoria/*', 'show-articulo/*', 'show-unidad/*', 'show-tipo-comision/*') ? 'active-dropdown':''  }}" title="Inventario y Catálogos">
                            <i class="bi bi-boxes"></i>
                            <span class="menu-text">Almacén</span>
                            <i class="bi bi-chevron-down menu-arrow"></i>
                        </a>
                        <div class="sidebar-submenu">
                            <ul>
                                <li class="{{ Request::is('categorias','show-categoria/*','add-categoria','edit-categoria/*') ? 'active-page-link':''  }}">
                                    <a href="{{ url('categorias') }}"><i class="bi bi-diagram-3"></i> Categorías </a>
                                </li>
                                <li class="{{ Request::is('articulos','show-articulo/*','add-articulo','edit-articulo/*') ? 'active-page-link':''  }}">
                                    <a href="{{ url('articulos') }}"><i class="bi bi-boxes"></i> Artículos y Servicios</a>
                                </li>
                                <li class="{{ Request::is('unidades','show-unidad/*','add-unidad','edit-unidad/*') ? 'active-page-link':''  }}">
                                    <a href="{{ url('unidades') }}"><i class="bi bi-rulers"></i> Unidades de Medida </a>
                                </li>
                                {{-- <li class="{{ Request::is('tipo-comisiones','show-tipo-comision/*','add-tipo-comision','edit-tipo-comision/*') ? 'active-page-link':''  }}">
                                    <a href="{{ url('tipo-comisiones') }}"><i class="bi bi-piggy-bank"></i> Tipos de Comisiones </a>
                                </li> --}}
                            </ul>
                        </div>
                    </li>
                @endif

                <!-- Trabajadores -->
                @if(Auth::user()->role_as != 1)
                <li class="menu-category">Gestión de Personal</li>
                <li class="sidebar-dropdown">
                    <a href="#" class="{{ Request::is('trabajadores','tipotrabajador','show-trabajador/*','edit-trabajador/*','add-trabajador','admin/pago-sueldo*') ? 'active-dropdown':''  }}" title="Gestión de Personal">
                        <i class="bi bi-people-fill"></i>
                        <span class="menu-text">Trabajadores</span>
                        <i class="bi bi-chevron-down menu-arrow"></i>
                    </a>
                    <div class="sidebar-submenu">
                        <ul>
                            <li class="{{ Request::is('trabajadores','show-trabajador/*','add-trabajador','edit-trabajador/*') ? 'active-page-link':''  }}">
                                <a href="{{ url('trabajadores') }}"><i class="bi bi-person-badge"></i> Trabajadores</a>
                            </li>
                            {{-- MÓDULO OCULTO: Los tipos de trabajador son fijos (Mecánico y Car Wash) --}}
                            {{-- <li class="{{ Request::is('tipo-trabajador','show-tipo-trabajador/*','add-tipo-trabajador','edit-tipo-trabajador/*') ? 'active-page-link':''  }}">
                                <a href="{{ url('tipo-trabajador') }}"><i class="bi bi-tags"></i> Tipos</a>
                            </li> --}}
                            <li class="{{ Request::is('admin/pago-sueldo*') ? 'active-page-link':''  }}">
                                <a href="{{ route('admin.pago-sueldo.index') }}"><i class="bi bi-wallet2"></i> Pagos de Sueldos</a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                <!-- Comisiones -->
                @if(Auth::user()->role_as != 1)
                <li class="menu-category">Sistema de Comisiones</li>
                <li class="sidebar-dropdown">
                    <a href="#" class="{{ Request::is('comisiones*') || Request::is('metas-ventas*') || Request::is('pagos_comisiones*') || Request::is('lotes-pago*') || Request::is('reportes/metas*') ? 'active-dropdown':''  }}" title="Sistema de Comisiones">
                        <i class="bi bi-currency-dollar"></i>
                        <span class="menu-text">Comisiones</span>
                        <i class="bi bi-chevron-down menu-arrow"></i>
                    </a>
                    <div class="sidebar-submenu">
                        <ul>
                            <li class="{{ Request::is('comisiones/dashboard') ? 'active-page-link':''  }}">
                                <a href="{{ route('comisiones.dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
                            </li>
                            
                            <li class="{{ Request::is('comisiones/gestion') ? 'active-page-link':''  }}">
                                <a href="{{ route('comisiones.gestion') }}"><i class="bi bi-cash-coin"></i> Gestión y Pagos</a>
                            </li>
                            
                            <li class="{{ Request::is('lotes-pago*') ? 'active-page-link':''  }}">
                                <a href="{{ route('lotes-pago.index') }}"><i class="bi bi-file-earmark-plus"></i> Lotes de Pago</a>
                            </li>
                            
                            <li class="{{ Request::is('metas-ventas*') ? 'active-page-link':''  }}">
                                <a href="{{ route('metas-ventas.index') }}"><i class="bi bi-bullseye"></i> Metas de Ventas</a>
                            </li>
                            
                            <li class="{{ Request::is('reportes/metas*') ? 'active-page-link':''  }}">
                                <a href="{{ route('reportes.metas.index') }}"><i class="bi bi-graph-up-arrow"></i> Reporte de Metas</a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                <!-- Compras -->
                @if(Auth::user()->role_as != 1)
                    <li class="menu-category">Operaciones</li>
                    <li class="sidebar-dropdown">
                        <a href="#" class="{{ Request::is('ingresos','proveedores','show-ingreso/*','show-proveedor/*') ? 'active-dropdown':''  }}" title="Gestión de Compras">
                            <i class="bi bi-cart4"></i>
                            <span class="menu-text">Compras</span>
                            <i class="bi bi-chevron-down menu-arrow"></i>
                        </a>
                        <div class="sidebar-submenu">
                            <ul>
                                <li class="{{ Request::is('ingresos','show-ingreso/*','add-ingreso','edit-ingreso/*') ? 'active-page-link':''  }}">
                                    <a href="{{ url('ingresos') }}"><i class="bi bi-cart-plus"></i> Ingresos</a>
                                </li>
                                <li class="{{ Request::is('proveedores','show-proveedor/*','add-proveedor','edit-proveedor/*') ? 'active-page-link':''  }}">
                                    <a href="{{ url('proveedores') }}"><i class="bi bi-person-badge-fill"></i> Proveedores</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                <!-- Ventas -->
                <li class="sidebar-dropdown">
                    <a href="#" class="{{ Request::is('ventas','reportearticulos','inventario','descuentos','show-venta/*','show-descuento/*','admin/auditoria*') ? 'active-dropdown':''  }}" title="Gestión de Ventas">
                        <i class="bi bi-cash-stack"></i>
                        <span class="menu-text">Ventas</span>
                        <i class="bi bi-chevron-down menu-arrow"></i>
                    </a>
                    <div class="sidebar-submenu">
                        <ul>
                            <li class="{{ Request::is('ventas','show-venta/*','add-venta','edit-venta/*') ? 'active-page-link':''  }}">
                                <a href="{{ url('ventas') }}"><i class="bi bi-cash-stack"></i> Ventas</a>
                            </li>
                            @if(Auth::user()->role_as != 1)
                                <li class="{{ Request::is('reportearticulos') ? 'active-page-link':''  }}">
                                    <a href="{{ url('reportearticulos') }}"><i class="bi bi-bar-chart-line"></i> Artículos Vendidos</a>
                                </li>
                            @endif
                            <li class="{{ Request::is('inventario') ? 'active-page-link':''  }}">
                                <a href="{{ url('inventario') }}"><i class="bi bi-inboxes"></i> Inventario</a>
                            </li>
                            @if(Auth::user()->role_as != 1)
                            <li class="{{ Request::is('descuentos','show-descuento/*','add-descuento','edit-descuento/*') ? 'active-page-link':''  }}">
                                <a href="{{ url('descuentos') }}"><i class="bi bi-piggy-bank"></i> Descuentos</a>
                            </li>
                            <li class="{{ Request::is('admin/auditoria*') ? 'active-page-link':''  }}">
                                <a href="{{ url('admin/auditoria') }}"><i class="bi bi-shield-check"></i> Auditoría de Ventas</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>

                <!-- Administración -->
                @if(Auth::user()->role_as != 1)
                <li class="menu-category">Administración</li>
                <li class="sidebar-dropdown">
                    <a href="#" class="{{ Request::is('users','show-user/*','add-user','edit-user/*') ? 'active-dropdown':''  }}" title="Administración y Seguridad">
                        <i class="bi bi-shield-shaded"></i>
                        <span class="menu-text">Seguridad</span>
                        <i class="bi bi-chevron-down menu-arrow"></i>
                    </a>
                    <div class="sidebar-submenu">
                        <ul>
                            <li class="{{ Request::is('users','show-user/*','add-user','edit-user/*') ? 'active-page-link':''  }}">
                                <a href="{{ url('users') }}"><i class="bi bi-people"></i> Usuarios</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="{{ Request::is('config') ? 'active-page-link':''  }}">
                    <a href="{{ url('config') }}">
                        <i class="bi bi-gear"></i>
                        <span class="menu-text">Configuración</span>
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </div>
    <!-- Sidebar menu ends -->

    <!-- Sidebar footer starts -->
    <div class="sidebar-footer">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <span class="small text-muted"><a class="text-white" href="https://szystems.com" target="_blank" rel="noopener noreferrer"><b>Szystems v1.6.0</b></a></span>
            </div>
            <div>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Cerrar sesión">
                    <i class="bi bi-power"></i>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
    <!-- Sidebar footer ends -->
</nav>
<!-- Sidebar wrapper end -->

<!-- JavaScript mejorado para el sidebar collapse -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarCollapse = document.getElementById('sidebarCollapse');
        const sidebar = document.getElementById('sidebar');
        const mainContainer = document.querySelector('.main-container');
        
        if (sidebarCollapse && sidebar) {
            sidebarCollapse.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                
                // Ajustar el padding del contenido principal
                if (sidebar.classList.contains('collapsed')) {
                    if (mainContainer) {
                        mainContainer.style.paddingLeft = '70px';
                    }
                    localStorage.setItem('sidebar-collapsed', 'true');
                } else {
                    if (mainContainer) {
                        mainContainer.style.paddingLeft = '250px';
                    }
                    localStorage.setItem('sidebar-collapsed', 'false');
                }
            });
        }

        // Restaurar el estado del sidebar
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            if (sidebar) {
                sidebar.classList.add('collapsed');
                if (mainContainer) {
                    mainContainer.style.paddingLeft = '70px';
                }
            }
        }
        
        // Manejar el responsive behavior
        function handleResponsive() {
            if (window.innerWidth <= 1199) {
                if (mainContainer) {
                    mainContainer.style.paddingLeft = '0px';
                }
            } else {
                // Restaurar el estado correcto en desktop
                if (sidebar && sidebar.classList.contains('collapsed')) {
                    if (mainContainer) {
                        mainContainer.style.paddingLeft = '70px';
                    }
                } else {
                    if (mainContainer) {
                        mainContainer.style.paddingLeft = '250px';
                    }
                }
            }
        }
        
        // Ejecutar al cargar y al redimensionar
        handleResponsive();
        window.addEventListener('resize', handleResponsive);
        
        // Manejar dropdowns del sidebar
        const dropdownLinks = document.querySelectorAll('.sidebar-dropdown > a');
        dropdownLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const parentLi = this.parentElement;
                const isActive = parentLi.classList.contains('active-dropdown');
                
                // Cerrar todos los otros dropdowns
                document.querySelectorAll('.sidebar-dropdown').forEach(dropdown => {
                    if (dropdown !== parentLi) {
                        dropdown.classList.remove('active-dropdown');
                    }
                });
                
                // Toggle del dropdown actual
                if (isActive) {
                    parentLi.classList.remove('active-dropdown');
                } else {
                    parentLi.classList.add('active-dropdown');
                }
            });
        });
    });
</script>

<!-- Estilos mejorados para el sidebar que mantienen el layout original -->
<style>
    /* Mejoras de categorías y separadores sin interferir con el layout principal */
    .sidebar-wrapper .menu-category {
        font-size: 0.75rem;
        color: #ffffff;
        font-weight: 700;
        padding: 15px 20px 8px;
        text-transform: uppercase;
        margin: 10px 0 5px 0;
        letter-spacing: 0.5px;
        border-bottom: 2px solid rgba(255, 255, 255, 0.3);
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
        border-radius: 4px 4px 0 0;
        position: relative;
    }
    
    .sidebar-wrapper .menu-category::before {
        content: '';
        position: absolute;
        left: 0;
        bottom: -2px;
        width: 30px;
        height: 2px;
        background: linear-gradient(90deg, #ffd700, #ffaa00);
        border-radius: 1px;
        transition: width 0.3s ease;
    }
    
    .sidebar-wrapper .menu-category:hover::before {
        width: 50px;
    }
    
    .sidebar-wrapper .menu-category:hover {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0.08));
        transition: all 0.3s ease;
    }
    
    .sidebar-wrapper .menu-separator {
        padding: 8px 20px;
        margin: 8px 0;
    }
    
    .sidebar-wrapper .menu-separator hr {
        border: none;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        margin: 5px 0;
        position: relative;
    }
    
    .sidebar-wrapper .menu-separator hr::before {
        content: '';
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 4px;
        height: 4px;
        background: rgba(255, 255, 255, 0.6);
        border-radius: 50%;
    }
    
    /* Mejoras en el scroll del menú manteniendo la estructura original */
    .sidebar-wrapper .sidebar-menu {
        position: relative;
        /* Mantener la estructura original del template */
    }
    
    .sidebar-wrapper .sidebarMenuScroll {
        max-height: calc(100vh - 200px); /* Ajustar según el header y footer */
        overflow-y: auto;
        overflow-x: hidden;
        padding-bottom: 70px; /* Espacio para el footer */
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,0.3) transparent;
    }
    
    /* Estilo personalizado del scrollbar para webkit */
    .sidebar-wrapper .sidebarMenuScroll::-webkit-scrollbar {
        width: 6px;
    }
    
    .sidebar-wrapper .sidebarMenuScroll::-webkit-scrollbar-track {
        background: transparent;
    }
    
    .sidebar-wrapper .sidebarMenuScroll::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.3);
        border-radius: 3px;
    }
    
    .sidebar-wrapper .sidebarMenuScroll::-webkit-scrollbar-thumb:hover {
        background: rgba(255,255,255,0.5);
    }
    
    /* Footer del sidebar sin interferir con el posicionamiento principal */
    .sidebar-wrapper .sidebar-footer {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 10px 15px;
        border-top: 1px solid rgba(255,255,255,0.1);
        background: #0f50ad;
        z-index: 10;
    }
    
    /* Funcionalidad de colapso mejorada */
    .sidebar-wrapper.collapsed {
        width: 70px !important;
    }
    
    .sidebar-wrapper.collapsed .menu-text,
    .sidebar-wrapper.collapsed .menu-category,
    .sidebar-wrapper.collapsed .menu-arrow,
    .sidebar-wrapper.collapsed .sidebar-submenu,
    .sidebar-wrapper.collapsed .sidebar-footer span {
        display: none;
    }
    
    /* Mostrar indicador visual para categorías cuando está colapsado */
    .sidebar-wrapper.collapsed .menu-category {
        display: block !important;
        width: 70px;
        height: 3px;
        background: linear-gradient(90deg, #ffd700, #ffaa00);
        margin: 15px 0 5px 0;
        padding: 0;
        border-radius: 0 2px 2px 0;
        text-indent: -9999px;
        overflow: hidden;
    }
    
    .sidebar-wrapper.collapsed .menu-category::before {
        display: none;
    }
    
    /* Ocultar chevrons cuando está colapsado */
    .sidebar-wrapper.collapsed .menu-arrow {
        display: none;
    }
    
    /* Mejorar tooltips para modo colapsado */
    .sidebar-wrapper.collapsed .sidebar-dropdown > a {
        position: relative;
    }
    
    .sidebar-wrapper.collapsed .sidebar-dropdown > a:hover::after {
        content: attr(title);
        position: absolute;
        left: 70px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0, 0, 0, 0.9);
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        white-space: nowrap;
        z-index: 1000;
        opacity: 0;
        animation: tooltipFadeIn 0.3s ease forwards;
    }
    
    @keyframes tooltipFadeIn {
        from { 
            opacity: 0; 
            transform: translateY(-50%) translateX(-10px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(-50%) translateX(0); 
        }
    }
    
    .sidebar-wrapper.collapsed .sidebar-toggle {
        text-align: center;
        width: 100%;
    }
    
    /* Ajustar el contenido principal cuando el sidebar está colapsado */
    .sidebar-wrapper.collapsed ~ .content-wrapper {
        margin-left: 70px !important;
        padding-left: 0 !important;
    }
    
    /* Rotación de iconos de dropdown mejorada */
    .sidebar-wrapper .sidebar-dropdown > a .menu-arrow {
        transition: all 0.3s ease;
        font-size: 0.8rem;
        margin-left: auto;
        opacity: 0.7;
    }
    
    .sidebar-wrapper .sidebar-dropdown.active-dropdown > a .menu-arrow,
    .sidebar-wrapper .sidebar-dropdown:hover > a .menu-arrow {
        transform: rotate(180deg);
        opacity: 1;
        color: #ffd700;
    }
    
    /* Mejorar el hover en los enlaces de dropdown */
    .sidebar-wrapper .sidebar-dropdown > a {
        transition: all 0.3s ease;
        position: relative;
    }
    
    .sidebar-wrapper .sidebar-dropdown > a:hover {
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
        transform: translateX(2px);
    }
    
    /* Animación del submenu */
    .sidebar-wrapper .sidebar-submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease, opacity 0.3s ease;
        opacity: 0;
    }
    
    .sidebar-wrapper .sidebar-dropdown.active-dropdown .sidebar-submenu {
        max-height: 500px;
        opacity: 1;
    }
    
    /* Efecto de cascada en los elementos del submenu */
    .sidebar-wrapper .sidebar-submenu ul li {
        opacity: 0;
        transform: translateX(-10px);
        transition: all 0.2s ease;
    }
    
    .sidebar-wrapper .sidebar-dropdown.active-dropdown .sidebar-submenu ul li {
        opacity: 1;
        transform: translateX(0);
    }
    
    .sidebar-wrapper .sidebar-dropdown.active-dropdown .sidebar-submenu ul li:nth-child(1) { transition-delay: 0.05s; }
    .sidebar-wrapper .sidebar-dropdown.active-dropdown .sidebar-submenu ul li:nth-child(2) { transition-delay: 0.1s; }
    .sidebar-wrapper .sidebar-dropdown.active-dropdown .sidebar-submenu ul li:nth-child(3) { transition-delay: 0.15s; }
    .sidebar-wrapper .sidebar-dropdown.active-dropdown .sidebar-submenu ul li:nth-child(4) { transition-delay: 0.2s; }
    .sidebar-wrapper .sidebar-dropdown.active-dropdown .sidebar-submenu ul li:nth-child(5) { transition-delay: 0.25s; }
    .sidebar-wrapper .sidebar-dropdown.active-dropdown .sidebar-submenu ul li:nth-child(6) { transition-delay: 0.3s; }
    
    /* Mejorar la visualización en dispositivos pequeños */
    @media (max-height: 600px) {
        .sidebar-wrapper .sidebarMenuScroll {
            max-height: calc(100vh - 150px);
            padding-bottom: 60px;
        }
        
        .sidebar-wrapper .sidebar-footer {
            padding: 8px 15px;
        }
    }
    
    /* Mejorar la separación entre categorías y elementos del menú */
    .sidebar-wrapper .menu-category + li {
        margin-top: 8px;
    }
    
    /* Espaciado adicional antes de una nueva categoría */
    .sidebar-wrapper li.menu-category:not(:first-child) {
        margin-top: 20px;
    }
    
    /* Asegurar que los elementos del menú no se corten */
    .sidebar-wrapper ul li {
        position: relative;
    }
    
    /* Ajustes para el layout principal */
    .main-container {
        transition: all 0.3s ease;
    }
    
    /* Corrección específica para cuando el sidebar está colapsado */
    .page-wrapper .main-container {
        transition: padding-left 0.3s ease;
    }
    
    .sidebar-wrapper.collapsed + .main-container {
        padding-left: 70px !important;
    }
    
    /* AJUSTES MÍNIMOS PARA SOLUCIONAR PROBLEMAS ESPECÍFICOS */
    
    /* 1. Evitar que el sidebar tape el navbar */
    .sidebar-wrapper {
        z-index: 1040 !important; /* Debajo del navbar */
    }
    
    /* 2. Asegurar que el navbar esté encima */
    .page-header {
        z-index: 1050 !important; /* Encima del sidebar */
        position: relative !important;
    }
    
    /* 3. En móvil, botón hamburguesa siempre accesible */
    @media (max-width: 991.98px) {
        .toggle-sidebar {
            z-index: 1060 !important; /* Encima de todo */
            position: relative !important;
        }
    }
</style>
