<!-- Mobile Navigation Drawer -->
<div x-show="mobileMenuOpen" 
     class="fixed inset-0 z-50 overflow-hidden lg:hidden" 
     aria-labelledby="mobile-nav-title" 
     role="dialog" 
     aria-modal="true"
     style="display: none;">
    
    <!-- Backdrop -->
    <div x-show="mobileMenuOpen" 
         x-transition:enter="ease-in-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in-out duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileMenuOpen = false" 
         class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity"></div>

    <div class="fixed inset-y-0 left-0 max-w-full flex pr-10">
        <!-- Drawer Panel -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transform transition ease-in-out duration-300"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-300"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="w-screen max-w-xs bg-[#111111] text-white shadow-2xl flex flex-col justify-between border-r border-neutral-800">
            
            <!-- Drawer Header -->
            <div class="p-5 border-b border-neutral-800 flex items-center justify-between">
                <a href="{{ url('/') }}" class="block">
                    <img src="{{ $siteLogo ?? (!empty(get_setting('system_logo')) && file_exists(public_path('uploads/logo/' . get_setting('system_logo'))) ? asset('uploads/logo/' . get_setting('system_logo')) : asset('frontend/images/robe_logo.png')) }}" 
                         alt="{{ $comName ?? 'Kingsman' }} Logo" class="h-8 w-auto object-contain">
                </a>
                <button @click="mobileMenuOpen = false" class="text-neutral-400 hover:text-white p-1" aria-label="Close menu">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Categories Nav Links -->
            <div class="flex-1 overflow-y-auto p-5 custom-scrollbar space-y-4">
                <p class="text-[11px] font-bold text-neutral-400 uppercase tracking-widest">Navigation</p>
                <ul class="space-y-1 text-sm font-medium">
                    @if(isset($navCategories) && $navCategories->isNotEmpty())
                        @foreach($navCategories as $cat)
                            @php
                                $slug = \Illuminate\Support\Str::slug($cat->name);
                                $isOffer = str_contains(strtolower($cat->name), 'offer');
                            @endphp
                            <li>
                                @if($isOffer)
                                    <a href="{{ route('category.products', 'offer') }}" @click="mobileMenuOpen = false" class="block py-2.5 px-3 rounded bg-red-950/40 text-red-400 font-bold hover:bg-red-900/60 transition border border-red-800/40">
                                        <i class="fa-solid fa-fire mr-2 text-red-500"></i> {{ $cat->name }}
                                    </a>
                                @else
                                    <a href="{{ route('category.products', $slug) }}" @click="mobileMenuOpen = false" class="block py-2.5 px-3 rounded hover:bg-neutral-800 text-gray-200 hover:text-white transition">
                                        <i class="fa-solid fa-angle-right mr-2 text-neutral-500 text-xs"></i> {{ $cat->name }}
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    @else
                        <li>
                            <a href="{{ route('products.index') }}" @click="mobileMenuOpen = false" class="block py-2.5 px-3 rounded hover:bg-neutral-800 text-gray-200 hover:text-white transition">
                                <i class="fa-solid fa-shirt mr-2 text-neutral-400"></i> All Products
                            </a>
                        </li>
                    @endif
                </ul>
            </div>

            <!-- Drawer Footer -->
            <div class="p-5 border-t border-neutral-800 text-center text-xs text-neutral-500">
                <p>© {{ $comName ?? 'Kingsman' }} {{ date('Y') }}</p>
            </div>
        </div>
    </div>
</div>
