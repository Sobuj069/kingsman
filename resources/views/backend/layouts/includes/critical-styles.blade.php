<style>
    /* Critical CSS to prevent Select2 FOUC (Flash of Unstyled Content) */
    select.select2, 
    .select2-container {
        visibility: hidden !important;
    }
    
    .select2-container.select2-container--default, 
    .select2-container.select2-container--open, 
    .select2-container.select2-container--focus,
    .select2-container--enabled {
        visibility: visible !important;
    }

    /* Global Print Layout Optimization Styles */
    @media print {
        .modern-sidebar,
        .sidebar-overlay,
        header,
        footer,
        nav,
        .md\:hidden,
        .md\:hidden.h-20,
        button:not(.print-keep),
        .btn:not(.print-keep),
        .no-print,
        .print-hide,
        #chatbot,
        .chatbot-btn {
            display: none !important;
            height: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .rightbar {
            margin-left: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        
        .main-container,
        #containerbar,
        .contentbar,
        .invoice-contentbar {
            margin: 0 !important;
            padding: 0 !important;
            background: transparent !important;
            border: none !important;
            width: 100% !important;
        }
        
        body {
            background-color: #fff !important;
            color: #000 !important;
        }
    }
</style>
