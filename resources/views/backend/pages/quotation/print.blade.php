@extends('backend.layouts.master')
@section('page-title', 'Quotation Print')
@push('css')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');

        .invoice-contentbar {
            margin: 60px 5px 0 5px;
            padding: 20px;
            margin-bottom: 60px;
            font-family: 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            color: #000;
        }

        .invoice-wrapper {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border: 1px solid #ccc;
        }

        /* ── HEADER ── */
        .inv-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 15px;
            border-bottom: 2px solid #000;
            position: relative;
        }

        .header-logo-left {
            flex: 1;
        }

        .header-company-center {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
        }

        .header-company-center .company-name {
            font-size: 22px;
            font-weight: 800;
            color: #000;
            margin: 0;
            line-height: 1.2;
        }

        .header-company-center .company-tagline {
            font-size: 11px;
            color: #555;
            margin-top: 3px;
        }

        .header-info-right {
            flex: 1;
            text-align: right;
            font-size: 13px;
            line-height: 1.4;
        }

        .head-office-title {
            font-weight: bold;
            text-decoration: underline;
            font-size: 15px;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .head-office-details {
            color: #000;
        }

        /* ── DETAILS GRID ── */
        .customer-info-section {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .customer-info-left {
            width: 50%;
        }

        .customer-info-right {
            width: 40%;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .customer-info-left table, .customer-info-right table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .customer-info-left td, .customer-info-right td {
            padding: 4px 0;
            vertical-align: top;
        }

        .customer-info-left td:first-child {
            width: 100px;
            font-weight: bold;
        }

        .customer-info-right td:first-child {
            width: 80px;
            font-weight: bold;
        }

        .bill-title-box {
            border: 2px solid #000;
            padding: 6px 20px;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            margin-bottom: 10px;
            width: 100%;
            max-width: 200px;
            text-transform: uppercase;
        }

        /* ── PRODUCT TABLE ── */
        .inv-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-top: 15px;
        }

        .inv-table th, .inv-table td {
            border: 1px solid #000;
            padding: 8px 10px;
            vertical-align: middle;
        }

        .inv-table th {
            font-weight: bold;
            background-color: #ffffff;
            color: #000;
        }

        .inv-table .desc-col {
            text-align: left;
        }

        /* ── BOTTOM SECTION ── */
        .inv-totals-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 15px;
        }

        .inv-inwords-section {
            flex: 1;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            padding-top: 10px;
            padding-right: 20px;
        }

        .inv-totals-section {
            width: 320px;
            min-width: 220px;
        }

        .inv-totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .inv-totals-table td {
            padding: 5px 10px;
            text-align: right;
        }

        .inv-totals-table td:first-child {
            font-weight: bold;
            color: #000;
        }

        .inv-totals-table td:last-child {
            width: 120px;
            color: #000;
        }

        .inv-totals-table tr.grand-total-row td {
            font-weight: bold;
            font-size: 14px;
            border-top: 1.5px solid #000;
            border-bottom: 1.5px solid #000;
            padding: 6px 10px;
        }

        /* ── FOOTER SECTION ── */
        .inv-footer-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 30px;
        }

        .footer-left-terms {
            width: 55%;
            font-size: 12px;
            line-height: 1.5;
        }

        .terms-title {
            font-weight: bold;
            margin-bottom: 5px;
            text-decoration: underline;
        }

        .terms-list {
            padding-left: 0;
            margin: 0;
            list-style: none;
        }

        .terms-list li {
            position: relative;
            padding-left: 12px;
            margin-bottom: 4px;
        }

        .terms-list li::before {
            content: "*";
            position: absolute;
            left: 0;
            top: 2px;
        }

        .footer-right-bank {
            width: 40%;
        }

        .bank-box {
            border: 2px solid #000;
            padding: 10px;
            font-size: 12px;
            line-height: 1.4;
        }

        .bank-support-title {
            font-weight: bold;
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 6px;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .bank-details-content {
            padding-left: 5px;
        }

        /* ── SIGNATURES ── */
        .inv-signatures-section {
            display: flex;
            justify-content: space-between;
            margin-top: 70px;
            margin-bottom: 20px;
        }

        .signature-block {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            border-top: 2.5px solid #000;
            margin-bottom: 6px;
        }

        .signature-label {
            font-size: 13px;
            font-weight: bold;
        }

        .print-btn {
            display: block;
            margin: 20px auto;
            max-width: 200px;
        }

        /* ── PRINT MEDIA ── */
        @media print {
            header, nav, footer, .breadcrumbbar, .print-btn, #digital-chatbot-wrapper { 
                display: none !important; 
            }
            .invoice-contentbar { margin: 0 !important; padding: 5px !important; }
            .invoice-wrapper {
                border: none !important;
                padding: 10px !important;
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }
        }

        /* ── MOBILE RESPONSIVE ── */
        @media screen and (max-width: 768px) {
            .invoice-contentbar {
                margin: 10px 0 0 0 !important;
                padding: 8px !important;
            }

            .invoice-wrapper {
                padding: 12px !important;
                border-radius: 0;
                width: 100% !important;
                box-sizing: border-box;
            }

            .inv-header {
                flex-direction: column !important;
                gap: 10px;
            }

            .header-logo-left {
                text-align: center;
            }

            .header-logo-left img {
                max-height: 55px !important;
            }

            .header-info-right {
                text-align: center !important;
                font-size: 11px !important;
                width: 100%;
            }

            .head-office-title {
                font-size: 13px !important;
            }

            .customer-info-section {
                flex-direction: column !important;
                gap: 10px;
            }

            .customer-info-left,
            .customer-info-right {
                width: 100% !important;
                align-items: flex-start !important;
            }

            .customer-info-left table,
            .customer-info-right table {
                font-size: 11px !important;
            }

            .customer-info-left td:first-child {
                width: 80px !important;
            }

            .customer-info-right td:first-child {
                width: 70px !important;
            }

            .bill-title-box {
                max-width: 100% !important;
                font-size: 14px !important;
                padding: 5px 10px !important;
            }

            .inv-table {
                font-size: 11px !important;
            }

            .inv-table th,
            .inv-table td {
                padding: 5px 5px !important;
            }

            .inv-totals-container {
                flex-direction: column !important;
                gap: 10px;
            }

            .inv-inwords-section {
                padding-right: 0 !important;
                font-size: 11px !important;
                width: 100%;
            }

            .inv-totals-section {
                width: 100% !important;
                min-width: 0 !important;
            }

            .inv-totals-table {
                font-size: 12px !important;
            }

            .inv-totals-table td {
                padding: 4px 6px !important;
            }

            .inv-footer-section {
                flex-direction: column !important;
                gap: 12px;
            }

            .footer-left-terms,
            .footer-right-bank {
                width: 100% !important;
                font-size: 11px !important;
            }

            .inv-signatures-section {
                margin-top: 40px !important;
                gap: 10px;
            }

            .signature-block {
                width: auto !important;
                flex: 1;
                min-width: 80px;
            }

            .signature-label {
                font-size: 11px !important;
            }

            .print-btn {
                width: 90% !important;
                max-width: none !important;
                margin: 15px auto !important;
                font-size: 14px !important;
                padding: 10px !important;
            }
        }
    </style>
