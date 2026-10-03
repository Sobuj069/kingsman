@extends('backend.layouts.master')
@section('section-title', __('Report'))
@section('page-title', __('VAT Report'))

@push('css')
    <style>
        .report-summary-card {
            border-radius: 12px;
            padding: 16px 20px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        .report-summary-card h6 {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.9;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .report-summary-card h3 {
            font-size: 24px;
            font-weight: 800;
            margin: 0;
        }
        .report-summary-card .card-icon {
            position: absolute;
            right: 15px;
            bottom: 12px;
            font-size: 40px;
            opacity: 0.2;
        }
        .bg-grad-success {
            background: linear-gradient(135deg, #059669, #10b981);
        }
        .bg-grad-info {
            background: linear-gradient(135deg, #0284c7, #38bdf8);
        }
        .bg-grad-primary {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
        }
        .bg-grad-warning {
            background: linear-gradient(135deg, #d97706, #f59e0b);
        }
        @media print {
            table, table th, table td {
                color: black !important;
            }
            #h-hide, .main-navbar, .modern-sidebar, .topbar {
                display: none !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <!-- Filter Bar -->
            <div class="card card-body card_style mb-3" id="h-hide">
                <form action="{{ route('report.vat') }}" method="GET">
                    <div class="form-row align-items-end">
                        <div class="col-md-2 col-12 mb-3">
                            <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Start Date') }}</label>
                            <input type="date" name="start_date" class="form-control" style="height: 38px !important;"
                                value="{{ isset($sdate) ? date('Y-m-d', strtotime($sdate)) : '' }}">
                        </div>
                        <div class="col-md-2 col-12 mb-3">
                            <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('End Date') }}</label>
                            <input type="date" name="end_date" class="form-control" style="height: 38px !important;"
                                value="{{ isset($edate) ? date('Y-m-d', strtotime($edate)) : '' }}">
                        </div>
                        <div class="col-md-2 col-12 mb-3">
                            <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Type') }}</label>
                            <select class="form-control select2" name="type" style="height: 38px !important;">
                                <option value="all" {{ ($type ?? '') == 'all' ? 'selected' : '' }}>{{ __('All (Sales & Purchases)') }}</option>
                                <option value="sale" {{ ($type ?? '') == 'sale' ? 'selected' : '' }}>{{ __('Sales Only (Output VAT)') }}</option>
                                <option value="purchase" {{ ($type ?? '') == 'purchase' ? 'selected' : '' }}>{{ __('Purchases Only (Input VAT)') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-12 mb-3">
                            <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Customer') }}</label>
                            <select class="form-control select2" name="customer_id">
                                <option value="">{{ __('All Customers') }}</option>
                                @foreach ($customers as $c)
                                    <option value="{{ $c->id }}" {{ ($customer_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->phone }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 col-12 mb-3">
                            <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Supplier') }}</label>
                            <select class="form-control select2" name="supplier_id">
                                <option value="">{{ __('All Suppliers') }}</option>
                                @foreach ($suppliers as $s)
                                    <option value="{{ $s->id }}" {{ ($supplier_id ?? '') == $s->id ? 'selected' : '' }}>{{ $s->name }} ({{ $s->phone }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 col-12 mb-3">
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <button type="submit" class="btn add_list_btn flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa fa-sliders mr-1"></i> {{ __('Filter') }}
                                </button>
                                <a href="{{ route('report.vat') }}" class="btn add_list_btn_reset" style="height: 38px !important; display: flex; align-items: center; justify-content: center;" title="{{ __('Reset') }}">
                                    <i class="feather icon-refresh-cw"></i>
                                </a>
                                <a href="#" class="btn add_list_btn" style="height: 38px !important; display: flex; align-items: center; justify-content: center;" onclick="window.print()" title="{{ __('Print') }}">
                                    <i class="feather icon-printer"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="row" id="h-hide">
                <div class="col-md-3 col-sm-6">
                    <div class="report-summary-card bg-grad-success">
                        <h6>{{ __('Total Sale VAT (Output)') }}</h6>
                        <h3>{{ get_setting('currency') ?? '৳' }} {{ number_format($total_sale_vat ?? 0, 2) }}</h3>
                        <i class="fa fa-arrow-up card-icon"></i>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="report-summary-card bg-grad-info">
                        <h6>{{ __('Total Purchase VAT (Input)') }}</h6>
                        <h3>{{ get_setting('currency') ?? '৳' }} {{ number_format($total_purchase_vat ?? 0, 2) }}</h3>
                        <i class="fa fa-arrow-down card-icon"></i>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="report-summary-card bg-grad-primary">
                        <h6>{{ __('Net VAT Payable') }}</h6>
                        <h3>{{ get_setting('currency') ?? '৳' }} {{ number_format($net_vat_payable ?? 0, 2) }}</h3>
                        <i class="fa fa-balance-scale card-icon"></i>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="report-summary-card bg-grad-warning">
                        <h6>{{ __('Total Transactions') }}</h6>
                        <h3>{{ $total_transactions ?? 0 }}</h3>
                        <i class="fa fa-receipt card-icon"></i>
                    </div>
                </div>
            </div>

            <!-- Report Table -->
            <div class="card m-b-30 print_area card_style">
                <div class="card-header text-center py-4 border-0">
                    <h3 class="font-weight-bold mb-1" style="font-size: 24px; color: #1e293b;">{{ get_setting('com_name') ?? 'Business Name' }}</h3>
                    <h5 class="text-primary font-weight-bold mb-2">{{ __('VAT / Tax Report') }}</h5>
                    <p class="text-muted small mb-0">
                        {{ __('Period') }}: <strong>{{ date('d M, Y', strtotime($sdate)) }}</strong> {{ __('to') }} <strong>{{ date('d M, Y', strtotime($edate)) }}</strong>
                    </p>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr class="header_bg text-white" style="background: #1e293b; color: white;">
                                    <th class="text-center" style="width: 50px;">{{ __('SL.') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Invoice / Ref #') }}</th>
                                    <th>{{ __('Party (Customer/Supplier)') }}</th>
                                    <th class="text-right">{{ __('Taxable Amount') }}</th>
                                    <th class="text-center">{{ __('VAT %') }}</th>
                                    <th class="text-right">{{ __('VAT Amount') }}</th>
                                    <th class="text-right">{{ __('Net Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($records as $index => $row)
                                    <tr>
                                        <td class="text-center font-weight-bold">{{ $index + 1 }}</td>
                                        <td>{{ date('d M, Y', strtotime($row['date'])) }}</td>
                                        <td>
                                            @if ($row['type'] === 'Sale')
                                                <span class="badge badge-success px-2 py-1" style="font-size: 11px;">
                                                    <i class="fa fa-arrow-up mr-1"></i>{{ __('Sale (Output)') }}
                                                </span>
                                            @else
                                                <span class="badge badge-info px-2 py-1" style="font-size: 11px;">
                                                    <i class="fa fa-arrow-down mr-1"></i>{{ __('Purchase (Input)') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ $row['url'] }}" class="font-weight-bold text-primary" target="_blank">
                                                {{ $row['reference'] }}
                                            </a>
                                        </td>
                                        <td>
                                            <strong>{{ $row['party_name'] }}</strong>
                                            @if (!empty($row['party_phone']))
                                                <br><small class="text-muted"><i class="fa fa-phone mr-1"></i>{{ $row['party_phone'] }}</small>
                                            @endif
                                        </td>
                                        <td class="text-right">{{ number_format($row['gross_amount'], 2) }}</td>
                                        <td class="text-center">{{ $row['vat_rate'] ? $row['vat_rate'] . '%' : '-' }}</td>
                                        <td class="text-right font-weight-bold text-primary">{{ number_format($row['vat_amount'], 2) }}</td>
                                        <td class="text-right font-weight-bold">{{ number_format($row['total_amount'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">
                                            <i class="fa fa-file-invoice text-slate-300" style="font-size: 40px; margin-bottom: 10px; display: block;"></i>
                                            <h5>{{ __('No VAT records found for this period') }}</h5>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if (count($records) > 0)
                                <tfoot>
                                    <tr class="font-weight-bold" style="background: #f1f5f9; font-size: 14px;">
                                        <td colspan="5" class="text-right uppercase">{{ __('Total') }}:</td>
                                        <td class="text-right">{{ number_format($total_taxable_sales + $total_taxable_purchases, 2) }}</td>
                                        <td class="text-center">-</td>
                                        <td class="text-right text-primary">{{ number_format($total_sale_vat + $total_purchase_vat, 2) }}</td>
                                        <td class="text-right">{{ number_format($records->sum('total_amount'), 2) }}</td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
