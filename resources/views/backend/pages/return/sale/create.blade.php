@extends('backend.layouts.master')
@section('page-title', __('Invoice Return'))

@push('css')
    <style>
        .invoice-contentbar {
            margin: 0 !important;
            padding: 20px;
            margin-bottom: 60px;
        }

        .cart-container {
            padding-top: 5px !important;
        }

        .table-responsive {
            margin-bottom: 4px !important;
        }

        /* pos footer section start */

        .footerpos {
            display: flex;
            justify-content: flex-end;
            width: 100%;
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
    </style>
@endpush

@section('invoice')
    <div class="invoice-contentbar">
        <div class="row">
            <!-- Start col -->
            <div class="col-md-12">
                <div class="card card_top">
                    <div class="d-flex justify-content-between align-items-center px-4 pt-3 pb-2">
                        <h3 class="text-xl font-black text-slate-800 tracking-tight m-0">{{ __('Return Product') }}</h3>
                        <div class="d-flex align-items-center">
                            <label class="font-weight-bold mb-0 mr-2 text-sm">{{ __('Return Date') }}:</label>
                            <input type="date" name="date" value="{{ date('Y-m-d') }}" class="form-control form-control-sm" style="width: 160px;" required>
                        </div>
                    </div>
                    <form action="{{ route('return.insert') }}" id="" method="POST">
                        @csrf
                        <div class="cart-container">
                            <div class="cart-head">
                                <div class="table-responsive">
                                    <table class="table table-striped text-center">
                                        <thead>
                                            <tr class="header_bg">
                                                <th class="header_style_left" width="17%">{{ __('Product') }}</th>
                                                {{-- <th width="17%">Variation</th> --}}
                                                <th width="35%">{{ __('Quantity') }}</th>
                                                <th width="15%">{{ __('Rate') }}</th>
                                                <th width="15%">{{ __('Discount') }}</th>
                                                <th width="15%">{{ __('Total') }}</th>
                                                <th class="header_style_right">{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody">
                                            @php
                                                $returnProductIds = App\Models\ReturnItem::where(
                                                    'invoice_id',
                                                    $invoice->id,
                                                )->pluck('product_id');
                                                $invoiceItems = App\Models\InvoiceItem::where(
                                                    'invoice_id',
                                                    $invoice->id,
                                                )
                                                    // ->whereNotIn('product_id', $returnProductIds)
                                                    ->where('is_return', 0)
                                                    ->get();
                                            @endphp
                                            <input type="hidden" name="invoice_id" id="invoice_id"
                                                value="{{ $invoice->id }}">
                                            <input type="hidden" name="estimate_amount" id="estimate_amount"
                                                value="{{ $invoice->estimated_amount }}">
                                            {{-- <input type="hidden" name="discountt_amount" id="discountt_amount" value="{{ $invoice->discount_amount }}"> --}}
                                            <input type="hidden" name="customer_id" value="{{ $invoice->customer_id }}">
                                            @forelse ($invoiceItems as $key => $item)
                                                @php
                                                    $product = App\Models\Product::where('id',$item->product_id)->with('unit')->first();
                                                    $total_qty_main = $item->main_qty;
                                                    if ($product && $product->unit && $product->unit->related_value > 0) {
                                                        $total_qty_main += ($item->sub_qty ?? 0) / $product->unit->related_value;
                                                    }
                                                    $purchase_price = $total_qty_main > 0 ? $item->pur_subtotal / $total_qty_main : 0;
                                                @endphp
                                                @if($product->is_service == 0)
                                                <tr>
                                                    <input type="hidden" name="item_id[]" value="{{ $item->id }}">
                                                    <input type="hidden" name="purchase_price[]"
                                                        value="{{ $purchase_price }}">
                                                    <td class="table_data_style_left">
                                                        {{ $item->product?->name }} - {{ $item->product?->barcode }}
                                                        @if ($item->product_variation_id != null)
                                                            ({{ $item->product_variation?->size?->size }}-{{ $item->product_variation?->color?->color }})
                                                        @endif
                                                        <input type="hidden" value="{{ $item->product?->id }}"
                                                            name="product_id[]" />
                                                        <input type="hidden" value="{{ $item->product_variation_id }}"
                                                            name="product_variation_id[]" />
                                                        @if (env('APP_IMEI') == 'yes' && $item->product?->imei == 1)
                                                            <div class="mt-2 text-left">
                                                                <small class="text-danger font-weight-bold">{{ __('Select IMEIs to Return') }}</small>
                                                                <select name="imei[{{ $key }}][]" class="form-control select2 mt-1" multiple>
                                                                    @if($item->imei)
                                                                        @php
                                                                            $imeis = array_filter(array_map('trim', preg_split('/[\r\n,]+/', (string)$item->imei)));
                                                                        @endphp
                                                                        @foreach($imeis as $imei)
                                                                            @if($imei)
                                                                                @php
                                                                                    $repurchased = isImeiRepurchased($item->product_id, $imei, $invoice);
                                                                                @endphp
                                                                                @if($repurchased)
                                                                                    <option value="{{ $imei }}" disabled>{{ $imei }} ({{ __('Repurchased - Cannot Return') }})</option>
                                                                                @else
                                                                                    <option value="{{ $imei }}">{{ $imei }}</option>
                                                                                @endif
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        @else
                                                            <input type="hidden" name="imei[{{ $key }}][]">
                                                        @endif
                                                    </td>
                                                    <td style="width:250px">
                                                        <div class="form-row">
                                                             @if ($item->product->unit->related_unit == null)
                                                                 {{-- ONLY MAIN UNIT --}}
                                                                 <input type="text" class="has_sub_unit" hidden
                                                                     value="false">
                                                                 <label class="ml-2 mr-2"
                                                                     style="padding-top: 5px;">{{ $item->product->unit->name }}:</label>

                                                                 <input type="number"
                                                                     value="{{ $item->actual_main }}"
                                                                     class="form-control col main_qty" name="main_qty[]"
                                                                     data-value="{{ $item->actual_main }}"
                                                                     data-max-main="{{ $item->actual_main }}"
                                                                     data-max-sub="0"
                                                                     data-related="{{ $item->product->unit->related_value }}"
                                                                     onkeydown="return event.keyCode !== 190" min="1" max="{{ $item->actual_main }}">
                                                                 <input type="hidden" name="sub_qty[]"
                                                                     value="0">
                                                             @else
                                                                 {{-- HAS SUB UNIT --}}
                                                                 <input type="text" class="has_sub_unit" hidden
                                                                     value="true">
                                                                 <input type="text" class="conversion" hidden
                                                                     value="{{ $item->product->unit->related_value }}">

                                                                 <label class="mr-1 ml-1"
                                                                     style="padding-top: 5px;">{{ $item->product->unit->name }}:</label>
                                                                 <input type="number"
                                                                     value="{{ $item->actual_main }}"
                                                                     class="form-control col main_qty mr-1" name="main_qty[]"
                                                                     data-value="{{ $item->actual_main }}"
                                                                     data-max-main="{{ $item->actual_main }}"
                                                                     data-max-sub="{{ $item->actual_sub }}"
                                                                     data-related="{{ $item->product->unit->related_value }}"
                                                                     onkeydown="return event.keyCode !== 190" min="0" max="{{ $item->actual_main }}">

                                                                 <label class="mr-1"
                                                                     style="padding-top: 5px;">{{ $item->product->unit->related_unit->name }}:</label>
                                                                 <input type="number"
                                                                     value="{{ $item->actual_sub }}"
                                                                     class="form-control col sub_qty mr-1" name="sub_qty[]"
                                                                     data-max-sub="{{ $item->actual_sub }}"
                                                                     onkeydown="return event.keyCode !== 190" min="0"
                                                                     max="{{ $item->actual_sub }}">
                                                             @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" style="min-width: 100px;"
                                                            class="form-control rate" name="new_rate[]"
                                                            value="{{ $item->rate }}" />
                                                    </td>
                                                    <td>
                                                        <input type="text" style="min-width: 100px;"
                                                            class="form-control product_discount"
                                                            name="product_discount[]" value="{{ $item->product_discount }}"
                                                            data-original-qty="{{ $total_qty_main }}"
                                                            data-original-discount="{{ $item->product_discount }}" />
                                                    </td>
                                                    <td>
                                                        <input type="number" style="min-width: 100px;" readonly
                                                            name="sub_total[]" class="form-control sub_total"
                                                            value="{{ $item->subtotal }}" />
                                                    </td>
                                                    <td class="table_data_style_right">
                                                        <a href="#" class="remove-btn item-index"
                                                            data-value="{{ $item->id }}"><i
                                                                class="fa fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                                @endif
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center text-danger no_data_style">
                                                        {{ __('No Invoice Found') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr class="">
                                                <td colspan="3" class="text-right fw-bold">{{ __('Total') }}</td>
                                                <td colspan="1">
                                                    <input type="number" step="any" name="estimated_amount"
                                                        value="0" class="form-control estimated_amount" readonly>
                                                </td>
                                                <td class="text-right"></td>
                                            </tr>
                                            {{-- Discount --}}
                                            <tr class="">
                                                <td colspan="3" class="text-right fw-bold">{{ __('Discount') }}</td>
                                                <td>
                                                    @php
                                                        $rtn_discount_amount = $invoice->discount_amount;
                                                        if (!is_numeric($rtn_discount_amount) || str_contains($rtn_discount_amount, '%')) {
                                                            $rtn_discount_amount = $invoice->discount;
                                                        }
                                                    @endphp
                                                    <input type="number" name="discount_amount"
                                                        value="{{ $rtn_discount_amount }}"
                                                        class="form-control discount_amount">

                                                    <input type="hidden" class="form-control discount" name="discount"
                                                        placeholder="0%">
                                                </td>
                                                <td class="text-right"></td>
                                            </tr>
                                            <tr class="">
                                                <td colspan="3" class="text-right fw-bold">{{ __('Payment') }}</td>
                                                <td>
                                                    <select class="select2" name="bank_id" required>
                                                        @foreach ($bank_accounts as $bank_account)
                                                            <option value="{{ $bank_account->id }}">
                                                                {{ $bank_account->bank_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="text-right"></td>
                                            <tr class="bg-blue-600 text-white font-black">
                                                <td colspan="3" class="text-right py-3">{{ __('Payable Amount') }}</td>
                                                <td colspan="1" class="text-center">
                                                    <div id="grand_total" class="text-lg">0.00</div>
                                                    <input type="hidden" name="payable_amount" id="payable_amount" value="">
                                                </td>
                                                <td class="text-right"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <footer class="footerpos mt-1 px-1 flex justify-end">
                                    <button class="w-auto border-0 focus:outline-none transition-all duration-300 hover:scale-[1.03] active:scale-[0.98]">
                                        <div class="footerpos_right text-center !rounded-[5px] px-8 py-2.5 flex items-center justify-center gap-2 shadow-sm hover:shadow-md transition-all duration-300 bg-emerald-600 hover:bg-emerald-500">
                                            <input type="hidden" name="return_cus_amount" id=""
                                                value="{{ $item->invoice->total_paid }}">
                                            <i class="feather icon-corner-up-left text-lg"></i>
                                            <span class="text-base font-bold uppercase tracking-wider"> {{ __('Return') }} </span>
                                        </div>
                                    </button>
                                </footer>
                            </div>
                        </div>
                        {{-- @include('backend.pages.invoice.payment-modal') --}}
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <!-- Payment Modal -->

@endsection

@push('js')
    <script>
        // Page Load
        var empty = '';
        // $('body').addClass('toggle-menu');
        $('#product_search').blur();

        var localData = localStorage.getItem('rtn-sale-items') ? JSON.parse(localStorage.getItem('rtn-sale-items')) : [];

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

        function calculate_sub_total(main_qty, sub_qty, unit_price, related_by, has_sub_unit, discount = 0) {
            var sub_unit_price = 0;

            if (has_sub_unit == "true" && related_by != 0) {
                sub_unit_price = parseFloat(unit_price / related_by);
            }
            let main_price = main_qty * unit_price;
            let sub_price = sub_qty * sub_unit_price;
            let total = main_price + sub_price;

            let discountAmount = 0;
            discount = (discount || '0').toString();
            if (discount.includes("%")) {
                let removed_percent_discount = discount.replace('%', '');
                let percent = parseFloat(removed_percent_discount) || 0;
                discountAmount = total * (percent / 100);
            } else {
                discountAmount = parseFloat(discount) || 0;
            }

            return parseFloat(total - discountAmount).toFixed(2);
        }

        // Manage Addition and Removal from LocalStorage
        function pExist(pid) {
            let ldata = localStorage.getItem('rtn-sale-items') ? JSON.parse(localStorage.getItem('rtn-sale-items')) : [];
            return ldata.some(function(el) {
                return el.product.id === pid
            });
        }

        function storedata(data) {
            if (localStorage.getItem('rtn-sale-items') != null) {
                cartList = JSON.parse(localStorage.getItem('rtn-sale-items'))
                cartList.push(data);
            } else {
                cartList.push(data);
            }
            localStorage.setItem('rtn-sale-items', JSON.stringify(cartList));
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
                let url = "{{ route('product-search') }}";
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
                let url = "{{ route('search-product-id', 'my_id') }}".replace('my_id', ui.item.id);
                $.get(url, (data) => {
                    // console.log(product);
                    // check stock
                    if (data.stock_qty <= 0) {
                        iziToast.warning({
                            title: "{{ __('This product is Stock out. Please Purchases the Product.') }}",
                            position: "topRight",
                        });
                        return false;
                    }

                    if (pExist(data.product.id) == true) {
                        iziToast.warning({
                            title: "{{ __('Please Increase the quantity.') }}",
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

        // Manage Cart items
        $(document).on('click', '.product', function() {
            let productId = $(this).attr('data-value');
            let url = "{{ route('pos-product-id', 'my_id') }}".replace('my_id', productId);
            $.get(url, data => {
                // check stock
                if (data.stock_qty <= 0) {
                    iziToast.warning({
                        title: "{{ __('This product is Stock out. Please Purchases the Product.') }}",
                        position: "topRight",
                    });
                    return false;
                }

                if (pExist(data.product.id) == true) {
                    iziToast.warning({
                        title: "{{ __('Please Increase the quantity.') }}",
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
            localStorage.removeItem('rtn-sale-items');
            localStorage.setItem('rtn-sale-items', JSON.stringify(localData))
            $(this).parents('tr').remove();
            estimatedAmount();
        });

        $("#clearList").on('click', function() {
            localStorage.removeItem('rtn-sale-items');
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
                                    <option value="">{{ __('Select Variation') }}</option>`;

                $.each(data.variations, function(index, value) {
                    variation_data += "<option stock=" + value.stock + " value=\"" +
                        value.id + "\">" + value.name + " - " + value.stock + "</option>";
                });

                variation_data += '</select>';
            } else {
                variation_data =
                    `<input type="text" class="has_size" data-has-size="false" hidden>`;
            }

            if (data.product.unit.related_unit == null) {
                // alert("NO SUB UNIT");
                quantity_data =
                    `<input type="text" class="has_sub_unit" hidden value="false">
                        <label class="ml-2 mr-2" style="padding-top: 5px;">${data.product.unit.name}:</label>
                        <input type="number" value="1" class="form-control col main_qty" name="main_qty[]" 
                        data-value="${data.stock_qty}" data-related="${data.product.unit.related_value}" 
                        onkeydown="return event.keyCode !== 190" min="0">`;
            } else {
                // alert("SUB UNIT");
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
                    <td>
                    ${data.product.name + " - " + data.product.barcode}
                    
                    <input type="hidden" value="${data.product.id}" name="product_id[]" />
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
                    <input type="number" style="min-width: 100px;" value="${data.product.selling_price}" class="form-control rate" name="new_rate[]" />
                    </td>
                    <td>
                    <input type="number" style="min-width: 100px;" readonly name="sub_total[]" class="form-control sub_total" value="${data.product.selling_price}"/>
                    </td>
                    <td>
                    <a href="#" class="remove-btn item-index" data-value="${index}"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            `;
            $("#tbody").prepend(dom);
        }

        function handle_change(obj) {
            var main_val = parseInt(empty_field_check(obj.parents('tr').find('.main_qty').val())) || 0;
            var sub_val = parseInt(empty_field_check(obj.parents('tr').find('.sub_qty').val())) || 0;
            let related_by = parseInt(empty_field_check(obj.parents('tr').find('.main_qty').attr('data-related'))) || 1;
            var has_sub_unit = obj.parents('tr').find('.has_sub_unit').val();
            let converted_sub = to_sub_unit(main_val, sub_val, related_by, has_sub_unit);

            let max_main = parseInt(obj.parents('tr').find('.main_qty').attr('data-max-main')) || 0;
            let max_sub  = parseInt(obj.parents('tr').find('.main_qty').attr('data-max-sub')) || 0;
            let max_allowed_sub = to_sub_unit(max_main, max_sub, related_by, has_sub_unit);

            let discount_input = obj.parents('tr').find('.product_discount');
            let orig_qty = parseFloat(discount_input.attr('data-original-qty')) || 1;
            let orig_discount = parseFloat(discount_input.attr('data-original-discount')) || 0;
            let orig_qty_normalized = has_sub_unit == 'true' ? orig_qty * related_by : orig_qty;
            
            // Only update discount proportionally if the user is changing the quantity
            if (obj.hasClass('main_qty') || obj.hasClass('sub_qty')) {
                let proportional_discount = (converted_sub / orig_qty_normalized) * orig_discount;
                discount_input.val(proportional_discount.toFixed(2));
            }

            if (converted_sub > max_allowed_sub) {
                // Limit to maximum sold quantity remaining for this invoice item
                var converted = convert_to_main_and_sub(max_allowed_sub, has_sub_unit, related_by);
                obj.parents('tr').find('.main_qty').val(converted.main_qty);
                if (has_sub_unit == "true") {
                    obj.parents('tr').find('.sub_qty').val(converted.sub_qty);
                }

                let price = parseFloat(obj.parents('tr').find('.rate').val()) || 0;
                let discount = discount_input.val() || '0';

                let subTotal = calculate_sub_total(converted.main_qty, converted.sub_qty, price, related_by, has_sub_unit, discount);

                obj.parents('tr').find('.sub_total').val(subTotal);
                estimatedAmount();

                iziToast.warning({
                    title: "{{ __('Return quantity cannot exceed remaining sold quantity.') }}",
                    position: "topRight",
                });
            } else {
                let price = parseFloat(obj.parents('tr').find('.rate').val()) || 0;
                let discount = discount_input.val() || '0';
                let subTotal = calculate_sub_total(main_val, sub_val, price, related_by, has_sub_unit, discount);
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

        // product_discount change
        $(document).on('keyup change', '.product_discount', function(e) {
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
        // totalCalculate();
        function totalCalculate() {

            let discount = parseFloat($(".discount_amount").val()) || 0;
            let estimated_amount = parseFloat(
                $("input[name='estimated_amount']").val()
            ) || 0;

            let total_amount = estimated_amount - discount;

            $("#grand_total").text(total_amount.toFixed(2));
            $(".sub_total").text(estimated_amount.toFixed(2));
            $(".discount_amount").val(discount.toFixed(2));
            $(".discount").val(discount.toFixed(2));
            $(".payable_amount").text(total_amount.toFixed(2));
            let pay = $("#payable_amount").val(total_amount.toFixed(2));
        }

        //discount amoutn 

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
            //customer_id
            var customer_id = $("#customer_id").val();
            if (customer_id == '') {
                //Walking Customer Can't to Create a Due
                iziToast.warning({
                    title: "{{ __('Please select a customer.') }}",
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
        // $(".pay_amount").on("keyup change", function() {
        //     var pay_amount = $(this).val();
        //     if (pay_amount == "") {
        //         pay_amount = '0.00';
        //     }
        //     pay_amount = parseFloat(pay_amount);
        //     pay_amount = pay_amount.toFixed(2);
        //     var payable_amount = $("#payment_modal")
        //         .find("input[name=payable_amount]")
        //         .val();

        //     var due_amount = payable_amount - pay_amount;
        //     due_amount = parseFloat(due_amount);
        //     due_amount = due_amount.toFixed(2);
        //     var balance = '0.00';
        //     if (due_amount < 0) {
        //         balance = Math.abs(due_amount);
        //         due_amount = '0.00';
        //     }

        //     $("#payment_modal").find(".due_amount").text(due_amount);
        //     $("#payment_modal").find("input[name=due_amount]").val(due_amount);

        //     $("#payment_modal").find(".balance").text(balance);
        //     $("#payment_modal").find("input[name=balance]").val(balance);

        //     $("#payment_modal").find(".paid_amount").text(pay_amount);
        //     $("#payment_modal").find("input[name=paid_amount]").val(pay_amount);
        // });

        //id="checkout"
        // $("#checkout").on("click", function() {
        //     var customer_id = $("#customer_id").val();
        //     var payable_amount = $("#payable_amount").val();
        //     var pay_amount = $(".pay_amount").val();
        //     var paid_amount = $("#paid_amount").val();
        //     var due_amount = $("#due_amount").val();
        //     if (parseFloat(pay_amount) > parseFloat(payable_amount)) {
        //         iziToast.warning({
        //             title: "Sorry Over Payment Not Allowed.",
        //             position: "topRight",
        //         });
        //         return false;
        //     }
        //     if (parseFloat(pay_amount) < 0) {
        //         iziToast.warning({
        //             title: "Sorry Below Payment Not Allowed.",
        //             position: "topRight",
        //         });
        //         return false;
        //     }
        //     if (paid_amount == 0.00 && due_amount == 0.00) {
        //         iziToast.warning({
        //             title: "Please Enter Pay Amount.",
        //             position: "topRight",
        //         });
        //         return false;
        //     }
        //     if (customer_id == 1 && due_amount != 0.00) {
        //         iziToast.warning({
        //             title: "Walking Customer Can't to Create a Due",
        //             position: "topRight",
        //         });
        //         return false;
        //     }
        //     localStorage.clear();
        //     $("#payment_form").submit();
        // });

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
