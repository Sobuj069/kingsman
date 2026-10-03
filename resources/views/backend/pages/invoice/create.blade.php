@extends('backend.layouts.master')
@section('page-title', __('POS'))

@push('css')
    <style>
        .invoice-contentbar {
            margin: 80px 5px 0 5px;
            padding: 20px;
            margin-bottom: 60px;
        }

        /* pos footer section start */

        .footerpos {
            display: grid;
            grid-template-columns: 1fr 50%;
        }

        .footerpos .footerpos_left {
            background-color: #000ce2 !important;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 12px 10px;
            border-top-left-radius: 35px;
            border-bottom-left-radius: 35px;
        }

        .footerpos .footerpos_left div {
            font-size: 25px;
            color: #fff;
        }

        .footerpos_right {
            background-color: #00a65a !important;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 12px 10px;
            border-top-right-radius: 35px;
            border-bottom-right-radius: 35px;
        }

        .footerpos_right div {
            font-size: 25px;
            color: #fff;
            cursor: pointer;
        }

        .productcss {
            /* border: 1px solid #DDD; */
            cursor: pointer;
            padding-bottom: 0px;
            height: 262px;
        }

        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        .table tbody tr td,
        .table tbody tr td:first-child,
        .table tbody tr td:last-child,
        .table_data_style_left,
        .table_data_style_right {
            border-radius: 0px !important;
        }

        .table tbody td input.form-control,
        .table tbody td select.form-control {
            font-size: 13px !important;
            height: 34px !important;
            color: #0f172a !important;
            font-weight: 600 !important;
            border: 1px solid #cbd5e1 !important;
            background-color: #ffffff !important;
            border-radius: 4px !important;
            text-align: center !important;
            padding: 4px 6px !important;
            transition: all 0.2s ease !important;
            width: 100% !important;
        }
        .table tbody td input.form-control:focus,
        .table tbody td select.form-control:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2) !important;
            background-color: #ffffff !important;
        }
        .table tbody td input.form-control[readonly],
        .table tbody td input.form-control[disabled] {
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #1e3a8a !important;
            font-weight: 700 !important;
            cursor: not-allowed !important;
        }
        .table tbody td .input-group {
            flex-wrap: nowrap !important;
        }
        .table tbody td .input-group-text {
            font-size: 11px !important;
            padding: 0 8px !important;
            background-color: #e2e8f0 !important;
            color: #334155 !important;
            border: 1px solid #cbd5e1 !important;
            border-left: none !important;
            font-weight: 600 !important;
            display: flex;
            align-items: center;
            height: 34px !important;
            border-radius: 0 4px 4px 0 !important;
        }

        /* Dark mode compatibility */
        body.dark-theme .table tbody td input.form-control,
        body.dark-theme .table tbody td select.form-control {
            color: #f8fafc !important;
            background-color: #1e293b !important;
            border-color: #475569 !important;
        }
        body.dark-theme .table tbody td input.form-control[readonly],
        body.dark-theme .table tbody td input.form-control[disabled] {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #38bdf8 !important;
        }
        body.dark-theme .table tbody td .input-group-text {
            background-color: #334155 !important;
            color: #cbd5e1 !important;
            border-color: #475569 !important;
        }

        /* ======= ADDITIONAL DARK MODE OVERRIDES ======= */
        body.dark-theme .rightbar,
        body.dark-theme #containerbar,
        body.dark-theme .invoice-contentbar {
            background-color: #0d1220 !important;
            color: #f1f5f9 !important;
        }

        body.dark-theme .invoice-contentbar .card.card_top, 
        body.dark-theme .invoice-contentbar .card {
            background-color: #121829 !important;
            border: 1px solid #1e293b !important;
        }

        body.dark-theme .cart-search-header,
        body.dark-theme .cart-head,
        body.dark-theme .cart-container {
            background: transparent !important;
        }

        body.dark-theme .form-control {
            background-color: #1a2035 !important;
            border-color: #2e3856 !important;
            color: #ffffff !important;
        }

        body.dark-theme .form-control:focus {
            background-color: #1a2035 !important;
            border-color: #3b82f6 !important;
            color: #ffffff !important;
        }

        body.dark-theme tbody tr {
            background: #2d3748 !important;
            color: #e2e8f0 !important;
        }

        body.dark-theme tbody tr td {
            color: #e2e8f0 !important;
            border-color: #4a5568 !important;
        }

        body.dark-theme .table-striped tbody tr:nth-of-type(odd) {
            background: #263044 !important;
        }

        body.dark-theme .bg-light-primary {
            background-color: #182235 !important;
            color: #f1f5f9 !important;
        }

        body.dark-theme .bg-light-primary td {
            color: #f1f5f9 !important;
        }

        body.dark-theme .bg-light-primary .form-control {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #38bdf8 !important;
        }

        body.dark-theme .text-dark {
            color: #f1f5f9 !important;
        }

        body.dark-theme .input-group-prepend .input-group-text {
            background-color: #1e293b !important;
            border-color: #2e3856 !important;
            color: #cbd5e1 !important;
        }

        body.dark-theme .input-group-prepend .barcod_style {
            background-color: #1e293b !important;
            border-color: #2e3856 !important;
            color: #cbd5e1 !important;
        }

        body.dark-theme .table tfoot tr {
            background-color: #121829 !important;
            color: #f1f5f9 !important;
        }

        body.dark-theme .table tfoot tr td {
            color: #f1f5f9 !important;
            border-color: #1e293b !important;
        }

        body.dark-theme .footerpos .footerpos_left {
            background-color: #1c2b5c !important;
        }

        body.dark-theme .footerpos_right {
            background-color: #0a693c !important;
        }

        /* ======= MOBILE RESPONSIVE ======= */
        @media (max-width: 767.98px) {
            .invoice-contentbar {
                margin: 70px 0 0 0 !important;
                padding: 10px !important;
                margin-bottom: 80px !important;
            }

            .invoice-contentbar .row > .col-md-6 {
                padding: 0 6px !important;
            }

            .invoice-contentbar .col-md-6:first-child {
                margin-bottom: 10px;
            }

            .cart-head .table-responsive {
                font-size: 12px !important;
            }

            .table thead th,
            .table tbody td {
                padding: 4px 3px !important;
                font-size: 11px !important;
            }

            .footerpos .footerpos_left div,
            .footerpos_right div {
                font-size: 16px !important;
            }

            .footerpos .footerpos_left,
            .footerpos_right {
                padding: 8px 6px !important;
            }

            .productcss {
                height: auto !important;
                min-height: 200px;
            }

            .productcss .product-head img {
                height: 90px !important;
            }

            .productcss .product-body {
                height: auto !important;
                min-height: 90px;
            }

            .productcss h6 {
                font-size: 11px !important;
                margin-bottom: 4px !important;
            }

            .productcss small {
                font-size: 10px !important;
            }

            /* Category select2 */
            #getProductsByCat {
                width: 100% !important;
            }

            /* product search input */
            #product_search {
                font-size: 13px !important;
            }
        }

        @media (max-width: 575.98px) {
            .invoice-contentbar {
                margin: 60px 0 0 0 !important;
                padding: 8px !important;
            }

            .productcss {
                height: auto !important;
            }
        }

        /* Product loading overlay */
        #products-loading {
            display: none;
            text-align: center;
            padding: 30px 0;
            color: #666;
        }

        #products-loading .spinner-border {
            width: 2rem;
            height: 2rem;
        }
    </style>
@endpush

