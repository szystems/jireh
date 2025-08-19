<!-- Page header starts -->
<div class="page-header">

    <div class="toggle-sidebar m-3" id="toggle-sidebar">
        <i class="bi bi-list"></i>
    </div>

    <!-- Sidebar brand starts -->
    <div class="brand">
        <a href="{{ url('dashboard') }}" class="logo mb-3 mt-1 align-self-center d-flex justify-content-center">
            <div class="border border-primary rounded p-2" style="background-color: #f8f9fa; width: 100%;">
                <img src="{{ asset('img/logo.png') }}" class="d-none d-md-block img-fluid" alt="Jireh" />
                <img src="{{ asset('img/jireh 2.png') }}" class="d-block d-md-none mx-auto" style="height: 36px;" alt="Jireh" />
            </div>
        </a>
    </div>
    <!-- Sidebar brand ends -->

    <!-- Header actions container start -->
    <div class="header-actions-container">

        <!-- Header actions start -->
        <div class="header-actions d-flex gap-3">
            @if(Auth::user()->role_as != 1)
            <a href="{{ url('config') }}" class="header-action-link" data-bs-toggle="tooltip" data-bs-placement="bottom"
                data-bs-title="Configuración">
                <i class="bi bi-gear fs-5"></i>
            </a>
            @endif

            <a href="{{ url('ventas') }}" class="header-action-link" data-bs-toggle="tooltip" data-bs-placement="bottom"
                data-bs-title="Ventas">
                <i class="bi bi-cash-stack fs-5"></i>
            </a>

            <a href="{{ url('inventario') }}" class="header-action-link" data-bs-toggle="tooltip" data-bs-placement="bottom"
                data-bs-title="Inventario">
                <i class="bi bi-box-seam fs-5"></i>
            </a>
        </div>
        <!-- Header actions end -->

        <!-- Header profile start -->
        <div class="header-profile d-flex align-items-center">
            <div class="dropdown">
                <a href="#" id="userSettings" class="user-settings" data-bs-toggle="dropdown" aria-haspopup="true">
                    @php
                        $usuario = Auth::user()->name;
                        $nombre = explode(' ', trim($usuario));
                        $rolUsuario = Auth::user()->role_as == 1 ? 'Administrador' : 'Usuario';
                    @endphp
                    <span class="user-name d-none d-md-block">{{ ucwords($nombre[0]) }} <small class="text-muted">({{ $rolUsuario }})</small></span>
                    <span class="avatar">
                        @if (Auth::user()->fotografia != null)
                            <img src="{{ asset('assets/imgs/users/'.Auth::user()->fotografia) }}" alt="Usuario" class="img-fluid rounded-circle" />
                        @else
                            <img src="{{ asset('assets/imgs/users/usericon4.png') }}" alt="Usuario" class="img-fluid rounded-circle" />
                        @endif
                        <span class="status online"></span>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end shadow-lg user-dropdown-menu" aria-labelledby="userSettings">
                    <div class="dropdown-header d-flex align-items-center">
                        <div class="user-avatar me-3">
                            @if (Auth::user()->fotografia != null)
                                <img src="{{ asset('assets/imgs/users/'.Auth::user()->fotografia) }}" alt="Usuario" class="img-fluid rounded-circle" width="40" />
                            @else
                                <img src="{{ asset('assets/imgs/users/usericon4.png') }}" alt="Usuario" class="img-fluid rounded-circle" width="40" />
                            @endif
                        </div>
                        <div>
                            <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                            <p class="mb-0 small text-muted">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <div class="header-profile-actions">
                        <a href="{{ url('show-user/' . Auth::id()) }}" class="dropdown-item">
                            <i class="bi bi-person-lines-fill"></i>&ensp;Mi Perfil
                        </a>
                        <a href="{{ url('edit-user/' . Auth::id()) }}" class="dropdown-item">
                            <i class="bi bi-pencil-square"></i>&ensp;Editar Perfil
                        </a>
                        @if(Auth::user()->role_as != 1)
                        <a href="{{ url('config') }}" class="dropdown-item">
                            <i class="bi bi-gear"></i>&ensp;Configuración
                        </a>
                        @endif
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right"></i>&ensp;Cerrar Sesión
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Header profile end -->

    </div>
    <!-- Header actions container end -->

</div>
<!-- Page header ends -->

