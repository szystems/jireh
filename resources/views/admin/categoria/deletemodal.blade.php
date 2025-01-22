

  <!-- Modal -->
  <div class="modal fade" id="deleteModal-{{ $categoria->id }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deleteModal" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title text-danger" id="deleteModal">
                <i class="bi bi-trash-fill text-danger"></i> Eliminar Categoría
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">¿Está seguro de eliminar esta categoria?</div>
          <div class="modal-footer">
              <button type="button" class="btn btn-info" data-bs-dismiss="modal">
                <i class="bi bi-x-circle"></i> Cancelar
              </button>
              <a href="{{ url('delete-categoria/'.$categoria->id) }}" type="button" class="btn btn-danger">
                <i class="bi bi-trash"></i> Eliminar
              </a>
          </div>
      </div>
  </div>
</div>
