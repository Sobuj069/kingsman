<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    /**
     * Clear all banner-related frontend caches.
     */
    protected function clearBannerCache()
    {
        Cache::forget('frontend_banners');
        Cache::forget('frontend_banners_hero');
        Cache::forget('frontend_banners_dual');
        Cache::forget('frontend_banners_festive');
        Cache::forget('frontend_banners_category');
    }

    /**
     * Display a listing of the banners.
     */
    public function index(Request $request)
    {
        $query = Banner::query();

        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('link', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }

        $banners = $query->orderBy('position', 'asc')
                         ->orderBy('order', 'asc')
                         ->orderBy('id', 'desc')
                         ->paginate(20);

        $branchs = class_exists(Branch::class) ? Branch::all() : collect();

        $positionCounts = [
            'all' => Banner::count(),
            'hero' => Banner::where('position', 'hero')->count(),
            'promo_dual' => Banner::where('position', 'promo_dual')->count(),
            'promo_festive' => Banner::where('position', 'promo_festive')->count(),
            'category' => Banner::where('position', 'category')->count(),
            'general' => Banner::where('position', 'general')->count(),
        ];

        return view('backend.pages.banner.index', compact('banners', 'branchs', 'positionCounts'));
    }

    /**
     * Store a newly created banner in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'position' => 'required|string|max:50',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            'link' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'status' => 'nullable|in:0,1',
            'branch_id' => 'nullable',
        ]);

        $banner = new Banner();
        $banner->title = $request->title;
        $banner->position = $request->position;
        $banner->link = $request->link;
        $banner->order = $request->order ?? 0;
        $banner->status = $request->has('status') ? (int) $request->status : 1;
        $banner->branch_id = $request->branch_id ?: null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = 'banner_' . time() . '_' . Str::random(6) . '.' . $image->getClientOriginalExtension();
            $uploadPath = public_path('uploads/banners');
            
            if (!file_exists($uploadPath)) {
                @mkdir($uploadPath, 0755, true);
            }
            
            $image->move($uploadPath, $filename);
            $banner->image = $filename;
        }

        $banner->save();
        $this->clearBannerCache();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Banner created successfully'),
                'banner' => $banner,
            ]);
        }

        session()->flash('success', __('Banner created successfully'));
        return back();
    }

    /**
     * Update the specified banner in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'position' => 'required|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:5120',
            'link' => 'nullable|string|max:255',
            'order' => 'nullable|integer',
            'status' => 'nullable|in:0,1',
            'branch_id' => 'nullable',
        ]);

        $banner = Banner::findOrFail($id);
        $banner->title = $request->title;
        $banner->position = $request->position;
        $banner->link = $request->link;
        $banner->order = $request->order ?? 0;
        $banner->status = $request->has('status') ? (int) $request->status : 1;
        $banner->branch_id = $request->branch_id ?: null;

        if ($request->hasFile('image')) {
            // Unlink old image if stored in uploads/banners
            if (!empty($banner->image) && file_exists(public_path('uploads/banners/' . $banner->image))) {
                @unlink(public_path('uploads/banners/' . $banner->image));
            }

            $image = $request->file('image');
            $filename = 'banner_' . time() . '_' . Str::random(6) . '.' . $image->getClientOriginalExtension();
            $uploadPath = public_path('uploads/banners');
            
            if (!file_exists($uploadPath)) {
                @mkdir($uploadPath, 0755, true);
            }
            
            $image->move($uploadPath, $filename);
            $banner->image = $filename;
        }

        $banner->save();
        $this->clearBannerCache();

        session()->flash('success', __('Banner updated successfully'));
        return back();
    }

    /**
     * Remove the specified banner from storage.
     */
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        if (!empty($banner->image) && file_exists(public_path('uploads/banners/' . $banner->image))) {
            @unlink(public_path('uploads/banners/' . $banner->image));
        }

        $banner->delete();
        $this->clearBannerCache();

        session()->flash('success', __('Banner deleted successfully'));
        return back();
    }

    /**
     * Quick status toggle.
     */
    public function toggleStatus(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);
        $banner->status = $banner->status == 1 ? 0 : 1;
        $banner->save();
        $this->clearBannerCache();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $banner->status,
                'message' => __('Banner status updated successfully'),
            ]);
        }

        session()->flash('success', __('Banner status updated successfully'));
        return back();
    }
}
