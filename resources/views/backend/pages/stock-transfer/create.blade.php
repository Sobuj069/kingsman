@extends('backend.layouts.master')
@section('page-title', __('Stock Transfer'))

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

        .ecommerce-sortbyd {
            margin-top: 0px !important;
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
                            <form action="{{ route('transfer.store') }}" id="payment_form" method="POST">
                                @csrf
                                <div class="card-body">
                                    <div class="cart-container">
                                        <div class="cart-head">
                                            <div class="row align-items-center ecommerce-sortbyd">
                                            <!-- Start col -->
                                            <div class="col-md-6">
                                                <label for="unit_id" class="form-label fw-bold">{{ __('From *') }}</label>
                                                @if ($userBranchId == 1)
                                                    @php
                                                        $branch_name = App\Models\Branch::where(
                                                            'id',
                                                            $filterBranchId,
                                                        )->first();
                                                    @endphp
                                                    <input readonly class="form-control" type="text"
                                                        value="{{ $branch_name->name }}">
                                                    <input type="hidden" name="from_branch_id" id="from_branch_id"
                                                        value="{{ $filterBranchId }}">
                                                @else
                                                    <input readonly class="form-control" type="text"
                                                        value="{{ auth()->user()->branch->name }}">
                                                    <input type="hidden" name="from_branch_id" class="form-control"
                                                        value="{{ auth()->user()->branch_id }}">
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <label for="unit_id" class="form-label fw-bold">{{ __('To *') }}</label>
                                                <select class="select2 to_branch_id" name="to_branch_id" id="to_branch_id">
                                                    <option selected value="">{{ __('Select Branch') }}</option>
                                                    @foreach ($allBranch as $branch)
                                                        @if ($userBranchId == 1)
                                                            @if ($filterBranchId != $branch->id)
                                                                <option value="{{ $branch->id }}">{{ $branch->name }}
                                                                </option>
                                                            @endif
                                                        @else
                                                            @if (auth()->user()->branch_id != $branch->id)
                                                                <option value="{{ $branch->id }}">{{ $branch->name }}
                                                                </option>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row align-items-center ecommerce-sortby mt-3">
                                            <!-- Start col -->
                                            <div class="col-md-6">
                                                <label for="unit_id" class="form-label fw-bold">{{ __('Date *') }}</label>
                                                <input type="date" class="form-control" id="date"
                                                    value="{{ date('Y-m-d') }}" name="date" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="unit_id" class="form-label fw-bold">{{ __('Note') }}</label>
                                                <textarea class="form-control" placeholder="{{ __('Enter Your Note') }} " name="note"></textarea>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend ">
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
                                                        <th width="20%">{{ __('Variation') }}</th>
                                                        <th width="15%">{{ __('Rate') }}</th>
                                                        <th width="29%">{{ __('Transfer Quantity') }}</th>
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
                                    </div>
                                    <div class="row ">
                                        <div class="footerpos mt-1 w-full px-1">
                                            <button type="button" class="checkout-btn" id="checkout">
                                                {{ __('Transfer Now') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="invoice-contentbar text-center">
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
                        <form action="{{ route('transfer.store') }}" id="payment_form" method="POST">
                            @csrf
                            <div class="card-body">
                                <div class="cart-container">
                                    <div class="cart-head">
                                        <div class="row align-items-center ecommerce-sortbyd">
                                            <!-- Start col -->
                                            <div class="col-md-6">
                                                <label for="unit_id" class="form-label fw-bold">{{ __('From *') }}</label>
                                                @if ($userBranchId == 1)
                                                    @php
                                                        $branch_name = App\Models\Branch::where(
                                                            'id',
                                                            $filterBranchId,
                                                        )->first();
                                                    @endphp
                                                    <input readonly class="form-control" type="text"
                                                        value="{{ $branch_name->name }}">
                                                    <input type="hidden" name="from_branch_id" id="from_branch_id"
                                                        value="{{ $filterBranchId }}">
                                                @else
                                                    <input readonly class="form-control" type="text"
                                                        value="{{ auth()->user()->branch->name }}">
                                                    <input type="hidden" name="from_branch_id" class="form-control"
                                                        value="{{ auth()->user()->branch_id }}">
                                                @endif
                                            </div>
                                            <div class="col-md-6">
                                                <label for="unit_id" class="form-label fw-bold">{{ __('To *') }}</label>
                                                <select class="select2 to_branch_id" name="to_branch_id" id="to_branch_id">
                                                    <option selected value="">{{ __('Select Branch') }}</option>
                                                    @foreach ($allBranch as $branch)
                                                        @if ($userBranchId == 1)
                                                            @if ($filterBranchId != $branch->id)
                                                                <option value="{{ $branch->id }}">{{ $branch->name }}
                                                                </option>
                                                            @endif
                                                        @else
                                                            @if (auth()->user()->branch_id != $branch->id)
                                                                <option value="{{ $branch->id }}">{{ $branch->name }}
                                                                </option>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row align-items-center ecommerce-sortby mt-3">
                                            <!-- Start col -->
                                            <div class="col-md-6">
                                                <label for="unit_id" class="form-label fw-bold">{{ __('Date *') }}</label>
                                                <input type="date" class="form-control" id="date"
                                                    value="{{ date('Y-m-d') }}" name="date" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="unit_id" class="form-label fw-bold">{{ __('Note') }}</label>
                                                <textarea class="form-control" placeholder="{{ __('Enter Your Note') }} " name="note"></textarea>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend ">
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
                                                        <th width="20%">{{ __('Variation') }}</th>
                                                        <th width="15%">{{ __('Rate') }}</th>
                                                        <th width="29%">{{ __('Transfer Quantity') }}</th>
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
                                    </div>
                                    <div class="row ">
                                        <div class="footerpos mt-1 w-full px-1">
                                            <button type="button" class="checkout-btn" id="checkout">
                                                {{ __('Transfer Now') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('js')
    <script>
        // Polyfill for iziToast using ToastMagic
        if (typeof iziToast === 'undefined') {
            window.iziToast = {
                success: function(obj) { 
                    if (typeof toastMagic !== 'undefined') toastMagic.success(obj.title || obj);
                    else console.log('Success:', obj.title || obj);
                },
                error: function(obj) { 
                    if (typeof toastMagic !== 'undefined') toastMagic.error(obj.title || obj);
                    else console.error('Error:', obj.title || obj);
                },
                warning: function(obj) { 
                    if (typeof toastMagic !== 'undefined') toastMagic.warning(obj.title || obj);
                    else console.warn('Warning:', obj.title || obj);
                },
                info: function(obj) { 
                    if (typeof toastMagic !== 'undefined') toastMagic.info(obj.title || obj);
                    else console.info('Info:', obj.title || obj);
                }
            };
        }

        $(document).ready(function() {

            // When clicking on product search
            $('#product_search').on('click focus', function() {

                var toBranch = $('#to_branch_id').val();

                if (toBranch === '' || toBranch === null) {
                    iziToast.warning({
                        title: "{{ __('Please select to branch first.') }}",
                        position: "topRight",
                    });
                    $('#to_branch_id').focus();
                    return false;
                }

            });

            // Auto update quantity when IMEIs are selected
            $(document).on('change', 'select[name^="imei"]', function() {
                let row = $(this).closest('tr');
                let selectedCount = $(this).val() ? $(this).val().length : 0;
                let qtyInput = row.find('.quantity-input');
                if (qtyInput.length > 0) {
                    qtyInput.val(selectedCount);
                    qtyInput.trigger('change');
                } else {
                    let mainQty = row.find('.main_qty');
                    mainQty.val(selectedCount).trigger('change');
                }
            });

        });
    </script>

    <script>
        // Select the input field when the page loads
        window.onload = function() {
            var inputField = document.getElementById('product_search');
            // inputField.select();
        };
    </script>

    <script>
        // Page Load
        var empty = '';
        // $('body').addClass('toggle-menu');
        $('#product_search').blur();

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
            if (placeholder === null || typeof placeholder === 'undefined' || isNaN(placeholder)) {
                return 0;
            }
            let val = placeholder.toString().trim();
            if (val === "" || val === 'null' || val === 'NaN') {
                return 0;
            }
            return placeholder;
        }

        function parseQuantityInput(input, related_by) {
            input = input.toString().trim();

            // Empty or invalid input
            if (input === '' || isNaN(input)) {
                return {
                    main_qty: 0,
                    sub_qty: 0
                };
            }

            let main_qty = 0;
            let sub_qty = 0;

            // Split by dot (.)
            let parts = input.split('.');
            main_qty = parseInt(parts[0]) || 0;

            if (parts.length > 1) {
                // Take the decimal digits directly as subunit (e.g. .9 => 9 gm)
                let decimalPart = parts[1].replace(/[^0-9]/g, ''); // remove non-numeric
                sub_qty = parseInt(decimalPart) || 0;

                // If user writes .000 -> sub_qty = 0
                if (isNaN(sub_qty)) sub_qty = 0;

                // Prevent overflow — if subunit >= related_by, convert extra to main_qty
                if (related_by && sub_qty >= related_by) {
                    main_qty += Math.floor(sub_qty / related_by);
                    sub_qty = sub_qty % related_by;
                }
            }

            return {
                main_qty: main_qty,
                sub_qty: sub_qty
            };
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

        function calculate_sub_total(main_qty, sub_qty, unit_price, related_by, has_sub_unit, discount = 0) {
            var sub_unit_price = 0;
            if (has_sub_unit == "true" && related_by != 0) {
                sub_unit_price = parseFloat(unit_price / related_by);
            }
            var main_price = main_qty * unit_price;
            var sub_price = sub_qty * sub_unit_price;
            var subtotal = main_price + sub_price;
            // ===== Apply Discount =====
            if (typeof discount === 'string' && discount.includes("%")) {
                // যদি % discount হয়
                let percent = parseFloat(discount.replace('%', '')) || 0;
                subtotal = subtotal - (subtotal * (percent / 100));
            } else {
                // flat discount
                discount = parseFloat(discount) || 0;
                subtotal = subtotal - discount;
            }

            return parseFloat(subtotal).toFixed(2);
        }

        $(document).on('keyup change', '.product_discount', function(e) {
            let discount = $(this).val();
            let row = $(this).closest('tr');
            let main_qty = parseInt(row.find('.main_qty').val()) || 0;
            let sub_qty = parseInt(row.find('.sub_qty').val()) || 0;
            let unit_price = parseFloat(row.find('.rate').val()) || 0;
            let related_by = parseInt(row.find('.main_qty').attr('data-related')) || 1;
            let has_sub_unit = row.find('.has_sub_unit').val();

            let sub_total = calculate_sub_total(main_qty, sub_qty, unit_price, related_by, has_sub_unit, discount);
            row.find('.sub_total').val(sub_total);
            estimatedAmount();
        });

        function parseStockQty(stockText, has_sub_unit, related_by = 1) {

            if (!stockText) return 0;

            // API object response
            if (typeof stockText === 'object' && stockText.available_stock !== undefined) {
                return parseFloat(stockText.available_stock) || 0;
            }

            // If string like "2 kg 500 gm"
            if (typeof stockText === 'string') {

                let mainMatch = stockText.match(/(\d+(?:\.\d+)?)/);
                let subMatch = stockText.match(/(\d+(?:\.\d+)?)\s*[a-zA-Z]+$/);

                let main = mainMatch ? parseFloat(mainMatch[1]) : 0;
                let sub = subMatch ? parseFloat(subMatch[1]) : 0;

                if (has_sub_unit && related_by > 0) {
                    return main + (sub / related_by);
                }

                return main;
            }

            return parseFloat(stockText) || 0;
        }

        function addProductToCard(data, weight = null, variation_code = null) {
            // Check if product with the same variation already exists
            let existingRow = $("#tbody tr").filter(function() {
                return $(this).find("input[name='product_id[]']").val() == data.product.id &&
                    $(this).find("select[name='variation_id[]']").val() == variation_code;
            });

            if (existingRow.length > 0) {
                // ===== Existing product quantity increase =====
                let qtyInput = existingRow.find(".quantity-input"); // visible input box
                let mainQtyHidden = existingRow.find(".main_qty"); // hidden main_qty
                let subQtyHidden = existingRow.find(".sub_qty"); // hidden sub_qty

                let currentQty = parseFloat(mainQtyHidden.val()) || 1;
                let newQty = currentQty + 1;

                // visible input update
                qtyInput.val(newQty);

                // hidden main_qty update
                mainQtyHidden.val(newQty);

                // sub_qty always 0
                subQtyHidden.val(0);

                // ===== Subtotal Update =====
                let rate = parseFloat(existingRow.find(".rate").val()) || 0;
                let discount = existingRow.find(".product_discount").val() || '0';
                let has_sub_unit = existingRow.find('.has_sub_unit').val();
                let related_by = parseInt(existingRow.find('.quantity-input').attr('data-related')) || 1;
                let newSubtotal = calculate_sub_total(newQty, 0, rate, related_by, has_sub_unit, discount);

                existingRow.find(".sub_total").val(newSubtotal);
                existingRow.find(".sub_total_text").text(newSubtotal);

                estimatedAmount();

                iziToast.success({
                    title: "{{ __('Quantity increased') }}",
                    position: "topRight",
                });
            } else {
                // Extract numeric stock value for checking
                let stockText = data.stock_qty;

                // check if product has sub unit
                let has_sub_unit = data.product.unit.related_unit != null;

                // conversion value (kg=1000gm / box=12pcs etc)
                let related_by = data.product.unit.related_value ?? 1;

                // parse stock quantity
                let stockQty = parseStockQty(stockText, has_sub_unit, related_by);

                // stock check
                if (data.product.is_service == 0 && stockQty <= 0) {
                    iziToast.warning({
                        title: "{{ __('This product is Stock out. Please Purchase the Product.') }}",
                        position: "topRight",
                    });
                    return false;
                }

                // ===== New product add =====
                let index = localData.length;
                localData.push({
                    product: data.product,
                    stock_qty: data.stock_qty,
                    variations: data.variations || [],
                    variation_code: variation_code // store variation_code
                });

                localStorage.setItem('pos-items', JSON.stringify(localData));

                // DOM এ নতুন row add
                domPrepend(data, index, variation_code);

                // set quantity, subtotal etc...
                let tr = $("#tbody tr:first"); // prepend হয়েছে তাই প্রথম tr ধরছি

                let main_qty = 1;
                let sub_qty = 0;

                if (weight !== null && weight !== '') {
                    let has_sub_unit = tr.find('.has_sub_unit').val();
                    let related_by = parseInt(tr.find('.main_qty').attr('data-related')) || 1000;
                    let gram = parseFloat(weight);

                    if (gram < 10 && related_by == 1000) {
                        gram = gram / 1000;
                    }

                    if (has_sub_unit === "true" && related_by > 0) {
                        main_qty = gram / related_by;
                        sub_qty = 0;
                    } else {
                        main_qty = gram / related_by;
                        sub_qty = 0;
                    }
                }

                tr.find('.main_qty').val(main_qty);
                tr.find('.sub_qty').val(sub_qty);
                tr.find('.quantity-input').val(main_qty);

                let rate = parseFloat(tr.find(".rate").val()) || 0;
                let discount = tr.find(".product_discount").val() || '0';
                let has_sub_unit = tr.find('.has_sub_unit').val();
                let related_by = parseInt(tr.find('.quantity-input').attr('data-related')) || 1;
                let subtotal = calculate_sub_total(main_qty, sub_qty, rate, related_by, has_sub_unit, discount);

                tr.find(".sub_total").val(subtotal);
                tr.find(".sub_total_text").text(subtotal);

                estimatedAmount();
            }
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

        var weight = null;
        $("#product_search_scale").autocomplete({
            source: function(req, res) {
                let barcode = req.term.substring(0, 6);
                weight = req.term.substring(6, 11);
                let url = "{{ route('product-search') }}";
                $.get(url, {
                    req: barcode
                }, (data) => {
                    res($.map(data, function(item) {
                        return {
                            id: item.id,
                            value: item.name + " " + item.barcode,
                            price: item.purchase_price
                        }
                    })); // end res
                });
            },
            select: function(event, ui) {
                $(this).val(ui.item.value);

                $("#search_product_id").val(ui.item.id);
                let url = "{{ route('search-product-id', 'my_id') }}".replace('my_id', ui.item.id);
                $.get(url, (data) => {
                    // console.log(data);
                    let stockText = data.stock_qty;
                    let stockQty = 0;

                    if (typeof stockText === 'object' && stockText.available_stock) {
                        stockQty = parseFloat(stockText.available_stock) || 0;
                    } else if (typeof stockText === 'string') {
                        let kgMatch = stockText.match(/(\d+(?:\.\d+)?)\s*kg/i);
                        let gmMatch = stockText.match(/(\d+(?:\.\d+)?)\s*gm/i);
                        let kg = kgMatch ? parseFloat(kgMatch[1]) : 0;
                        let gm = gmMatch ? parseFloat(gmMatch[1]) : 0;
                        stockQty = kg + (gm / 1000);
                    } else {
                        stockQty = parseFloat(stockText) || 0;
                    }

                    if (pExist(data.product.id) == true) {
                        iziToast.warning({
                            title: "{{ __('Please Increase the quantity.') }}",
                            position: "topRight",
                        });
                    } else {
                        addProductToCard(data, weight);
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

        var variation_code = '';
        $("#product_search").autocomplete({
            source: function(req, res) {
                let term = req.term.trim();

                // Adjust this dynamically if needed, or keep 6 as default minimum product barcode
                let product_barcode_length = 13;

                // Split barcode and variation_code
                let barcode = term;
                variation_code = '';
                if (term.length > product_barcode_length) {
                    barcode = term.substring(0, product_barcode_length);
                    variation_code = term.substring(product_barcode_length);
                }

                let url = "{{ route('sc-product-search') }}";
                let from_branch_id = $('#from_branch_id').val() || $('input[name="from_branch_id"]').val();
                $.get(url, {
                    req: barcode,
                    branch_id: from_branch_id
                }, function(data) {
                    res($.map(data, function(item) {
                        return {
                            id: item.id,
                            label: item.name + " (" + item.purchase_price +
                                " {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}) - " +
                                item.barcode,
                            value: item.name + " " + item.barcode,
                            price: item.purchase_price
                        };
                    }));
                });
            },
            select: function(event, ui) {
                $(this).val(ui.item.value);
                $("#search_product_id").val(ui.item.id);

                let url = "{{ route('sc-search-product-id', 'my_id') }}".replace('my_id', ui.item.id);
                let from_branch_id = $('#from_branch_id').val() || $('input[name="from_branch_id"]').val();
                $.get(url, { branch_id: from_branch_id }, function(data) {
                    let stockText = data.stock_qty;

                    // check if product has sub unit
                    let has_sub_unit = data.product.unit.related_unit != null;

                    // conversion value (kg=1000gm / box=12pcs etc)
                    let related_by = data.product.unit.related_value ?? 1;

                    // parse stock quantity
                    let stockQty = parseStockQty(stockText, has_sub_unit, related_by);

                    // stock check
                    if (data.product.is_service == 0 && stockQty <= 0) {
                        iziToast.warning({
                            title: "{{ __('This product is Stock out. Please Purchase the Product.') }}",
                            position: "topRight",
                        });
                        return false;
                    }
                    // Pass the dynamic variation_code to addProductToCard
                    addProductToCard(data, null, variation_code);
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

        $(document).on('click', '.remove-btn', function() {
            let itemIndex = $(this).attr('data-value');
            localData.splice(itemIndex, 1);
            localStorage.removeItem('pos-items');
            localStorage.setItem('pos-items', JSON.stringify(localData))
            $(this).parents('tr').remove();
            estimatedAmount();
        });

        // Manage Cart items
        $(document).on('click', '.product', function() {
            let productId = $(this).attr('data-value');
            let url = "{{ route('sc-pos-product-id', 'my_id') }}".replace('my_id', productId);
            $.get(url, data => {
                let stockText = data.stock_qty;

                // check if product has sub unit
                let has_sub_unit = data.product.unit.related_unit != null;

                // conversion value (kg=1000gm / box=12pcs etc)
                let related_by = data.product.unit.related_value ?? 1;

                // parse stock quantity
                let stockQty = parseStockQty(stockText, has_sub_unit, related_by);

                // stock check
                if (data.product.is_service == 0 && stockQty <= 0) {
                    iziToast.warning({
                        title: "This product is Stock out. Please Purchase the Product.",
                        position: "topRight",
                    });
                    return false;
                }
                addProductToCard(data);
            }); // Load Data to cart

        });

        $("#clearList").on('click', function() {
            localStorage.removeItem('pos-items');
            $("#tbody").html(empty);
            estimatedAmount();
        });

        function domPrepend(data = null, index = null, variation_code = null) {
            var name = data.product.name;
            var quantity_data = '';
            var variation_data = ``;
            var imei_section = '';
            let main_qty_val = 1; // Default to 1 for new products
            let sub_qty_val = 0;

            if (data.product.imei == 1 || data.product.imei == '1') {
                let options = '';
                if (data.imeis && data.imeis.length > 0) {
                    data.imeis.forEach(imei => {
                        options += `<option value="${imei}">${imei}</option>`;
                    });
                }
                imei_section = `
                    <div class="mt-2 text-left">
                        <small class="text-danger font-weight-bold">{{ __('Select IMEIs') }}</small>
                        <select name="imei[${index}][]" class="form-control select2 mt-1" multiple>
                            ${options}
                        </select>
                    </div>
                `;
            }

            // Format stock_qty for display
            let displayStock = '';
            if (typeof data.stock_qty === 'object' && data.stock_qty.available_stock) {
                displayStock = data.stock_qty.available_stock;
            } else {
                displayStock = data.stock_qty;
            }

            if (data.variations && data.variations.length > 0) {
                variation_data += `<input type="text" class="has_size" data-has-size="true" hidden>
                <select name="variation_id[]" class="form-control size" required>
                <option value="">{{ __('Select Variation') }}</option>`;

                $.each(data.variations, function(idx, value) {
                    if (parseFloat(value.stock) <= 0) return; // skip zero stock
                    let selected = (variation_code && parseInt(variation_code) === value.id) ? "selected" : "";
                    console.log("Variation ID:", value.id, "Selected:", selected);
                    variation_data += `<option stock='${value.stock}' value='${value.id}' ${selected}>
                    ${value.size} - ${value.color} - ${value.stock}</option>`;
                });

                variation_data += '</select>';
            } else {
                variation_data = `<input type="text" class="has_size" data-has-size="false" hidden>
                <input type="hidden" name="variation_id[]">
                `;
            }

            let initialSubtotal = calculate_sub_total(
                1,
                0,
                data.product.purchase_price,
                data.product.unit.related_value || 1,
                data.product.unit.related_unit != null ? "true" : "false"
            );

            if (data.product.is_service == 0) {
                if (typeof weight !== "undefined" && weight !== null && weight !== "" && !isNaN(weight)) {
                    let gram = parseInt(weight, 10); // সবসময় gram এ আসবে
                    if (data.product.unit.related_unit != null) {
                        // যদি related unit থাকে (যেমন kg = 1000 gm)
                        let related_by = parseInt(data.product.unit.related_value) || 1000;
                        main_qty_val = (gram / related_by).toFixed(3); // decimal এ convert
                    } else {
                        // শুধু main unit
                        main_qty_val = gram;
                    }
                }
                if (data.product.unit.related_unit == null) {
                    quantity_data =
                        `
                            <input type="text" class="has_sub_unit" hidden value="false">
                            <label class="ml-2 mr-2" style="padding-top: 5px;">${data.product.unit.name}:</label>
                            <input type="text" class="form-control quantity-input" value="${main_qty_val}" 
                                placeholder="e.g., 5 kg, 500 gm, 5.5" 
                                data-related="${data.product.unit.related_value}" 
                                data-stock="${displayStock}">
                            <input type="hidden" class="main_qty" name="main_qty[]" value="1">
                            <input type="hidden" class="sub_qty" name="sub_qty[]" value="0">`;
                } else {
                    quantity_data =
                        `<input type="text" class="has_sub_unit" hidden value="true">
                                <input type="text" class="conversion" hidden value="${data.product.unit.related_value}">
                                <label class="mr-4 ml-1" style="padding-top: 5px;">${data.product.unit.name}:</label>
                                <input type="text" class="form-control quantity-input" value="${main_qty_val}" 
                                    placeholder="e.g., 5 kg, 500 gm, 5.5" 
                                    data-related="${data.product.unit.related_value}" 
                                    data-stock="${displayStock}">
                                <input type="hidden" class="main_qty" name="main_qty[]" value="1">
                                <input type="hidden" class="sub_qty" name="sub_qty[]" value="0">`;
                }
            } else {
                quantity_data =
                    `
                        <label class="ml-2 mr-2" style="padding-top: 5px;">pcs:</label>
                        <input type="number" value="1" class="form-control col main_qty" 
                            name="main_qty[]" onkeydown="return event.keyCode !== 190" min="0">`;
            }

            let dom = `
                    <tr id="tbody_tr">
                        <td class="table_data_style_left" style="min-width: 100px;">
                            ${data.product.name + " - " + data.product.barcode } (${displayStock})
                            ${imei_section}
                            <input type="hidden" class="name" 
                                value="${name.replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, '')}" name="name[]" />
                            <input type="hidden" value="${data.product.id}" name="product_id[]" />
                        </td>
                        <td style="min-width: 120px;">
                            ${variation_data}
                        </td>
                        <td>
                            <input type="number" style="min-width: 100px;" 
                                value="${data.product.purchase_price}" 
                                class="form-control rate" name="rate[]" />
                        </td>
                        <td>
                            <div class="form-row" style="min-width: 100px;">
                                ${quantity_data}
                            </div>
                        </td>
                        <td hidden>
                            <input type="text" style="min-width: 100px;" 
                                class="form-control product_discount" 
                                name="product_discount[]" value="0" placeholder="Discount" />
                        </td>
                        <td>
                            <input type="number" style="min-width: 100px;" readonly 
                                name="sub_total[]" class="form-control sub_total" 
                                value="${initialSubtotal}"/>
                        </td>
                        <td class="table_data_style_right">
                            <a href="#" class="remove-btn item-index" data-value="${index}">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    `;

            $("#tbody").prepend(dom);
            $('.select2').select2();
        }

        function parseStockText(stockText, has_sub_unit, related_by = 1) {

            if (!stockText) return 0;

            // PC / Piece product
            if (!has_sub_unit || has_sub_unit === "false") {
                return parseFloat(stockText) || 0;
            }

            // API object response
            if (typeof stockText === 'object' && stockText.available_stock !== undefined) {
                return parseFloat(stockText.available_stock) || 0;
            }

            // If string like "2 unit 5 sub"
            if (typeof stockText === 'string') {

                let numbers = stockText.match(/\d+(\.\d+)?/g);

                let main = numbers && numbers[0] ? parseFloat(numbers[0]) : 0;
                let sub = numbers && numbers[1] ? parseFloat(numbers[1]) : 0;

                return main + (sub / related_by);
            }

            return parseFloat(stockText) || 0;
        }

        // $(document).on('keyup change', '.quantity-input', function(e) {
        //     let row = $(this).closest('tr');

        //     let input = $(this).val();
        //     let related_by = parseInt($(this).attr('data-related')) || 1;
        //     let has_sub_unit = row.find('.has_sub_unit').val();
        //     let stockText = $(this).attr('data-stock');

        //     // parse stock
        //     let stock_qty = parseStockText(stockText, has_sub_unit, related_by);
        //     let stock = stock_qty * related_by;

        //     // parse input quantity
        //     let quantities = parseQuantityInput(input, related_by);
        //     let total_quantity = to_sub_unit(
        //         quantities.main_qty,
        //         quantities.sub_qty,
        //         related_by,
        //         has_sub_unit
        //     );

        //     if (stock < total_quantity) {
        //         iziToast.warning({
        //             title: "Not Enough Stock.",
        //             position: "topRight",
        //         });

        //         // Set input to max available stock
        //         let converted = convert_to_main_and_sub(stock, has_sub_unit, related_by);
        //         quantities.main_qty = converted.main_qty;
        //         quantities.sub_qty = converted.sub_qty;

        //         $(this).val(
        //             quantities.main_qty +
        //             (quantities.sub_qty > 0 ?
        //                 '.' + quantities.sub_qty.toString().padStart(3, '0').substring(0, 3) :
        //                 '')
        //         );
        //     }

        //     // Update hidden inputs
        //     row.find('.main_qty').val(quantities.main_qty);
        //     row.find('.sub_qty').val(quantities.sub_qty);

        //     // Call handle_change to recalc subtotal
        //     handle_change($(this));
        // });
        $(document).on('keyup change', '.quantity-input', function(e) {
            let row = $(this).closest('tr');
            let input = $(this).val();
            let related_by = parseInt($(this).attr('data-related')) || 1;
            let has_sub_unit = row.find('.has_sub_unit').val();

            // ✅ Get stock from selected variation if exists
            let variation_select = row.find('select[name="variation_id[]"]');
            let stock = 0;

            if (variation_select.length > 0) {
                let selectedOption = variation_select.find('option:selected');
                let variationStock = parseFloat(selectedOption.attr('stock')) || 0;
                stock = has_sub_unit === "true" ? variationStock * related_by : variationStock;
            } else {
                // fallback to product stock
                let stockText = $(this).attr('data-stock');
                let stock_qty = parseStockText(stockText, has_sub_unit, related_by);
                stock = stock_qty * related_by;
            }

            // parse input quantity
            let quantities = parseQuantityInput(input, related_by);
            let total_quantity = to_sub_unit(
                quantities.main_qty,
                quantities.sub_qty,
                related_by,
                has_sub_unit
            );

            if (stock < total_quantity) {
                iziToast.warning({
                    title: "Not Enough Stock for selected variation.",
                    position: "topRight",
                });

                // Set input to max available stock
                let converted = convert_to_main_and_sub(stock, has_sub_unit, related_by);
                quantities.main_qty = converted.main_qty;
                quantities.sub_qty = converted.sub_qty;

                $(this).val(
                    quantities.main_qty +
                    (quantities.sub_qty > 0 ?
                        '.' + quantities.sub_qty.toString().padStart(3, '0').substring(0, 3) :
                        '')
                );
            }

            // Update hidden inputs
            row.find('.main_qty').val(quantities.main_qty);
            row.find('.sub_qty').val(quantities.sub_qty);

            // Recalc subtotal
            handle_change($(this));
        });

        // function handle_change(obj) {
        //     let row = obj.closest('tr');

        //     let main_val = parseInt(empty_field_check(row.find('.main_qty').val())) || 0;
        //     let sub_val = parseInt(empty_field_check(row.find('.sub_qty').val())) || 0;

        //     let related_by = parseInt(row.find('.quantity-input').attr('data-related')) || 1;
        //     let has_sub_unit = row.find('.has_sub_unit').val();

        //     // stock calculate
        //     let stockText = row.find('.quantity-input').attr('data-stock');
        //     let stock_qty = parseStockText(stockText, has_sub_unit, related_by);
        //     let stock = stock_qty * related_by;

        //     let converted_sub = to_sub_unit(main_val, sub_val, related_by, has_sub_unit);

        //     if (stock < converted_sub) {
        //         iziToast.warning({
        //             title: "Not Enough Stock.",
        //             position: "topRight",
        //         });

        //         // Set max available qty
        //         let converted = convert_to_main_and_sub(stock, has_sub_unit, related_by);
        //         main_val = converted.main_qty;
        //         sub_val = converted.sub_qty;
        //         row.find('.main_qty').val(main_val);
        //         row.find('.sub_qty').val(sub_val);
        //     }

        //     let price = parseFloat(row.find('.rate').val()) || 0;
        //     let subTotal = calculate_sub_total(main_val, sub_val, price, related_by, has_sub_unit);

        //     row.find('.sub_total').val(subTotal);

        //     // Recalculate total amount
        //     estimatedAmount();
        // }

        function handle_change(obj) {
            let row = obj.closest('tr');

            let main_val = parseInt(empty_field_check(row.find('.main_qty').val())) || 0;
            let sub_val = parseInt(empty_field_check(row.find('.sub_qty').val())) || 0;

            let related_by = parseInt(row.find('.quantity-input').attr('data-related')) || 1;
            let has_sub_unit = row.find('.has_sub_unit').val();

            // ✅ Check if variation exists
            let variation_select = row.find('select[name="variation_id[]"]');
            let stock = 0;

            if (variation_select.length > 0) {
                // Get stock from selected variation
                let selectedOption = variation_select.find('option:selected');
                let variationStock = parseFloat(selectedOption.attr('stock')) || 0;

                // Convert to smallest unit if sub-unit exists
                stock = has_sub_unit === "true" ? variationStock * related_by : variationStock;
            } else {
                // Fallback: use product stock
                let stockText = row.find('.quantity-input').attr('data-stock');
                let stock_qty = parseStockText(stockText, has_sub_unit, related_by);
                stock = stock_qty * related_by;
            }

            let converted_sub = to_sub_unit(main_val, sub_val, related_by, has_sub_unit);

            if (stock < converted_sub) {
                iziToast.warning({
                    title: "Not Enough Stock for selected variation.",
                    position: "topRight",
                });

                // Set max available qty
                let converted = convert_to_main_and_sub(stock, has_sub_unit, related_by);
                main_val = converted.main_qty;
                sub_val = converted.sub_qty;
                row.find('.main_qty').val(main_val);
                row.find('.sub_qty').val(sub_val);
            }

            let price = parseFloat(row.find('.rate').val()) || 0;
            let subTotal = calculate_sub_total(main_val, sub_val, price, related_by, has_sub_unit);

            row.find('.sub_total').val(subTotal);

            // Recalculate total amount
            estimatedAmount();
        }
        // Qty increase
        $(document).on("click", ".btn-increase", function() {
            let input = $(this).closest(".input-group").find(".main_qty");
            let val = parseFloat(input.val()) || 0;
            input.val(val + 1).trigger("change");
        });

        // Qty decrease
        $(document).on("click", ".btn-decrease", function() {
            let input = $(this).closest(".input-group").find(".main_qty");
            let val = parseFloat(input.val()) || 0;
            if (val > 1) {
                input.val(val - 1).trigger("change");
            } else if (val === 1) {
                // If it's the last item, ask if they want to remove it
                if (confirm("Remove this item from cart?")) {
                    $(this).closest('tr').find('.remove-btn').trigger('click');
                }
            }
        });

        // rate change
        $(document).on('keyup change', '.rate', function(e) {
            handle_change($(this));
            return;
        });
        // rate change
        $(document).on('keyup change', '.main_qty', function(e) {
            handle_change($(this));
            return;
        });

        $(document).on('keyup change', '.product_discount', function(e) {
            let discount = $(this).val();
            let row = $(this).closest('tr');
            let main_qty = parseInt(row.find('.main_qty').val()) || 0;
            let sub_qty = parseInt(row.find('.sub_qty').val()) || 0;
            let unit_price = parseFloat(row.find('.rate').val()) || 0;
            let related_by = parseInt(row.find('.quantity-input').attr('data-related')) || 1;
            let has_sub_unit = row.find('.has_sub_unit').val();

            let sub_total = calculate_sub_total(main_qty, sub_qty, unit_price, related_by, has_sub_unit, discount);
            row.find('.sub_total').val(sub_total);
            estimatedAmount();
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

        $(document).on(
            "keyup change",
            "input[name='delivery_charge']",
            function() {
                totalCalculate();
            });

        function totalCalculate() {
            let discount = $(".discount_amount").val() || 0;
            let estimated_amount = parseFloat(
                $("input[name='estimated_amount']").val()
            ) || 0;
            discount = empty_field_check(discount);

            let delivery_amount = parseFloat(
                $("input[name='delivery_charge']").val()
            ) || 0;

            let discountAmount = 0;
            if ((typeof discount === 'string' || discount instanceof String) && discount.includes("%")) {
                let removed_percent_discount = discount.replace('%', '');
                discount = parseFloat(removed_percent_discount) || 0;
                discountAmount = Math.round($(".estimated_amount").val() * (discount / 100));
            } else {
                discountAmount = parseFloat(discount) || 0;
            }
            let total_amount = estimated_amount - discountAmount + delivery_amount;
            $("#grand_total").text(total_amount.toFixed(2));
            $(".sub_total").text(estimated_amount.toFixed(2));
            $(".discount_amount").text(discountAmount.toFixed(2));
            $(".discount").val(discountAmount.toFixed(2));
            $(".payable_amount").text(total_amount.toFixed(2));
            $("#payable_amount").val(total_amount.toFixed(2));
            $(".delivery_charge").text(delivery_amount.toFixed(2));
            $("#delivery_charge").val(delivery_amount.toFixed(2));
        }

        // ===================order modal===================
        //payment_modal_btn
        $("#payment_modal_btn").on("click", function() {
            //date
            var date = $("#date").val();
            var variation = $("#variation_id").val();
            // if (date == '') {
            //     //Walking Customer Can't to Create a Due
            //     iziToast.warning({
            //         title: "Please select a date.",
            //         position: "topRight",
            //     });
            //     return false;
            // }
            if (variation == '') {
                //Walking Customer Can't to Create a Due
                iziToast.warning({
                    title: "Please select a product variation.",
                    position: "topRight",
                });
                return false;
            }
            //customer_id
            // var customer_id = $("#customer_id").val();
            // if (customer_id == '') {
            //     //Walking Customer Can't to Create a Due
            //     iziToast.warning({
            //         title: "Please select a customer.",
            //         position: "topRight",
            //     });
            //     return false;
            // }
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
            // $("#payment_modal").modal("show");
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
            var pay_point = $(".pay_amount_div")
                .find("input[name=pay_point]")
                .val();

            var due_amount = payable_amount - pay_point - pay_amount;
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

            updateAmounts();
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
            // if (parseFloat(pay_amount) < 0) {
            //     iziToast.warning({
            //         title: "Sorry Below Payment Not Allowed.",
            //         position: "topRight",
            //     });
            //     return false;
            // }
            // if (paid_amount == 0.00 && due_amount == 0.00) {
            //     iziToast.warning({
            //         title: "Please Enter Pay Amount.",
            //         position: "topRight",
            //     });
            //     return false;
            // }
            // if (customer_id == 1 && due_amount != 0.00) {
            //     iziToast.warning({
            //         title: "Walking Customer Can't to Create a Due",
            //         position: "topRight",
            //     });
            //     return false;
            // }
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
