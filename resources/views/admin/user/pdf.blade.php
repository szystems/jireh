<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ isset($titulo) ? $titulo : __('Usuarios') }}</title>
    <style>
        @page {
            size: portrait;
            margin: 10px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 5px;
        }

        .company-logo {
            max-height: 70px;
            max-width: 90%;
            margin: 5px auto;
            display: block;
            text-align: center;
        }

        .content {
            margin-top: 10px;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 10px;
            padding-top: 5px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
            font-size: 16px;
        }
        .header p {
            margin: 5px 0;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 3px;
            font-size: 9px;
        }
        thead {
            background-color: #007bff;
            color: white;
        }
        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .section-title {
            background-color: #007bff;
            color: white;
            padding: 3px;
            margin: 8px 0 5px;
            font-weight: bold;
            text-align: center;
            font-size: 11px;
        }
        .footer {
            margin-top: 10px;
            text-align: center;
            font-size: 8px;
            color: #6c757d;
        }
        .text-primary { color: #007bff; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-info { color: #17a2b8; }
        .text-warning { color: #ffc107; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .badge {
            display: inline-block;
            padding: 2px 4px;
            border-radius: 3px;
            font-size: 8px;
        }
        .badge-success { background-color: #28a745; color: white; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; color: white; }
        .filters-info {
            clear: both;
            font-size: 8px;
            margin-top: 5px;
            margin-bottom: 5px;
            text-align: center;
            border: 1px solid #ddd;
            padding: 3px;
            background-color: #f8f9fa;
        }
        .header div.logo-container {
            width: 25%;
            float: left;
            text-align: left;
        }
        .header div.title-container {
            width: 50%;
            float: left;
            text-align: center;
        }
        .header div.info-container {
            width: 25%;
            float: right;
            text-align: right;
        }
        .summary-section {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 5px;
            margin-bottom: 10px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table th, .summary-table td {
            padding: 2px 4px;
            font-size: 8px;
        }
        .summary-table tr.header-row {
            background-color: #343a40;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        .user-image {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="content">
        <div class="logo-container">
            @if(isset($imagen) && $imagen)
                <img src="{{ $imagen }}" alt="Logo de la empresa" class="company-logo">
            @endif
        </div>

        <!-- Cabecera compacta -->
        <div class="header">
            <div class="title-container">
                <h1>{{ isset($titulo) ? strtoupper($titulo) : strtoupper(__('LISTADO DE USUARIOS')) }}</h1>
            </div>
            <div class="info-container">
                <p>{{ isset($config) ? $config->nombre_negocio : '' }}<br>{{ isset($config) ? $config->telefono : '' }}</p>
            </div>
        </div>

        <!-- Filtros aplicados -->
        <div class="filters-info">
            <strong>Fecha Reporte:</strong> {{ now()->format('d/m/Y') }}
            @if(isset($queryUser) && $queryUser)
                | <strong>Búsqueda:</strong> {{ $queryUser }}
            @endif
            @if(isset($role_filter) && $role_filter !== null && $role_filter !== '')
                | <strong>Rol:</strong> {{ $role_filter == 0 ? 'Administrador' : 'Vendedor' }}
            @endif
        </div>

        <!-- Resumen estadístico -->
        <div class="summary-section">
            <table class="summary-table">
                <tr>
                    <td width="25%" class="text-center">
                        <div class="text-primary text-bold">Total Usuarios</div>
                        <div>{{ count($usuarios) }}</div>
                    </td>
                    <td width="25%" class="text-center">
                        <div class="text-success text-bold">Administradores</div>
                        <div>{{ $usuarios->where('role_as', 0)->count() }}</div>
                    </td>
                    <td width="25%" class="text-center">
                        <div class="text-info text-bold">Vendedores</div>
                        <div>{{ $usuarios->where('role_as', 1)->count() }}</div>
                    </td>
                    <td width="25%" class="text-center">
                        <div class="text-danger text-bold">Usuarios Principales</div>
                        <div>{{ $usuarios->where('principal', 1)->count() }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tabla de usuarios -->
        <div class="section-title">LISTADO DE USUARIOS</div>
        <table>
            <thead>
                <tr>
                    <th width="5%">ID</th>
                    <th width="25%">Datos Personales</th>
                    <th width="15%">Rol</th>
                    <th width="25%">Contacto</th>
                    <th width="30%">Dirección</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($usuarios as $usuario)
                    @php
                        $fnacimiento = null;
                        $edad = 0;
                        if ($usuario->fecha_nacimiento != null) {
                            $fnacimiento = date("d-m-Y", strtotime($usuario->fecha_nacimiento));
                            $cumpleanos = new DateTime($usuario->fecha_nacimiento);
                            $hoy = new DateTime();
                            $annos = $hoy->diff($cumpleanos);
                            $edad = $annos->y;
                        }
                    @endphp
                    <tr>
                        <td class="text-center">{{ $usuario->id }}</td>
                        <td>
                            <div class="text-bold">{{ $usuario->name }}</div>
                            <div><small>{{ $fnacimiento }}</small>
                                @if ($edad > 0)
                                    <span class="badge badge-warning">{{ $edad }} años</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $usuario->role_as == 0 ? 'badge-danger' : 'badge-success' }}">
                                {{ $usuario->role_as == 0 ? 'Administrador' : 'Vendedor' }}
                            </span>
                            @if($usuario->principal == 1)
                                <br><span class="badge badge-warning">Usuario Principal</span>
                            @endif
                        </td>
                        <td>
                            <div>Email: <span class="text-primary">{{ $usuario->email }}</span></div>
                            <div>Tel: {{ $usuario->telefono }}</div>
                            @if($usuario->celular)
                                <div>Cel: {{ $usuario->celular }}</div>
                            @endif
                        </td>
                        <td>{{ $usuario->direccion }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Documento generado el {{ now()->format('d/m/Y H:i:s') }} | {{ isset($config) ? $config->nombre_negocio : 'Sistema de Gestión' }}</p>
    </div>
</body>
</html>
