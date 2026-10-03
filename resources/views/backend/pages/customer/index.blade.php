@extends('backend.layouts.master')
@section('section-title', __('Customer'))
@section('page-title', __('Customer List'))
@if (check_permission('customer.store'))
    @section('action-button')
        <a href="#" data-toggle="modal" data-target="#addModal" class="btn add_list_btn">
            <i class="mr-2 feather icon-plus"></i>
            {{ __('Add Customer') }}
        </a>
    @endsection
@endif
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
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body pt-1">
                    <form action="{{ route('customer.index') }}" method="GET">
                        @php
                            $custommer = App\Models\Customer::get();
                        @endphp
                        <div class="form-row align-items-end mb-3 h-hide">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">{{ __('Select Customer') }}</label>
                                <select name="customer_id" id="" class="form-control select2">
                                    <option value="">{{ __('All Customer') }}</option>
                                    @foreach ($custommer as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $customer_id == $item->id ? 'selected' : '' }}>{{ $item->name }}{{ $item->phone ? ' - ' . $item->phone : '' }}</option>
                                    @endforeach

                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">{{ __('Type') }}</label>
                                <select name="customer_type" id="" class="form-control select2">
                                    <option value="">{{ __('Type') }}</option>
                                    <option value="regular">{{ __('Regular') }}</option>
                                    <option value="golden">{{ __('Golden') }}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label class="font-weight-bold">{{ __('Phone / Barcode') }}</label>
                                <input type="text" placeholder="{{ __('Scan Barcode / Phone') }}" name="barcode"
                                    value="{{ $barcode ?? ($phone_no ?? '') }}" class="form-control barcode-filter-input" data-barcode-input style="height: 38px !important;">
                            </div>
                            <div class="form-group col-md-5">
                                <label>&nbsp;</label>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <button type="submit" class="btn add_list_btn" style="padding-top: 8px !important; padding-bottom: 8px !important;">
                                            <i class="fa fa-sliders mr-1"></i> {{ __('Filter') }}
                                        </button>
                                        <a href="{{ route('customer.index') }}" class="btn add_list_btn_reset ml-1" style="padding-top: 8px !important; padding-bottom: 8px !important;">
                                            {{ __('Reset') }}
                                        </a>
                                    </div>
                                    <div>
                                        <a href="" class="btn add_list_btn" style="padding-top: 8px !important; padding-bottom: 8px !important;" onclick="window.print()">
                                            <i class="feather icon-printer mr-1"></i> {{ __('Print') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive mt-3">
                        <table id="datatable-buttons" class="table table-striped text-center">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th>{{ __('Name & Email & Phone') }}</th>
                                    <th>{{ __('Branch') }}</th>
                                    <th>{{ __('Total Invoice') }}</th>
                                    <th>{{ __('Paid Invoice') }}</th>
                                    <th>{{ __('Due Invoice') }}</th>
                                    <th>{{ __('Personal Balance') }}</th>
                                    @if(env('APP_LOYALTY') == 'yes')
                                        <th>{{ __('Reward Points') }}</th>
                                    @endif
                                    <th class="header_style_right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_amount = 0;
                                    $total_paid = 0;
                                    $total_due = 0;
                                    $personal_balance = 0;
                                    $total_count = 0;
                                @endphp
                                @forelse($customers as $data)
                                    @php
                                        $count_cus = App\Models\Invoice::where('customer_id', $data->id)->count();
                                        $count_tra = App\Models\Transaction::where('customer_id', $data->id)->count();
                                        $open_balance = open_balance_customer($data->id, $data->due_amount);
                                        $inv_total = App\Models\Invoice::where('customer_id', $data->id)->sum(
                                            'total_amount',
                                        );
                                        $inv_paid = App\Models\Invoice::where('customer_id', $data->id)->sum(
                                            'total_paid',
                                        );
                                        // $inv_due = App\Models\Invoice::where('customer_id', $data->id)->sum('total_due');
                                        $inv_due = App\Models\Invoice::where('customer_id', $data->id)
                                            ->where('status', 0)
                                            ->sum('total_due');
                                        $total_amount += $inv_total;
                                        $total_paid += $inv_paid;
                                        $total_due += $inv_due;
                                        $personal_balance += $open_balance;

                                    @endphp
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>{{ $data->name }} <br> {{ $data->phone }} <br>
                                            {{ $data->email == null ? 'NULL' : $data->email }}
                                            @if($data->discountGroup)
                                                <br><span class="badge badge-info text-white mt-1"><i class="feather icon-tag mr-1"></i>{{ $data->discountGroup->name }} ({{ $data->discountGroup->type == 'percentage' ? number_format($data->discountGroup->value, 0).'%' : 'Tk '.number_format($data->discountGroup->value, 0) }})</span>
                                            @endif
                                        </td>
                                        <td>{{ $data->branch?->name }}</td>
                                        <td class="font-weight-bold">{{ $inv_total }}
                                            {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</td>
                                        <td class="font-weight-bold">{{ $inv_paid }}
                                            {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</td>
                                        <td class="font-weight-bold">{{ $inv_due }}
                                            {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</td>
                                        <td class="font-weight-bold">{{ $open_balance }}
                                            {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</td>
                                        @if(env('APP_LOYALTY') == 'yes')
                                        <td class="font-weight-bold">
                                            <span class="badge badge-warning text-dark px-2 py-1" style="background-color: #fef08a; border: 1px solid #fde047; font-size: 12px;">
                                                <i class="fa fa-star text-warning mr-1"></i>{{ number_format((float)$data->total_point, 0) }} pts
                                                <br><small class="text-muted">(৳{{ number_format((float)$data->total_point * 0.75, 2) }})</small>
                                            </span>
                                        </td>
                                        @endif
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item show-barcode" href="#"
                                                        class="btn btn-success-rgba" data-toggle="modal"
                                                        data-code="{{ $data->memberShip_id }}"
                                                        data-target="#printModal-{{ $data->id }}">
                                                        <i class="feather icon-printer"></i> {{ __('Label') }}
                                                    </a>
                                                    <a href="#" data-toggle="modal"
                                                        data-target="#detailsModal-{{ $data->id }}"
                                                        class="dropdown-item text-info">
                                                        <i class="feather icon-eye"></i> {{ __('View Details') }}
                                                    </a>
                                                    <a href="{{ route('report.customer.full-report', ['customer_id' => $data->id]) }}"
                                                        class="dropdown-item text-secondary">
                                                        <i class="feather icon-file-text"></i> {{ __('Full Report') }}
                                                    </a>
                                                    @if ($data->id != 1)
                                                        @if (check_permission('customer.update'))
                                                            <a href="#" data-toggle="modal"
                                                                data-target="#editModal-{{ $data->id }}"
                                                                class="dropdown-item text-primary">
                                                                <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                            </a>
                                                        @endif

                                                        @if (check_permission('customer.destroy'))
                                                            @if ($count_cus < 1)
                                                                <a href="#" data-toggle="modal"
                                                                    data-target="#deleteModal-{{ $data->id }}"
                                                                    class="dropdown-item text-danger">
                                                                    <i class="feather icon-trash"></i> {{ __('Delete') }}
                                                                </a>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center text-danger">{{ __('No Data Available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="header_bg text-right">
                                    <td class="header_style_left" colspan="3"><strong
                                            style="font-size: 18px;color:rgb(255, 255, 255);">{{ __('Total') }}({{ count($customers) }}):
                                        </strong></td>
                                    <td> <strong
                                            style="font-size: 18px;color:rgb(255, 255, 255);">{{ number_format($total_amount, 2) }}{{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</strong>
                                    </td>
                                    <td> <strong
                                            style="font-size: 18px;color:rgb(255, 255, 255);">{{ number_format($total_paid, 2) }}{{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</strong>
                                    </td>
                                    <td> <strong
                                            style="font-size: 18px;color:rgb(255, 255, 255);">{{ number_format($total_due, 2) }}{{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</strong>
                                    </td>
                                    <td> <strong
                                            style="font-size: 18px;color:rgb(255, 255, 255);">{{ number_format($personal_balance, 2) }}{{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</strong>
                                    </td>
                                    <td class="header_style_right" colspan="1"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{--  Loop through again to render modals outside table for better layout --}}
                    @foreach ($customers as $data)
                        {{-- edit modal  --}}
                        <form action="{{ route('customer.update', $data->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <x-edit-modal title="{{ __('Edit Customer') }}" id="{{ $data->id }}" sizeClass="modal-xl">
                                <div class="row">
                                    @php $phoneOnlyAllowed = (env('APP_CUSTOMER_PHONE_ONLY') == 'yes' && env('APP_ONLINE') != 'yes'); @endphp
                                    <div class="mb-3 col-md-6 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Customer Name') }} {{ $phoneOnlyAllowed ? '' : '*' }}</label>
                                        <input type="text" class="form-control" name="name" placeholder="{{ __('Enter Name') }}" {{ $phoneOnlyAllowed ? '' : 'required' }} value="{{ $data->name }}">
                                    </div>
                                    @if ($data->id != 1)
                                        @if (auth()->user()->branch_id == 1)
                                            <div class="mb-3 col-md-6 text-left">
                                                <label class="form-label font-weight-bold">{{ __('Branch *') }}</label>
                                                <select class="form-control select2" name="branch_id" required style="width: 100%" data-placeholder="{{ __('Select Branch') }}">
                                                    @foreach ($allBranch as $branch)
                                                        <option value="{{ $branch->id }}" {{ $branch->id == $data->branch_id ? 'selected' : '' }}>
                                                            {{ $branch->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                    @endif
                                    <div class="mb-3 col-md-6 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Phone *') }}</label>
                                        <input type="text" class="form-control" name="phone" value="{{ $data->phone }}" required placeholder="{{ __('Enter Phone') }}">
                                    </div>
                                    <div class="mb-3 col-md-6 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Email') }}</label>
                                        <input type="email" class="form-control" name="email" value="{{ $data->email }}" placeholder="{{ __('Enter Email') }}">
                                    </div>
                                    <div class="mb-3 col-md-6 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Address') }}</label>
                                        <input type="text" class="form-control" name="address" value="{{ $data->address }}" placeholder="{{ __('Enter Address') }}">
                                    </div>
                                    @if(!is_hide_customer_dates())
                                    <div class="mb-3 col-md-6 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Birth Date') }}</label>
                                        <input type="date" class="form-control" name="birth_date" value="{{ $data->birth_date }}">
                                    </div>
                                    <div class="mb-3 col-md-6 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Anniversary Date') }}</label>
                                        <input type="date" class="form-control" name="anni_date" value="{{ $data->anni_date }}">
                                    </div>
                                    @endif
                                    <div class="mb-3 col-md-6 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Due Amount') }}</label>
                                        <input type="text" class="form-control" name="due_amount" value="{{ $data->due_amount }}">
                                    </div>
                                    @if (env('APP_DISCOUNT_GROUP') == 'yes')
                                    <div class="mb-3 col-md-6 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Discount Group') }}</label>
                                        <select class="form-control select2" name="discount_group_id" style="width: 100%" data-placeholder="{{ __('Select Discount Group') }}">
                                            <option value="">{{ __('No Discount Group') }}</option>
                                            @foreach ($discountGroups as $dg)
                                                <option value="{{ $dg->id }}" {{ $data->discount_group_id == $dg->id ? 'selected' : '' }}>{{ $dg->name }} ({{ $dg->type == 'percentage' ? number_format($dg->value, 0).'%' : 'Tk '.number_format($dg->value, 0) }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                    
                                    @if(env('APP_AUTOMOBILE') == 'yes')
                                    <div class="col-md-12 text-left">
                                        <h6 class="font-weight-bold text-primary mt-3">{{ __('Vehicle Details') }}</h6>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="vehicle-container">
                                            @forelse($data->vehicles as $vehicle)
                                                <div class="row col-md-12 vehicle-block position-relative border p-2 mb-3 rounded" style="border-style: dashed !important; border-width: 1.5px !important; border-color: #cbd5e1 !important; margin-left: 0; margin-right: 0;">
                                                    <button type="button" class="btn btn-sm btn-danger remove-vehicle-btn position-absolute" style="top: -10px; right: -10px; z-index: 10; border-radius: 50%; width: 24px; height: 24px; padding: 0; {{ $loop->first ? 'display: none;' : '' }}"><i class="fa-solid fa-xmark"></i></button>
                                                    <div class="mb-3 col-md-3 text-left">
                                                        <label class="form-label font-weight-bold">{{ __('Vehicle Name') }}</label>
                                                        <input type="text" class="form-control" name="vehicle_name[]" value="{{ $vehicle->vehicle_name }}" placeholder="{{ __('Vehicle Name') }}">
                                                    </div>
                                                    <div class="mb-3 col-md-3 text-left">
                                                        <label class="form-label font-weight-bold">{{ __('Reg No') }}</label>
                                                        <input type="text" class="form-control" name="reg_no[]" value="{{ $vehicle->reg_no }}" placeholder="{{ __('Reg No') }}">
                                                    </div>
                                                    <div class="mb-3 col-md-3 text-left">
                                                        <label class="form-label font-weight-bold">{{ __('Model') }}</label>
                                                        <input type="text" class="form-control" name="model[]" value="{{ $vehicle->model }}" placeholder="{{ __('Model') }}">
                                                    </div>
                                                    <div class="mb-3 col-md-3 text-left">
                                                        <label class="form-label font-weight-bold">{{ __('Made In') }}</label>
                                                        <input type="text" class="form-control" name="made_in[]" value="{{ $vehicle->made_in }}" placeholder="{{ __('Made In') }}">
                                                    </div>
                                                    <div class="mb-3 col-md-3 text-left">
                                                        <label class="form-label font-weight-bold">{{ __('Engine No') }}</label>
                                                        <input type="text" class="form-control" name="engine_no[]" value="{{ $vehicle->engine_no }}" placeholder="{{ __('Engine No') }}">
                                                    </div>
                                                    <div class="mb-3 col-md-3 text-left">
                                                        <label class="form-label font-weight-bold">{{ __('Chassis No') }}</label>
                                                        <input type="text" class="form-control" name="chassis_no[]" value="{{ $vehicle->chassis_no }}" placeholder="{{ __('Chassis No') }}">
                                                    </div>
                                                    <div class="mb-3 col-md-3 text-left">
                                                        <label class="form-label font-weight-bold">{{ __('Milage') }}</label>
                                                        <input type="text" class="form-control" name="milage[]" value="{{ $vehicle->milage }}" placeholder="{{ __('Milage') }}">
                                                    </div>
                                                    <div class="mb-3 col-md-3 text-left">
                                                        <label class="form-label font-weight-bold">{{ __('Driver Name') }}</label>
                                                        <input type="text" class="form-control" name="driver_name[]" value="{{ $vehicle->driver_name }}" placeholder="{{ __('Driver Name') }}">
                                                    </div>
                                                    <div class="mb-3 col-md-3 text-left">
                                                        <label class="form-label font-weight-bold">{{ __('Driver Phone') }}</label>
                                                        <input type="text" class="form-control" name="driver_phone[]" value="{{ $vehicle->driver_phone }}" placeholder="{{ __('Driver Phone') }}">
                                                    </div>
                                                </div>
                                                                            @empty
                                                <div class="row col-md-12 vehicle-block position-relative border p-2 mb-3 rounded" style="border-style: dashed !important; border-width: 1.5px !important; border-color: #cbd5e1 !important; margin-left: 0; margin-right: 0;">
                                                    <button type="button" class="btn btn-sm btn-danger remove-vehicle-btn position-absolute" style="top: -10px; right: -10px; z-index: 10; border-radius: 50%; width: 24px; height: 24px; padding: 0; display: none;"><i class="fa-solid fa-xmark"></i></button>
                                                    <div class="mb-3 col-md-3 text-left">
                                                        <label class="form-label font-weight-bold">{{ __('Vehicle Name') }}</label>
                                                        <input type="text" class="form-control" name="vehicle_name[]" value="{{ $data->vehicle_name }}" placeholder="{{ __('Vehicle Name') }}">
                                                    </div>
                                                    <div class="mb-3 col-md-3 text-left">
                                                        <label class="form-label font-weight-bold">{{ __('Reg No') }}</label>
                                                        <input type="text" class="form-control" name="reg_no[]" value="{{ $data->reg_no }}" placeholder="{{ __('Reg No') }}">
                                                    </div>
                                                    <div class="mb-3 col-md-3 text-left">
                                                        <label class="form-label font-weight-bold">{{ __('Model') }}</label>
                                                        <input type="text" class="form-control" name="model[]" value="{{ $data->model }}" placeholder="{{ __('Model') }}">
                                                    </div>
                                                    <div class="mb-3 col-md-3 text-left">
                                                        <label class="form-label font-weight-bold">{{ __('Made In') }}</label>
                                                        <input type="text" class="form-control" name="made_in[]" value="{{ $data->made_in }}" placeholder="{{ __('Made In') }}">
                                                    </div>
                                                    <div class="mb-3 col-md-3 text-left">
                                                        <label class="form-label font-weight-bold">{{ __('Engine No') }}</label>
                                                        <input type="text" class="form-control" name="engine_no[]" value="{{ $data->engine_no }}" placeholder="{{ __('Engine No') }}">
                                                    </div>
                                                    <div class="mb-3 col-md-3 text-left">
                                                        <label class="form-label font-weight-bold">{{ __('Chassis No') }}</label>
                                                        <input type="text" class="form-control" name="chassis_no[]" value="{{ $data->chassis_no }}" placeholder="{{ __('Chassis No') }}">
                                                    </div>
                                                    <div class="mb-3 col-md-3 text-left">
                                                        <label class="form-label font-weight-bold">{{ __('Milage') }}</label>
                                                        <input type="text" class="form-control" name="milage[]" value="{{ $data->milage }}" placeholder="{{ __('Milage') }}">
                                                    </div>
                                                    <div class="mb-3 col-md-3 text-left">
                                                        <label class="form-label font-weight-bold">{{ __('Driver Name') }}</label>
                                                        <input type="text" class="form-control" name="driver_name[]" value="{{ $data->driver_name }}" placeholder="{{ __('Driver Name') }}">
                                                    </div>
                                                    <div class="mb-3 col-md-3 text-left">
                                                        <label class="form-label font-weight-bold">{{ __('Driver Phone') }}</label>
                                                        <input type="text" class="form-control" name="driver_phone[]" value="{{ $data->driver_phone }}" placeholder="{{ __('Driver Phone') }}">
                                                    </div>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-12 mt-2 text-left">
                                        <button type="button" class="btn btn-info btn-sm add-vehicle-btn" style="border-radius: 6px;"><i class="fa fa-plus mr-1"></i>{{ __('Add Another Vehicle') }}</button>
                                    </div>
                                    @endif
                                </div>
                            </x-edit-modal>
                        </form>

                        {{-- delete modal --}}
                        <form action="{{ route('customer.destroy', $data->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <x-delete-modal title="{{ __('Customer') }}" id="{{ $data->id }}" />
                        </form>

                        {{-- Customer Details Modal --}}
                        <div class="modal fade" id="detailsModal-{{ $data->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);">
                                        <h5 class="modal-title text-white font-weight-bold">
                                            <i class="feather icon-user mr-2"></i>{{ $data->name }} &mdash; {{ __('Customer Details') }}
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal">
                                            <i class="feather icon-x"></i>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        {{-- Basic Info --}}
                                        <h6 class="font-weight-bold" style="color:#4f46e5; border-left:4px solid #4f46e5; padding-left:10px;">{{ __('Basic Information') }}</h6>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <table class="table table-sm table-borderless">
                                                    <tr><th width="140">{{ __('Name') }}</th><td>: {{ $data->name }}</td></tr>
                                                    <tr><th>{{ __('Phone') }}</th><td>: {{ $data->phone }}</td></tr>
                                                    <tr><th>{{ __('Email') }}</th><td>: {{ $data->email ?? '—' }}</td></tr>
                                                    <tr><th>{{ __('Address') }}</th><td>: {{ $data->address ?? '—' }}</td></tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-sm table-borderless">
                                                    <tr><th width="160">{{ __('Customer Type') }}</th><td>: {{ $data->customer_type ?? '—' }}</td></tr>
                                                    @if(!is_hide_customer_dates())
                                                    <tr><th>{{ __('Birth Date') }}</th><td>: {{ $data->birth_date ?? '—' }}</td></tr>
                                                    <tr><th>{{ __('Anniversary Date') }}</th><td>: {{ $data->anni_date ?? '—' }}</td></tr>
                                                    @endif
                                                    <tr><th>{{ __('Opening Due') }}</th><td>: <strong class="text-danger">{{ number_format($data->due_amount ?? 0, 2) }}</strong></td></tr>
                                                </table>
                                            </div>
                                        </div>

                                        @if(env('APP_AUTOMOBILE') == 'yes')
                                        {{-- Vehicles --}}
                                        @if($data->vehicles->count() > 0)
                                        <h6 class="font-weight-bold mt-2" style="color:#059669; border-left:4px solid #059669; padding-left:10px;">{{ __('Vehicle Details') }}</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>{{ __('Vehicle') }}</th>
                                                        <th>{{ __('Reg No') }}</th>
                                                        <th>{{ __('Model') }}</th>
                                                        <th>{{ __('Made In') }}</th>
                                                        <th>{{ __('Engine No') }}</th>
                                                        <th>{{ __('Chassis No') }}</th>
                                                        <th>{{ __('Milage') }}</th>
                                                        <th>{{ __('Driver') }}</th>
                                                        <th>{{ __('Driver Phone') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($data->vehicles as $vi => $v)
                                                    <tr>
                                                        <td>{{ $vi+1 }}</td>
                                                        <td>{{ $v->vehicle_name ?? '—' }}</td>
                                                        <td>{{ $v->reg_no ?? '—' }}</td>
                                                        <td>{{ $v->model ?? '—' }}</td>
                                                        <td>{{ $v->made_in ?? '—' }}</td>
                                                        <td>{{ $v->engine_no ?? '—' }}</td>
                                                        <td>{{ $v->chassis_no ?? '—' }}</td>
                                                        <td>{{ $v->milage ?? '—' }}</td>
                                                        <td>{{ $v->driver_name ?? '—' }}</td>
                                                        <td>{{ $v->driver_phone ?? '—' }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @endif
                                        @endif

                                    </div>
                                    <div class="modal-footer">
                                        <a href="{{ route('report.customer.full-report', ['customer_id' => $data->id]) }}" class="btn add_list_btn">
                                            <i class="feather icon-file-text mr-1"></i>{{ __('View Full Report') }}
                                        </a>
                                        <button type="button" class="btn cancel_btn" data-dismiss="modal">{{ __('Close') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="printModal-{{ $data->id }}" tabindex="-1"
                            aria-labelledby="printModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header d-flex justify-content-between align-items-center">
                                        <h5 class="modal-title font-weight-bold" id="printModalLabel">{{ __('Print Member Card') }}</h5>
                                        <button type="button" class="close_modal_btn" data-dismiss="modal" aria-label="Close">
                                            <i class="feather icon-x"></i>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Printable design starts here -->
                                        <div id="printableArea-{{ $data->id }}" class="print_part mt-2 p-4" style="background: #ffffff !important; border-radius: 12px; color: #000000 !important; box-shadow: 0 0 15px rgba(0,0,0,0.1); border: 1px solid #eee;">

                                            <div class="logo_part text-center mb-4">
                                                <span style="font-size: 28px; display: block; color: #000000 !important;">
                                                    <strong style="color: #000000 !important;">{{ empty(get_setting('com_name')) ? '----' : get_setting('com_name') }}</strong>
                                                </span>
                                                <div style="height: 2px; background: #4f46e5; width: 60px; margin: 10px auto;"></div>
                                            </div>

                                            <div class="card_info">
                                                <div class="text-center mb-4">
                                                    <h4 class="font-weight-bold" style="letter-spacing: 2px; color: #4f46e5 !important;">{{ __('MEMBER SHIP CARD') }}</h4>
                                                </div>
                                                <div class="row no-gutters border-top pt-3" style="border-top: 1px solid #eee !important;">
                                                    <div class="col-12 mb-2">
                                                        <span class="small uppercase" style="font-size: 10px; display: block; color: #666666 !important;">{{ __('NAME') }}</span>
                                                        <strong style="font-size: 16px; color: #000000 !important;">{{ $data->name }}</strong>
                                                    </div>
                                                    <div class="col-12">
                                                        <span class="small uppercase" style="font-size: 10px; display: block; color: #666666 !important;">{{ __('MEMBER ID') }}</span>
                                                        <strong style="font-size: 16px; color: #000000 !important;">{{ $data->member_id }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Printable design ends here -->
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn add_list_btn"
                                            onclick="printDiv('printableArea-{{ $data->id }}')">
                                            <i class="feather icon-printer mr-1"></i> {{ __('Print Now') }}
                                        </button>
                                        <button type="button" class="btn cancel_btn"
                                            data-dismiss="modal">{{ __('Close') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="pagination justify-content-center">
                        {{ $customers->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <form action="{{ route('customer.store') }}" method="POST">
        @csrf
        <x-add-modal title="{{ __('Add Customer') }}" sizeClass="modal-xl">
            <div class="row">
                @if (auth()->user()->branch_id == 1)
                    <div class="mb-3 col-md-6 text-left">
                        <label class="form-label font-weight-bold">{{ __('Branch *') }}</label>
                        <select class="form-control select2" name="branch_id" required style="width: 100%" data-placeholder="{{ __('Select Branch') }}">
                            <option value=""></option>
                            @foreach ($allBranch as $branch_option)
                                <option value="{{ $branch_option->id }}">{{ $branch_option->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                @php $phoneOnlyAllowed = (env('APP_CUSTOMER_PHONE_ONLY') == 'yes' && env('APP_ONLINE') != 'yes'); @endphp
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Customer Name') }} {{ $phoneOnlyAllowed ? '' : '*' }}</label>
                    <input type="text" class="form-control" name="name" placeholder="{{ __('Enter Customer Name') }}" {{ $phoneOnlyAllowed ? '' : 'required' }}>
                </div>
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Phone * (11 digits)') }}</label>
                    <input type="text" class="form-control" name="phone" placeholder="{{ __('Enter 11-digit Phone Number') }}" required minlength="11">
                </div>
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Email') }}</label>
                    <input type="email" class="form-control" name="email" placeholder="{{ __('Enter Email') }}">
                </div>
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Delivery Address') }} {{ $phoneOnlyAllowed ? '' : '* (For Courier)' }}</label>
                    <input type="text" class="form-control" name="address" placeholder="{{ __('Enter Complete Address (House, Road, Area, District)') }}" {{ $phoneOnlyAllowed ? '' : 'required minlength=5' }}>
                </div>
                @if(!is_hide_customer_dates())
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Birth Date') }}</label>
                    <input type="date" class="form-control" name="birth_date">
                </div>
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Anniversary Date') }}</label>
                    <input type="date" class="form-control" name="anni_date">
                </div>
                @endif
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Due Amount') }}</label>
                    <input type="text" class="form-control" name="due_amount" value="0">
                </div>
                @if (env('APP_DISCOUNT_GROUP') == 'yes')
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Discount Group') }}</label>
                    <select class="form-control select2" name="discount_group_id" style="width: 100%" data-placeholder="{{ __('Select Discount Group') }}">
                        <option value="">{{ __('No Discount Group') }}</option>
                        @foreach ($discountGroups as $dg)
                            <option value="{{ $dg->id }}">{{ $dg->name }} ({{ $dg->type == 'percentage' ? number_format($dg->value, 0).'%' : 'Tk '.number_format($dg->value, 0) }})</option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                @if(env('APP_AUTOMOBILE') == 'yes')
                <div class="col-md-12 text-left">
                    <h6 class="font-weight-bold text-primary mt-3">{{ __('Vehicle Details') }}</h6>
                </div>
                <div class="col-md-12">
                    <div class="vehicle-container">
                        <div class="row col-md-12 vehicle-block position-relative border p-2 mb-3 rounded" style="border-style: dashed !important; border-width: 1.5px !important; border-color: #cbd5e1 !important; margin-left: 0; margin-right: 0;">
                            <button type="button" class="btn btn-sm btn-danger remove-vehicle-btn position-absolute" style="top: -10px; right: -10px; z-index: 10; border-radius: 50%; width: 24px; height: 24px; padding: 0; display: none;"><i class="fa-solid fa-xmark"></i></button>
                            <div class="mb-3 col-md-3 text-left">
                                <label class="form-label font-weight-bold">{{ __('Vehicle Name') }}</label>
                                <input type="text" class="form-control" name="vehicle_name[]" placeholder="{{ __('Vehicle Name') }}">
                            </div>
                            <div class="mb-3 col-md-3 text-left">
                                <label class="form-label font-weight-bold">{{ __('Reg No') }}</label>
                                <input type="text" class="form-control" name="reg_no[]" placeholder="{{ __('Reg No') }}">
                            </div>
                            <div class="mb-3 col-md-3 text-left">
                                <label class="form-label font-weight-bold">{{ __('Model') }}</label>
                                <input type="text" class="form-control" name="model[]" placeholder="{{ __('Model') }}">
                            </div>
                            <div class="mb-3 col-md-3 text-left">
                                <label class="form-label font-weight-bold">{{ __('Made In') }}</label>
                                <input type="text" class="form-control" name="made_in[]" placeholder="{{ __('Made In') }}">
                            </div>
                            <div class="mb-3 col-md-3 text-left">
                                <label class="form-label font-weight-bold">{{ __('Engine No') }}</label>
                                <input type="text" class="form-control" name="engine_no[]" placeholder="{{ __('Engine No') }}">
                            </div>
                            <div class="mb-3 col-md-3 text-left">
                                <label class="form-label font-weight-bold">{{ __('Chassis No') }}</label>
                                <input type="text" class="form-control" name="chassis_no[]" placeholder="{{ __('Chassis No') }}">
                            </div>
                            <div class="mb-3 col-md-3 text-left">
                                <label class="form-label font-weight-bold">{{ __('Milage') }}</label>
                                <input type="text" class="form-control" name="milage[]" placeholder="{{ __('Milage') }}">
                            </div>
                            <div class="mb-3 col-md-3 text-left">
                                <label class="form-label font-weight-bold">{{ __('Driver Name') }}</label>
                                <input type="text" class="form-control" name="driver_name[]" placeholder="{{ __('Driver Name') }}">
                            </div>
                            <div class="mb-3 col-md-3 text-left">
                                <label class="form-label font-weight-bold">{{ __('Driver Phone') }}</label>
                                <input type="text" class="form-control" name="driver_phone[]" placeholder="{{ __('Driver Phone') }}">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-12 mt-2 text-left">
                    <button type="button" class="btn btn-info btn-sm add-vehicle-btn" style="border-radius: 6px;"><i class="fa fa-plus mr-1"></i>{{ __('Add Another Vehicle') }}</button>
                </div>
                @endif
            </div>
        </x-add-modal>
    </form>

@endsection
@push('js')
    <script>
        function printDiv(divId) {
            var printContents = document.getElementById(divId).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        }

        $(document).ready(function() {
            $('.modal').on('shown.bs.modal', function () {
                $(this).find('.select2').each(function() {
                    $(this).select2({
                        dropdownParent: $(this).closest('.modal'),
                        width: '100%',
                        placeholder: $(this).data('placeholder') || 'Select Option',
                        allowClear: true
                    });
                });
            });
        });

        $(document).on('click', '.add-vehicle-btn', function() {
            var modal = $(this).closest('.modal');
            var container = modal.find('.vehicle-container');
            var firstBlock = container.find('.vehicle-block').first();
            var clone = firstBlock.clone();
            
            clone.find('input').val('');
            clone.find('.remove-vehicle-btn').show();
            container.append(clone);
        });

        $(document).on('click', '.remove-vehicle-btn', function() {
            $(this).closest('.vehicle-block').remove();
        });
    </script>
@endpush
