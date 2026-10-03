<!-- Quick View Product Modal (Alpine.js Interactive) -->
<div x-show="quickViewOpen" 
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true"
     style="display: none;">
    
    <!-- Backdrop -->
    <div x-show="quickViewOpen" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="quickViewOpen = false" 
         class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <!-- Modal Card -->
        <div x-show="quickViewOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-3xl border border-neutral-200">
            
            <!-- Close Button -->
            <button @click="quickViewOpen = false" 
                    type="button" 
                    class="absolute top-4 right-4 z-10 w-9 h-9 rounded-full bg-white/90 text-gray-700 hover:text-black hover:bg-white shadow flex items-center justify-center transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            <div class="grid grid-cols-1 md:grid-cols-2">
                <!-- Product Image Gallery Column -->
                <div class="relative bg-neutral-100 flex items-center justify-center p-6 border-b md:border-b-0 md:border-r border-neutral-200">
                    <div class="aspect-[3/4] w-full max-h-[420px] rounded overflow-hidden shadow-sm bg-white">
                        <img :src="quickProduct.image || '{{ asset('frontend/images/no-image.svg') }}'" 
                             :alt="quickProduct.name" 
                             class="w-full h-full object-cover">
                    </div>
                    <!-- Brand Label -->
                    <span class="absolute top-4 left-4 bg-neutral-900 text-white text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded">
                        KINGSMAN SIGNATURE
                    </span>
                </div>

                <!-- Product Details Column -->
                <div class="p-6 md:p-8 flex flex-col justify-between space-y-4">
                    <div>
                        <span class="text-[11px] font-bold text-red-600 uppercase tracking-wider block" x-text="quickProduct.category"></span>
                        <h2 class="text-lg md:text-xl font-bold text-gray-900 mt-1" x-text="quickProduct.name"></h2>

                        <!-- Price Section -->
                        <div class="mt-3 flex items-baseline gap-3">
                            <span class="text-2xl font-black text-gray-900" x-text="'৳ ' + Number(getQuickPrice()).toLocaleString()"></span>
                            <template x-if="getQuickOldPrice() && getQuickOldPrice() > getQuickPrice()">
                                <span class="text-sm text-gray-400 line-through" x-text="'৳ ' + Number(getQuickOldPrice()).toLocaleString()"></span>
                            </template>
                            <template x-if="getQuickOldPrice() && getQuickOldPrice() > getQuickPrice()">
                                <span class="text-xs font-bold text-red-600 bg-red-50 border border-red-200 px-2 py-0.5 rounded"
                                      x-text="Math.round(((getQuickOldPrice() - getQuickPrice())/getQuickOldPrice())*100) + '% OFF'"></span>
                            </template>
                        </div>

                        <!-- Brief Description -->
                        <p class="mt-3 text-xs text-gray-600 leading-relaxed" x-text="quickProduct.description"></p>

                        <hr class="my-4 border-gray-200">

                        <!-- Color Selector (if product has variations) -->
                        <template x-if="quickProduct.colors && quickProduct.colors.length > 1">
                            <div class="space-y-2 mb-3">
                                <label class="text-xs font-bold uppercase tracking-wider text-gray-700">
                                    Select Color: <span class="text-black font-extrabold" x-text="typeof quickProduct.selectedColor === 'object' ? quickProduct.selectedColor.name : quickProduct.selectedColor"></span>
                                </label>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="c in quickProduct.colors" :key="typeof c === 'object' ? c.name : c">
                                        <button type="button" 
                                                @click="selectQuickColor(c)"
                                                :class="[
                                                    (typeof quickProduct.selectedColor === 'object' ? quickProduct.selectedColor.name : quickProduct.selectedColor) === (typeof c === 'object' ? c.name : c) 
                                                    ? 'bg-black text-white border-black ring-2 ring-neutral-400' 
                                                    : 'bg-white text-gray-800 border-gray-300 hover:border-black'
                                                ]"
                                                class="px-3 py-1.5 rounded border text-xs font-bold transition flex items-center gap-1.5">
                                            <span x-text="typeof c === 'object' ? c.name : c"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Size Selector -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold uppercase tracking-wider text-gray-700">
                                    Select Size: <span class="text-black font-extrabold" x-text="quickProduct.selectedSize"></span>
                                </label>
                                <a href="#size-chart" class="text-[11px] text-red-600 hover:underline font-semibold">Size Guide</a>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="sz in quickProduct.sizes" :key="sz">
                                    <button type="button" 
                                            @click="
                                                quickProduct.selectedSize = sz;
                                                const szStk = getQuickSizeStock(sz);
                                                if (szStk <= 0) {
                                                    quickProduct.qty = 0;
                                                } else {
                                                    quickProduct.qty = Math.max(1, Math.min(quickProduct.qty || 1, szStk));
                                                }
                                            "
                                            :disabled="getQuickSizeStock(sz) <= 0"
                                            :class="[
                                                quickProduct.selectedSize === sz ? 'bg-black text-white border-black ring-2 ring-neutral-400' : 'bg-white text-gray-800 border-gray-300 hover:border-black',
                                                getQuickSizeStock(sz) <= 0 ? 'opacity-40 cursor-not-allowed bg-gray-100 text-gray-400' : ''
                                            ]"
                                            class="min-w-[48px] min-h-[48px] p-1.5 rounded border text-xs font-bold flex flex-col items-center justify-center transition">
                                        <span x-text="sz"></span>
                                        <template x-if="getQuickSizePrice(sz)">
                                            <span class="text-[9px] font-extrabold leading-tight"
                                                  :class="quickProduct.selectedSize === sz ? 'text-amber-300' : 'text-emerald-700'"
                                                  x-text="'৳' + Number(getQuickSizePrice(sz)).toLocaleString()"></span>
                                        </template>
                                        <span class="text-[8px] font-normal leading-tight mt-0.5" 
                                              :class="getQuickSizeStock(sz) <= 0 ? 'text-rose-400 font-bold' : (quickProduct.selectedSize === sz ? 'text-gray-300' : 'text-gray-400')"
                                              x-text="getQuickSizeStock(sz) > 0 ? (getQuickSizeStock(sz) + ' left') : 'Out'"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Quantity Selector -->
                        <div class="mt-4 space-y-2">
                            <label class="text-xs font-bold uppercase tracking-wider text-gray-700">Quantity:</label>
                            <div class="flex items-center gap-3">
                                <template x-if="getQuickSizeStock(quickProduct.selectedSize) > 0">
                                    <div class="flex items-center border border-gray-300 rounded overflow-hidden">
                                        <button type="button" 
                                                @click="if(quickProduct.qty > 1) quickProduct.qty--" 
                                                :disabled="quickProduct.qty <= 1"
                                                class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-sm font-bold disabled:opacity-40">
                                            -
                                        </button>
                                        <span class="w-10 text-center text-xs font-bold" x-text="quickProduct.qty"></span>
                                        <button type="button" 
                                                @click="
                                                    const curMax = getQuickSizeStock(quickProduct.selectedSize);
                                                    if(quickProduct.qty < curMax) {
                                                        quickProduct.qty++;
                                                    } else {
                                                        if (window.showRobeToast) window.showRobeToast('Maximum available stock for size ' + quickProduct.selectedSize + ' is ' + curMax);
                                                    }
                                                " 
                                                :disabled="quickProduct.qty >= getQuickSizeStock(quickProduct.selectedSize)"
                                                class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-sm font-bold disabled:opacity-40">
                                            +
                                        </button>
                                    </div>
                                </template>
                                <template x-if="getQuickSizeStock(quickProduct.selectedSize) <= 0">
                                    <div class="flex items-center border border-gray-200 bg-gray-100 rounded px-3 py-1.5 opacity-70">
                                        <span class="text-xs font-bold text-gray-500">Qty: 0 (Out of stock)</span>
                                    </div>
                                </template>
                                <template x-if="getQuickSizeStock(quickProduct.selectedSize) > 0">
                                    <span class="text-[11px] text-emerald-600 font-semibold flex items-center gap-1">
                                        <i class="fa-solid fa-circle-check"></i> In Stock (<span x-text="getQuickSizeStock(quickProduct.selectedSize) + ' Available'"></span>)
                                    </span>
                                </template>
                                <template x-if="getQuickSizeStock(quickProduct.selectedSize) <= 0">
                                    <span class="text-[11px] text-rose-600 font-bold flex items-center gap-1">
                                        <i class="fa-solid fa-circle-xmark"></i> Out of Stock for size <span x-text="quickProduct.selectedSize"></span>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="space-y-2 pt-4">
                        <template x-if="getQuickSizeStock(quickProduct.selectedSize) > 0">
                            <button type="button" 
                                    @click="addToCartQuick()" 
                                    class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-bold text-xs md:text-sm uppercase tracking-wider rounded shadow hover:shadow-lg transition flex items-center justify-center gap-2">
                                <i class="fa-solid fa-bag-shopping"></i> Add to Bag
                            </button>
                        </template>
                        <template x-if="getQuickSizeStock(quickProduct.selectedSize) <= 0">
                            <button type="button" 
                                    disabled
                                    class="w-full py-3 bg-neutral-300 text-gray-500 font-bold text-xs md:text-sm uppercase tracking-wider rounded cursor-not-allowed flex items-center justify-center gap-2 border border-neutral-300">
                                <i class="fa-solid fa-ban"></i> Out of Stock (Cannot Order)
                            </button>
                        </template>
                        <a :href="'/product/' + (quickProduct.id || '')" 
                           class="block text-center text-xs font-bold text-gray-700 hover:text-black uppercase tracking-wider py-1.5 underline">
                            View Full Atelier Craft Details →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
