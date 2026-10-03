<?php

namespace App\Http\Controllers\Backend;

use App\Models\Unit;
use App\Models\Brand;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Variation;
use App\Models\BranchBrand;
use App\Models\Rack;
use App\Models\BranchRack;
use App\Models\ProductSize;
use App\Models\ProductColor;
use App\Models\PurchaseItem;
use Illuminate\Http\Request;
use App\Models\BranchProduct;
use App\Models\BranchCategory;
use App\Models\BusinessSetting;
use App\Models\ProductVariation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Clear all frontend product caches.
     */
    public function clearFrontendProductCache()
    {
        Cache::forget('frontend_new_arrivals');
        Cache::forget('frontend_top_selling');
        Cache::forget('frontend_home_tshirts');
        Cache::forget('frontend_home_shirts');
        Cache::forget('frontend_home_denims');
        Cache::forget('frontend_home_kablis');
        Cache::forget('frontend_home_kids');
        Cache::forget('frontend_catalog_products');
        Cache::forget('frontend_categories');
    }
    public function index(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
        $data['product_id'] = $request->product_id;
        $data['category_id'] = $request->category_id;
        $data['sub_category_id'] = $request->sub_category_id;
        $data['barcode'] = $request->barcode;
        $data['subCategories'] = $request->category_id ? SubCategory::where('category_id', $request->category_id)->orderBy('name', 'asc')->get() : SubCategory::orderBy('name', 'asc')->get();
        $query = Product::with(['brand', 'category', 'subCategory', 'unit.related_unit'])->where('is_service', 0);

        if ($userBranchId == 1) {
            if ($filterBranchId && empty($request->barcode) && empty($request->product_id)) {
                $productIds = BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id');
                $categoryds = BranchCategory::where('branch_id', $filterBranchId)->pluck('category_id');
                $query->whereIn('id', $productIds);
                $data['produc'] = Product::whereIn('id', $productIds)->where('is_service', 0)->orderBy('id', 'desc')->paginate(20);
                $data['categories'] = Category::whereIn('id', $categoryds)->orderBy('id', 'desc')->get();
            } else {
                $data['produc'] = Product::where('is_service', 0)->orderBy('id', 'desc')->paginate(20);
                $data['categories'] = Category::orderBy('id', 'desc')->get();
            }
        } else {
            $categoryds = BranchCategory::where('branch_id', $userBranchId)->pluck('category_id');
            $productIds = BranchProduct::where('branch_id', $userBranchId)->pluck('product_id');
            $data['produc'] = Product::whereIn('id', $productIds)->where('is_service', 0)->orderBy('id', 'desc')->paginate(20);
            $query->whereIn('id', $productIds)->orderBy('id', 'desc')->paginate(20);
            $query->whereIn('id', $productIds)->orderBy('id', 'desc')->get();
            $data['categories'] = Category::whereIn('id', $categoryds)->orderBy('id', 'desc')->get();
            $query->whereIn('id', $productIds)->count();
        }

        $data['com'] = BusinessSetting::where('id', 6)->first();

        if ($request->product_id != null) {
            $query->where('id', $request->product_id);
        }
        if ($request->filled('barcode')) {
            $barcodeVal = trim($request->barcode);
            $resolved = function_exists('resolveProductAndVariationFromBarcode') ? resolveProductAndVariationFromBarcode($barcodeVal) : ['product_id' => null];
            if ($resolved['product_id']) {
                $query->where('id', $resolved['product_id']);
            } else {
                $query->where(function($q) use ($barcodeVal) {
                    $q->where('barcode', $barcodeVal)
                      ->orWhere('barcode', 'like', "%{$barcodeVal}%")
                      ->orWhere('name', 'like', "%{$barcodeVal}%")
                      ->orWhereHas('product_variations', function($vq) use ($barcodeVal) {
                          $vq->where('barcode', $barcodeVal)->orWhere('barcode', 'like', "%{$barcodeVal}%");
                      });
                });
            }
        }
        if ($request->category_id != null) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->sub_category_id != null) {
            $query->where('sub_category_id', $request->sub_category_id);
        }
        if ($request->category_id != null && $request->product_id != null) {
            $query->where('category_id', $request->category_id)->where('id', $request->product_id);
        }
        $data['showcase_filter'] = $request->showcase_filter;
        if ($request->showcase_filter == 'new_arrival') {
            $query->where('is_new_arrival', 1);
        } elseif ($request->showcase_filter == 'top_selling') {
            $query->where('is_top_selling', 1);
        } elseif ($request->showcase_filter == 'featured') {
            $query->where('is_featured', 1);
        }
        $data['products'] = $query
            ->orderBy('created_at', 'DESC')
            ->paginate(10)->appends($request->all());

        return view('backend.pages.product.index', $data);
    }

    public function create()
    {

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $productIds = BranchProduct::where('branch_id', $filterBranchId)->pluck('product_id');
                $categoryds = BranchCategory::where('branch_id', $filterBranchId)->pluck('category_id');
                $brandids = BranchBrand::where('branch_id', $filterBranchId)->pluck('brand_id');
                $data['products'] = Product::whereIn('id', $productIds)->orderBy('id', 'desc')->paginate(20);
                $data['allProduct'] = Product::whereIn('id', $productIds)->orderBy('id', 'desc')->get();
                $data['categories'] = Category::whereIn('id', $categoryds)->orderBy('id', 'desc')->get();
                $data['brands'] = Brand::whereIn('id', $brandids)->orderBy('id', 'desc')->get();
            } else {
                $data['allProduct'] = Product::orderBy('id', 'desc')->get();
                $data['products']  = Product::orderBy('id', 'desc')->paginate(20);
                $data['categories'] = Category::orderBy('id', 'desc')->get();
                $data['brands'] = Brand::orderBy('id', 'desc')->get();
            }
        } else {
            $categoryds = BranchCategory::where('branch_id', $userBranchId)->pluck('category_id');
            $brandids = BranchBrand::where('branch_id', $userBranchId)->pluck('brand_id');
            $productIds = BranchProduct::where('branch_id', $userBranchId)->pluck('product_id');
            $data['products']  = Product::whereIn('id', $productIds)->orderBy('id', 'desc')->paginate(20);
            $data['allProduct'] = Product::whereIn('id', $productIds)->orderBy('id', 'desc')->get();
            $data['categories'] = Category::whereIn('id', $categoryds)->orderBy('id', 'desc')->get();
            $data['brands'] = Brand::whereIn('id', $brandids)->orderBy('id', 'desc')->get();
        }

        // $brands = Brand::all();
        // $categories = Category::all();
        $data['subCategories'] = SubCategory::orderBy('name', 'asc')->get();
        $data['warranties'] = \App\Models\Warranty::where('status', 1)->orderBy('id', 'asc')->get();
        $variations = Variation::all();
        $sizes = ProductSize::all();
        $colors = ProductColor::all();
        $units = Unit::all();
        $suppliers = Supplier::all();
        $allBranch = Branch::get();
        $warranties = $data['warranties'];
        return view('backend.pages.product.create', $data, compact('allBranch', 'sizes', 'colors', 'variations', 'units', 'suppliers', 'warranties'));
    }

    function generateUniqueBarcode()
    {
        do {
            // always 6 digit (leading zero সহ)
            $barcode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            $exists = \App\Models\Product::where('barcode', $barcode)->exists();
        } while ($exists);

        return $barcode;
    }

    public function store(Request $request)
    {
        // dd($request->all());

        $request->validate(
            [
                'name' => 'required',
                'barcode' => 'nullable|unique:products',
                'category_id' => 'required',
                'unit_id' => is_unit_enabled() ? 'required' : 'nullable',
                'purchase_price' => 'required',
                'selling_price' => 'required',
                'status' => 'required',
            ],
            [
                'barcode.unique' => 'The product barcode has already been taken !',
            ]
        );

        $product = new Product();
        $product->name = $request->name;
        $product->date = date('Y-m-d');
        if (empty($request->barcode)) {
            $product->barcode = $this->generateUniqueBarcode();
        } else {
            $product->barcode = $request->barcode;
        }

        $product->is_service = 0;
        $product->category_id = $request->category_id;
        $product->sub_category_id = $request->sub_category_id;
        $product->child_category_id = $request->child_category_id;
        $product->brand_id = $request->brand_id;
        $product->unit_id = $request->unit_id ?? (is_unit_enabled() ? null : \App\Models\Unit::first()?->id);
        $product->main_qty = $request->main_qty;
        $product->has_serial = $request->has_serial;
        $product->purchase_price = $request->purchase_price !== null ? (float) $request->purchase_price : 0;
        $dis_selling_price = $request->dis_selling_price !== null ? (float) $request->dis_selling_price : 0;
        $product->dis_selling_price = $dis_selling_price;
        $discount = $request->discount;
        if (!empty($discount) && str_contains($discount, '%')) {
            $percent = (float) str_replace('%', '', $discount);
            $discount = ($dis_selling_price * $percent) / 100;
        } elseif ($discount !== null && $discount !== '') {
            $discount = (float) $discount;
        } else {
            $discount = 0;
        }
        $product->discount = $discount;
        $product->selling_price = $request->selling_price !== null ? (float) $request->selling_price : ($dis_selling_price - $discount);
        $product->status = $request->status;
        $product->is_new_arrival = $request->has('is_new_arrival') ? 1 : 0;
        $product->is_top_selling = $request->has('is_top_selling') ? 1 : 0;
        $product->is_featured = $request->has('is_featured') ? 1 : 0;
        $product->description = $request->description;
        $product->imei = $request->imei;

        // Warranty configuration
        $product->warranty_id = $request->warranty_id ?? null;
        if ($request->filled('warranty_id')) {
            $selectedWarranty = \App\Models\Warranty::find($request->warranty_id);
            if ($selectedWarranty) {
                $product->has_warranty = 1;
                $product->warranty_value = $request->filled('warranty_value') ? $request->warranty_value : $selectedWarranty->duration;
                $product->warranty_unit = $request->filled('warranty_unit') ? $request->warranty_unit : $selectedWarranty->period;
            }
        } elseif ($request->filled('warranty_value') || $request->has_warranty == 1) {
            $product->has_warranty = 1;
            $product->warranty_value = $request->warranty_value ?? null;
            $product->warranty_unit = $request->warranty_unit ?? 'Month';
        } else {
            $product->has_warranty = 0;
            $product->warranty_value = null;
            $product->warranty_unit = null;
        }
        $product->created_by = Auth::user()->id;

        $colorImageMap = [];
        if ($request->hasFile('color_images')) {
            foreach ($request->file('color_images') as $cId => $cFile) {
                if ($cFile && $cFile->isValid()) {
                    $cImgName = date('YmdHi') . '_c' . $cId . '_' . $cFile->getClientOriginalName();
                    $cFile->move(public_path('uploads/products/'), $cImgName);
                    $colorImageMap[$cId] = $cImgName;
                }
            }
        }

        $colorImageTwoMap = [];
        if ($request->hasFile('color_images_two')) {
            foreach ($request->file('color_images_two') as $cId => $cFile) {
                if ($cFile && $cFile->isValid()) {
                    $cImgName = date('YmdHi') . '_c' . $cId . '_2_' . $cFile->getClientOriginalName();
                    $cFile->move(public_path('uploads/products/'), $cImgName);
                    $colorImageTwoMap[$cId] = $cImgName;
                }
            }
        }

        if ($product->save()) {
            if ($request->size != null || $request->color != null) {
                $sizes = $request->size ?? [null];
                $colors = $request->color ?? [null];
                $sizeSellingPrices = $request->input('size_selling_price', []);
                $sizeDisSellingPrices = $request->input('size_dis_selling_price', []);
                $sizePurchasePrices = $request->input('size_purchase_price', []);

                foreach ($sizes as $size) {
                    $vSellingPrice = ($size && isset($sizeSellingPrices[$size]) && $sizeSellingPrices[$size] !== '') 
                        ? (float)$sizeSellingPrices[$size] 
                        : null;
                    $vDisSellingPrice = ($size && isset($sizeDisSellingPrices[$size]) && $sizeDisSellingPrices[$size] !== '') 
                        ? (float)$sizeDisSellingPrices[$size] 
                        : null;
                    $vPurchasePrice = ($size && isset($sizePurchasePrices[$size]) && $sizePurchasePrices[$size] !== '') 
                        ? (float)$sizePurchasePrices[$size] 
                        : null;

                    foreach ($colors as $color) {
                        $varImg = ($color && isset($colorImageMap[$color])) ? $colorImageMap[$color] : null;
                        $varImgTwo = ($color && isset($colorImageTwoMap[$color])) ? $colorImageTwoMap[$color] : null;
                        $product->product_variations()->create([
                            'product_id' => $product->id,
                            'size_id' => $size,
                            'color_id' => $color,
                            'image' => $varImg,
                            'image_two' => $varImgTwo,
                            'selling_price' => $vSellingPrice,
                            'dis_selling_price' => $vDisSellingPrice,
                            'purchase_price' => $vPurchasePrice,
                        ]);
                    }
                }
            }
        }

        $image = $request->file('images');
        if ($image) {
            $imgName = date('YmdHi') . $image->getClientOriginalName();
            $image->move(public_path('uploads/products/'), $imgName);
            $product->images = $imgName;
        }

        DB::transaction(function () use ($request, $product) {
            if (auth()->user()->branch_id == 1) {
                foreach ($request->branch_id as $branch_id) {
                    // Ensure the category exists in the branch
                    $categoryExists = DB::table('branch_categories')
                        ->where('branch_id', $branch_id)
                        ->where('category_id', $product->category_id)
                        ->exists();

                    if (!$categoryExists) {
                        DB::table('branch_categories')->insert([
                            'branch_id' => $branch_id,
                            'category_id' => $product->category_id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    // Ensure the brand exists in the branch
                    if ($product->brand_id) {
                        $brandExists = DB::table('branch_brands')
                            ->where('branch_id', $branch_id)
                            ->where('brand_id', $product->brand_id)
                            ->exists();

                        if (!$brandExists) {
                            DB::table('branch_brands')->insert([
                                'branch_id' => $branch_id,
                                'brand_id' => $product->brand_id,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }

                    // Save product to the branch
                    BranchProduct::create([
                        'product_id' => $product->id,
                        'branch_id' => $branch_id,
                    ]);
                }
            } else {
                $branch_id = auth()->user()->branch_id;

                // Ensure the category exists in the user's branch
                $categoryExists = DB::table('branch_categories')
                    ->where('branch_id', $branch_id)
                    ->where('category_id', $product->category_id)
                    ->exists();

                if (!$categoryExists) {
                    DB::table('branch_categories')->insert([
                        'branch_id' => $branch_id,
                        'category_id' => $product->category_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Ensure the brand exists in the user's branch
                if ($product->brand_id) {
                    $brandExists = DB::table('branch_brands')
                        ->where('branch_id', $branch_id)
                        ->where('brand_id', $product->brand_id)
                        ->exists();

                    if (!$brandExists) {
                        DB::table('branch_brands')->insert([
                            'branch_id' => $branch_id,
                            'brand_id' => $product->brand_id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                // Save product to the user's branch
                BranchProduct::create([
                    'product_id' => $product->id,
                    'branch_id' => $branch_id,
                ]);
            }
        });

        $product->save();
        $product->load('category', 'brand');
        $this->clearFrontendProductCache();
        session()->flash('success', __('Product created successfully!'));
        logActivity('Create Product', "Product '{$product->name}' created", $product);
        return Redirect()->route('product.index');
    }

    public function edit(string $id)
    {
        $data['brands'] = Brand::all();
        $data['categories'] = Category::all();
        $data['subCategories'] = SubCategory::orderBy('name', 'asc')->get();
        $data['sizes'] = ProductSize::all();
        $data['colors'] = ProductColor::all();
        $data['units'] = Unit::all();
        $data['allBranch'] = Branch::get();
        $selectedVariations = ProductVariation::where('product_id', $id)->get();
        $data['selectedColorIds'] = $selectedVariations->pluck('color_id')->unique()->toArray();
        $data['selectedSizeIds'] = $selectedVariations->pluck('size_id')->unique()->toArray();
        
        $lockedSizeIds = [];
        $lockedColorIds = [];
        foreach ($selectedVariations as $pv) {
            if ($pv->hasTransactions()) {
                if ($pv->size_id) {
                    $lockedSizeIds[] = $pv->size_id;
                }
                if ($pv->color_id) {
                    $lockedColorIds[] = $pv->color_id;
                }
            }
        }
        $data['lockedSizeIds'] = array_unique($lockedSizeIds);
        $data['lockedColorIds'] = array_unique($lockedColorIds);

        $existingColorImages = [];
        $existingSizePrices = [];
        foreach ($selectedVariations as $pv) {
            if ($pv->color_id) {
                if (!isset($existingColorImages[$pv->color_id])) {
                    $existingColorImages[$pv->color_id] = [
                        'image' => $pv->image,
                        'url' => $pv->image_url,
                        'image_two' => $pv->image_two,
                        'url_two' => $pv->image_two_url,
                    ];
                } else {
                    if (empty($existingColorImages[$pv->color_id]['image']) && !empty($pv->image)) {
                        $existingColorImages[$pv->color_id]['image'] = $pv->image;
                        $existingColorImages[$pv->color_id]['url'] = $pv->image_url;
                    }
                    if (empty($existingColorImages[$pv->color_id]['image_two']) && !empty($pv->image_two)) {
                        $existingColorImages[$pv->color_id]['image_two'] = $pv->image_two;
                        $existingColorImages[$pv->color_id]['url_two'] = $pv->image_two_url;
                    }
                }
            }
            if ($pv->size_id && !isset($existingSizePrices[$pv->size_id])) {
                $existingSizePrices[$pv->size_id] = [
                    'selling_price' => $pv->selling_price,
                    'dis_selling_price' => $pv->dis_selling_price,
                    'purchase_price' => $pv->purchase_price,
                ];
            }
        }
        $data['existingColorImages'] = $existingColorImages;
        $data['existingSizePrices'] = $existingSizePrices;

        $data['data'] = Product::with(['brand', 'category', 'unit.related_unit', 'warranty'])->where('id', $id)->first();
        $data['childCategories'] = ($data['data'] && $data['data']->sub_category_id)
            ? ChildCategory::where('sub_category_id', $data['data']->sub_category_id)->orderBy('name', 'asc')->get()
            : collect([]);
        $data['warranties'] = \App\Models\Warranty::where('status', 1)->orderBy('id', 'asc')->get();

        return view('backend.pages.product.edit', $data);
    }

    public function update(Request $request, string $id)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'barcode' => 'nullable|unique:products,barcode,' . $id,
            'category_id' => 'required',
            'purchase_price' => 'required',
            'selling_price' => 'required',
            'status' => 'required',
        ], [
            'barcode.unique' => 'The product barcode has already been taken !',
        ]);

        $product = Product::findorfail($id);

        if ($request->has('unit_id') && $request->unit_id != $product->unit_id) {
            if ($product->hasTransactions()) {
                session()->flash('error', __('Cannot change product unit because this product already has transaction history.'));
                return back();
            }
        }

        // Check if any variation being deleted has transactions/stock
        $existing = ProductVariation::where('product_id', $product->id)->get();
        $sizes = $request->size ?? [];
        $colors = $request->color ?? [];

        // Ensure locked/transaction size & color IDs are preserved even if omitted by browser due to disabled attribute
        foreach ($existing as $old) {
            if ($old->hasTransactions()) {
                if ($old->size_id && !in_array($old->size_id, $sizes)) {
                    $sizes[] = (string)$old->size_id;
                }
                if ($old->color_id && !in_array($old->color_id, $colors)) {
                    $colors[] = (string)$old->color_id;
                }
            }
        }

        $new = collect();
        if (!empty($sizes) && !empty($colors)) {
            foreach ($sizes as $size) {
                foreach ($colors as $color) {
                    $new->push([$size, $color]);
                }
            }
        } elseif (!empty($sizes) && empty($colors)) {
            foreach ($sizes as $size) {
                $new->push([$size, null]);
            }
        } elseif (empty($sizes) && !empty($colors)) {
            foreach ($colors as $color) {
                $new->push([null, $color]);
            }
        }

        $cannotDelete = [];
        foreach ($existing as $old) {
            $exists = $new->contains(function ($v) use ($old) {
                return $v[0] == $old->size_id && $v[1] == $old->color_id;
            });

            if (!$exists && $old->hasTransactions()) {
                $sizeName = $old->size->size ?? '';
                $colorName = $old->color->color ?? '';
                $comb = [];
                if ($sizeName) $comb[] = "Size: $sizeName";
                if ($colorName) $comb[] = "Color: $colorName";
                $cannotDelete[] = implode(', ', $comb) ?: 'Default Variation';
            }
        }

        if (!empty($cannotDelete)) {
            session()->flash('error', __('Cannot remove variation(s) (:variations) because they have stock or purchase history.', ['variations' => implode(' | ', $cannotDelete)]));
            return back();
        }
        $product->name = $request->name;
        $numberBarcode = rand(000000, 999999);
        if ($product->barcode == NULL) {
            $product->barcode = $numberBarcode;
        } else {
            $product->barcode = $request->barcode;
        }
        $product->category_id = $request->category_id;
        $product->sub_category_id = $request->sub_category_id;
        $product->child_category_id = $request->child_category_id;
        $product->brand_id = $request->brand_id;
        $product->unit_id = $request->unit_id ?? $product->unit_id;
        $product->main_qty = $request->main_qty;
        $product->purchase_price = $request->purchase_price !== null ? (float) $request->purchase_price : 0;
        $discount = $request->discount;
        $dis_selling_price = $request->dis_selling_price !== null ? (float) $request->dis_selling_price : 0;
        if (!empty($discount) && str_contains($discount, '%')) {
            $percent = (float) str_replace('%', '', $discount);
            $discount = ($dis_selling_price * $percent) / 100;
        } elseif ($discount !== null && $discount !== '') {
            $discount = (float) $discount;
        } else {
            $discount = 0;
        }
        $product->discount = $discount;
        $product->dis_selling_price = $dis_selling_price;
        $product->selling_price = $request->selling_price !== null ? (float) $request->selling_price : ($dis_selling_price - $discount);
        $product->status = $request->status;
        $product->is_new_arrival = $request->has('is_new_arrival') ? 1 : 0;
        $product->is_top_selling = $request->has('is_top_selling') ? 1 : 0;
        $product->is_featured = $request->has('is_featured') ? 1 : 0;
        $product->description = $request->description;
        $product->imei = $request->imei;

        // Warranty configuration
        $product->warranty_id = $request->warranty_id ?? null;
        if ($request->filled('warranty_id')) {
            $selectedWarranty = \App\Models\Warranty::find($request->warranty_id);
            if ($selectedWarranty) {
                $product->has_warranty = 1;
                $product->warranty_value = $request->filled('warranty_value') ? $request->warranty_value : $selectedWarranty->duration;
                $product->warranty_unit = $request->filled('warranty_unit') ? $request->warranty_unit : $selectedWarranty->period;
            }
        } elseif ($request->filled('warranty_value') || $request->has_warranty == 1) {
            $product->has_warranty = 1;
            $product->warranty_value = $request->warranty_value ?? null;
            $product->warranty_unit = $request->warranty_unit ?? 'Month';
        } else {
            $product->has_warranty = 0;
            $product->warranty_value = null;
            $product->warranty_unit = null;
        }

        if ($product->save()) {

            $sizes = $request->size ?? [];
            $colors = $request->color ?? [];

            $existing = ProductVariation::where('product_id', $product->id)->get();

            $colorImageMap = [];
            if ($request->filled('old_color_images') && is_array($request->old_color_images)) {
                foreach ($request->old_color_images as $cId => $oldVal) {
                    if (!empty($oldVal)) {
                        $colorImageMap[$cId] = $oldVal;
                    }
                }
            } else {
                foreach ($existing as $oldVar) {
                    if ($oldVar->color_id && !empty($oldVar->image)) {
                        $colorImageMap[$oldVar->color_id] = $oldVar->image;
                    }
                }
            }

            $colorImageTwoMap = [];
            if ($request->filled('old_color_images_two') && is_array($request->old_color_images_two)) {
                foreach ($request->old_color_images_two as $cId => $oldVal) {
                    if (!empty($oldVal)) {
                        $colorImageTwoMap[$cId] = $oldVal;
                    }
                }
            } else {
                foreach ($existing as $oldVar) {
                    if ($oldVar->color_id && !empty($oldVar->image_two)) {
                        $colorImageTwoMap[$oldVar->color_id] = $oldVar->image_two;
                    }
                }
            }

            if ($request->hasFile('color_images')) {
                foreach ($request->file('color_images') as $cId => $cFile) {
                    if ($cFile && $cFile->isValid()) {
                        $cImgName = date('YmdHi') . '_c' . $cId . '_' . $cFile->getClientOriginalName();
                        $cFile->move(public_path('uploads/products/'), $cImgName);
                        $colorImageMap[$cId] = $cImgName;
                    }
                }
            }

            if ($request->hasFile('color_images_two')) {
                foreach ($request->file('color_images_two') as $cId => $cFile) {
                    if ($cFile && $cFile->isValid()) {
                        $cImgName = date('YmdHi') . '_c' . $cId . '_2_' . $cFile->getClientOriginalName();
                        $cFile->move(public_path('uploads/products/'), $cImgName);
                        $colorImageTwoMap[$cId] = $cImgName;
                    }
                }
            }

            foreach ($existing as $old) {
                if ($old->hasTransactions()) {
                    if ($old->size_id && !in_array($old->size_id, $sizes)) {
                        $sizes[] = (string)$old->size_id;
                    }
                    if ($old->color_id && !in_array($old->color_id, $colors)) {
                        $colors[] = (string)$old->color_id;
                    }
                }
            }

            $new = collect();

            $sizeSellingPrices = $request->input('size_selling_price', []);
            $sizeDisSellingPrices = $request->input('size_dis_selling_price', []);
            $sizePurchasePrices = $request->input('size_purchase_price', []);

            // CASE 1: Size + Color Both Provided
            if (!empty($sizes) && !empty($colors)) {
                foreach ($sizes as $size) {
                    $vSellingPrice = ($size && isset($sizeSellingPrices[$size]) && $sizeSellingPrices[$size] !== '') 
                        ? (float)$sizeSellingPrices[$size] 
                        : null;
                    $vDisSellingPrice = ($size && isset($sizeDisSellingPrices[$size]) && $sizeDisSellingPrices[$size] !== '') 
                        ? (float)$sizeDisSellingPrices[$size] 
                        : null;
                    $vPurchasePrice = ($size && isset($sizePurchasePrices[$size]) && $sizePurchasePrices[$size] !== '') 
                        ? (float)$sizePurchasePrices[$size] 
                        : null;

                    foreach ($colors as $color) {
                        $new->push([$size, $color]);

                        ProductVariation::updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'size_id' => $size,
                                'color_id' => $color
                            ],
                            [
                                'image' => $colorImageMap[$color] ?? null,
                                'image_two' => $colorImageTwoMap[$color] ?? null,
                                'selling_price' => $vSellingPrice,
                                'dis_selling_price' => $vDisSellingPrice,
                                'purchase_price' => $vPurchasePrice,
                            ]
                        );
                    }
                }
            }

            // CASE 2: Only Size Provided
            if (!empty($sizes) && empty($colors)) {
                foreach ($sizes as $size) {
                    $vSellingPrice = ($size && isset($sizeSellingPrices[$size]) && $sizeSellingPrices[$size] !== '') 
                        ? (float)$sizeSellingPrices[$size] 
                        : null;
                    $vDisSellingPrice = ($size && isset($sizeDisSellingPrices[$size]) && $sizeDisSellingPrices[$size] !== '') 
                        ? (float)$sizeDisSellingPrices[$size] 
                        : null;
                    $vPurchasePrice = ($size && isset($sizePurchasePrices[$size]) && $sizePurchasePrices[$size] !== '') 
                        ? (float)$sizePurchasePrices[$size] 
                        : null;

                    $new->push([$size, null]);

                    ProductVariation::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'size_id' => $size,
                            'color_id' => null
                        ],
                        [
                            'selling_price' => $vSellingPrice,
                            'dis_selling_price' => $vDisSellingPrice,
                            'purchase_price' => $vPurchasePrice,
                        ]
                    );
                }
            }

            // CASE 3: Only Color Provided
            if (empty($sizes) && !empty($colors)) {
                foreach ($colors as $color) {
                    $new->push([null, $color]);

                    ProductVariation::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'size_id' => null,
                            'color_id' => $color
                        ],
                        [
                            'image' => $colorImageMap[$color] ?? null,
                            'image_two' => $colorImageTwoMap[$color] ?? null,
                        ]
                    );
                }
            }

            // DELETE MISSING VARIATIONS
            foreach ($existing as $old) {
                $exists = $new->contains(function ($v) use ($old) {
                    return $v[0] == $old->size_id && $v[1] == $old->color_id;
                });

                if (!$exists) {
                    $old->delete();
                }
            }
            $this->clearFrontendProductCache();
        }

        // if ($product->save()) {
        //     $newSizes = $request->size ?? [];
        //     $newColors = $request->color ?? [];

        //     $desiredCombinations = [];
        //     foreach ($newSizes as $sizeId) {
        //         foreach ($newColors as $colorId) {
        //             $desiredCombinations[] = ['size_id' => $sizeId, 'color_id' => $colorId];
        //         }
        //     }
        //     $existingCombinations = $product->product_variations()->get(['id', 'size_id', 'color_id']);
        //     $existingMap = [];
        //     foreach ($existingCombinations as $variation) {
        //         $existingMap[$variation->size_id . '-' . $variation->color_id] = $variation->id;
        //     }
        //     foreach ($desiredCombinations as $combo) {
        //         $key = $combo['size_id'] . '-' . $combo['color_id'];
        //         if (!isset($existingMap[$key])) {
        //             $product->product_variations()->create($combo);
        //         } else {
        //             unset($existingMap[$key]);
        //         }
        //     }
        //     if (!empty($existingMap)) {
        //         $product->product_variations()->whereIn('id', array_values($existingMap))->delete();
        //     }
        // }


        DB::transaction(function () use ($request, $product) {
            if ($product->save()) {
                if (auth()->user()->branch_id == 1) {
                    $selectedBranchIds = $request->branch_id ?? [];
                    BranchProduct::where('product_id', $product->id)
                        ->whereNotIn('branch_id', $selectedBranchIds)
                        ->delete();
                    foreach ($selectedBranchIds as $branch_id) {
                        BranchProduct::updateOrCreate(
                            [
                                'product_id' => $product->id,
                                'branch_id' => $branch_id,
                            ]
                        );
                    }
                } else {
                    $branch_id = auth()->user()->branch_id;
                    BranchProduct::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'branch_id' => $branch_id,
                        ]
                    );
                }
            }
        });

        $image = $request->file('images');
        if ($image) {
            $oldImage = $product->images;
            $imgName = date('YmdHi') . $image->getClientOriginalName();
            $image->move(public_path('uploads/products/'), $imgName);
            if (!empty($oldImage) && file_exists(public_path('uploads/products/' . $oldImage))) {
                @unlink(public_path('uploads/products/' . $oldImage));
            }
            $product->images = $imgName;
        } else {
            $product->images = $request->old_image;
        }
        
        $product->save();
        $product->load('category', 'brand');
        $this->clearFrontendProductCache();
        logActivity('Update Product', "Product '{$product->name}' updated", $product);

        session()->flash('success', __('Product update successfully!'));
        return Redirect()->route('product.index');
    }

    public function updateRacks(Request $request, $id)
    {
        $product = Product::with('racks')->findOrFail($id);
        $rackIds = $request->rack_ids ?? [];
        $product->racks()->sync($rackIds);
        $updatedRacks = $product->racks()->get(['racks.id', 'racks.name']);

        return response()->json([
            'success' => true,
            'message' => __('Rack updated successfully for :name', ['name' => $product->name]),
            'product_id' => $product->id,
            'racks' => $updatedRacks,
        ]);
    }

    public function destroy(string $id)
    {
        $product = Product::findorfail($id);

        if ($product->hasTransactions()) {
            session()->flash('error', __('Cannot delete product because it has purchase or sales transaction history.'));
            return back();
        }
        
        // Capture info BEFORE delete
        $product->load('category', 'brand');
        logActivity('Delete Product', "Product '{$product->name}' deleted", $product);

        if (file_exists(public_path('uploads/products/' . $product->images)) and !empty($product->images)) {
            @unlink(public_path('uploads/products/' . $product->images));
        }
        $product->delete();
        $this->clearFrontendProductCache();
        session()->flash('success', __('Successfully Product Delete!'));
        return back();
    }

    public function multipleBarcode()
    {
        return view('backend.pages.product.multiple-barcode');
    }

    public function print(Request $request)
    {
        $productIds = $request->products;   // product id array
        $variationIds = $request->variation; // variation id array
        $qtys = $request->qty;              // qty array

        $settings = [
            'b_company' => $request->has('b_company'),
            'b_category' => $request->has('b_category'),
            'b_name' => $request->has('b_name'),
            'b_variation' => $request->has('b_variation'),
            'b_price' => $request->has('b_price'),
            'b_vat' => $request->has('b_vat'),
        ];

        $printItems = [];

        foreach ($productIds as $index => $productId) {
            $product = Product::find($productId);
            $variationId = $variationIds[$index] ?? null;
            $qty = (int)($qtys[$index] ?? 1);
            $company = get_setting('com_name');
            $currency = get_setting('com_currency');
            // Variation info load
            $variation = null;
            if ($variationId) {
                $variation = \App\Models\ProductVariation::find($variationId);
            }

            $printItems[] = [
                'company'   => $company,
                'currency'   => $currency,
                'product'   => $product,
                'variation' => $variation,
                'qty'       => $qty,
            ];
        }
        return view('backend.pages.product.print-barcode', compact('printItems', 'settings'));
    }


    public function search(Request $request)
    {
        $data['product_id'] = $request->product_id;
        $data['category_id'] = $request->category_id;

        if ($request->product_id != null) {
            $data['products'] = Product::with(['brand', 'category', 'unit.related_unit'])
                ->where('id', $request->product_id)
                ->paginate(10);
        }
        if ($request->category_id != null) {
            $data['products'] = Product::with(['brand', 'category', 'unit.related_unit'])
                ->where('category_id', $request->category_id)
                ->paginate(10);
        }
        return view('backend.pages.product.index', $data);
    }

    public function updateDescription(Request $request, $id)
    {
        $request->validate([
            'description' => 'nullable|string'
        ]);

        $product = Product::findOrFail($id);
        $product->description = $request->description;
        $product->save();
        $this->clearFrontendProductCache();

        return response()->json([
            'status' => 'success',
            'message' => __('Product description updated successfully'),
            'description' => $product->description
        ]);
    }
}