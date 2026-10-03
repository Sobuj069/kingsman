<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariation;
use App\Models\BusinessSetting;
use App\Models\Banner;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Transaction;
use App\Models\PurchaseItem;

class FrontendController extends Controller
{
    /**
     * Display the ROBE e-commerce storefront home page.
     */
    public function index()
    {
        // Cache dynamic categories for 1 hour
        $categories = Cache::remember('frontend_categories', 3600, function () {
            try {
                if (class_exists(Category::class)) {
                    return Category::take(12)->get();
                }
            } catch (\Exception $e) {}
            return collect();
        });

        // Cache dynamic banners for 1 hour
        $heroBanners = Cache::remember('frontend_banners_hero', 3600, function () {
            try {
                if (class_exists(Banner::class)) {
                    return Banner::active()->position('hero')->ordered()->get();
                }
            } catch (\Exception $e) {}
            return collect();
        });

        $dualBanners = Cache::remember('frontend_banners_dual', 3600, function () {
            try {
                if (class_exists(Banner::class)) {
                    return Banner::active()->position('promo_dual')->ordered()->get();
                }
            } catch (\Exception $e) {}
            return collect();
        });

        $festiveBanners = Cache::remember('frontend_banners_festive', 3600, function () {
            try {
                if (class_exists(Banner::class)) {
                    return Banner::active()->position('promo_festive')->ordered()->get();
                }
            } catch (\Exception $e) {}
            return collect();
        });

        // Cache dynamic products for 15 minutes
        $newArrivals = Cache::remember('frontend_new_arrivals', 900, function () {
            try {
                if (class_exists(Product::class)) {
                    return Product::with(['category', 'subCategory', 'brand', 'product_variations.size', 'product_variations.color'])
                        ->active()
                        ->where('is_new_arrival', 1)
                        ->latest()
                        ->take(12)
                        ->get();
                }
            } catch (\Exception $e) {}
            return collect();
        });

        $topSelling = Cache::remember('frontend_top_selling', 900, function () {
            try {
                if (class_exists(Product::class)) {
                    return Product::with(['category', 'subCategory', 'brand', 'product_variations.size', 'product_variations.color'])
                        ->active()
                        ->where('is_top_selling', 1)
                        ->latest()
                        ->take(12)
                        ->get();
                }
            } catch (\Exception $e) {}
            return collect();
        });

        // Cache dynamic category product sections for 15 minutes
        $categorySections = Cache::remember('frontend_home_category_sections', 900, function () {
            try {
                if (class_exists(Category::class)) {
                    return Category::with(['products' => function ($q) {
                        $q->with(['category', 'subCategory', 'brand', 'product_variations.size', 'product_variations.color'])
                          ->active()
                          ->latest();
                    }])
                    ->whereHas('products', function ($q) {
                        $q->active();
                    })
                    ->get();
                }
            } catch (\Exception $e) {}
            return collect();
        });

        // Business settings cache for 1 hour
        $settings = Cache::remember('frontend_business_settings', 3600, function () {
            try {
                if (class_exists(BusinessSetting::class)) {
                    return BusinessSetting::first();
                }
            } catch (\Exception $e) {}
            return null;
        });

        return view('frontend.home', compact(
            'categories', 
            'heroBanners', 
            'dualBanners', 
            'festiveBanners', 
            'newArrivals', 
            'topSelling', 
            'categorySections',
            'settings'
        ));
    }

    /**
     * Helper to resolve hex code for color display swatches
     */
    public function getColorHexByName(?string $name): string
    {
        if (empty($name)) {
            return '#0B0B0C';
        }

        $n = strtolower(trim($name));
        $colorMap = [
            'noir black' => '#0B0B0C',
            'obsidian' => '#0B0B0C',
            'jet black' => '#0B0B0C',
            'black' => '#0B0B0C',
            'kalo' => '#0B0B0C',
            'pearl ivory' => '#F8F5F0',
            'pearl' => '#F8F5F0',
            'ivory' => '#F8F5F0',
            'off white' => '#F1EFEA',
            'white' => '#FFFFFF',
            'shada' => '#FFFFFF',
            'imperial navy' => '#0F172A',
            'midnight navy' => '#0F172A',
            'navy' => '#0F172A',
            'royal blue' => '#1D4ED8',
            'sapphire' => '#1D4ED8',
            'blue' => '#2563EB',
            'sky blue' => '#38BDF8',
            'neel' => '#1D4ED8',
            'emerald teal' => '#0F766E',
            'emerald' => '#047857',
            'teal' => '#0F766E',
            'forest' => '#1B3022',
            'green' => '#15803D',
            'olive' => '#4D7C0F',
            'shobuj' => '#15803D',
            'rich burgundy' => '#701A75',
            'burgundy' => '#701A75',
            'crimson' => '#BE123C',
            'wine' => '#881337',
            'maroon' => '#881337',
            'red' => '#DC2626',
            'lal' => '#DC2626',
            'classic plum' => '#581C87',
            'plum' => '#581C87',
            'purple' => '#7E22CE',
            'violet' => '#6D28D9',
            'beguni' => '#7E22CE',
            'pink' => '#EC4899',
            'rose' => '#F43F5E',
            'gulabi' => '#EC4899',
            'warm khaki' => '#A16207',
            'khaki' => '#A16207',
            'almond' => '#D4B996',
            'beige' => '#E5D0B8',
            'brown' => '#78350F',
            'chocolate' => '#451A03',
            'tan' => '#B45309',
            'badami' => '#78350F',
            'canary' => '#EAB308',
            'yellow' => '#EAB308',
            'mustard' => '#CA8A04',
            'holud' => '#EAB308',
            'tangerine' => '#EA580C',
            'orange' => '#F97316',
            'komola' => '#F97316',
            'grey' => '#64748B',
            'gray' => '#64748B',
            'silver' => '#94A3B8',
            'ash' => '#64748B',
            'gold' => '#D97706',
            'golden' => '#D97706',
            'champagne' => '#EFE6DB',
        ];

        foreach ($colorMap as $key => $hex) {
            if (str_contains($n, $key)) {
                return $hex;
            }
        }

        return '#0B0B0C';
    }

