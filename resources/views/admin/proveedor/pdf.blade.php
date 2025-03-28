<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Listado de Proveedores</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 10px;
        }

        /* Agregar estilos para el logo integrado */
        .company-logo {
            max-height: 70px;
            max-width: 90%;
            margin: 5px auto;
            display: block;
            text-align: center;
        }

        .content {
            margin-top: 10px; /* Reducir el margen superior */
        }

        .logo-container {
            text-align: center;
            margin-bottom: 10px;
            padding-top: 5px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
        }
        .info-section {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-cell {
            display: table-cell;
            padding: 3px 5px;
            width: 50%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 5px;
            font-size: 10px;
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
            padding: 5px;
            margin: 15px 0 10px;
            font-weight: bold;
            text-align: center;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
        }
        .text-primary { color: #007bff; }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-info { color: #17a2b8; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 10px;
        }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .summary-table td, .summary-table th {
            padding: 3px 5px;
        }
        .page-number {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="content">
        <!-- Añadir logo integrado -->
        <div class="logo-container">
            @if($config->logo)
                <img src="{{ public_path('assets/imgs/logos/' . $config->logo) }}" alt="Logo de la empresa" class="company-logo">
            @endif
        </div>

        <!-- Encabezado con logo -->
        <div class="header">
            <h1>LISTADO DE PROVEEDORES</h1>
            <p>Fecha: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
        </div>

        <!-- Sección de información general -->
        <div class="section-title">DETALLE DE PROVEEDORES REGISTRADOS</div>

        <table>
            <thead>
                <tr>
                    <th width="5%" class="text-center">No.</th>
                    <th width="25%">Proveedor</th>
                    <th width="35%">Información de Contacto</th>
                    <th width="35%">Información Bancaria</th>
                </tr>
            </thead>
            <tbody>
                @php $i = 1; @endphp
                @foreach ($proveedores as $proveedor)
                    <tr>
                        <td class="text-center">{{ $i++ }}</td>
                        <td>
                            <span class="text-bold">{{ $proveedor->nombre }}</span>
                            <br>
                            <small>NIT: {{ $proveedor->nit ?: 'No registrado' }}</small>
                        </td>
                        <td>
                            <span class="text-bold">Contacto:</span> {{ $proveedor->contacto ?: 'No registrado' }}<br>
                            <span class="text-bold">Email:</span> <span class="text-info">{{ $proveedor->email ?: 'No registrado' }}</span><br>
                            <span class="text-bold">Teléfono:</span> {{ $proveedor->telefono ?: 'No registrado' }}
                            @if ($proveedor->celular)
                                / {{ $proveedor->celular }}
                            @endif
                            <br>
                            <span class="text-bold">Dirección:</span> {{ $proveedor->direccion ?: 'No registrada' }}
                        </td>
                        <td>
                            <span class="text-bold">Banco:</span> {{ $proveedor->banco ?: 'No registrado' }}<br>
                            <span class="text-bold">Titular:</span> {{ $proveedor->nombre_cuenta ?: 'No registrado' }}<br>
                            <span class="text-bold">Tipo:</span>
                            @if($proveedor->tipo_cuenta)
                                <span class="text-success">{{ $proveedor->tipo_cuenta }}</span>
                            @else
                                No registrado
                            @endif
                            <br>
                            <span class="text-bold">No. Cuenta:</span> {{ $proveedor->numero_cuenta ?: 'No registrado' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Sección de resumen -->
        <div class="section-title">RESUMEN</div>
        <table class="summary-table">
            <tr>
                <td width="70%" class="text-right text-bold">Total de proveedores registrados:</td>
                <td width="30%" class="text-center text-primary text-bold">{{ count($proveedores) }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Documento generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
        <p>{{ $config->nombre_negocio }} - {{ $config->telefono }}</p>
    </div>

    <div class="page-number">
        Página 1
    </div>
</body>
</html>
