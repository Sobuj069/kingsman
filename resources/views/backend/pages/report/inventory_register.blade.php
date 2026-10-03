@extends('backend.layouts.master')
@section('section-title', __('Report'))
@section('page-title', __('Inventory Register'))

@push('css')
    <style>
        .table-register th {
            font-size: 11px !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            padding: 8px 6px !important;
            vertical-align: middle !important;
            text-align: center;
            border: 1px solid #dee2e6 !important;
        }
        .table-register td {
            font-size: 12px !important;
            padding: 8px 6px !important;
            vertical-align: middle !important;
            border: 1px solid #dee2e6 !important;
        }
        .bg-group-header {
            background-color: #f1f5f9 !important;
            font-weight: bold;
        }
        @media print {
            table, table th, table td {
                color: black !important;
                border: 1px solid #000 !important;
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
                <form action="{{ route('report.inventory.register') }}">
                    <div class="form-row align-items-end mb-2">
                        <div class="form-group col-md-3">
                            <label class="font-weight-bold text-muted small uppercase">{{ __('Date Range') }}</label>
                            <div class="d-flex align-items-center">
                                <input type="date" name="start_date" class="form-control" style="height: 38px !important;"
                                    value="{{ isset($sdate) ? date('Y-m-d', strtotime($sdate)) : '' }}">
                                <span class="mx-2 text-muted">{{ __('to') }}</span>
                                <input type="date" name="end_date" class="form-control" style="height: 38px !important;"
                                    value="{{ isset($edate) ? date('Y-m-d', strtotime($edate)) : '' }}">
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="font-weight-bold text-muted small uppercase">{{ __('Product') }}</label>
                            <select name="product_id" class="select2 form-control">
                                <option value="">{{ __('All Products') }}</option>
                                @foreach ($allProducts as $p)
                                    <option value="{{ $p->id }}" {{ $product_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="font-weight-bold text-muted small uppercase">{{ __('Category') }}</label>
                            <select name="category_id" class="select2 form-control">
                                <option value="">{{ __('All Categories') }}</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}" {{ $category_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="font-weight-bold text-muted small uppercase">{{ __('Brand') }}</label>
                            <select name="brand_id" class="select2 form-control">
                                <option value="">{{ __('All Brands') }}</option>
                                @foreach ($brands as $b)
                                    <option value="{{ $b->id }}" {{ $brand_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="font-weight-bold text-muted small uppercase">{{ __('Group By') }}</label>
                            <select name="group_by" class="form-control" style="height: 38px !important;">
                                <option value="date_wise" {{ $group_by == 'date_wise' ? 'selected' : '' }}>{{ __('Date Wise') }}</option>
                                <option value="product_wise" {{ $group_by == 'product_wise' ? 'selected' : '' }}>{{ __('Product Wise') }}</option>
                            </select>
                        </div>
                        <div class="form-group col-md-1 text-right">
                            <button class="btn add_list_btn btn-block" type="submit" style="height: 38px !important;">
                                <i class="fa fa-sliders"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card m-b-30 print_area card_style">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 font-weight-bold">{{ __('Inventory Register') }}</h4>
                    <button class="btn btn-sm add_list_btn" onclick="window.print()"><i class="feather icon-printer"></i> {{ __('Print') }}</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-register text-center">
                            <thead class="bg-primary text-white header_bg">
                                <tr>
                                    <th rowspan="2" class="header_style_left">#</th>
                                    @if ($group_by == 'date_wise')
                                        <th rowspan="2">{{ __('Date') }}</th>
                                    @endif
                                    <th rowspan="2">{{ __('Product') }}</th>
                                    <th colspan="2">{{ __('Opening Balance') }}</th>
                                    <th colspan="4">{{ __('Purchase') }}</th>
                                    <th colspan="2">{{ __('Damage') }}</th>
                                    <th colspan="2">{{ __('Available Stock') }}</th>
                                    <th colspan="2">{{ __('Transfer') }}</th>
                                    <th colspan="2">{{ __('Adjustment') }}</th>
                                    <th colspan="4">{{ __('Sales') }}</th>
                                    <th colspan="2">{{ __('Ending Balance') }}</th>
                                    <th colspan="2" class="header_style_right">{{ __('Unit') }}</th>
                                </tr>
                                <tr>
                                    <!-- Opening Balance -->
                                    <th>{{ __('Qty') }}</th>
                                    <th>{{ __('Value') }}</th>
                                    <!-- Purchase -->
                                    <th>{{ __('Qty') }}</th>
                                    <th>{{ __('Val') }}</th>
                                    <th>{{ __('Return Qty') }}</th>
                                    <th>{{ __('Return Val') }}</th>
                                    <!-- Damage -->
                                    <th>{{ __('Qty') }}</th>
                                    <th>{{ __('Value') }}</th>
                                    <!-- Available Stock -->
                                    <th>{{ __('Qty') }}</th>
                                    <th>{{ __('Value') }}</th>
                                    <!-- Transfer -->
                                    <th>{{ __('Out Qty') }}</th>
                                    <th>{{ __('In Qty') }}</th>
                                    <!-- Adjustment -->
                                    <th>{{ __('In Qty') }}</th>
                                    <th>{{ __('Out Qty') }}</th>
                                    <!-- Sales -->
                                    <th>{{ __('Sale Out Qty') }}</th>
                                    <th>{{ __('Sale Out Val') }}</th>
                                    <th>{{ __('Return Qty') }}</th>
                                    <th>{{ __('Return Val') }}</th>
                                    <!-- Ending Balance -->
                                    <th>{{ __('Qty') }}</th>
                                    <th>{{ __('Value') }}</th>
                                    <!-- Unit -->
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Name') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $tot_opening_qty = 0; $tot_opening_val = 0;
                                    $tot_pur_qty = 0; $tot_pur_val = 0;
                                    $tot_pur_rtn_qty = 0; $tot_pur_rtn_val = 0;
                                    $tot_dmg_qty = 0; $tot_dmg_val = 0;
                                    $tot_avl_qty = 0; $tot_avl_val = 0;
                                    $tot_trf_out = 0; $tot_trf_in = 0;
                                    $tot_adjust_in = 0; $tot_adjust_out = 0;
                                    $tot_sale_qty = 0; $tot_sale_val = 0;
                                    $tot_sale_rtn_qty = 0; $tot_sale_rtn_val = 0;
                                    $tot_end_qty = 0; $tot_end_val = 0;
                                @endphp
                                @forelse($rows as $idx => $row)
                                    @php
                                        $tot_opening_qty += $row['opening_qty'];
                                        $tot_opening_val += $row['opening_value'];
                                        $tot_pur_qty += $row['purchase_qty'];
                                        $tot_pur_val += $row['purchase_val'];
                                        $tot_pur_rtn_qty += $row['purchase_return_qty'];
                                        $tot_pur_rtn_val += $row['purchase_return_val'];
                                        $tot_dmg_qty += $row['damage_qty'];
                                        $tot_dmg_val += $row['damage_val'];
                                        $tot_avl_qty += $row['available_qty'];
                                        $tot_avl_val += $row['available_value'];
                                        $tot_trf_out += $row['transfer_out_qty'];
                                        $tot_trf_in += $row['transfer_in_qty'];
                                        $tot_adjust_in += $row['adjust_in_qty'];
                                        $tot_adjust_out += $row['adjust_out_qty'];
                                        $tot_sale_qty += $row['sales_out_qty'];
                                        $tot_sale_val += $row['sales_out_val'];
                                        $tot_sale_rtn_qty += $row['sales_return_qty'];
                                        $tot_sale_rtn_val += $row['sales_return_val'];
                                        $tot_end_qty += $row['ending_qty'];
                                        $tot_end_val += $row['ending_value'];
                                    @endphp
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        @if ($group_by == 'date_wise')
                                            <td>{{ $row['date'] }}</td>
                                        @endif
                                        <td class="text-left font-weight-bold">{{ $row['product_name'] }}</td>
                                        <!-- Opening Balance -->
                                        <td>{{ number_format($row['opening_qty'], 2) }}</td>
                                        <td>{{ number_format($row['opening_value'], 2) }}</td>
                                        <!-- Purchase -->
                                        <td>{{ number_format($row['purchase_qty'], 2) }}</td>
                                        <td>{{ number_format($row['purchase_val'], 2) }}</td>
                                        <td>{{ number_format($row['purchase_return_qty'], 2) }}</td>
                                        <td>{{ number_format($row['purchase_return_val'], 2) }}</td>
                                        <!-- Damage -->
                                        <td>{{ number_format($row['damage_qty'], 2) }}</td>
                                        <td>{{ number_format($row['damage_val'], 2) }}</td>
                                        <!-- Available Stock -->
                                        <td>{{ number_format($row['available_qty'], 2) }}</td>
                                        <td>{{ number_format($row['available_value'], 2) }}</td>
                                        <!-- Transfer -->
                                        <td>{{ number_format($row['transfer_out_qty'], 2) }}</td>
                                        <td>{{ number_format($row['transfer_in_qty'], 2) }}</td>
                                        <!-- Adjustment -->
                                        <td>{{ number_format($row['adjust_in_qty'], 2) }}</td>
                                        <td>{{ number_format($row['adjust_out_qty'], 2) }}</td>
                                        <!-- Sales -->
                                        <td>{{ number_format($row['sales_out_qty'], 2) }}</td>
                                        <td>{{ number_format($row['sales_out_val'], 2) }}</td>
                                        <td>{{ number_format($row['sales_return_qty'], 2) }}</td>
                                        <td>{{ number_format($row['sales_return_val'], 2) }}</td>
                                        <!-- Ending Balance -->
                                        <td class="font-weight-bold text-primary">{{ number_format($row['ending_qty'], 2) }}</td>
                                        <td class="font-weight-bold text-primary">{{ number_format($row['ending_value'], 2) }}</td>
                                        <!-- Unit -->
                                        <td>{{ number_format($row['product']->purchase_price, 2) }}</td>
                                        <td>{{ $row['product']->unit?->name ?? 'pcs' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="30" class="text-center text-danger font-weight-bold">{{ __('No Data Found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if(count($rows) > 0)
                                <tfoot class="font-weight-bold header_bg text-white">
                                    <tr>
                                        <td colspan="{{ $group_by == 'date_wise' ? 3 : 2 }}" class="text-right">{{ __('TOTAL') }}:</td>
                                        <!-- Opening Balance -->
                                        <td>{{ number_format($tot_opening_qty, 2) }}</td>
                                        <td>{{ number_format($tot_opening_val, 2) }}</td>
                                        <!-- Purchase -->
                                        <td>{{ number_format($tot_pur_qty, 2) }}</td>
                                        <td>{{ number_format($tot_pur_val, 2) }}</td>
                                        <td>{{ number_format($tot_pur_rtn_qty, 2) }}</td>
                                        <td>{{ number_format($tot_pur_rtn_val, 2) }}</td>
                                        <!-- Damage -->
                                        <td>{{ number_format($tot_dmg_qty, 2) }}</td>
                                        <td>{{ number_format($tot_dmg_val, 2) }}</td>
                                        <!-- Available Stock -->
                                        <td>{{ number_format($tot_avl_qty, 2) }}</td>
                                        <td>{{ number_format($tot_avl_val, 2) }}</td>
                                        <!-- Transfer -->
                                        <td>{{ number_format($tot_trf_out, 2) }}</td>
                                        <td>{{ number_format($tot_trf_in, 2) }}</td>
                                        <!-- Adjustment -->
                                        <td>{{ number_format($tot_adjust_in, 2) }}</td>
                                        <td>{{ number_format($tot_adjust_out, 2) }}</td>
                                        <!-- Sales -->
                                        <td>{{ number_format($tot_sale_qty, 2) }}</td>
                                        <td>{{ number_format($tot_sale_val, 2) }}</td>
                                        <td>{{ number_format($tot_sale_rtn_qty, 2) }}</td>
                                        <td>{{ number_format($tot_sale_rtn_val, 2) }}</td>
                                        <!-- Ending Balance -->
                                        <td>{{ number_format($tot_end_qty, 2) }}</td>
                                        <td>{{ number_format($tot_end_val, 2) }}</td>
                                        <!-- Unit -->
                                        <td colspan="2"></td>
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
