@extends('backend.layouts.master')
@section('section-title', __('Report'))
@section('page-title', __('Profit Loss Report'))

@push('css')
    <style>
        @media print {

            table,
            table th,
            table td {
                color: black !important;
            }

            #h-hide {
                display: none;
            }
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-body card_style mb-2" style="margin-top: -5px;" id="h-hide">
                <form action="{{ route('report.profit-loss') }}">
                    <div class="form-row align-items-end">
                        <div class="col-md-3 col-12 mb-3">
                            <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Start Month') }}</label>
                            <input type="month" name="start_month" placeholder="{{ __('Enter Start Month') }}" class="form-control" style="height: 38px !important;"
                                value="{{ isset($sdate) ? date('Y-m', strtotime($sdate)) : '' }}">
                        </div>
                        <div class="col-md-3 col-12 mb-3">
                            <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('End Month') }}</label>
                            <input type="month" name="end_month" placeholder="{{ __('Enter End Month') }}" class="form-control" style="height: 38px !important;"
                                value="{{ isset($edate) ? date('Y-m', strtotime($edate)) : '' }}">
                        </div>
                        <div class="col-md-6 col-12 mb-3">
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <button type="submit" class="btn add_list_btn flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa fa-sliders mr-1"></i> {{ __('Filter') }}
                                </button>
                                <a href="{{ route('report.profit-loss') }}" class="btn add_list_btn_reset flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;">
                                    <i class="feather icon-refresh-cw mr-1"></i> {{ __('Reset') }}
                                </a>
                                <a href="#" class="btn add_list_btn flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;" onclick="window.print()">
                                    <i class="feather icon-printer mr-1"></i> {{ __('Print') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card m-b-30 card_style print_area">
                <div class="card-header">
                    <h4 style="text-align: center; font-weight:bold; margin-top:25px;font-size:30px">{{ __('Profit Loss') }}
                        ({{ isset($sdate) ? date('F Y', strtotime($sdate)) : '' }} -
                        {{ isset($edate) ? date('F Y', strtotime($edate)) : '' }})</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        @if (isset($groupedDates))
                            <table class="table">
                                <thead>
                                    <tr class="header_bg">
                                        <th class="header_style_left">{{ __('Month') }}</th>
                                        <th>{{ __('Sales') }}</th>
                                        <th>{{ __('Cost of Goods Sold') }}</th>
                                        <th>{{ __('Gross Profit') }} </th>
                                        <th>{{ __('Expenses') }}</th>
                                        <th class="header_style_right">{{ __('Net Profit') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($groupedDates as $key => $data)
                                        @php
                                            $userBranchId = auth()->user()->branch_id;
                                            $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

                                            $mVal = date('m', strtotime($key));
                                            $yVal = date('Y', strtotime($key));

                                            if ($userBranchId == 1) {
                                                if ($filterBranchId) {
                                                    $grossSale = App\Models\InvoiceItem::getFakeSum(App\Models\InvoiceItem::where('branch_id', $filterBranchId)->whereMonth('date', $mVal)->whereYear('date', $yVal), 'inv_subtotal');
                                                    $returnSale = App\Models\ReturnItem::where('branch_id', $filterBranchId)->whereMonth('date', $mVal)->whereYear('date', $yVal)->sum('subtotal');
                                                    $sale_amount = max(0, $grossSale - $returnSale);

                                                    $grossPur = App\Models\InvoiceItem::getFakeSum(App\Models\InvoiceItem::where('branch_id', $filterBranchId)->whereMonth('date', $mVal)->whereYear('date', $yVal), 'pur_subtotal');
                                                    $returnPur = App\Models\ReturnItem::where('branch_id', $filterBranchId)->whereMonth('date', $mVal)->whereYear('date', $yVal)->sum('pur_subtotal');
                                                    $purchase_amount = max(0, $grossPur - $returnPur);

                                                    $expense = App\Models\Expense::where('branch_id', $filterBranchId)
                                                        ->whereMonth('date', $mVal)
                                                        ->whereYear('date', $yVal)
                                                        ->sum('amount');
                                                } else {
                                                    $grossSale = App\Models\InvoiceItem::getFakeSum(App\Models\InvoiceItem::whereMonth('date', $mVal)->whereYear('date', $yVal), 'inv_subtotal');
                                                    $returnSale = App\Models\ReturnItem::whereMonth('date', $mVal)->whereYear('date', $yVal)->sum('subtotal');
                                                    $sale_amount = max(0, $grossSale - $returnSale);

                                                    $grossPur = App\Models\InvoiceItem::getFakeSum(App\Models\InvoiceItem::whereMonth('date', $mVal)->whereYear('date', $yVal), 'pur_subtotal');
                                                    $returnPur = App\Models\ReturnItem::whereMonth('date', $mVal)->whereYear('date', $yVal)->sum('pur_subtotal');
                                                    $purchase_amount = max(0, $grossPur - $returnPur);

                                                    $expense = App\Models\Expense::whereMonth('date', $mVal)
                                                        ->whereYear('date', $yVal)
                                                        ->sum('amount');
                                                }
                                            } else {
                                                $grossSale = App\Models\InvoiceItem::getFakeSum(App\Models\InvoiceItem::where('branch_id', $userBranchId)->whereMonth('date', $mVal)->whereYear('date', $yVal), 'inv_subtotal');
                                                $returnSale = App\Models\ReturnItem::where('branch_id', $userBranchId)->whereMonth('date', $mVal)->whereYear('date', $yVal)->sum('subtotal');
                                                $sale_amount = max(0, $grossSale - $returnSale);

                                                $grossPur = App\Models\InvoiceItem::getFakeSum(App\Models\InvoiceItem::where('branch_id', $userBranchId)->whereMonth('date', $mVal)->whereYear('date', $yVal), 'pur_subtotal');
                                                $returnPur = App\Models\ReturnItem::where('branch_id', $userBranchId)->whereMonth('date', $mVal)->whereYear('date', $yVal)->sum('pur_subtotal');
                                                $purchase_amount = max(0, $grossPur - $returnPur);

                                                $expense = App\Models\Expense::where('branch_id', $userBranchId)
                                                    ->whereMonth('date', $mVal)
                                                    ->whereYear('date', $yVal)
                                                    ->sum('amount');
                                            }

                                            $profit = $sale_amount - $purchase_amount;
                                            $net_profit = (float) ($profit - $expense);
                                        @endphp
                                        <tr>
                                            <td class="table_data_style_left">{{ date('F Y', strtotime($key)) }}</td>
                                            <td>{{ number_format($sale_amount, 2) }}
                                                {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                            </td>
                                            <td>{{ number_format($purchase_amount, 2) }}
                                                {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                            </td>
                                            <td>{{ number_format($profit, 2) }}
                                                {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                            </td>
                                            <td>{{ number_format($expense, 2) }}
                                                {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                            </td>
                                            <td class="table_data_style_right">
                                                {{ $net_profit >= 0 ? number_format($net_profit, 2) : '(' . number_format(abs($net_profit), 2) . ')' }}
                                                {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center text-danger no_data_style">{{ __('No Data Found') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        @else
                            <div class="col-md-12" style="padding-bottom: 30px;">
                                <div class="alert alert-danger text-center" role="alert"> {{ __('Please Select Start and End Month') }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
