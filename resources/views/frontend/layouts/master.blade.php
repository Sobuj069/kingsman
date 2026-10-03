<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', ($comName ?? 'Kingsman') . ' - Official Online Store | Luxury Ethnic & Men\'s Fashion')</title>
    <meta name="description" content="@yield('meta_description', 'Official Online Store of ' . ($comName ?? 'Kingsman') . '. Discover Panjabi, Kabli Sets, Shirts, T-Shirts, Denim Jeans, Women\'s and Kids collection with premium quality in Bangladesh.')">

    <!-- Dynamic Favicon / System Icon -->
    <link rel="shortcut icon" href="{{ $siteIcon ?? (function_exists('get_setting') && !empty(get_setting('system_icon')) ? asset('uploads/logo/' . get_setting('system_icon')) : asset('backend/images/favicon.png')) }}" type="image/x-icon">
    <link rel="icon" href="{{ $siteIcon ?? (function_exists('get_setting') && !empty(get_setting('system_icon')) ? asset('uploads/logo/' . get_setting('system_icon')) : asset('backend/images/favicon.png')) }}">
    <link rel="apple-touch-icon" href="{{ $siteIcon ?? (function_exists('get_setting') && !empty(get_setting('system_icon')) ? asset('uploads/logo/' . get_setting('system_icon')) : asset('backend/images/favicon.png')) }}">

    <!-- DNS-Prefetch & Preconnect for Lightning Fast Network Handshakes -->
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="https://lh3.googleusercontent.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>

    <!-- Optimized Google Fonts with font-display swap -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;800&family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript>
        <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;800&family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">
    </noscript>

    <!-- FontAwesome 6 Icons (Async / Non-blocking) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    </noscript>

    <!-- Tailwind CSS with Forms & Typography Plugins -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries,typography"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brandBlack: '#111111',
                        brandDark: '#1c1c1c',
                        brandRed: '#dc2626',
                        brandRedHover: '#b91c1c',
                        brandGold: '#c59d5f',
                        brandGoldLight: '#dfb775',
                        brandGray: '#f8f9fa',
                        brandBorder: '#e5e7eb',
                        brandDarkBorder: '#262626',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
                        serif: ['Outfit', 'Inter', 'sans-serif'],
                        royal: ['Cinzel', 'serif'],
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        slideDown: { '0%': { transform: 'translateY(-100%)' }, '100%': { transform: 'translateY(0)' } },
                        slideUp: { '0%': { transform: 'translateY(20px)', opacity: '0' }, '100%': { transform: 'translateY(0)', opacity: '1' } },
                        pulseSlow: { '0%, 100%': { transform: 'scale(1)' }, '50%': { transform: 'scale(1.06)' } },
                        shimmer: { '100%': { transform: 'translateX(100%)' } },
                    },
                    animation: {
                        fadeIn: 'fadeIn 0.3s ease-in-out',
                        slideDown: 'slideDown 0.4s ease-out',
                        slideUp: 'slideUp 0.5s ease-out',
                        pulseSlow: 'pulseSlow 2.5s infinite ease-in-out',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js & Motion JS (Deferred for instant HTML rendering) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/motion@10.16.2/dist/motion.js"></script>

    <style>
        html, body {
            width: 100%;
            max-width: 100%;
            overflow-x: hidden;
            -webkit-tap-highlight-color: transparent;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            color: #1f2937;
            touch-action: manipulation;
        }
        
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
            -webkit-overflow-scrolling: touch;
        }

        /* Prevent auto zoom on iOS Safari inputs */
        @media screen and (max-width: 768px) {
            input, select, textarea {
                font-size: 16px !important;
            }
        }

        /* Product Card & Hover Effects */
        .product-card {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        .product-card:hover {
            box-shadow: 0 12px 28px -6px rgba(0, 0, 0, 0.09);
        }
        .product-card:hover .product-img {
            transform: scale(1.05);
        }
        .product-img {
            transition: transform 0.45s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Watermark */
        .footer-watermark {
            background-image: radial-gradient(circle, rgba(255,255,255,0.03) 0%, rgba(0,0,0,0) 70%);
        }

        /* Custom Scrollbar for drawers */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Product Description Rich HTML Formatting */
        .product-description-content {
            color: #1e293b;
            font-size: 13px;
            line-height: 1.7;
            word-break: break-word;
        }
        .product-description-content p {
            margin-bottom: 0.65rem;
        }
        .product-description-content p:last-child {
            margin-bottom: 0;
        }
        .product-description-content ul {
            list-style-type: disc !important;
            padding-left: 1.35rem !important;
            margin: 0.5rem 0 0.75rem 0 !important;
        }
        .product-description-content ol {
            list-style-type: decimal !important;
            padding-left: 1.35rem !important;
            margin: 0.5rem 0 0.75rem 0 !important;
        }
        .product-description-content li {
            margin-bottom: 0.35rem !important;
            list-style: inherit !important;
        }
        .product-description-content strong,
        .product-description-content b {
            font-weight: 700 !important;
            color: #0f172a;
        }
        .product-description-content em,
        .product-description-content i {
            font-style: italic !important;
        }
        .product-description-content u {
            text-decoration: underline !important;
        }
        .product-description-content h1,
        .product-description-content h2,
        .product-description-content h3,
        .product-description-content h4,
        .product-description-content h5,
        .product-description-content h6 {
            font-weight: 700 !important;
            color: #0f172a;
            margin-top: 0.75rem;
            margin-bottom: 0.4rem;
            line-height: 1.3;
        }
        .product-description-content h1 { font-size: 1.3rem; }
        .product-description-content h2 { font-size: 1.18rem; }
        .product-description-content h3 { font-size: 1.08rem; }
        .product-description-content h4 { font-size: 0.98rem; }
        .product-description-content table {
            width: 100% !important;
            border-collapse: collapse !important;
            margin: 0.75rem 0 !important;
            border: 1px solid #cbd5e1 !important;
        }
        .product-description-content th,
        .product-description-content td {
            border: 1px solid #cbd5e1 !important;
            padding: 6px 10px !important;
            text-align: left;
        }
        .product-description-content th {
            background-color: #f1f5f9 !important;
            font-weight: 600 !important;
        }
        .product-description-content img {
            max-width: 100% !important;
            height: auto !important;
            border-radius: 6px;
            margin: 0.5rem 0;
        }
        .product-description-content blockquote {
            border-left: 3px solid #c59d5f;
            padding-left: 0.75rem;
            font-style: italic;
            color: #64748b;
            margin: 0.75rem 0;
        }
        .product-description-content a {
            color: #2563eb;
            text-decoration: underline;
        }

        /* Active navigation indicator */
        .nav-link {
            position: relative;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: #dc2626;
            transition: all 0.25s ease;
            transform: translateX(-50%);
        }
        .nav-link:hover::after {
            width: 80%;
        }
    </style>

    @stack('styles')
</head>
<body class="antialiased text-gray-900 bg-white" 
      x-data="robeApp()" 
      x-init="initApp()" 
      @open-cart.window="cartOpen = true"
      @close-cart.window="cartOpen = false"
      @toggle-cart.window="cartOpen = ($event.detail && $event.detail.open !== undefined && $event.detail.open !== null) ? $event.detail.open : !cartOpen"
      @keydown.escape="cartOpen = false; quickViewOpen = false; mobileMenuOpen = false; searchOpen = false">

    <!-- Main Navigation Header -->
    @include('frontend.partials.header')

    <!-- Main Content Area -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Slide-over Mini Cart Drawer -->
    @include('frontend.partials.cart-drawer')

    <!-- Quick View Product Modal -->
    @include('frontend.partials.quick-view-modal')

    <!-- Mobile Slide-out Menu Drawer -->
    @include('frontend.partials.mobile-nav')

    <!-- Floating Support & Quick Actions -->
    @include('frontend.partials.floating-actions')

    <!-- Official Brand Footer -->
    @include('frontend.partials.footer')

    <!-- Toast Notification Overlay -->
    <div class="fixed bottom-20 left-1/2 -translate-x-1/2 z-50 flex flex-col gap-2 pointer-events-none"
         x-show="toast.visible"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         style="display: none;">
        <div class="px-5 py-3 rounded-full bg-stone-900/95 text-white text-sm font-medium shadow-2xl backdrop-blur-md flex items-center gap-3 border border-neutral-700 pointer-events-auto">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            <span x-text="toast.message"></span>
            <button @click="toast.visible = false" class="text-gray-400 hover:text-white ml-2">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>
    </div>

    <!-- Alpine Global State & Interactive Store Setup -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('cart', {
                items: (function() {
                    try {
                        const raw = JSON.parse(localStorage.getItem('robe_cart') || '[]');
                        return raw.filter(item => {
                            return item && item.in_stock !== false && (item.stock === undefined || item.stock === null || Number(item.stock) > 0);
                        }).map(item => {
                            const q = item.quantity !== undefined ? Number(item.quantity) : (item.qty !== undefined ? Number(item.qty) : 1);
                            return {
                                ...item,
                                quantity: q,
                                qty: q
                            };
                        });
                    } catch(e) {
                        return [];
                    }
                })(),
                
                get count() {
                    return this.items.reduce((total, item) => total + (item.quantity || item.qty || 1), 0);
                },

                get subtotal() {
                    return this.items.reduce((total, item) => total + ((parseFloat(item.price) || 0) * (item.quantity || item.qty || 1)), 0);
                },

                get hasOutOfStock() {
                    return this.items.some(i => i.in_stock === false || (i.stock !== undefined && i.stock !== null && Number(i.stock) <= 0));
                },

                addItem(product, qty = 1, size = 'L', color = 'Default') {
                    let itemSize = size;
                    let itemColor = color;
                    let itemQty = Number(qty) || 1;
                    let p = product;

                    if (product && typeof product === 'object') {
                        if (product.size) itemSize = product.size;
                        if (product.color) itemColor = product.color;
                        if (product.quantity !== undefined) itemQty = Number(product.quantity);
                        else if (product.qty !== undefined) itemQty = Number(product.qty);
                    }

                    // Strict Stock Validation: Disallow adding out-of-stock items
                    let stock = (p && p.stock !== undefined && p.stock !== null) ? Number(p.stock) : null;
                    if (p && p.variation_stocks && p.variation_stocks[itemColor] && p.variation_stocks[itemColor][itemSize] !== undefined) {
                        stock = Number(p.variation_stocks[itemColor][itemSize]);
                    } else if (p && p.size_stocks && p.size_stocks[itemSize] !== undefined) {
                        stock = Number(p.size_stocks[itemSize]);
                    }
                    const inStock = (p && p.in_stock === false) ? false : (stock !== null ? stock > 0 : true);

                    if (!inStock || (stock !== null && stock <= 0) || itemQty <= 0) {
                        if (window.showRobeToast) {
                            window.showRobeToast(`"${p?.name || 'This garment'}" (Size: ${itemSize}) is currently out of stock and cannot be added.`);
                        }
                        return false;
                    }

                    // Calculate accurate size/variation price
                    let itemPrice = parseFloat(p.price) || 0;
                    let itemOldPrice = p.old_price ? parseFloat(p.old_price) : null;
                    if (p.variation_prices && p.variation_prices[itemColor] && p.variation_prices[itemColor][itemSize] !== undefined && p.variation_prices[itemColor][itemSize] !== null) {
                        itemPrice = parseFloat(p.variation_prices[itemColor][itemSize]) || 0;
                    } else if (p.size_prices && p.size_prices[itemSize] !== undefined && p.size_prices[itemSize] !== null) {
                        itemPrice = parseFloat(p.size_prices[itemSize]) || 0;
                    }

                    if (p.variation_old_prices && p.variation_old_prices[itemColor] && p.variation_old_prices[itemColor][itemSize] !== undefined && p.variation_old_prices[itemColor][itemSize] !== null) {
                        itemOldPrice = parseFloat(p.variation_old_prices[itemColor][itemSize]) || null;
                    } else if (p.size_old_prices && p.size_old_prices[itemSize] !== undefined && p.size_old_prices[itemSize] !== null) {
                        itemOldPrice = parseFloat(p.size_old_prices[itemSize]) || null;
                    }

                    const existingIndex = this.items.findIndex(i => String(i.id) === String(p.id) && String(i.size) === String(itemSize) && String(i.color) === String(itemColor));
                    if (existingIndex > -1) {
                        const currentQ = this.items[existingIndex].quantity || this.items[existingIndex].qty || 1;
                        const maxStock = stock !== null ? stock : (this.items[existingIndex].stock !== undefined && this.items[existingIndex].stock !== null ? Number(this.items[existingIndex].stock) : 999);
                        if (currentQ + itemQty > maxStock) {
                            if (window.showRobeToast) {
                                window.showRobeToast(`Cannot add more. Stock limit of ${maxStock} reached for size ${itemSize}.`);
                            }
                            this.items[existingIndex].quantity = maxStock;
                            this.items[existingIndex].qty = maxStock;
                            this.items[existingIndex].price = itemPrice;
                            this.items[existingIndex].old_price = itemOldPrice;
                            this.persist();
                            return false;
                        }
                        this.items[existingIndex].quantity = currentQ + itemQty;
                        this.items[existingIndex].qty = this.items[existingIndex].quantity;
                        this.items[existingIndex].stock = stock;
                        this.items[existingIndex].price = itemPrice;
                        this.items[existingIndex].old_price = itemOldPrice;
                    } else {
                        if (stock !== null && itemQty > stock) {
                            if (window.showRobeToast) {
                                window.showRobeToast(`Only ${stock} items available in stock for size ${itemSize}.`);
                            }
                            itemQty = stock;
                        }
                        this.items.push({
                            id: p.id,
                            name: p.name,
                            price: itemPrice,
                            old_price: itemOldPrice,
                            image: p.image,
                            size: itemSize,
                            color: itemColor,
                            quantity: itemQty,
                            qty: itemQty,
                            stock: stock,
                            size_stocks: p.size_stocks || null,
                            variation_stocks: p.variation_stocks || null,
                            size_prices: p.size_prices || null,
                            variation_prices: p.variation_prices || null,
                            in_stock: true
                        });
                    }
                    this.persist();
                    return true;
                },

                updateQuantity(identifier, qty, size = null, color = null) {
                    let index = -1;
                    if (typeof identifier === 'number' && size === null && color === null && identifier < this.items.length && identifier >= 0) {
                        index = identifier;
                    } else {
                        index = this.items.findIndex(i => {
                            const idMatch = String(i.id) === String(identifier);
                            const sizeMatch = size ? String(i.size) === String(size) : true;
                            const colorMatch = color ? String(i.color) === String(color) : true;
                            return idMatch && sizeMatch && colorMatch;
                        });
                    }

                    if (index > -1) {
                        const newQty = Number(qty);
                        if (newQty <= 0) {
                            this.removeItem(index);
                            return;
                        }

                        const item = this.items[index];
                        if (item.in_stock === false || (item.stock !== undefined && item.stock !== null && Number(item.stock) <= 0)) {
                            if (window.showRobeToast) {
                                window.showRobeToast(`"${item.name || 'This item'}" is out of stock and removed.`);
                            }
                            this.removeItem(index);
                            return;
                        }

                        const maxStock = (item.stock !== undefined && item.stock !== null) ? Number(item.stock) : 999;
                        if (newQty > maxStock) {
                            if (window.showRobeToast) {
                                window.showRobeToast(`Cannot increase quantity. Maximum available stock is ${maxStock}.`);
                            }
                            this.items[index].quantity = maxStock;
                            this.items[index].qty = maxStock;
                            this.persist();
                            return;
                        }

                        this.items[index].quantity = newQty;
                        this.items[index].qty = newQty;
                        this.persist();
                    }
                },

                updateQty(identifier, qty, size = null, color = null) {
                    this.updateQuantity(identifier, qty, size, color);
                },

                removeItem(identifier, size = null, color = null) {
                    let index = -1;
                    if (typeof identifier === 'number' && size === null && color === null && identifier < this.items.length && identifier >= 0) {
                        index = identifier;
                    } else {
                        index = this.items.findIndex(i => {
                            const idMatch = String(i.id) === String(identifier);
                            const sizeMatch = size ? String(i.size) === String(size) : true;
                            const colorMatch = color ? String(i.color) === String(color) : true;
                            return idMatch && sizeMatch && colorMatch;
                        });
                    }

                    if (index > -1) {
                        const removed = this.items.splice(index, 1);
                        this.persist();
                        if (window.showRobeToast && removed.length > 0) {
                            window.showRobeToast('Item removed from Shopping Bag');
                        }
                    }
                },

                clear() {
                    this.items = [];
                    this.persist();
                },

                clearCart() {
                    this.clear();
                },

                toggle(forceState = null) {
                    window.dispatchEvent(new CustomEvent('toggle-cart', { detail: { open: forceState } }));
                    const appEl = document.querySelector('body[x-data]');
                    if (appEl && appEl._x_dataStack && appEl._x_dataStack[0]) {
                        const app = appEl._x_dataStack[0];
                        if (app && 'cartOpen' in app) {
                            app.cartOpen = forceState !== null ? forceState : !app.cartOpen;
                        }
                    }
                },

                persist() {
                    localStorage.setItem('robe_cart', JSON.stringify(this.items));
                }
            });

            Alpine.store('wishlist', {
                items: JSON.parse(localStorage.getItem('robe_wishlist') || '[]'),

                get count() {
                    return this.items.length;
                },

                persist() {
                    localStorage.setItem('robe_wishlist', JSON.stringify(this.items));
                },

                clear() {
                    this.items.splice(0, this.items.length);
                    this.persist();
                    if (window.showRobeToast) {
                        window.showRobeToast('Wishlist has been cleared');
                    }
                },

                remove(productId) {
                    const idx = this.items.findIndex(i => String(i.id) === String(productId));
                    if (idx > -1) {
                        this.items.splice(idx, 1);
                        this.persist();
                        if (window.showRobeToast) {
                            window.showRobeToast('Removed from Wishlist');
                        }
                    }
                },

                toggle(product) {
                    if (!product || !product.id) return;
                    const idx = this.items.findIndex(i => String(i.id) === String(product.id));
                    if (idx > -1) {
                        this.items.splice(idx, 1);
                        this.persist();
                        if (window.showRobeToast) {
                            window.showRobeToast('Removed from Wishlist');
                        }
                    } else {
                        this.items.push(product);
                        this.persist();
                        if (window.showRobeToast) {
                            window.showRobeToast('Added to Wishlist ❤');
                        }
                    }
                },

                has(productId) {
                    if (!productId) return false;
                    return this.items.some(i => String(i.id) === String(productId));
                },

                moveAllToBag() {
                    if (!this.items || this.items.length === 0) return;
                    const cartStore = Alpine.store('cart');
                    if (!cartStore) return;

                    let addedCount = 0;
                    const remaining = [];

                    for (let i = 0; i < this.items.length; i++) {
                        const item = this.items[i];
                        const inStock = (item.in_stock !== false) && (item.stock === undefined || item.stock === null || Number(item.stock) > 0);
                        if (inStock) {
                            const added = cartStore.addItem(
                                item,
                                1,
                                item.selectedSize || item.size || 'L',
                                item.selectedColor || item.color || 'Default'
                            );
                            if (added !== false) {
                                addedCount++;
                            } else {
                                remaining.push(item);
                            }
                        } else {
                            remaining.push(item);
                        }
                    }

                    this.items.splice(0, this.items.length, ...remaining);
                    this.persist();

                    if (addedCount > 0) {
                        if (window.showRobeToast) {
                            window.showRobeToast(`${addedCount} available item(s) moved to your Shopping Bag!`);
                        }
                        cartStore.toggle(true);
                    } else {
                        if (window.showRobeToast) {
                            window.showRobeToast('Items in your wishlist are currently out of stock.');
                        }
                    }
                },

                moveToBag(item) {
                    if (!item || !item.id) return;
                    const inStock = (item.in_stock !== false) && (item.stock === undefined || item.stock === null || Number(item.stock) > 0);
                    if (!inStock) {
                        if (window.showRobeToast) {
                            window.showRobeToast(`"${item.name || 'This item'}" is currently out of stock.`);
                        }
                        return;
                    }

                    const cartStore = Alpine.store('cart');
                    if (!cartStore) return;

                    const added = cartStore.addItem(
                        item,
                        1,
                        item.selectedSize || item.size || 'L',
                        item.selectedColor || item.color || 'Default'
                    );

                    if (added !== false) {
                        this.remove(item.id);
                        if (window.showRobeToast) {
                            window.showRobeToast('Moved to Shopping Bag!');
                        }
                        cartStore.toggle(true);
                    }
                }
            });
        });

        function robeApp() {
            return {
                cartOpen: false,
                quickViewOpen: false,
                mobileMenuOpen: false,
                searchOpen: false,
                searchQuery: '',
                searchResults: [],
                searchLoading: false,
                quickProduct: {
                    id: 1,
                    name: '',
                    price: 0,
                    old_price: 0,
                    image: '',
                    category: '',
                    description: '',
                    sizes: ['M', 'L', 'XL', 'XXL'],
                    colors: ['Black', 'Teal', 'White'],
                    selectedSize: 'L',
                    selectedColor: 'Black',
                    qty: 1
                },
                toast: {
                    visible: false,
                    message: ''
                },

                initApp() {
                    window.showToast = window.showRobeToast = (msg) => {
                        this.toast.message = msg;
                        this.toast.visible = true;
                        setTimeout(() => {
                            this.toast.visible = false;
                        }, 3000);
                    };

                    // Trigger Motion JS entrance animations on DOM ready
                    if (window.Motion) {
                        const { animate, inView, stagger } = window.Motion;
                        
                        // Smoothly animate in hero content
                        animate('.motion-hero-title', { opacity: [0, 1], y: [30, 0] }, { duration: 0.8, easing: 'ease-out' });
                        animate('.motion-hero-sub', { opacity: [0, 1], y: [20, 0] }, { duration: 0.8, delay: 0.2, easing: 'ease-out' });
                        animate('.motion-hero-btn', { opacity: [0, 1], scale: [0.9, 1] }, { duration: 0.6, delay: 0.4, easing: 'ease-out' });

                        // Scroll trigger for section product grids
                        inView('.motion-grid', ({ target }) => {
                            const cards = target.querySelectorAll('.product-card');
                            if (cards.length > 0) {
                                animate(cards, { opacity: [0, 1], y: [30, 0] }, { delay: stagger(0.08), duration: 0.6 });
                            }
                        });
                    }
                },

                openQuickView(product) {
                    const sizes = product.sizes || ['M', 'L', 'XL', 'XXL'];
                    const isProductInStock = product.in_stock !== false;
                    const stockVal = isProductInStock && (product.stock !== undefined && product.stock !== null) ? Math.max(0, Number(product.stock)) : (isProductInStock ? 10 : 0);
                    const inStock = isProductInStock && (product.stock !== undefined && product.stock !== null ? Number(product.stock) > 0 : true);

                    // Compute size_stocks if not directly provided
                    let sizeStocks = product.size_stocks || null;
                    if (!sizeStocks && sizes.length > 0) {
                        sizeStocks = {};
                        if (!inStock || stockVal <= 0) {
                            sizes.forEach(sz => sizeStocks[sz] = 0);
                        } else {
                            const basePerSize = Math.floor(stockVal / sizes.length);
                            const rem = stockVal % sizes.length;
                            sizes.forEach((sz, idx) => {
                                sizeStocks[sz] = basePerSize + (idx < rem ? 1 : 0);
                            });
                        }
                    }

                    // Choose first in-stock size
                    let selSize = sizes[0] || 'M';
                    if (sizeStocks && sizeStocks[selSize] !== undefined && Number(sizeStocks[selSize]) <= 0) {
                        for (let sz of sizes) {
                            if (Number(sizeStocks[sz]) > 0) {
                                selSize = sz;
                                break;
                            }
                        }
                    }

                    const selColor = (product.colors && product.colors[0] && typeof product.colors[0] === 'object' ? product.colors[0].name : product.colors?.[0]) || 'Default';
                    
                    this.quickProduct = {
                        id: product.id,
                        name: product.name,
                        price: product.price,
                        old_price: product.old_price || null,
                        image: product.image,
                        category: product.category || 'Apparel',
                        description: product.description || 'Premium craftsmanship with high-grade breathable fabric, tailored silhouette, and signature Kingsman finish.',
                        sizes: sizes,
                        colors: product.colors || ['Default'],
                        selectedSize: selSize,
                        selectedColor: selColor,
                        stock: inStock ? stockVal : 0,
                        size_stocks: sizeStocks,
                        variation_stocks: product.variation_stocks || null,
                        size_prices: product.size_prices || null,
                        size_old_prices: product.size_old_prices || null,
                        variation_prices: product.variation_prices || null,
                        variation_old_prices: product.variation_old_prices || null,
                        in_stock: inStock,
                        qty: 0
                    };

                    const sizeStock = this.getQuickSizeStock(selSize);
                    this.quickProduct.in_stock = inStock && sizeStock > 0;
                    this.quickProduct.qty = (inStock && sizeStock > 0) ? 1 : 0;
                    this.quickViewOpen = true;
                },

                getQuickPrice() {
                    if (!this.quickProduct) return 0;
                    const sz = this.quickProduct.selectedSize;
                    const c = typeof this.quickProduct.selectedColor === 'object' ? this.quickProduct.selectedColor.name : this.quickProduct.selectedColor;
                    if (this.quickProduct.variation_prices && c && this.quickProduct.variation_prices[c] && this.quickProduct.variation_prices[c][sz] !== undefined && this.quickProduct.variation_prices[c][sz] !== null) {
                        return Number(this.quickProduct.variation_prices[c][sz]);
                    }
                    if (this.quickProduct.size_prices && this.quickProduct.size_prices[sz] !== undefined && this.quickProduct.size_prices[sz] !== null) {
                        return Number(this.quickProduct.size_prices[sz]);
                    }
                    return Number(this.quickProduct.price) || 0;
                },

                getQuickOldPrice() {
                    if (!this.quickProduct) return null;
                    const sz = this.quickProduct.selectedSize;
                    const c = typeof this.quickProduct.selectedColor === 'object' ? this.quickProduct.selectedColor.name : this.quickProduct.selectedColor;
                    if (this.quickProduct.variation_old_prices && c && this.quickProduct.variation_old_prices[c] && this.quickProduct.variation_old_prices[c][sz] !== undefined && this.quickProduct.variation_old_prices[c][sz] !== null) {
                        return Number(this.quickProduct.variation_old_prices[c][sz]);
                    }
                    if (this.quickProduct.size_old_prices && this.quickProduct.size_old_prices[sz] !== undefined && this.quickProduct.size_old_prices[sz] !== null) {
                        return Number(this.quickProduct.size_old_prices[sz]);
                    }
                    return this.quickProduct.old_price ? Number(this.quickProduct.old_price) : null;
                },

                getQuickSizePrice(sz) {
                    if (!this.quickProduct) return 0;
                    const c = typeof this.quickProduct.selectedColor === 'object' ? this.quickProduct.selectedColor.name : this.quickProduct.selectedColor;
                    if (this.quickProduct.variation_prices && c && this.quickProduct.variation_prices[c] && this.quickProduct.variation_prices[c][sz] !== undefined && this.quickProduct.variation_prices[c][sz] !== null) {
                        return Number(this.quickProduct.variation_prices[c][sz]);
                    }
                    if (this.quickProduct.size_prices && this.quickProduct.size_prices[sz] !== undefined && this.quickProduct.size_prices[sz] !== null) {
                        return Number(this.quickProduct.size_prices[sz]);
                    }
                    return Number(this.quickProduct.price) || 0;
                },

                getQuickSizeStock(sz) {
                    if (!this.quickProduct || this.quickProduct.in_stock === false) return 0;
                    const c = this.quickProduct.selectedColor;
                    if (this.quickProduct.variation_stocks && c && this.quickProduct.variation_stocks[c] && this.quickProduct.variation_stocks[c][sz] !== undefined) {
                        return Number(this.quickProduct.variation_stocks[c][sz]);
                    }
                    if (this.quickProduct.size_stocks && this.quickProduct.size_stocks[sz] !== undefined) {
                        return Number(this.quickProduct.size_stocks[sz]);
                    }
                    return (this.quickProduct.stock !== undefined && this.quickProduct.stock !== null) ? Number(this.quickProduct.stock) : 0;
                },

                selectQuickColor(color) {
                    const cName = (typeof color === 'object' && color !== null) ? (color.name || color) : color;
                    this.quickProduct.selectedColor = cName;
                    const curStock = this.getQuickSizeStock(this.quickProduct.selectedSize);
                    if (curStock <= 0) {
                        let found = null;
                        for (let sz of (this.quickProduct.sizes || [])) {
                            if (this.getQuickSizeStock(sz) > 0) {
                                found = sz;
                                break;
                            }
                        }
                        if (found) {
                            this.quickProduct.selectedSize = found;
                            this.quickProduct.qty = 1;
                        } else {
                            this.quickProduct.qty = 0;
                        }
                    } else {
                        this.quickProduct.qty = Math.max(1, Math.min(this.quickProduct.qty || 1, curStock));
                    }
                },

                addToCartQuick() {
                    const selSize = this.quickProduct.selectedSize || 'M';
                    const sizeStock = this.getQuickSizeStock(selSize);

                    if (this.quickProduct.in_stock === false || sizeStock <= 0 || (this.quickProduct.qty || 0) <= 0) {
                        window.showRobeToast(`"${this.quickProduct.name}" (Size: ${selSize}) is currently out of stock.`);
                        return;
                    }

                    if (this.quickProduct.qty > sizeStock) {
                        this.quickProduct.qty = sizeStock;
                    }

                    const added = Alpine.store('cart').addItem(
                        this.quickProduct, 
                        this.quickProduct.qty, 
                        this.quickProduct.selectedSize, 
                        this.quickProduct.selectedColor
                    );
                    if (added !== false) {
                        this.quickViewOpen = false;
                        this.cartOpen = true;
                        window.showRobeToast(`Added ${this.quickProduct.name} (${selSize}) to Cart`);
                    }
                },

                addDirectToCart(product) {
                    if (!product || product.in_stock === false || (product.stock !== undefined && product.stock !== null && Number(product.stock) <= 0)) {
                        window.showRobeToast(`"${product ? (product.name || 'This item') : 'This item'}" is currently out of stock.`);
                        return;
                    }

                    const sizes = product.sizes || ['38 (S)', '40 (M)', '42 (L)', '44 (XL)', '46 (XXL)'];
                    let chosenSize = '42 (L)';
                    const chosenColor = (product.colors && product.colors[0] && typeof product.colors[0] === 'object' ? product.colors[0].name : product.colors?.[0]) || 'Default';
                    
                    if (product.variation_stocks && product.variation_stocks[chosenColor]) {
                        let found = null;
                        for (let sz of sizes) {
                            if (Number(product.variation_stocks[chosenColor][sz]) > 0) {
                                found = sz;
                                break;
                            }
                        }
                        if (!found) {
                            window.showRobeToast(`"${product.name || 'This item'}" is currently out of stock in all sizes.`);
                            return;
                        }
                        chosenSize = found;
                    } else if (product.size_stocks) {
                        let found = null;
                        for (let sz of sizes) {
                            if (Number(product.size_stocks[sz]) > 0) {
                                found = sz;
                                break;
                            }
                        }
                        if (!found) {
                            window.showRobeToast(`"${product.name || 'This item'}" is currently out of stock in all sizes.`);
                            return;
                        }
                        chosenSize = found;
                    } else if (sizes.length > 0) {
                        chosenSize = sizes[0];
                    }

                    const added = Alpine.store('cart').addItem(product, 1, chosenSize, chosenColor);
                    if (added !== false) {
                        this.cartOpen = true;
                        window.showRobeToast(`Added ${product.name} (${chosenSize}) to Bag`);
                    }
                },

                performSearch() {
                    if (this.searchQuery.trim().length < 2) {
                        this.searchResults = [];
                        return;
                    }
                    this.searchLoading = true;
                    fetch(`/search?q=${encodeURIComponent(this.searchQuery)}`)
                        .then(res => res.json())
                        .then(data => {
                            this.searchResults = data.results || [];
                            this.searchLoading = false;
                        })
                        .catch(() => {
                            this.searchLoading = false;
                        });
                },

                submitSearch() {
                    const q = (this.searchQuery || '').trim();
                    if (!q) return;
                    if (this.searchResults && this.searchResults.length === 1) {
                        window.location.href = this.searchResults[0].url || `/product/${this.searchResults[0].id}`;
                    } else {
                        window.location.href = `/products?q=${encodeURIComponent(q)}`;
                    }
                }
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
