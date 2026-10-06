@extends('backend.layouts.master')
@section('section-title', __('Invoice'))
@section('page-title', __('Invoice Details'))

@section('action-button')
    <a href="{{ route('invoice.index') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-arrow-left"></i>
        {{ __('Back to List') }}
    </a>
    <a href="{{ route('invoice.print', $invoice->id) }}" class="btn btn-success ml-2">
        <i class="mr-2 feather icon-printer"></i>
        {{ __('Print Invoice') }}
    </a>
@endsection

@push('css')
    <style>
        .invoice-card {
            border-radius: 12px !important;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0 !important;
        }
        .detail-label {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .detail-value {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
        }
        body.dark-theme .detail-label {
            color: #94a3b8;
        }
        body.dark-theme .detail-value {
            color: #f1f5f9;
        }
        .section-header {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        body.dark-theme .section-header {
            color: #f1f5f9;
            border-bottom-color: #334155;
        }
        .summary-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
        }
        body.dark-theme .summary-box {
            background-color: #1e293b;
            border-color: #334155;
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 767.98px) {
            .invoice-card .card-body {
                padding: 14px !important;
            }
            .section-header {
                font-size: 14px !important;
                margin-bottom: 12px !important;
            }
            .detail-label {
                font-size: 10px !important;
            }
            .detail-value {
                font-size: 13px !important;
            }
            .table-responsive {
                width: 100% !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
                margin-bottom: 12px !important;
            }
            .table-responsive table {
                min-width: 580px !important;
            }
            .table-responsive table th,
            .table-responsive table td {
                padding: 6px 8px !important;
                font-size: 12px !important;
            }
            .summary-box {
                padding: 12px !important;
                margin-top: 12px;
            }
            .btn {
                font-size: 12px !important;
                padding: 6px 12px !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <!-- Main Details -->
        <div class="col-lg-8">
            <div class="card m-b-30 invoice-card">
                <div class="card-body">
                    <h5 class="section-header"><i class="feather icon-info mr-2 text-primary"></i>{{ __('Invoice Overview') }}</h5>
                    <div class="row mb-3">
                        <div class="col-6 col-md-4 mb-3">
                            <div class="detail-label">{{ __('Invoice No') }}</div>
                            <div class="detail-value text-primary font-weight-bold">{{ $invoice->invoice_no }}</div>
                        </div>
                        <div class="col-6 col-md-4 mb-3">
                            <div class="detail-label">{{ __('Date') }}</div>
                            <div class="detail-value">{{ $invoice->date }}</div>
                        </div>
                        <div class="col-6 col-md-4 mb-3">
                            <div class="detail-label">{{ __('Status') }}</div>
                            <div class="detail-value">
                                @if ($invoice->status == 0)
                                    <span class="badge badge-warning">{{ __('Due') }}</span>
                                @elseif($invoice->status == 1)
                                    <span class="badge badge-success">{{ __('Paid') }}</span>
                                @elseif($invoice->status == 2)
                                    <span class="badge badge-danger">{{ __('Returned') }}</span>
                                @endif
                                @if ($invoice->is_edited == 1 || $invoice->edit_status == 'edited')
                                    <span class="badge badge-info">{{ __('Edited') }}</span>
                                @elseif($invoice->is_edited == 2 || $invoice->edit_status == 'exchange')
                                    <span class="badge badge-primary">{{ __('Exchange') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-6 col-md-4 mb-3">
                            <div class="detail-label">{{ __('Customer') }}</div>
                            <div class="detail-value">{{ $invoice->customer->name }} ({{ $invoice->customer->phone }})</div>
                        </div>
                        <div class="col-6 col-md-4 mb-3">
                            <div class="detail-label">{{ __('Branch') }}</div>
                            <div class="detail-value">{{ $invoice->branch?->name ?? __('N/A') }}</div>
                        </div>
                        <div class="col-6 col-md-4 mb-3">
                            <div class="detail-label">{{ __('Sale Type') }}</div>
                            <div class="detail-value">{{ ucfirst($invoice->sale_type) }}</div>
                        </div>
                        @if(env('APP_REF_INV') == 'yes' && !empty($invoice->ref_no))
                        <div class="col-6 col-md-4 mb-3">
                            <div class="detail-label">{{ __('Ref Inv') }}</div>
                            <div class="detail-value">{{ $invoice->ref_no }}</div>
                        </div>
                        @endif
                    </div>

                    @php
                        $isEditedInvoice = ($invoice->is_edited == 1 || $invoice->is_edited == 2 || $invoice->edit_status == 'edited' || $invoice->edit_status == 'exchange' || $invoice->bankTransactions->count() > 1);
                        $firstTx = $invoice->bankTransactions->where('pay_type', 'invpay')->first();
                        $origDate = $firstTx?->date ?? date('Y-m-d', strtotime($invoice->created_at));
                        $origPaid = (float)($firstTx?->amount ?? 0);
                        $addedPaid = (float)$invoice->bankTransactions->where('pay_type', 'invpay_edit')->sum('amount');
                        $origAmt = max(0, (float)$invoice->total_amount - $addedPaid);
                        $totalQtyCount = $inv_items->sum('actual_main');
                        $origQtyCount = max(1, $totalQtyCount - ($addedPaid > 0 ? 1 : 0));
                    @endphp

                    @if ($isEditedInvoice)
                        <!-- Edit & Date History Card -->
                        <div class="mb-4 p-3 rounded-lg border shadow-sm" style="background: linear-gradient(135deg, #f0fdf4 0%, #eff6ff 100%); border-color: #bfdbfe !important;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="font-weight-bold text-primary" style="font-size: 14px;">
                                    <i class="feather icon-calendar mr-1"></i> {{ __('Invoice Edit & Date Breakdown') }}
                                </span>
                                @if ($invoice->is_edited == 2 || $invoice->edit_status == 'exchange')
                                    <span class="badge badge-primary">{{ __('Exchange') }}</span>
                                @else
                                    <span class="badge badge-info">{{ __('Edited') }}</span>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                    <div class="p-3 bg-white rounded border border-slate-200 h-100 shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted small font-weight-bold uppercase" style="letter-spacing: 0.5px;">{{ __('Initial Sale') }}</span>
                                            <span class="badge badge-light border text-dark font-weight-bold">{{ $origDate }}</span>
                                        </div>
                                        <div class="mt-2 text-dark font-weight-bold" style="font-size: 14px;">
                                            <span>{{ __('Qty') }}: <span class="text-primary">{{ $origQtyCount }} pcs</span></span>
                                            <span class="text-muted mx-1">|</span>
                                            <span>{{ __('Amount') }}: <span class="text-primary">৳{{ number_format($origAmt > 0 ? $origAmt : $origPaid, 2) }}</span></span>
                                        </div>
                                        <div class="small text-muted mt-1">
                                            {{ __('Paid on') }} {{ $origDate }}: <strong class="text-success">৳{{ number_format($origPaid, 2) }}</strong>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="p-3 bg-white rounded border border-slate-200 h-100 shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-success small font-weight-bold uppercase" style="letter-spacing: 0.5px;">{{ __('After Edit') }}</span>
                                            <span class="badge badge-info font-weight-bold">{{ $invoice->date }}</span>
                                        </div>
                                        <div class="mt-2 text-dark font-weight-bold" style="font-size: 14px;">
                                            <span>{{ __('Total Qty') }}: <span class="text-success">{{ $totalQtyCount }} pcs</span></span>
                                            <span class="text-muted mx-1">|</span>
                                            <span>{{ __('Total Amount') }}: <span class="text-success">৳{{ number_format($invoice->total_amount, 2) }}</span></span>
                                        </div>
                                        <div class="small text-muted mt-1">
                                            {{ __('Added on') }} {{ $invoice->date }}: <strong class="text-success">৳{{ number_format($addedPaid, 2) }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <h5 class="section-header"><i class="feather icon-box mr-2 text-primary"></i>{{ __('Product / Item Details') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Item Name') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Rate') }}</th>
                                    <th>{{ __('Discount') }}</th>
                                    <th>{{ __('Subtotal') }}</th>
                                    <th>{{ __('Profit') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $grouped_items = $inv_items->groupBy(function($item) {
                                        return $item->product_id . '_' . ($item->product_variation_id ?? 0);
                                    });
                                    $calculated_total_profit = 0;
                                @endphp
                                @foreach ($grouped_items as $group)
                                    @php
                                        $item = $group->first();
                                        $comb_main = $group->sum('actual_main');
                                        $comb_sub = $group->sum('actual_sub');
                                        $comb_subtotal = $group->sum('subtotal');
                                        $comb_pur_subtotal = $group->sum('pur_subtotal');
                                        $comb_inv_subtotal = $group->sum('inv_subtotal');
                                        $item_profit = $comb_inv_subtotal - $comb_pur_subtotal;
                                        $calculated_total_profit += $item_profit;
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $item->product?->name }}</strong>
                                            @if(!empty($item->product?->barcode))
                                                <span class="badge badge-light border text-dark px-1.5 py-0.5 ml-1 font-monospace" style="font-size: 11px;">
                                                    <i class="feather icon-maximize-2 mr-0.5"></i>{{ $item->product?->barcode }}
                                                </span>
                                            @endif
                                            @if ($item->suppliers->isNotEmpty())
                                                <span class="badge badge-secondary ml-1">{{ $item->suppliers->pluck('name')->implode(', ') }}</span>
                                            @endif
                                            @if ($item->is_return == 1)
                                                <span class="badge badge-danger ml-1">{{ __('Return') }}</span>
                                            @endif
                                            @if ($item->product_variation_id != null)
                                                <br><small class="text-muted">Variation: {{ $item->product_variation?->size?->size }}-{{ $item->product_variation?->color?->color }}</small>
                                            @endif
                                            @if (!empty($item->imei))
                                                <br><small class="text-muted">IMEI: {{ str_replace(',', ', ', $item->imei) }}</small>
                                            @endif
                                            @if($item->warranty_value)
                                                <br><small class="text-muted">Warranty: {{ $item->warranty_value }} {{ $item->warranty_unit }}{{ $item->warranty_value > 1 ? 's' : '' }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($item->product?->unit?->related_unit == null)
                                                {{ $comb_main }} {{ $item->product?->unit?->name ?? 'pcs' }}
                                            @else
                                                @if($comb_main == 0 && $comb_sub == 0)
                                                    0 {{ $item->product?->unit?->name }}
                                                @else
                                                    @if($comb_main > 0) {{ $comb_main }} {{ $item->product?->unit?->name }} @endif
                                                    @if($comb_main > 0 && $comb_sub > 0) - @endif
                                                    @if($comb_sub > 0) {{ $comb_sub }} {{ $item->product?->unit?->related_unit?->name }} @endif
                                                @endif
                                            @endif
                                        </td>
                                        <td>{{ number_format($item->rate, 2) }}</td>
                                        <td>
                                            @if (str_contains($item->product_discount, '%'))
                                                {{ $item->product_discount }}
                                            @else
                                                {{ number_format((float) $item->product_discount, 2) }}
                                            @endif
                                        </td>
                                        <td>{{ number_format($comb_subtotal, 2) }}</td>
                                        <td class="text-success font-weight-bold">{{ number_format($item_profit, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if ($invoice->installment)
                <div class="card m-b-30 invoice-card mt-4">
                    <div class="card-body">
                        <h5 class="section-header text-danger"><i class="feather icon-calendar mr-2"></i>{{ __('Installment Sale Agreement') }}</h5>
                        <div class="row mb-4">
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="detail-label">{{ __('Agreement Status') }}</div>
                                <div class="detail-value">
                                    @if ($invoice->installment->status == 'pending')
                                        <span class="badge badge-warning text-white">{{ __('Pending') }}</span>
                                    @else
                                        <span class="badge badge-success">{{ __('Completed') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="detail-label">{{ __('Advance Pay') }}</div>
                                <div class="detail-value text-success">TK {{ number_format($invoice->installment->advance_pay, 2) }}</div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="detail-label">{{ __('Remaining (Principal)') }}</div>
                                <div class="detail-value">TK {{ number_format($invoice->installment->remaining_amount, 2) }}</div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="detail-label">{{ __('Interest Rate') }}</div>
                                <div class="detail-value">{{ $invoice->installment->interest_percentage }}% (TK {{ number_format($invoice->installment->interest_amount, 2) }})</div>
                            </div>
                            
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="detail-label">{{ __('Total with Interest') }}</div>
                                <div class="detail-value text-danger font-weight-bold">TK {{ number_format($invoice->installment->total_with_interest, 2) }}</div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="detail-label">{{ __('Per Installment') }}</div>
                                <div class="detail-value text-primary font-weight-bold">TK {{ number_format($invoice->installment->per_installment_amount, 2) }}</div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="detail-label">{{ __('Interval / Count') }}</div>
                                <div class="detail-value">{{ $invoice->installment->interval_days }} {{ __('Days') }} ({{ $invoice->installment->total_installments }} {{ __('Installments') }})</div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="detail-label">{{ __('Due Range') }}</div>
                                <div class="detail-value" style="font-size:12px;">{{ $invoice->installment->first_due_date }} {{ __('to') }} {{ $invoice->installment->last_due_date }}</div>
                            </div>
                        </div>

                        <h5 class="section-header"><i class="feather icon-list mr-2"></i>{{ __('Payment Schedule') }}</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-center">
                                <thead class="bg-light">
                                    <tr>
                                        <th>{{ __('Installment #') }}</th>
                                        <th>{{ __('Due Date') }}</th>
                                        <th>{{ __('Scheduled Amount') }}</th>
                                        <th>{{ __('Paid Amount') }}</th>
                                        <th>{{ __('Paid Date') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoice->installment->schedules as $sched)
                                        <tr>
                                            <td class="font-weight-bold">#{{ $sched->installment_no }}</td>
                                            <td>{{ $sched->due_date }}</td>
                                            <td>TK {{ number_format($sched->amount, 2) }}</td>
                                            <td>
                                                @if($sched->status == 'paid')
                                                    TK {{ number_format($sched->paid_amount, 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $sched->paid_date ?? '-' }}</td>
                                            <td>
                                                @if($sched->status == 'paid')
                                                    <span class="badge badge-success">{{ __('Paid') }}</span>
                                                @else
                                                    <span class="badge badge-warning text-white">{{ __('Pending') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Invoice Edit & Product Change History -->
            @if ($invoice->editLogs && $invoice->editLogs->count() > 0)
                <div class="card m-b-30 invoice-card mt-3">
                    <div class="card-body">
                        <h5 class="section-header text-primary"><i class="feather icon-clock mr-2"></i>{{ __('Invoice Edit & Product Change History') }}</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped text-center align-middle">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>{{ __('Date & Time') }}</th>
                                        <th>{{ __('User') }}</th>
                                        <th>{{ __('Action') }}</th>
                                        <th>{{ __('Product & Change Details') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoice->editLogs as $log)
                                        <tr>
                                            <td style="font-size: 12px; white-space: nowrap;">
                                                <strong class="text-dark">{{ $log->edit_date }}</strong><br>
                                                <small class="text-muted">{{ $log->created_at->format('h:i A') }}</small>
                                            </td>
                                            <td>
                                                <span class="font-weight-bold text-slate-700">{{ $log->user?->name ?? __('System') }}</span>
                                            </td>
                                            <td>
                                                @if (str_contains($log->action, 'Added'))
                                                    <span class="badge badge-success px-2 py-1">{{ __($log->action) }}</span>
                                                @elseif (str_contains($log->action, 'Removed'))
                                                    <span class="badge badge-danger px-2 py-1">{{ __($log->action) }}</span>
                                                @else
                                                    <span class="badge badge-info px-2 py-1">{{ __($log->action) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-left" style="font-size: 13px;">
                                                <strong class="text-primary">{{ $log->product_name }}</strong>
                                                <p class="mb-0 text-dark">{{ $log->details }}</p>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar Summary / Financials -->
        <div class="col-lg-4">
            <!-- Totals Box -->
            <div class="card m-b-30 invoice-card">
                <div class="card-body">
                    <h5 class="section-header"><i class="feather icon-credit-card mr-2 text-primary"></i>{{ __('Financial Summary') }}</h5>
                    <div class="summary-box">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="detail-label">{{ __('Sub Total') }}</span>
                            <span class="detail-value">{{ number_format($invoice->estimated_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="detail-label">
                                {{ __('Discount') }}
                                @if (str_contains($invoice->discount, '%'))
                                    ({{ $invoice->discount }})
                                @endif
                            </span>
                            <span class="detail-value text-danger">- {{ number_format($invoice->discount_amount, 2) }}</span>
                        </div>
                        @if ($invoice->vat_amount > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="detail-label">
                                    {{ __('VAT') }}
                                    @if (str_contains($invoice->vat, '%'))
                                        ({{ $invoice->vat }})
                                    @endif
                                </span>
                                <span class="detail-value">+ {{ number_format($invoice->vat_amount, 2) }}</span>
                            </div>
                        @endif
                        <hr class="my-2" style="border-top: 2px solid #cbd5e1;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="detail-label font-weight-bold text-primary">{{ __('Grand Total') }}</span>
                            <span class="detail-value text-primary font-weight-bold" style="font-size: 16px;">{{ number_format($invoice->total_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="detail-label">{{ __('Total Paid') }}</span>
                            <span class="detail-value text-success font-weight-bold">{{ number_format($invoice->total_paid, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="detail-label">{{ __('Total Due') }}</span>
                            <span class="detail-value text-danger font-weight-bold">{{ number_format($invoice->total_due, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profit details -->
            <div class="card m-b-30 invoice-card">
                <div class="card-body">
                    <h5 class="section-header"><i class="feather icon-trending-up mr-2 text-primary"></i>{{ __('Profit Analysis') }}</h5>
                    <div class="summary-box bg-light-success">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="detail-label">{{ __('Invoice Profit') }}</span>
                            <span class="detail-value text-success font-weight-bold" style="font-size: 18px;">
                                {{ number_format($calculated_total_profit - $invoice->discount_amount, 2) }}
                            </span>
                        </div>
                        <small class="text-muted d-block mt-2">
                            * Calculated as: Sum of Item Profits ({{ number_format($calculated_total_profit, 2) }}) minus overall invoice discount ({{ number_format($invoice->discount_amount, 2) }}).
                        </small>
                    </div>
                </div>
            </div>

            <!-- Payment Methods & Log Info -->
            <div class="card m-b-30 invoice-card">
                <div class="card-body">
                    <h5 class="section-header"><i class="feather icon-user mr-2 text-primary"></i>{{ __('Activity & Accounts') }}</h5>
                    
                    <div class="mb-3">
                        <div class="detail-label font-weight-bold mb-2">{{ __('Payment Date & Accounts Breakdown') }}</div>
                        <div class="detail-value">
                            @php
                                $paymentsList = $payments ?? $invoice->bankTransactions ?? collect([]);
                            @endphp
                            @if ($paymentsList->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered text-center mb-0" style="font-size: 12px;">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>{{ __('Date') }}</th>
                                                <th>{{ __('Account') }}</th>
                                                <th>{{ __('Type') }}</th>
                                                <th>{{ __('Amount') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($paymentsList as $pay)
                                                <tr>
                                                    <td class="font-weight-bold text-dark">{{ $pay->date }}</td>
                                                    <td>{{ $pay->bank_account?->bank_name ?? __('Cash') }}</td>
                                                    <td>
                                                        @if ($pay->trans_type == 'deposit')
                                                            <span class="badge badge-success">{{ __('Received') }}</span>
                                                        @else
                                                            <span class="badge badge-danger">{{ __('Refunded') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="font-weight-bold text-primary">
                                                        {{ number_format($pay->amount, 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <span class="text-muted">{{ __('No Payment transactions registered') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="detail-label">{{ __('Created By') }}</div>
                            <div class="detail-value">{{ $invoice->user->name }}</div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="detail-label">{{ __('Created At') }}</div>
                            <div class="detail-value" style="font-size: 12px;">{{ $invoice->created_at }}</div>
                        </div>
                        @if($invoice->updatedBy)
                            <div class="col-6 mb-3">
                                <div class="detail-label">{{ __('Updated By') }}</div>
                                <div class="detail-value">{{ $invoice->updatedBy->name }}</div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="detail-label">{{ __('Updated At') }}</div>
                                <div class="detail-value" style="font-size: 12px;">{{ $invoice->updated_at }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