    /**
     * Find matching product variation with priority for exact match & stock availability
     */
    public function matchProductVariation($product, ?string $itemSize, ?string $itemColor)
    {
        if (!$product || $product->product_variations->isEmpty()) {
            return null;
        }

        $cleanSize = $itemSize ? trim($itemSize) : '';
        $cleanColor = $itemColor ? trim($itemColor) : '';

        // 1. Exact match on both size and color
        $exactBoth = $product->product_variations->filter(function ($v) use ($cleanSize, $cleanColor) {
            $s = trim($v->size?->size ?? '');
            $c = trim($v->color?->color ?? '');
            $sOk = empty($cleanSize) || strcasecmp($s, $cleanSize) === 0;
            $cOk = empty($cleanColor) || strcasecmp($c, $cleanColor) === 0;
            return $sOk && $cOk;
        });

        if ($exactBoth->isNotEmpty()) {
            foreach ($exactBoth as $eb) {
                $stk = (float) PurchaseItem::where('product_variation_id', $eb->id)->sum('stock_qty');
                if ($stk > 0) {
                    return $eb;
                }
            }
            return $exactBoth->first();
        }

        // 2. Exact match on size
        if (!empty($cleanSize)) {
            $sizeMatches = $product->product_variations->filter(function ($v) use ($cleanSize) {
                return strcasecmp(trim($v->size?->size ?? ''), $cleanSize) === 0;
            });

            if ($sizeMatches->isNotEmpty()) {
                foreach ($sizeMatches as $sm) {
                    $stk = (float) PurchaseItem::where('product_variation_id', $sm->id)->sum('stock_qty');
                    if ($stk > 0) {
                        return $sm;
                    }
                }
                return $sizeMatches->first();
            }
        }

        // 3. Fallback: normalized alphanumeric match
        if (!empty($cleanSize)) {
            $normSize = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $cleanSize));
            $candidates = $product->product_variations->filter(function ($v) use ($normSize) {
                $sNorm = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $v->size?->size ?? ''));
                return !empty($sNorm) && ($sNorm === $normSize || str_contains($sNorm, $normSize) || str_contains($normSize, $sNorm));
            });

            if ($candidates->isNotEmpty()) {
                foreach ($candidates as $cand) {
                    $stk = (float) PurchaseItem::where('product_variation_id', $cand->id)->sum('stock_qty');
                    if ($stk > 0) {
                        return $cand;
                    }
                }
                return $candidates->first();
            }
        }

        return $product->product_variations->first();
    }

    /**
     * Display the luxury product details page.
     */
    public function productDetails($id = 1)
    {
        $product = null;

        // Try finding product from Database
        try {
            if (class_exists(Product::class) && $id) {
                $dbProduct = Product::with(['category', 'subCategory', 'brand', 'product_variations.size', 'product_variations.color'])->find($id);
                if ($dbProduct) {
                    $sizes = $dbProduct->product_variations->map(fn($v) => $v->size?->size)->filter()->unique()->values()->toArray();
                    if (empty($sizes) && $dbProduct->sizes) {
                        $sizes = $dbProduct->sizes->pluck('size')->filter()->unique()->values()->toArray();
                    }

                    // Extract unique colors with their specific image and resolved hex code
                    $colorGroups = $dbProduct->product_variations->filter(fn($v) => !empty($v->color?->color))->groupBy('color_id');
                    $colors = [];
                    $variationImages = [];
                    $idx = 0;

                    foreach ($colorGroups as $colorId => $variations) {
                        $firstVar = $variations->first();
                        $colorName = $firstVar->color?->color ?? 'Color ' . ($idx + 1);
                        $varImageUrl = $firstVar->image_url;
                        $varImageTwoUrl = $firstVar->image_two_url;
                        $hex = $this->getColorHexByName($colorName);

                        $colorSpecificImage = $varImageUrl ?: $dbProduct->image_url;
                        if ($varImageUrl) {
                            $variationImages[] = $varImageUrl;
                        }
                        if ($varImageTwoUrl) {
                            $variationImages[] = $varImageTwoUrl;
                        }

                        $colorImgs = array_values(array_filter([$varImageUrl, $varImageTwoUrl]));
                        if (empty($colorImgs) && $dbProduct->image_url) {
                            $colorImgs = [$dbProduct->image_url];
                        }

                        $colors[] = [
                            'id' => $colorId,
                            'name' => $colorName,
                            'hex' => $hex,
                            'image' => $colorSpecificImage,
                            'image_two' => $varImageTwoUrl,
                            'images' => $colorImgs,
                            'active' => $idx === 0,
                        ];
                        $idx++;
                    }

                    // Build gallery images array: primary image + all color variation images
                    $galleryImages = array_values(array_filter(array_unique(array_merge(
                        [$dbProduct->image_url],
                        $variationImages
                    ))));

                    if (empty($galleryImages)) {
                        $galleryImages = [asset('frontend/images/no-image.svg')];
                    }

                    // Stock calculation and custom variation prices
                    $variationStockMap = [];
                    $variationPrices = [];
                    $variationOldPrices = [];
                    $sizePrices = [];
                    $sizeOldPrices = [];
                    $sizeTotalStocks = [];
                    $totalVariationStock = 0;

                    $basePrice = (float)($dbProduct->selling_price ?: 0);
                    $baseOldPrice = (float)($dbProduct->dis_selling_price ?: ($dbProduct->selling_price + ($dbProduct->discount ?? 0)));

                    if ($dbProduct->product_variations->isNotEmpty()) {
                        foreach ($dbProduct->product_variations as $v) {
                            $sName = $v->size?->size ?? 'N/A';
                            $cName = $v->color?->color ?? 'Default';
                            $pStock = (float) PurchaseItem::where('product_variation_id', $v->id)->sum('stock_qty');
                            
                            $totalVariationStock += $pStock;
                            
                            if (!isset($variationStockMap[$cName])) {
                                $variationStockMap[$cName] = [];
                                $variationPrices[$cName] = [];
                                $variationOldPrices[$cName] = [];
                            }
                            $variationStockMap[$cName][$sName] = (int) $pStock;
                            
                            $sizeTotalStocks[$sName] = ($sizeTotalStocks[$sName] ?? 0) + (int) $pStock;

                            $vSelling = $v->selling_price !== null ? (float)$v->selling_price : $basePrice;
                            $vDisSelling = $v->dis_selling_price !== null ? (float)$v->dis_selling_price : ($v->selling_price !== null ? $vSelling : $baseOldPrice);

                            if ($v->selling_price !== null) {
                                $sizePrices[$sName] = $vSelling;
                            }
                            if ($v->dis_selling_price !== null) {
                                $sizeOldPrices[$sName] = $vDisSelling;
                            }
                            $variationPrices[$cName][$sName] = $vSelling;
                            $variationOldPrices[$cName][$sName] = $vDisSelling;
                        }
                    }

                    $directPurchasedStock = (float) PurchaseItem::where('product_id', $dbProduct->id)->sum('stock_qty');
                    $stockQty = max($totalVariationStock, $directPurchasedStock, (float)($dbProduct->main_qty ?? 0));

                    $inStock = $stockQty > 0;
                    $stockStatus = $inStock ? "In Stock • " . (int)$stockQty . " Available" : "Out of Stock • Unavailable";

                    // Default size_stocks to first active color
                    $firstColorName = !empty($colors) ? ($colors[0]['name'] ?? null) : null;
                    $sizeStocks = [];
                    if ($firstColorName && isset($variationStockMap[$firstColorName]) && array_sum($variationStockMap[$firstColorName]) > 0) {
                        $sizeStocks = $variationStockMap[$firstColorName];
                        foreach ($sizes as $sName) {
                            if (!isset($sizeStocks[$sName])) {
                                $sizeStocks[$sName] = 0;
                            }
                        }
                    } elseif (!empty($sizeTotalStocks) && array_sum($sizeTotalStocks) > 0) {
                        $sizeStocks = $sizeTotalStocks;
                    } else {
                        $sizeCount = count($sizes) ?: 1;
                        if ($stockQty <= 0) {
                            foreach ($sizes as $sName) {
                                $sizeStocks[$sName] = 0;
                            }
                        } else {
                            $basePerSize = (int)floor($stockQty / $sizeCount);
                            $rem = (int)((int)$stockQty % $sizeCount);
                            foreach ($sizes as $idx => $sName) {
                                $sizeStocks[$sName] = max(1, $basePerSize + ($idx < $rem ? 1 : 0));
                            }
                        }
                    }

                    $product = [
                        'id' => $dbProduct->id,
                        'name' => $dbProduct->name,
                        'sku' => $dbProduct->barcode ?? ('KM-' . $dbProduct->id),
                        'category' => $dbProduct->category ? $dbProduct->category->name : 'Apparel',
                        'subcategory' => $dbProduct->subCategory ? $dbProduct->subCategory->name : ($dbProduct->category ? $dbProduct->category->name : ''),
                        'price' => (float)($dbProduct->selling_price ?: 0),
                        'old_price' => (float)($dbProduct->dis_selling_price ?: ($dbProduct->selling_price + ($dbProduct->discount ?? 0))),
                        'rating' => 5.0,
                        'reviews_count' => 0,
                        'stock' => $stockQty,
                        'in_stock' => $inStock,
                        'stock_status' => $stockStatus,
                        'images' => array_values($galleryImages),
                        'colors' => $colors,
                        'sizes' => $sizes,
                        'size_stocks' => $sizeStocks,
                        'variation_stocks' => $variationStockMap,
                        'size_prices' => !empty($sizePrices) ? $sizePrices : null,
                        'size_old_prices' => !empty($sizeOldPrices) ? $sizeOldPrices : null,
                        'variation_prices' => !empty($variationPrices) ? $variationPrices : null,
                        'variation_old_prices' => !empty($variationOldPrices) ? $variationOldPrices : null,
                        'default_size' => $sizes[0] ?? null,
                        'description' => $dbProduct->description ?? '',
                    ];
                }
            }
        } catch (\Exception $e) {
            $product = null;
        }

        if (!$product) {
            abort(404, 'Product not found');
        }

        // Curated Dynamic Pairings from Database
        $pairings = [];
        try {
            if (isset($dbProduct) && $dbProduct) {
                $relatedProducts = Product::active()
                    ->where('id', '!=', $dbProduct->id)
                    ->when($dbProduct->category_id, fn($q) => $q->where('category_id', $dbProduct->category_id))
                    ->take(4)
                    ->get();

                if ($relatedProducts->isEmpty()) {
                    $relatedProducts = Product::active()
                        ->where('id', '!=', $dbProduct->id)
                        ->inRandomOrder()
                        ->take(4)
                        ->get();
                }

                foreach ($relatedProducts as $rel) {
                    $pairings[] = [
                        'id' => $rel->id,
                        'name' => $rel->name,
                        'category' => $rel->category?->name ?? 'Apparel',
                        'tag' => $rel->brand?->name ?? 'Kingsman',
                        'price' => (float)$rel->selling_price,
                        'image' => $rel->image_url,
                    ];
                }
            }
        } catch (\Exception $e) {}

        // Patron Reviews (empty until real reviews exist)
        $reviews = [];

        // Business settings
        $settings = null;
        try {
            if (class_exists(BusinessSetting::class)) {
                $settings = BusinessSetting::first();
            }
        } catch (\Exception $e) {
            $settings = null;
        }

        return view('frontend.product-details', compact('product', 'pairings', 'reviews', 'settings'));
    }

    /**
     * Display shopping bag / cart page.
     */
    public function cart()
    {
        $settings = null;
        try {
            if (class_exists(BusinessSetting::class)) {
                $settings = BusinessSetting::first();
            }
        } catch (\Exception $e) {
            $settings = null;
        }

        $pairings = [];
        try {
            $dbProducts = Product::with(['category', 'brand'])->active()->inRandomOrder()->take(4)->get();
            foreach ($dbProducts as $rel) {
                $pairings[] = [
                    'id' => $rel->id,
                    'name' => $rel->name,
                    'category' => $rel->category?->name ?? 'Apparel',
                    'tag' => $rel->brand?->name ?? 'Kingsman',
                    'price' => (float)$rel->selling_price,
                    'image' => $rel->image_url,
                ];
            }
        } catch (\Exception $e) {}

        return view('frontend.cart', compact('pairings', 'settings'));
    }

    /**
     * Display patron saved wishlist page.
     */
    public function wishlist()
    {
        $settings = null;
        try {
            if (class_exists(BusinessSetting::class)) {
                $settings = BusinessSetting::first();
            }
        } catch (\Exception $e) {
            $settings = null;
        }

        $pairings = [];
        try {
            $dbProducts = Product::with(['category', 'brand'])->active()->inRandomOrder()->take(4)->get();
            foreach ($dbProducts as $rel) {
                $pairings[] = [
                    'id' => $rel->id,
                    'name' => $rel->name,
                    'category' => $rel->category?->name ?? 'Apparel',
                    'tag' => $rel->brand?->name ?? 'Kingsman',
                    'price' => (float)$rel->selling_price,
                    'image' => $rel->image_url,
                ];
            }
        } catch (\Exception $e) {}

        return view('frontend.wishlist', compact('pairings', 'settings'));
    }

    /**
     * Display checkout page.
     */
    public function checkout()
    {
        $settings = null;
        try {
            if (class_exists(BusinessSetting::class)) {
                $settings = BusinessSetting::first();
            }
        } catch (\Exception $e) {
            $settings = null;
        }

        return view('frontend.checkout', compact('settings'));
    }

    /**
     * Process checkout form submission and save to backend Web Orders list.
     */
    public function processCheckout(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:150',
            'phone' => 'required|string|max:25',
            'address' => 'required|string|max:500',
            'delivery_zone' => 'required|string',
            'payment_method' => 'required|string',
        ]);

        $cartItems = json_decode($request->input('cart_items_json', '[]'), true);
        if (empty($cartItems) || !is_array($cartItems)) {
            return back()->withInput()->with('error', 'Your shopping bag is empty. Please add items before placing an order.');
        }

        // Strict Stock Validation: Ensure none of the cart items are out of stock or exceed stock
        foreach ($cartItems as $cItem) {
            if (!empty($cItem['id'])) {
                $dbP = Product::with(['product_variations.size', 'product_variations.color'])->find($cItem['id']);
                $availableProductStock = max(
                    (float) PurchaseItem::where('product_id', $dbP?->id)->sum('stock_qty'),
                    (float)($dbP?->main_qty ?? 0)
                );

                if (!$dbP || $dbP->status != 1 || $availableProductStock <= 0) {
                    return back()->withInput()->with('error', "Sorry, '" . ($dbP ? $dbP->name : 'This product') . "' is currently out of stock and cannot be ordered.");
                }
                $reqQty = max(1, (float)($cItem['quantity'] ?? $cItem['qty'] ?? 1));
                $itemSize = $cItem['size'] ?? null;
                $itemColor = $cItem['color'] ?? null;

                if ($dbP->product_variations->isNotEmpty()) {
                    $matchedVar = $this->matchProductVariation($dbP, $itemSize, $itemColor);
                    if ($matchedVar) {
                        $varStock = (float) PurchaseItem::where('product_variation_id', $matchedVar->id)->sum('stock_qty');
                        if ($varStock <= 0) {
                            $varStock = $availableProductStock;
                        }
                        if ($varStock <= 0) {
                            return back()->withInput()->with('error', "Sorry, '{$dbP->name}' (Size: {$itemSize}) is currently out of stock.");
                        }
                        if ($varStock < $reqQty) {
                            return back()->withInput()->with('error', "Sorry, only " . (int)$varStock . " unit(s) available for '{$dbP->name}' (Size: {$itemSize}).");
                        }
                    }
                } else {
                    if ($availableProductStock < $reqQty) {
                        return back()->withInput()->with('error', "Sorry, only " . (int)$availableProductStock . " unit(s) available in stock for '{$dbP->name}'.");
                    }
                }
            }
        }

        $subtotal = (float) $request->input('subtotal', 0);
        
        // Calculate delivery charge
        $deliveryZone = $request->input('delivery_zone', 'inside_dhaka');
        $shippingCharge = ($subtotal >= 3000) ? 0 : (($deliveryZone === 'inside_dhaka') ? 80 : 150);
        
        // Discount calculation
        $discount = (float) $request->input('discount_amount', 0);
        $grandTotal = max(0, $subtotal + $shippingCharge - $discount);
        $paymentMethod = $request->input('payment_method', 'cash_on_delivery');
        $isCod = ($paymentMethod === 'cash_on_delivery');

        // Customer Details
        $cleanPhone = trim($request->input('phone'));
        $fullAddress = trim($request->input('address'));
        if ($request->filled('city')) {
            $fullAddress .= ', ' . trim($request->input('city'));
        }
        if ($request->filled('division')) {
            $fullAddress .= ', ' . trim($request->input('division'));
        }

        DB::beginTransaction();
        try {
            // 1. Find or Create Registered Customer
            $customer = Customer::where('phone', $cleanPhone)->first();
            if (!$customer) {
                $customer = Customer::create([
                    'name' => $request->input('customer_name'),
                    'phone' => $cleanPhone,
                    'email' => $request->input('email', ''),
                    'address' => $fullAddress,
                    'branch_id' => 1,
                    'status' => 1,
                ]);
            } else {
                if (empty($customer->address)) {
                    $customer->address = $fullAddress;
                    $customer->save();
                }
                if ($customer->name == 'Walk-in Customer' || empty($customer->name)) {
                    $customer->name = $request->input('customer_name');
                    $customer->save();
                }
            }

            // 2. Generate Sequential Invoice Number (INV-0000001 format)
            $lastInvoice = Invoice::where('invoice_no', 'LIKE', 'INV-%')->orderBy('id', 'desc')->first();
            if (!$lastInvoice) {
                $nextNumber = 1;
            } else {
                $digits = preg_replace('/\D/', '', $lastInvoice->invoice_no);
                $nextNumber = ((int) $digits) + 1;
            }

            $invoiceNo = "INV-" . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
            while (Invoice::where('invoice_no', $invoiceNo)->exists()) {
                $nextNumber++;
                $invoiceNo = "INV-" . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
            }

            // 3. Create Invoice Record with sale_type = 'Online' (Web Orders)
            $invoice = new Invoice();
            $invoice->date = date('Y-m-d');
            $invoice->unique_id = uniqid('WEB_');
            $invoice->invoice_no = $invoiceNo;
            $invoice->customer_id = $customer->id;
            $invoice->branch_id = 1;
            $invoice->sale_type = 'Online';
            $invoice->order_status = 'Pending';
            $invoice->estimated_amount = $grandTotal;
            $invoice->total_amount = $grandTotal;
            $invoice->delivery_charge = $shippingCharge;
            $invoice->discount = $request->filled('coupon_code') ? $request->input('coupon_code') : null;
            $invoice->discount_amount = $discount;
            $invoice->total_paid = $isCod ? 0 : $grandTotal;
            $invoice->total_due = $isCod ? $grandTotal : 0;
            $invoice->note = "Web Order | Delivery: {$deliveryZone} | Pay: {$paymentMethod}" . ($request->filled('notes') ? (" | Notes: " . $request->input('notes')) : "");
            $invoice->created_by = 1;
            $invoice->status = 1;
            $invoice->save();

            // 4. Create InvoiceItems & decrement product main_qty
            $savedItemsForSession = [];
            foreach ($cartItems as $cItem) {
                $productId = $cItem['id'] ?? null;
                $reqQty = (float)($cItem['quantity'] ?? $cItem['qty'] ?? 1);
                $unitPrice = (float)($cItem['price'] ?? 0);
                $itemSize = $cItem['size'] ?? null;
                $itemColor = $cItem['color'] ?? null;

                $product = Product::with(['product_variations.size', 'product_variations.color'])->find($productId);
                $variationId = null;

                if ($product && $product->product_variations->isNotEmpty()) {
                    $matchedVar = $this->matchProductVariation($product, $itemSize, $itemColor);
                    $variationId = $matchedVar ? $matchedVar->id : $product->product_variations->first()->id;
                }

                $lineSubtotal = $unitPrice * $reqQty;

                $invoiceItem = new InvoiceItem();
                $invoiceItem->invoice_id = $invoice->id;
                $invoiceItem->branch_id = 1;
                $invoiceItem->product_id = $productId;
                $invoiceItem->product_variation_id = $variationId;
                $invoiceItem->rate = $unitPrice;
                $invoiceItem->main_qty = $reqQty;
                $invoiceItem->actual_main = $reqQty;
                $invoiceItem->subtotal = $lineSubtotal;
                $invoiceItem->actual_total = $lineSubtotal;
                $invoiceItem->inv_subtotal = $lineSubtotal;
                $invoiceItem->pur_subtotal = calculateUnitPriceUsingFIFO($productId, $reqQty, $variationId, 1);
                $invoiceItem->date = date('Y-m-d');
                $invoiceItem->status = 1;
                $invoiceItem->save();

                // Deduct stock from product
                if ($product && $product->is_service != 1) {
                    if ($product->main_qty >= $reqQty) {
                        $product->decrement('main_qty', $reqQty);
                    } else {
                        $product->main_qty = 0;
                        $product->save();
                    }
                }

                $savedItemsForSession[] = [
                    'id' => $productId,
                    'name' => $cItem['name'] ?? ($product ? $product->name : 'Outfit Item'),
                    'price' => $unitPrice,
                    'size' => $itemSize ?? 'L',
                    'color' => $itemColor ?? 'Default',
                    'quantity' => $reqQty,
                    'image' => $cItem['image'] ?? ($product ? $product->image_url : ''),
                ];
            }

            // 5. Record Customer Transaction
            $transaction = new Transaction();
            $transaction->transaction_type = 'Invoice';
            $transaction->branch_id = 1;
            $transaction->date = date('Y-m-d');
            $transaction->invoice_id = $invoice->id;
            $transaction->customer_id = $customer->id;
            $transaction->debit = $grandTotal;
            $transaction->credit = $isCod ? null : $grandTotal;
            $transaction->created_by = 1;
            $transaction->save();

            DB::commit();

            // Store in session for confirmation page
            $orderData = [
                'order_id' => $invoiceNo,
                'invoice_id' => $invoice->id,
                'customer_name' => $customer->name,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'address' => $fullAddress,
                'division' => $request->input('division', 'Dhaka'),
                'city' => $request->input('city', 'Dhaka'),
                'delivery_zone' => $deliveryZone,
                'shipping_charge' => $shippingCharge,
                'payment_method' => $paymentMethod,
                'notes' => $request->input('notes', ''),
                'items' => $savedItemsForSession,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'coupon_code' => $request->input('coupon_code', ''),
                'grand_total' => $grandTotal,
                'created_at' => now()->format('d M Y, h:i A')
            ];
            session(['latest_order' => $orderData]);

            return redirect()->route('order.confirmation', ['order_id' => $invoiceNo])
                ->with('success', "Your bespoke luxury consignment #{$invoiceNo} has been placed successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout Order Creation Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withInput()->with('error', 'Unable to place order: ' . $e->getMessage());
        }
    }

    /**
     * Display order confirmation / success page.
     */
    public function orderConfirmation($order_id = null)
    {
        $settings = null;
        try {
            if (class_exists(BusinessSetting::class)) {
                $settings = BusinessSetting::first();
            }
        } catch (\Exception $e) {
            $settings = null;
        }

        $sessionOrder = session('latest_order');

        if ($sessionOrder && (!$order_id || $sessionOrder['order_id'] === $order_id)) {
            $order = $sessionOrder;
        } elseif ($order_id && class_exists(Invoice::class)) {
            $dbInvoice = Invoice::with(['customer', 'invoiceItems.product', 'invoiceItems.product_variation.size', 'invoiceItems.product_variation.color'])
                ->where('invoice_no', $order_id)
                ->orWhere('id', $order_id)
                ->orWhere('unique_id', $order_id)
                ->first();
            if ($dbInvoice) {
                $items = [];
                foreach ($dbInvoice->invoiceItems as $it) {
                    $items[] = [
                        'id' => $it->product_id,
                        'name' => $it->product?->name ?? 'Outfit Item',
                        'price' => (float)$it->rate,
                        'size' => $it->product_variation?->size?->size ?? 'Standard',
                        'color' => $it->product_variation?->color?->color ?? 'Default',
                        'quantity' => (int)$it->main_qty,
                        'image' => $it->product_variation?->image_url ?: ($it->product?->image_url ?: 'https://via.placeholder.com/150'),
                    ];
                }
                $order = [
                    'order_id' => $dbInvoice->invoice_no,
                    'customer_name' => $dbInvoice->customer?->name ?? 'Patron',
                    'phone' => $dbInvoice->customer?->phone ?? '',
                    'email' => $dbInvoice->customer?->email ?? '',
                    'address' => $dbInvoice->customer?->address ?? '',
                    'division' => 'Dhaka Division',
                    'city' => '',
                    'delivery_zone' => 'inside_dhaka',
                    'shipping_charge' => (float)$dbInvoice->delivery_charge,
                    'payment_method' => $dbInvoice->total_paid > 0 ? 'Online Payment' : 'Cash On Delivery',
                    'notes' => $dbInvoice->note ?? '',
                    'items' => $items,
                    'subtotal' => (float)($dbInvoice->total_amount - $dbInvoice->delivery_charge + $dbInvoice->discount_amount),
                    'discount' => (float)$dbInvoice->discount_amount,
                    'coupon_code' => $dbInvoice->discount ?? '',
                    'grand_total' => (float)$dbInvoice->total_amount,
                    'created_at' => $dbInvoice->created_at ? $dbInvoice->created_at->format('d M Y, h:i A') : now()->format('d M Y, h:i A'),
                ];
            } else {
                $order = [
                    'order_id' => $order_id ?: ('ROBE-' . rand(10000, 99999)),
                    'customer_name' => 'Patron',
                    'phone' => '+880 1700-000000',
                    'email' => '',
                    'address' => 'Dhaka, Bangladesh',
                    'division' => 'Dhaka Division',
                    'city' => 'Dhaka',
                    'delivery_zone' => 'inside_dhaka',
                    'shipping_charge' => 0,
                    'payment_method' => 'Cash on Delivery',
                    'notes' => '',
                    'items' => [],
                    'subtotal' => 0,
                    'discount' => 0,
                    'coupon_code' => '',
                    'grand_total' => 0,
                    'created_at' => now()->format('d M Y, h:i A')
                ];
            }
        } else {
            $order = [
                'order_id' => $order_id ?: ('ROBE-' . rand(10000, 99999)),
                'customer_name' => 'Patron',
                'phone' => '+880 1700-000000',
                'email' => '',
                'address' => 'Dhaka, Bangladesh',
                'division' => 'Dhaka Division',
                'city' => 'Dhaka',
                'delivery_zone' => 'inside_dhaka',
                'shipping_charge' => 0,
                'payment_method' => 'Cash on Delivery',
                'notes' => '',
                'items' => [],
                'subtotal' => 0,
                'discount' => 0,
                'coupon_code' => '',
                'grand_total' => 0,
                'created_at' => now()->format('d M Y, h:i A')
            ];
        }

        return view('frontend.order-confirmation', compact('order', 'settings'));
    }

    /**
     * Display the dedicated Products Catalog / All Products page.
     */
    public function products(Request $request)
    {
        return $this->category('all', $request);
    }

    /**
     * Display products filtered by category or sub-category.
     */
    public function category($slug = 'all', Request $request = null)
    {
        $request = $request ?: request();
        $settings = null;
        try {
            if (class_exists(BusinessSetting::class)) {
                $settings = BusinessSetting::first();
            }
        } catch (\Exception $e) {
            $settings = null;
        }

        $allProducts = $this->getCatalogProducts();
        $rawSlug = $slug ? strtolower(trim($slug)) : 'all';
        $slugMap = [
            'mens-ethnic' => 'panjabi',
            'ethnic' => 'panjabi',
            'men-s-ethnic' => 'panjabi',
            'shirt' => 'shirts',
            'tshirt' => 't-shirt',
            'kids-winter' => 'kids-item',
            'kids-and-winter' => 'kids-item',
            'kids' => 'kids-item',
        ];
        $activeSlug = $slugMap[$rawSlug] ?? $rawSlug;
        $activeSub = $request->get('sub', 'all');
        $activeChild = $request->get('child', 'all');
        $sortBy = $request->get('sort', 'default');

        $dbCategory = null;
        if (class_exists(Category::class) && $rawSlug !== 'all') {
            $cleanSearchTerm = str_replace('-', ' ', $rawSlug);
            $query = Category::query();
            if (is_numeric($rawSlug)) {
                $query->where('id', (int)$rawSlug);
            } else {
                $query->where(function ($q) use ($cleanSearchTerm) {
                    $q->where('name', 'LIKE', $cleanSearchTerm)
                      ->orWhere('name', 'LIKE', "%{$cleanSearchTerm}%");
                });
            }
            $dbCategory = $query->first();

            // Fallback: match by accessor slug
            if (!$dbCategory) {
                $allCats = Category::all();
                $dbCategory = $allCats->first(function ($cat) use ($rawSlug) {
                    return $cat->slug === $rawSlug;
                });
            }
        }

        // Subcategories: Dynamic from database only (no hardcoded tabs)
        $subcategories = [];
        if ($rawSlug === 'all') {
            if (class_exists(Category::class)) {
                $dbCategories = Category::all();
                if ($dbCategories->count() > 1) {
                    $subcategories[] = ['name' => 'All Items', 'slug' => 'all'];
                    foreach ($dbCategories as $cat) {
                        $subcategories[] = [
                            'name' => $cat->name,
                            'slug' => $cat->slug ?: \Illuminate\Support\Str::slug($cat->name),
                        ];
                    }
                }
            }
        } else {
            if ($dbCategory && class_exists(\App\Models\SubCategory::class)) {
                $dbSubs = \App\Models\SubCategory::where('category_id', $dbCategory->id)->get();
                if ($dbSubs->count() > 0) {
                    $subcategories[] = ['name' => 'All Items', 'slug' => 'all'];
                    foreach ($dbSubs as $sub) {
                        $subcategories[] = [
                            'name' => $sub->name,
                            'slug' => \Illuminate\Support\Str::slug($sub->name),
                        ];
                    }
                }
            }
        }

        // Find active SubCategory if filtering by subcategory
        $dbSubCategory = null;
        $activeSubName = null;
        if ($activeSub !== 'all' && class_exists(\App\Models\SubCategory::class)) {
            $normalizedActiveSub = \Illuminate\Support\Str::slug($activeSub);
            $normTarget = rtrim(str_replace(['-', ' ', '\'', '"'], '', strtolower($activeSub)), 's');

            if ($dbCategory) {
                $catSubs = \App\Models\SubCategory::where('category_id', $dbCategory->id)->get();
                // 1. Exact slug match
                $dbSubCategory = $catSubs->first(function ($s) use ($normalizedActiveSub) {
                    return \Illuminate\Support\Str::slug($s->name) === $normalizedActiveSub;
                });

                // 2. Normalized without 's' (e.g. 'mens' -> 'men' === 'men')
                if (!$dbSubCategory) {
                    $dbSubCategory = $catSubs->first(function ($s) use ($normTarget) {
                        $sNorm = rtrim(str_replace(['-', ' ', '\'', '"'], '', strtolower($s->name)), 's');
                        return $sNorm === $normTarget;
                    });
                }
            }

            if (!$dbSubCategory) {
                $allSubs = \App\Models\SubCategory::all();
                $dbSubCategory = $allSubs->first(function ($s) use ($normalizedActiveSub) {
                    return \Illuminate\Support\Str::slug($s->name) === $normalizedActiveSub;
                });
                if (!$dbSubCategory) {
                    $dbSubCategory = $allSubs->first(function ($s) use ($normTarget) {
                        $sNorm = rtrim(str_replace(['-', ' ', '\'', '"'], '', strtolower($s->name)), 's');
                        return $sNorm === $normTarget;
                    });
                }
            }

            if ($dbSubCategory) {
                $activeSubName = $dbSubCategory->name;
            }
        }

        // Child Categories: Dynamic from database for active SubCategory
        $childCategories = [];
        $dbChildCategory = null;
        $activeChildName = null;
        if ($dbSubCategory && class_exists(\App\Models\ChildCategory::class)) {
            $dbChilds = \App\Models\ChildCategory::where('sub_category_id', $dbSubCategory->id)->get();
            if ($dbChilds->count() > 0) {
                $childCategories[] = ['name' => 'All ' . $dbSubCategory->name, 'slug' => 'all'];
                foreach ($dbChilds as $c) {
                    $childCategories[] = [
                        'name' => $c->name,
                        'slug' => $c->slug ?: \Illuminate\Support\Str::slug($c->name),
                    ];
                }
            }
        }

        // Find active ChildCategory if filtering by child category
        if ($activeChild !== 'all' && class_exists(\App\Models\ChildCategory::class)) {
            $normalizedActiveChild = \Illuminate\Support\Str::slug($activeChild);
            $normChildTarget = rtrim(str_replace(['-', ' ', '\'', '"'], '', strtolower($activeChild)), 's');

            if ($dbSubCategory) {
                $subChilds = \App\Models\ChildCategory::where('sub_category_id', $dbSubCategory->id)->get();
                $dbChildCategory = $subChilds->first(function ($c) use ($normalizedActiveChild) {
                    return ($c->slug === $normalizedActiveChild) || (\Illuminate\Support\Str::slug($c->name) === $normalizedActiveChild);
                });
                if (!$dbChildCategory) {
                    $dbChildCategory = $subChilds->first(function ($c) use ($normChildTarget) {
                        $cNorm = rtrim(str_replace(['-', ' ', '\'', '"'], '', strtolower($c->name)), 's');
                        return $cNorm === $normChildTarget;
                    });
                }
            }

            if (!$dbChildCategory) {
                $allChilds = \App\Models\ChildCategory::all();
                $dbChildCategory = $allChilds->first(function ($c) use ($normalizedActiveChild) {
                    return ($c->slug === $normalizedActiveChild) || (\Illuminate\Support\Str::slug($c->name) === $normalizedActiveChild);
                });
                if (!$dbChildCategory) {
                    $dbChildCategory = $allChilds->first(function ($c) use ($normChildTarget) {
                        $cNorm = rtrim(str_replace(['-', ' ', '\'', '"'], '', strtolower($c->name)), 's');
                        return $cNorm === $normChildTarget;
                    });
                }
            }

            if ($dbChildCategory) {
                $activeChildName = $dbChildCategory->name;
            }
        }

        // Category Banner: Dynamic from DB category or active banner only (no static fallback)
        $bannerUrl = null;
        if ($dbCategory && !empty($dbCategory->image)) {
            $bannerUrl = $dbCategory->image_url;
        } elseif (class_exists(Banner::class)) {
            $dbBanner = Banner::active()
                ->where('position', 'category')
                ->where(function ($q) use ($rawSlug, $dbCategory) {
                    $q->where('link', 'LIKE', "%{$rawSlug}%");
                    if ($dbCategory) {
                        $q->orWhere('title', 'LIKE', "%{$dbCategory->name}%");
                    }
                })
                ->first();
            if ($dbBanner) {
                $bannerUrl = $dbBanner->image_url;
            }
        }

        $categoryName = $rawSlug === 'all' 
            ? 'All Products' 
            : ($dbCategory ? $dbCategory->name : ucwords(str_replace('-', ' ', $rawSlug)));

        $displayName = $categoryName;
        if ($activeSubName) {
            $displayName .= ' - ' . $activeSubName;
        }
        if ($activeChildName) {
            $displayName .= ' (' . $activeChildName . ')';
        }

        $currentConfig = [
            'name'             => $displayName,
            'title'            => $displayName,
            'subtitle'         => 'Explore our collection of ' . strtolower($displayName),
            'banner'           => $bannerUrl,
            'subcategories'    => $subcategories,
            'child_categories' => $childCategories,
        ];

        // Filter products by Category
        $filteredProducts = $allProducts;
        if ($activeSlug !== 'all') {
            $targetCatSlug = \Illuminate\Support\Str::slug($rawSlug);
            $targetCatNorm = rtrim(str_replace(['-', ' ', '\'', '"'], '', strtolower($rawSlug)), 's');

            $filteredProducts = array_filter($filteredProducts, function ($p) use ($activeSlug, $rawSlug, $targetCatSlug, $targetCatNorm, $dbCategory) {
                if ($activeSlug === 'offer') {
                    return !empty($p['old_price']) && ($p['old_price'] > $p['price']);
                }
                if ($dbCategory && isset($p['category_id']) && (int)$p['category_id'] === (int)$dbCategory->id) {
                    return true;
                }
                $pCatSlug = \Illuminate\Support\Str::slug($p['category'] ?? ($p['category_slug'] ?? ''));
                if (!empty($pCatSlug) && ($pCatSlug === $targetCatSlug || $pCatSlug === $activeSlug)) {
                    return true;
                }
                $pCatNorm = rtrim(str_replace(['-', ' ', '\'', '"'], '', strtolower($p['category'] ?? '')), 's');
                if (!empty($pCatNorm) && $pCatNorm === $targetCatNorm) {
                    return true;
                }
                return false;
            });
        }

        // Filter products by Subcategory if specified (Strict matching, never str_contains)
        if ($activeSub !== 'all') {
            $targetSubSlug = \Illuminate\Support\Str::slug($activeSub);
            $targetSubNorm = rtrim(str_replace(['-', ' ', '\'', '"'], '', strtolower($activeSub)), 's');

            $filteredProducts = array_filter($filteredProducts, function ($p) use ($targetSubSlug, $targetSubNorm, $dbSubCategory) {
                // Priority 1: Match by SubCategory database ID
                if ($dbSubCategory && isset($p['sub_category_id']) && !empty($p['sub_category_id'])) {
                    return (int)$p['sub_category_id'] === (int)$dbSubCategory->id;
                }

                // Priority 2: Match by exact slug
                $pSubSlug = \Illuminate\Support\Str::slug($p['sub_category_name'] ?? ($p['sub_slug'] ?? ''));
                if (!empty($pSubSlug) && $pSubSlug === $targetSubSlug) {
                    return true;
                }

                // Priority 3: Match normalized exact string (strictly equals, e.g. 'men' === 'men', but 'women' !== 'men')
                $pSubNorm = rtrim(str_replace(['-', ' ', '\'', '"'], '', strtolower($p['sub_category_name'] ?? ($p['sub_slug'] ?? ''))), 's');
                if (!empty($pSubNorm) && $pSubNorm === $targetSubNorm) {
                    return true;
                }

                return false;
            });
        }

        // Filter products by Child Category if specified (Strict matching)
        if ($activeChild !== 'all') {
            $targetChildSlug = \Illuminate\Support\Str::slug($activeChild);
            $targetChildNorm = rtrim(str_replace(['-', ' ', '\'', '"'], '', strtolower($activeChild)), 's');

            $filteredProducts = array_filter($filteredProducts, function ($p) use ($targetChildSlug, $targetChildNorm, $dbChildCategory) {
                // Priority 1: Match by ChildCategory database ID
                if ($dbChildCategory && isset($p['child_category_id']) && !empty($p['child_category_id'])) {
                    return (int)$p['child_category_id'] === (int)$dbChildCategory->id;
                }

                // Priority 2: Match by exact slug
                $pChildSlug = \Illuminate\Support\Str::slug($p['child_category_name'] ?? ($p['child_slug'] ?? ''));
                if (!empty($pChildSlug) && $pChildSlug === $targetChildSlug) {
                    return true;
                }

                // Priority 3: Match normalized exact string
                $pChildNorm = rtrim(str_replace(['-', ' ', '\'', '"'], '', strtolower($p['child_category_name'] ?? ($p['child_slug'] ?? ''))), 's');
                if (!empty($pChildNorm) && $pChildNorm === $targetChildNorm) {
                    return true;
                }

                return false;
            });
        }

        // Filter products by Search Query if specified
        $searchQuery = $request ? trim((string)$request->get('q', $request->get('search', ''))) : '';
        if (!empty($searchQuery)) {
            $sq = strtolower($searchQuery);
            $filteredProducts = array_filter($filteredProducts, function ($p) use ($sq) {
                return str_contains(strtolower($p['name']), $sq) ||
                       str_contains(strtolower($p['category']), $sq) ||
                       str_contains(strtolower($p['sku']), $sq);
            });
            $currentConfig['name'] = "Search: " . $searchQuery;
            $currentConfig['title'] = "Search Results for \"" . $searchQuery . "\"";
            $currentConfig['subtitle'] = "Displaying garments matching your search criteria";
        }

        // Sorting
        $productsList = array_values($filteredProducts);
        if ($sortBy === 'price_low_high') {
            usort($productsList, fn($a, $b) => $a['price'] <=> $b['price']);
        } elseif ($sortBy === 'price_high_low') {
            usort($productsList, fn($a, $b) => $b['price'] <=> $a['price']);
        } elseif ($sortBy === 'discount_high') {
            usort($productsList, function ($a, $b) {
                $discA = ($a['old_price'] > $a['price']) ? (($a['old_price'] - $a['price']) / $a['old_price']) : 0;
                $discB = ($b['old_price'] > $b['price']) ? (($b['old_price'] - $b['price']) / $b['old_price']) : 0;
                return $discB <=> $discA;
            });
        }

        return view('frontend.products', [
            'products' => $productsList,
            'categoryConfig' => $currentConfig,
            'activeSlug' => $activeSlug,
            'activeSub' => $activeSub,
            'activeChild' => $activeChild,
            'sortBy' => $sortBy,
            'searchQuery' => $searchQuery,
            'settings' => $settings
        ]);
    }

    /**
     * Get complete catalog of products for easy browsing and viewing.
     */
    private function getCatalogProducts()
    {
        return Cache::remember('frontend_catalog_products', 900, function () {
            try {
                if (class_exists(Product::class)) {
                    $dbProducts = Product::with(['category', 'subCategory', 'childCategory', 'brand', 'product_variations.size', 'product_variations.color'])->active()->orderBy('id', 'desc')->get();
                    if ($dbProducts->isNotEmpty()) {
                        $items = [];
                        foreach ($dbProducts as $p) {
                            $catName = $p->category?->name ?? 'Apparel';
                            $catSlug = \Illuminate\Support\Str::slug($catName);
                            $subCatName = $p->subCategory?->name ?? '';
                            $subSlug = \Illuminate\Support\Str::slug($subCatName);
                            $childCatName = $p->childCategory?->name ?? '';
                            $childSlug = \Illuminate\Support\Str::slug($childCatName);
                            $sellingPrice = (float)($p->selling_price ?: 0);
                            $oldPrice = (float)($p->dis_selling_price > $sellingPrice ? $p->dis_selling_price : ($sellingPrice + ($p->discount ?? 0)));

                            $sizes = $p->product_variations->map(fn($v) => $v->size?->size)->filter()->unique()->values()->toArray();
                            if (empty($sizes) && $p->sizes) {
                                $sizes = $p->sizes->pluck('size')->filter()->unique()->values()->toArray();
                            }

                            $colors = $p->product_variations->map(fn($v) => $v->color?->color)->filter()->unique()->values()->toArray();
                            if (empty($colors) && $p->colors) {
                                $colors = $p->colors->pluck('color')->filter()->unique()->values()->toArray();
                            }

                            $variationStockMap = [];
                            $variationPrices = [];
                            $variationOldPrices = [];
                            $sizePrices = [];
                            $sizeOldPrices = [];
                            $sizeStocks = [];
                            $totalVarStock = 0;

                            if ($p->product_variations->isNotEmpty()) {
                                foreach ($p->product_variations as $v) {
                                    $sName = $v->size?->size ?? 'N/A';
                                    $cName = $v->color?->color ?? 'Default';
                                    $pStock = (float) PurchaseItem::where('product_variation_id', $v->id)->sum('stock_qty');
                                    $totalVarStock += $pStock;
                                    if (!isset($variationStockMap[$cName])) {
                                        $variationStockMap[$cName] = [];
                                        $variationPrices[$cName] = [];
                                        $variationOldPrices[$cName] = [];
                                    }
                                    $variationStockMap[$cName][$sName] = (int)$pStock;
                                    $sizeStocks[$sName] = ($sizeStocks[$sName] ?? 0) + (int)$pStock;

                                    $vSelling = $v->selling_price !== null ? (float)$v->selling_price : $sellingPrice;
                                    $vDisSelling = $v->dis_selling_price !== null ? (float)$v->dis_selling_price : ($v->selling_price !== null ? $vSelling : $oldPrice);

                                    if ($v->selling_price !== null) {
                                        $sizePrices[$sName] = $vSelling;
                                    }
                                    if ($v->dis_selling_price !== null) {
                                        $sizeOldPrices[$sName] = $vDisSelling;
                                    }
                                    $variationPrices[$cName][$sName] = $vSelling;
                                    $variationOldPrices[$cName][$sName] = $vDisSelling;
                                }
                            }

                            $directPurchasedStock = (float) PurchaseItem::where('product_id', $p->id)->sum('stock_qty');
                            $stockQty = max($totalVarStock, $directPurchasedStock, (float)($p->main_qty ?? 0));
                            $inStock = $stockQty > 0;

                            $items[] = [
                                'id' => $p->id,
                                'name' => $p->name,
                                'sku' => $p->barcode ?? ('KM-' . $p->id),
                                'price' => $sellingPrice,
                                'old_price' => $oldPrice,
                                'category' => $catName,
                                'category_id' => $p->category_id,
                                'category_slug' => $catSlug,
                                'parent_slug' => $catSlug,
                                'sub_category_id' => $p->sub_category_id,
                                'sub_category_name' => $subCatName,
                                'sub_slug' => $subSlug,
                                'child_category_id' => $p->child_category_id,
                                'child_category_name' => $childCatName,
                                'child_slug' => $childSlug,
                                'tag' => $p->brand?->name ?? '',
                                'image' => $p->image_url,
                                'stock' => $stockQty,
                                'in_stock' => $inStock,
                                'sizes' => $sizes,
                                'colors' => $colors,
                                'size_stocks' => !empty($sizeStocks) ? $sizeStocks : null,
                                'variation_stocks' => !empty($variationStockMap) ? $variationStockMap : null,
                                'size_prices' => !empty($sizePrices) ? $sizePrices : null,
                                'size_old_prices' => !empty($sizeOldPrices) ? $sizeOldPrices : null,
                                'variation_prices' => !empty($variationPrices) ? $variationPrices : null,
                                'variation_old_prices' => !empty($variationOldPrices) ? $variationOldPrices : null,
                            ];
                        }
                        return $items;
                    }
                }
            } catch (\Exception $e) {}

            return [];
        });
    }

    /**
     * Real-time stock validation endpoint for cart / checkout
     */
    public function validateCartStock(Request $request)
    {
        $items = $request->input('items', []);
        if (is_string($items)) {
            $items = json_decode($items, true) ?: [];
        }

        if (empty($items) || !is_array($items)) {
            return response()->json([
                'valid' => true,
                'items' => [],
                'message' => 'Cart is empty'
            ]);
        }

        $allValid = true;
        $validatedItems = [];

        foreach ($items as $item) {
            $productId = $item['id'] ?? null;
            $reqQty = max(1, (float)($item['quantity'] ?? $item['qty'] ?? 1));
            $itemSize = $item['size'] ?? null;
            $itemColor = $item['color'] ?? null;

            if (!$productId) {
                continue;
            }

            $product = Product::with(['product_variations.size', 'product_variations.color'])->find($productId);
            $availableProductStock = max(
                (float) PurchaseItem::where('product_id', $product?->id)->sum('stock_qty'),
                (float)($product?->main_qty ?? 0)
            );

            if (!$product || $product->status != 1 || $availableProductStock <= 0) {
                $allValid = false;
                $validatedItems[] = [
                    'id' => $productId,
                    'name' => $item['name'] ?? ($product ? $product->name : 'Product'),
                    'size' => $itemSize,
                    'color' => $itemColor,
                    'requested_qty' => $reqQty,
                    'available_stock' => 0,
                    'in_stock' => false,
                    'error' => "Sorry, '" . ($product ? $product->name : 'Item') . "' is currently out of stock."
                ];
                continue;
            }

            // If product has variations
            if ($product->product_variations->isNotEmpty()) {
                $matchedVar = $this->matchProductVariation($product, $itemSize, $itemColor);
                $varStock = $matchedVar ? (float) PurchaseItem::where('product_variation_id', $matchedVar->id)->sum('stock_qty') : $availableProductStock;
                if ($varStock <= 0) {
                    $varStock = $availableProductStock;
                }

                if ($varStock <= 0) {
                    $allValid = false;
                    $validatedItems[] = [
                        'id' => $productId,
                        'name' => $product->name,
                        'size' => $itemSize,
                        'color' => $itemColor,
                        'requested_qty' => $reqQty,
                        'available_stock' => 0,
                        'in_stock' => false,
                        'error' => "Sorry, '{$product->name}' (Size: {$itemSize}) is currently out of stock."
                    ];
                } elseif ($varStock < $reqQty) {
                    $allValid = false;
                    $validatedItems[] = [
                        'id' => $productId,
                        'name' => $product->name,
                        'size' => $itemSize,
                        'color' => $itemColor,
                        'requested_qty' => $reqQty,
                        'available_stock' => (int)$varStock,
                        'in_stock' => true,
                        'error' => "Only " . (int)$varStock . " unit(s) available for '{$product->name}' (Size: {$itemSize})."
                    ];
                } else {
                    $validatedItems[] = [
                        'id' => $productId,
                        'name' => $product->name,
                        'size' => $itemSize,
                        'color' => $itemColor,
                        'requested_qty' => $reqQty,
                        'available_stock' => (int)$varStock,
                        'in_stock' => true,
                        'error' => null
                    ];
                }
            } else {
                // Non-variation product
                if ($availableProductStock < $reqQty) {
                    $allValid = false;
                    $validatedItems[] = [
                        'id' => $productId,
                        'name' => $product->name,
                        'size' => $itemSize,
                        'color' => $itemColor,
                        'requested_qty' => $reqQty,
                        'available_stock' => (int)$availableProductStock,
                        'in_stock' => $availableProductStock > 0,
                        'error' => "Only " . (int)$availableProductStock . " unit(s) available in stock for '{$product->name}'."
                    ];
                } else {
                    $validatedItems[] = [
                        'id' => $productId,
                        'name' => $product->name,
                        'size' => $itemSize,
                        'color' => $itemColor,
                        'requested_qty' => $reqQty,
                        'available_stock' => (int)$availableProductStock,
                        'in_stock' => true,
                        'error' => null
                    ];
                }
            }
        }

        return response()->json([
            'valid' => $allValid,
            'items' => $validatedItems,
            'message' => $allValid ? 'All items in stock' : 'Some items in your bag are out of stock or have insufficient quantity.'
        ]);
    }

    /**
     * Search products endpoint
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        if (empty($query)) {
            return response()->json(['results' => []]);
        }

        try {
            $products = Product::active()
                ->where(function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%")
                      ->orWhere('barcode', 'LIKE', "%{$query}%")
                      ->orWhereHas('category', fn($cq) => $cq->where('name', 'LIKE', "%{$query}%"));
                })
                ->take(8)
                ->get()
                ->map(function($p) {
                    $stockQty = max(
                        (float) PurchaseItem::where('product_id', $p->id)->sum('stock_qty'),
                        (float)($p->main_qty ?? 0)
                    );
                    $inStock = $stockQty > 0;
                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'price' => (float)$p->selling_price,
                        'old_price' => (float)($p->dis_selling_price ?: ($p->selling_price + ($p->discount ?? 0))),
                        'image' => $p->image_url,
                        'url' => route('product.details', $p->id),
                    ];
                });

            return response()->json(['results' => $products]);
        } catch (\Exception $e) {
            return response()->json(['results' => []]);
        }
    }

    /**
     * Display About Us Page
     */
    public function about()
    {
        $settings = Cache::remember('frontend_business_settings', 3600, function () {
            try {
                if (class_exists(BusinessSetting::class)) {
                    return BusinessSetting::first();
                }
            } catch (\Exception $e) {}
            return null;
        });

        return view('frontend.about', compact('settings'));
    }

    /**
     * Display Contact Us Page
     */
    public function contact()
    {
        $settings = Cache::remember('frontend_business_settings', 3600, function () {
            try {
                if (class_exists(BusinessSetting::class)) {
                    return BusinessSetting::first();
                }
            } catch (\Exception $e) {}
            return null;
        });

        return view('frontend.contact', compact('settings'));
    }

    /**
     * Display Return & Exchange Policy Page
     */
    public function returnPolicy()
    {
        $settings = Cache::remember('frontend_business_settings', 3600, function () {
            try {
                if (class_exists(BusinessSetting::class)) {
                    return BusinessSetting::first();
                }
            } catch (\Exception $e) {}
            return null;
        });

        return view('frontend.return-policy', compact('settings'));
    }

    /**
     * Display Privacy Policy Page
     */
    public function privacyPolicy()
    {
        $settings = Cache::remember('frontend_business_settings', 3600, function () {
            try {
                if (class_exists(BusinessSetting::class)) {
                    return BusinessSetting::first();
                }
            } catch (\Exception $e) {}
            return null;
        });

        return view('frontend.privacy-policy', compact('settings'));
    }

    /**
     * Display Refund Policy Page
     */
    public function refundPolicy()
    {
        $settings = Cache::remember('frontend_business_settings', 3600, function () {
            try {
                if (class_exists(BusinessSetting::class)) {
                    return BusinessSetting::first();
                }
            } catch (\Exception $e) {}
            return null;
        });

        return view('frontend.refund-policy', compact('settings'));
    }

    /**
     * Display Terms & Conditions Page
     */
    public function terms()
    {
        $settings = Cache::remember('frontend_business_settings', 3600, function () {
            try {
                if (class_exists(BusinessSetting::class)) {
                    return BusinessSetting::first();
                }
            } catch (\Exception $e) {}
            return null;
        });

        return view('frontend.terms', compact('settings'));
    }
}
