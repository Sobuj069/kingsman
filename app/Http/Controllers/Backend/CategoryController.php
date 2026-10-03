<?php

namespace App\Http\Controllers\Backend;

use App\Models\Branch;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\BranchCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $categoryIds = BranchCategory::where('branch_id', $filterBranchId)->pluck('category_id');
                $categories = Category::whereIn('id', $categoryIds)->orderBy('id', 'desc')->paginate(20);
            } else {
                $categories = Category::orderBy('id', 'desc')->paginate(20);
            }
        } else {
            $categoryIds = BranchCategory::where('branch_id', $userBranchId)->pluck('category_id');
            $categories = Category::whereIn('id', $categoryIds)->orderBy('id', 'desc')->paginate(20);
        }
        $categoryIds = BranchCategory::where('branch_id', $userBranchId)->pluck('category_id');

        $branchs = Branch::all();
        return view('backend.pages.category.index', compact('categories', 'branchs', 'filterBranchId', 'categoryIds'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:3072',
            'branch_id' => auth()->user()->branch_id == 1 ? 'required|array' : 'nullable',
        ]);

        $category = new Category();
        $category->name = $request->name;

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'cat_' . time() . '_' . rand(100, 999) . '.' . $image->getClientOriginalExtension();
            $uploadPath = public_path('uploads/category');
            if (!file_exists($uploadPath)) {
                @mkdir($uploadPath, 0755, true);
            }
            $image->move($uploadPath, $imageName);
            $category->image = $imageName;
        }

        DB::transaction(function () use ($request, $category) {
            if ($category->save()) {
                if (auth()->user()->branch_id == 1 && $request->branch_id) {
                    foreach ($request->branch_id as $key => $branch_id) {
                        $branch_category = new BranchCategory();
                        $branch_category->category_id = $category->id;
                        $branch_category->branch_id = $branch_id;
                        $branch_category->save();
                    }
                } else {
                    $branch_category = new BranchCategory();
                    $branch_category->category_id = $category->id;
                    $branch_category->branch_id = auth()->user()->branch_id;
                    $branch_category->save();
                }
            }
        });

        Cache::forget('frontend_global_categories');
        Cache::forget('frontend_categories');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Category created successfully'),
                'category' => $category,
            ]);
        }

        session()->flash('success', __('Category created successfully'));
        return back();
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:3072',
        ]);

        $category = Category::findOrFail($id);
        $category->name = $request->name;

        // Handle Image Update
        if ($request->hasFile('image')) {
            $uploadPath = public_path('uploads/category');
            if (!file_exists($uploadPath)) {
                @mkdir($uploadPath, 0755, true);
            }

            // Remove previous image if exists
            if (!empty($category->image) && file_exists($uploadPath . '/' . $category->image)) {
                @unlink($uploadPath . '/' . $category->image);
            }

            $image = $request->file('image');
            $imageName = 'cat_' . time() . '_' . rand(100, 999) . '.' . $image->getClientOriginalExtension();
            $image->move($uploadPath, $imageName);
            $category->image = $imageName;
        }

        DB::transaction(function () use ($request, $category) {
            if ($category->save()) {
                if ($request->branch_id != null) {
                    $existingBranchIds = BranchCategory::where('category_id', $category->id)
                        ->pluck('branch_id')
                        ->toArray();

                    foreach ($request->branch_id as $branch_id) {
                        if (!in_array($branch_id, $existingBranchIds)) {
                            BranchCategory::create([
                                'category_id' => $category->id,
                                'branch_id' => $branch_id,
                            ]);
                        }
                    }
                    BranchCategory::where('category_id', $category->id)
                        ->whereNotIn('branch_id', $request->branch_id)
                        ->delete();
                }
            }
        });

        Cache::forget('frontend_global_categories');
        Cache::forget('frontend_categories');

        session()->flash('success', __('Category updated successfully'));
        return back();
    }

    public function destroy(string $id)
    {
        $category = Category::find($id);
        if ($category) {
            if (!empty($category->image) && file_exists(public_path('uploads/category/' . $category->image))) {
                @unlink(public_path('uploads/category/' . $category->image));
            }
            $category->delete();
        }

        Cache::forget('frontend_global_categories');
        Cache::forget('frontend_categories');

        session()->flash('success', __('Category deleted successfully'));
        return back();
    }
}
