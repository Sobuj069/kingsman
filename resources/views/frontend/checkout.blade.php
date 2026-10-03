@extends('frontend.layouts.master')

@section('title', 'VIP Checkout | ' . ($comName ?? 'Kingsman') . ' Official Online Store')
@section('meta_description', 'Complete your bespoke luxury menswear order with ' . ($comName ?? 'Kingsman') . '. Fast, secure checkout with Cash on Delivery, bKash, and Card payments across Bangladesh.')

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
     x-data="checkoutPageState()">

    <!-- 1. BREADCRUMB RAIL -->
    <section class="w-full bg-[#F5F5F7] border-b border-gray-200/80 py-3.5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between text-xs uppercase tracking-wider text-gray-500 font-medium">
            <nav class="flex items-center gap-2">
                <a href="{{ route('home') }}" class="hover:text-black transition-colors">Home</a>
                <span class="text-gray-300">/</span>
                <a href="{{ route('cart') }}" class="hover:text-black transition-colors">Shopping Bag</a>
                <span class="text-gray-300">/</span>
                <span class="text-black font-bold">Express Checkout</span>
            </nav>
            <div class="hidden md:flex items-center gap-2 text-xs text-[#725b38]">
                <span class="material-symbols-outlined text-sm text-[#C5A880]">lock</span>
                <span>Bank-Grade 256-Bit SSL Encrypted Checkout</span>
            </div>
        </div>
    </section>

    <!-- 2. MAIN CHECKOUT SECTION -->
    <section class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
        
        <!-- Cart empty notice -->
        <template x-if="$store.cart.count === 0">
            <div class="text-center py-16 px-4 bg-[#F5F5F7] border border-gray-200/80 flex flex-col items-center justify-center">
                <div class="w-20 h-20 rounded-full bg-white flex items-center justify-center text-gray-400 mb-4 shadow-sm">
                    <span class="material-symbols-outlined text-4xl">shopping_bag</span>
                </div>
                <h2 class="font-outfit text-2xl font-bold text-black uppercase tracking-tight">Your Sartorial Bag is Empty</h2>
                <p class="text-xs text-gray-500 max-w-sm mt-2 mb-6">Please add items to your shopping bag before proceeding to checkout.</p>
                <a href="{{ route('home') }}" 
                   class="px-8 py-3.5 bg-black hover:bg-neutral-800 text-white text-xs font-bold uppercase tracking-widest transition shadow-md">
                    Return To Collections
                </a>
            </div>
        </template>

        <!-- Checkout Form when cart has items -->
        <template x-if="$store.cart.count > 0">
            <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form" @submit="prepareSubmission($event)">
                @csrf
                
                <!-- Hidden inputs to submit cart payload to backend -->
                <input type="hidden" name="cart_items_json" :value="JSON.stringify($store.cart.items)">
                <input type="hidden" name="subtotal" :value="$store.cart.subtotal">
                <input type="hidden" name="discount_amount" :value="discountAmount">
                <input type="hidden" name="coupon_code" :value="voucherCode">
                <input type="hidden" name="delivery_zone" :value="deliveryZone">
                <input type="hidden" name="payment_method" :value="paymentMethod">

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                    
                    <!-- LEFT COLUMN: CUSTOMER & DELIVERY & PAYMENT (7 cols) -->
                    <div class="lg:col-span-7 flex flex-col gap-8">
                        
                        <!-- Step 1: Customer Contact Details -->
                        <div class="bg-[#F5F5F7] p-4 sm:p-6 border border-gray-200/80 flex flex-col gap-4 rounded-sm">
                            <div class="flex items-center gap-2 border-b border-gray-200 pb-3">
                                <span class="w-6 h-6 rounded-full bg-black text-white text-xs font-bold flex items-center justify-center">1</span>
                                <h3 class="font-outfit text-base font-bold uppercase tracking-wider text-black">
                                    Customer Contact Details
                                </h3>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="flex flex-col gap-1 sm:col-span-2">
                                    <label class="text-xs font-bold uppercase tracking-wider text-gray-700">Full Name <span class="text-red-500">*</span></label>
                                    <input type="text" 
                                           name="customer_name" 
                                           required 
                                           placeholder="e.g. Tasmir Rahman" 
                                           class="bg-white border border-gray-300 text-xs p-3 focus:ring-black focus:border-black">
                                </div>

                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-bold uppercase tracking-wider text-gray-700">Mobile Phone Number <span class="text-red-500">*</span></label>
                                    <input type="tel" 
                                           name="phone" 
                                           required 
                                           placeholder="017XX-XXXXXX" 
                                           class="bg-white border border-gray-300 text-xs p-3 focus:ring-black focus:border-black">
                                </div>

                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-bold uppercase tracking-wider text-gray-700">Email Address (Optional)</label>
                                    <input type="email" 
                                           name="email" 
                                           placeholder="For invoice & courier tracking" 
                                           class="bg-white border border-gray-300 text-xs p-3 focus:ring-black focus:border-black">
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Shipping Address & Region -->
                        <div class="bg-[#F5F5F7] p-4 sm:p-6 border border-gray-200/80 flex flex-col gap-4 rounded-sm">
                            <div class="flex items-center gap-2 border-b border-gray-200 pb-3">
                                <span class="w-6 h-6 rounded-full bg-black text-white text-xs font-bold flex items-center justify-center">2</span>
                                <h3 class="font-outfit text-base font-bold uppercase tracking-wider text-black">
                                    Delivery Address in Bangladesh
                                </h3>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-bold uppercase tracking-wider text-gray-700">Division <span class="text-red-500">*</span></label>
                                    <select name="division" 
                                            @change="handleDivisionChange($event.target.value)"
                                            class="bg-white border border-gray-300 text-xs p-3 focus:ring-black focus:border-black font-medium">
                                        <option value="Dhaka">Dhaka Division</option>
                                        <option value="Chittagong">Chittagong Division</option>
                                        <option value="Sylhet">Sylhet Division</option>
                                        <option value="Rajshahi">Rajshahi Division</option>
                                        <option value="Khulna">Khulna Division</option>
                                        <option value="Barisal">Barisal Division</option>
                                        <option value="Rangpur">Rangpur Division</option>
                                        <option value="Mymensingh">Mymensingh Division</option>
                                    </select>
                                </div>

                                <div class="flex flex-col gap-1">
                                    <label class="text-xs font-bold uppercase tracking-wider text-gray-700">City / District / Area <span class="text-red-500">*</span></label>
                                    <input type="text" 
                                           name="city" 
                                           required 
                                           placeholder="e.g. Banani / Gulshan / Dhanmondi" 
                                           class="bg-white border border-gray-300 text-xs p-3 focus:ring-black focus:border-black">
                                </div>

                                <div class="flex flex-col gap-1 sm:col-span-2">
                                    <label class="text-xs font-bold uppercase tracking-wider text-gray-700">Street Address / House & Road <span class="text-red-500">*</span></label>
                                    <textarea name="address" 
                                              rows="2" 
                                              required 
                                              placeholder="House No., Road No., Sector/Block, Landmark details..." 
                                              class="bg-white border border-gray-300 text-xs p-3 focus:ring-black focus:border-black"></textarea>
                                </div>
                            </div>

                            <!-- Delivery Zone Selection -->
                            <div class="flex flex-col gap-2 pt-2">
                                <label class="text-xs font-bold uppercase tracking-wider text-gray-700">Delivery Speed & Courier:</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    
                                    <label class="p-3 border transition cursor-pointer flex flex-col justify-between"
                                           :class="deliveryZone === 'inside_dhaka' ? 'bg-white border-black ring-1 ring-black' : 'bg-white border-gray-200 hover:border-gray-400'">
                                        <div class="flex items-center justify-between mb-1">
                                            <div class="flex items-center gap-2">
                                                <input type="radio" value="inside_dhaka" x-model="deliveryZone" class="text-black focus:ring-black">
                                                <span class="text-xs font-bold text-black">Inside Dhaka City</span>
                                            </div>
                                            <span class="text-xs font-bold text-black" x-text="$store.cart.subtotal >= 3000 ? 'FREE' : '৳80'"></span>
                                        </div>
                                        <span class="text-[11px] text-gray-500 pl-6">Express 24-48h by Kingsman Concierge</span>
                                    </label>

                                    <label class="p-3 border transition cursor-pointer flex flex-col justify-between"
                                           :class="deliveryZone === 'outside_dhaka' ? 'bg-white border-black ring-1 ring-black' : 'bg-white border-gray-200 hover:border-gray-400'">
                                        <div class="flex items-center justify-between mb-1">
                                            <div class="flex items-center gap-2">
                                                <input type="radio" value="outside_dhaka" x-model="deliveryZone" class="text-black focus:ring-black">
                                                <span class="text-xs font-bold text-black">Outside Dhaka City</span>
                                            </div>
                                            <span class="text-xs font-bold text-black" x-text="$store.cart.subtotal >= 3000 ? 'FREE' : '৳150'"></span>
                                        </div>
                                        <span class="text-[11px] text-gray-500 pl-6">Tracked Nationwide Courier (48-72h)</span>
                                    </label>

                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Payment Method -->
                        <div class="bg-[#F5F5F7] p-4 sm:p-6 border border-gray-200/80 flex flex-col gap-4 rounded-sm">
                            <div class="flex items-center gap-2 border-b border-gray-200 pb-3">
                                <span class="w-6 h-6 rounded-full bg-black text-white text-xs font-bold flex items-center justify-center">3</span>
                                <h3 class="font-outfit text-base font-bold uppercase tracking-wider text-black">
                                    Payment Method
                                </h3>
                            </div>

                            <div class="flex flex-col gap-3">
                                
                                <!-- Payment Option 1: Cash on Delivery -->
                                <label class="p-3.5 sm:p-4 border transition cursor-pointer flex items-start justify-between gap-4"
                                       :class="paymentMethod === 'cash_on_delivery' ? 'bg-white border-black ring-1 ring-black' : 'bg-white border-gray-200 hover:border-gray-400'">
                                    <div class="flex items-start gap-3">
                                        <input type="radio" value="cash_on_delivery" x-model="paymentMethod" class="mt-0.5 text-black focus:ring-black">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold uppercase tracking-wider text-black">Cash On Delivery (COD)</span>
                                            <span class="text-[11px] text-gray-500 mt-0.5">Pay in cash when your bespoke parcel arrives at your doorstep across Bangladesh.</span>
                                        </div>
                                    </div>
                                    <span class="material-symbols-outlined text-[#C5A880] text-xl">payments</span>
                                </label>

                                <!-- Payment Option 2: bKash / Nagad Mobile Banking -->
                                <label class="p-3.5 sm:p-4 border transition cursor-pointer flex items-start justify-between gap-4"
                                       :class="paymentMethod === 'bkash_nagad' ? 'bg-white border-black ring-1 ring-black' : 'bg-white border-gray-200 hover:border-gray-400'">
                                    <div class="flex items-start gap-3">
                                        <input type="radio" value="bkash_nagad" x-model="paymentMethod" class="mt-0.5 text-black focus:ring-black">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold uppercase tracking-wider text-black">bKash / Nagad / Rocket (Mobile Wallet)</span>
                                            <span class="text-[11px] text-gray-500 mt-0.5">Instant automated payment or merchant direct transfer.</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-[10px] font-bold text-gray-400 uppercase">
                                        <span class="px-1.5 py-0.5 bg-pink-100 text-pink-700 rounded font-bold">bKash</span>
                                        <span class="px-1.5 py-0.5 bg-orange-100 text-orange-700 rounded font-bold">Nagad</span>
                                    </div>
                                </label>

                                <!-- Payment Option 3: Credit/Debit Card -->
                                <label class="p-3.5 sm:p-4 border transition cursor-pointer flex items-start justify-between gap-4"
                                       :class="paymentMethod === 'online_card' ? 'bg-white border-black ring-1 ring-black' : 'bg-white border-gray-200 hover:border-gray-400'">
                                    <div class="flex items-start gap-3">
                                        <input type="radio" value="online_card" x-model="paymentMethod" class="mt-0.5 text-black focus:ring-black">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold uppercase tracking-wider text-black">Credit / Debit Card / Net Banking</span>
                                            <span class="text-[11px] text-gray-500 mt-0.5">Visa, MasterCard, American Express, UnionPay & City Bank.</span>
                                        </div>
                                    </div>
                                    <span class="material-symbols-outlined text-gray-600 text-xl">credit_card</span>
                                </label>

                            </div>
                        </div>

                        <!-- Step 4: Special Alteration / Tailor Notes -->
                        <div class="bg-[#F5F5F7] p-4 sm:p-5 border border-gray-200/80 flex flex-col gap-2 rounded-sm">
                            <label class="text-xs font-bold uppercase tracking-wider text-black flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-[#C5A880]">edit_note</span>
                                <span>Order Notes & Tailoring Alteration Request</span>
                            </label>
                            <textarea name="notes" 
                                      rows="2" 
                                      placeholder="Any special timing or fitting requests for our atelier team..." 
                                      class="w-full text-xs p-3 bg-white border border-gray-300 focus:ring-black focus:border-black"></textarea>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: STICKY ORDER REVIEW & CONFIRMATION (5 cols) -->
                    <div class="lg:col-span-5 flex flex-col gap-6 lg:sticky lg:top-24">
                        
                        <div class="bg-[#F5F5F7] p-4 sm:p-6 border border-gray-200/80 flex flex-col gap-5 shadow-sm rounded-sm">
                            <h3 class="font-outfit text-lg font-bold uppercase tracking-wider text-black border-b border-gray-200 pb-3">
                                Consignment Review (<span x-text="$store.cart.count"></span> Items)
                            </h3>

                            <!-- Mini Items Review List -->
                            <div class="max-h-60 overflow-y-auto custom-scrollbar divide-y divide-gray-200 pr-1">
                                <template x-for="item in $store.cart.items" :key="item.id + '_' + item.size + '_' + item.color">
                                    <div class="py-3 flex items-center gap-3">
                                        <div class="w-12 h-16 bg-white shrink-0 overflow-hidden border border-gray-200">
                                            <img :src="item.image" :alt="item.name" class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-xs font-bold text-black truncate" x-text="item.name"></h4>
                                            <div class="text-[10px] text-gray-500">
                                                <span x-text="'Qty: ' + item.quantity"></span> • 
                                                <span x-text="'Size: ' + (item.size || '42')"></span>
                                            </div>
                                            <template x-if="item.in_stock === false || (item.stock !== undefined && item.stock !== null && Number(item.stock) <= 0)">
                                                <span class="text-[9px] font-bold text-rose-600 bg-rose-50 border border-rose-200 px-1 py-0.5 rounded uppercase block mt-0.5">
                                                    Out of Stock (Remove in Bag)
                                                </span>
                                            </template>
                                        </div>
                                        <span class="font-outfit text-xs font-bold text-black" 
                                              x-text="'৳' + (item.price * item.quantity).toLocaleString()"></span>
                                    </div>
                                </template>
                            </div>

                            <!-- Out of Stock Alert -->
                            <template x-if="$store.cart.hasOutOfStock">
                                <div class="p-3 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded flex items-center gap-2">
                                    <span class="material-symbols-outlined text-rose-600 text-base">warning</span>
                                    <span>Some item(s) in your bag are out of stock. Please return to bag and remove them.</span>
                                </div>
                            </template>

                            <!-- Voucher Applicator -->
                            <div class="pt-3 border-t border-gray-200 flex flex-col gap-2">
                                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-600">Have a Privilege Voucher?</span>
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

                            <!-- Cost Breakdown -->
                            <div class="flex flex-col gap-2.5 pt-3 border-t border-gray-200 text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Subtotal</span>
                                    <span class="font-outfit font-bold text-black" x-text="'৳' + $store.cart.subtotal.toLocaleString()"></span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Delivery Fee</span>
                                    <span class="font-outfit font-bold text-black" 
                                          x-text="calculateShipping() === 0 ? 'FREE' : ('৳' + calculateShipping())"></span>
                                </div>

                                <div x-show="discountAmount > 0" class="flex items-center justify-between text-red-600 font-bold">
                                    <span>Voucher Discount</span>
                                    <span x-text="'-৳' + discountAmount.toLocaleString()"></span>
                                </div>

                                <div class="flex items-baseline justify-between pt-3 border-t border-gray-300">
                                    <span class="text-xs uppercase font-bold text-black">Payable Amount</span>
                                    <span class="font-outfit text-2xl font-bold text-black" 
                                          x-text="'৳' + calculateGrandTotal().toLocaleString()"></span>
                                </div>
                            </div>

                            <!-- Terms and Policies Checkbox -->
                            <div class="flex items-start gap-2 pt-1 text-[11px] text-gray-600">
                                <input type="checkbox" required checked class="mt-0.5 text-black focus:ring-black">
                                <span>I agree to {{ $comName ?? 'Kingsman' }}'s <a href="{{ route('frontend.terms') }}" class="underline hover:text-black">Terms of Service</a> & <a href="{{ route('frontend.return-policy') }}" class="underline hover:text-black">7-Day Doorstep Exchange Policy</a>.</span>
                            </div>

                            <!-- Place Order Button -->
                            <template x-if="!$store.cart.hasOutOfStock">
                                <button type="submit" 
                                        :disabled="submitting"
                                        :class="submitting ? 'opacity-70 cursor-wait' : 'hover:bg-black active:scale-[0.99]'"
                                        class="w-full py-4 bg-[#0B0B0C] text-white text-xs md:text-sm font-bold uppercase tracking-widest text-center transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-lg text-[#C5A880]">verified</span>
                                    <span x-text="submitting ? 'Verifying & Placing Order...' : ('Place Consignment • ৳' + calculateGrandTotal().toLocaleString())"></span>
                                </button>
                            </template>
                            <template x-if="$store.cart.hasOutOfStock">
                                <button type="button" 
                                        disabled
                                        class="w-full py-4 bg-neutral-200 text-neutral-400 font-bold uppercase text-xs md:text-sm tracking-widest cursor-not-allowed flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-lg">block</span>
                                    <span>Cannot Order (Out of Stock Items in Bag)</span>
                                </button>
                            </template>

                            <!-- Security strip -->
                            <div class="flex items-center justify-center gap-2 pt-2 text-[10px] text-gray-400 uppercase tracking-wider font-bold">
                                <span class="material-symbols-outlined text-xs text-[#25D366]">shield</span>
                                <span>Doorstep Inspection & Genuine Guarantee</span>
                            </div>
                        </div>

                        @php
                            $chkPhone = $hotline ?? '01987258406';
                            $chkWaDigits = preg_replace('/[^0-9]/', '', $chkPhone);
                            if (!str_starts_with($chkWaDigits, '88') && !empty($chkWaDigits)) {
                                $chkWaDigits = '88' . $chkWaDigits;
                            }
                        @endphp
                        <!-- Atelier Support Widget -->
                        <div class="bg-[#F5F5F7] p-4 border border-gray-200/80 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-base text-status-whatsapp">support_agent</span>
                                <span class="text-gray-700">Need checkout assistance?</span>
                            </div>
                            <a href="https://wa.me/{{ $chkWaDigits }}?text=Hello%20Kingsman%20Concierge,%20I%20need%20assistance%20with%20my%20checkout." target="_blank" class="text-black font-bold uppercase tracking-wider underline hover:text-[#C5A880]">
                                WhatsApp Concierge →
                            </a>
                        </div>

                    </div>
                </div>
            </form>
        </template>
    </section>

