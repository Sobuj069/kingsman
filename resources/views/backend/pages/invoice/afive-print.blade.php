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
        address {
            margin-bottom: 0px;
        }
        .invoice-header address {
            text-align: center;
            font-size: 14px;
        }
        .logo_n_name {
            text-align: center;
        }
        .logo_n_name img {
            max-width: 50%;
        }
        .logo_n_name {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .logo_n_name img {
            display: block;
            max-width: 100%;
            height: auto;
            margin: 0 auto;
        }
        .logo_n_name h2 {
            text-align: center;
            margin-top: 5px;
        }
        .footer-note {
            margin-top: 20px;
        }

        /* ===== টেবিলের প্যাডিং কমানো ===== */
        .order-details th,
        .order-details td {
            padding: 4px 6px !important;
            font-size: 13px;
        }

        /* ===== সারাংশ অংশ ডান দিকে সারিবদ্ধ ===== */
        .summary-section {
            margin-top: 10px;
            padding: 0 5px;
            text-align: right;
            font-size: 14px;
        }
        .summary-section p {
            margin: 2px 0;
            border: none !important;
        }
        .summary-section strong {
            font-weight: 700;
            margin-right: 10px;
        }

        @media print {
            @page {
                size: A5;
                margin: 10mm;
            }
            html, body {
                width: 148mm;
                height: 210mm;
                font-size: 12px;
            }
            .print_hidden {
                display: none !important;
            }
            .card.print {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100%;
            }
            .invoice-contentbar {
                margin: 0 !important;
                padding: 0 !important;
            }
            .order-details th,
            .order-details td {
                padding: 3px 4px !important;
                font-size: 11px;
            }
            .order-details th,
            .order-details td,
            .order-details .product-total-row td {
                border: 1px solid #000 !important;
            }
            .summary-section p {
                border: none !important;
                font-size: 12px;
            }
            #print-area > h2 {
                font-size: 28px !important;
                font-weight: 700 !important;
                text-align: center !important;
                color: #000 !important;
                margin: 0 0 5px 0 !important;
                display: block !important;
            }
        }
    </style>
@endpush

@php
    // ========== Helper function for formatting quantity ==========
    if (!function_exists('format_qty')) {
        function format_qty($value) {
            if (!is_numeric($value)) return $value;
            if (floor($value) == $value) {
                return (int)$value;
            }
            return rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');
        }
    }
@endphp

