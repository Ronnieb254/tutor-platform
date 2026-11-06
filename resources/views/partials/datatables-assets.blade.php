{{-- DataTables Assets & Initialization --}}
{{-- jQuery (required for DataTables) --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

{{-- DataTables Core --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.11/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.11/js/jquery.dataTables.min.js"></script>

{{-- DataTables Responsive Extension --}}
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

{{-- DataTables Buttons Extension --}}
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>

{{-- Export Dependencies --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

{{-- Export Buttons --}}
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

{{-- Font Awesome for button icons (if not already loaded) --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* DataTables Custom Styling to Match Application Theme */
    
    /* Override default DataTables wrapper */
    .dataTables_wrapper {
        font-family: 'DM Sans', sans-serif;
    }
    
    /* Table styling */
    table.dataTable {
        border-collapse: collapse !important;
        border: 1px solid var(--hairline, rgba(21,40,31,0.1));
        border-radius: 8px;
        overflow: hidden;
    }
    
    table.dataTable thead th {
        background: rgba(21,40,31,.03);
        border-bottom: 1.5px solid var(--hairline, rgba(21,40,31,0.1));
        color: var(--ink, #15281F);
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        padding: 12px 16px;
    }
    
    table.dataTable tbody tr {
        border-bottom: 1px solid rgba(21,40,31,0.05);
    }
    
    table.dataTable tbody tr:hover {
        background: #FAFCFA !important;
    }
    
    table.dataTable tbody td {
        padding: 13px 16px;
        font-size: 13.5px;
        color: var(--ink, #15281F);
        vertical-align: middle;
    }
    
    /* Top controls layout */
    .dt-top {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
        padding: 12px;
        background: #F9FAFB;
        border-radius: 10px;
    }
    
    .dt-length {
        flex: 0 0 auto;
    }
    
    .dt-buttons {
        flex: 1 1 auto;
        display: flex;
        gap: 6px;
        justify-content: center;
    }
    
    .dt-search {
        flex: 0 0 auto;
        margin-left: auto;
    }
    
    /* Length selector */
    .dataTables_length select {
        height: 36px;
        padding: 0 32px 0 12px;
        border-radius: 10px;
        border: 1.5px solid var(--hairline, rgba(21,40,31,0.1));
        background: #fff;
        font-size: 13px;
        font-family: 'DM Sans', sans-serif;
        color: var(--ink, #15281F);
        outline: none;
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2315281F' d='M6 8L2 4h8z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
    }
    
    .dataTables_length label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 500;
        color: var(--muted, rgba(21,40,31,0.5));
    }
    
    /* Search input */
    .dataTables_filter input {
        height: 36px;
        padding: 0 12px;
        border-radius: 10px;
        border: 1.5px solid var(--hairline, rgba(21,40,31,0.1));
        background: #fff;
        font-size: 13px;
        font-family: 'DM Sans', sans-serif;
        color: var(--ink, #15281F);
        outline: none;
        width: 220px;
        transition: border-color 0.15s, box-shadow 0.15s;
    }
    
    .dataTables_filter input:focus {
        border-color: var(--trust-blue, #0A64BC);
        box-shadow: 0 0 0 3px rgba(10,100,188,0.1);
    }
    
    .dataTables_filter input::placeholder {
        color: rgba(21,40,31,0.35);
    }
    
    .dataTables_filter label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 500;
        color: var(--muted, rgba(21,40,31,0.5));
    }
    
    /* Export buttons */
    .btn-dt {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        height: 36px;
        padding: 0 14px;
        border-radius: 10px;
        border: 1.5px solid var(--hairline, rgba(21,40,31,0.1));
        background: #fff;
        font-size: 12px;
        font-weight: 600;
        font-family: 'DM Sans', sans-serif;
        color: var(--ink, #15281F);
        cursor: pointer;
        transition: all 0.15s;
    }
    
    .btn-dt:hover {
        border-color: var(--trust-blue, #0A64BC);
        background: rgba(10,100,188,0.05);
        color: var(--trust-blue, #0A64BC);
    }
    
    .btn-dt i {
        font-size: 11px;
    }
    
    /* Bottom controls */
    .dt-bottom {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-top: 16px;
        padding-top: 12px;
        border-top: 1px solid var(--hairline, rgba(21,40,31,0.1));
    }
    
    .dt-info {
        font-size: 13px;
        color: var(--muted, rgba(21,40,31,0.5));
    }
    
    /* Pagination */
    .dt-pagination .pagination {
        display: flex;
        gap: 4px;
        margin: 0;
    }
    
    .dataTables_paginate .paginate_button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 10px;
        border-radius: 8px;
        border: 1px solid var(--hairline, rgba(21,40,31,0.1));
        background: #fff;
        font-size: 13px;
        font-weight: 500;
        color: var(--ink, #15281F);
        cursor: pointer;
        transition: all 0.15s;
        text-decoration: none;
    }
    
    .dataTables_paginate .paginate_button:hover {
        border-color: var(--trust-blue, #0A64BC);
        background: rgba(10,100,188,0.05);
        color: var(--trust-blue, #0A64BC);
    }
    
    .dataTables_paginate .paginate_button.current {
        border-color: var(--trust-blue, #0A64BC);
        background: var(--trust-blue, #0A64BC);
        color: #fff;
    }
    
    .dataTables_paginate .paginate_button.disabled {
        opacity: 0.4;
        cursor: not-allowed;
        pointer-events: none;
    }
    
    /* Responsive controls */
    table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control:before,
    table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control:before {
        background-color: var(--trust-blue, #0A64BC);
        box-shadow: 0 0 0 3px rgba(10,100,188,0.1);
    }
    
    table.dataTable.dtr-inline.collapsed > tbody > tr.parent > td.dtr-control:before,
    table.dataTable.dtr-inline.collapsed > tbody > tr.parent > th.dtr-control:before {
        background-color: var(--coral, #E8522A);
    }
    
    /* Sorting icons */
    table.dataTable thead .sorting:before,
    table.dataTable thead .sorting_asc:before,
    table.dataTable thead .sorting_desc:before,
    table.dataTable thead .sorting_asc_disabled:before,
    table.dataTable thead .sorting_desc_disabled:before {
        right: 1em;
        opacity: 0.3;
    }
    
    table.dataTable thead .sorting:after,
    table.dataTable thead .sorting_asc:after,
    table.dataTable thead .sorting_desc:after,
    table.dataTable thead .sorting_asc_disabled:after,
    table.dataTable thead .sorting_desc_disabled:after {
        right: 0.5em;
        opacity: 0.3;
    }
    
    /* Mobile responsive */
    @media (max-width: 768px) {
        .dt-top {
            flex-direction: column;
            align-items: stretch;
        }
        
        .dt-buttons {
            flex-wrap: wrap;
            justify-content: flex-start;
        }
        
        .dt-search {
            margin-left: 0;
        }
        
        .dataTables_filter input {
            width: 100%;
        }
        
        .dt-bottom {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<script>
    /**
     * Initialize DataTable with consistent configuration
     * @param {string} selector - jQuery selector for the table
     * @param {object} customOptions - Custom options to merge with defaults
     */
    function initDataTable(selector, customOptions = {}) {
        try {
            const defaultOptions = {
                responsive: true,
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                dom: '<"dt-top"<"dt-length"l><"dt-buttons"B><"dt-search"f>>rt<"dt-bottom"<"dt-info"i><"dt-pagination"p>>',
                buttons: [
                    { 
                        extend: 'copy', 
                        text: '<span style="font-size:inherit;">❓</span> Copy', 
                        className: 'btn-dt',
                        exportOptions: {
                            columns: ':visible:not(.no-export)'
                        }
                    },
                    { 
                        extend: 'csv', 
                        text: '<span style="font-size:inherit;">📄</span> CSV', 
                        className: 'btn-dt',
                        exportOptions: {
                            columns: ':visible:not(.no-export)'
                        }
                    },
                    { 
                        extend: 'excel', 
                        text: '<span style="font-size:inherit;">📄</span> Excel', 
                        className: 'btn-dt',
                        exportOptions: {
                            columns: ':visible:not(.no-export)'
                        }
                    },
                    { 
                        extend: 'pdf', 
                        text: '<span style="font-size:inherit;">📄</span> PDF', 
                        className: 'btn-dt',
                        exportOptions: {
                            columns: ':visible:not(.no-export)'
                        }
                    },
                    { 
                        extend: 'print', 
                        text: '<span style="font-size:inherit;">🖨️</span> Print', 
                        className: 'btn-dt',
                        exportOptions: {
                            columns: ':visible:not(.no-export)'
                        }
                    }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search...",
                    lengthMenu: "Show _MENU_",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "No entries found",
                    infoFiltered: "(filtered from _MAX_ total entries)",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Prev"
                    },
                    emptyTable: "No data available in table",
                    zeroRecords: "No matching records found"
                },
                order: [[0, 'asc']] // Default sort by first column ascending
            };
            
            // Merge custom options with defaults
            const options = $.extend(true, {}, defaultOptions, customOptions);
            
            // Initialize DataTable
            const table = $(selector).DataTable(options);
            
            console.log('DataTables initialized successfully for:', selector);
            return table;
            
        } catch (error) {
            console.error('DataTables initialization failed for', selector, ':', error);
            return null;
        }
    }
    
    // Make jQuery available globally without conflicting with Alpine.js
    window.jQuery = window.$ = jQuery;
    
    // Ensure DataTables waits for DOM to be ready
    $(document).ready(function() {
        console.log('DataTables assets loaded and ready');
    });
</script>
