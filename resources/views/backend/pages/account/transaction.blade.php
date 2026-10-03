@extends('backend.layouts.master')
@section('section-title', __('Account'))
@section('page-title', __('Transaction History'))
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-body card_style mb-2" style="margin-top: -5px" id="h-hide">
                <form action="{{ route('transaction-history')}}">
                    <div class="form-row align-items-end mb-0"> 
                        <div class="form-group col-md-3">
                            <label class="font-weight-bold text-muted small uppercase">{{ __('Start Date') }}</label>
                            <input type="date" name="start_date" class="form-control" style="height: 38px !important;" value="{{ (isset($sdate))?date('Y-m-d', strtotime($sdate)):''; }}">
                        </div>
                        <div class="form-group col-md-3">
                            <label class="font-weight-bold text-muted small uppercase">{{ __('End Date') }}</label>
                            <input type="date" name="end_date" class="form-control" style="height: 38px !important;" value="{{ (isset($edate))?date('Y-m-d', strtotime($edate)):''; }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label class="d-none d-md-block">&nbsp;</label>
                            <div class="d-flex flex-wrap justify-content-between align-items-center">
                                <div class="mb-2 mb-sm-0">
                                    <button class="btn add_list_btn" type="submit" style="padding-top: 8px !important; padding-bottom: 8px !important;">
                                        <i class="fa fa-sliders mr-1"></i> {{ __('Filter') }}
                                    </button>
                                    <a href="{{ route('transaction-history') }}" class="btn add_list_btn_reset ml-1" style="padding-top: 8px !important; padding-bottom: 8px !important;">{{ __('Reset') }}</a>
                                </div>
                                <div class="mb-2 mb-sm-0">
                                    <a href="" class="btn add_list_btn" style="padding-top: 8px !important; padding-bottom: 8px !important;" onclick="window.print()">
                                        <i class="feather icon-printer mr-1"></i> {{ __('Print') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable-buttons" class="table table-striped">
                            <thead class="header_bg">
                                <tr class="text-center">
                                    <th class="header_style_left"> {{ __('Date') }} </th>
                                    <th> {{ __('Branch') }}</th>
                                    <th> {{ __('Transaction Type') }} </th>
                                    <th> {{ __('Note') }} </th>
                                    <th> {{ __('Amount') }} </th>
                                    <th class="header_style_right"> {{ __('Created By') }} </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bank_transactions as $transaction)
                                    <tr class="text-center">
                                        <td class="table_data_style_left"> {{ date('d-m-Y',strtotime($transaction->date)) }} </td>
                                        <td>{{ $transaction->branch?->name }}</td>
                                        <td> {{ ucfirst($transaction->trans_type) }} {{ __('from') }} {{ $transaction->bank_account->bank_name }} </td>
                                        <td> {{ ($transaction->note != NULL)?$transaction->note:'NULL' }} </td>
                                        <td> {{ number_format($transaction->amount,2) }} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }} </td>
                                        <td class="table_data_style_right"> {{ $transaction->user->name }} </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center no_data_style">{{ __('No Data Found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination justify-content-center">
                        {{-- {{ $bank_transactions->links() }} --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
