@extends('frontend.layouts.master')

@section('title', 'Return & Exchange Policy | ' . ($comName ?? 'Kingsman') . ' Official')

@section('content')
<!-- Page Header -->
<section class="bg-neutral-950 text-white py-12 md:py-16 border-b border-neutral-800 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <nav class="flex items-center justify-center gap-2 text-xs uppercase tracking-widest text-neutral-400 mb-4">
            <a href="{{ route('home') }}" class="hover:text-white transition">Home</a>
            <span>/</span>
            <span class="text-red-500 font-semibold">Return &amp; Exchange Policy</span>
        </nav>
        
        <span class="text-xs uppercase font-bold tracking-[0.3em] text-[#C5A880] block mb-2">Customer First Guarantee</span>
        <h1 class="font-outfit text-3xl sm:text-5xl font-black uppercase tracking-tight text-white mb-3">
            Return &amp; Exchange Policy
        </h1>
        <p class="text-xs sm:text-sm text-neutral-300 max-w-xl mx-auto font-light leading-relaxed">
            We want you to be completely delighted with your {{ $comName ?? 'Kingsman' }} garments. Review our seamless 7-day return and sizing exchange guidelines below.
        </p>
    </div>
</section>

<!-- Content Section -->
<section class="py-14 md:py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        
        @php
            $retPhone = $hotline ?? '01987258406';
            $retWaDigits = preg_replace('/[^0-9]/', '', $retPhone);
            if (!str_starts_with($retWaDigits, '88') && !empty($retWaDigits)) {
                $retWaDigits = '88' . $retWaDigits;
            }
        @endphp
        <!-- Highlight Box -->
        <div class="p-6 sm:p-8 rounded-2xl bg-amber-50/70 border border-amber-200/80 flex flex-col sm:flex-row items-start sm:items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-amber-500 text-white flex items-center justify-center text-2xl flex-shrink-0">
                <i class="fa-solid fa-arrow-rotate-left"></i>
            </div>
            <div>
                <h3 class="font-outfit text-lg font-bold text-amber-950 mb-1">7 Days Hassle-Free Exchange Window</h3>
                <p class="text-xs sm:text-sm text-amber-900/80 leading-relaxed">
                    You can exchange any garment for a different size, color, or alternative design within <strong class="font-bold">7 calendar days</strong> from the date of package delivery.
                </p>
            </div>
        </div>

        <!-- 1. Eligibility Criteria -->
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-full bg-neutral-900 text-white flex items-center justify-center font-bold text-xs">1</span>
                <h2 class="font-outfit text-xl sm:text-2xl font-bold text-neutral-900">Eligibility &amp; Conditions</h2>
            </div>
            <div class="pl-11 space-y-3 text-xs sm:text-sm text-neutral-600 leading-relaxed">
                <p>To qualify for a valid return or size exchange, the item must fulfill the following criteria:</p>
                <ul class="list-disc pl-5 space-y-2">
                    <li>The product must be in its <strong class="text-neutral-900">original, unworn, unwashed, and undamaged</strong> condition.</li>
                    <li>All original brand tags, barcodes, garment poly bags, and luxury boxes must remain intact and attached.</li>
                    <li>A valid proof of purchase (Order ID, Invoice number, or registered phone number) must be provided.</li>
                    <li>Items showing perfume scents, deodorant stains, altered stitching, or fabric damage will not be eligible.</li>
                </ul>
            </div>
        </div>

        <!-- 2. Defective or Wrong Item Received -->
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-full bg-neutral-900 text-white flex items-center justify-center font-bold text-xs">2</span>
                <h2 class="font-outfit text-xl sm:text-2xl font-bold text-neutral-900">Defective or Incorrect Item Replacement</h2>
            </div>
            <div class="pl-11 space-y-3 text-xs sm:text-sm text-neutral-600 leading-relaxed">
                <p>
                    In the rare event that you receive a defective item, a misprinted garment, or an incorrect size/color shipped by mistake:
                </p>
                <ul class="list-disc pl-5 space-y-2">
                    <li><strong class="text-neutral-900">{{ $comName ?? 'Kingsman' }} will cover 100% of the return and replacement courier shipping charges.</strong></li>
                    <li>Please notify our customer concierge within <strong class="text-neutral-900">48 hours</strong> of parcel delivery via WhatsApp at <a href="https://wa.me/{{ $retWaDigits }}?text=Hello%20Kingsman%20Concierge,%20I%20received%20a%20defective/incorrect%20item." class="text-green-600 font-bold underline">{{ $retPhone }}</a> along with photos/video of the issue.</li>
                    <li>An immediate priority replacement will be dispatched to your doorstep.</li>
                </ul>
            </div>
        </div>

        <!-- 3. How to Exchange Step by Step -->
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-full bg-neutral-900 text-white flex items-center justify-center font-bold text-xs">3</span>
                <h2 class="font-outfit text-xl sm:text-2xl font-bold text-neutral-900">How to Process Your Exchange</h2>
            </div>
            <div class="pl-11 grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <div class="p-5 rounded-xl border border-neutral-200 bg-neutral-50/50">
                    <span class="text-xs font-bold uppercase text-red-600 tracking-wider block mb-1">Option A</span>
                    <h4 class="font-bold text-sm text-neutral-900 mb-2">Showroom Walk-In Exchange (Fastest)</h4>
                    <p class="text-xs text-neutral-600 leading-relaxed">
                        Bring your product with the original invoice and tags to any of our physical showrooms for instant on-the-spot sizing exchange.
                    </p>
                </div>

                <div class="p-5 rounded-xl border border-neutral-200 bg-neutral-50/50">
                    <span class="text-xs font-bold uppercase text-neutral-700 tracking-wider block mb-1">Option B</span>
                    <h4 class="font-bold text-sm text-neutral-900 mb-2">Doorstep Courier Exchange</h4>
                    <p class="text-xs text-neutral-600 leading-relaxed">
                        Message our WhatsApp Concierge ({{ $retPhone }}) with your Order ID and desired new size. Our delivery rider will bring the replacement to your doorstep and collect the return parcel simultaneously.
                    </p>
                </div>

            </div>
        </div>

        <!-- 4. Exchange Courier Charges -->
        <div class="space-y-4">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-full bg-neutral-900 text-white flex items-center justify-center font-bold text-xs">4</span>
                <h2 class="font-outfit text-xl sm:text-2xl font-bold text-neutral-900">Courier Fees for Change of Mind / Sizing</h2>
            </div>
            <div class="pl-11 space-y-3 text-xs sm:text-sm text-neutral-600 leading-relaxed">
                <p>
                    If an exchange is requested due to personal preference or selecting the wrong size during checkout:
                </p>
                <ul class="list-disc pl-5 space-y-2">
                    <li>Inside Dhaka: Standard delivery fee of <strong class="text-neutral-900">৳70</strong> applies for doorstep swap.</li>
                    <li>Outside Dhaka: Return courier fee of <strong class="text-neutral-900">৳130</strong> applies.</li>
                    <li>If exchanging at any physical showroom, <strong class="text-green-700 font-bold">no additional fee is charged</strong>.</li>
                </ul>
            </div>
        </div>

        <!-- Need Help Box -->
        <div class="p-6 rounded-2xl bg-neutral-950 text-white flex flex-col sm:flex-row items-center justify-between gap-6">
            <div>
                <h4 class="font-outfit text-lg font-bold">Have Questions About Your Return?</h4>
                <p class="text-xs text-neutral-400 mt-1">Our customer service representatives are available everyday from 10 AM to 10 PM.</p>
            </div>
            <a href="https://wa.me/{{ $retWaDigits }}?text=Hello%20Kingsman%20Concierge,%20I%20have%20a%20question%20regarding%20returns." target="_blank" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white text-xs font-bold uppercase tracking-wider rounded-lg transition whitespace-nowrap flex items-center gap-2">
                <i class="fa-brands fa-whatsapp text-sm"></i>
                <span>WhatsApp {{ $retPhone }}</span>
            </a>
        </div>

    </div>
</section>
@endsection
