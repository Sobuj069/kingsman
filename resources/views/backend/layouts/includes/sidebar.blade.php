@php
    $currentRoute = Route::currentRouteName();
    $currentUrl = request()->path();
    $hiddenModules = json_decode(get_setting('hidden_sidebar_modules'), true) ?: [];

    if (!function_exists('sidebarLink')) {
        function sidebarLink($href, $icon, $label, $active = false) {
            $activeClass = $active
                ? 'active'
                : 'text-slate-300 hover:bg-slate-700/60 hover:text-white';
            return "
            <a href=\"{$href}\" class=\"flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 group {$activeClass}\">
                <span class=\"flex-shrink-0 w-[22px] h-[22px] flex items-center justify-center\">{$icon}</span>
                <span class=\"sidebar-label\">{$label}</span>
            </a>";
        }
    }

    if (!function_exists('sidebarCategory')) {
        function sidebarCategory($label) {
            return "<div class=\"px-3 pt-3 pb-1 mt-2 border-t border-slate-700/50\">
                <p class=\"text-xs font-bold uppercase tracking-wider text-orange-400/80 sidebar-label\">{$label}</p>
            </div>";
        }
    }
@endphp

<style>
    /* Sidebar Styles */
    .modern-sidebar {
        width: 260px;
        min-height: 100vh;
        background: #0f172a;
        border-right: 1px solid rgba(255,255,255,0.06);
        display: flex;
        flex-direction: column;
        position: fixed;
        left: 0;
        top: 0;
        bottom: 0;
        z-index: 1100; /* Bootstrap topbar is ~1000, we go higher */
        transition: width 0.3s ease, transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    .modern-sidebar.mini {
        width: 80px;
    }

    /* Hover effect for mini sidebar */
    .modern-sidebar.mini:hover {
        width: 260px;
        box-shadow: 10px 0 30px rgba(0,0,0,0.3);
    }

    .modern-sidebar.mini .sidebar-label,
    .modern-sidebar.mini .sidebar-category-label,
    .modern-sidebar.mini .chevron {
        display: none !important;
    }

    /* Show elements when hovering the mini sidebar */
    .modern-sidebar.mini:hover .sidebar-label,
    .modern-sidebar.mini:hover .sidebar-category-label,
    .modern-sidebar.mini:hover .chevron {
        display: inline-block !important;
    }

    .modern-sidebar.mini .sidebar-nav a {
        justify-content: center;
        padding-left: 0;
        padding-right: 0;
    }

    .modern-sidebar.mini:hover .sidebar-nav a {
        justify-content: flex-start !important;
        padding-left: 0.75rem !important;
        padding-right: 0.75rem !important;
    }

    /* Submenu adjustments for mini sidebar */
    .modern-sidebar.mini .sidebar-submenu {
        padding-left: 0 !important;
    }

    .modern-sidebar.mini:hover .sidebar-submenu {
        padding-left: 2.25rem !important; /* Restore padding on hover */
    }

    .modern-sidebar.mini .sidebar-submenu a {
        justify-content: center !important;
        font-size: 0 !important; /* Hides the text labels */
    }

    .modern-sidebar.mini:hover .sidebar-submenu a {
        justify-content: flex-start !important;
        font-size: 0.88rem !important; /* Restore font size on hover */
    }

    .modern-sidebar.mini .sidebar-submenu a i {
        font-size: 16px !important; /* Make icon slightly larger for mini view */
        margin: 0 !important;
        opacity: 1 !important;
    }

    .modern-sidebar.mini:hover .sidebar-submenu a i {
        font-size: 13px !important; /* Reset to normal size on hover */
        margin-right: 8px !important;
    }

    .modern-sidebar.mini .sidebar-submenu a::before {
        display: none !important; /* Hide dot if present */
    }

    .modern-sidebar.mini .logo-area-wrapper {
        flex-direction: column;
        justify-content: center;
        gap: 10px;
        padding: 10px 0;
    }

    .modern-sidebar.mini:hover .logo-area-wrapper {
        flex-direction: row;
        justify-content: space-between;
        padding: 10px 1rem;
    }

    .modern-sidebar.mini .logo-area-wrapper a {
        justify-content: center;
    }

    .modern-sidebar.mini .logo-area-wrapper button {
        margin: 0 auto;
    }

    /* Logo / Icon visibility */
    .sidebar-icon {
        display: none !important;
    }
    
    .modern-sidebar.mini .sidebar-logo {
        display: none !important;
    }
    
    .modern-sidebar.mini .sidebar-icon {
        display: block !important;
        width: 32px;
        height: 32px;
        margin: 0 auto;
    }
    
    .modern-sidebar.mini:hover .sidebar-logo {
        display: block !important;
    }
    
    .modern-sidebar.mini:hover .sidebar-icon {
        display: none !important;
    }

    .sidebar-nav {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,0.08) transparent;
        padding: 0 0.75rem;
    }

    .sidebar-nav::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar-nav::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-nav::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.08);
        border-radius: 4px;
    }

    /* Submenu */
    .sidebar-submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.35s ease, opacity 0.3s ease, padding 0.3s ease;
        opacity: 0;
        padding-left: 2.25rem;
        padding-top: 0;
        padding-bottom: 0;
    }

    .has-submenu.open > .sidebar-submenu {
        max-height: 800px; /* Increased to ensure enough space */
        opacity: 1;
        padding-top: 2px;
        padding-bottom: 8px;
        overflow: visible !important;
    }

    .has-submenu > a .chevron {
        transition: transform 0.3s ease;
    }

    .has-submenu.open > a .chevron {
        transform: rotate(90deg);
    }

    .sidebar-submenu a {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.88rem; /* Slightly increased for readability */
        color: #94a3b8;
        transition: all 0.2s ease;
        margin-bottom: 1px;
    }

    .sidebar-submenu a:hover {
        background: rgba(255,255,255,0.04);
        color: #fff;
    }

    .sidebar-nav a.active,
    .sidebar-submenu a.active-sub {
        background: rgba(249, 115, 22, 0.05) !important;
        color: #f97316 !important;
        box-shadow: inset 0 0 0 1px rgba(249, 115, 22, 0.4), 0 4px 15px rgba(249, 115, 22, 0.15) !important;
    }

    .sidebar-nav a.active svg {
        color: #f97316;
    }

    .sidebar-submenu a::before {
        content: '';
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: #334155;
        flex-shrink: 0;
        transition: background 0.2s;
        display: none; /* Hide default dot if we use icons */
    }

    .sidebar-submenu a i {
        font-size: 13px;
        width: 14px;
        text-align: center;
        opacity: 0.7;
        transition: all 0.2s;
    }

    .sidebar-submenu a:hover i,
    .sidebar-submenu a.active-sub i {
        opacity: 1;
        color: #f97316;
        transform: scale(1.1);
    }

    /* Main content offset */
    .rightbar {
        margin-left: 260px;
        transition: margin-left 0.3s ease;
    }

    .rightbar.mini {
        margin-left: 80px;
    }

    /* Sidebar Search */
    .sidebar-search-wrapper {
        transition: all 0.3s ease;
    }
    
    .modern-sidebar.mini .sidebar-search-wrapper {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }
    
    .modern-sidebar.mini .sidebar-search-wrapper .group {
        justify-content: center;
    }

    .modern-sidebar.mini .sidebar-search-wrapper input {
        display: none !important;
        width: 0;
        padding-left: 0;
        padding-right: 0;
        border-color: transparent;
        opacity: 0;
    }
    
    .modern-sidebar.mini:hover .sidebar-search-wrapper input {
        display: block !important;
        width: 100%;
        padding-left: 36px !important;
        padding-right: 0.75rem;
        border-color: #334155;
        opacity: 1;
    }

    .modern-sidebar.mini .sidebar-search-wrapper .search-icon {
        display: none !important;
    }

    .modern-sidebar.mini:hover .sidebar-search-wrapper .search-icon {
        display: flex !important;
    }

    .modern-sidebar.mini .sidebar-search-wrapper .search-icon-only {
        display: flex !important;
    }

    .modern-sidebar.mini:hover .sidebar-search-wrapper .search-icon-only {
        display: none !important;
    }
</style>

