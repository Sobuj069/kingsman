@extends('backend.layouts.master')
@section('page-title', __('Adjust Stock'))
@push('css')
    <style>
        .invoice-contentbar {
            margin: 0 !important;
            padding: 20px;
            margin-bottom: 60px;
        }

        .cart-container {
            padding-top: 20px !important;
        }

        .table-responsive {
            margin-bottom: 4px !important;
        }

        .footerpos {
            display: flex;
            justify-content: flex-end;
            width: 100%;
        }

        .footerpos .footerpos_left {
            background-color: transparent !important;
            padding: 12px 10px;
            display: flex;
            align-items: center;
        }

        .footerpos .footerpos_left div {
            font-size: 20px;
            color: #333;
            font-weight: 700;
        }

        .checkout-btn {
            background-color: #10b981 !important;
            color: white !important;
            padding: 10px 35px;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .checkout-btn:hover {
            background-color: #059669 !important;
            transform: scale(1.03);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            color: white !important;
        }

        .productcss {
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

        .table-responsive {
            overflow-x: auto;
        }
        .ecommerce-sortbyd {
            margin-top: 0px !important;
        }
        .ui-autocomplete {
            z-index: 9999999 !important;
            background-color: #ffffff !important;
            color: #000000 !important;
            border: 1px solid #cccccc !important;
            max-height: 300px;
            overflow-y: auto;
            overflow-x: hidden;
        }
        body.dark-theme .ui-autocomplete {
            background-color: #1e293b !important;
            color: #f8fafc !important;
            border-color: #475569 !important;
        }
        .ui-menu-item {
            text-align: left !important;
        }
    </style>
@endpush
@section('invoice')
    @if ($userBranchId == 1)
        @if ($filterBranchId != null)
            <div class="invoice-contentbar">
                <div class="row">
                    <!-- Start col -->
                    <div class="col-md-12">
                        <div class="card card_top">
                            <form action="{{ route('stock-adjust.store') }}" id="payment_form" method="POST">
                                @csrf
                                <div class="card-body">
                                    <div class="cart-container">
                                        <div class="cart-head">
                                            <div class="row align-items-center ecommerce-sortbyd">
                                                <!-- Start col -->
                                                <div class="col-md-6" style="margin-right: -6px">
                                                    <label for="unit_id" class="form-label fw-bold">{{ __('Branch *') }}</label>
                                                    @if ($userBranchId == 1)
                                                        <select class="select2" name="branch_id" id="from_branch_id">
                                                            @foreach ($allBranch as $branch)
                                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <input readonly class="form-control" type="text"
                                                            value="{{ auth()->user()->branch->name }}">
                                                        <input type="hidden" name="branch_id" class="form-control"
                                                            value="{{ auth()->user()->branch_id }}">
                                                    @endif
                                                </div>
                                                <div class="col-md-6" style="margin-right: -6px">
                                                    <label for="unit_id" class="form-label fw-bold">{{ __('Stock Status *') }}</label>
                                                    <select class="select2 to_branch_id" name="stock_to" id="to_branch_id">
                                                        <option value="stock_in">{{ __('Stock In') }}</option>
                                                        <option value="stock_out">{{ __('Stock Out') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row align-items-center ecommerce-sortby">
                                                <!-- Start col -->
                                                <div class="col-md-6" style="margin-right: -6px">
                                                    <label for="unit_id" class="form-label fw-bold">{{ __('Date *') }}</label>
                                                    <input type="date" class="form-control" id="date"
                                                        value="{{ date('Y-m-d') }}" name="date" required>
                                                </div>

                                                <div class="col-md-6" style="margin-right: -6px">
                                                    <label for="unit_id" class="form-label fw-bold">{{ __('Note') }}</label>
                                                    <textarea class="form-control" placeholder="{{ __('Enter Your Note') }} " name="note"></textarea>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text barcod_style" id="basic-addon1"><i
                                                            class="fa fa-barcode"></i></span>
                                                </div>
                                                <input type="text" id="product_search" class="form-control"
                                                    placeholder="{{ __('Type & Barcode') }}" aria-label="{{ __('Type & Barcode') }}"
                                                    onkeydown="return event.keyCode !== 13" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="cart-head">
                                            <div class="table-responsive">
                                                <table class="table table-striped text-center">
                                                    <thead class="header_bg">
                                                        <tr>
                                                            <th class="header_style_left" width="17%">{{ __('Product') }}</th>
                                                            @if (env('APP_SC') == 'yes')
                                                                <th width="20%">{{ __('Variation') }}</th>
                                                            @else
                                                                <th></th>
                                                            @endif
                                                            <th width="15%">{{ __('Rate') }}</th>
                                                            <th width="29%">{{ __('Adjust Quantity') }}</th>
                                                            <th width="29%">{{ __('Sub Total') }}</th>
                                                            <th class="header_style_right">{{ __('Action') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbody">

                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="bg-light-primary">
                                                            <td colspan="4" class="text-right fw-bold" style="font-size: 18px; vertical-align: middle;">{{ __('Grand Total') }}:</td>
                                                            <td colspan="2" class="text-left">
                                                                <div id="grand_total" style="font-size: 20px; font-weight: 800; color: #10b981;">0.00</div>
                                                                <input type="hidden" name="estimated_amount" value="0" class="estimated_amount">
                                                                <input type="hidden" name="payable_amount" id="payable_amount" value="">
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                            <div class="row ">
                                                <div class="footerpos mt-1 w-full px-1">
                                                    <button type="submit" class="checkout-btn" id="payment_modal_btn">
                                                        {{ __('Adjust Now') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
                <div class="col-md-12">
                    <div class="card card_top">
                        <form action="{{ route('stock-adjust.store') }}" id="payment_form" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="cart-container">
                                <div class="cart-head">
                                    <div class="row align-items-center ecommerce-sortbyd">
                                        <!-- Start col -->
                                        <div class="col-md-6" style="margin-right: -6px">
                                            <label for="unit_id" class="form-label fw-bold">{{ __('Branch *') }}</label>
                                            @if ($userBranchId == 1)
                                                <select class="select2" name="branch_id" id="from_branch_id">
                                                    @foreach ($allBranch as $branch)
                                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input readonly class="form-control" type="text"
                                                    value="{{ auth()->user()->branch->name }}">
                                                <input type="hidden" name="branch_id" class="form-control"
                                                    value="{{ auth()->user()->branch_id }}">
                                            @endif
                                        </div>
                                        <div class="col-md-6" style="margin-right: -6px">
                                            <label for="unit_id" class="form-label fw-bold">{{ __('Stock Status *') }}</label>
                                            <select class="select2 to_branch_id" name="stock_to" id="to_branch_id">
                                                <option value="stock_in">{{ __('Stock In') }}</option>
                                                <option value="stock_out">{{ __('Stock Out') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row align-items-center ecommerce-sortby">
                                        <!-- Start col -->
                                        <div class="col-md-6" style="margin-right: -6px">
                                            <label for="unit_id" class="form-label fw-bold">{{ __('Date *') }}</label>
                                            <input type="date" class="form-control" id="date"
                                                value="{{ date('Y-m-d') }}" name="date" required>
                                        </div>

                                        <div class="col-md-6" style="margin-right: -6px">
                                            <label for="unit_id" class="form-label fw-bold">{{ __('Note') }}</label>
                                            <textarea class="form-control" placeholder="{{ __('Enter Your Note') }} " name="note"></textarea>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text barcod_style" id="basic-addon1"><i
                                                    class="fa fa-barcode"></i></span>
                                        </div>
                                        <input type="text" id="product_search" class="form-control"
                                            placeholder="{{ __('Type & Barcode') }}" aria-label="{{ __('Type & Barcode') }}"
                                            onkeydown="return event.keyCode !== 13" autocomplete="off">
                                    </div>
                                </div>
                                <div class="cart-head">
                                    <div class="table-responsive">
                                        <table class="table table-striped text-center">
                                            <thead class="header_bg">
                                                <tr>
                                                    <th class="header_style_left" width="17%">{{ __('Product') }}</th>
                                                    @if (env('APP_SC') == 'yes')
                                                        <th width="20%">{{ __('Variation') }}</th>
                                                    @else
                                                        <th></th>
                                                    @endif
                                                    <th width="15%">{{ __('Rate') }}</th>
                                                    <th width="29%">{{ __('Adjust Quantity') }}</th>
                                                    <th width="29%">{{ __('Sub Total') }}</th>
                                                    <th class="header_style_right">{{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody">

                                            </tbody>
                                            <tfoot>
                                                <tr class="bg-light-primary">
                                                    <td colspan="4" class="text-right fw-bold" style="font-size: 18px; vertical-align: middle;">{{ __('Grand Total') }}:</td>
                                                    <td colspan="2" class="text-left">
                                                        <div id="grand_total" style="font-size: 20px; font-weight: 800; color: #10b981;">0.00</div>
                                                        <input type="hidden" name="estimated_amount" value="0" class="estimated_amount">
                                                        <input type="hidden" name="payable_amount" id="payable_amount" value="">
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="row ">
                                        <div class="footerpos mt-1 w-full px-1">
                                            <button type="submit" class="checkout-btn" id="payment_modal_btn">
                                                {{ __('Adjust Now') }}
                                            </button>
                                        </div>
                                </div>
                            </div>
                            </div>
                            {{-- @include('backend.pages.invoice.payment-modal') --}}
                        </form>
                    </div>
                </div>
                <!-- End col -->
            </div>
        </div>
    @endif

@endsection

@push('js')
    <script>
        // Select the input field when the page loads
        window.onload = function() {
            var inputField = document.getElementById('product_search');
            inputField.select();
        };

        $(document).ready(function() {
            console.log("Autocomplete input element found count:", $("#product_search").length);
            $("#payment_modal_btn").click(function() {
                // $("#payment_form").submit();
                // localStorage.clear();
            });

            $(document).on('change', '#to_branch_id', function() {
                localData = localStorage.getItem('adjust-items') ? JSON.parse(localStorage.getItem('adjust-items')) : [];
                $("#tbody").html('');
                showList();
                estimatedAmount();
            });
        });
    </script>
    <script>
        // Page Load
        var empty = '';
        // $('body').addClass('toggle-menu');
        $('#product_search').blur();

        var localData = localStorage.getItem('adjust-items') ? JSON.parse(localStorage.getItem('adjust-items')) : [];

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
            // console.log(typeof placeholder);
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
            let ldata = localStorage.getItem('adjust-items') ? JSON.parse(localStorage.getItem('adjust-items')) : [];
            return ldata.some(function(el) {
                return el.product.id === pid
            });
        }

        function storedata(data) {
            if (localStorage.getItem('adjust-items') != null) {
                cartList = JSON.parse(localStorage.getItem('adjust-items'))
                cartList.push(data);
            } else {
                cartList.push(data);
            }
            localStorage.setItem('adjust-items', JSON.stringify(cartList));
        }

        function addProductToCard(data, variation_code = null) {
            storedata(data);
            let currentItems = localStorage.getItem('adjust-items') ? JSON.parse(localStorage.getItem('adjust-items')) : [];
            let index = currentItems.length - 1;
            domPrepend(data, index, variation_code);
            estimatedAmount();
        }

        // Search Product
        var variation_code = '';
        $("#product_search").autocomplete({
            source: function(req, res) {
                console.log("Autocomplete triggered with input: " + req.term);
                let term = req.term.trim();
                let product_barcode_length = 13;
                let barcode = term;
                variation_code = '';
                if (term.length > product_barcode_length) {
                    barcode = term.substring(0, product_barcode_length);
                    variation_code = term.substring(product_barcode_length);
                }
                let branch_id = $('#from_branch_id').val() || $('input[name="branch_id"]').val();
                let url = "{{ route('sc-product-search') }}";
                console.log("AJAX request sent to: " + url + " with query: " + barcode + " and branch: " + branch_id);
                $.get(url, {
                    req: barcode,
                    branch_id: branch_id
                }, (data) => {
                    console.log("AJAX search results returned:", data);
                    res($.map(data, function(item) {
                        let outOfStock = item.stock_qty <= 0 ? " (Out of Stock)" : "";
                        let displayLabel = item.name + " " + item.barcode + outOfStock;
                        return {
                            id: item.id,
                            label: displayLabel,
                            value: displayLabel,
                            price: item.purchase_price,
                            stock_qty: item.stock_qty
                        }
                    })); // end res

                });
            },
            select: function(event, ui) {
                let to_branch_id = $('#to_branch_id').val();
                // let product_id = ui.item.id;
                if (to_branch_id == '') {
                    //Walking Customer Can't to Create a Due
                    iziToast.warning({
                        title: "{{ __('Please select To Branch.') }}",
                        position: "topRight",
                    });
                    $(this).val('');
                    return false;
                }
                $(this).val(ui.item.value);
                $("#search_product_id").val(ui.item.id);
                let branch_id = $('#from_branch_id').val() || $('input[name="branch_id"]').val();
                let url = "{{ route('sc-search-product-id', 'my_id') }}".replace('my_id', ui.item.id);
                $.get(url, { branch_id: branch_id }, (data) => {
                    let stock_to = $('#to_branch_id').val();
                    // check stock
                    if (stock_to === 'stock_out') {
                        if (data.stock_qty <= 0) {
                            iziToast.error({
                                title: "{{ __('Out of Stock!') }}",
                                message: "{{ __('Cannot adjust stock out for a product with 0 stock.') }}",
                                position: "topRight",
                            });
                            return false;
                        }
                    }
                    addProductToCard(data, variation_code);
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
            let url = "{{ route('sc-pos-product-id', 'my_id') }}".replace('my_id', productId);
            $.get(url, data => {
                // check stock
                // if (data.stock_qty <= 0) {
                //     iziToast.warning({
                //         title: "This product is Stock out. Please Purchases the Product.",
                //         position: "topRight",
                //     });
                //     return false;
                // }
                addProductToCard(data);
            });
        });
        $(document).on('click', '.remove-btn', function() {
            let itemIndex = $(this).attr('data-value');
            localData.splice(itemIndex, 1);
            localStorage.removeItem('adjust-items');
            localStorage.setItem('adjust-items', JSON.stringify(localData))
            $(this).parents('tr').remove();
            estimatedAmount();
        });
        $("#clearList").on('click', function() {
            localStorage.removeItem('adjust-items');
            $("#tbody").html(empty);
            estimatedAmount();
        });

        function domPrepend(data = null, index = null, variation_code = null) {
            // console.log(data.variations);
            var name = data.product.name;
            var quantity_data = '';
            var variation_data = ``;
            var imei_section = '';

            if (data.variations.length > 0) {
                variation_data +=
                    `<input type="text" class="has_size" data-has-size="true" hidden>
                                <select name="variation[${data.product.id}]" id="variation_id" class="variation_select form-control size" required>
                                    <option value="">{{ __('Select Variation') }}</option>`;

                $.each(data.variations, function(index, value) {
                    let isOutOfStock = parseFloat(value.stock) <= 0;
                    let selected = (value.id == variation_code) ? "selected" : "";
                    variation_data += "<option stock='" + value.stock + "' value='" + value.id + "' " + selected + ">" +
                        value.size + " - " + value.color + " - " + value.stock + (isOutOfStock ? " (Out of Stock)" : "") + "</option>";
                });

                variation_data += '</select>';
            } else {
                variation_data =
                    `<input type="text" class="has_size" data-has-size="false" hidden>`;
            }

            // IMEI Section - conditional UI based on stock status
            if (data.product.imei == 1 || data.product.imei == '1') {
                const stockStatus = $('#to_branch_id').val();
                if (stockStatus === 'stock_out') {
                    let options = '';
                    if (data.imeis && data.imeis.length > 0) {
                        data.imeis.forEach(imei => {
                            let serial = typeof imei === 'object' ? imei.serial : imei;
                            options += `<option value="${serial}">${serial}</option>`;
                        });
                    }
                    imei_section = `
                        <div class="mt-2 text-left">
                            <small class="text-danger font-weight-bold">{{ __('Select IMEIs') }}</small>
                            <select name="imei[${index}][]" class="form-control select2 imei-select mt-1" multiple>
                                ${options}
                            </select>
                        </div>
                    `;
                } else if (stockStatus === 'stock_in') {
                    imei_section = `
                        <div class="mt-2 text-left new-imei-section">
                            <small class="text-danger font-weight-bold">{{ __('Enter New IMEIs (comma separated)') }}</small>
                            <textarea name="new_imei[${index}]" class="form-control" rows="2" placeholder="e.g., IMEI123, IMEI456"></textarea>
                        </div>
                    `;
                }
            }

            if (data.product.unit.related_unit == null) {
                let isImei = data.product.imei == 1 || data.product.imei == '1';
                let defaultQty = isImei ? 0 : 1;
                quantity_data =
                    `<input type="text" class="has_sub_unit" hidden value="false">
                        <label class="ml-2 mr-2" style="padding-top: 5px;">${data.product.unit.name}:</label>
                        <input type="number" value="${defaultQty}" class="form-control col main_qty" name="main_qty[]" 
                        data-value="${data.stock_qty}" data-related="${data.product.unit.related_value}" 
                        onkeydown="return event.keyCode !== 190" min="0" ${isImei ? 'readonly' : ''}>

                        <input type="hidden" value="0" class="form-control col sub_qty mr-1" name="sub_qty[]"  
                        min="0" max="">`;
            } else {
                let isImei = data.product.imei == 1 || data.product.imei == '1';
                let defaultQty = isImei ? 0 : 1;
                quantity_data =
                    `<input type="text" class="has_sub_unit" hidden value="true">
                        <input type="text" class="conversion" hidden value="${data.product.unit.related_value}">
                        <label class="mr-1 ml-1" style="padding-top: 5px;">${data.product.unit.name}:</label>
                        <input type="number" value="${defaultQty}" class="form-control col main_qty mr-1" name="main_qty[]" 
                        data-value="${data.stock_qty}" data-related="${data.product.unit.related_value}" 
                        onkeydown="return event.keyCode !== 190" min="0" ${isImei ? 'readonly' : ''}>

                        <label class="mr-1" style="padding-top: 5px;">${data.product.unit.related_unit.name}:</label>
                        <input type="number" value="0" class="form-control col sub_qty mr-1" name="sub_qty[]"  
                        onkeydown="return event.keyCode !== 190" min="0" max="${data.product.unit.related_value-1}">`;
            }

            let dom = `
                <tr id="tbody_tr">
                    <td class="table_data_style_left" style="min-width: 100px;">
                    ${data.product.name + " - " + data.product.barcode }
                    ${imei_section}
                    <input type="hidden" class="name" value="${name.replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, '')}" name="name[]" />
                    <input type="hidden" value="${data.product.id}" name="product_id[]" />
                    </td>
                    @if (env('APP_SC') == 'yes')
                    <td style="min-width: 120px;">
                        ${variation_data}
                        <input type="hidden" name="variation_id[]" value="${variation_code ?? ''}">
                    </td>
                    @else
                    <td></td>
                    @endif
                    <td>
                        <input type="number" style="min-width: 100px;" readonly value="${data.product.purchase_price}" class="form-control rate" name="rate[]" />
                    </td>
                    <td>
                        <div class="form-row" style="min-width: 100px;">
                            ${quantity_data}
                        </div>
                    </td>
                    <td>
                    <input type="number" readonly style="min-width: 100px;" readonly name="sub_total[]" class="form-control sub_total" value="${data.product.purchase_price}"/>
                    </td>
                    <td class="table_data_style_right">
                    <a href="#" class="remove-btn item-index" data-value="${index}"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            `;
            $("#tbody").prepend(dom);
            $('.select2').select2();
        }

        $(document).on('change', '.size', function() {
            const variationId = $(this).val();
            $(this).siblings('input[name="variation_id[]"]').val(variationId);
        });

        function handle_change(obj) {
            var main_val = parseInt(empty_field_check(obj.parents('tr').find('.main_qty').val()));
            var sub_val = parseInt(empty_field_check(obj.parents('tr').find('.sub_qty').val()));
            let related_by = parseInt(empty_field_check(obj.parents('tr').find('.main_qty').attr('data-related')));
            var has_sub_unit = obj.parents('tr').find('.has_sub_unit').val();
            let converted_sub = to_sub_unit(main_val, sub_val, related_by, has_sub_unit);
            let stock = obj.parents('tr').find('.main_qty').attr('data-value');
            // alert(stock);


            // if (stock < converted_sub) {
            //     // put the max stock
            //     var converted;
            //     if (has_sub_unit == "true") {
            //         converted = convert_to_main_and_sub(stock, has_sub_unit, related_by);
            //         obj.parents('tr').find('.main_qty').val(converted.main_qty);
            //         obj.parents('tr').find('.sub_qty').val(converted.sub_qty);
            //     } else {
            //         converted = convert_to_main_and_sub(stock, has_sub_unit, related_by);
            //         obj.parents('tr').find('.main_qty').val(converted.main_qty);
            //     }

            //     let price = obj.parents('tr').find('.rate').val();
            //     price = parseFloat(price);

            //     let subTotal = calculate_sub_total(converted.main_qty, converted.sub_qty, price, related_by, has_sub_unit);

            //     obj.parents('tr').find('.sub_total').val(subTotal);
            //     estimatedAmount();

            //     iziToast.warning({
            //         title: "Not Enough Stock.",
            //         position: "topRight",
            //     });
            // } else {
            let price = obj.parents('tr').find('.rate').val();
            price = parseFloat(price);
            let subTotal = calculate_sub_total(main_val, sub_val, price, related_by, has_sub_unit);
            obj.parents('tr').find('.sub_total').val(subTotal);
            estimatedAmount();
            //}
        }

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

        // IMEI select change - auto-update quantity based on selected IMEIs
        $(document).on('change', 'select.imei-select', function() {
            let selectedCount = $(this).val() ? $(this).val().length : 0;
            let row = $(this).closest('tr');
            row.find('.main_qty').val(selectedCount).trigger('change');
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
                    title: "{{ __('Please select a date.') }}",
                    position: "topRight",
                });
                return false;
            }
            let variationValid = true;
            $(".variation_select").each(function() {
                if ($(this).val() == '') {
                    variationValid = false;
                }
            });
            if (!variationValid) {
                iziToast.warning({
                    title: "{{ __('Please select a product variation.') }}",
                    position: "topRight",
                });
                return false;
            }
            //order_modal_obj
            if ($.trim($('.name').val()) == '') {
                iziToast.warning({
                    title: "{{ __('Please select at least one product') }}",
                    position: "topRight",
                });
                return false;
            }
            // count of tr in cart_list table
            var count = $("#tbody").find("tr#tbody_tr").length;
            
            // Stock Status and Rule Validations
            let stock_to = $('#to_branch_id').val();
            let isValid = true;
            let errorMsg = '';

            $('#tbody tr').each(function() {
                let main_qty = parseFloat($(this).find('.main_qty').val()) || 0;
                let sub_qty = parseFloat($(this).find('.sub_qty').val()) || 0;
                let stock = parseFloat($(this).find('.main_qty').attr('data-value')) || 0;
                let related_by = parseFloat($(this).find('.main_qty').attr('data-related')) || 1;
                let total_qty = (main_qty * related_by) + sub_qty;
                let productName = $(this).find('td:first').text().trim();

                if (stock_to === 'stock_out') {
                    if (stock <= 0) {
                        isValid = false;
                        errorMsg = "Product " + productName + " has 0 stock. Cannot adjust out.";
                        return false;
                    }
                    if (stock < total_qty) {
                        isValid = false;
                        errorMsg = "Not enough stock for " + productName + ". Current stock: " + stock;
                        return false;
                    }
                }

                if (stock_to === 'stock_in' && total_qty > 10) {
                    isValid = false;
                    errorMsg = "Maximum 10 units allowed for Stock In for " + productName;
                    return false;
                }
            });

            if (!isValid) {
                iziToast.error({
                    title: errorMsg,
                    position: "topRight",
                });
                return false;
            }

            $(".total_item").text(count);
            //show payment_modal
            // $("#payment_modal").modal("show");
            $("#payment_form").submit();
            localStorage.removeItem('adjust-items');
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
            localStorage.removeItem('adjust-items');
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
                    // console.log(data)
                },
                error: function() {
                    alert('Error !');
                }
            });
        });
    </script>
@endpush
