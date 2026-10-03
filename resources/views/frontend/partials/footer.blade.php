<!-- Official Brand Footer -->
<footer class="bg-black text-gray-300 pt-16 pb-8 border-t border-neutral-900 relative overflow-hidden" data-purpose="kingsman-footer">
    
    <!-- Big Stylized KINGSMAN Watermark Behind Content -->
    <div class="absolute inset-x-0 bottom-8 flex justify-center pointer-events-none opacity-[0.03] select-none">
        <span class="text-[110px] md:text-[200px] font-black tracking-widest text-white leading-none font-royal">KINGSMAN</span>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <!-- 4-Column Grid Structure -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-10 mb-12">
            
            <!-- Column 1: Contact Information & Brand -->
            <div class="space-y-4">
                <div class="mb-4">
                    <img src="{{ $siteLogo ?? (!empty(get_setting('system_logo')) && file_exists(public_path('uploads/logo/' . get_setting('system_logo'))) ? asset('uploads/logo/' . get_setting('system_logo')) : asset('frontend/images/robe_logo.png')) }}" 
                         alt="{{ $comName ?? 'Kingsman' }} Logo" 
                         class="h-11 w-auto object-contain">
                </div>
                <p class="text-xs text-neutral-400 leading-relaxed">
                    {{ $comName ?? 'Kingsman' }} is Bangladesh's premier contemporary lifestyle & ethnic fashion brand, dedicated to delivering unrivaled quality, sophisticated panjabis, formal shirts, and luxury casual wear.
                </p>
                <ul class="space-y-3 text-xs md:text-sm text-neutral-300 pt-2">
                    <li class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded bg-neutral-900 border border-neutral-800 flex items-center justify-center flex-shrink-0 mt-0.5 text-red-500">
                            <i class="fa-solid fa-phone text-xs"></i>
                        </div>
                        <div>
                            <span class="font-semibold text-white block text-xs">Customer Care:</span>
                            <p class="text-neutral-400">{{ $hotline ?? '01987258406' }}</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded bg-neutral-900 border border-neutral-800 flex items-center justify-center flex-shrink-0 mt-0.5 text-red-500">
                            <i class="fa-solid fa-envelope text-xs"></i>
                        </div>
                        <div>
                            <span class="font-semibold text-white block text-xs">Email Address:</span>
                            <p class="text-neutral-400">{{ $comEmail ?? 'info@kingsman.com.bd' }}</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-7 h-7 rounded bg-neutral-900 border border-neutral-800 flex items-center justify-center flex-shrink-0 mt-0.5 text-red-500">
                            <i class="fa-solid fa-location-dot text-xs"></i>
                        </div>
                        <div>
                            <span class="font-semibold text-white block text-xs">Head Office:</span>
                            <p class="text-neutral-400">{{ $comAddress ?? 'Mirpur 10, Dhaka - 1216, Bangladesh' }}</p>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Column 2: Our Showrooms -->
            <div id="showrooms">
                <h4 class="text-white font-bold text-xs md:text-sm uppercase tracking-wider mb-4 border-b border-neutral-800 pb-2.5 flex items-center justify-between">
                    <span>OUR SHOWROOMS</span>
                    @if(isset($showrooms) && $showrooms->isNotEmpty())
                        <span class="text-[10px] text-red-500 font-normal">{{ $showrooms->count() }} {{ \Illuminate\Support\Str::plural('Branch', $showrooms->count()) }}</span>
                    @endif
                </h4>
                <ul class="space-y-3 text-xs text-neutral-400">
                    @forelse($showrooms ?? [] as $showroom)
                        <li class="hover:text-neutral-200 transition">
                            <span class="text-gray-200 font-semibold block">{{ $showroom->name }}</span>
                            <span>{{ $showroom->address ?: ($showroom->shop_name ?: 'Location details available on inquiry') }}</span>
                            @if(!empty($showroom->phone))
                                <span class="block text-[11px] text-neutral-500 mt-0.5">
                                    <i class="fa-solid fa-phone text-[9px] text-red-500/80 mr-1"></i>{{ $showroom->phone }}
                                </span>
                            @endif
                        </li>
                    @empty
                        <li class="text-neutral-500">Showroom information coming soon.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Column 3: Quick Links -->
            <div>
                <h4 class="text-white font-bold text-xs md:text-sm uppercase tracking-wider mb-4 border-b border-neutral-800 pb-2.5">
                    QUICK LINKS
                </h4>
                <ul class="space-y-2 text-xs md:text-sm text-neutral-400">
                    <li><a href="{{ route('frontend.about') }}" class="hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">About Us</a></li>
                    <li><a href="{{ route('frontend.contact') }}" class="hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Contact Us</a></li>
                    <li><a href="{{ route('products.index') }}" class="hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">All Collections</a></li>
                    <li><a href="{{ route('frontend.contact') }}" class="hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Complaint / Advice</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Account Login</a></li>
                    <li><a href="{{ route('frontend.about') }}#showrooms" class="hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Showroom Locations</a></li>
                    <li><a href="{{ route('frontend.return-policy') }}" class="hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">FAQ &amp; Sizing Help</a></li>
                </ul>
            </div>

            <!-- Column 4: Policies & Regulations -->
            <div>
                <h4 class="text-white font-bold text-xs md:text-sm uppercase tracking-wider mb-4 border-b border-neutral-800 pb-2.5">
                    POLICY &amp; TERMS
                </h4>
                <ul class="space-y-2 text-xs md:text-sm text-neutral-400">
                    <li><a href="{{ route('frontend.return-policy') }}" class="hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Return Policy</a></li>
                    <li><a href="{{ route('frontend.privacy-policy') }}" class="hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Privacy Policy</a></li>
                    <li><a href="{{ route('frontend.terms') }}" class="hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Terms &amp; Conditions</a></li>
                    <li><a href="{{ route('frontend.refund-policy') }}" class="hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Refund Policy</a></li>
                    <li><a href="{{ route('frontend.return-policy') }}" class="hover:text-white hover:translate-x-1 inline-block transition-transform duration-200">Exchange Policy</a></li>
                </ul>

                <!-- Social Media Channels -->
                <div class="mt-6 pt-4 border-t border-neutral-900">
                    <span class="text-xs uppercase font-semibold text-neutral-400 block mb-3">Connect with us</span>
                    <div class="flex items-center gap-2.5 text-neutral-400">
                        <a href="https://www.facebook.com/profile.php?id=61581393854313&mibextid=ZbWKwL" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-neutral-900 border border-neutral-800 flex items-center justify-center hover:text-white hover:bg-[#1877F2] hover:border-[#1877F2] transition" aria-label="Facebook">
                            <i class="fa-brands fa-facebook-f text-xs"></i>
                        </a>
                        <a href="https://www.tiktok.com/@kingsmen_rohimanagor?_r=1&_t=ZS-99xtPnn0rEj" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-neutral-900 border border-neutral-800 flex items-center justify-center hover:text-white hover:bg-black hover:border-neutral-600 transition" aria-label="TikTok">
                            <i class="fa-brands fa-tiktok text-xs"></i>
                        </a>
                        <a href="https://www.instagram.com" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-neutral-900 border border-neutral-800 flex items-center justify-center hover:text-white hover:bg-[#E4405F] hover:border-[#E4405F] transition" aria-label="Instagram">
                            <i class="fa-brands fa-instagram text-xs"></i>
                        </a>
                        <a href="https://www.youtube.com" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-neutral-900 border border-neutral-800 flex items-center justify-center hover:text-white hover:bg-[#FF0000] hover:border-[#FF0000] transition" aria-label="YouTube">
                            <i class="fa-brands fa-youtube text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom Copyright Bar -->
        <div class="border-t border-neutral-900 pt-6 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-neutral-500">
            <p>© {{ $comName ?? 'Kingsman' }} {{ date('Y') }} All rights reserved.</p>
            
            <!-- Payment Badges / Trust -->
            <div class="flex items-center gap-3 text-neutral-400 text-sm">
                <span>Secure Payments via</span>
                <span class="font-bold text-gray-300">bKash / Nagad / Cards / Cash On Delivery</span>
            </div>

            <div class="flex items-center gap-1.5">
                <span>Crafted with</span>
                <span class="text-red-500 animate-pulse">❤</span>
                <span>in Bangladesh</span>
            </div>
        </div>
    </div>
</footer>
