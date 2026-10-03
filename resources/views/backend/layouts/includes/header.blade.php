<header class="sticky top-0 z-[100] h-[72px] bg-white/90 dark:bg-slate-900/95 backdrop-blur-2xl backdrop-saturate-[180%] border-b border-slate-200/60 dark:border-slate-700/60 transition-all duration-400 ease-[cubic-bezier(0.4,0,0.2,1)] flex items-center px-4 lg:px-8 shadow-sm">
    <div class="w-full flex items-center justify-between">
        
        {{-- Left: Navigation Toggles --}}
        <div class="flex items-center gap-3">
            <div class="bg-slate-50 dark:bg-slate-800 p-[5px] rounded-full flex items-center gap-[2px] border border-slate-100 dark:border-slate-700 shadow-sm">
                <button onclick="toggleSidebar()" class="w-[38px] h-[38px] flex items-center justify-center rounded-full transition-all duration-300 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-blue-500 dark:hover:text-blue-400 border border-transparent hover:border-blue-500/10 hover:-translate-y-[1.5px]" title="Toggle Sidebar">
                    <i class="fa-solid fa-bars-staggered text-sm"></i>
                </button>
                <button id="full-hide-toggle" class="w-[38px] h-[38px] flex items-center justify-center rounded-full transition-all duration-300 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-blue-500 dark:hover:text-blue-400 border border-transparent hover:border-blue-500/10 hover:-translate-y-[1.5px]" title="Full Width View">
                    <i class="fa-solid fa-eye-slash text-sm"></i>
                </button>
            </div> 
            
        </div>

        {{-- Right: System Suite & User Profile --}}
        <div class="flex items-center gap-5">
            @php
                $supportNumber = (env('APP_MODE') == 'demo') ? '01784-159071' : '01901-166585';
                $waNumber = '88' . str_replace('-', '', $supportNumber);
            @endphp
            
            <!-- Tools Center -->
            <div class="hidden sm:flex bg-slate-50 dark:bg-slate-800 p-[5px] rounded-full items-center gap-[2px] border border-slate-100 dark:border-slate-700 shadow-sm">
                <div class="flex items-center gap-3 px-4 py-1.5 border-r border-slate-100 dark:border-slate-800 mr-1">
                    <div class="relative flex h-2 w-2">
                        <span class="animate-[ping_2s_infinite] absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </div>
                    <span id="digital-clock" class="text-[12px] font-black text-slate-700 dark:text-slate-300 tracking-wider">00:00:00 AM</span>
                </div>

                <button id="calculator-toggle" class="w-[38px] h-[38px] flex items-center justify-center rounded-full transition-all duration-300 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-blue-500 dark:hover:text-blue-400 border border-transparent hover:border-blue-500/10 hover:-translate-y-[1.5px]" title="Quick Calculator">
                    <i class="fa-solid fa-calculator text-xs"></i>
                </button>

                <button id="full-screen-toggle" class="w-[38px] h-[38px] flex items-center justify-center rounded-full transition-all duration-300 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-blue-500 dark:hover:text-blue-400 border border-transparent hover:border-blue-500/10 hover:-translate-y-[1.5px]" title="Full Screen">
                    <i class="fa-solid fa-expand text-xs"></i>
                </button>
                
                <button id="theme-toggle" class="w-[38px] h-[38px] flex items-center justify-center rounded-full transition-all duration-300 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-blue-500 dark:hover:text-blue-400 border border-transparent hover:border-blue-500/10 hover:-translate-y-[1.5px]" title="Switch Theme">
                    <i class="fa-solid fa-moon text-xs"></i>
                </button>

                <div class="flex items-center ml-2 bg-slate-50 dark:bg-slate-800 p-0.5 rounded-full border border-slate-100 dark:border-slate-700">
                    <a href="{{ url('lang/en') }}" class="px-3 py-1.5 text-[10px] font-black rounded-full {{ App::getLocale() == 'en' ? 'bg-white shadow-sm text-blue-600' : 'text-slate-400' }} transition-all uppercase">EN</a>
                    <a href="{{ url('lang/bn') }}" class="px-3 py-1.5 text-[10px] font-black rounded-full {{ App::getLocale() == 'bn' ? 'bg-white shadow-sm text-blue-600' : 'text-slate-400' }} transition-all uppercase">BN</a>
                </div>
            </div>

            <!-- Mobile Quick Actions Suite (Only on Mobile) -->
            <div class="flex md:hidden dropdown">
                <button class="w-10 h-10 flex items-center justify-center bg-slate-50 dark:bg-slate-800 text-slate-500 rounded-full border border-slate-100 dark:border-slate-700 shadow-sm" type="button" data-toggle="dropdown">
                    <i class="fa-solid fa-grip text-sm"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-center !left-1/2 !-translate-x-1/2 !right-auto mt-2 w-[280px] bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl shadow-xl p-3">
                    <div class="flex flex-col gap-3">
                        <div class="flex items-center justify-between px-2 pb-2 border-b border-slate-50 dark:border-slate-800">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ __('System Suite') }}</span>
                            <span id="digital-clock-mobile" class="text-[11px] font-black text-blue-500">00:00:00</span>
                        </div>
                        
                        <div class="grid grid-cols-4 gap-2">
                            <button id="theme-toggle-mobile" class="flex flex-col items-center justify-center gap-1.5 p-2 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 transition-all active:scale-95">
                                <i class="fa-solid fa-moon text-slate-500"></i>
                                <span class="text-[9px] font-bold text-slate-600 dark:text-slate-400">{{ __('Theme') }}</span>
                            </button>
                            <button id="calculator-toggle-mobile" class="flex flex-col items-center justify-center gap-1.5 p-2 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 transition-all active:scale-95">
                                <i class="fa-solid fa-calculator text-slate-500"></i>
                                <span class="text-[9px] font-bold text-slate-600 dark:text-slate-400">{{ __('Calc') }}</span>
                            </button>
                            <button id="full-screen-toggle-mobile" class="flex flex-col items-center justify-center gap-1.5 p-2 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 transition-all active:scale-95">
                                <i class="fa-solid fa-expand text-slate-500"></i>
                                <span class="text-[9px] font-bold text-slate-600 dark:text-slate-400">{{ __('Full') }}</span>
                            </button>
                            <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="flex flex-col items-center justify-center gap-1.5 p-2 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-100 dark:border-green-900/30 transition-all">
                                <i class="fa-brands fa-whatsapp text-green-500"></i>
                                <span class="text-[9px] font-bold text-green-600">{{ __('Help') }}</span>
                            </a>
                        </div>

                        <div class="flex items-center gap-2 p-1.5 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700">
                            <a href="{{ url('lang/en') }}" class="flex-1 py-2 text-center text-[10px] font-black rounded-lg {{ App::getLocale() == 'en' ? 'bg-white shadow-sm text-blue-600' : 'text-slate-400' }}">ENGLISH</a>
                            <a href="{{ url('lang/bn') }}" class="flex-1 py-2 text-center text-[10px] font-black rounded-lg {{ App::getLocale() == 'bn' ? 'bg-white shadow-sm text-blue-600' : 'text-slate-400' }}">বাংলা</a>
                        </div>

                        @if (is_branch_switch_enabled() && check_permission('switch.branch'))
                        <div class="pt-2 border-t border-slate-50 dark:border-slate-800">
                             <form method="POST" action="{{ route('switch.branch') }}" class="m-0">
                                @csrf
                                <select name="branch_id" onchange="this.form.submit()" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-lg py-2 px-3 text-[11px] font-bold text-slate-700 dark:text-slate-300 focus:outline-none">
                                    @foreach (App\Models\Branch::all() as $branch)
                                        <option value="{{ $branch->id }}" {{ session('branch_filter_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                        @else
                        <div class="pt-2 border-t border-slate-50 dark:border-slate-800 text-center">
                            <span class="w-full block bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-lg py-2 px-3 text-[11px] font-bold text-slate-700 dark:text-slate-300">{{ auth()->user()->branch->name ?? 'No Branch' }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="hidden md:flex items-center gap-3">
                <a href="https://wa.me/{{ $waNumber }}" target="_blank" class="w-10 h-10 flex items-center justify-center bg-green-500/10 text-green-600 dark:text-green-500 rounded-full hover:bg-green-500 hover:text-white transition-all shadow-sm group">
                    <i class="fa-brands fa-whatsapp text-lg group-hover:scale-110 transition-transform"></i>
                </a>

                @if (is_branch_switch_enabled() && check_permission('switch.branch'))
                <form method="POST" action="{{ route('switch.branch') }}" id="branchForm" class="m-0">
                    @csrf
                    <div class="flex items-center gap-2 px-4 py-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl hover:border-blue-400 transition-all cursor-pointer shadow-sm">
                        <i class="fa-solid fa-code-branch text-blue-500 text-xs"></i>
                        <select name="branch_id" onchange="this.form.submit()" class="bg-transparent border-none text-[12px] font-black text-slate-700 dark:text-slate-300 focus:outline-none appearance-none cursor-pointer pr-1">
                            @foreach (App\Models\Branch::all() as $branch)
                                <option value="{{ $branch->id }}" {{ session('branch_filter_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
                @else
                <div class="flex items-center gap-2 px-4 py-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm">
                    <i class="fa-solid fa-code-branch text-blue-500 text-xs"></i>
                    <span class="text-[12px] font-black text-slate-700 dark:text-slate-300">{{ auth()->user()->branch->name ?? 'No Branch' }}</span>
                </div>
                @endif
            </div>

            <div class="w-px h-6 bg-slate-200 dark:bg-slate-800 hidden md:block"></div>

          <!-- Profile Dropdown -->
<div class="dropdown relative">

    {{-- Trigger Button --}}
    <div class="flex items-center gap-[10px] pl-[5px] pr-3 py-[5px] bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-700/60 rounded-full cursor-pointer transition-all duration-200 hover:border-blue-500 hover:shadow-[0_0_0_3px_rgba(59,130,246,0.08)] group"
        id="userDropdown" data-toggle="dropdown" data-display="static">

        {{-- Avatar --}}
        <div class="w-9 h-9 rounded-full p-[2px]" style="background: linear-gradient(135deg, #3b82f6, #6366f1)">
            <div class="w-full h-full rounded-full bg-white dark:bg-slate-900 flex items-center justify-center">
                <img src="{{ asset('backend/images/users/profile.svg') }}"
                     class="w-full h-full rounded-full object-cover p-0.5">
            </div>
        </div>

        {{-- Name & Role --}}
        <div class="hidden sm:flex flex-col">
            <span class="text-[12px] font-semibold text-slate-800 dark:text-white leading-none uppercase tracking-tight">
                {{ Auth::user()->name }}
            </span>
            <span class="text-[9px] font-semibold text-slate-400 dark:text-slate-500 mt-1 uppercase tracking-wide leading-none">
                {{ __('Master Admin') }}
            </span>
        </div>

        {{-- Chevron --}}
        <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 group-hover:text-blue-500 transition-all ml-0.5"></i>
    </div>

    {{-- Dropdown Menu --}}
    <div class="dropdown-menu dropdown-menu-right !right-0 !left-auto mt-2 w-[260px] bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl overflow-hidden shadow-xl shadow-slate-900/10 p-0">

        {{-- Header --}}
        <div class="flex items-center gap-3 px-4 py-3.5 border-b border-slate-100 dark:border-slate-800">
            <div class="w-[42px] h-[42px] rounded-full p-[2px] flex-shrink-0" style="background: linear-gradient(135deg, #3b82f6, #6366f1)">
                <div class="w-full h-full rounded-full bg-white dark:bg-slate-900 flex items-center justify-center">
                    <img src="{{ asset('backend/images/users/profile.svg') }}"
                         class="w-full h-full rounded-full object-cover p-0.5">
                </div>
            </div>
            <div class="min-w-0">
                <p class="text-[13px] font-semibold text-slate-800 dark:text-white truncate leading-none">
                    {{ Auth::user()->name }}
                </p>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 truncate mt-1 leading-none">
                    {{ Auth::user()->email }}
                </p>
                <span class="inline-flex mt-1.5 px-2 py-0.5 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-[9px] font-bold uppercase tracking-wider rounded-full">
                    {{ __('Master Admin') }}
                </span>
            </div>
        </div>

        {{-- Menu Items --}}
        <div class="p-1.5">
            @if (env('APP_MODE') != 'demo')
            <a href="{{ route('profile') }}"
               class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-[13px] font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                <div class="w-[30px] h-[30px] flex items-center justify-center bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg flex-shrink-0">
                    <i class="fa-regular fa-user text-xs"></i>
                </div>
                {{ __('My Profile') }}
            </a>
            @endif

            <div class="h-px bg-slate-100 dark:bg-slate-800 mx-1 my-1"></div>

            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logoutForm').submit();"
               class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg text-[13px] font-medium text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors">
                <div class="w-[30px] h-[30px] flex items-center justify-center bg-rose-50 dark:bg-rose-900/30 text-rose-500 rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                </div>
                {{ __('Sign Out') }}
            </a>

            <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        </div>

    </div>
</div>
            
        </div>
    </div>
</header>

@include('backend.layouts.includes.calculator')
@push('js')
<script>
    // Digital Clock
    function updateClock() {
        const now = new Date();
        const options = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
        const timeStr = now.toLocaleTimeString('en-US', options);
        
        const clockElement = document.getElementById('digital-clock');
        const clockMobile = document.getElementById('digital-clock-mobile');
        
        if(clockElement) clockElement.textContent = timeStr;
        if(clockMobile) clockMobile.textContent = timeStr;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Full Screen Toggle Logic
    function toggleFullScreen() {
        const fsButton = document.getElementById('full-screen-toggle');
        const fsButtonMobile = document.getElementById('full-screen-toggle-mobile');
        
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
            if(fsButton) fsButton.innerHTML = '<i class="fa-solid fa-compress text-xs"></i>';
            if(fsButtonMobile) fsButtonMobile.innerHTML = '<i class="fa-solid fa-compress text-slate-500"></i><span class="text-[9px] font-bold text-slate-600 dark:text-slate-400">Exit</span>';
        } else {
            document.exitFullscreen();
            if(fsButton) fsButton.innerHTML = '<i class="fa-solid fa-expand text-xs"></i>';
            if(fsButtonMobile) fsButtonMobile.innerHTML = '<i class="fa-solid fa-expand text-slate-500"></i><span class="text-[9px] font-bold text-slate-600 dark:text-slate-400">Full</span>';
        }
    }

    document.getElementById('full-screen-toggle')?.addEventListener('click', toggleFullScreen);
    document.getElementById('full-screen-toggle-mobile')?.addEventListener('click', toggleFullScreen);

    // Theme Toggle Logic
    function toggleTheme() {
        const body = document.body;
        const html = document.documentElement;
        const icon = document.getElementById('theme-toggle')?.querySelector('i');
        const iconMobile = document.getElementById('theme-toggle-mobile')?.querySelector('i');
        
        if(body.classList.contains('dark-theme')) {
            body.classList.remove('dark-theme');
            html.classList.remove('dark-theme');
            if(icon) icon.classList.replace('fa-sun', 'fa-moon');
            if(iconMobile) iconMobile.classList.replace('fa-sun', 'fa-moon');
            localStorage.setItem('theme', 'light');
        } else {
            body.classList.add('dark-theme');
            html.classList.add('dark-theme');
            if(icon) icon.classList.replace('fa-moon', 'fa-sun');
            if(iconMobile) iconMobile.classList.replace('fa-moon', 'fa-sun');
            localStorage.setItem('theme', 'dark');
        }
    }

    document.getElementById('theme-toggle')?.addEventListener('click', toggleTheme);
    document.getElementById('theme-toggle-mobile')?.addEventListener('click', toggleTheme);

    // Full Hide Sidebar Toggle
    const hideSidebarBtn = document.getElementById('full-hide-toggle');
    const container = document.getElementById('containerbar');
    
    if(hideSidebarBtn && container) {
        hideSidebarBtn.addEventListener('click', function() {
            container.classList.toggle('sidebar-none');
            const icon = this.querySelector('i');
            
            if(container.classList.contains('sidebar-none')) {
                icon.classList.replace('fa-eye-slash', 'fa-eye');
                localStorage.setItem('sidebar_hidden', 'true');
            } else {
                icon.classList.replace('fa-eye', 'fa-eye-slash');
                localStorage.setItem('sidebar_hidden', 'false');
            }
        });
    }

    // Restore States
    if (localStorage.getItem('sidebar_hidden') === 'true') {
        const container = document.getElementById('containerbar');
        if(container) container.classList.add('sidebar-none');
        const hideBtnI = document.getElementById('full-hide-toggle')?.querySelector('i');
        if(hideBtnI) hideBtnI.classList.replace('fa-eye-slash', 'fa-eye');
    }

    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-theme');
        document.documentElement.classList.add('dark-theme');
        const themeBtnI = document.getElementById('theme-toggle')?.querySelector('i');
        if(themeBtnI) themeBtnI.classList.replace('fa-moon', 'fa-sun');
    }
</script>
@endpush
