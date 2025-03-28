<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Información del Proveedor</title>
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
            font-size: 11px;
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
        .detail-card {
            border: 1px solid #eee;
            border-radius: 5px;
            margin-bottom: 15px;
            padding: 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .detail-header {
            background-color: #f8f9fa;
            padding: 8px;
            border-bottom: 1px solid #eee;
            font-weight: bold;
            font-size: 13px;
            color: #495057;
        }
        .detail-body {
            padding: 10px;
        }
        .logo {
            max-width: 200px;
            height: auto;
            margin-bottom: 10px;
        }
        .field-name {
            font-weight: bold;
            color: #495057;
            width: 30%;
        }
        .field-value {
            color: #333;
            width: 70%;
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
            <h1>INFORMACIÓN DEL PROVEEDOR</h1>
            <p>{{ $proveedor->nombre }}</p>
            <p>NIT: {{ $proveedor->nit ?: 'No especificado' }}</p>
        </div>

        <!-- Sección de datos generales -->
        <div class="section-title">DATOS GENERALES</div>
        <div class="detail-card">
            <div class="detail-body">
                <table class="info-table">
                    <tr>
                        <td class="field-name">Nombre:</td>
                        <td class="field-value">{{ $proveedor->nombre }}</td>
                        <td class="field-name">NIT:</td>
                        <td class="field-value">{{ $proveedor->nit ?: 'No especificado' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Sección de información de contacto -->
        <div class="section-title">INFORMACIÓN DE CONTACTO</div>
        <div class="detail-card">
            <div class="detail-body">
                <table class="info-table">
                    <tr>
                        <td class="field-name">Contacto:</td>
                        <td class="field-value">{{ $proveedor->contacto ?: 'No especificado' }}</td>
                    </tr>
                    <tr>
                        <td class="field-name">Teléfono:</td>
                        <td class="field-value">{{ $proveedor->telefono ?: 'No especificado' }}</td>
                    </tr>
                    <tr>
                        <td class="field-name">Celular:</td>
                        <td class="field-value">{{ $proveedor->celular ?: 'No especificado' }}</td>
                    </tr>
                    <tr>
                        <td class="field-name">Email:</td>
                        <td class="field-value text-info">{{ $proveedor->email ?: 'No especificado' }}</td>
                    </tr>
                    <tr>
                        <td class="field-name">Dirección:</td>
                        <td class="field-value">{{ $proveedor->direccion ?: 'No especificada' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Sección de información bancaria -->
        <div class="section-title">INFORMACIÓN BANCARIA</div>
        <div class="detail-card">
            <div class="detail-body">
                <table class="info-table">
                    <tr>
                        <td class="field-name">Banco:</td>
                        <td class="field-value">{{ $proveedor->banco ?: 'No especificado' }}</td>
                    </tr>
                    <tr>
                        <td class="field-name">Nombre de Cuenta:</td>
                        <td class="field-value">{{ $proveedor->nombre_cuenta ?: 'No especificado' }}</td>
                    </tr>
                    <tr>
                        <td class="field-name">Tipo de Cuenta:</td>
                        <td class="field-value text-success">{{ $proveedor->tipo_cuenta ?: 'No especificado' }}</td>
                    </tr>
                    <tr>
                        <td class="field-name">Número de Cuenta:</td>
                        <td class="field-value">{{ $proveedor->numero_cuenta ?: 'No especificado' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Documento generado el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
        <p>{{ $config->nombre_negocio }} - {{ $config->telefono }}</p>
    </div>
</body>
</html>
