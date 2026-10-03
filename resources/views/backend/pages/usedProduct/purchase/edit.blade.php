@extends('backend.layouts.master')
@section('section-title', 'Purchase')
@section('page-title', 'Update Purchase')
@section('action-button')
    <a href="{{ route('purchase.index') }}" class="btn btn-primary-rgba">
        <i class="mr-2 feather icon-list"></i>
        Purchase List
    </a>
@endsection

@section('content')
    <div class="row width-auto">
        <div class="col-lg-12">
            <div class="card m-b-30">
                <div class="card-body">
                    <form class="needs-validation" novalidate id="purchaseForm" action="{{ route('purchase.updat',$data->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            {{-- Purchase Date --}}
                            <div class="mb-3 col-md-6">
                                <label class="form-label font-weight-bold">
                                    Date *
                                </label>
                                <input type="date" class="form-control" value="{{ $data->date }}" name="date"
                                    required>
                            </div>
                            {{-- Purchase No --}}
                            <div class="mb-3 col-md-6">
                                <label for="validationCustom02" class="form-label font-weight-bold">Purchase No *</label>
                                <input type="text" class="form-control" value="{{ $data->purchase_no }}"
                                    name="purchase_no" required readonly>
                            </div>
                        </div>
                        <div class="form-row">
                            {{-- Note --}}
                            <div class="mb-3 col-md-6">
                                <label class="form-label font-weight-bold">
                                    Note</label>
                                <input type="text" class="form-control" placeholder="Enter Note" name="note"
                                    value="{{ $data->note }}">
                            </div>
                            {{-- Supplier --}}
                            <div class="mb-3 col-md-6">
                                <div class="row">
                                    <div class="col-md-12 col-12" style="margin-right: -7px">
                                        <label class="form-label font-weight-bold">Supplier *</label>
                                        <select class="select2" id="supplier" name="supplier_id" required>
                                            @php
                                                $suppliers = App\Models\Supplier::get();
                                            @endphp
                                            @foreach ($suppliers as $supplier)
                                                <option
                                                    value="{{ $supplier->id }}"{{ $data->supplier_id == $supplier->id }}>
                                                    {{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-12 table-responsive">
                                <table class="table table-bordered text-center table-sm">
                                    <thead>
                                        <tr class="bg-primary container-fluid">
                                            <th width="5%">#SL</th>
                                            <th width="25%">Product</th>
                                            <th width="13%">Rate</th>
                                            <th width="20%">Qty</th>
                                            <th width="20%">Sub Total</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="table_body">
                                        @php
                                            $purchaseItem = App\Models\PurchaseItem::where(
                                                'purchase_id',
                                                $data->id,
                                            )->get();
                                        @endphp
                                        <input type="hidden" name="id" value="{{ $data->id }}">
                                        @foreach ($purchaseItem as $key => $item)
                                            <tr>
                                                <input type="hidden" name="itemID[]" value="{{ $item->id }}">
                                                <td>{{ ++$key }}</td>
                                                <td>
                                                    {{ $item->product?->name }}
                                                <input type="hidden" value="{{ $item->product_id }}" name="new_product[]" class="product">
                                                
                                                </td>
                                                <td >
                                                    <div style="width:100px" class="form-row"> 
                                                        <input type="number" value="{{ $item->rate }}" class="form-control rate" name="new_rate[]">
                                                    </div>
                                                </td>
                                                <td class="" >
                                                    <div class="form-row" style="width:300px" >
                                                        @if ($item->product->unit->related_unit == null) 
                                                            <input type="text" class="has_sub_unit" hidden value="false">
                                                                    <label class="ml-4 mr-2" style="padding-top: 8px;">{{ $item->product->unit->name }}:</label>
                                                                    <input type="number" value="{{ $item->main_qty }}" class="form-control col main_qty" name="new_main_qty[]"  onkeydown="return event.keyCode !== 190" min="1">
                                                        @else 
                                                            <input type="text" class="has_sub_unit" hidden value="true">
                                                                    <input type="text" class="conversion" hidden value="{{$item->product->unit->related_value}}">
                                                                    <label class="mr-2 ml-4" style="padding-top: 8px;">{{ $item->product->unit->name }}:</label>
                                                                    <input type="number" value="{{ $item->main_qty }}" class="form-control col main_qty mr-4" name="new_main_qty[]"  onkeydown="return event.keyCode !== 190" min="1">
                                                                    <label class="mr-2" style="padding-top: 8px;">{{ $item->product->unit->related_unit->name }}:</label>
                                                                    <input type="number" value="{{ $item->sub_qty }}" class="form-control col sub_qty" name="new_sub_qty[]"  onkeydown="return event.keyCode !== 190" min="1">
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                <strong><span class="sub_total">{{ $item->subtotal }}</span> {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</strong>
                                                <input type="hidden" name="new_subtotal_input[]" class="subtotal_input" value="0">
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-light-primary">
                                            <td colspan="4" class="text-right fw-bold">Grand Total</td>
                                            <td colspan="1">
                                                <input type="number" step="any" style="min-width:100px"
                                                    name="estimated_amount" value="{{ $data->estimated_amount }}" class="form-control" readonly>
                                            </td>
                                            <td class="text-right"></td>
                                        </tr>
                                        {{-- Discount --}}
                                        <tr class="bg-light-primary">
                                            <td colspan="4" class="text-right fw-bold">Discount</td>
                                            <td colspan="1">
                                                <input type="number" step="any" name="discount_amount" min="0"
                                                    value="{{ $data->discount }}" class="form-control discount_amount" required>
                                            </td>
                                            <td class="text-right"></td>
                                        </tr>
                                        {{-- Total Amount --}}
                                        <tr class="bg-warning">
                                            <td colspan="4" class="text-right fw-bold">Payable
                                                Amount</td>
                                            <td colspan="1">
                                                <input type="number" step="any" name="total_amount" min="0"
                                                    value="0" class="form-control total_amount" readonly>
                                            </td>
                                            <td class="text-right"></td>
                                        </tr>
                                        {{-- Payment Method --}}
                                        {{-- <tr class="bg-primary">
                                            <td colspan="5" class="text-right fw-bold text-light">Bank Account</td>
                                            <td colspan="1">
                                                <select class="select2" name="bank_id" required>
                                                    @foreach ($bank_accounts as $bank_account)
                                                        <option value="{{ $bank_account->id }}">
                                                            {{ $bank_account->bank_name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="text-right"></td>
                                        </tr> --}}
                                        {{-- Paid Amount --}}
                                        <tr class="bg-primary">
                                            <td colspan="4" class="text-right fw-bold text-light">Paid
                                                Amount</td>
                                            <td colspan="1">
                                                <input type="text" step="any" name="paidAmount" min="0"
                                                    value="{{ $data->total_paid }}" class="form-control paidAmount" readonly required>
                                            </td>
                                            <td class="text-right"></td>
                                        </tr>
                                        {{-- Due Amount in --}}
                                        <tr class="bg-light-danger due_amount">
                                            <td colspan="4" class="text-right fw-bold">Due
                                                Amount</td>
                                            <td colspan="1">
                                                <input type="number" step="any" name="due_amount" min="0"
                                                    value="{{ $data->total_due }}" class="form-control due_amount" readonly>
                                            </td>
                                            <td class="text-right"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                {{-- Submit & Reset Button --}}
                                <div class="row" >
                                    <div class="text-center col-md-12">
                                        <button class="btn btn-primary col-sm-6 submit_button">Submit</button>
                                        {{-- <button type="reset" class="btn btn-danger col-sm-4 reset_button">Reset</button> --}}
                                    </div>
                                </div>
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
        var count = 0;
        var db_pid_array;

        $('.rate').each(function() {
            let subTotal = calculate_sub_total($(this));
            $(this).parents('tr').children('td').find('.sub_total').text(subTotal);
            $(this).parents('tr').children('td').find('.subtotal_input').val(subTotal);
        });
        estimatedAmount();
        $(document).ready(function() {
            $("#product_search").autocomplete({
                source: function(req, res) {
                    let url = "{{ route('product-search') }}";
                    $.get(url, {
                        req: req.term
                    }, (data) => {

                        res($.map(data, function(item) {
                            return {
                                id: item.id,
                                value: item.name + " - " + item.barcode,
                                price: item.selling_price
                            }
                        })); // end res

                    });
                },
                select: function(event, ui) {
                    //check is supplier is selected.
                    if ($("#supplier").val() == '') {
                        iziToast.warning({
                            title: "Please Select Supplier First !",
                            position: "topRight",
                        });
                        return false;
                    }

                    if (db_pid_array && db_pid_array.includes(ui.item.id)) {
                        //SHOW ERROR
                        iziToast.warning({
                            title: "Product already added.",
                            position: "topRight",
                        });
                        return;
                    }

                    count++;
                    $(this).val(ui.item.value);
                    $("#search_product_id").val(ui.item.id);
                    let url = "{{ route('search-product-id', 'my_id') }}".replace('my_id', ui.item.id);
                    $.get(url, (data) => {
                        console.log(data);
                        // product add
                        var quantity_data = '';
                        var variation_data = ``;

                        if (data.variations.length > 0) {

                            // console.log('have variation');
                            variation_data +=
                                `<input type="text" class="has_size" data-has-size="true" hidden>
                                <select name="variation[]" id="" class="form-control size" required>
                                    <option value="">Select Variation</option>`;

                            $.each(data.variations, function(index, value) {
                                variation_data += "<option stock=" + value.stock +
                                    " value=\"" + value.id + "\">" + value.name +
                                    "</option>";
                            });

                            variation_data += '</select>';
                        } else {
                            variation_data =
                                `<input type="text" class="has_size" data-has-size="false" hidden>`;
                        }


                        if (data.product.unit.related_unit == null) {
                            quantity_data =
                                `<input type="text" class="has_sub_unit" hidden value="false">
                                    <label class="ml-4 mr-2" style="padding-top: 8px;">${data.product.unit.name}:</label>
                                    <input type="number" value="" class="form-control col main_qty" name="new_main_qty[]"  onkeydown="return event.keyCode !== 190" min="1">`;
                        } else {
                            quantity_data =
                                `<input type="text" class="has_sub_unit" hidden value="true">
                                    <input type="text" class="conversion" hidden value="${data.product.unit.related_value}">
                                    <label class="mr-2 ml-4" style="padding-top: 8px;">${data.product.unit.name}:</label>
                                    <input type="number" value="" class="form-control col main_qty mr-4" name="new_main_qty[]"  onkeydown="return event.keyCode !== 190" min="1">
                                    <label class="mr-2" style="padding-top: 8px;">${data.product.unit.related_unit.name}:</label>
                                    <input type="number" value="0" class="form-control col sub_qty" name="new_sub_qty[]"  onkeydown="return event.keyCode !== 190" min="1">`;
                        }
                        // <input type="number" value="1" class="form-control sale_qty" name="qty[]">
                        let row = `
                            <tr>
                                <td>${count}</td>
                                <td>
                                ${data.product.name + " - " + data.product.barcode}
                                <input type="hidden" value="${data.product.id}" name="new_product[]" class="product">
                                <input type="hidden" value="${data.product.name}" name="product_name[]" />
                                </td>
                                <td style="width:100px">
                                    ${variation_data}
                                </td>
                                <td >
                                    <div style="width:100px" class="form-row"> 
                                        <input type="text" value="${data.product.purchase_price}" class="form-control rate" name="new_rate[]">
                                    </div>
                                </td>
                                <td class="" >
                                    <div class="form-row" style="width:300px" >
                                        ${quantity_data}
                                    </div>
                                </td>
                                <td>
                                <strong><span class="sub_total">0</span> {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</strong>
                                <input type="hidden" name="new_subtotal_input[]" class="subtotal_input" value="">
                                </td>
                                <td>
                                <a href="#" class="remove">
                                    <i class="fa fa-trash"></i>
                                </a>
                                </td>
                            </tr>`;

                        // Duplicate check
                        let tableBody = document.querySelector('#table_body');
                        let products = tableBody.querySelectorAll('.product');
                        let isDuplicate = false;
                        products.forEach(function(item) {
                            if (ui.item.id == item.value) {
                                isDuplicate = true;
                            } else {
                                isDuplicate = false;
                            }
                        });

                        // if (isDuplicate) {
                        //     iziToast.warning({
                        //         title: "Product already added.",
                        //         position: "topRight",
                        //     });
                        //     return false;
                        // } else {
                        // }
                        $("#table_body").append(row);
                        tFootShowHide();

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
        });

        //calculate total amount with order tax is percentage shipping charge,others charge and discount on keyup and change
        $(document).on(
            "keyup change",
            "input[name='discount_amount']",
            function() {
                totalAmount();
                due_calculation();
            }
        );

        //paid_amount on keyup and change
        $(document).on("keyup change", "input[name='paid_amount']", function() {
            due_calculation();
        });

        //submit_button
        $(document).on("click", ".submit_button", function() {
            //get all buying price value and check if any value is 0

            var total_price = $(".subtotal_input").val();
            console.log(total_price);

            if (total_price <= 0) {
                iziToast.warning({
                    title: "Buying price can't be 0",
                    position: "topRight",
                });
                return false;
            } else {
                //submit purchaseForm
                $("#purchaseForm").submit();
            }
        });

        //reset_button
        $(document).on("click", ".reset_button", function() {
            $("tbody").html("");
            tFootShowHide();
        });

        //table footer show hide function
        function tFootShowHide() {
            if ($("tbody tr").length == 0) {
                $(".submitAndReset").hide();
            } else {
                $(".submitAndReset").show();
            }
        }

        function calculate_sub_total(obj) {
            var has_sub_unit = $(obj).parents('tr').find('.has_sub_unit').val();
            var rate = $(obj).parents('tr').find('.rate').val();
            var sub_total = parseFloat($(obj).parents('tr').find('.main_qty').val() * rate);
            if (has_sub_unit == "true") {
                sub_total += parseFloat(($(obj).parents('tr').find('.sub_qty').val() / $(obj).parents('tr').find(
                    '.conversion').val()) * rate);
            } else {

            }
            // $(obj).val() * $(obj).parents('tr').find('.sale_qty').val();
            return sub_total;
        }

        // change rate
        $(document).on('keyup change', '.rate', function() {
            let subTotal = calculate_sub_total($(this));
            $(this).parents('tr').children('td').find('.sub_total').text(subTotal);
            $(this).parents('tr').children('td').find('.subtotal_input').val(subTotal);
            estimatedAmount();
        });
        
        // change rate
        // $(document).on('keyup change', '.rate', function() {
        //     let subTotal = calculate_sub_total($(this));
        //     $(this).parents('tr').children('td').find('.sub_total').text(subTotal);
        //     $(this).parents('tr').children('td').find('.subtotal_input').val(subTotal);
        //     estimatedAmount();
        // });

        // change qty
        $(document).on('keyup change', '.main_qty', function() {
            // let rate = $(this).parents('tr').find('.rate').val();
            let subTotal = calculate_sub_total($(this));
            $(this).parents('tr').children('td').find('.sub_total').text(subTotal);
            $(this).parents('tr').children('td').find('.subtotal_input').val(subTotal);
            estimatedAmount();
        });
        
        $(document).on('keyup change', '.sub_qty', function() {
            // let rate = $(this).parents('tr').find('.rate').val();
            let subTotal = calculate_sub_total($(this));
            $(this).parents('tr').children('td').find('.sub_total').text(subTotal);
            $(this).parents('tr').children('td').find('.subtotal_input').val(subTotal);
            estimatedAmount();
        });

        // Remove DOM
        $(document).on('click', '.remove', function() {
            $(this).parents('tr').remove();
            count--;
        });

        //estimatedAmount function
        function estimatedAmount() {
            var sum = 0;

            $(".subtotal_input").each(function() {
                var value = $(this).val();
                if (!isNaN(value) && value.length != 0) {
                    sum += parseFloat(value);
                }
            });
            $("input[name='estimated_amount']").val(sum);
            totalAmount();
            due_calculation();
        }
        //due calculation
        function due_calculation() {
            var total_amount = parseFloat($("input[name='total_amount']").val());

            var paid_amount = parseFloat($("input[name='paidAmount']").val());

            var due_amount = $("input[name='due_amount']").val();

            due_amount = total_amount - paid_amount;

            $("input[name='due_amount']").val(due_amount);
        }

        //total amount calculation
        function totalAmount() {
            var discount_amount = parseFloat($("input[name='discount_amount']").val());
            var estimated_amount = parseFloat(
                $("input[name='estimated_amount']").val()
            );

            var total_amount = estimated_amount - discount_amount;

            $("input[name='total_amount']").val(total_amount);
            //set max value for paid amount

            $("input[name='paid_amount']").attr("max", total_amount);
            //set max value for due amount

            $("input[name='due_amount']").attr("max", total_amount);

            var due = $("input[name='due_amount']").val();

            $("input[name='paid_amount']").val(total_amount - due);
        }
    </script>
@endpush
