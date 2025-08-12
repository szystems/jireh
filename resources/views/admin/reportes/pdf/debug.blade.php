<!DOCTYPE html>
<html>
<head>
    <title>Debug PDF Data</title>
</head>
<body>
    <h1>Debug de datos del PDF</h1>
    
    <h2>Configuración</h2>
    <p>Período: {{ $periodo }}</p>
    <p>Fecha inicio: {{ $fechaInicio->format('Y-m-d') }}</p>
    <p>Fecha fin: {{ $fechaFin->format('Y-m-d') }}</p>
    
    <h2>Metas Originales ({{ $metasOriginales->count() }})</h2>
    @foreach($metasOriginales as $meta)
        <p>- {{ $meta->nombre }} (ID: {{ $meta->id }}) - {{ $meta->periodo }} - ${{ number_format($meta->monto_minimo, 2) }}</p>
    @endforeach
    
    <h2>Trabajadores ({{ $trabajadores->count() }})</h2>
    @foreach($trabajadores as $trabajador)
        <h3>{{ $trabajador->name }} (ID: {{ $trabajador->id }})</h3>
        <p>Tiene metasConProgreso: {{ isset($trabajador->metasConProgreso) ? 'SÍ' : 'NO' }}</p>
        @if(isset($trabajador->metasConProgreso))
            <p>Cantidad de metas: {{ $trabajador->metasConProgreso->count() }}</p>
            @foreach($trabajador->metasConProgreso as $metaData)
                <p>&nbsp;&nbsp;- {{ $metaData['meta']->nombre }}: ${{ number_format($metaData['ventas_para_meta'], 2) }} ({{ number_format($metaData['porcentaje'], 2) }}%)</p>
            @endforeach
        @endif
    @endforeach
</body>
</html>
