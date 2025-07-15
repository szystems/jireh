<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="csrf-token" content="{{ csrf_token() }}">

		<!-- Meta -->
		<meta name="description" content="" />
		<meta name="author" content="Szystems" />
		<link rel="canonical" href="https://www.szystems.com">
		<meta property="og:url" content="https://www.szystems.com">
		<meta property="og:title" content="Jireh">
		<meta property="og:description" content="Software de manejo de centro de servicios de Car Wash y Taller">
		<meta property="og:type" content="Web App">
		<meta property="og:site_name" content="https://www.app.jirehautomotriz.com">
		<link rel="shortcut icon" href="assets/imgs/logos/favicon.ico" />

		<!-- Title -->
		<title>{{ config('app.name', 'JIREH') }}</title>

		<!-- Primero cargamos jQuery para evitar conflictos -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

        <!-- Agregamos Moment.js directamente desde CDN ANTES de cualquier script que lo use -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

		<!-- Bootstrap css -->
        <link rel="stylesheet" href="{{ asset('dashboardtemplate/design/assets/css/bootstrap.min.css') }}" />

        <!-- Bootstrap font icons css -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        <!-- Main css -->
        <link rel="stylesheet" href="{{ asset('dashboardtemplate/design/assets/css/main.min.css') }}" />

        <!-- Vendor Css Files -->
        <!-- Scrollbar CSS -->
        <link rel="stylesheet" href="{{ asset('dashboardtemplate/design/assets/vendor/overlay-scroll/OverlayScrollbars.min.css') }}" />

        <!-- Date Range CSS -->
        {{-- <link rel="stylesheet" href="{{ asset('dashboardtemplate/design/assets/vendor/daterange/daterange.css') }}" /> --}}

        <!-- Dropzone CSS -->
        <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" />

        {{-- Datepicker --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

        <!-- Select2 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <!-- Select2 Bootstrap 5 Theme -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

        <!-- OverlayScrollbars JS - Cargado tempranamente para garantizar disponibilidad -->
        <script src="{{ asset('dashboardtemplate/design/assets/vendor/overlay-scroll/jquery.overlayScrollbars.min.js') }}"></script>

        <!-- Datepicker JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

        <!-- Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <!-- CKeditor 5 -->
        {{-- <script src="{{ asset('assets/ckeditor5/ckeditor.js') }}"></script> --}}

        <style>
            html, body {
                height: 100%;
                margin: 0;
                padding: 0;
                overflow-x: hidden;
            }

            .page-wrapper {
                padding: 0;
                position: relative;
                min-height: 100vh;
            }

            .main-container {
                transition: padding-left 0.3s ease;
                padding: 0 0 0 250px; /* Mantener consistencia con el template original */
                min-height: calc(100vh - 65px);
                background: #eef1f6;
                position: relative;
                overflow-x: hidden;
            }

            .content-wrapper {
                padding: 20px;
                min-height: calc(100vh - 125px); /* Ajustar para el header y footer */
                overflow-x: hidden;
            }

            .custom-bg {
                background-color: #b6becc; /* Un color gris claro */
            }

            .app-footer {
                width: 100%;
                background: #ffffff;
                border-top: 1px solid #dee2e6;
                padding: 10px 0;
            }

            /* Ajustes específicos para los contenedores del contenido */
            .container-fluid,
            .container {
                overflow-x: hidden;
            }

            /* Ajustes cuando el sidebar está colapsado */
            .sidebar-wrapper.collapsed ~ .main-container {
                padding-left: 70px !important;
            }

            /* Responsivo para dispositivos móviles */
            @media (max-width: 1199.98px) {
                .main-container {
                    padding-left: 0 !important;
                }
                
                .sidebar-wrapper.collapsed ~ .main-container {
                    padding-left: 0 !important;
                }
            }
        </style>


	</head>

	<body>

		<!-- Loading wrapper start -->
		{{-- <div id="loading-wrapper">
			<div class="spinner">
				<div class="line1"></div>
				<div class="line2"></div>
				<div class="line3"></div>
				<div class="line4"></div>
				<div class="line5"></div>
				<div class="line6"></div>
			</div>
		</div> --}}
		<!-- Loading wrapper end -->

		<!-- Page wrapper start -->
		<div class="page-wrapper">

			@include('layouts.incadmin.nav')

			<!-- Main container start -->
			<div class="main-container">

				@include('layouts.incadmin.sidebar')

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    @yield('content')
                </div>

				@include('layouts.incadmin.footer')

			</div>
			<!-- Main container end -->

		</div>
		<!-- Page wrapper end -->

		<!-- Required JavaScript Files -->
        <!-- No cargar jQuery de nuevo aquí, ya se cargó en el head -->

        <!-- Cargar la versión completa de Bootstrap (no minificada) para facilitar depuración -->
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.js"></script>

        <!-- Ya no cargar Moment.js aquí (ya está cargado en el head) -->
        <!-- <script src="{{ asset('dashboardtemplate/design/assets/js/moment.js') }}"></script> -->

        <!-- Modernizr -->
        <script src="{{ asset('dashboardtemplate/design/assets/js/modernizr.js') }}"></script>

        <!-- Cargar custom-scrollbar.js después de que OverlayScrollbars ya está disponible -->
        <script src="{{ asset('dashboardtemplate/design/assets/vendor/overlay-scroll/custom-scrollbar.js') }}"></script>

        <!-- News ticker -->
        <script src="{{ asset('dashboardtemplate/design/assets/vendor/newsticker/newsTicker.min.js') }}"></script>
        <script src="{{ asset('dashboardtemplate/design/assets/vendor/newsticker/custom-newsTicker.js') }}"></script>

        <!-- Eliminar el script de verificación existente ya que ahora aseguramos carga correcta -->

        <!-- Dropzone JS -->
        <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

        <!-- Main Js Required -->
        <script src="{{ asset('dashboardtemplate/design/assets/js/main.js') }}"></script>

        <!-- Sweet Alert -->
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>


		<!-- Apex Charts -->
		{{-- <script src="{{ asset('dashboardtemplate/design/assets/vendor/apex/apexcharts.min.js') }}"></script>
		<script src="{{ asset('dashboardtemplate/design/assets/vendor/apex/custom/dash1/analytics.js') }}"></script>
		<script src="{{ asset('dashboardtemplate/design/assets/vendor/apex/custom/dash1/visitors.js') }}"></script>
		<script src="{{ asset('dashboardtemplate/design/assets/vendor/apex/custom/dash1/income.js') }}"></script>
		<script src="{{ asset('dashboardtemplate/design/assets/vendor/apex/custom/dash1/orders.js') }}"></script>
		<script src="{{ asset('dashboardtemplate/design/assets/vendor/apex/custom/dash1/sales.js') }}"></script>
		<script src="{{ asset('dashboardtemplate/design/assets/vendor/apex/custom/dash1/sparkline.js') }}"></script>
		<script src="{{ asset('dashboardtemplate/design/assets/vendor/apex/custom/dash1/conversion.js') }}"></script> --}}

		<!-- Main Js Required -->
		{{-- <script src="{{ asset('dashboardtemplate/design/assets/js/main.js') }}"></script> --}}

        @yield('scripts')

        @if (session('status'))
            <script>
                swal("{{ session('status') }}");
            </script>
        @endif

        {{-- Hora y fecha --}}
        <script>
            // Función para actualizar el contador de notificaciones
            function actualizarContadorNotificaciones() {
                fetch('/api/notificaciones/resumen')
                    .then(response => response.json())
                    .then(data => {
                        const contador = document.getElementById('notification-count');
                        if (contador) {
                            const total = data.por_prioridad.alta + data.por_prioridad.media;
                            contador.textContent = total;
                            contador.style.display = total > 0 ? 'inline' : 'none';
                        }
                    })
                    .catch(error => console.log('Error al obtener notificaciones:', error));
            }

            // Función para actualizar la hora y fecha
            function actualizarReloj() {
                const ahora = new Date();
                const horas = ahora.getHours();
                const minutos = ahora.getMinutes();
                const segundos = ahora.getSeconds();

                // Calcula si es AM o PM
                const amPm = horas >= 12 ? 'PM' : 'AM';
                const hora12 = horas % 12 || 12; // Convierte a formato de 12 horas

                // Formatea la hora con dos dígitos
                const horaFormateada = `${hora12.toString().padStart(2, '0')}:${minutos.toString().padStart(2, '0')}:${segundos.toString().padStart(2, '0')} ${amPm}`;

                // Obtiene la fecha actual
                const dia = ahora.getDate();
                const mes = ahora.getMonth() + 1; // Los meses en JavaScript son base 0 (enero = 0)
                const anio = ahora.getFullYear();
                const fechaFormateada = `${dia}/${mes}/${anio}`;

                // Actualiza el contenido del elemento con la fecha y la hora
                const elementoReloj = document.getElementById('reloj');
                if (elementoReloj) {
                    elementoReloj.textContent = `${fechaFormateada} ${horaFormateada}`;
                }
            }

            // Inicializar cuando el DOM esté listo
            document.addEventListener('DOMContentLoaded', function() {
                actualizarContadorNotificaciones();
                // Actualizar contador cada 30 segundos
                setInterval(actualizarContadorNotificaciones, 30000);
            });

            // Actualiza la hora y la fecha cada segundo
            setInterval(actualizarReloj, 1000);
        </script>

	</body>

</html>