</div>

<script>
function checkoutPageState() {
    return {
        deliveryZone: 'inside_dhaka',
        paymentMethod: 'cash_on_delivery',
        voucherCode: '',
        voucherApplied: false,
        voucherMessage: '',
        discountAmount: 0,

        handleDivisionChange(div) {
            if (div === 'Dhaka') {
                this.deliveryZone = 'inside_dhaka';
            } else {
                this.deliveryZone = 'outside_dhaka';
            }
        },

        applyVoucher() {
            const code = this.voucherCode.trim().toUpperCase();
            if (code === 'KINGSMAN10' || code === 'ROBE10') {
                this.discountAmount = Math.round(window.Alpine.store('cart').subtotal * 0.10);
                this.voucherApplied = true;
                this.voucherMessage = 'KINGSMAN10 Applied: 10% Royal Privilege!';
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

        calculateShipping() {
            const sub = window.Alpine ? window.Alpine.store('cart').subtotal : 0;
            if (sub >= 3000 || sub === 0) return 0;
            return this.deliveryZone === 'inside_dhaka' ? 80 : 150;
        },

        calculateGrandTotal() {
            const sub = window.Alpine ? window.Alpine.store('cart').subtotal : 0;
            return Math.max(0, sub + this.calculateShipping() - this.discountAmount);
        },

        submitting: false,

        async prepareSubmission(e) {
            e.preventDefault();
            if (this.submitting) return;

            const cartStore = window.Alpine ? window.Alpine.store('cart') : null;
            if (!cartStore || cartStore.count === 0) {
                alert('Your shopping bag is empty. Please add items before checking out.');
                return;
            }

            if (cartStore.hasOutOfStock) {
                alert('Some items in your shopping bag are out of stock. Please return to bag and remove them.');
                return;
            }

            this.submitting = true;

            try {
                const response = await fetch("{{ route('cart.validate-stock') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ items: cartStore.items })
                });

                const resData = await response.json();
                if (!resData.valid) {
                    this.submitting = false;
                    const errItem = resData.items.find(i => !i.in_stock || i.error);
                    const errMsg = errItem ? errItem.error : resData.message;
                    alert(errMsg || 'Some items in your shopping bag are currently out of stock.');
                    return;
                }

                // If completely valid, submit form
                document.getElementById('checkout-form').submit();
            } catch (err) {
                // Fallback to submit
                document.getElementById('checkout-form').submit();
            }
        }
    };
}
</script>
@endsection
