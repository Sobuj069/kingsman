@extends('backend.layouts.master')
@section('section-title', __('Service'))
@section('page-title', __('Service List'))
@if (check_permission('service.create'))
    @section('action-button')
        <a href="{{ route('service.create') }}" class="btn add_list_btn">
            <i class="mr-2 feather icon-plus"></i>
            {{ __('Add Service') }}
        </a>
    @endsection
@endif
@push('css')
<style>
    @media print {
        @page {
        size: auto;
        }

        body {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: Roboto,sans-serif;
        }

        .print_area {
            position: absolute;
            top: 0;
            width: 100%;
        }

        .print_area * {
            visibility: visible !important;
        }
    }
</style>
@endpush
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <form action="{{ route('service.index') }}" method="GET">
                        <div class="row">

                            <div class="col-md-3 mt-3">
                                <input type="text" class="form-control" name="barcode" placeholder="{{ __('Enter Barcode') }}" value="{{ $barcode }}" />
                            </div>

                            <div class="col-md-3 mt-3">
                                <select name="product_id" id="" class="select2">
                                    <option value="">{{ __('Select Service') }}</option>
                                    @foreach ($produc as $item)
                                        <option value="{{ $item->id }}"{{ ($product_id == $item->id)?'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mt-3">
                                <button type="submit" class="btn add_list_btn">{{ __('Filter') }}</button>
                                <a href="{{ route('service.index') }}" class="btn add_list_btn_reset">{{ __('Reset') }}</a>
                            </div>
                            <div class="col-md-3 mt-3 text-right">
                                <a href="" class="btn add_list_btn" onclick="window.print()">
                                    <i class="feather icon-printer mr-1"></i> {{ __('Print') }}
                                </a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive mt-2.5">
                        <table id="datatable-buttons" class="table table-striped">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th>{{ __('Branch') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Barcode') }}</th>
                                    <th>{{ __('Cost Price') }}</th>
                                    <th>{{ __('Sale Price') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="header_style_right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $data)
                                    @php
                                        $count_inv = App\Models\InvoiceItem::where('product_id', $data->id)->count();
                                        $count_pur = App\Models\PurchaseItem::where('product_id', $data->id)->count();
 
                                        $userBranchId = auth()->user()->branch_id;
                                        $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);
 
                                        if ($userBranchId == 1) {
                                            if($filterBranchId){
                                                $branch_product = App\Models\BranchProduct::where('branch_id',$filterBranchId)->where(
                                                    'product_id',
                                                    $data->id,
                                                )->get();
                                            }else{
                                                $branch_product = App\Models\BranchProduct::where(
                                                    'product_id',
                                                    $data->id,
                                                )->get();
                                            }
                                        } else {
                                            $branch_product = App\Models\BranchProduct::where('product_id', $data->id)
                                                ->where('branch_id', $userBranchId)
                                                ->get();
                                        }
                                    @endphp
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>
                                            @foreach ($branch_product as $branch)
                                                <span class="badge badge-primary">{{ $branch->branch?->name }}</span>
                                            @endforeach
                                        </td>
                                        <td>{{ $data->name }}</td>
                                        <td>{{ $data->barcode }}</td>
                                        <td>{{ number_format($data->purchase_price ?? 0, 2) }}</td>
                                        <td>{{ number_format($data->selling_price ?? 0, 2) }}</td>
                                        <td>
                                            @if($data->status == 1 )
                                                {{ __('Active') }}
                                            @endif
                                            @if($data->status == 0)
                                                {{ __('Deactive') }}
                                            @endif
                                        </td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    @if (check_permission('service.edit'))
                                                        <a href="{{ route('service.edit', $data->id) }}"
                                                            class="dropdown-item text-primary">
                                                            <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                        </a>
                                                    @endif

                                                    {{-- delete --}}
                                                    @if (check_permission('service.destroy'))
                                                        @if ($count_inv<1 && $count_pur<1)
                                                            <a href="#" data-toggle="modal"
                                                                data-target="#deleteModal-{{ $data->id }}"
                                                                class="dropdown-item text-danger {{ $data->id == 1 ? 'disabled' : '' }}">
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
                                        <td colspan="100%" class="text-center text-danger no_data_style">{{ __('No Data Available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{--  Loop through again to render modals outside table for better layout --}}
                    @foreach ($products as $data)
                        {{-- delete modal --}}
                        <form action="{{ route('service.destroy', $data->id) }}" method="POST">
                            @csrf
                            @method('delete')
                            <x-delete-modal title="{{ __('Service') }}" id="{{ $data->id }}" />
                        </form>
                    @endforeach

                    <div class="pagination justify-content-center mt-3">
                        {{ $products->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
