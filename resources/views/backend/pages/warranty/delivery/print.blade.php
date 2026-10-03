@extends('backend.layouts.master')
@section('page-title', 'Warranty Delivery Print')
@push('css')
    <style>
        .print-btn {
            margin-top: 20px;
        }
        @media print {
            .print-btn { display: none !important; }
            header, nav, footer, .breadcrumbbar, .leftbar, .topbar { display: none !important; }
            .contentbar { margin: 0 !important; padding: 0 !important; }
            body { background: #fff !important; }
        }
        .receipt-container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border: 1px solid #dee2e6;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 5px;
        }
        .receipt-title {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #555;
            margin-top: 10px;
        }
        .info-table td {
            padding: 8px 0;
            border: none;
        }
        .details-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .details-table th, .details-table td {
            padding: 12px;
            border: 1px solid #dee2e6;
        }
        .signature-section {
            margin-top: 80px;
            display: flex;
            justify-content: space-between;
        }
        .sig-line {
            width: 200px;
            border-top: 1px solid #333;
            text-align: center;
            padding-top: 5px;
            font-weight: 500;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 text-center print-btn">
            <button class="btn btn-secondary" onclick="print_receipt('print-area')">
                <i class="fa fa-print"></i> {{ __('Print Receipt') }}
            </button>
        </div>
    </div>
    
    <div id="print-area" class="receipt-container">
        <div class="receipt-header">
            <div class="company-name">{{ empty(get_setting('com_name')) ? 'Fast IT' : get_setting('com_name') }}</div>
            <div>{{ empty(get_setting('com_address')) ? '' : get_setting('com_address') }}</div>
            <div>{{ __('Phone:') }} {{ empty(get_setting('com_phone')) ? '' : get_setting('com_phone') }}</div>
            <div class="receipt-title">{{ __('Warranty Delivery Receipt') }}</div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <table class="info-table w-100">
                    <tr>
                        <td width="35%"><strong>{{ __('Customer Name:') }}</strong></td>
                        <td>{{ $delivery->claim->customer ? $delivery->claim->customer->name : __('Walking Customer') }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('Customer Phone:') }}</strong></td>
                        <td>{{ $delivery->claim->customer ? $delivery->claim->customer->phone : '' }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('Customer Address:') }}</strong></td>
                        <td>{{ $delivery->claim->customer ? $delivery->claim->customer->address : '' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="info-table w-100">
                    <tr>
                        <td width="35%"><strong>{{ __('Claim No:') }}</strong></td>
                        <td>{{ $delivery->claim->claim_no }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('Delivery Date:') }}</strong></td>
                        <td>{{ \Carbon\Carbon::parse($delivery->delivered_date)->format('d-m-Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>{{ __('Received Date:') }}</strong></td>
                        <td>{{ \Carbon\Carbon::parse($delivery->claim->received_date)->format('d-m-Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <table class="table details-table w-100">
                    <thead>
                        <tr>
                            <th>{{ __('Description') }}</th>
                            <th>{{ __('Product Name') }}</th>
                            <th>{{ __('Serial / IMEI No') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>{{ __('Received Item') }}</strong></td>
                            <td>{{ $delivery->claim->product_name }}</td>
                            <td>{{ $delivery->claim->serial_no }}</td>
                        </tr>
                        <tr>
                            <td><strong>{{ __('Delivered Item (New/Fixed)') }}</strong></td>
                            <td>{{ $delivery->product->name }}</td>
                            <td>{{ $delivery->delivered_serial ?? __('N/A') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @if($delivery->delivery_note)
            <div class="row mb-4">
                <div class="col-md-12">
                    <p><strong>{{ __('Delivery Note / Remarks:') }}</strong></p>
                    <div class="p-3 bg-light border rounded">
                        {{ $delivery->delivery_note }}
                    </div>
                </div>
            </div>
        @endif

        <div class="signature-section">
            <div class="sig-line">
                {{ __('Customer Signature') }}
            </div>
            <div class="sig-line">
                {{ __('Authorized Signature') }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script>
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
