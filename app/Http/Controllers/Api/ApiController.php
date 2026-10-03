<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\BranchBrand;
use App\Models\BranchCategory;
use App\Models\BranchProduct;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * Default branch ID (Must be 2 as requested)
     */
    protected int $defaultBranchId = 2;

    protected function getBranchId(Request $request): int
    {
        return (int) $request->get('branch_id', $this->defaultBranchId);
    }

    /**
     * Get products for default branch (branch_id = 2)
     */
    public function products(Request $request)
    {
        $branchId = $this->getBranchId($request);

        $productIds = BranchProduct::where('branch_id', $branchId)->pluck('product_id')->toArray();

        $query = Product::with(['category', 'brand', 'unit'])->where('is_service', 0);

        if (!empty($productIds)) {
            $query->whereIn('id', $productIds);
        }

        $products = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Products retrieved successfully',
            'branch_id' => $branchId,
            'count' => $products->count(),
            'data' => $products,
        ]);
    }

    /**
     * Get categories for default branch (branch_id = 2)
     */
    public function categories(Request $request)
    {
        $branchId = $this->getBranchId($request);

        $categoryIds = BranchCategory::where('branch_id', $branchId)->pluck('category_id')->toArray();

        $query = Category::query();

        if (!empty($categoryIds)) {
            $query->whereIn('id', $categoryIds);
        }

        $categories = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Categories retrieved successfully',
            'branch_id' => $branchId,
            'count' => $categories->count(),
            'data' => $categories,
        ]);
    }

    /**
     * Get brands for default branch (branch_id = 2)
     */
    public function brands(Request $request)
    {
        $branchId = $this->getBranchId($request);

        $brandIds = BranchBrand::where('branch_id', $branchId)->pluck('brand_id')->toArray();

        $query = Brand::query();

        if (!empty($brandIds)) {
            $query->whereIn('id', $brandIds);
        }

        $brands = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'status' => true,
            'message' => 'Brands retrieved successfully',
            'branch_id' => $branchId,
            'count' => $brands->count(),
            'data' => $brands,
        ]);
    }

    /**
     * Get customers for default branch (branch_id = 2)
     */
    public function customers(Request $request)
    {
        $branchId = $this->getBranchId($request);

        $customers = Customer::where(function ($q) use ($branchId) {
            $q->where('branch_id', $branchId)
              ->orWhereNull('branch_id');
        })
        ->orderBy('id', 'desc')
        ->get();

        return response()->json([
            'status' => true,
            'message' => 'Customers retrieved successfully',
            'branch_id' => $branchId,
            'count' => $customers->count(),
            'data' => $customers,
        ]);
    }

    /**
     * Get suppliers for default branch (branch_id = 2)
     */
    public function suppliers(Request $request)
    {
        $branchId = $this->getBranchId($request);

        $suppliers = Supplier::where(function ($q) use ($branchId) {
            $q->where('branch_id', $branchId)
              ->orWhereNull('branch_id');
        })
        ->orderBy('id', 'desc')
        ->get();

        return response()->json([
            'status' => true,
            'message' => 'Suppliers retrieved successfully',
            'branch_id' => $branchId,
            'count' => $suppliers->count(),
            'data' => $suppliers,
        ]);
    }
}
