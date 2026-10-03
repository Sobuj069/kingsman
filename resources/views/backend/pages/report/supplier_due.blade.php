@extends('backend.layouts.master')
@section('section-title', __('Supplier'))
@section('page-title', __('Supplier Due'))

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <form action="{{ route('report.supplier-due') }}" method="GET">
                        <div class="form-row align-items-end h-hide">
                            <div class="col-md-3 col-12 mb-3">
                                <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Supplier') }}</label>
                                <select name="supplier_id" id="" class="select2">
                                    <option value="">{{ __('All Supplier') }}</option>
                                    @foreach ($supliers as $item)
                                        <option value="{{ $item->id }}" {{ ($supplier_id == $item->id)? 'selected' : '' }}>{{ $item->name }}</option>
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
                                    <a href="{{ route('report.supplier-due') }}" class="btn add_list_btn_reset flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;">
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
                                    <th>{{ __('Total Purchase') }}</th>
                                    <th>{{ __('Purchase Paid') }}</th>
                                    <th>{{ __('Purchase Due') }}</th>
                                    <th>{{ __('Previous Due') }}</th>
                                    <th class="header_style_right">{{ __('Total Due') }}</th>
                                </tr>
                            </thead>
                                @php
                                    $sub_total = 0;
                                @endphp
                            <tbody>
                                @forelse($suppliers as $data)
                                    @php
                                    $count_sup = App\Models\Purchase::where('supplier_id', $data->id)->count();
                                    $count_tra = App\Models\Transaction::where('supplier_id', $data->id)->count();
                                    $open_balance = open_balance_supplier($data->id, $data->open_receivable, $data->open_payable);
                                    $pur_total = App\Models\Purchase::where('supplier_id', $data->id)->sum('total_amount');
                                    $pur_paid = App\Models\Purchase::where('supplier_id', $data->id)->sum('total_paid');
                                    $pur_due = App\Models\Purchase::where('supplier_id', $data->id)->sum('total_due');
                                    $sub_total += ($pur_due + $open_balance);
                                    @endphp
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>{{ $data->name }} <br> {{ $data->phone }} <br> {{ ($data->email == Null)?'NULL':$data->email; }}</td>
                                        <td class="font-weight-bold">{{ $pur_total }} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</td>
                                        <td class="font-weight-bold">{{ $pur_paid }} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</td>
                                        <td class="font-weight-bold">{{ $pur_due }} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</td>
                                        <td class="font-weight-bold">{{ $open_balance }} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</td>
                                        <td class="font-weight-bold table_data_style_right">{{ $open_balance + $pur_due }} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</td>
                                    </tr>

                                    {{-- edit modal  --}}
                                    <form action="{{ route('supplier.update', $data->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <x-edit-modal title="{{ __('Edit Supplier') }}" sizeClass="modal-lg" id="{{ $data->id }}">
                                            <x-input label="{{ __('Name *') }}" type="text" name="name" placeholder="{{ __('Enter Name') }}"
                                                required md="6" value="{{ $data->name }}" />
                                            <x-input label="{{ __('Email') }}" type="email" name="email" placeholder="{{ __('Enter Email') }}"
                                                md="6" value="{{ $data->email }}" />
                                            <x-input label="{{ __('Phone *') }}" type="text" name="phone" placeholder="{{ __('Enter Phone') }}"
                                                required md="6" value="{{ $data->phone }}" />
                                            <x-input label="{{ __('Address') }}" type="text" name="address"
                                                placeholder="{{ __('Enter Address') }}" md="6"
                                                value="{{ $data->address }}" />
                                        </x-edit-modal>
                                    </form>

                                    {{-- delete modal --}}
                                    <form action="{{ route('supplier.destroy', $data->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-delete-modal title="{{ __('Supplier') }}" id="{{ $data->id }}" />
                                    </form>

                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center no_data_style text-danger">{{ __('No Data Available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfooter>
                                <tr style="background: #000ce2; font-size: 20px; font-width: 700; font-family:sans-serif">
                                    <td class="header_style_left" colspan="4"></td>
                                    <td colspan="1"> <strong class="text-white"> {{ __('Total Price:') }} </strong></td>
                                    <td class="header_style_right text-white" colspan="2"><strong> {{number_format($sub_total,2)}} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }} </strong></td>
                                </tr>
                            </tfooter>
                        </table>
                        {{ $suppliers->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