<style>
.header-action-link {
    color: #495057;
    padding: 0.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.header-action-link:hover {
    background-color: #e9ecef;
    color: #0d6efd;
}

/* Fix para el z-index del dropdown del usuario - Solución específica */
.header-profile {
    position: relative;
    z-index: 999999; /* Z-index máximo para el contenedor del perfil */
}

.header-profile .dropdown {
    position: relative;
    z-index: 999999;
}

/* Forzar el dropdown fuera del contexto de apilamiento normal */
.user-dropdown-menu {
    position: fixed !important; /* Fixed positioning para salir del stacking context */
    z-index: 9999999 !important; /* Z-index extremadamente alto */
    min-width: 280px;
    border: 1px solid rgba(0,0,0,.15);
    background-color: #fff !important;
    background-clip: padding-box;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175) !important;
    /* El posicionamiento se calculará dinámicamente con JavaScript */
}

.header-profile-actions .dropdown-item {
    padding: 0.5rem 1rem;
    white-space: nowrap;
}

.header-profile-actions .dropdown-item:hover {
    background-color: #f8f9fa;
}

.dropdown-header {
    padding: 1rem;
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.user-settings:hover {
    text-decoration: none;
}

.user-name {
    margin-right: 0.5rem;
}

/* Asegurar que el page-header no interfiera */
.page-header {
    position: relative;
    z-index: 100; /* Z-index bajo para que no interfiera con el dropdown */
}

/* Corrección para dispositivos móviles */
@media (max-width: 767.98px) {
    .header-actions {
        gap: 0.5rem !important;
    }

    .header-action-link {
        padding: 0.25rem;
    }
    
    .user-dropdown-menu {
        min-width: 250px;
        right: 0 !important;
        left: auto !important;
    }
}

/* Asegurar que el dropdown esté visible en todas las condiciones */
.dropdown-menu.show {
    display: block !important;
    z-index: 9999999 !important; /* Z-index extremo para estar encima de absolutamente todo */
}

/* Regla específica para controlar el Dashboard Header que interfiere */
.content-wrapper .page-header,
.main-container .page-header {
    position: relative !important;
    z-index: 1 !important; /* Z-index muy bajo para que no interfiera nunca */
}
</style>

<!-- Script para posicionar correctamente el dropdown con position: fixed -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Posicionamiento correcto del dropdown con position: fixed
    const userSettingsBtn = document.getElementById('userSettings');
    const userDropdownMenu = document.querySelector('.user-dropdown-menu');
    
    if (userSettingsBtn && userDropdownMenu) {
        // Prevenir que el dropdown se cierre cuando se hace click dentro del dropdown
        userDropdownMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
        userSettingsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Calcular posición del botón
            const btnRect = this.getBoundingClientRect();
            
            // Posicionar el dropdown
            userDropdownMenu.style.top = (btnRect.bottom + 5) + 'px';
            userDropdownMenu.style.left = (btnRect.right - 280) + 'px'; // 280px es el ancho del dropdown
            
            // Asegurar que no se salga de la pantalla
            const viewportWidth = window.innerWidth;
            if (parseInt(userDropdownMenu.style.left) < 10) {
                userDropdownMenu.style.left = '10px';
            }
            if (parseInt(userDropdownMenu.style.left) + 280 > viewportWidth - 10) {
                userDropdownMenu.style.left = (viewportWidth - 290) + 'px';
            }
            
            // Toggle dropdown usando Bootstrap
            const dropdown = new bootstrap.Dropdown(this);
            dropdown.toggle();
        });
        
        // Actualizar posición cuando se redimensiona la ventana
        window.addEventListener('resize', function() {
            if (userDropdownMenu.classList.contains('show')) {
                const btnRect = userSettingsBtn.getBoundingClientRect();
                userDropdownMenu.style.top = (btnRect.bottom + 5) + 'px';
                userDropdownMenu.style.left = (btnRect.right - 280) + 'px';
                
                const viewportWidth = window.innerWidth;
                if (parseInt(userDropdownMenu.style.left) < 10) {
                    userDropdownMenu.style.left = '10px';
                }
                if (parseInt(userDropdownMenu.style.left) + 280 > viewportWidth - 10) {
                    userDropdownMenu.style.left = (viewportWidth - 290) + 'px';
                }
            }
        });
        
        // Cerrar dropdown al hacer click fuera (pero no en elementos que interfieren)
        document.addEventListener('click', function(e) {
            if (!userSettingsBtn.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                if (userDropdownMenu.classList.contains('show')) {
                    const dropdown = bootstrap.Dropdown.getInstance(userSettingsBtn);
                    if (dropdown) {
                        dropdown.hide();
                    }
                }
            }
        });
    }
});
</script>
