<!-- Row start -->
<div class="row gx-3 mb-3">
    <div class="col-xl-12">
        <div class="card card-background-mask-info">
            <div class="card-body">
                <form action="{{ url('unidades') }}" method="GET">
                    @csrf
                    <div class="row">
                        <div class="col-md-8 col-lg-10">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input class="form-control" placeholder="Buscar por nombre o abreviatura..." name="funidad" value="{{ $queryUnidad }}" autofocus/>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-2 mt-2 mt-md-0">
                            <div class="d-grid">
                                <button class="btn btn-primary" type="submit">
                                    Buscar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Row end -->
