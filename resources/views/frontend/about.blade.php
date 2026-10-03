@extends('frontend.layouts.master')

@section('title', 'About Us | ' . ($comName ?? 'Kingsman') . ' Luxury Ethnic & Menswear Bangladesh')

@section('content')
<!-- Page Header Banner -->
<section class="bg-neutral-950 text-white py-12 md:py-16 border-b border-neutral-800 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <!-- Breadcrumbs -->
        <nav class="flex items-center justify-center gap-2 text-xs uppercase tracking-widest text-neutral-400 mb-4">
            <a href="{{ route('home') }}" class="hover:text-white transition">Home</a>
            <span>/</span>
            <span class="text-red-500 font-semibold">About Us</span>
        </nav>
        
        <span class="text-xs uppercase font-bold tracking-[0.3em] text-[#C5A880] block mb-2">Heritage • Craft • Sartorial Finesse</span>
        <h1 class="font-outfit text-3xl sm:text-5xl font-black uppercase tracking-tight text-white mb-3">
            The World of {{ $comName ?? 'Kingsman' }}
        </h1>
        <p class="text-xs sm:text-sm text-neutral-300 max-w-xl mx-auto font-light leading-relaxed">
            Redefining contemporary ethnic menswear and everyday lifestyle garments across Bangladesh with royal aesthetics and uncompromising fabric standards.
        </p>
    </div>
</section>

<!-- 1. Brand Story & Heritage -->
<section class="py-16 md:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">
            
            <!-- Left Image Composite -->
            <div class="lg:col-span-6 relative">
                <div class="rounded-2xl overflow-hidden shadow-2xl bg-neutral-900 border border-neutral-100">
                    <img src="{{ asset('frontend/images/banner_hero_eid.jpg') }}" 
                         alt="{{ $comName ?? 'Kingsman' }} Brand Heritage" 
                         class="w-full h-[380px] sm:h-[480px] object-cover">
                </div>
                <!-- Floating Quality Badge -->
                <div class="absolute -bottom-6 -right-4 sm:right-6 bg-neutral-900 text-white p-5 rounded-xl shadow-2xl border border-neutral-700 max-w-xs hidden sm:block">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-red-600/20 text-red-500 flex items-center justify-center font-bold text-lg">
                            <i class="fa-solid fa-gem"></i>
                        </div>
                        <div>
                            <span class="font-bold text-sm text-white block">100% Bespoke Craft</span>
                            <span class="text-[11px] text-neutral-400">Pure Giza Cotton &amp; Fine Silk</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Content Narrative -->
            <div class="lg:col-span-6 space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 border border-red-100 text-red-600 text-xs font-bold uppercase tracking-widest">
                    <span>Our Story</span>
                </div>

                <h2 class="font-outfit text-2xl sm:text-4xl font-bold text-neutral-900 leading-tight">
                    Where Timeless Tradition Meets Modern Metropolitan Luxury
                </h2>

                <p class="text-sm md:text-base text-neutral-600 leading-relaxed font-light">
                    Founded with a passion for exceptional sartorial excellence, <strong class="text-neutral-900 font-semibold">{{ $comName ?? 'Kingsman' }}</strong> was born to bridge the gap between historic South Asian royal grandeur and modern urban functionality. From our signature festive Panjabis and royal Kabli sets to tailored executive shirts and heavy-knit street tees, every garment is thoughtfully designed and precisely calibrated.
                </p>

                <p class="text-sm md:text-base text-neutral-600 leading-relaxed font-light">
                    We believe true luxury lies in the details—the crispness of 120s thread counts, natural breathable dyes, seamless collar fusing, and reinforced stitching engineered to retain shape wear after wear.
                </p>

                <!-- Key Metrics Grid -->
                <div class="grid grid-cols-3 gap-4 pt-6 border-t border-neutral-100">
                    <div>
                        <span class="font-outfit text-2xl sm:text-3xl font-black text-neutral-900 block">6+</span>
                        <span class="text-xs text-neutral-500 uppercase tracking-wider mt-1 block">Showrooms</span>
                    </div>
                    <div>
                        <span class="font-outfit text-2xl sm:text-3xl font-black text-neutral-900 block">50,000+</span>
                        <span class="text-xs text-neutral-500 uppercase tracking-wider mt-1 block">Happy Patrons</span>
                    </div>
                    <div>
                        <span class="font-outfit text-2xl sm:text-3xl font-black text-neutral-900 block">100%</span>
                        <span class="text-xs text-neutral-500 uppercase tracking-wider mt-1 block">Premium Quality</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- 2. Core Pillars & Brand Values -->
