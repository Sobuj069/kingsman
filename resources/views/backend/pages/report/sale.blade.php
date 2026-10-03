@extends('backend.layouts.master')
@section('section-title', __('Report'))
@section('page-title', __('Sale Report'))

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
                <form action="{{ route('report.sale') }}">
                    <div class="form-row align-items-end">
                        <div class="col-md-2 col-12 mb-3">
                            <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Category') }}</label>
                            <select class="select2" name="category_id">
                                <option selected value="">{{ __('Select Category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if (env('APP_SUB_CATEGORY') == 'yes')
                            <div class="col-md-2 col-12 mb-3">
                                <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Sub Category') }}</label>
                                <select class="select2" name="sub_category_id">
                                    <option selected value="">{{ __('Select Sub Category') }}</option>
                                    @foreach ($subCategories ?? [] as $subCat)
                                        <option value="{{ $subCat->id }}" {{ request('sub_category_id') == $subCat->id ? 'selected' : '' }}>
                                            {{ $subCat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-md-2 col-12 mb-3">
                            <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Product') }}</label>
                            <select class="select2" name="product_id">
                                <option selected value="">{{ __('Select Product') }}</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                        @isset($oneProduct) @if ($product->id == $oneProduct->id) selected @endif @endisset>
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
                                <a href="{{ route('report.sale') }}" class="btn add_list_btn_reset flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;">
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
                    <h4 style="text-align: center; font-weight:bold; margin-top:25px;font-size:30px">{{ __('Sale Report') }}
                        ({{ isset($sdate) ? date('m-d-Y', strtotime($sdate)) : '' }} -
                        {{ isset($edate) ? date('m-d-Y', strtotime($edate)) : '' }})</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        @if (isset($invoiceItem))
                            <table class="table">
                                <thead>
                                    <tr class="header_bg">
                                        <th class="header_style_left">{{ __('SL.') }}</th>
                                        <th>{{ __('Date') }}</th>
                                        <th>{{ __('Invoice No') }}</th>
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Quantity') }}</th>
                                        <th>{{ __('Unit Price') }}</th>
                                        <th>{{ __('Sub Total') }}</th>
                                        <th>{{ __('Discount') }}</th>
                                        <th class="header_style_right">{{ __('Total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $sub_total = 0;
                                        $total_sale = 0;
                                        $total_goods = 0;
                                        $total_discount = 0;
                                        $total_delivery_charge = 0;
                                        $total_profit = 0;
                                        $total_amount = 0;
                                        $service_amount = 0;
                                        $total_qty = 0;
                                    @endphp
                                    @forelse($invoiceItem as $key => $data)
                                        @php
                                            $itemsQuery = App\Models\InvoiceItem::where('invoice_id', $data->id);
                                            if (isset($sdate) && isset($edate)) {
                                                $itemsQuery->whereBetween('date', [$sdate, $edate]);
                                            }
                                            $items = $itemsQuery->get();
                                            $saleAmt = 0;
                                            $goodCost = 0;
                                            foreach ($items as $item) {
                                                // Sale subtotal থেকে return বাদ
                                                $netSale = $item->inv_subtotal - $item->rtn_total;
                                                $saleAmt += $netSale;
                                                // Purchase subtotal থেকে return বাদ
                                                $unitCost =
                                                    $item->main_qty > 0 ? $item->pur_subtotal / $item->main_qty : 0;
                                                $returnCost = $unitCost * $item->rtn_main;
                                                $netPurchase = $item->pur_subtotal - $returnCost;
                                                $goodCost += $netPurchase;
                                                if (($item->product?->is_service ?? 0) == 1) {
                                                    $service_amount += $item->subtotal;
                                                }
                                                $total_qty += $item->main_qty;
                                            }
                                            $rowSubtotal = $items->sum('subtotal');
                                            $sub_total += $rowSubtotal;
                                            $grossProfit =
                                                $saleAmt - $goodCost - $data->discount_amount + $data->delivery_charge;
                                            $total_sale += $saleAmt;
                                            $total_goods += $goodCost;
                                            $total_discount += $data->discount_amount;
                                            $total_delivery_charge += $data->delivery_charge;
                                            $total_profit += $grossProfit;
                                            $total_amount += $rowSubtotal;
                                        @endphp
                                        @if ($items->count() > 0)
                                            <tr>
                                                <td class="table_data_style_left">{{ $key + 1 }}</td>
                                                <td>{{ date('m-d-Y', strtotime($items->first()?->date ?? $data->date)) }}</td>
                                                <td>{{ $data->invoice_no }}</td>
                                                <td>
                                                    @foreach ($items as $item)
                                                        <ul>
                                                            <li>
                                                                {{ $item->product?->name }}
                                                                @if (env('APP_IMEI') == 'yes' && !empty($item->imei))
                                                                    <br>
                                                                    IMEI: {{ str_replace(',', ', ', $item->imei) }}
                                                                @endif
                                                                @php
                                                                    $itemNetSale = $item->inv_subtotal - $item->rtn_total;
                                                                    $itemUnitCost = $item->main_qty > 0 ? $item->pur_subtotal / $item->main_qty : 0;
                                                                    $itemReturnCost = $itemUnitCost * $item->rtn_main;
                                                                    $itemNetPurchase = $item->pur_subtotal - $itemReturnCost;
                                                                    
                                                                    $total_inv_sub = $items->sum('inv_subtotal');
                                                                    $prop_discount = $total_inv_sub > 0 ? ($item->inv_subtotal / $total_inv_sub) * $data->discount_amount : 0;
                                                                    
                                                                    $itemProfit = $itemNetSale - $itemNetPurchase;
                                                                @endphp
                                                                <br><small class="text-success font-weight-bold">{{ __('Profit:') }} {{ number_format($itemProfit, 2) }}</small>
                                                            </li>
                                                        </ul>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @foreach ($items as $item)
                                                        @php
                                                            
                                                            $main_qty = $item->main_qty - $item->rtn_main;
                                                            $sub_qty = $item->sub_qty - $item->rtn_sub;
                                                        @endphp
                                                        @if ($item->product->unit?->related_unit == null)
                                                            <ul>
                                                                <li>{{ $main_qty . ' ' . $item->product->unit?->name }}
                                                                </li>
                                                            </ul>
                                                        @else
                                                            <ul>
                                                                <li>{{ $main_qty . ' ' . $item->product->unit->name . ' ' . $sub_qty . ' ' . $item->product->unit->related_unit->name }}
                                                                </li>
                                                            </ul>
                                                        @endif
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @foreach ($items as $item)
                                                        <ul>
                                                            <li>{{ $item->rate }}
                                                            </li>
                                                        </ul>
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @foreach ($items as $item)
                                                        <ul>
                                                            <li>{{ $item->subtotal }}
                                                            </li>
                                                        </ul>
                                                    @endforeach
                                                </td>
                                                <td>{{ number_format($data->discount_amount, 2) }}
                                                    {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                                </td>
                                                <td class="table_data_style_right">
                                                    {{ number_format($rowSubtotal, 2) }}
                                                    {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center text-danger no_data_style">{{ __('No Data Found') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                        <tr class="header_bg" style="font-size: 16px; font-weight: 700; font-family:sans-serif">
                                            <td class="header_style_left" colspan="4"><strong class="text-white">{{ __('Total:') }}</strong></td>
                                            <td class="text-white">{{ $total_qty }} pcs</td>
                                            <td></td>
                                            <td class="text-white">{{ number_format($total_sale, 2) }}</td>
                                            <td class="text-white"><strong>{{ number_format($total_discount, 2) }} {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</strong></td>
                                            <td class="header_style_right text-white"><strong>{{ number_format($total_amount, 2) }} {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</strong></td>
                                        </tr>
                                        <tr style="background: #1e293b; color: white; font-size: 15px; font-weight: 600;">
                                            <td colspan="3" class="text-left text-white py-2" style="border-top: 1px solid rgba(255,255,255,0.2);">
                                                {{ __('Total Return:') }} <strong class="text-danger">{{ number_format($total_return_amount ?? 0, 2) }} {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</strong>
                                            </td>
                                            <td colspan="3" class="text-center text-white py-2" style="border-top: 1px solid rgba(255,255,255,0.2);">
                                                {{ __('Net Sale:') }} <strong class="text-primary">{{ number_format(max(0, $total_amount - ($total_return_amount ?? 0)), 2) }} {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</strong>
                                            </td>
                                            <td colspan="3" class="text-right text-white py-2" style="border-top: 1px solid rgba(255,255,255,0.2);">
                                                {{ __('Overall Profit:') }} <strong class="text-success">{{ number_format($total_profit, 2) }} {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</strong>
                                            </td>
                                        </tr>
                                    </tfoot>
                            </table>
                            @if (env('APP_SERVICE') == 'yes')
                            {{-- <div>
                                @php
                                    $product_amount = $total_amount - $service_amount;
                                @endphp
                                <h4 class="text-right">Total Service Amount : {{ $service_amount }}
                                    {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</h4>
                                <h4 class="text-right">Total Product Amount : {{ $product_amount }}
                                    {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</h4>
                            </div> --}}
                            @endif
                        @else
                            <div class="col-md-12" style="padding-bottom: 30px;">
                                <div class="alert alert-danger text-right" role="alert"> {{ __('Please Select Start and End Month') }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
