@extends('backend.layouts.master')
@section('section-title', __('Report'))
@section('page-title', __('Customer Full Report'))

@push('css')
<style>
    .report-summary-card {
        border-radius: 12px;
        padding: 18px 22px;
        color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .report-summary-card .label { font-size: 12px; opacity: 0.85; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase; }
    .report-summary-card .value { font-size: 22px; font-weight: 800; margin-top: 4px; }
    .bg-inv    { background: linear-gradient(135deg,#4f46e5,#7c3aed); }
    .bg-paid   { background: linear-gradient(135deg,#059669,#10b981); }
    .bg-due    { background: linear-gradient(135deg,#dc2626,#ef4444); }
    .bg-grand  { background: linear-gradient(135deg,#d97706,#f59e0b); }
    .section-title { font-size: 14px; font-weight: 700; color: #4f46e5; border-left: 4px solid #4f46e5; padding-left: 10px; margin: 20px 0 12px; }
    .cust-info-table td { padding: 5px 12px; font-size: 13px; }
    .cust-info-table td:first-child { font-weight: 700; color: #6b7280; width: 180px; }
    .badge-type { background: #ede9fe; color: #6d28d9; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .vehicle-pill { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 10px 14px; margin-bottom: 10px; font-size: 13px; }
    .vehicle-pill strong { color: #166534; }
    table.report-tbl th { font-size: 12px; background: #1e1b4b; color: #fff; }
    table.report-tbl td { font-size: 13px; vertical-align: middle; }
    @media print {
        .no-print, .no-print * { display: none !important; }
        table th, table td { color: black !important; }
        .report-summary-card { color: #000 !important; border: 1px solid #ccc !important; background: none !important; }
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">

        {{-- Filter Card --}}
        <div class="card card-body card_style mb-3 no-print">
            <form action="{{ route('report.customer.full-report') }}" method="GET">
                <div class="form-row align-items-end">
                    <div class="col-md-5 mb-3">
                        <label class="font-weight-bold text-muted small mb-1">{{ __('Select Customer') }}</label>
                        <select name="customer_id" class="select2 form-control" required>
                            <option value="">-- {{ __('Choose a Customer') }} --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ $customer_id == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} &mdash; {{ $c->phone }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5 mb-3 d-flex" style="gap:8px;">
                        <button type="submit" class="btn add_list_btn flex-grow-1" style="height:38px;">
                            <i class="fa fa-search mr-1"></i> {{ __('Generate Report') }}
                        </button>
                        <a href="{{ route('report.customer.full-report') }}" class="btn add_list_btn_reset flex-grow-1" style="height:38px;">
                            <i class="feather icon-refresh-cw mr-1"></i> {{ __('Reset') }}
                        </a>
                        @if($customer)
                        <a href="#" onclick="window.print()" class="btn add_list_btn flex-grow-1" style="height:38px;">
                            <i class="feather icon-printer mr-1"></i> {{ __('Print') }}
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        @if($customer)

        {{-- Company Header (print only) --}}
        <div class="text-center mb-3" style="display:none;" id="print-header">
            <strong style="font-size:20px;">{{ get_setting('com_name') }}</strong><br>
            <small>{{ get_setting('com_address') }} | {{ get_setting('com_phone') }}</small>
            <hr>
        </div>

        {{-- Customer Info --}}
        <div class="card card_style mb-3">
            <div class="card-body">
                <div class="section-title">{{ __('Customer Information') }}</div>
                <div class="row">
                    <div class="col-md-6">
                        <table class="cust-info-table">
                            <tr><td>{{ __('Name') }}</td><td>: <strong>{{ $customer->name }}</strong></td></tr>
                            <tr><td>{{ __('Phone') }}</td><td>: {{ $customer->phone }}</td></tr>
                            <tr><td>{{ __('Email') }}</td><td>: {{ $customer->email ?? '—' }}</td></tr>
                            <tr><td>{{ __('Address') }}</td><td>: {{ $customer->address ?? '—' }}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="cust-info-table">
                            <tr><td>{{ __('Customer Type') }}</td><td>: {{ $customer->customer_type ? '<span class="badge-type">'.$customer->customer_type.'</span>' : '—' }}</td></tr>
                            @if(!is_hide_customer_dates())
                            <tr><td>{{ __('Birth Date') }}</td><td>: {{ $customer->birth_date ?? '—' }}</td></tr>
                            <tr><td>{{ __('Anniversary Date') }}</td><td>: {{ $customer->anni_date ?? '—' }}</td></tr>
                            @endif
                            <tr><td>{{ __('Opening Due') }}</td><td>: <strong class="text-danger">{{ number_format($customer->due_amount ?? 0, 2) }}</strong></td></tr>
                        </table>
                    </div>
                </div>

                @if(env('APP_AUTOMOBILE') == 'yes')
                @if($customer->vehicles->count() > 0)
                <div class="section-title mt-3">{{ __('Vehicle(s)') }}</div>
                <div class="row">
                    @foreach($customer->vehicles as $v)
                    <div class="col-md-6">
                        <div class="vehicle-pill">
                            <strong>{{ $v->vehicle_name ?? '—' }}</strong> &nbsp;|&nbsp; Reg: {{ $v->reg_no ?? '—' }} &nbsp;|&nbsp; Model: {{ $v->model ?? '—' }}<br>
                            <span class="text-muted" style="font-size:12px;">
                                Made In: {{ $v->made_in ?? '—' }} &nbsp;|&nbsp;
                                Engine: {{ $v->engine_no ?? '—' }} &nbsp;|&nbsp;
                                Chassis: {{ $v->chassis_no ?? '—' }} &nbsp;|&nbsp;
                                Milage: {{ $v->milage ?? '—' }}
                            </span><br>
                            <span class="text-muted" style="font-size:12px;">
                                Driver: {{ $v->driver_name ?? '—' }} ({{ $v->driver_phone ?? '—' }})
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
                @endif
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="row mb-3">
            <div class="col-md-3 col-6 mb-2">
                <div class="report-summary-card bg-inv">
                    <div class="label">{{ __('Total Invoice') }}</div>
                    <div class="value">{{ number_format($total_invoice_amount, 2) }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="report-summary-card bg-paid">
                    <div class="label">{{ __('Total Paid') }}</div>
                    <div class="value">{{ number_format($total_paid, 2) }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="report-summary-card bg-due">
                    <div class="label">{{ __('Invoice Due') }}</div>
                    <div class="value">{{ number_format($total_due, 2) }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-2">
                <div class="report-summary-card bg-grand">
                    <div class="label">{{ __('Grand Due (incl. Opening)') }}</div>
                    <div class="value">{{ number_format($grand_due, 2) }}</div>
                </div>
            </div>
        </div>

        {{-- Invoices Table --}}
        <div class="card card_style mb-3">
            <div class="card-body">
                <div class="section-title">{{ __('All Invoices') }} ({{ $invoices->count() }})</div>
                <div class="table-responsive">
                    <table class="table table-hover report-tbl">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Invoice No') }}</th>
                                <th>{{ __('Items') }}</th>
                                <th>{{ __('Total Amount') }}</th>
                                <th>{{ __('Paid') }}</th>
                                <th>{{ __('Due') }}</th>
                                <th>{{ __('Type') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $i => $inv)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $inv->date }}</td>
                                <td><strong>#{{ $inv->invoice_no }}</strong></td>
                                <td>{{ $inv->invoiceItems->count() }}</td>
                                <td class="font-weight-bold">{{ number_format($inv->total_amount, 2) }}</td>
                                <td class="text-success font-weight-bold">{{ number_format($inv->total_paid, 2) }}</td>
                                <td class="{{ $inv->total_due > 0 ? 'text-danger font-weight-bold' : 'text-muted' }}">
                                    {{ number_format($inv->total_due, 2) }}
                                </td>
                                <td><span class="badge badge-secondary">{{ $inv->sale_type ?? 'N/A' }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center text-muted py-3">{{ __('No invoices found') }}</td></tr>
                            @endforelse
                        </tbody>
                        @if($invoices->count() > 0)
                        <tfoot>
                            <tr style="background:#f1f5f9; font-weight:bold;">
                                <td colspan="4" class="text-right">{{ __('Total') }}:</td>
                                <td>{{ number_format($total_invoice_amount, 2) }}</td>
                                <td class="text-success">{{ number_format($total_paid, 2) }}</td>
                                <td class="text-danger">{{ number_format($total_due, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- Transactions Table --}}
        <div class="card card_style mb-3">
            <div class="card-body">
                <div class="section-title">{{ __('All Transactions (Ledger)') }} ({{ $transactions->count() }})</div>
                <div class="table-responsive">
                    <table class="table table-hover report-tbl">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Type / Particulars') }}</th>
                                <th>{{ __('Invoice Ref') }}</th>
                                <th>{{ __('Debit') }}</th>
                                <th>{{ __('Credit') }}</th>
                                <th>{{ __('Running Balance') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $runBalance = $opening_due; @endphp
                            @if($opening_due > 0)
                            <tr style="background:#fef3c7;">
                                <td colspan="4"><strong>{{ __('Opening Due Amount') }}</strong></td>
                                <td class="text-danger font-weight-bold">{{ number_format($opening_due, 2) }}</td>
                                <td>—</td>
                                <td class="font-weight-bold">{{ number_format($runBalance, 2) }}</td>
                            </tr>
                            @endif
                            @forelse($transactions as $i => $tr)
                            @php
                                $runBalance = $runBalance + ($tr->debit ?? 0) - ($tr->credit ?? 0);
                            @endphp
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $tr->date }}</td>
                                <td>{{ $tr->transaction_type }}</td>
                                <td>{{ $tr->invoice ? '#'.$tr->invoice->invoice_no : '—' }}</td>
                                <td class="{{ $tr->debit > 0 ? 'text-danger' : 'text-muted' }}">
                                    {{ $tr->debit ? number_format($tr->debit, 2) : '—' }}
                                </td>
                                <td class="{{ $tr->credit > 0 ? 'text-success' : 'text-muted' }}">
                                    {{ $tr->credit ? number_format($tr->credit, 2) : '—' }}
                                </td>
                                <td class="font-weight-bold {{ $runBalance > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format(abs($runBalance), 2) }}
                                    {{ $runBalance > 0 ? '(Dr)' : ($runBalance < 0 ? '(Cr)' : '') }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center text-muted py-3">{{ __('No transactions found') }}</td></tr>
                            @endforelse
                        </tbody>
                        @if($transactions->count() > 0 || $opening_due > 0)
                        <tfoot>
                            <tr style="background:#1e1b4b; color:#fff; font-weight:bold;">
                                <td colspan="4" class="text-right text-white">{{ __('Closing Balance') }}:</td>
                                <td class="text-white">{{ number_format($transactions->sum('debit') + $opening_due, 2) }}</td>
                                <td class="text-white">{{ number_format($transactions->sum('credit'), 2) }}</td>
                                <td class="text-warning">{{ number_format($grand_due, 2) }} (Dr)</td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        @else
        <div class="card card_style">
            <div class="card-body text-center py-5">
                <i class="feather icon-user-check" style="font-size:48px; color:#c7d2fe;"></i>
                <p class="mt-3 text-muted">{{ __('Please select a customer to generate their full report.') }}</p>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection

@push('js')
<script>
    // Show print header when printing
    window.onbeforeprint = function() {
        document.getElementById('print-header') && (document.getElementById('print-header').style.display = 'block');
    };
    window.onafterprint = function() {
        document.getElementById('print-header') && (document.getElementById('print-header').style.display = 'none');
    };
</script>
@endpush
