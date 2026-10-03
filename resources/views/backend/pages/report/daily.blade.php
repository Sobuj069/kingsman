@extends('backend.layouts.master')
@section('section-title', __('Report'))
@section('page-title', __('Daily Report'))

@push('css')
    <style>
        @media print {

            table,
            table th,
            table td {
                color: black !important;
            }

            .h-hide {
                display: none;
            }
        }

        .font_size {
            font-size: 13px;
            font-weight: 700;
        }

        .font-size_td {
            font-size: 11px;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-body card_style mb-2 h-hide" style="margin-top: -5px" id="h-hide">
                <form action="{{ route('report.daily') }}">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <input type="date" name="start_date" class="form-control"
                                value="{{ isset($sdate) ? date('Y-m-d', strtotime($sdate)) : '' }}">
                        </div>
                        <div class="form-group col-6 text-right">
                            <button class="btn add_list_btn" type="submit">
                                <i class="fa fa-sliders"></i> {{ __('Filter') }}
                            </button>
                            <a href="{{ route('report.daily') }}" class="btn add_list_btn_reset mr-3">{{ __('Reset') }}</a>
                            <a href="" class="btn add_list_btn float-right" onclick="window.print()">{{ __('Print') }}</a>
                        </div>
                        {{-- <div class="form-group col-md-6">
                        <input type="date" name="end_date" class="form-control" value="{{ (isset($edate))?date('Y-m-d', strtotime($edate)):''; }}">
                        </div> --}}
                    </div>
                    {{-- <div class="form-row mt-2">
                        
                    </div> --}}
                </form>
            </div>
            <div class="card card_style m-b-30 print_area">
                <div class="card-header">
                    <h4 style="text-align: center; font-weight:bold; margin-top:30px;">{{ __('Petty Cash') }}
                        ({{ isset($sdate) ? date('m-d-Y', strtotime($sdate)) : '' }})</h4>

                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        @if (isset($invoices))
                            {{-- @dd($sdate) --}}
                            <div class="col-12">
                                @php
                                    $data = App\Models\BankAccount::first();
                                    $previous_balance = previous_balance($data->id, $sdate);
                                    // ? number_format(previous_balance($data->id, $sdate), 2)
                                    // : number_format(previous_balance($data->id, $sdate), 2);
                                @endphp
                                <h3 class=" text-center py-2" style="border-radius: 25px; background:#000ce2;color:white;">
                                    {{ __('Previous Balance') }} : {{ $previous_balance }}</h3>
                            </div>
                            <div class="col-12">
                                @php
                                    $total_today_sales = 0;
                                    $today_sales_bank_amount = 0;
                                    $today_total_due = 0;
                                    $total_sale = 0;
                                    $bank_amount_sal = 0;
                                    $amount = 0;
                                @endphp
                                <table class="table">
                                    <tbody>
                                        @php

                                            $bankAccount = App\Models\BankAccount::where('status', 1)->get();
                                            $userBranchId = auth()->user()->branch_id;
                                            $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

                                            if ($userBranchId == 1) {
                                                if ($filterBranchId) {
                                                    $today_sale = App\Models\BankTransaction::getFakeSum(App\Models\BankTransaction::where(
                                                        'branch_id',
                                                        $filterBranchId,
                                                    )
                                                        ->whereIn('pay_type', ['invpay', 'invpay_edit'])
                                                        ->where('date', $sdate), 'amount');
                                                    $today_total_due = App\Models\Invoice::getFakeSum(App\Models\Invoice::where(
                                                        'branch_id',
                                                        $filterBranchId,
                                                    )
                                                        ->where('date', $sdate), 'total_due');
                                                    $today_due_coll = App\Models\BankTransaction::getFakeSum(App\Models\BankTransaction::where(
                                                        'branch_id',
                                                        $filterBranchId,
                                                    )
                                                        ->where('pay_type', 'duepay')
                                                        ->where('date', $sdate), 'amount');
                                                    $today_deposit = App\Models\BankTransaction::where(
                                                        'branch_id',
                                                        $filterBranchId,
                                                    )
                                                        ->where('pay_type', 'ownpay')
                                                        ->where('date', $sdate)
                                                        ->sum('amount');
                                                    $today_expense = App\Models\BankTransaction::where(
                                                        'branch_id',
                                                        $filterBranchId,
                                                    )
                                                        ->where('pay_type', 'expense')
                                                        ->where('date', $sdate)
                                                        ->sum('amount');
                                                    $today_purchase = App\Models\BankTransaction::where(
                                                        'branch_id',
                                                        $filterBranchId,
                                                    )
                                                        ->where('pay_type', 'purchase')
                                                        ->where('date', $sdate)
                                                        ->sum('amount');
                                                    $today_pur_due_pay = App\Models\BankTransaction::where(
                                                        'branch_id',
                                                        $filterBranchId,
                                                    )
                                                        ->where('pay_type', 'purdue')
                                                        ->where('date', $sdate)
                                                        ->sum('amount');
                                                    $today_transfer = App\Models\BankTransaction::where(
                                                        'branch_id',
                                                        $filterBranchId,
                                                    )
                                                        ->where('pay_type', 'transfer')
                                                        ->where('date', $sdate)
                                                        ->sum('amount');
                                                    $today_withdraw = App\Models\BankTransaction::where(
                                                        'branch_id',
                                                        $filterBranchId,
                                                    )
                                                        ->where('pay_type', 'ownwith')
                                                        ->where('date', $sdate)
                                                        ->sum('amount');
                                                    $today_total_point_pay = App\Models\Invoice::getFakeSum(App\Models\Invoice::where('branch_id', $filterBranchId)
                                                    ->where('date', $sdate), 'pay_point');
                                                    $today_rtn_pay = App\Models\BankTransaction::where('branch_id', $filterBranchId)
                                                        ->where('pay_type', 'rtn_pay')
                                                        ->where('date', $sdate)
                                                        ->sum('amount');
                                                } else {
                                                    $today_sale = App\Models\BankTransaction::getFakeSum(App\Models\BankTransaction::whereIn(
                                                        'pay_type',
                                                        ['invpay', 'invpay_edit'],
                                                    )
                                                        ->where('date', $sdate), 'amount');
                                                    $today_total_due = App\Models\Invoice::getFakeSum(App\Models\Invoice::where('date', $sdate), 'total_due');
                                                    $today_due_coll = App\Models\BankTransaction::getFakeSum(App\Models\BankTransaction::where(
                                                        'pay_type',
                                                        'duepay',
                                                    )
                                                        ->where('date', $sdate), 'amount');
                                                    $today_deposit = App\Models\BankTransaction::where(
                                                        'pay_type',
                                                        'ownpay',
                                                    )
                                                        ->where('date', $sdate)
                                                        ->sum('amount');
                                                    $today_expense = App\Models\BankTransaction::where(
                                                        'pay_type',
                                                        'expense',
                                                        )
                                                        ->where('date', $sdate)
                                                        ->sum('amount');
                                                    $today_purchase = App\Models\BankTransaction::where(
                                                        'pay_type',
                                                        'purchase',
                                                    )
                                                        ->where('date', $sdate)
                                                        ->sum('amount');
                                                    $today_pur_due_pay = App\Models\BankTransaction::where(
                                                        'pay_type',
                                                        'purdue',
                                                    )
                                                        ->where('date', $sdate)
                                                        ->sum('amount');
                                                    $today_transfer = App\Models\BankTransaction::where(
                                                        'pay_type',
                                                        'transfer',
                                                    )
                                                        ->where('date', $sdate)
                                                        ->sum('amount');
                                                    $today_withdraw = App\Models\BankTransaction::where(
                                                        'pay_type',
                                                        'ownwith',
                                                    )
                                                        ->where('date', $sdate)
                                                        ->sum('amount');
                                                    $today_total_point_pay = App\Models\Invoice::getFakeSum(App\Models\Invoice::where('date', $sdate), 'pay_point');
                                                    $today_rtn_pay = App\Models\BankTransaction::where('pay_type', 'rtn_pay')
                                                        ->where('date', $sdate)
                                                        ->sum('amount');
                                                }
                                            } else {
                                                $today_sale = App\Models\BankTransaction::getFakeSum(App\Models\BankTransaction::where(
                                                    'branch_id',
                                                    $userBranchId,
                                                )
                                                    ->whereIn('pay_type', ['invpay', 'invpay_edit'])
                                                    ->where('date', $sdate), 'amount');
                                                $today_total_due = App\Models\Invoice::getFakeSum(App\Models\Invoice::where('branch_id', $userBranchId)
                                                    ->where('date', $sdate), 'total_due');
                                                $today_due_coll = App\Models\BankTransaction::getFakeSum(App\Models\BankTransaction::where(
                                                    'branch_id',
                                                    $userBranchId,
                                                )
                                                    ->where('pay_type', 'duepay')
                                                    ->where('date', $sdate), 'amount');
                                                $today_deposit = App\Models\BankTransaction::where(
                                                    'branch_id',
                                                    $userBranchId,
                                                )
                                                    ->where('pay_type', 'ownpay')
                                                    ->where('date', $sdate)
                                                    ->sum('amount');
                                                $today_expense = App\Models\BankTransaction::where(
                                                    'branch_id',
                                                    $userBranchId,
                                                )
                                                    ->where('pay_type', 'expense')
                                                    ->where('date', $sdate)
                                                    ->sum('amount');
                                                $today_purchase = App\Models\BankTransaction::where(
                                                    'branch_id',
                                                    $userBranchId,
                                                )
                                                    ->where('pay_type', 'purchase')
                                                    ->where('date', $sdate)
                                                    ->sum('amount');
                                                $today_pur_due_pay = App\Models\BankTransaction::where(
                                                    'branch_id',
                                                    $userBranchId,
                                                )
                                                    ->where('pay_type', 'purdue')
                                                    ->where('date', $sdate)
                                                    ->sum('amount');
                                                $today_transfer = App\Models\BankTransaction::where(
                                                    'branch_id',
                                                    $userBranchId,
                                                )
                                                    ->where('pay_type', 'transfer')
                                                    ->where('date', $sdate)
                                                    ->sum('amount');
                                                $today_withdraw = App\Models\BankTransaction::where(
                                                    'branch_id',
                                                    $userBranchId,
                                                )
                                                    ->where('pay_type', 'ownwith')
                                                    ->where('date', $sdate)
                                                    ->sum('amount');
                                                $today_total_point_pay = App\Models\Invoice::where('branch_id', $userBranchId)
                                                    ->where('date', $sdate)
                                                    ->sum('pay_point');
                                                $today_rtn_pay = App\Models\BankTransaction::where('branch_id', $userBranchId)
                                                    ->where('pay_type', 'rtn_pay')
                                                    ->where('date', $sdate)
                                                    ->sum('amount');
                                            }
                                            $today_total_inc = $today_sale + $today_due_coll + $today_deposit;
                                            $today_total_exp =
                                                $today_expense + $today_purchase + $today_pur_due_pay + $today_withdraw + $today_rtn_pay;

                                            $today_balance = $today_total_inc - $today_total_exp;
                                            $current_balance = $today_balance + $previous_balance;
                                        @endphp

                                        {{-- today sales --}}

                                        <tr class="text-center header_bg">
                                            <th colspan="2" style="border-radius: 25px;">
                                                <h4 class="text-white">{{ __('Income') }}</h4>
                                            </th>
                                        </tr>
                                        <tr class=" text-center table-striped text-white" style="background: #747be4">
                                            <th style="border-top-left-radius: 25px; border-bottom-left-radius:25px">{{ __('Today Sales') }}</th>
                                            <th style="border-top-right-radius: 25px; border-bottom-right-radius:25px">{{ __('Method') }}</th>
                                        </tr>

                                        <tr class="text-center">
                                            <td>{{ $today_sale }}</td>
                                            <td>
                                                @foreach ($bankAccount as $cash)
                                                    @php
                                                        $userBranchId = auth()->user()->branch_id;
                                                        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
                                                        if ($userBranchId == 1) {
                                                            if ($filterBranchId) {
                                                                $cash_amount = App\Models\BankTransaction::getFakeSum(App\Models\BankTransaction::whereIn(
                                                                    'pay_type',
                                                                    ['invpay', 'invpay_edit'],
                                                                )
                                                                    ->where('bank_id', $cash->id)
                                                                    ->where('branch_id', $filterBranchId)
                                                                    ->where('date', $sdate), 'amount');
                                                            } else {
                                                                $cash_amount = App\Models\BankTransaction::getFakeSum(App\Models\BankTransaction::whereIn(
                                                                    'pay_type',
                                                                    ['invpay', 'invpay_edit'],
                                                                )
                                                                    ->where('bank_id', $cash->id)
                                                                    ->where('date', $sdate), 'amount');
                                                            }
                                                        } else {
                                                            $cash_amount = App\Models\BankTransaction::getFakeSum(App\Models\BankTransaction::whereIn(
                                                                'pay_type',
                                                                ['invpay', 'invpay_edit'],
                                                            )
                                                                ->where('bank_id', $cash->id)
                                                                ->where('branch_id', $userBranchId)
                                                                ->where('date', $sdate), 'amount');
                                                        }
                                                    @endphp
                                                    @if ($cash_amount)
                                                        {{ $cash->bank_name }}= {{ $cash_amount }} <br>
                                                    @endif
                                                @endforeach
                                            </td>
                                        </tr>
                                        <tr class="text-center">
                                            <td colspan="2">{{ __('Total Point Pay') }} : {{ $today_total_point_pay }}</td>
                                        </tr>

                                        {{-- today due --}}
                                        @if ($today_total_due != 0)
                                            <tr class="text-center header_bg">
                                                <td colspan="2" style="font-size: 16px;border-radius: 25px;color:white;">
                                                    {{ __('Today Due') }}</td>
                                            </tr>
                                            <tr class="text-center" style="background: #747be4">
                                                <th style="border-top-left-radius: 25px; border-bottom-left-radius:25px">
                                                    {{ __('Today Due') }}</th>
                                                <th style="border-top-right-radius: 25px; border-bottom-right-radius:25px">
                                                    {{ __('Method') }}</th>
                                            </tr>
                                            <tr class="text-center">
                                                <td>{{ $today_total_due }} </td>
                                                <td>
                                                    @foreach ($bankAccount as $cash)
                                                        @php
                                                            $userBranchId = auth()->user()->branch_id;
                                                            $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
                                                            if ($userBranchId == 1) {
                                                                if ($filterBranchId) {
                                                                    $due_collect_amount = App\Models\BankTransaction::getFakeSum(App\Models\BankTransaction::where(
                                                                        'pay_type',
                                                                        'duepay',
                                                                    )
                                                                        ->where('bank_id', $cash->id)
                                                                        ->where('branch_id', $filterBranchId)
                                                                        ->where('date', $sdate), 'amount');
                                                                } else {
                                                                    $due_collect_amount = App\Models\BankTransaction::getFakeSum(App\Models\BankTransaction::where(
                                                                        'pay_type',
                                                                        'duepay',
                                                                    )
                                                                        ->where('bank_id', $cash->id)
                                                                        ->where('date', $sdate), 'amount');
                                                                }
                                                            } else {
                                                                $due_collect_amount = App\Models\BankTransaction::getFakeSum(App\Models\BankTransaction::where(
                                                                    'pay_type',
                                                                    'duepay',
                                                                )
                                                                    ->where('bank_id', $cash->id)
                                                                    ->where('branch_id', $userBranchId)
                                                                    ->where('date', $sdate), 'amount');
                                                            }

                                                        @endphp
                                                        @if ($due_collect_amount != 0)
                                                        @endif
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endif

                                        {{-- due collection --}}
                                        @if ($today_due_coll != 0)
                                            <tr class="text-center header_bg">
                                                <td colspan="2" style="font-size: 16px;border-radius: 25px;color:white;">
                                                    {{ __('Due Collection') }}</td>
                                            </tr>
                                            <tr class="text-center" style="background: #747be4">
                                                <th style="border-top-left-radius: 25px; border-bottom-left-radius:25px">
                                                    {{ __('Today Due Collection') }}</th>
                                                <th style="border-top-right-radius: 25px; border-bottom-right-radius:25px">
                                                    {{ __('Method') }}</th>
                                            </tr>
                                            <tr class="text-center">
                                                <td>{{ $today_due_coll }} </td>
                                                <td>
                                                    @foreach ($bankAccount as $cash)
                                                        @php
                                                            $userBranchId = auth()->user()->branch_id;
                                                            $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
                                                            if ($userBranchId == 1) {
                                                                if ($filterBranchId) {
                                                                    $due_collect_amount = App\Models\BankTransaction::where(
                                                                        'pay_type',
                                                                        'duepay',
                                                                    )
                                                                        ->where('bank_id', $cash->id)
                                                                        ->where('branch_id', $filterBranchId)
                                                                        ->where('date', $sdate)
                                                                        ->sum('amount');
                                                                } else {
                                                                    $due_collect_amount = App\Models\BankTransaction::where(
                                                                        'pay_type',
                                                                        'duepay',
                                                                    )
                                                                        ->where('bank_id', $cash->id)
                                                                        ->where('date', $sdate)
                                                                        ->sum('amount');
                                                                }
                                                            } else {
                                                                $due_collect_amount = App\Models\BankTransaction::where(
                                                                    'pay_type',
                                                                    'duepay',
                                                                )
                                                                    ->where('bank_id', $cash->id)
                                                                    ->where('branch_id', $userBranchId)
                                                                    ->where('date', $sdate)
                                                                    ->sum('amount');
                                                            }
                                                        @endphp
                                                        @if ($due_collect_amount != 0)
                                                            {{ $cash->bank_name }}= {{ $due_collect_amount }} <br>
                                                        @endif
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endif

                                        {{-- deposit --}}

                                        @if ($today_deposit != 0)
                                            <tr class="text-center header_bg">
                                                <td colspan="2" style="font-size: 16px;border-radius: 25px;color:white;">
                                                    {{ __('Deposit') }}</td>
                                            </tr>
                                            <tr class="text-center" style="background: #747be4">
                                                <th style="border-top-left-radius: 25px; border-bottom-left-radius:25px">
                                                    {{ __('Today Deposit') }}</th>
                                                <th style="border-top-right-radius: 25px; border-bottom-right-radius:25px">
                                                    {{ __('Method') }}</th>
                                            </tr>
                                            <tr class="text-center">
                                                <td>{{ $today_deposit }} </td>
                                                <td>
                                                    @foreach ($bankAccount as $cash)
                                                        @php
                                                            $userBranchId = auth()->user()->branch_id;
                                                            $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
                                                            if ($userBranchId == 1) {
                                                                if ($filterBranchId) {
                                                                    $deposit_amount = App\Models\BankTransaction::where(
                                                                        'pay_type',
                                                                        'ownpay',
                                                                    )
                                                                        ->where('bank_id', $cash->id)
                                                                        ->where('branch_id', $filterBranchId)
                                                                        ->where('date', $sdate)
                                                                        ->sum('amount');
                                                                } else {
                                                                    $deposit_amount = App\Models\BankTransaction::where(
                                                                        'pay_type',
                                                                        'ownpay',
                                                                    )
                                                                        ->where('bank_id', $cash->id)
                                                                        ->where('date', $sdate)
                                                                        ->sum('amount');
                                                                }
                                                            } else {
                                                                $deposit_amount = App\Models\BankTransaction::where(
                                                                    'pay_type',
                                                                    'ownpay',
                                                                )
                                                                    ->where('bank_id', $cash->id)
                                                                    ->where('branch_id', $userBranchId)
                                                                    ->where('date', $sdate)
                                                                    ->sum('amount');
                                                            }

                                                        @endphp
                                                        {{ $cash->bank_name }}= {{ $deposit_amount }} <br>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endif

                                        @if ($today_withdraw != 0)

                                            <tr class="text-center header_bg">
                                                <td colspan="2" style="font-size: 16px;border-radius: 25px;color:white;">
                                                    {{ __('Withdraw') }}</td>
                                            </tr>
                                            <tr class="text-center" style="background: #747be4">
                                                <th style="border-top-left-radius: 25px; border-bottom-left-radius:25px">
                                                    {{ __('Today Withdraw') }}</th>
                                                <th style="border-top-right-radius: 25px; border-bottom-right-radius:25px">
                                                    {{ __('Method') }}</th>
                                            </tr>
                                            <tr class="text-center">
                                                <td>{{ $today_withdraw }} </td>
                                                <td>
                                                    @foreach ($bankAccount as $cash)
                                                        @php
                                                            $userBranchId = auth()->user()->branch_id;
                                                            $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

                                                            if ($userBranchId == 1) {
                                                                if ($filterBranchId) {
                                                                    $deposit_amount = App\Models\BankTransaction::where(
                                                                        'pay_type',
                                                                        'ownwith',
                                                                    )
                                                                        ->where('bank_id', $cash->id)
                                                                        ->where('branch_id', $filterBranchId)
                                                                        ->where('date', $sdate)
                                                                        ->sum('amount');
                                                                } else {
                                                                    $deposit_amount = App\Models\BankTransaction::where(
                                                                        'pay_type',
                                                                        'ownwith',
                                                                    )
                                                                        ->where('bank_id', $cash->id)
                                                                        ->where('date', $sdate)
                                                                        ->sum('amount');
                                                                }
                                                            } else {
                                                                $deposit_amount = App\Models\BankTransaction::where(
                                                                    'pay_type',
                                                                    'ownwith',
                                                                )
                                                                    ->where('bank_id', $cash->id)
                                                                    ->where('branch_id', $userBranchId)
                                                                    ->where('date', $sdate)
                                                                    ->sum('amount');
                                                            }
                                                        @endphp
                                                        {{ $cash->bank_name }}= {{ $deposit_amount }} <br>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endif

                                        {{-- expenses --}}

                                        @if ($today_expense != 0)
                                            <tr class="text-center header_bg">
                                                <td colspan="2" style="font-size: 16px;border-radius: 25px;color:white;">
                                                    {{ __('Expenses') }}</td>
                                            </tr>
                                            <tr class="text-center" style="background: #747be4">
                                                <th style="border-top-left-radius: 25px; border-bottom-left-radius:25px">
                                                    {{ __('Today Expense') }}</th>
                                                <th style="border-top-right-radius: 25px; border-bottom-right-radius:25px">
                                                    {{ __('Method') }}</th>
                                            </tr>
                                            <tr class="text-center">
                                                <td>{{ $today_expense }} </td>
                                                <td>
                                                    @foreach ($bankAccount as $cash)
                                                        @php

                                                            $userBranchId = auth()->user()->branch_id;
                                                            $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
                                                            if ($userBranchId == 1) {
                                                                if ($filterBranchId) {
                                                                    $expense_amount = App\Models\BankTransaction::where(
                                                                        'pay_type',
                                                                        'expense',
                                                                    )
                                                                        ->where('bank_id', $cash->id)
                                                                        ->where('branch_id', $filterBranchId)
                                                                        ->where('date', $sdate)
                                                                        ->sum('amount');
                                                                } else {
                                                                    $expense_amount = App\Models\BankTransaction::where(
                                                                        'pay_type',
                                                                        'expense',
                                                                    )
                                                                        ->where('bank_id', $cash->id)
                                                                        ->where('date', $sdate)
                                                                        ->sum('amount');
                                                                }
                                                            } else {
                                                                $expense_amount = App\Models\BankTransaction::where(
                                                                    'pay_type',
                                                                    'expense',
                                                                )
                                                                    ->where('bank_id', $cash->id)
                                                                    ->where('branch_id', $userBranchId)
                                                                    ->where('date', $sdate)
                                                                    ->sum('amount');
                                                            }
                                                        @endphp
                                                        @if ($expense_amount != 0)
                                                            {{ $cash->bank_name }}= {{ $expense_amount }} <br>
                                                        @endif
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endif

                                        {{-- Purchase --}}

                                        @if ($today_purchase != 0)
                                            <tr class="text-center header_bg">
                                                <td colspan="2" style="font-size: 16px;border-radius: 25px;color:white;">
                                                    {{ __('Purchase') }}</td>
                                            </tr>
                                            <tr class="text-center" style="background: #747be4">
                                                <th style="border-top-left-radius: 25px; border-bottom-left-radius:25px">
                                                    {{ __('Today Purchase') }}</th>
                                                <th style="border-top-right-radius: 25px; border-bottom-right-radius:25px">
                                                    {{ __('Method') }}</th>
                                            </tr>
                                            <tr class="text-center">
                                                <td>{{ $today_purchase }} </td>
                                                <td>
                                                    @foreach ($bankAccount as $cash)
                                                        @php

                                                            $userBranchId = auth()->user()->branch_id;
                                                            $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

                                                            if ($userBranchId == 1) {
                                                                if ($filterBranchId) {
                                                                    $purchase_amount = App\Models\BankTransaction::where(
                                                                        'pay_type',
                                                                        'purchase',
                                                                    )
                                                                        ->where('bank_id', $cash->id)
                                                                        ->where('branch_id', $filterBranchId)
                                                                        ->where('date', $sdate)
                                                                        ->sum('amount');
                                                                } else {
                                                                    $purchase_amount = App\Models\BankTransaction::where(
                                                                        'pay_type',
                                                                        'purchase',
                                                                    )
                                                                        ->where('bank_id', $cash->id)
                                                                        ->where('date', $sdate)
                                                                        ->sum('amount');
                                                                }
                                                            } else {
                                                                $purchase_amount = App\Models\BankTransaction::where(
                                                                    'pay_type',
                                                                    'purchase',
                                                                )
                                                                    ->where('bank_id', $cash->id)
                                                                    ->where('branch_id', $userBranchId)
                                                                    ->where('date', $sdate)
                                                                    ->sum('amount');
                                                            }
                                                        @endphp
                                                        @if ($purchase_amount != 0)
                                                            {{ $cash->bank_name }}= {{ $purchase_amount }} <br>
                                                        @endif
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endif
                                        {{-- Purchase due pay --}}

                                        @if ($today_pur_due_pay != 0)
                                            <tr class="text-center header_bg">
                                                <td colspan="2" style="font-size: 16px;border-radius: 25px;color:white;">
                                                    {{ __('Purchase Due Payment') }}</td>
                                            </tr>
                                            <tr class="text-center" style="background: #747be4">
                                                <th style="border-top-left-radius: 25px; border-bottom-left-radius:25px">
                                                    {{ __('Today Purchase Due Payment') }}</th>
                                                <th style="border-top-right-radius: 25px; border-bottom-right-radius:25px">
                                                    {{ __('Method') }}</th>
                                            </tr>
                                            <tr class="text-center">
                                                <td>{{ $today_pur_due_pay }} </td>
                                                <td>
                                                    @foreach ($bankAccount as $cash)
                                                        @php
                                                            $userBranchId = auth()->user()->branch_id;
                                                            $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
                                                            if ($userBranchId == 1) {
                                                                if ($filterBranchId) {
                                                                    $purchase_due_pay = App\Models\BankTransaction::where(
                                                                        'pay_type',
                                                                        'purdue',
                                                                    )
                                                                        ->where('bank_id', $cash->id)
                                                                        ->where('branch_id', $filterBranchId)
                                                                        ->where('date', $sdate)
                                                                        ->sum('amount');
                                                                } else {
                                                                    $purchase_due_pay = App\Models\BankTransaction::where(
                                                                        'pay_type',
                                                                        'purdue',
                                                                    )
                                                                        ->where('bank_id', $cash->id)
                                                                        ->where('date', $sdate)
                                                                        ->sum('amount');
                                                                }
                                                            } else {
                                                                $purchase_due_pay = App\Models\BankTransaction::where(
                                                                    'pay_type',
                                                                    'purdue',
                                                                )
                                                                    ->where('bank_id', $cash->id)
                                                                    ->where('branch_id', $userBranchId)
                                                                    ->where('date', $sdate)
                                                                    ->sum('amount');
                                                            }

                                                        @endphp
                                                        @if ($purchase_due_pay != 0)
                                                            {{ $cash->bank_name }}= {{ $purchase_due_pay }} <br>
                                                        @endif
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endif

                                        {{-- Sale Return Refund --}}
                                        @if ($today_rtn_pay != 0)
                                            <tr class="text-center header_bg">
                                                <td colspan="2" style="font-size: 16px;border-radius: 25px;color:white;">
                                                    {{ __('Sale Return Refund') }}</td>
                                            </tr>
                                            <tr class="text-center" style="background: #747be4">
                                                <th style="border-top-left-radius: 25px; border-bottom-left-radius:25px">
                                                    {{ __('Today Return Refund') }}</th>
                                                <th style="border-top-right-radius: 25px; border-bottom-right-radius:25px">
                                                    {{ __('Method') }}</th>
                                            </tr>
                                            <tr class="text-center">
                                                <td>{{ $today_rtn_pay }} </td>
                                                <td>
                                                    @foreach ($bankAccount as $cash)
                                                        @php
                                                            $userBranchId = auth()->user()->branch_id;
                                                            $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

                                                            if ($userBranchId == 1) {
                                                                if ($filterBranchId) {
                                                                    $rtn_pay_amount = App\Models\BankTransaction::where('pay_type', 'rtn_pay')
                                                                        ->where('bank_id', $cash->id)
                                                                        ->where('branch_id', $filterBranchId)
                                                                        ->where('date', $sdate)
                                                                        ->sum('amount');
                                                                } else {
                                                                    $rtn_pay_amount = App\Models\BankTransaction::where('pay_type', 'rtn_pay')
                                                                        ->where('bank_id', $cash->id)
                                                                        ->where('date', $sdate)
                                                                        ->sum('amount');
                                                                }
                                                            } else {
                                                                $rtn_pay_amount = App\Models\BankTransaction::where('pay_type', 'rtn_pay')
                                                                    ->where('bank_id', $cash->id)
                                                                    ->where('branch_id', $userBranchId)
                                                                    ->where('date', $sdate)
                                                                    ->sum('amount');
                                                            }
                                                        @endphp
                                                        @if ($rtn_pay_amount != 0)
                                                            {{ $cash->bank_name }}= {{ $rtn_pay_amount }} <br>
                                                        @endif
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endif

                                        {{-- bank transfar --}}
                                        @if ($today_transfer != 0)
                                            <tr class="text-center header_bg">
                                                <td colspan="2" style="font-size: 16px;border-radius: 25px;color:white;">
                                                    {{ __('Bank Transfer') }}</td>
                                            </tr>
                                            <tr class="text-center">
                                                <th style="border-top-left-radius: 25px; border-bottom-left-radius:25px">
                                                    {{ __('Account') }}</th>
                                                {{-- <th>From Account</th> --}}
                                                <th style="border-top-right-radius: 25px; border-bottom-right-radius:25px">
                                                    {{ __('Amount') }}</th>
                                            </tr>
                                            <tr class="text-center">
                                                <td>
                                                    @foreach ($bankAccount as $cash)
                                                        @php
                                                            $userBranchId = auth()->user()->branch_id;
                                                            $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

                                                            if ($userBranchId == 1) {
                                                                if ($filterBranchId) {
                                                                    $purc_due_pay = App\Models\BankTransaction::where(
                                                                        'pay_type',
                                                                        'transfer',
                                                                    )
                                                                        ->where('from_bank_id', $cash->id)
                                                                        ->where('branch_id', $filterBranchId)
                                                                        ->where('date', $sdate)
                                                                        ->get();
                                                                } else {
                                                                    $purc_due_pay = App\Models\BankTransaction::where(
                                                                        'pay_type',
                                                                        'transfer',
                                                                    )
                                                                        ->where('from_bank_id', $cash->id)
                                                                        ->where('date', $sdate)
                                                                        ->get();
                                                                }
                                                            } else {
                                                                $purc_due_pay = App\Models\BankTransaction::where(
                                                                    'pay_type',
                                                                    'transfer',
                                                                )
                                                                    ->where('from_bank_id', $cash->id)
                                                                    ->where('branch_id', $userBranchId)
                                                                    ->where('date', $sdate)
                                                                    ->get();
                                                            }

                                                        @endphp
                                                        @foreach ($purc_due_pay as $trans)
                                                            {{ __('From') }} {{ $trans->from_bank_account->bank_name }} {{ __('Transfer To') }}
                                                            {{ $trans->to_bank_account->bank_name }} <br>
                                                        @endforeach
                                                    @endforeach
                                                </td>
                                                <td>
                                                    @foreach ($bankAccount as $cash)
                                                        @php

                                                            $userBranchId = auth()->user()->branch_id;
                                                            $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

                                                            if ($userBranchId == 1) {
                                                                if ($filterBranchId) {
                                                                    $purc_due_pay = App\Models\BankTransaction::where(
                                                                        'pay_type',
                                                                        'transfer',
                                                                    )
                                                                        ->where('from_bank_id', $cash->id)
                                                                        ->where('branch_id', $filterBranchId)
                                                                        ->where('date', $sdate)
                                                                        ->get();
                                                                } else {
                                                                    $purc_due_pay = App\Models\BankTransaction::where(
                                                                        'pay_type',
                                                                        'transfer',
                                                                    )
                                                                        ->where('from_bank_id', $cash->id)
                                                                        ->where('date', $sdate)
                                                                        ->get();
                                                                }
                                                            } else {
                                                                $purc_due_pay = App\Models\BankTransaction::where(
                                                                    'pay_type',
                                                                    'transfer',
                                                                )
                                                                    ->where('from_bank_id', $cash->id)
                                                                    ->where('branch_id', $userBranchId)
                                                                    ->where('date', $sdate)
                                                                    ->get();
                                                            }
                                                        @endphp
                                                        @foreach ($purc_due_pay as $trans)
                                                            {{ $trans->amount }} <br>
                                                        @endforeach
                                                    @endforeach
                                                </td>
                                            </tr>
                                            <tr class="text-center">
                                                <td>{{ __('Total Transfer') }} </td>
                                                <td>{{ $today_transfer }}</td>
                                            </tr>
                                        @endif
                                    </tbody>

                                    @php
                                        $current = 0;
                                        $current += current_balance($data->id);
                                    @endphp
                                    {{-- today balance --}}
                                    <tr class="header_bg"
                                        style=" font-size: 20px; font-width: 700; font-family:sans-serif;text-align:center;">
                                        <td class="text-center text-white header_style_left"> <strong> {{ __('Today Balance') }} :
                                                {{ $today_balance }} </strong></td>
                                        <td class="text-white header_style_right">
                                            @foreach ($bankAccount as $cash)
                                                {{ $cash->bank_name }}= {{ date_current_balance($cash->id, $sdate) }} <br>
                                            @endforeach
                                        </td>
                                    </tr>
                                    {{-- current balance --}}
                                    <tr
                                        style="background:#747be4;font-size: 20px; font-width: 700; font-family:sans-serif;text-align:center;">
                                        <td colspan="3" class="text-center" style="border-radius: 25px;color:white">
                                            <strong> {{ __('Current Balance') }} :
                                                {{ $today_balance + previous_balance($data->id, $sdate) }}
                                                {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>
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
