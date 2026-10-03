@extends('frontend.layouts.master')

@section('title', 'My Saved Wishlist | ' . ($comName ?? 'Kingsman') . ' Official Online Store')
@section('meta_description', 'View and manage your saved luxury ethnic garments, royal Kabli sets, and bespoke menswear in your ' . ($comName ?? 'Kingsman') . ' Wishlist.')

@push('styles')
<style>
    .font-outfit { font-family: 'Outfit', sans-serif; }
    .bg-deep-noir { background-color: #0B0B0C; }
    .text-gold-metallic { color: #C5A880; }
    .bg-gold-metallic { background-color: #C5A880; }
    .bg-gold-light { background-color: #EFE6DB; }
</style>
@endpush

@section('content')
<div class="flex flex-col w-full bg-white font-sans text-[#1a1b22]">

    <!-- 1. BREADCRUMB RAIL -->
    <section class="w-full bg-[#F5F5F7] border-b border-gray-200/80 py-3.5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between text-xs uppercase tracking-wider text-gray-500 font-medium">
            <nav class="flex items-center gap-2">
                <a href="{{ route('home') }}" class="hover:text-black transition-colors">Home</a>
                <span class="text-gray-300">/</span>
                <span class="text-black font-bold">My Saved Wishlist (<span x-text="$store.wishlist.count"></span>)</span>
            </nav>
            <div class="hidden md:flex items-center gap-2 text-xs text-[#725b38]">
                <span class="material-symbols-outlined text-sm text-[#C5A880]">favorite</span>
                <span>Personal Sartorial Wardrobe</span>
            </div>
        </div>
    </section>

    <!-- 2. MAIN WISHLIST CONTENT -->
    <section class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
        
        <!-- Empty State When No Items Saved -->
        <template x-if="$store.wishlist.count === 0">
            <div class="text-center py-20 px-4 bg-[#F5F5F7] border border-gray-200/80 flex flex-col items-center justify-center rounded-sm">
                <div class="w-20 h-20 rounded-full bg-white flex items-center justify-center text-red-500 mb-4 shadow-sm">
                    <i class="fa-regular fa-heart text-3xl"></i>
                </div>
                <span class="text-xs font-bold uppercase tracking-widest text-[#C5A880] mb-1">Your Personal Wishlist</span>
                <h2 class="font-outfit text-2xl md:text-3xl font-bold text-black uppercase tracking-tight">Your Wishlist is Empty</h2>
                <p class="text-xs text-gray-500 max-w-md mt-2 mb-8 leading-relaxed">
                    Explore our latest royal Kabli sets, Egyptian Giza cotton Panjabis, and tailored menswear. Click the heart icon on any outfit to save it here.
                </p>
                <div class="flex items-center gap-3 flex-wrap justify-center">
                    <a href="{{ route('products.index') }}" 
                       class="px-8 py-3.5 bg-black hover:bg-neutral-800 text-white text-xs font-bold uppercase tracking-widest transition shadow-md">
                        Explore Collections
                    </a>
                    <a href="{{ route('category.products', 'kabli-set') }}" 
                       class="px-6 py-3.5 bg-white border border-gray-300 hover:border-black text-black text-xs font-bold uppercase tracking-widest transition">
                        Royal Kabli Sets
                    </a>
                </div>
            </div>
        </template>

        <!-- Wishlist Grid When Items Exist -->
        <template x-if="$store.wishlist.count > 0">
            <div class="flex flex-col gap-8">
                
                <!-- Action & Status Header Bar -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-200">
                    <div>
                        <h1 class="font-outfit text-2xl md:text-3xl font-black uppercase tracking-tight text-black">
                            Saved Garments (<span x-text="$store.wishlist.count"></span>)
                        </h1>
                        <p class="text-xs text-gray-500 mt-0.5">Review, purchase or transfer your saved items directly to your shopping bag.</p>
                    </div>

                    <div class="flex items-center gap-3 flex-wrap">
                        <button type="button" 
                                @click="$store.wishlist.moveAllToBag()" 
                                class="px-5 py-2.5 bg-black hover:bg-neutral-800 text-white text-xs font-bold uppercase tracking-wider transition flex items-center gap-2 shadow-sm active:scale-95 cursor-pointer">
                            <i class="fa-solid fa-bag-shopping text-xs"></i>
                            <span>Move All To Bag</span>
                        </button>
                        <button type="button" 
                                @click="$store.wishlist.clear()" 
                                class="px-4 py-2.5 bg-[#F5F5F7] hover:bg-red-50 text-gray-700 hover:text-red-600 border border-gray-300 hover:border-red-300 text-xs font-bold uppercase tracking-wider transition flex items-center gap-1.5 cursor-pointer">
                            <i class="fa-regular fa-trash-can text-xs"></i>
                            <span>Clear Wishlist</span>
                        </button>
                    </div>
                </div>

                <!-- Products Grid (2 cols mobile, 4 cols desktop) -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3.5 sm:gap-5 md:gap-6">
                    <template x-for="(item, index) in $store.wishlist.items" :key="item.id + '_' + index">
                        <div class="product-card group relative border border-gray-200 rounded-sm bg-white overflow-hidden flex flex-col justify-between transition-all duration-300 hover:shadow-lg">
                            
                            <!-- Thumbnail & Actions -->
                            <div class="relative aspect-[3/4] bg-neutral-100 overflow-hidden">
                                <a :href="'/product/' + (item.id || 1)" class="block w-full h-full">
                                    <!-- Discount Badge if available -->
                                    <template x-if="item.old_price && item.old_price > item.price">
                                        <span class="absolute bottom-2.5 left-2.5 z-10 text-[9px] sm:text-[10px] font-extrabold bg-red-600 text-white px-1.5 sm:px-2 py-0.5 rounded shadow-sm"
                                              x-text="Math.round(((item.old_price - item.price) / item.old_price) * 100) + '% OFF'"></span>
                                    </template>

                                    <!-- Product Image -->
                                    <img :src="item.image || '{{ asset('frontend/images/no-image.svg') }}'" 
                                         :alt="item.name" 
                                         class="product-img w-full h-full object-cover">
                                </a>

                                <!-- Remove From Wishlist Button -->
                                <button type="button" 
                                        @click.stop="$store.wishlist.remove(item.id)" 
                                        class="absolute top-2.5 right-2.5 z-20 w-7 h-7 sm:w-8 sm:h-8 rounded-full shadow flex items-center justify-center transition-all duration-200 text-red-600 bg-red-50 hover:bg-red-600 hover:text-white cursor-pointer" 
                                        title="Remove from wishlist">
                                    <i class="fa-solid fa-heart text-[11px] sm:text-xs"></i>
                                </button>
                            </div>

                            <!-- Content Info & Action Triggers -->
                            <div class="p-2.5 sm:p-3.5 flex flex-col flex-1 justify-between bg-white">
                                <div>
                                    <span class="text-[9px] sm:text-[10px] uppercase font-semibold text-gray-400 tracking-wider block mb-1" 
                                          x-text="item.category || 'Luxury Menswear'"></span>
                                    <h3 class="text-xs sm:text-sm font-medium text-gray-800 line-clamp-2 hover:text-red-600 transition leading-snug">
                                        <a :href="'/product/' + (item.id || 1)" x-text="item.name"></a>
                                    </h3>
                                </div>

                                <div class="mt-3 pt-2.5 border-t border-gray-100 flex flex-col gap-2">
                                    <!-- Price Row -->
                                    <div class="flex items-baseline justify-between gap-1 flex-wrap">
                                        <span class="text-xs sm:text-sm font-bold text-gray-900" x-text="'TK ' + Number(item.price).toLocaleString()"></span>
                                        <template x-if="item.old_price && item.old_price > item.price">
                                            <span class="text-[10px] sm:text-xs text-gray-400 line-through font-medium" x-text="'TK ' + Number(item.old_price).toLocaleString()"></span>
                                        </template>
                                    </div>

                                    <!-- Move to Cart Button -->
                                    <template x-if="item.in_stock !== false && (item.stock === undefined || Number(item.stock) > 0)">
                                        <button type="button" 
                                                @click.stop="$store.wishlist.moveToBag(item)" 
                                                class="w-full py-2 bg-neutral-900 hover:bg-black text-white text-[11px] sm:text-xs font-bold uppercase tracking-wider rounded transition flex items-center justify-center gap-1.5 shadow-sm active:scale-98 cursor-pointer">
                                            <i class="fa-solid fa-bag-shopping text-[11px]"></i>
                                            <span>Move to Bag</span>
                                        </button>
                                    </template>
                                    <template x-if="item.in_stock === false || (item.stock !== undefined && Number(item.stock) <= 0)">
                                        <button type="button" 
                                                disabled 
                                                class="w-full py-2 bg-neutral-200 text-gray-400 text-[11px] sm:text-xs font-bold uppercase tracking-wider rounded cursor-not-allowed flex items-center justify-center gap-1.5">
                                            <i class="fa-solid fa-ban text-[11px]"></i>
                                            <span>Out of Stock</span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Bottom Back Link -->
                <div class="pt-6 text-center">
                    <a href="{{ route('products.index') }}" 
                       class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-black hover:text-[#C5A880] transition">
                        <span class="material-symbols-outlined text-base">arrow_back</span>
                        <span>Continue Browsing Collections</span>
                    </a>
                </div>

            </div>
        </template>

    </section>

    <!-- 3. CURATED RECOMMENDATIONS -->
    @if(!empty($pairings) && count($pairings) > 0)
    <section class="w-full bg-[#F5F5F7] py-14 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                <div>
                    <span class="text-xs font-bold text-[#C5A880] uppercase tracking-widest block mb-1">Recommended By Lead Stylist</span>
                    <h3 class="font-outfit text-2xl font-bold uppercase tracking-tight text-black">You May Also Admire</h3>
                </div>
                <a href="{{ route('products.index') }}" class="text-xs font-bold uppercase tracking-wider text-black hover:underline">
                    View Complete Catalog →
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                @foreach($pairings as $pair)
                    <div class="bg-white p-3 sm:p-4 border border-gray-200/80 flex flex-col justify-between group shadow-sm hover:shadow-md transition">
                        <div>
                            <div class="aspect-[3/4] bg-neutral-100 overflow-hidden mb-3">
                                <img src="{{ $pair['image'] }}" alt="{{ $pair['name'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                            <span class="text-[10px] uppercase font-bold text-gray-400 block">{{ $pair['category'] }}</span>
                            <h4 class="text-xs sm:text-sm font-bold text-black line-clamp-1 mt-0.5">{{ $pair['name'] }}</h4>
                        </div>
                        <div class="flex items-center justify-between mt-3 pt-2 border-t border-gray-200">
                            <span class="font-outfit text-xs sm:text-sm font-bold text-black">৳{{ number_format($pair['price']) }}</span>
                            <button type="button" 
                                    @click="$store.cart.addItem({ id: {{ $pair['id'] }}, name: '{{ addslashes($pair['name']) }}', price: {{ $pair['price'] }}, image: '{{ $pair['image'] }}', size: 'Standard', color: 'Default' }); $store.cart.toggle(true)"
                                    class="w-7 h-7 bg-neutral-100 hover:bg-black text-black hover:text-white flex items-center justify-center transition shadow-sm rounded-full"
                                    title="Add to Sartorial Bag">
                                <i class="fa-solid fa-plus text-xs"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

</div>
@endsection
