@extends('frontend.layouts.master')

@section('title', 'Consignment Confirmation #' . ($order['order_id'] ?? 'KM') . ' | ' . ($comName ?? 'Kingsman') . ' Official Online Store')
@section('meta_description', 'Your bespoke order has been confirmed. Thank you for choosing ' . ($comName ?? 'Kingsman') . ' Atelier Dhaka.')

@push('styles')
<style>
    .font-outfit { font-family: 'Outfit', sans-serif; }
    .bg-deep-noir { background-color: #0B0B0C; }
    .text-gold-metallic { color: #C5A880; }
    .bg-gold-metallic { background-color: #C5A880; }
    .bg-gold-light { background-color: #EFE6DB; }
    .text-status-whatsapp { color: #25D366; }

    @media print {
        header, footer, .no-print {
            display: none !important;
        }
        main {
            padding: 0 !important;
        }
        body {
            background: white !important;
        }
    }
</style>
@endpush

@section('content')
<div class="flex flex-col w-full bg-white font-sans text-[#1a1b22]" 
     x-data="{ init() { if (window.Alpine && window.Alpine.store('cart')) { window.Alpine.store('cart').clear(); } } }">

    <!-- 1. BREADCRUMB RAIL -->
    <section class="w-full bg-[#F5F5F7] border-b border-gray-200/80 py-3.5 no-print">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between text-xs uppercase tracking-wider text-gray-500 font-medium">
            <nav class="flex items-center gap-2">
                <a href="{{ route('home') }}" class="hover:text-black transition-colors">Home</a>
                <span class="text-gray-300">/</span>
                <span class="text-black font-bold">Consignment Confirmed</span>
            </nav>
            <div class="hidden md:flex items-center gap-2 text-xs text-[#25D366] font-bold">
                <span class="material-symbols-outlined text-sm">verified</span>
                <span>Verified Order #{{ $order['order_id'] ?? 'KM-89241' }}</span>
            </div>
        </div>
    </section>

    <!-- 2. MAIN CELEBRATORY ORDER CONFIRMATION -->
    <section class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
        
        <!-- Celebratory Hero Header -->
        <div class="text-center flex flex-col items-center justify-center pb-10 border-b border-gray-200">
            <div class="w-20 h-20 rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center mb-4 shadow-sm animate-pulseSlow">
                <span class="material-symbols-outlined text-4xl">check_circle</span>
            </div>

            <span class="text-xs uppercase font-bold text-[#C5A880] tracking-widest block mb-1">
                Atelier Dhaka • Confirmed Order
            </span>

            <h1 class="font-outfit text-3xl sm:text-4xl font-bold text-black uppercase tracking-tight">
                Thank You, {{ $order['customer_name'] ?? 'Gentleman' }}!
            </h1>

            <p class="text-xs sm:text-sm text-gray-600 max-w-lg mx-auto mt-2 leading-relaxed font-light">
                Your luxury consignment <strong class="text-black">#{{ $order['order_id'] ?? 'KM' }}</strong> has been placed successfully and is being tailored & packed at our Dhaka Atelier.
            </p>

            @php
                $rawWa = function_exists('get_whatsapp_phone') ? get_whatsapp_phone() : ($whatsappNumber ?? ($hotline ?? '01987258406'));
                $confWaDigits = preg_replace('/[^0-9]/', '', $rawWa);
                if (str_starts_with($confWaDigits, '0') && strlen($confWaDigits) === 11) {
                    $confWaDigits = '88' . $confWaDigits;
                } elseif (!str_starts_with($confWaDigits, '88') && !empty($confWaDigits) && strlen($confWaDigits) <= 11) {
                    $confWaDigits = '88' . $confWaDigits;
                }
            @endphp
            <!-- Action buttons header -->
            <div class="flex items-center justify-center gap-4 mt-6 flex-wrap no-print">
                <a href="https://wa.me/{{ $confWaDigits }}?text={{ urlencode('Hello ' . ($comName ?? 'Kingsman') . ' Concierge, I would like to track my order #' . ($order['order_id'] ?? 'KM') . '.') }}" 
                   target="_blank"
                   class="px-6 py-3 bg-[#F5F5F7] hover:bg-gray-200 text-gray-800 hover:text-black text-xs font-bold uppercase tracking-wider transition border border-gray-300 flex items-center gap-2">
                    <span class="material-symbols-outlined text-base text-[#25D366]">chat</span>
                    <span>Track on WhatsApp</span>
                </a>

                <button type="button" 
                        onclick="window.print()"
                        class="px-6 py-3 bg-black hover:bg-neutral-800 text-white text-xs font-bold uppercase tracking-wider transition flex items-center gap-2 shadow-sm">
                    <span class="material-symbols-outlined text-base">print</span>
                    <span>Print Receipt</span>
                </button>
            </div>
        </div>

        <!-- Order Metadata Strip -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-5 bg-[#F5F5F7] border border-gray-200/80 my-8 text-xs">
            <div class="flex flex-col">
                <span class="text-[10px] uppercase font-bold text-gray-400">Order Reference</span>
                <span class="font-outfit font-bold text-black text-sm mt-0.5">#{{ $order['order_id'] ?? 'KM-89241' }}</span>
            </div>
            <div class="flex flex-col">
                <span class="text-[10px] uppercase font-bold text-gray-400">Placed On</span>
                <span class="font-semibold text-black mt-0.5">{{ $order['created_at'] ?? now()->format('d M Y') }}</span>
            </div>
            <div class="flex flex-col">
                <span class="text-[10px] uppercase font-bold text-gray-400">Payment Protocol</span>
                <span class="font-semibold text-black uppercase mt-0.5">
                    @if(($order['payment_method'] ?? '') === 'bkash_nagad')
                        bKash / Mobile Wallet
                    @elseif(($order['payment_method'] ?? '') === 'online_card')
                        Card / Net Banking
                    @else
                        Cash On Delivery
                    @endif
                </span>
            </div>
            <div class="flex flex-col">
                <span class="text-[10px] uppercase font-bold text-gray-400">Payable Total</span>
                <span class="font-outfit font-bold text-black text-base mt-0.5">৳{{ number_format($order['grand_total'] ?? 0) }}</span>
            </div>
        </div>

        <!-- 3. LIVE CONSIGNMENT PROGRESS TRACKER -->
        <div class="bg-white p-6 border border-gray-200/80 mb-8 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-outfit text-sm font-bold uppercase tracking-wider text-black">
                    Live Consignment Status
                </h3>
                <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded border border-emerald-200 flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                    <span>Processing at Atelier</span>
                </span>
            </div>

            <!-- Steps Progress Bar -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 relative">
                <!-- Step 1 -->
                <div class="flex flex-col sm:items-center text-left sm:text-center gap-1.5 relative">
                    <div class="w-9 h-9 rounded-full bg-black text-white flex items-center justify-center text-xs font-bold shadow-sm">
                        <span class="material-symbols-outlined text-base">check</span>
                    </div>
                    <span class="text-xs font-bold text-black uppercase mt-1">Order Received</span>
                    <span class="text-[11px] text-gray-500 font-medium">Verified into System</span>
                </div>

                <!-- Step 2 -->
                <div class="flex flex-col sm:items-center text-left sm:text-center gap-1.5 relative">
                    <div class="w-9 h-9 rounded-full bg-[#C5A880] text-white flex items-center justify-center text-xs font-bold shadow-sm">
                        <span class="material-symbols-outlined text-base animate-spin">sync</span>
                    </div>
                    <span class="text-xs font-bold text-black uppercase mt-1">Atelier Inspection</span>
                    <span class="text-[11px] text-[#725b38] font-medium">Quality Check & Pack</span>
                </div>

                <!-- Step 3 -->
                <div class="flex flex-col sm:items-center text-left sm:text-center gap-1.5 opacity-60">
                    <div class="w-9 h-9 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-xs font-bold">
                        <span class="material-symbols-outlined text-base">local_shipping</span>
                    </div>
                    <span class="text-xs font-bold text-gray-700 uppercase mt-1">Dispatched</span>
                    <span class="text-[11px] text-gray-500">Handed to Courier</span>
                </div>

                <!-- Step 4 -->
                <div class="flex flex-col sm:items-center text-left sm:text-center gap-1.5 opacity-60">
                    <div class="w-9 h-9 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center text-xs font-bold">
                        <span class="material-symbols-outlined text-base">home</span>
                    </div>
                    <span class="text-xs font-bold text-gray-700 uppercase mt-1">Doorstep Delivery</span>
                    <span class="text-[11px] text-gray-500">24-48 Hours Est.</span>
                </div>
            </div>
        </div>

        <!-- 4. ITEMIZED INVOICE TABLE -->
        <div class="bg-[#F5F5F7] p-6 border border-gray-200/80 mb-8 flex flex-col gap-4">
            <h3 class="font-outfit text-base font-bold uppercase tracking-wider text-black border-b border-gray-200 pb-3">
                Itemized Sartorial Consignment
            </h3>

            <div class="divide-y divide-gray-200">
                @if(!empty($order['items']) && is_array($order['items']))
                    @foreach($order['items'] as $item)
                        <div class="py-3.5 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-12 h-16 sm:w-14 sm:h-18 bg-white border border-gray-200 shrink-0 overflow-hidden rounded-xs">
                                    <img src="{{ !empty($item['image']) ? $item['image'] : asset('frontend/images/no-image.svg') }}" alt="{{ $item['name'] ?? '' }}" class="w-full h-full object-cover">
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <h4 class="font-outfit text-xs sm:text-sm font-bold text-black truncate">{{ $item['name'] ?? 'Kingsman Bespoke Garment' }}</h4>
                                    <div class="text-[10px] sm:text-[11px] text-gray-500 font-medium truncate">
                                        <span>Size: {{ $item['size'] ?? '42 (L)' }}</span> • 
                                        <span>Color: {{ $item['color'] ?? 'Noir' }}</span> • 
                                        <span>Qty: {{ $item['quantity'] ?? 1 }}</span>
                                    </div>
                                </div>
                            </div>
                            <span class="font-outfit text-xs sm:text-sm font-bold text-black shrink-0">
                                ৳{{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1)) }}
                            </span>
                        </div>
                    @endforeach
                @else
                    <div class="py-4 text-xs text-gray-500">No items available.</div>
                @endif
            </div>

            <!-- Cost Summary Breakdown -->
            <div class="pt-4 border-t border-gray-300 flex flex-col gap-2 text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-outfit font-bold text-black">৳{{ number_format($order['subtotal'] ?? 0) }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-gray-600">Delivery Fee</span>
                    <span class="font-outfit font-bold text-black">
                        {{ ($order['shipping_charge'] ?? 0) == 0 ? 'FREE' : ('৳' . number_format($order['shipping_charge'])) }}
                    </span>
                </div>

                @if(!empty($order['discount']) && $order['discount'] > 0)
                    <div class="flex items-center justify-between text-red-600 font-bold">
                        <span>Voucher Discount ({{ $order['coupon_code'] ?? 'PROMO' }})</span>
                        <span>-৳{{ number_format($order['discount']) }}</span>
                    </div>
                @endif

                <div class="flex items-baseline justify-between pt-3 border-t border-gray-300">
                    <span class="text-xs uppercase font-bold text-black">Grand Total</span>
                    <span class="font-outfit text-2xl font-bold text-black">৳{{ number_format($order['grand_total'] ?? 0) }}</span>
                </div>
            </div>
        </div>

        <!-- 5. DELIVERY & RECIPIENT DETAILS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6 bg-white border border-gray-200/80 mb-8 shadow-sm">
            <div class="flex flex-col gap-1.5 text-xs">
                <span class="text-[10px] uppercase font-bold text-gray-400">Recipient Contact</span>
                <span class="font-bold text-black text-sm">{{ $order['customer_name'] ?? '' }}</span>
                <span class="text-gray-600">{{ $order['phone'] ?? '' }}</span>
                @if(!empty($order['email']))
                    <span class="text-gray-500">{{ $order['email'] }}</span>
                @endif
            </div>

            <div class="flex flex-col gap-1.5 text-xs">
                <span class="text-[10px] uppercase font-bold text-gray-400">Consignment Destination</span>
                <span class="font-semibold text-black">{{ $order['address'] ?? '' }}</span>
                <span class="text-gray-600">{{ $order['city'] ?? 'Dhaka' }}, {{ $order['division'] ?? 'Dhaka' }}</span>
                @if(!empty($order['notes']))
                    <div class="mt-2 p-2.5 bg-[#F5F5F7] border border-gray-200 text-[11px] text-gray-700">
                        <span class="font-bold block text-black">Tailor/Delivery Note:</span>
                        <span>{{ $order['notes'] }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Bottom Return CTA -->
        <div class="text-center pt-4 no-print">
            <a href="{{ route('home') }}" 
               class="inline-flex items-center gap-2 px-8 py-3.5 bg-black hover:bg-neutral-800 text-white text-xs font-bold uppercase tracking-widest transition shadow-md">
                <span class="material-symbols-outlined text-base">storefront</span>
                <span>Continue Shopping At {{ $comName ?? 'Kingsman' }}</span>
            </a>
        </div>

    </section>

</div>
@endsection