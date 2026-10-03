<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
use Illuminate\Http\Request;

class ChildCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $subCategories = SubCategory::with('category')->orderBy('name', 'asc')->get();

        $query = ChildCategory::with(['category', 'subCategory'])->orderBy('id', 'desc');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('sub_category_id')) {
            $query->where('sub_category_id', $request->sub_category_id);
        }

        $childCategories = $query->paginate(20)->appends($request->all());

        return view('backend.pages.category.childcategory', compact('childCategories', 'categories', 'subCategories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sub_category_id' => 'required|exists:sub_categories,id',
            'name' => 'required|string|max:255',
        ]);

        $subCategory = SubCategory::findOrFail($request->sub_category_id);

        $childCategory = new ChildCategory();
        $childCategory->category_id = $request->category_id ?: $subCategory->category_id;
        $childCategory->sub_category_id = $request->sub_category_id;
        $childCategory->name = $request->name;
        $childCategory->slug = \Illuminate\Support\Str::slug($request->name);
        $childCategory->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Child Category created successfully'),
                'child_category' => $childCategory,
            ]);
        }

        session()->flash('success', __('Child Category created successfully'));
        return back();
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'sub_category_id' => 'required|exists:sub_categories,id',
            'name' => 'required|string|max:255',
        ]);

        $subCategory = SubCategory::findOrFail($request->sub_category_id);

        $childCategory = ChildCategory::findOrFail($id);
        $childCategory->category_id = $request->category_id ?: $subCategory->category_id;
        $childCategory->sub_category_id = $request->sub_category_id;
        $childCategory->name = $request->name;
        $childCategory->slug = \Illuminate\Support\Str::slug($request->name);
        $childCategory->save();

        session()->flash('success', __('Child Category updated successfully'));
        return back();
    }

    public function destroy(string $id)
    {
        $childCategory = ChildCategory::findOrFail($id);
        $childCategory->delete();

        session()->flash('success', __('Child Category deleted successfully'));
        return back();
    }

    public function getBySubCategory(Request $request)
    {
        $subCategoryId = $request->sub_category_id;
        if (!$subCategoryId) {
            return response()->json([]);
        }

        $childCategories = ChildCategory::where('sub_category_id', $subCategoryId)->orderBy('name', 'asc')->get();

        return response()->json($childCategories);
    }
}
