@extends('backend.layouts.master')
@section('section-title', __('Report'))
@section('page-title', __('Customer Due'))
@push('css')
<style>
    @media print{
        table,table th,table td {
            color:black !important;
        }

        .h-hide {
            display: none;
        }
    }
</style>
@endpush
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <form action="{{ route('report.customer-due') }}" method="GET">
                        <div class="form-row align-items-end h-hide">
                            <div class="col-md-3 col-12 mb-3">
                                <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Customer') }}</label>
                                <select name="customer_id" id="" class="select2">
                                    <option value="">{{ __('All Customer') }}</option>
                                    @foreach ($custommer as $item)
                                        <option value="{{ $item->id }}" {{ ($customer_id == $item->id)? 'selected' : '' }}>{{ $item->name }}{{ $item->phone ? ' - ' . $item->phone : '' }}</option>
                                    @endforeach

                                </select>
                            </div>
                            <div class="col-md-3 col-12 mb-3">
                                <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Phone No') }}</label>
                                <input type="text" placeholder="{{ __('Enter Phone No') }}" name="phone_no" value="{{ $phone_no }}" class="form-control" style="height: 38px !important;">
                            </div>
                            <div class="col-md-6 col-12 mb-3">
                                <div class="d-flex align-items-center" style="gap: 5px;">
                                    <button type="submit" class="btn add_list_btn flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa fa-sliders mr-1"></i> {{ __('Filter') }}
                                    </button>
                                    <a href="{{ route('report.customer-due') }}" class="btn add_list_btn_reset flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;">
                                        <i class="feather icon-refresh-cw mr-1"></i> {{ __('Reset') }}
                                    </a>
                                    <a href="#" class="btn add_list_btn flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;" onclick="window.print()">
                                        <i class="feather icon-printer mr-1"></i> {{ __('Print') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive mt-2.5">
                        <table id="datatable-buttons" class="table table-striped text-center">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th>{{ __('Name & Email & Phone') }}</th>
                                    <th>{{ __('Total Invoice') }}</th>
                                    <th>{{ __('Paid Invoice') }}</th>
                                    <th>{{ __('Due Invoice') }}</th>
                                    <th>{{ __('Previous Due') }}</th>
                                    <th class="header_style_right">{{ __('Total Due') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $sub_total = 0;
                                @endphp
                                @forelse($customers as $data)
                                    @php
                                    $count_cus = App\Models\Invoice::where('customer_id', $data->id)->count();
                                    $count_tra = App\Models\Transaction::where('customer_id', $data->id)->count();
                                    $open_balance = open_balance_customer($data->id, $data->due_amount);
                                    $inv_total = App\Models\Invoice::where('customer_id', $data->id)->sum('total_amount');
                                    $inv_paid = App\Models\Invoice::where('customer_id', $data->id)->sum('total_paid');
                                    $inv_due = App\Models\Invoice::where('customer_id', $data->id)->sum('total_due');
                                    $inv_due_cust = App\Models\Invoice::where('customer_id', $data->id)->get();
                                    $sub_total += ($inv_due + $open_balance);
                                    @endphp
                                    @if( $inv_due + $open_balance > 0)
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>{{ $data->name }} <br> {{ $data->phone }} <br> {{ ($data->email == Null)?'NULL':$data->email; }}</td>
                                        <td class="font-weight-bold">{{ $inv_total }} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</td>
                                        <td class="font-weight-bold">{{ $inv_paid }} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</td>
                                        <td class="font-weight-bold">{{ $inv_due }} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</td>
                                        <td class="font-weight-bold">{{ $open_balance }} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</td>
                                        <td class="font-weight-bold table_data_style_right">
                                            {{ $inv_due + $open_balance }} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}
                                        </td>
                                    </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center text-danger no_data_style">{{ __('No Data Available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfooter>
                                <tr style="background:#000ce2;font-size: 20px; font-width: 700; font-family:sans-serif">
                                    <td class="header_style_left" colspan="4"></td>
                                    <td class="text-white" colspan="1"> <strong> {{ __('Total Price:') }} </strong></td>
                                    <td class="header_style_right text-white" colspan="2"><strong> {{number_format($sub_total,2)}} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }} </strong></td>
                                </tr>
                            </tfooter>
                        </table>
                        {{ $customers->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
