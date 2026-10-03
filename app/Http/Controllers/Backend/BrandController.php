<?php

namespace App\Http\Controllers\Backend;

use App\Models\Brand;
use App\Models\Branch;
use App\Models\BranchBrand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class BrandController extends Controller
{
    public function index()
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        if ($userBranchId == 1) {
            if (session()->has('branch_filter_id')) {
                $brandIds = BranchBrand::where('branch_id', $filterBranchId)->pluck('brand_id');
                $brands = Brand::whereIn('id', $brandIds)->orderBy('id', 'desc')->paginate(20);
            } else {
                $brands = Brand::orderBy('id', 'desc')->paginate(20);
            }
        } else {
            $brandIds = BranchBrand::where('branch_id', $userBranchId)->pluck('brand_id');
            $brands = Brand::whereIn('id', $brandIds)->orderBy('id', 'desc')->paginate(20);
        }
        $brandIds = BranchBrand::where('branch_id', $userBranchId)->pluck('brand_id');

        $branchs = Branch::all();
        return view('backend.pages.brand.index', compact('brands', 'branchs', 'filterBranchId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'branch_id' => auth()->user()->branch_id == 1 ? 'required|array' : 'nullable',
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name)));

        DB::transaction(function () use ($request, $brand) {
            if ($brand->save()) {
                if (auth()->user()->branch_id == 1) {
                    foreach ($request->branch_id as $key => $branch_id) {
                        $branch_brand = new BranchBrand();
                        $branch_brand->brand_id = $brand->id;
                        $branch_brand->branch_id = $branch_id;
                        $branch_brand->save();
                    }
                } else {
                    $branch_brand = new BranchBrand();
                    $branch_brand->brand_id = $brand->id;
                    $branch_brand->branch_id = auth()->user()->branch_id;
                    $branch_brand->save();
                }
            }
        });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Brand created successfully'),
                'brand' => $brand,
            ]);
        }

        session()->flash('success', __('Brand created successfully'));
        return back();
    }

    public function update(Request $request, string $id)
    {
        $brand = Brand::find($id);
        $brand->name = $request->name;
        //slug
        $brand->slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name)));

        DB::transaction(function () use ($request, $brand) {
            if ($brand->save()) {
                if($request->branch_id != null){
                $existingBranchIds = BranchBrand::where('brand_id', $brand->id)
                    ->pluck('branch_id')
                    ->toArray();
        
                foreach ($request->branch_id as $branch_id) {
                    if (!in_array($branch_id, $existingBranchIds)) {
                        BranchBrand::create([
                            'brand_id' => $brand->id,
                            'branch_id' => $branch_id,
                        ]);
                    }
                }
                BranchBrand::where('brand_id', $brand->id)
                    ->whereNotIn('branch_id', $request->branch_id)
                    ->delete();
                }
            }
        });

        $brand->save();
        session()->flash('success', __('Brand updated successfully'));
        return back();
    }

    public function destroy(string $id)
    {
        $brand = Brand::find($id);
        $brand->delete();
        session()->flash('success', __('Brand deleted successfully'));
        return back();
    }
}
