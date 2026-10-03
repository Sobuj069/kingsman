@props([
    'id' => 1,
    'name' => 'Product',
    'price' => 0,
    'old_price' => null,
    'image' => '',
    'category' => 'Apparel',
    'discount' => null,
    'tag' => 'Kingsman',
    'stock' => null,
    'in_stock' => null,
    'sizes' => [],
    'colors' => [],
    'size_stocks' => null,
    'variation_stocks' => null,
])

@php
    $calcDiscount = ($old_price && $old_price > $price) ? round((($old_price - $price) / $old_price) * 100) : ($discount ?? null);
    $isInStock = true;
    if ($in_stock === false) {
        $isInStock = false;
    } elseif ($stock !== null && (float)$stock <= 0) {
        $isInStock = false;
    } elseif ($in_stock !== null) {
        $isInStock = (bool)$in_stock;
    }

    $productData = [
        'id' => $id,
        'name' => $name,
        'price' => $price,
        'old_price' => $old_price,
        'image' => $image ?: asset('frontend/images/no-image.svg'),
        'category' => $category,
        'stock' => $stock,
        'in_stock' => $isInStock,
        'sizes' => $sizes ?? [],
        'colors' => $colors ?? [],
        'size_stocks' => $size_stocks ?? null,
        'variation_stocks' => $variation_stocks ?? null
    ];
@endphp

<div class="product-card group relative border border-gray-200 rounded-sm bg-white overflow-hidden flex flex-col justify-between transition-all duration-300"
     data-purpose="product-card">
    
    <!-- Image & Top Actions Container -->
    <div class="relative aspect-[3/4] bg-neutral-100 overflow-hidden cursor-pointer">
        <a href="{{ route('product.details', $id) }}" class="block w-full h-full">
            @if(!$isInStock)
                <!-- Out of Stock Badge -->
                <span class="absolute top-2.5 right-12 z-10 text-[9px] font-bold bg-rose-600 text-white px-2 py-0.5 rounded shadow">
                    OUT OF STOCK
                </span>
            @endif

            <!-- Discount Badge if on Sale -->
            @if($calcDiscount)
                <span class="absolute bottom-2.5 left-2.5 z-10 text-[10px] font-extrabold bg-red-600 text-white px-2 py-0.5 rounded shadow-sm">
                    {{ $calcDiscount }}% OFF
                </span>
            @endif

            <!-- Product Image -->
            <img src="{{ $image ?: asset('frontend/images/no-image.svg') }}" 
                 alt="{{ $name }}" 
                 loading="lazy"
                 decoding="async"
                 width="400"
                 height="530"
                 class="product-img w-full h-full object-cover">
        </a>

        <!-- Top Right Action: Wishlist Toggle -->
        <button type="button" 
                @click.stop="$store.wishlist.toggle({{ json_encode($productData) }})"
                :class="$store.wishlist.has({{ $id }}) ? 'text-red-600 bg-red-50' : 'text-gray-400 hover:text-red-500 bg-white/90 hover:bg-white'"
                class="absolute top-2.5 right-2.5 z-20 w-8 h-8 rounded-full shadow flex items-center justify-center transition-all duration-200"
                aria-label="Save to wishlist">
            <i :class="$store.wishlist.has({{ $id }}) ? 'fa-solid fa-heart' : 'fa-regular fa-heart'" class="text-xs"></i>
        </button>


    </div>

    <!-- Product Meta & Price Section -->
    <div class="p-2.5 sm:p-3.5 flex flex-col flex-1 justify-between bg-white">
        <div>
            <!-- Category Sub-text -->
            <span class="text-[9px] sm:text-[10px] uppercase font-semibold text-gray-400 tracking-wider block mb-1">
                {{ $category }}
            </span>
            <!-- Product Title -->
            <h3 class="text-xs sm:text-sm font-medium text-gray-800 line-clamp-2 hover:text-red-600 transition leading-snug">
                <a href="{{ route('product.details', $id) }}" class="hover:text-red-600">
                    {{ $name }}
                </a>
            </h3>
        </div>

        <div class="mt-2.5 sm:mt-3 pt-2 border-t border-gray-100 flex items-center justify-between gap-1">
            <!-- Price Display -->
            <div class="flex items-baseline gap-1 sm:gap-1.5 flex-wrap min-w-0">
                <span class="text-xs sm:text-sm font-bold text-gray-900">TK {{ number_format($price) }}</span>
                @if($old_price && $old_price > $price)
                    <span class="text-[10px] sm:text-xs text-red-500 line-through font-medium">TK {{ number_format($old_price) }}</span>
                @endif
                @if($calcDiscount)
                    <span class="text-[8px] sm:text-[9px] font-extrabold bg-red-600 text-white px-1 sm:px-1.5 py-0.5 rounded uppercase leading-none">
                        OFF {{ $calcDiscount }}%
                    </span>
                @endif
            </div>

            <!-- Out of Stock Indicator (if applicable) -->
            @if(!$isInStock)
                <span class="text-[9px] font-bold text-rose-600 bg-rose-50 border border-rose-200 px-1.5 py-0.5 rounded uppercase shrink-0" title="Out of Stock">
                    Out of Stock
                </span>
            @endif
        </div>
    </div>
</div>
