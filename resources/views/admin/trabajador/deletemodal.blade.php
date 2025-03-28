<!-- Modal -->
<div class="modal fade" id="deleteModal-{{ $trabajador->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title {{ $trabajador->estado === 'activo' ? 'text-danger' : 'text-success' }}" id="deleteModal">
                @if($trabajador->estado === 'activo')
                  <i class="bi bi-person-dash-fill text-danger"></i> Desactivar trabajador
                @else
                  <i class="bi bi-person-check-fill text-success"></i> Activar trabajador
                @endif
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            @if($trabajador->estado === 'activo')
              <p>¿Está seguro de desactivar a <strong>{{ $trabajador->nombre }}</strong>?</p>
              <p class="text-muted small">Los trabajadores desactivados no aparecerán en las listas por defecto.</p>
            @else
              <p>¿Está seguro de activar a <strong>{{ $trabajador->nombre }}</strong>?</p>
              <p class="text-muted small">Los trabajadores activos aparecerán en las listas por defecto.</p>
            @endif
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="bi bi-x-circle"></i> Cancelar
              </button>
              <a href="{{ url('delete-trabajador/'.$trabajador->id) }}" class="btn {{ $trabajador->estado === 'activo' ? 'btn-danger' : 'btn-success' }}">
                @if($trabajador->estado === 'activo')
                  <i class="bi bi-person-dash"></i> Desactivar
                @else
                  <i class="bi bi-person-check"></i> Activar
                @endif
              </a>
          </div>
      </div>
  </div>
</div>
