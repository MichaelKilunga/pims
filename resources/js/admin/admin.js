// PIMS Admin JS - Professional Standard
$(document).ready(function() {
    // Initialize DataTables
    $('.datatable').DataTable({
        pageLength: 25,
        responsive: true,
        dom: '<"d-flex justify-content-between align-items-center mb-3"lf>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records..."
        }
    });

    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    // Handle tooltips/popovers
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
