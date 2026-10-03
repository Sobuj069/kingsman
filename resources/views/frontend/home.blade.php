@extends('frontend.layouts.master')

@section('title', ($comName ?? 'Kingsman') . ' - Official Online Store | Luxury Ethnic & Men\'s Fashion Bangladesh')

@section('content')
<!-- ==========================================
     1. HERO FESTIVE EID & ROYAL ARRIVALS BANNER
=========================================== -->
@php
    $heroSlides = (isset($heroBanners) && $heroBanners->isNotEmpty()) ? $heroBanners : collect([]);
@endphp
@if($heroSlides->isNotEmpty())
<section class="relative w-full bg-stone-950 overflow-hidden" data-purpose="eid-hero-banner" 
         x-data="{ currentSlide: 0, slidesCount: {{ count($heroSlides) }} }" 
         x-init="if(slidesCount > 1) { setInterval(() => { currentSlide = (currentSlide + 1) % slidesCount }, 6000) }">
    
    <div class="grid grid-cols-1 w-full">
        @foreach($heroSlides as $index => $slide)
            <div x-show="currentSlide === {{ $index }}" 
                 x-transition:enter="transition ease-out duration-700"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-500"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="col-start-1 row-start-1 w-full"
                 @if($index > 0) style="display: none;" @endif>
                
                @if(!empty($slide->link))
                    <a href="{{ $slide->link }}" class="w-full block">
                        <img src="{{ $slide->image_url }}" 
                             alt="{{ $slide->title ?? (($comName ?? 'Kingsman') . ' Store Banner') }}" 
                             fetchpriority="{{ $index === 0 ? 'high' : 'auto' }}"
                             loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                             decoding="async"
                             class="w-full h-auto block">
                    </a>
                @else
                    <img src="{{ $slide->image_url }}" 
                         alt="{{ $slide->title ?? (($comName ?? 'Kingsman') . ' Store Banner') }}" 
                         fetchpriority="{{ $index === 0 ? 'high' : 'auto' }}"
                         loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                         decoding="async"
                         class="w-full h-auto block">
                @endif
            </div>
        @endforeach
    </div>

    <!-- Slide Indicators -->
    @if(count($heroSlides) > 1)
        <div class="absolute bottom-3 sm:bottom-5 left-1/2 -translate-x-1/2 z-20 flex items-center gap-2">
            @foreach($heroSlides as $index => $slide)
                <button @click="currentSlide = {{ $index }}" 
                        :class="currentSlide === {{ $index }} ? 'w-8 bg-white' : 'w-2 bg-white/50'" 
                        class="h-1.5 rounded-full transition-all duration-300 shadow" 
                        aria-label="Slide {{ $index + 1 }}"></button>
            @endforeach
        </div>
    @endif
</section>
@endif


<!-- ==========================================
     2. OUR CATEGORIES CAROUSEL
=========================================== -->
@php
    $hasCategories = isset($categories) && (is_array($categories) ? count($categories) > 0 : $categories->isNotEmpty());
    $hasCatSections = isset($categorySections) && (is_array($categorySections) ? count($categorySections) > 0 : $categorySections->isNotEmpty());
    $carouselCategories = $hasCategories 
        ? (is_array($categories) ? collect($categories) : $categories) 
        : ($hasCatSections ? (is_array($categorySections) ? collect($categorySections) : $categorySections) : collect());
