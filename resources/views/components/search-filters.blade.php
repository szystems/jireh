<div class="row mb-3">
    <div class="col-xl-12">
        <div class="card card-background-mask-info">
            <div class="card-header">
                <h6 class="card-title mb-0">{{ $title ?? 'Filtros y búsqueda' }}</h6>
            </div>
            <div class="card-body">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
