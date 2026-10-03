@extends('backend.layouts.master')
@section('section-title', __('Supplier'))
@section('page-title', __('Supplier List'))
@if (check_permission('supplier.store'))
    @section('action-button')
        <a href="#" data-toggle="modal" data-target="#addModal" class="btn add_list_btn">
            <i class="mr-2 feather icon-plus"></i>
            {{ __('Add Supplier') }}
        </a>
    @endsection
@endif

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <form action="{{ route('supplier.index') }}" method="GET">
                        @php
                            $supliers = App\Models\Supplier::get();
                        @endphp
                        <div class="form-row align-items-end mb-3">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">{{ __('Select Supplier') }}</label>
                                <select name="supplier_id" id="" class="form-control select2">
                                    <option value="">{{ __('All Supplier') }}</option>
                                    @foreach ($suppliers as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $supplier_id == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">{{ __('Phone / Barcode') }}</label>
                                <input type="text" placeholder="{{ __('Scan Barcode / Phone') }}" name="barcode"
                                    value="{{ $barcode ?? ($phone_no ?? '') }}" class="form-control barcode-filter-input" data-barcode-input style="height: 38px !important;">
                            </div>
                            <div class="form-group col-md-6">
                                <label>&nbsp;</label>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <button type="submit" class="btn add_list_btn" style="padding-top: 8px !important; padding-bottom: 8px !important;">
                                            <i class="fa fa-sliders mr-1"></i> {{ __('Filter') }}
                                        </button>
                                        <a href="{{ route('supplier.index') }}" class="btn add_list_btn_reset ml-1" style="padding-top: 8px !important; padding-bottom: 8px !important;">
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
                                    <th>{{ __('Total Purchase') }}</th>
                                    <th>{{ __('Purchase Paid') }}</th>
                                    <th>{{ __('Purchase Due') }}</th>
                                    <th>{{ __('Personal Balance') }}</th>
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
                                @forelse($suppliers as $data)
                                    @php
                                        $count_sup = App\Models\Purchase::where('supplier_id', $data->id)->count();
                                        $count_tra = App\Models\Transaction::where('supplier_id', $data->id)->count();
                                        $open_balance = open_balance_supplier(
                                            $data->id,
                                            $data->due_amount,
                                            $data->advance_amount,
                                        );
                                        $pur_total = App\Models\Purchase::where('supplier_id', $data->id)->sum(
                                            'total_amount',
                                        );
                                        $pur_paid = App\Models\Purchase::where('supplier_id', $data->id)->sum(
                                            'total_paid',
                                        );
                                        $pur_due = App\Models\Purchase::where('supplier_id', $data->id)->sum(
                                            'total_due',
                                        );
                                        $total_amount += $pur_total;
                                        $total_paid += $pur_paid;
                                        $total_due += $pur_due;
                                        $personal_balance += $open_balance;
                                    @endphp
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>{{ $data->name }} <br> {{ $data->phone }} <br>
                                            {{ $data->email == null ? 'NULL' : $data->email }}</td>
                                        <td>{{ $data->branch?->name }}</td>
                                        <td class="font-weight-bold">{{ $pur_total }}
                                            {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</td>
                                        <td class="font-weight-bold">{{ $pur_paid }}
                                            {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</td>
                                        <td class="font-weight-bold">{{ $pur_due }}
                                            {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</td>
                                        <td class="font-weight-bold">{{ $open_balance }}
                                            {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    @if (check_permission('supplier.update'))
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#editModal-{{ $data->id }}"
                                                            class="dropdown-item text-primary">
                                                            <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                        </a>
                                                    @endif

                                                    @if (check_permission('supplier.destroy'))
                                                        @if ($count_sup < 1)
                                                            <a href="#" data-toggle="modal"
                                                                data-target="#deleteModal-{{ $data->id }}"
                                                                class="dropdown-item text-danger">
                                                                <i class="feather icon-trash"></i> {{ __('Delete') }}
                                                            </a>
                                                        @endif
                                                    @endif

                                                </div>
                                            </div>

                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center no_data_style text-danger">{{ __('No Data Available') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="header_bg text-right">
                                    <td class="header_style_left" colspan="3"><strong
                                            style="font-size: 18px;color:rgb(255, 255, 255);">{{ __('Total') }}({{ count($suppliers) }}):
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
                    @foreach ($suppliers as $data)
                        {{-- edit modal  --}}
                        <form action="{{ route('supplier.update', $data->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <x-edit-modal title="{{ __('Edit Supplier') }}" id="{{ $data->id }}">
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Supplier Name *') }}</label>
                                    <input type="text" class="form-control" name="name" placeholder="{{ __('Enter Name') }}"
                                        required value="{{ $data->name }}">
                                </div>
                                @if (auth()->user()->branch_id == 1)
                                    <div class="mb-3 col-md-12 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Branch *') }}</label>
                                        <select class="form-control select2" name="branch_id" required style="width: 100%" data-placeholder="{{ __('Select Branch') }}">
                                            @foreach ($allBranch as $branch_item)
                                                <option value="{{ $branch_item->id }}" {{ $data->branch_id == $branch_item->id ? 'selected' : '' }}>
                                                    {{ $branch_item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <input type="hidden" name="branch_id" value="{{ $data->branch_id }}">
                                @endif
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Email') }}</label>
                                    <input type="email" class="form-control" name="email" value="{{ $data->email }}" placeholder="{{ __('Enter Email') }}">
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Phone *') }}</label>
                                    <input type="text" class="form-control" name="phone" value="{{ $data->phone }}" required placeholder="{{ __('Enter Phone') }}">
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Address') }}</label>
                                    <input type="text" class="form-control" name="address" value="{{ $data->address }}" placeholder="{{ __('Enter Address') }}">
                                </div>
                            </x-edit-modal>
                        </form>

                        {{-- delete modal --}}
                        <form action="{{ route('supplier.destroy', $data->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <x-delete-modal title="{{ __('Supplier') }}" id="{{ $data->id }}" />
                        </form>
                    @endforeach

                    <div class="pagination justify-content-center">
                        {{ $suppliers->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <form action="{{ route('supplier.store') }}" method="POST">
        @csrf
        <x-add-modal title="{{ __('Add Supplier') }}">
            @if (auth()->user()->branch_id == 1)
                <div class="mb-3 col-md-12 text-left">
                    <label class="form-label font-weight-bold">{{ __('Branch *') }}</label>
                    <select class="form-control select2" name="branch_id" required style="width: 100%" data-placeholder="{{ __('Select Branch') }}">
                        <option value=""></option>
                        @foreach ($allBranch as $branch_option)
                            <option value="{{ $branch_option->id }}">{{ $branch_option->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Supplier Name *') }}</label>
                <input type="text" class="form-control" name="name" placeholder="{{ __('Enter Supplier Name') }}" required>
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Email') }}</label>
                <input type="email" class="form-control" name="email" placeholder="{{ __('Enter Email') }}">
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Phone *') }}</label>
                <input type="text" class="form-control" name="phone" placeholder="{{ __('Enter Phone') }}" required>
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Address') }}</label>
                <input type="text" class="form-control" name="address" placeholder="{{ __('Enter Address') }}">
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Advance Amount') }}</label>
                <input type="text" class="form-control" name="advance_amount" value="0">
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Due Amount') }}</label>
                <input type="text" class="form-control" name="due_amount" value="0">
            </div>
        </x-add-modal>
    </form>

@endsection

@push('js')
    <script>
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
    </script>
@endpush
