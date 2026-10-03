@extends('backend.layouts.master')
@section('page-title', 'POS')

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
            border: 1px solid #DDD;
            cursor: pointer;
            padding-bottom: 30px;
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

        /* ======= DARK MODE OVERRIDES ======= */
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

        body.dark-theme .table tbody td input.form-control,
        body.dark-theme .table tbody td select.form-control,
        body.dark-theme .table tbody td textarea.form-control {
            color: #f8fafc !important;
            background-color: #1e293b !important;
            border-color: #475569 !important;
        }

        body.dark-theme .table tbody td input.form-control[readonly],
        body.dark-theme .table tbody td input.form-control[disabled],
        body.dark-theme .table tbody td textarea.form-control[readonly],
        body.dark-theme .table tbody td textarea.form-control[disabled] {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #38bdf8 !important;
        }

        body.dark-theme .table tbody td .input-group-text {
            background-color: #334155 !important;
            color: #cbd5e1 !important;
            border-color: #475569 !important;
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

        .table tbody tr td,
        .table tbody tr td:first-child,
        .table tbody tr td:last-child,
        .table_data_style_left,
        .table_data_style_right {
            border-radius: 0px !important;
        }
    </style>
@endpush

@section('invoice')
    <div class="invoice-contentbar">
        <div class="row">
            <!-- Start col -->
            <div class="col-md-12 col-12">
                <div class="card card_top">
                    <form action="{{ route('invoice.store') }}" id="payment_form" method="POST">
                        @csrf
                        <div class="cart-container">
                            <div class="cart-head">
                                <div class="row">
                                    <div class="input-group col-md-6 mb-3">
                                        <input type="date" class="form-control" id="date"
                                            value="{{ date('Y-m-d') }}" name="date" required>
                                    </div>
                                    <div class="col-md-6"></div>
                                </div>
                                <div class="row align-items-end ecommerce-sortby">
                                    {{-- modal add customer --}}
                                    <div class="col-8 pr-0">
                                        <select class="select2" name="customer_id" id="customer_id">
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-2 pl-2">
                                        <a href="#" data-toggle="modal" data-target="#addModal" onclick="$('#addModal').modal('show'); return false;" class="btn extra_btn shadow-sm">
                                            <i class="feather icon-plus"></i>
                                        </a>
                                    </div>
                                    <div class="col-2"></div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="input-group col-md-6 mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text barcod_style" id="basic-addon1"><i
                                                    class="fa fa-barcode"></i></span>
                                        </div>
                                        <input type="text" id="product_search" class="form-control"
                                            placeholder="Type & Barcode" aria-label="Type & Barcode"
                                            onkeydown="return event.keyCode !== 13" autocomplete="off">
                                    </div>
                                    <div class="col-md-6"></div>
                                    <div class="input-group col-md-6 mb-3">
                                        <input type="text" id="serial_number" class="form-control"
                                            placeholder="Scan serial number" aria-label="Type & Barcode"
                                            onkeydown="return event.keyCode !== 13" autocomplete="off">
                                    </div>
                                    <div class="col-md-6"></div>
                                </div>
                            </div>
                            <div class="cart-head">
                                <div class="table-responsive">
                                    <table class="table table-striped text-center">
                                        <thead>
                                            <tr class="header_bg">
                                                <th class="header_style_left">Product</th>
                                                <th>IMEI</th>
                                                <th>Details</th>
                                                @if (trim(strtolower(env('APP_WARRANTY'))) == 'yes')
                                                    <th>Warranty</th>
                                                @endif
                                                <th>Rate</th>
                                                <th>Total</th>
                                                <th class="header_style_right">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody">

                                        </tbody>
                                        <tfoot>
                                            <tr class="">

                                                <td colspan="{{ (env('APP_WARRANTY') == 'yes' ? 4 : 3) }}" class="text-right fw-bold">Grand Total</td>
                                                <td colspan="1">
                                                    <input type="number" step="any" name="estimated_amount"
                                                        value="0" class="form-control estimated_amount" readonly>
                                                </td>
                                                <td class="text-right"></td>
                                            </tr>
                                            {{-- Discount --}}
                                            <tr class="">
                                                <td colspan="{{ (env('APP_WARRANTY') == 'yes' ? 4 : 3) }}" class="text-right fw-bold">Discount</td>
                                                <td>
                                                    <input type="text" class="form-control discount_amount"
                                                        name="discount_amount" placeholder="0%">

                                                    <input type="hidden" class="form-control discount" name="discount"
                                                        placeholder="0%">
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
                                        <div class="text-center" id="payment_modal_btn"> Pay Now </div>
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
            {{-- <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center ecommerce-sortby">
                            <div class="col-md-12 col-lg-12 col-xl-12">
                                <label for="validationCustom04" class="form-label font-weight-bold">Category</label>
                                <select class="select2" name="category_id" id="getProductsByCat">
                                    <option selected value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="products">
                            <div class="row">
                                @forelse($products as $product)
                                    @php
                                        $stock_qty = product_stock($product);
                                    @endphp
                                    <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3">
                                        <div class="product-bar productcss m-b-30 product" data-value="{{ $product->id }}">
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
                                                        {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="text-center">
                                                            <small class="stock">Stock : {{ $stock_qty }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-md-12" style="padding-bottom: 30px;">
                                        <div class="alert alert-danger text-center" role="alert"> Products not
                                            available!</div>
                                    </div>
                                @endforelse
                            </div>
                            <div class="pagination justify-content-center">
                                {{ $products->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
    <!-- Payment Modal -->
    {{-- Add Modal --}}
    <form action="{{ route('customer.store') }}" method="POST">
        @csrf
        <x-add-modal title="Add Customer" sizeClass="modal-lg">
            @php $phoneOnlyAllowed = (env('APP_CUSTOMER_PHONE_ONLY') == 'yes' && env('APP_ONLINE') != 'yes'); @endphp
            @if ($phoneOnlyAllowed)
                <x-input label="Customer Name" type="text" name="name" placeholder="Enter Customer Name" md="6" />
            @else
                <x-input label="Customer Name *" type="text" name="name" placeholder="Enter Customer Name" required md="6" />
            @endif
            <x-input label="Email" type="email" name="email" placeholder="Enter Email" md="6" />
            <x-input label="Phone *" type="text" name="phone" placeholder="Enter Phone" required md="6" />
            <x-input label="Address" type="text" name="address" placeholder="Enter Address" md="6" />
            @if(!is_hide_customer_dates())
            <x-input label="Birth Date" type="date" name="birth_date" md="6" />
            @endif
            <x-input label="Opening Receivable" type="text" name="open_receivable" value="0" md="6" />
            <x-input label="Opening Payable" type="text" name="open_payable" value="0" md="6" />
        </x-add-modal>
    </form>
@endsection

@push('js')
    <script>
        // Select the input field when the page loads
        window.onload = function() {
            var inputField = document.getElementById('product_search');
            inputField.select();
        };
    </script>
    <script>
        // Page Load
        var empty = '';
        // $('body').addClass('toggle-menu');
        $('#product_search').blur();

        @if(!isset($quotation))
            localStorage.removeItem("pos-items");
        @endif
        var localData = localStorage.getItem('pos-items') ? JSON.parse(localStorage.getItem('pos-items')) : [];
        var env_warranty = "{{ env('APP_WARRANTY') }}";

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

        function updateTotalQty() {
            let totalQty = 0;
            $(".main_qty").each((i, element) => {
                if ($(element).is(":visible")) {
                    totalQty += parseInt(element.value == '' ? 0 : element.value);
                }
            });

            $(".imei").each((i, element) => {
                var imeis = element.value;
                var qty = 0;
                if (imeis != undefined) {
                    var imeis = imeis.split('\n');

                    imeis = imeis.filter(function(value) {
                        return value != '';
                    });
                    qty = imeis.length;
                }
                totalQty += parseInt(qty);
            });

            $("#total_qty").val(totalQty);
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



        // Search Product by barcode or name
        $("#product_search").autocomplete({
            source: function(req, res) {
                let url = "{{ route('product-search') }}";
                $.get(url, {
                    req: req.term
                }, (data) => {
                    res($.map(data, function(item) {
                        return {
                            id: item.id,
                            value: item.name + " " + item.barcode + (item.stock_qty <= 0 ? " (Out of Stock)" : ""),
                            price: item.selling_price
                        }
                    })); // end res

                });
            },
            select: function(event, ui) {

                $(this).val(ui.item.value);
                $("#search_product_id").val(ui.item.id);
                let url = "{{ route('search-product-id', 'my_id') }}".replace('my_id', ui.item.id);
                $.get(url, (data) => {

                    // check stock
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
                            title: "Please Increase the quantity.",
                            position: "topRight",
                        });
                    } else {
                        addProductToCard(data);
                    }

                });

                $(this).val('');

                return false;
            },
            response: function(event, ui) {
                if (ui.content.length == 1) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');

                }
            },
            minLength: 0
        });

        // Search by serial number
        $("#serial_number").autocomplete({
            source: function(req, res) {
                let url = "{{ route('serial-search') }}";
                $.get(url, {
                    req: req.term
                }, (data) => {
                    res($.map(data, function(item) {
                        return {
                            id: item.id,
                            pro_id: item.product_id,
                            value: item.serial,
                            // price: item.
                        }
                    })); // end res
                });
            },
            select: function(event, ui) {
                $(this).val(ui.item.value);
                $("#search_product_id").val(ui.item.id);
                let url = "{{ route('search-serial-id', 'my_id') }}".replace('my_id', ui.item.value);
                $.get(url, (data) => {
                    // console.log(data);
                    // check stock
                    if (data.imeis.status == 0) {
                        iziToast.error({
                            title: "{{ __('Not Available!') }}",
                            message: "{{ __('This product or serial is not available.') }}",
                            position: "topRight",
                        });
                        return false;
                    }
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
                            title: "Please Increase the quantity.",
                            position: "topRight",
                        });
                    } else {
                        // Include the scanned serial in the data for domPrepend
                        data.scanned_serial = ui.item.value;
                        addProductToCard(data);
                    }
                });

                $(this).val('');

                return false;
            },
            response: function(event, ui) {
                if (ui.content.length == 1) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');

                }
            },
            minLength: 0
        });

        // Manage Cart items
        $(document).on('click', '.product', function() {
            let productId = $(this).attr('data-value');
            let url = "{{ route('pos-product-id', 'my_id') }}".replace('my_id', productId);
            $.get(url, data => {
                // check stock
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


            if (data.product.has_serial == 1) {
                let serial_val = data.scanned_serial || '';
                quantity_data =
                    `<textarea name="new_imei[${data.product.id}]" class="form-control imei"  placeholder="IMEI">${serial_val}</textarea>`;
            } else {
                if(data.product.is_service == 0){
                    if (data.product.unit.related_unit == null) {
                        // alert("NO SUB UNIT");
                        quantity_data =
                            `<input type="text" class="has_sub_unit" hidden value="false">
                                    <label class="ml-2 mr-2" style="padding-top: 5px;">${data.product.unit.name}:</label>
                                    <input type="number" value="1" class="form-control col main_qty" name="main_qty[${data.product.id}]" data-value="${data.stock_qty}" data-related="${data.product.unit.related_value}" onkeydown="return event.keyCode !== 190" min="0">`;
                    } else {
                        // alert("SUB UNIT");
                        quantity_data =
                            `<input type="text" class="has_sub_unit" hidden value="true">
                                    <input type="text" class="conversion" hidden value="${data.product.unit.related_value}">
                                    <label class="mr-1 ml-1" style="padding-top: 5px;">${data.product.unit.name}:</label>
                                    <input type="number" value="1" class="form-control col main_qty mr-1" name="main_qty[${data.product.id}]" data-value="${data.stock_qty}" data-related="${data.product.unit.related_value}" onkeydown="return event.keyCode !== 190" min="0">
                                    <label class="mr-1" style="padding-top: 5px;">${data.product.unit.related_unit.name}:</label>
                                    <input type="number" value="0" class="form-control col sub_qty mr-1" name="sub_qty[${data.product.id}]"  onkeydown="return event.keyCode !== 190" min="0" max="${data.product.unit.related_value}">`;
                    }
                }else{
                    quantity_data =
                            `
                                    <label class="ml-2 mr-2" style="padding-top: 5px;">pcs:</label>
                                    <input type="number" value="1" class="form-control col main_qty" name="main_qty[${data.product.id}]"  min="0">`;
                }
            }
            let warranty_data = '';
            if (env_warranty == 'yes' && data.product.has_warranty == 1) {
                let w_val = data.product.warranty_value || '';
                let w_unit = data.product.warranty_unit || 'Month';
                warranty_data = `
                    <div class="d-flex align-items-center" style="min-width: 150px;">
                        <input type="number" name="warranty_value[${data.product.id}]" class="form-control form-control-sm mr-1" placeholder="Val" style="min-width: 50px; width: 60px;" value="${w_val}">
                        <select name="warranty_unit[${data.product.id}]" class="form-control form-control-sm" style="min-width: 90px;">
                            <option value="Day" ${w_unit == 'Day' ? 'selected' : ''}>{{ __('Day') }}</option>
                            <option value="Month" ${w_unit == 'Month' ? 'selected' : ''}>{{ __('Month') }}</option>
                            <option value="Year" ${w_unit == 'Year' ? 'selected' : ''}>{{ __('Year') }}</option>
                        </select>
                    </div>
                `;
            } else {
                warranty_data = `
                    <input type="hidden" name="warranty_value[${data.product.id}]" value="">
                    <input type="hidden" name="warranty_unit[${data.product.id}]" value="">
                `;
            }

            let dom = `
                <tr id="tbody_tr">
                    <td class="table_data_style_left">
                    ${data.product.name + " - Stock (" + data.stock_qty +")"}
                    <input type="hidden" class="name" value="${name.replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, '')}" name="name[${data.product.id}]" />
                    <input type="hidden" value="${data.product.id}" name="product_id[${data.product.id}]" />
                    </td>
                    <td>
                        <div class="form-row" style="min-width: 100px;">
                            ${quantity_data}
                        </div>
                    </td>
                    <td><textarea name="new_details[${data.product.id}]" class="form-control" placeholder="">${data.product.description}</textarea></td>
                    @if (env('APP_WARRANTY') == 'yes')
                    <td>
                        ${warranty_data}
                    </td>
                    @endif
                    <td>
                    <input type="number" style="min-width: 100px;" value="${data.product.selling_price}" class="form-control rate" name="rate[${data.product.id}]" />
                    @if (env('SHOW_COST_RATE_IN_POS') == 'yes')
                        <div class="text-danger small mt-1 font-weight-bold text-center cost-rate-label" style="font-size: 11px;">
                            Cost: ${parseFloat(data.product.purchase_price || 0).toFixed(2)}
                        </div>
                    @endif
                    </td>
                    <td>
                    <input type="number" style="min-width: 100px;" readonly name="sub_total[${data.product.id}]" class="form-control sub_total" value="${data.product.selling_price}"/>
                    </td>
                    <td class="table_data_style_right">
                    <a href="#" class="remove-btn item-index" data-value="${index}"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            `;
                $("#tbody").prepend(dom);
            }

            function handle_change(obj) {
                var main_val = parseInt(empty_field_check(obj.parents('tr').find('.main_qty').val()));
                var sub_val = parseInt(empty_field_check(obj.parents('tr').find('.sub_qty').val()));
                let related_by = parseInt(empty_field_check(obj.parents('tr').find('.main_qty').attr('data-related')));
                var has_sub_unit = obj.parents('tr').find('.has_sub_unit').val();
                let converted_sub = to_sub_unit(main_val, sub_val, related_by, has_sub_unit);
                // alert(has_sub_unit);
                let stock = obj.parents('tr').find('.main_qty').attr('data-value');

                // var existing_imeis = obj.parents('tr').find('.imei').attr('existing');
                // existing_imeis = existing_imeis.split(',').filter(function(value) {
                //     return value != '';
                // });
                // existing_imeis = existing_imeis.map((value)=>value.trim());
                // var imeis = $('.imei').val().split('\n');
                // var imeis = obj.parents('tr').find('.imei').val();
                // imeis = imeis.filter(function(value) {
                //     return value != '';
                // });

                // imeis = imeis.map((value)=>value.trim());

                // for (var i = 0; i < imeis.length; i++) {
                //     // console.log(myStringArray[i]);
                //     //Do something
                //     if (!existing_imeis.includes(imeis[i])) {
                //         alert(imeis[i] + " Wrong Imei");
                //         return;
                //     }
                // }




                var imeis = obj.parents('tr').find('.imei').val();
                var qty = 0;
                if (imeis != undefined) {
                    var imeis = imeis.split('\n');
                    imeis = imeis.filter(function(value) {
                        return value !== '';
                    });
                    var qty = imeis.length;

                }
                console.log(qty);

                if (qty > 0) {
                    var rate = obj.parents('tr').find('.rate').val();
                    var subTotal = qty * rate;

                    obj.parents('tr').find('.sub_total').val(subTotal);
                    estimatedAmount();
                } else {
                    if (stock < converted_sub) {
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

                        let subTotal = calculate_sub_total(converted.main_qty, converted.sub_qty, price, related_by,
                            has_sub_unit);

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


            }



            // main_qty
            $(document).on('keyup change', '.main_qty', function(e) {
                handle_change($(this));
            });
            $(document).on('keyup change', '.imei', function(e) {
                // let givenSerials = $(this).val();
                // let imeies = $('.imei').data-attr('existing'));

                // // Split the given serial numbers into an array and trim spaces/newline characters
                // let serialArray = givenSerials.split('\n').map(serial => serial.trim());

                // // Extract valid serial numbers from the imeies array
                // let validSerials = imeies.map(item => item.serial.trim());

                // // Find the serial numbers that are not valid
                // let wrongSerials = serialArray.filter(serial => serial && !validSerials.includes(serial));

                // // Check if there are any wrong serials
                // if (wrongSerials.length > 0) {
                //     // If there are wrong serials, show an alert with the wrong serial numbers
                //     alert('Wrong serial numbers: ' + wrongSerials.join(', '));
                // } else {
                //     // All serial numbers are valid
                //     console.log('All serial numbers are valid');
                //     // You can update the UI or proceed with further actions here
                // }

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


            function totalCalculate() {
                let discount = $(".discount_amount").val();
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
                if (parseFloat(pay_amount) > parseFloat(payable_amount)) {
                    iziToast.warning({
                        title: "Sorry Over Payment Not Allowed.",
                        position: "topRight",
                    });
                    return false;
                }
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
            });

            // Product Search
            $(document).on("change", "#getProductsByCat", function() {
                var cat_id = $(this).val();
                $.ajax({
                    url: "{{ route('posProducts') }}",
                    type: "GET",
                    data: {
                        cat_id: cat_id
                    },
                    success: function(data) {
                        $("#products").html(data);
                    },
                    error: function() {
                        alert('Error !');
                    }
                });
            });
    </script>
@endpush
