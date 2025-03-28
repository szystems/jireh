<!-- Row start -->
<div class="row gx-3">
    <div class="col-xl-12">
        <div class="card card-background-mask-info">
            <div class="card-body">
                <form action="{{ url('trabajadores') }}" method="GET">
                    @csrf
                    <div class="input-group">
                        <input class="form-control" list="exampleDataList" id="exampleDataList" placeholder="Buscar Trabajador ..." name="search" value="{{ request()->input('search') }}"/>
                        <datalist id="exampleDataList">
                            @foreach ($trabajadores as $trabajador)
                                <option value="{{ $trabajador->nombre }}">{{ $trabajador->nombre }}</option>
                            @endforeach
                        </datalist>
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Row end -->