@endpush

@section('invoice')
    @if(empty($quotation))
        <div class="container mt-5">
            <div class="alert alert-danger text-center">
                <h4>Quotation not found or invalid.</h4>
            </div>
        </div>
    @else
    @php
        /* ── Amount in Words ── */
        $ones = ['','One','Two','Three','Four','Five','Six','Seven','Eight','Nine',
                 'Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen',
                 'Seventeen','Eighteen','Nineteen'];
        $tens = ['','','Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];

        $cvtH = function(int $n) use ($ones, $tens): string {
            $r = '';
            if ($n >= 100) { $r .= $ones[intval($n/100)].' Hundred '; $n %= 100; }
            if ($n >= 20)  { $r .= $tens[intval($n/10)].' '; $n %= 10; }
            if ($n > 0)    { $r .= $ones[$n].' '; }
            return $r;
        };

        $num = (int) $quotation->total_amount;
        $inWords = '';
        if ($num == 0) {
            $inWords = 'Zero';
        } else {
            $t = $num;
            if ($t >= 10000000) { $inWords .= $cvtH(intval($t/10000000)).'Crore '; $t %= 10000000; }
            if ($t >= 100000)   { $inWords .= $cvtH(intval($t/100000)).'Lakh '; $t %= 100000; }
            if ($t >= 1000)     { $inWords .= $cvtH(intval($t/1000)).'Thousand '; $t %= 1000; }
            if ($t > 0)         { $inWords .= $cvtH($t); }
        }
        $inWords = strtoupper(trim($inWords)).' TAKA ONLY';

        /* ── Company details ── */
        $branch = App\Models\Branch::where('id', $quotation->branch_id)->first();
        $shopName    = ($branch && !empty($branch->shop_name)) ? $branch->shop_name : (get_setting('com_name')    ?: 'Fast IT');
        $branchAddress = ($branch && !empty($branch->address) && strtolower(trim($branch->address)) !== 'all address') ? $branch->address : null;
        $shopAddress = $branchAddress ?: (get_setting('com_address') ?: 'Suite: 807, Shah Ali Plaza, Mirpur-10, Dhaka-1216.');
        $shopPhone   = ($branch && !empty($branch->phone))  ? $branch->phone  : (get_setting('com_phone')   ?: '01784-159071');
        $shopEmail   = ($branch && !empty($branch->email))  ? $branch->email  : (get_setting('com_email')   ?: 'fastit.com.bd@gmail.com');
        $shopLogo    = !empty(get_setting('system_logo')) ? url('uploads/logo/'.get_setting('system_logo')) : url('backend/images/fastLogo.jpeg');

        $currency = get_setting('com_currency') ?: 'Tk.';
    @endphp

    <div class="invoice-contentbar">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div id="print-area" class="invoice-wrapper">

                    {{-- ══ HEADER ══ --}}
                    <div class="inv-header">
                        <div class="header-logo-left">
                            <img src="{{ $shopLogo }}" alt="logo" style="max-height: 75px; width: auto;">
                        </div>
                        <div class="header-company-center">
                            <p class="company-name">{{ $shopName }}</p>
                        </div>
                        <div class="header-info-right">
                            <div class="head-office-title">Head Office</div>
                            <div class="head-office-details">
                                {{ $shopAddress }}<br>
                                Phone: {{ $shopPhone }}<br>
                                Email: {{ $shopEmail }}
                            </div>
                        </div>
                    </div>

                    {{-- ══ CUSTOMER DETAILS GRID ══ --}}
                    <div class="customer-info-section">
                        <div class="customer-info-left">
                            <table>
                                <tr>
                                    <td>Quotation No</td>
                                    <td>: {{ $quotation->quotation_no }}</td>
                                </tr>
                                <tr>
                                    <td>Customer</td>
                                    <td>: {{ $quotation->customer->name }}</td>
                                </tr>
                                <tr>
                                    <td>Address</td>
                                    <td>: {{ $quotation->customer->address }}</td>
                                </tr>
                                <tr>
                                    <td>Mobile</td>
                                    <td>: {{ $quotation->customer->phone }}</td>
                                </tr>
                                @if(env('APP_AUTOMOBILE') == 'yes')
                                @if(!empty($quotation->vehicle_reg_no))
                                <tr>
                                    <td>Reg No</td>
                                    <td>: <strong>{{ $quotation->vehicle_reg_no }}</strong></td>
                                </tr>
                                @endif
                                @endif
                                @if($quotation->note)
                                <tr>
                                    <td>Subject</td>
                                    <td>: {{ $quotation->note }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                        <div class="customer-info-right">
                            <div class="bill-title-box">Quotation</div>
                            <table>
                                <tr>
                                    <td>Date</td>
                                    <td>: {{ date('Y-m-d', strtotime($quotation->date)) }}</td>
                                </tr>
                                <tr>
                                    <td>Ref No</td>
                                    <td>: </td>
                                </tr>
                                <tr>
                                    <td>Prepared By</td>
                                    <td>: {{ $quotation->user->name }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @php
                        $regularItems = [];
                        $serviceItems = [];
                        foreach ($quotation->quotationItems as $item) {
                            if ($item->product?->is_service == 1) {
                                $serviceItems[] = $item;
                            } else {
                                $regularItems[] = $item;
                            }
                        }
                        $showSectionHeaders = (count($regularItems) > 0 && count($serviceItems) > 0);
                    @endphp
                    <table class="inv-table">
                        <thead>
                            <tr>
                                <th class="desc-col">Description</th>
                                <th width="15%" style="text-align: right;">Qty</th>
                                <th width="15%" style="text-align: right;">Unit</th>
                                <th width="15%" style="text-align: right;">Rate</th>
                                <th width="15%" style="text-align: right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($regularItems) > 0)
                                @if($showSectionHeaders)
                                    <tr style="background-color: #eaeaea; font-weight: bold;">
                                        <td colspan="5" style="text-align: left; padding: 6px 10px; font-size: 12px; text-transform: uppercase;">Items</td>
                                    </tr>
                                @endif
                                @foreach ($regularItems as $item)
                                    @php
                                        $product = $item->product;
                                        $qtyStr = number_format($item->main_qty, 2, '.', '');
                                    @endphp
                                    <tr>
                                        <td class="desc-col">
                                            <strong>{{ $product?->name }}</strong>
                                            @if(!empty($product?->description))
                                                <div style="font-size: 12px; color: #000; margin-top: 4px;">
                                                    {!! $product->description !!}
                                                </div>
                                            @endif
                                            @if($item->product_variation_id != null)
                                                <div style="font-size: 11px; color: #555; margin-top: 2px;">
                                                    ({{ $item->product_variation?->size?->size ?? '' }}-{{ $item->product_variation?->color?->color ?? '' }})
                                                </div>
                                            @endif
                                            @if(!empty($item->imei))
                                                <div style="font-size: 11px; color: #555; margin-top: 2px;">
                                                    <strong>IMEI:</strong> {{ $item->imei }}
                                                </div>
                                            @endif
                                            @if($item->warranty_value)
                                                <div style="font-size: 11px; color: #555; margin-top: 2px;">
                                                    <strong>Warranty:</strong> {{ $item->warranty_value }} {{ $item->warranty_unit }}
                                                </div>
                                            @endif
                                        </td>
                                        <td style="text-align: right;">{{ $qtyStr }}</td>
                                        <td style="text-align: right;">{{ $item->product_unit }}</td>
                                        <td style="text-align: right;">{{ number_format($item->rate, 2) }}</td>
                                        <td style="text-align: right;">{{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            @endif

                            @if(count($serviceItems) > 0)
                                @if($showSectionHeaders)
                                    <tr style="background-color: #eaeaea; font-weight: bold;">
                                        <td colspan="5" style="text-align: left; padding: 6px 10px; font-size: 12px; text-transform: uppercase;">Services</td>
                                    </tr>
                                @endif
                                @foreach ($serviceItems as $item)
                                    @php
                                        $product = $item->product;
                                        $qtyStr = number_format($item->main_qty, 2, '.', '');
                                    @endphp
                                    <tr>
                                        <td class="desc-col">
                                            <strong>{{ $product?->name }}</strong>
                                            @if(!empty($product?->description))
                                                <div style="font-size: 12px; color: #000; margin-top: 4px;">
                                                    {!! $product->description !!}
                                                </div>
                                            @endif
                                            @if($item->product_variation_id != null)
                                                <div style="font-size: 11px; color: #555; margin-top: 2px;">
                                                    ({{ $item->product_variation?->size?->size ?? '' }}-{{ $item->product_variation?->color?->color ?? '' }})
                                                </div>
                                            @endif
                                            @if(!empty($item->imei))
                                                <div style="font-size: 11px; color: #555; margin-top: 2px;">
                                                    <strong>IMEI:</strong> {{ $item->imei }}
                                                </div>
                                            @endif
                                            @if($item->warranty_value)
                                                <div style="font-size: 11px; color: #555; margin-top: 2px;">
                                                    <strong>Warranty:</strong> {{ $item->warranty_value }} {{ $item->warranty_unit }}
                                                </div>
                                            @endif
                                        </td>
                                        <td style="text-align: right;">{{ $qtyStr }}</td>
                                        <td style="text-align: right;">{{ $item->product_unit }}</td>
                                        <td style="text-align: right;">{{ number_format($item->rate, 2) }}</td>
                                        <td style="text-align: right;">{{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                    {{-- ══ INWORDS + TOTALS ══ --}}
                    <div class="inv-totals-container">
                        <div class="inv-inwords-section">
                            IN WORD : {{ $inWords }}
                        </div>
                        <div class="inv-totals-section">
                            <table class="inv-totals-table">
                                <tr>
                                    <td>Total</td>
                                    <td>{{ number_format($quotation->estimated_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Discount</td>
                                    <td>{{ number_format($quotation->discount_amount, 2) }}</td>
                                </tr>
                                @if($quotation->vat_amount > 0)
                                    <tr>
                                        <td>VAT</td>
                                        <td>{{ number_format($quotation->vat_amount, 2) }}</td>
                                    </tr>
                                @endif
                                @if($quotation->delivery_charge > 0)
                                    <tr>
                                        <td>Delivery Charge</td>
                                        <td>{{ number_format($quotation->delivery_charge, 2) }}</td>
                                    </tr>
                                @endif
                                <tr class="grand-total-row">
                                    <td>Grand Total</td>
                                    <td>{{ number_format($quotation->total_amount, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- ══ SIGNATURES ══ --}}
                    <div class="inv-signatures-section">
                        <div class="signature-block">
                            <div class="signature-line"></div>
                            <div class="signature-label">Customer Signature</div>
                        </div>
                        <div class="signature-block">
                            <div class="signature-line"></div>
                            <div class="signature-label">Authorized Signature</div>
                        </div>
                    </div>

                </div>{{-- /invoice-wrapper --}}

                <button onclick="window.print()" class="btn btn-primary btn-block print-btn print_hidden mt-3">
                    <i class="feather icon-printer mr-2"></i>Print Quotation
                </button>

            </div>
        </div>
    </div>
    @endif
@endsection

@push('js')
    <script>
        @if(env('APP_AUTO_PRINT') == 'yes')
        $(document).ready(function() {
            window.print();
        });
        @endif
    </script>
@endpush
