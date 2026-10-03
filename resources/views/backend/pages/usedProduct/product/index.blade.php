@extends('backend.layouts.master')
@section('section-title', __('Used Product'))
@section('page-title', __('Product List'))
@if (check_permission('usedProduct.create'))
    @section('action-button')
        <a href="{{ route('usedProduct.create') }}" class="btn add_list_btn">
            <i class="mr-2 feather icon-plus"></i>
            {{ __('Add Product') }}
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
            <div class="card m-b-30">
                <div class="card-body">
                    <form action="{{ route('product.index') }}" method="GET">
                        @php
                            $categories = App\Models\Category::get();
                            $produc = App\Models\UsedProduct::get();
                        @endphp
                        <div class="row">
                            
                            <div class="col-md-3 mt-3">
                                <input type="text" class="form-control barcode-filter-input" data-barcode-input name="barcode" placeholder="{{ __('Scan Barcode / Product') }}" value="{{ $barcode ?? '' }}" />
                            </div>
                            
                            <div class="col-md-3 mt-3">
                                <select name="product_id" id="" class="select2">
                                    <option value="">{{ __('Select Product') }}</option>
                                    @foreach ($produc as $item)
                                        <option value="{{ $item->id }}"{{ ($product_id == $item->id)?'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mt-3">
                                <select name="category_id" id="" class="select2">
                                    <option value="">{{ __('Select Category') }}</option>
                                    @foreach ($categories as $item)
                                        <option value="{{ $item->id }}"{{ ($category_id == $item->id)?'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mt-3">
                                <button type="submit" class="btn add_list_btn">{{ __('Filter') }}</button>
                                <a href="{{ route('usedProduct.index') }}" class="btn add_list_btn_reset">{{ __('Reset') }}</a>
                                <a href="" class="btn add_list_btn float-right" onclick="window.print()">{{ __('Print') }}</a>
                            </div>
                        </div>
                    </form>
                    <div class="row mt-2" style="background: #f2f2f2;padding-top: 3px">
                        <div class="col-md-12 text-right">
                            @php 
                            $total_product = App\Models\UsedProduct::count();
                            @endphp
                            <h5><strong>{{ __('Total Used Product') }} : {{$total_product}} </strong> </h5>
                            
                        </div>
                    </div>
                    <div class="table-responsive mt-3">
                        <table id="datatable-buttons" class="table table-striped">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Barcode') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Brand') }}</th>
                                    <th>{{ __('Cost') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="header_style_right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $data)
                                    @php
                                        $count_inv = App\Models\InvoiceItem::where('product_id', $data->id)->count();
                                        $count_pur = App\Models\PurchaseItem::where('product_id', $data->id)->count();
                                        
                                        
                                    @endphp
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>{{ $data->name }}</td>
                                        <td>{{ $data->barcode }}</td>
                                        <td>{{ $data->category->name }}</td>
                                        <td>{{ ($data->brand_id==NULL)? __('No Brand') :$data->brand->name }}</td>
                                        <td>{{ $data->purchase_price }}</td>
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
                                                    @if (check_permission('usedProduct.edit'))
                                                        <a href="{{ route('usedProduct.edit', $data->id) }}"
                                                            class="dropdown-item text-primary">
                                                            <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                        </a>
                                                    @endif

                                                    {{-- delete --}}
                                                    @if (check_permission('usedProduct.destroy'))
                                                        {{-- @if ($count_inv<1 && $count_pur<1) --}}
                                                            <a href="#" data-toggle="modal"
                                                                data-target="#deleteModal-{{ $data->id }}"
                                                                class="dropdown-item text-danger {{ $data->id == 1 ? 'disabled' : '' }}">
                                                                <i class="feather icon-trash"></i> {{ __('Delete') }}
                                                            </a>
                                                        {{-- @endif --}}
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    {{-- delete modal --}}
                                    <form action="{{ route('usedProduct.destroy', $data->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-delete-modal title="{{ __('Used Product') }}" id="{{ $data->id }}" />
                                    </form>

                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center no_data_style text-danger">{{ __('No Data Available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $products->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="bar_code_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body" id="barcode-page">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success delete" onclick="print_barcode()"><i class="fa fa-print"></i>
                        {{ __('Print') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')

@endpush