<div class="modern-sidebar" id="modernSidebar">

    {{-- ===== LOGO AREA ===== --}}
    <div class="logo-area-wrapper flex items-center justify-between px-4  border-b border-slate-700/50 py-1">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 overflow-hidden w-full justify-center">
            <!-- Full Logo -->
            <img src="{{ !empty(get_setting('system_logo')) ? url('uploads/logo/' . get_setting('system_logo')) : url('backend/images/logo.png') }}"
                class="w-full h-16 rounded-lg object-contain flex-shrink-0 sidebar-logo" alt="logo">
            <!-- Mini System Icon -->
            <img src="{{ !empty(get_setting('system_icon')) ? url('uploads/logo/' . get_setting('system_icon')) : url('backend/images/favicon.png') }}"
                class="w-8 h-8 rounded-lg object-contain flex-shrink-0 sidebar-icon" alt="icon">
        </a>
    </div>

    {{-- ===== SEARCH MODULE ===== --}}
    <div class="sidebar-search-wrapper px-3 py-3 border-b border-slate-700/50">
        <div class="relative group flex items-center">
            <span class="absolute inset-y-0 left-0 flex items-center pointer-events-none text-slate-400 group-focus-within:text-orange-400 transition-colors search-icon" style="padding-left: 12px;">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" id="sidebarSearch" placeholder="{{ __('Search Module...') }}" 
                   style="padding-left: 36px !important;"
                   class="block w-full pr-3 py-2 bg-slate-800/50 border border-slate-700 text-slate-200 text-sm rounded-lg focus:outline-none focus:ring-1 focus:ring-orange-500/50 focus:border-orange-500/50 transition-all placeholder-slate-500">
            
            <div class="search-icon-only hidden text-slate-400 hover:text-white transition-colors cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>
    </div>

    {{-- ===== NAVIGATION ===== --}}
    <nav class="sidebar-nav py-3">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ $currentRoute === 'dashboard' ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
            <span class="flex-shrink-0 w-[22px] h-[22px]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
            </span>
            <span class="sidebar-label">{{ __('Dashboard') }}</span>
        </a>

        {{-- ===== SALE & PURCHASE ===== --}}
        <div class="sidebar-category-label px-3 pt-3 pb-1 mt-2 border-t border-slate-700/50">
            <p class="sidebar-label text-xs font-bold uppercase tracking-wider text-orange-400/80">{{ __('Sale & Purchase') }}</p>
        </div>

        {{-- POS --}}
        @if(!in_array('pos', $hiddenModules))
        <a href="{{ route('invoice.create') }}"
            onclick="if(window.requestFullscreen) { document.documentElement.requestFullscreen(); }"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ $currentRoute === 'invoice.create' ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
            <span class="flex-shrink-0 w-[22px] h-[22px]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </span>
            <span class="sidebar-label">{{ __('New Sale') }}</span>
        </a>
        @endif

        {{-- Sale List --}}
        @if(!in_array('sale_list', $hiddenModules))
        @if (main_menu_permission('invoice'))
            @if (check_permission('invoice.index'))
                <a href="{{ route('invoice.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ $currentRoute === 'invoice.index' ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                    <span class="flex-shrink-0 w-[22px] h-[22px]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </span>
                    <span class="sidebar-label">{{ __('Sale List') }}</span>
                </a>
        @endif
        @endif
        @endif

        {{-- Quotation --}}
        @if(env('APP_AUTOMOBILE') == 'yes')
        @if(!in_array('quotation', $hiddenModules))
        <div class="has-submenu {{ str_starts_with($currentUrl, 'quotation') ? 'open' : '' }} mb-0.5">
            <a href="javascript:void(0);" onclick="toggleSubmenu(this)"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 cursor-pointer {{ str_starts_with($currentUrl, 'quotation') ? 'bg-slate-700/80 text-white' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                <span class="flex-shrink-0 w-[22px] h-[22px] text-orange-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </span>
                <span class="sidebar-label flex-1">{{ __('Quotation') }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="chevron sidebar-label w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            <div class="sidebar-submenu">
                <a href="{{ route('quotation.create') }}" class="{{ $currentRoute === 'quotation.create' ? 'active-sub' : '' }}">
                    <i class="feather icon-plus-square"></i> {{ __('Add Quotation') }}
                </a>
                <a href="{{ route('quotation.index') }}" class="{{ $currentRoute === 'quotation.index' ? 'active-sub' : '' }}">
                    <i class="feather icon-list"></i> {{ __('Quotation List') }}
                </a>
            </div>
        </div>
        @endif
        @endif

        {{-- Installment --}}
        @if(env('APP_INSTALLMENT') == 'yes')
        @if(!in_array('installment', $hiddenModules))
        <div class="has-submenu {{ str_starts_with($currentUrl, 'installments') ? 'open' : '' }} mb-0.5">
            <a href="javascript:void(0);" onclick="toggleSubmenu(this)"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 cursor-pointer {{ str_starts_with($currentUrl, 'installments') ? 'bg-slate-700/80 text-white' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                <span class="flex-shrink-0 w-[22px] h-[22px] text-orange-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                </span>
                <span class="sidebar-label flex-1">{{ __('Installment') }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="chevron sidebar-label w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            <div class="sidebar-submenu">
                <a href="{{ route('installments.index') }}" class="{{ $currentRoute === 'installments.index' ? 'active-sub' : '' }}">
                    <i class="feather icon-list"></i> {{ __('Installment List') }}
                </a>
                <a href="{{ route('installments.today-due') }}" class="{{ $currentRoute === 'installments.today-due' ? 'active-sub' : '' }} flex items-center justify-between">
                    <span><i class="feather icon-calendar"></i> {{ __('Today\'s Due') }}</span>
                    <span class="px-2 py-0.5 text-xs font-bold text-white bg-amber-500 rounded-full">{{ $todayDueCount }}</span>
                </a>
                <a href="{{ route('installments.today-collection') }}" class="{{ $currentRoute === 'installments.today-collection' ? 'active-sub' : '' }} flex items-center justify-between">
                    <span><i class="feather icon-check-square"></i> {{ __('Today\'s Collection') }}</span>
                    <span class="px-2 py-0.5 text-xs font-bold text-white bg-emerald-500 rounded-full">{{ $todayCollectionCount }}</span>
                </a>
                <a href="{{ route('installments.overdue') }}" class="{{ $currentRoute === 'installments.overdue' ? 'active-sub' : '' }} flex items-center justify-between">
                    <span><i class="feather icon-alert-circle"></i> {{ __('Overdue List') }}</span>
                    <span class="px-2 py-0.5 text-xs font-bold text-white bg-emerald-500 rounded-full">{{ $overdueCount }}</span>
                </a>
                <a href="{{ route('installments.overdue') }}" class="flex flex-col gap-1 items-start py-2">
                    <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider"><i class="feather icon-x-circle"></i> {{ __('Total Overdue') }}</span>
                    <div class="flex gap-1.5 mt-0.5">
                        <span class="px-2 py-0.5 text-xs font-bold text-white bg-red-500 rounded-md">{{ $totalOverdueCount }}</span>
                        <span class="px-2 py-0.5 text-xs font-bold text-white bg-rose-600 rounded-md">TK {{ number_format($totalOverdueSum, 2) }}</span>
                    </div>
                </a>
                <a href="{{ route('installments.completed') }}" class="{{ $currentRoute === 'installments.completed' ? 'active-sub' : '' }} flex items-center justify-between">
                    <span><i class="feather icon-check-circle"></i> {{ __('Completed') }}</span>
                    <span class="px-2 py-0.5 text-xs font-bold text-white bg-sky-500 rounded-full">{{ $completedCount }}</span>
                </a>
            </div>
        </div>
        @endif
        @endif

        {{-- Web Orders & Online Sales --}}
        @if(!in_array('online_sale', $hiddenModules))
            @php
                $pendingWebOrderCount = \App\Models\Invoice::where('sale_type', 'Online')
                    ->where('status', '!=', 2)
                    ->where(function ($q) {
                        $q->whereNull('consignment_id')
                          ->orWhere('consignment_id', '')
                          ->orWhere('order_status', 'Pending');
                    })->count();

                $onlineSaleCount = \App\Models\Invoice::where('sale_type', 'Online')
                    ->where('status', '!=', 2)
                    ->whereNotNull('consignment_id')
                    ->where('consignment_id', '!=', '')
                    ->count();

                $preOrderCount = \App\Models\PreOrder::where('status', 'pending')->count();
            @endphp
            {{-- Web Order List (Pending from Website) --}}
            <a href="{{ route('web-orders.index') }}"
                class="flex items-center justify-between px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ $currentRoute === 'web-orders.index' ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <span class="flex-shrink-0 w-[22px] h-[22px] text-orange-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064" />
                        </svg>
                    </span>
                    <span class="sidebar-label">{{ __('Web Order List') }}</span>
                </div>
                @if($pendingWebOrderCount > 0)
                    <span class="sidebar-label px-2 py-0.5 text-xs font-bold text-white bg-orange-500 rounded-full shadow-sm animate-pulse">{{ $pendingWebOrderCount }}</span>
                @endif
            </a>

            {{-- Online Sale List (Dispatched & Confirmed) --}}
            <a href="{{ route('invoice.online.sale') }}"
                class="flex items-center justify-between px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ $currentRoute === 'invoice.online.sale' ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <span class="flex-shrink-0 w-[22px] h-[22px] text-orange-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.125-.504 1.125-1.125V15m-19.5 0v-4.5c0-.621.504-1.125 1.125-1.125h13.5c.621 0 1.125.504 1.125 1.125v4.5m-15.75 0h15.75" />
                        </svg>
                    </span>
                    <span class="sidebar-label">{{ __('Online Sale List') }}</span>
                </div>
                @if($onlineSaleCount > 0)
                    <span class="sidebar-label px-2 py-0.5 text-xs font-bold text-white bg-slate-600 rounded-full shadow-sm">{{ $onlineSaleCount }}</span>
                @endif
            </a>

            {{-- Pre-Order List --}}
            <a href="{{ route('pre-orders.index') }}"
                class="flex items-center justify-between px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ $currentRoute === 'pre-orders.index' ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <span class="flex-shrink-0 w-[22px] h-[22px] text-orange-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <span class="sidebar-label">{{ __('Pre-Order List') }}</span>
                </div>
                @if($preOrderCount > 0)
                    <span class="sidebar-label px-2 py-0.5 text-xs font-bold text-white bg-amber-500 rounded-full shadow-sm">{{ $preOrderCount }}</span>
                @endif
            </a>
        @endif

        {{-- Purchase --}}
        @if(!in_array('purchase', $hiddenModules))
        @if (main_menu_permission('purchase'))
            <div class="has-submenu {{ str_starts_with($currentUrl, 'purchase') ? 'open' : '' }} mb-0.5">
                <a href="javascript:void(0);" onclick="toggleSubmenu(this)"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 cursor-pointer {{ str_starts_with($currentUrl, 'purchase') ? 'bg-slate-700/80 text-white' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                    <span class="flex-shrink-0 w-[22px] h-[22px]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </span>
                    <span class="sidebar-label flex-1">{{ __('Purchase') }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="chevron sidebar-label w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <div class="sidebar-submenu">
                    @if (check_permission('purchase.create'))
                        <a href="{{ route('purchase.create') }}" class="{{ $currentRoute === 'purchase.create' ? 'active-sub' : '' }}">
                            <i class="feather icon-plus-square"></i> {{ __('Add Purchase') }}
                        </a>
                    @endif
                    @if (check_permission('purchase.index'))
                        <a href="{{ route('purchase.index') }}" class="{{ $currentRoute === 'purchase.index' ? 'active-sub' : '' }}">
                            <i class="feather icon-list"></i> {{ __('Purchase List') }}
                        </a>
                    @endif
                </div>
            </div>
        @endif
        @endif

        {{-- Return --}}
        @if(!in_array('return', $hiddenModules))
        <div class="has-submenu {{ str_starts_with($currentUrl, 'return') || str_starts_with($currentUrl, 'rtnPurchase') ? 'open' : '' }} mb-0.5">
            <a href="javascript:void(0);" onclick="toggleSubmenu(this)"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 cursor-pointer {{ str_starts_with($currentUrl, 'return') || str_starts_with($currentUrl, 'rtnPurchase') ? 'bg-slate-700/80 text-white' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                <span class="flex-shrink-0 w-[22px] h-[22px]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                    </svg>
                </span>
                <span class="sidebar-label flex-1">{{ __('Return') }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="chevron sidebar-label w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            <div class="sidebar-submenu">
                @if (check_permission('return.sale'))
                    <a href="{{ route('return.sale') }}" class="{{ $currentRoute === 'return.sale' ? 'active-sub' : '' }}">
                        <i class="feather icon-corner-up-left"></i> {{ __('Return Sale') }}
                    </a>
                @endif
                @if (check_permission('rtnPurchase.index'))
                    <a href="{{ route('rtnPurchase.index') }}" class="{{ $currentRoute === 'rtnPurchase.index' ? 'active-sub' : '' }}">
                        <i class="feather icon-corner-up-right"></i> {{ __('Return Purchase') }}
                    </a>
                @endif
            </div>
        </div>
        @endif

        {{-- Damage --}}
        @if(!in_array('damage', $hiddenModules))
        <a href="{{ route('damage.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ $currentRoute === 'damage.index' ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
            <span class="flex-shrink-0 w-[22px] h-[22px]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </span>
            <span class="sidebar-label">{{ __('Damage') }}</span>
        </a>
        @endif

        {{-- Stock Report --}}
        @if(!in_array('stock_report', $hiddenModules))
        @if (check_permission('report.stock'))
            <a href="{{ route('report.stock') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ $currentRoute === 'report.stock' ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                <span class="flex-shrink-0 w-[22px] h-[22px]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </span>
                <span class="sidebar-label">{{ __('Stock Report') }}</span>
            </a>
        @endif
        @endif

        {{-- AI Stock Auditor --}}
        @if(!in_array('ai_stock_auditor', $hiddenModules))
        @if(config('services.app_auditor') == 'yes')
        <a href="{{ route('ai-auditor.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ $currentRoute === 'ai-auditor.index' ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
            <span class="flex-shrink-0 w-[22px] h-[22px] text-orange-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                </svg>
            </span>
            <span class="sidebar-label">AI Stock Auditor ✨</span>
        </a>
        @endif
        @endif

        {{-- Stock Adjust --}}
        @if(!in_array('stock_adjust', $hiddenModules))
        @if (auth()->user()->id == 1 || auth()->user()->id == 2)
            <div class="has-submenu {{ str_starts_with($currentUrl, 'stock-adjust') ? 'open' : '' }} mb-0.5">
                <a href="javascript:void(0);" onclick="toggleSubmenu(this)"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 cursor-pointer {{ str_starts_with($currentUrl, 'stock-adjust') ? 'bg-slate-700/80 text-white' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                    <span class="flex-shrink-0 w-[22px] h-[22px]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                    </span>
                    <span class="sidebar-label flex-1">{{ __('Stock Adjust') }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="chevron sidebar-label w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <div class="sidebar-submenu">
                    @if (check_permission('stock-adjust.create'))
                        <a href="{{ route('stock-adjust.create') }}" class="{{ $currentRoute === 'stock-adjust.create' ? 'active-sub' : '' }}">
                            <i class="feather icon-sliders"></i> {{ __('Adjust') }}
                        </a>
                    @endif
                    @if (check_permission('stock-adjust.index'))
                        <a href="{{ route('stock-adjust.index') }}" class="{{ $currentRoute === 'stock-adjust.index' ? 'active-sub' : '' }}">
                            <i class="feather icon-file-text"></i> {{ __('Adjust List') }}
                        </a>
                    @endif
                </div>
            </div>
        @endif
        @endif

        {{-- Stock Audit --}}
        @if(!in_array('stock_audit', $hiddenModules))
            <div class="has-submenu {{ str_starts_with($currentUrl, 'stock-audit') ? 'open' : '' }} mb-0.5">
                <a href="javascript:void(0);" onclick="toggleSubmenu(this)"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 cursor-pointer {{ str_starts_with($currentUrl, 'stock-audit') ? 'bg-slate-700/80 text-white' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                    <span class="flex-shrink-0 w-[22px] h-[22px] text-emerald-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <span class="sidebar-label flex-1">{{ __('Stock Audit') }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="chevron sidebar-label w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <div class="sidebar-submenu">
                    <a href="{{ route('stock-audit.create') }}" class="{{ $currentRoute === 'stock-audit.create' ? 'active-sub' : '' }}">
                        <i class="feather icon-check-square"></i> {{ __('Start Audit') }}
                    </a>
                    <a href="{{ route('stock-audit.index') }}" class="{{ $currentRoute === 'stock-audit.index' ? 'active-sub' : '' }}">
                        <i class="feather icon-list"></i> {{ __('Audit History') }}
                    </a>
                </div>
            </div>
        @endif

        {{-- Transfer --}}
        @if(!in_array('transfer', $hiddenModules))
        <div class="has-submenu {{ str_starts_with($currentUrl, 'transfer') ? 'open' : '' }} mb-0.5">
            <a href="javascript:void(0);" onclick="toggleSubmenu(this)"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 cursor-pointer {{ str_starts_with($currentUrl, 'transfer') ? 'bg-slate-700/80 text-white' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                <span class="flex-shrink-0 w-[22px] h-[22px]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </span>
                <span class="sidebar-label flex-1">{{ __('Transfer') }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="chevron sidebar-label w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            <div class="sidebar-submenu">
                @if (check_permission('transfer.create'))
                    <a href="{{ route('transfer.create') }}" class="{{ $currentRoute === 'transfer.create' ? 'active-sub' : '' }}">
                        <i class="feather icon-repeat"></i> {{ __('Transfer') }}
                    </a>
                @endif
                @if (check_permission('transfer.index'))
                    <a href="{{ route('transfer.index') }}" class="{{ $currentRoute === 'transfer.index' ? 'active-sub' : '' }}">
                        <i class="feather icon-list"></i> {{ __('Transfer List') }}
                    </a>
                @endif
            </div>
        </div>
        @endif

        {{-- ===== PRODUCT INFORMATION ===== --}}
        <div class="sidebar-category-label px-3 pt-3 pb-1 mt-2 border-t border-slate-700/50">
            <p class="sidebar-label text-xs font-bold uppercase tracking-wider text-orange-400/80">{{ __('Product Information') }}</p>
        </div>

        {{-- Unit --}}
        @if(is_unit_enabled() && !in_array('unit', $hiddenModules))
        @if (check_permission('unit.index'))
            <a href="{{ route('unit.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ $currentRoute === 'unit.index' ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                <span class="flex-shrink-0 w-[22px] h-[22px]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </span>
                <span class="sidebar-label">{{ __('Unit') }}</span>
            </a>
        @endif
        @endif

        {{-- Product --}}
        @if(!in_array('product', $hiddenModules))
        <div class="has-submenu {{ str_starts_with($currentUrl, 'product') || str_starts_with($currentUrl, 'multiple/barcode') ? 'open' : '' }} mb-0.5">
            <a href="javascript:void(0);" onclick="toggleSubmenu(this)"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 cursor-pointer {{ str_starts_with($currentUrl, 'product') || str_starts_with($currentUrl, 'multiple/barcode') ? 'bg-slate-700/80 text-white' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                <span class="flex-shrink-0 w-[22px] h-[22px]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </span>
                <span class="sidebar-label flex-1">{{ __('Product') }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="chevron sidebar-label w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            <div class="sidebar-submenu">
                @if (check_permission('product.create'))
                    <a href="{{ route('product.create') }}" class="{{ $currentRoute === 'product.create' ? 'active-sub' : '' }}">
                        <i class="feather icon-box"></i> {{ __('Add Product') }}
                    </a>
                @endif
                @if (check_permission('product.index'))
                    <a href="{{ route('product.index') }}" class="{{ $currentRoute === 'product.index' ? 'active-sub' : '' }}">
                        <i class="feather icon-layers"></i> {{ __('Product List') }}
                    </a>
                @endif
                @if (check_permission('multiple.barcode'))
                    <a href="{{ route('multiple.barcode') }}" class="{{ $currentRoute === 'multiple.barcode' ? 'active-sub' : '' }}">
                        <i class="fa fa-barcode"></i> {{ __('Barcode') }}
                    </a>
                @endif
            </div>
        </div>
        @endif

        {{-- Service --}}
        @if(!in_array('service', $hiddenModules))
        @if (env('APP_SERVICE') == 'yes')
            <div class="has-submenu {{ str_starts_with($currentUrl, 'service') || str_starts_with($currentUrl, 'service-receive') || str_starts_with($currentUrl, 'service-invoice') ? 'open' : '' }} mb-0.5">
                <a href="javascript:void(0);" onclick="toggleSubmenu(this)"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 cursor-pointer {{ str_starts_with($currentUrl, 'service') || str_starts_with($currentUrl, 'service-receive') || str_starts_with($currentUrl, 'service-invoice') ? 'bg-slate-700/80 text-white' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                    <span class="flex-shrink-0 w-[22px] h-[22px]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </span>
                    <span class="sidebar-label flex-1">{{ __('Service') }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="chevron sidebar-label w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <div class="sidebar-submenu">
                    <a href="{{ route('service.index') }}" class="{{ $currentRoute === 'service.index' ? 'active-sub' : '' }}">
                        <i class="feather icon-tool"></i> {{ __('Service List') }}
                    </a>
                    <!--<a href="{{ route('service-receive.index') }}" class="{{ $currentRoute === 'service-receive.index' ? 'active-sub' : '' }}">-->
                    <!--    <i class="feather icon-list"></i> {{ __('Service Received List') }}-->
                    <!--</a>-->
                    <!--<a href="{{ route('service-receive.create') }}" class="{{ $currentRoute === 'service-receive.create' ? 'active-sub' : '' }}">-->
                    <!--    <i class="feather icon-plus-circle"></i> {{ __('Service Received Create') }}-->
                    <!--</a>-->
                    <a href="{{ route('service-invoice.index') }}" class="{{ $currentRoute === 'service-invoice.index' ? 'active-sub' : '' }}">
                        <i class="feather icon-file-text"></i> {{ __('Service Invoice') }}
                    </a>
                    <a href="{{ route('service-invoice.create') }}" class="{{ $currentRoute === 'service-invoice.create' ? 'active-sub' : '' }}">
                        <i class="feather icon-plus-square"></i> {{ __('Service Sales Create') }}
                    </a>
                    @if (check_permission('report.service'))
                    <a href="{{ route('report.service') }}" class="{{ $currentRoute === 'report.service' ? 'active-sub' : '' }}">
                        <i class="feather icon-bar-chart-2"></i> {{ __('Service Report') }}
                    </a>
                    @endif
                </div>
            </div>
        @endif
        @endif

        {{-- Warranty Management --}}
        @if(!in_array('warranty_management', $hiddenModules))
        <div class="has-submenu {{ str_starts_with($currentUrl, 'warranty-claim') || str_starts_with($currentUrl, 'service-center') ? 'open' : '' }} mb-0.5">
            <a href="javascript:void(0);" onclick="toggleSubmenu(this)"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 cursor-pointer {{ str_starts_with($currentUrl, 'warranty-claim') || str_starts_with($currentUrl, 'service-center') ? 'bg-slate-700/80 text-white' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                <span class="flex-shrink-0 w-[22px] h-[22px]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                    </svg>
                </span>
                <span class="sidebar-label flex-1">{{ __('Warranty Management') }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="chevron sidebar-label w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            <div class="sidebar-submenu">
                <a href="{{ route('warranty-claim.create') }}" class="{{ $currentRoute === 'warranty-claim.create' ? 'active-sub' : '' }}">
                    <i class="feather icon-plus-circle"></i> {{ __('Warranty Claim') }}
                </a>
                <a href="{{ route('warranty-claim.index', ['status' => 'Pending']) }}" class="{{ $currentRoute === 'warranty-claim.index' && request('status') == 'Pending' ? 'active-sub' : '' }}">
                    <i class="feather icon-check-circle"></i> {{ __('Product Checking') }}
                </a>
                <a href="{{ route('warranty-claim.index') }}" class="{{ $currentRoute === 'warranty-claim.index' && !request('status') ? 'active-sub' : '' }}">
                    <i class="feather icon-settings"></i> {{ __('Manage Product') }}
                </a>
                <a href="{{ route('warranty-delivery.index') }}" class="{{ $currentRoute === 'warranty-delivery.index' ? 'active-sub' : '' }}">
                    <i class="feather icon-truck"></i> {{ __('Warranty Delivered') }}
                </a>
                <a href="{{ route('warranty-claim.index', ['status' => 'Returned from Supplier']) }}" class="{{ $currentRoute === 'warranty-claim.index' && request('status') == 'Returned from Supplier' ? 'active-sub' : '' }}">
                    <i class="feather icon-package"></i> {{ __('Warranty Stock') }}
                </a>
                <a href="{{ route('supplier-claim.index') }}" class="{{ $currentRoute === 'supplier-claim.index' ? 'active-sub' : '' }}">
                    <i class="feather icon-external-link"></i> {{ __('Claim To Supplier') }}
                </a>
                <a href="{{ route('warranty.index') }}" class="{{ $currentRoute === 'warranty.index' ? 'active-sub' : '' }}">
                    <i class="feather icon-shield"></i> {{ __('Warranty List / Types') }}
                </a>
                <a href="{{ route('service-center.index') }}" class="{{ $currentRoute === 'service-center.index' ? 'active-sub' : '' }}">
                    <i class="feather icon-home"></i> {{ __('Service Center') }}
                </a>
            </div>
        </div>
        @endif

        {{-- Category --}}
        @if(!in_array('category', $hiddenModules))
        @if (check_permission('category.index'))
            <a href="{{ route('category.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ $currentRoute === 'category.index' ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                <span class="flex-shrink-0 w-[22px] h-[22px]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </span>
                <span class="sidebar-label">{{ __('Category') }}</span>
            </a>
        @endif
        @if (env('APP_SUB_CATEGORY') == 'yes')
        @if (check_permission('sub-category.index'))
            <a href="{{ route('sub-category.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ $currentRoute === 'sub-category.index' ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                <span class="flex-shrink-0 w-[22px] h-[22px]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6z" />
                    </svg>
                </span>
                <span class="sidebar-label">{{ __('Sub Category') }}</span>
            </a>
            <a href="{{ route('child-category.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ $currentRoute === 'child-category.index' ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                <span class="flex-shrink-0 w-[22px] h-[22px]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                </span>
                <span class="sidebar-label">{{ __('Child Category') }}</span>
            </a>
        @endif
        @endif
        @endif

        {{-- Brand --}}
        @if(!in_array('brand', $hiddenModules))
        @if (check_permission('brand.index'))
            <a href="{{ route('brand.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ $currentRoute === 'brand.index' ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                <span class="flex-shrink-0 w-[22px] h-[22px]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </span>
                <span class="sidebar-label">{{ __('Brand') }}</span>
            </a>
        @endif
        @endif

        {{-- Rack --}}
        @if (is_rack_enabled())
        @if(!in_array('rack', $hiddenModules))
        @if (check_permission('rack.index'))
            <a href="{{ route('rack.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ $currentRoute === 'rack.index' ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                <span class="flex-shrink-0 w-[22px] h-[22px]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5M5.25 3.75v16.5m13.5-16.5v16.5" />
                    </svg>
                </span>
                <span class="sidebar-label">{{ __('Rack') }}</span>
            </a>
        @endif
        @endif
        @endif

        {{-- Platform --}}
        @if (env('APP_ONLINE') == 'yes')
            <a href="{{ route('platform.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ $currentRoute === 'platform.index' ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                <span class="flex-shrink-0 w-[22px] h-[22px]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-.778.099-1.533.284-2.253" />
                    </svg>
                </span>
                <span class="sidebar-label">{{ __('Platform') }}</span>
            </a>
        @endif

        {{-- Banners --}}
        @if(!in_array('banner', $hiddenModules))
        @if (check_permission('banner.index'))
            <a href="{{ route('banner.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ $currentRoute === 'banner.index' ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                <span class="flex-shrink-0 w-[22px] h-[22px]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                </span>
                <span class="sidebar-label">{{ __('Banners') }}</span>
            </a>
        @endif
        @endif

        {{-- ===== MARKETING ANALYTICS ===== --}}
        @if (env('APP_ONLINE') == 'yes')
            <div class="sidebar-category-label px-3 pt-3 pb-1 mt-2 border-t border-slate-700/50">
                <p class="sidebar-label text-xs font-bold uppercase tracking-wider text-orange-400/80">{{ __('Marketing Analytics') }}</p>
            </div>

            <div class="has-submenu {{ str_starts_with($currentUrl, 'ad-cost') || str_starts_with($currentUrl, 'roi-tracking') || str_starts_with($currentUrl, 'courier-tracking') ? 'open' : '' }} mb-0.5">
                <a href="javascript:void(0);" onclick="toggleSubmenu(this)"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 cursor-pointer {{ str_starts_with($currentUrl, 'ad-cost') || str_starts_with($currentUrl, 'roi-tracking') || str_starts_with($currentUrl, 'courier-tracking') ? 'bg-slate-700/80 text-white' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                    <span class="flex-shrink-0 w-[22px] h-[22px]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                        </svg>
                    </span>
                    <span class="sidebar-label flex-1">{{ __('Marketing') }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="chevron sidebar-label w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <div class="sidebar-submenu">
                    <a href="{{ route('ad-cost.index') }}" class="{{ $currentRoute === 'ad-cost.index' ? 'active-sub' : '' }}">
                        <i class="feather icon-list"></i> {{ __('Ad Cost List') }}
                    </a>
                    <a href="{{ route('ad-cost.create') }}" class="{{ $currentRoute === 'ad-cost.create' ? 'active-sub' : '' }}">
                        <i class="feather icon-plus-circle"></i> {{ __('Add Ad Cost') }}
                    </a>
                    <a href="{{ route('roi.tracking') }}" class="{{ $currentRoute === 'roi.tracking' ? 'active-sub' : '' }}">
                        <i class="feather icon-trending-up"></i> {{ __('ROI Tracking') }}
                    </a>
                    <a href="{{ route('courier.tracking') }}" class="{{ $currentRoute === 'courier.tracking' ? 'active-sub' : '' }}">
                        <i class="feather icon-search"></i> {{ __('Search Invoice by Courier ID') }}
                    </a>
                </div>
            </div>
        @endif

        {{-- Color & Size --}}
        @if (env('APP_SC') == 'yes')
            @if(!in_array('color', $hiddenModules))
            @if (check_permission('color.index'))
                <a href="{{ route('color.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ $currentRoute === 'color.index' ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                    <span class="flex-shrink-0 w-[22px] h-[22px]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                        </svg>
                    </span>
                    <span class="sidebar-label">{{ __('Color') }}</span>
                </a>
            @endif
            @endif
            @if(!in_array('size', $hiddenModules))
            @if (check_permission('size.index'))
                <a href="{{ route('size.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ $currentRoute === 'size.index' ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                    <span class="flex-shrink-0 w-[22px] h-[22px]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                        </svg>
                    </span>
                    <span class="sidebar-label">{{ __('Size') }}</span>
                </a>
            @endif
            @endif
        @endif

        {{-- ===== EXPENSE & PAYMENT ===== --}}
        <div class="sidebar-category-label px-3 pt-3 pb-1 mt-2 border-t border-slate-700/50">
            <p class="sidebar-label text-xs font-bold uppercase tracking-wider text-orange-400/80">{{ __('Expense & Payment') }}</p>
        </div>

        {{-- Expense --}}
        @if(!in_array('expense', $hiddenModules))
        <div class="has-submenu {{ str_starts_with($currentUrl, 'expense') || str_starts_with($currentUrl, 'asset') ? 'open' : '' }} mb-0.5">
            <a href="javascript:void(0);" onclick="toggleSubmenu(this)"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 cursor-pointer {{ str_starts_with($currentUrl, 'expense') || str_starts_with($currentUrl, 'asset') ? 'bg-slate-700/80 text-white' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                <span class="flex-shrink-0 w-[22px] h-[22px]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </span>
                <span class="sidebar-label flex-1">{{ __('Expense') }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="chevron sidebar-label w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            <div class="sidebar-submenu">
                @if (check_permission('expense.create'))
                    <a href="{{ route('expense.create') }}" class="{{ $currentRoute === 'expense.create' ? 'active-sub' : '' }}">
                        <i class="feather icon-plus-circle"></i> {{ __('Add Expense') }}
                    </a>
                @endif
                @if (check_permission('expense.index'))
                    <a href="{{ route('expense.index') }}" class="{{ $currentRoute === 'expense.index' ? 'active-sub' : '' }}">
                        <i class="feather icon-file-minus"></i> {{ __('Expense List') }}
                    </a>
                @endif
                <a href="{{ route('expense.category-index') }}" class="{{ $currentRoute === 'expense.category-index' ? 'active-sub' : '' }}">
                    <i class="feather icon-tag"></i> {{ __('Expense Category') }}
                </a>
                <a href="{{ route('asset.create') }}" class="{{ $currentRoute === 'asset.create' ? 'active-sub' : '' }}">
                    <i class="feather icon-plus-square"></i> {{ __('Add Asset') }}
                </a>
                <a href="{{ route('asset.index') }}" class="{{ $currentRoute === 'asset.index' ? 'active-sub' : '' }}">
                    <i class="feather icon-package"></i> {{ __('Asset List') }}
                </a>
            </div>
        </div>
        @endif

        {{-- Payment --}}
        @if(!in_array('payment', $hiddenModules))
        <div class="has-submenu {{ str_starts_with($currentUrl, 'payment') ? 'open' : '' }} mb-0.5">
            <a href="javascript:void(0);" onclick="toggleSubmenu(this)"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 cursor-pointer {{ str_starts_with($currentUrl, 'payment') ? 'bg-slate-700/80 text-white' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                <span class="flex-shrink-0 w-[22px] h-[22px]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                </span>
                <span class="sidebar-label flex-1">{{ __('Payment') }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="chevron sidebar-label w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            <div class="sidebar-submenu">
                @if (check_permission('payment.pay-supplier'))
                    <a href="{{ route('payment.pay-supplier') }}" class="{{ $currentRoute === 'payment.pay-supplier' ? 'active-sub' : '' }}">
                        <i class="feather icon-arrow-up-right"></i> {{ __('Pay Supplier') }}
                    </a>
                @endif
                @if (check_permission('payment.pay-customer'))
                    <a href="{{ route('payment.pay-customer') }}" class="{{ $currentRoute === 'payment.pay-customer' ? 'active-sub' : '' }}">
                        <i class="feather icon-arrow-down-left"></i> {{ __('Pay Customer') }}
                    </a>
                @endif
            </div>
        </div>
        @endif

        {{-- ===== CUSTOMERS & SUPPLIERS ===== --}}
        <div class="sidebar-category-label px-3 pt-3 pb-1 mt-2 border-t border-slate-700/50">
            <p class="sidebar-label text-xs font-bold uppercase tracking-wider text-orange-400/80">{{ __('CRM') }}</p>
        </div>

        <a href="{{ route('supplier.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ str_starts_with($currentUrl, 'supplier') ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
            <span class="flex-shrink-0 w-[22px] h-[22px]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </span>
            <span class="sidebar-label">{{ __('Supplier') }}</span>
        </a>

        <a href="{{ route('customer.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ str_starts_with($currentUrl, 'customer') && !str_contains($currentUrl, 'upcoming-wishlist') ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
            <span class="flex-shrink-0 w-[22px] h-[22px]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </span>
            <span class="sidebar-label">{{ __('Customer') }}</span>
        </a>

        @if (env('APP_DISCOUNT_GROUP') == 'yes')
        <a href="{{ route('discount-group.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ str_starts_with($currentUrl, 'discount-group') ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
            <span class="flex-shrink-0 w-[22px] h-[22px]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                </svg>
            </span>
            <span class="sidebar-label">{{ __('Discount Group') }}</span>
        </a>
        @endif

        <a href="{{ route('customer.upcoming-wishlist') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ str_contains($currentUrl, 'upcoming-wishlist') ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
            <span class="flex-shrink-0 w-[22px] h-[22px]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                </svg>
            </span>
            <span class="sidebar-label">{{ __('Upcoming Wishlist') }}</span>
        </a>

        <a href="{{ route('employee.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ str_starts_with($currentUrl, 'employee') ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
            <span class="flex-shrink-0 w-[22px] h-[22px]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>
            <span class="sidebar-label">{{ __('Employee') }}</span>
        </a>

        {{-- ===== PAYROLL ===== --}}
        @if(!in_array('payroll', $hiddenModules))
        <div class="sidebar-category-label px-3 pt-3 pb-1 mt-2 border-t border-slate-700/50">
            <p class="sidebar-label text-xs font-bold uppercase tracking-wider text-orange-400/80">{{ __('Payroll Management') }}</p>
        </div>

        <div class="has-submenu {{ str_starts_with($currentUrl, 'payroll') ? 'open' : '' }} mb-0.5">
            <a href="javascript:void(0);" onclick="toggleSubmenu(this)"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 cursor-pointer {{ str_starts_with($currentUrl, 'payroll') ? 'bg-slate-700/80 text-white' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                <span class="flex-shrink-0 w-[22px] h-[22px]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 8.25H9m6 3H9m3 6l-3-3h1.5a3 3 0 100-6M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                <span class="sidebar-label flex-1">{{ __('Payroll') }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="chevron sidebar-label w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            <div class="sidebar-submenu">
                <a href="{{ route('payroll.department.index') }}" class="{{ $currentRoute === 'payroll.department.index' ? 'active-sub' : '' }}">
                    <i class="feather icon-chevron-right"></i> {{ __('Department List') }}
                </a>
                <a href="{{ route('payroll.department.index', ['add' => 1]) }}" class="">
                    <i class="feather icon-chevron-right"></i> {{ __('Add Department') }}
                </a>
                <a href="{{ route('payroll.designation.index') }}" class="{{ $currentRoute === 'payroll.designation.index' ? 'active-sub' : '' }}">
                    <i class="feather icon-chevron-right"></i> {{ __('Designation List') }}
                </a>
                <a href="{{ route('payroll.designation.index', ['add' => 1]) }}" class="">
                    <i class="feather icon-chevron-right"></i> {{ __('Add Designation') }}
                </a>
                <a href="{{ route('employee.index') }}" class="{{ $currentRoute === 'employee.index' ? 'active-sub' : '' }}">
                    <i class="feather icon-chevron-right"></i> {{ __('Employee List') }}
                </a>
                <a href="{{ route('employee.index', ['add' => 1]) }}" class="">
                    <i class="feather icon-chevron-right"></i> {{ __('Add Employee') }}
                </a>
                <a href="{{ route('payroll.leave-type.index') }}" class="{{ $currentRoute === 'payroll.leave-type.index' ? 'active-sub' : '' }}">
                    <i class="feather icon-chevron-right"></i> {{ __('Leave Type') }}
                </a>
                <a href="{{ route('payroll.leave-type.index', ['add' => 1]) }}" class="">
                    <i class="feather icon-chevron-right"></i> {{ __('Add Leave Type') }}
                </a>
                <a href="{{ route('payroll.leave-application.index') }}" class="{{ $currentRoute === 'payroll.leave-application.index' ? 'active-sub' : '' }}">
                    <i class="feather icon-chevron-right"></i> {{ __('Leave Record') }}
                </a>
                <a href="{{ route('payroll.leave-application.index', ['add' => 1]) }}" class="">
                    <i class="feather icon-chevron-right"></i> {{ __('Add Leave Application') }}
                </a>
                <a href="{{ route('payroll.attendance.index') }}" class="{{ $currentRoute === 'payroll.attendance.index' ? 'active-sub' : '' }}">
                    <i class="feather icon-chevron-right"></i> {{ __('Attendance Record') }}
                </a>
                <a href="{{ route('payroll.salary-sheet.index') }}" class="{{ $currentRoute === 'payroll.salary-sheet.index' ? 'active-sub' : '' }}">
                    <i class="feather icon-chevron-right"></i> {{ __('Salary Sheet & Payslip') }}
                </a>
            </div>
        </div>
        @endif

        {{-- ===== FINANCE RECORD ===== --}}
        @if(!in_array('finance', $hiddenModules))
        <div class="sidebar-category-label px-3 pt-3 pb-1 mt-2 border-t border-slate-700/50">
            <p class="sidebar-label text-xs font-bold uppercase tracking-wider text-green-400/80">{{ __('Finance Management') }}</p>
        </div>

        <div class="has-submenu mb-0.5">
            <a href="javascript:void(0);" onclick="toggleSubmenu(this)"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 cursor-pointer text-slate-300 hover:bg-slate-700/60 hover:text-white">
                <span class="flex-shrink-0 w-[22px] h-[22px]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                </span>
                <span class="sidebar-label flex-1">{{ __('Finance Record') }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="chevron sidebar-label w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            <div class="sidebar-submenu">
                <a href="#" class="">
                    <i class="feather icon-chevron-right"></i> {{ __('Chart of Account') }}
                </a>
                <a href="#" class="">
                    <i class="feather icon-chevron-right"></i> {{ __('Profit And Loss') }}
                </a>
                <a href="#" class="">
                    <i class="feather icon-chevron-right"></i> {{ __('Trial Balance') }}
                </a>
                <a href="#" class="">
                    <i class="feather icon-chevron-right"></i> {{ __('Balance Sheet') }}
                </a>
                <a href="#" class="">
                    <i class="feather icon-chevron-right"></i> {{ __('Finance Analysis') }}
                </a>
            </div>
        </div>
        @endif

        {{-- ===== ACCOUNT ===== --}}
        @if (main_menu_permission('bank-account'))
            <div class="sidebar-category-label px-3 pt-3 pb-1 mt-2 border-t border-slate-700/50">
                <p class="sidebar-label text-xs font-bold uppercase tracking-wider text-orange-400/80">{{ __('Account') }}</p>
            </div>

            <div class="has-submenu {{ in_array($currentUrl, ['bank-account','deposit','withdraw','bank-transfer-index','transaction-history']) || str_starts_with($currentUrl, 'ownership') ? 'open' : '' }} mb-0.5">
                <a href="javascript:void(0);" onclick="toggleSubmenu(this)"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[15px] font-medium transition-all duration-200 cursor-pointer text-slate-300 hover:bg-slate-700/60 hover:text-white">
                    <span class="flex-shrink-0 w-[22px] h-[22px]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                        </svg>
                    </span>
                    <span class="sidebar-label flex-1">{{ __('Account') }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="chevron sidebar-label w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <div class="sidebar-submenu">
                    @if (check_permission('bank-account.index'))
                        @if(!in_array('bank_account', $hiddenModules))
                        <a href="{{ url('bank-account') }}"><i class="feather icon-credit-card"></i> {{ __('Bank Account') }}</a>
                        @endif
                    @endif
                    @if (check_permission('deposit-create'))
                        @if(!in_array('deposit', $hiddenModules))
                        <a href="{{ url('deposit') }}"><i class="feather icon-plus-square"></i> {{ __('Deposit') }}</a>
                        @endif
                    @endif
                    @if (check_permission('bank-transfer-index'))
                        @if(!in_array('bank_transfer', $hiddenModules))
                        <a href="{{ url('bank-transfer-index') }}"><i class="feather icon-shuffle"></i> {{ __('Bank Transfer') }}</a>
                        @endif
                    @endif
                    @if (check_permission('withdraw'))
                        @if(!in_array('withdraw', $hiddenModules))
                        <a href="{{ url('withdraw') }}"><i class="feather icon-minus-square"></i> {{ __('Withdraw') }}</a>
                        @endif
                    @endif
                    @if (check_permission('ownership'))
                        <a href="{{ route('ownership.index') }}"><i class="feather icon-user-check"></i> {{ __('Ownership') }}</a>
                    @endif
                    @if (check_permission('transaction-history'))
                        @if(!in_array('transaction_history', $hiddenModules))
                        <a href="{{ url('transaction-history') }}"><i class="feather icon-activity"></i> {{ __('Transactions') }}</a>
                        @endif
                    @endif
                </div>
            </div>
        @endif

        {{-- ===== REPORT & LEDGER ===== --}}
        <div class="sidebar-category-label px-3 pt-3 pb-1 mt-2 border-t border-slate-700/50">
            <p class="sidebar-label text-xs font-bold uppercase tracking-wider text-orange-400/80">{{ __('Report & Ledger') }}</p>
        </div>

        <div class="has-submenu {{ str_starts_with($currentUrl, 'report') ? 'open' : '' }} mb-0.5">
            <a href="javascript:void(0);" onclick="toggleSubmenu(this)"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 cursor-pointer {{ str_starts_with($currentUrl, 'report') ? 'bg-slate-700/80 text-white' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                <span class="flex-shrink-0 w-[22px] h-[22px]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </span>
                <span class="sidebar-label flex-1">{{ __('Reports') }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="chevron sidebar-label w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            <div class="sidebar-submenu">
                @if (check_permission('report.daily.stock'))
                    <a href="{{ route('report.daily.stock') }}" class="{{ Route::is('report.daily.stock') ? 'active-sub' : '' }}"><i class="feather icon-calendar"></i> {{ __('Daily Stock') }}</a>
                @endif
                @if (check_permission('report.inventory.register'))
                    <a href="{{ route('report.inventory.register') }}" class="{{ Route::is('report.inventory.register') ? 'active-sub' : '' }}"><i class="feather icon-list"></i> {{ __('Inventory Register') }}</a>
                @endif
                @if (check_permission('report.stock'))
                    <a href="{{ route('report.stock') }}" class="{{ Route::is('report.stock') ? 'active-sub' : '' }}"><i class="feather icon-box"></i> {{ __('Stock Report') }}</a>
                @endif
                @if (check_permission('report.daily'))
                    <a href="{{ route('report.daily') }}" class="{{ Route::is('report.daily') ? 'active-sub' : '' }}"><i class="feather icon-pie-chart"></i> {{ __('Daily Report') }}</a>
                @endif
                @if (check_permission('report.item-sale'))
                    <a href="{{ route('report.item-sale') }}" class="{{ Route::is('report.item-sale') ? 'active-sub' : '' }}"><i class="feather icon-shopping-bag"></i> {{ __('Item Sale') }}</a>
                @endif
                @if (check_permission('report.sale'))
                    <a href="{{ route('report.sale') }}" class="{{ Route::is('report.sale') ? 'active-sub' : '' }}"><i class="feather icon-trending-up"></i> {{ __('Sale Report') }}</a>
                @endif
                @if (check_permission('report.purchase'))
                    <a href="{{ route('report.purchase') }}" class="{{ Route::is('report.purchase') ? 'active-sub' : '' }}"><i class="feather icon-shopping-cart"></i> {{ __('Purchase Report') }}</a>
                @endif
                @if (check_permission('report.supplier-ledger'))
                    <a href="{{ route('report.supplier-ledger') }}" class="{{ Route::is('report.supplier-ledger') ? 'active-sub' : '' }}"><i class="feather icon-book"></i> {{ __('Supplier Ledger') }}</a>
                @endif
                @if (check_permission('report.customer-ledger'))
                    <a href="{{ route('report.customer-ledger') }}" class="{{ Route::is('report.customer-ledger') ? 'active-sub' : '' }}"><i class="feather icon-book-open"></i> {{ __('Customer Ledger') }}</a>
                @endif
                @if (check_permission('report.account-ledger'))
                    <a href="{{ route('report.account-ledger') }}" class="{{ Route::is('report.account-ledger') ? 'active-sub' : '' }}"><i class="feather icon-list"></i> {{ __('Account Ledger') }}</a>
                @endif
                @if (check_permission('report.profit-loss'))
                    <a href="{{ route('report.profit-loss') }}" class="{{ Route::is('report.profit-loss') ? 'active-sub' : '' }}"><i class="feather icon-dollar-sign"></i> {{ __('Profit & Loss') }}</a>
                @endif
                @if (check_permission('report.low.stock'))
                    <a href="{{ route('report.low.stock') }}" class="{{ Route::is('report.low.stock') ? 'active-sub' : '' }}"><i class="feather icon-alert-triangle"></i> {{ __('Low Stock') }}</a>
                @endif
                @if (check_permission('report.user.sell'))
                    <a href="{{ route('report.user.sell') }}" class="{{ Route::is('report.user.sell') ? 'active-sub' : '' }}"><i class="feather icon-user"></i> {{ __('User Sell') }}</a>
                @endif
                @if (check_permission('report.customer-due'))
                    <a href="{{ route('report.customer-due') }}" class="{{ Route::is('report.customer-due') ? 'active-sub' : '' }}"><i class="feather icon-user-minus"></i> {{ __('Customer Due') }}</a>
                @endif
                @if (check_permission('report.supplier-due'))
                    <a href="{{ route('report.supplier-due') }}" class="{{ Route::is('report.supplier-due') ? 'active-sub' : '' }}"><i class="feather icon-user-plus"></i> {{ __('Supplier Due') }}</a>
                @endif
                @if (check_permission('report.service'))
                    <a href="{{ route('report.service') }}" class="{{ Route::is('report.service') ? 'active-sub' : '' }}"><i class="feather icon-server"></i> {{ __('Service Report') }}</a>
                @endif
                @if (check_permission('report.vat'))
                    <a href="{{ route('report.vat') }}" class="{{ Route::is('report.vat') ? 'active-sub' : '' }}"><i class="feather icon-percent"></i> {{ __('VAT Report') }}</a>
                @endif
                @if (check_permission('report.discount'))
                    <a href="{{ route('report.discount') }}" class="{{ Route::is('report.discount') ? 'active-sub' : '' }}"><i class="feather icon-tag"></i> {{ __('Discount Report') }}</a>
                @endif
                @if (check_permission('report.top-selling') || check_permission('report.sale'))
                    <a href="{{ route('report.top-selling') }}" class="{{ Route::is('report.top-selling') ? 'active-sub' : '' }}"><i class="feather icon-award"></i> {{ __('Top Selling Product') }}</a>
                @endif
            </div>
        </div>

        {{-- ===== SETTINGS ===== --}}
        <div class="sidebar-category-label px-3 pt-3 pb-1 mt-2 border-t border-slate-700/50">
            <p class="sidebar-label text-xs font-bold uppercase tracking-wider text-orange-400/80">{{ __('Settings') }}</p>
        </div>

        @if(!in_array('branch', $hiddenModules))
        @if (check_permission('branch.index'))
        <a href="{{ route('branch.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ str_starts_with($currentUrl, 'branch') ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
            <span class="flex-shrink-0 w-[22px] h-[22px]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </span>
            <span class="sidebar-label">{{ __('Branch') }}</span>
        </a>
        @endif
        @endif

        @if(!in_array('user', $hiddenModules))
        @if (main_menu_permission('user'))
            <div class="has-submenu {{ str_starts_with($currentUrl, 'user') ? 'open' : '' }} mb-0.5">
                <a href="javascript:void(0);" onclick="toggleSubmenu(this)"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 cursor-pointer {{ str_starts_with($currentUrl, 'user') ? 'bg-slate-700/80 text-white' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                    <span class="flex-shrink-0 w-[22px] h-[22px]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </span>
                    <span class="sidebar-label flex-1">{{ __('Users') }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="chevron sidebar-label w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <div class="sidebar-submenu">
                    @if (check_permission('user.create'))
                        <a href="{{ route('user.create') }}" class="{{ $currentRoute === 'user.create' ? 'active-sub' : '' }}">
                            <i class="feather icon-plus-circle"></i> {{ __('Add User') }}
                        </a>
                    @endif
                    @if (check_permission('user.index'))
                        <a href="{{ route('user.index') }}" class="{{ $currentRoute === 'user.index' ? 'active-sub' : '' }}">
                            <i class="feather icon-users"></i> {{ __('User List') }}
                        </a>
                    @endif
                    <a href="{{ route('profile') }}" class="{{ $currentRoute === 'profile' ? 'active-sub' : '' }}">
                        <i class="feather icon-user"></i> {{ __('My Profile') }}
                    </a>
                </div>
            </div>
        @endif
        @endif

        @if (main_menu_permission('roles-permission'))
            <div class="has-submenu {{ str_starts_with($currentUrl, 'roles-permission') ? 'open' : '' }} mb-0.5">
                <a href="javascript:void(0);" onclick="toggleSubmenu(this)"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 cursor-pointer {{ str_starts_with($currentUrl, 'roles-permission') ? 'bg-slate-700/80 text-white' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                    <span class="flex-shrink-0 w-[22px] h-[22px]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </span>
                    <span class="sidebar-label flex-1">{{ __('Roles') }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="chevron sidebar-label w-3.5 h-3.5 text-slate-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <div class="sidebar-submenu">
                    <a href="{{ route('roles-permission.index') }}">
                        <i class="feather icon-shield"></i> {{ __('Role & Permission') }}
                    </a>
                </div>
            </div>
        @endif

        @if (auth()->check() && auth()->user()->role && in_array(auth()->user()->role->slug, ['superadmin', 'super-admin']))
            <a href="{{ route('super-admin.settings') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ $currentRoute === 'super-admin.settings' ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                <span class="flex-shrink-0 w-[22px] h-[22px] text-orange-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </span>
                <span class="sidebar-label">{{ __('Super Admin Settings') }}</span>
            </a>
        @endif

        @if (check_permission('setting.index'))
            <a href="{{ route('setting.index') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ $currentRoute === 'setting.index' ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
                <span class="flex-shrink-0 w-[22px] h-[22px]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </span>
                <span class="sidebar-label">{{ __('Settings') }}</span>
            </a>
        @endif

        @if(!in_array('activity_log', $hiddenModules))
        <a href="{{ route('activity-log.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 {{ $currentRoute === 'activity-log.index' ? 'active' : 'text-slate-300 hover:bg-slate-700/60 hover:text-white' }}">
            <span class="flex-shrink-0 w-[22px] h-[22px]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
            </span>
            <span class="sidebar-label">{{ __('Activity Log') }}</span>
        </a>
        @endif

        @if (check_permission('status.download.backup'))
            <a href="{{ route('status.download.backup') }}"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[14.5px] font-medium transition-all duration-200 mb-0.5 text-slate-300 hover:bg-slate-700/60 hover:text-white">
                <span class="flex-shrink-0 w-[22px] h-[22px]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                </span>
                <span class="sidebar-label">{{ __('Backup') }}</span>
            </a>
        @endif
    </nav>



</div>

{{-- Hidden Logout Form --}}
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
    @csrf
</form>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('modernSidebar');
        const rightbar = document.querySelector('.rightbar');
        const icon = document.getElementById('toggleIcon');

        if (window.innerWidth > 768) {
            const isMini = sidebar.classList.toggle('mini');
            if (rightbar) {
                rightbar.classList.toggle('mini');
                rightbar.style.marginLeft = isMini ? '80px' : '270px';
            }
            if (icon) {
                icon.style.transform = isMini ? 'rotate(180deg)' : 'rotate(0deg)';
            }
            // Save state
            localStorage.setItem('sidebarState', isMini ? 'mini' : 'full');
        } else {
            if (sidebar.classList.contains('mobile-open')) {
                closeMobileSidebar();
            } else {
                openMobileSidebar();
            }
        }
    }

    // Initialize — open submenus and restore mini state
    document.addEventListener('DOMContentLoaded', function() {
        // Restore mini state
        if (window.innerWidth > 768 && localStorage.getItem('sidebarState') === 'mini') {
            const sidebar = document.getElementById('modernSidebar');
            const rightbar = document.querySelector('.rightbar');
            const icon = document.getElementById('toggleIcon');
            
            sidebar.classList.add('mini');
            if (rightbar) {
                rightbar.classList.add('mini');
                rightbar.style.marginLeft = '80px';
            }
            if (icon) {
                icon.style.transform = 'rotate(180deg)';
            }
        }

        document.querySelectorAll('.has-submenu.open .sidebar-submenu').forEach(function(submenu) {
            submenu.style.maxHeight = submenu.scrollHeight + 'px';
            submenu.style.opacity = '1';
            submenu.style.paddingTop = '2px';
            submenu.style.paddingBottom = '4px';
        });

        // Close mobile sidebar immediately on clicking a normal link for visual feedback
        document.querySelectorAll('.sidebar-nav a:not([onclick])').forEach(function(link) {
            link.addEventListener('click', function(e) {
                if (window.innerWidth <= 768 && link.getAttribute('href') !== '#') {
                    closeMobileSidebar();
                }
            });
        });
    });

    function toggleSubmenu(el) {
        const parent = el.closest('.has-submenu');
        const submenu = parent.querySelector('.sidebar-submenu');
        const isOpen = parent.classList.contains('open');

        if (isOpen) {
            // Close
            submenu.style.maxHeight = submenu.scrollHeight + 'px';
            // Force reflow
            submenu.offsetHeight;
            submenu.style.maxHeight = '0px';
            submenu.style.opacity = '0';
            submenu.style.paddingTop = '0';
            submenu.style.paddingBottom = '0';
            parent.classList.remove('open');
        } else {
            // Open
            submenu.style.maxHeight = submenu.scrollHeight + 'px';
            submenu.style.opacity = '1';
            submenu.style.paddingTop = '2px';
            submenu.style.paddingBottom = '4px';
            parent.classList.add('open');
        }
    }

    function openMobileSidebar() {
        const sidebar = document.getElementById('modernSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.add('mobile-open');
        if (overlay) overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileSidebar() {
        const sidebar = document.getElementById('modernSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.remove('mobile-open');
        if (overlay) overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    // Close sidebar on resize to desktop
    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) {
            closeMobileSidebar();
        }
    });
</script>
