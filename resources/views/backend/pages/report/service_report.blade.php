@extends('backend.layouts.master')
@section('section-title', __('Report'))
@section('page-title', __('Service Report'))

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
                <form action="{{ route('report.service') }}">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <select class="select2" name="product_id">
                                <option selected value="">{{ __('Select Service/Product') }}</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}"
                                        @isset($oneProduct) @if ($product->id == $oneProduct->id) selected @endif @endisset>
                                        {{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <input type="date" name="start_date" class="form-control"
                                value="{{ isset($sdate) ? date('Y-m-d', strtotime($sdate)) : '' }}">
                        </div>
                        <div class="form-group col-md-4">
                            <input type="date" name="end_date" class="form-control"
                                value="{{ isset($edate) ? date('Y-m-d', strtotime($edate)) : '' }}">
                        </div>
                    </div>
                    <div class="form-row mt-2">
                        <div class="form-group col-12">
                            <button class="btn add_list_btn" type="submit">
                                <i class="fa fa-sliders"></i> {{ __('Filter') }}
                            </button>
                            <a href="{{ route('report.service') }}" class="btn add_list_btn_reset">{{ __('Reset') }}</a>
                            <a href="" class="btn add_list_btn float-right" onclick="window.print()">{{ __('Print') }}</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card m-b-30 print_area card_style">
                <div class="card-header">
                    <h4 style="text-align: center; font-weight:bold; margin-top:25px;font-size:30px">{{ __('Service Report') }}
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
                                        <th>{{ __('Customer') }}</th>
                                        <th>{{ __('Service/Product') }}</th>
                                        <th>{{ __('Quantity') }}</th>
                                        <th>{{ __('Sale Price') }}</th>
                                        <th>{{ __('Cost Price') }}</th>
                                        <th>{{ __('Total Sale') }}</th>
                                        <th>{{ __('Discount') }}</th>
                                        <th class="header_style_right">{{ __('Total Profit') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $sl = 1;
                                        $grand_total_qty = 0;
                                        $grand_total_sale = 0;
                                        $grand_total_discount = 0;
                                        $grand_total_profit = 0;
                                    @endphp
                                    @forelse($invoiceItem as $invoice)
                                        @php
                                            $all_invoice_items = App\Models\ServiceInvoiceItem::where('service_invoice_id', $invoice->id)->get();
                                            $total_invoice_sale = $all_invoice_items->sum('subtotal');

                                            $items = App\Models\ServiceInvoiceItem::where('service_invoice_id', $invoice->id);
                                            if (!empty($oneProduct)) {
                                                $items->where('product_id', $oneProduct->id);
                                            }
                                            $items = $items->get();
                                        @endphp
                                        @foreach($items as $item)
                                            @php
                                                $qty = $item->quantity;
                                                $sale_subtotal = $item->subtotal;
                                                
                                                // Proportion the invoice level discount to this item
                                                $item_discount = 0;
                                                if ($total_invoice_sale > 0 && $invoice->discount > 0) {
                                                    $item_discount = ($sale_subtotal / $total_invoice_sale) * $invoice->discount;
                                                }

                                                $item_cost_price = ($item->cost_price > 0) ? $item->cost_price : ($item->product?->purchase_price ?? 0);
                                                $cost_subtotal = $item_cost_price * $qty;
                                                $profit = $sale_subtotal - $cost_subtotal - $item_discount;
 
                                                $grand_total_qty += $qty;
                                                $grand_total_sale += $sale_subtotal;
                                                $grand_total_discount += $item_discount;
                                                $grand_total_profit += $profit;
                                            @endphp
                                            <tr>
                                                <td class="table_data_style_left">{{ $sl++ }}</td>
                                                <td>{{ date('m-d-Y', strtotime($invoice->date)) }}</td>
                                                <td>{{ $invoice->invoice_no }}</td>
                                                <td>{{ $invoice->customer ? $invoice->customer->name : '' }}</td>
                                                <td>{{ $item->product?->name }}</td>
                                                <td>{{ $qty }}</td>
                                                <td>{{ number_format($item->price, 2) }}</td>
                                                <td>{{ number_format($item_cost_price, 2) }}</td>
                                                <td>{{ number_format($sale_subtotal, 2) }}</td>
                                                <td>{{ number_format($item_discount, 2) }}</td>
                                                <td class="table_data_style_right font-weight-bold {{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format($profit, 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center text-danger no_data_style">{{ __('No Data Found') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="header_bg" style="font-size: 16px; font-weight: 700; font-family:sans-serif; background: #000ce2; color: white;">
                                        <td class="header_style_left" style="background: #000ce2; color: white;" colspan="5"> <strong>{{ __('Total:') }}</strong></td>
                                        <td style="background: #000ce2; color: white;"><strong>{{ $grand_total_qty }}</strong></td>
                                        <td colspan="2" style="background: #000ce2; color: white;"></td>
                                        <td style="background: #000ce2; color: white;"><strong>{{ number_format($grand_total_sale, 2) }}</strong></td>
                                        <td style="background: #000ce2; color: white;"><strong>{{ number_format($grand_total_discount, 2) }}</strong></td>
                                        <td class="header_style_right" style="background: #000ce2; color: white;"><strong>{{ number_format($grand_total_profit, 2) }}</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
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
