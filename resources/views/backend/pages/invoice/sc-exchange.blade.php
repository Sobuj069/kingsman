@extends('backend.layouts.master')
@section('page-title', 'Invoice Exchange')

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
            <div class="col-md-12">
                <div class="card card_top">
                    <form action="{{ route('invoice.ex.update') }}" id="" method="POST">
                        @csrf
                        <div class="cart-container">
                            <div class="cart-head">
                                <div class="input-group text-dark">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text barcod_style" id="basic-addon1">Selling Date:</span>
                                    </div>
                                    <input type="date" class="form-control" id="" readonly
                                        value="{{ $invoice->date }}" name="date">
                                </div>
                                <div class="row align-items-center ecommerce-sortby">
                                </div>
                                {{-- <hr> --}}
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend ">
                                        <span class="input-group-text barcod_style" id="basic-addon1"><i
                                                class="fa fa-barcode"></i></span>
                                    </div>
                                    <input type="text" id="product_search" class="form-control"
                                        placeholder="Type & Barcode" aria-label="Type & Barcode"
                                        data-price="{{ $invoice->selling_type }}" onkeydown="return event.keyCode !== 13"
                                        autocomplete="off">
                                </div>
                            </div>
                            <div class="cart-head">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped text-center">
                                        <thead class="header_bg text-white">
                                            <tr class="">
                                                <th width="17%" class="header_style_left">Product</th>
                                                <th width="17%">Size-Color</th>
                                                <th width="45%">Quantity</th>
                                                <th width="19%">Rate</th>
                                                <th width="19%">Total</th>
                                                <th class="header_style_right">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody">
                                            @php
                                                $invoiceItem = App\Models\InvoiceItem::where(
                                                    'invoice_id',
                                                    $invoice->id,
                                                )->get();
                                            @endphp
                                            <input type="hidden" name="id" value="{{ $invoice->id }}">
                                            @forelse ($invoiceItem as $item)
                                                <tr>
                                                    <input type="hidden" name="item_id[]" value="{{ $item->id }}">
                                                        {{ $item->product?->name }} - {{ $item->product?->barcode }}
                                                        <input type="hidden" value="{{ $item->product?->id }}"
                                                            name="product_id[]" />
                                                        @if(env('APP_IMEI') == 'yes')
                                                            @if($item->product->imei == 1)
                                                                <div class="mt-2 text-left">
                                                                    <small class="text-danger font-weight-bold">{{ __('IMEIs (one per line)') }}</small>
                                                                    <textarea name="imei[]" class="form-control imei_input mt-1" 
                                                                        placeholder="{{ __('Enter IMEI') }}" 
                                                                        style="height: 60px; font-size: 11px;">{{ $item->imei }}</textarea>
                                                                </div>
                                                            @else
                                                                <input type="hidden" name="imei[]">
                                                            @endif
                                                        @else
                                                            <input type="hidden" name="imei[]">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $variations = App\Models\ProductVariation::where(
                                                                'product_id',
                                                                $item->product?->id,
                                                            )->get();
                                                        @endphp
                                                        @if ($variations->count() > 0)
                                                            <input type="text" class="has_size" data-has-size="true"
                                                                hidden>
                                                            <select name="variation[]" id=""
                                                                class="form-control size" required>
                                                                @foreach ($variations as $data)
                                                                    <option value="{{ $data->id }}"
                                                                        {{ $data->id == $item->product_variation_id ? 'selected' : '' }}>
                                                                        {{ $data->size->size ?? '' }}-{{ $data->color->color ?? '' }}-{{ variation_stock($data->id) }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        @else
                                                            <input type="text" class="has_size" data-has-size="false"
                                                                hidden>
                                                            <input type="hidden" name="variation[]" value="">
                                                        @endif
                                                    </td>
                                                    <td style="width:250px">
                                                        @if ($item->product->is_service == 0)
                                                            <div class="form-row">
                                                                @if ($item->product->unit->related_unit == null)
                                                                    {{-- ONLY MAIN UNIT --}}
                                                                    <input type="text" class="has_sub_unit" hidden
                                                                        value="false">
                                                                    <label class="ml-2 mt-2  mr-2"
                                                                        style="padding-top: 5px;">{{ $item->product->unit->name }}:</label>
                                                                    <input type="number" value="{{ $item->main_qty }}"
                                                                        class="form-control col main_qty" name="main_qty[]"
                                                                        data-value="{{ product_stock($item->product) }}"
                                                                        data-related="{{ $item->product->unit->related_value }}"
                                                                        onkeydown="return event.keyCode !== 190"
                                                                        min="1">
                                                                    <input type="hidden" value="{{ $item->sub_qty }}"
                                                                        class="form-control col sub_qty mr-1"
                                                                        name="sub_qty[]"
                                                                        onkeydown="return event.keyCode !== 190"
                                                                        min="0"
                                                                        max="{{ $item->product->unit->related_value - 1 }}">
                                                                @else
                                                                    {{-- HAS SUB UNIT --}}
                                                                    <input type="text" class="has_sub_unit" hidden
                                                                        value="true">
                                                                    <input type="text" class="conversion" hidden
                                                                        value="{{ $item->product->unit->related_value }}">
                                                                    <label class="mr-1 ml-1"
                                                                        style="padding-top: 5px;">{{ $item->product->unit->name }}:</label>
                                                                    <input type="text" value="{{ $item->main_qty }}"
                                                                        class="form-control col main_qty mr-1"
                                                                        name="main_qty[]"
                                                                        data-value="{{ product_stock($item->product) }}"
                                                                        data-related="{{ $item->product->unit->related_value }}"
                                                                        onkeydown="return event.keyCode !== 190"
                                                                        min="0">
                                                                    <label hidden class="mr-1"
                                                                        style="padding-top: 5px;">{{ $item->product->unit->related_unit->name }}:</label>
                                                                    <input type="hidden" value="{{ $item->sub_qty }}"
                                                                        class="form-control col sub_qty mr-1"
                                                                        name="sub_qty[]"
                                                                        onkeydown="return event.keyCode !== 190"
                                                                        min="0"
                                                                        max="{{ $item->product->unit->related_value - 1 }}">
                                                                @endif
                                                            </div>
                                                        @else
                                                            <input type="number" value="{{ $item->main_qty }}"
                                                                class="form-control col main_qty" name="main_qty[]"
                                                                data-value="{{ product_stock($item->product) }}"
                                                                onkeydown="return event.keyCode !== 190" min="1">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <input type="number" style="min-width: 100px;"
                                                            class="form-control rate" name="rate[]"
                                                            value="{{ $item->rate }}" />
                                                    </td>
                                                    <td>
                                                        <input type="number" style="min-width: 100px;" readonly
                                                            name="sub_total[]" class="form-control sub_total"
                                                            value="{{ $item->subtotal }}" />
                                                    </td>
                                                    <td class="table_data_style_right">
                                                        <a href="#" class="remove-btn item-index"
                                                            data-value="${index}"><i
                                                                class="fa fa-undo text-danger"></i></a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center">No Invoice Found</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-light-primary">
                                                <td colspan="4" class="text-right fw-bold">Total</td>
                                                <td colspan="1">
                                                    <input type="number" step="any" name="estimated_amount"
                                                        value="0" class="form-control estimated_amount" readonly>
                                                </td>
                                                <td class="text-right"></td>
                                            </tr>
                                            {{-- Discount --}}
                                            <tr class="bg-light-primary">
                                                <td colspan="4" class="text-right fw-bold">Discount</td>
                                                <td>
                                                    <input type="text" class="form-control discount_amount" readonly
                                                        name="discount_amount" placeholder="0%"
                                                        value="{{ $item->invoice->discount == 0.0 ? '0' : $item->invoice->discount }}">

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
                                        <input type="hidden" name="total_paid" id=""
                                            value="{{ $item->invoice->total_paid }}">
                                        <input type="hidden" name="customer_id" id=""
                                            value="{{ $item->invoice->customer_id }}">
                                        <input type="hidden" name="payable_amount" id="payable_amount" value="">
                                    </div>
                                    <button class="border-0">
                                        <div class="footerpos_right text-center" id="checkout">
                                            <div class="text-center">Update</div>
                                        </div>
                                    </button>
                                </footer>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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

        var localData = localStorage.getItem('pos-items') ? JSON.parse(localStorage.getItem('pos-items')) : [];

        function showList() {
            if (localData.length <= 0) {
                $("#tbody");
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

        // Search Product
        $("#product_search").autocomplete({
            source: function(req, res) {
                let url = "{{ route('sc-product-search') }}";
                $.get(url, {
                    req: req.term
                }, (data) => {
                    res($.map(data, function(item) {
                        return {
                            id: item.id,
                            value: item.name + " " + item.barcode,
                            price: item.selling_price
                        }
                    })); // end res

                });
            },
            select: function(event, ui) {

                $(this).val(ui.item.value);
                $("#search_product_id").val(ui.item.id);
                let url = "{{ route('sc-search-product-id', 'my_id') }}".replace('my_id', ui.item.id);
                $.get(url, (data) => {
                    // check stock
                    if (data.stock_qty <= 0) {
                        iziToast.warning({
                            title: "This product is Stock out. Please Purchases the Product.",
                            position: "topRight",
                        });
                        return false;
                    }


                    // if (pExist(data.product.id) == true) {
                    //     iziToast.warning({
                    //         title: "Please Increase the quantity.",
                    //         position: "topRight",
                    //     });
                    // } else {
                    // }
                    addProductToCard(data);

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

        $("#clearList").on('click', function() {
            localStorage.removeItem('pos-items');
            $("#tbody").html(empty);
            estimatedAmount();
        });



        function domPrepend(data = null, index = null) {
            var name = data.product.name;
            var quantity_data = '';
            var variation_data = ``;

            if (data.variations.length > 0) {
                variation_data +=
                    `<input type="text" class="has_size" data-has-size="true" hidden>
                            <select name="variation[]" id="" class="form-control size" required>
                                <option value="">Select Variation</option>`;

                $.each(data.variations, function(index, value) {
                    variation_data += "<option stock=" + value.stock + " value=\"" +
                        value.id + "\">" + value.size + " - " + value.color + " - " + value.stock + "</option>";
                });

                variation_data += '</select>';
            } else {
                variation_data =
                    `<input type="text" class="has_size" data-has-size="false" hidden>`;
            }


            if (data.product.unit.related_unit == null) {
                quantity_data =
                    `<input type="text" class="has_sub_unit" hidden value="false">
                    <label class="ml-2 mr-2" style="padding-top: 5px;">${data.product.unit.name}:</label>
                    <input type="number" value="1" class="form-control col main_qty" name="main_qty[]" 
                    data-value="${data.stock_qty}" data-related="${data.product.unit.related_value}" 
                    onkeydown="return event.keyCode !== 190" min="0">`;
            } else {
                quantity_data =
                    `<input type="text" class="has_sub_unit" hidden value="true">
                    <input type="text" class="conversion" hidden value="${data.product.unit.related_value}">
                    <label class="mr-1 ml-1" style="padding-top: 5px;">${data.product.unit.name}:</label>
                    <input type="number" value="1" class="form-control col main_qty mr-1" name="main_qty[]" 
                    data-value="${data.stock_qty}" data-related="${data.product.unit.related_value}" 
                    onkeydown="return event.keyCode !== 190" min="0">

                    <label class="mr-1" style="padding-top: 5px;">${data.product.unit.related_unit.name}:</label>
                    <input type="number" value="0" class="form-control col sub_qty mr-1" name="sub_qty[]"  
                    onkeydown="return event.keyCode !== 190" min="0" max="${data.product.unit.related_value-1}">`;
            }

            let dom = `
            <tr id="tbody_tr">
                <td class="table_data_style_left" style="min-width: 100px;">
                ${data.product.name + " - " + data.product.barcode }
                <input type="hidden" class="name" value="${name.replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, '')}" name="name[]" />
                <input type="hidden" value="${data.product.id}" name="product_id[]" />
                @if(env('APP_IMEI') == 'yes')
                    ${data.product.imei == 1 ? `
                        <div class="mt-2 text-left">
                            <small class="text-danger font-weight-bold">{{ __('Enter IMEI (one per line)') }}</small>
                            <textarea name="imei[]" class="form-control imei_input mt-1" 
                                placeholder="{{ __('Enter IMEI') }}" 
                                style="height: 60px; font-size: 11px;"></textarea>
                        </div>
                    ` : `<input type="hidden" name="imei[]">`}
                @else
                    <input type="hidden" name="imei[]">
                @endif
                </td>
                <td style="min-width: 120px;">
                    ${variation_data}
                </td>
                <td>
                    <div class="form-row" style="min-width: 100px;">
                        ${quantity_data}
                    </div>
                </td>
                <td>
                <input type="number" style="min-width: 100px;" value="${data.product.selling_price}" class="form-control rate" name="rate[]" />
                </td>
                <td>
                <input type="number" style="min-width: 100px;" readonly name="sub_total[]" class="form-control sub_total" value="${data.product.selling_price}"/>
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
            let stock = obj.parents('tr').find('.main_qty').attr('data-value');

            // alert(converted_sub);

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
            let pay = $("#payable_amount").val(total_amount.toFixed(2));
        }

        $("#checkout").on("click", function() {
            var imeiError = false;
            $(".imei_input").each(function() {
                var row = $(this).closest('tr');
                var qty = parseInt(row.find(".main_qty").val()) || 0;
                var imeiText = $(this).val().trim();
                var imeiList = imeiText ? imeiText.split('\n').filter(x => x.trim() !== "") : [];
                
                if (qty > 0 && imeiList.length !== qty) {
                    iziToast.warning({
                        title: "{{ __('IMEI Required') }}",
                        message: "{{ __('Please enter exactly') }} " + qty + " {{ __('IMEIs for this product. Current: ') }}" + imeiList.length,
                        position: "topRight",
                    });
                    imeiError = true;
                    return false;
                }
            });

            if (imeiError) return false;

            localStorage.clear();
            $(this).closest('form').submit();
        });
    </script>
@endpush
