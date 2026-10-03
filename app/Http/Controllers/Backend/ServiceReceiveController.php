<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ServiceReceive;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ServiceReceiveController extends Controller
{
    public function index(Request $request)
    {
        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        if (in_array(auth()->user()->id, [1, 2])) {
            $query = ServiceReceive::with('customer', 'branch', 'user');
        } else {
            $query = ServiceReceive::with('customer', 'branch', 'user')->where('branch_id', $userBranchId);
        }

        if ($userBranchId == 1 && $filterBranchId) {
            $query->where('branch_id', $filterBranchId);
        }

        $barcode = $request->barcode ?? $request->service_no;
        $data['barcode'] = $barcode;
        if (!empty($barcode)) {
            $barcodeVal = trim($barcode);
            $query->where(function($q) use ($barcodeVal) {
                $q->where('service_no', 'like', "%{$barcodeVal}%")
                  ->orWhere('pname', 'like', "%{$barcodeVal}%")
                  ->orWhereHas('customer', function ($subQ) use ($barcodeVal) {
                      $subQ->where('phone', 'like', "%{$barcodeVal}%")
                           ->orWhere('name', 'like', "%{$barcodeVal}%");
                  });
            });
        }

        if ($request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->start_date) {
            $query->whereDate('received_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('received_date', '<=', $request->end_date);
        }

        if ($request->pname) {
            $query->where('pname', $request->pname);
        }

        $data['services'] = $query->orderBy('created_at', 'desc')->paginate(20)->appends($request->all());
        $data['allCustomer'] = Customer::get();
        $data['allProducts'] = Product::orderBy('name', 'asc')->get();
        
        return view('backend.pages.service.received.index', $data);
    }

    public function create()
    {
        $data['customers'] = Customer::orderBy('id', 'desc')->get();
        $data['branches'] = Branch::get();
        $data['products'] = Product::where('status', 1)->orderBy('name', 'asc')->get();
        return view('backend.pages.service.received.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'cname' => 'required',
            'cphone' => 'required',
            'pname' => 'required',
            'received_date' => 'required|date',
        ]);

        $branchId = auth()->user()->branch_id == 1 ? $request->branch_id : auth()->user()->branch_id;

        $last_receive = ServiceReceive::where('branch_id', $branchId)
            ->orderBy('id', 'desc')
            ->first();

        if ($last_receive == null) {
            $service_no = "SRV-0000001";
        } else {
            $numberPart = (int) substr($last_receive->service_no, strlen("SRV-"));
            $newNumber = str_pad($numberPart + 1, 7, '0', STR_PAD_LEFT);
            $service_no = "SRV-" . $newNumber;
        }

        ServiceReceive::create([
            'branch_id' => $branchId,
            'customer_id' => $request->customer_id,
            'service_no' => $service_no,
            'cname' => $request->cname,
            'cphone' => $request->cphone,
            'caddress' => $request->caddress,
            'pname' => $request->pname,
            'pmodel' => $request->pmodel,
            'pdescription' => $request->pdescription,
            'received_date' => $request->received_date,
            'deli_date' => $request->deli_date,
            'status' => 'Pending',
            'created_by' => auth()->id(),
        ]);

        session()->flash('success', __('Service Received Successfully'));
        return redirect()->route('service-receive.index');
    }

    public function edit($id)
    {
        $data['service'] = ServiceReceive::findOrFail($id);
        $data['customers'] = Customer::get();
        $data['branches'] = Branch::get();
        $data['products'] = Product::where('status', 1)->orderBy('name', 'asc')->get();
        return view('backend.pages.service.received.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $service = ServiceReceive::findOrFail($id);
        
        $request->validate([
            'cname' => 'required',
            'cphone' => 'required',
            'pname' => 'required',
        ]);

        $service->update([
            'customer_id' => $request->customer_id,
            'cname' => $request->cname,
            'cphone' => $request->cphone,
            'caddress' => $request->caddress,
            'pname' => $request->pname,
            'pmodel' => $request->pmodel,
            'pdescription' => $request->pdescription,
            'received_date' => $request->received_date,
            'deli_date' => $request->deli_date,
            'status' => $request->status,
        ]);

        session()->flash('success', __('Service Updated Successfully'));
        return redirect()->route('service-receive.index');
    }

    public function destroy($id)
    {
        $service = ServiceReceive::findOrFail($id);
        $service->delete();
        session()->flash('success', __('Service Deleted Successfully'));
        return back();
    }
}