@endphp
@if($carouselCategories->isNotEmpty())
<section class="py-12 bg-white border-b border-gray-100" data-purpose="quick-category-navigation" 
         x-data="{
             autoSlideTimer: null,
             isHovered: false,
             scrollStep: 280,
             
             init() {
                 this.startAutoSlide();
             },
             
             startAutoSlide() {
                 this.autoSlideTimer = setInterval(() => {
                     if (!this.isHovered) {
                         const el = this.$refs.catScroll;
                         if (el) {
                             if (el.scrollLeft + el.clientWidth >= el.scrollWidth - 25) {
                                 el.scrollTo({ left: 0, behavior: 'smooth' });
                             } else {
                                 el.scrollBy({ left: this.scrollStep, behavior: 'smooth' });
                             }
                         }
                     }
                 }, 2800);
             },
             
             scrollContainer(direction) {
                 const el = this.$refs.catScroll;
                 if (el) {
                     el.scrollBy({ left: direction * this.scrollStep, behavior: 'smooth' });
                 }
             }
         }"
         @mouseenter="isHovered = true"
         @mouseleave="isHovered = false"
         @touchstart="isHovered = true"
         @touchend="setTimeout(() => { isHovered = false }, 2000)">
    <div class="max-w-7xl mx-auto px-4">
        
        <!-- Centered Section Title with Underline -->
        <div class="relative text-center mb-10">
            <div class="inline-block relative pb-2.5 border-b-2 border-black z-10">
                <h2 class="font-bold text-xl md:text-2xl text-gray-900 tracking-wide">
                    Our Categories
                </h2>
            </div>
            <!-- Background Full Width Line -->
            <div class="absolute inset-x-0 bottom-0 border-b border-gray-200 z-0"></div>
        </div>

        <!-- Carousel Container with Navigation Arrows -->
        <div class="relative group/carousel">
            
            <!-- Floating Left Arrow -->
            <button @click="scrollContainer(-1)" 
                    aria-label="Previous Category" 
                    class="absolute -left-3 md:-left-4 top-1/2 -translate-y-1/2 z-20 w-9 h-9 md:w-10 md:h-10 rounded-full bg-neutral-900 text-white flex items-center justify-center shadow-xl hover:bg-black hover:scale-105 transition border border-neutral-700">
                <i class="fa-solid fa-chevron-left text-xs"></i>
            </button>

            <!-- Categories Card Row -->
            <div x-ref="catScroll" class="w-full flex items-center overflow-x-auto gap-4 md:gap-5 py-3 px-1 hide-scrollbar scroll-smooth">
                @foreach($carouselCategories as $cat)
                    @php
                        $catImg = $cat->image_url ?? $cat->image ?? null;
                        $catSlug = $cat->slug ?? \Illuminate\Support\Str::slug($cat->name ?? 'cat');
                    @endphp
                    <a href="#category-section-{{ $cat->id }}" class="flex-shrink-0 w-44 sm:w-52 md:w-60 h-44 sm:h-52 rounded-md overflow-hidden relative border border-gray-200/90 shadow-sm group bg-gradient-to-b from-[#e5e7eb] via-[#f1f3f5] to-[#d8dce2] transition duration-300 hover:shadow-md">
                        <div class="w-full h-full flex flex-col items-center justify-start pt-3">
                            @if($catImg)
                                <img src="{{ $catImg }}" 
                                     alt="{{ $cat->name }}" 
                                     class="h-[72%] w-auto object-contain drop-shadow-lg group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="h-[72%] w-full flex items-center justify-center text-gray-400">
                                    <i class="fa-solid fa-shirt text-4xl"></i>
                                </div>
                            @endif
                        </div>
                        <!-- Bottom Floating Badge -->
                        <div class="absolute bottom-3 inset-x-0 flex justify-center px-3">
                            <span class="bg-white/95 text-gray-900 font-bold uppercase text-[11px] sm:text-xs tracking-wider py-1.5 px-4 rounded shadow-md group-hover:bg-red-600 group-hover:text-white transition-colors duration-200 text-center whitespace-nowrap border border-gray-100 truncate max-w-full">
                                {{ $cat->name }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Floating Right Arrow -->
            <button @click="scrollContainer(1)" 
                    aria-label="Next Category" 
                    class="absolute -right-3 md:-right-4 top-1/2 -translate-y-1/2 z-20 w-9 h-9 md:w-10 md:h-10 rounded-full bg-neutral-900 text-white flex items-center justify-center shadow-xl hover:bg-black hover:scale-105 transition border border-neutral-700">
                <i class="fa-solid fa-chevron-right text-xs"></i>
            </button>
        </div>
    </div>
</section>
@endif


<!-- ==========================================
     3. NEW ARRIVALS SECTION
=========================================== -->
<section class="py-14 bg-white" data-purpose="new-arrival-products">
    <div class="max-w-7xl mx-auto px-4">
        
        <!-- Centered Section Header -->
        <div class="text-center max-w-xl mx-auto mb-10">
            <span class="text-xs font-bold text-red-600 uppercase tracking-widest block mb-1">Fresh In Store</span>
            <h2 class="text-2xl md:text-3xl font-black uppercase tracking-wide text-gray-900">
                New Arrival Products
            </h2>
            <div class="w-12 h-0.5 bg-red-600 mx-auto mt-2.5"></div>
        </div>

@php
if (!function_exists('renderRobeCard')) {
    function renderRobeCard($id, $name = '', $price = 0, $old_price = 0, $image = '', $category = 'Apparel', $tag = 'Kingsman') {
        $stock = 10;
        $inStock = true;
        if ($id instanceof \App\Models\Product) {
            $prod = $id;
            $id = $prod->id;
            $name = $prod->name;
            $directStock = (float) \App\Models\PurchaseItem::where('product_id', $prod->id)->sum('stock_qty');
            $stock = max($directStock, (float)($prod->main_qty ?? 0));
            $inStock = $stock > 0;
            $price = (float)($prod->selling_price ?? 0);
            $disSelling = (float)($prod->dis_selling_price ?? 0);
            $discount = (float)($prod->discount ?? 0);
            
            if ($disSelling > $price) {
                $old_price = $disSelling;
            } elseif ($discount > 0) {
                $old_price = $price + $discount;
            } else {
                $old_price = 0;
            }

            $image = $prod->image_url;
            $category = optional($prod->category)->name ?? 'Apparel';
            $tag = optional($prod->brand)->name ?? 'Kingsman';

            $sizes = [];
            $sizeStocks = [];
            $varStockMap = [];

            if ($prod->relationLoaded('product_variations') || $prod->product_variations) {
                $sizes = $prod->product_variations->map(fn($v) => optional($v->size)->size)->filter()->unique()->values()->all();
                foreach ($prod->product_variations as $v) {
                    $sName = optional($v->size)->size ?? 'N/A';
                    $cName = optional($v->color)->color ?? 'Default';
                    $pStock = (float) \App\Models\PurchaseItem::where('product_variation_id', $v->id)->sum('stock_qty');
                    if (!isset($varStockMap[$cName])) {
                        $varStockMap[$cName] = [];
                    }
                    $varStockMap[$cName][$sName] = (int)$pStock;
                    $sizeStocks[$sName] = ($sizeStocks[$sName] ?? 0) + (int)$pStock;
                }
                if ($prod->product_variations->isNotEmpty()) {
                    $varStockTotal = array_sum($sizeStocks);
                    $stock = max($varStockTotal, $directStock, (float)($prod->main_qty ?? 0));
                    $inStock = $stock > 0;
                }
            }
            if (empty($sizes) && $prod->sizes) {
                $sizes = $prod->sizes->pluck('size')->filter()->unique()->values()->all();
            }
            if (empty($sizes)) {
                $sizes = [];
            }

            $colors = [];
            if ($prod->relationLoaded('product_variations') || $prod->product_variations) {
                $colors = $prod->product_variations->map(fn($v) => optional($v->color)->color)->filter()->unique()->values()->all();
            }
            if (empty($colors) && $prod->colors) {
                $colors = $prod->colors->pluck('color')->filter()->unique()->values()->all();
            }
            if (empty($colors)) {
                $colors = [];
            }
        } else {
            $sizes = [];
            $colors = [];
            $sizeStocks = [];
            $varStockMap = [];
        }

        $calcDiscount = ($old_price && $old_price > $price) ? round((($old_price - $price) / $old_price) * 100) : null;
        $productJson = htmlspecialchars(json_encode([
            'id' => $id,
            'name' => $name,
            'price' => (float)$price,
            'old_price' => (float)$old_price,
            'image' => $image,
            'category' => $category,
            'stock' => $stock,
            'in_stock' => $inStock,
            'sizes' => $sizes,
            'colors' => $colors,
            'size_stocks' => !empty($sizeStocks) ? $sizeStocks : null,
            'variation_stocks' => !empty($varStockMap) ? $varStockMap : null
        ]), ENT_QUOTES, 'UTF-8');
        $detailsUrl = route('product.details', $id);
        $formattedPrice = number_format($price);
        $formattedOldPrice = $old_price ? number_format($old_price) : '';
        $discBadge = $calcDiscount ? '<span class="absolute bottom-2.5 left-2.5 z-10 text-[9px] sm:text-[10px] font-extrabold bg-red-600 text-white px-1.5 sm:px-2 py-0.5 rounded shadow-sm">'.$calcDiscount.'% OFF</span>' : '';
        $outOfStockBadge = !$inStock ? '<span class="absolute top-2.5 right-12 z-10 text-[9px] sm:text-[10px] font-bold bg-rose-600 text-white px-2 py-0.5 rounded shadow">OUT OF STOCK</span>' : '';
        $oldPriceHtml = ($old_price && $old_price > $price) ? '<span class="text-[10px] sm:text-xs text-red-500 line-through font-medium">TK '.$formattedOldPrice.'</span> <span class="text-[8px] sm:text-[9px] font-extrabold bg-red-600 text-white px-1 sm:px-1.5 py-0.5 rounded uppercase leading-none">OFF '.$calcDiscount.'%</span>' : '';
        $fallbackImg = $image ?: asset('frontend/images/no-image.svg');
        $cartBtnHtml = !$inStock ? '<span class="text-[9px] font-bold text-rose-600 bg-rose-50 border border-rose-200 px-1.5 py-0.5 rounded uppercase shrink-0" title="Out of Stock">Out of Stock</span>' : '';
        
        return '
<div class="product-card group relative border border-gray-200 rounded-sm bg-white overflow-hidden flex flex-col justify-between transition-all duration-300" data-purpose="product-card">
    <div class="relative aspect-[3/4] bg-neutral-100 overflow-hidden cursor-pointer">
        <a href="'.$detailsUrl.'" class="block w-full h-full">
            '.$outOfStockBadge.'
            '.$discBadge.'
            <img src="'.$fallbackImg.'" alt="'.e($name).'" loading="lazy" decoding="async" width="400" height="530" class="product-img w-full h-full object-cover">
        </a>
        <button type="button" @click.stop="$store.wishlist.toggle('.$productJson.')" :class="$store.wishlist.has('.$id.') ? \'text-red-600 bg-red-50\' : \'text-gray-400 hover:text-red-500 bg-white/90 hover:bg-white\'" class="absolute top-2.5 right-2.5 z-20 w-7 h-7 sm:w-8 sm:h-8 rounded-full shadow flex items-center justify-center transition-all duration-200" aria-label="Save to wishlist">
            <i :class="$store.wishlist.has('.$id.') ? \'fa-solid fa-heart\' : \'fa-regular fa-heart\'" class="text-[11px] sm:text-xs"></i>
        </button>

    </div>
    <div class="p-2.5 sm:p-3.5 flex flex-col flex-1 justify-between bg-white">
        <div>
            <span class="text-[9px] sm:text-[10px] uppercase font-semibold text-gray-400 tracking-wider block mb-1">'.$category.'</span>
            <h3 class="text-xs sm:text-sm font-medium text-gray-800 line-clamp-2 hover:text-red-600 transition leading-snug">
                <a href="'.$detailsUrl.'" class="hover:text-red-600">'.e($name).'</a>
            </h3>
        </div>
        <div class="mt-2.5 sm:mt-3 pt-2 border-t border-gray-100 flex items-center justify-between gap-1">
            <div class="flex items-baseline gap-1 sm:gap-1.5 flex-wrap min-w-0">
                <span class="text-xs sm:text-sm font-bold text-gray-900">TK '.$formattedPrice.'</span>
                '.$oldPriceHtml.'
            </div>
            '.$cartBtnHtml.'
        </div>
    </div>
</div>';
    }
}
@endphp

        <!-- 4-Grid Product Cards (Animated on Scroll) -->
@if(isset($newArrivals) && $newArrivals->isNotEmpty())
        <!-- 4-Grid Product Cards (Animated on Scroll) -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 motion-grid">
            @foreach($newArrivals->take(8) as $prod)
                {!! renderRobeCard($prod) !!}
            @endforeach
        </div>

        <!-- Centered View All Action -->
        <div class="text-center mt-8">
            <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-6 py-2.5 border border-neutral-900 text-neutral-900 hover:bg-neutral-900 hover:text-white text-xs font-bold uppercase tracking-widest rounded transition duration-200">
                <span>View All Products</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
@endif

        <!-- Dual Promotional Festive Showcase Banners -->
        @php
            $dualItems = (isset($dualBanners) && $dualBanners->isNotEmpty()) ? $dualBanners : collect([]);
        @endphp
        @if($dualItems->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-10">
            @foreach($dualItems as $dualItem)
                <a href="{{ $dualItem->link ?: '#offer' }}" class="block rounded-lg bg-neutral-900 overflow-hidden shadow-md group cursor-pointer">
                    <img src="{{ $dualItem->image_url }}" 
                         alt="{{ $dualItem->title ?? 'Exclusive Festive Showcase Banner' }}" 
                         loading="lazy"
                         decoding="async"
                         class="w-full h-auto block group-hover:scale-105 transition-transform duration-500">
                </a>
            @endforeach
        </div>
        @endif
    </div>
</section>


<!-- ==========================================
     4. TOP SELLING PRODUCTS SECTION
=========================================== -->
@if(isset($topSelling) && $topSelling->isNotEmpty())
<section class="py-14 bg-gray-50 border-y border-neutral-200" data-purpose="top-selling-products">
    <div class="max-w-7xl mx-auto px-4">
        
        <!-- Centered Section Header -->
        <div class="text-center max-w-xl mx-auto mb-10">
            <span class="text-xs font-bold text-red-600 uppercase tracking-widest block mb-1">Trending Now</span>
            <h2 class="text-2xl md:text-3xl font-black uppercase tracking-wide text-gray-900">
                Top Selling Products
            </h2>
            <div class="w-12 h-0.5 bg-red-600 mx-auto mt-2.5"></div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 motion-grid">
            @foreach($topSelling->take(8) as $prod)
                {!! renderRobeCard($prod) !!}
            @endforeach
        </div>

        <!-- Asymmetrical Festive Punjabi Showcase Banners (Dynamic Grid) -->
        @php
            $festiveItems = (isset($festiveBanners) && $festiveBanners->isNotEmpty()) ? $festiveBanners : collect([]);
            $mainFestive = $festiveItems->first();
            $rightFestive = $festiveItems->slice(1, 2);
        @endphp
        @if($festiveItems->isNotEmpty())
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-5 mt-12 items-stretch">
            @if($mainFestive)
                <!-- Left Large Panoramic Punjabi Banner (8 cols) -->
                <div class="{{ $rightFestive->isNotEmpty() ? 'lg:col-span-8' : 'lg:col-span-12' }} rounded-lg overflow-hidden shadow-md group relative bg-neutral-900 flex">
                    <a href="{{ $mainFestive->link ?: '#kabli-section' }}" class="w-full h-full block">
                        <img src="{{ $mainFestive->image_url }}" 
                             alt="{{ $mainFestive->title ?? 'Festive Punjabi Showcase' }}" 
                             class="w-full h-full object-cover group-hover:scale-[1.015] transition-transform duration-500">
                    </a>
                </div>
            @endif

            @if($rightFestive->isNotEmpty())
                <!-- Right Column with 2 Stacked Punjabi Banners (4 cols) -->
                <div class="lg:col-span-4 flex flex-col justify-between gap-4 sm:gap-5">
                    @foreach($rightFestive as $item)
                        <div class="rounded-lg overflow-hidden shadow-md group relative bg-neutral-900 flex-1">
                            <a href="{{ $item->link ?: '#kabli-section' }}" class="w-full h-full block">
                                <img src="{{ $item->image_url }}" 
                                     alt="{{ $item->title ?? 'Premium Punjabi Celebration' }}" 
                                     class="w-full h-full object-cover group-hover:scale-[1.02] transition-transform duration-500">
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        @endif
    </div>
</section>
@endif


@php
    $catSectionsCollection = isset($categorySections) ? (is_array($categorySections) ? collect($categorySections) : $categorySections) : collect();
@endphp
@if($catSectionsCollection->isNotEmpty())
    @foreach($catSectionsCollection as $catIndex => $category)
        @php
            $catProducts = $category->products;
            $catSlug = $category->slug ?? \Illuminate\Support\Str::slug($category->name);
            $sectionBg = ($catIndex % 2 == 0) ? 'bg-white' : 'bg-gray-50 border-y border-neutral-200';
        @endphp
        @if($catProducts && $catProducts->isNotEmpty())
            <section id="category-section-{{ $category->id }}" class="py-14 {{ $sectionBg }}" data-purpose="category-section-{{ $catSlug }}">
                <div class="max-w-7xl mx-auto px-4">
                    
                    <!-- Centered Section Header -->
                    <div class="text-center max-w-xl mx-auto mb-10">
                        <h2 class="text-2xl md:text-3xl font-black uppercase tracking-wide text-gray-900">
                            {{ $category->name }}
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">Explore our exclusive {{ $category->name }} collection</p>
                        <div class="w-12 h-0.5 bg-neutral-900 mx-auto mt-2.5"></div>
                    </div>

                    <!-- Products Grid -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 motion-grid">
                        @foreach($catProducts->take(8) as $prod)
                            {!! renderRobeCard($prod) !!}
                        @endforeach
                    </div>

                    <!-- Centered View All Action -->
                    <div class="text-center mt-8">
                        <a href="{{ route('category.products', $catSlug) }}" class="inline-flex items-center gap-2 px-6 py-2.5 border border-neutral-900 text-neutral-900 hover:bg-neutral-900 hover:text-white text-xs font-bold uppercase tracking-widest rounded transition duration-200">
                            <span>{{ __('View All') }} {{ $category->name }}</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </section>
        @endif
    @endforeach
@endif

@php
    $hasAnyProducts = (isset($newArrivals) && $newArrivals->isNotEmpty()) 
        || (isset($topSelling) && $topSelling->isNotEmpty()) 
        || (isset($catSectionsCollection) && $catSectionsCollection->isNotEmpty());
@endphp
@if(!$hasAnyProducts)
<section class="py-20 bg-white text-center border-b border-gray-100">
    <div class="max-w-md mx-auto px-4">
        <div class="w-16 h-16 rounded-full bg-neutral-100 flex items-center justify-center text-neutral-400 mx-auto mb-4">
            <i class="fa-solid fa-shirt text-2xl"></i>
        </div>
        <h3 class="font-bold text-lg text-gray-900 uppercase tracking-wide">No Products Found</h3>
        <p class="text-xs text-gray-500 mt-1">Please add products from the admin panel to display them here.</p>
    </div>
</section>
@endif

<!-- ==========================================
     11. BRAND VALUE & PROPOSITION PILLARS
=========================================== -->
<section class="py-12 bg-white border-t border-neutral-100" data-purpose="features-bar">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            
            <div class="p-4 rounded-lg bg-neutral-50 border border-neutral-100 flex flex-col items-center">
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xl mb-3">
                    <i class="fa-solid fa-gem"></i>
                </div>
                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide">100% Authentic</h4>
                <p class="text-xs text-gray-500 mt-1">Premium imported & combed fabrics</p>
            </div>

            <div class="p-4 rounded-lg bg-neutral-50 border border-neutral-100 flex flex-col items-center">
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xl mb-3">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Fast Delivery</h4>
                <p class="text-xs text-gray-500 mt-1">Dhaka 24-48h, Nationwide express</p>
            </div>

            <div class="p-4 rounded-lg bg-neutral-50 border border-neutral-100 flex flex-col items-center">
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xl mb-3">
                    <i class="fa-solid fa-arrow-rotate-left"></i>
                </div>
                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide">7 Days Exchange</h4>
                <p class="text-xs text-gray-500 mt-1">Hassle-free sizing & exchange policy</p>
            </div>

            <div class="p-4 rounded-lg bg-neutral-50 border border-neutral-100 flex flex-col items-center">
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xl mb-3">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
                <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Cash on Delivery</h4>
                <p class="text-xs text-gray-500 mt-1">Pay on delivery & instant bKash</p>
            </div>
        </div>
    </div>
</section>


<!-- ==========================================
     12. VIP NEWSLETTER SUBSCRIPTION
=========================================== -->
<section id="offer" class="py-14 bg-neutral-900 text-white relative overflow-hidden" data-purpose="newsletter-section" x-data="{ email: '', subscribed: false }">
    <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
        <span class="text-xs font-bold text-red-500 uppercase tracking-widest block mb-2">Join The Kingsman VIP Club</span>
        <h2 class="text-2xl sm:text-3xl font-royal font-bold tracking-wider mb-3">
            Get 10% OFF Your First Online Order
        </h2>
        <p class="text-xs sm:text-sm text-neutral-400 max-w-lg mx-auto mb-6">
            Subscribe to receive exclusive access to our newest Festive Panjabi drops, seasonal offers, and showroom flash events.
        </p>

        <form @submit.prevent="if(email){ subscribed = true; window.showRobeToast('Thank you for subscribing to Kingsman VIP Club!'); email = ''; }" class="flex flex-col sm:flex-row items-center justify-center gap-3 max-w-md mx-auto">
            <input type="email" 
                   x-model="email" 
                   required
                   placeholder="Enter your email address..." 
                   class="w-full bg-neutral-800 border border-neutral-700 text-white placeholder-neutral-500 text-xs sm:text-sm px-4 py-3 rounded focus:outline-none focus:border-red-500">
            <button type="submit" 
                    class="w-full sm:w-auto px-6 py-3 bg-red-600 hover:bg-red-700 text-white text-xs sm:text-sm font-bold uppercase tracking-wider rounded transition whitespace-nowrap">
                Subscribe
            </button>
        </form>
    </div>
</section>
@endsection
