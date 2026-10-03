<!-- Main Sticky Header & Navigation Bar -->
<header class="w-full sticky top-0 z-40 bg-[#121212] border-b border-neutral-800 shadow-lg" data-purpose="robe-main-header">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 md:h-20 gap-4">
            
            <!-- Mobile Menu Toggle Button -->
            <button @click="mobileMenuOpen = true" 
                    type="button" 
                    class="lg:hidden p-2 text-gray-300 hover:text-white focus:outline-none"
                    aria-label="Open Navigation Menu">
                <i class="fa-solid fa-bars-staggered text-xl"></i>
            </button>

            <!-- Brand Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ url('/') }}" class="flex items-center gap-2 group">
                    <img src="{{ $siteLogo ?? (!empty(get_setting('system_logo')) && file_exists(public_path('uploads/logo/' . get_setting('system_logo'))) ? asset('uploads/logo/' . get_setting('system_logo')) : asset('frontend/images/robe_logo.png')) }}" 
                         alt="{{ $comName ?? 'Kingsman' }} Official Logo" 
                         class="h-9 md:h-12 w-auto object-contain brightness-100 group-hover:scale-105 transition-transform duration-300">
                </a>
            </div>

            <!-- Central Live Product Search Bar (Desktop) -->
            <div class="flex-1 max-w-2xl mx-2 md:mx-6 hidden sm:block relative" x-data="{ focused: false }">
                <div class="relative w-full">
                    <input type="text" 
                           placeholder="Search Panjabi, Shirt, T-Shirt, Jeans Pant..." 
                           x-model="searchQuery"
                           @input.debounce.300ms="performSearch()"
                           @keydown.enter="submitSearch()"
                           @focus="focused = true; searchOpen = true"
                           @click.away="focused = false"
                           class="w-full bg-[#1e1e1e] text-sm text-gray-200 placeholder-gray-400 pl-4 pr-12 py-2.5 rounded-full border border-neutral-700 focus:outline-none focus:border-neutral-500 focus:ring-1 focus:ring-neutral-400 transition shadow-inner">
                    
                    <button type="button" 
                            @click="submitSearch()"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition" 
                            aria-label="Search">
                        <template x-if="!searchLoading">
                            <i class="fa-solid fa-magnifying-glass text-sm"></i>
                        </template>
                        <template x-if="searchLoading">
                            <i class="fa-solid fa-circle-notch fa-spin text-sm text-red-500"></i>
                        </template>
                    </button>
                </div>

                <!-- Instant Search Results Dropdown -->
                <div x-show="focused && searchResults.length > 0" 
                     x-transition 
                     class="absolute left-0 right-0 top-full mt-2 bg-[#1a1a1a] border border-neutral-700 rounded-xl shadow-2xl z-50 overflow-hidden"
                     style="display: none;">
                    <div class="p-2 border-b border-neutral-800 text-[11px] font-semibold text-neutral-400 uppercase tracking-wider px-3 flex items-center justify-between">
                        <span>Matching Garments (<span x-text="searchResults.length"></span>)</span>
                        <span class="text-[10px] text-neutral-500">Click to view product</span>
                    </div>
                    <ul class="max-h-80 overflow-y-auto divide-y divide-neutral-800 custom-scrollbar">
                        <template x-for="item in searchResults" :key="item.id">
                            <li class="p-3 hover:bg-neutral-800/90 cursor-pointer flex items-center justify-between transition group"
                                @click="window.location.href = item.url || ('/product/' + item.id)">
                                <div class="flex items-center gap-3 min-w-0 flex-1 pr-2">
                                    <div class="w-12 h-14 bg-neutral-900 rounded overflow-hidden flex-shrink-0 border border-neutral-700">
                                        <img :src="item.image || '{{ asset('frontend/images/no-image.svg') }}'" :alt="item.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs sm:text-sm font-semibold text-gray-100 line-clamp-1 group-hover:text-red-400 transition" x-text="item.name"></p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-xs font-bold text-red-400">৳ <span x-text="Number(item.price).toLocaleString()"></span></span>
                                            <template x-if="item.old_price && item.old_price > item.price">
                                                <span class="text-[10px] text-gray-500 line-through">৳ <span x-text="Number(item.old_price).toLocaleString()"></span></span>
                                            </template>
                                            <template x-if="item.in_stock === false">
                                                <span class="text-[9px] font-bold text-rose-300 bg-rose-950/80 px-1.5 py-0.5 rounded border border-rose-800/60">Out of Stock</span>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <a :href="item.url || ('/product/' + item.id)" 
                                       @click.stop=""
                                       class="text-xs bg-red-600 hover:bg-red-700 text-white font-semibold px-3 py-1 rounded shadow-sm transition">
                                        View
                                    </a>
                                </div>
                            </li>
                        </template>
                    </ul>
                    <div class="p-2.5 bg-[#141414] text-center border-t border-neutral-800">
                        <button type="button" @click="submitSearch()" class="text-xs text-red-400 hover:text-red-300 font-bold uppercase tracking-wider transition">
                            View All Search Results for "<span x-text="searchQuery"></span>" →
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Actions: Currency, Wishlist, Account, Cart Trigger -->
            <div class="flex items-center gap-4 md:gap-5 text-gray-300">
                
                <!-- Currency Toggle -->
                <div class="hidden lg:flex items-center text-xs font-semibold tracking-wider text-gray-300 border-r border-neutral-700 pr-4">
                    <span>EN | ৳ BDT</span>
                </div>

                <!-- User Account / Login Link -->
                <a href="{{ route('login') }}" 
                   class="flex items-center gap-1.5 hover:text-white text-sm transition group" 
                   title="My Account / Sign In">
                    <div class="w-8 h-8 rounded-full bg-neutral-800 group-hover:bg-neutral-700 flex items-center justify-center transition">
                        <i class="fa-regular fa-user text-sm"></i>
                    </div>
                </a>

                <!-- Wishlist Badge -->
                <a href="{{ route('frontend.wishlist') }}" 
                   class="relative flex items-center hover:text-white transition group" 
                   title="My Saved Wishlist">
                    <div class="w-8 h-8 rounded-full bg-neutral-800 group-hover:bg-neutral-700 flex items-center justify-center transition">
                        <i class="fa-regular fa-heart text-sm"></i>
                    </div>
                    <span class="absolute -top-1.5 -right-1.5 bg-red-600 text-white text-[10px] font-bold h-4 w-4 rounded-full flex items-center justify-center border-2 border-[#121212]" 
                          x-text="$store.wishlist.count">0</span>
                </a>

                <!-- Mini Cart Trigger Button -->
                <button @click="cartOpen = true" 
                        type="button" 
                        class="relative flex items-center gap-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white px-3.5 py-2 rounded-full shadow-md transition transform active:scale-95" 
                        title="Shopping Bag">
                    <i class="fa-solid fa-bag-shopping text-base"></i>
                    <span class="hidden md:inline text-xs font-bold uppercase tracking-wider">Bag</span>
                    <span class="bg-black text-white text-[11px] font-extrabold px-1.5 py-0.5 rounded-full min-w-[20px] text-center" 
                          x-text="$store.cart.count">0</span>
                </button>
            </div>
        </div>

        <!-- Mobile Search Input (Visible on small screens) -->
        <div class="pb-3 sm:hidden relative" x-data="{ focusedMobile: false }">
            <div class="relative w-full">
                <input type="text" 
                       placeholder="Search products..." 
                       x-model="searchQuery"
                       @input.debounce.300ms="performSearch()"
                       @keydown.enter="submitSearch()"
                       @focus="focusedMobile = true; searchOpen = true"
                       @click.away="focusedMobile = false"
                       class="w-full bg-[#1e1e1e] text-xs text-gray-200 placeholder-gray-400 pl-4 pr-10 py-2.5 rounded-full border border-neutral-700 focus:outline-none">
                <button @click="submitSearch()" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white transition" type="button" aria-label="Search">
                    <template x-if="!searchLoading">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </template>
                    <template x-if="searchLoading">
                        <i class="fa-solid fa-circle-notch fa-spin text-xs text-red-500"></i>
                    </template>
                </button>
            </div>

            <!-- Mobile Search Results Dropdown -->
            <div x-show="focusedMobile && searchResults.length > 0" 
                 x-transition 
                 class="absolute left-0 right-0 top-full mt-1.5 bg-[#1a1a1a] border border-neutral-700 rounded-xl shadow-2xl z-50 overflow-hidden"
                 style="display: none;">
                <div class="p-2 border-b border-neutral-800 text-[10px] font-semibold text-neutral-400 uppercase tracking-wider px-3 flex items-center justify-between">
                    <span>Products (<span x-text="searchResults.length"></span>)</span>
                    <span class="text-[9px] text-neutral-500">Tap to view</span>
                </div>
                <ul class="max-h-64 overflow-y-auto divide-y divide-neutral-800 custom-scrollbar">
                    <template x-for="item in searchResults" :key="item.id">
                        <li class="p-2.5 hover:bg-neutral-800/90 cursor-pointer flex items-center justify-between transition"
                            @click="window.location.href = item.url || ('/product/' + item.id)">
                            <div class="flex items-center gap-2.5 min-w-0 flex-1 pr-2">
                                <div class="w-10 h-12 bg-neutral-900 rounded overflow-hidden flex-shrink-0 border border-neutral-700">
                                    <img :src="item.image || '{{ asset('frontend/images/no-image.svg') }}'" :alt="item.name" class="w-full h-full object-cover">
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold text-gray-100 line-clamp-1" x-text="item.name"></p>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="text-xs font-bold text-red-400">৳ <span x-text="Number(item.price).toLocaleString()"></span></span>
                                        <template x-if="item.old_price && item.old_price > item.price">
                                            <span class="text-[10px] text-gray-500 line-through">৳ <span x-text="Number(item.old_price).toLocaleString()"></span></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <span class="text-[11px] text-red-400 font-bold shrink-0">View →</span>
                        </li>
                    </template>
                </ul>
                <div class="p-2 bg-[#141414] text-center border-t border-neutral-800">
                    <button type="button" @click="submitSearch()" class="text-xs text-red-400 font-bold uppercase tracking-wider">
                        All results for "<span x-text="searchQuery"></span>" →
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Mega Navigation Menu Bar -->
    <nav class="border-t border-b border-gray-200 bg-white text-gray-900 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 overflow-x-auto hide-scrollbar">
            <ul class="flex items-center justify-between md:justify-center md:gap-8 text-xs md:text-[13px] font-bold whitespace-nowrap py-3 uppercase tracking-wider text-gray-900">
                @if(isset($navCategories) && $navCategories->isNotEmpty())
                    @foreach($navCategories as $cat)
                        @php
                            $slug = \Illuminate\Support\Str::slug($cat->name);
                            $isOffer = str_contains(strtolower($cat->name), 'offer');
                        @endphp
                        <li>
                            @if($isOffer)
                                <a href="{{ route('category.products', 'offer') }}" class="text-red-600 font-extrabold hover:text-red-700 hover:bg-red-100 transition px-3 py-1 bg-red-50 rounded border border-red-200 flex items-center gap-1.5 shadow-sm">
                                    <i class="fa-solid fa-fire text-xs text-red-600 animate-pulse"></i>
                                    <span>{{ $cat->name }}</span>
                                </a>
                            @else
                                <a href="{{ route('category.products', $slug) }}" class="nav-link text-gray-900 hover:text-red-600 transition px-2 py-1">
                                    {{ $cat->name }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                @else
                    <li><a href="{{ route('products.index') }}" class="nav-link text-gray-900 hover:text-red-600 transition px-2 py-1">ALL PRODUCTS</a></li>
                @endif
            </ul>
        </div>
    </nav>
</header>
