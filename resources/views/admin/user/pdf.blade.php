<!doctype html>
<html lang="es">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Pure css -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/purecss@3.0.0/build/pure-min.css"
        integrity="sha384-X38yfunGUhNzHpBaEBsWLO+A0HDYOQi8ufWDkZ0k9e0eXz/tH3II7uKZ9msv++Ls" crossorigin="anonymous">


    <title>{{ __('Usuarios') }}</title>

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
    <h3 align="center"><u>{{ __('Usuarios') }}</u></h3>
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

    <h5><u>{{ __('Listado de Usuarios') }}:</u></h5>
    <table class="table" Width=100%>
        <thead>
            <tr>
                <th>
                    <font size="1">Usuario</font>
                </th>
                <th>
                    <font size="1">{{ __('Fecha Nacimiento') }}</font>
                </th>
                <th>
                    <font size="1">{{ __('Dirección') }}</font>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($usuarios as $usuario)
                <tr>
                    <td align="center">
                        <font size="1">
                            @php
                                $fnacimiento = null;
                                $edad = 0;
                                if ($usuario->fecha_nacimiento != null) {
                                    $fnacimiento = date("d-m-Y", strtotime($usuario->fecha_nacimiento));
                                    //calcular edad
                                    $fecha_nacimiento = date("d-m-Y", strtotime($usuario->fecha_nacimiento));
                                    $cumpleanos = new DateTime($usuario->fecha_nacimiento);
                                    $hoy = new DateTime();
                                    $annos = $hoy->diff($cumpleanos);
                                    $edad = $annos->y;
                                }

                            @endphp
                            <b>{{ $usuario->name }} </b>
                            <br>
                            {{ $usuario->email }}
                            <br>
                            {{ $usuario->telefono }} {{ $usuario->celular }}
                        </font>
                    </td>

                    <td align="center">
                        @php
                            $today = now();
                            $fecha_naciemiento = date("d/m/Y", strtotime($usuario->fecha_naciemiento));
                        @endphp
                        <font size="1">
                            {{ $fecha_naciemiento }}
                            @if ($edad > 0)
                            <br>
                                ({{ $edad }} Años)
                            @endif
                        </font>
                    </td>
                    <td align="center">
                        <font size="1">{{ $usuario->direccion }}</font>
                    </td>


                </tr>
            @endforeach

        </tbody>

    </table>


</body>

</html>
