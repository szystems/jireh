<div class="modal fade" id="deletePagoModal{{ $pago->id }}" tabindex="-1" aria-labelledby="deletePagoModalLabel{{ $pago->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deletePagoModalLabel{{ $pago->id }}">Eliminar Pago</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea eliminar este pago?</p>
                <div class="alert alert-warning">
                    <strong>Detalles del Pago:</strong><br>
                    <ul class="mb-0">
                        <li>Fecha: {{ \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y') }}</li>
                        <li>Monto: {{ $config->currency_simbol }}.{{ number_format($pago->monto, 2) }}</li>
                        <li>Método: {{ $pago->nombre_metodo_pago }}</li>
                        <li>Referencia: {{ $pago->referencia ?: 'Sin referencia' }}</li>
                    </ul>
                </div>
                <p class="text-danger">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ url('pagos/'.$pago->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar Pago</button>
                </form>
            </div>
        </div>
    </div>
</div>
