@extends('backend.layouts.master')
@section('page-title', 'Invoice Edit & Exchange')

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
            text-align: center;
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
            /* border-top-right-radius: 35px;
                border-bottom-right-radius: 35px; */
            border-radius: 35px;
            text-align: center;
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
        }); // End of $(document).ready
    </style>
@endpush

@section('invoice')
    <div class="invoice-contentbar">
        <div class="row">
            <!-- Start col -->
            <div class="col-md-10 offset-1">
                <div class="card card_top">
                    <form action="{{ route('invoice.up', $invoice->id) }}" id="payment_form" method="POST">
                        @csrf
                        <div class="cart-container">
                            <div class="cart-head">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text barcod_style" id="basic-addon1"><i class="fa fa-barcode"></i></span>
                                    </div>
                                    <input type="text" id="product_search" class="form-control" placeholder="Search Product by Name or Barcode">
                                    <input type="hidden" id="search_product_id">
                                </div>
                                <div class="row align-items-center">
                                    <div class="col-md-4 mb-3">
                                        <label style="font-weight: 600; font-size: 11px; margin-bottom: 4px; color: #475569;">{{ __('Invoice Date') }}</label>
                                        <input type="date" class="form-control" id="date" value="{{ date('Y-m-d') }}" name="date" max="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label style="font-weight: 600; font-size: 11px; margin-bottom: 4px; color: #475569;">{{ __('Customer Name') }}</label>
                                        <input type="text" class="form-control" readonly value="{{ $invoice->customer?->name ?? 'Walk-in Customer' }} {{ $invoice->customer?->phone ? '('.$invoice->customer->phone.')' : '' }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label style="font-weight: 600; font-size: 11px; margin-bottom: 4px; color: #dc2626;">{{ __('Previous Customer Due') }}</label>
                                        <div style="background: #fef2f2; border: 1px solid #fca5a5; padding: 6px 12px; border-radius: 6px; font-weight: 700; font-size: 14px; color: #b91c1c; display: flex; justify-content: space-between; align-items: center;">
                                            <span>৳ {{ number_format($previousDue ?? 0, 2) }}</span>
                                            <span style="font-size: 10px; color: #64748b; font-weight: 600;">(Total: ৳ {{ number_format($totalCustomerDue ?? 0, 2) }})</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="cart-head">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped text-center">
                                        <thead>
                                            <tr class="header_bg">
                                                <th class="header_style_left" width="5%">#Sl</th>
                                                <th width="15%">Product</th>
                                                <th width="30%">Quantity</th>
                                                <th width="15%">Rate</th>
                                                <th width="10%">Discount</th>
                                                <th width="15%">Total</th>
                                                <th class="header_style_right" width="5%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbody">
                                            @foreach ($invoiceItem as $key => $item)
                                                @php
                                                    $factor = ($item->product->unit && $item->product->unit->related_value) ? $item->product->unit->related_value : 1;
                                                    $sold_qty_sub = ($item->main_qty * $factor) + ($item->sub_qty ?? 0);
                                                    $max_available_sub = product_fake_stock_val($item->product) + $sold_qty_sub;
                                                @endphp
                                                <input type="hidden" name="itemId[]" value="{{ $item->id }}">
                                                <tr>
                                                    <td class="table_data_style_left">{{ ++$key }}</td>
                                                    <td style="">
                                                        {{ $item->product->name }}
                                                        <input type="hidden" value="{{ $item->product_id }}"
                                                            name="product_id[]" />
                                                        
                                                        @php
                                                            $variations = $item->product->product_variations()
                                                                ->leftJoin('product_sizes', 'product_variations.size_id', '=', 'product_sizes.id')
                                                                ->select('product_variations.*')
                                                                ->orderBy('product_sizes.size', 'asc')
                                                                ->get();
                                                        @endphp
                                                        @if($variations && count($variations) > 0)
                                                            <div class="mt-1">
                                                                <select name="variation_id[]" class="form-control size" required>
                                                                    <option value="">{{ __('Select Variation') }}</option>
                                                                    @foreach($variations as $variation)
                                                                        @php
                                                                            $stock = variation_stock($variation->id);
                                                                            if ($item->product_variation_id == $variation->id) {
                                                                                $stock += $sold_qty_sub;
                                                                            }
                                                                            $isOutOfStock = $stock <= 0;
                                                                        @endphp
                                                                        <option value="{{ $variation->id }}" stock="{{ $stock }}"
                                                                            {{ $item->product_variation_id == $variation->id ? 'selected' : '' }}
                                                                            {{ ($isOutOfStock && $item->product_variation_id != $variation->id) ? 'disabled' : '' }}>
                                                                            {{ $variation->size->size ?? '' }} - {{ $variation->color->color ?? '' }} - Qty: {{ $stock }}{{ ($isOutOfStock && $item->product_variation_id != $variation->id) ? ' (Out of Stock)' : '' }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        @else
                                                            <input type="hidden" name="variation_id[]">
                                                        @endif
                                                        @if(env('APP_IMEI') == 'yes')
                                                            @if($item->product->imei == 1)
                                                                <div class="mt-2 text-left">
                                                                    <button type="button" class="btn btn-sm btn-info btn-enter-invoice-imei" 
                                                                            data-product-id="{{ $item->product_id }}"
                                                                            data-product-name="{{ $item->product->name }}"
                                                                            style="padding: 3px 8px; font-size: 11px;">
                                                                        <i class="fa fa-barcode"></i> Add IMEI (<span class="imei-count">{{ count(array_filter(array_map('trim', explode("\n", $item->imei)))) }}</span>)
                                                                    </button>
                                                                    <textarea name="imei[]" class="imei_input imei-hidden-input d-none">{{ $item->imei }}</textarea>
                                                                </div>
                                                            @else
                                                                <input type="hidden" name="imei[]">
                                                            @endif
                                                        @else
                                                            <input type="hidden" name="imei[]">
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center justify-content-center" style="min-width: 150px; flex-wrap: nowrap;">
                                                            @if ($item->product->is_service == 0)
                                                                @if ($item->product->unit->related_unit == null)
                                                                    <input type="text" class="has_sub_unit" hidden
                                                                        value="false">
                                                                    <label class="ml-2 mr-2"
                                                                        style="padding-top: 5px; white-space: nowrap;">{{ $item->product->unit->name }}:</label>
                                                                    <input type="number" value="{{ $item->main_qty }}"
                                                                        class="form-control main_qty" style="width: 80px;" name="main_qty[]"
                                                                        data-value="{{ $max_available_sub }}"
                                                                        data-related="{{ $item->product->unit->related_value }}"
                                                                        onkeydown="return event.keyCode !== 190"
                                                                        min="0" @if (env('APP_IMEI') == 'yes' && $item->product->imei == 1) readonly @endif>
                                                                @else
                                                                    <input type="text" class="has_sub_unit" hidden
                                                                        value="true">
                                                                    <input type="text" class="conversion" hidden
                                                                        value="${data.product.unit.related_value}">
                                                                    <label class="mr-1 ml-1"
                                                                        style="padding-top: 5px; white-space: nowrap;">{{ $item->product->unit->name }}:</label>
                                                                    <input type="number" value="{{ $item->main_qty }}"
                                                                        class="form-control main_qty mr-1" style="width: 70px;"
                                                                        name="main_qty[]"
                                                                        data-value="{{ $max_available_sub }}"
                                                                        data-related="{{ $item->product->unit->related_value }}"
                                                                        onkeydown="return event.keyCode !== 190"
                                                                        min="0" @if (env('APP_IMEI') == 'yes' && $item->product->imei == 1) readonly @endif>

                                                                    <label class="mr-1"
                                                                        style="padding-top: 5px; white-space: nowrap;">{{ $item->product->unit->related_unit->name }}:</label>
                                                                    <input type="number" value="{{ $item->sub_qty }}"
                                                                        class="form-control sub_qty mr-1" style="width: 70px;"
                                                                        name="sub_qty[]"
                                                                        onkeydown="return event.keyCode !== 190"
                                                                        min="0" max="" @if (env('APP_IMEI') == 'yes' && $item->product->imei == 1) readonly @endif>
                                                                @endif
                                                            @else
                                                                <label class="ml-2 mr-2"
                                                                    style="padding-top: 5px; white-space: nowrap;">Pcs:</label>
                                                                <input type="number" value="{{ $item->main_qty }}"
                                                                    class="form-control main_qty" style="width: 80px;" name="main_qty[]"
                                                                    data-value="99999999"
                                                                    min="0" @if (env('APP_IMEI') == 'yes' && $item->product->imei == 1) readonly @endif>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="number" value="{{ $item->rate }}"
                                                            class="form-control rate" name="rate[]" />
                                                    </td>
                                                    <td>
                                                        <input type="text" value="{{ $item->product_discount ?? 0 }}"
                                                            class="form-control product_discount" name="product_discount[]" />
                                                    </td>
                                                    <td>
                                                        <input type="number" readonly name="sub_total[]"
                                                            class="form-control sub_total" value="{{ $item->subtotal }}" />
                                                    </td>
                                                    <td class="table_data_style_right text-center">
                                                        <a href="#" class="remove-btn-server"><i class="fa fa-trash text-danger"></i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="bg-light-primary">
                                                <td colspan="5" class="text-right fw-bold">Grand Total</td>
                                                <td colspan="2">
                                                    <input type="number" step="any" name="estimated_amount"
                                                        value="0" class="form-control estimated_amount" readonly>
                                                </td>
                                            </tr>
                                            {{-- Discount --}}
                                            <tr class="bg-light-primary">
                                                <td colspan="5" class="text-right fw-bold">Discount</td>
                                                <td colspan="2">
                                                    @php
                                                        $edit_discount = $invoice->discount;
                                                        if (is_numeric($edit_discount) && ($invoice->discount_amount && (str_contains($invoice->discount_amount, '%') || !is_numeric($invoice->discount_amount)))) {
                                                            $edit_discount = $invoice->discount_amount;
                                                        }
                                                    @endphp
                                                    <input type="text" class="form-control discount_amount"
                                                        name="discount_amount" placeholder="0%"
                                                        value="{{ $edit_discount }}">

                                                    <input type="hidden" class="form-control discount" name="discount">
                                                </td>
                                            </tr>
                                            <tr class="bg-light-primary">
                                                <td colspan="5" class="text-right fw-bold">Total</td>
                                                <td colspan="2">
                                                    <div class="text-center" style="font-size: 20px;" id="grand_total">
                                                    </div>
                                                    <input type="hidden" id="grandTotal" name="total_amount">
                                                </td>
                                            </tr>
                                            <tr class="bg-light-primary">
                                                 <td colspan="5" class="text-right fw-bold" style="color: #2563eb;">Previously Paid on Invoice</td>
                                                 <td colspan="2">
                                                     <input type="text" readonly value="৳ {{ number_format($invoice->total_paid ?? 0, 2) }}" class="form-control text-center font-weight-bold" style="font-size: 15px; color: #1d4ed8; background-color: #eff6ff; border: 1px solid #bfdbfe;">
                                                 </td>
                                             </tr>
                                             <tr class="bg-light-primary">
                                                 <td colspan="5" class="text-right fw-bold">
                                                     Paid Amount 
                                                     <button type="button" class="btn btn-xs btn-outline-primary ml-2 py-0 px-1" id="btn_pay_full" style="font-size: 11px;">Pay Full</button>
                                                     <button type="button" class="btn btn-xs btn-outline-secondary ml-1 py-0 px-1" id="btn_pay_reset" style="font-size: 11px;">Reset</button>
                                                 </td>
                                                 <td colspan="2">
                                                     <input type="number" step="any" id="total_paid" name="total_paid"
                                                         value="{{ $invoice->total_paid }}" class="form-control text-center" style="font-size: 16px;">
                                                 </td>
                                             </tr>
                                             <tr class="bg-light-primary">
                                                  <td colspan="5" class="text-right fw-bold" style="color: #dc2626;">
                                                      Due Amount
                                                      <button type="button" class="btn btn-xs btn-success ml-2 py-1 px-2 font-weight-bold" id="btn_pay_remaining_due" style="font-size: 11px; border-radius: 4px;">
                                                          <i class="fa fa-credit-card"></i> Pay Remaining Due (<span id="due_badge_val">৳0.00</span>)
                                                      </button>
                                                  </td>
                                                  <td colspan="2">
                                                      <input type="number" step="any" id="total_due" name="total_due"
                                                          value="{{ $invoice->total_due }}" class="form-control text-center text-danger font-weight-bold" style="font-size: 16px; background-color: #fef2f2; border: 1px solid #fca5a5;" readonly>
                                                  </td>
                                              </tr>
                                              <tr class="bg-light-primary">
                                                  <td colspan="5" class="text-right fw-bold" style="color: #16a34a;">Change Amount</td>
                                                  <td colspan="2">
                                                      <input type="number" step="any" id="change_amount" name="change_amount"
                                                          value="{{ number_format($invoice->change_amount ?? 0, 2, '.', '') }}" class="form-control text-center text-success font-weight-bold" style="font-size: 16px; background-color: #f0fdf4; border: 1px solid #86efac;" readonly>
                                                  </td>
                                              </tr>
                                            <tr class="bg-light-primary">
                                                <td colspan="5" class="text-right fw-bold" style="color: #dc2626;">Previous Customer Due</td>
                                                <td colspan="2">
                                                    <input type="text" readonly value="৳ {{ number_format($previousDue ?? 0, 2) }}" class="form-control text-center text-danger font-weight-bold" style="font-size: 15px; background-color: #fef2f2; border: 1px solid #fca5a5;">
                                                </td>
                                            </tr>
                                            <input type="hidden" name="customer_id"
                                                value="{{ $invoice->customer_id }}">
                                        </tfoot>
                                    </table>
                                </div>
                                <footer class="footerpos">
                                    <div></div>
                                    <div class="footerpos_right">
                                        <button type="button" id="update_btn" class="border-0" style="background: #00a65a;">
                                            <div class="text-center"> Update </div>
                                        </button>
                                    </div>
                                </footer>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- End col -->
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('update_btn').addEventListener('click', function(e) {
                var imeiError = false;
                var imeiInputs = document.querySelectorAll('.imei_input');
                
                for (var i = 0; i < imeiInputs.length; i++) {
                    var input = imeiInputs[i];
                    var row = input.closest('tr');
                    var qtyInput = row.querySelector('.main_qty');
                    var qty = parseInt(qtyInput.value) || 0;
                    var imeiText = input.value.trim();
                    imeiText = imeiText.replace(/\r/g, '');
                    var imeiList = imeiText ? imeiText.split(/[,\n]+/).filter(x => x.trim() !== "") : [];
                    
                    if (qty > 0 && imeiList.length !== qty) {
                        iziToast.warning({
                            title: "{{ __('IMEI Required') }}",
                            message: "{{ __('Please enter exactly') }} " + qty + " {{ __('IMEIs for this product. Current: ') }}" + imeiList.length,
                            position: "topRight",
                        });
                        imeiError = true;
                        break;
                    }
                }

                if (!imeiError) {
                    var customer_id = document.querySelector("input[name='customer_id']").value;
                    var total_amount = parseFloat(document.getElementById("grandTotal").value) || 0;
                    var paid_amount = parseFloat(document.getElementById("total_paid").value) || 0;
                    var due_amount = total_amount - paid_amount;
                    if (customer_id == 1 && due_amount > 0.01) {
                        iziToast.warning({
                            title: "{{ __('Walk-in customer cannot have a due amount!') }}",
                            message: "{{ __('Please pay the full amount.') }}",
                            position: "topRight",
                        });
                        return false;
                    }
                    document.getElementById('payment_form').submit();
                }
            });
        });
    </script>
    <!-- Payment Modal -->


