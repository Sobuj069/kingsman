<!-- Floating Right-Edge Side Cart Widget & Support Actions -->
<div>
    <!-- Fixed Vertical Side Cart Tab (Right Edge of Screen) -->
    <div class="fixed top-1/2 -translate-y-1/2 right-0 z-40 cursor-pointer select-none group"
         @click="cartOpen = true"
         title="View Shopping Bag">
        
        <div class="flex flex-col items-center bg-white border-y border-l border-neutral-300 shadow-2xl rounded-l-lg overflow-hidden group-hover:-translate-x-1.5 transition-all duration-200 min-w-[62px] md:min-w-[68px]">
            
            <!-- Top White Section: Bag Icon & Item Count -->
            <div class="p-2.5 flex flex-col items-center justify-center gap-1 w-full bg-white group-hover:bg-neutral-50 transition-colors">
                <i class="fa-solid fa-bag-shopping text-base md:text-lg text-gray-900 group-hover:text-red-600 transition-colors"></i>
                <span class="text-[10px] md:text-[11px] font-bold text-gray-800 uppercase tracking-tight text-center leading-none"
                      x-text="$store.cart.count + ' items'">
                    0 Items
                </span>
            </div>

            <!-- Bottom Black Section: Real-time Subtotal -->
            <div class="w-full py-1.5 px-2 bg-black text-white text-center flex items-center justify-center border-t border-neutral-200">
                <span class="text-[10px] md:text-[11px] font-extrabold tracking-tight"
                      x-text="'TK ' + $store.cart.subtotal.toLocaleString()">
                    TK 0
                </span>
            </div>
        </div>
    </div>

    <!-- Floating Support Actions (Bottom Right) -->
    <aside class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-40 flex flex-col gap-2.5" data-purpose="floating-customer-support" x-data="{ showTopBtn: false }" @scroll.window="showTopBtn = (window.pageYOffset > 400)">
        
        <!-- Scroll To Top Button -->
        <button x-show="showTopBtn" 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-4"
                @click="window.scrollTo({ top: 0, behavior: 'smooth' })" 
                type="button" 
                class="w-10 h-10 sm:w-11 sm:h-11 bg-neutral-800 text-white rounded-full flex items-center justify-center shadow-xl hover:bg-neutral-900 transition transform hover:scale-105 border border-neutral-700" 
                title="Scroll to Top"
                style="display: none;">
            <i class="fa-solid fa-arrow-up text-xs sm:text-sm"></i>
        </button>

        @php
            $rawWa = function_exists('get_whatsapp_phone') ? get_whatsapp_phone() : ($whatsappNumber ?? ($hotline ?? '01987258406'));
            $rawPhone = function_exists('get_hotline_phone') ? get_hotline_phone() : ($hotline ?? '01987258406');
            $floatingWaDigits = preg_replace('/[^0-9]/', '', $rawWa);
            if (str_starts_with($floatingWaDigits, '0') && strlen($floatingWaDigits) === 11) {
                $floatingWaDigits = '88' . $floatingWaDigits;
            } elseif (!str_starts_with($floatingWaDigits, '88') && !empty($floatingWaDigits) && strlen($floatingWaDigits) <= 11) {
                $floatingWaDigits = '88' . $floatingWaDigits;
            }
            $callDigits = preg_replace('/[^0-9+]/', '', $rawPhone);
        @endphp
        <!-- WhatsApp Floating Action -->
        <a href="https://wa.me/{{ $floatingWaDigits }}?text={{ urlencode('Hello ' . ($comName ?? 'Kingsman') . ', I would like to know more about your collection.') }}" 
           target="_blank" 
           rel="noopener noreferrer" 
           class="relative w-11 h-11 sm:w-12 sm:h-12 bg-[#25D366] text-white rounded-full flex items-center justify-center shadow-2xl hover:bg-emerald-600 transition transform hover:scale-105" 
           title="Chat on WhatsApp">
            <i class="fa-brands fa-whatsapp text-xl sm:text-2xl"></i>
            <!-- Ping Animation Indicator -->
            <span class="absolute -top-1 -right-1 flex h-3 w-3 sm:h-3.5 sm:w-3.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 sm:h-3.5 sm:w-3.5 bg-emerald-500 border-2 border-white"></span>
            </span>
        </a>

        <!-- Call Floating Action Button -->
        <a href="tel:{{ $callDigits }}" 
           class="w-11 h-11 sm:w-12 sm:h-12 bg-neutral-900 text-white rounded-full flex items-center justify-center shadow-xl hover:bg-black transition transform hover:scale-105 border border-neutral-700" 
           title="Call Hotline: {{ $rawPhone }}">
            <i class="fa-solid fa-phone text-sm sm:text-base"></i>
        </a>
    </aside>
</div>
