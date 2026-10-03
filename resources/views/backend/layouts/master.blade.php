<!DOCTYPE html>
<html lang="en">

<head>
    <script>
        (function() {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark-theme');
            }
        })();
    </script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="My Software">
    <meta name="keywords" content="admin, software">
    <meta name="author" content="Atrytech Information Technology">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>{{ get_setting('com_name') }} | @yield('page-title')</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ (!empty(get_setting('system_icon')))?url('uploads/logo/'.get_setting('system_icon')):url('backend/images/no_images.png') }}">
    

    
    <!-- Start css -->

    <!-- Slick css -->
    <link href="{{ asset('backend') }}/plugins/slick/slick.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('backend') }}/css/all.min.css">
    <link href="{{ asset('backend') }}/plugins/slick/slick-theme.css" rel="stylesheet">
    <link href="{{ asset('backend') }}/plugins/select2/select2.css" rel="stylesheet">
    @include('backend.layouts.includes.critical-styles')
    <link href="{{ asset('backend') }}/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend') }}/css/bootstrap-fileupload.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend') }}/css/icons.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend') }}/css/flag-icon.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend') }}/css/style.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend') }}/css/multi-dash.css" rel="stylesheet" type="text/css">
    <!-- Summernote css -->
    <link href="{{ asset('backend') }}/plugins/summernote/summernote-bs4.css" rel="stylesheet">
    <!-- ToastMagic css -->
    {!! ToastMagic::styles() !!}
    <!-- toggle css -->
    <link href="{{ asset('backend') }}/css/bootstrap-toggle.min.css" rel="stylesheet" type="text/css">
    <!-- End css -->
    
    <!-- Start script -->
    <link href="{{ asset('backend') }}/css/jquery-ui.css" rel="stylesheet" type="text/css">
    <script src="{{ asset('backend') }}/js/jquery.min.js"></script>
    <script src="{{ asset('backend') }}/js/jquery-ui.js"></script>
    <!-- End script -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('css')
</head>

