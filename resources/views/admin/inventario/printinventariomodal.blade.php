<!-- Modal -->
<div class="modal fade" id="printInventarioModal" tabindex="-1" aria-labelledby="printInventarioModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printInventarioModalTitle">
                    <i class="bi bi-printer text-info"></i> Imprimir
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('print-inventario') }}" method="GET" target="_blank">
                <div class="modal-body">

                    <div class="row gx-3 p-3">

                        <div class="col-md-4 mb-3">
                            <!-- Form Field Start -->
                            <div class="mb-3">
                                <label for="pdftamaño" class="form-label">Tamaño</label>
                                <select name="pdftamaño" class="form-select" aria-label="Default select example">
                                    <option value="Letter"{{ request('pdftamaño') == 'Letter' ? ' selected' : '' }}>Letter</option>
                                    <option value="A4"{{ request('pdftamaño') == 'A4' ? ' selected' : '' }}>A4</option>
                                    <option value="Legal"{{ request('pdftamaño') == 'Legal' ? ' selected' : '' }}>Legal</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <!-- Form Field Start -->
                            <div class="mb-3">
                                <label for="pdfhorientacion" class="form-label">Orientación</label>
                                <select name="pdfhorientacion" class="form-select" aria-label="Default select example">
                                    <option value="portrait"{{ request('pdfhorientacion') == 'portrait' ? ' selected' : '' }}>Vertical</option>
                                    <option value="landscape"{{ request('pdfhorientacion') == 'landscape' ? ' selected' : '' }}>Horizontal</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <!-- Form Field Start -->
                            <div class="mb-3">
                                <label for="pdfarchivo" class="form-label">Archivo</label>
                                <select name="pdfarchivo" class="form-select" aria-label="Default select example">
                                    <option value="stream"{{ request('pdfarchivo') == 'stream' ? ' selected' : '' }}>Vista previa</option>
                                    <option value="download"{{ request('pdfarchivo') == 'download' ? ' selected' : '' }}>Descargar</option>
                                </select>
                            </div>
                        </div>

                        <!-- Pasar los filtros actuales -->
                        <input type="hidden" name="articulo" value="{{ request('articulo') }}">
                        <input type="hidden" name="categoria" value="{{ request('categoria') }}">
                        <input type="hidden" name="stock" value="{{ request('stock') }}">
                        <input type="hidden" name="stock_minimo" value="{{ request('stock_minimo') }}">
                        <input type="hidden" name="tipo" value="{{ request('tipo') }}">
                        <input type="hidden" name="ordenar" value="{{ request('ordenar') }}">

                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-info">
                        <i class="bi bi-printer"></i> Imprimir
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

