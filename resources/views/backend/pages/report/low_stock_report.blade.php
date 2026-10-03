@extends('backend.layouts.master')
@section('section-title', __('Stock'))
@section('page-title', __('Product Stock'))
{{-- @section('action-button')
    <a href="" class="btn btn-primary " onclick="window.print()">Print</a>
@endsection --}}
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
            <div class="card card_style m-b-30">
                <div class="card-header ">
                    
                    <form action="{{ route('report.low.stock') }}" method="GET">
                        <div class="form-row align-items-end h-hide">
                            <div class="col-md-2 col-12 mb-3">
                                <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Search') }}</label>
                                <input type="text" class="form-control" style="height: 38px !important;" id="search_keyword" name="search_keyword" value="{{ $keyword }}" placeholder="{{ __('Barcode or Name') }}">
                            </div>  
                            <div class="col-md-2 col-12 mb-3">
                                <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Product') }}</label>
                                <select class="select2" name="product_id" id="product_id">
                                    <option value="">{{ __('Select Product') }}</option>
                                    @foreach ($produc as $product)
                                        <option value="{{ $product->id }}" 
                                            {{ $product_id == $product->id ? 'SELECTED' : '' }} >
                                            {{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-12 mb-3">
                                <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Category') }}</label>
                                <select name="category_id" id="category_id" class="select2">
                                    <option value="">{{ __('Select Category') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $category_id == $category->id ? 'SELECTED' : '' }}
                                            >{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 col-12 mb-3">
                                <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Brand') }}</label>
                                <select name="brand_id" id="brand_id" class="select2">
                                    <option value="">{{ __('Select Brand') }}</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}"
                                            {{ $brand_id == $brand->id ? 'SELECTED' : '' }}
                                            >{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 col-12 mb-3">
                                <div class="d-flex align-items-center" style="gap: 5px;">
                                    <button type="submit" class="btn add_list_btn flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;">
                                        <i class="fa fa-sliders mr-1"></i> {{ __('Filter') }}
                                    </button>
                                    <a href="{{ route('report.low.stock') }}" class="btn add_list_btn_reset flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;">
                                        <i class="feather icon-refresh-cw mr-1"></i> {{ __('Reset') }}
                                    </a>
                                    <a href="#" class="btn add_list_btn flex-grow-1" style="height: 38px !important; display: flex; align-items: center; justify-content: center;" onclick="window.print()">
                                        <i class="feather icon-printer mr-1"></i> {{ __('Print') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                </div>
                <div class="card-body">
                    <div class="table-responsive -mt-2.5">
                        <table id="alltableinfo" class="table table-striped text-center">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th style="width: 25%">{{ __('Product') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Brand') }}</th>
                                    <th>{{ __('Low Stock Alert Qty') }}</th>
                                    <th class="header_style_right">{{ __('Available Stock') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $sl = 1;
                                    $hasData = false;
                                @endphp
                                @foreach($products as $key => $data)
                                    @php
                                        $stock_qty = product_stock($data);
                                        $factor = ($data->unit && $data->unit->related_unit) ? ($data->unit->related_value ?: 1) : 1;
                                        $currentBaseStock = (float)product_fake_stock_val($data);
                                        
                                        if ($data->main_qty !== null && $data->main_qty > 0) {
                                            $alertThresholdInBase = (float)$data->main_qty * $factor;
                                            $isLowStock = ($currentBaseStock <= $alertThresholdInBase);
                                        } else {
                                            $isLowStock = ($currentBaseStock <= 0);
                                        }
                                    @endphp
                                    @if($isLowStock)
                                        @php $hasData = true; @endphp
                                        <tr>
                                            <td class="table_data_style_left">{{ $sl++ }}</td>
                                            <td>{{ $data->name }} - {{ $data->barcode }}</td>
                                            <td>{{ $data->category->name ?? '' }}</td>
                                            <td>{{ ($data->brand_id == NULL) ? __('No Brand') : ($data->brand->name ?? '') }}</td>
                                            <td>{{ $data->main_qty ?? 0 }} {{ $data->unit->name ?? '' }}</td>
                                            <td class="table_data_style_right">{{ $stock_qty }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                @if(!$hasData)
                                    <tr>
                                        <td colspan="100%" class="text-center text-danger no_data_style">{{ __('No Data Available') }}</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // $(doucment).ready(function(){
            $('#resetBtn').click(function(){
                $("#product_id option:first").prop("selected", true).change();
                $("#category_id option:first").prop("selected", true).change();
                $("#brand_id option:first").prop("selected", true).change();
                $("#search_keyword").val('');
            });
        // });
    </script>
@endsection