<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 transition-colors duration-300 {{ Route::currentRouteName() == 'dashboard' ? 'dashboard-page' : '' }}">
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-theme');
        }
    </script>
    <div id="containerbar" class="main-container">
        @php
            $route = Route::currentRouteName();
        @endphp
        <!-- Mobile Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeMobileSidebar()"></div>
        @include('backend.layouts.includes.sidebar')
        <div class="rightbar">

            @include('backend.layouts.includes.header')
            @if ($route == 'invoice.create'|| $route == 'quotation.create' || $route == 'quotation.edit' || $route == 'quotation.print' || $route == 'purchase.return.print' || $route == 'stock-adjust.show' ||$route == 'stock-adjust.create'||$route == 'employee.salary.details'|| $route == 'transfer.print'|| $route == 'transfer.create' || $route == 'purchase.imei-print' || $route == 'due.invoice.print' || $route == 'used.create' || $route == 'return.create' || $route == 'damage.create' || $route == 'invoice.exchange' || $route == 'inv.edit' || $route == 'invoice.print' || $route == 'purchase.print')
                <div class="flex-grow w-full">
                    @yield('invoice')
                </div>
            @else
                <div class="contentbar">
                    @if ($route != 'dashboard' && $route != 'setting.index')
                        @include('backend.layouts.includes.breadcrumb')
                    @endif
                    @yield('content')
                </div>
            @endif
            @include('backend.layouts.includes.footer')
        </div>
    </div>
    <!-- Start js -->
    <script src="{{ asset('backend') }}/js/popper.min.js"></script>
    <script src="{{ asset('backend') }}/js/bootstrap.min.js"></script>
    <script src="{{ asset('backend') }}/js/modernizr.min.js"></script>
    <script src="{{ asset('backend') }}/js/detect.js"></script>
    <!-- Slick js -->
    <script src="{{ asset('backend') }}/plugins/slick/slick.min.js"></script>

    <!-- Select2 js -->
    <script src="{{ asset('backend') }}/plugins/select2/select2.min.js"></script>

    <!--select2-->
    <script>
        $(document).ready(function() {
            if ($.isFunction($.fn.select2)) {
                $('.select2').each(function() {
                    var $this = $(this);
                    var modal = $this.closest('.modal');
                    if (modal.length) {
                        $this.select2({
                            width: '100%',
                            theme: 'default',
                            dropdownParent: modal
                        });
                    } else {
                        $this.select2({
                            width: '100%',
                            theme: 'default'
                        });
                    }
                });
            }

            // Auto-submit filter forms on change
            $(document).on('change', 'form[method="GET"] select, form[method="GET"] input[type="date"], form[method="GET"] input[type="text"]', function() {
                $(this).closest('form').submit();
            });

            // Auto-open modal if 'add=1' is in URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('add')) {
                $('#addModal').modal('show');
            }
        });

        // Disable mouse wheel scroll changing value on number inputs
        $(document).on("wheel", "input[type=number]", function (e) {
            $(this).blur();
        });

        // Handle print events to disable dark mode temporarily during print
        window.addEventListener('beforeprint', () => {
            if (document.documentElement.classList.contains('dark-theme')) {
                document.documentElement.classList.remove('dark-theme');
                document.documentElement.classList.add('was-dark-theme');
            }
            if (document.body.classList.contains('dark-theme')) {
                document.body.classList.remove('dark-theme');
                document.body.classList.add('was-dark-theme');
            }
        });

        window.addEventListener('afterprint', () => {
            if (document.documentElement.classList.contains('was-dark-theme')) {
                document.documentElement.classList.add('dark-theme');
                document.documentElement.classList.remove('was-dark-theme');
            }
            if (document.body.classList.contains('was-dark-theme')) {
                document.body.classList.add('dark-theme');
                document.body.classList.remove('was-dark-theme');
            }
        });

        $(document).ready(function() {
            // Dynamically inject print CSS to hide Action columns for any table
            $('table').each(function(tableIndex) {
                let table = $(this);
                // Assign a unique ID if missing so we can target it in CSS
                if (!table.attr('id')) {
                    table.attr('id', 'print-table-auto-' + tableIndex);
                }
                let tableId = table.attr('id');
                let hiddenIndexes = [];
                table.find('thead th').each(function(index) {
                    let text = $(this).text().trim().toLowerCase();
                    if (text === 'action' || text === 'actions') {
                        hiddenIndexes.push(index + 1); // nth-child is 1-indexed
                    }
                    
                    @if(request()->routeIs('invoice.index'))
                    if (text === 'status') {
                        hiddenIndexes.push(index + 1);
                    }
                    @endif
                });
                
                if (hiddenIndexes.length > 0) {
                    let cssRules = '';
                    hiddenIndexes.forEach(function(colIndex) {
                        cssRules += '#' + tableId + ' th:nth-child(' + colIndex + '), #' + tableId + ' td:nth-child(' + colIndex + ') { display: none !important; } ';
                    });
                    
                    let style = '<style> @media print { ' + cssRules + '} </style>';
                    $('head').append(style);
                }
            });
        });
    </script>

    <!-- Summernote js -->
    <script src="{{ asset('backend') }}/plugins/summernote/summernote-bs4.min.js"></script>
    <!-- Core js -->
    <script src="{{ asset('backend') }}/js/core.js"></script>
    <script src="{{ asset('backend') }}/js/bootstrap-fileupload.js"></script>
    <script src="{{ asset('backend') }}/js/status-update.js"></script>
    <!-- toggle js -->
    <script src="{{ asset('backend') }}/js/bootstrap-toggle.min.js"></script>
    <!-- ToastMagic js -->
    {!! ToastMagic::scripts() !!}

    <!-- Toast Notifications Handler -->
    @include('backend.layouts.includes.toasts')
    
    <!-- End js -->
    <!-- Navigation & UI Scripts -->
    @include('backend.layouts.includes.sidebar-scripts')
    @include('backend.layouts.includes.button-loader-scripts')
    @if(!request()->routeIs('invoice.create') && !request()->routeIs('quotation.create') && !request()->routeIs('inv.edit'))
        @include('backend.layouts.includes.barcode-scanner-scripts')
    @endif
    <!-- AI Chatbot UI -->
    @if(!request()->routeIs('invoice.create'))
        @include('backend.layouts.includes.chatbot')
    @endif
    @stack('js')
</body>

</html>
