@extends('backend.layouts.master')
@section('section-title', __('Stock Adjust'))
@section('page-title', __('Adjust Stock List'))
@if (check_permission('stock-adjust.create'))
    @section('action-button')
        <a href="{{ route('stock-adjust.create') }}" class="btn add_list_btn">
            <i class="mr-2 feather icon-plus"></i>
            {{ __('Add Stock Adjust') }}
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
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <form action="{{ route('stock-adjust.index') }}" method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-4 mt-1">
                                <input type="text" name="barcode" class="form-control barcode-filter-input" data-barcode-input
                                    placeholder="{{ __('Scan Barcode / Adjust No / Product') }}" value="{{ $barcode ?? '' }}">
                            </div>
                            <div class="col-md-3 mt-1">
                                <input type="date" name="startDate" class="form-control" value="{{ $startDate ?? '' }}">
                            </div>
                            <div class="col-md-3 mt-1">
                                <input type="date" name="endDate" class="form-control" value="{{ $endDate ?? '' }}">
                            </div>
                            <div class="col-md-2 mt-1">
                                <button type="submit" class="btn add_list_btn">{{ __('Filter') }}</button>
                                <a href="{{ route('stock-adjust.index') }}" class="btn add_list_btn_reset">{{ __('Reset') }}</a>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive mt-2">
                        <table id="datatable-buttons" class="table table-striped table-bordered text-center">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left"> {{ __('SL#') }} </th>
                                    <th> {{ __('Date') }} </th>
                                    <th> {{ __('Branch') }} </th>
                                    <th> {{ __('Adjust No') }} </th>
                                    <th> {{ __('Product Item(s)') }} </th>
                                    <th> {{ __('Stock Status') }}</th>
                                    <th> {{ __('Create By') }} </th>
                                    <th class="header_style_right"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($adjust_stocks as $key => $data)
                                    @php
                                        $adjust_items = App\Models\AdjustStockItem::where(
                                            'adjust_id',
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
                                    <tr>
                                        <td class="table_data_style_left">{{ $key + 1 }}</td>
                                        <td>{{ $data->date }}</td>
                                        <td>{{ $data->branch?->name }}</td>
                                        <td>{{ $data->adjust_no }}</td>
                                        <td>
                                            @foreach ($adjust_items as $item)
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
                                                        @if ($item->product_variation_id != null)
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
                                            @if ($data->stock_status == 0)
                                                <span class="badge bg-danger">{{ __('Stock Out') }}</span>
                                            @elseif($data->stock_status == 1)
                                                <span class="badge bg-success">{{ __('Stock In') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $data->user?->name }}</td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown">
                                                    Action
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    @if (check_permission('stock-adjust.destroy'))
                                                        <a href="#" class="dropdown-item" data-toggle="modal"
                                                            data-target="#deleteModal-{{ $data->id }}"
                                                            class="btn btn-danger-rgba">
                                                            <i class="feather icon-trash"></i> Delete
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center text-danger no_data_style">{{ __('No Data Found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{--  Loop through again to render modals outside table for better layout --}}
                    @foreach ($adjust_stocks as $key => $data)
                        {{-- delete modal --}}
                        <form action="{{ route('stock-adjust.destroy', $data->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <x-delete-modal title="{{ __('Stock Adjust') }}" id="{{ $data->id }}" />
                        </form>
                    @endforeach

                    <div class="mt-3">
                        {{ $adjust_stocks->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
