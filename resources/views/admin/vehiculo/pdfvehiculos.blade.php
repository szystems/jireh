<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vehículos</title>
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
            text-align: center;
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
        <h1><u>Vehículos</u></h1>
        @php
            $horafecha = new DateTime("now", new DateTimeZone('America/Guatemala'));
            $horafecha = $horafecha->format('d/m/Y')
        @endphp
        <p align="left"><font size="1">Fecha de impresion: {{ $horafecha }}</font></p>
    </header>
    <section>
        <h2>Listado de vehículos:</h2>
        {{-- <p class="m-0 fw-normal">
            <hr>
            <strong><u>Filtros:</u></strong>
            <br>
            <small>
                @if (request('vehiculo_imprimir'))
                    <strong>Nombre: </strong><font color="Blue">{{ request('vehiculo_imprimir') }}</font>
                @endif
                @if (request('categoria_imprimir'))
                    @php
                        $categoria = \App\Models\Categoria::find(request('categoria_imprimir'));
                    @endphp
                    <strong>Clinica: </strong><font color="Blue">{{ $categoria->nombre }}</font>
                @endif
                @if (request('proveedor_imprimir'))
                    @php
                        $proveedor = \App\Models\Proveedor::find(request('proveedor_imprimir'));
                    @endphp
                    <strong>Proveedor: </strong><font color="Blue">{{ $proveedor->nombre }}</font>
                @endif
            </small>
            <hr>
        </p> --}}
        <table class="table">
            <thead>
                <tr>
                    <th>Vehículo</th>
                    <th>Cliente</th>
                    {{-- <th>Imagen</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($vehiculos as $vehiculo)
                    <tr>
                        <td>
                            <font size="1">
                                <p class="m-0">
                                    {{-- <img src="{{ $vehiculo->fotografia ? asset($pathvehiculo . $vehiculo->fotografia) : asset('assets/imgs/vehiculos/vehiculoicon.png') }}"
                                        alt="Fotografía de {{ $vehiculo->marca }} {{ $vehiculo->modelo }}"
                                        style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;" />
                                    <br> --}}
                                    <font color="blue">{{ $vehiculo->marca }} <b>{{ $vehiculo->modelo }} {{ $vehiculo->ano }}</b></font>
                                    <br>
                                    <small>
                                        Color: <b>{{ $vehiculo->color }}</b>
                                        <br>
                                        Placa: <b>{{ $vehiculo->placa }}</b>
                                        <br>
                                        VIN: <b>{{ $vehiculo->vin }}</b>
                                    </small>
                                </p>
                            </font>
                        </td>
                        <td>
                            @if ($vehiculo->cliente)
                                <font size="1">
                                    <p class="m-0">
                                        {{-- <img src="{{ $vehiculo->cliente->fotografia ? asset('assets/imgs/clientes/' . $vehiculo->cliente->fotografia) : asset('assets/imgs/clientes/clienteicon.png') }}"
                                            alt="Fotografía de {{ $vehiculo->cliente->nombre }}"
                                            style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;" />
                                        <br> --}}
                                        <font color="blue">{{ $vehiculo->cliente->nombre }}</font>
                                        <br>
                                        <small>
                                            Fecha de Nacimiento: <b>{{ $vehiculo->cliente->fecha_nacimiento }}</b>
                                            <br>
                                            Teléfono: <b>{{ $vehiculo->cliente->telefono }}</b>
                                            <br>
                                            Celular: <b>{{ $vehiculo->cliente->celular }}</b>
                                            <br>
                                            Dirección: <b>{{ $vehiculo->cliente->direccion }}</b>
                                            <br>
                                            Email: <b>{{ $vehiculo->cliente->email }}</b>
                                            <br>
                                            DPI: <b>{{ $vehiculo->cliente->dpi }}</b>
                                            <br>
                                            NIT: <b>{{ $vehiculo->cliente->nit }}</b>
                                        </small>
                                    </p>
                                </font>
                            @else
                                <font size="1">Cliente Desconocido</font>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>


</body>

</html>
