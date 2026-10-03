<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\WarrantyDelivery;
use App\Models\WarrantyClaim;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarrantyDeliveryController extends Controller
{
    public function index()
    {
        $data['deliveries'] = WarrantyDelivery::with('claim.customer', 'product')->orderBy('id', 'desc')->paginate(20);
        return view('backend.pages.warranty.delivery.index', $data);
    }

    public function create(Request $request)
    {
        $data['claim'] = null;
        if ($request->claim_id) {
            $data['claim'] = WarrantyClaim::with('customer', 'product')->find($request->claim_id);
        }
        $data['products'] = Product::where('status', 1)->get();
        return view('backend.pages.warranty.delivery.create', $data);
    }

    public function store(Request $request)
    {
        $product = Product::findOrFail($request->delivered_product_id);
        
        $rules = [
            'warranty_claim_id' => 'required',
            'delivered_product_id' => 'required',
            'delivered_date' => 'required|date',
        ];

        if ($product->imei == 1) {
            $rules['delivered_serial'] = 'required';
        } else {
            $rules['delivered_serial'] = 'nullable';
        }

        $request->validate($rules);

        DB::transaction(function () use ($request) {
            WarrantyDelivery::create([
                'warranty_claim_id' => $request->warranty_claim_id,
                'delivered_product_id' => $request->delivered_product_id,
                'delivered_serial' => $request->delivered_serial,
                'delivery_note' => $request->delivery_note,
                'delivered_date' => $request->delivered_date,
                'created_by' => auth()->id(),
            ]);

            WarrantyClaim::where('id', $request->warranty_claim_id)->update(['status' => 'Delivered']);
        });

        session()->flash('success', __('Product Delivered Successfully'));
        return redirect()->route('warranty-delivery.index');
    }

    public function show($id)
    {
        $delivery = WarrantyDelivery::with('claim.customer', 'product')->findOrFail($id);
        return view('backend.pages.warranty.delivery.print', compact('delivery'));
    }
}
