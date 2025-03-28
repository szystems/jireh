
<!-- Modal -->
<div class="modal fade" id="printVehiculoModal{{ $vehiculo->id }}" tabindex="-1" aria-labelledby="printVehiculoModal{{ $vehiculo->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printVehiculoModal{{ $vehiculo->id }}Title">
                    <i class="bi bi-printer text-info"></i> Imprimir
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('print-vehiculo') }}" method="GET" target="_blank">
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
                                    <option value="portrait "{{ request('pdfhorientacion') == 'portrait' ? ' selected' : '' }}>portrait</option>
                                    <option value="landscape"{{ request('pdfhorientacion') == 'landscape' ? ' selected' : '' }}>landscape</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <!-- Form Field Start -->
                            <div class="mb-3">
                                <label for="pdfarchivo" class="form-label">Archivo</label>
                                <select name="pdfarchivo" class="form-select" aria-label="Default select example">
                                    <option value="stream"{{ request('pdfarchivo') == 'stream' ? ' selected' : '' }}>stream</option>
                                    <option value="download "{{ request('pdfarchivo') == 'download' ? ' selected' : '' }}>download</option>
                                </select>
                            </div>
                        </div>

                        <input type="hidden" name="vehiculo_id" value="{{ $vehiculo->id }}">

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

