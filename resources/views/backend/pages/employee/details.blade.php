@extends('backend.layouts.master')
@section('page-title', 'Invoice Print')
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
                border: 1px solid #000 !important;
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

        .table-bordered td.rm-b-l {
            border-left: 1px solid transparent !important;
        }

        .table-bordered td.rm-b-t {
            border-top: 1px solid transparent !important;
        }

        .table-bordered td.rm-b-b {
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
                                <address class="col-12">
                                    <div class="logo_n_name">
                                        @if (get_setting('inv_logo') == 'logo')
                                            <img src="{{ !empty(get_setting('system_logo')) ? url('uploads/logo/' . get_setting('system_logo')) : url('backend/images/fastLogo.jpeg') }}"
                                                style="width: 150px; height: 50px;" alt="logo">
                                        @elseif (get_setting('inv_logo') == 'name')
                                            <h2 style="font-weight: bold; margin-bottom:0">
                                                {{ empty(get_setting('com_name')) ? 'Fast IT' : get_setting('com_name') }}
                                            </h2>
                                        @else
                                            <img src="{{ !empty(get_setting('system_logo')) ? url('uploads/logo/' . get_setting('system_logo')) : url('backend/images/fastLogo.jpeg') }}"
                                                style="width: 150px; height: 50px;" alt="logo">
                                            <h2 style="font-weight: bold; margin-bottom:0">
                                                {{ empty(get_setting('com_name')) ? 'Fast IT' : get_setting('com_name') }}
                                            </h2>
                                        @endif
                                    </div>
                                    {{ __('Address') }} :
                                    {{ empty(get_setting('com_address')) ? 'Suite: 807,Shah Ali Plaza, Mirpur-10, Dhaka-1216.' : get_setting('com_address') }}
                                    <br>
                                    {{ __('Phone') }} :
                                    {{ empty(get_setting('com_phone')) ? '01784-159071' : get_setting('com_phone') }}
                                    {{ __('Email') }} :
                                    {{ empty(get_setting('com_email')) ? 'fastitbd00@gamil.com' : get_setting('com_email') }}
                                    <br />
                                </address>
                            </div>
                            <hr>
                            <div class="invoice-header row">
                                <div class="col-6">
                                    <table class="table table-borderedd table-plist-headerr my-1 order-detailss">
                                        <tbody>
                                            <tr>
                                                <td width="8%">{{ __('Name') }} </td>
                                                <td width="3%" class="text-center">: </td>
                                                <td colspan="4">
                                                    <strong>{{ $employee->name }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('Address') }}</td>
                                                <td class="text-center">:</td>
                                                <td colspan="4">
                                                    <strong>{{ $employee->address }}</strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <table class="table table-borderedd table-plist-headerr my-1 order-detailss">
                                        <tbody>
                                            <tr>
                                                <td>{{ __('Mobile') }}</td>
                                                <td class="text-center">: </td>
                                                <td colspan="4">
                                                    <strong>{{ $employee->phone }}</strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('Branch') }}</td>
                                                <td class="text-center">: </td>
                                                <td colspan="4">
                                                    <strong>{{ $employee->branch?->name }}</strong>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            {{-- <hr> --}}
                            <table class="table table-bordered table-plist my-3 order-details">
                                <tr>
                                    <th>{{ __('#SL') }}</th>
                                    <th>{{ __('Month') }}</th>
                                    <th>{{ __('Payment Date') }}</th>
                                    <th>{{ __('Payment Type') }}</th>
                                    <th>{{ __('Payment Method') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Note') }}</th>
                                </tr>
                                @foreach ($employee_payment as $key => $payment)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ __(\Carbon\Carbon::parse($payment->month)->format('F')) }}</td>
                                        <td>{{ $payment->payment_date }}</td>
                                        <td>{{ $payment->payment_type }}</td>
                                        <td>{{ $payment->bank_account?->bank_name }}</td>
                                        <td>{{ $payment->payment }}
                                            {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</td>
                                        <td>{{ $payment->note }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                        <button class="btn btn-secondary btn-block print_hidden mt-3" onclick="print_receipt('print-area')">
                                    <i class="fa fa-print"></i> {{ __('Print') }} </button>
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
    </script>
@endpush
