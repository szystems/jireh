<!-- Row start -->
<div class="row gx-3">
    <div class="col-xl-12">
        <div class="card card-background-mask-info">
            {{-- <div class="card-header">
                <div class="card-title"><u>Doctores</u></div>
            </div> --}}
            <div class="card-body">
                <form action="{{ url('users') }}" method="GET">
                    @csrf
                    <div class="input-group">
                        <input class="form-control" list="exampleDataList" id="exampleDataList" placeholder="Buscar Usuario ..." name="fuser" value="{{ $queryUser }}"/>
                        <datalist id="exampleDataList">
                            @if ($queryUser != null)
                                <option selected value="{{ $queryUser }}" >{{ $queryUser }}</option>
                            @endif
                            @foreach ($filterUsers as $item)
                                <option value="{{ $item->name }}">{{ $item->name }}</option>
                            @endforeach
                        </datalist>
                        <button class="btn btn-outline-secondary" type="button">
                            Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Row end -->
