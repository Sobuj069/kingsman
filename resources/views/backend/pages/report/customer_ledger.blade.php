@extends('backend.layouts.master')
@section('section-title', __('Report'))
@section('page-title', __('Customer Ledger'))

@push('css')
    <style>
        table th,
        table td {
            padding: 5px !important;
        }

        .invoice-header {
            width: 100%;
            display: block;
            box-sizing: border-box;
            overflow: hidden;
        }

        .invoice-header table {
            width: 50%;
            float: left;
            padding: 5px;
        }

        .logo-area img {
            width: 40%;
            display: inline;
            float: left;
        }

        .logo-area h1 {
            display: inline;
            float: left;
            font-size: 17px;
            padding-left: 8px;
        }

        .logo-area h4 {
            font-weight: bold;
            margin-top: 5px;
        }

        .invoice-header .logo-area {
            width: 50%;
            float: left;
            padding: 5px;
        }
    </style>

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
            <div class="card card-body card_style mb-2" style="mask-type: -5px;" id="h-hide">
                <form action="{{ route('report.customer-ledger') }}">
                    <div class="form-row align-items-end">
                        <div class="col-md-3 col-12 mb-3">
                            <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Customer') }}</label>
                            <select class="select2" id="customer" name="customer_id" required>
                                <option selected value="">{{ __('Select Customer') }}</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        @isset($oneCustomer) @if ($customer->id == $oneCustomer->id) selected @endif @endisset>
                                        {{ $customer->name }}{{ $customer->phone ? ' - ' . $customer->phone : '' }}</option>
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
                                <a href="{{ route('report.customer-ledger') }}" class="btn add_list_btn_reset flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;">
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
            <div class="card col-lg-12 card_style print_area m-b-30">
                <div class="card-body">
                    <div class="table-responsive">
                        <div class="invoice-header">
                            <div class="logo-area">
                                <img src="{{ !empty(get_setting('system_logo')) ? asset('public/uploads/logo/' . get_setting('system_logo')) : asset('backend/images/logo.png') }}"
                                    class="img-fluid" alt="logo">
                            </div>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th style="width:15%;">{{ __('Phone') }}</th>
                                        <th style="width:2%;">:</th>
                                        <th>{{ empty(get_setting('com_phone')) ? '----' : get_setting('com_phone') }}</th>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Email') }}</th>
                                        <th style="width:2%;">:</th>
                                        <th>{{ empty(get_setting('com_phone')) ? '----' : get_setting('com_address') }}</th>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Address:') }}</th>
                                        <th style="width:2%;">:</th>
                                        <th>{{ empty(get_setting('com_email')) ? '----' : get_setting('com_email') }}</th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        @if (isset($oneCustomer))
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th style="width:25%;">{{ __('Name') }}</th>
                                        <th style="width:2%;">:</th>
                                        <th>{{ $oneCustomer->name }}</th>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Phone') }}</th>
                                        <th style="width:2%;">:</th>
                                        <th>{{ $oneCustomer->phone }}</th>
                                    </tr>
                                    <tr>
                                        <th>{{ __('Address') }}</th>
                                        <th style="width:2%;">:</th>
                                        <th>{{ $oneCustomer->address == null ? 'NULL' : $oneCustomer->address }}</th>
                                    </tr>
                                </tbody>
                            </table>
                            <h4 style="text-align: center; font-weight:bold; margin-top:50px;">{{ __('Customer Ledger') }}</h4>
                            <table class="table">
                                <thead>
                                    <tr class="header_bg">
                                        <th class="header_style_left">{{ __('Date') }}</th>
                                        <th>{{ __('Particulars') }}</th>
                                        <th>{{ __('Debit') }}</th>
                                        <th>{{ __('Credit') }}</th>
                                        <th class="header_style_right">{{ __('Balance') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $balance = $previous_balance ?? 0;
                                    @endphp
                                    @if(isset($previous_balance))
                                        <tr>
                                            <td class="table_data_style_left"></td>
                                            <td><strong>{{ __('Previous Balance') }}</strong></td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td class="table_data_style_right">
                                                {{ $balance >= 0 ? number_format($balance, 2) : '(' . number_format(abs($balance), 2) . ')' }}
                                            </td>
                                        </tr>
                                    @endif
                                    @forelse($transaction as $key => $data)
                                        @php
                                            $debit = $data->debit;
                                            $credit = $data->credit;
                                            $balance = $balance + $debit - $credit;
                                        @endphp
                                        <tr>
                                            <td class="table_data_style_left">{{ date('Y-m-d', strtotime($data->date)) }}
                                            </td>
                                            <td>
                                                @if ($data->transaction_type == 'Invoice')
                                                    {{ $data->transaction_type . ' (' . $data->invoice?->invoice_no . ')' }}
                                                @elseif ($data->transaction_type == 'Received from Customer')
                                                    {{ $data->transaction_type . ' (' . $data->invoice?->invoice_no . ')' }}
                                                @else
                                                    {{ $data->transaction_type }}
                                                @endif
                                            </td>
                                            <td>{{ $debit }}</td>
                                            <td>{{ $credit }}</td>
                                            <td class="table_data_style_right">
                                                {{ $balance >= 0 ? number_format($balance, 2) : '(' . number_format(abs($balance), 2) . ')' }}
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
                              <div class="col-12 text-right py-2">
                                  <h4 style="text-decoration-line: underline;text-decoration-style: double;"> <strong>{{ __('Closing Balance :') }}
                                          {{ $closeing_balance >= 0 ? number_format($closeing_balance, 2) : '(' . number_format(abs($closeing_balance), 2) . ')' }}
                                      </strong></h4>
                              </div>
                            @else
                            <div class="col-md-12" style="padding-bottom: 30px;">
                              <div class="alert alert-danger text-center" role="alert"> {{ __('Please Select a Customer') }}</div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

        </div>

    </div>
@endsection
