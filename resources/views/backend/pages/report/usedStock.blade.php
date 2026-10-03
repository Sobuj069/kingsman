@extends('backend.layouts.master')
@section('section-title', __('Stock'))
@section('page-title', __('Used Product Stock'))
@section('action-button')
    <a href="" class="btn add_list_btn btn-sm" onclick="window.print()">{{ __('Print') }}</a>
    {{-- <a href="{{route('report.stock.pdf')}}" class="btn btn-primary btn-sm">pdf</a> --}}
@endsection
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
                <div class="card-header ">
                    <h4 style="text-align: center; font-weight:bold; margin-top:5px;">{{ __('Used Product Stock Report') }}</h4>
                    @php
                        $brands = App\Models\Brand::get();
                        $categories = App\Models\Category::get();
                        $produc = App\Models\UsedProduct::get();
                    @endphp
                    <form action="{{ route('report.used-stock') }}" method="GET">
                        <div class="row h-hide">

                            <div class="col-md-3 mt-3">
                                <input type="text" class="form-control" id="search_keyword" name="search_keyword" value="{{ $keyword }}" placeholder="{{ __('Barcode or Product Name') }}">
                            </div>  
                            <div class="col-md-3 mt-3">
                                <select class="select2" name="product_id" id="product_id" data-provide="selectpicker"
                                data-live-search="true" data-size="10">
                                    <option value="">{{ __('Select Product') }}</option>
                                    @foreach ($produc as $product)
                                        <option value="{{ $product->id }}" 
                                            {{ $product_id == $product->id ? 'SELECTED' : '' }} >
                                            {{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-3 mt-3">
                                <select name="category_id" id="category_id" class="select2" data-provide="selectpicker"
                                data-live-search="true" data-size="10">
                                    <option value="">{{ __('Select Category') }}</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ $category_id == $category->id ? 'SELECTED' : '' }}
                                            >{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mt-3">
                                <select name="brand_id" id="brand_id" class="select2" data-provide="selectpicker"
                                data-live-search="true" data-size="10">
                                    <option value="">{{ __('Select Brand') }}</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}"
                                            {{ $brand_id == $brand->id ? 'SELECTED' : '' }}
                                            >{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3 h-hide">
                            <div class="col-md-12 col-12 d-flex justify-content-center">
                                <a href="{{ route('report.used-stock') }}" class="btn btn-md mx-1 text-light add_list_btn_reset" id="">{{ __('Reset') }}</a>
                                <button type="submit" class="btn btn-md add_list_btn mx-1">{{ __('Filter') }}</button>
                            </div>
                        </div>
                    </form>
                    
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped text-center">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th style="width: 25%">{{ __('Product') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Purchase') }}</th>
                                    <th>{{ __('Used') }}</th>
                                    <th class="header_style_right">{{ __('Available Stock') }}</th>
                                </tr>
                            </thead>
                            
                            <tbody>
                                @forelse($products as $data)
                                    @php
                                        $purchased_qty = used_purchased_qty($data);
                                        $invoiced_qty = used_qty($data);
                                        $stock_qty = used_product_stock_check($data);
                                    @endphp
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>{{ $data->name }} - {{ $data->barcode }}</td>
                                        <td>{{ $data->category->name }}</td>
                                        <td>{{ $purchased_qty }}</td>
                                        <td>{{ $invoiced_qty }}</td>
                                        <td class="table_data_style_right">{{ $stock_qty }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center text-danger">{{ __('No Data Available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            {{-- <tfooter>
                                <tr style="background: #3393f2; font-size: 18px; font-width: 700;">
                                    <td colspan="8"><strong>Total Available Stock Price: </strong></td>
                                    <td colspan="2"><strong>{{number_format($totalPrice,2)}} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</strong></td>
                                </tr>
                            </tfooter> --}}
                        </table>
                    </div>
                    <div class="pagination justify-content-center">
                        {{ $products->onEachSide(1)->links() }}
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
