<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Unit;
use App\Models\UsedProduct;
use Illuminate\Http\Request;

class UsedProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['products'] = UsedProduct::with(['brand', 'category', 'unit.related_unit'])
            ->orderBy('created_at', 'DESC')->paginate(10);
        $data['product_id'] = $request->product_id;
        $data['category_id'] = $request->category_id;
        $data['barcode'] = $request->barcode;
        // $data['com'] = BusinessSetting::where('id', 6)->first();

        if ($request->product_id != null) {
            $data['products'] = UsedProduct::with(['brand', 'category', 'unit.related_unit'])
                ->where('id', $request->product_id)
                ->orderBy('created_at', 'DESC')
                ->paginate(10)->appends([
                    'product_id' => $request->product_id,
                ]);
        }
        if ($request->barcode != null) {
            $data['products'] = UsedProduct::with(['brand', 'category', 'unit.related_unit'])
                ->where('barcode', $request->barcode)
                ->orderBy('created_at', 'DESC')
                ->paginate(10);
        }
        if ($request->category_id != null) {
            $data['products'] = UsedProduct::with(['brand', 'category', 'unit.related_unit'])
                ->where('category_id', $request->category_id)
                ->orderBy('created_at', 'DESC')
                ->paginate(30)
                ->appends([
                    'category_id' => $request->category_id,
                ]);
        }
        if ($request->category_id != null && $request->product_id != null) {
            $data['products'] = UsedProduct::with(['brand', 'category', 'unit.related_unit'])
                ->where('category_id', $request->category_id)->where('id', $request->product_id)
                ->orderBy('created_at', 'DESC')
                ->paginate(10)
                ->appends([
                    'category_id' => $request->category_id,
                    'product_id' => $request->product_id,
                ]);
        }

        return view('backend.pages.usedProduct.product.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $brands = Brand::all();
        $categories = Category::all();
        $units = Unit::all();
        $suppliers = Supplier::all();
        return view('backend.pages.usedProduct.product.create', compact('brands', 'categories', 'units', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'barcode' => 'unique:used_products',
                'category_id' => 'required',
                'unit_id' => 'required',
                'purchase_price' => 'required',
                'status' => 'required',
            ],
            [
                'barcode.unique' => 'The product barcode has already been taken !',
            ]
        );

        $product = new UsedProduct();
        $product->name = $request->name;
        $numberBarcode = rand(000000, 999999);
        if ($request->barcode == NULL) {
            $product->barcode = $numberBarcode;
        } else {
            $product->barcode = $request->barcode;
        }
        // $product->is_service = 0;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->unit_id = $request->unit_id;
        $product->main_qty = $request->main_qty;
        $product->sub_qty = $request->sub_qty;
        $product->purchase_price = $request->purchase_price;
        // $product->selling_price = $request->selling_price;
        $product->status = $request->status;
        $product->description = $request->description;


        $product->save();
        // if($request->main_qty != null || $request->main_qty != null && $request->sub_qty != null){
        //     $this->open_stock_purchase($request, $product->id);
        // }
        session()->flash('success', 'Product created successfully!');
        return Redirect()->route('usedProduct.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data['brands'] = Brand::all();
        $data['categories'] = Category::all();
        $data['units'] = Unit::all();
        $data['suppliers'] = Supplier::all();
        $data['data'] = UsedProduct::with(['brand', 'category', 'unit.related_unit'])->where('id', $id)->first();
        return view('backend.pages.usedProduct.product.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'barcode' => 'numeric',
            'category_id' => 'required',
            'purchase_price' => 'required',
            'status' => 'required',
        ]);

        $product = UsedProduct::findorfail($id);
        $product->name = $request->name;
        $numberBarcode = rand(000000, 999999);
        if ($product->barcode == NULL) {
            $product->barcode = $numberBarcode;
        } else {
            $product->barcode = $request->barcode;
        }
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->purchase_price = $request->purchase_price;
        $product->status = $request->status;
        $product->description = $request->description;

        $product->save();
        session()->flash('success', 'Product update successfully!');
        return Redirect()->route('usedProduct.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = UsedProduct::findorfail($id);
        $product->delete();
        session()->flash('success', 'Successfully Product Delete!');
        return back();

    }
}
