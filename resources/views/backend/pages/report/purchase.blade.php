@extends('backend.layouts.master')
@section('section-title', __('Report'))
@section('page-title', __('Purchase Report'))

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
                <form action="{{ route('report.purchase') }}">
                    <div class="form-row align-items-end">
                        <div class="col-md-2 col-12 mb-3">
                            <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Supplier') }}</label>
                            <select class="select2" name="supplier_id">
                                <option value="">{{ __('Select Supplier') }}</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ (isset($supplier_id) && $supplier_id == $supplier->id) ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 col-12 mb-3">
                            <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Product') }}</label>
                            <select class="select2" name="product_id">
                                <option selected value="">{{ __('Select Product') }}</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                        {{ (isset($product_id) && $product_id == $product->id) ? 'selected' : '' }}>
                                        {{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
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
                        <div class="col-md-4 col-12 mb-3">
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <button type="submit" class="btn add_list_btn flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa fa-sliders mr-1"></i> {{ __('Filter') }}
                                </button>
                                <a href="{{ route('report.purchase') }}" class="btn add_list_btn_reset flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;">
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
            <div class="card m-b-30 print_area card_style">
                <div class="card-header">
                    <h4 style="text-align: center; font-weight:bold; margin-top:25px;font-size:30px">{{ __('Purchase Report') }}
                        ({{ isset($sdate) ? date('m-d-Y', strtotime($sdate)) : '' }} -
                        {{ isset($edate) ? date('m-d-Y', strtotime($edate)) : '' }})</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        @if (isset($purchaseItem))
                            <table class="table">
                                <thead>
                                    <tr class="header_bg">
                                        <th class="header_style_left">{{ __('SL.') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Purchase No') }}</th>
                                        <th>{{ __('Supplier') }}</th>
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Quantity') }}</th>
                                        <th>{{ __('Return Qty') }}</th>
                                        <th>{{ __('Unit Price') }}</th>
                                        <th class="header_style_right">{{ __('Sub Total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $sub_total = 0;
                                        $total_qty = 0;
                                        $total_rtn_qty = 0;
                                    @endphp
                                    @forelse($purchaseItem as $key => $data)
                                        @php
                                            if ($data->product->unit->related_unit == null) {
                                                // Purchase qty
                                                $qty = $data->main_qty . ' ' . $data->product->unit->name;

                                                // Return qty
                                                $rtn_qty = $data->rtn_main . ' ' . $data->product->unit->name;
                                            } else {
                                                // Purchase qty convert
                                                $main_qty = $data->main_qty;
                                                $pur_total_main = $main_qty * $data->product->unit->related_value;
                                                $sub_qty = $data->sub_qty ?? 0;

                                                $pur_total_qty = $pur_total_main + $sub_qty;

                                                $check = $pur_total_qty / $data->product->unit->related_value;

                                                if (is_integer($check)) {
                                                    $qty =
                                                        $check .
                                                        ' ' .
                                                        $data->product->unit->name .
                                                        ' 0 ' .
                                                        $data->product->unit->related_unit->name;
                                                } else {
                                                    $main_value = floor($check) * $data->product->unit->related_value;
                                                    $sub_value = $pur_total_qty - $main_value;

                                                    $qty =
                                                        floor($check) .
                                                        ' ' .
                                                        $data->product->unit->name .
                                                        ' ' .
                                                        $sub_value .
                                                        ' ' .
                                                        $data->product->unit->related_unit->name;
                                                }

                                                // 🔹 Return qty convert
                                                $rtn_main = $data->rtn_main ?? 0;
                                                $rtn_sub = $data->rtn_sub ?? 0;

                                                $rtn_total_main = $rtn_main * $data->product->unit->related_value;
                                                $rtn_total_qty = $rtn_total_main + $rtn_sub;

                                                $rtn_check = $rtn_total_qty / $data->product->unit->related_value;

                                                if (is_integer($rtn_check)) {
                                                    $rtn_qty =
                                                        $rtn_check .
                                                        ' ' .
                                                        $data->product->unit->name .
                                                        ' 0 ' .
                                                        $data->product->unit->related_unit->name;
                                                } else {
                                                    $rtn_main_value =
                                                        floor($rtn_check) * $data->product->unit->related_value;
                                                    $rtn_sub_value = $rtn_total_qty - $rtn_main_value;

                                                    $rtn_qty =
                                                        floor($rtn_check) .
                                                        ' ' .
                                                        $data->product->unit->name .
                                                        ' ' .
                                                        $rtn_sub_value .
                                                        ' ' .
                                                        $data->product->unit->related_unit->name;
                                                }
                                            }

                                            $sub_total += $data->subtotal;
                                            $total_qty += $data->main_qty;
                                            $total_rtn_qty += ($data->rtn_main ?? 0);
                                        @endphp
                                        <tr>
                                            <td class="table_data_style_left">{{ $key + 1 }}</td>
                                            <td>{{ date('m-d-Y', strtotime($data->purchase->date)) }}</td>
                                            <td>{{ $data->purchase->purchase_no }}</td>
                                            <td>{{ $data->purchase->supplier?->name ?? '-' }}</td>
                                            <td>{{ $data->product->name }}</td>
                                            <td>{{ $qty }}</td>
                                            <td>{{ $rtn_qty }}</td>
                                            <td>{{ number_format($data->rate, 2) }}
                                                {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                            </td>
                                            <td class="table_data_style_right">{{ number_format($data->subtotal, 2) }}
                                                {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-danger no_data_style">{{ __('No Data Found') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="header_bg text-white" style="font-size: 16px; font-weight: 700; font-family: sans-serif; background: #000ce2 !important; color: #ffffff !important;">
                                        <td class="header_style_left text-white" colspan="5" style="background: #000ce2 !important; color: #ffffff !important; text-align: right; padding-right: 15px;">
                                            <strong>{{ __('Total :') }}</strong>
                                        </td>
                                        <td class="text-white text-center" style="background: #000ce2 !important; color: #ffffff !important;">
                                            <strong>{{ $total_qty }}</strong>
                                        </td>
                                        <td class="text-white text-center" style="background: #000ce2 !important; color: #ffffff !important;">
                                            <strong>{{ $total_rtn_qty }}</strong>
                                        </td>
                                        <td class="text-white" style="background: #000ce2 !important; color: #ffffff !important;"></td>
                                        <td class="header_style_right text-white text-right" style="background: #000ce2 !important; color: #ffffff !important;">
                                            <strong>
                                                {{ number_format($sub_total, 2) }}
                                                {{ empty(get_setting('com_currency')) ? '' : get_setting('com_currency') }}
                                            </strong>
                                        </td>
                                    </tr>
                                </tfoot>
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
