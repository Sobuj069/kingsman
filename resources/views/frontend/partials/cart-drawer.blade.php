<!-- Slide-over Mini Cart Drawer (Alpine.js Powered) -->
<div x-show="cartOpen" 
     class="fixed inset-0 z-50 overflow-hidden" 
     aria-labelledby="slide-over-title" 
     role="dialog" 
     aria-modal="true"
     style="display: none;">
    
    <!-- Background Backdrop -->
    <div x-show="cartOpen" 
         x-transition:enter="ease-in-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in-out duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="cartOpen = false"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

    <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
        <!-- Drawer Panel -->
        <div x-show="cartOpen"
             x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="w-screen max-w-md bg-white shadow-2xl flex flex-col justify-between">

            <!-- Cart Header -->
            <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-neutral-900 text-white">
                <div class="flex items-center gap-2.5">
                    <i class="fa-solid fa-bag-shopping text-red-500 text-lg"></i>
                    <h2 class="text-base font-bold uppercase tracking-wider">Your Shopping Bag</h2>
                    <span class="text-xs bg-neutral-800 text-neutral-300 px-2 py-0.5 rounded-full" 
                          x-text="'(' + $store.cart.count + ' items)'"></span>
                </div>
                <button @click="cartOpen = false" 
                        class="text-neutral-400 hover:text-white p-1 rounded-full transition" 
                        aria-label="Close cart">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Cart Items List Container -->
            <div class="flex-1 overflow-y-auto p-5 custom-scrollbar">
                <!-- Empty State -->
                <template x-if="$store.cart.items.length === 0">
                    <div class="py-16 text-center">
                        <div class="w-20 h-20 mx-auto rounded-full bg-neutral-100 flex items-center justify-center text-neutral-400 mb-4">
                            <i class="fa-solid fa-cart-arrow-down text-3xl"></i>
                        </div>
                        <h3 class="text-base font-bold text-gray-800 uppercase tracking-wider mb-1">Your bag is empty</h3>
                        <p class="text-xs text-gray-500 max-w-xs mx-auto mb-6">Looks like you haven't added any premium outfits to your cart yet.</p>
                        <button @click="cartOpen = false" 
                                class="px-6 py-2.5 bg-black text-white text-xs font-bold uppercase tracking-wider rounded-md hover:bg-neutral-800 transition">
                            Explore Collections
                        </button>
                    </div>
                </template>

                <!-- Items Loop -->
                <template x-if="$store.cart.items.length > 0">
                    <ul class="divide-y divide-gray-100">
                        <template x-for="(item, index) in $store.cart.items" :key="item.id + '_' + (item.size || 'M') + '_' + (item.color || 'Def') + '_' + index">
                            <li class="py-4 flex gap-4 items-start">
                                <!-- Thumbnail -->
                                <div class="w-20 h-24 rounded bg-gray-100 overflow-hidden flex-shrink-0 border border-gray-200">
                                    <img :src="item.image || '{{ asset('frontend/images/no-image.svg') }}'" :alt="item.name" class="w-full h-full object-cover">
                                </div>
                                <!-- Info -->
                                <div class="flex-1 flex flex-col justify-between min-w-0">
                                    <div>
                                        <div class="flex justify-between items-start gap-2">
                                            <h4 class="text-xs md:text-sm font-semibold text-gray-900 line-clamp-1" x-text="item.name"></h4>
                                            <button type="button" 
                                                    @click.stop="$store.cart.removeItem(item.id, item.size, item.color)" 
                                                    class="w-7 h-7 flex items-center justify-center rounded text-gray-400 hover:text-red-600 hover:bg-red-50 transition text-sm cursor-pointer shrink-0"
                                                    title="Remove Item">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </div>
                                        <div class="flex items-center gap-2 mt-1 text-[11px] text-gray-500">
                                            <span class="bg-gray-100 px-1.5 py-0.5 rounded font-medium" x-text="'Size: ' + (item.size || 'L')"></span>
                                            <span class="bg-gray-100 px-1.5 py-0.5 rounded font-medium" x-text="'Color: ' + (item.color || 'Default')"></span>
                                        </div>
                                        <template x-if="item.in_stock === false || (item.stock !== undefined && item.stock !== null && Number(item.stock) <= 0)">
                                            <span class="text-[9px] font-bold text-rose-600 bg-rose-50 border border-rose-200 px-1.5 py-0.5 rounded uppercase mt-1 inline-block">
                                                Out of Stock (Remove to proceed)
                                            </span>
                                        </template>
                                    </div>

                                    <!-- Price & Qty Controls -->
                                    <div class="flex items-center justify-between mt-3">
                                        <div class="flex items-center border border-gray-300 rounded overflow-hidden">
                                            <button type="button" 
                                                    @click.stop="$store.cart.updateQuantity(item.id, (item.quantity || item.qty || 1) - 1, item.size, item.color)" 
                                                    :disabled="(item.quantity || item.qty || 1) <= 1"
                                                    class="w-6 h-6 flex items-center justify-center bg-gray-50 hover:bg-gray-200 text-gray-600 text-xs font-bold disabled:opacity-40 disabled:cursor-not-allowed">
                                                -
                                            </button>
                                            <span class="w-8 text-center text-xs font-bold text-gray-800" x-text="item.quantity || item.qty || 1"></span>
                                            <button type="button" 
                                                    @click.stop="$store.cart.updateQuantity(item.id, (item.quantity || item.qty || 1) + 1, item.size, item.color)" 
                                                    :disabled="item.stock !== undefined && item.stock !== null && (item.quantity || item.qty || 1) >= Number(item.stock)"
                                                    class="w-6 h-6 flex items-center justify-center bg-gray-50 hover:bg-gray-200 text-gray-600 text-xs font-bold disabled:opacity-40 disabled:cursor-not-allowed">
                                                +
                                            </button>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-sm font-bold text-gray-900" x-text="'৳ ' + ((item.price || 0) * (item.quantity || item.qty || 1)).toLocaleString()"></span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </template>
                    </ul>
                </template>
            </div>

            <!-- Cart Footer & Checkout Button -->
            <template x-if="$store.cart.items.length > 0">
                <div class="p-5 border-t border-gray-200 bg-gray-50 space-y-3">
                    <template x-if="$store.cart.hasOutOfStock">
                        <div class="p-2.5 bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold rounded flex items-center gap-2">
                            <i class="fa-solid fa-triangle-exclamation text-rose-600"></i>
                            <span>Some item(s) are out of stock. Please remove them before checkout.</span>
                        </div>
                    </template>

                    <div class="space-y-1.5 text-xs text-gray-600">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span class="font-bold text-gray-900" x-text="'৳ ' + $store.cart.subtotal.toLocaleString()"></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Delivery:</span>
                            <span class="text-emerald-600 font-semibold" x-text="$store.cart.subtotal >= 3000 ? 'FREE' : '৳ 80'"></span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-gray-200 flex justify-between items-baseline">
                        <span class="text-sm font-bold uppercase text-gray-900">Total:</span>
                        <span class="text-lg font-extrabold text-black" 
                              x-text="'৳ ' + ($store.cart.subtotal + ($store.cart.subtotal >= 3000 ? 0 : 80)).toLocaleString()"></span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-2 pt-1">
                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ route('cart') }}" 
                               @click="cartOpen = false"
                               class="py-3 bg-white border border-gray-300 hover:bg-gray-100 text-black font-bold text-xs uppercase tracking-wider rounded text-center transition">
                                View Bag
                            </a>
                            <template x-if="!$store.cart.hasOutOfStock">
                                <a href="{{ route('checkout') }}" 
                                   @click="cartOpen = false"
                                   class="py-3 bg-black hover:bg-neutral-800 text-white font-bold text-xs uppercase tracking-wider rounded text-center transition flex items-center justify-center gap-1.5 shadow-md">
                                    <i class="fa-solid fa-lock text-xs"></i> Checkout
                                </a>
                            </template>
                            <template x-if="$store.cart.hasOutOfStock">
                                <button type="button" 
                                        disabled
                                        class="py-3 bg-neutral-200 text-neutral-400 font-bold text-xs uppercase tracking-wider rounded text-center cursor-not-allowed flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-ban text-xs"></i> Out of Stock
                                </button>
                            </template>
                        </div>
                        <button @click="$store.cart.clear(); window.showRobeToast('Cart cleared')" 
                                class="w-full py-2 bg-transparent hover:bg-gray-200 text-gray-500 text-xs font-semibold uppercase tracking-wider rounded transition">
                            Clear Bag
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>
