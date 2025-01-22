<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detalles del Vehículo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
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
        img {
            max-width: 300px; /* Ajusta el tamaño máximo de la imagen */
            height: auto; /* Mantiene la proporción de la imagen */
            border-radius: 5px; /* Opcional: agrega bordes redondeados */
        }
    </style>
</head>
<body>
    <header>
        @if ($imagen)
            <img src="{{ $imagen }}" alt="Logo" height="300">
        @endif
        <h1><u>Detalles del Vehículo</u></h1>
        <p>Fecha de impresión: {{ date('d/m/Y g:ia') }}</p>
    </header>

    <section>
        <h2>Información del Cliente</h2>
        <table class="table">
            <tr>
                <th>Nombre</th>
                <td>{{ $cliente ? $cliente->nombre : 'No disponible' }}</td>
            </tr>
            <tr>
                <th>Teléfono</th>
                <td>{{ $cliente ? $cliente->telefono : 'No disponible' }}</td>
            </tr>
            <tr>
                <th>Celular</th>
                <td>{{ $cliente ? $cliente->celular : 'No disponible' }}</td>
            </tr>
            <tr>
                <th>Dirección</th>
                <td>{{ $cliente ? $cliente->direccion : 'No disponible' }}</td>
            </tr>
            <tr>
                <th>Email</th>
                <td>{{ $cliente ? $cliente->email : 'No disponible' }}</td>
            </tr>
            <tr>
                <th>DPI</th>
                <td>{{ $cliente ? $cliente->dpi : 'No disponible' }}</td>
            </tr>
            <tr>
                <th>NIT</th>
                <td>{{ $cliente ? $cliente->nit : 'No disponible' }}</td>
            </tr>
        </table>
    </section>

    <section>
        <h2>Información del Vehículo</h2>
        <table class="table">

            <tr>
                <th>Marca</th>
                <td>{{ $vehiculo->marca }}</td>
            </tr>
            <tr>
                <th>Modelo</th>
                <td>{{ $vehiculo->modelo }}</td>
            </tr>
            <tr>
                <th>Año</th>
                <td>{{ $vehiculo->ano }}</td>
            </tr>
            <tr>
                <th>Color</th>
                <td>{{ $vehiculo->color }}</td>
            </tr>
            <tr>
                <th>Placa</th>
                <td>{{ $vehiculo->placa }}</td>
            </tr>
            <tr>
                <th>VIN</th>
                <td>{{ $vehiculo->vin }}</td>
            </tr>
            <tr>
                <th>Imagen</th>
                <td>
                    @if ($vehiculo->fotografia)
                        <img src="{{ $pathvehiculo . $vehiculo->fotografia }}"
                            alt="Fotografía de {{ $vehiculo->marca }} {{ $vehiculo->modelo }}">
                    @else
                        <p>No disponible</p> <!-- Texto alternativo si no hay imagen -->
                    @endif
                </td>
            </tr>
        </table>
    </section>


</body>
</html>
