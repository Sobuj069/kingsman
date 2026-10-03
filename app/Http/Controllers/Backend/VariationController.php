<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductSize;
use App\Models\ProductColor;
use App\Models\Variation;

class VariationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $variations = Variation::orderBy('id','DESC')->get();
        return view('backend.pages.product-variation.variation',compact('variations'));
    }

    public function generateVariations()
    {
        // Fetch all sizes and colors
        $sizes = ProductSize::all();
        $colors = ProductColor::all();

        // Clear existing variations
        Variation::truncate();

        // Generate combinations
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

    public function getVariations()
    {
        $variations = Variation::with('size', 'color')->get();
        return response()->json($variations);
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
        //
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
        //
    }
}
