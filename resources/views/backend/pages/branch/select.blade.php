<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Branch | {{ get_setting('com_name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>    
        body {     
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #020617; 
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.9); /* More solid for better contrast */
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.15); /* Brighter border */
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5); /* Stronger shadow */
        }
        .branch-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .branch-card:hover {
            transform: translateY(-5px);
            background: rgba(15, 23, 42, 0.98);
            border-color: rgba(99, 102, 241, 0.6);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.7);
        }
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }
        /* Hide scrollbar completely */
        ::-webkit-scrollbar { display: none; }
        html { scrollbar-width: none; -ms-overflow-style: none; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center py-6 px-4 relative overflow-x-hidden">
    
    <!-- Background Image (Same as Login) -->
    <div class="fixed inset-0 z-0">
        <img
            src="{{ asset('backend/images/multishop_bg.png') }}"
            alt="Background"
            class="w-full h-full object-cover"
            style="filter: blur(4px) brightness(0.3); transform: scale(1.05);"
        />
        <div class="absolute inset-0 bg-slate-950/40"></div>
    </div>
    
    <!-- Background Glows -->
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-indigo-900/20 rounded-full blur-[100px] z-1"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-900/20 rounded-full blur-[100px] z-1"></div>

    <div class="w-full max-w-4xl relative z-10 animate-fade-in">
        <div class="text-center mb-6 sm:mb-10">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-500/10 border border-indigo-500/20 rounded-full mb-3 sm:mb-4">
                <span class="relative flex h-1.5 w-1.5">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-indigo-500"></span>
                </span>
                <span class="text-indigo-400 text-[10px] font-bold uppercase tracking-widest">Authentication Successful</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white mb-1 sm:mb-2 tracking-tight">Select a Branch</h1>
            <p class="text-slate-400 text-xs sm:text-sm">Choose your store location to manage your operations</p>
        </div>

        <div class="flex flex-wrap justify-center gap-3 sm:gap-5">
            @forelse($branches as $branch)
            <form action="{{ route('branch.set') }}" method="POST" class="w-full sm:w-[280px]">
                @csrf
                <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                <button type="submit" class="w-full text-left group outline-none">
                    <div class="branch-card glass-card p-4 sm:p-6 rounded-2xl h-full relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-500/5 rounded-full blur-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        
                        <div class="flex items-center justify-between mb-4 sm:mb-6">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-slate-800/80 text-indigo-400 rounded-xl flex items-center justify-center text-xl sm:text-2xl group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 border border-slate-700/50 group-hover:border-indigo-500">
                                <i class="fa-solid fa-store"></i>
                            </div>
                            <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-slate-800/50 flex items-center justify-center text-slate-500 group-hover:text-indigo-400 transition-all text-[10px] sm:text-xs">
                                <i class="fa-solid fa-arrow-right"></i>
                            </div>
                        </div>
                        
                        <h3 class="text-base sm:text-lg font-bold text-white mb-1 leading-tight group-hover:text-indigo-400 transition-colors">{{ $branch->name }}</h3>
                        <p class="text-slate-400 text-[11px] sm:text-xs mb-4 sm:mb-6 h-8 line-clamp-2 leading-relaxed">{{ $branch->address ?? 'Location not specified' }}</p>
                        
                        <div class="flex items-center gap-1.5">
                            <span class="px-2 py-0.5 bg-emerald-500/10 text-emerald-500 text-[9px] font-bold uppercase tracking-wider rounded-md border border-emerald-500/20">Operational</span>
                            @if(auth()->user()->branch_id == $branch->id)
                            <span class="px-2 py-0.5 bg-indigo-500/10 text-indigo-400 text-[9px] font-bold uppercase tracking-wider rounded-md border border-indigo-500/20">Default</span>
                            @endif
                        </div>
                    </div>
                </button>
            </form>
            @empty
            <div class="w-full max-w-sm text-center p-8 sm:p-12 glass-card rounded-2xl border-dashed border-slate-700">
                <i class="fa-solid fa-store-slash text-slate-600 text-3xl mb-4"></i>
                <p class="text-slate-400 font-bold">No active branches found</p>
            </div>
            @endforelse
        </div>

        <div class="mt-8 sm:mt-12 text-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 hover:bg-red-500 hover:text-white transition-all font-bold text-xs tracking-wide">
                    <i class="fa-solid fa-power-off"></i>
                    LOGOUT SYSTEM
                </button>
            </form>
        </div>
    </div>

    <script>
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const card = this.querySelector('.branch-card');
                if (card) {
                    card.style.transform = 'scale(0.97)';
                    card.style.opacity = '0.7';
                }
            });
        });
    </script>
</body>
</html>
