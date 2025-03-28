<div class="modal fade" id="createPagoModal" tabindex="-1" aria-labelledby="createPagoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="createPagoModalLabel">Registrar Nuevo Pago</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('pagos') }}" method="POST" enctype="multipart/form-data" id="formCreatePago">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="venta_id" value="{{ $venta->id }}">
                    <input type="hidden" name="usuario_id" value="{{ Auth::id() }}">

                    <div class="mb-3">
                        <label for="fecha" class="form-label">Fecha de Pago *</label>
                        <input type="date" class="form-control" id="fecha" name="fecha" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="monto" class="form-label">Monto *</label>
                        <div class="input-group">
                            <span class="input-group-text">{{ $config->currency_simbol }}</span>
                            @php
                                $totalPagado = 0;
                                if (isset($venta->pagos)) {
                                    $totalPagado = $venta->pagos->sum('monto');
                                }
                                $montoPendiente = max(0, $totalVenta - $totalPagado);
                            @endphp
                            <input type="number" class="form-control" id="monto" name="monto"
                                   step="0.01" min="0.01" max="{{ $montoPendiente }}"
                                   value="{{ number_format($montoPendiente, 2, '.', '') }}"
                                   data-saldo-pendiente="{{ $montoPendiente }}" required>
                        </div>
                        <div class="invalid-feedback" id="montoError" style="display: none;">
                            El monto no puede superar el saldo pendiente de {{ $config->currency_simbol }}.{{ number_format($montoPendiente, 2, '.', ',') }}
                        </div>
                        <small class="text-muted">Saldo pendiente: {{ $config->currency_simbol }}.{{ number_format($montoPendiente, 2, '.', ',') }}</small>
                    </div>

                    <div class="mb-3">
                        <label for="metodo_pago" class="form-label">Método de Pago *</label>
                        <select class="form-select" id="metodo_pago" name="metodo_pago" required>
                            @foreach(App\Models\Pago::$metodosPago as $valor => $etiqueta)
                                <option value="{{ $valor }}">{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="referencia" class="form-label">Referencia / Nº Comprobante</label>
                        <input type="text" class="form-control" id="referencia" name="referencia">
                        <small class="text-muted">Ej: Número de transacción, cheque, etc.</small>
                    </div>

                    <div class="mb-3">
                        <label for="comprobante_imagen" class="form-label">Comprobante (opcional)</label>
                        <input type="file" class="form-control" id="comprobante_imagen" name="comprobante_imagen" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnSubmitPago">Registrar Pago</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formCreatePago = document.getElementById('formCreatePago');
    const montoInput = document.getElementById('monto');
    const montoError = document.getElementById('montoError');
    const saldoPendiente = parseFloat(montoInput.dataset.saldoPendiente);

    formCreatePago.addEventListener('submit', function(e) {
        const montoValue = parseFloat(montoInput.value);

        if (montoValue > saldoPendiente) {
            e.preventDefault();
            montoError.style.display = 'block';
            montoInput.classList.add('is-invalid');
            return false;
        }

        montoError.style.display = 'none';
        montoInput.classList.remove('is-invalid');
    });

    montoInput.addEventListener('input', function() {
        if (parseFloat(this.value) > saldoPendiente) {
            montoError.style.display = 'block';
            this.classList.add('is-invalid');
        } else {
            montoError.style.display = 'none';
            this.classList.remove('is-invalid');
        }
    });
});
</script>
