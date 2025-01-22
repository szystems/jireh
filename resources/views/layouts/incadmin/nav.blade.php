<!-- Page header starts -->
<div class="page-header">

    <div class="toggle-sidebar m-3" id="toggle-sidebar">
        <i class="bi bi-list"></i>
    </div>

    <!-- Sidebar brand starts -->
    <div class="brand">
        <a href="{{ url('dashboard') }}" class="logo mb-3 mt-1 align-self-center d-flex justify-content-center">
            <div class="border border-primary custom-bg p-2 rounded" style="width: 100%;">
                <img src="{{ asset('img/logo.png') }}" class="d-none d-md-block me-4" alt="Jireh" />
                <img src="{{ asset('img/logo.png') }}" class="d-block d-md-none me-4" alt="Jireh" />
            </div>
        </a>
    </div>
    <!-- Sidebar brand ends -->

    <!-- Header actions ccontainer start -->
    <div class="header-actions-container">

        <!-- Search container start -->
        {{-- <div class="search-container me-4 d-xl-block d-lg-none">

            <input type="text" class="form-control" placeholder="Search" />

        </div> --}}
        <!-- Search container end -->

        <!-- Header actions start -->
        <div class="header-actions d-xl-flex d-lg-none gap-4">
            {{-- <div class="dropdown">
                <a class="dropdown-toggle" href="#!" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-envelope-open fs-5 lh-1"></i>
                    <span class="count-label"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-end shadow-lg">
                    <div class="dropdown-item">
                        <div class="d-flex py-2 border-bottom">
                            <img src="dashboardtemplate/design/assets/images/user.png" class="img-3x me-3 rounded-3" alt="Admin Dashboards" />
                            <div class="m-0">

                                <h6 class="mb-1 fw-semibold">Jehovah Roy</h6>
                                <p class="mb-1">Membership has been ended.</p>
                                <p class="small m-0 text-secondary">Today, 07:30pm</p>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown-item">
                        <div class="d-flex py-2 border-bottom">
                            <img src="dashboardtemplate/design/assets/images/user2.png" class="img-3x me-3 rounded-3" alt="Admin Dashboards" />
                            <div class="m-0">
                                <h6 class="mb-1 fw-semibold">Benjamin Michiels</h6>
                                <p class="mb-1">Congratulate, James for new job.</p>
                                <p class="small m-0 text-secondary">Today, 08:00pm</p>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown-item">
                        <div class="d-flex py-2">
                            <img src="dashboardtemplate/design/assets/images/user1.png" class="img-3x me-3 rounded-3" alt="Admin Dashboards" />
                            <div class="m-0">
                                <h6 class="mb-1 fw-semibold">Jehovah Roy</h6>
                                <p class="mb-1">Lewis added new schedule release.</p>
                                <p class="small m-0 text-secondary">Today, 09:30pm</p>
                            </div>
                        </div>
                    </div>
                    <div class="d-grid mx-3 my-1">
                        <a href="javascript:void(0)" class="btn btn-primary">View all</a>
                    </div>
                </div>
            </div> --}}
            <a href="{{ url('configs') }}" data-bs-toggle="tooltip" data-bs-placement="bottom"
                data-bs-custom-class="custom-tooltip-blue" data-bs-title="Configuración">
                <i class="bi bi-gear fs-5"></i>
            </a>
        </div>
        <!-- Header actions start -->

        <!-- Header profile start -->
        <div class="header-profile d-flex align-items-center">
            <div class="dropdown">
                <a href="#" id="userSettings" class="user-settings" data-toggle="dropdown" aria-haspopup="true">
                    @php
                        $usuario = Auth::user()->name;
                        $nombre = explode(' ', trim($usuario));
                    @endphp
                    <span class="user-name d-none d-md-block">{{ ucwords($nombre[0]) }}</span>
                    <span class="avatar">
                        @if (Auth::user()->fotografia != null)
                            <img src="{{ asset('assets/imgs/users/'.Auth::user()->fotografia) }}" alt="Doctores" />
                        @else
                            <img src="{{ asset('assets/imgs/users/usericon4.png') }}" alt="Doctores" />
                        @endif
                        <span class="status online"></span>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="userSettings">
                    <div class="header-profile-actions">
                        <a href="{{ url('show-user/' . Auth::id()) }}"><i class="bi bi-person-lines-fill"></i>&ensp;Perfil</a>
                        {{-- <a href="account-settings.html">Settings</a> --}}
                        <a class="nav-link text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right"></i>&ensp;Salir
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Header profile end -->

    </div>
    <!-- Header actions ccontainer end -->

</div>
<!-- Page header ends -->
