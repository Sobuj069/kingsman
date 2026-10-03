@extends('backend.layouts.master')
@section('page-title', 'Invoice Print')
@push('css')
    <style rel="stylesheet">
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
            body * {
                visibility: visible;
                color: #000 !important;
                font-size: 10px !important;
                line-height: 12px;
                font-weight: 800 !important;
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
    <div class="invoice-contentbar">
        <div class="row">
            <div class="col-md-12">
                <div class="row justify-content-center">
                    <div class="col-md-7 card card-body print">
                        <div id="print-area">
                            <div class="invoice-header mt-3">
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
                                        // dd($branch);
                                    @endphp
                                    <address class="col-12">
                                        <div class="logo_n_name" style="text-align: center; width: 100%;">
                                            @php
                                                $sysLogo = ($branch && !empty($branch->logo)) ? asset('uploads/logo/' . $branch->logo) : (!empty(get_setting('system_logo')) ? url('uploads/logo/' . get_setting('system_logo')) : url('backend/images/fastLogo.jpeg'));
                                            @endphp
                                            @if (get_setting('inv_logo') == 'logo')
                                                <img src="{{ $sysLogo }}" style="width:150px; height:50px; margin: 0 auto; display: inline-block;" alt="logo">
                                            @elseif (get_setting('inv_logo') == 'name')
                                                <h2 style="font-weight: bold; margin-bottom:0; font-size: 18px;">
                                                    {{ $branch->shop_name }}
                                                </h2>
                                            @else
                                                <img src="{{ $sysLogo }}" style="width:150px; height:50px; margin: 0 auto; display: inline-block;" alt="logo">
                                                <h2 style="font-weight: bold; margin-bottom:0; font-size: 18px;">
                                                    {{ $branch->shop_name }}
                                                </h2>
                                            @endif
                                        </div>
                                        Address :
                                        {{ $branch->address }}
                                        <br>
                                        Phone :
                                        {{ $branch->phone }} <br>
                                        Email :
                                        {{ $branch->email }}
                                        <br />
                                    </address>
                                @else
                                    <address class="col-12">
                                        <div class="logo_n_name" style="text-align: center; width: 100%;">
                                            @php
                                                $sysLogo = !empty(get_setting('system_logo')) ? url('uploads/logo/' . get_setting('system_logo')) : url('backend/images/fastLogo.jpeg');
                                            @endphp
                                            @if (get_setting('inv_logo') == 'logo')
                                                <img src="{{ $sysLogo }}" style="width:150px; height:50px; margin: 0 auto; display: inline-block;" alt="logo">
                                            @elseif (get_setting('inv_logo') == 'name')
                                                <h2 style="font-weight: bold; margin-bottom:0; font-size: 18px;">
                                                    {{ empty(get_setting('com_name')) ? 'Fast IT' : get_setting('com_name') }}
                                                </h2>
                                            @else
                                                <img src="{{ $sysLogo }}" style="width:150px; height:50px; margin: 0 auto; display: inline-block;" alt="logo">
                                                <h2 style="font-weight: bold; margin-bottom:0; font-size: 18px;">
                                                    {{ empty(get_setting('com_name')) ? 'Fast IT' : get_setting('com_name') }}
                                                </h2>
                                            @endif
                                        </div>
                                        Address :
                                        {{ empty(get_setting('com_address')) ? 'Suite: 807,Shah Ali Plaza, Mirpur-10, Dhaka-1216.' : get_setting('com_address') }}
                                        <br>
                                        Phone :
                                        {{ empty(get_setting('com_phone')) ? '01784-159071' : get_setting('com_phone') }}
                                        Email :
                                        {{ empty(get_setting('com_email')) ? 'fastitbd00@gamil.com' : get_setting('com_email') }}
                                        <br />
                                    </address>
                                @endif
                                <h4 class="text-center mt-0 text-black sales_border"><strong>Sales Invoice</strong></h4>
                            </div>
                            <div class="col-12 text-left">
                                <div class="row">
                                    <div class="name" style="font-size: 15px"> <label style="width: 35%;margin-top:1px">
                                            Cashier </label> : {{ $invoice->user->name }}
                                    </div>
                                    <div class="name" style="font-size: 15px"> <label style="width: 35%;margin-top:1px">
                                            Date </label> : {{ date('d-M-Y h:i:s A', strtotime($invoice->created_at)) }}
                                    </div>
                                     <div class="name" style="font-size: 15px"> <label style="width: 35%;margin-top:1px">
                                            Invoice No </label> : {{ $invoice->invoice_no }} @if($invoice->is_edited == 1 || $invoice->edit_status == 'edited') (Edited) @elseif($invoice->is_edited == 2 || $invoice->edit_status == 'exchange') (Exchange) @endif
                                     </div>
                                    <div class="name" style="font-size: 15px"> <label style="width: 35%;margin-top:1px">
                                            Customer Phone
                                            </label> : {{ $invoice->customer?->phone }}
                                    </div>
                                </div>
                            </div>
                            <table class="table table-bordereds table-plists order-detailss">
                                <tr class="border_th">
                                    <th width="5%">SL</th>
                                    <th width="45%">Item</th>
                                    <th width="12%">Discount</th>
                                    <th width="13%">Rate</th>
                                    <th width="10%">Qty</th>
                                    <th width="15%">Amount</th>
                                </tr>
                                @php
                                    $printItems = $invoice->invoiceItems->groupBy(function($it) {
                                        return $it->product_id . '_' . ($it->product_variation_id ?? 0);
                                    });
                                @endphp
                                @foreach ($printItems as $key => $group)
                                    @php
                                        $item = $group->first();
                                        $comb_main = $group->sum('actual_main');
                                        $comb_sub = $group->sum('actual_sub');
                                        $comb_subtotal = $group->sum('subtotal');
                                        $product = App\Models\Product::where('id', $item->product_id)
                                            ->with('unit.related_unit')
                                            ->first();
                                        if ($product && $product->is_service == 0) {
                                            if ($product->unit?->related_unit == null) {
                                                $qty = $comb_main . ' ' . $product->unit?->name;
                                            } else {
                                                $related_value = $product->unit->related_value ?: 1;
                                                $total_main = $comb_main + $comb_sub / $related_value;
                                                $qty = $total_main . ' ' . $product->unit->name;
                                            }
                                        } else {
                                            $qty = $comb_main . ' pcs';
                                        }

                                        $cash = App\Models\BankTransaction::where('invoice_id', $invoice->id)->get();

                                        $cash_amount = App\Models\BankTransaction::where('invoice_id', $invoice->id)
                                            ->where('bank_id', 1)
                                            ->sum('amount');
                                        $return_amount_cash = (float) $cash_amount - $invoice->return_amount;
                                    @endphp
                                    <tbody class="border_group">
                                        <tr class="text-center border_item">
                                            <td>{{ $loop->iteration }}</td>
                                            <td style="text-align: left; padding-left: 5px;">
                                                <p style="margin: 0;">
                                                    @if ($item->status == 2)
                                                        {{ $item->product?->name }}
                                                        @if (env('APP_SC') == 'yes')
                                                            @if ($item->product_variation_id != null)
                                                                ({{ $item->product_variation?->size?->size }}-{{ $item->product_variation?->color?->color }})
                                                            @endif
                                                        @endif
                                                    @else
                                                        {{ $item->product?->name }}
                                                        @if ($item->is_return == 1)
                                                            <span class="badge bg-danger">Return</span>
                                                        @endif
                                                        @if (env('APP_SC') == 'yes')
                                                            @if ($item->product_variation_id != null)
                                                                ({{ $item->product_variation?->size?->size }}-{{ $item->product_variation?->color?->color }})
                                                            @endif
                                                        @endif
                                                    @endif
                                                    @if(!empty($item->imei))
                                                        <br><small><strong>IMEI:</strong> {{ $item->imei }}</small>
                                                    @endif
                                                </p>
                                            </td>
                                            <td>{{ $item->product_discount }} </td>
                                            <td>{{ $item->rate }} </td>
                                            <td><small>{{ $qty }}</small></td>
                                            <td>{{ $comb_subtotal }} </td>
                                        </tr>
                                    </tbody>
                                @endforeach
                                <tfoot class="tfoot_style">
                                    <tr class="border_item">
                                        <td colspan="5" class="text-left">Sub Total : </td>
                                        <td colspan="1" class="text-center">
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
                                        <td colspan="1" class="text-center">
                                            {{ $invoice->discount_amount }}
                                        </td>
                                    </tr>
                                    @if ($invoice->vat_amount > 0)
                                    <tr class="border_disco">
                                        <td colspan="5" class="text-left">VAT 
                                            @if (str_contains($invoice->vat, '%'))
                                                ({{ $invoice->vat }})
                                            @endif
                                            : 
                                        </td>
                                        <td colspan="1" class="text-center">
                                            {{ $invoice->vat_amount }}
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td colspan="5" class="text-left">Total Amount : </td>
                                        <td colspan="1" class="text-center">
                                            {{ $invoice->total_amount }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-left">Paid : </td>
                                        <td colspan="1" class="text-center">
                                            {{ $invoice->total_paid }}
                                        </td>
                                    </tr>
                                    @if ($invoice->return_amount != 0)
                                        <tr>
                                            <td colspan="5" class="text-left">Return Amount : </td>
                                            <td colspan="1" class="text-center">
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
                                         <td colspan="1" class="text-center">
                                             {{ number_format($invoice->total_due, 2) }}
                                         </td>
                                     </tr>
                                     @if($displayPreviousDue > 0)
                                     <tr>
                                         <td colspan="5" class="text-left">Previous Due : </td>
                                         <td colspan="1" class="text-center">
                                             {{ number_format($displayPreviousDue, 2) }}
                                         </td>
                                     </tr>
                                     <tr>
                                         <td colspan="5" class="text-left" style="font-weight: bold;">Total Due : </td>
                                         <td colspan="1" class="text-center" style="font-weight: bold;">
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
                                        <td colspan="1" class="text-center">
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
                                            <td colspan="1" class="text-center">
                                                {{ number_format($invoice->pay_point, 2) }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="5" class="text-left">Change Amount: </td>
                                        <td colspan="1" class="text-center">
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
                                            <td colspan="1" class="text-center">

                                                {{ $invoice->total_due }}
                                                {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="5">Mode of Payment ( @foreach ($cash as $bank)
                                                {{ $bank->bank_account->bank_name }} ,
                                            @endforeach) </td>
                                    </tr>
                                    <tr class="credit_border">
                                        <td colspan="5" class="text-left"><strong> </strong> </td>
                                        <td colspan="1" class="text-center">
                                            <strong>
                                                @php
                                                    $creadit = $invoice->total_paid - $invoice->return_amount;
                                                @endphp
                                                {{ number_format($creadit, 2) }}

                                            </strong>
                                        </td>
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

                            @if(is_invoice_qr_enabled() && env('APP_ZATCA') === 'yes')
                                {{-- ZATCA Phase-1 QR Code Section --}}
                                @php
                                    $sellerName = $branch->shop_name ?? (get_setting('com_name') ?: 'Fast IT');
                                    $vatNumber  = $branch->vat_no ?? (get_setting('vat_no') ?: '300000000000003');
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

                            <p class="text-center mt-4 text-black">
                                <strong> ***** The Product should be change within 7 days.Don't remove the tag can not be
                                    change ***** </strong>
                            </p>
                            <h6 class="text-center">Thank you for Shopping at
                                {{ empty(get_setting('com_name')) ? 'Fast IT' : get_setting('com_name') }}</h6>
                            {{-- <h6 class="text-center">Thank you for Shopping at
                                {{ empty(get_setting('com_name')) ? 'Fast IT' : get_setting('com_name') }}</h6> --}}
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
