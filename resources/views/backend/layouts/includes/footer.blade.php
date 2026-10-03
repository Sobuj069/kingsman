@push('css')
    <style>
        /* Modern Bottom Navigation Styles */
        .nav-item-active {
            @apply text-blue-600;
        }
        .nav-item-active i {
            @apply scale-110;
        }
        .nav-item-active .icon-bg {
            @apply bg-blue-50 dark:bg-blue-900/20;
        }
    </style>
@endpush

<!-- Desktop Footer -->
<footer class="hidden md:block border-t shadow-xl border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 mt-auto">
    <div class="px-4 py-1 text-center">
        <p class="text-[11px] text-slate-400 dark:text-slate-500 font-bold tracking-widest uppercase mb-0">
            © {{ date('Y') }} <a href="https://fastit.com.bd/" target="_blank" rel="noopener noreferrer" class="text-blue-500 hover:text-blue-600 transition-colors">FastIT</a> . {{ __('All Rights Reserved') }}
        </p>
    </div>
</footer>

<!-- Mobile Sleek Bottom Navigation -->
<nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 shadow-[0_-10px_25px_rgba(0,0,0,0.03)] z-[999] px-2 pb-safe">
    <div class="flex items-center justify-between max-w-lg mx-auto h-20">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" 
           class="flex-1 flex flex-col items-center justify-center gap-1 group transition-all duration-300 {{ Route::is('dashboard') ? 'nav-item-active' : 'text-slate-400' }}">
            <div class="icon-bg w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 group-active:scale-95">
                <i class="feather icon-home text-lg"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-tight">{{ __('Home') }}</span>
        </a>

        <!-- POS -->
        <a href="{{ route('invoice.create') }}" 
           class="flex-1 flex flex-col items-center justify-center gap-1 group transition-all duration-300 {{ Route::is('invoice.create') ? 'nav-item-active' : 'text-slate-400' }}">
            <div class="icon-bg w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 group-active:scale-95">
                <i class="feather icon-shopping-cart text-lg"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-tight">{{ __('POS') }}</span>
        </a>

        <!-- Invoice -->
        <a href="{{ route('invoice.index') }}" 
           class="flex-1 flex flex-col items-center justify-center gap-1 group transition-all duration-300 {{ Route::is('invoice.index') ? 'nav-item-active' : 'text-slate-400' }}">
            <div class="icon-bg w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 group-active:scale-95">
                <i class="feather icon-file-text text-lg"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-tight">{{ __('Invoice') }}</span>
        </a>

        <!-- Product/Stock -->
        <a href="{{ route('product.index') }}" 
           class="flex-1 flex flex-col items-center justify-center gap-1 group transition-all duration-300 {{ Route::is('product.index') ? 'nav-item-active' : 'text-slate-400' }}">
            <div class="icon-bg w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 group-active:scale-95">
                <i class="feather icon-box text-lg"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-tight">{{ __('Stock') }}</span>
        </a>

        <!-- More Menu -->
        <button onclick="openMobileSidebar()" 
           class="flex-1 flex flex-col items-center justify-center gap-1 group transition-all duration-300 text-slate-400 cursor-pointer">
            <div class="icon-bg w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-300 active:bg-slate-100">
                <i class="feather icon-grid text-lg"></i>
            </div>
            <span class="text-[10px] font-bold uppercase tracking-tight">{{ __('More') }}</span>
        </button>
    </div>
</nav>

<!-- Spacer for mobile to avoid content overlapping fixed bar -->
<div class="md:hidden h-20"></div>
