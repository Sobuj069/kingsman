<?php

namespace App\Http\Controllers\Backend;

use App\Models\Rack;
use App\Models\Branch;
use App\Models\BranchRack;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class RackController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!is_rack_enabled()) {
                abort(404);
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        $query = Rack::with(['branchRacks.branch', 'products'])->withCount('products');

        if ($userBranchId == 1) {
            if (session()->has('branch_filter_id')) {
                $rackIds = BranchRack::where('branch_id', $filterBranchId)->pluck('rack_id');
                $query->whereIn('id', $rackIds);
            }
        } else {
            $rackIds = BranchRack::where('branch_id', $userBranchId)->pluck('rack_id');
            $query->whereIn('id', $rackIds);
        }

        $search = $request->barcode ?? $request->search;
        if (!empty($search)) {
            $searchVal = trim($search);
            $query->where(function($q) use ($searchVal) {
                $q->where('name', 'like', "%{$searchVal}%")
                  ->orWhere('code', 'like', "%{$searchVal}%")
                  ->orWhere('description', 'like', "%{$searchVal}%")
                  ->orWhereHas('products', function ($subQ) use ($searchVal) {
                      $subQ->where('barcode', $searchVal)
                           ->orWhere('barcode', 'like', "%{$searchVal}%")
                           ->orWhere('name', 'like', "%{$searchVal}%");
                  });
            });
        }

        $racks = $query->orderBy('id', 'desc')->paginate(20)->appends($request->all());
        $branchs = Branch::all();

        return view('backend.pages.rack.index', compact('racks', 'branchs', 'filterBranchId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'branch_id' => auth()->user()->branch_id == 1 ? 'required|array' : 'nullable',
        ]);

        $rack = new Rack();
        $rack->name = $request->name;
        $rack->code = $request->code ?? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name)));
        $rack->description = $request->description;
        $rack->created_by = auth()->id();
        $rack->branch_id = auth()->user()->branch_id;

        DB::transaction(function () use ($request, $rack) {
            if ($rack->save()) {
                if (auth()->user()->branch_id == 1) {
                    if ($request->has('branch_id') && is_array($request->branch_id)) {
                        foreach ($request->branch_id as $branch_id) {
                            BranchRack::create([
                                'rack_id' => $rack->id,
                                'branch_id' => $branch_id,
                            ]);
                        }
                    } else {
                        BranchRack::create([
                            'rack_id' => $rack->id,
                            'branch_id' => 1,
                        ]);
                    }
                } else {
                    BranchRack::create([
                        'rack_id' => $rack->id,
                        'branch_id' => auth()->user()->branch_id,
                    ]);
                }
            }
        });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Rack created successfully'),
                'rack' => $rack,
            ]);
        }

        session()->flash('success', __('Rack created successfully'));
        return back();
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $rack = Rack::findOrFail($id);
        $rack->name = $request->name;
        if ($request->filled('code')) {
            $rack->code = $request->code;
        }
        $rack->description = $request->description;

        DB::transaction(function () use ($request, $rack) {
            $rack->save();

            if ($request->has('branch_id') && is_array($request->branch_id)) {
                $existingBranchIds = BranchRack::where('rack_id', $rack->id)->pluck('branch_id')->toArray();

                foreach ($request->branch_id as $branch_id) {
                    if (!in_array($branch_id, $existingBranchIds)) {
                        BranchRack::create([
                            'rack_id' => $rack->id,
                            'branch_id' => $branch_id,
                        ]);
                    }
                }

                BranchRack::where('rack_id', $rack->id)
                    ->whereNotIn('branch_id', $request->branch_id)
                    ->delete();
            }
        });

        session()->flash('success', __('Rack updated successfully'));
        return back();
    }

    public function destroy(string $id)
    {
        $rack = Rack::withCount('products')->findOrFail($id);

        // Security / Safety Check: If products are currently in this rack, prevent deletion
        if ($rack->products_count > 0 || $rack->products()->exists()) {
            session()->flash('error', __('Cannot delete rack ":name" because it currently has :count product(s) assigned to it.', [
                'name' => $rack->name,
                'count' => $rack->products()->count(),
            ]));
            return back();
        }

        DB::transaction(function () use ($rack) {
            BranchRack::where('rack_id', $rack->id)->delete();
            $rack->delete();
        });

        session()->flash('success', __('Rack deleted successfully'));
        return back();
    }

    public function ajaxStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $rack = new Rack();
        $rack->name = $request->name;
        $rack->code = $request->code ?? strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name)));
        $rack->description = $request->description;
        $rack->created_by = auth()->id();
        $rack->branch_id = auth()->user()->branch_id;

        DB::transaction(function () use ($request, $rack) {
            if ($rack->save()) {
                $branchId = $request->branch_id ?? (session('branch_filter_id') ?? auth()->user()->branch_id);
                if (auth()->user()->branch_id == 1 && is_array($request->branch_id)) {
                    foreach ($request->branch_id as $bId) {
                        BranchRack::create([
                            'rack_id' => $rack->id,
                            'branch_id' => $bId,
                        ]);
                    }
                } else {
                    BranchRack::create([
                        'rack_id' => $rack->id,
                        'branch_id' => $branchId ?: 1,
                    ]);
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => __('Rack added successfully'),
            'rack' => $rack,
        ]);
    }
}