@section('invoice')
    <div class="invoice-contentbar">
        <div class="row">
            <div class="col-md-12">
                <div class="row justify-content-center">
                    <div class="col-md-7 card card-body print">
                        <div id="print-area">
                            <div class="invoice-header row">
                                @if (get_setting('inv_details') == 'branch')
                                    @php
                                        $branch = App\Models\Branch::where('id', $invoice->branch_id)->first();
                                    @endphp
                                    <address class="col-12">
                                        <div class="logo_n_name" style="text-align:center;">
                                            @if (get_setting('inv_logo') == 'logo')
                                                <img src="{{ asset('uploads/logo/' . $branch->logo) }}" style="width:150px; height:50px; display:block; margin:0 auto;" alt="logo">
                                            @elseif (get_setting('inv_logo') == 'name')
                                                <h2 style="font-weight: bold; margin-bottom:0; text-align:center;">{{ $branch->shop_name }}</h2>
                                            @else
                                                <img src="{{ asset('uploads/logo/' . $branch->logo) }}" style="width:150px; height:50px; display:block; margin:0 auto;" alt="logo">
                                                <h2 style="font-weight: bold; margin-bottom:0; text-align:center;">{{ $branch->shop_name }}</h2>
                                            @endif
                                        </div>
                                        {{ $branch->address }}<br>
                                        Phone : {{ $branch->phone }} Email : {{ $branch->email }}<br>
                                    </address>
                                @else
                                    <address class="col-12">
                                        <div class="logo_n_name">
                                            @if (get_setting('inv_logo') == 'logo')
                                                <img src="{{ !empty(get_setting('system_logo')) ? url('uploads/logo/' . get_setting('system_logo')) : url('backend/images/fastLogo.jpeg') }}" style="width:150px; height:50px;" alt="logo">
                                            @elseif (get_setting('inv_logo') == 'name')
                                                <h2 style="font-weight: bold; margin-bottom:0;">{{ empty(get_setting('com_name')) ? 'Fast IT' : get_setting('com_name') }}</h2>
                                            @else
                                                <img src="{{ !empty(get_setting('system_logo')) ? url('uploads/logo/' . get_setting('system_logo')) : url('backend/images/fastLogo.jpeg') }}" style="width:150px; height:50px;" alt="logo">
                                                <h2 style="font-weight: bold; margin-bottom:0;">{{ empty(get_setting('com_name')) ? 'Fast IT' : get_setting('com_name') }}</h2>
                                            @endif
                                        </div>
                                        {{ empty(get_setting('com_address')) ? 'Suite: 807,Shah Ali Plaza, Mirpur-10, Dhaka-1216.' : get_setting('com_address') }}<br>
                                        Phone : {{ empty(get_setting('com_phone')) ? '01784-159071' : get_setting('com_phone') }} Email : {{ empty(get_setting('com_email')) ? 'fastitbd00@gamil.com' : get_setting('com_email') }}<br>
                                    </address>
                                @endif
                            </div>

                            <h2 style="font-weight: bold; margin-bottom:0;text-align:center;font-size:28px;">Invoice</h2>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin: 5px 0; font-size: 14px; font-weight: 500;">
                                <span><strong>Invoice ID:</strong> {{ $invoice->invoice_no }} @if($invoice->is_edited == 1 || $invoice->edit_status == 'edited') (Edited) @elseif($invoice->is_edited == 2 || $invoice->edit_status == 'exchange') (Exchange) @endif</span>
                                <span><strong>Date & Time:</strong> {{ $invoice->created_at->format('d/m/Y h:i A') }}</span>
                            </div>

                            <!-- প্রোডাক্ট টেবিল (শুধু প্রোডাক্ট ও তার মোট পর্যন্ত) -->
                            <table class="table table-bordered order-details text-black">
                                <tr>
                                    <th>SL</th>
                                    <th>Product</th>
                                    <th>MRP</th>
                                    <th>Qty</th>
                                    <th>Total Value</th>
                                    <th>Total Disc</th>
                                    <th>Net Pay</th>
                                </tr>
                                @php
                                    $total_qty = 0;
                                    $total_value = 0;
                                    $total_discount = 0;
                                    $total_net_pay = 0;
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
                                        $product = App\Models\Product::where('id', $item->product_id)->with('unit.related_unit')->first();
                                        if ($product->is_service == 0) {
                                            if ($product->unit->related_unit == null) {
                                                $qty = format_qty($comb_main) . ' ' . $product->unit->name;
                                            } else {
                                                $qty = format_qty($comb_main) . ' ' . $product->unit->name . ' ' . format_qty($comb_sub) . ' ' . $product->unit->related_unit->name;
                                            }
                                        } else {
                                            $qty = format_qty($comb_main) . ' pcs';
                                        }
                                        $total_qty += (float)$comb_main;
                                        $total_value += (float)($comb_main * $item->rate);
                                        if (is_numeric($item->product_discount)) {
                                            $total_discount += (float)$item->product_discount;
                                        }
                                        $total_net_pay += (float)$comb_subtotal;
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td style="width:30%;">
                                            {{ $item->product?->name }}
                                            @if($item->is_return == 1) <span class="badge bg-danger">Return</span> @endif
                                            @if(env('APP_SC')=='yes' && $item->product_variation_id != null)
                                                ({{ $item->product_variation?->size?->size }}-{{ $item->product_variation?->color?->color }})
                                            @endif
                                            @if($item->product_discount > 0)
                                                <br><small><strong>Discount:</strong> 
                                                @if(str_contains($item->product_discount, '%')) {{ $item->product_discount }} @else {{ number_format((float)$item->product_discount, 2) }} {{ empty(get_setting('com_currency')) ? '' : get_setting('com_currency') }} @endif
                                                </small>
                                            @endif
                                        </td>
                                        <td>{{ $item->rate }}</td>
                                        <td><small>{{ $qty }}</small></td>
                                        <td>{{ number_format($item->main_qty * $item->rate, 2) }}</td>
                                        <td>{{ $item->product_discount }}</td>
                                        <td>{{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                                <!-- প্রোডাক্টের মোট (বর্ডার থাকবে) -->
                                <tr class="product-total-row" style="font-weight:700; background:#f2f2f2;">
                                    <td colspan="2" class="text-right">Total :</td>
                                    <td></td>
                                    <td>{{ format_qty($total_qty) }}</td>
                                    <td>{{ number_format($total_value, 2) }}</td>
                                    <td>{{ number_format($total_discount, 2) }}</td>
                                    <td>{{ number_format($total_net_pay, 2) }}</td>
                                </tr>
                            </table>

                            <!-- সারাংশ অংশ (টেবিলের বাইরে, ডান দিকে সারিবদ্ধ) -->
                            <div class="summary-section">
                                @php
                                    $cash = App\Models\BankTransaction::where('invoice_id', $invoice->id)->get();
                                @endphp
                                <p><strong>Total Value :</strong> {{ $invoice->estimated_amount }}</p>
                                @foreach($cash as $bank)
                                    <p><strong>{{ $bank->bank_account?->bank_name ?? 'Cash' }} ({{ $bank->date ?: date('Y-m-d', strtotime($invoice->created_at)) }}) :</strong> {{ $bank->trans_type == 'withdraw' ? '-' : '' }}{{ number_format($bank->amount, 2) }}</p>
                                @endforeach
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
                                <p><strong>Due :</strong> {{ number_format($invoice->total_due, 2) }}</p>
                                <p><strong>Previous Due :</strong> {{ number_format($displayPreviousDue, 2) }}</p>
                                <p><strong>Total Due :</strong> {{ number_format($invoice->total_due + $displayPreviousDue, 2) }}</p>
                            </div>

                            @if (env('APP_LOYALTY') == 'yes' && $invoice->customer_id != 1 && ((float)($invoice->inv_point ?? 0) > 0 || (float)($invoice->pay_point ?? 0) > 0 || (float)($invoice->total_point ?? $invoice->customer?->total_point ?? 0) > 0))
                                @php
                                    $usedPts = (float)($invoice->pay_point ?? 0);
                                    $earnedPts = (float)($invoice->inv_point ?? 0);
                                    $remPts = (float)($invoice->total_point ?? $invoice->customer?->total_point ?? 0);
                                    $prevPts = max(0, $remPts + $usedPts - $earnedPts);
                                    $usedTk = round($usedPts * 0.75, 2);
                                    $remTk = round($remPts * 0.75, 2);
                                @endphp
                                <div style="border: 1px solid #000; border-radius: 4px; margin-top: 10px; padding: 6px 10px; font-size: 11px;">
                                    <div style="font-weight: bold; text-transform: uppercase; font-size: 11px; text-align: center; margin-bottom: 4px; border-bottom: 1px dashed #000; padding-bottom: 2px;">
                                        ⭐ {{ __('REWARD POINTS SUMMARY') }} ⭐
                                    </div>
                                    <table style="width: 100%; font-size: 11px; border: none; margin: 0;">
                                        <tr>
                                            <td style="padding: 2px 0; border: none;">Previous Points:</td>
                                            <td style="padding: 2px 0; text-align: right; font-weight: bold; border: none;">{{ number_format($prevPts, 0) }} pts</td>
                                        </tr>
                                        @if($usedPts > 0)
                                        <tr>
                                            <td style="padding: 2px 0; border: none;">Points Used (Redeemed):</td>
                                            <td style="padding: 2px 0; text-align: right; font-weight: bold; color: #dc2626; border: none;">-{{ number_format($usedPts, 0) }} pts (৳{{ number_format($usedTk, 2) }})</td>
                                        </tr>
                                        @endif
                                        @if($earnedPts > 0)
                                        <tr>
                                            <td style="padding: 2px 0; border: none;">Points Earned:</td>
                                            <td style="padding: 2px 0; text-align: right; font-weight: bold; color: #16a34a; border: none;">+{{ number_format($earnedPts, 0) }} pts</td>
                                        </tr>
                                        @endif
                                        <tr style="border-top: 1px dotted #000;">
                                            <td style="padding: 3px 0 0 0; font-weight: bold; border: none;">Remaining Points Balance:</td>
                                            <td style="padding: 3px 0 0 0; text-align: right; font-weight: bold; color: #2563eb; border: none;">{{ number_format($remPts, 0) }} pts (৳{{ number_format($remTk, 2) }})</td>
                                        </tr>
                                    </table>
                                </div>
                            @endif

                            @if($invoice->note)
                                <div style="margin-top: 10px; font-size: 13px; border-bottom: 1px dashed #000; padding-bottom: 5px;">
                                    <strong>Note:</strong> {{ $invoice->note }}
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
                                <div class="text-center my-3" style="margin-top: 15px; margin-bottom: 10px; text-align: center;">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($zatcaQrBase64) }}" 
                                         alt="ZATCA QR Code" 
                                         style="width: 85px; height: 85px; display: inline-block;" />
                                    <div style="font-size: 10px; font-weight: bold; margin-top: 2px; font-family: monospace;">ZATCA E-Invoice QR</div>
                                </div>
                            @elseif(is_invoice_qr_enabled())
                                @php
                                    $qrInvoiceData = (string) ($invoice->invoice_no ?: $invoice->id);
                                @endphp
                                <div class="text-center my-3" style="margin-top: 15px; margin-bottom: 12px; text-align: center;">
                                    <div style="display: inline-block; padding: 5px; background: #fff; border: 1.5px solid #1e293b; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=110x110&data={{ urlencode($qrInvoiceData) }}" 
                                             alt="Invoice QR Code" 
                                             style="width: 95px; height: 95px; display: block;" />
                                    </div>
                                </div>
                            @endif

                            <!-- ফুটার নোট -->
                            <div class="footer-note mt-3">
                                <p class="note">
                                    <ol style="list-style-type: decimal !important; padding-left: 15px !important; margin:0;">
                                        <li>দয়া করে মূল্য পরিশোধ করে পণ্য বুঝে নিন।</li>
                                        <li>কোন প্রোডাক্ট রিটার্ন করলে কোন ধরনের অফার প্রযোজ্য হবে না।</li>
                                        <li>আমাদের সিল যুক্ত পণ্যের কোন সমস্যা থাকলে আমরা সমাধান করে দিবো।</li>
                                        <li>ডেলিভারি সম্পর্কিত কোনো অভিযোগ বা পরামর্শ থাকলে এই নম্বর এ (01329655700) এ যোগাযোগ করুন।</li>
                                    </ol>
                                </p>
                                <h6 class="mt-5 text-center">Thank you for Shopping at {{ empty(get_setting('com_name')) ? 'Fast IT' : get_setting('com_name') }}</h6>
                                <h6 class="text-center">Developed By Fast iT Ltd</h6>
                            </div>
                        </div>
                        <button class="btn btn-secondary btn-block print_hidden" onclick="print_receipt('print-area')">
                            <i class="fa fa-print"></i> Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
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