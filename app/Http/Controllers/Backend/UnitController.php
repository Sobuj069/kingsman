<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::orderBy('id', 'desc')->paginate(20);
        return view('backend.pages.unit.index', compact('units'));
    }

    public function store(Request $request)
    {
        $unit = new Unit();
        $unit->name = $request->name;
        $unit->related_unit_id = $request->related_unit_id;
        $unit->related_sign = $request->related_sign;
        $unit->related_value = $request->related_value;
        $unit->save();
        if ($unit) {
            session()->flash('success', __('Unit added successfully'));
            return back();
        } else {
            session()->flash('error', __('Unit could not be added'));
            return back();
        }
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
        $unit = Unit::findOrFail($id);
        $unit->name = $request->name;
        $unit->related_unit_id = $request->related_unit_id;
        $unit->related_sign = $request->related_sign;
        $unit->related_value = $request->related_value;
        $unit->save();

        session()->flash('success', __('Unit updated successfully'));
        return back();
    }

    public function destroy(string $id)
    {
        //
        $unit = Unit::findOrFail($id);
        $unit->delete();
        session()->flash('success', __('Unit deleted successfully'));
        return back();
    }
}
