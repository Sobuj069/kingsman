@extends('frontend.layouts.master')

@section('title', 'Privacy Policy | ' . ($comName ?? 'Kingsman') . ' Official')

@section('content')
<!-- Page Header -->
<section class="bg-neutral-950 text-white py-12 md:py-16 border-b border-neutral-800 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <nav class="flex items-center justify-center gap-2 text-xs uppercase tracking-widest text-neutral-400 mb-4">
            <a href="{{ route('home') }}" class="hover:text-white transition">Home</a>
            <span>/</span>
            <span class="text-red-500 font-semibold">Privacy Policy</span>
        </nav>
        
        <span class="text-xs uppercase font-bold tracking-[0.3em] text-[#C5A880] block mb-2">Transparency &amp; Trust</span>
        <h1 class="font-outfit text-3xl sm:text-5xl font-black uppercase tracking-tight text-white mb-3">
            Privacy Policy
        </h1>
        <p class="text-xs sm:text-sm text-neutral-300 max-w-xl mx-auto font-light leading-relaxed">
            Your privacy and personal data protection are foundational to our brand integrity. Read how {{ $comName ?? 'Kingsman' }} collects, protects, and manages your information.
        </p>
    </div>
</section>

<!-- Content Section -->
<section class="py-14 md:py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        
        <!-- Last Updated Pill -->
        <div class="flex items-center justify-between pb-4 border-b border-neutral-200 text-xs text-neutral-500">
            <span>Effective Date: <strong>January 1, 2026</strong></span>
            <span>Last Updated: <strong>{{ date('F Y') }}</strong></span>
        </div>

        <!-- 1. Introduction -->
        <div class="space-y-3 text-xs sm:text-sm text-neutral-600 leading-relaxed">
            <h2 class="font-outfit text-xl sm:text-2xl font-bold text-neutral-900">1. Overview &amp; Commitment</h2>
            <p>
                <strong class="text-neutral-900">{{ $comName ?? 'Kingsman' }}</strong> ("{{ request()->getHost() }}", "we", "our", or "us") is committed to safeguarding the privacy and confidential personal information of all patrons who visit our online store or physical showrooms across Bangladesh. This policy outlines our standards regarding data collection, usage, storage, and legal rights.
            </p>
        </div>

        <!-- 2. Information We Collect -->
        <div class="space-y-4">
            <h2 class="font-outfit text-xl sm:text-2xl font-bold text-neutral-900">2. Personal Information We Collect</h2>
            <div class="space-y-3 text-xs sm:text-sm text-neutral-600 leading-relaxed">
                <p>When you browse our website, create an account, or place an order, we collect:</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="p-4 rounded-xl border border-neutral-200 bg-neutral-50">
                        <strong class="text-neutral-900 block font-bold mb-1">Contact &amp; Delivery Details</strong>
                        <span>Full name, mobile phone number, delivery address (District, Thana, Street), and email address.</span>
                    </div>
                    <div class="p-4 rounded-xl border border-neutral-200 bg-neutral-50">
                        <strong class="text-neutral-900 block font-bold mb-1">Order &amp; Purchase History</strong>
                        <span>Garment preferences, sizing selections, past transactions, and feedback records.</span>
                    </div>
                    <div class="p-4 rounded-xl border border-neutral-200 bg-neutral-50">
                        <strong class="text-neutral-900 block font-bold mb-1">Technical &amp; Device Data</strong>
                        <span>IP address, browser type, device resolution, and session cookies to optimize page load speeds.</span>
                    </div>
                    <div class="p-4 rounded-xl border border-neutral-200 bg-neutral-50">
                        <strong class="text-neutral-900 block font-bold mb-1">Payment Information</strong>
                        <span>Transaction reference IDs. <em class="text-neutral-500">We never store your bank PINs, CVV codes, or bKash passwords.</em></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. How We Use Your Data -->
        <div class="space-y-4">
            <h2 class="font-outfit text-xl sm:text-2xl font-bold text-neutral-900">3. How We Use Your Information</h2>
            <div class="space-y-2 text-xs sm:text-sm text-neutral-600 leading-relaxed">
                <ul class="list-disc pl-5 space-y-2">
                    <li>To seamlessly process, pack, invoice, and deliver your orders to your doorstep.</li>
                    <li>To provide live order tracking updates and SMS verification notifications.</li>
                    <li>To handle sizing exchange inquiries and customer concierge assistance via phone or WhatsApp.</li>
                    <li>To prevent fraudulent orders and maintain platform security.</li>
                    <li>With your explicit consent, to notify you of festive collections, VIP sales, and new showroom openings.</li>
                </ul>
            </div>
        </div>

        <!-- 4. Third-Party Sharing & Logistics -->
        <div class="space-y-4">
            <h2 class="font-outfit text-xl sm:text-2xl font-bold text-neutral-900">4. Sharing with Trusted Partners</h2>
            <div class="space-y-3 text-xs sm:text-sm text-neutral-600 leading-relaxed">
                <p>
                    {{ $comName ?? 'Kingsman' }} does not sell, rent, or trade your personal data to any external advertising agencies. We only share essential operational details with:
                </p>
                <ul class="list-disc pl-5 space-y-2">
                    <li><strong class="text-neutral-900">Courier &amp; Logistics Partners:</strong> Delivery couriers (such as Steadfast Courier, Pathao, or Paperfly) receive your name, address, and phone number solely to deliver your package.</li>
                    <li><strong class="text-neutral-900">Licensed Payment Gateways:</strong> Secure MFS/card gateways (bKash, Nagad, SSLCommerz) handle encrypted transactions under strict banking compliance.</li>
                </ul>
            </div>
        </div>

        <!-- 5. Data Security & Encryption -->
        <div class="space-y-4">
            <h2 class="font-outfit text-xl sm:text-2xl font-bold text-neutral-900">5. Data Protection &amp; Security Measures</h2>
            <div class="space-y-3 text-xs sm:text-sm text-neutral-600 leading-relaxed">
                <p>
                    All communications and transactions on {{ request()->getHost() }} are safeguarded using industry-standard <strong class="text-neutral-900">256-bit SSL Encryption</strong>. Access to customer records is strictly restricted to authorized customer support personnel.
                </p>
            </div>
        </div>

        <!-- 6. Contact for Privacy Inquiries -->
        <div class="p-6 rounded-2xl bg-neutral-50 border border-neutral-200">
            <h3 class="font-outfit text-base font-bold text-neutral-900 mb-2">Questions Regarding Your Data?</h3>
            <p class="text-xs text-neutral-600 mb-4">
                If you wish to review, update, or request the deletion of your personal data from our systems, please reach out to our compliance desk:
            </p>
            <div class="flex flex-wrap gap-4 text-xs font-semibold text-neutral-900">
                <a href="mailto:{{ $comEmail ?? 'info@kingsman.com.bd' }}" class="flex items-center gap-2 hover:text-red-600 transition">
                    <i class="fa-solid fa-envelope text-red-500"></i>
                    <span>{{ $comEmail ?? 'info@kingsman.com.bd' }}</span>
                </a>
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', function_exists('get_hotline_phone') ? get_hotline_phone() : ($hotline ?? '01987258406')) }}" class="flex items-center gap-2 hover:text-red-600 transition">
                    <i class="fa-solid fa-phone text-red-500"></i>
                    <span>{{ function_exists('get_hotline_phone') ? get_hotline_phone() : ($hotline ?? '01987258406') }}</span>
                </a>
            </div>
        </div>

    </div>
</section>
@endsection
