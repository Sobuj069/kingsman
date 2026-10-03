<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductSize;
use App\Models\ProductColor;
use App\Models\Variation;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product_size = ProductSize::orderBy('id', 'DESC')->get();
        return view('backend.pages.product-variation.size', compact('product_size'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'size_name' => 'required|string|unique:product_sizes,size', // Correct column name in the 'product_sizes' table
        // ], [
        //     'size_name.unique' => 'This size already exists.', // Custom error message
        // ]);

        $size = new ProductSize();
        $size->size = $request->size_name;
        $size->save();
        $this->generateVariations();

        session()->flash('success', __('Size created successfully'));
        return back();
    }
    public function ajaxStore(Request $request)
    {
        try {
            $request->validate([
                'size_name' => 'required|string|max:255|unique:product_sizes,size',
            ]);

            $size = new ProductSize();
            $size->size = $request->size_name;
            $size->save();

            // Generate variations
            $this->generateVariations();

            return response()->json([
                'success' => true,
                'message' => 'Size created successfully',
                'size' => [
                    'id' => $size->id,
                    'size_name' => $size->size // ✅ Frontend-এ যেভাবে show করবে
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $size = ProductSize::find($id);
        $size->delete();
        session()->flash('success', __('Size deleted successfully'));
        return back();
    }

    function generateVariations()
    {
        $sizes = ProductSize::all();
        $colors = ProductColor::all();

        // Clear existing variations
        Variation::truncate();

        foreach ($sizes as $size) {
            foreach ($colors as $color) {
                Variation::create([
                    'size_id' => $size->id,
                    'color_id' => $color->id,
                ]);
            }
        }

        return response()->json(['message' => 'Variations generated successfully.']);
    }
}
