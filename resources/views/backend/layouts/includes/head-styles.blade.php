<style>
    /* Flash of White Fix for Select2 in Dark Mode */
    .dark-theme .select2-container--default .select2-selection--single,
    .dark-theme .select2-container--default .select2-selection--multiple {
        background-color: #151b2e !important;
        border-color: #242f49 !important;
    }

    .dark-theme .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #ffffff !important;
    }

    .dark-theme .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #94a3b8 !important;
    }

    /* Global Dark Theme Select2 Dropdown styles */
    .dark-theme .select2-dropdown {
        background-color: #121829 !important;
        border-color: #242f49 !important;
        color: #ffffff !important;
    }
    .dark-theme .select2-container--default .select2-search--dropdown .select2-search__field {
        background-color: #151b2e !important;
        border: 1px solid #242f49 !important;
        color: #ffffff !important;
    }
    .dark-theme .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #1c233a !important;
        color: #ffffff !important;
    }
    .dark-theme .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #2563eb !important;
        color: #ffffff !important;
    }
    .dark-theme .select2-container--default .select2-results__option {
        color: #cbd5e1 !important;
    }

    /* Hide raw select to prevent FOUC */
    select.select2 {
        visibility: hidden;
        height: 0;
        display: block;
    }
</style>
