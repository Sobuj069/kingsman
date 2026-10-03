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
            <div class="card card-body card_style mb-2" style="margin-top: -5px" id="h-hide">
                <form action="{{ route('report.user.sell') }}">
                    <div class="form-row align-items-end">
                        <div class="col-md-3 col-12 mb-3">
                            <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Select user') }}</label>
                            <select class="select2" name="user_id">
                                <option selected value="">{{ __('Select user') }}</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
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
                        <div class="col-md-5 col-12 mb-3">
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <button type="submit" class="btn add_list_btn flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;">
                                    <i class="fa fa-sliders mr-1"></i> {{ __('Filter') }}
                                </button>
                                <a href="{{ route('report.user.sell') }}" class="btn add_list_btn_reset flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;">
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
                                        <th>{{ __('Sale By') }}</th>
                                        <th>{{ __('Discount') }}</th>
                                        <th class="header_style_right">{{ __('Total') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $sub_total = 0;
                                    @endphp
                                    @forelse($invoiceItem as $key => $data)
                                        @php
                                            $itemsQuery = App\Models\InvoiceItem::where('invoice_id', $data->id);
                                            if (isset($sdate) && isset($edate)) {
                                                $itemsQuery->whereBetween('date', [$sdate, $edate]);
                                            }
                                            $items = $itemsQuery->get();
                                            $rowSubtotal = $items->sum('subtotal');
                                            $sub_total += $rowSubtotal;
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
                                                        </li>
                                                    </ul>
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach ($items as $item)
                                                    <ul>
                                                        <li>{{ $item->main_qty }}</li>
                                                    </ul>
                                                @endforeach
                                            </td>
                                            <td>
                                                @foreach ($items as $item)
                                                    <ul>
                                                        <li>{{ $item->rate }}</li>
                                                    </ul>
                                                @endforeach
                                            </td>
                                            <td>{{ number_format($data->estimated_amount, 2) }}
                                                {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                            </td>
                                            <td>{{ $data->user->name }}</td>
                                            <td>{{ number_format($data->discount_amount, 2) }}
                                                {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                            </td>
                                            <td class="table_data_style_right">{{ number_format($rowSubtotal, 2) }}
                                                {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                            </td>
                                        </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center text-danger no_data_style">{{ __('No Data Found') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfooter>
                                    <tr class="header_bg" style=" font-size: 20px; font-width: 700; font-family:sans-serif">
                                        <td class="header_style_left" colspan="6"></td>
                                        <td colspan="2"> <strong class="text-white"> {{ __('Total Price:') }} </strong></td>
                                        <td class="header_style_right" colspan="2"><strong class="text-white">
                                                {{ number_format($sub_total, 2) }}
                                                {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                            </strong></td>
                                    </tr>
                                </tfooter>
                            </table>
                        @else
                            <div class="col-md-12" style="padding-bottom: 30px;">
                                <div class="alert alert-danger text-center" role="alert"> {{ __('Please Select Start and End Month') }}</div>
                            </div>
                        @endif
                    </div>
                </div>
                <div>

                </div>
            </div>
        </div>
    </div>
@endsection
