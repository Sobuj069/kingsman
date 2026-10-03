<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ServiceCenter;
use Illuminate\Http\Request;

class ServiceCenterController extends Controller
{
    public function index()
    {
        $data['centers'] = ServiceCenter::orderBy('id', 'desc')->paginate(20);
        return view('backend.pages.warranty.service-center.index', $data);
    }

    public function create()
    {
        return view('backend.pages.warranty.service-center.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        
        ServiceCenter::create($request->all() + ['created_by' => auth()->id()]);

        session()->flash('success', __('Service Center Created Successfully'));
        return redirect()->route('service-center.index');
    }

    public function edit($id)
    {
        $data['center'] = ServiceCenter::findOrFail($id);
        return view('backend.pages.warranty.service-center.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $center = ServiceCenter::findOrFail($id);
        $request->validate(['name' => 'required']);
        $center->update($request->all());

        session()->flash('success', __('Service Center Updated Successfully'));
        return redirect()->route('service-center.index');
    }

    public function destroy($id)
    {
        ServiceCenter::findOrFail($id)->delete();
        session()->flash('success', __('Service Center Deleted Successfully'));
        return back();
    }
}
