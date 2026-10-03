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
            border-bottom: 1px dashed #000;
            border-left: 0px;
            border-right: 0px;
            /*border: 0px;*/
        }

        .border_th {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
            border-left: 0px;
            border-right: 0px;
            /*border:0px;*/
        }

        .border_item {
            border-top: 1px dashed #000;
            /*border-bottom: 1px dashed #000;*/
            border-left: 0px;
            border-right: 0px;
            /*border:0px;*/
        }

        .border_disco {
            /*border-top: 1px dashed #000;*/
            border-bottom: 1px dashed #000;
            border-left: 0px;
            border-right: 0px;
            /*border:0px;*/
        }

        .credit_border {
            border-top: 1px dashed #000;
            border-bottom: 1px dashed #000;
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
                                @if (get_setting('inv_details') == 'branch')
                                    @php
                                        $branch = App\Models\Branch::where('id', $invoice->branch_id)->first();
                                    @endphp
                                    <div class="logo-area">
                                        @php
                                            $sysLogo = ($branch && !empty($branch->logo)) ? asset('uploads/logo/' . $branch->logo) : (!empty(get_setting('system_logo')) ? url('uploads/logo/' . get_setting('system_logo')) : url('backend/images/fastLogo.jpeg'));
                                        @endphp
                                        @if (get_setting('inv_logo') == 'logo')
                                            <img src="{{ $sysLogo }}" alt="logo">
                                        @elseif (get_setting('inv_logo') == 'name')
                                            <h2 style="font-weight: bold; margin-bottom:0;">
                                                {{ $branch->shop_name }}
                                            </h2>
                                        @else
                                            <img src="{{ $sysLogo }}" alt="logo">
                                            <h2 style="font-weight: bold; margin-bottom:0">
                                                {{ $branch->shop_name }}
                                            </h2>
                                        @endif
                                    </div>
                                    <address>
                                        <strong>{{ $branch->address }}</strong>
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
                                            <img src="{{ $sysLogo }}" alt="logo">
                                        @elseif (get_setting('inv_logo') == 'name')
                                            <h2 style="font-weight: bold; margin-bottom:0;">
                                                {{ empty(get_setting('com_name')) ? 'Fast IT' : get_setting('com_name') }}</h2>
                                        @else
                                            <img src="{{ $sysLogo }}" alt="logo">
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
                                <h4 class="text-center mt-3 text-black sales_border"><strong>Due Collect Memo</strong></h4>
                            </div>
                            <div class="col-12 text-center">
                                <div class="row">
                                    <div class="name" style="font-size: 15px"> <label style="width: 35%;margin-top:1px">
                                            Cashier </label> : {{ $invoice->user->name }}
                                    </div>
                                    <div class="name" style="font-size: 15px"> <label style="width: 35%;margin-top:1px">
                                            Date </label> : {{ date('d-M-Y', strtotime($invoice->date)) }}
                                    </div>
                                    <div class="name" style="font-size: 15px"> <label style="width: 35%;margin-top:1px">
                                            Invoice No </label> : {{ $invoice->invoice_no }}
                                    </div>
                                    <div class="name" style="font-size: 15px"> <label style="width: 35%;margin-top:1px">
                                            Customer </label> : {{ $invoice->customer->name }}
                                    </div>
                                </div>
                            </div>
                            <table class="table table-bordered table-plist order-details">
                                
                                <tr>
                                    <td colspan="3" class="text-right">Total Amount : </td>
                                    <td colspan="1" class="text-left">
                                        {{ $invoice->total_amount }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right">Total Paid : </td>
                                    <td colspan="1" class="text-left">
                                        {{ number_format($invoice->total_paid - $invoice->due_pay,2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right"> Due Paid: </td>
                                    <td colspan="1" class="text-left">
                                        {{ number_format($invoice->due_pay,2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right">Due Amount: </td>
                                    <td colspan="1" class="text-left">
                                        {{ number_format($invoice->total_due,2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">Made of Payment </td>
                                </tr>
                                <tr class="credit_border">
                                    <td colspan="3" class="text-center"><strong>Credit </strong> </td>
                                    <td colspan="1" class="text-left">
                                        <strong>{{ $invoice->due_pay }} </strong>
                                    </td>
                                </tr>
                            </table>

                            @if($invoice->note)
                                 <div style="font-family: monospace; font-size: 12px; font-weight: bold; text-align: left; margin-top: 10px; border-bottom: 1px dashed #000; padding-bottom: 5px;">
                                     Note: {{ $invoice->note }}
                                 </div>
                             @endif

                            @if(is_invoice_qr_enabled() && env('APP_ZATCA') === 'yes')
                                {{-- ZATCA Phase-1 QR Code Section --}}
                                @php
                                    $sellerName = (get_setting('inv_details') == 'branch' && isset($branch) && $branch) ? $branch->shop_name : (get_setting('com_name') ?: 'Fast IT');
                                    $vatNumber  = (get_setting('inv_details') == 'branch' && isset($branch) && $branch) ? ($branch->vat_no ?? (get_setting('vat_no') ?: '300000000000003')) : (get_setting('vat_no') ?: '300000000000003');
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
                                             style="width: 100px; height: 100px; display: block;" />
                                    </div>
                                </div>
                            @endif
                            
                            <h6 class="text-center">Thank you for Shopping at
                                {{ (get_setting('inv_details') == 'branch' && isset($branch) && $branch) ? $branch->shop_name : (empty(get_setting('com_name')) ? 'Fast IT' : get_setting('com_name')) }}</h6>
                            <h6 class="text-center" style="margin-bottom: 20px;">Developed By Fast iT Ltd <br>
                                <span> Call: 01784-159071</span>
                            </h6>
                            <p>.</p>
                            <p>.</p>
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
