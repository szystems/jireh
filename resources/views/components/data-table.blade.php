<div class="table-view{{ isset($view) && $view === 'cards' ? ' d-none' : '' }}" data-storage-key="{{ $storageKey ?? 'viewMode' }}">
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle">
            <thead class="table-light">
                {{ $header }}
            </thead>
            <tbody>
                {{ $body }}
            </tbody>
            @if(isset($footer))
            <tfoot>
                {{ $footer }}
            </tfoot>
            @endif
        </table>
    </div>
</div>
