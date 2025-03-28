<div class="cards-view row g-3{{ isset($view) && $view === 'table' ? ' d-none' : ' d-none' }}" data-storage-key="{{ $storageKey ?? 'viewMode' }}">
    {{ $slot }}
</div>
