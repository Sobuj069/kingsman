<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\DiscountGroup;
use Illuminate\Http\Request;

class DiscountGroupController extends Controller
{
    /**
     * Display a listing of the discount groups.
     */
    public function index(Request $request)
    {
        $discountGroups = DiscountGroup::withCount('customers')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('backend.pages.discount_group.index', compact('discountGroups'));
    }

    /**
     * Store a newly created discount group in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255|unique:discount_groups,name',
            'type'  => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
        ], [
            'name.required'  => __('Discount Group name is required.'),
            'name.unique'    => __('This Discount Group name already exists.'),
            'type.required'  => __('Please select a discount type.'),
            'value.required' => __('Please enter a discount value.'),
        ]);

        $discountGroup = new DiscountGroup();
        $discountGroup->name   = $request->name;
        $discountGroup->type   = $request->type;
        $discountGroup->value  = $request->value;
        $discountGroup->status = $request->has('status') ? $request->status : 1;
        $discountGroup->save();

        if ($request->ajax()) {
            return response()->json([
                'success'        => true,
                'message'        => __('Discount Group created successfully'),
                'discountGroup' => $discountGroup,
            ]);
        }

        session()->flash('success', __('Discount Group created successfully'));
        return back();
    }

    /**
     * Update the specified discount group in storage.
     */
    public function update(Request $request, string $id)
    {
        $discountGroup = DiscountGroup::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255|unique:discount_groups,name,' . $id,
            'type'  => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
        ], [
            'name.required'  => __('Discount Group name is required.'),
            'name.unique'    => __('This Discount Group name already exists.'),
            'type.required'  => __('Please select a discount type.'),
            'value.required' => __('Please enter a discount value.'),
        ]);

        $discountGroup->name   = $request->name;
        $discountGroup->type   = $request->type;
        $discountGroup->value  = $request->value;
        $discountGroup->status = $request->has('status') ? $request->status : 1;
        $discountGroup->save();

        session()->flash('success', __('Discount Group updated successfully'));
        return back();
    }

    /**
     * Remove the specified discount group from storage.
     */
    public function destroy(string $id)
    {
        $discountGroup = DiscountGroup::findOrFail($id);
        
        // Nullify discount_group_id on associated customers
        $discountGroup->customers()->update(['discount_group_id' => null]);
        
        $discountGroup->delete();

        session()->flash('success', __('Discount Group deleted successfully'));
        return back();
    }
}
