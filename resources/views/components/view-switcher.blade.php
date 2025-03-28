<div class="btn-group me-2">
    <button type="button" class="btn btn-outline-secondary btn-sm view-option" data-view="table" data-storage-key="{{ $storageKey }}">
        <i class="bi bi-table"></i>
    </button>
    <button type="button" class="btn btn-outline-secondary btn-sm view-option" data-view="cards" data-storage-key="{{ $storageKey }}">
        <i class="bi bi-grid-3x3-gap"></i>
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const storageKey = '{{ $storageKey }}';
        const viewButtons = document.querySelectorAll('.view-option[data-storage-key="{{ $storageKey }}"]');
        const tableView = document.querySelector('.table-view[data-storage-key="{{ $storageKey }}"]');
        const cardsView = document.querySelector('.cards-view[data-storage-key="{{ $storageKey }}"]');

        // Set initial state based on localStorage
        const savedView = localStorage.getItem(storageKey) || 'table';
        setActiveView(savedView);

        // Add click handlers
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                const view = this.getAttribute('data-view');
                setActiveView(view);
                localStorage.setItem(storageKey, view);
            });
        });

        function setActiveView(view) {
            // Update buttons
            viewButtons.forEach(btn => {
                if (btn.getAttribute('data-view') === view) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            // Update views
            if (tableView && cardsView) {
                if (view === 'table') {
                    tableView.classList.remove('d-none');
                    cardsView.classList.add('d-none');
                } else {
                    tableView.classList.add('d-none');
                    cardsView.classList.remove('d-none');
                }
            }
        }
    });
</script>
