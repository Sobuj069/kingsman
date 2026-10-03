<?php

namespace App\Http\Controllers\Backend;

use App\Models\Branch;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\BranchProduct;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        $query = Product::where('is_service', '1');

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $productIds = BranchProduct::where('branch_id', $filterBranchId)
                    ->pluck('product_id');
                $data['produc'] = Product::whereIn('id', $productIds)->where('is_service', '1')->get();
                $data['total_product'] = Product::whereIn('id', $productIds)->where('is_service', '1')->count();
                $query->whereIn('id', $productIds);
            } else {
                $data['produc'] = Product::where('is_service', '1')->get();
                $data['total_product'] = Product::where('is_service', '1')->count();
            }
        } else {
            $productIds = BranchProduct::where('branch_id', $userBranchId)
                ->pluck('product_id');
            $data['produc'] = Product::whereIn('id', $productIds)->where('is_service', '1')->get();
            $data['total_product'] = Product::whereIn('id', $productIds)->where('is_service', '1')->count();
            $query->whereIn('id', $productIds);
        }

        if ($request->product_id != null) {
            $query->where('id', $request->product_id);
        }
        if ($request->barcode != null) {
            $query->where('barcode', $request->barcode);
        }

        $data['products'] = $query
            ->orderBy('created_at', 'DESC')->paginate(20)->appends($request->only(['product_id', 'barcode']));
        $data['product_id'] = $request->product_id;
        $data['barcode'] = $request->barcode;

        return view('backend.pages.service.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $allBranch = Branch::get();
        return view('backend.pages.service.create', compact('allBranch'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate(
            [
                'name' => 'required',
                'barcode' => 'unique:products',
                'selling_price' => 'required',
                'purchase_price' => 'required',
                'status' => 'required',
            ],
            [
                'barcode.unique' => 'The service barcode has already been taken !',
            ]
        );

        $product = new Product();
        $product->date = date('Y-m-d');
        $product->name = $request->name;
        $product->is_service = 1;
        $numberBarcode = rand(000000, 999999);
        if ($request->barcode == NULL) {
            $product->barcode = $numberBarcode;
        } else {
            $product->barcode = $request->barcode;
        }
        $product->selling_price = $request->selling_price;
        $product->purchase_price = $request->purchase_price;
        $product->status = $request->status;
        $product->description = $request->description;

        DB::transaction(function () use ($request, $product) {
            if ($product->save()) {
                if (auth()->user()->branch_id == 1) {
                    foreach ($request->branch_id as $branch_id) {
                        BranchProduct::create([
                            'product_id' => $product->id,
                            'branch_id' => $branch_id,
                        ]);
                    }
                } else {
                    $branch_id = auth()->user()->branch_id;
                    // Save product to the user's branch
                    BranchProduct::create([
                        'product_id' => $product->id,
                        'branch_id' => $branch_id,
                    ]);
                }
            }
        });

        $product->save();
        session()->flash('success', __('Service created successfully!'));
        return Redirect()->route('service.index');
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
        $data['data'] = Product::where('is_service', 1)->where('id', $id)->first();
        $data['allBranch'] = Branch::get();
        return view('backend.pages.service.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'barcode' => 'numeric',
            'selling_price' => 'required',
            'purchase_price' => 'required',
            'status' => 'required',
        ]);
        $product = Product::findorfail($id);
        $product->name = $request->name;
        $numberBarcode = rand(000000, 999999);
        if ($product->barcode == NULL) {
            $product->barcode = $numberBarcode;
        } else {
            $product->barcode = $request->barcode;
        }
        $product->selling_price = $request->selling_price;
        $product->purchase_price = $request->purchase_price;
        $product->status = $request->status;
        $product->description = $request->description;
        $product->save();

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


        session()->flash('success', __('Service update successfully!'));
        return Redirect()->route('service.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findorfail($id);
        $product->delete();
        session()->flash('success', __('Successfully Service Delete!'));
        return back();
    }
}
