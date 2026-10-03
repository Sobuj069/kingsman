<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Order Invoice</title>
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
        .tfoot_style tr td{
            padding: 0px !important;
            margin: 4px !important;
        }
        .name_des{
            padding: 0px !important;
            margin: 0px !important;
        }
        .name_des p{
            margin: 0px !important;
            padding: 0px !important;
            text-align: left;
            font-size: 12px;
            line-height: normal;
        }
    </style>

</head>
<body>
    <div class="invoice-contentbar">
        <div class="row">
            <div class="col-md-12">
                <div class="row justify-content-center">
                    <div class="col-md-7 card card-body print">
                        <div id="print-area">
                            <div class="invoice-header mt-3">
                                @if (get_setting('inv_details') == 'branch')
                                    @php
                                        $branch = App\Models\Branch::where('id', $invoice->branch_id)->first();
                                    @endphp
                                    <div class="logo-area">
                                        @php
                                            $sysLogo = ($branch && !empty($branch->logo)) ? asset('uploads/logo/' . $branch->logo) : (!empty(get_setting('system_logo')) ? url('uploads/logo/' . get_setting('system_logo')) : url('backend/images/fastLogo.jpeg'));
                                        @endphp
                                        @if (get_setting('inv_logo') == 'logo')
                                            <img src="{{ $sysLogo }}" style="width:150px; height:50px;" alt="logo">
                                        @elseif (get_setting('inv_logo') == 'name')
                                            <h2 style="font-weight: bold; margin-bottom:0;">
                                                {{ $branch->shop_name }}
                                            </h2>
                                        @else
                                            <img src="{{ $sysLogo }}" style="width:150px; height:50px;" alt="logo">
                                            <h2 style="font-weight: bold; margin-bottom:0">
                                                {{ $branch->shop_name }}
                                            </h2>
                                        @endif
                                    </div>
                                    <address>
                                        Address : <strong> {{ $branch->address }}</strong>
                                        <br> Mobile:
                                        <strong> +88 {{ $branch->phone }}</strong>
                                        <br> Email :
                                        <strong>{{ $branch->email }}</strong>
                                        <br>
                                    </address>
                                @else
                                    <div class="logo-area">
                                        @php
                                            $sysLogo = !empty(get_setting('system_logo')) ? url('uploads/logo/' . get_setting('system_logo')) : url('backend/images/fastLogo.jpeg');
                                        @endphp
                                        @if (get_setting('inv_logo') == 'logo')
                                            <img src="{{ $sysLogo }}" style="width:150px; height:50px;" alt="logo">
                                        @elseif (get_setting('inv_logo') == 'name')
                                            <h2 style="font-weight: bold; margin-bottom:0;">
                                                {{ empty(get_setting('com_name')) ? 'Fast IT' : get_setting('com_name') }}</h2>
                                        @else
                                            <img src="{{ $sysLogo }}" style="width:150px; height:50px;" alt="logo">
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
                                        <br>
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
                                            Invoice No </label> : {{ $invoice->invoice_no }}
                                    </div>
                                    <div class="name" style="font-size: 15px"> <label style="width: 35%;margin-top:1px">
                                            Customer Id </label> : {{ $invoice->customer->phone }}
                                    </div>
                                </div>
                            </div>
                            <table class="table table-bordereds table-plists order-detailss">
                                <tr class="border_th">
                                    <th width="1">SL</th>
                                    <th width="25">Item</th>
                                    <th width="15">Discount</th>
                                    <th width="15">Rate</th>
                                    <th width="13">Qty</th>
                                    <th width="60">Amount</th>
                                </tr>
                                @foreach ($invoice->invoiceItems as $key => $item)
                                    @php
                                        $product = App\Models\Product::where('id', $item->product_id)
                                            ->with('unit.related_unit')
                                            ->first();
                                        if ($product->is_service == 0) {
                                            if ($product->unit->related_unit == null) {
                                                // Sub unit নাই → normal show
                                                $qty = $item->main_qty . ' ' . $product->unit->name;
                                            } else {
                                                // Sub unit আছে → সব convert হবে main unit এ
                                                $sub_qty = $item->sub_qty ?? 0;
                                                $related_value = $product->unit->related_value; // ১ main unit = কত sub unit

                                                // main_qty + sub_qty convert to main unit
                                                $total_main = $item->main_qty + $sub_qty / $related_value;

                                                // চূড়ান্ত show → শুধু main unit এ
                                                $qty = $total_main . ' ' . $product->unit->name;
                                            }
                                        } else {
                                            $qty = $item->main_qty . ' pcs';
                                        }

                                        $cash = App\Models\BankTransaction::where('invoice_id', $invoice->id)->get();

                                        $cash_amount = App\Models\BankTransaction::where('invoice_id', $invoice->id)
                                            ->where('bank_id', 1)
                                            ->sum('amount');
                                        $return_amount_cash = (float) $cash_amount - $invoice->return_amount;
                                    @endphp
                                    <tbody class="border_group">
                                        <tr class="text-center border_item">
                                            <td>{{ ++$key }}</td>
                                            <td style="text-align: left; padding-left: 5px;">
                                                <p style="margin: 0;">{{ $item->product?->name }}</p>
                                            </td>
                                            <td>{{ $item->product_discount }} </td>
                                            <td>{{ $item->rate }} </td>
                                            <td><small>{{ $qty }}</small></td>
                                            <td>{{ $item->subtotal }} </td>
                                        </tr>
                                    </tbody>
                                @endforeach
                                <tfoot class="tfoot_style">
                                    <tr class="border_item">
                                        <td colspan="4" class="text-left">Sub Total : </td>
                                        <td colspan="1" class="text-right">
                                            {{ $invoice->estimated_amount }}
                                        </td>
                                    </tr>
                                    <tr class="border_disco">
                                        <td colspan="4" class="text-left">Discount : </td>
                                        <td colspan="1" class="text-right">
                                            @if (gettype($invoice->discount) === 'string')
                                                {{ $invoice->discount }}
                                            @else
                                                {{ $invoice->discount }}
                                                {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-left">Total Amount : </td>
                                        <td colspan="1" class="text-right">
                                            {{ $invoice->total_amount }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-left">Paid : </td>
                                        <td colspan="1" class="text-right">
                                            {{ $invoice->total_paid }}
                                        </td>
                                    </tr>
                                    @if ($invoice->return_amount != 0)
                                        <tr>
                                            <td colspan="4" class="text-left">Return Amount : </td>
                                            <td colspan="1" class="text-right">
                                                {{ $invoice->return_amount }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if($invoice->customer_id != 1)
                                    <tr>
                                        <td colspan="4" class="text-left">Previous Due : </td>
                                        <td colspan="1" class="text-right">
                                            {{ $invoice->previous_due }}
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td colspan="4" class="text-left">Current Due : </td>
                                        <td colspan="1" class="text-right">
                                            {{ $invoice->total_due }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-left">

                                            {{-- <ul style="list-style: none">
                                                <li> --}}
                                            @foreach ($cash as $bank)
                                                {{ $bank->bank_account->bank_name }} Amount : <br>
                                            @endforeach
                                            {{-- </li>
                                            </ul> --}}

                                        </td>
                                        <td colspan="1" class="text-left">
                                            {{-- <ul style="list-style: none">
                                                <li> --}}
                                            @if ($invoice->return_amount != 0)
                                                {{ number_format($return_amount_cash, 2) }}
                                            @else
                                                @foreach ($cash as $bank)
                                                    {{ number_format($bank->amount, 2) }} <br>
                                                @endforeach
                                            @endif
                                            {{-- </li>
                                            </ul> --}}
                                        </td>
                                    </tr>
                                    @if ($invoice->pay_point != 0)
                                        <tr>
                                            <td colspan="4" class="text-left">Pay Point : </td>
                                            <td colspan="1" class="text-right">
                                                {{ number_format($invoice->pay_point, 2) }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="4" class="text-left">Change Amount: </td>
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
                                            <td colspan="4" class="text-left">Total Due : </td>
                                            <td colspan="1" class="text-right">

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
                                        <td colspan="4" class="text-left"><strong> </strong> </td>
                                        <td colspan="1" class="text-right">
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

                             @if($invoice->note)
                                 <div style="font-family: monospace; font-size: 12px; font-weight: bold; text-align: left; margin-top: 10px; border-bottom: 1px dashed #000; padding-bottom: 5px;">
                                     Note: {{ $invoice->note }}
                                 </div>
                             @endif
                         
                            <p class="text-center mt-4 text-black">
                                <strong> ***** The Product should be change within 7 days.Don't remove the tag can not be
                                    change ***** </strong>
                            </p>
                            <h6 class="text-center">Thank you for Shopping at
                                {{ (get_setting('inv_details') == 'branch' && isset($branch) && $branch) ? $branch->shop_name : (empty(get_setting('com_name')) ? 'Fast IT' : get_setting('com_name')) }}</h6>
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
    
    </body>

</html>

