<?php

namespace App\Http\Controllers\Backend;

use App\Models\Branch;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\DiscountGroup;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $data['customers'] = Customer::orderBy('id','desc')->paginate(20);
        // $data['customer_id'] = $request->customer_id;
        // $data['phone_no'] = $request->phone_no;
        // $data['allBranch'] = Branch::orderBy('id','asc')->get();

        // $userBranchId = auth()->user()->branch_id;
        // $filterBranchId = session('branch_filter_id', null);

        // $query = Customer::orderBy('id', 'asc');

        // if($userBranchId == 1){
        //     if($filterBranchId){
        //         $query->where('branch_id', $filterBranchId);
        //     }else{
        //         $data['customers'] = Customer::orderBy('id','asc')->paginate(20);
        //     }
        // }else{
        //     $query->where('branch_id', $userBranchId);
        // }

        // if($request->customer_id != null){
        //     $query->where('id', $request->customer_id);
        // }
        // if($request->phone_no != null){
        //     $query->where('phone', $request->phone_no);
        // }

        // $data['customers'] = $query->paginate(20)->appends($request->all());

        $data['customer_id'] = $request->customer_id;
        $data['phone_no'] = $request->phone_no;
        $data['allBranch'] = Branch::orderBy('id', 'asc')->get();
        $data['discountGroups'] = DiscountGroup::where('status', 1)->orderBy('name', 'asc')->get();

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        $query = Customer::with('discountGroup')->orderBy('id', 'asc');

        if ($userBranchId == 1) {
            // Super admin
            if ($filterBranchId) {
                $query->where(function ($q) use ($filterBranchId) {
                    $q->where('branch_id', $filterBranchId)
                        ->orWhereNull('branch_id');
                });
            } else {
                $data['customers'] = Customer::with('discountGroup')->orderBy('id', 'asc')->paginate(20);
            }
        } else {
            // Regular branch user
            $query->where(function ($q) use ($userBranchId) {
                $q->where('branch_id', $userBranchId)
                    ->orWhereNull('branch_id');
            });
        }

        if ($request->customer_id != null) {
            $query->where('id', $request->customer_id);
        }

        if ($request->customer_type != null) {
            $query->where('customer_type', $request->customer_type);
        }

        $barcode = $request->barcode ?? $request->phone_no;
        $data['barcode'] = $barcode;
        if (!empty($barcode)) {
            $barcodeVal = trim($barcode);
            $query->where(function($q) use ($barcodeVal) {
                $q->where('phone', 'like', "%{$barcodeVal}%")
                  ->orWhere('name', 'like', "%{$barcodeVal}%")
                  ->orWhere('id', $barcodeVal);
            });
        }

        $data['customers'] = $query->paginate(20)->appends($request->all());

        return view('backend.pages.customer.index', $data);
    }

    public function store(Request $request)
    {
        if (auth()->user()->branch_id == 1) {
            $uniBranch = $request->branch_id;
        } else {
            $uniBranch = auth()->user()->branch_id;
        }

        $phoneOnlyAllowed = (env('APP_CUSTOMER_PHONE_ONLY') == 'yes' && env('APP_ONLINE') != 'yes');

        if ($phoneOnlyAllowed) {
            $nameRule = 'nullable|string|max:255';
            $addressRule = 'nullable|string';
        } else {
            $nameRule = 'required|string|max:255';
            $addressRule = 'required|string|min:10';
        }

        $request->validate([
            'name' => $nameRule,
            'phone' => [
                'required',
                'string',
                'min:11',
                Rule::unique('customers')->where(function ($query) use ($uniBranch) {
                    return $query->where('branch_id', $uniBranch);
                }),
            ],
            'address' => $addressRule,
        ], [
            'name.required' => __('Customer name is required.'),
            'phone.required' => __('Customer phone number is required.'),
            'phone.min' => __('Please enter a valid 11-digit mobile number.'),
            'address.required' => __('Full delivery address is required for courier order and customer record.'),
            'address.min' => __('Pathao and Steadfast courier require a detailed delivery address (minimum 10 characters long e.g. House, Road, Area, District).'),
        ]);

        $customer = new Customer();
        $customer->date = date('Y-m-d');
        if (auth()->user()->branch_id == 1) {
            $customer->branch_id = $request->branch_id;
        } else {
            $customer->branch_id = auth()->user()->branch_id;
        }
        $customer->name = $request->filled('name') ? $request->name : ('Customer ' . $request->phone);
        $customer->email = $request->email;
        $customer->member_id = rand(100000000, 999999999);
        $customer->phone = $request->phone;
        $customer->address = $request->address;
        $customer->birth_date = $request->birth_date;
        $customer->anni_date = $request->anni_date;
        $customer->due_amount = $request->due_amount;
        $customer->vehicle_name = is_array($request->vehicle_name) ? ($request->vehicle_name[0] ?? null) : $request->vehicle_name;
        $customer->reg_no = is_array($request->reg_no) ? ($request->reg_no[0] ?? null) : $request->reg_no;
        $customer->model = is_array($request->model) ? ($request->model[0] ?? null) : $request->model;
        $customer->made_in = is_array($request->made_in) ? ($request->made_in[0] ?? null) : $request->made_in;
        $customer->engine_no = is_array($request->engine_no) ? ($request->engine_no[0] ?? null) : $request->engine_no;
        $customer->chassis_no = is_array($request->chassis_no) ? ($request->chassis_no[0] ?? null) : $request->chassis_no;
        $customer->milage = is_array($request->milage) ? ($request->milage[0] ?? null) : $request->milage;
        $customer->driver_name = is_array($request->driver_name) ? ($request->driver_name[0] ?? null) : $request->driver_name;
        $customer->driver_phone = is_array($request->driver_phone) ? ($request->driver_phone[0] ?? null) : $request->driver_phone;
        $customer->discount_group_id = $request->discount_group_id;
        $customer->due_amount = $request->due_amount;

        DB::transaction(function () use ($request, $customer) {
            if ($customer->save()) {
                if ($request->has('vehicle_name')) {
                    if (is_array($request->vehicle_name)) {
                        foreach ($request->vehicle_name as $index => $name) {
                            if (!empty($name) || !empty($request->reg_no[$index])) {
                                \App\Models\Vehicle::create([
                                    'customer_id' => $customer->id,
                                    'vehicle_name' => $name,
                                    'reg_no' => $request->reg_no[$index] ?? null,
                                    'model' => $request->model[$index] ?? null,
                                    'made_in' => $request->made_in[$index] ?? null,
                                    'engine_no' => $request->engine_no[$index] ?? null,
                                    'chassis_no' => $request->chassis_no[$index] ?? null,
                                    'milage' => $request->milage[$index] ?? null,
                                    'driver_name' => $request->driver_name[$index] ?? null,
                                    'driver_phone' => $request->driver_phone[$index] ?? null,
                                ]);
                            }
                        }
                    } else {
                        if (!empty($request->vehicle_name) || !empty($request->reg_no)) {
                            \App\Models\Vehicle::create([
                                'customer_id' => $customer->id,
                                'vehicle_name' => $request->vehicle_name,
                                'reg_no' => $request->reg_no,
                                'model' => $request->model,
                                'made_in' => $request->made_in,
                                'engine_no' => $request->engine_no,
                                'chassis_no' => $request->chassis_no,
                                'milage' => $request->milage,
                                'driver_name' => $request->driver_name,
                                'driver_phone' => $request->driver_phone,
                            ]);
                        }
                    }
                }
                if ($request->due_amount != NULL) {
                    $transaction = new Transaction();
                    if (auth()->user()->branch_id == 1) {
                        $transaction->branch_id = $request->branch_id;
                    } else {
                        $transaction->branch_id = auth()->user()->branch_id;
                    }
                    $transaction->transaction_type = 'Opening Due Amount';
                    $transaction->date = Carbon::now()->format('Y-m-d');
                    $transaction->customer_id = $customer->id;
                    $transaction->debit = $request->due_amount;
                    $transaction->credit = NULL;
                    $transaction->created_by = auth()->user()->id;
                    $transaction->save();
                }
            }
        });


        // 👉 Check if it's an Ajax request
        if ($request->ajax()) {
            $customer->load('discountGroup');
            return response()->json([
                'success'  => true,
                'customer' => $customer
            ]);
        }

        session()->flash('success', __('Customer created successfully'));
        return back();
    }

    public function update(Request $request, string $id)
    {
        $phoneOnlyAllowed = (env('APP_CUSTOMER_PHONE_ONLY') == 'yes' && env('APP_ONLINE') != 'yes');

        if ($phoneOnlyAllowed) {
            $nameRule = 'nullable|string|max:255';
        } else {
            $nameRule = 'required|string|max:255';
        }

        $request->validate([
            'name' => $nameRule,
            'phone' => 'required|string|min:11',
            'address' => 'nullable|string',
        ], [
            'name.required' => __('Customer name is required.'),
            'phone.required' => __('Customer phone number is required.'),
            'phone.min' => __('Please enter a valid 11-digit mobile number.'),
        ]);

        $customer = Customer::findOrFail($id);

        $branchId = $customer->branch_id;
        if ($request->filled('branch_id')) {
            $branchId = $request->branch_id;
        }

        $customer->update([
            'name' => $request->filled('name') ? $request->name : ($customer->name ?: ('Customer ' . $request->phone)),
            'branch_id' => $branchId,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'birth_date' => $request->birth_date,
            'anni_date' => $request->anni_date,
            'customer_type' => $request->customer_type,
            'discount_group_id' => $request->discount_group_id,
            'due_amount' => $request->due_amount,
            'vehicle_name' => is_array($request->vehicle_name) ? ($request->vehicle_name[0] ?? null) : $request->vehicle_name,
            'reg_no' => is_array($request->reg_no) ? ($request->reg_no[0] ?? null) : $request->reg_no,
            'model' => is_array($request->model) ? ($request->model[0] ?? null) : $request->model,
            'made_in' => is_array($request->made_in) ? ($request->made_in[0] ?? null) : $request->made_in,
            'engine_no' => is_array($request->engine_no) ? ($request->engine_no[0] ?? null) : $request->engine_no,
            'chassis_no' => is_array($request->chassis_no) ? ($request->chassis_no[0] ?? null) : $request->chassis_no,
            'milage' => is_array($request->milage) ? ($request->milage[0] ?? null) : $request->milage,
            'driver_name' => is_array($request->driver_name) ? ($request->driver_name[0] ?? null) : $request->driver_name,
            'driver_phone' => is_array($request->driver_phone) ? ($request->driver_phone[0] ?? null) : $request->driver_phone,
        ]);

        if ($request->has('due_amount')) {
            $transaction = Transaction::where('customer_id', $customer->id)
                ->where('transaction_type', 'Opening Due Amount')
                ->first();
            if ($transaction) {
                if ($request->due_amount != 0 && $request->due_amount != null) {
                    $transaction->update([
                        'debit' => $request->due_amount,
                        'branch_id' => $request->branch_id ?? $transaction->branch_id
                    ]);
                } else {
                    $transaction->delete();
                }
            } else {
                if ($request->due_amount != 0 && $request->due_amount != null) {
                    $transaction = new Transaction();
                    $transaction->branch_id = $request->branch_id ?? auth()->user()->branch_id;
                    $transaction->transaction_type = 'Opening Due Amount';
                    $transaction->date = Carbon::now()->format('Y-m-d');
                    $transaction->customer_id = $customer->id;
                    $transaction->debit = $request->due_amount;
                    $transaction->credit = NULL;
                    $transaction->created_by = auth()->user()->id;
                    $transaction->save();
                }
            }
        }

        $customer->vehicles()->delete();
        if ($request->has('vehicle_name')) {
            if (is_array($request->vehicle_name)) {
                foreach ($request->vehicle_name as $index => $name) {
                    if (!empty($name) || !empty($request->reg_no[$index])) {
                        \App\Models\Vehicle::create([
                            'customer_id' => $customer->id,
                            'vehicle_name' => $name,
                            'reg_no' => $request->reg_no[$index] ?? null,
                            'model' => $request->model[$index] ?? null,
                            'made_in' => $request->made_in[$index] ?? null,
                            'engine_no' => $request->engine_no[$index] ?? null,
                            'chassis_no' => $request->chassis_no[$index] ?? null,
                            'milage' => $request->milage[$index] ?? null,
                            'driver_name' => $request->driver_name[$index] ?? null,
                            'driver_phone' => $request->driver_phone[$index] ?? null,
                        ]);
                    }
                }
            } else {
                if (!empty($request->vehicle_name) || !empty($request->reg_no)) {
                    \App\Models\Vehicle::create([
                        'customer_id' => $customer->id,
                        'vehicle_name' => $request->vehicle_name,
                        'reg_no' => $request->reg_no,
                        'model' => $request->model,
                        'made_in' => $request->made_in,
                        'engine_no' => $request->engine_no,
                        'chassis_no' => $request->chassis_no,
                        'milage' => $request->milage,
                        'driver_name' => $request->driver_name,
                        'driver_phone' => $request->driver_phone,
                    ]);
                }
            }
        }

        session()->flash('success', __('Customer updated successfully'));
        return back();
    }

    public function getCustomersByBranch(Request $request)
    {
        $customers = Customer::where('branch_id', $request->branch_id)->get();
        return response()->json($customers);
    }

    public function quickHistory($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer not found']);
        }

        // Last 5 invoices
        $recentInvoices = Invoice::with('invoiceItems.product')
            ->where('customer_id', $id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($inv) {
                return [
                    'invoice_no'   => $inv->invoice_no,
                    'date'         => $inv->date,
                    'grand_total'  => $inv->total_amount,
                    'due_amount'   => $inv->total_due,
                    'items_count'  => $inv->invoiceItems->count(),
                    'items'        => $inv->invoiceItems->take(3)->map(fn($i) => $i->product?->name)->filter()->values(),
                ];
            });

        // Total due
        $totalDue = Invoice::where('customer_id', $id)->sum('total_due');

        // Total purchases
        $totalPurchases = Invoice::where('customer_id', $id)->count();
        $totalSpent     = Invoice::where('customer_id', $id)->sum('total_amount');

        // Active warranty claims
        $activeClaims = \App\Models\WarrantyClaim::where('customer_id', $id)
            ->where('status', '!=', 'Delivered')
            ->count();

        // Courier Fraud Check
        $fraudChecker = app(\App\Services\CourierFraudCheckerService::class);
        $fraudInfo = $fraudChecker->checkFraud($id);

        return response()->json([
            'success' => true,
            'customer' => [
                'name'    => $customer->name,
                'phone'   => $customer->phone,
                'email'   => $customer->email,
                'address' => $customer->address,
                'points'  => $customer->total_point ?? 0,
            ],
            'stats' => [
                'total_purchases' => $totalPurchases,
                'total_spent'     => number_format($totalSpent, 2),
                'total_due'       => number_format($totalDue, 2),
                'active_claims'   => $activeClaims,
            ],
            'fraud_check'     => $fraudInfo,
            'recent_invoices' => $recentInvoices,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::find($id);
        $transactions = Transaction::where('customer_id', $id)->get();
        foreach ($transactions as $transaction) {
            $transaction->delete();
        }
        $customer->delete();
        session()->flash('success', __('Customer deleted successfully'));
        return back();
    }

    public function getPreviousDue(Request $request)
    {
        $customerId = $request->customer_id;
        if (!$customerId || $customerId == 1) {
            return response()->json(['total_due' => 0, 'due' => 0]);
        }

        $totalDue = (float) Invoice::where('customer_id', $customerId)->sum('total_due');
        return response()->json(['total_due' => $totalDue, 'due' => $totalDue]);
    }

    public function upcomingWishlist(Request $request)
    {
        $filterType = $request->get('type', 'all');

        // Handle Date Range Filter
        $today = Carbon::now()->startOfDay();

        if ($request->filled('start_date')) {
            try {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
            } catch (\Exception $e) {
                $startDate = $today->copy();
            }
        } else {
            $startDate = $today->copy();
        }

        if ($request->filled('end_date')) {
            try {
                $endDate = Carbon::parse($request->end_date)->startOfDay();
            } catch (\Exception $e) {
                $endDate = $startDate->copy()->addDays(3);
            }
        } else {
            $endDate = $startDate->copy()->addDays(3);
        }

        // Ensure end_date is not before start_date
        if ($endDate->lt($startDate)) {
            $endDate = $startDate->copy();
        }

        // Build array of dates in the selected range
        $days = [];
        $curr = $startDate->copy();
        $maxDays = 366; // Safety limit
        $i = 0;

        while ($curr->lte($endDate) && $i < $maxDays) {
            $diffInDays = (int) $today->diffInDays($curr, false);

            $dayLabel = '';
            if ($diffInDays === 0) {
                $dayLabel = __('Today');
            } elseif ($diffInDays === 1) {
                $dayLabel = __('Tomorrow');
            } elseif ($diffInDays > 1) {
                $dayLabel = __('In ') . $diffInDays . __(' days');
            } elseif ($diffInDays === -1) {
                $dayLabel = __('Yesterday');
            } else {
                $dayLabel = abs($diffInDays) . __(' days ago');
            }

            $md = $curr->format('m-d');

            if (!isset($days[$md])) {
                $days[$md] = [];
            }

            $days[$md][] = [
                'date_str' => $curr->format('d M Y'),
                'day_label' => $dayLabel,
                'carbon' => $curr->copy(),
            ];

            $curr->addDay();
            $i++;
        }

        $userBranchId = auth()->user()->branch_id;
        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

        $query = Customer::query();

        if ($userBranchId == 1) {
            if ($filterBranchId) {
                $query->where(function ($q) use ($filterBranchId) {
                    $q->where('branch_id', $filterBranchId)->orWhereNull('branch_id');
                });
            }
        } else {
            $query->where(function ($q) use ($userBranchId) {
                $q->where('branch_id', $userBranchId)->orWhereNull('branch_id');
            });
        }

        $query->where(function ($q) {
            $q->whereNotNull('birth_date')->where('birth_date', '!=', '')
                ->orWhere(function ($q2) {
                    $q2->whereNotNull('anni_date')->where('anni_date', '!=', '');
                });
        });

        $allCustomers = $query->get();

        $upcomingList = collect();

        foreach ($allCustomers as $customer) {
            $events = [];

            if (!empty($customer->birth_date)) {
                $bDate = null;
                try {
                    $bDate = Carbon::parse($customer->birth_date);
                } catch (\Exception $e) {
                }

                if ($bDate) {
                    $md = $bDate->format('m-d');
                    if (isset($days[$md]) && ($filterType == 'all' || $filterType == 'birthday')) {
                        foreach ($days[$md] as $dayInfo) {
                            $events[] = [
                                'type' => 'Birthday',
                                'label' => __('Birthday') . ' (' . $dayInfo['day_label'] . ' - ' . $dayInfo['date_str'] . ')',
                                'date_info' => $dayInfo,
                            ];
                        }
                    }
                }
            }

            if (!empty($customer->anni_date)) {
                $aDate = null;
                try {
                    $aDate = Carbon::parse($customer->anni_date);
                } catch (\Exception $e) {
                }

                if ($aDate) {
                    $md = $aDate->format('m-d');
                    if (isset($days[$md]) && ($filterType == 'all' || $filterType == 'anniversary')) {
                        foreach ($days[$md] as $dayInfo) {
                            $events[] = [
                                'type' => 'Anniversary',
                                'label' => __('Anniversary') . ' (' . $dayInfo['day_label'] . ' - ' . $dayInfo['date_str'] . ')',
                                'date_info' => $dayInfo,
                            ];
                        }
                    }
                }
            }

            if (!empty($events)) {
                $customer->upcoming_events = $events;
                $upcomingList->push($customer);
            }
        }

        $totalDays = $startDate->diffInDays($endDate) + 1;

        $data['customers'] = $upcomingList;
        $data['filterType'] = $filterType;
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        $data['totalDays'] = $totalDays;

        return view('backend.pages.customer.upcoming-wishlist', $data);
    }
}