@section('invoice')
    @if (auth()->user()->branch_id == 1)
        @if ($filterBranchId != null)
            <div class="invoice-contentbar">
                <div class="row">
                    <!-- Start col -->
                    <div class="col-md-6">
                        <div class="card card_top">
                            <form action="{{ route('invoice.store') }}" id="payment_form" method="POST" onsubmit="return checkExplicitSubmit(event);">
                                @csrf
                                <div class="cart-container">
                                    <div class="cart-head">
                                        @if (auth()->user()->branch_id == 1)
                                            <input type="hidden" name="branch_id" id="branch_id"
                                                value="{{ $filterBranchId }}">
                                        @else
                                            <input type="hidden" name="branch_id" id="branch_id"
                                                value="{{ auth()->user()->branch_id }}">
                                        @endif
                                        <div class="input-group mb-3">
                                            <input type="date" class="form-control" id="date"
                                                value="{{ date('Y-m-d') }}" name="date" max="{{ date('Y-m-d') }}" required>
                                        </div>
                                        <div class="row align-items-center ecommerce-sortby">
                                            <!-- Start col -->
                                            <div class="col-10 pr-0">
                                                <select class="select2" name="customer_id" id="customer_id">
                                                    @foreach ($customers as $customer)
                                                        <option value="{{ $customer->id }}" 
                                                            @if(env('APP_DISCOUNT_GROUP') == 'yes')
                                                            data-discount-type="{{ $customer->discountGroup?->type }}" 
                                                            data-discount-value="{{ $customer->discountGroup?->value }}"
                                                            @endif>
                                                            {{ $customer->name }} - {{ $customer->phone }} @if(env('APP_DISCOUNT_GROUP') == 'yes' && $customer->discountGroup) (Discount: {{ $customer->discountGroup->type == 'percentage' ? number_format($customer->discountGroup->value, 0).'%' : 'Tk '.number_format($customer->discountGroup->value, 0) }}) @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            {{-- modal add customer --}}
                                            <div class="col-2 pl-2">
                                                <a href="#" data-toggle="modal" data-target="#addModal" onclick="$('#addModal').modal('show'); return false;"
                                                    class="btn extra_btn shadow-sm">
                                                    <i class="feather icon-plus"></i>
                                                </a>
                                            </div>
                                            <!-- End col -->
                                        </div>
                                        <hr>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend ">
                                                <span class="input-group-text barcod_style" id="basic-addon1"><i
                                                        class="fa fa-barcode"></i></span>
                                                @if(env('APP_MOBILE_SCANNER') == 'yes')
                                                    <span class="input-group-text bg-primary text-white cursor-pointer btn-scan-camera" style="cursor: pointer;" title="{{ __('Scan with Camera') }}"><i class="fa fa-camera"></i></span>
                                                @endif
                                            </div>
                                            <input type="text" id="product_search" class="form-control"
                                                placeholder="{{ __('Type & Barcode') }}" aria-label="{{ __('Type & Barcode') }}"
                                                autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="cart-head">
                                        <div class="table-responsive">
                                            <table class="table table-striped text-center">
                                                <thead class="header_bg">
                                                    <tr>
                                                        <th class="header_style_left" width="17%">{{ __('Product') }}</th>
                                                        <th width="45%">{{ __('Quantity') }}</th>
                                                        <th width="19%">{{ __('Rate') }}</th>
                                                        <th width="19%">{{ __('Total') }}</th>
                                                        <th class="header_style_right">{{ __('Action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbody">

                                                </tbody>
                                                <tfoot>
                                                    <tr class="btn_list_style">
                                                        <td colspan="3" class="text-right fw-bold">{{ __('Grand Total') }}</td>
                                                        <td colspan="1">
                                                            <input type="number" step="any" name="estimated_amount"
                                                                value="0" class="form-control estimated_amount"
                                                                readonly>
                                                        </td>
                                                        <td class="text-right"></td>
                                                    </tr>
                                                    {{-- Discount --}}
                                                    <tr class="bg-light-primary">
                                                        <td colspan="3" class="text-right fw-bold">{{ __('Discount') }}</td>
                                                        <td>
                                                            <input type="text" class="form-control discount_amount"
                                                                name="discount_amount" placeholder="0%">

                                                            <input type="hidden" class="form-control discount"
                                                                name="discount" placeholder="0%">
                                                        </td>
                                                        <td class="text-right"></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                        <footer class="footerpos">
                                            <div class="footerpos_left">
                                                <div class="text-center" id="grand_total"></div>
                                            </div>
                                            <div class="footerpos_right d-flex align-items-center justify-content-end" style="gap: 10px;">
                                                @if(env('APP_ONLINE') == 'yes')
                                                    <button type="button" class="btn btn-warning font-weight-bold text-dark shadow-sm px-3 py-2" id="btn_submit_pre_order" style="border-radius: 8px;">
                                                        <i class="feather icon-clock"></i> {{ __('Pre-Order') }}
                                                    </button>
                                                @endif
                                                <div class="text-center" id="payment_modal_btn"> {{ __('Pay Now') }} </div>
                                            </div>
                                        </footer>
                                    </div>
                                </div>
                                @include('backend.pages.invoice.payment-modal')
                            </form>
                        </div>
                    </div>
                    <!-- End col -->
                    <!-- Start col -->
                    <div class="col-md-6">
                        <div class="card card_top">
                            <div class="card-body">
                                <!-- Start row -->
                                <div class="row align-items-center ecommerce-sortby">
                                    <!-- Start col -->
                                    <div class="col-md-12 col-lg-12 col-xl-12">
                                        <label for="validationCustom04" class="form-label font-weight-bold">{{ __('Category') }}</label>
                                        <select class="select2" name="category_id" id="getProductsByCat">
                                            <option selected value="">{{ __('Select Category') }}</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- End col -->
                                </div>
                                <!-- End row -->
                                <!-- Start row -->
                                <div id="products">
                                    <p class="text-dark">{{ __('Products') }}</p>
                                    <div class="row">
                                        {{-- @dd($products) --}}
                                        @forelse($products as $product)
                                            @php
                                                $stock_qty = product_stock($product);
                                                $pure_stock = (float) product_fake_stock_val($product);
                                                $is_out_of_stock = ($product->is_service == 0 && $pure_stock <= 0);
                                            @endphp
                                            <!-- Start col -->
                                            <div class="col-6 col-sm-4 col-md-6 col-lg-4 col-xl-3 mb-2">
                                                <div class="product-bar productcss m-b-30 product {{ $is_out_of_stock ? 'out-of-stock' : '' }}" style="position:relative;"
                                                    data-value="{{ $product->id }}">
                                                    @if ($is_out_of_stock)
                                                        <div class="stock-badge" style="position: absolute; top: 10px; right: 10px; z-index: 1;">
                                                            <span class="badge badge-danger">{{ __('Out of Stock') }}</span>
                                                        </div>
                                                    @endif
                                                        <div class="product-head">
                                                            <a href="#"><img
                                                                    src="{{ !empty($product->images) ? url('uploads/products/' . $product->images) : url('backend/images/no_images.png') }}"
                                                                    class="img-fluid"
                                                                    style="height: 125px; width: 100%;border-radius:25px;"
                                                                    alt="product"></a>
                                                        </div>
                                                        <div class="product-body py-3" style="height: 145px">
                                                            <div class="row">
                                                                <div class="col-12 text-center">
                                                                    <h6 class="mt-1 mb-3">{{ $product->name }}</h6>
                                                                </div>
                                                                <div class="col-12 text-center">
                                                                    <small
                                                                        class="font-weight-bold">{{ $product->selling_price }}</small>
                                                                    {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                                                </div>
                                                                <div class="col-12">
                                                                    <div class="text-center">
                                                                        <small class="stock">{{ __('Stock') }} :
                                                                            {{ $stock_qty }}</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <!-- End col -->
                                        @empty
                                            <div class="col-md-12" style="padding-bottom: 30px;">
                                                <div class="alert alert-danger text-center" role="alert"> {{ __('Products not available!') }}</div>
                                            </div>
                                        @endforelse
                                        <div class="pagination justify-content-center">
                                            {{ $products->links() }}
                                        </div>
                                    </div>
                                    <!-- Start row -->
                                    @if (env('APP_SERVICE') == 'yes')
                                        <p class="text-dark">Services</p>
                                        <div class="row">
                                            {{-- @dd($products) --}}
                                            @forelse($products as $product)
                                                {{-- @php
                                            $stock_qty = product_stock($product);

                                        @endphp --}}
                                                <!-- Start col -->
                                                @if ($product->is_service == 1)
                                                    <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3">
                                                        <div class="product-bar productcss m-b-30 product"
                                                            data-value="{{ $product->id }}">
                                                            <div class="product-head">
                                                                <a href="#"><img
                                                                        src="{{ !empty($product->images) ? url('uploads/products/' . $product->images) : url('backend/images/no_images.png') }}"
                                                                        class="img-fluid"
                                                                        style="height: 125px; width: 100%;"
                                                                        alt="product"></a>
                                                            </div>
                                                            <div class="product-body py-3" style="height: 145px">
                                                                <div class="row">
                                                                    <div class="col-12 text-center">
                                                                        <h6 class="mt-1 mb-3">{{ $product->name }}</h6>
                                                                    </div>
                                                                    <div class="col-12 text-center">
                                                                        <small
                                                                            class="font-weight-bold">{{ $product->selling_price }}</small>
                                                                        {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <!-- End col -->
                                            @empty
                                                <div class="col-md-12" style="padding-bottom: 30px;">
                                                    <div class="alert alert-danger text-center" role="alert"> Products
                                                        not
                                                        available!</div>
                                                </div>
                                            @endforelse
                                            <div class="pagination justify-content-center">
                                                {{ $products->links() }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- End col -->
                </div>
            </div>
        @else
            <div class="invoice-contentbar">
                <div class="row">
                    <div class="col-md-12 text-center mt-5">
                        <h2 class="text-danger">{{ __('Please Select Branch') }}</h2>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="invoice-contentbar">
            <div class="row">
                <!-- Start col -->
                <div class="col-md-6">
                    <div class="card card_top">
                        <form action="{{ route('invoice.store') }}" id="payment_form" method="POST" onsubmit="return checkExplicitSubmit(event);">
                            @csrf
                            <div class="cart-container">
                                <div class="cart-head">
                                    @if (auth()->user()->branch_id == 1)
                                        <input type="hidden" name="branch_id" id="branch_id"
                                            value="{{ $filterBranchId }}">
                                    @else
                                        <input type="hidden" name="branch_id" id="branch_id"
                                            value="{{ auth()->user()->branch_id }}">
                                    @endif
                                    <div class="input-group mb-3">
                                        <input type="date" class="form-control" id="date"
                                            value="{{ date('Y-m-d') }}" name="date" max="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="row align-items-center ecommerce-sortby">
                                        <!-- Start col -->
                                        <div class="col-md-11 col-10 " style="margin-right: -6px">
                                            <select class="select2" name="customer_id" id="customer_id">
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}"
                                                        @if(env('APP_DISCOUNT_GROUP') == 'yes')
                                                        data-discount-type="{{ $customer->discountGroup?->type }}" 
                                                        data-discount-value="{{ $customer->discountGroup?->value }}"
                                                        @endif>
                                                        {{ $customer->name }} - {{ $customer->phone }} @if(env('APP_DISCOUNT_GROUP') == 'yes' && $customer->discountGroup) (Discount: {{ $customer->discountGroup->type == 'percentage' ? number_format($customer->discountGroup->value, 0).'%' : 'Tk '.number_format($customer->discountGroup->value, 0) }}) @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- End col -->
                                        <!-- Start col -->
                                        <div class="col-md-1 col-2 m-0 p-0">
                                            <a href="#" data-toggle="modal" data-target="#addModal" onclick="$('#addModal').modal('show'); return false;"
                                                class="btn extra_btn">
                                                <i class="feather icon-plus"></i>
                                            </a>
                                        </div>
                                        <!-- End col -->
                                    </div>
                                    <hr>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend ">
                                            <span class="input-group-text barcod_style" id="basic-addon1"><i
                                                    class="fa fa-barcode"></i></span>
                                            @if(env('APP_MOBILE_SCANNER') == 'yes')
                                                <span class="input-group-text bg-primary text-white cursor-pointer btn-scan-camera" style="cursor: pointer;" title="{{ __('Scan with Camera') }}"><i class="fa fa-camera"></i></span>
                                            @endif
                                        </div>
                                        <input type="text" id="product_search" class="form-control"
                                            placeholder="{{ __('Type & Barcode') }}" aria-label="{{ __('Type & Barcode') }}"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="cart-head">
                                    <div class="table-responsive">
                                        <table class="table table-striped text-center">
                                            <thead class="header_bg">
                                                <tr>
                                                    <th class="header_style_left" width="17%">{{ __('Product') }}</th>
                                                    <th width="45%">{{ __('Quantity') }}</th>
                                                    <th width="19%">{{ __('Rate') }}</th>
                                                    <th width="19%">{{ __('Total') }}</th>
                                                    <th class="header_style_right">{{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody">

                                            </tbody>
                                            <tfoot>
                                                <tr class="btn_list_style">
                                                    <td colspan="3" class="text-right fw-bold">{{ __('Grand Total') }}</td>
                                                    <td colspan="1">
                                                        <input type="number" step="any" name="estimated_amount"
                                                            value="0" class="form-control estimated_amount" readonly>
                                                    </td>
                                                    <td class="text-right"></td>
                                                </tr>
                                                {{-- Discount --}}
                                                <tr class="bg-light-primary">
                                                    <td colspan="3" class="text-right fw-bold">{{ __('Discount') }}</td>
                                                    <td>
                                                        <input type="text" class="form-control discount_amount"
                                                            name="discount_amount" placeholder="0%">

                                                        <input type="hidden" class="form-control discount"
                                                            name="discount" placeholder="0%">
                                                    </td>
                                                    <td class="text-right"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <footer class="footerpos">
                                        <div class="footerpos_left">
                                            <div class="text-center" id="grand_total"></div>
                                        </div>
                                        <div class="footerpos_right">
                                            <div class="text-center" id="payment_modal_btn"> {{ __('Pay Now') }} </div>
                                        </div>
                                    </footer>
                                </div>
                            </div>
                            @include('backend.pages.invoice.payment-modal')
                        </form>
                    </div>
                </div>
                <!-- End col -->
                <!-- Start col -->
                <div class="col-md-6">
                    <div class="card card_top">
                        <div class="card-body">
                            <!-- Start row -->
                            <div class="row align-items-center ecommerce-sortby">
                                <!-- Start col -->
                                <div class="col-md-12 col-lg-12 col-xl-12">
                                    <label for="validationCustom04" class="form-label font-weight-bold">{{ __('Category') }}</label>
                                    <select class="select2" name="category_id" id="getProductsByCat">
                                        <option selected value="">{{ __('Select Category') }}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- End col -->
                            </div>
                            <!-- End row -->
                            <!-- Start row -->
                            <div id="products">
                                <p class="text-dark">{{ __('Products') }}</p>
                                <div class="row">
                                    {{-- @dd($products) --}}
                                    @forelse($products as $product)
                                        @php
                                            $stock_qty = product_stock($product);
                                            $pure_stock = (float) product_fake_stock_val($product);
                                            $is_out_of_stock = ($product->is_service == 0 && $pure_stock <= 0);
                                        @endphp
                                        <!-- Start col -->
                                        <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3">
                                            <div class="product-bar productcss m-b-30 product {{ $is_out_of_stock ? 'out-of-stock' : '' }}"
                                                data-value="{{ $product->id }}">
                                                @if ($is_out_of_stock)
                                                    <div class="stock-badge" style="position: absolute; top: 10px; right: 10px; z-index: 1;">
                                                        <span class="badge badge-danger">{{ __('Out of Stock') }}</span>
                                                    </div>
                                                @endif
                                                    <div class="product-head">
                                                        <a href="#"><img
                                                                src="{{ !empty($product->images) ? url('uploads/products/' . $product->images) : url('backend/images/no_images.png') }}"
                                                                class="img-fluid"
                                                                style="height: 125px; width: 100%;border-radius:25px;"
                                                                alt="product"></a>
                                                    </div>
                                                    <div class="product-body py-3" style="height: 145px">
                                                        <div class="row">
                                                            <div class="col-12 text-center">
                                                                <h6 class="mt-1 mb-3">{{ $product->name }}</h6>
                                                            </div>
                                                            <div class="col-12 text-center">
                                                                <small
                                                                    class="font-weight-bold">{{ $product->selling_price }}</small>
                                                                {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="text-center">
                                                                    <small class="stock">{{ __('Stock') }} :
                                                                        {{ $stock_qty }}</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <!-- End col -->
                                    @empty
                                        <div class="col-md-12" style="padding-bottom: 30px;">
                                            <div class="alert alert-danger text-center" role="alert"> {{ __('Products not available!') }}</div>
                                        </div>
                                    @endforelse
                                    <div class="pagination justify-content-center">
                                        {{ $products->links() }}
                                    </div>
                                </div>
                                <!-- Start row -->
                                @if (env('APP_SERVICE') == 'yes')
                                    <p class="text-dark">{{ __('Services') }}</p>
                                    <div class="row">
                                        {{-- @dd($products) --}}
                                        @forelse($products as $product)
                                            {{-- @php
                                        $stock_qty = product_stock($product);

                                    @endphp --}}
                                            <!-- Start col -->
                                            @if ($product->is_service == 1)
                                                <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3">
                                                    <div class="product-bar productcss m-b-30 product"
                                                        data-value="{{ $product->id }}">
                                                        <div class="product-head">
                                                            <a href="#"><img
                                                                    src="{{ !empty($product->images) ? url('uploads/products/' . $product->images) : url('backend/images/no_images.png') }}"
                                                                    class="img-fluid" style="height: 125px; width: 100%;"
                                                                    alt="product"></a>
                                                        </div>
                                                        <div class="product-body py-3" style="height: 145px">
                                                            <div class="row">
                                                                <div class="col-12 text-center">
                                                                    <h6 class="mt-1 mb-3">{{ $product->name }}</h6>
                                                                </div>
                                                                <div class="col-12 text-center">
                                                                    <small
                                                                        class="font-weight-bold">{{ $product->selling_price }}</small>
                                                                    {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <!-- End col -->
                                        @empty
                                            <div class="col-md-12" style="padding-bottom: 30px;">
                                                <div class="alert alert-danger text-center" role="alert"> {{ __('Products not available!') }}</div>
                                            </div>
                                        @endforelse
                                        <div class="pagination justify-content-center">
                                            {{ $products->links() }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
                <!-- End col -->
            </div>
        </div>
    @endif

    <form action="#" method="POST" id="ajaxForm">
        @csrf
        <x-add-modal title="{{ __('Add Customer') }}" sizeClass="modal-lg">
            @if (auth()->user()->branch_id == 1)
                <x-select label="{{ __('Branch *') }}" name="branch_id" md="12">
                    @foreach ($allBranch as $data)
                        <option value="{{ $data->id }}">{{ $data->name }}</option>
                    @endforeach
                </x-select>
            @endif
            @php $phoneOnlyAllowed = (env('APP_CUSTOMER_PHONE_ONLY') == 'yes' && env('APP_ONLINE') != 'yes'); @endphp
            @if ($phoneOnlyAllowed)
                <x-input label="{{ __('Customer Name') }}" type="text" id="name" name="name"
                    placeholder="{{ __('Enter Customer Name') }}" md="6" />
                <x-input label="{{ __('Email') }}" type="email" name="email" placeholder="{{ __('Enter Email') }}" md="6" />
                <x-input label="{{ __('Phone * (11 digits)') }}" type="text" name="phone" id="phone" placeholder="{{ __('Enter 11-digit Phone') }}" required minlength="11" md="6" />
                <x-input label="{{ __('Delivery Address') }}" type="text" name="address" placeholder="{{ __('Enter Full Address') }}" md="6" />
            @else
                <x-input label="{{ __('Customer Name *') }}" type="text" id="name" name="name"
                    placeholder="{{ __('Enter Customer Name') }}" required md="6" />
                <x-input label="{{ __('Email') }}" type="email" name="email" placeholder="{{ __('Enter Email') }}" md="6" />
                <x-input label="{{ __('Phone * (11 digits)') }}" type="text" name="phone" id="phone" placeholder="{{ __('Enter 11-digit Phone') }}" required minlength="11" md="6" />
                <x-input label="{{ __('Delivery Address * (For Courier)') }}" type="text" name="address" placeholder="{{ __('Enter Full Address (House, Road, Area, District)') }}" required minlength="5" md="6" />
            @if(!is_hide_customer_dates())
            <x-input label="{{ __('Birth Date') }}" type="date" name="birth_date" md="6" />
            @endif
            <x-input label="{{ __('Due Amount') }} " type="text" name="due_amount" value="0" md="6" />
            @if (env('APP_DISCOUNT_GROUP') == 'yes')
            <div class="form-group col-md-6 text-left">
                <label class="font-weight-bold">{{ __('Discount Group') }}</label>
                <select name="discount_group_id" class="form-control">
                    <option value="">{{ __('No Discount Group') }}</option>
                    @foreach ($discountGroups as $dg)
                        <option value="{{ $dg->id }}">{{ $dg->name }} ({{ $dg->type == 'percentage' ? number_format($dg->value, 0).'%' : 'Tk '.number_format($dg->value, 0) }})</option>
                    @endforeach
                </select>
            </div>
            @endif
        </x-add-modal>

    </form>

    <!-- Product Description Edit Modal -->
    <div class="modal fade" id="editProductDescModal" tabindex="-1" role="dialog" aria-labelledby="editProductDescModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductDescModalLabel">{{ __('Edit Product Description') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_desc_product_id">
                    <div class="form-group">
                        <label for="edit_desc_textarea" class="fw-bold">{{ __('Product Description') }}</label>
                        <textarea id="edit_desc_textarea" class="form-control" rows="4" placeholder="{{ __('Enter product description') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-primary" id="save_product_desc_btn">{{ __('Save Changes') }}</button>
                </div>
            </div>
    @if(env('APP_MOBILE_SCANNER') == 'yes')
    <!-- Camera Barcode Scanner Modal -->
    <div class="modal fade" id="cameraScannerModal" tabindex="-1" role="dialog" aria-labelledby="cameraScannerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cameraScannerModalLabel"><i class="fa fa-camera mr-2"></i>{{ __('Scan Barcode') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="btn-close-scanner">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div id="reader" style="width: 100%; min-height: 250px; background: #f8f9fa; border: 1px dashed #ccc; border-radius: 4px;"></div>
                    <div id="scanner-result" class="mt-2 text-success font-weight-bold"></div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@push('js')
    <script>
        //customer modal ajax code
        $(document).ready(function() {
            $('#ajaxForm').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                $.ajax({
                    url: "{{ route('customer.store') }}", // Define the route for submission
                    method: 'POST',
                    data: $(this).serialize(), // Serialize form data
                    success: function(response) {
                        // Reload the table data dynamically
                        $('#addModal').modal('hide');
                        // Clear form
                        $('#ajaxForm')[0].reset();

                        if (response.success && response.customer) {
                            var c = response.customer;
                            var dg = c.discount_group || {};
                            var dgText = dg.name ? ' (Discount: ' + (dg.type === 'percentage' ? parseInt(dg.value) + '%' : 'Tk ' + parseInt(dg.value)) + ')' : '';
                            var newOption = new Option(c.name + ' - ' + c.phone + dgText, c.id, true, true);
                            $(newOption).attr('data-discount-type', dg.type || '');
                            $(newOption).attr('data-discount-value', dg.value || '');
                            $('#customer_id').append(newOption).trigger('change');
                        } else {
                            $('#customer_id').load(location.href + ' #customer_id>*', function() {
                                $('#customer_id').trigger('change');
                            });
                        }

                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            var errMsgs = [];
                            $.each(errors, function(key, val) {
                                errMsgs.push(val.join(' '));
                            });
                            alert(errMsgs.join("\n"));
                        } else {
                            alert("{{ __('Failed to create customer. Please enter a valid customer name, 11-digit phone, and full delivery address.') }}");
                        }
                    }
                });
            });
        });
    </script>
    <script>
        // Select the input field when the page loads
        window.onload = function() {
            var inputField = document.getElementById('product_search');
            inputField.select();
        };
    </script>

    <script>
        $(document).ready(function() {
            var appLoyaltyEnabled = "{{ env('APP_LOYALTY') == 'yes' }}";

            function updateTotalPoint() {
                if (!appLoyaltyEnabled) {
                    $('#point_row').hide();
                    $('.pay_amount_div').hide();
                    return;
                }
                var customerId = $('#customer_id').val();

                if (customerId == "1") {
                    $('#point_row').hide();
                } else {
                    $('#point_row').show();
                }

                if (customerId) {
                    $.ajax({
                        url: '/customer/points/' + customerId,
                        type: 'GET',
                        success: function(response) {
                            var totalPoint = response.total_point ? response.total_point : 0;
                            $('.total_point').text(totalPoint);

                            if (totalPoint >= 100) {
                                $('.pay_amount_div').show();
                            } else {
                                $('.pay_amount_div').hide();
                            }
                        }
                    });
                } else {
                    $('.total_point').text('0.00');
                    $('.pay_amount_div').hide();
                }
            }

            $('#customer_id').change(updateTotalPoint);

            updateTotalPoint();
        });
    </script>

    <script>
        $(document).ready(function() {
            function fetchCustomerPreviousDue() {
                var customerId = $('#customer_id').val();
                if (!customerId || customerId == 1) {
                    $('.previous_due').text('0.00 {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}');
                    $('#previous_due').val(0);
                    totalCalculate();
                    return;
                }
                $.ajax({
                    url: "{{ route('customer.previous.due') }}",
                    type: "GET",
                    data: {
                        customer_id: customerId
                    },
                    success: function(response) {
                        var dueVal = parseFloat(response.total_due || 0).toFixed(2);
                        $('.previous_due').text(dueVal + ' {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}');
                        $('#previous_due').val(dueVal);
                        totalCalculate();
                    },
                    error: function(xhr) {
                        console.log(xhr);
                    }
                });
            }

            function applyCustomerDiscountGroup() {
                var selectedOption = $('#customer_id').find('option:selected');
                var discountType = selectedOption.attr('data-discount-type');
                var discountValue = selectedOption.attr('data-discount-value');

                if (discountType && discountValue !== undefined && discountValue !== null && discountValue !== '') {
                    if (discountType === 'percentage') {
                        $('.discount_amount').val(parseFloat(discountValue) + '%');
                    } else if (discountType === 'fixed') {
                        $('.discount_amount').val(parseFloat(discountValue));
                    }
                } else {
                    $('.discount_amount').val('');
                }
                totalCalculate();
            }

            $(document).on('change', '#customer_id', function() {
                fetchCustomerPreviousDue();
                applyCustomerDiscountGroup();
            });
            fetchCustomerPreviousDue();
            applyCustomerDiscountGroup();
        });
    </script>
    <script>
        // Page Load
        var empty = '';
        // $('body').addClass('toggle-menu');
        setTimeout(function() {
            var $search = $('#product_search:visible').first();
            if ($search.length) $search.focus();
        }, 100);

        @if(!isset($quotation))
            localStorage.removeItem("pos-items");
        @endif
        var localData = localStorage.getItem('pos-items') ? JSON.parse(localStorage.getItem('pos-items')) : [];

        function showList() {
            if (localData.length <= 0) {
                $("#tbody").html(empty);
            } else {
                localData.forEach((item, index) => {
                    domPrepend(item, index);
                });
            }
        }

        showList();
        estimatedAmount();

        var cartList = [];

        // Helper Functions
        function empty_field_check(placeholder) {
            if (typeof placeholder == NaN) {
                placeholder = 0;
            } else if (placeholder == null) {
                placeholder = 0;
            } else if (placeholder.trim() == "") {
                placeholder = 0;
            } else if (placeholder == 'null') {
                placeholder = 0;
            }
            return placeholder;
        }

        function to_sub_unit(main_val, sub_val, related_by, has_sub_unit) {
            if (has_sub_unit == 'true') {
                return (main_val * related_by) + sub_val;
            }
            return main_val;


        }

        function convert_to_main_and_sub(quantity, has_sub_unit, related_by) {
            var main_qty = 0;
            var main_qty_as_sub = 0;
            var sub_qty = 0;

            main_qty = parseInt(quantity);

            if (has_sub_unit == "true" && quantity != 0 && related_by != 0) {
                main_qty = parseInt(quantity / related_by);
                main_qty_as_sub = main_qty * related_by;
                sub_qty = quantity - main_qty_as_sub;
            }

            return {
                'main_qty': main_qty,
                'sub_qty': sub_qty
            };
        }

        function calculate_sub_total(main_qty, sub_qty, unit_price, related_by, has_sub_unit) {
            var sub_unit_price = 0;

            if (has_sub_unit == "true" && related_by != 0) {
                sub_unit_price = parseFloat(unit_price / related_by);
            }
            var main_price = main_qty * unit_price;
            var sub_price = sub_qty * sub_unit_price;

            return parseFloat(main_price + sub_price).toFixed(2);
        }

        // Manage Addition and Removal from LocalStorage
        function pExist(pid) {
            let ldata = localStorage.getItem('pos-items') ? JSON.parse(localStorage.getItem('pos-items')) : [];
            return ldata.some(function(el) {
                return el.product.id === pid
            });
        }

        function storedata(data) {
            if (localStorage.getItem('pos-items') != null) {
                cartList = JSON.parse(localStorage.getItem('pos-items'))
                cartList.push(data);
            } else {
                cartList.push(data);
            }
            localStorage.setItem('pos-items', JSON.stringify(cartList));
        }

        function addProductToCard(data) {
            storedata(data);
            var x = 0;
            domPrepend(data, x++);
            estimatedAmount();
        }

        var scanned_term = '';

        // Function to handle instant barcode scan or Enter key press on search input
        function handleDirectBarcodeScan(term, $input) {
            term = (term || '').trim();
            if (!term) return;

            if ($input && $input.data('ui-autocomplete')) {
                try { $input.autocomplete('close'); } catch(e) {}
            }
            if ($input && $input.length) {
                $input.val('');
            }

            let url = "{{ route('product-search') }}";
            $.get(url, { req: term }, function(data) {
                if (!data || data.length === 0) {
                    iziToast.error({
                        title: "{{ __('Product Not Found') }}",
                        message: "{{ __('No product found for barcode: ') }}" + term,
                        position: "topRight"
                    });
                    return;
                }

                let targetProduct = data.find(p => p.barcode && p.barcode.toString().trim().toLowerCase() === term.toLowerCase())
                                 || data.find(p => p.matched_variation_id)
                                 || data.find(p => p.name && p.name.toString().trim().toLowerCase() === term.toLowerCase())
                                 || data[0];

                let quickStock = targetProduct.stock_qty !== undefined ? (parseFloat(targetProduct.stock_qty) || 0) : null;
                if (targetProduct.is_service == 0 && quickStock !== null && quickStock <= 0) {
                    iziToast.error({
                        title: "{{ __('Out of Stock!') }}",
                        message: "{{ __('This product is out of stock. Please purchase more stock.') }} (" + (targetProduct.name || '') + ")",
                        position: "topRight"
                    });
                    return;
                }

                let detailsUrl = "{{ route('search-product-id', 'my_id') }}".replace('my_id', targetProduct.id);
                $.get(detailsUrl, function(fullData) {
                    if (fullData.product.is_service == 0 && fullData.stock_qty <= 0) {
                        iziToast.error({
                            title: "{{ __('Out of Stock!') }}",
                            message: "{{ __('This product is out of stock. Please purchase more stock.') }}",
                            position: "topRight",
                        });
                        return false;
                    }

                    if (pExist(fullData.product.id) == true) {
                        iziToast.warning({
                            title: "{{ __('Please Increase the quantity.') }}",
                            position: "topRight",
                        });
                    } else {
                        addProductToCard(fullData);
                    }

                    if ($input && $input.length) {
                        $input.focus();
                    } else {
                        $('#product_search:visible').first().focus();
                    }
                }).fail(function() {
                    iziToast.error({
                        title: "{{ __('Error') }}",
                        message: "{{ __('Failed to load product details') }}",
                        position: "topRight"
                    });
                });
            }).fail(function() {
                iziToast.error({
                    title: "{{ __('Error') }}",
                    message: "{{ __('Network error during barcode search') }}",
                    position: "topRight"
                });
            });
        }
        window.handleDirectBarcodeScan = handleDirectBarcodeScan;

        window.isExplicitSubmitAllowed = false;
        window.checkExplicitSubmit = function(e) {
            if (!window.isExplicitSubmitAllowed) {
                if (e) {
                    if (typeof e.preventDefault === 'function') e.preventDefault();
                    if (typeof e.stopPropagation === 'function') e.stopPropagation();
                }
                return false;
            }
            return true;
        };

        // Explicit trigger for checkout buttons
        $(document).on('click', '#checkout, .full_pay_btn, .full_due_btn, .btn-checkout', function() {
            window.isExplicitSubmitAllowed = true;
        });

        // Enter key handler on search box
        $(document).on('keydown keypress keyup', '#product_search', function(e) {
            if (e.keyCode === 13 || e.key === 'Enter' || e.which === 13) {
                e.preventDefault();
                e.stopPropagation();
                if (typeof e.stopImmediatePropagation === 'function') {
                    e.stopImmediatePropagation();
                }
                if (e.type === 'keydown') {
                    let term = $(this).val().trim();
                    if (term) {
                        handleDirectBarcodeScan(term, $(this));
                    }
                }
                return false;
            }
        });

        // Prevent unwanted form submission on Enter keypress in form inputs
        $(document).on('keydown keypress', '#payment_form input:not([type="submit"]):not([type="button"]), #payment_form select', function(e) {
            if (e.keyCode === 13 || e.key === 'Enter' || e.which === 13) {
                if ($(this).attr('id') === 'product_search') {
                    return; // Handled by #product_search handler
                }
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });

        // Global barcode scanner listener (when focus is outside search box)
        let barcodeBuffer = '';
        let barcodeLastKeyTime = 0;
        $(document).on('keydown keypress', function(e) {
            if ($('.modal.show, .modal.in, .select2-container--open').length > 0) return;
            let target = $(e.target);
            if (target.is('input:not(#product_search), textarea, select') && target.attr('id') !== 'product_search') {
                return;
            }
            if (target.attr('id') === 'product_search') {
                return;
            }

            let currentTime = new Date().getTime();
            if (e.key === 'Enter' || e.keyCode === 13 || e.which === 13) {
                if (barcodeBuffer.length >= 2 && (currentTime - barcodeLastKeyTime < 300)) {
                    e.preventDefault();
                    e.stopPropagation();
                    let scannedCode = barcodeBuffer.trim();
                    barcodeBuffer = '';
                    handleDirectBarcodeScan(scannedCode, $('#product_search:visible').first());
                    return false;
                }
                barcodeBuffer = '';
            } else if (e.type === 'keydown' && e.key && e.key.length === 1) {
                if (currentTime - barcodeLastKeyTime > 150) {
                    barcodeBuffer = '';
                }
                barcodeBuffer += e.key;
                barcodeLastKeyTime = currentTime;
            }
        });

        // Search Product Autocomplete
        $("#product_search").autocomplete({
            source: function(req, res) {
                let rawTerm = req.term || '';
                let term = rawTerm.trim();
                if (!term) {
                    res([]);
                    return;
                }
                scanned_term = term;
                let url = "{{ route('product-search') }}";
                $.get(url, {
                    req: term
                }, (data) => {
                    if (data && data.length > 0) {
                        res($.map(data, function(item) {
                            return {
                                id: item.id,
                                value: item.name + " " + item.barcode + (item.stock_qty <= 0 ? " (Out of Stock)" : ""),
                                price: item.selling_price,
                                barcode: item.barcode || ''
                            }
                        }));
                    } else {
                        res([]);
                    }
                }).fail(function() {
                    res([]);
                });
            },
            select: function(event, ui) {
                let $input = $(this);
                $input.val(ui.item.value);
                $("#search_product_id").val(ui.item.id);
                let url = "{{ route('search-product-id', 'my_id') }}".replace('my_id', ui.item.id);
                $.get(url, (data) => {
                    if (data.product.is_service == 0) {
                        if (data.stock_qty <= 0) {
                            iziToast.error({
                                title: "{{ __('Out of Stock!') }}",
                                message: "{{ __('This product is out of stock. Please purchase more stock.') }}",
                                position: "topRight",
                            });
                            return false;
                        }
                    }

                    if (pExist(data.product.id) == true) {
                        iziToast.warning({
                            title: "{{ __('Please Increase the quantity.') }}",
                            position: "topRight",
                        });
                    } else {
                        addProductToCard(data);
                    }

                    if ($input && $input.length) {
                        $input.focus();
                    }
                });

                $input.val('');
                return false;
            },
            response: function(event, ui) {
                if (!ui.content || ui.content.length === 0) return;

                if (ui.content.length === 1) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                    return;
                }

                if (scanned_term) {
                    let exactMatch = ui.content.find(item => item.barcode && item.barcode.toString().trim().toLowerCase() === scanned_term.trim().toLowerCase());
                    if (exactMatch) {
                        ui.item = exactMatch;
                        $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                        $(this).autocomplete('close');
                        return;
                    }
                }
            },
            minLength: 1,
            delay: 150
        });

        // Manage Cart items
        $(document).on('click', '.product', function() {
            let productId = $(this).attr('data-value');
            let url = "{{ route('pos-product-id', 'my_id') }}".replace('my_id', productId);
            $.get(url, data => {
                // check stock
                if (data.product.is_service == '0') {
                    if (data.stock_qty <= 0) {
                        iziToast.error({
                            title: "{{ __('Out of Stock!') }}",
                            message: "{{ __('This product is out of stock. Please purchase more stock.') }}",
                            position: "topRight",
                        });
                        return false;
                    }
                }

                if (pExist(data.product.id) == true) {
                    iziToast.warning({
                        title: "Please Increase the quantity.",
                        position: "topRight",
                    });
                } else {
                    addProductToCard(data);
                }
            }); // Load Data to cart

        });


        $(document).on('click', '.remove-btn', function() {
            let itemIndex = $(this).attr('data-value');
            localData.splice(itemIndex, 1);
            localStorage.removeItem('pos-items');
            localStorage.setItem('pos-items', JSON.stringify(localData))
            $(this).parents('tr').remove();
            estimatedAmount();
        });

        $("#clearList").on('click', function() {
            localStorage.removeItem('pos-items');
            $("#tbody").html(empty);
            estimatedAmount();
        });


        function domPrepend(data = null, index = null) {
            var name = data.product.name;
            var quantity_data = '';
            var name_data = '';

            if (data.product.is_service == 0) {
                if (data.product.unit.related_unit == null) {
                    // alert("NO SUB UNIT");
                    quantity_data =
                        `<input type="text" class="has_sub_unit" hidden value="false">
                            <label class="ml-2 mr-2" style="padding-top: 5px;">${data.product.unit.name}:</label>
                            <div class="input-group" style="width: 120px;">
                                <button class="btn btn-outline-secondary btn-decrease" type="button">-</button>
                                <input type="number" 
                                        class="form-control main_qty" 
                                        value="1" 
                                        min="0"
                                        name="main_qty[${data.product.id}]"
                                        data-value="${data.stock_qty}"
                                        data-related="0"
                                        onkeydown="return event.keyCode !== 190">
                                <button class="btn btn-outline-secondary btn-increase" type="button">+</button>
                            </div>
                            `;
                } else {
                    // alert("SUB UNIT");
                    quantity_data =
                        `<input type="text" class="has_sub_unit" hidden value="true">
                            <input type="text" class="conversion" hidden value="${data.product.unit.related_value}">
                            <label class="mr-0 ml-0" style="padding-top: 5px;">${data.product.unit.name}:</label>
                            <input type="number" value="1" class="form-control col main_qty mr-0" name="main_qty[${data.product.id}]" data-value="${data.stock_qty}" data-related="${data.product.unit.related_value}" onkeydown="return event.keyCode !== 190" min="0">
                            <label class="mr-0" style="padding-top: 5px;">${data.product.unit.related_unit.name}:</label>
                            <input type="number" value="0" class="form-control col sub_qty mr-0" name="sub_qty[${data.product.id}]"  onkeydown="return event.keyCode !== 190" min="0" max="${data.product.unit.related_value}">`;
                }
                name_data = `
                ${data.product.name + " - Stock (" + data.stock_qty +")" }
                    
                    <input type="hidden" class="name" value="${name.replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, '')}" name="name[${data.product.id}]" />
                    <input type="hidden" value="${data.product.id}" name="product_id[${data.product.id}]" />
                `;
            } else {
                quantity_data =
                    `
                            <label class="ml-2 mr-2" style="padding-top: 5px;">pcs:</label>
                            <input type="number" value="1" class="form-control col main_qty" name="main_qty[${data.product.id}]" onkeydown="return event.keyCode !== 190" min="0">`;
                name_data = `
                ${data.product.name}
                    
                    <input type="hidden" class="name" value="${name.replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, '')}" name="name[${data.product.id}]" />
                    <input type="hidden" value="${data.product.id}" name="product_id[${data.product.id}]" />
                `;
            }

            let dom = `
                <tr id="tbody_tr" data-product-id="${data.product.id}">
                    <td class="table_data_style_left">
                        ${name_data}
                        <div class="product-description-container mt-1 d-flex align-items-center">
                            <span class="product-desc-text text-muted small" style="max-width: 180px; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${data.product.description || ''}</span>
                            <a href="javascript:void(0)" class="edit-description-btn ml-2" data-id="${data.product.id}" data-name="${data.product.name.replace(/"/g, '&quot;')}" style="cursor: pointer;">
                                <i class="fa fa-edit text-primary" style="font-size: 13px;"></i>
                            </a>
                        </div>
                    </td>
                    <td>
                        <div class="form-row" style="min-width: 100px;">
                            ${quantity_data}
                        </div>
                    </td>
                    <td>
                    <input type="number" style="min-width: 70px;" value="${data.product.selling_price}" class="form-control rate" name="rate[${data.product.id}]" />
                    @if (env('SHOW_COST_RATE_IN_POS') == 'yes')
                        <div class="text-danger small mt-1 font-weight-bold text-center cost-rate-label" style="font-size: 11px;">
                            Cost: ${parseFloat(data.product.purchase_price || 0).toFixed(2)}
                        </div>
                    @endif
                    </td>
                    <td>
                    <input type="number" style="min-width: 70px;" readonly name="sub_total[${data.product.id}]" class="form-control sub_total" value="${data.product.selling_price}"/>
                    </td>
                    <td  class="table_data_style_right mr-0">
                    <a href="#" class="remove-btn item-index" data-value="${index}"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            `;
            $("#tbody").prepend(dom);
        }

        function handle_change(obj) {
            var main_val = parseFloat(empty_field_check(obj.parents('tr').find('.main_qty').val()));
            var sub_val = parseFloat(empty_field_check(obj.parents('tr').find('.sub_qty').val()));
            let related_by = parseInt(empty_field_check(obj.parents('tr').find('.main_qty').attr('data-related')));
            var has_sub_unit = obj.parents('tr').find('.has_sub_unit').val();
            let converted_sub = to_sub_unit(main_val, sub_val, related_by, has_sub_unit);
            // alert(has_sub_unit);
            let stock = parseFloat(obj.parents('tr').find('.main_qty').attr('data-value'));

            if (!isNaN(stock) && stock < converted_sub) {
                // put the max stock
                var converted;
                if (has_sub_unit == "true") {
                    converted = convert_to_main_and_sub(stock, has_sub_unit, related_by);
                    obj.parents('tr').find('.main_qty').val(converted.main_qty);
                    obj.parents('tr').find('.sub_qty').val(converted.sub_qty);
                } else {
                    converted = convert_to_main_and_sub(stock, has_sub_unit, related_by);
                    obj.parents('tr').find('.main_qty').val(converted.main_qty);
                }

                let price = obj.parents('tr').find('.rate').val();
                price = parseFloat(price);

                let subTotal = calculate_sub_total(converted.main_qty, converted.sub_qty, price, related_by, has_sub_unit);

                obj.parents('tr').find('.sub_total').val(subTotal);
                estimatedAmount();

                iziToast.warning({
                    title: "Not Enough Stock.",
                    position: "topRight",
                });
            } else {
                let price = obj.parents('tr').find('.rate').val();
                price = parseFloat(price);
                let subTotal = calculate_sub_total(main_val, sub_val, price, related_by, has_sub_unit);
                obj.parents('tr').find('.sub_total').val(subTotal);
                estimatedAmount();
            }
        }
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-increase')) {
                let input = e.target.previousElementSibling;
                input.value = parseInt(input.value) + 1;
                $(input).trigger('change'); // ✅ Trigger change event
            }

            if (e.target.classList.contains('btn-decrease')) {
                let input = e.target.nextElementSibling;
                if (parseInt(input.value) > 0) {
                    input.value = parseInt(input.value) - 1;
                    $(input).trigger('change'); // ✅ Trigger change event
                }
            }
        });

        // main_qty
        $(document).on('keyup change', '.main_qty', function(e) {
            handle_change($(this));
        });

        //sub_qty change
        $(document).on('keyup change', '.sub_qty', function(e) {
            handle_change($(this));
        });

        // rate change
        $(document).on('keyup change', '.rate', function(e) {
            handle_change($(this));
            return;
        });

        //estimatedAmount function
        function estimatedAmount() {
            var sum = 0;

            $(".sub_total").each(function() {
                var value = $(this).val();
                if (!isNaN(value) && value.length != 0) {
                    sum += parseFloat(value);
                }
            });
            $("input[name='estimated_amount']").val(sum);
            totalCalculate();
        }

        // Other Calculations - discount
        $(document).on(
            "keyup change",
            "input[name='discount_amount']",
            function() {
                totalCalculate();
            }
        );

        $(document).on(
            "keyup change",
            "input[name='pay_point']",
            function() {
                totalCalculate();
            }
        );

        function totalCalculate() {
            let discount = $(".discount_amount").val();
            let previousDue = parseFloat($("#previous_due").val()) || 0;
            let estimated_amount = parseFloat(
                $("input[name='estimated_amount']").val()
            );

            discount = empty_field_check(discount);


            let discountAmount = 0;
            if ((typeof discount === 'string' || discount instanceof String) && discount.includes("%")) {
                let removed_percent_discount = discount.replace('%', '');
                discount = parseFloat(removed_percent_discount);
                discountAmount = Math.round($(".estimated_amount").val() * (discount / 100));
            } else {
                discountAmount = parseFloat(discount);
            }
            let total_amount = estimated_amount - discountAmount;
            $("#grand_total").text(total_amount.toFixed(2));
            $(".sub_total").text(estimated_amount.toFixed(2));
            $(".discount_amount").text(discountAmount.toFixed(2));
            $(".discount").val(discountAmount.toFixed(2));
            $(".payable_amount").text(total_amount.toFixed(2));
            $("#payable_amount").val(total_amount.toFixed(2));
        }

        // ===================order modal===================
        //payment_modal_btn
        $("#payment_modal_btn").on("click", function() {
            //date
            var date = $("#date").val();
            if (date == '') {
                //Walking Customer Can't to Create a Due
                iziToast.warning({
                    title: "Please select a date.",
                    position: "topRight",
                });
                return false;
            }
            //customer_id
            var customer_id = $("#customer_id").val();
            if (customer_id == '') {
                //Walking Customer Can't to Create a Due
                iziToast.warning({
                    title: "Please select a customer.",
                    position: "topRight",
                });
                return false;
            }
            //order_modal_obj
            if ($.trim($('.name').val()) == '') {
                iziToast.warning({
                    title: "Please select at least one product",
                    position: "topRight",
                });
                return false;
            }
            // count of tr in cart_list table
            var count = $("#tbody").find("tr#tbody_tr").length;
            $(".total_item").text(count);
            //get customer name from id="customer_id"
            var customer_name = $("#customer_id").find("option:selected").text();
            $("#payment_modal").find("#customer_name").text(customer_name);
            var customer_id = $("#customer_id").val();
            $("#payment_modal").find("input[name=customer_id]").val(customer_id);
            //show payment_modal
            $("#payment_modal").modal("show");
        });

        //.full_pay_btn
        $(".full_pay_btn").on("click", function() {
            var payable_amount = $("#grand_total").text();
            let due = parseFloat($("#previous_due").val()) || 0;
            payable_amount = parseFloat(payable_amount);
            payable_amount = payable_amount.toFixed(2);

            $(".pay_amount").val(payable_amount);
            $("#paid_amount").val(payable_amount);
            $(".paid_amount").text(payable_amount);
            $("#due_amount").val('0.00');
            $(".due_amount").text('0.00');
        });

        //.full_due_btn
        $(".full_due_btn").on("click", function() {
            var payable_amount = $("#grand_total").text();
            let due = parseFloat($("#previous_due").val()) || 0;
            payable_amount = parseFloat(payable_amount);
            payable_amount = payable_amount.toFixed(2);

            $(".pay_amount").val('0.00');
            $("#due_amount").val(payable_amount);
            $(".due_amount").text(payable_amount);
            $("#paid_amount").val('0.00');
            $(".paid_amount").text('0.00');
        });

        //name="pay_amount"
        $(".pay_amount").on("keyup change", function() {
            var pay_amount = $(this).val();
            if (pay_amount == "") {
                pay_amount = '0.00';
            }
            pay_amount = parseFloat(pay_amount);
            pay_amount = pay_amount.toFixed(2);
            var payable_amount = $("#payment_modal")
                .find("input[name=payable_amount]")
                .val();

            var due_amount = payable_amount - pay_amount;
            due_amount = parseFloat(due_amount);
            due_amount = due_amount.toFixed(2);
            var balance = '0.00';
            if (due_amount < 0) {
                balance = Math.abs(due_amount);
                due_amount = '0.00';
            }

            $("#payment_modal").find(".due_amount").text(due_amount);
            $("#payment_modal").find("input[name=due_amount]").val(due_amount);

            $("#payment_modal").find(".balance").text(balance);
            $("#payment_modal").find("input[name=balance]").val(balance);

            $("#payment_modal").find(".paid_amount").text(pay_amount);
            $("#payment_modal").find("input[name=paid_amount]").val(pay_amount);
        });

        //id="checkout"
        $("#checkout").on("click", function() {
            var customer_id = $("#customer_id").val();
            var payable_amount = $("#payable_amount").val();
            var pay_amount = $(".pay_amount").val();
            var paid_amount = $("#paid_amount").val();
            var due_amount = $("#due_amount").val();
            // if (parseFloat(pay_amount) > parseFloat(payable_amount)) {
            //     iziToast.warning({
            //         title: "Sorry Over Payment Not Allowed.",
            //         position: "topRight",
            //     });
            //     return false;
            // }
            if (parseFloat(pay_amount) < 0) {
                iziToast.warning({
                    title: "Sorry Below Payment Not Allowed.",
                    position: "topRight",
                });
                return false;
            }
            if (paid_amount == 0.00 && due_amount == 0.00) {
                iziToast.warning({
                    title: "Please Enter Pay Amount.",
                    position: "topRight",
                });
                return false;
            }
            if (customer_id == 1 && due_amount != 0.00) {
                iziToast.warning({
                    title: "Walking Customer Can't to Create a Due",
                    position: "topRight",
                });
                return false;
            }
            localStorage.clear();
            $("#payment_form").submit();
            $(this).prop('disabled', true).text('Processing...');
        });

        // Category Product Filter (Select2 compatible)
        $(document).ready(function() {
            // Select2 triggers its own 'select2:select' and 'select2:unselect' events
            // But also triggers native 'change' - use both for compatibility
            $('#getProductsByCat').on('change', function() {
                var cat_id = $(this).val();

                // Show loading
                $('#products').html(
                    '<div id="products-loading" style="display:block; text-align:center; padding:40px 0;">' +
                    '<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>' +
                    '<p class="mt-2 text-muted">{{ __("Loading products...") }}</p>' +
                    '</div>'
                );

                $.ajax({
                    url: "{{ route('posProducts') }}",
                    type: "GET",
                    data: {
                        cat_id: cat_id
                    },
                    success: function(data) {
                        $('#products').html(data);
                    },
                    error: function() {
                        $('#products').html(
                            '<div class="col-12"><div class="alert alert-danger">{{ __("Something went wrong!") }}</div></div>'
                        );
                    }
                });
            });
        });

        // Initialize Summernote on description textarea
        $(document).ready(function() {
            if ($.fn.summernote) {
                $('#edit_desc_textarea').summernote({
                    height: 180,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'clear']],
                        ['fontname', ['fontname']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['insert', ['link', 'picture', 'table']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ]
                });
            }
        });

        // Product description editing in POS
        $(document).on('click', '.edit-description-btn', function(e) {
            e.preventDefault();
            let productId = $(this).data('id');
            let productName = $(this).data('name');
            let currentRow = $(this).closest('tr');
            let currentDesc = currentRow.find('.product-desc-text').html() || '';

            $('#edit_desc_product_id').val(productId);
            if ($.fn.summernote && $('#edit_desc_textarea').data('summernote')) {
                $('#edit_desc_textarea').summernote('code', currentDesc);
            } else {
                $('#edit_desc_textarea').val(currentDesc);
            }
            $('#editProductDescModalLabel').text('{{ __("Edit Description of") }} ' + productName);
            $('#editProductDescModal').modal('show');
        });

        $('#save_product_desc_btn').on('click', function() {
            let productId = $('#edit_desc_product_id').val();
            let newDesc = ($.fn.summernote && $('#edit_desc_textarea').data('summernote')) ? $('#edit_desc_textarea').summernote('code') : $('#edit_desc_textarea').val();
            let url = "{{ route('product.update-description', 'my_id') }}".replace('my_id', productId);

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    description: newDesc
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Update DOM row(s)
                        $('tr[data-product-id="' + productId + '"]').each(function() {
                            $(this).find('.product-desc-text').html(newDesc);
                        });

                        // Update localStorage pos-items
                        let posItems = localStorage.getItem('pos-items') ? JSON.parse(localStorage.getItem('pos-items')) : [];
                        posItems.forEach(function(item) {
                            if (item.product && item.product.id == productId) {
                                item.product.description = newDesc;
                            }
                        });
                        localStorage.setItem('pos-items', JSON.stringify(posItems));
                        
                        // Update localData array in memory
                        if (typeof localData !== 'undefined') {
                            localData.forEach(function(item) {
                                if (item.product && item.product.id == productId) {
                                    item.product.description = newDesc;
                                }
                            });
                        }

                        if (typeof window.toastMagic !== 'undefined') {
                            window.toastMagic.success(response.message);
                        } else if (typeof iziToast !== 'undefined') {
                            iziToast.success({
                                title: "{{ __('Success') }}",
                                message: response.message,
                                position: "topRight"
                            });
                        }

                        $('#editProductDescModal').modal('hide');
                    }
                },
                error: function(xhr) {
                    let errMsg = "{{ __('Something went wrong!') }}";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    if (typeof window.toastMagic !== 'undefined') {
                        window.toastMagic.error(errMsg);
                    } else if (typeof iziToast !== 'undefined') {
                        iziToast.error({
                            title: "{{ __('Error') }}",
                            message: errMsg,
                            position: "topRight"
                        });
                    }
                }
            });
        });
    </script>

    @if(env('APP_MOBILE_SCANNER') == 'yes')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        $(document).ready(function() {
            let html5QrcodeScanner = null;

            $(document).on('click', '.btn-scan-camera', function() {
                $('#cameraScannerModal').modal('show');
                
                setTimeout(() => {
                    if (!html5QrcodeScanner) {
                        html5QrcodeScanner = new Html5Qrcode("reader");
                    }
                    
                    const config = { 
                        fps: 10, 
                        qrbox: function(width, height) {
                            let minSize = Math.min(width, height);
                            let size = Math.floor(minSize * 0.7);
                            return { width: size, height: Math.floor(size * 0.6) };
                        },
                        aspectRatio: 1.0
                    };
                    
                    html5QrcodeScanner.start(
                        { facingMode: "environment" },
                        config,
                        onScanSuccess,
                        onScanFailure
                    ).catch(err => {
                        console.error("Error starting camera scanner: ", err);
                        iziToast.error({
                            title: "{{ __('Camera Error') }}",
                            message: "{{ __('Could not access camera. Please allow camera permissions.') }}",
                            position: "topRight"
                        });
                        $('#cameraScannerModal').modal('hide');
                    });
                }, 400);
            });

            function onScanSuccess(decodedText, decodedResult) {
                iziToast.success({
                    title: "{{ __('Scanned successfully') }}",
                    message: decodedText,
                    position: "topRight",
                    timeout: 1000
                });
                
                stopScanner();
                $('#cameraScannerModal').modal('hide');
                
                let $searchBox = $("#product_search:visible").first();
                handleDirectBarcodeScan(decodedText, $searchBox);
            }

            function onScanFailure(error) {
                // Ignore silent failures for QR detection
            }

            function stopScanner() {
                if (html5QrcodeScanner && html5QrcodeScanner.isScanning) {
                    html5QrcodeScanner.stop().then(() => {
                        console.log("Camera scanner stopped.");
                    }).catch(err => {
                        console.error("Error stopping scanner: ", err);
                    });
                }
            }

            $('#cameraScannerModal').on('hidden.bs.modal', function () {
                stopScanner();
            });
    <script>
        $(document).ready(function() {
            // Handle Sale Type dropdown change
            $(document).on('change', '#sale_type', function() {
                var val = $(this).val();
                if (val === 'Pre-Order') {
                    $('#pre_order_notice').slideDown();
                    $('#agent_details').slideUp();
                } else {
                    $('#pre_order_notice').slideUp();
                }
            });

            // Handle payment form submission when Pre-Order is selected
            $(document).on('submit', '#payment_form', function(e) {
                var saleType = $('#sale_type').val();
                if (saleType === 'Pre-Order') {
                    e.preventDefault();

                    var rowCount = $('#tbody tr').length;
                    if (rowCount < 1) {
                        alert("{{ __('Please select at least one product for Pre-Order.') }}");
                        return false;
                    }

                    var customerId = $('#customer_id').val();
                    if (!customerId) {
                        alert("{{ __('Please select a customer for Pre-Order.') }}");
                        return false;
                    }

                    if (!confirm("{{ __('Place this Pre-Order? (No stock will be deducted until converted to sale)') }}")) {
                        return false;
                    }

                    var formData = $(this).serialize();

                    $.ajax({
                        url: "{{ route('pre-orders.store') }}",
                        method: "POST",
                        data: formData,
                        success: function(res) {
                            if (res.status == 'success') {
                                alert(res.message);
                                $('#payment_modal').modal('hide');
                                window.location.href = "{{ route('pre-orders.index') }}";
                            } else {
                                alert(res.message || "{{ __('Failed to place Pre-Order.') }}");
                            }
                        },
                        error: function(xhr) {
                            var msg = "{{ __('Failed to create Pre-Order.') }}";
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            alert(msg);
                        }
                    });
                }
            });

            // Direct Pre-Order button handler
            $(document).on('click', '#btn_submit_pre_order, .btn_submit_pre_order', function(e) {
                e.preventDefault();
                $('#sale_type').val('Pre-Order').trigger('change');
                $('#payment_modal').modal('show');
            });
        });
    </script>
@endpush
