<!doctype html>
<html lang="es">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Pure css -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/purecss@3.0.0/build/pure-min.css"
        integrity="sha384-X38yfunGUhNzHpBaEBsWLO+A0HDYOQi8ufWDkZ0k9e0eXz/tH3II7uKZ9msv++Ls" crossorigin="anonymous">


    <title>{{ __('Proveedores') }}</title>

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
    <center>
        <img align="center" src="{{ $imagen }}" alt="" height="100">
    </center>
    <h3 align="center"><u>{{ __('Proveedores') }}</u></h3>
    <label>
        <font size="1">{{ __('Fecha Reporte:') }}</font>
        <font color="blue" size="1">
            @php
                $horafecha = now();
                $horafecha = $horafecha->format('d/m/Y')
            @endphp
            {{ $horafecha }}
        </font>
    </label>
    <br>

    <h5><u>{{ __('Listado de Proveedores') }}:</u></h5>
    <table class="table" Width=100%>
        <thead>
            <tr>
                <th>
                    <font size="1">Proveedor</font>
                </th>
                <th>
                    <font size="1">Contacto</font>
                </th>
                <th>
                    <font size="1">Banco</font>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($proveedores as $proveedor)
                <tr>
                    <td align="center">
                        <font size="1">

                            <b>{{ $proveedor->nombre }} </b>
                            <br>
                            {{ $proveedor->nit }}
                        </font>
                    </td>

                    <td align="center">
                        <font size="1">
                            <p>
                                <b>{{ $proveedor->contacto }}</b>
                                <br>
                                <small>
                                    {{ $proveedor->email }}
                                    <br>
                                        {{ $proveedor->telefono }}
                                    @if ($proveedor->celular != null)
                                        / {{ $proveedor->celular }}
                                    @endif
                                    <br>
                                    {{$proveedor->direccion}}
                                </small>
                            </p>
                        </font>
                    </td>
                    <td align="center">
                        <font size="1">
                            <p>
                                <b>{{ $proveedor->banco }}</b>
                                <br>
                                <small>
                                    {{ $proveedor->nombre_cuenta }}
                                    <br>
                                    {{ $proveedor->tipo_cuenta }}
                                    <br>
                                    {{ $proveedor->numero_cuenta }}
                                </small>
                            </p>
                        </font>
                    </td>


                </tr>
            @endforeach

        </tbody>

    </table>


</body>

</html>
