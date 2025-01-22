<!-- Sidebar wrapper start -->
<nav class="sidebar-wrapper">
    <!-- Sidebar menu starts -->
    <div class="sidebar-menu">
        <div class="sidebarMenuScroll">
            <ul>
                @if (Auth::user()->role_as == 0)
                <li class="active">
                    <a href="{{ url('show-user/'.Auth::user()->id) }}">
                        <span class="avatar">
                            @if (Auth::user()->fotografia != null)
                                <img src="{{ asset('assets/imgs/users/'.Auth::user()->fotografia) }}" alt="Doctores" class="img-thumbnail rounded-4 border-success m-2 img-fluid" style="height: 40px;"/>
                            @else
                                <img src="{{ asset('assets/imgs/users/usericon4.png') }}" alt="Doctores" class="img-thumbnail rounded-4 border-success m-2 img-fluid" style="height: 40px;"/>
                            @endif
                            <span class="status online"></span>
                        </span>
                        @php
                            $usuario = Auth::user()->name;
                            $nombre = explode(' ', trim($usuario));
                        @endphp
                        <span class="menu-text"><u><strong> {{ ucwords($nombre[0]) }}</strong></u></span>
                        {{-- @php
                            $hoy = Carbon\Carbon::now('America/Guatemala');
                            $hoy = $hoy->format('Y-m-d');
                            $cita_count = \App\Models\Cita::where('fecha_cita',$hoy)->where('estado','Confirmada')->where('doctor_id',Auth::user()->id)->count();


                        @endphp
                        <span class="badge red">Hoy {{ $cita_count }}</span> --}}
                    </a>
                </li>
                @endif

                <li class="{{ Request::is('dashboard') ? 'active-page-link':''  }}">
                    <a href="{{ url('/dashboard') }}">
                        <i class="bi bi-house"></i>
                        <span class="menu-text">Panel de Control</span>
                    </a>
                </li>
                {{-- <li>
                    <a href="https://www.jirehautomotriz.com/">
                        <i class="bi bi-globe"></i>
                        <span class="menu-text">Sitio Web</span>
                    </a>
                </li> --}}
                <li class="{{ Request::is('clientes','show-cliente/*','add-cliente','edit-cliente/*') ? 'active-page-link':''  }}">
                    <a href="{{ url('clientes') }}">
                        <i class="bi bi-person-video2"></i>
                        <span class="menu-text">Clientes</span>
                        {{-- @php
                            $hoy = Carbon\Carbon::now('America/Guatemala');
                            $hoy = $hoy->format('Y-m-d');
                            $cita_count = \App\Models\Cita::where('fecha_cita',$hoy)->where('estado','Confirmada')->count();


                        @endphp
                        <span class="badge green">Hoy {{ $cita_count }}</span> --}}
                    </a>
                </li>
                <li class="{{ Request::is('vehiculos','show-vehiculo/*','add-vehiculo','edit-vehiculo/*') ? 'active-page-link':''  }}">
                    <a href="{{ url('vehiculos') }}">
                        <i class="bi bi-car-front"></i>
                        <span class="menu-text">Vehículos</span>
                        {{-- <span class="badge green">15</span> --}}
                    </a>
                </li>
                {{-- <li class="{{ Request::is('inventario') ? 'active-page-link':''  }}">
                    <a href="{{ url('inventario') }}">
                        <i class="bi bi-inboxes"></i>
                        <span class="menu-text">Inventario</span>
                    </a>
                </li> --}}
                <li class="sidebar-dropdown">
                    <a href="#">
                        <i class="bi bi-boxes"></i>
                        <span class="menu-text">Almacén</span>
                        {{-- <span class="badge red">15</span> --}}
                    </a>
                    <div class="sidebar-submenu">
                        <ul>
                            <li class="{{ Request::is('categorias','show-categoria/*','add-categoria','edit-categoria/*') ? 'active-page-link':''  }}">
                                <a href="{{ url('categorias') }}"><i class="bi bi-diagram-3"></i></i> Categorías </a>
                            </li>
                        </ul>
                        <ul>
                            <li class="{{ Request::is('articulos','show-articulo/*','add-articulo','edit-articulo/*') ? 'active-page-link':''  }}">
                                <a href="{{ url('articulos') }}"><i class="bi bi-box"></i> Artículos</a>
                            </li>
                        </ul>
                        <ul>
                            <li class="{{ Request::is('servicios','show-servicio/*','add-servicio','edit-servicio/*') ? 'active-page-link':''  }}">
                                <a href="{{ url('servicios') }}"><i class="bi bi-boxes"></i> Servicios</a>
                            </li>
                        </ul>
                        <ul>
                            <li class="{{ Request::is('unidades','show-unidad/*','add-unidad','edit-unidad/*') ? 'active-page-link':''  }}">
                                <a href="{{ url('unidades') }}"><i class="bi bi-rulers"></i> Unidades de Medida </a>
                            </li>
                        </ul>
                        <ul>
                            <li class="{{ Request::is('comisiones','show-comision/*','add-comision','edit-comision/*') ? 'active-page-link':''  }}">
                                <a href="{{ url('comisiones') }}"><i class="bi bi-piggy-bank"></i>Tipos de Comisiones </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="sidebar-dropdown">
                    <a href="#">
                        <i class="bi bi-cart4"></i>
                        <span class="menu-text">Compras</span>
                        {{-- <span class="badge red">15</span> --}}
                    </a>
                    <div class="sidebar-submenu">
                        <ul>
                            <li class="{{ Request::is('ingresos','show-ingreso/*','add-ingreso','edit-ingreso/*') ? 'active-page-link':''  }}">
                                <a href="{{ url('ingresos') }}"><i class="bi bi-cart-plus"></i> Ingresos</a>
                            </li>
                        </ul>
                        <ul>
                            <li class="{{ Request::is('proveedores','show-proveedor/*','add-proveedor','edit-proveedor/*') ? 'active-page-link':''  }}">
                                <a href="{{ url('proveedores') }}"><i class="bi bi-person-badge-fill"></i> Proveedores</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="sidebar-dropdown">
                    <a href="#">
                        <i class="bi bi-cash-stack"></i>
                        <span class="menu-text">Ventas</span>
                        {{-- <span class="badge red">15</span> --}}
                    </a>
                    <div class="sidebar-submenu">
                        <ul>
                            <li class="{{ Request::is('clientes','show-cliente/*','add-cliente','edit-cliente/*') ? 'active-page-link':''  }}">
                                <a href="{{ url('clientes') }}"><i class="bi bi-person-video2"></i> Clientes</a>
                            </li>
                        </ul>
                        <ul>
                            <li class="{{ Request::is('vehiculos','show-vehiculo/*','add-vehiculo','edit-vehiculo/*') ? 'active-page-link':''  }}">
                                <a href="{{ url('vehiculos') }}"><i class="bi bi-car-front"></i> Vehículos</a>
                            </li>
                        </ul>
                        <ul>
                            <li class="{{ Request::is('ventas','show-venta/*','add-venta','edit-venta/*') ? 'active-page-link':''  }}">
                                <a href="{{ url('ventas') }}"><i class="bi bi-cash-stack"></i> Ventas</a>
                            </li>
                        </ul>
                        <ul>
                            <li class="{{ Request::is('inventario') ? 'active-page-link':''  }}">
                                <a href="{{ url('inventario') }}"><i class="bi bi-inboxes"></i> Inventario</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="sidebar-dropdown">
                    <a href="#">
                        <i class="bi bi-shield-shaded"></i>
                        <span class="menu-text">Seguridad</span>
                        {{-- <span class="badge red">15</span> --}}
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
            </ul>
        </div>
    </div>
    <!-- Sidebar menu ends -->

</nav>
<!-- Sidebar wrapper end -->
