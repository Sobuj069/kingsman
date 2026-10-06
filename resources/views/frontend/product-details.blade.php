@extends('frontend.layouts.master')

@section('title', ($product['name'] ?? 'Product Details') . ' | ' . ($comName ?? 'Kingsman') . ' Bespoke Luxury Ethnic Menswear')
@section('meta_description', ($product['name'] ?? 'Product Details') . ' - Handcrafted bespoke luxury ethnic menswear with Egyptian Giza cotton, gold zari embroidery, and architectural tailoring by ' . ($comName ?? 'Kingsman') . ' Atelier Dhaka.')

@push('styles')
<style>
    /* Custom Luxury Styling Extensions */
    .font-outfit { font-family: 'Outfit', sans-serif; }
    .bg-deep-noir { background-color: #0B0B0C; }
    .text-gold-metallic { color: #C5A880; }
    .bg-gold-metallic { background-color: #C5A880; }
    .border-gold-metallic { border-color: #C5A880; }
    .bg-gold-light { background-color: #EFE6DB; }
    .text-gold-light { color: #EFE6DB; }
    .bg-surface-subtle { background-color: #F5F5F7; }
    .text-status-alert { color: #FF4D03; }
    .text-status-whatsapp { color: #25D366; }
    
    .cursor-crosshair-zoom {
        cursor: zoom-in;
    }

    /* Product Description Rich HTML Formatting (Same as Admin Pasted) */
    .product-description-content {
        color: #1e293b;
        font-size: 13px;
        line-height: 1.7;
        word-break: break-word;
    }
    .product-description-content p {
        margin-bottom: 0.65rem;
    }
    .product-description-content p:last-child {
        margin-bottom: 0;
    }
    .product-description-content ul {
        list-style-type: disc !important;
        padding-left: 1.35rem !important;
        margin: 0.5rem 0 0.75rem 0 !important;
    }
    .product-description-content ol {
        list-style-type: decimal !important;
        padding-left: 1.35rem !important;
        margin: 0.5rem 0 0.75rem 0 !important;
    }
    .product-description-content li {
        margin-bottom: 0.35rem !important;
        list-style: inherit !important;
    }
    .product-description-content strong,
    .product-description-content b {
        font-weight: 700 !important;
        color: #0f172a;
    }
    .product-description-content em,
    .product-description-content i {
        font-style: italic !important;
    }
    .product-description-content u {
        text-decoration: underline !important;
    }
    .product-description-content h1,
    .product-description-content h2,
    .product-description-content h3,
    .product-description-content h4,
    .product-description-content h5,
    .product-description-content h6 {
        font-weight: 700 !important;
        color: #0f172a;
        margin-top: 0.75rem;
        margin-bottom: 0.4rem;
        line-height: 1.3;
    }
    .product-description-content h1 { font-size: 1.3rem; }
    .product-description-content h2 { font-size: 1.18rem; }
    .product-description-content h3 { font-size: 1.08rem; }
    .product-description-content h4 { font-size: 0.98rem; }
    .product-description-content table {
        width: 100% !important;
        border-collapse: collapse !important;
        margin: 0.75rem 0 !important;
        border: 1px solid #cbd5e1 !important;
    }
    .product-description-content th,
    .product-description-content td {
        border: 1px solid #cbd5e1 !important;
        padding: 6px 10px !important;
        text-align: left;
    }
    .product-description-content th {
        background-color: #f1f5f9 !important;
        font-weight: 600 !important;
    }
    .product-description-content img {
        max-width: 100% !important;
        height: auto !important;
        border-radius: 6px;
        margin: 0.5rem 0;
    }
    .product-description-content blockquote {
        border-left: 3px solid #C5A880;
        padding-left: 0.75rem;
        font-style: italic;
        color: #64748b;
        margin: 0.75rem 0;
    }
    .product-description-content a {
        color: #2563eb;
        text-decoration: underline;
    }
</style>
@endpush

@section('content')
<div class="flex flex-col w-full bg-white font-sans text-[#1a1b22] pb-20 md:pb-10" 
     x-data="productDetailsState({{ json_encode($product) }})">

    <!-- 1. TOP BREADCRUMB & QUICK REFERENCE RAIL -->
    <section class="w-full bg-[#F5F5F7] border-b border-gray-200/80 py-3.5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between flex-wrap gap-2 text-xs uppercase tracking-wider text-gray-500 font-medium">
            <nav class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('home') }}" class="hover:text-black transition-colors">Home</a>
                <span class="text-gray-300">/</span>
                <a href="#offer" class="hover:text-black transition-colors">{{ $product['category'] ?? 'Festive Collection 2026' }}</a>
                <span class="text-gray-300">/</span>
                <span class="hover:text-black transition-colors">{{ $product['subcategory'] ?? 'Panjabi' }}</span>
                <span class="text-gray-300">/</span>
                <span class="text-black font-bold truncate max-w-xs sm:max-w-md">{{ $product['name'] }}</span>
            </nav>
            <div class="hidden lg:flex items-center gap-4 text-xs text-[#725b38]">
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm text-[#C5A880]">verified</span> 
                    <span>Atelier Master Series</span>
                </span>
                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                <span class="text-gray-500">SKU: {{ $product['sku'] ?? 'KM-PJ-8821' }}</span>
            </div>
        </div>
    </section>

    <!-- 2. MAIN PRODUCT SHOWCASE GRID -->
    <section class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
            
            <!-- LEFT COLUMN: ASYMMETRIC LUXURY GALLERY (7 cols) -->
            <div class="lg:col-span-7 flex flex-col gap-6">
                <div class="flex flex-col-reverse md:flex-row gap-4 items-start">
                    
                    <!-- Vertical Thumbnail Switcher -->
                    <div class="flex md:flex-col gap-3 overflow-x-auto md:overflow-visible shrink-0 pb-2 md:pb-0 w-full md:w-20 hide-scrollbar">
                        @foreach($product['images'] as $index => $img)
                            <button type="button" 
                                    @click="selectImage({{ $index }})"
                                    :class="activeImageIndex === {{ $index }} ? 'ring-2 ring-black opacity-100 shadow-md' : 'opacity-70 hover:opacity-100 ring-1 ring-gray-200'"
                                    class="w-16 h-20 sm:w-20 sm:h-24 bg-[#F5F5F7] relative overflow-hidden transition-all shadow-sm focus:outline-none shrink-0 group rounded-none">
                                <img src="{{ $img }}" 
                                     alt="{{ $product['name'] }} Thumbnail {{ $index + 1 }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <span class="absolute bottom-1 right-1 bg-black/80 text-white text-[9px] font-bold px-1 uppercase tracking-tighter">
                                    @if($index === 0) 01
                                    @elseif($index === 1) DETAIL
                                    @elseif($index === 2) PROFILE
                                    @elseif($index === 3) BACK
                                    @else LOOK
                                    @endif
                                </span>
                            </button>
                        @endforeach
                    </div>

                    <!-- Primary Interactive Hero Showcase -->
                    <div class="relative flex-1 w-full bg-[#F5F5F7] overflow-hidden group shadow-sm border border-gray-100">
                        <div class="w-full aspect-[3/4] relative overflow-hidden bg-neutral-100 select-none" 
                             id="main-image-container"
                             :class="isHoverZoom ? 'cursor-crosshair' : 'cursor-zoom-in'"
                             @mousemove="onImageHover($event)"
                             @mouseenter="onImageHover($event)"
                             @mouseleave="onImageLeave()"
                             @click="zoomModalOpen = true">
                            
                            <img :src="activeImage" 
                                 alt="{{ $product['name'] }}" 
                                 id="primary-product-img"
                                 class="w-full h-full object-cover select-none pointer-events-none will-change-transform"
                                 :style="isHoverZoom 
                                    ? `transform: scale(2.6); transform-origin: ${zoomX}% ${zoomY}%; transition: transform 0.05s ease-out;` 
                                    : 'transform: scale(1); transform-origin: center center; transition: transform 0.3s ease-out;'">
                            
                            <!-- Floating Zoom & Gallery Trigger -->
                            <button type="button" 
                                    @click.stop="zoomModalOpen = true"
                                    class="absolute top-4 right-4 w-10 h-10 bg-white/90 backdrop-blur-sm flex items-center justify-center text-black shadow-sm hover:bg-black hover:text-white transition-colors z-10" 
                                    title="Inspect High-Res Atelier Craftsmanship">
                                <span class="material-symbols-outlined text-lg">zoom_in</span>
                            </button>

                            <!-- Floating Stock / Atelier Batch Badge -->
                            @if(!empty($product['in_stock']))
                                <div class="absolute bottom-4 left-4 bg-[#0B0B0C]/85 text-white backdrop-blur-sm px-3.5 py-1.5 flex items-center gap-2 text-[10px] tracking-wider uppercase font-semibold pointer-events-none z-10">
                                    <span class="w-2 h-2 rounded-full bg-[#25D366] animate-pulse"></span>
                                    <span>{{ $product['stock_status'] ?? 'In Stock • Available' }}</span>
                                </div>
                            @else
                                <div class="absolute bottom-4 left-4 bg-rose-950/90 text-white backdrop-blur-sm px-3.5 py-1.5 flex items-center gap-2 text-[10px] tracking-wider uppercase font-semibold border border-rose-500/30 pointer-events-none z-10">
                                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                    <span>Out of Stock • Unavailable</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Brand Value & Assurance Bar -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 md:p-5 bg-[#F5F5F7] border border-gray-200/80">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white flex items-center justify-center text-black shrink-0 shadow-sm">
                            <span class="material-symbols-outlined text-lg">verified</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs uppercase font-bold text-black tracking-wider">100% Authentic</span>
                            <span class="text-[11px] text-gray-500">Premium Quality Assured</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white flex items-center justify-center text-black shrink-0 shadow-sm">
                            <span class="material-symbols-outlined text-lg">local_shipping</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs uppercase font-bold text-black tracking-wider">Fast Delivery</span>
                            <span class="text-[11px] text-gray-500">24-48h Nationwide</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white flex items-center justify-center text-black shrink-0 shadow-sm">
                            <span class="material-symbols-outlined text-lg">sync_alt</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs uppercase font-bold text-black tracking-wider">7 Days Exchange</span>
                            <span class="text-[11px] text-gray-500">Doorstep Fit Support</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: STICKY COMMERCIAL & TAILORING CONFIGURATION PANE (5 cols) -->
            <div class="lg:col-span-5 flex flex-col gap-6 lg:sticky lg:top-24">
                
                <!-- Editorial Identification & Rating -->
                <div class="flex flex-col gap-2 pb-2 border-b border-gray-100">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        @if(!empty($product['category']))
                            <span class="bg-[#EFE6DB] text-[#725b38] text-[11px] font-bold px-2.5 py-1 tracking-widest uppercase">
                                {{ $product['category'] }}
                            </span>
                        @endif
                        @if(!empty($product['sku']))
                            <span class="text-gray-500 text-xs">SKU: {{ $product['sku'] }}</span>
                        @endif
                    </div>

                    <h1 class="font-outfit text-2xl md:text-3xl lg:text-[28px] font-bold text-black uppercase tracking-tight mt-1 leading-tight">
                        {{ $product['name'] }}
                    </h1>

                    <div class="flex items-baseline gap-3 pt-1 flex-wrap">
                        <span class="font-outfit text-2xl md:text-3xl font-bold text-black" x-text="'৳' + Number(currentPrice).toLocaleString()">৳{{ number_format($product['price']) }}</span>
                        <template x-if="currentOldPrice && currentOldPrice > currentPrice">
                            <span class="text-base line-through text-gray-400 font-medium" x-text="'৳' + Number(currentOldPrice).toLocaleString()"></span>
                        </template>
                        <template x-if="currentOldPrice && currentOldPrice > currentPrice">
                            <span class="text-[11px] text-[#FF4D03] tracking-wider uppercase font-bold bg-orange-50 px-2 py-0.5 rounded-sm border border-orange-100" 
                                  x-text="Math.round(((currentOldPrice - currentPrice) / currentOldPrice) * 100) + '% Off'"></span>
                        </template>
                    </div>

                    <!-- Product Details / Description -->
                    @if(!empty($product['description']))
                        @php
                            $rawDesc = $product['description'];
                            $hasHtml = strip_tags($rawDesc) !== $rawDesc || preg_match('/<[a-z][\s\S]*>/i', $rawDesc);
                        @endphp
                        <div class="product-description-content text-[13px] md:text-sm text-gray-700 leading-relaxed pt-3 border-t border-gray-100 mt-2">
                            @if($hasHtml)
                                {!! $rawDesc !!}
                            @else
                                {!! nl2br(e($rawDesc)) !!}
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Color Selection Module (Only if colors configured) -->
                @if(!empty($product['colors']) && count($product['colors']) > 0)
                <div class="flex flex-col gap-2">
                    <div class="flex justify-between items-center text-xs font-semibold">
                        <span class="uppercase tracking-wider text-gray-800">
                            Color: <span class="font-bold text-black" x-text="selectedColor"></span>
                        </span>
                    </div>
                    <div class="flex items-center gap-3">
                        @foreach($product['colors'] as $c)
                            <button type="button" 
                                    @click="selectColor({{ json_encode($c) }})"
                                    :class="selectedColor === '{{ $c['name'] }}' ? 'ring-2 ring-black ring-offset-2' : 'ring-1 ring-gray-300 hover:ring-gray-400 ring-offset-1'"
                                    class="w-9 h-9 rounded-none flex items-center justify-center transition-all focus:outline-none"
                                    style="background-color: {{ $c['hex'] }};"
                                    title="{{ $c['name'] }}">
                                <span class="material-symbols-outlined text-xs" 
                                      :class="selectedColor === '{{ $c['name'] }}' ? ('{{ $c['hex'] }}' === '#F8F5F0' || '{{ $c['hex'] }}' === '#FFFFFF' ? 'text-black' : 'text-white') : 'hidden'">
                                    check
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Sizing & Multi-Variant Selection Module (If sizes configured) -->
                @if(!empty($product['sizes']) && count($product['sizes']) > 0)
                <div class="flex flex-col gap-2.5">
                    <div class="flex justify-between items-center text-xs">
                        <div class="flex items-center gap-2 font-semibold uppercase tracking-wider text-gray-800">
                            <span>Select Variants / Sizes:</span>
                            <template x-if="totalSelectedQty > 0">
                                <span class="bg-black text-white px-2 py-0.5 text-[10px] font-bold rounded" x-text="totalSelectedQty + ' items selected'"></span>
                            </template>
                        </div>
                        @if(!empty($product['has_size_guide']))
                        <button type="button" 
                                @click="sizeModalOpen = true"
                                class="flex items-center gap-1 text-black hover:text-red-600 transition-colors underline font-semibold uppercase tracking-wider text-[11px]">
                            <span class="material-symbols-outlined text-sm">straighten</span>
                            <span>Size Guide</span>
                        </button>
                        @endif
                    </div>

                    <!-- Multi-Variant Sizing Cards with Individual Quantity Counters -->
                    <div class="flex flex-col gap-2 border border-gray-200 rounded-lg p-3 bg-[#fafafa]">
                        @foreach($product['sizes'] as $s)
                            <div class="flex items-center justify-between p-2.5 bg-white border rounded-md transition-all shadow-2xs"
                                 :class="getVariantQty('{{ $s }}') > 0 ? 'border-black ring-1 ring-black bg-neutral-50/70' : 'border-gray-200 hover:border-gray-300'">
                                <!-- Left: Size & Price & Stock -->
                                <div class="flex flex-col min-w-0 pr-2">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-sm text-black uppercase tracking-tight">{{ $s }}</span>
                                        <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded"
                                              :class="getSizeStock('{{ $s }}') > 0 ? 'text-emerald-700 bg-emerald-50' : 'text-rose-600 bg-rose-50'"
                                              x-text="getSizeStock('{{ $s }}') > 0 ? (getSizeStock('{{ $s }}') + ' available') : 'Out of stock'"></span>
                                    </div>
                                    <div class="flex items-baseline gap-2 mt-0.5">
                                        <span class="font-outfit font-extrabold text-sm text-black" x-text="'৳' + Number(getSizePrice('{{ $s }}')).toLocaleString()"></span>
                                        <template x-if="getSizeOldPrice('{{ $s }}') && getSizeOldPrice('{{ $s }}') > getSizePrice('{{ $s }}')">
                                            <span class="text-[11px] text-gray-400 line-through" x-text="'৳' + Number(getSizeOldPrice('{{ $s }}')).toLocaleString()"></span>
                                        </template>
                                    </div>
                                </div>

                                <!-- Right: Quantity Counter for this Variant -->
                                <div class="flex items-center shrink-0">
                                    <template x-if="getSizeStock('{{ $s }}') > 0">
                                        <div class="flex items-center border border-gray-300 rounded overflow-hidden bg-white shadow-xs h-9">
                                            <button type="button" 
                                                    @click="updateVariantQty('{{ $s }}', -1)"
                                                    :disabled="getVariantQty('{{ $s }}') <= 0"
                                                    class="w-8 h-full flex items-center justify-center text-gray-600 hover:text-black hover:bg-gray-100 font-bold disabled:opacity-30 disabled:cursor-not-allowed">
                                                <span class="material-symbols-outlined text-sm">remove</span>
                                            </button>
                                            <input type="number" 
                                                   :value="getVariantQty('{{ $s }}')" 
                                                   @input="setVariantQty('{{ $s }}', $event.target.value)"
                                                   min="0"
                                                   :max="getSizeStock('{{ $s }}')"
                                                   class="w-10 text-center font-bold text-xs text-black border-x border-gray-200 focus:outline-none p-0 h-full">
                                            <button type="button" 
                                                    @click="updateVariantQty('{{ $s }}', 1)"
                                                    :disabled="getVariantQty('{{ $s }}') >= getSizeStock('{{ $s }}')"
                                                    class="w-8 h-full flex items-center justify-center text-gray-600 hover:text-black hover:bg-gray-100 font-bold disabled:opacity-30 disabled:cursor-not-allowed">
                                                <span class="material-symbols-outlined text-sm">add</span>
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="getSizeStock('{{ $s }}') <= 0">
                                        <span class="text-xs text-gray-400 font-semibold px-2 py-1 bg-gray-100 rounded">Out of stock</span>
                                    </template>
                                </div>
                            </div>
                        @endforeach

                        <!-- Selection Summary Bar -->
                        <div class="flex items-center justify-between pt-2 border-t border-gray-200 text-xs">
                            <span class="text-gray-600 font-medium">
                                Total Selected: <strong class="text-black" x-text="totalSelectedQty + ' items'"></strong>
                            </span>
                            <div class="flex items-baseline gap-1">
                                <span class="text-gray-500 font-medium text-[11px]">Subtotal:</span>
                                <span class="font-outfit font-black text-base text-black" x-text="'৳' + Number(totalSelectedPrice).toLocaleString()"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Fit Context Hint -->
                    <div class="bg-[#F5F5F7] p-3 flex items-center gap-2.5 text-gray-600 text-xs border border-gray-200/60 rounded-md">
                        <span class="material-symbols-outlined text-base text-[#C5A880]">accessibility_new</span>
                        <span>Select quantities for one or multiple variations above to add them to your cart.</span>
                    </div>
                </div>
                @endif

                <!-- Quantity & Luxury Purchase Triggers -->
                <div class="flex flex-col gap-3.5 pt-1">
                    @if(!empty($product['in_stock']))
                        <div class="flex items-stretch gap-2.5 sm:gap-3 flex-wrap sm:flex-nowrap">
                            <!-- Show standalone counter ONLY if product has NO sizes -->
                            @if(empty($product['sizes']) || count($product['sizes']) == 0)
                            <div class="flex items-center bg-[#F5F5F7] border border-gray-200/90 rounded-lg px-2 shrink-0 h-12">
                                <button type="button" 
                                        @click="updateQty(-1)"
                                        :disabled="quantity <= 1"
                                        class="w-8 h-full flex items-center justify-center text-gray-700 hover:text-black font-bold disabled:opacity-40 disabled:cursor-not-allowed">
                                    <span class="material-symbols-outlined text-sm">remove</span>
                                </button>
                                <span class="w-8 text-center font-bold text-sm text-black" x-text="quantity"></span>
                                <button type="button" 
                                        @click="updateQty(1)"
                                        :disabled="quantity >= currentSizeStock"
                                        class="w-8 h-full flex items-center justify-center text-gray-700 hover:text-black font-bold disabled:opacity-40 disabled:cursor-not-allowed">
                                    <span class="material-symbols-outlined text-sm">add</span>
                                </button>
                            </div>
                            @endif

                            <!-- Primary Cart Action -->
                            <button type="button" 
                                    @click="addToCart()"
                                    :disabled="totalSelectedQty <= 0"
                                    :class="totalSelectedQty <= 0 ? 'opacity-50 cursor-not-allowed bg-neutral-100 text-gray-400' : 'bg-[#EFEFEF] hover:bg-[#e4e4e4] text-[#1a1b22] border border-gray-200 active:scale-[0.99]'"
                                    class="flex-1 h-12 rounded-lg text-xs md:text-sm font-bold uppercase tracking-wider transition-all flex items-center justify-center gap-2 shadow-sm min-w-[130px]">
                                <span class="material-symbols-outlined text-base">shopping_bag</span>
                                <span x-text="totalSelectedQty > 0 ? (totalSelectedQty > 1 ? ('Add ' + totalSelectedQty + ' Items To Cart') : 'Add To Cart') : 'Select Variant'"></span>
                            </button>

                            <!-- Direct Instant Buy -->
                            <button type="button" 
                                    @click="buyNow()"
                                    :disabled="totalSelectedQty <= 0"
                                    :class="totalSelectedQty <= 0 ? 'opacity-50 cursor-not-allowed bg-neutral-600' : 'bg-black hover:bg-neutral-800 active:scale-[0.99]'"
                                    class="flex-1 h-12 rounded-lg text-white text-xs md:text-sm font-bold uppercase tracking-wider transition-all flex items-center justify-center gap-2 shadow-md hover:shadow-lg min-w-[130px]">
                                <span class="material-symbols-outlined text-base">bolt</span>
                                <span x-text="totalSelectedQty > 0 ? ('Order Now (' + '৳' + Number(totalSelectedPrice).toLocaleString() + ')') : 'Order Now'"></span>
                            </button>
                        </div>
                    @else
                        <!-- Out of stock display -->
                        <div class="p-4 bg-rose-50 border border-rose-200 rounded-lg flex items-center gap-3">
                            <span class="material-symbols-outlined text-rose-600 text-2xl">inventory_2</span>
                            <div>
                                <p class="text-xs font-bold text-rose-800 uppercase tracking-wider">Currently Out of Stock</p>
                                <p class="text-[11px] text-rose-600">This item is currently unavailable for order.</p>
                            </div>
                        </div>
                        <button type="button" 
                                disabled
                                class="w-full h-12 bg-neutral-200 text-neutral-400 font-bold uppercase text-xs md:text-sm tracking-widest cursor-not-allowed flex items-center justify-center gap-2 rounded-lg">
                            <span class="material-symbols-outlined text-lg">block</span>
                            <span>Out of Stock (Cannot Order)</span>
                        </button>
                    @endif

                    <!-- Call Now & WhatsApp Now Action Cards -->
                    @php
                        $rawWa = function_exists('get_whatsapp_phone') ? get_whatsapp_phone() : ($whatsappNumber ?? ($hotline ?? '01987258406'));
                        $rawPhone = function_exists('get_hotline_phone') ? get_hotline_phone() : ($hotline ?? '01987258406');
                        $waDigits = preg_replace('/[^0-9]/', '', $rawWa);
                        if (str_starts_with($waDigits, '0') && strlen($waDigits) === 11) {
                            $waDigits = '88' . $waDigits;
                        } elseif (!str_starts_with($waDigits, '88') && !empty($waDigits) && strlen($waDigits) <= 11) {
                            $waDigits = '88' . $waDigits;
                        }
                        $callDigits = preg_replace('/[^0-9+]/', '', $rawPhone);
                        $waProductMsg = 'Hello ' . ($comName ?? 'Kingsman') . ', I am inquiring about ' . ($product['name'] ?? 'Product') . ' (SKU: ' . ($product['sku'] ?? 'KM-PJ-8821') . ').';
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <!-- Call Now Card -->
                        <a href="tel:{{ $callDigits }}" 
                           class="flex items-center gap-3 p-3.5 bg-white rounded-xl border border-gray-200 shadow-sm hover:border-gray-400 hover:shadow transition-all group">
                            <div class="w-10 h-10 rounded-full bg-neutral-200 flex items-center justify-center text-neutral-800 group-hover:bg-black group-hover:text-white transition-colors shrink-0">
                                <i class="fa-solid fa-phone text-sm"></i>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="text-xs font-bold text-gray-900 leading-tight">Call Now</span>
                                <span class="text-xs text-gray-600 font-semibold tracking-wide truncate">{{ $rawPhone }}</span>
                            </div>
                        </a>

                        <!-- WhatsApp Now Card -->
                        <a href="https://wa.me/{{ $waDigits }}?text={{ urlencode($waProductMsg) }}" 
                           target="_blank" 
                           rel="noopener noreferrer"
                           class="flex items-center justify-between p-3.5 bg-white rounded-xl border border-gray-200 shadow-sm hover:border-emerald-400 hover:shadow transition-all group">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-[#25D366] group-hover:bg-[#25D366] group-hover:text-white transition-colors shrink-0">
                                    <i class="fa-brands fa-whatsapp text-xl"></i>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span class="text-xs font-bold text-gray-900 leading-tight">Whatsapp Now</span>
                                    <span class="text-xs text-gray-600 font-semibold tracking-wide truncate">{{ $rawWa }}</span>
                                </div>
                            </div>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 shrink-0 ml-2 animate-pulse" title="Online"></span>
                        </a>
                    </div>

                    <!-- Delivery & Payment In Cash Assurance -->
                    <div class="flex items-center gap-1.5 text-xs sm:text-[13px] text-gray-700 font-medium pt-0.5">
                        <span class="material-symbols-outlined text-base text-gray-500 shrink-0">local_shipping</span>
                        <span>Delivery 1-3 Day, Payment In Cash</span>
                    </div>
                </div>

                <!-- Sartorial Assurances Strip -->
                <div class="bg-[#F5F5F7] p-4 flex flex-col gap-3 border border-gray-200/80">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#C5A880] text-lg">schedule_send</span>
                        <span class="text-xs text-gray-700"><strong>Complimentary 24-Hour Dispatch:</strong> Next-day in Dhaka City, 48 hours nationwide.</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#C5A880] text-lg">published_with_changes</span>
                        <span class="text-xs text-gray-700"><strong>7-Day Doorstep Fit Exchange:</strong> Effortless swap service at your home or office.</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-[#C5A880] text-lg">content_cut</span>
                        <span class="text-xs text-gray-700"><strong>Custom Sleeve & Hem Alteration:</strong> Available free at any Kingsman Flagship Atelier.</span>
                    </div>
                </div>

                <!-- Collapsible Editorial Accordions -->
                <div class="flex flex-col gap-2">
                    <!-- Accordion: Delivery & Exchange Information -->
                    <div class="bg-[#F5F5F7] p-4 border border-gray-200/60">
                        <button type="button" 
                                @click="accordion2 = !accordion2"
                                class="w-full flex items-center justify-between text-xs font-bold uppercase text-black text-left tracking-wider">
                            <span>Delivery & Exchange Policy</span>
                            <span class="material-symbols-outlined text-base transition-transform duration-300" :class="accordion2 ? 'rotate-180' : ''">expand_more</span>
                        </button>
                        <div x-show="accordion2" x-collapse class="pt-3 text-xs text-gray-600 flex flex-col gap-2 leading-relaxed border-t border-gray-200/60 mt-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1 text-[11px]">
                                <div class="p-2.5 bg-white border border-gray-200">
                                    <span class="font-bold text-black block mb-0.5">Inside Dhaka City:</span>
                                    <span>Fast 24-48 hour delivery with cash on delivery.</span>
                                </div>
                                <div class="p-2.5 bg-white border border-gray-200">
                                    <span class="font-bold text-black block mb-0.5">Outside Dhaka:</span>
                                    <span>2-3 days express courier service across all districts.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. COMPLETE THE ENSEMBLE / CURATED PAIRINGS -->
    @if(!empty($pairings))
    <section class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
            <div class="flex flex-col gap-1">
                <span class="text-xs font-bold text-[#C5A880] uppercase tracking-widest">Sartorial Harmony</span>
                <h3 class="font-outfit text-2xl md:text-3xl font-bold text-black uppercase tracking-tight">Complete The Look</h3>
            </div>
            <p class="text-xs text-gray-500 max-w-md">Curated to pair seamlessly with {{ $product['name'] }}.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($pairings as $pair)
                <div class="group flex flex-col bg-[#F5F5F7] p-4 shadow-sm transition-all duration-300 hover:-translate-y-1 border border-gray-200/60 justify-between">
                    <div>
                        <div class="aspect-[3/4] bg-white relative overflow-hidden mb-3">
                            <a href="{{ route('product.details', $pair['id']) }}" class="block w-full h-full">
                                <img src="{{ $pair['image'] }}" 
                                     alt="{{ $pair['name'] }}" 
                                     class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            </a>
                            @if(!empty($pair['tag']))
                                <span class="absolute top-2 left-2 bg-black text-white text-[9px] font-bold px-2 py-0.5 uppercase tracking-wider">
                                    {{ $pair['tag'] }}
                                </span>
                            @endif
                        </div>
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block mb-1">
                            {{ $pair['category'] }}
                        </span>
                        <h4 class="text-sm font-bold text-black line-clamp-1 group-hover:text-red-600 transition-colors">
                            <a href="{{ route('product.details', $pair['id']) }}">{{ $pair['name'] }}</a>
                        </h4>
                    </div>

                    <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-200">
                        <span class="font-outfit text-sm font-bold text-black">৳{{ number_format($pair['price']) }}</span>
                        <a href="{{ route('product.details', $pair['id']) }}" 
                           class="w-8 h-8 bg-white text-black flex items-center justify-center hover:bg-black hover:text-white transition-colors shadow-sm border border-gray-200"
                           title="View Product">
                            <span class="material-symbols-outlined text-sm">visibility</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- 5. PATRON REVIEWS & SOCIAL PROOF SECTION -->
    @if(!empty($reviews))
    <section class="w-full bg-[#F5F5F7] py-14 md:py-18 border-t border-gray-200" id="patron-reviews">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col gap-10">
            
            <!-- Review Header & Score Analytics -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center pb-8 border-b border-gray-200">
                
                <div class="lg:col-span-4 flex flex-col gap-2">
                    <span class="text-xs uppercase font-bold text-[#C5A880] tracking-widest">Patron Verdict</span>
                    <div class="flex items-baseline gap-3">
                        <span class="font-outfit text-4xl sm:text-5xl font-bold text-black">4.9</span>
                        <span class="text-sm text-gray-500 font-medium">/ 5.0 Rating</span>
                    </div>
                    <div class="flex items-center text-[#C5A880]">
                        <span class="material-symbols-outlined text-lg" style="font-variation-settings: 'FILL' 1;">star</span>
                        <span class="material-symbols-outlined text-lg" style="font-variation-settings: 'FILL' 1;">star</span>
                        <span class="material-symbols-outlined text-lg" style="font-variation-settings: 'FILL' 1;">star</span>
                        <span class="material-symbols-outlined text-lg" style="font-variation-settings: 'FILL' 1;">star</span>
                        <span class="material-symbols-outlined text-lg" style="font-variation-settings: 'FILL' 1;">star_half</span>
                    </div>
                    <p class="text-xs text-gray-500">Verified reviews.</p>
                </div>

                <div class="lg:col-span-8 flex flex-col items-start lg:items-end justify-center">
                    <button type="button" 
                            @click="reviewModalOpen = true"
                            class="px-6 py-3.5 bg-black text-white text-xs font-bold uppercase tracking-widest hover:bg-neutral-900 transition-colors shadow-sm">
                        Write Patron Review
                    </button>
                </div>
            </div>

            <!-- Verified Patron Testimonials Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($reviews as $rev)
                    <div class="bg-white p-6 shadow-sm flex flex-col justify-between border border-gray-200/80">
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-[#C5A880]">
                                    @for($i = 0; $i < ($rev['rating'] ?? 5); $i++)
                                        <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">star</span>
                                    @endfor
                                </div>
                                <span class="text-[10px] text-gray-400 font-medium">{{ $rev['date'] }}</span>
                            </div>
                            <h5 class="text-sm font-bold text-black">{{ $rev['title'] }}</h5>
                            <p class="text-xs text-gray-600 leading-relaxed font-light">
                                "{{ $rev['comment'] }}"
                            </p>
                        </div>

                        <div class="flex items-center gap-3 pt-5 mt-4 border-t border-gray-100">
                            <div class="w-8 h-8 rounded-full bg-[#EFE6DB] flex items-center justify-center font-bold text-xs text-[#725b38]">
                                {{ $rev['initials'] }}
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-black">{{ $rev['author'] }}, {{ $rev['location'] }}</span>
                                <span class="text-[10px] text-[#25D366] flex items-center gap-1 font-medium">
                                    <span class="material-symbols-outlined text-xs">verified</span> Verified Patron • Size {{ $rev['size'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- 6. INTERACTIVE MEASUREMENT & SIZE GUIDE MODAL -->
    @if(!empty($product['has_size_guide']))
    <div x-show="sizeModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4"
         style="display: none;"
         @keydown.escape.window="sizeModalOpen = false">
        
        <div class="bg-white w-full max-w-2xl shadow-2xl overflow-hidden flex flex-col max-h-[85vh] animate-fadeIn"
             @click.away="sizeModalOpen = false">
            
            <div class="p-5 bg-[#0B0B0C] text-white flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="text-[11px] uppercase tracking-widest text-[#C5A880] font-bold">Size Guide & Sizing Matrix</span>
                    <h3 class="font-outfit text-lg md:text-xl font-bold text-white">
                        @if(($product['size_guide_type'] ?? '') === 'shirt')
                            Shirt Size & Measurement Matrix
                        @elseif(($product['size_guide_type'] ?? '') === 'tshirt')
                            T-Shirt / Polo Size Matrix
                        @elseif(($product['size_guide_type'] ?? '') === 'pants')
                            Pants / Trouser Size Matrix
                        @elseif(($product['size_guide_type'] ?? '') === 'custom_image')
                            Custom Size Chart
                        @else
                            Panjabi Size & Metric Matrix
                        @endif
                    </h3>
                </div>
                <button type="button" 
                        @click="sizeModalOpen = false"
                        class="w-8 h-8 flex items-center justify-center text-white hover:text-[#C5A880] transition-colors">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>

            <div class="p-6 flex flex-col gap-4 overflow-y-auto custom-scrollbar">
                @if(!empty($product['size_guide_content']))
                    <p class="text-xs text-gray-700 leading-relaxed bg-amber-50/70 p-3 border border-amber-200/80 rounded">
                        {{ $product['size_guide_content'] }}
                    </p>
                @endif

                @if(!empty($product['size_guide_image']))
                    <div class="w-full flex justify-center p-2 bg-gray-50 border border-gray-200 rounded">
                        <img src="{{ $product['size_guide_image'] }}" alt="Size Guide" class="max-w-full max-h-[420px] object-contain">
                    </div>
                @endif

                @if(($product['size_guide_type'] ?? 'panjabi') === 'panjabi')
                    <p class="text-xs text-gray-600 leading-relaxed">
                        All measurements are provided in inches. For a tailored slim silhouette, select your exact chest dimension. For festive layering or comfort fit, size up one unit.
                    </p>
                    <div class="overflow-x-auto border border-gray-200">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#F5F5F7] uppercase text-black font-bold border-b border-gray-200">
                                <tr>
                                    <th class="p-3">Size</th>
                                    <th class="p-3">Chest</th>
                                    <th class="p-3">Length</th>
                                    <th class="p-3">Shoulder</th>
                                    <th class="p-3">Sleeve</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50"><td class="p-3 font-bold text-black">38 (S)</td><td class="p-3">39.0"</td><td class="p-3">40.0"</td><td class="p-3">17.0"</td><td class="p-3">24.5"</td></tr>
                                <tr class="hover:bg-gray-50"><td class="p-3 font-bold text-black">40 (M)</td><td class="p-3">41.0"</td><td class="p-3">41.5"</td><td class="p-3">17.5"</td><td class="p-3">25.0"</td></tr>
                                <tr class="bg-[#EFE6DB]/50 font-semibold"><td class="p-3 text-[#725b38] font-bold">42 (L)</td><td class="p-3">43.0"</td><td class="p-3">43.0"</td><td class="p-3">18.25"</td><td class="p-3">25.5"</td></tr>
                                <tr class="hover:bg-gray-50"><td class="p-3 font-bold text-black">44 (XL)</td><td class="p-3">45.0"</td><td class="p-3">44.0"</td><td class="p-3">19.0"</td><td class="p-3">26.0"</td></tr>
                                <tr class="hover:bg-gray-50"><td class="p-3 font-bold text-black">46 (XXL)</td><td class="p-3">47.5"</td><td class="p-3">45.0"</td><td class="p-3">19.75"</td><td class="p-3">26.5"</td></tr>
                            </tbody>
                        </table>
                    </div>
                @elseif(($product['size_guide_type'] ?? '') === 'shirt')
                    <p class="text-xs text-gray-600 leading-relaxed">
                        All measurements are in inches. Regular fit shirts designed for supreme comfort and sharp contours.
                    </p>
                    <div class="overflow-x-auto border border-gray-200">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#F5F5F7] uppercase text-black font-bold border-b border-gray-200">
                                <tr>
                                    <th class="p-3">Size</th>
                                    <th class="p-3">Chest</th>
                                    <th class="p-3">Length</th>
                                    <th class="p-3">Collar</th>
                                    <th class="p-3">Sleeve</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50"><td class="p-3 font-bold text-black">S (38)</td><td class="p-3">38.0"</td><td class="p-3">28.5"</td><td class="p-3">14.5"</td><td class="p-3">24.5"</td></tr>
                                <tr class="hover:bg-gray-50"><td class="p-3 font-bold text-black">M (40)</td><td class="p-3">40.0"</td><td class="p-3">29.5"</td><td class="p-3">15.5"</td><td class="p-3">25.0"</td></tr>
                                <tr class="hover:bg-gray-50"><td class="p-3 font-bold text-black">L (42)</td><td class="p-3">42.0"</td><td class="p-3">30.5"</td><td class="p-3">16.5"</td><td class="p-3">25.5"</td></tr>
                                <tr class="hover:bg-gray-50"><td class="p-3 font-bold text-black">XL (44)</td><td class="p-3">44.0"</td><td class="p-3">31.5"</td><td class="p-3">17.5"</td><td class="p-3">26.0"</td></tr>
                                <tr class="hover:bg-gray-50"><td class="p-3 font-bold text-black">XXL (46)</td><td class="p-3">46.0"</td><td class="p-3">32.0"</td><td class="p-3">18.0"</td><td class="p-3">26.5"</td></tr>
                            </tbody>
                        </table>
                    </div>
                @elseif(($product['size_guide_type'] ?? '') === 'tshirt')
                    <p class="text-xs text-gray-600 leading-relaxed">
                        All measurements are in inches. Premium combed cotton fit.
                    </p>
                    <div class="overflow-x-auto border border-gray-200">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#F5F5F7] uppercase text-black font-bold border-b border-gray-200">
                                <tr>
                                    <th class="p-3">Size</th>
                                    <th class="p-3">Chest</th>
                                    <th class="p-3">Length</th>
                                    <th class="p-3">Shoulder</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50"><td class="p-3 font-bold text-black">M</td><td class="p-3">38.0"</td><td class="p-3">27.5"</td><td class="p-3">17.0"</td></tr>
                                <tr class="hover:bg-gray-50"><td class="p-3 font-bold text-black">L</td><td class="p-3">40.0"</td><td class="p-3">28.5"</td><td class="p-3">18.0"</td></tr>
                                <tr class="hover:bg-gray-50"><td class="p-3 font-bold text-black">XL</td><td class="p-3">42.0"</td><td class="p-3">29.5"</td><td class="p-3">19.0"</td></tr>
                                <tr class="hover:bg-gray-50"><td class="p-3 font-bold text-black">XXL</td><td class="p-3">44.0"</td><td class="p-3">30.5"</td><td class="p-3">20.0"</td></tr>
                            </tbody>
                        </table>
                    </div>
                @elseif(($product['size_guide_type'] ?? '') === 'pants')
                    <p class="text-xs text-gray-600 leading-relaxed">
                        All measurements are in inches. Tailored waistband and contemporary taper.
                    </p>
                    <div class="overflow-x-auto border border-gray-200">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-[#F5F5F7] uppercase text-black font-bold border-b border-gray-200">
                                <tr>
                                    <th class="p-3">Size</th>
                                    <th class="p-3">Waist</th>
                                    <th class="p-3">Length</th>
                                    <th class="p-3">Thigh</th>
                                    <th class="p-3">Hip</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50"><td class="p-3 font-bold text-black">30</td><td class="p-3">30.0"</td><td class="p-3">39.0"</td><td class="p-3">23.0"</td><td class="p-3">38.0"</td></tr>
                                <tr class="hover:bg-gray-50"><td class="p-3 font-bold text-black">32</td><td class="p-3">32.0"</td><td class="p-3">40.0"</td><td class="p-3">24.0"</td><td class="p-3">40.0"</td></tr>
                                <tr class="hover:bg-gray-50"><td class="p-3 font-bold text-black">34</td><td class="p-3">34.0"</td><td class="p-3">41.0"</td><td class="p-3">25.0"</td><td class="p-3">42.0"</td></tr>
                                <tr class="hover:bg-gray-50"><td class="p-3 font-bold text-black">36</td><td class="p-3">36.0"</td><td class="p-3">41.5"</td><td class="p-3">26.0"</td><td class="p-3">44.0"</td></tr>
                                <tr class="hover:bg-gray-50"><td class="p-3 font-bold text-black">38</td><td class="p-3">38.0"</td><td class="p-3">42.0"</td><td class="p-3">27.0"</td><td class="p-3">46.0"</td></tr>
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="p-4 bg-[#F5F5F7] flex items-start gap-3 border border-gray-200/80">
                    <span class="material-symbols-outlined text-[#C5A880] text-lg mt-0.5">help_outline</span>
                    <div class="flex flex-col text-xs text-gray-700">
                        <span class="font-bold text-black">Need a Custom Fit or Sizing Help?</span>
                        <span class="text-gray-500 mt-0.5">Mention your measurements in checkout notes or reach out to our concierge team on WhatsApp for personalized assistance.</span>
                    </div>
                </div>

                <button type="button" 
                        @click="sizeModalOpen = false"
                        class="w-full py-3 bg-[#0B0B0C] text-white text-xs font-bold uppercase tracking-widest hover:bg-black transition-colors">
                    Close Size Guide
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- 7. ZOOM MODAL -->
    <div x-show="zoomModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-black/90 backdrop-blur-md flex items-center justify-center p-4"
         style="display: none;"
         @keydown.escape.window="zoomModalOpen = false">
        
        <button type="button" 
                @click="zoomModalOpen = false"
                class="absolute top-6 right-6 text-white hover:text-[#C5A880] transition p-2 z-50">
            <span class="material-symbols-outlined text-3xl">close</span>
        </button>

        <div class="max-w-4xl max-h-[90vh] overflow-hidden" @click.away="zoomModalOpen = false">
            <img :src="activeImage" alt="{{ $product['name'] }}" class="max-w-full max-h-[85vh] object-contain shadow-2xl">
        </div>
    </div>

    <!-- 8. WRITE REVIEW MODAL -->
    <div x-show="reviewModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4"
         style="display: none;"
         @keydown.escape.window="reviewModalOpen = false">
        
        <div class="bg-white w-full max-w-lg shadow-2xl p-6 flex flex-col gap-4 animate-fadeIn"
             @click.away="reviewModalOpen = false">
            <div class="flex items-center justify-between border-b pb-3">
                <h4 class="font-outfit text-lg font-bold text-black uppercase">Write Patron Review</h4>
                <button @click="reviewModalOpen = false" class="text-gray-400 hover:text-black">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>
            
            <form @submit.prevent="submitReview()" class="space-y-4 text-xs">
                <div>
                    <label class="block font-bold uppercase text-gray-700 mb-1">Your Rating</label>
                    <div class="flex items-center gap-1 text-[#C5A880] text-xl">
                        <i class="fa-solid fa-star cursor-pointer"></i>
                        <i class="fa-solid fa-star cursor-pointer"></i>
                        <i class="fa-solid fa-star cursor-pointer"></i>
                        <i class="fa-solid fa-star cursor-pointer"></i>
                        <i class="fa-solid fa-star cursor-pointer"></i>
                    </div>
                </div>
                <div>
                    <label class="block font-bold uppercase text-gray-700 mb-1">Review Headline</label>
                    <input type="text" placeholder="e.g. Flawless stitch & royal look" class="w-full border-gray-300 p-2.5 text-xs focus:ring-black focus:border-black" required>
                </div>
                <div>
                    <label class="block font-bold uppercase text-gray-700 mb-1">Detailed Feedback</label>
                    <textarea rows="4" placeholder="Describe the fabric texture, fit, embroidery craftsmanship..." class="w-full border-gray-300 p-2.5 text-xs focus:ring-black focus:border-black" required></textarea>
                </div>
                <button type="submit" class="w-full py-3 bg-black text-white uppercase font-bold tracking-widest hover:bg-neutral-800 transition">
                    Submit Review for Verification
                </button>
            </form>
        </div>
    </div>

    <!-- 9. MOBILE FIXED BOTTOM ACTION BAR (md:hidden) -->
    <div class="md:hidden fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-gray-200 p-3 z-40 shadow-2xl flex items-center justify-between gap-3">
        <div class="flex flex-col min-w-0 pr-1">
            <div class="flex items-baseline gap-1.5 truncate">
                <span class="font-outfit font-bold text-base text-black" x-text="'৳' + Number(totalSelectedPrice || currentPrice).toLocaleString()">৳{{ number_format($product['price']) }}</span>
            </div>
            <div class="text-[10px] text-gray-500 font-medium truncate flex items-center gap-1">
                <span x-text="totalSelectedQty > 0 ? (totalSelectedQty + ' item(s) selected') : 'No variant selected'"></span>
            </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            @if(!empty($product['in_stock']))
                <button type="button" 
                        @click="addToCart()"
                        :disabled="totalSelectedQty <= 0"
                        :class="totalSelectedQty <= 0 ? 'opacity-50 cursor-not-allowed bg-neutral-600' : 'bg-[#0B0B0C] hover:bg-black active:scale-95'"
                        class="px-3.5 py-2.5 text-white text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 shadow-sm rounded-none">
                    <span class="material-symbols-outlined text-sm">shopping_bag</span>
                    <span x-text="totalSelectedQty > 1 ? ('Add (' + totalSelectedQty + ')') : 'Add to Bag'"></span>
                </button>
                <button type="button" 
                        @click="buyNow()"
                        :disabled="totalSelectedQty <= 0"
                        :class="totalSelectedQty <= 0 ? 'opacity-50 cursor-not-allowed bg-neutral-400' : 'bg-[#C5A880] hover:bg-[#b0926b] active:scale-95'"
                        class="px-3.5 py-2.5 text-white text-xs font-bold uppercase tracking-wider flex items-center gap-1 shadow-sm rounded-none">
                    <span class="material-symbols-outlined text-sm">bolt</span>
                    <span x-text="totalSelectedQty > 1 ? ('Order (' + totalSelectedQty + ')') : 'Order Now'"></span>
                </button>
            @else
                <button type="button" disabled class="px-4 py-2.5 bg-neutral-200 text-neutral-500 text-xs font-bold uppercase tracking-wider cursor-not-allowed">
                    Out of Stock
                </button>
            @endif
        </div>
    </div>

</div>

<script>
function productDetailsState(initialProduct) {
    const isAvailable = initialProduct.in_stock !== false && (initialProduct.stock !== undefined && initialProduct.stock !== null ? Number(initialProduct.stock) > 0 : true);
    const initialColor = (initialProduct.colors && initialProduct.colors.length > 0 && initialProduct.colors[0].name) ? initialProduct.colors[0].name : 'Default';
    
    // Helper to get stock for any size under a specific color
    const getStockForSize = (cName, sName) => {
        if (initialProduct.in_stock === false) return 0;
        if (initialProduct.variation_stocks && Object.keys(initialProduct.variation_stocks).length > 0) {
            if (cName && initialProduct.variation_stocks[cName] && initialProduct.variation_stocks[cName][sName] !== undefined) {
                return Math.max(0, Number(initialProduct.variation_stocks[cName][sName]) || 0);
            }
            if (initialProduct.size_stocks && initialProduct.size_stocks[sName] !== undefined) {
                return Math.max(0, Number(initialProduct.size_stocks[sName]) || 0);
            }
            const firstCol = Object.keys(initialProduct.variation_stocks)[0];
            if (firstCol && initialProduct.variation_stocks[firstCol][sName] !== undefined) {
                return Math.max(0, Number(initialProduct.variation_stocks[firstCol][sName]) || 0);
            }
            return 0;
        }
        if (initialProduct.size_stocks && Object.keys(initialProduct.size_stocks).length > 0) {
            if (initialProduct.size_stocks[sName] !== undefined) {
                return Math.max(0, Number(initialProduct.size_stocks[sName]) || 0);
            }
            return 0;
        }
        return (initialProduct.stock !== undefined && initialProduct.stock !== null) ? Math.max(0, Number(initialProduct.stock) || 0) : 0;
    };

    // Choose initial size that is in stock for the initial color, or default
    let defaultSz = initialProduct.default_size || (initialProduct.sizes && initialProduct.sizes.length > 0 ? initialProduct.sizes[0] : 'Default');
    if (getStockForSize(initialColor, defaultSz) <= 0) {
        for (let sz of (initialProduct.sizes || [])) {
            if (getStockForSize(initialColor, sz) > 0) {
                defaultSz = sz;
                break;
            }
        }
    }

    const initSizeStock = getStockForSize(initialColor, defaultSz);

    // Initialize multi-variant quantity map
    const initialVariantQuantities = {};
    if (initialProduct.sizes && initialProduct.sizes.length > 0) {
        initialProduct.sizes.forEach(sz => {
            initialVariantQuantities[sz] = (sz === defaultSz && isAvailable && initSizeStock > 0) ? 1 : 0;
        });
    } else {
        initialVariantQuantities['Default'] = isAvailable ? 1 : 0;
    }

    return {
        product: initialProduct,
        activeImageIndex: 0,
        activeImage: initialProduct.images && initialProduct.images.length > 0 ? initialProduct.images[0] : '',
        selectedColor: initialColor,
        selectedSize: defaultSz,
        variantQuantities: initialVariantQuantities,
        quantity: (isAvailable && initSizeStock > 0) ? 1 : 0,
        accordion1: true,
        accordion2: false,
        accordion3: false,
        sizeModalOpen: false,
        zoomModalOpen: false,
        reviewModalOpen: false,
        isHoverZoom: false,
        zoomX: 50,
        zoomY: 50,

        onImageHover(e) {
            const rect = e.currentTarget.getBoundingClientRect();
            const x = Math.max(0, Math.min(100, ((e.clientX - rect.left) / rect.width) * 100));
            const y = Math.max(0, Math.min(100, ((e.clientY - rect.top) / rect.height) * 100));
            this.zoomX = x.toFixed(2);
            this.zoomY = y.toFixed(2);
            this.isHoverZoom = true;
        },

        onImageLeave() {
            this.isHoverZoom = false;
            this.zoomX = 50;
            this.zoomY = 50;
        },

        get currentSizeStock() {
            return this.getSizeStock(this.selectedSize);
        },

        getSizeStock(sizeName) {
            if (this.product.in_stock === false) return 0;
            if (this.product.variation_stocks && Object.keys(this.product.variation_stocks).length > 0) {
                if (this.selectedColor && this.product.variation_stocks[this.selectedColor] && this.product.variation_stocks[this.selectedColor][sizeName] !== undefined) {
                    return Math.max(0, Number(this.product.variation_stocks[this.selectedColor][sizeName]) || 0);
                }
                if (this.product.size_stocks && this.product.size_stocks[sizeName] !== undefined) {
                    return Math.max(0, Number(this.product.size_stocks[sizeName]) || 0);
                }
                const firstCol = Object.keys(this.product.variation_stocks)[0];
                if (firstCol && this.product.variation_stocks[firstCol][sizeName] !== undefined) {
                    return Math.max(0, Number(this.product.variation_stocks[firstCol][sizeName]) || 0);
                }
                return 0;
            }
            if (this.product.size_stocks && Object.keys(this.product.size_stocks).length > 0) {
                if (this.product.size_stocks[sizeName] !== undefined) {
                    return Math.max(0, Number(this.product.size_stocks[sizeName]) || 0);
                }
                return 0;
            }
            return (this.product.stock !== undefined && this.product.stock !== null) ? Math.max(0, Number(this.product.stock) || 0) : 0;
        },

        getSizePrice(sizeName) {
            if (this.product.variation_prices && this.selectedColor && this.product.variation_prices[this.selectedColor] && this.product.variation_prices[this.selectedColor][sizeName] !== undefined && this.product.variation_prices[this.selectedColor][sizeName] !== null) {
                return Number(this.product.variation_prices[this.selectedColor][sizeName]);
            }
            if (this.product.size_prices && this.product.size_prices[sizeName] !== undefined && this.product.size_prices[sizeName] !== null) {
                return Number(this.product.size_prices[sizeName]);
            }
            if (this.product.variation_prices && Object.keys(this.product.variation_prices).length > 0) {
                const firstCol = Object.keys(this.product.variation_prices)[0];
                if (firstCol && this.product.variation_prices[firstCol][sizeName] !== undefined && this.product.variation_prices[firstCol][sizeName] !== null) {
                    return Number(this.product.variation_prices[firstCol][sizeName]);
                }
            }
            return Number(this.product.price) || 0;
        },

        getSizeOldPrice(sizeName) {
            if (this.product.variation_old_prices && this.selectedColor && this.product.variation_old_prices[this.selectedColor] && this.product.variation_old_prices[this.selectedColor][sizeName] !== undefined && this.product.variation_old_prices[this.selectedColor][sizeName] !== null) {
                return Number(this.product.variation_old_prices[this.selectedColor][sizeName]);
            }
            if (this.product.size_old_prices && this.product.size_old_prices[sizeName] !== undefined && this.product.size_old_prices[sizeName] !== null) {
                return Number(this.product.size_old_prices[sizeName]);
            }
            if (this.product.variation_old_prices && Object.keys(this.product.variation_old_prices).length > 0) {
                const firstCol = Object.keys(this.product.variation_old_prices)[0];
                if (firstCol && this.product.variation_old_prices[firstCol][sizeName] !== undefined && this.product.variation_old_prices[firstCol][sizeName] !== null) {
                    return Number(this.product.variation_old_prices[firstCol][sizeName]);
                }
            }
            return Number(this.product.old_price) || 0;
        },

        get currentPrice() {
            return this.getSizePrice(this.selectedSize);
        },

        get currentOldPrice() {
            return this.getSizeOldPrice(this.selectedSize);
        },

        getVariantQty(sizeName) {
            return this.variantQuantities[sizeName] || 0;
        },

        setVariantQty(sizeName, val) {
            let n = parseInt(val, 10);
            if (isNaN(n) || n < 0) n = 0;
            const maxStock = this.getSizeStock(sizeName);
            if (n > maxStock) {
                n = maxStock;
                const msg = `Maximum available stock for size ${sizeName} is ${maxStock}.`;
                if (window.showRobeToast) window.showRobeToast(msg);
                else if (window.showToast) window.showToast(msg, 'error');
            }
            this.variantQuantities[sizeName] = n;
            if (sizeName === this.selectedSize) {
                this.quantity = n;
            }
        },

        updateVariantQty(sizeName, delta) {
            const current = this.getVariantQty(sizeName);
            const maxStock = this.getSizeStock(sizeName);
            let next = current + delta;
            if (next < 0) next = 0;
            if (next > maxStock) {
                next = maxStock;
                const msg = `Maximum available stock for size ${sizeName} is ${maxStock}.`;
                if (window.showRobeToast) window.showRobeToast(msg);
                else if (window.showToast) window.showToast(msg, 'error');
            }
            this.variantQuantities[sizeName] = next;
            this.selectedSize = sizeName;
            this.quantity = next;
        },

        get totalSelectedQty() {
            if (this.product.sizes && this.product.sizes.length > 0) {
                return Object.values(this.variantQuantities).reduce((acc, q) => acc + (Number(q) || 0), 0);
            }
            return Number(this.quantity) || 0;
        },

        get totalSelectedPrice() {
            if (this.product.sizes && this.product.sizes.length > 0) {
                return Object.keys(this.variantQuantities).reduce((acc, sz) => {
                    const q = Number(this.variantQuantities[sz]) || 0;
                    if (q > 0) {
                        return acc + (q * this.getSizePrice(sz));
                    }
                    return acc;
                }, 0);
            }
            return (Number(this.quantity) || 0) * (this.currentPrice || 0);
        },

        get selectedVariantsList() {
            const list = [];
            if (this.product.sizes && this.product.sizes.length > 0) {
                for (let sz of this.product.sizes) {
                    const q = Number(this.variantQuantities[sz]) || 0;
                    if (q > 0) {
                        list.push({
                            size: sz,
                            color: this.selectedColor,
                            quantity: q,
                            price: this.getSizePrice(sz),
                            old_price: this.getSizeOldPrice(sz),
                            stock: this.getSizeStock(sz)
                        });
                    }
                }
            } else if (this.quantity > 0) {
                list.push({
                    size: 'Default',
                    color: this.selectedColor,
                    quantity: this.quantity,
                    price: this.currentPrice,
                    old_price: this.currentOldPrice,
                    stock: this.currentSizeStock
                });
            }
            return list;
        },

        selectSize(sizeName) {
            this.selectedSize = sizeName;
            const stockLimit = this.getSizeStock(sizeName);
            if (stockLimit <= 0) {
                this.variantQuantities[sizeName] = 0;
                this.quantity = 0;
            } else {
                if (!this.variantQuantities[sizeName] || this.variantQuantities[sizeName] <= 0) {
                    this.variantQuantities[sizeName] = 1;
                }
                this.quantity = this.variantQuantities[sizeName];
            }
        },

        selectColor(colorObj) {
            if (typeof colorObj === 'object' && colorObj !== null) {
                this.selectedColor = colorObj.name;
                if (colorObj.image) {
                    this.activeImage = colorObj.image;
                    const foundIdx = this.product.images.findIndex(img => img === colorObj.image);
                    if (foundIdx !== -1) {
                        this.activeImageIndex = foundIdx;
                    }
                }
            } else {
                this.selectedColor = colorObj;
            }

            // Cap quantities according to new color's stock per size
            if (this.product.sizes && this.product.sizes.length > 0) {
                for (let sz of this.product.sizes) {
                    const maxStock = this.getSizeStock(sz);
                    if ((this.variantQuantities[sz] || 0) > maxStock) {
                        this.variantQuantities[sz] = maxStock;
                    }
                }
            }
        },

        selectImage(index) {
            this.activeImageIndex = index;
            this.activeImage = this.product.images[index] || this.activeImage;
        },

        updateQty(delta) {
            const maxStock = this.currentSizeStock;
            if (this.product.in_stock === false || maxStock <= 0) {
                this.quantity = 0;
                if (window.showRobeToast) window.showRobeToast(`Size "${this.selectedSize}" is currently out of stock.`);
                else if (window.showToast) window.showToast(`Size "${this.selectedSize}" is currently out of stock.`, 'error');
                return;
            }
            let newVal = (this.quantity || 0) + delta;
            if (newVal < 1) {
                this.quantity = 1;
                return;
            }
            if (newVal > maxStock) {
                const msg = `Maximum available stock for size ${this.selectedSize} is ${maxStock}.`;
                if (window.showRobeToast) {
                    window.showRobeToast(msg);
                } else if (window.showToast) {
                    window.showToast(msg, 'error');
                }
                this.quantity = maxStock;
                return;
            }
            this.quantity = newVal;
            if (this.variantQuantities[this.selectedSize] !== undefined) {
                this.variantQuantities[this.selectedSize] = newVal;
            }
        },

        addToCart() {
            const list = this.selectedVariantsList;
            if (list.length === 0) {
                const msg = `Please select at least 1 item/size to add to your bag.`;
                if (window.showRobeToast) window.showRobeToast(msg);
                else if (window.showToast) window.showToast(msg, 'error');
                else alert(msg);
                return false;
            }

            let anySuccess = false;
            for (let v of list) {
                if (v.stock <= 0) continue;
                const item = {
                    id: this.product.id,
                    name: this.product.name,
                    price: v.price,
                    old_price: v.old_price,
                    image: this.activeImage,
                    size: v.size,
                    color: v.color,
                    quantity: v.quantity,
                    stock: v.stock,
                    size_stocks: this.product.size_stocks,
                    size_prices: this.product.size_prices,
                    variation_prices: this.product.variation_prices,
                    in_stock: true
                };

                if (window.Alpine && window.Alpine.store('cart')) {
                    const added = window.Alpine.store('cart').addItem(item);
                    if (added !== false) {
                        anySuccess = true;
                    }
                }
            }

            if (anySuccess) {
                if (window.Alpine && window.Alpine.store('cart')) {
                    window.Alpine.store('cart').toggle(true);
                }
                const summary = list.map(v => `${v.size} (×${v.quantity})`).join(', ');
                if (window.showRobeToast) {
                    window.showRobeToast(`Added to Bag: ${summary}`);
                }
                return true;
            }
            return false;
        },

        buyNow() {
            if (this.addToCart()) {
                window.location.href = "{{ route('checkout') }}";
            }
        },

        submitReview() {
            this.reviewModalOpen = false;
            alert('Thank you! Your verified patron review has been submitted for approval.');
        }
    };
}
</script>
@endsection