<section class="py-16 bg-neutral-50 border-y border-neutral-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <span class="text-xs font-bold text-red-600 uppercase tracking-widest block mb-2">Our Commitments</span>
            <h2 class="font-outfit text-2xl sm:text-3xl font-black uppercase tracking-tight text-neutral-900">
                The {{ $comName ?? 'Kingsman' }} Standard of Excellence
            </h2>
            <div class="w-12 h-0.5 bg-red-600 mx-auto mt-3"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <div class="p-6 rounded-xl bg-white border border-neutral-200 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-crown"></i>
                </div>
                <h3 class="font-outfit text-base font-bold text-neutral-900 mb-2">Artisanal Handcraft</h3>
                <p class="text-xs text-neutral-600 leading-relaxed">
                    Intricate collar embroidery, concealed button plackets, and hand-finished hems executed by generational artisans.
                </p>
            </div>

            <div class="p-6 rounded-xl bg-white border border-neutral-200 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-shirt"></i>
                </div>
                <h3 class="font-outfit text-base font-bold text-neutral-900 mb-2">Finest Imported Fabrics</h3>
                <p class="text-xs text-neutral-600 leading-relaxed">
                    Sourced from premium mills—combining Egyptian Giza cottons, Mulberry jacquard silk, and heavyweight combed cotton.
                </p>
            </div>

            <div class="p-6 rounded-xl bg-white border border-neutral-200 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <h3 class="font-outfit text-base font-bold text-neutral-900 mb-2">Nationwide Express</h3>
                <p class="text-xs text-neutral-600 leading-relaxed">
                    Guaranteed 24-48 hour delivery in Dhaka and rapid doorstep delivery across all 64 districts with Cash on Delivery.
                </p>
            </div>

            <div class="p-6 rounded-xl bg-white border border-neutral-200 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-arrow-rotate-left"></i>
                </div>
                <h3 class="font-outfit text-base font-bold text-neutral-900 mb-2">Hassle-Free Exchange</h3>
                <p class="text-xs text-neutral-600 leading-relaxed">
                    Easy 7-day sizing exchange and dedicated WhatsApp concierge support for instant sizing assistance.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- 3. Showroom Network -->
<section class="py-16 md:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-xl mx-auto mb-12">
            <span class="text-xs font-bold text-neutral-500 uppercase tracking-widest block mb-2">Experience In Person</span>
            <h2 class="font-outfit text-2xl sm:text-3xl font-black uppercase tracking-tight text-neutral-900">
                Our Physical Showrooms
            </h2>
            <p class="text-xs sm:text-sm text-neutral-600 mt-2">Visit our luxury boutiques for bespoke fitting and fabric touch</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($showrooms ?? [] as $showroom)
                <div class="p-5 rounded-xl border border-neutral-200 hover:border-black transition bg-neutral-50/50">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-bold text-sm text-neutral-900">{{ $showroom->name }}</h4>
                        <span class="text-[10px] px-2 py-0.5 bg-neutral-900 text-white font-semibold rounded">
                            {{ $showroom->shop_name ?: 'Showroom' }}
                        </span>
                    </div>
                    <p class="text-xs text-neutral-600 mb-3 flex items-start gap-2">
                        <i class="fa-solid fa-location-dot text-red-500 mt-0.5"></i>
                        <span>{{ $showroom->address ?: 'Address details available on contact' }}</span>
                    </p>
                    @if(!empty($showroom->phone))
                        <div class="text-xs text-neutral-500 flex items-center gap-2">
                            <i class="fa-solid fa-phone text-neutral-400"></i>
                            <span>{{ $showroom->phone }}</span>
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-3 text-center text-xs text-neutral-500 py-8">
                    Showroom locations will be updated shortly.
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-14 bg-neutral-900 text-white text-center">
    <div class="max-w-4xl mx-auto px-4">
        <h2 class="font-outfit text-2xl sm:text-3xl font-black uppercase tracking-tight mb-3">
            Explore The Latest Festive Edit
        </h2>
        <p class="text-xs sm:text-sm text-neutral-400 mb-6 max-w-lg mx-auto">
            Discover unmatched luxury ethnic wear and contemporary wardrobe essentials.
        </p>
        <a href="{{ route('products.index') }}" 
           class="inline-flex items-center gap-2 px-8 py-3.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold uppercase tracking-widest rounded transition duration-200">
            <span>Shop All Products</span>
            <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
    </div>
</section>
@endsection
