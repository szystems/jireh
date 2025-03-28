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

                <li class="{{ Request::is('dashboard') ? 'active-page-link':''  }}">
                    <a href="{{ url('/dashboard') }}">
                        <i class="bi bi-house"></i>
                        <span class="menu-text">Panel de Control</span>
                    </a>
                </li>

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
                        <a href="#" class="{{ Request::is('categorias','articulos','unidades','tipo-comisiones', 'show-categoria/*', 'show-articulo/*', 'show-unidad/*', 'show-tipo-comision/*') ? 'active-dropdown':''  }}">
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
                                <li class="{{ Request::is('tipo-comisiones','show-tipo-comision/*','add-tipo-comision','edit-tipo-comision/*') ? 'active-page-link':''  }}">
                                    <a href="{{ url('tipo-comisiones') }}"><i class="bi bi-piggy-bank"></i> Tipos de Comisiones </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif
                <!-- Compras -->
                @if(Auth::user()->role_as != 1)
                    <li class="menu-category">Operaciones</li>
                    <li class="sidebar-dropdown">
                        <a href="#" class="{{ Request::is('ingresos','proveedores','show-ingreso/*','show-proveedor/*') ? 'active-dropdown':''  }}">
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
                    <a href="#" class="{{ Request::is('ventas','reportearticulos','trabajadores','inventario','descuentos','show-venta/*','show-trabajador/*','show-descuento/*') ? 'active-dropdown':''  }}">
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
                            @if(Auth::user()->role_as != 1)
                            <li class="{{ Request::is('trabajadores','show-trabajador/*','add-trabajador','edit-trabajador/*') ? 'active-page-link':''  }}">
                                <a href="{{ url('trabajadores') }}"><i class="bi bi-people"></i> Trabajadores</a>
                            </li>
                            @endif
                            <li class="{{ Request::is('inventario') ? 'active-page-link':''  }}">
                                <a href="{{ url('inventario') }}"><i class="bi bi-inboxes"></i> Inventario</a>
                            </li>
                            @if(Auth::user()->role_as != 1)
                            <li class="{{ Request::is('descuentos','show-descuento/*','add-descuento','edit-descuento/*') ? 'active-page-link':''  }}">
                                <a href="{{ url('descuentos') }}"><i class="bi bi-piggy-bank"></i> Descuentos</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li>

                <!-- Administración -->
                @if(Auth::user()->role_as != 1)
                <li class="menu-category">Administración</li>
                <li class="sidebar-dropdown">
                    <a href="#" class="{{ Request::is('users','show-user/*','add-user','edit-user/*') ? 'active-dropdown':''  }}">
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
                <span class="small text-muted"><a href="https://szystems.com" target="_blank" rel="noopener noreferrer">Szystems v1.0.0</a></span>
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

<!-- JavaScript para el sidebar collapse -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarCollapse = document.getElementById('sidebarCollapse');
        if (sidebarCollapse) {
            sidebarCollapse.addEventListener('click', function() {
                document.getElementById('sidebar').classList.toggle('collapsed');
                // Guardar el estado en localStorage
                if (document.getElementById('sidebar').classList.contains('collapsed')) {
                    localStorage.setItem('sidebar-collapsed', 'true');
                } else {
                    localStorage.setItem('sidebar-collapsed', 'false');
                }
            });
        }

        // Restaurar el estado del sidebar
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            document.getElementById('sidebar').classList.add('collapsed');
        }
    });
</script>

<!-- Añadir estos estilos en su archivo CSS o en un bloque de estilo -->
<style>
    .sidebar-wrapper {
        transition: all 0.3s ease;
    }
    .sidebar-wrapper .menu-category {
        font-size: 0.8rem;
        color: #6c757d;
        font-weight: 600;
        padding: 12px 15px 5px;
        text-transform: uppercase;
    }
    .sidebar-wrapper .menu-separator {
        padding: 0 15px;
    }
    .sidebar-wrapper.collapsed {
        width: 70px;
    }
    .sidebar-wrapper.collapsed .menu-text,
    .sidebar-wrapper.collapsed .menu-category,
    .sidebar-wrapper.collapsed .menu-arrow,
    .sidebar-wrapper.collapsed .sidebar-submenu,
    .sidebar-wrapper.collapsed .sidebar-logo img,
    .sidebar-wrapper.collapsed .sidebar-footer span {
        display: none;
    }
    .sidebar-wrapper.collapsed .sidebar-toggle {
        text-align: center;
        width: 100%;
    }
    .sidebar-wrapper.collapsed + .content-wrapper {
        margin-left: 70px;
    }
    .sidebar-wrapper .sidebar-footer {
        padding: 10px 15px;
        border-top: 1px solid rgba(0,0,0,0.1);
        position: absolute;
        bottom: 0;
        width: 100%;
    }
    .sidebar-wrapper .active-dropdown i.menu-arrow {
        transform: rotate(180deg);
    }
</style>
