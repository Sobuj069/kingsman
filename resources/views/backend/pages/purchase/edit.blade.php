@extends('backend.layouts.master')
@section('section-title', __('Purchase'))
@section('page-title', __('Edit Purchase'))
@section('action-button')
    <a href="{{ route('purchase.index') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-list"></i>
        {{ __('Purchase List') }}
    </a>
@endsection
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
            /* border: 1px solid #DDD;  */
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
    </style>
@endpush
@section('content')
    <div class="row">
        <!-- Start col -->
        <div class="col-md-12">
            <div class="card  card_style">
                <form action="{{ route('purchase.updat', $purchase->id) }}" id="payment_form" method="POST">
                    @csrf
                    <input type="hidden" name="branch_id" id="branch_id" value="{{ $purchase->branch_id }}">
                    <input type="hidden" name="redirect_query" value="{{ request()->getQueryString() }}">
                    <div class="cart-container">
                        <div class="cart-head">
                            <div class="form-row">
                                {{-- Purchase Date --}}
                                <div class="mb-3 col-md-6">
                                    <label class="form-label font-weight-bold">
                                        {{ __('Date *') }}
                                    </label>
                                    <input type="date" class="form-control" value="{{ $purchase->date }}" name="date"
                                        required>
                                </div>
                                {{-- Purchase No --}}
                                <div class="mb-3 col-md-6">
                                    <label for="validationCustom02" class="form-label font-weight-bold">{{ __('Purchase No *') }}</label>
                                    <input type="text" class="form-control" value="{{ $purchase->purchase_no }}"
                                        name="purchase_no" required readonly>
                                </div>
                            </div>
                            <div class="form-row">
                                {{-- Note --}}
                                <div class="mb-3 col-md-6">
                                    <label class="form-label font-weight-bold">
                                        {{ __('Note') }}</label>
                                    <input type="text" class="form-control" placeholder="{{ __('Enter Note') }}" name="note">
                                </div>
                                {{-- Supplier --}}
                                <div class="mb-3 col-md-6">
                                    <div class="row">
                                        <div class="col-md-12 col-12" style="margin-right: -7px">
                                            <label class="form-label font-weight-bold">{{ __('Suppliers *') }}</label>
                                            <select class="select2" id="supplier" name="supplier_id" required>
                                                @foreach ($supplier as $supplier)
                                                    <option value="{{ $supplier->id }}"
                                                        @if ($purchase->supplier_id == $supplier->id) selected @endif>
                                                        {{ $supplier->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text barcod_style" id="basic-addon1"><i
                                            class="fa fa-barcode"></i></span>
                                </div>
                                <input type="text" id="product_search" class="form-control" placeholder="{{ __('Type & Barcode') }}"
                                    aria-label="{{ __('Type & Barcode') }}" onkeydown="return event.keyCode !== 13" autocomplete="off">
                            </div>
                        </div>
                        <div class="cart-head">
                            <div class="table-responsive">
                                <table class="table table-striped text-center">
                                    <thead>
                                        <tr class="header_bg">
                                            {{-- <th class="header_style_left" width="">Sl</th> --}}
                                            <th class="header_style_left">{{ __('Product') }}</th>
                                            @if (env('APP_IMEI') == 'yes')
                                                <th width="">{{ __('IMEI') }}</th>
                                            @endif
                                            @if (env('APP_SC') == 'yes')
                                                <th width="">{{ __('Size - Color') }}</th>
                                            @else
                                                <th></th>
                                            @endif
                                            @if (is_rack_enabled())
                                                <th width="15%">{{ __('Rack') }}</th>
                                            @endif
                                            <th width="">{{ __('Rate') }}</th>
                                            <th width="">{{ __('Quantity') }}</th>
                                            @if (env('APP_WARRANTY') == 'yes')
                                            <th width="">{{ __('Warranty') }}</th>
                                            @endif
                                            <th width="">{{ __('Total') }}</th>
                                            <th class="header_style_right">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    @php
                                        $sl = 1;
                                        $purchaseItem = App\Models\PurchaseItem::where(
                                            'purchase_id',
                                            $purchase->id,
                                        )->get();
                                    @endphp
                                    <input type="hidden" name="id" value="{{ $purchase->id }}">
                                    <tbody id="tbody">
                                        @foreach ($purchaseItem as $key => $item)
                                        @php
                                            $product_variation = App\Models\ProductVariation::where(
                                                'product_id',
                                                $item->product_id,
                                            )->get();
                                            $selectedRackIds = $item->product ? $item->product->racks->pluck('id')->toArray() : [];
                                        @endphp
                                        <tr>
                                            <input type="hidden" name="itemID[{{ $key }}]" value="{{ $item->id }}">
                                            <td class="table_data_style_left">
                                                {{-- Product Name --}}
                                                {{ $item->product?->name }} - {{ $item->product?->barcode }}
                                                <input type="hidden" value="{{ $item->product_id }}"
                                                    name="product_id[{{ $key }}]" />
                                            </td>
                                            @if (env('APP_IMEI') == 'yes')
                                                <td>
                                                    @if ($item->product->imei == 1 || $item->product->imei == '1')
                                                        <button type="button" class="btn btn-sm btn-info btn-enter-imei" data-id="old_{{ $item->id }}">
                                                             <i class="fa fa-barcode"></i> Add IMEI (<span class="imei-count">{{ count(array_filter(array_map('trim', explode("\n", $item->imei)))) }}</span>)
                                                        </button>
                                                        <textarea name="old_imei[{{ $item->id }}]" class="imei-hidden-input d-none">{{ $item->imei }}</textarea>
                                                    @endif
                                                </td>
                                            @endif
                                            @if (env('APP_SC') == 'yes')
                                                <td>
                                                    @if ($item->product_variation_id != null)
                                                        <select name="product_variation_id[{{ $key }}]" id=""
                                                            class="form-control">
                                                            @foreach ($product_variation as $variation)
                                                                <option value="{{ $variation->id }}"
                                                                    @if ($variation->id == $item->product_variation_id) selected @endif>
                                                                    {{ $variation->size?->size }} -
                                                                    {{ $variation->color?->color }}</option>
                                                            @endforeach
                                                        </select>
                                                    @else
                                                        <input type="hidden" value="{{ $item->product_variation_id }}"
                                                            name="product_variation_id[{{ $key }}]" />
                                                    @endif
                                                </td>
                                            @else
                                                <td></td>
                                            @endif
                                            @if (is_rack_enabled())
                                            <td>
                                                <select name="new_rack_ids[{{ $key }}][]" class="form-control select2-rack" multiple data-placeholder="{{ __('Select Rack') }}" style="width: 100%;">
                                                    @foreach ($racks ?? [] as $rack)
                                                        <option value="{{ $rack->id }}" {{ in_array($rack->id, $selectedRackIds) ? 'selected' : '' }}>{{ $rack->name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            @endif
                                            <td>
                                                <input type="number" style="min-width: 70px;" class="form-control rate"
                                                    name="new_rate[{{ $key }}]" value="{{ $item->rate }}"
                                                    onchange="recalcRow(this)" oninput="recalcRow(this)" />
                                            </td>
                                            <td style="width:250px">
                                                <div class="form-row">
                                                    @php
                                                        $isImei = ($item->product->imei == 1 || $item->product->imei == '1');
                                                    @endphp
                                                    @if ($item->product->unit->related_unit == null)
                                                        <input type="text" class="has_sub_unit" hidden value="false">
                                                        <label class="ml-2 mt-2  mr-2"
                                                            style="padding-top: 5px;">{{ $item->product->unit->name }}:</label>
                                                        <input type="number" value="{{ $item->main_qty }}"
                                                            class="form-control col main_qty" name="new_main_qty[{{ $key }}]"
                                                            data-value="{{ $item->stock_qty }}"
                                                            data-related="{{ $item->product->unit->related_value }}"
                                                            {{ $isImei ? 'readonly' : '' }}
                                                            onkeydown="return event.keyCode !== 190" min="0"
                                                            onchange="recalcRow(this)" oninput="recalcRow(this)">
                                                        <input type="hidden" value="{{ $item->sub_qty }}"
                                                            class="form-control col sub_qty mr-1" name="new_sub_qty[{{ $key }}]"
                                                            onkeydown="return event.keyCode !== 190" min="0"
                                                            max="{{ $item->product->unit->related_value - 1 }}">
                                                    @else
                                                        {{-- HAS SUB UNIT --}}
                                                        <input type="text" class="has_sub_unit" hidden value="true">
                                                        <input type="text" class="conversion" hidden
                                                            value="{{ $item->product->unit->related_value }}">
                                                        <label class="mr-1 ml-1"
                                                            style="padding-top: 5px;">{{ $item->product->unit->name }}:</label>
                                                        <input type="number" value="{{ $item->main_qty }}"
                                                            class="form-control col main_qty mr-1" name="new_main_qty[{{ $key }}]"
                                                            data-value="{{ $item->stock_qty }}"
                                                            data-related="{{ $item->product->unit->related_value }}"
                                                            {{ $isImei ? 'readonly' : '' }}
                                                            onkeydown="return event.keyCode !== 190" min="0">
                                                        <label class="mr-1"
                                                            style="padding-top: 5px;">{{ $item->product->unit->related_unit->name }}:</label>
                                                        <input type="number" value="{{ $item->sub_qty }}"
                                                            class="form-control sub_qty mr-1" style="width: 80px; flex: none;" name="new_sub_qty[{{ $key }}]"
                                                            onkeydown="return event.keyCode !== 190" min="0"
                                                            max="{{ $item->product->unit->related_value - 1 }}">
                                                    @endif
                                                </div>
                                            </td>
                                            @if (env('APP_WARRANTY') == 'yes')
                                            <td class="text-center align-middle">
                                                <span class="badge badge-info px-2 py-1 font-weight-bold" style="font-size: 13px;">
                                                    @if(!empty($item->warranty_value))
                                                        {{ $item->warranty_value }} {{ $item->warranty_unit }}
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </span>
                                                <input type="hidden" name="new_warranty_value[{{ $key }}]" value="{{ $item->warranty_value }}">
                                                <input type="hidden" name="new_warranty_unit[{{ $key }}]" value="{{ $item->warranty_unit }}">
                                            </td>
                                            @endif
                                            <td>
                                                {{-- <strong><span class="sub_total">{{ $item->subtotal }}</span>
                                                    {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</strong> --}}
                                                <input type="text" name="new_subtotal_input[{{ $key }}]"
                                                    class="sub_total form-control" value="{{ $item->subtotal }}">
                                            </td>
                                            <td class="table_data_style_right">
                                                <a href="#" class="remove-btn item-index" data-value="{{ $key }}"><i
                                                        class="fa fa-undo text-danger"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                     @php
                                         $footerColspan = 4 + (env('APP_IMEI') == 'yes' ? 1 : 0) + (env('APP_WARRANTY') == 'yes' ? 1 : 0) + (is_rack_enabled() ? 1 : 0);
                                     @endphp
                                     <tfoot>
                                         <tr class="bg-light-primary">
                                             <td colspan="{{ $footerColspan }}" class="text-right fw-bold">{{ __('Grand Total') }}</td>
                                             <td colspan="1">
                                                 <input type="number" step="any" style="min-width:100px"
                                                     name="estimated_amount" value="{{ $purchase->rtn_total_amount > 0 ? $purchase->rtn_total_amount : $purchase->estimated_amount }}"
                                                     class="form-control estimated_amount">
                                             </td>
                                             <td class="text-right"></td>
                                         </tr>
                                         {{-- Discount --}}
                                         <tr class="bg-light-primary">
                                             <td colspan="{{ $footerColspan }}" class="text-right fw-bold">{{ __('Discount') }}</td>
                                             <td colspan="1">
                                                 <input type="text" name="discount_percent" placeholder="10 or 10%"
                                                     class="discount_percent form-control" value="{{ $purchase->discount ?? 0 }}">
                                                 <input type="hidden" name="discount_amount" class="discount_amount"
                                                     value="{{ $purchase->discount_amount ?? 0 }}">
                                             </td>
                                             <td class="text-right"></td>
                                         </tr>
                                         {{-- VAT --}}
                                         @if (is_vat_enabled())
                                         <tr class="bg-light-primary">
                                             <td colspan="{{ $footerColspan }}" class="text-right fw-bold">{{ __('VAT') }}</td>
                                             <td colspan="1">
                                                 <input type="text" name="vat_percent" placeholder="5 or 5%"
                                                     class="vat_percent form-control" value="{{ $purchase->vat ?? 0 }}">
                                                 <input type="hidden" name="vat_amount" class="vat_amount"
                                                     value="{{ $purchase->vat_amount ?? 0 }}">
                                             </td>
                                             <td class="text-right"></td>
                                         </tr>
                                         @else
                                         <input type="hidden" name="vat_percent" class="vat_percent" value="{{ $purchase->vat ?? 0 }}">
                                         <input type="hidden" name="vat_amount" class="vat_amount" value="{{ $purchase->vat_amount ?? 0 }}">
                                         @endif
                                         {{-- Total Amount --}}
                                         <tr class="">
                                             <td colspan="{{ $footerColspan }}" class="text-right fw-bold">{{ __('Payable Amount') }}</td>
                                             <td colspan="1">
                                                 <input type="number" step="any" name="total_amount" min="0"
                                                     value="{{ $purchase->rtn_total_amount > 0 ? $purchase->rtn_total_amount : $purchase->total_amount }}"
                                                     class="form-control total_amount">
                                             </td>
                                             <td class="text-right"></td>
                                         </tr>
                                         {{-- Payment Method --}}
                                         <tr class="">
                                             <td colspan="{{ $footerColspan - 1 }}" class="text-right fw-bold text-black">{{ __('Bank Account') }}</td>
                                             <td colspan="1">
                                                 <select class="select2" id="bankAmount" name="bank_id" required>
                                                     @foreach ($bank_accounts as $bank_account)
                                                         <option value="{{ $bank_account->id }}"
                                                             data-bank-name="{{ $bank_account->bank_name }}"
                                                             data-bank-balance="{{ current_balance($bank_account->id) }}">
                                                             {{ $bank_account->bank_name }}
                                                         </option>
                                                     @endforeach
                                                 </select>
                                             </td>
                                             <td class="text-right"></td>
                                         </tr>
                                         {{-- Paid Amount --}}
                                         <tr class="">
                                             <td colspan="{{ $footerColspan }}" class="text-right fw-bold text-black">{{ __('Paid Amount') }}</td>
                                             <td colspan="1">
                                                 <input type="number" step="any" name="paid_amount" min="0"
                                                     value="{{ $purchase->rtn_total_amount > 0 ? $purchase->rtn_total_paid : $purchase->total_paid }}"
                                                     class="form-control paid_amount" required>
                                             </td>
                                             <td class="text-right"></td>
                                         </tr>
                                         {{-- Due Amount in --}}
                                         <tr class="bg-light-danger">
                                             <td colspan="{{ $footerColspan }}" class="text-right fw-bold">{{ __('Due Amount') }}</td>
                                             <td colspan="1">
                                                 <input type="number" step="any" name="due_amount" min="0"
                                                     value="{{ $purchase->rtn_total_amount > 0 ? $purchase->rtn_total_due : $purchase->total_due }}"
                                                     class="form-control due_amount">
                                             </td>
                                             <td class="text-right"></td>
                                         </tr>
                                     </tfoot>
                                </table>
                                <div class="text-center col-md-12">
                                    <button class="btn col-sm-6 submit_button"
                                        style="padding: 5px 20px; background: #12b76a;color:white; border-radius:25px;margin-right: 5px">{{ __('Submit') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- End col -->
    </div>

    <!-- IMEI Input Modal -->
    <div class="modal fade" id="purchaseImeiModal" tabindex="-1" role="dialog" aria-labelledby="purchaseImeiModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content card_style">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="purchaseImeiModalLabel">{{ __('Enter IMEI(s)') }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="modalImeiTextarea" class="font-weight-bold">{{ __('Enter IMEIs (1 item/unit per line. For Dual-SIM, enter 2 IMEIs on 1 line separated by comma e.g. 12, 11):') }}</label>
                        <textarea id="modalImeiTextarea" class="form-control" rows="8" placeholder="12, 11&#10;13, 14&#10;15"></textarea>
                        <div id="modalImeiError" class="alert alert-danger d-none mt-2" style="padding: 8px 12px; font-size: 13px;"></div>
                    </div>
                    <div class="text-right">
                        <small class="text-muted"><span id="modalImeiCount">0</span> {{ __('Unit(s) entered') }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn cancel_btn" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" id="btnConfirmPurchaseImei" class="btn save_btn">{{ __('Confirm') }}</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('js')
    <script>
        $(document).ready(function() {
            function updateTotalPoint() {
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

        // Plain JS recalcRow - globally available
        window.recalcRow = function(el) {
            var tr = el.closest('tr');
            if (!tr) return;
            var mainQtyEl = tr.querySelector('.main_qty');
            var subQtyEl = tr.querySelector('.sub_qty');
            var rateEl = tr.querySelector('.rate');
            var subtotalEl = tr.querySelector('.sub_total');
            if (!mainQtyEl || !rateEl || !subtotalEl) return;

            var main_val = parseFloat(mainQtyEl.value) || 0;
            var sub_val = subQtyEl ? (parseFloat(subQtyEl.value) || 0) : 0;
            var rate = parseFloat(rateEl.value) || 0;
            var related_by = parseFloat(mainQtyEl.getAttribute('data-related')) || 0;
            var has_sub_unit = (tr.querySelector('.has_sub_unit') || {}).value;

            var sub_unit_price = (has_sub_unit == 'true' && related_by > 0) ? (rate / related_by) : 0;
            var subTotal = ((main_val * rate) + (sub_val * sub_unit_price)).toFixed(2);

            subtotalEl.value = subTotal;
            recalcTotals();
        };

        window.recalcTotals = function() {
            var sum = 0;
            document.querySelectorAll('.sub_total').forEach(function(el) {
                var v = parseFloat(el.value);
                if (!isNaN(v)) sum += v;
            });
            var estimatedEl = document.querySelector('input[name="estimated_amount"]');
            var totalEl = document.querySelector('input[name="total_amount"]');
            var dueEl = document.querySelector('input[name="due_amount"]');
            var paidEl = document.querySelector('input[name="paid_amount"]');
            var discountEl = document.querySelector('.discount_percent');
            var vatEl = document.querySelector('.vat_percent');

            if (estimatedEl) estimatedEl.value = sum.toFixed(2);

            var discountInput = discountEl ? discountEl.value : '0';
            var discountAmount = 0;
            if (discountInput && discountInput.toString().includes('%')) {
                discountAmount = sum * (parseFloat(discountInput) / 100);
            } else {
                discountAmount = parseFloat(discountInput) || 0;
            }

            var vatInput = vatEl ? vatEl.value : '0';
            var vatAmount = 0;
            if (vatInput && vatInput.toString().includes('%')) {
                vatAmount = sum * (parseFloat(vatInput) / 100);
            } else {
                vatAmount = parseFloat(vatInput) || 0;
            }

            var total = sum - discountAmount + vatAmount;
            if (total < 0) total = 0;
            if (totalEl) totalEl.value = total.toFixed(2);

            var paid = paidEl ? (parseFloat(paidEl.value) || 0) : 0;
            var due = total - paid;
            if (due < 0) due = 0;
            if (dueEl) dueEl.value = due.toFixed(2);
        };

        var localData = localStorage.getItem('pos-items') ? JSON.parse(localStorage.getItem('pos-items')) : [];

        function showList() {
            if (localData.length > 0) {
                localData.forEach((item, index) => {
                    domPrepend(item, index);
                });
            }
        }

        showList();
        estimatedAmount();
        $('.select2-rack').select2({ placeholder: "{{ __('Select Rack') }}", width: '100%' });

        var cartList = [];

        // Helper Functions
        function empty_field_check(placeholder) {
            if (placeholder === undefined || placeholder === null || placeholder === NaN) {
                return 0;
            }
            if (typeof placeholder === 'string') {
                if (placeholder.trim() === "" || placeholder === 'null') {
                    return 0;
                }
            }
            return placeholder;
        }

        function to_sub_unit(main_val, sub_val, related_by, has_sub_unit) {
            main_val = parseFloat(main_val) || 0;
            sub_val = parseFloat(sub_val) || 0;
            related_by = parseFloat(related_by) || 0;

            if (has_sub_unit == 'true' && related_by > 0) {
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
            main_qty = parseFloat(main_qty) || 0;
            sub_qty = parseFloat(sub_qty) || 0;
            unit_price = parseFloat(unit_price) || 0;
            related_by = parseFloat(related_by) || 0;

            var sub_unit_price = 0;
            if (has_sub_unit == "true" && related_by > 0) {
                sub_unit_price = unit_price / related_by;
            }
            var total = (main_qty * unit_price) + (sub_qty * sub_unit_price);
            return total.toFixed(2);
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

        var nextRowIndex = {{ count($purchaseItem) }};
        var allAvailableRacks = @json($racks ?? []);

        function buildPurchaseRackOptions(selectedIds = []) {
            let html = '';
            if (allAvailableRacks && allAvailableRacks.length > 0) {
                allAvailableRacks.forEach(function(r) {
                    let isSelected = (selectedIds.map(String).indexOf(String(r.id)) !== -1) ? 'selected' : '';
                    html += `<option value="${r.id}" ${isSelected}>${r.name}</option>`;
                });
            }
            return html;
        }

        function addProductToCard(data, variation_code = null) {
            storedata(data);
            domPrepend(data, null, variation_code);
            estimatedAmount();
        }

        // Search Product
        var variation_code = '';
        $("#product_search").autocomplete({
            source: function(req, res) {
                let barcode = req.term.substring(0, 6);
                variation_code = req.term.substring(6);
                let url = "{{ route('purchase.search') }}";
                let branchId = $('#branch_id').val();
                $.get(url, {
                    req: barcode,
                    branch_id: branchId
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
                let url = "{{ route('sc-search-product-id', 'my_id') }}".replace('my_id', ui.item.id);
                $.get(url, (data) => {
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
                addProductToCard(data);
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

        function domPrepend(data = null, index = null, variation_code = null) {
            var name = data.product.name;
            var quantity_data = '';
            var variation_data = ``;
            var indexToUse = nextRowIndex++;
            if (data.variations.length > 0) {
                variation_data +=
                    `<input type="text" class="has_size" data-has-size="true" hidden>
                                <select name="product_variation_id[${indexToUse}]" id="variation_id" class="form-control size" required>
                                    <option value="">Select Variation</option>`;

                $.each(data.variations, function(index, value) {
                    if (parseFloat(value.stock) <= 0) return;
                    let selected = (value.id == variation_code) ? "selected" : "";
                    variation_data += "<option stock='" + value.stock + "' value='" + value.id + "' " + selected +
                        ">" +
                        value.size + " - " + value.color + " - " + value.stock + "</option>";
                });

                variation_data += '</select>';
            } else {
                variation_data =
                    `<input type="text" class="has_size" data-has-size="false" hidden>`;
            }
            if (data.product.is_service == 0) {
                let isImei = data.product.imei == 1 || data.product.imei == '1';
                let readonlyAttr = isImei ? 'readonly' : '';
                if (data.product.unit.related_unit == null) {
                    quantity_data =
                        `<input type="text" class="has_sub_unit" hidden value="false">
                            <label class="ml-2 mr-2" style="padding-top: 5px;">${data.product.unit.name}:</label>
                            <input type="number" value="${data.stock_qty}" class="form-control col main_qty" name="new_main_qty[${indexToUse}]" 
                            data-value="${data.stock_qty}" data-related="${data.product.unit.related_value}" 
                            ${readonlyAttr}
                            onkeydown="return event.keyCode !== 190" min="0"
                            onchange="recalcRow(this)" oninput="recalcRow(this)">`;
                } else {
                    quantity_data =
                        `<input type="text" class="has_sub_unit" hidden value="true">
                            <input type="text" class="conversion" hidden value="${data.product.unit.related_value}">
                            <label class="mr-1 ml-1" style="padding-top: 5px;">${data.product.unit.name}:</label>
                            <input type="number" value="${data.stock_qty / (data.product.unit.related_value || 1) | 0}" class="form-control col main_qty mr-1" name="new_main_qty[${indexToUse}]" 
                            data-value="${data.stock_qty}" data-related="${data.product.unit.related_value}" 
                            ${readonlyAttr}
                            onkeydown="return event.keyCode !== 190" min="0"
                            onchange="recalcRow(this)" oninput="recalcRow(this)">

                            <label class="mr-1" style="padding-top: 5px;">${data.product.unit.related_unit ? data.product.unit.related_unit.name : 'sub'}:</label>
                            <input type="number" value="${data.stock_qty % (data.product.unit.related_value || 1)}" class="form-control sub_qty mr-1" style="width: 80px; flex: none;" name="new_sub_qty[${indexToUse}]"  
                            onkeydown="return event.keyCode !== 190" min="0" max="${data.product.unit.related_value-1}"
                            onchange="recalcRow(this)" oninput="recalcRow(this)">`;
                }
            } else {
                quantity_data =
                    `
                            <label class="ml-2 mr-2" style="padding-top: 5px;">pcs:</label>
                            <input type="number" value="1" class="form-control col main_qty" name="new_main_qty[${indexToUse}]" onkeydown="return event.keyCode !== 190" min="0">`;
                name_data = `
                ${data.product.name}
                    
                    <input type="hidden" class="name" value="${name.replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, '')}" name="name[]" />
                    
                `;
            }
            let dom = `
                <tr id="tbody_tr">
                    <td class="table_data_style_left" style="min-width: 100px;">
                        ${data.product.name + " - " + data.product.barcode }
                        <input type="hidden" class="name" value="${name.replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, '')}" name="name[]" />
                        <input type="hidden" value="${data.product.id}" name="product_id[${indexToUse}]" />
                    </td>
                    @if (env('APP_IMEI') == 'yes')
                        <td>
                        ${data.product.imei == 1 || data.product.imei == '1' ? `
                            <button type="button" class="btn btn-sm btn-info btn-enter-imei" data-id="${indexToUse}">
                                <i class="fa fa-barcode"></i> Add IMEI (<span class="imei-count">0</span>)
                            </button>
                            <textarea name="new_imei[${indexToUse}]" class="imei-hidden-input d-none"></textarea>
                        ` : ''}
                        </td>
                    @endif
                    <td style="min-width: 120px;">
                        ${variation_data}
                        <input type="hidden" name="product_variation_id[${indexToUse}]"  value="${variation_code ?? ''}">
                    </td>
                    @if (is_rack_enabled())
                    <td>
                        <select name="new_rack_ids[${indexToUse}][]" class="form-control select2-rack" multiple data-placeholder="{{ __('Select Rack') }}" style="width: 100%;">
                            ${buildPurchaseRackOptions(data.rack_ids || [])}
                        </select>
                    </td>
                    @endif
                    <td>
                        <input type="number" style="min-width: 100px;" value="${data.product.purchase_price}" class="form-control rate" name="new_rate[${indexToUse}]" />
                    </td>
                    <td>
                        <div class="form-row" style="min-width: 100px;">
                            ${quantity_data}
                        </div>
                    </td>
                    @if (env('APP_WARRANTY') == 'yes')
                    <td class="text-center align-middle">
                        <span class="badge badge-info px-2 py-1 font-weight-bold" style="font-size: 13px;">
                            ${(data.product.warranty_value || data.warranty_value) ? ((data.product.warranty_value || data.warranty_value) + ' ' + (data.product.warranty_unit || data.warranty_unit || 'Month')) : '<span class="text-muted">-</span>'}
                        </span>
                        <input type="hidden" name="new_warranty_value[${indexToUse}]" value="${data.product.warranty_value || data.warranty_value || ''}">
                        <input type="hidden" name="new_warranty_unit[${indexToUse}]" value="${data.product.warranty_unit || data.warranty_unit || ''}">
                    </td>
                    @endif
                    <td>
                        <input type="number" style="min-width: 100px;"  name="new_subtotal_input[${indexToUse}]" class="form-control sub_total" value="${data.product.purchase_price}"/>
                    </td>
                    <td class="table_data_style_right">
                        <a href="#" class="remove-btn item-index" data-value="${indexToUse}"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            `;
            $("#tbody").prepend(dom);
            $('.select2-rack').select2({ placeholder: "{{ __('Select Rack') }}", width: '100%' });
        }

        function handle_change(obj) {
            var tr = obj.closest('tr');
            var main_val = parseFloat(tr.find('.main_qty').val()) || 0;
            var sub_val = parseFloat(tr.find('.sub_qty').val()) || 0;
            let related_by = parseFloat(tr.find('.main_qty').attr('data-related')) || 0;
            var has_sub_unit = tr.find('.has_sub_unit').val();

            let price = parseFloat(tr.find('.rate').val()) || 0;
            let subTotal = calculate_sub_total(main_val, sub_val, price, related_by, has_sub_unit);
            // use class selector - avoids jQuery escaping issue with [] in name attribute
            tr.find('.sub_total').val(subTotal);
            estimatedAmount();
        }

        // quantity and rate change
        $(document).on('keyup change input', '.main_qty, .sub_qty, .rate', function() {
            handle_change($(this));
        });

        // Handle IMEI Modal Opening and Saving
        let currentImeiTarget = null;
        let debounceTimer = null;

        function parseImeiInput(text) {
            let lines = (text || '').split(/\r?\n/).map(l => l.trim()).filter(l => l !== '');
            let tokens = [];
            for (let line of lines) {
                let items = line.split(/[\s,\/]+/).map(i => i.trim()).filter(i => i !== '');
                tokens.push(...items);
            }
            return { lines: lines, tokens: tokens };
        }

        function validateImeis() {
            if (!currentImeiTarget) return;

            let text = $('#modalImeiTextarea').val();
            let data = parseImeiInput(text);
            let lines = data.lines;
            let tokens = data.tokens;
            
            // Reset errors and enable button by default
            $('#modalImeiError').addClass('d-none').html('');
            $('#btnConfirmPurchaseImei').prop('disabled', false);

            // 1. Check for duplicate individual IMEIs within the current textarea list itself
            let uniqueTokens = new Set();
            let internalDuplicates = new Set();
            for (let token of tokens) {
                if (uniqueTokens.has(token)) {
                    internalDuplicates.add(token);
                }
                uniqueTokens.add(token);
            }

            if (internalDuplicates.size > 0) {
                let dupList = Array.from(internalDuplicates).join(', ');
                showError("Duplicate IMEI(s) in list: " + dupList);
                return;
            }

            // 2. Check for duplicates across other rows on the same screen (client-side cross-row duplicates)
            let otherTokens = new Set();
            let crossRowDuplicates = new Set();
            $('.imei-hidden-input').not(currentImeiTarget).each(function() {
                let val = $(this).val() || '';
                let rData = parseImeiInput(val);
                for (let rToken of rData.tokens) {
                    otherTokens.add(rToken);
                }
            });

            for (let token of tokens) {
                if (otherTokens.has(token)) {
                    crossRowDuplicates.add(token);
                }
            }

            if (crossRowDuplicates.size > 0) {
                let dupList = Array.from(crossRowDuplicates).join(', ');
                showError("IMEIs already entered in other rows: " + dupList);
                return;
            }

            // 3. Database check (AJAX) - check all tokens against SerialNumber table
            if (tokens.length > 0) {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(function() {
                    let excludePurchaseId = $('input[name="id"]').val() || null;
                    
                    $.ajax({
                        url: "{{ route('check-imei-duplicates') }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            imeis: tokens,
                            exclude_purchase_id: excludePurchaseId
                        },
                        success: function(response) {
                            if (response.duplicates && response.duplicates.length > 0) {
                                let dbDups = response.duplicates.join(', ');
                                showError("IMEIs currently active in stock: " + dbDups);
                            }
                        }
                    });
                }, 300); // 300ms debounce
            }
        }

        function showError(msg) {
            $('#modalImeiError').removeClass('d-none').text(msg);
            $('#btnConfirmPurchaseImei').prop('disabled', true);
        }

        $(document).on('click', '.btn-enter-imei', function() {
            let btn = $(this);
            currentImeiTarget = btn.siblings('.imei-hidden-input');
            let existingValue = currentImeiTarget.val() || '';
            
            // Set modal textarea value
            $('#modalImeiTextarea').val(existingValue);
            
            // Update count: number of lines = quantity
            let data = parseImeiInput(existingValue);
            $('#modalImeiCount').text(data.lines.length);

            // Reset error state
            $('#modalImeiError').addClass('d-none').html('');
            $('#btnConfirmPurchaseImei').prop('disabled', false);
            
            $('#purchaseImeiModal').modal('show');
            validateImeis();
        });

        $(document).on('input', '#modalImeiTextarea', function() {
            let data = parseImeiInput($(this).val());
            $('#modalImeiCount').text(data.lines.length);
            validateImeis();
        });

        $('#btnConfirmPurchaseImei').on('click', function() {
            if (!currentImeiTarget) return;
            
            let text = $('#modalImeiTextarea').val();
            let data = parseImeiInput(text);
            let count = data.lines.length; // Quantity = Number of lines (1 line = 1 unit)
            
            // Set values back to row
            currentImeiTarget.val(text);
            
            let row = currentImeiTarget.closest('tr');
            row.find('.imei-count').text(count);
            
            let mainQtyInput = row.find('.main_qty');
            if (count > 0) {
                mainQtyInput.val(count);
            } else {
                mainQtyInput.val(1);
            }
            
            // Trigger events to update totals
            mainQtyInput.trigger('input');
            mainQtyInput.trigger('change');
            if (typeof recalcRow === 'function') {
                recalcRow(mainQtyInput[0]);
            }
            
            $('#purchaseImeiModal').modal('hide');
        });

        $(document).on(
            "keyup change",
            "input[name='paid_amount']",
            function() {
                let payableAmount = parseFloat($(".total_amount").val()) || 0;
                let paidAmount = parseFloat($(this).val()) || 0;

                if (paidAmount > payableAmount) {
                    alert('Paid amount cannot be greater than Total Amount!');
                    $(this).val(payableAmount.toFixed(2));
                }

                totalCalculate();
            }
        );


        //estimatedAmount function
        function estimatedAmount() {
            var sum = 0;

            $('.sub_total').each(function() {
                var value = parseFloat($(this).val());
                if (!isNaN(value)) {
                    sum += value;
                }
            });
            $("input[name='estimated_amount']").val(sum.toFixed(2));
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


        // function totalCalculate() {
        //     let discount = $(".discount_amount").val();
        //     let estimated_amount = parseFloat(
        //         $("input[name='estimated_amount']").val()
        //     );
        //     discount = empty_field_check(discount);


        //     let discountAmount = 0;
        //     if ((typeof discount === 'string' || discount instanceof String) && discount.includes("%")) {
        //         let removed_percent_discount = discount.replace('%', '');
        //         discount = parseFloat(removed_percent_discount);
        //         discountAmount = Math.round($(".estimated_amount").val() * (discount / 100));
        //     } else {
        //         discountAmount = parseFloat(discount);
        //     }
        //     let paidAmount = $(".paid_amount").val();
        //     let total_amount = estimated_amount - discountAmount;
        //     let dueAmount = total_amount - paidAmount;
        //     $("#grand_total").text(total_amount.toFixed(2));
        //     $(".sub_total").text(estimated_amount.toFixed(2));
        //     $(".discount_amount").text(discountAmount.toFixed(2));
        //     $(".discount").val(discountAmount.toFixed(2));
        //     $(".payable_amount").text(total_amount.toFixed(2));
        //     $("#payable_amount").val(total_amount.toFixed(2));
        //     $(".total_amount").val(total_amount.toFixed(2));
        //     $(".due_amount").val(dueAmount.toFixed(2));
        // }

        $(document).on("keyup change", ".discount_percent, .vat_percent", function() {
            totalCalculate();
        });

        function totalCalculate() {
            let discountInput = $(".discount_percent").val(); // Your discount input field
            let vatInput = $(".vat_percent").val(); // Your vat input field
            let estimated_amount = parseFloat($("input[name='estimated_amount']").val());
            if (isNaN(estimated_amount)) estimated_amount = 0;

            let discountAmount = 0;
            let vatAmount = 0;

            // Check if discount input has % sign
            if (discountInput && discountInput.toString().includes("%")) {
                let percent = parseFloat(discountInput.replace("%", ""));
                if (!isNaN(percent)) {
                    discountAmount = estimated_amount * (percent / 100);
                }
            } else {
                let fixedDiscount = parseFloat(discountInput);
                if (!isNaN(fixedDiscount)) {
                    discountAmount = fixedDiscount;
                }
            }

            // Check if vat input has % sign
            if (vatInput && vatInput.toString().includes("%")) {
                let percent = parseFloat(vatInput.replace("%", ""));
                if (!isNaN(percent)) {
                    vatAmount = estimated_amount * (percent / 100);
                }
            } else {
                let fixedVat = parseFloat(vatInput);
                if (!isNaN(fixedVat)) {
                    vatAmount = fixedVat;
                }
            }

            // Round discountAmount and vatAmount to 2 decimals
            discountAmount = parseFloat(discountAmount.toFixed(2));
            vatAmount = parseFloat(vatAmount.toFixed(2));

            // Calculate total amount after discount and vat
            let total_amount = estimated_amount - discountAmount + vatAmount;
            if (total_amount < 0) total_amount = 0;

            // Paid amount and due amount
            let paidAmount = parseFloat($(".paid_amount").val()) || 0;
            let dueAmount = total_amount - paidAmount;
            if (dueAmount < 0) dueAmount = 0;

            // Update DOM fields
            $(".discount_amount").val(discountAmount); // hidden field
            $(".vat_amount").val(vatAmount); // hidden field
            $(".total_amount").val(total_amount); // total after discount
            $(".due_amount").val(dueAmount); // due amount
        }

        // ===================order modal===================
        //payment_modal_btn
        $("#payment_modal_btn").on("click", function() {
            //date
            var date = $("#date").val();
            var variation = $("#variation_id").val();
            if (date == '') {
                //Walking Customer Can't to Create a Due
                iziToast.warning({
                    title: "Please select a date.",
                    position: "topRight",
                });
                return false;
            }
            if (variation == '') {
                //Walking Customer Can't to Create a Due
                iziToast.warning({
                    title: "Please select a product variation.",
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

        $("#checkout").on("click", function() {
            if ($("#tbody tr").length === 0) {
                iziToast.warning({
                    title: "{{ __('Please select at least one product.') }}",
                    position: "topRight",
                });
                return false;
            }
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
                    alert('Error !');
                }
            });
        });

        $(document).ready(function() {
            recalcTotals();
        });
    </script>
@endpush
