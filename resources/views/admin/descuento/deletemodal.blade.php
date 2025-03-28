<!-- Modal -->
<div class="modal fade" id="deleteModal-{{ $descuento->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title {{ $descuento->estado ? 'text-danger' : 'text-success' }}" id="deleteModal">
                @if($descuento->estado)
                  <i class="bi bi-dash-circle-fill text-danger"></i> Desactivar descuento
                @else
                  <i class="bi bi-check-circle-fill text-success"></i> Activar descuento
                @endif
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            @if($descuento->estado)
              <p>¿Está seguro de desactivar el descuento <strong>{{ $descuento->nombre }}</strong>?</p>
              <p class="text-muted small">Los descuentos desactivados no aparecerán en las listas por defecto.</p>
            @else
              <p>¿Está seguro de activar el descuento <strong>{{ $descuento->nombre }}</strong>?</p>
              <p class="text-muted small">Los descuentos activos aparecerán en las listas por defecto.</p>
            @endif
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="bi bi-x-circle"></i> Cancelar
              </button>
              <a href="{{ url('delete-descuento/'.$descuento->id) }}" class="btn {{ $descuento->estado ? 'btn-danger' : 'btn-success' }}">
                @if($descuento->estado)
                  <i class="bi bi-dash-circle"></i> Desactivar
                @else
                  <i class="bi bi-check-circle"></i> Activar
                @endif
              </a>
          </div>
      </div>
  </div>
</div>
