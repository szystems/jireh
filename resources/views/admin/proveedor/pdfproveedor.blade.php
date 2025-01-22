<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Proveedor</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }

        .table {
            border-collapse: collapse;
            width: 100%;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 8px;
            /* text-align: center; */
        }

        .table th {
            background-color: #f0f0f0;
        }

        header {
            text-align: center;
        }

        header h1 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        section {
            margin-bottom: 20px;
        }

        section h2 {
            font-size: 14px;
            margin-top: 10px;
        }

        footer {
            text-align: center;
            margin-top: 20px;
        }

        footer p {
            margin-bottom: 10px;
        }
        img {
        width: 100%;
        height: auto;
        margin: 0;
        padding: 0;
    }
    </style>
</head>

<body>
    <header>
        <img align="center" src="{{ $imagen }}" alt="" height="100">
        <h1><u>Proveedor</u></h1>
        @php
            $horafecha = new DateTime("now", new DateTimeZone('America/Guatemala'));
            $horafecha = $horafecha->format('d/m/Y')
        @endphp
        <p align="left"><font size="1">Fecha de impresion: {{ $horafecha }}</font></p>
    </header>
    <section>
        {{-- <h2>Proveedor</h2> --}}
        <table class="table">
            <thead>

                <tr>
                    <th align="right">Nombre:</th>
                    <td colspan="2">{{ $proveedor->nombre }}</td>
                    <th align="right">NIT:</th>
                    <td colspan="2">{{ $proveedor->nit }}</td>
                </tr>
                <tr>
                    <th align="center" colspan="6">Contacto</th>
                </tr>
                <tr>
                    <th align="right">nombre:</th>
                    <td>{{ $proveedor->contacto }}</td>
                    <th align="right">Teléfonos:</th>
                    <td">{{ $proveedor->telefono }} / {{ $proveedor->celular }}</td>
                    <th align="right">Email:</th>
                    <td>{{ $proveedor->email }}</td>
                </tr>
                <tr>
                    <th>Dirección:</th>
                    <td colspan="5">{{ $proveedor->direccion }}</td>
                </tr>
                <tr>
                    <th align="center" colspan="6">Banco</th>
                </tr>
                <tr>
                    <th align="right">Banco:</th>
                    <td>{{ $proveedor->banco }}</td>
                    <th align="right">Nombre Cuenta:</th>
                    <td">{{ $proveedor->nombre_cuenta }}</td>
                    <th align="right">Numero Cuenta:</th>
                    <td>{{ $proveedor->numero_cuenta }}</td>
                </tr>

            </thead>
        </table>
        <!-- Other sections... -->
    </section>


</body>

</html>
