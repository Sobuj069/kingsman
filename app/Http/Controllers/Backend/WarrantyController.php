<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Warranty;
use Illuminate\Http\Request;

class WarrantyController extends Controller
{
    public function index(Request $request)
    {
        $query = Warranty::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('period', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('period')) {
            $query->where('period', $request->period);
        }

        $warranties = $query->orderBy('id', 'desc')->paginate(15);

        return view('backend.pages.warranty.warranty.index', compact('warranties'));
    }

    public function show($id)
    {
        return redirect()->route('warranty.index');
    }

    public function create()
    {
        return redirect()->route('warranty.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'nullable|integer|min:0',
            'period' => 'required|string|in:Day,Month,Year,Lifetime',
            'description' => 'nullable|string',
        ]);

        Warranty::create([
            'name' => $request->name,
            'duration' => $request->period === 'Lifetime' ? 99 : ($request->duration ?? 1),
            'period' => $request->period,
            'description' => $request->description,
            'status' => $request->status ?? 1,
            'branch_id' => auth()->user()->branch_id ?? 1,
            'created_by' => auth()->id() ?? 1,
        ]);

        session()->flash('success', __('Warranty created successfully.'));
        return redirect()->route('warranty.index');
    }

    public function edit($id)
    {
        $warranty = Warranty::find($id);
        if (request()->ajax()) {
            return response()->json($warranty);
        }
        return redirect()->route('warranty.index');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'nullable|integer|min:0',
            'period' => 'required|string|in:Day,Month,Year,Lifetime',
            'description' => 'nullable|string',
        ]);

        $warranty = Warranty::find($id);
        if (!$warranty) {
            session()->flash('error', __('Warranty not found.'));
            return redirect()->route('warranty.index');
        }

        $warranty->update([
            'name' => $request->name,
            'duration' => $request->period === 'Lifetime' ? 99 : ($request->duration ?? 1),
            'period' => $request->period,
            'description' => $request->description,
            'status' => $request->status ?? 1,
        ]);

        session()->flash('success', __('Warranty updated successfully.'));
        return redirect()->route('warranty.index');
    }

    public function destroy($id)
    {
        $warranty = Warranty::find($id);
        if ($warranty) {
            $warranty->delete();
            session()->flash('success', __('Warranty deleted successfully.'));
        } else {
            session()->flash('info', __('Warranty already deleted.'));
        }

        return redirect()->route('warranty.index');
    }

    public function getWarranties(Request $request)
    {
        $warranties = Warranty::where('status', 1)->orderBy('id', 'asc')->get();
        return response()->json($warranties);
    }
}
