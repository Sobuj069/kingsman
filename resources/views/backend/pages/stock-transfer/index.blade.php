@extends('backend.layouts.master')
@section('section-title', __('Stock Transfer'))
@section('page-title', __('Transfer List'))
@if (check_permission('transfer.create'))
    @section('action-button')
        <a href="{{ route('transfer.create') }}" class="btn add_list_btn">
            <i class="mr-2 feather icon-plus"></i>
            {{ __('Add Stock Transfer') }}
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

        .table-responsive {
            overflow-x: auto;
            min-height: 250px;
        }

        @media (min-width: 992px) {
            .table-responsive {
                overflow: visible !important;
            }
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <form action="{{ route('transfer.index') }}" method="GET">
                        @php
                            $products = App\Models\Product::get();
                        @endphp
                        <div class="row h-hide">
                            <div class="col-md-3 col-12 mt-3">
                                <input type="date" class="form-control" name="startDate" value="{{ $startDate }}" />
                            </div>
                            <div class="col-md-3 col-12 mt-3">
                                <input type="date" class="form-control" name="endDate" value="{{ $endDate }}" />
                            </div>
                            <div class="col-md-3 col-12 mt-3">
                                <input type="text" placeholder="{{ __('Scan Barcode / Transfer No') }}" name="barcode"
                                    value="{{ $barcode ?? ($invoice_no ?? '') }}" class="form-control barcode-filter-input" data-barcode-input>
                            </div>
                            <div class="col-md-3 col-12 mt-3">
                                <select name="product_id" id="" class="select2">
                                    <option value="">{{ __('Select Product') }}</option>
                                    @foreach ($products as $item)
                                        <option value="{{ $item->id }}"{{ $product_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3 h-hide d-flex justify-content-between">
                            <div class="col-md-12">
                                <button type="submit" class="btn add_list_btn">{{ __('Filter') }}</button>
                                <a href="{{ route('transfer.index') }}" class="btn add_list_btn_reset">{{ __('Reset') }}</a>
                                <a href="" class="btn add_list_btn float-right" onclick="window.print()">{{ __('Print') }}</a>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive mt-2">
                        <table id="datatable-buttons" class="table table-striped table-bordered">
                            <thead class="header_bg">
                                <tr class="text-center">
                                    <th class="header_style_left"> {{ __('SL#') }} </th>
                                    <th> {{ __('Date') }} </th>
                                    <th> {{ __('Transfer No') }} </th>
                                    <th> {{ __('From') }} </th>
                                    <th> {{ __('To') }} </th>
                                    <th> {{ __('Product Item(s)') }} </th>
                                    <th> {{ __('Status') }} </th>
                                    <th> {{ __('Create By') }} </th>
                                    <th class="header_style_right"> {{ __('Action') }} </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transfers as $key => $data)
                                    @php
                                        $transfer_items = App\Models\TransferItem::where(
                                            'transfer_id',
                                            $data->id,
                                        )->get();

                                        $userBranchId = auth()->user()->branch_id;
                                        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
                                        $receive = false;
                                        $cancel = false;
                                        if ($userBranchId == 1) {
                                            if ($filterBranchId) {
                                                if (
                                                    $filterBranchId == $data->to_branch_id ||
                                                    $filterBranchId == $data->from_branch_id
                                                ) {
                                                    $receive = true;
                                                    $cancel = true;
                                                }
                                            } else {
                                            }
                                        } else {
                                            if ($userBranchId == $data->to_branch_id) {
                                                $receive = true;
                                                $cancel = true;
                                            }
                                            if ($userBranchId == $data->from_branch_id) {
                                                $cancel = true;
                                            }
                                        }

                                    @endphp
                                    <tr class="text-center">
                                        <td class="table_data_style_left">{{ $key + 1 }}</td>
                                        <td>{{ $data->date }}</td>
                                        <td>{{ $data->transfer_no }}</td>
                                        <td>{{ $data->fromBranch?->name }}</td>
                                        <td>{{ $data->toBranch?->name }}</td>
                                        <td>
                                            @foreach ($transfer_items as $item)
                                                @php
                                                    $product = App\Models\Product::where(
                                                        'id',
                                                        $item->product_id,
                                                    )->first();
                                                    if ($product->unit->related_unit == null) {
                                                        $qty = $item->main_qty;
                                                    } else {
                                                        $qty = 0;
                                                    }
                                                    
                                                @endphp
                                                <ul>
                                                    <li>
                                                        {{ $item->product?->name }}
                                                        @if($item->product_variation_id != null)
                                                            ({{ $item->product_variation->color?->color }}-{{ $item->product_variation->size?->size }})
                                                        @endif
                                                        (@if ($item->product->unit?->related_unit == null)
                                                            {{ $item->main_qty . ' ' . $item->product->unit?->name }}
                                                        @else
                                                            {{ $item->main_qty . ' ' . $item->product->unit->name . ' ' . $item->sub_qty . ' ' . $item->product->unit->related_unit->name }}
                                                        @endif)
                                                    </li>
                                                </ul>
                                            @endforeach
                                        </td>
                                        <td>
                                            @if ($data->status == 0)
                                                <span class="badge bg-info">{{ __('Pending') }}</span>
                                            @elseif($data->status == 1)
                                                <span class="badge bg-success">{{ __('Receive') }}</span>
                                            @elseif($data->status == 2)
                                                <span class="badge bg-danger">{{ __('Cancel') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $data->user?->name }}</td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item" href="{{ route('transfer.print', $data->id) }}"
                                                        class="btn btn-success-rgba">
                                                        <i class="feather icon-printer"></i> {{ __('Print') }}
                                                    </a>
                                                    @if ($data->status == 0)
                                                        @if ($receive)
                                                            <form action="{{ route('transfer.receive', $data->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item btn">
                                                                    <i class="fa fa-envelope-open"></i> {{ __('Receive') }}
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endif
                                                    @if ($data->status == 0)
                                                        @if ($cancel)
                                                        <form action="{{ route('transfer.cancel', $data->id) }}"
                                                                method="POST">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item btn">
                                                                    <i class="fa fa-times"></i> {{ __('Cancel') }}
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endif
                                                    
                                                    {{-- delete --}}
                                                    @if (check_permission('transfer.destroy'))
                                                        <a href="#" class="dropdown-item" data-toggle="modal"
                                                            data-target="#deleteModal-{{ $data->id }}"
                                                            class="btn btn-danger-rgba">
                                                            <i class="feather icon-trash"></i> {{ __('Delete') }}
                                                        </a>
                                                        {{-- @endif --}}
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- delete modal --}}
                                    <form action="{{ route('transfer.destroy', $data->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-delete-modal title="{{ __('Stock Transfer') }}" id="{{ $data->id }}" />
                                    </form>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center text-danger no_data_style">{{ __('No Data Found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $transfers->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