@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Page Load
            var empty = '';
            $('#product_search').blur();

            estimatedAmount();

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
            var main_price = main_qty * unit_price;
            var sub_price = sub_qty * sub_unit_price;
            var total = main_price + sub_price;

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

        // Manage Addition and Removal from DOM
        function pExist(pid) {
            let exists = false;
            $("input[name='product_id[]']").each(function() {
                if ($(this).val() == pid) {
                    exists = true;
                }
            });
            return exists;
        }

        function addProductToCard(data) {
            var x = Date.now(); // use timestamp as unique index
            domPrepend(data, x);
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
                            value: item.name + (item.barcode ? " " + item.barcode : ""),
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
                    if (data.product.is_service == 0 && data.pure_stock <= 0) {
                        iziToast.warning({
                            title: "Out of stock this product.",
                            position: "topRight",
                        });
                        return false;
                    }


                    if (data.variations && data.variations.length > 0) {
                        addProductToCard(data);
                    } else if (pExist(data.product.id) == true) {
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

        // Manage Cart items
        $(document).on('click', '.product', function() {
            let productId = $(this).attr('data-value');
            let url = "{{ route('sc-search-product-id', 'my_id') }}".replace('my_id', productId);
            $.get(url, data => {
                // console.log(data.product.variations);
                // check stock
                if (data.product.is_service == 0 && data.pure_stock <= 0) {
                    iziToast.warning({
                        title: "Out of stock this product.",
                        position: "topRight",
                    });
                    return false;
                }

                if (data.variations && data.variations.length > 0) {
                    addProductToCard(data);
                } else if (pExist(data.product.id) == true) {
                    iziToast.warning({
                        title: "Please Increase the quantity.",
                        position: "topRight",
                    });
                } else {
                    addProductToCard(data);
                }
            }); // Load Data to cart

        });


        $(document).on('click', '.remove-btn', function(e) {
            e.preventDefault();
            $(this).parents('tr').remove();
            estimatedAmount();
        });

        $(document).on('click', '.remove-btn-server', function(e) {
            e.preventDefault();
            $(this).closest('tr').remove();
            estimatedAmount();
        });

        $("#clearList").on('click', function(e) {
            e.preventDefault();
            $("#tbody").html('');
            estimatedAmount();
        });



        function domPrepend(data = null, index = null) {
            var name = data.product.name;
            var quantity_data = '';
            var variation_data = '';

            if (data.variations && data.variations.length > 0) {
                variation_data += `<select name="variation_id[]" class="form-control size" style="min-width:150px" required>
                <option value="">{{ __('Select Variation') }}</option>`;

                $.each(data.variations, function(idx, value) {
                    let isOutOfStock = parseFloat(value.stock) <= 0;
                    variation_data += `<option stock='${value.stock}' value='${value.id}' ${isOutOfStock ? 'disabled' : ''}>
                    ${value.size} - ${value.color} - Qty: ${value.stock}${isOutOfStock ? ' (Out of Stock)' : ''}</option>`;
                });

                variation_data += '</select>';
            } else {
                variation_data = `<input type="hidden" name="variation_id[]">`;
            }

            if (data.product.is_service == 0) {
                if (!data.product.unit || data.product.unit.related_unit == null) {
                    quantity_data =
                        `<input type="text" class="has_sub_unit" hidden value="false">
                            <label class="ml-2 mr-2" style="padding-top: 5px; white-space: nowrap;">${data.product.unit ? data.product.unit.name : 'Unit'}:</label>
                            <input type="number" value="1" class="form-control main_qty" style="width: 80px;" name="main_qty[]" 
                            data-value="${data.pure_stock}" data-related="${data.product.unit ? data.product.unit.related_value : 0}" 
                            onkeydown="return event.keyCode !== 190" min="0">`;
                } else {
                    quantity_data =
                        `<input type="text" class="has_sub_unit" hidden value="true">
                            <input type="text" class="conversion" hidden value="${data.product.unit.related_value}">
                            <label class="mr-1 ml-1" style="padding-top: 5px; white-space: nowrap;">${data.product.unit.name}:</label>
                            <input type="number" value="1" class="form-control main_qty mr-1" style="width: 70px;" name="main_qty[]" 
                            data-value="${data.pure_stock}" data-related="${data.product.unit.related_value}" 
                            onkeydown="return event.keyCode !== 190" min="0">

                            <label class="mr-1" style="padding-top: 5px; white-space: nowrap;">${data.product.unit.related_unit.name}:</label>
                            <input type="number" value="0" class="form-control sub_qty mr-1" style="width: 70px;" name="sub_qty[]"  
                            onkeydown="return event.keyCode !== 190" min="0" max="${data.product.unit.related_value-1}">`;
                }
            } else {
                quantity_data =
                    `<label class="ml-2 mr-2" style="padding-top: 5px; white-space: nowrap;">Pcs:</label>
                     <input type="number" value="1" class="form-control main_qty" style="width: 80px;" name="main_qty[]" data-value="99999999" onkeydown="return event.keyCode !== 190" min="0">`;
            }

            var imei_data = '';
            @if(env('APP_IMEI') == 'yes')
                if (data.product.imei == 1) {
                    imei_data = `
                        <div class="mt-2 text-left">
                            <button type="button" class="btn btn-sm btn-info btn-enter-invoice-imei" 
                                    data-product-id="${data.product.id}"
                                    data-product-name="${data.product.name}"
                                    style="padding: 3px 8px; font-size: 11px;">
                                <i class="fa fa-barcode"></i> Add IMEI (<span class="imei-count">0</span>)
                            </button>
                            <textarea name="imei[]" class="imei_input imei-hidden-input d-none"></textarea>
                        </div>`;
                } else {
                    imei_data = `<input type="hidden" name="imei[]">`;
                }
            @else
                imei_data = `<input type="hidden" name="imei[]">`;
            @endif

            // ${data.product.name + " - Stock (" + data.stock_qty +")"}
            let dom = `
                <tr id="tbody_tr">
                    <td class="table_data_style_left">-</td>
                    <td style="min-width: 100px;">
                    ${data.product.name} ${data.product.barcode ? " - " + data.product.barcode : ""}
                    <div class="mt-1">${variation_data}</div>
                    ${imei_data}
                    <input type="hidden" class="name" value="${name.replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, '')}" name="name[]" />
                    <input type="hidden" value="${data.product.id}" name="product_id[]" />
                    </td>
                    
                    <td>
                        <div class="d-flex align-items-center justify-content-center" style="min-width: 150px; flex-wrap: nowrap;">
                            ${quantity_data}
                        </div>
                    </td>
                    <td>
                    <input type="number" style="min-width: 100px;" value="${data.product.selling_price}" class="form-control rate" name="rate[]" />
                    </td>
                    <td>
                        <input type="text" value="0" class="form-control product_discount" name="product_discount[]" />
                    </td>
                    <td>
                    <input type="number" style="min-width: 100px;" readonly name="sub_total[]" class="form-control sub_total" value="${data.product.selling_price}"/>
                    </td>
                    <td class="table_data_style_right text-center">
                    <a href="#" class="remove-btn item-index" data-value="${index}"><i class="fa fa-trash text-danger"></i></a>
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
            
            let stock = 0;
            let variation_select = obj.parents('tr').find('select[name="variation_id[]"]');
            if (variation_select.length > 0) {
                let selectedOption = variation_select.find('option:selected');
                let variationStock = parseFloat(selectedOption.attr('stock'));
                if (!isNaN(variationStock)) {
                    stock = variationStock;
                } else {
                    stock = parseFloat(obj.parents('tr').find('.main_qty').attr('data-value')) || 0;
                }
            } else {
                stock = parseFloat(obj.parents('tr').find('.main_qty').attr('data-value')) || 0;
            }


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
                let discount = obj.parents('tr').find('.product_discount').val() || '0';

                let subTotal = calculate_sub_total(converted.main_qty, converted.sub_qty, price, related_by, has_sub_unit, discount);

                obj.parents('tr').find('.sub_total').val(subTotal);
                estimatedAmount();

                iziToast.warning({
                    title: "Not Enough Stock.",
                    position: "topRight",
                });
            } else {
                let price = obj.parents('tr').find('.rate').val();
                price = parseFloat(price);
                let discount = obj.parents('tr').find('.product_discount').val() || '0';
                let subTotal = calculate_sub_total(main_val, sub_val, price, related_by, has_sub_unit, discount);
                obj.parents('tr').find('.sub_total').val(subTotal);
                estimatedAmount();
            }
        }

        function imeiCount(value) {
            value = (value ?? '').toString().replace(/\r/g, '').trim();
            if (!value) {
                return 0;
            }
            return value.split(/[,\n]+/).map(v => v.trim()).filter(v => v !== '').length;
        }

        function syncQtyFromImeiInput($input) {
            var $row = $input.closest('tr');
            var count = imeiCount($input.val());
            var $mainQty = $row.find('.main_qty').first();
            var $subQty = $row.find('.sub_qty').first();
            if ($mainQty.length) {
                $mainQty.val(count);
                $mainQty.prop('readonly', true);
            }
            if ($subQty.length) {
                $subQty.val(0);
                $subQty.prop('readonly', true);
            }
            if ($mainQty.length) {
                handle_change($mainQty);
            } else {
                estimatedAmount();
            }
        }

        $(function() {
            $('.imei_input').each(function() {
                syncQtyFromImeiInput($(this));
            });
        });

        $(document).on('input', '.imei_input', function() {
            syncQtyFromImeiInput($(this));
        });

        // main_qty
        $(document).on('keyup change', '.main_qty', function(e) {
            handle_change($(this));
        });

        // variation change
        $(document).on('change', 'select[name="variation_id[]"]', function(e) {
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
            $("#grandTotal").val(total_amount.toFixed(2));
            $(".sub_total").text(estimated_amount.toFixed(2));
            $(".discount_amount").text(discountAmount.toFixed(2));
            $(".discount").val(discountAmount.toFixed(2));
            $(".payable_amount").text(total_amount.toFixed(2));
            $("#payable_amount").val(total_amount.toFixed(2));

            // Recalculate Paid, Due, and Change Amount without overwriting previous paid amount
            let paidVal = $("#total_paid").val();
            let paid = parseFloat(paidVal);
            if (isNaN(paid)) {
                paid = {{ (float)($invoice->total_paid ?? 0) }};
                $("#total_paid").val(paid.toFixed(2));
            }
            let due = total_amount - paid;
            let change = paid - total_amount;
            if (due < 0) due = 0;
            if (change < 0) change = 0;
            $("#total_due").val(due.toFixed(2));
            $("#change_amount").val(change.toFixed(2));
            $("#due_badge_val").text("৳" + due.toFixed(2));

            if (due > 0.01) {
                $("#btn_pay_remaining_due").show();
            } else {
                $("#btn_pay_remaining_due").hide();
            }
        }

        $(document).on("keyup change input", "#total_paid", function() {
            let total_amount = parseFloat($("#grandTotal").val()) || 0;
            let paid_amount = parseFloat($(this).val()) || 0;
            let due = total_amount - paid_amount;
            let change = paid_amount - total_amount;
            if (due < 0) due = 0;
            if (change < 0) change = 0;
            $("#total_due").val(due.toFixed(2));
            $("#change_amount").val(change.toFixed(2));
            $("#due_badge_val").text("৳" + due.toFixed(2));

            if (due > 0.01) {
                $("#btn_pay_remaining_due").show();
            } else {
                $("#btn_pay_remaining_due").hide();
            }
        });

        $(document).on("click", "#btn_pay_remaining_due, #btn_pay_full", function() {
            let total_amount = parseFloat($("#grandTotal").val()) || 0;
            $("#total_paid").val(total_amount.toFixed(2));
            $("#total_paid").trigger("input");
            iziToast.success({
                title: "{{ __('Payment Updated') }}",
                message: "{{ __('Full payment recorded') }} (৳" + total_amount.toFixed(2) + "). {{ __('Due amount is now ৳0.00.') }}",
                position: "topRight",
            });
        });

        $(document).on("click", "#btn_pay_reset", function() {
            let initialPaid = {{ (float)($invoice->total_paid ?? 0) }};
            $("#total_paid").val(initialPaid.toFixed(2));
            $("#total_paid").trigger("input");
        });



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
                    // console.log(data)
                },
                error: function() {
                    $('#invoiceImeiChecklist').html('<div class="text-danger text-center">Failed to load IMEIs.</div>');
                }
            });
        });

        // Invoice IMEI Modal logic
        let currentImeiTarget = null;
        let currentProductRow = null;

        $(document).on('click', '.btn-enter-invoice-imei', function() {
            let btn = $(this);
            currentProductRow = btn.closest('tr');
            currentImeiTarget = btn.siblings('.imei-hidden-input');
            
            let productId = btn.data('product-id');
            let productName = btn.data('product-name');
            let invoiceId = "{{ $invoice->id }}";
            
            $('#invoiceImeiModalProductName').text(productName);
            $('#invoiceImeiChecklist').html('<div class="text-center text-white"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
            
            // Get selected IMEIs from input value
            let selectedList = (currentImeiTarget.val() || '').split(/[,\n]+/).map(x => x.trim()).filter(x => x !== '');

            $.ajax({
                url: "{{ route('get-invoice-item-candidate-imeis') }}",
                type: "GET",
                data: {
                    product_id: productId,
                    invoice_id: invoiceId
                },
                success: function(data) {
                    let html = '';
                    
                    // Merge current and available list, ensuring uniqueness
                    let allCandidateImeis = Array.from(new Set([...data.current, ...data.available])).filter(x => x !== '');
                    
                    if (allCandidateImeis.length === 0) {
                        html = '<div class="text-muted text-center text-white">No available IMEIs for this product.</div>';
                    } else {
                        allCandidateImeis.forEach(function(imei) {
                            let isChecked = selectedList.includes(imei) ? 'checked' : '';
                            html += `
                                <div class="form-check mb-2 text-left" style="display: flex; align-items: center; justify-content: flex-start; gap: 8px;">
                                    <input class="form-check-input imei-checkbox" type="checkbox" value="${imei}" id="chk_${imei}" ${isChecked} style="width: 18px; height: 18px; cursor: pointer; flex: none;">
                                    <label class="form-check-label text-white mb-0" for="chk_${imei}" style="font-size: 14px; cursor: pointer; user-select: none;">
                                        ${imei} ${data.current.includes(imei) ? '<span class="badge badge-success ml-1" style="font-size: 10px; background-color: #28a745;">Already Sold</span>' : ''}
                                    </label>
                                </div>
                            `;
                        });
                    }
                    $('#invoiceImeiChecklist').html(html);
                    updateModalSelectedCount();
                    $('#invoiceImeiModal').modal('show');
                },
                error: function() {
                    $('#invoiceImeiChecklist').html('<div class="text-danger text-center">Failed to load IMEIs.</div>');
                }
            });
        });

        $(document).on('change', '.imei-checkbox', function() {
            updateModalSelectedCount();
        });

        function updateModalSelectedCount() {
            let count = $('.imei-checkbox:checked').length;
            $('#invoiceImeiCount').text(count);
        }

        $(document).on('click', '#btnConfirmInvoiceImei', function() {
            if (!currentImeiTarget) return;
            
            let checkedValues = [];
            $('.imei-checkbox:checked').each(function() {
                checkedValues.push($(this).val());
            });
            
            let count = checkedValues.length;
            let text = checkedValues.join('\n');
            
            currentImeiTarget.val(text);
            currentProductRow.find('.imei-count').text(count);
            
            // Set qty inputs and sync
            let mainQtyInput = currentProductRow.find('.main_qty');
            if (mainQtyInput.length) {
                mainQtyInput.val(count);
            }
            let subQtyInput = currentProductRow.find('.sub_qty');
            if (subQtyInput.length) {
                subQtyInput.val(0);
            }
            
            if (mainQtyInput.length) {
                handle_change(mainQtyInput);
            } else {
                estimatedAmount();
            }

            $('#invoiceImeiModal').modal('hide');
        });
        }); // End of $(document).ready
    </script>

    <!-- Invoice IMEI Modal -->
    <div class="modal fade" id="invoiceImeiModal" tabindex="-1" role="dialog" aria-labelledby="invoiceImeiModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content card_style">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold text-white" id="invoiceImeiModalLabel">{{ __('Select IMEI(s)') }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group text-left">
                        <label class="font-weight-bold text-white mb-2" id="invoiceImeiModalProductName" style="font-size: 16px;"></label>
                        <div id="invoiceImeiChecklist" style="max-height: 300px; overflow-y: auto; background: rgba(0,0,0,0.2); padding: 15px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1);">
                            <!-- Selectable checkboxes will be rendered here via JS -->
                        </div>
                    </div>
                    <div class="text-right">
                        <small class="text-white"><span id="invoiceImeiCount" class="font-weight-bold">0</span> {{ __('IMEI(s) selected') }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="background-color: #6c757d; border-color: #6c757d; color: #fff;">{{ __('Close') }}</button>
                    <button type="button" id="btnConfirmInvoiceImei" class="btn btn-success" style="background-color: #28a745; border-color: #28a745; color: #fff;">{{ __('Confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
@endpush
