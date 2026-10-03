@extends('frontend.layouts.master')

@section('title', 'Refund Policy | ' . ($comName ?? 'Kingsman') . ' Official')

@section('content')
<!-- Page Header -->
<section class="bg-neutral-950 text-white py-12 md:py-16 border-b border-neutral-800 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <nav class="flex items-center justify-center gap-2 text-xs uppercase tracking-widest text-neutral-400 mb-4">
            <a href="{{ route('home') }}" class="hover:text-white transition">Home</a>
            <span>/</span>
            <span class="text-red-500 font-semibold">Refund Policy</span>
        </nav>
        
        <span class="text-xs uppercase font-bold tracking-[0.3em] text-[#C5A880] block mb-2">Fair &amp; Prompt Resolutions</span>
        <h1 class="font-outfit text-3xl sm:text-5xl font-black uppercase tracking-tight text-white mb-3">
            Refund Policy
        </h1>
        <p class="text-xs sm:text-sm text-neutral-300 max-w-xl mx-auto font-light leading-relaxed">
            At {{ $comName ?? 'Kingsman' }}, our highest priority is your utmost satisfaction. Read our clear, structured monetary refund guidelines below.
        </p>
    </div>
</section>

<!-- Content Section -->
<section class="py-14 md:py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        
        @php
            $refPhone = $hotline ?? '01987258406';
            $refWaDigits = preg_replace('/[^0-9]/', '', $refPhone);
            if (!str_starts_with($refWaDigits, '88') && !empty($refWaDigits)) {
                $refWaDigits = '88' . $refWaDigits;
            }
        @endphp
        <!-- Quick Timeline Badges -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-5 rounded-xl border border-neutral-200 bg-neutral-50 text-center">
                <div class="w-10 h-10 rounded-full bg-pink-100 text-pink-600 flex items-center justify-center mx-auto mb-2 font-bold text-base">
                    <i class="fa-solid fa-mobile-screen-button"></i>
                </div>
                <h4 class="font-bold text-xs uppercase tracking-wider text-neutral-900 mb-1">bKash / Nagad</h4>
                <p class="text-xs text-neutral-600 font-semibold">3 – 5 Business Days</p>
            </div>

            <div class="p-5 rounded-xl border border-neutral-200 bg-neutral-50 text-center">
                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mx-auto mb-2 font-bold text-base">
                    <i class="fa-solid fa-credit-card"></i>
                </div>
                <h4 class="font-bold text-xs uppercase tracking-wider text-neutral-900 mb-1">Bank / Cards</h4>
                <p class="text-xs text-neutral-600 font-semibold">7 – 10 Business Days</p>
            </div>

            <div class="p-5 rounded-xl border border-neutral-200 bg-neutral-50 text-center">
                <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center mx-auto mb-2 font-bold text-base">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
                <h4 class="font-bold text-xs uppercase tracking-wider text-neutral-900 mb-1">Cash on Delivery</h4>
                <p class="text-xs text-neutral-600 font-semibold">MFS Refund Upon Return</p>
            </div>
        </div>

        <!-- 1. When You Are Eligible For a Refund -->
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-full bg-neutral-900 text-white flex items-center justify-center font-bold text-xs">1</span>
                <h2 class="font-outfit text-xl sm:text-2xl font-bold text-neutral-900">Eligibility for Monetary Refund</h2>
            </div>
            <div class="pl-11 space-y-3 text-xs sm:text-sm text-neutral-600 leading-relaxed">
                <p>A full or partial monetary refund is issued under the following circumstances:</p>
                <ul class="list-disc pl-5 space-y-2">
                    <li><strong class="text-neutral-900">Prepaid Order Cancellation:</strong> If you cancel an order paid via bKash/Card before the parcel is dispatched from our central atelier.</li>
                    <li><strong class="text-neutral-900">Out-of-Stock Items:</strong> If an item paid in advance is found to be unavailable in our inventory during packaging.</li>
                    <li><strong class="text-neutral-900">Defective / Damaged Item:</strong> If you receive a damaged garment and we do not have an identical replacement piece in stock.</li>
                    <li><strong class="text-neutral-900">Unfulfilled Delivery:</strong> If a prepaid parcel fails to be delivered due to logistical errors on our part.</li>
                </ul>
            </div>
        </div>

        <!-- 2. Inspection & Return Conditions -->
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-full bg-neutral-900 text-white flex items-center justify-center font-bold text-xs">2</span>
                <h2 class="font-outfit text-xl sm:text-2xl font-bold text-neutral-900">Quality Inspection &amp; Approval</h2>
            </div>
            <div class="pl-11 space-y-3 text-xs sm:text-sm text-neutral-600 leading-relaxed">
                <p>
                    For returned products, the refund process initiates immediately after our quality assurance team inspects the returned parcel at our central warehouse.
                </p>
                <ul class="list-disc pl-5 space-y-2">
                    <li>The product must be unworn, unwashed, unaltered, and with all original tags attached.</li>
                    <li>Once approved by QA (usually within 24 hours of receiving the return parcel), the finance team processes the payment.</li>
                </ul>
            </div>
        </div>

        <!-- 3. Delivery Fee Rules -->
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-full bg-neutral-900 text-white flex items-center justify-center font-bold text-xs">3</span>
                <h2 class="font-outfit text-xl sm:text-2xl font-bold text-neutral-900">Shipping Charges Policy</h2>
            </div>
            <div class="pl-11 space-y-3 text-xs sm:text-sm text-neutral-600 leading-relaxed">
                <ul class="list-disc pl-5 space-y-2">
                    <li>If the return is caused by our fault (wrong product, defective fabric, or sizing mismatch from our end), <strong class="text-green-700 font-bold">100% of the shipping cost is refunded</strong>.</li>
                    <li>If an order is returned due to customer change of mind or unreachable contact at delivery time, the third-party courier delivery fee (৳70 Inside Dhaka / ৳130 Outside Dhaka) will be deducted from the refunded sum.</li>
                </ul>
            </div>
        </div>

        <!-- 4. How to Request a Refund -->
        <div class="p-6 sm:p-8 rounded-2xl bg-neutral-950 text-white">
            <h3 class="font-outfit text-lg font-bold mb-3">How to Initiate a Refund Request</h3>
            <p class="text-xs sm:text-sm text-neutral-300 mb-6 leading-relaxed">
                Please provide your <strong>Order ID</strong>, <strong>Reason for Refund</strong>, and your preferred <strong>bKash / Nagad / Bank Account Number</strong> to our concierge team:
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="https://wa.me/{{ $refWaDigits }}?text=Hello%20Kingsman%20Concierge,%20I%20would%20like%20to%20request%20a%20refund." target="_blank" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white text-xs font-bold uppercase tracking-wider rounded-lg transition flex items-center gap-2">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>WhatsApp Concierge ({{ $refPhone }})</span>
                </a>
                <a href="mailto:{{ $comEmail ?? 'info@kingsman.com.bd' }}" class="px-6 py-3 bg-neutral-800 hover:bg-neutral-700 text-white text-xs font-bold uppercase tracking-wider rounded-lg transition flex items-center gap-2">
                    <i class="fa-solid fa-envelope text-sm"></i>
                    <span>Email: {{ $comEmail ?? 'info@kingsman.com.bd' }}</span>
                </a>
            </div>
        </div>

    </div>
</section>
@endsection
