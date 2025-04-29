<!-- Modal -->
<div class="modal fade" id="deleteModal-{{ $tipoTrabajador->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="deleteModal">
                    <i class="bi bi-trash-fill text-danger"></i> Eliminar Tipo de Trabajador
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de eliminar este tipo de trabajador?</p>

                @if($tipoTrabajador->trabajadores()->count() > 0)
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Este tipo tiene {{ $tipoTrabajador->trabajadores()->count() }} trabajadores asociados.
                        La eliminación no será posible hasta que reasigne estos trabajadores a otro tipo.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-info" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                @if($tipoTrabajador->trabajadores()->count() == 0)
                    <a href="{{ url('delete-tipo-trabajador/'.$tipoTrabajador->id) }}" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Eliminar
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
