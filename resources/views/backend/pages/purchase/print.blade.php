@extends('backend.layouts.master')
@section('page-title', 'Purchase Print')
@push('css')
    <style rel="stylesheet">
        hr {
            margin: 0px;
            margin-bottom: 5px;
            margin-top: 5px;
            border: 1px dashed #000;
        }

        .page-footer hr {
            margin: 2px;
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

        .order-details th.details {
            width: 200px;
        }

        strong {
            font-weight: 800;
        }

        address {
            margin-bottom: 0px;
        }

        .invoice-header {
            width: 100%;
            box-sizing: border-box;
            overflow: hidden;
        }

        .invoice-header address {
            text-align: center;
            font-size: 14px;
        }

        .logo-area img {
            width: 12%;
            display: inline;
            float: left;
            margin-right: 10px;
        }

        .logo-area h1 {
            display: inline;
            float: left;
            font-size: 17px;
            padding-left: 8px;
        }

        .logo-area h4 {
            font-weight: bold;
            font-size: 50px;
        }

        /* .invoice-header .invoice{} */
        .invoice-header .logo-area {
            width: 75%;
            float: left;
            padding: 5px;
            border-right: 1px dotted #000;
        }

        .invoice-header .invoice {
            color: #000;
            border-left: 1px dashed black
        }

        .invoice-header .invoice h3 {
            background-color: #000;
            font-weight: 600;
            text-align: center;
            width: 80%;
            margin: auto;
            color: #fff;
            border-radius: 10px;
        }

        .invoice p {
            margin-bottom: 0px;
        }

        .invoice .invoice-content {
            margin-bottom: 1em;
        }

        .bill-date {
            width: 100%;
            border: 1px solid #000;
            overflow: hidden;
            padding: 0 15px;
        }

        .date {
            width: 50%;
            float: left;
        }

        .bill-no {
            width: 50%;
            float: left;
        }

        .name,
        .address,
        .mobile-no,
        .cus_info {
            width: 100%;
            border-left: 1px solid #000;
            border-bottom: 1px solid #000;
            border-right: 1px solid #000;
            padding: 0 15px;
        }

        .name span,
        .address span,
        .mobile-no span,
        .cus_info span {
            padding-left: 15px;
            font-weight: 800;
        }

        .footer-note {
            margin-top: -66px;
        }

        .sign {
            width: 250px;
            border-top: 1px solid #000;
            float: right;
            margin: 40px 20px 0 0;
            text-align: center;
        }

        @media print {
            body * {
                visibility: visible;
            }

            .table-plist td {
                padding: 0px;
                text-align: center;
            }

            .table-plist th {
                padding: 0px;
                text-align: center;
                background: #000 !important;
                color: #ffffff !important;
                border: 1px solid #e9ecef !important;
            }

            .table-plist th.details {
                width: 200px !important;
            }

            .border-bottom {
                border-bottom: 1px dotted #000;
            }

            .print {
                margin-bottom: 0;
            }

            .table-bordered td {
                border: 1px solid #000 !important;
            }
        }

        .note,
        .signature,
        .bill-no,
        .date,
        .name,
        .mobile-no,
        .address,
        th,
        td,
        address,
        h4 {
            color: black;
        }

        .invoice-contentbar {
            margin: 60px 5px 0 5px;
            padding: 20px;
            margin-bottom: 60px;
            font-family: 'Petrona', serif;
        }
    </style>
    <style>
        .table-plist td {
            padding: 0px;
            text-align: center;
            border: 1px solid #000;
        }

        .table-plist-header {
            width: 100%;
            float: left;
        }

        .table-plist-header td {
            padding: 5px;
            text-align: left;
            border: 1px solid #000;
            font-weight: 600;
            font-size: 14px;
            font-family: 'Roboto';
        }

        .table-plist th {
            padding: 0px;
            text-align: center;
            background: #000;
            color: #ffffff;
        }

        .border-bottom {
            border-bottom: 1px dotted #000;
        }

        .rm-b-l {
            border-left: 1px solid transparent !important;
        }

        .rm-b-t {
            border-top: 1px solid transparent !important;
        }

        .rm-b-b {
            border-bottom: 1px solid transparent !important;
        }

        .logo_n_name {
            text-align: center;
        }

        .logo_n_name img {
            max-width: 50%;
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
                            <div class="invoice-header row">
                                @php

                                    $branch = App\Models\Branch::where('id', $purchase->branch_id)->first();
                                    // dd($branch);
                                @endphp
                                <address class="col-12">
                                    <div class="logo_n_name">
                                        @if (get_setting('inv_logo') == 'logo')
                                            <img src="{{ !empty(get_setting('system_logo')) ? url('uploads/logo/' . get_setting('system_logo')) : url('public/backend/images/logo.png') }}"
                                                alt="logo">
                                            {{-- @elseif (get_setting('inv_logo') == 'name')
                                    <h2 style="font-weight: bold; margin-bottom:0">{{ (empty(get_setting('com_name'))?'----':get_setting('com_name')) }}</h2>
                                    @else
                                    <img src="{{ (!empty(get_setting('system_logo')))?url('uploads/logo/'.get_setting('system_logo')):url('public/backend/images/logo.png') }}" alt="logo">
                                    <h2 style="font-weight: bold; margin-bottom:0">{{ (empty(get_setting('com_name'))?'----':get_setting('com_name')) }}</h2> --}}
                                        @endif
                                        <h2 style="font-weight: bold; margin-bottom:0">{{ $branch->shop_name }}</h2>
                                        {{-- <img src="#" alt="logo"> --}}
                                    </div>
                                    {{-- Address : {{ (empty(get_setting('com_phone'))?'----':get_setting('com_address')) }} <br> Phone : {{ (empty(get_setting('com_phone'))?'----':get_setting('com_phone')) }} Email : {{ (empty(get_setting('com_email'))?'----':get_setting('com_email')) }} <br /> --}}
                                    Address : {{ $branch->address}} <br>
                                    Phone : {{ $branch->phone }} Email :
                                    {{ $branch->email }} <br />
                                </address>
                            </div>
                            <hr>
                            <div class="invoice-header row">
                                <div class="col-8">
                                    <table class="table table-bordered table-plist-header my-3 order-details">
                                        <tbody>
                                            <tr>
                                                <td width="8%">Name </td>
                                                <td width="3%" class="text-center">: </td>
                                                <td colspan="4">
                                                    <strong>{{ $purchase->supplier->name }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Address</td>
                                                <td class="text-center">:</td>
                                                <td colspan="4">
                                                    <strong>{{ $purchase->supplier->address }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Mobile</td>
                                                <td class="text-center">: </td>
                                                <td colspan="4">
                                                    <strong>{{ $purchase->supplier->phone }}</strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="invoice col-4 text-center">
                                    <div class="invoice-content">
                                        <h3 class="mt-3">PURCHASE</h3>
                                    </div>
                                    <p>PURCHASE NO</p>
                                    <p>( {{ $purchase->purchase_no }} )</p>
                                    <p>Date : {{ date('d/m/Y', strtotime($purchase->date)) }}</p>
                                </div>
                            </div>
                            <hr>
                            <table class="table table-bordered table-plist my-3 order-details">
                                <tr class="bg-primary">
                                    <th>#SL</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total Amount</th>
                                </tr>
                                @foreach ($purchase->purchaseItems as $key => $item)
                                    @php
                                        $product = App\Models\Product::where('id', $item->product_id)
                                            ->with('unit.related_unit')
                                            ->first();
                                        if ($product->unit->related_unit == null) {
                                            $qty = $item->main_qty . ' ' . $product->unit->name;
                                        } else {
                                            $sub_qty = $item->sub_qty == null ? 0 : $item->sub_qty;
                                            $qty =
                                                $item->main_qty .
                                                ' ' .
                                                $product->unit->name .
                                                ' ' .
                                                $sub_qty .
                                                ' ' .
                                                $product->unit->related_unit->name;
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td style="width: 30%">
                                            {{ $item->product->name }}
                                            @if (env('APP_IMEI') == 'yes' && !empty($item->imei))
                                                <br><small class="text-dark">{{ __('IMEI') }}: {{ str_replace("\n", ", ", $item->imei) }}</small>
                                            @endif
                                            @if (env('APP_WARRANTY') == 'yes' && !empty($item->warranty_value))
                                                <br><small class="text-dark">{{ __('Warranty') }}: {{ $item->warranty_value }} {{ $item->warranty_unit }}</small>
                                            @endif
                                        </td>
                                        <td><small>{{ $qty }}</small></td>
                                        <td>{{ $item->rate }}
                                            {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</td>
                                        <td>{{ $item->subtotal }}
                                            {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan="3" class="rm-b-l rm-b-t rm-b-b"></td>
                                    <td class="text-right">Sub Total : </td>
                                    <td>
                                        <strong>{{ $purchase->estimated_amount }}
                                        </strong>{{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                    </td>
                                </tr>
                                @if($purchase->discount_amount > 0 || $purchase->discount > 0)
                                <tr>
                                    <td colspan="3" class="rm-b-l rm-b-t rm-b-b"></td>
                                    <td class="text-right">Discount 
                                        @if ($purchase->discount > 0 && str_contains($purchase->discount, '%'))
                                            ({{ $purchase->discount }})
                                        @elseif ($purchase->discount > 0)
                                            ({{ $purchase->discount }}%)
                                        @endif
                                        : 
                                    </td>
                                    <td>
                                        <strong> {{ $purchase->discount_amount }}
                                            {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                        </strong>
                                    </td>
                                </tr>
                                @endif
                                @if($purchase->vat_amount > 0 || $purchase->vat > 0)
                                <tr>
                                    <td colspan="3" class="rm-b-l rm-b-t rm-b-b"></td>
                                    <td class="text-right">VAT 
                                        @if ($purchase->vat > 0 && str_contains($purchase->vat, '%'))
                                            ({{ $purchase->vat }})
                                        @elseif ($purchase->vat > 0)
                                            ({{ $purchase->vat }}%)
                                        @endif
                                        : 
                                    </td>
                                    <td>
                                        <strong> {{ $purchase->vat_amount }}
                                            {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                        </strong>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="rm-b-l rm-b-t rm-b-b"></td>
                                    <td class="text-right">Grand Total : </td>
                                    <td>
                                        <strong>{{ $purchase->total_amount }}
                                        </strong>{{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                    </td>
                                </tr>
                            </table>
                            <div class="footer-note">
                                <p class="note">Notes: </p>
                            </div>
                            <div class="signature">
                                <div class="customers text-center">
                                    <span>--------------------------</span>
                                    <p>Supplier Signature</p>
                                </div>
                                <div class="authorized text-center">
                                    <span>--------------------------</span>
                                    <p>Authorized Signature</p>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-secondary btn-block print_hidden" onclick="print_receipt('print-area')">
                            <i class="fa fa-print"></i> Print </button>
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

        function print_receipt(divName) {
            let printDoc = $('#' + divName).html();
            let originalContents = $('body').html();
            $("body").html(printDoc);
            window.print();
            $('body').html(originalContents);
        }

        @if(env('APP_AUTO_PRINT') == 'yes')
        $(document).ready(function() {
            print_receipt('print-area');
        });
        @endif
    </script>
@endpush
