<?php

namespace App\Http\Controllers\Backend;

use App\Models\Platform;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlatformController extends Controller
{
    public function index()
    {
        $platforms = Platform::orderBy('id', 'desc')->paginate(20);
        return view('backend.pages.platform.index', compact('platforms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:platforms,name',
        ]);

        $platform = new Platform();
        $platform->name = $request->name;
        $platform->slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name)));
        $platform->status = 1;
        $platform->save();

        session()->flash('success', __('Platform created successfully'));
        return back();
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:platforms,name,' . $id,
        ]);

        $platform = Platform::find($id);
        $platform->name = $request->name;
        $platform->slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name)));
        $platform->save();

        session()->flash('success', __('Platform updated successfully'));
        return back();
    }

    public function destroy(string $id)
    {
        $platform = Platform::find($id);
        $platform->delete();
        session()->flash('success', __('Platform deleted successfully'));
        return back();
    }
}
