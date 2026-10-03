<!-- Top Announcement / Promotion Bar -->
<div class="bg-gradient-to-r from-neutral-950 via-[#181818] to-neutral-950 text-neutral-300 text-[10px] sm:text-[11px] md:text-xs py-1.5 sm:py-2 px-2 sm:px-4 border-b border-neutral-800 overflow-hidden">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <!-- Left Hotline & Contact -->
        <div class="hidden sm:flex items-center gap-4">
            <a href="tel:{{ preg_replace('/[^0-9+]/', '', function_exists('get_hotline_phone') ? get_hotline_phone() : ($hotline ?? '01987258406')) }}" class="flex items-center gap-1.5 hover:text-white transition">
                <i class="fa-solid fa-phone text-[10px] text-red-500"></i>
                <span>Hotline: <strong class="text-gray-200">{{ function_exists('get_hotline_phone') ? get_hotline_phone() : ($hotline ?? '01987258406') }}</strong></span>
            </a>
            <span class="text-neutral-700">|</span>
            <span class="text-neutral-400">Dhaka & Nationwide Express Delivery</span>
        </div>

        <!-- Center Announcement Ticker / Promo Message -->
        <div class="flex-1 text-center font-medium tracking-wide truncate px-1">
            <span class="inline-flex items-center gap-1 text-amber-300">
                <i class="fa-solid fa-sparkles text-[9px] sm:text-[10px]"></i>
                <span class="uppercase font-semibold tracking-wider">EID & Festive:</span>
            </span>
            <span class="text-gray-200 ml-1">Up To 30% OFF Selected Panjabi & Ethnic Wear!</span>
        </div>

        <!-- Right Quick Links & Currency -->
        <div class="hidden md:flex items-center gap-4 text-[11px]">
            <a href="#showrooms" class="hover:text-white transition">Showroom Locator</a>
            <span class="text-neutral-700">|</span>
            <a href="#tracking" class="hover:text-white transition">Track Order</a>
            <span class="text-neutral-700">|</span>
            <span class="font-bold text-gray-200">৳ BDT</span>
        </div>
    </div>
</div>
