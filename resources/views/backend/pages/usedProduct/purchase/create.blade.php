@extends('backend.layouts.master')
@section('section-title', __('Used Product'))
@section('page-title', __('New Buy'))
@section('action-button')
    <a href="{{ route('usedPurchase.index') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-list"></i>
        {{ __('Buy List') }}
    </a>
@endsection

@section('content')
    <div class="row width-auto">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <form class="needs-validation" novalidate id="purchaseForm" action="{{ route('usedPurchase.store') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            {{-- Purchase Date --}}
                            <div class="mb-3 col-md-6">
                                <label class="form-label font-weight-bold">
                                    {{ __('Date *') }}
                                </label>
                                <input type="date" class="form-control" value="{{ date('Y-m-d') }}" name="date"
                                    required>
                            </div>
                            {{-- Purchase No --}}
                            <div class="mb-3 col-md-6">
                                <label for="validationCustom02" class="form-label font-weight-bold">{{ __('Buy No *') }}</label>
                                <input type="text" class="form-control" value="{{ $purchase_no }}" name="purchase_no"
                                    required readonly>
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
                                <div class="row align-items-end">
                                    <div class="col-10 pr-0">
                                        <label class="form-label font-weight-bold mb-1">{{ __('Supplier *') }}</label>
                                        <select class="select2" id="supplier" name="supplier_id" required>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-2 pl-2">
                                        <a href="#" data-toggle="modal" data-target="#addModal"
                                            class="btn extra_btn shadow-sm">
                                            <i class="feather icon-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="form-row">
                            {{-- Product --}}
                            <div class="mb-3 col-md-12 text-center">
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
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-12 table-responsive">
                                <table class="table text-center table-sm">
                                    <thead>
                                        <tr class="container-fluid header_bg">
                                            {{-- <th width="5%">#SL</th> --}}
                                            <th class="header_style_left" width="25%">{{ __('Product') }}</th>
                                            <th width="10%">{{ __('Rate') }}</th>
                                            <th width="35%">{{ __('Qty') }}</th>
                                            <th width="20%">{{ __('Sub Total') }}</th>
                                            <th class="header_style_right" width="5%">
                                                <i class="fa fa-trash"></i>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="table_body">

                                    </tbody>
                                    <tfoot>
                                        <tr class="">
                                            <td colspan="3" class="text-right fw-bold">{{ __('Grand Total') }}</td>
                                            <td colspan="1">
                                                <input type="number" step="any" style="min-width:100px"
                                                    name="estimated_amount" value="0" class="form-control" readonly>
                                            </td>
                                            <td class="text-right"></td>
                                        </tr>
                                        {{-- Discount --}}
                                        <tr class="">
                                            <td colspan="3" class="text-right fw-bold">{{ __('Discount') }}</td>
                                            <td colspan="1">
                                                <input type="number" step="any" name="discount_amount" min="0"
                                                    value="0" class="form-control discount_amount" required>
                                            </td>
                                            <td class="text-right"></td>
                                        </tr>
                                        {{-- Total Amount --}}
                                        <tr class="">
                                            <td colspan="3" class="text-right fw-bold">{{ __('Payable Amount') }}</td>
                                            <td colspan="1">
                                                <input type="number" step="any" name="total_amount" min="0"
                                                    value="0" class="form-control total_amount" readonly>
                                            </td>
                                            <td class="text-right"></td>
                                        </tr>
                                        {{-- Payment Method --}}
                                        <tr class="">
                                            <td colspan="3" class="text-right fw-bold text-black">{{ __('Bank Account') }}</td>
                                            <td colspan="1">
                                                <select class="select2" name="bank_id" required>
                                                    @foreach ($bank_accounts as $bank_account)
                                                        <option value="{{ $bank_account->id }}">
                                                            {{ $bank_account->bank_name }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="text-right"></td>
                                        </tr>
                                        {{-- Paid Amount --}}
                                        <tr class="">
                                            <td colspan="3" class="text-right fw-bold text-black">{{ __('Paid Amount') }}</td>
                                            <td colspan="1">
                                                <input type="number" step="any" name="paid_amount" min="0"
                                                    value="0" class="form-control paid_amount" readonly required>
                                            </td>
                                            <td class="text-right"></td>
                                        </tr>
                                        {{-- Due Amount in --}}
                                        <tr class=" due_amount" hidden>
                                            <td colspan="3" class="text-right fw-bold">{{ __('Due Amount') }}</td>
                                            <td colspan="1">
                                                <input type="number" step="any" name="due_amount" min="0"
                                                    value="0" class="form-control due_amount" readonly>
                                            </td>
                                            <td class="text-right"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                {{-- Submit & Reset Button --}}
                                <div class="row submitAndReset" style="display: block;">
                                    <div class="text-center col-md-12">
                                        <button type="reset" class="btn btn-danger col-sm-4 reset_button" style="padding: 5px 20px; background: #f04438;color:white; border-radius:5px">{{ __('Reset') }}</button>
                                        <button class="btn col-sm-6 submit_button" style="padding: 5px 20px; background: #12b76a;color:white; border-radius:5px;margin-right: 5px">{{ __('Submit') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Add Modal --}}
    <form action="#" id="supplierForm" method="POST">
        @csrf
        <x-add-modal title="{{ __('Add Supplier') }}" sizeClass="modal-lg">
            <x-input label="{{ __('Supplier Name *') }}" type="text" name="name" placeholder="{{ __('Enter Supplier Name') }}" required
                md="6" />
            <x-input label="{{ __('Email') }}" type="email" name="email" placeholder="{{ __('Enter Email') }}" md="6" />
            <x-input label="{{ __('Phone *') }}" type="text" name="phone" placeholder="{{ __('Enter Phone') }}" required md="6" />
            <x-input label="{{ __('Address') }}" type="text" name="address" placeholder="{{ __('Enter Address') }}" md="6" />
            <x-input label="{{ __('Opening Receivable') }}" type="text" name="open_receivable" value="0" md="6" />
            <x-input label="{{ __('Opening Payable') }}" type="text" name="open_payable" value="0" md="6" />
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

        var localData = localStorage.getItem('pur-items') ? JSON.parse(localStorage.getItem('pur-items')) : [];

        function showList() {
            if (localData.length <= 0) {
                $("#table_body").html(empty);
            } else {
                localData.forEach((item, index) => {
                    domPrepend(item, index);
                });
            }
        }

        showList();
        // estimatedAmount();

        var cartList = [];


        //category modal ajax code
        $(document).ready(function() {
            $('#supplierForm').on('submit', function(e) {
                e.preventDefault(); // Prevent the default form submission

                $.ajax({
                    url: "{{ route('supplier.store') }}", // Define the route for submission
                    method: 'POST',
                    data: $(this).serialize(), // Serialize form data
                    success: function(response) {
                        // Reload the table data dynamically
                        $('#addModal').modal('hide');
                        // Clear form
                        $('#supplierForm')[0].reset();

                        // Add the new customer to the dropdown without appending manually
                        // Reset and reload the dropdown
                        $('#supplier').load(location.href + ' #supplier>*');

                    },
                    error: function(xhr) {
                        // Handle validation errors or server errors here
                        console.error(xhr.responseText);
                    }
                });
            });
        });
        // Select the input field when the page loads
        window.onload = function() {
            var inputField = document.getElementById('product_search');
            inputField.select();
        };
        var count = 0;
        var db_pid_array;

        $(document).ready(function() {
            $("#product_search").autocomplete({
                source: function(req, res) {
                    let url = "{{ route('used-product-search') }}";
                    $.get(url, {
                        req: req.term
                    }, (data) => {
                        console.log(data);
                        res($.map(data, function(item) {
                            return {
                                id: item.id,
                                value: item.name + " - " + item.barcode,
                                price: item.purchase_price
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
                    let url = "{{ route('used-search-product-id', 'my_id') }}".replace('my_id', ui.item.id);
                    $.get(url, (data) => {
                        // product add
                        // var quantity_data = '';
                        // if (data.product.unit.related_unit == null) {
                        //     quantity_data =
                        //         `<input type="text" class="has_sub_unit" hidden value="false">
                    //             <label class="ml-4 mr-2" style="padding-top: 8px;">${data.product.unit.name}:</label>
                    //             <input type="number" value="" class="form-control col main_qty" name="new_main_qty[${data.product.id}]"  onkeydown="return event.keyCode !== 190" min="1">`;
                        // } else {
                        //     quantity_data =
                        //         `<input type="text" class="has_sub_unit" hidden value="true">
                    //             <input type="text" class="conversion" hidden value="${data.product.unit.related_value}">
                    //             <label class="mr-2 ml-4" style="padding-top: 8px;">${data.product.unit.name}:</label>
                    //             <input type="number" value="" class="form-control col main_qty mr-4" name="new_main_qty[${data.product.id}]"  onkeydown="return event.keyCode !== 190" min="1">
                    //             <label class="mr-2" style="padding-top: 8px;">${data.product.unit.related_unit.name}:</label>
                    //             <input type="number" value="0" class="form-control col sub_qty" name="new_sub_qty[${data.product.id}]"  onkeydown="return event.keyCode !== 190" min="1">`;
                        // }
                        // <input type="number" value="1" class="form-control sale_qty" name="qty[]">
                        // let row = `
                    //     <tr>
                    //         <td>${count}</td>
                    //         <td>
                    //         ${data.product.name + " - " + data.product.barcode}
                    //         <input type="hidden" value="${data.product.id}" name="new_product[${data.product.id}]" class="product">
                    //         <input type="hidden" value="${data.product.name}" name="product_name[${data.product.id}]" />
                    //         </td>
                    //         <td >
                    //             <div style="width:100px" class="form-row"> 
                    //                 <input type="text" value="${data.product.purchase_price}" class="form-control rate" name="new_rate[${data.product.id}]">
                    //             </div>
                    //         </td>
                    //         <td class="" >
                    //             <div class="form-row" style="width:300px" >
                    //                 ${quantity_data}
                    //             </div>
                    //         </td>
                    //         <td>
                    //         <strong><span class="sub_total">0</span> {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</strong>
                    //         <input type="hidden" name="new_subtotal_input[${data.product.id}]" class="subtotal_input" value="0">
                    //         </td>
                    //         <td>
                    //         <a href="#" class="remove">
                    //             <i class="fa fa-trash"></i>
                    //         </a>
                    //         </td>
                    //     </tr>`;

                        // Duplicate check
                        // let tableBody = document.querySelector('#table_body');
                        // let products = tableBody.querySelectorAll('.product');
                        // let isDuplicate = false;
                        // products.forEach(function(item) {
                        //     if (ui.item.id == item.value) {
                        //         isDuplicate = true;
                        //     } else {
                        //         isDuplicate = false;
                        //     }
                        // });

                        if (pExist(data.product.id) == true) {
                            iziToast.warning({
                                title: "Product already added. Increase Quantity.",
                                position: "topRight",
                            });
                            return false;
                        } else {
                            // $("#table_body").append(row);
                            addProductToCard(data);
                        }
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

        function pExist(pid) {
            let ldata = localStorage.getItem('pur-items') ? JSON.parse(localStorage.getItem('pur-items')) : [];
            return ldata.some(function(el) {
                return el.product.id === pid
            });
        }

        function storedata(data) {
            if (localStorage.getItem('pur-items') != null) {
                cartList = JSON.parse(localStorage.getItem('pur-items'))
                cartList.push(data);
            } else {
                cartList.push(data);
            }
            localStorage.setItem('pur-items', JSON.stringify(cartList));
        }

        function addProductToCard(data) {
            storedata(data);
            var x = 0;
            domPrepend(data, x++);
            estimatedAmount();
        }

        function domPrepend(data = null, index = null) {
            // var name = data.product.name;
            var quantity_data = '';

                if (data.product.unit.related_unit == null) {
                    quantity_data =
                        `<input type="text" class="has_sub_unit" hidden value="false">
                            <label class="ml-4 mr-2" style="padding-top: 8px;">${data.product.unit.name}:</label>
                            <input type="number" value="" class="form-control col main_qty" name="new_main_qty[${data.product.id}]"  onkeydown="return event.keyCode !== 190" min="1">`;
                } else {
                    quantity_data =
                        `<input type="text" class="has_sub_unit" hidden value="true">
                            <input type="text" class="conversion" hidden value="${data.product.unit.related_value}">
                            <label class="mr-2 ml-4" style="padding-top: 8px;">${data.product.unit.name}:</label>
                            <input type="number" value="" class="form-control col main_qty mr-4" name="new_main_qty[${data.product.id}]"  onkeydown="return event.keyCode !== 190" min="1">
                            <label class="mr-2" style="padding-top: 8px;">${data.product.unit.related_unit.name}:</label>
                            <input type="number" value="0" class="form-control col sub_qty" name="new_sub_qty[${data.product.id}]"  onkeydown="return event.keyCode !== 190" min="1">`;
                }

            let dom = `
                <tr>
                    
                    <td>
                    ${data.product.name + " - " + data.product.barcode}
                    <input type="hidden" value="${data.product.id}" name="new_product[${data.product.id}]" class="product">
                    <input type="hidden" value="${data.product.name}" name="product_name[${data.product.id}]" />
                    </td>
                    <td >
                        <div style="width:100px" class="form-row"> 
                            <input type="text" value="${data.product.purchase_price}" class="form-control rate" name="new_rate[${data.product.id}]">
                        </div>
                    </td>
                    <td class="" >
                        <div class="form-row" style="width:300px" >
                            ${quantity_data}
                        </div>
                    </td>
                    <td>
                    <strong><span class="sub_total">0</span> {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</strong>
                    <input type="hidden" name="new_subtotal_input[${data.product.id}]" class="subtotal_input" value="0">
                    </td>
                    <td>
                    <a href="#" class="remove" data-value="${index}">
                        <i class="fa fa-trash"></i>
                    </a>
                    </td>
                </tr>
            `;
            $("#table_body").prepend(dom);
        }

        //calculate total amount with order tax is percentage shipping charge,others charge and discount on keyup and change
        $(document).on(
            "keyup change",
            "input[name='discount_amount']",
            function() {
                totalAmount();
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

            if (total_price <= 0) {
                iziToast.warning({
                    title: "Buying price can't be 0",
                    position: "topRight",
                });
                return false;
            } else {
                //submit purchaseForm
                $("#purchaseForm").submit();
                localStorage.clear();
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
            let itemIndex = $(this).attr('data-value');
            localData.splice(itemIndex, 1);
            localStorage.removeItem('pur-items');
            localStorage.setItem('pur-items', JSON.stringify(localData))
            $(this).parents('tr').remove();
            estimatedAmount();
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

            var paid_amount = parseFloat($("input[name='paid_amount']").val());

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
