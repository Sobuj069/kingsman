@extends('frontend.layouts.master')

@section('title', 'Terms & Conditions | ' . ($comName ?? 'Kingsman') . ' Official')

@section('content')
<!-- Page Header -->
<section class="bg-neutral-950 text-white py-12 md:py-16 border-b border-neutral-800 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <nav class="flex items-center justify-center gap-2 text-xs uppercase tracking-widest text-neutral-400 mb-4">
            <a href="{{ route('home') }}" class="hover:text-white transition">Home</a>
            <span>/</span>
            <span class="text-red-500 font-semibold">Terms &amp; Conditions</span>
        </nav>
        
        <span class="text-xs uppercase font-bold tracking-[0.3em] text-[#C5A880] block mb-2">Legal Terms of Service</span>
        <h1 class="font-outfit text-3xl sm:text-5xl font-black uppercase tracking-tight text-white mb-3">
            Terms &amp; Conditions
        </h1>
        <p class="text-xs sm:text-sm text-neutral-300 max-w-xl mx-auto font-light leading-relaxed">
            Please read these terms and conditions carefully before using or placing orders on the {{ $comName ?? 'Kingsman' }} e-commerce platform.
        </p>
    </div>
</section>

<!-- Content Section -->
<section class="py-14 md:py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 text-xs sm:text-sm text-neutral-600 leading-relaxed">
        
        <div class="space-y-3">
            <h2 class="font-outfit text-xl sm:text-2xl font-bold text-neutral-900">1. Agreement to Terms</h2>
            <p>
                By accessing and shopping at <strong>{{ request()->getHost() }}</strong>, you agree to be bound by these terms of service, all applicable laws and regulations of Bangladesh, and agree that you are responsible for compliance with any applicable local laws.
            </p>
        </div>

        <div class="space-y-3">
            <h2 class="font-outfit text-xl sm:text-2xl font-bold text-neutral-900">2. Pricing &amp; Product Representation</h2>
            <p>
                All prices on our website are stated in Bangladeshi Taka (BDT) and are inclusive of standard local taxes. While we take extreme care to ensure accurate color representation across studio photography, slight variations may occur due to device screen calibrations.
            </p>
        </div>

        <div class="space-y-3">
            <h2 class="font-outfit text-xl sm:text-2xl font-bold text-neutral-900">3. Orders, Confirmation &amp; Verification</h2>
            <p>
                Orders placed via Cash on Delivery or digital payment methods are subject to phone or SMS verification by our concierge team before dispatch. {{ $comName ?? 'Kingsman' }} reserves the right to cancel suspicious, unverified, or fraudulent orders.
            </p>
        </div>

        <div class="space-y-3">
            <h2 class="font-outfit text-xl sm:text-2xl font-bold text-neutral-900">4. Intellectual Property</h2>
            <p>
                All original branding, designs, embroidery motifs, logos, visual photographs, and website content are the exclusive intellectual property of {{ $comName ?? 'Kingsman' }} Bangladesh. Unauthorized duplication or commercial use without prior written consent is strictly prohibited.
            </p>
        </div>

        <div class="p-6 rounded-xl bg-neutral-50 border border-neutral-200">
            <h3 class="font-bold text-neutral-900 mb-2">Need Further Legal Clarification?</h3>
            <p class="text-xs text-neutral-500">Contact our corporate compliance office at <a href="mailto:{{ $comEmail ?? 'info@kingsman.com.bd' }}" class="text-neutral-900 font-bold underline">{{ $comEmail ?? 'info@kingsman.com.bd' }}</a>.</p>
        </div>

    </div>
</section>
@endsection
