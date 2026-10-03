@extends('backend.layouts.master')
@section('section-title', __('Report'))
@section('page-title', __('Top Selling Product'))
@section('action-button')
    <a href="javascript:void(0)" class="btn add_list_btn" onclick="window.print()">
        <i class="feather icon-printer mr-1"></i> {{ __('Print') }}
    </a>
@endsection

@push('css')
    <style>
        @media print {
            table, table th, table td {
                color: black !important;
            }
            #h-hide, .main-footer, .breadcrumb, .page-header {
                display: none !important;
            }
            .card {
                border: none !important;
                box-shadow: none !important;
            }
            .card_style {
                background: transparent !important;
            }
            .print-title {
                display: block !important;
            }
        }

        .print-title {
            display: none;
        }

        /* KPI Bar Styles */
        .top-selling-kpi-bar {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }

        body.dark-theme .top-selling-kpi-bar {
            background: #121829 !important;
            border-color: #1e293b !important;
        }

        .kpi-item {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .kpi-label {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        body.dark-theme .kpi-label {
            color: #94a3b8 !important;
        }

        .kpi-value {
            font-size: 22px;
            font-weight: 800;
            line-height: 1.2;
        }

        /* Rank Badges */
        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            font-weight: 800;
            font-size: 13px;
        }
        .rank-1 {
            background: linear-gradient(135deg, #fef08a, #eab308);
            color: #713f12;
            box-shadow: 0 2px 6px rgba(234, 179, 8, 0.4);
        }
        .rank-2 {
            background: linear-gradient(135deg, #f1f5f9, #94a3b8);
            color: #1e293b;
            box-shadow: 0 2px 6px rgba(148, 163, 184, 0.4);
        }
        .rank-3 {
            background: linear-gradient(135deg, #fed7aa, #f97316);
            color: #7c2d12;
            box-shadow: 0 2px 6px rgba(249, 115, 22, 0.4);
        }
        .rank-other {
            background: #f1f5f9;
            color: #475569;
        }
        body.dark-theme .rank-other {
            background: #1e293b;
            color: #cbd5e1;
        }

        @media (max-width: 991px) {
            .top-selling-kpi-bar .kpi-item {
                flex: 1 1 calc(33.333% - 15px);
                margin-bottom: 10px;
            }
        }
        @media (max-width: 576px) {
            .top-selling-kpi-bar .kpi-item {
                flex: 1 1 calc(50% - 10px);
            }
        }
    </style>
@endpush

@section('content')
@php
    $currency = empty(get_setting('com_currency')) ? 'Tk' : get_setting('com_currency');
@endphp

<div class="row">
    <div class="col-lg-12">
        
        {{-- Filter Card --}}
        <div class="card card-body card_style mb-3" style="margin-top: -5px;" id="h-hide">
            <form action="{{ route('report.top-selling') }}" method="GET">
                <div class="form-row align-items-end">
                    
                    {{-- Start Date --}}
                    <div class="col-md-2 col-sm-6 col-12 mb-3">
                        <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Start Date') }}</label>
                        <input type="date" name="start_date" class="form-control" style="height: 38px !important;"
                            value="{{ $sdate }}">
                    </div>

                    {{-- End Date --}}
                    <div class="col-md-2 col-sm-6 col-12 mb-3">
                        <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('End Date') }}</label>
                        <input type="date" name="end_date" class="form-control" style="height: 38px !important;"
                            value="{{ $edate }}">
                    </div>

                    {{-- Category --}}
                    <div class="col-md-2 col-sm-6 col-12 mb-3">
                        <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Category') }}</label>
                        <select class="select2" name="category_id">
                            <option value="">{{ __('All Categories') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sub Category --}}
                    @if (env('APP_SUB_CATEGORY') == 'yes')
                        <div class="col-md-2 col-sm-6 col-12 mb-3">
                            <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Sub Category') }}</label>
                            <select class="select2" name="sub_category_id">
                                <option value="">{{ __('All Sub Categories') }}</option>
                                @foreach ($subCategories as $subCat)
                                    <option value="{{ $subCat->id }}" {{ $subCategoryId == $subCat->id ? 'selected' : '' }}>
                                        {{ $subCat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Brand --}}
                    <div class="col-md-2 col-sm-6 col-12 mb-3">
                        <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Brand') }}</label>
                        <select class="select2" name="brand_id">
                            <option value="">{{ __('All Brands') }}</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" {{ $brandId == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Top Limit --}}
                    <div class="col-md-2 col-sm-6 col-12 mb-3">
                        <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Top Limit') }}</label>
                        <select class="form-control" name="limit" style="height: 38px !important;">
                            <option value="10" {{ $limit == 10 ? 'selected' : '' }}>{{ __('Top 10 Products') }}</option>
                            <option value="20" {{ $limit == 20 ? 'selected' : '' }}>{{ __('Top 20 Products') }}</option>
                            <option value="50" {{ $limit == 50 ? 'selected' : '' }}>{{ __('Top 50 Products') }}</option>
                            <option value="100" {{ $limit == 100 ? 'selected' : '' }}>{{ __('Top 100 Products') }}</option>
                            <option value="0" {{ $limit === 0 ? 'selected' : '' }}>{{ __('All Products') }}</option>
                        </select>
                    </div>

                    {{-- Sort By --}}
                    <div class="col-md-2 col-sm-6 col-12 mb-3">
                        <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Sort By') }}</label>
                        <select class="form-control" name="sort_by" style="height: 38px !important;">
                            <option value="qty" {{ $sortBy == 'qty' ? 'selected' : '' }}>{{ __('Highest Sold Qty') }}</option>
                            <option value="amount" {{ $sortBy == 'amount' ? 'selected' : '' }}>{{ __('Highest Sales Amount') }}</option>
                            <option value="profit" {{ $sortBy == 'profit' ? 'selected' : '' }}>{{ __('Highest Gross Profit') }}</option>
                        </select>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="col-md-4 col-12 mb-3">
                        <div class="d-flex align-items-center" style="gap: 5px;">
                            <button type="submit" class="btn add_list_btn flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-sliders mr-1"></i> {{ __('Filter') }}
                            </button>
                            <a href="{{ route('report.top-selling') }}" class="btn add_list_btn_reset flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;">
                                <i class="feather icon-refresh-cw mr-1"></i> {{ __('Reset') }}
                            </a>
                            <a href="javascript:void(0)" class="btn add_list_btn flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;" onclick="window.print()">
                                <i class="feather icon-printer mr-1"></i> {{ __('Print') }}
                            </a>
                        </div>
                    </div>

                </div>
            </form>
        </div>

        {{-- Report Card --}}
        <div class="card m-b-30 card_style">
            <div class="card-header pb-0">
                <div class="print-title text-center mb-4">
                    <h3 class="font-weight-bold">{{ __('Top Selling Product Report') }}</h3>
                    <p class="text-muted">{{ __('Period') }}: {{ date('d M Y', strtotime($sdate)) }} - {{ date('d M Y', strtotime($edate)) }}</p>
                </div>
            </div>

            <div class="card-body">
                
                {{-- KPI Summary Bar --}}
                <div class="top-selling-kpi-bar">
                    <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 15px;">
                        
                        {{-- Total Products Ranked --}}
                        <div class="kpi-item" style="border-left: 4px solid #8b5cf6; padding-left: 12px; min-width: 100px;">
                            <div class="kpi-label">{{ __('PRODUCTS RANKED') }}</div>
                            <div class="kpi-value" style="color: #8b5cf6;">
                                {{ $paginatedList->count() }}
                            </div>
                        </div>

                        {{-- Total Sold Qty --}}
                        <div class="kpi-item" style="border-left: 4px solid #0284c7; padding-left: 12px; min-width: 110px;">
                            <div class="kpi-label">{{ __('TOTAL NET SOLD QTY') }}</div>
                            <div class="kpi-value" style="color: #0284c7;">
                                {{ number_format($paginatedList->sum('net_qty'), 0) }} <span style="font-size: 15px; font-weight: 600;">{{ __('Pcs') }}</span>
                            </div>
                        </div>

                        {{-- Total Sales Value --}}
                        <div class="kpi-item" style="border-left: 4px solid #10b981; padding-left: 12px; min-width: 120px;">
                            <div class="kpi-label">{{ __('TOTAL SALES VALUE') }}</div>
                            <div class="kpi-value" style="color: #10b981;">
                                {{ number_format($paginatedList->sum('net_amount'), 2) }} <span style="font-size: 15px; font-weight: 600;">{{ $currency }}</span>
                            </div>
                        </div>

                        {{-- Total Purchase Cost --}}
                        <div class="kpi-item" style="border-left: 4px solid #f59e0b; padding-left: 12px; min-width: 120px;">
                            <div class="kpi-label">{{ __('TOTAL PURCHASE COST') }}</div>
                            <div class="kpi-value" style="color: #f59e0b;">
                                {{ number_format($paginatedList->sum('pur_cost'), 2) }} <span style="font-size: 15px; font-weight: 600;">{{ $currency }}</span>
                            </div>
                        </div>

                        {{-- Total Gross Profit --}}
                        <div class="kpi-item" style="border-left: 4px solid #16a34a; padding-left: 12px; min-width: 120px;">
                            <div class="kpi-label">{{ __('TOTAL GROSS PROFIT') }}</div>
                            <div class="kpi-value" style="color: #16a34a;">
                                {{ number_format($paginatedList->sum('profit'), 2) }} <span style="font-size: 15px; font-weight: 600;">{{ $currency }}</span>
                            </div>
                        </div>

                        {{-- Average Margin --}}
                        @php
                            $sumSales = $paginatedList->sum('net_amount');
                            $sumProfit = $paginatedList->sum('profit');
                            $avgMargin = $sumSales > 0 ? ($sumProfit / $sumSales) * 100 : 0;
                        @endphp
                        <div class="kpi-item text-center" style="min-width: 100px;">
                            <div class="kpi-label text-muted" style="font-size: 12px; font-weight: 600; text-transform: none;">{{ __('Avg Margin:') }}</div>
                            <div class="kpi-value" style="color: #6366f1;">
                                {{ number_format($avgMargin, 1) }}%
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Table Area --}}
                <div class="table-responsive">
                    <table class="table table-striped table-hover text-center align-middle">
                        <thead class="header_bg">
                            <tr>
                                <th class="header_style_left" style="width: 60px;">{{ __('Rank') }}</th>
                                <th style="text-align: left; min-width: 180px;">{{ __('Product') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Brand') }}</th>
                                <th>{{ __('Current Stock') }}</th>
                                <th>{{ __('Sold Qty') }}</th>
                                <th>{{ __('Return Qty') }}</th>
                                <th>{{ __('Net Sold Qty') }}</th>
                                <th>{{ __('Sales Amount') }}</th>
                                <th>{{ __('Purchase Cost') }}</th>
                                <th>{{ __('Gross Profit') }}</th>
                                <th class="header_style_right">{{ __('Margin') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paginatedList as $index => $item)
                                @php
                                    $p = $item->product;
                                    $currentStock = product_stock($p);
                                    $rank = $index + 1;
                                @endphp
                                <tr>
                                    {{-- Rank Badge --}}
                                    <td>
                                        @if ($rank === 1)
                                            <span class="rank-badge rank-1" title="Top 1">🥇 1</span>
                                        @elseif ($rank === 2)
                                            <span class="rank-badge rank-2" title="Top 2">🥈 2</span>
                                        @elseif ($rank === 3)
                                            <span class="rank-badge rank-3" title="Top 3">🥉 3</span>
                                        @else
                                            <span class="rank-badge rank-other">#{{ $rank }}</span>
                                        @endif
                                    </td>

                                    {{-- Product Name & Barcode --}}
                                    <td style="text-align: left;">
                                        <span class="font-weight-bold d-block text-dark dark:text-white">{{ $p->name }}</span>
                                        <small class="text-muted"><i class="fa fa-barcode mr-1"></i>{{ $p->barcode ?? 'N/A' }}</small>
                                    </td>

                                    {{-- Category --}}
                                    <td>
                                        <span class="badge badge-secondary" style="padding: 4px 8px; font-size: 11px;">
                                            {{ $p->category?->name ?? 'N/A' }}
                                        </span>
                                        @if ($p->subCategory)
                                            <br><small class="text-muted">{{ $p->subCategory->name }}</small>
                                        @endif
                                    </td>

                                    {{-- Brand --}}
                                    <td>
                                        <span class="badge badge-light" style="padding: 4px 8px; font-size: 11px;">
                                            {{ $p->brand?->name ?? '-' }}
                                        </span>
                                    </td>

                                    {{-- Current Stock --}}
                                    <td>
                                        <span class="badge {{ $currentStock > 5 ? 'badge-success' : ($currentStock > 0 ? 'badge-warning' : 'badge-danger') }}" style="padding: 4px 8px; font-size: 12px; font-weight: 700;">
                                            {{ $currentStock }} {{ $p->unit?->name ?? 'Pcs' }}
                                        </span>
                                    </td>

                                    {{-- Sold Qty --}}
                                    <td>{{ number_format($item->sold_qty, 0) }}</td>

                                    {{-- Return Qty --}}
                                    <td class="text-danger">{{ number_format($item->return_qty, 0) }}</td>

                                    {{-- Net Sold Qty --}}
                                    <td class="font-weight-bold text-primary" style="font-size: 14px;">
                                        {{ number_format($item->net_qty, 0) }} {{ $p->unit?->name ?? 'Pcs' }}
                                    </td>

                                    {{-- Sales Amount --}}
                                    <td class="font-weight-bold" style="color: #10b981;">
                                        {{ $currency }} {{ number_format($item->net_amount, 2) }}
                                    </td>

                                    {{-- Purchase Cost --}}
                                    <td class="text-muted">
                                        {{ $currency }} {{ number_format($item->pur_cost, 2) }}
                                    </td>

                                    {{-- Gross Profit --}}
                                    <td class="font-weight-bold" style="color: #16a34a;">
                                        {{ $currency }} {{ number_format($item->profit, 2) }}
                                    </td>

                                    {{-- Margin % --}}
                                    <td>
                                        <span class="badge {{ $item->margin >= 20 ? 'badge-success' : ($item->margin >= 10 ? 'badge-info' : 'badge-warning') }}" style="padding: 4px 8px; font-size: 11px;">
                                            {{ number_format($item->margin, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center py-4 text-muted">
                                        <i class="feather icon-inbox d-block mb-2" style="font-size: 32px;"></i>
                                        {{ __('No sales data found for the selected criteria.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($paginatedList->count() > 0)
                            <tfoot style="background: #f8fafc; font-weight: bold;">
                                <tr>
                                    <td colspan="5" class="text-right uppercase">{{ __('Total Summary:') }}</td>
                                    <td>{{ number_format($paginatedList->sum('sold_qty'), 0) }}</td>
                                    <td class="text-danger">{{ number_format($paginatedList->sum('return_qty'), 0) }}</td>
                                    <td class="text-primary font-weight-bold">{{ number_format($paginatedList->sum('net_qty'), 0) }}</td>
                                    <td class="text-success font-weight-bold">{{ $currency }} {{ number_format($paginatedList->sum('net_amount'), 2) }}</td>
                                    <td class="text-muted">{{ $currency }} {{ number_format($paginatedList->sum('pur_cost'), 2) }}</td>
                                    <td class="text-success font-weight-bold">{{ $currency }} {{ number_format($paginatedList->sum('profit'), 2) }}</td>
                                    <td>{{ number_format($avgMargin, 1) }}%</td>
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