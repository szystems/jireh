<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Usuario</title>
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
        <h1><u>Usuario</u></h1>
        @php
            $horafecha = new DateTime("now", new DateTimeZone('America/Guatemala'));
            $horafecha = $horafecha->format('d/m/Y')
        @endphp
        <p align="left"><font size="1">Fecha de impresion: {{ $horafecha }}</font></p>
    </header>
    <section>
        {{-- <h2>Usuario</h2> --}}
        <table class="table">
            <thead>

                <tr>
                    <th align="right">Nombre:</th>
                    <td>{{ $usuario->name }}</td>
                    <th align="right">Fecha Nacimiento:</th>
                    @php
                        $fnacimiento = null;
                        $edad = 0;
                        if ($usuario->fecha_nacimiento != null) {
                            $fnacimiento = date("d/m/Y", strtotime($usuario->fecha_nacimiento));
                            //calcular edad
                            $fecha_nacimiento = date("d-m-Y", strtotime($usuario->fecha_nacimiento));
                            $cumpleanos = new DateTime($usuario->fecha_nacimiento);
                            $hoy = new DateTime();
                            $annos = $hoy->diff($cumpleanos);
                            $edad = $annos->y;
                        }

                    @endphp
                    <td>{{ $fnacimiento }}  ({{ $edad }} años)</td>
                </tr>
                <tr>
                    <th align="right">Email</th>
                    <td>{{ $usuario->email }}</td>
                    <th align="right">Teléfonos</th>
                    <td colspan="3">{{ $usuario->telefono }} / {{ $usuario->celular }}</td>
                </tr>
                <tr>
                    <th align="right">Dirección</th>
                    <td colspan="5">{{ $usuario->direccion }}</td>
                </tr>
                @if ($usuario->fotografia != null)
                <tr>
                    <th align="right">Imagen:</th>
                    <td align="center" colspan="5">
                        <img src="{{$pathusuario.$usuario->fotografia}}" style="max-height: 200px; width: auto;"/>
                    </td>
                </tr>
                @endif

            </thead>
        </table>
        <!-- Other sections... -->
    </section>


</body>

</html>
