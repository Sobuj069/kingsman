@extends('backend.layouts.master')
@section('page-title', 'Invoice Print')
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
            align-items: flex-start;
            padding-bottom: 15px;
            border-bottom: 2px solid #000;
        }

        .header-logo-left {
            flex: 0 0 30%;
        }

        .header-info-right {
            flex: 0 0 70%;
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

        .inv-totals-table tr.total-due-row td {
            font-weight: bold;
            border-top: 1px solid #000;
            padding-top: 6px;
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

        @media print {
            .print-btn { display: none !important; }

            .invoice-wrapper {
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
            }
        }
    </style>
@endpush

@section('invoice')
    @if(empty($invoice))
        <div class="container mt-5">
            <div class="alert alert-danger text-center">
                <h4>Invoice not found or invalid.</h4>
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

        $num = (int) $invoice->total_amount;
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

        /* ── Branch info ── */
        $branch = null;
        if (get_setting('inv_details') == 'branch') {
            $branch = App\Models\Branch::where('id', $invoice->branch_id)->first();
        }
        $shopName    = $branch ? $branch->shop_name    : (get_setting('com_name')    ?: 'Fast IT');
        $shopAddress = $branch ? $branch->address      : (get_setting('com_address') ?: 'Suite: 807, Shah Ali Plaza, Mirpur-10, Dhaka-1216.');
        $shopPhone   = $branch ? $branch->phone        : (get_setting('com_phone')   ?: '01784-159071');
        $shopEmail   = $branch ? $branch->email        : (get_setting('com_email')   ?: 'fastit.com.bd@gmail.com');
        $shopLogo    = ($branch && !empty($branch->logo)) ? asset('uploads/logo/'.$branch->logo) : (!empty(get_setting('system_logo')) ? url('uploads/logo/'.get_setting('system_logo')) : url('backend/images/fastLogo.jpeg'));

        $currency = get_setting('com_currency') ?: 'Tk.';
    @endphp

    <div class="invoice-contentbar">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div id="print-area" class="invoice-wrapper">

                    {{-- ══ HEADER ══ --}}
                    <div class="inv-header">
                        <div class="header-logo-left">
                            @if (get_setting('inv_logo') == 'logo')
                                <img src="{{ $shopLogo }}" alt="logo" style="max-height: 75px; width: auto;">
                            @elseif (get_setting('inv_logo') == 'name')
                                <h2 style="font-weight: bold; margin: 0; font-size: 24px; color: #000;">{{ $shopName }}</h2>
                            @else
                                <img src="{{ $shopLogo }}" alt="logo" style="max-height: 75px; width: auto; display: block; margin-bottom: 5px;">
                                <h2 style="font-weight: bold; margin: 0; font-size: 22px; color: #000;">{{ $shopName }}</h2>
                            @endif
                        </div>
                        <div class="header-info-right" style="display: flex; gap: 30px; text-align: left; justify-content: flex-end;">
                            @if($branch)
                                <div>
                                    <div class="head-office-title" style="text-align: left;">Head Office</div>
                                    <div class="head-office-details" style="text-align: left;">
                                        {!! nl2br(e(get_setting('com_address') ?: 'Suite: 807, Shah Ali Plaza, Mirpur-10, Dhaka-1216.')) !!}<br>
                                        Phone: {{ get_setting('com_phone') ?: '01784-159071' }}<br>
                                        Email: {{ get_setting('com_email') ?: 'fastit.com.bd@gmail.com' }}
                                    </div>
                                </div>
                                <div>
                                    <div class="head-office-title" style="text-align: left;">Branch Office</div>
                                    <div class="head-office-details" style="text-align: left;">
                                        {!! nl2br(e($branch->address)) !!}<br>
                                        Phone: {{ $branch->phone }}<br>
                                        Email: {{ $branch->email }}
                                    </div>
                                </div>
                            @else
                                <div style="text-align: right;">
                                    <div class="head-office-title">Head Office</div>
                                    <div class="head-office-details">
                                        {!! nl2br(e($shopAddress)) !!}<br>
                                        Phone: {{ $shopPhone }}<br>
                                        Email: {{ $shopEmail }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- ══ CUSTOMER DETAILS GRID ══ --}}
                    <div class="customer-info-section">
                        <div class="customer-info-left">
                            <table>
                                <tr>
                                    <td>Invoice No</td>
                                    <td>: {{ $invoice->invoice_no }} @if($invoice->is_edited == 1 || $invoice->edit_status == 'edited') <span style="color:#0284c7;font-weight:bold;font-size:12px;">(Edited)</span> @elseif($invoice->is_edited == 2 || $invoice->edit_status == 'exchange') <span style="color:#2563eb;font-weight:bold;font-size:12px;">(Exchange)</span> @endif</td>
                                </tr>
                                <tr>
                                    <td>Customer</td>
                                    <td>: {{ $invoice->customer->name }}</td>
                                </tr>
                                <tr>
                                    <td>Address</td>
                                    <td>: {{ $invoice->customer->address }}</td>
                                </tr>
                                <tr>
                                    <td>Mobile</td>
                                    <td>: {{ $invoice->customer->phone }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="customer-info-right">
                            <div class="bill-title-box">Invoice / Bill</div>
                            <table>
                                <tr>
                                    <td>Date</td>
                                    <td>: {{ date('Y-m-d', strtotime($invoice->date)) }}</td>
                                </tr>
                                @if(env('APP_REF_INV') == 'yes')
                                <tr>
                                    <td>Ref Inv</td>
                                    <td>: {{ $invoice->ref_no }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td>Sold By</td>
                                    <td>: {{ $invoice->user->name }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @php
                        $hasProductDiscount = $invoice->invoiceItems->contains(function($i) {
                            return !empty($i->product_discount) && ((float)$i->product_discount > 0 || str_contains($i->product_discount, '%'));
                        });
                        $printItems = $invoice->invoiceItems->groupBy(function($it) {
                            return $it->product_id . '_' . ($it->product_variation_id ?? 0);
                        });
                    @endphp
                    {{-- ══ PRODUCT TABLE ══ --}}
                    <table class="inv-table">
                        <thead>
                            <tr>
                                <th class="desc-col">Item</th>
                                <th width="{{ $hasProductDiscount ? '12%' : '15%' }}" style="text-align: right;">Qty</th>
                                <th width="{{ $hasProductDiscount ? '14%' : '15%' }}" style="text-align: right;">Rate</th>
                                @if($hasProductDiscount)
                                    <th width="14%" style="text-align: right;">Discount</th>
                                @endif
                                <th width="15%" style="text-align: right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($printItems as $key => $group)
                                @php
                                    $item = $group->first();
                                    $comb_main = $group->sum('actual_main');
                                    $comb_sub = $group->sum('actual_sub');
                                    $comb_subtotal = $group->sum('subtotal');
                                    $product = App\Models\Product::where('id', $item->product_id)->with('unit.related_unit')->first();
                                    if ($product && $product->unit) {
                                        if ($product->unit->name == 'pcs' || $product->unit->name == 'Pcs') {
                                            $qtyStr = number_format($comb_main, 2, '.', '');
                                        } elseif ($product->unit->related_unit == null) {
                                            $qtyStr = number_format($comb_main, 2, '.', '') . ' ' . $product->unit->name;
                                        } else {
                                            $qtyStr = number_format($comb_main, 2, '.', '').' '.$product->unit->name.' '.number_format($comb_sub, 2, '.', '').' '.$product->unit->related_unit->name;
                                        }
                                    } else {
                                        $qtyStr = number_format($comb_main, 2, '.', '');
                                    }
                                @endphp
                                <tr>
                                    <td class="desc-col">
                                        {{ $item->product?->name }}
                                        @if($item->is_return == 1)
                                            <span style="background:#dc3545;color:#fff;font-size:10px;padding:1px 4px;border-radius:3px;">Return</span>
                                        @endif
                                        @if(env('APP_SC') == 'yes' && $item->product_variation_id != null)
                                            ({{ $item->product_variation?->size?->size }}-{{ $item->product_variation?->color?->color }})
                                        @endif
                                        @if(env('APP_IMEI') == 'yes' && !empty($item->imei))
                                            <br><small><strong>IMEI:</strong> {{ str_replace("\n", ", ", $item->imei) }}</small>
                                        @endif
                                        @if($item->warranty_value)
                                            <br><small><strong>Warranty:</strong> {{ $item->warranty_value }} {{ $item->warranty_unit }}{{ $item->warranty_value > 1 ? 's' : '' }}</small>
                                        @endif
                                    </td>
                                    <td style="text-align: right;">{{ $qtyStr }}</td>
                                    <td style="text-align: right;">{{ number_format($item->rate, 2) }}</td>
                                    @if($hasProductDiscount)
                                        <td style="text-align: right;">
                                            @if(!empty($item->product_discount) && ((float)$item->product_discount > 0 || str_contains($item->product_discount, '%')))
                                                {{ str_contains($item->product_discount, '%') ? $item->product_discount : number_format((float)$item->product_discount, 2) }}
                                            @else
                                                0.00
                                            @endif
                                        </td>
                                    @endif
                                    <td style="text-align: right;">{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- ══ INWORDS + TOTALS ══ --}}
                    <div class="inv-totals-container">
                        <div class="inv-inwords-section">
                            IN WORD : {{ $inWords }}
                            @if($invoice->note)
                                <br><br><span style="text-transform: none; font-style: italic; font-weight: normal;">Note: {{ $invoice->note }}</span>
                            @endif

                            @if (env('APP_LOYALTY') == 'yes' && $invoice->customer_id != 1 && ((float)($invoice->inv_point ?? 0) > 0 || (float)($invoice->pay_point ?? 0) > 0 || (float)($invoice->total_point ?? $invoice->customer?->total_point ?? 0) > 0))
                                @php
                                    $usedPts = (float)($invoice->pay_point ?? 0);
                                    $earnedPts = (float)($invoice->inv_point ?? 0);
                                    $remPts = (float)($invoice->total_point ?? $invoice->customer?->total_point ?? 0);
                                    $prevPts = max(0, $remPts + $usedPts - $earnedPts);
                                    $usedTk = round($usedPts * 0.75, 2);
                                    $remTk = round($remPts * 0.75, 2);
                                @endphp
                                <div style="border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 6px; margin-top: 15px; padding: 8px 12px; font-size: 11px; width: 100%; box-sizing: border-box;">
                                    <div style="font-weight: bold; text-transform: uppercase; font-size: 11px; color: #1e293b; margin-bottom: 4px; border-bottom: 1px solid #e2e8f0; padding-bottom: 3px;">
                                        ⭐ {{ __('REWARD POINTS SUMMARY') }}
                                    </div>
                                    <table style="width: 100%; font-size: 11px; border: none; margin: 0;">
                                        <tr>
                                            <td style="padding: 2px 0; border: none; text-transform: none;">Previous Points:</td>
                                            <td style="padding: 2px 0; text-align: right; font-weight: bold; border: none;">{{ number_format($prevPts, 0) }} pts</td>
                                        </tr>
                                        @if($usedPts > 0)
                                        <tr>
                                            <td style="padding: 2px 0; border: none; text-transform: none;">Points Used (Redeemed):</td>
                                            <td style="padding: 2px 0; text-align: right; font-weight: bold; color: #dc2626; border: none;">-{{ number_format($usedPts, 0) }} pts (৳{{ number_format($usedTk, 2) }})</td>
                                        </tr>
                                        @endif
                                        @if($earnedPts > 0)
                                        <tr>
                                            <td style="padding: 2px 0; border: none; text-transform: none;">Points Earned:</td>
                                            <td style="padding: 2px 0; text-align: right; font-weight: bold; color: #16a34a; border: none;">+{{ number_format($earnedPts, 0) }} pts</td>
                                        </tr>
                                        @endif
                                        <tr style="border-top: 1px dotted #cbd5e1;">
                                            <td style="padding: 3px 0 0 0; font-weight: bold; border: none; text-transform: none;">Remaining Points Balance:</td>
                                            <td style="padding: 3px 0 0 0; text-align: right; font-weight: bold; color: #2563eb; border: none;">{{ number_format($remPts, 0) }} pts (৳{{ number_format($remTk, 2) }})</td>
                                        </tr>
                                    </table>
                                </div>
                            @endif
                        </div>
                        <div class="inv-totals-section">
                            <table class="inv-totals-table">
                                <tr>
                                    <td>Total</td>
                                    <td>{{ number_format($invoice->estimated_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Discount</td>
                                    <td>{{ number_format($invoice->discount_amount, 2) }}</td>
                                </tr>
                                <tr class="grand-total-row">
                                    <td>Grand Total</td>
                                    <td>{{ number_format($invoice->total_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Paid</td>
                                    <td>{{ number_format($invoice->total_paid, 2) }}</td>
                                </tr>
                                @php
                                    $invoicePayments = \App\Models\BankTransaction::where('invoice_id', $invoice->id)->with('bank_account')->orderBy('date', 'asc')->get();
                                @endphp
                                @if($invoicePayments->count() > 0)
                                    @foreach($invoicePayments as $pay)
                                        <tr style="font-size: 11px; color: #444;">
                                            <td style="text-align: right; font-style: italic; padding-right: 5px;">
                                                - Paid ({{ $pay->date ?: date('Y-m-d', strtotime($invoice->created_at)) }}) [{{ $pay->bank_account?->bank_name ?? 'Cash' }}]:
                                            </td>
                                            <td>
                                                {{ $pay->trans_type == 'withdraw' ? '-' : '' }}{{ number_format($pay->amount, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                <tr>
                                    <td>Due</td>
                                    <td>{{ number_format($invoice->total_due, 2) }}</td>
                                </tr>
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
                                    <td>Previous Due</td>
                                    <td>{{ number_format($displayPreviousDue, 2) }}</td>
                                </tr>
                                <tr class="total-due-row">
                                    <td>Total Due</td>
                                    <td>{{ number_format($invoice->total_due + $displayPreviousDue, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                                        @if ($invoice->installment)
                        <div style="margin-top: 30px; border-top: 2px solid #000; padding-top: 15px;">
                            <h4 style="font-weight: bold; font-size: 15px; text-transform: uppercase; margin-bottom: 10px; border-bottom: 1px solid #000; padding-bottom: 5px;">Installment Sale Agreement</h4>
                            <table style="width: 100%; font-size: 13px; margin-bottom: 15px; border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 4px 0; font-weight: bold; width: 25%;">Agreement Status:</td>
                                    <td style="padding: 4px 0; width: 25%;">{{ strtoupper($invoice->installment->status) }}</td>
                                    <td style="padding: 4px 0; font-weight: bold; width: 25%;">Advance Pay:</td>
                                    <td style="padding: 4px 0; width: 25%;">Tk {{ number_format($invoice->installment->advance_pay, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 0; font-weight: bold;">Principal Remaining:</td>
                                    <td>Tk {{ number_format($invoice->installment->remaining_amount, 2) }}</td>
                                    <td style="padding: 4px 0; font-weight: bold;">Interest:</td>
                                    <td>{{ $invoice->installment->interest_percentage }}% (Tk {{ number_format($invoice->installment->interest_amount, 2) }})</td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 0; font-weight: bold;">Total with Interest:</td>
                                    <td style="color: red; font-weight: bold;">Tk {{ number_format($invoice->installment->total_with_interest, 2) }}</td>
                                    <td style="padding: 4px 0; font-weight: bold;">Per Installment:</td>
                                    <td style="color: blue; font-weight: bold;">Tk {{ number_format($invoice->installment->per_installment_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 4px 0; font-weight: bold;">Installments Count:</td>
                                    <td>{{ $invoice->installment->total_installments }} (Every {{ $invoice->installment->interval_days }} days)</td>
                                    <td style="padding: 4px 0; font-weight: bold;">Due Range:</td>
                                    <td>{{ $invoice->installment->first_due_date }} to {{ $invoice->installment->last_due_date }}</td>
                                </tr>
                            </table>

                            <h4 style="font-weight: bold; font-size: 14px; text-transform: uppercase; margin-bottom: 8px; border-bottom: 1px solid #000; padding-bottom: 3px;">Payment Schedule</h4>
                            <table style="width: 100%; border-collapse: collapse; font-size: 12px;" class="table-bordered text-center">
                                <thead>
                                    <tr style="background-color: #f8f9fa;">
                                        <th style="border: 1px solid #000; padding: 6px; font-weight: bold;">Installment #</th>
                                        <th style="border: 1px solid #000; padding: 6px; font-weight: bold;">Due Date</th>
                                        <th style="border: 1px solid #000; padding: 6px; font-weight: bold;">Scheduled Amount</th>
                                        <th style="border: 1px solid #000; padding: 6px; font-weight: bold;">Paid Amount</th>
                                        <th style="border: 1px solid #000; padding: 6px; font-weight: bold;">Paid Date</th>
                                        <th style="border: 1px solid #000; padding: 6px; font-weight: bold;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoice->installment->schedules as $sched)
                                        <tr>
                                            <td style="border: 1px solid #000; padding: 6px; font-weight: bold;">#{{ $sched->installment_no }}</td>
                                            <td style="border: 1px solid #000; padding: 6px;">{{ $sched->due_date }}</td>
                                            <td style="border: 1px solid #000; padding: 6px;">Tk {{ number_format($sched->amount, 2) }}</td>
                                            <td style="border: 1px solid #000; padding: 6px;">{{ $sched->status == 'paid' ? 'Tk '.number_format($sched->paid_amount, 2) : '-' }}</td>
                                            <td style="border: 1px solid #000; padding: 6px;">{{ $sched->paid_date ?? '-' }}</td>
                                            <td style="border: 1px solid #000; padding: 6px; font-weight: bold;">{{ strtoupper($sched->status) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

{{-- ══ TERMS & BANK DETAILS ══ --}}
                    <div class="inv-footer-section" style="display: none;">
                        <div class="footer-left-terms">
                            <div class="terms-title">Terms & Conditions:</div>
                            <ul class="terms-list">
                                <li>Goods sold and once received or accepted by the customer are not returnable</li>
                                <li>Warranty will void of all Products if sticker is removed</li>
                                <li>No warranty for Printhead, ribbon and kind of physical damage</li>
                            </ul>
                        </div>
                        <div class="footer-right-bank">
                            <div class="bank-box">
                                <div class="bank-support-title">Support: 01901166585</div>
                                <div class="bank-details-content">
                                    <strong>Bank Name:</strong> NRB BANK<br>
                                    <strong>A/C Name:</strong> FAST IT<br>
                                    <strong>A/C No:</strong> 1212010039418<br>
                                    <strong>Branch:</strong> Mirpur Branch
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ══ QR CODE & SIGNATURES ══ --}}
                    @if(is_invoice_qr_enabled() && env('APP_ZATCA') === 'yes')
                        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 30px;">
                            @php
                                $sellerName = $shopName ?? (get_setting('com_name') ?: 'Fast IT');
                                $vatNumber  = get_setting('vat_no') ?: (get_setting('com_vat') ?: '300000000000003');
                                $zatcaQrBase64 = zatca_qr_code(
                                    $sellerName,
                                    $vatNumber,
                                    $invoice->created_at ?? $invoice->date ?? now(),
                                    $invoice->total_amount ?? 0,
                                    $invoice->vat_amount ?? 0
                                );
                            @endphp
                            <div style="text-align: center;">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data={{ urlencode($zatcaQrBase64) }}" 
                                     alt="ZATCA QR Code" 
                                     style="width: 110px; height: 110px; border: 1px solid #ddd; padding: 3px;" />
                                <div style="font-size: 10px; font-weight: bold; margin-top: 4px; font-family: monospace;">ZATCA E-Invoice QR</div>
                            </div>

                            <div class="inv-signatures-section" style="flex: 1; display: flex; justify-content: space-around; margin-top: 0;">
                                <div class="signature-block">
                                    <div class="signature-line"></div>
                                    <div class="signature-label">Customer Signature</div>
                                </div>
                                <div class="signature-block">
                                    <div class="signature-line"></div>
                                    <div class="signature-label">Authorized Signature</div>
                                </div>
                            </div>
                        </div>
                    @elseif(is_invoice_qr_enabled())
                        @php
                            $qrInvoiceData = (string) ($invoice->invoice_no ?: $invoice->id);
                        @endphp
                        <div class="inv-signatures-section" style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 35px; margin-bottom: 20px;">
                            <div class="signature-block">
                                <div class="signature-line"></div>
                                <div class="signature-label">Customer Signature</div>
                            </div>

                            <div style="text-align: center; margin: 0 15px; align-self: center;">
                                <div style="display: inline-block; padding: 6px; background: #fff; border: 1.5px solid #1e293b; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.06);">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=130x130&data={{ urlencode($qrInvoiceData) }}" 
                                         alt="Invoice QR Code" 
                                         style="width: 110px; height: 110px; display: block;" />
                                </div>
                            </div>

                            <div class="signature-block">
                                <div class="signature-line"></div>
                                <div class="signature-label">Authorized Signature</div>
                            </div>
                        </div>
                    @else
                        <div class="inv-signatures-section" style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 50px; margin-bottom: 20px;">
                            <div class="signature-block">
                                <div class="signature-line"></div>
                                <div class="signature-label">Customer Signature</div>
                            </div>
                            <div class="signature-block">
                                <div class="signature-line"></div>
                                <div class="signature-label">Authorized Signature</div>
                            </div>
                        </div>
                    @endif

                </div>{{-- /invoice-wrapper --}}

                <button class="btn btn-secondary print-btn print_hidden" onclick="print_receipt('print-area')">
                    <i class="fa fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('js')
    <script>
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
