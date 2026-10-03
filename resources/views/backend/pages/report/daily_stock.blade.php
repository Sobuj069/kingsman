@extends('backend.layouts.master')
@section('section-title', __('Report'))
@section('page-title', __('Daily Stock'))

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
                <form action="{{ route('report.daily.stock') }}">
                    <div class="form-row align-items-end mb-2">
                        <div class="form-group col-md-3">
                            <label class="font-weight-bold text-muted small uppercase">{{ __('Barcode / Search') }}</label>
                            <input type="text" name="barcode" class="form-control barcode-filter-input" data-barcode-input style="height: 38px !important;"
                                placeholder="{{ __('Scan Barcode / Product') }}" value="{{ $barcode ?? '' }}">
                        </div>
                        <div class="form-group col-md-3">
                            <label class="font-weight-bold text-muted small uppercase">{{ __('Start Date') }}</label>
                            <input type="date" name="start_date" class="form-control" style="height: 38px !important;"
                                value="{{ isset($sdate) ? date('Y-m-d', strtotime($sdate)) : '' }}">
                        </div>
                        <div class="form-group col-md-3">
                            <label class="font-weight-bold text-muted small uppercase">{{ __('End Date') }}</label>
                            <input type="date" name="end_date" class="form-control" style="height: 38px !important;"
                                value="{{ isset($edate) ? date('Y-m-d', strtotime($edate)) : '' }}">
                        </div>
                        <div class="form-group col-md-3 text-right">
                            <div class="d-flex flex-wrap justify-content-between align-items-center">
                                <div class="mb-2 mb-sm-0">
                                    <button class="btn add_list_btn" type="submit" style="padding-top: 8px !important; padding-bottom: 8px !important;">
                                        <i class="fa fa-sliders mr-1"></i> {{ __('Filter') }}
                                    </button>
                                    <a href="{{ route('report.daily.stock') }}" class="btn add_list_btn_reset ml-1" style="padding-top: 8px !important; padding-bottom: 8px !important;">{{ __('Reset') }}</a>
                                </div>
                                <div class="mb-2 mb-sm-0">
                                    <a href="#" class="btn add_list_btn" style="padding-top: 8px !important; padding-bottom: 8px !important;" onclick="window.print()">
                                        <i class="feather icon-printer mr-1"></i> {{ __('Print') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card m-b-30 print_area card_style">
                <div class="card-header">
                    <h4 style="text-align: center; font-weight:bold;font-size:30px">{{ __('Daily Stock Report') }}
                        ({{ isset($sdate) ? date('m-d-Y', strtotime($sdate)) : '' }})</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        @if (isset($products))
                            <table class="table">
                                <thead class="header_bg">
                                    <tr>
                                        <th class="header_style_left">{{ __('#SL') }}</th>
                                        <th>{{ __('Product') }}</th>
                                        <th>{{ __('Stock In') }}</th>
                                        <th>{{ __('Stock Transfer') }}</th>
                                        <th>{{ __('Stock Receive') }}</th>
                                        <th>{{ __('Stock Out') }}</th>
                                        <th>{{ __('Return') }}</th>
                                        <th>{{ __('Damage') }}</th>
                                        <th class="header_style_right"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalPurchase = 0;
                                        $totalTransfer = 0;
                                        $totalReceive = 0;
                                        $totalInvoice = 0;
                                        $totalReturn = 0;
                                        $totalDamage = 0;
                                        $totalStock = 0;

                                        $quantities = $productQuantities ?? [];
                                    @endphp

                                    @forelse($products as $index => $product)
                                        @php
                                            $q = $quantities[$product->id] ?? [
                                                'purchase' => 0,
                                                'transfer' => 0,
                                                'receive' => 0,
                                                'invoice' => 0,
                                                'return' => 0,
                                                'damage' => 0,
                                            ];

                                            $stock =
                                                $q['purchase'] +
                                                $q['return'] -
                                                ($q['transfer'] + $q['invoice'] + $q['damage']);

                                            // ✅ column wise sum
                                            $totalPurchase += $q['purchase'];
                                            $totalTransfer += $q['transfer'];
                                            $totalReceive += $q['receive'];
                                            $totalInvoice += $q['invoice'];
                                            $totalReturn += $q['return'];
                                            $totalDamage += $q['damage'];
                                            $totalStock += $stock;
                                        @endphp

                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $q['purchase'] }}</td>
                                            <td>{{ $q['transfer'] }}</td>
                                            <td>{{ $q['receive'] }}</td>
                                            <td>{{ $q['invoice'] }}</td>
                                            <td>{{ $q['return'] }}</td>
                                            <td>{{ $q['damage'] }}</td>
                                            {{-- <td>{{ $stock }}</td> --}}
                                        </tr>
                                    @empty

                                        <tr>
                                            <td colspan="10" class="text-center text-danger no_data_style">{{ __('No Data Found') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr class="header_bg" style="font-size:18px;font-weight:700;font-family:sans-serif">
                                        <td colspan="2" class="text-white text-right">{{ __('TOTAL') }}</td>
                                        <td class="text-white">{{ $totalPurchase }}</td>
                                        <td class="text-white">{{ $totalTransfer }}</td>
                                        <td class="text-white">{{ $totalReceive }}</td>
                                        <td class="text-white">{{ $totalInvoice }}</td>
                                        <td class="text-white">{{ $totalReturn }}</td>
                                        <td class="text-white">{{ $totalDamage }}</td>
                                        {{-- <td class="text-white">{{ $totalStock }}</td> --}}
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
