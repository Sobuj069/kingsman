@extends('frontend.layouts.master')

@section('title', 'Your Sartorial Shopping Bag | ' . ($comName ?? 'Kingsman') . ' Official Online Store')
@section('meta_description', 'Review your selected bespoke luxury Panjabi, Kabli Sets, and tailoring garments in your ' . ($comName ?? 'Kingsman') . ' shopping bag. Complimentary express delivery across Bangladesh.')

@push('styles')
<style>
    .font-outfit { font-family: 'Outfit', sans-serif; }
    .bg-deep-noir { background-color: #0B0B0C; }
    .text-gold-metallic { color: #C5A880; }
    .bg-gold-metallic { background-color: #C5A880; }
    .bg-gold-light { background-color: #EFE6DB; }
    .text-status-whatsapp { color: #25D366; }
</style>
@endpush

@section('content')
<div class="flex flex-col w-full bg-white font-sans text-[#1a1b22]" 
     x-data="cartPageState()">

    <!-- 1. BREADCRUMB RAIL -->
    <section class="w-full bg-[#F5F5F7] border-b border-gray-200/80 py-3.5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between text-xs uppercase tracking-wider text-gray-500 font-medium">
            <nav class="flex items-center gap-2">
                <a href="{{ route('home') }}" class="hover:text-black transition-colors">Home</a>
                <span class="text-gray-300">/</span>
                <span class="text-black font-bold">Shopping Bag (<span x-text="$store.cart.count"></span>)</span>
            </nav>
            <div class="hidden md:flex items-center gap-2 text-xs text-[#725b38]">
                <span class="material-symbols-outlined text-sm text-[#C5A880]">lock</span>
                <span>256-Bit SSL Encrypted Sartorial Checkout</span>
            </div>
        </div>
    </section>

    <!-- 2. MAIN SHOPPING BAG CONTENT -->
    <section class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
            
            <!-- LEFT COLUMN: CART ITEMS LIST (8 cols) -->
            <div class="lg:col-span-8 flex flex-col gap-6">
                
                <!-- If Cart is Empty -->
                <template x-if="$store.cart.count === 0">
                    <div class="text-center py-16 px-4 bg-[#F5F5F7] border border-gray-200/80 flex flex-col items-center justify-center">
                        <div class="w-20 h-20 rounded-full bg-white flex items-center justify-center text-gray-400 mb-4 shadow-sm">
                            <span class="material-symbols-outlined text-4xl">shopping_bag</span>
                        </div>
                        <h2 class="font-outfit text-2xl font-bold text-black uppercase tracking-tight">Your Sartorial Bag is Empty</h2>
                        <p class="text-xs text-gray-500 max-w-sm mt-2 mb-6">Explore our latest festive arrivals, royal Kabli sets, and Egyptian cotton Panjabi garments.</p>
                        <a href="{{ route('home') }}" 
                           class="px-8 py-3.5 bg-black hover:bg-neutral-800 text-white text-xs font-bold uppercase tracking-widest transition shadow-md">
                            Discover New Arrivals
                        </a>
                    </div>
                </template>

                <!-- If Cart has items -->
                <template x-if="$store.cart.count > 0">
                    <div class="flex flex-col gap-6">
                        
                        <!-- Desktop Header Strip -->
                        <div class="hidden sm:grid grid-cols-12 gap-4 pb-3 border-b border-gray-200 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                            <div class="col-span-6">Garment & Specifications</div>
                            <div class="col-span-2 text-center">Unit Price</div>
                            <div class="col-span-2 text-center">Quantity</div>
                            <div class="col-span-2 text-right">Line Total</div>
                        </div>

                        <!-- Item Rows -->
                        <div class="divide-y divide-gray-200 border-b border-gray-200">
                            <template x-for="(item, index) in $store.cart.items" :key="item.id + '_' + (item.size || 'M') + '_' + (item.color || 'Def') + '_' + index">
                                <div class="py-4 sm:py-5 flex flex-col sm:grid sm:grid-cols-12 gap-3 sm:gap-4 items-start sm:items-center">
                                    
                                    <!-- Product Info & Thumb (6 cols) -->
                                    <div class="w-full sm:col-span-6 flex items-start gap-3.5">
                                        <div class="w-16 h-20 sm:w-20 sm:h-24 bg-[#F5F5F7] shrink-0 overflow-hidden border border-gray-200 relative rounded-sm">
                                            <img :src="item.image" :alt="item.name" class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex flex-col gap-1 min-w-0 flex-1">
                                            <a :href="'/product/' + item.id" 
                                               class="font-outfit text-xs sm:text-sm font-bold text-black hover:text-red-600 transition truncate" 
                                               x-text="item.name"></a>
                                            <div class="flex items-center gap-1.5 flex-wrap text-[10px] sm:text-[11px] text-gray-500 font-medium">
                                                <span class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-700" x-text="'Size: ' + (item.size || '42')"></span>
                                                <span class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-700" x-text="'Color: ' + (item.color || 'Noir')"></span>
                                            </div>
                                            <template x-if="item.in_stock === false || (item.stock !== undefined && item.stock !== null && Number(item.stock) <= 0)">
                                                <span class="text-[9px] font-bold text-rose-600 bg-rose-50 border border-rose-200 px-1.5 py-0.5 rounded uppercase mt-1 inline-block">
                                                    Out of Stock (Please remove)
                                                </span>
                                            </template>
                                            <button type="button" 
                                                    @click.stop="$store.cart.removeItem(item.id, item.size, item.color)"
                                                    class="text-[10px] sm:text-[11px] text-red-600 hover:text-red-800 font-semibold uppercase tracking-wider text-left flex items-center gap-1 mt-0.5 transition cursor-pointer">
                                                <span class="material-symbols-outlined text-xs sm:text-sm">delete</span>
                                                <span>Remove</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Mobile Price, Quantity & Subtotal Row (6 cols on Desktop) -->
                                    <div class="w-full sm:col-span-6 flex items-center justify-between sm:grid sm:grid-cols-6 gap-2 pt-2 sm:pt-0 border-t border-gray-100 sm:border-0">
                                        <!-- Unit Price (2 cols) -->
                                        <div class="sm:col-span-2 text-left sm:text-center">
                                            <span class="font-outfit text-xs sm:text-sm font-bold text-black" x-text="'৳' + Number(item.price).toLocaleString()"></span>
                                            <template x-if="item.old_price && item.old_price > item.price">
                                                <span class="text-[9px] sm:text-[10px] text-gray-400 line-through block" x-text="'৳' + Number(item.old_price).toLocaleString()"></span>
                                            </template>
                                        </div>

                                        <!-- Quantity Modifier (2 cols) -->
                                        <div class="sm:col-span-2 flex items-center justify-center">
                                            <div class="flex items-center border border-gray-300 bg-[#F5F5F7] rounded-xs">
                                                <button type="button" 
                                                        @click="$store.cart.updateQuantity(item.id, item.quantity - 1, item.size, item.color)"
                                                        :disabled="item.quantity <= 1"
                                                        class="w-7 h-7 flex items-center justify-center text-gray-700 hover:text-black font-bold text-xs disabled:opacity-40 disabled:cursor-not-allowed">
                                                    -
                                                </button>
                                                <span class="w-7 text-center text-xs font-bold text-black" x-text="item.quantity"></span>
                                                <button type="button" 
                                                        @click="$store.cart.updateQuantity(item.id, item.quantity + 1, item.size, item.color)"
                                                        :disabled="item.stock !== undefined && item.stock !== null && item.quantity >= Number(item.stock)"
                                                        class="w-7 h-7 flex items-center justify-center text-gray-700 hover:text-black font-bold text-xs disabled:opacity-40 disabled:cursor-not-allowed">
                                                    +
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Line Total (2 cols) -->
                                        <div class="sm:col-span-2 text-right">
                                            <span class="font-outfit text-xs sm:text-base font-bold text-black" 
                                                  x-text="'৳' + (item.price * item.quantity).toLocaleString()"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Actions Bar -->
                        <div class="flex items-center justify-between flex-wrap gap-4 pt-2">
                            <a href="{{ route('home') }}" 
                               class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-black hover:text-[#C5A880] transition">
                                <span class="material-symbols-outlined text-base">arrow_back</span>
                                <span>Continue Browsing Collections</span>
                            </a>
                            <button type="button" 
                                    @click="if(confirm('Are you sure you want to empty your shopping bag?')) $store.cart.clear()"
                                    class="text-xs font-bold uppercase tracking-wider text-gray-400 hover:text-red-600 transition">
                                Clear All Items
                            </button>
                        </div>

                        <!-- Tailoring Notes & Instructions -->
                        <div class="p-5 bg-[#F5F5F7] border border-gray-200/80 flex flex-col gap-2 mt-4">
                            <label class="text-xs font-bold uppercase tracking-wider text-black flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-[#C5A880]">content_cut</span>
                                <span>Special Atelier Tailoring Alterations / Order Notes</span>
                            </label>
                            <textarea rows="2" 
                                      placeholder="Mention any specific collar height, sleeve length hem drop, or special gift message here..."
                                      class="w-full text-xs p-3 border-gray-300 focus:ring-black focus:border-black bg-white"></textarea>
                        </div>
                    </div>
                </template>
            </div>

            <!-- RIGHT COLUMN: ORDER SUMMARY CARD (4 cols) -->
            <div class="lg:col-span-4 flex flex-col gap-6 lg:sticky lg:top-24">
                
                <div class="bg-[#F5F5F7] p-6 border border-gray-200/80 flex flex-col gap-5 shadow-sm">
                    <h3 class="font-outfit text-lg font-bold uppercase tracking-wider text-black border-b border-gray-200 pb-3">
                        Order Summary
                    </h3>

                    <!-- Subtotal -->
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-600 uppercase font-semibold">Subtotal</span>
                        <span class="font-outfit text-base font-bold text-black" x-text="'৳' + $store.cart.subtotal.toLocaleString()"></span>
                    </div>

                    <!-- Delivery Option Selector -->
                    <div class="flex flex-col gap-2 text-xs border-t border-b border-gray-200 py-3">
                        <span class="text-gray-600 uppercase font-semibold block">Select Delivery Location:</span>
                        <label class="flex items-center justify-between cursor-pointer p-2 bg-white border border-gray-200">
                            <div class="flex items-center gap-2">
                                <input type="radio" name="delivery" value="inside" x-model="deliveryZone" class="text-black focus:ring-black">
                                <span>Inside Dhaka City (24-48h)</span>
                            </div>
                            <span class="font-bold text-black" x-text="$store.cart.subtotal >= 3000 ? 'FREE' : '৳80'"></span>
                        </label>
                        <label class="flex items-center justify-between cursor-pointer p-2 bg-white border border-gray-200">
                            <div class="flex items-center gap-2">
                                <input type="radio" name="delivery" value="outside" x-model="deliveryZone" class="text-black focus:ring-black">
                                <span>Outside Dhaka City (Tracked)</span>
                            </div>
                            <span class="font-bold text-black" x-text="$store.cart.subtotal >= 3000 ? 'FREE' : '৳150'"></span>
                        </label>
                    </div>

                    <!-- Voucher Applicator -->
                    <div class="flex flex-col gap-2">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-600">Promo Voucher</span>
                        <div class="flex gap-2">
                            <input type="text" 
                                   x-model="voucherCode" 
                                   placeholder="e.g. KINGSMAN10 / EID2026" 
                                   class="flex-1 bg-white border border-gray-300 text-xs p-2 uppercase focus:ring-black focus:border-black">
                            <button type="button" 
                                    @click="applyVoucher()"
                                    class="px-4 py-2 bg-black text-white text-xs font-bold uppercase tracking-wider hover:bg-neutral-800 transition">
                                Apply
                            </button>
                        </div>
                        <span x-show="voucherApplied" class="text-[11px] text-emerald-600 font-bold flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs">check_circle</span>
                            <span x-text="voucherMessage"></span>
                        </span>
                    </div>

                    <!-- Discount (if applied) -->
                    <div x-show="discountAmount > 0" class="flex items-center justify-between text-xs text-red-600 font-bold">
                        <span>Voucher Discount</span>
                        <span x-text="'-৳' + discountAmount.toLocaleString()"></span>
                    </div>

                    <!-- Grand Total -->
                    <div class="flex items-baseline justify-between pt-2 border-t border-gray-300">
                        <span class="text-xs uppercase font-bold text-black">Estimated Total</span>
                        <span class="font-outfit text-2xl font-bold text-black" 
                              x-text="'৳' + calculateGrandTotal().toLocaleString()"></span>
                    </div>

                    <!-- Out of Stock Warning -->
                    <template x-if="$store.cart.hasOutOfStock">
                        <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded flex items-center gap-2">
                            <span class="material-symbols-outlined text-rose-600 text-base">warning</span>
                            <span>Some item(s) in your bag are out of stock. Please remove them before checkout.</span>
                        </div>
                    </template>

                    <!-- Checkout CTA Button -->
                    <template x-if="!$store.cart.hasOutOfStock">
                        <a href="{{ route('checkout') }}" 
                           class="w-full py-4 bg-[#0B0B0C] hover:bg-black text-white text-xs md:text-sm font-bold uppercase tracking-widest text-center transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2 active:scale-[0.99]">
                            <span class="material-symbols-outlined text-lg">lock</span>
                            <span>Proceed To Secure Checkout</span>
                        </a>
                    </template>
                    <template x-if="$store.cart.hasOutOfStock">
                        <button type="button" 
                                disabled
                                class="w-full py-4 bg-neutral-200 text-neutral-400 font-bold uppercase text-xs md:text-sm tracking-widest cursor-not-allowed flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">block</span>
                            <span>Cannot Checkout (Out of Stock Items)</span>
                        </button>
                    </template>

                    <!-- Fast Payment Badges -->
                    <div class="flex items-center justify-center gap-2 pt-2 text-[10px] text-gray-400 font-bold uppercase">
                        <span>bKash</span>
                        <span>•</span>
                        <span>Nagad</span>
                        <span>•</span>
                        <span>Visa / Master</span>
                        <span>•</span>
                        <span>Cash On Delivery</span>
                    </div>
                </div>

                <!-- Sartorial Assurances Strip -->
                <div class="bg-[#F5F5F7] p-4 flex flex-col gap-3 border border-gray-200/80 text-xs text-gray-600">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-base text-[#C5A880]">verified</span>
                        <span>100% Genuine Atelier Craft & Egyptian Giza Cotton</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-base text-[#C5A880]">published_with_changes</span>
                        <span>7-Day Hassle-Free Doorstep Size Exchange</span>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined text-base text-status-whatsapp">support_agent</span>
                        <span>Live WhatsApp Concierge: {{ $hotline ?? '+880 1987-258406' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. CURATED PAIRINGS SECTION -->
        @if(!empty($pairings) && count($pairings) > 0)
        <div class="mt-16 pt-12 border-t border-gray-200">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <span class="text-xs font-bold text-[#C5A880] uppercase tracking-widest block mb-1">Style Harmony</span>
                    <h3 class="font-outfit text-2xl font-bold uppercase tracking-tight text-black">Recommended Atelier Pairings</h3>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                @foreach($pairings as $pair)
                    <div class="bg-[#F5F5F7] p-3 sm:p-4 border border-gray-200/80 flex flex-col justify-between group">
                        <div>
                            <div class="aspect-[3/4] bg-white overflow-hidden mb-3">
                                <img src="{{ $pair['image'] }}" alt="{{ $pair['name'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </div>
                            <span class="text-[10px] uppercase font-bold text-gray-400 block">{{ $pair['category'] }}</span>
                            <h4 class="text-xs sm:text-sm font-bold text-black line-clamp-1 mt-0.5">{{ $pair['name'] }}</h4>
                        </div>
                        <div class="flex items-center justify-between mt-3 pt-2 border-t border-gray-200">
                            <span class="font-outfit text-xs sm:text-sm font-bold text-black">৳{{ number_format($pair['price']) }}</span>
                            <button type="button" 
                                    @click="$store.cart.addItem({ id: {{ $pair['id'] }}, name: '{{ addslashes($pair['name']) }}', price: {{ $pair['price'] }}, image: '{{ $pair['image'] }}', size: 'Standard', color: 'Default' }); $store.cart.toggle(true)"
                                    class="w-7 h-7 bg-white hover:bg-black text-black hover:text-white flex items-center justify-center transition shadow-sm border border-gray-200">
                                <span class="material-symbols-outlined text-xs">add</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </section>

</div>

<script>
function cartPageState() {
    return {
        deliveryZone: 'inside',
        voucherCode: '',
        voucherApplied: false,
        voucherMessage: '',
        discountAmount: 0,

        applyVoucher() {
            const code = this.voucherCode.trim().toUpperCase();
            if (code === 'KINGSMAN10' || code === 'ROBE10') {
                this.discountAmount = Math.round(window.Alpine.store('cart').subtotal * 0.10);
                this.voucherApplied = true;
                this.voucherMessage = 'KINGSMAN10 Applied: 10% Royal Discount!';
            } else if (code === 'EID2026') {
                this.discountAmount = Math.round(window.Alpine.store('cart').subtotal * 0.15);
                this.voucherApplied = true;
                this.voucherMessage = 'EID2026 Applied: 15% Festive Privilege!';
            } else {
                alert('Invalid Voucher Code. Try "KINGSMAN10" or "EID2026"');
                this.discountAmount = 0;
                this.voucherApplied = false;
            }
        },

        calculateGrandTotal() {
            const sub = window.Alpine ? window.Alpine.store('cart').subtotal : 0;
            const shipping = (sub >= 3000 || sub === 0) ? 0 : (this.deliveryZone === 'inside' ? 80 : 150);
            return Math.max(0, sub + shipping - this.discountAmount);
        }
    };
}
</script>
@endsection