<!-- Row start -->
<div class="row gx-3">
    <div class="col-xl-12">
        <div class="card card-background-mask-info">
            {{-- <div class="card-header">
                <div class="card-title"><u>Doctores</u></div>
            </div> --}}
            <div class="card-body">
                <form action="{{ url('vehiculos') }}" method="GET">
                    @csrf
                    <div class="input-group">
                        <input class="form-control" list="exampleDataList" id="exampleDataList" placeholder="Buscar Vehículo ..." name="fvehiculo" value="{{ $queryVehiculo }}"/>
                        <datalist id="exampleDataList">
                            @if ($queryVehiculo != null)
                                <option selected value="{{ $queryVehiculo }}" >{{ $queryVehiculo }}</option>
                            @endif
                            @foreach ($filterVehiculos as $item)
                                <option value="{{ $item->nombre }}">{{ $item->nombre }}</option>
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
