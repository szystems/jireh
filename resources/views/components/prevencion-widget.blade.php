<!-- Widget para mostrar en el sidebar -->
<div class="sidebar-widget bg-light p-3 rounded mb-3">
    <h6 class="text-primary mb-2">🛡️ Sistema de Protección</h6>
    <div class="d-flex align-items-center mb-2">
        <div class="progress flex-grow-1 me-2" style="height: 6px;">
            <div class="progress-bar bg-success" id="widget-salud-bar" style="width: 95%"></div>
        </div>
        <small class="text-muted" id="widget-salud-text">95%</small>
    </div>
    <div class="row text-center">
        <div class="col-6">
            <small class="text-muted">Alertas</small><br>
            <span class="badge bg-warning" id="widget-alertas">0</span>
        </div>
        <div class="col-6">
            <small class="text-muted">Estado</small><br>
            <span class="badge bg-success" id="widget-estado">OK</span>
        </div>
    </div>
    <a href="{{ route('admin.prevencion.dashboard') }}" class="btn btn-outline-primary btn-sm w-100 mt-2">
        Ver Dashboard
    </a>
</div>

<script>
// Actualizar widget del sidebar cada minuto
setInterval(function() {
    $.get('{{ route("admin.prevencion.estado_sistema") }}', function(data) {
        $('#widget-salud-bar').css('width', data.salud_sistema + '%');
        $('#widget-salud-text').text(data.salud_sistema + '%');
        $('#widget-alertas').text(data.total_alertas || 0);
        
        const $barra = $('#widget-salud-bar');
        const $estado = $('#widget-estado');
        $barra.removeClass('bg-success bg-warning bg-danger');
        $estado.removeClass('bg-success bg-warning bg-danger');
        
        if (data.salud_sistema >= 90) {
            $barra.addClass('bg-success');
            $estado.addClass('bg-success').text('OK');
        } else if (data.salud_sistema >= 70) {
            $barra.addClass('bg-warning');
            $estado.addClass('bg-warning').text('ALERTA');
        } else {
            $barra.addClass('bg-danger');
            $estado.addClass('bg-danger').text('CRÍTICO');
        }
    }).fail(function() {
        $('#widget-estado').removeClass('bg-success bg-warning').addClass('bg-danger').text('ERROR');
    });
}, 60000); // Cada minuto
</script>
