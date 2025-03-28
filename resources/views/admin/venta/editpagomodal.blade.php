<div class="modal fade" id="editPagoModal{{ $pago->id }}" tabindex="-1" aria-labelledby="editPagoModalLabel{{ $pago->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="editPagoModalLabel{{ $pago->id }}">Editar Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('pagos/'.$pago->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="venta_id" value="{{ $venta->id }}">
                    <input type="hidden" name="usuario_id" value="{{ $pago->usuario_id }}">

                    <div class="mb-3">
                        <label for="fecha{{ $pago->id }}" class="form-label">Fecha de Pago *</label>
                        <input type="date" class="form-control" id="fecha{{ $pago->id }}" name="fecha" value="{{ $pago->fecha }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="monto{{ $pago->id }}" class="form-label">Monto *</label>
                        <div class="input-group">
                            <span class="input-group-text">{{ $config->currency_simbol }}</span>
                            @php
                                $totalPagadoOtros = $venta->pagos->where('id', '!=', $pago->id)->sum('monto');
                                $montoPendienteMax = max(0, $totalVenta - $totalPagadoOtros);
                            @endphp
                            <input type="number" class="form-control" id="monto{{ $pago->id }}" name="monto"
                                   step="0.01" min="0.01" max="{{ $montoPendienteMax }}"
                                   value="{{ number_format($pago->monto, 2, '.', '') }}"
                                   data-saldo-pendiente="{{ $montoPendienteMax }}"
                                   data-pago-actual="{{ $pago->monto }}" required>
                        </div>
                        <div class="invalid-feedback" id="montoError{{ $pago->id }}" style="display: none;">
                            El monto no puede superar el saldo pendiente (incluyendo este pago) de {{ $config->currency_simbol }}.{{ number_format($montoPendienteMax, 2, '.', ',') }}
                        </div>
                        <small class="text-muted">Monto máximo permitido: {{ $config->currency_simbol }}.{{ number_format($montoPendienteMax, 2, '.', ',') }}</small>
                    </div>

                    <div class="mb-3">
                        <label for="metodo_pago{{ $pago->id }}" class="form-label">Método de Pago *</label>
                        <select class="form-select" id="metodo_pago{{ $pago->id }}" name="metodo_pago" required>
                            @foreach(App\Models\Pago::$metodosPago as $valor => $etiqueta)
                                <option value="{{ $valor }}" {{ $pago->metodo_pago == $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="referencia{{ $pago->id }}" class="form-label">Referencia / Nº Comprobante</label>
                        <input type="text" class="form-control" id="referencia{{ $pago->id }}" name="referencia" value="{{ $pago->referencia }}">
                    </div>

                    <div class="mb-3">
                        <label for="comprobante_imagen{{ $pago->id }}" class="form-label">Comprobante (opcional)</label>
                        @if($pago->comprobante_imagen)
                            <div class="mb-2">
                                <a href="{{ asset($pago->comprobante_imagen) }}" target="_blank">
                                    Ver comprobante actual
                                </a>
                            </div>
                        @endif
                        <input type="file" class="form-control" id="comprobante_imagen{{ $pago->id }}" name="comprobante_imagen" accept="image/*">
                        <small class="text-muted">Deje en blanco para mantener el comprobante actual</small>
                    </div>

                    <div class="mb-3">
                        <label for="observaciones{{ $pago->id }}" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones{{ $pago->id }}" name="observaciones" rows="3">{{ $pago->observaciones }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Actualizar Pago</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const montoInput{{ $pago->id }} = document.getElementById('monto{{ $pago->id }}');
    const montoError{{ $pago->id }} = document.getElementById('montoError{{ $pago->id }}');
    const saldoPendiente{{ $pago->id }} = parseFloat(montoInput{{ $pago->id }}.dataset.saldoPendiente);

    document.getElementById('editPagoModal{{ $pago->id }}').querySelector('form').addEventListener('submit', function(e) {
        const montoValue = parseFloat(montoInput{{ $pago->id }}.value);

        if (montoValue > saldoPendiente{{ $pago->id }}) {
            e.preventDefault();
            montoError{{ $pago->id }}.style.display = 'block';
            montoInput{{ $pago->id }}.classList.add('is-invalid');
            return false;
        }

        montoError{{ $pago->id }}.style.display = 'none';
        montoInput{{ $pago->id }}.classList.remove('is-invalid');
    });

    montoInput{{ $pago->id }}.addEventListener('input', function() {
        if (parseFloat(this.value) > saldoPendiente{{ $pago->id }}) {
            montoError{{ $pago->id }}.style.display = 'block';
            this.classList.add('is-invalid');
        } else {
            montoError{{ $pago->id }}.style.display = 'none';
            this.classList.remove('is-invalid');
        }
    });
});
</script>
