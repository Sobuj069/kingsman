@extends('frontend.layouts.master')

@section('title', ($categoryConfig['title'] ?? 'Collections') . ' | ' . ($comName ?? 'Kingsman') . ' Official Online Store')
@section('meta_description', $categoryConfig['subtitle'] ?? 'Browse our latest bespoke collections with complimentary delivery across Bangladesh.')

@push('styles')
<style>
    .font-outfit { font-family: 'Outfit', sans-serif; }
    .bg-deep-noir { background-color: #0B0B0C; }
    .text-gold-metallic { color: #C5A880; }
    .bg-gold-metallic { background-color: #C5A880; }
</style>
@endpush

@section('content')
<div class="flex flex-col w-full bg-white font-sans text-[#1a1b22]" 
     x-data="{ 
        activeCategory: '{{ $activeSlug }}', 
        activeSub: '{{ $activeSub }}', 
        activeChild: '{{ $activeChild ?? 'all' }}',
        sortBy: '{{ $sortBy }}',
        changeSort(newSort) {
            let url = new URL(window.location.href);
            url.searchParams.set('sort', newSort);
            window.location.href = url.toString();
        }
     }">

    <!-- 1. TOP SUB-CATEGORY FILTER TABS (Matching User Reference Image) -->
    @if(!empty($categoryConfig['subcategories']) && count($categoryConfig['subcategories']) > 1)
    <section class="w-full bg-white border-b border-gray-200 sticky top-16 md:top-20 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5">
            <div class="flex items-center gap-2 overflow-x-auto hide-scrollbar py-1">
                @foreach($categoryConfig['subcategories'] as $sub)
                    @php
                        $isActive = ($activeSub === $sub['slug']) || ($activeSub === 'all' && $sub['slug'] === 'all');
                        $subUrl = ($sub['slug'] === 'all') 
                            ? route('category.products', $activeSlug) 
                            : route('category.products', ['slug' => $activeSlug, 'sub' => $sub['slug']]);
                    @endphp
                    <a href="{{ $subUrl }}" 
                       class="px-4 py-1.5 text-xs font-semibold uppercase tracking-wider rounded-sm transition whitespace-nowrap border {{ $isActive ? 'bg-black text-white border-black shadow-sm' : 'bg-gray-100 text-gray-700 border-gray-200 hover:bg-gray-200 hover:text-black' }}">
                        {{ $sub['name'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- 1.1 CHILD-CATEGORY FILTER PILLS -->
    @if(!empty($categoryConfig['child_categories']) && count($categoryConfig['child_categories']) > 1)
    <section class="w-full bg-neutral-50/90 backdrop-blur-sm border-b border-gray-200 sticky top-[5.75rem] md:top-[6.5rem] z-20 shadow-2xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
            <div class="flex items-center gap-2 overflow-x-auto hide-scrollbar py-0.5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mr-1 flex items-center gap-1 shrink-0">
                    <i class="fa-solid fa-filter text-[9px]"></i> Filter:
                </span>
                @foreach($categoryConfig['child_categories'] as $child)
                    @php
                        $isChildActive = ($activeChild === $child['slug']) || ($activeChild === 'all' && $child['slug'] === 'all');
                        $childUrl = ($child['slug'] === 'all') 
                            ? route('category.products', ['slug' => $activeSlug, 'sub' => $activeSub]) 
                            : route('category.products', ['slug' => $activeSlug, 'sub' => $activeSub, 'child' => $child['slug']]);
                    @endphp
                    <a href="{{ $childUrl }}" 
                       class="px-3 py-1 text-xs font-medium rounded-full transition whitespace-nowrap border {{ $isChildActive ? 'bg-[#1a1b22] text-white border-[#1a1b22] shadow-xs' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100 hover:text-black' }}">
                        {{ $child['name'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- 2. MAIN CATEGORY PROMOTIONAL BANNER -->
    @if(!empty($categoryConfig['banner']))
    <section class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 sm:pt-6">
        <div class="w-full rounded-xl overflow-hidden shadow-sm border border-neutral-200 bg-neutral-900">
            <img src="{{ $categoryConfig['banner'] }}" 
                 alt="{{ $categoryConfig['name'] }} Collection Banner" 
                 class="w-full h-auto block"
                 fetchpriority="high"
                 loading="eager"
                 decoding="async">
        </div>
    </section>
    @endif

    <!-- 3. BREADCRUMB & TOOLBAR STRIP -->
    <section class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-4">
        <div class="flex flex-wrap items-center justify-between gap-4 pb-3 border-b border-gray-200 text-xs text-gray-500">
            <!-- Breadcrumbs -->
            <nav class="flex items-center gap-2 uppercase tracking-wider font-semibold">
                <a href="{{ route('home') }}" class="hover:text-black transition flex items-center gap-1">
                    <i class="fa-solid fa-house text-xs"></i>
                    <span class="hidden sm:inline">Home</span>
                </a>
                <span>/</span>
                @if($activeSlug !== 'all')
                    <a href="{{ route('products.index') }}" class="hover:text-black transition">Collections</a>
                    <span>/</span>
                @endif
                <span class="text-black font-extrabold">{{ $categoryConfig['name'] }}</span>
                <span class="text-gray-400 font-normal">({{ count($products) }} items)</span>
            </nav>

            <!-- Sort By Dropdown -->
            <div class="flex items-center gap-2">
                <label for="sort-select" class="font-bold uppercase tracking-wider text-[11px] text-gray-500">Sort By:</label>
                <select id="sort-select" 
                        @change="changeSort($event.target.value)" 
                        class="bg-[#F5F5F7] border border-gray-300 text-xs text-gray-900 font-medium py-1.5 px-3 rounded focus:ring-black focus:border-black">
                    <option value="default" {{ $sortBy === 'default' ? 'selected' : '' }}>Featured</option>
                    <option value="price_low_high" {{ $sortBy === 'price_low_high' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_high_low" {{ $sortBy === 'price_high_low' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="discount_high" {{ $sortBy === 'discount_high' ? 'selected' : '' }}>Highest Discount</option>
                </select>
            </div>
        </div>
    </section>

    <!-- 4. MAIN 4-COLUMN PRODUCTS GRID (Exact match to User reference image) -->
    <section class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-16">
        @if(count($products) > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 gap-3.5 sm:gap-5 md:gap-6">
                @foreach($products as $product)
                    @include('frontend.partials.product-card', [
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'old_price' => $product['old_price'] ?? null,
                        'image' => $product['image'],
                        'category' => $product['category'] ?? 'Apparel',
                        'tag' => $product['tag'] ?? 'Kingsman',
                        'stock' => $product['stock'] ?? null,
                        'in_stock' => $product['in_stock'] ?? true,
                        'sizes' => $product['sizes'] ?? [],
                        'colors' => $product['colors'] ?? [],
                        'size_stocks' => $product['size_stocks'] ?? null,
                        'variation_stocks' => $product['variation_stocks'] ?? null
                    ])
                @endforeach
            </div>
        @else
            <!-- No Products State -->
            <div class="text-center py-20 px-4 bg-[#F5F5F7] border border-gray-200 rounded my-6 flex flex-col items-center justify-center">
                <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center text-gray-400 mb-4 shadow-xs">
                    <i class="fa-solid fa-magnifying-glass text-2xl"></i>
                </div>
                <h3 class="font-outfit text-xl font-bold text-black uppercase tracking-tight">No Products Found</h3>
                <p class="text-xs text-gray-500 max-w-md mt-1 mb-6">We couldn't find any garments matching your current filter in this collection.</p>
                <a href="{{ route('products.index') }}" 
                   class="px-6 py-2.5 bg-black hover:bg-neutral-800 text-white text-xs font-bold uppercase tracking-wider rounded transition">
                    View All Collections
                </a>
            </div>
        @endif
    </section>

</div>
@endsection
