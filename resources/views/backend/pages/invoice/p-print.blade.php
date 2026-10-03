@extends('backend.layouts.master')
@section('page-title', 'Invoice Print')
@push('css')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&display=swap');
        
        #print-area, #print-area * {
            font-family: 'Courier Prime', Courier, monospace !important;
        }
        
        #print-area {
            font-size: 14px;
            max-width: 80mm;
            margin: 0 auto;
            padding: 5px 15px; /* Added left/right padding */
            box-sizing: border-box;
        }

        .signature {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature p {
            margin-top: -10px;
        }

        .order-details th {
            font-weight: bold;
        }

        strong {
            font-weight: 800;
        }

        address {
            margin-bottom: 0px;
        }

        .invoice-header {
            width: 100%;
            display: block;
            box-sizing: border-box;
            overflow: hidden;
            /*border-bottom: 1px dashed rgb(8, 8, 8);*/
            margin-bottom: 10px;
        }

        .invoice-header address {
            width: 100%;
            text-align: center;
            padding: 5px;
        }

        .logo-area img {
            width: 40%;
            display: inline;
            /*float: left;*/
        }

        .logo-area h1 {
            display: inline;
            float: left;
            font-size: 17px;
            padding-left: 8px;
        }

        .logo-area h4 {
            font-weight: bold;
            font-size: 26px;
        }

        .invoice-header .logo-area {
            width: 100%;
            text-align: center;
            /*padding: 5px;*/
        }

        .bill-date {
            width: 100%;
            overflow: hidden;
            padding: 0 15px;
        }

        .date {
            width: 50%;
            float: right;
            text-align: end;
        }

        .bill-no {
            width: 50%;
            float: left;
        }

        .name,
        .address,
        .saler,
        .time,
        .mobile-no,
        .cus_info {
            width: 100%;
            /* border-left: 1px solid #ccc; */
            /*border-bottom: 1px solid #ccc; */
            /* border-right: 1px solid #ccc; */
            padding: 0 15px;
        }

        .name span,
        .address span,
        .mobile-no span,
        .cus_info span,
        .saler span,
        .time span {
            padding-left: 5px;

        }

        .sign {
            width: 250px;
            border-top: 1px solid #000;
            float: right;
            margin: 40px 20px 0 0;
            text-align: center;
        }

        .sales_border {
            border-bottom: 1px dashed #000;
            ;
        }

        .table-bordered {
            border-top: 1px dashed #000;
            /* border-bottom: 1px dashed #000; */
            border-left: 0px;
            border-right: 0px;
            /*border: 0px;*/
        }

        .border_th {
            border-top: 1px dashed #000;
            /* border-bottom: 1px dashed #000; */
            border-left: 0px;
            border-right: 0px;
            /*border:0px;*/
        }

        .border_item {
            border-top: 1px dashed #000;
            /*border-bottom: 1px dashed #000;*/
            /* border-left: 0px; */
            /* border-right: 0px; */
            /*border:0px;*/
        }

        .border_item td {
            padding: 0px !important;
            margin: 0px !important;
        }

        .border_group tr:last-child {
            border-bottom: 1px solid #000;
            /* শুধু শেষ tr এ border */
        }


        .border_disco {
            /*border-top: 1px dashed #000;*/
            /* border-bottom: 1px dashed #000; */
            border-left: 0px;
            border-right: 0px;
            /*border:0px;*/
        }

        .credit_border {
            border-top: 1px dashed #000;
            border-left: 0px;
            border-right: 0px;
            /*border:0px;*/
        }

        .table-bordered td,
        .table-bordered th {
            /*border-top: 1px dashed #000;*/
            /*border-bottom: 1px dashed #000;*/
            /*border-left: 0px;*/
            /*border-right: 0px;*/
            border: 0px;
        }

        .table tbody th {
            /*border-top: 1px dashed #000;*/
            /*border-bottom: 1px dashed #000;*/
            /*border-left: 0px;*/
            /*border-right: 0px;*/
            border: 0px;
        }

        @media print {
            @page {
                margin: 0;
                size: 80mm auto;
            }
            body {
                width: 78mm !important;
                margin: 0 auto;
                padding: 0 4mm !important; /* Adding left/right space */
                box-sizing: border-box !important;
            }
            body * {
                visibility: visible;
                color: #000 !important;
                font-family: 'Courier Prime', Courier, monospace !important;
                font-size: 14px !important;
                line-height: 1.2 !important;
                font-weight: bold !important;
            }

            .table-rheader td {
                border-top: 0px;
                padding: 5px !important;
                /*vertical-align: baseline !important;*/
            }

            h2 {
                font-size: 20px !important;
            }

            .table-plist td {
                padding: 5px !important;
                text-align: left !important;
                width: 250px !important;
            }

            .table-plist th {
                padding: 5px;
                text-align: left !important;
                width: 250px !important;
            }

            .border-bottom {
                /* border-bottom: 1px dotted #CCC; */
            }

            .print {
                margin: 0;
            }

            .customers,
            .authorized {
                line-height: 2;
                margin-top: 15px;
            }

            .table-bordered {
                /*border-top: 1px dashed #000 !important;*/
                /*border-bottom: 1px dashed #000 !important;*/
                /*border-left: 0px !important;*/
                /*border-right: 0px !important;*/
            }

            .table-bordered td,
            .table-bordered th {
                /*border-top: 1px dashed #000 !important;*/
                /*border-bottom: 1px dashed #000 !important;*/
                /*border-left: 0px !important;*/
                /*border-right: 0px !important;*/
            }

            .table tbody th {
                /*border-top: 1px dashed #000 !important;*/
                /*border-bottom: 1px dashed #000 !important;*/
                /*border-left: 0px !important;*/
                /*border-right: 0px !important;*/
            }

            .lead {
                margin-top: -43px !important;
                line-height: 2;
            }

        }

        .bill-no,
        .date,
        .saler,
        .time,
        .name,
        .mobile-no,
        .address,
        th,
        td,
        address,
        h4 {
            color: black;
        }

        .saler {
            float: left;
            width: 50%;
        }

        .time {
            float: right;
            text-align: end;
            width: 50%;
        }

        .invoice-contentbar {
            margin: 60px 0px 0 0px;
            padding: 20px;
            margin-bottom: 60px;
            font-family: 'Petrona', serif;
        }

        .table-bordered td.rm-b-t {
            /*border-top: 1px solid transparent !important;*/
        }

        .table-bordered td.rm-b-b {
            /*border-bottom: 1px solid transparent !important;*/
        }

        .table-bordered td.rm-b-l {
            border-left: 1px solid transparent !important;
        }

        .table-bordered td.rm-b-r {
            border-right: 1px solid transparent !important;
        }
    </style>
    <style>
        .table-rheader td {
            border-top: 0px;
            padding: 5px;
            /*vertical-align: baseline !important;*/
        }

        .table-plist td {
            padding: 5px;
            /*text-align: center !important;*/
        }

        .table-plist th {
            padding: 5px;
            text-align: center;
            /* background: #ddd; */
        }

        .border-bottom {
            /*border-bottom: 1px dotted #CCC;*/
        }

        .tfoot_style tr td {
            padding: 0px !important;
            margin: 4px !important;
        }

        .name_des {
            padding: 0px !important;
            margin: 0px !important;
        }

        .name_des p {
            margin: 0px !important;
            padding: 0px !important;
            text-align: left;
            font-size: 12px;
            line-height: normal;
        }
    </style>
@endpush
@section('invoice')
    <link href="https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&display=swap" rel="stylesheet">
    <div class="invoice-contentbar">
        <div class="row">
            <div class="col-md-12">
                <div class="row justify-content-center">
                    <div class="col-md-7 card card-body print">
                        <div id="print-area">
                            <div class="invoice-header mt-0">
                                {{-- @php
                                    $branch = App\Models\Branch::where('id', $invoice->branch_id)->first();
                                    // dd($branch);
                                @endphp --}}
                                {{-- <div class="logo-area">
                                    @if (get_setting('inv_logo') == 'logo')
                                        <img src="{{ !empty(get_setting('system_logo')) ? url('uploads/logo/' . get_setting('system_logo')) : url('backend/images/fastLogo.jpeg') }}"
                                            style="width: 150px; height: 50px;" alt="logo">
                                    @elseif (get_setting('inv_logo') == 'name')
                                        <h2 style="font-weight: bold; margin-bottom:0;">
                                            {{ empty(get_setting('com_name')) ? 'Fast IT' : get_setting('com_name') }}</h2>
                                    @else
                                        <img src="{{ !empty(get_setting('system_logo')) ? url('uploads/logo/' . get_setting('system_logo')) : url('backend/images/fastLogo.jpeg') }}"
                                            style="width: 150px; height: 50px;" alt="logo">
                                        <h2 style="font-weight: bold; margin-bottom:0">
                                            {{ empty(get_setting('com_name')) ? 'Fast IT' : get_setting('com_name') }}</h2>
                                    @endif
                                </div>
                                <address>
                                    <strong>{{ empty(get_setting('com_address')) ? 'Suite: 807,Shah Ali Plaza, Mirpur-10, Dhaka-1216.' : get_setting('com_address') }}</strong>
                                    <br> Mobile:
                                    <strong> +88
                                        {{ empty(get_setting('com_phone')) ? '01784-159071' : get_setting('com_phone') }}</strong>
                                    <br> Email :
                                    <strong>{{ empty(get_setting('com_email')) ? 'fastitbd00@gmail.com' : get_setting('com_email') }}</strong>
                                    
                                </address> --}}

                                @if (get_setting('inv_details') == 'branch')
                                    @php
                                        $branch = App\Models\Branch::where('id', $invoice->branch_id)->first();
                                        $sysLogo = ($branch && !empty($branch->logo)) ? asset('uploads/logo/' . $branch->logo) : (!empty(get_setting('system_logo')) ? url('uploads/logo/' . get_setting('system_logo')) : null);
                                        $shopName = $branch ? $branch->shop_name : 'CAR SPARES';
                                        $shopAddress = $branch ? $branch->address : '';
                                        $shopPhone = $branch ? $branch->phone : '';
                                    @endphp
                                @else
                                    @php
                                        $sysLogo = !empty(get_setting('system_logo')) ? url('uploads/logo/' . get_setting('system_logo')) : null;
                                        $shopName = empty(get_setting('com_name')) ? 'CAR SPARES' : get_setting('com_name');
                                        $shopAddress = empty(get_setting('com_address')) ? '111JAN SMUTS AVENUE GREENFIELDS, EAST LONDON' : get_setting('com_address');
                                        $shopPhone = empty(get_setting('com_phone')) ? '0609735953' : get_setting('com_phone');
                                    @endphp
                                @endif
                                <div style="text-align: center; margin-bottom: 15px; line-height: 1.4;">
                                    @if (get_setting('inv_logo') == 'logo')
                                        @if ($sysLogo)
                                            <img src="{{ $sysLogo }}" style="max-height: 65px; margin: 0 auto 5px auto; display: block;" alt="logo">
                                        @endif
                                    @elseif (get_setting('inv_logo') == 'name')
                                        <h2 style="font-weight: 900; font-size: 28px; margin: 0; padding: 0; letter-spacing: 1px;">{{ strtoupper($shopName) }}</h2>
                                    @else
                                        @if ($sysLogo)
                                            <img src="{{ $sysLogo }}" style="max-height: 65px; margin: 0 auto 5px auto; display: block;" alt="logo">
                                        @endif
                                        <h2 style="font-weight: 900; font-size: 28px; margin: 0; padding: 0; letter-spacing: 1px;">{{ strtoupper($shopName) }}</h2>
                                    @endif
                                    <div style="font-size: 1em; font-weight: 500;">{!! nl2br(e($shopAddress)) !!}</div>
                                    <div style="font-size: 1em; font-weight: 500;">CONTACT: {{ $shopPhone }}</div>
                                </div>
                            </div>
                            
                            <div class="text-left" style="padding: 0; font-size: 1em; line-height: 1.5; margin-bottom: 10px; font-weight: 500;">
                                <div>RECEIPT NO.: {{ $invoice->invoice_no }} @if($invoice->is_edited == 1 || $invoice->edit_status == 'edited') (Edited) @elseif($invoice->is_edited == 2 || $invoice->edit_status == 'exchange') (Exchange) @endif</div> 
                                <div>{{ date('Y/m/d H:i:s', strtotime($invoice->created_at)) }}</div>
                                <div>USER: {{ strtoupper($invoice->user->name) }}</div>
                                <div>CUSTOMER: 
                                    @if($invoice->customer && stripos($invoice->customer->name, 'walking') === false)
                                        {{ strtoupper($invoice->customer->name) }} {{ $invoice->customer->phone ? '| ' . $invoice->customer->phone : '' }}
                                    @else
                                        WALKING
                                    @endif
                                </div>
                            </div>
                            
                            <div style="border-bottom: 1px dashed #000; margin-bottom: 5px; width: 100%;"></div>
                            <table style="width: 100%; font-size: 12px; font-weight: 500; table-layout: fixed; word-wrap: break-word;" class="mb-2">
                                <thead>
                                    <tr>
                                        <th style="text-align: left; padding: 2px 0; width: 40%;">Product</th>
                                        <th style="text-align: left; padding: 2px 0; width: 12%;">Qty</th>
                                        <th style="text-align: left; padding: 2px 0; width: 16%;">Rate</th>
                                        <th style="text-align: left; padding: 2px 0; width: 12%;">Dis.</th>
                                        <th style="text-align: right; padding: 2px 0; width: 20%;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="5" style="border-bottom: 1px dashed #000; padding: 0;"></td>
                                    </tr>
                                @php
                                    $printItems = $invoice->invoiceItems->groupBy(function($it) {
                                        return $it->product_id . '_' . ($it->product_variation_id ?? 0);
                                    });
                                    $totalItems = $printItems->count();
                                    $totalQty = 0;
                                @endphp
                                @foreach ($printItems as $key => $group)
                                    @php
                                        $item = $group->first();
                                        $comb_main = $group->sum('actual_main');
                                        $comb_sub = $group->sum('actual_sub');
                                        $comb_subtotal = $group->sum('subtotal');
                                        $product = App\Models\Product::where('id', $item->product_id)->with('unit.related_unit')->first();
                                        if ($product && $product->is_service == 0) {
                                            if ($product->unit?->related_unit == null) {
                                                $qty = $comb_main;
                                            } else {
                                                $related_value = $product->unit->related_value ?: 1;
                                                $qty = $comb_main + $comb_sub / $related_value;
                                            }
                                        } else {
                                            $qty = $comb_main;
                                        }
                                        $totalQty += $qty;
                                        
                                        $productName = $item->product?->name;
                                        if (env('APP_SC') == 'yes' && $item->product_variation_id != null) {
                                            $productName .= ' (' . $item->product_variation?->size?->size . '-' . $item->product_variation?->color?->color . ')';
                                        }
                                        if ($item->is_return == 1) {
                                            $productName .= ' (Return)';
                                        }
                                    @endphp
                                    <tr>
                                        <td style="text-align: left; padding: 2px 5px 2px 0; vertical-align: top;">{{ $productName }}</td>
                                        <td style="text-align: left; padding: 2px 0; vertical-align: top;">{{ (float) $qty }}</td>
                                        <td style="text-align: left; padding: 2px 0; vertical-align: top;">{{ (float) $item->rate }}</td>
                                        <td style="text-align: left; padding: 2px 0; vertical-align: top;">{{ $item->product_discount ? (str_contains($item->product_discount, '%') ? $item->product_discount : (float) $item->product_discount) : '-' }}</td>
                                        <td style="text-align: right; padding: 2px 0; vertical-align: top;">{{ (float) $comb_subtotal }}</td>
                                    </tr>
                                @endforeach
                                    <tr>
                                        <td colspan="5" style="border-bottom: 1px dashed #000; padding: 0;"></td>
                                    </tr>

                                </tbody>
                            </table>
                            <div style="font-size: 1em; font-weight: bold; margin-bottom: 10px; margin-top: 5px;">ITEMS COUNT: {{ $totalItems }}</div>
                            @php
                                $cash = App\Models\BankTransaction::where('invoice_id', $invoice->id)->get();
                                $cash_amount = App\Models\BankTransaction::where('invoice_id', $invoice->id)
                                    ->where('bank_id', 1)
                                    ->sum('amount');
                                $return_amount_cash = (float) $cash_amount - $invoice->return_amount;
                            @endphp
                            <table class="table table-bordereds table-plists order-detailss">
                                <tfoot class="tfoot_style">
                                    <tr class="border_item">
                                        <td colspan="5" class="text-left">Sub Total : </td>
                                        <td colspan="1" class="text-right">
                                            {{ $invoice->estimated_amount }}
                                        </td>
                                    </tr>

                                    <tr class="border_disco">
                                        <td colspan="5" class="text-left">Discount 
                                            @if (str_contains($invoice->discount, '%'))
                                                ({{ $invoice->discount }})
                                            @endif
                                            : 
                                        </td>
                                        <td colspan="1" class="text-right">
                                            {{ $invoice->discount_amount }}
                                        </td>
                                    </tr>
                                    @if ($invoice->vat_amount > 0)
                                    <tr class="border_disco">
                                        <td colspan="5" class="text-left">Vat{{ (str_contains($invoice->vat, '%') && $invoice->vat) ? '(' . $invoice->vat . ')' : '' }} : 
                                        </td>
                                        <td colspan="1" class="text-right">
                                            {{ $invoice->vat_amount }}
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td colspan="5" class="text-left">Total Amount : </td>
                                        <td colspan="1" class="text-right">
                                            {{ $invoice->total_amount }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-left">Paid : </td>
                                        <td colspan="1" class="text-right">
                                            {{ $invoice->total_paid }}
                                        </td>
                                    </tr>
                                    @if ($invoice->return_amount != 0)
                                        <tr>
                                            <td colspan="5" class="text-left">Return Amount : </td>
                                            <td colspan="1" class="text-right">
                                                {{ $invoice->return_amount }}
                                            </td>
                                        </tr>
                                    @endif
                                    @php
                                          $displayPreviousDue = (float) $invoice->previous_due;
                                          if ($displayPreviousDue <= 0 && $invoice->customer_id && $invoice->customer_id != 1) {
                                              $priorInvDue = (float) \App\Models\Invoice::where('customer_id', $invoice->customer_id)
                                                  ->where('id', '<', $invoice->id)
                                                  ->sum('total_due');
                                              $custObj = \App\Models\Customer::find($invoice->customer_id);
                                              $openBal = $custObj ? open_balance_customer($invoice->customer_id, $custObj->due_amount) : 0;
                                              $displayPreviousDue = $priorInvDue + $openBal;
                                          }
                                      @endphp
                                     <tr>
                                         <td colspan="5" class="text-left">Current Due : </td>
                                         <td colspan="1" class="text-right">
                                             {{ number_format($invoice->total_due, 2) }}
                                         </td>
                                     </tr>
                                     @if($displayPreviousDue > 0)
                                     <tr>
                                         <td colspan="5" class="text-left">Previous Due : </td>
                                         <td colspan="1" class="text-right">
                                             {{ number_format($displayPreviousDue, 2) }}
                                         </td>
                                     </tr>
                                     <tr>
                                         <td colspan="5" class="text-left" style="font-weight: bold;">Total Due : </td>
                                         <td colspan="1" class="text-right" style="font-weight: bold;">
                                             {{ number_format($invoice->total_due + $displayPreviousDue, 2) }}
                                         </td>
                                     </tr>
                                     @endif


                                    <tr>
                                        <td colspan="5" class="text-left">

                                            {{-- <ul style="list-style: none">
                                                <li> --}}
                                            @foreach ($cash as $bank)
                                                {{ $bank->bank_account?->bank_name ?? 'Cash' }} ({{ $bank->date ?: date('Y-m-d', strtotime($invoice->created_at)) }}) : <br>
                                            @endforeach
                                        </td>
                                        <td colspan="1" class="text-right">
                                            @if ($invoice->return_amount != 0)
                                                {{ number_format($return_amount_cash, 2) }}
                                            @else
                                                @foreach ($cash as $bank)
                                                    {{ $bank->trans_type == 'withdraw' ? '-' : '' }}{{ number_format($bank->amount, 2) }} <br>
                                                @endforeach
                                            @endif
                                            {{-- </li>
                                            </ul> --}}
                                        </td>
                                    </tr>
                                    @if (env('APP_LOYALTY') == 'yes' && $invoice->pay_point != 0)
                                        <tr>
                                            <td colspan="5" class="text-left">Pay Point : </td>
                                            <td colspan="1" class="text-right">
                                                {{ number_format($invoice->pay_point, 2) }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="5" class="text-left">Change Amount: </td>
                                        <td colspan="1" class="text-right">
                                            @if ($invoice->change_amount != null)
                                                {{ $invoice->change_amount }}
                                            @else
                                                0.00
                                            @endif
                                        </td>
                                    </tr>
                                    @if ($invoice->total_due > 0)
                                        <tr>
                                            <td colspan="5" class="text-left">Total Due : </td>
                                            <td colspan="1" class="text-right">

                                                {{ $invoice->total_due }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="5">Mode of Payment ( @foreach ($cash as $bank)
                                                {{ $bank->bank_account?->bank_name ?? 'Cash' }}{{ !$loop->last ? ' , ' : '' }}
                                            @endforeach ) </td>
                                    </tr>
                                    <tr class="credit_border">
                                      
                                    </tr>
                                </tfoot>
                            </table>

                             @if (env('APP_LOYALTY') == 'yes' && $invoice->customer_id != 1 && ((float)($invoice->inv_point ?? 0) > 0 || (float)($invoice->pay_point ?? 0) > 0 || (float)($invoice->total_point ?? $invoice->customer?->total_point ?? 0) > 0))
                                 @php
                                     $usedPts = (float)($invoice->pay_point ?? 0);
                                     $earnedPts = (float)($invoice->inv_point ?? 0);
                                     $remPts = (float)($invoice->total_point ?? $invoice->customer?->total_point ?? 0);
                                     $prevPts = max(0, $remPts + $usedPts - $earnedPts);
                                     $usedTk = round($usedPts * 0.75, 2);
                                     $remTk = round($remPts * 0.75, 2);
                                 @endphp
                                 <div style="border-top: 1px dashed #000; border-bottom: 1px dashed #000; margin: 8px 0; padding: 5px 0; font-size: 11px; font-weight: 500;">
                                     <div style="text-align: center; font-weight: bold; text-transform: uppercase; margin-bottom: 3px;">
                                         ⭐ REWARD POINTS SUMMARY ⭐
                                     </div>
                                     <table style="width: 100%; font-size: 11px;">
                                         <tr>
                                             <td style="text-align: left;">Previous Points:</td>
                                             <td style="text-align: right; font-weight: bold;">{{ number_format($prevPts, 0) }} pts</td>
                                         </tr>
                                         @if($usedPts > 0)
                                         <tr>
                                             <td style="text-align: left;">Points Used (Redeemed):</td>
                                             <td style="text-align: right; font-weight: bold; color: #dc2626;">-{{ number_format($usedPts, 0) }} pts (৳{{ number_format($usedTk, 2) }})</td>
                                         </tr>
                                         @endif
                                         @if($earnedPts > 0)
                                         <tr>
                                             <td style="text-align: left;">Points Earned:</td>
                                             <td style="text-align: right; font-weight: bold; color: #16a34a;">+{{ number_format($earnedPts, 0) }} pts</td>
                                         </tr>
                                         @endif
                                         <tr style="border-top: 1px dotted #ccc;">
                                             <td style="text-align: left; font-weight: bold;">Remaining Points Balance:</td>
                                             <td style="text-align: right; font-weight: bold; color: #2563eb;">{{ number_format($remPts, 0) }} pts (৳{{ number_format($remTk, 2) }})</td>
                                         </tr>
                                     </table>
                                 </div>
                             @endif

                             @if($invoice->note)
                                 <div style="font-family: monospace; font-size: 12px; font-weight: bold; text-align: left; margin-top: 10px; border-bottom: 1px dashed #000; padding-bottom: 5px;">
                                     Note: {{ $invoice->note }}
                                 </div>
                             @endif

                                                        @if ($invoice->installment)
                                <div style="border-bottom: 1px dashed #000; margin: 10px 0; width: 100%;"></div>
                                <div style="font-size: 12px; font-weight: bold; font-family: monospace; text-align: left;">
                                    <div style="text-align: center; font-weight: 900; margin-bottom: 5px;">INSTALLMENT AGREEMENT</div>
                                    <table style="width: 100%; font-size: 11px;">
                                        <tr>
                                            <td>Down Payment:</td>
                                            <td style="text-align: right;">{{ number_format($invoice->installment->advance_pay, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Principal:</td>
                                            <td style="text-align: right;">{{ number_format($invoice->installment->remaining_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Interest Rate:</td>
                                            <td style="text-align: right;">{{ $invoice->installment->interest_percentage }}% ({{ number_format($invoice->installment->interest_amount, 2) }})</td>
                                        </tr>
                                        <tr>
                                            <td>Total Payable:</td>
                                            <td style="text-align: right;">{{ number_format($invoice->installment->total_with_interest, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Per Installment:</td>
                                            <td style="text-align: right;">{{ number_format($invoice->installment->per_installment_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Installments:</td>
                                            <td style="text-align: right;">{{ $invoice->installment->total_installments }} (Every {{ $invoice->installment->interval_days }} days)</td>
                                        </tr>
                                    </table>
                                    
                                    <div style="border-bottom: 1px dashed #000; margin: 5px 0; width: 100%;"></div>
                                    <div style="text-align: center; font-weight: 900; margin-bottom: 5px;">SCHEDULE & STATUS</div>
                                    <table style="width: 100%; font-size: 10px; line-height: 1.3;">
                                        <thead>
                                            <tr style="border-bottom: 1px dashed #000;">
                                                <th style="text-align: left; width: 15%;">No</th>
                                                <th style="text-align: left; width: 35%;">Due Date</th>
                                                <th style="text-align: right; width: 25%;">Amount</th>
                                                <th style="text-align: right; width: 25%;">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($invoice->installment->schedules as $sched)
                                                <tr>
                                                    <td>#{{ $sched->installment_no }}</td>
                                                    <td>{{ $sched->due_date }}</td>
                                                    <td style="text-align: right;">{{ number_format($sched->amount, 2) }}</td>
                                                    <td style="text-align: right;">{{ strtoupper($sched->status) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif

<div class="mt-4 text-black" style="font-size: 12px; font-weight: bold; text-align: left; line-height: 1.5; font-family: monospace;">
                                <div style="margin-bottom: 5px;">Terms & Conditions:</div>
                                <div style="margin-left: 15px;"># Product can be exchange but not refund.</div>
                                <div style="margin-left: 15px;"># Exchange should be done within 7 days with good condition at Once.</div>
                             
                                
                                @php
                                    if (get_setting('inv_details') == 'branch' && $invoice->branch_id) {
                                        $branch = App\Models\Branch::where('id', $invoice->branch_id)->first();
                                        $contactNumber = $branch ? $branch->phone : get_setting('com_phone');
                                        $sellerName    = $branch->shop_name ?? (get_setting('com_name') ?: 'Fast IT');
                                        $vatNumber     = $branch->vat_no ?? (get_setting('vat_no') ?: '300000000000003');
                                    } else {
                                        $contactNumber = empty(get_setting('com_phone')) ? '012335346456' : get_setting('com_phone');
                                        $sellerName    = get_setting('com_name') ?: 'Fast IT';
                                        $vatNumber     = get_setting('vat_no') ?: '300000000000003';
                                    }
                                @endphp

                                @if(is_invoice_qr_enabled() && env('APP_ZATCA') === 'yes')
                                    {{-- ZATCA Phase-1 QR Code Section --}}
                                    @php
                                        $zatcaQrBase64 = zatca_qr_code(
                                            $sellerName,
                                            $vatNumber,
                                            $invoice->created_at ?? $invoice->date ?? now(),
                                            $invoice->total_amount ?? 0,
                                            $invoice->vat_amount ?? 0
                                        );
                                    @endphp
                                    <div class="text-center my-3" style="margin-top: 15px; margin-bottom: 15px; text-align: center;">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($zatcaQrBase64) }}" 
                                             alt="ZATCA QR Code" 
                                             style="width: 120px; height: 120px; display: inline-block;" />
                                        <div style="font-size: 10px; font-weight: bold; margin-top: 4px; font-family: monospace;">ZATCA E-Invoice QR</div>
                                    </div>
                                @elseif(is_invoice_qr_enabled())
                                    @php
                                        $qrInvoiceData = (string) ($invoice->invoice_no ?: $invoice->id);
                                    @endphp
                                    <div class="text-center my-3" style="margin-top: 15px; margin-bottom: 15px; text-align: center;">
                                        <div style="display: inline-block; padding: 5px; background: #fff; border: 1.5px solid #000; border-radius: 6px;">
                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($qrInvoiceData) }}" 
                                                 alt="Invoice QR Code" 
                                                 style="width: 105px; height: 105px; display: block;" />
                                        </div>
                                    </div>
                                @endif


                                <div class="mt-3 text-center" style="font-size: 12px; font-weight: 900;">
                                    Develop by Fast IT LTD
                                </div>
                            </div> 

                        </div>
                        <hr>
                        <button class="btn btn-secondary btn-block print_hidden" onclick="print_receipt('print-area')"> <i
                                class="fa fa-print"></i> Print </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        // clear localstore
        localStorage.removeItem('pos-items');

        let hasPrinted = false;

        function print_receipt(divName) {
            hasPrinted = true;
            let printDoc = $('#' + divName).html();
            let originalContents = $('body').html();
            $("body").html(printDoc);
            window.print();
            $('body').html(originalContents);
        }

        $(document).on('keydown', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                if (!hasPrinted) {
                    print_receipt('print-area');
                } else {
                    window.location.href = "{{ route('invoice.create') }}";
                }
            } else if (e.key === 'Escape' || e.keyCode === 27) {
                window.location.href = "{{ route('invoice.create') }}";
            }
        });

        window.onafterprint = function() {
            hasPrinted = true;
        };

        @if(env('APP_AUTO_PRINT') == 'yes')
        $(document).ready(function() {
            print_receipt('print-area');
            window.location.href = "{{ route('invoice.create') }}";
        });
        @endif
    </script>
@endpush
