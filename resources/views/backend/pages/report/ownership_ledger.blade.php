@extends('backend.layouts.master')
@section('section-title', 'Report')
@section('page-title', 'OwnerShip Ledger')

@push('css')
<style>
    table th, table td{
        padding:5px !important;
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

    .logo-area h4{
        font-weight: bold;
        margin-top:5px;
    }

    .invoice-header .logo-area {
        width: 50%;
        float: left;
        padding: 5px;
    }

</style>

<style>
    @media print{
        table,table th,table td {
            color:black !important;
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
                <form action="#">
                    <div class="form-row align-items-end">
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
                                    <a href="{{ route('ownership.index') }}" class="btn add_list_btn_reset ml-1" style="padding-top: 8px !important; padding-bottom: 8px !important;">{{ __('Reset') }}</a>
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
            <div class="card col-lg-12 card_style print_area m-b-30">
                <div class="card-body">
                  <div class="table-responsive">
                    <div class="invoice-header">
                      <div class="logo-area">
                        <img src="{{ (!empty(get_setting('system_logo')))?url('uploads/logo/'.get_setting('system_logo')):url('public/backend/images/logo.png') }}" class="img-fluid" alt="logo">
                      </div>
                      <table class="table">
                        <tbody>
                            <tr>
                                <th style="width:15%;">Phone</th>
                                <th style="width:2%;">:</th>
                                <th>{{ (empty(get_setting('com_phone'))?'----':get_setting('com_phone')) }}</th>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <th style="width:2%;">:</th>
                                <th>{{ (empty(get_setting('com_phone'))?'----':get_setting('com_address')) }}</th>
                            </tr>
                            <tr>
                                <th>Address:</th>
                                <th style="width:2%;">:</th>
                                <th>{{ (empty(get_setting('com_email'))?'----':get_setting('com_email')) }}</th>
                            </tr>
                        </tbody>
                      </table>
                    </div>
                    {{-- @dd($oneCustomer) --}}
                    @if (isset($oneCustomer))
                    <table class="table">
                      <tbody>
                        <tr>
                          <th style="width:25%;">Name</th>
                          <th style="width:2%;">:</th>
                          <th>{{ $oneCustomer->name }}</th>
                        </tr>
                        <tr>
                        <th>Phone</th>
                        <th style="width:2%;">:</th>
                        <th>{{ $oneCustomer->phone }}</th>
                        </tr>
                        <tr>
                          <th>Address</th>
                          <th style="width:2%;">:</th>
                          <th>{{ ($oneCustomer->address == NULL)?'NULL':$oneCustomer->address; }}</th>
                        </tr>
                      </tbody>
                    </table>
                    <h4 style="text-align: center; font-weight:bold; margin-top:50px; margin-bottom:10px;">{{ __('Ownership Ledger') }}</h4> 
                    <table class="table">
                      <thead>
                        <tr class="header_bg">
                          <th class="header_style_left">Date</th>
                          <th>Particulars</th>
                          <th>Debit</th>
                          <th>Credit</th>
                          <th class="header_style_right">Balance</th>
                        </tr>
                      </thead>
                      <tbody>
                        @php
                            $balance = 0;
                        @endphp
                        @forelse($transaction as $key => $data)
                        
                        @php
                            $balance = $balance + $data->debit - $data->credit;
                        @endphp
                        <tr>
                          <td class="table_data_style_left">{{ date('Y-m-d', strtotime($data->date)) }}</td>
                          <td>
                            @if ($data->transaction_type)
                            {{ $data->transaction_type}}
                            @else
                            {{ $data->transaction_type }}
                            @endif
                          </td>
                          <td>{{ $data->debit }}</td>
                          <td>{{ $data->credit }}</td>
                          <td class="table_data_style_right">{{ $balance >= 0 ? number_format($balance,2) : '('.number_format(abs($balance), 2).')' }}</td>
                        </tr>
                        @empty 
                            <tr>
                                <td colspan="10" class="text-center text-danger no_data_style">No Data Found</td>
                            </tr>
                        @endforelse
                      </tbody>
                    </table>
                    <div class="col-12 text-right py-2">
                      <h4 style="text-decoration-line: underline;text-decoration-style: double;"> <strong>Closing Balance : {{ $balance >= 0 ? number_format($balance,2) : '('.number_format(abs($balance), 2).')' }} </strong></h4>
                    </div>
                    @else
                    <div class="col-md-12" style="padding-bottom: 30px;">
                        <div class="alert alert-danger text-center" role="alert"> Please Select a Customer</div>
                    </div>
                    @endif
                  </div>
                  
                </div>
            </div>
            
        </div>
        
    </div>
@endsection
