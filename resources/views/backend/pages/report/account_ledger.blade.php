@extends('backend.layouts.master')
@section('section-title', __('Report'))
@section('page-title', __('Account Ledger'))

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

    .invoice-header .logo-area {
        width: 50%;
        float: left;
        padding: 5px;
    }

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
        {{-- Filter Form --}}
        <div class="card card-body card_style mb-2" id="h-hide">
            <form action="{{ route('report.account-ledger') }}" method="GET">
                <div class="form-row align-items-end">
                    <div class="col-md-3 col-12 mb-3">
                        <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Bank Account') }}</label>
                        <select class="select2 " name="bank_id" required>
                            <option value="">{{ __('Select Bank Account') }}</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}" {{ request('bank_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->bank_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-12 mb-3">
                        <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Start Date') }}</label>
                        <input type="date" name="start_date" class="form-control" style="height: 38px !important;" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2 col-12 mb-3">
                        <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('End Date') }}</label>
                        <input type="date" name="end_date" class="form-control" style="height: 38px !important;" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-5 col-12 mb-3">
                        <div class="d-flex align-items-center" style="gap: 5px;">
                            <button type="submit" class="btn add_list_btn flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;">
                                <i class="fa fa-sliders mr-1"></i> {{ __('Filter') }}
                            </button>
                            <a href="{{ route('report.account-ledger') }}" class="btn add_list_btn_reset flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;">
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

        {{-- Ledger Table --}}
        <div class="card col-lg-12 card_style print_area m-b-30">
            <div class="card-body">
                <div class="table-responsive">

                    {{-- Company Info --}}
                    <div class="invoice-header">
                        <div class="logo-area">
                            <img src="{{ !empty(get_setting('system_logo')) 
                                    ? asset('public/uploads/logo/' . get_setting('system_logo')) 
                                    : asset('backend/images/logo.png') }}" class="img-fluid" alt="logo">
                        </div>
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th style="width:15%;">{{ __('Phone') }}</th>
                                    <th style="width:2%;">:</th>
                                    <th>{{ get_setting('com_phone') ?? '----' }}</th>
                                </tr>
                                <tr>
                                    <th>{{ __('Email') }}</th>
                                    <th style="width:2%;">:</th>
                                    <th>{{ get_setting('com_email') ?? '----' }}</th>
                                </tr>
                                <tr>
                                    <th>{{ __('Address:') }}</th>
                                    <th style="width:2%;">:</th>
                                    <th>{{ get_setting('com_address') ?? '----' }}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @if(isset($oneAccount))
                        {{-- Selected Bank Info --}}
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th style="width:25%;">{{ __('Bank Name') }}</th>
                                    <th style="width:2%;">:</th>
                                    <th>{{ $oneAccount->bank_name }}</th>
                                </tr>
                                <tr>
                                    <th>{{ __('Account Number') }}</th>
                                    <th style="width:2%;">:</th>
                                    <th>{{ $oneAccount->account_number ?? '---' }}</th>
                                </tr>
                            </tbody>
                        </table>

                        {{-- Ledger Table --}}
                        <h4 class="text-center font-weight-bold mt-3">{{ __('Account Ledger') }}</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr class="header_bg">
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Particulars') }}</th>
                                    <th>{{ __('Note Type (Taka)') }}</th>
                                    <th>{{ __('Remark') }}</th>
                                    <th>{{ __('Debit') }}</th>
                                    <th>{{ __('Credit') }}</th>
                                    <th>{{ __('Balance') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                            {{-- @if(isset($oneAccount) && isset($sdate)))
                                @php 
                                        $balance = $oneAccount->opening_balance ?? 0; 
                                @endphp --}}
                                <tr>
                                    <td></td>
                                    <td><strong>{{ __('Previous Balance') }}</strong></td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>0</td>
                                    <td>{{ $previous_balance_closing }}</td>
                                </tr>
                            {{--  @else
                             @endif --}}
                                 @php
                                    $balance = $previous_balance_closing ?? 0;
                                 @endphp
                                @foreach($bank_transaction as $transaction)
                                     @php
                                     
                                        $fromBankId = null;
                                        $toBankId   = null;
                                    
                                        if ($transaction->trans_type === 'transfer') {
                                            $fromBankId = $transaction->from_bank_id ?? null;
                                            $toBankId   = $transaction->to_bank_id ?? null;
                                        }
                                    
                                        $debit  = 0;
                                        $credit = 0;
                                    
                                        if ($transaction->trans_type === 'withdraw') {
                                            $debit = $transaction->amount;
                                        } elseif ($transaction->trans_type === 'deposit') {
                                            $credit = $transaction->amount;
                                        } elseif ($transaction->trans_type === 'transfer') {
                                            if ($fromBankId == $oneAccount->id) {
                                                // এই account থেকে টাকা গেছে → শুধু debit
                                                $debit = $transaction->amount;
                                            } elseif ($toBankId == $oneAccount->id) {
                                                // এই account এ টাকা এসেছে → শুধু credit
                                                $credit = $transaction->amount;
                                            }
                                        }
                                        
                                         $invoice = App\Models\Invoice::where('id',$transaction->invoice_id)->first();
                                    
                                        $balance = $balance + $credit - $debit;
                                    @endphp

                                    <tr>
                                        <td>{{ date('Y-m-d', strtotime($transaction->date)) }}</td>
                                        <td>
                                            @if($transaction->pay_type == 'purchase')
                                            {{ __('Purchase') }}
                                             ( {{$transaction->purchase?->purchase_no }} )
                                            @elseif($transaction->pay_type == 'ownpay')
                                             {{ __('Owner Deposit') }}
                                            @elseif($transaction->pay_type == 'ownwith')
                                             {{ __('Owner Withdrow') }}
                                            @elseif($transaction->pay_type == 'invpay' || $transaction->pay_type == 'invpay_edit')
                                             {{ __('Sale') }}
                                              ( {{$transaction->invoice?->invoice_no }} )
                                             @elseif($transaction->pay_type == 'rtn_pay_edit')
                                             {{ __('Sale Edit Refund') }}
                                              ( {{$transaction->invoice?->invoice_no }} )
                                             @else
                                             {{$transaction->pay_type}}
                                             @endif
                                        </td>
                                        <td>{{ $transaction?->note_type ?? $invoice?->note_type }}</td>
                                        <td>{{ $transaction?->note ?? $invoice?->note }}</td>
                                        <td>{{ $debit }}</td>
                                        <td>{{ $credit }}</td>
                                        <td>{{ $balance }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Closing Balance --}}
                        <div class="text-right py-2">
                            <h4 style="text-decoration-line:  underline;text-decoration-style: double;">
                                <strong>{{ __('Closing Balance :') }} {{ $balance_closing }}
                                </strong>
                            </h4>
                        </div>
                    @else
                        <div class="alert alert-danger text-center">
                            {{ __('Please Select a Bank Account') }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
