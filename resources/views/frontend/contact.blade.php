@extends('frontend.layouts.master')

@section('title', 'Contact Us | ' . ($comName ?? 'Kingsman') . ' Customer Care & Showrooms')

@section('content')
<!-- Page Header -->
<section class="bg-neutral-950 text-white py-12 md:py-16 border-b border-neutral-800 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <nav class="flex items-center justify-center gap-2 text-xs uppercase tracking-widest text-neutral-400 mb-4">
            <a href="{{ route('home') }}" class="hover:text-white transition">Home</a>
            <span>/</span>
            <span class="text-red-500 font-semibold">Contact Us</span>
        </nav>
        
        <span class="text-xs uppercase font-bold tracking-[0.3em] text-[#C5A880] block mb-2">We Are Here To Assist You</span>
        <h1 class="font-outfit text-3xl sm:text-5xl font-black uppercase tracking-tight text-white mb-3">
            Get In Touch With {{ $comName ?? 'Kingsman' }}
        </h1>
        <p class="text-xs sm:text-sm text-neutral-300 max-w-xl mx-auto font-light leading-relaxed">
            Have questions about your order, bespoke sizing, or showroom availability? Reach out through our direct hotlines or message form.
        </p>
    </div>
</section>

<!-- Contact Info Grid & Form Section -->
<section class="py-14 md:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- 4 Quick Direct Cards -->
        @php
            $rawWa = function_exists('get_whatsapp_phone') ? get_whatsapp_phone() : ($whatsappNumber ?? ($hotline ?? '01987258406'));
            $rawPhone = function_exists('get_hotline_phone') ? get_hotline_phone() : ($hotline ?? '01987258406');
            $cntWaDigits = preg_replace('/[^0-9]/', '', $rawWa);
            if (str_starts_with($cntWaDigits, '0') && strlen($cntWaDigits) === 11) {
                $cntWaDigits = '88' . $cntWaDigits;
            } elseif (!str_starts_with($cntWaDigits, '88') && !empty($cntWaDigits) && strlen($cntWaDigits) <= 11) {
                $cntWaDigits = '88' . $cntWaDigits;
            }
            $callDigits = preg_replace('/[^0-9+]/', '', $rawPhone);
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-14">
            
            <!-- Hotline 1 -->
            <div class="p-6 rounded-2xl bg-neutral-50 border border-neutral-200/80 shadow-sm flex flex-col justify-between hover:border-black transition">
                <div class="w-12 h-12 rounded-xl bg-red-100 text-red-600 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-phone-volume"></i>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-neutral-400 block mb-1">Customer Support</span>
                    <h3 class="font-bold text-neutral-900 text-base mb-1">{{ $rawPhone }}</h3>
                    <p class="text-xs text-neutral-500">Everyday: 10:00 AM - 10:00 PM</p>
                </div>
                <a href="tel:{{ $callDigits }}" class="mt-4 text-xs font-bold text-red-600 hover:text-red-700 flex items-center gap-1.5">
                    <span>Call Now</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <!-- WhatsApp Direct -->
            <div class="p-6 rounded-2xl bg-green-50/50 border border-green-200/80 shadow-sm flex flex-col justify-between hover:border-green-600 transition">
                <div class="w-12 h-12 rounded-xl bg-green-100 text-green-600 flex items-center justify-center text-xl mb-4">
                    <i class="fa-brands fa-whatsapp"></i>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-green-700 block mb-1">WhatsApp Concierge</span>
                    <h3 class="font-bold text-neutral-900 text-base mb-1">{{ $rawWa }}</h3>
                    <p class="text-xs text-neutral-500">Instant Sizing &amp; Order Help</p>
                </div>
                <a href="https://wa.me/{{ $cntWaDigits }}?text={{ urlencode('Hello ' . ($comName ?? 'Kingsman') . ' Concierge, I have an inquiry.') }}" target="_blank" class="mt-4 text-xs font-bold text-green-700 hover:text-green-800 flex items-center gap-1.5">
                    <span>Chat on WhatsApp</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <!-- Email Support -->
            <div class="p-6 rounded-2xl bg-neutral-50 border border-neutral-200/80 shadow-sm flex flex-col justify-between hover:border-black transition">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-neutral-400 block mb-1">Official Email</span>
                    <h3 class="font-bold text-neutral-900 text-sm mb-1 break-all">{{ $comEmail ?? 'info@kingsman.com.bd' }}</h3>
                    <p class="text-xs text-neutral-500">24/7 Inquiry &amp; Corporate</p>
                </div>
                <a href="mailto:{{ $comEmail ?? 'info@kingsman.com.bd' }}" class="mt-4 text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1.5">
                    <span>Send Email</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <!-- Head Office -->
            <div class="p-6 rounded-2xl bg-neutral-50 border border-neutral-200/80 shadow-sm flex flex-col justify-between hover:border-black transition">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl mb-4">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-neutral-400 block mb-1">Corporate Office</span>
                    <h3 class="font-bold text-neutral-900 text-sm mb-1">{{ $comAddress ?? 'Mirpur 10, Dhaka' }}</h3>
                    <p class="text-xs text-neutral-500">Dhaka - 1216, Bangladesh</p>
                </div>
                <span class="mt-4 text-xs font-bold text-neutral-700 flex items-center gap-1.5">
                    <span>Mon - Thu: 10AM - 7PM</span>
                </span>
            </div>

        </div>

        <!-- 2-Column: Contact Form & Showrooms Quick List -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14">
            
            <!-- Contact Form (7 cols) -->
            <div class="lg:col-span-7 bg-neutral-50/70 p-6 sm:p-10 rounded-2xl border border-neutral-200" 
                 x-data="{ 
                     name: '', 
                     phone: '', 
                     email: '', 
                     subject: 'General Inquiry', 
                     message: '', 
                     submitted: false,
                     submitForm() {
                         this.submitted = true;
                         window.showRobeToast('Thank you! Your message has been received. Our team will contact you shortly.');
                         this.name = '';
                         this.phone = '';
                         this.email = '';
                         this.message = '';
                     }
                 }">
                
                <div class="mb-8">
                    <span class="text-xs font-bold text-red-600 uppercase tracking-widest block mb-1">Message Us</span>
                    <h2 class="font-outfit text-2xl sm:text-3xl font-bold text-neutral-900">
                        Send Your Inquiry
                    </h2>
                    <p class="text-xs sm:text-sm text-neutral-500 mt-1">
                        Fill out the form below and our customer concierge will respond within 2 hours.
                    </p>
                </div>

                <div x-show="submitted" class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-xs sm:text-sm flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-green-600 text-lg"></i>
                    <span>Thank you! Your message has been submitted successfully.</span>
                </div>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Your Full Name *</label>
                            <input type="text" 
                                   x-model="name" 
                                   required 
                                   placeholder="e.g. Asif Mahmud" 
                                   class="w-full px-4 py-3 rounded-lg border border-neutral-300 text-xs sm:text-sm bg-white focus:outline-none focus:border-neutral-900 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Mobile Number *</label>
                            <input type="tel" 
                                   x-model="phone" 
                                   required 
                                   placeholder="01XXXXXXXXX" 
                                   class="w-full px-4 py-3 rounded-lg border border-neutral-300 text-xs sm:text-sm bg-white focus:outline-none focus:border-neutral-900 transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Email Address</label>
                            <input type="email" 
                                   x-model="email" 
                                   placeholder="name@domain.com" 
                                   class="w-full px-4 py-3 rounded-lg border border-neutral-300 text-xs sm:text-sm bg-white focus:outline-none focus:border-neutral-900 transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Subject / Department</label>
                            <select x-model="subject" 
                                    class="w-full px-4 py-3 rounded-lg border border-neutral-300 text-xs sm:text-sm bg-white focus:outline-none focus:border-neutral-900 transition">
                                <option>Order Status / Tracking</option>
                                <option>Sizing &amp; Fabric Advice</option>
                                <option>Exchange / Return Request</option>
                                <option>Showroom Stock Inquiry</option>
                                <option>Corporate / Wholesale Order</option>
                                <option>Other Inquiry</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Your Message *</label>
                        <textarea x-model="message" 
                                  required 
                                  rows="4" 
                                  placeholder="How can we help you today? Please include order ID if applicable..." 
                                  class="w-full px-4 py-3 rounded-lg border border-neutral-300 text-xs sm:text-sm bg-white focus:outline-none focus:border-neutral-900 transition"></textarea>
                    </div>

                    <button type="submit" 
                            class="w-full sm:w-auto px-8 py-3.5 bg-neutral-900 hover:bg-black text-white text-xs font-bold uppercase tracking-widest rounded-lg shadow-md transition duration-200">
                        <span>Submit Message</span>
                        <i class="fa-solid fa-paper-plane ml-2 text-[10px]"></i>
                    </button>
                </form>
            </div>

            <!-- Showrooms & Hours (5 cols) -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-neutral-950 text-white p-6 sm:p-8 rounded-2xl">
                    <h3 class="font-outfit text-xl font-bold uppercase tracking-wide mb-3 flex items-center gap-2">
                        <i class="fa-solid fa-clock text-red-500 text-base"></i>
                        <span>Operating Hours</span>
                    </h3>
                    <ul class="space-y-3 text-xs text-neutral-300 pt-2 border-t border-neutral-800">
                        <li class="flex justify-between items-center">
                            <span>Online Concierge:</span>
                            <span class="font-bold text-white">10:00 AM – 11:00 PM</span>
                        </li>
                        <li class="flex justify-between items-center">
                            <span>Showrooms (Sat – Thu):</span>
                            <span class="font-bold text-white">10:00 AM – 10:00 PM</span>
                        </li>
                        <li class="flex justify-between items-center">
                            <span>Showrooms (Friday):</span>
                            <span class="font-bold text-white">02:00 PM – 10:00 PM</span>
                        </li>
                    </ul>
                </div>

                <!-- Showrooms Quick Address Box -->
                <div class="bg-neutral-50 p-6 rounded-2xl border border-neutral-200">
                    <h4 class="font-bold text-sm text-neutral-900 uppercase tracking-wide mb-4">
                        Major Showroom Addresses
                    </h4>
                    <div class="space-y-4 text-xs text-neutral-600">
                        @forelse($showrooms ?? [] as $showroom)
                            <div class="@if(!$loop->last) pb-3 border-b border-neutral-200/80 @endif">
                                <strong class="text-neutral-900 block">{{ $showroom->name }}</strong>
                                <span>{{ $showroom->address ?: ($showroom->shop_name ?: 'Location details available on inquiry') }}</span>
                                @if(!empty($showroom->phone))
                                    <span class="block text-[11px] text-neutral-500 mt-0.5"><i class="fa-solid fa-phone text-[9px] text-red-500/80 mr-1"></i>{{ $showroom->phone }}</span>
                                @endif
                            </div>
                        @empty
                            <p class="text-neutral-500">Showroom information coming soon.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

    </div>
</section>
@endsection
