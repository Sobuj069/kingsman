<div class="modal" id="payment_modal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg ">
        <div class="modal-content" style="border-radius: 25px;background:#f2f2f2">
            <div class="modal-header ">
                <h5 class="modal-title" id="paymentModalLabel">{{ __('Payment') }} > <span id="customer_name"></span>
                </h5>
                <button type="button"
                    style="border-radius: 25px;border:1px solid #eae9e9;padding:5px 10px;background:#fff"
                    class="btn-close " data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <div>
                <div class="text-center">
                    <label for="oneAccountPayment" style="display: none !important;">
                        <input type="radio" id="oneAccountPayment" name="payment_type" value="pos" checked>
                        {{ __('Single Account Payment') }}
                    </label>
                    <label for="multipleAccountPayment">
                        <input type="radio" id="multipleAccountPayment" name="payment_type" value="checking">
                        {{ __('Multiple Account Payment') }}
                    </label>
                </div>
                <!-- Your existing content for one account payment -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="modal-body">
                            <div class="row">
                                {{-- payment method --}}
                                <div class="col-md-6">
                                    @if (env('APP_ONLINE') == 'yes')
                                        <div class="form-group">
                                            <h5 class="text-center">{{ __('Selling Option') }}</h5>
                                            <label class="form-label font-weight-bold">{{ __('Sale Types') }}</label>
                                            <select class="select2" id="sale_type" name="sale_type" required>
                                                <option value="Outlet">{{ __('Outlet') }}</option>
                                                <option value="Online">{{ __('Online') }}</option>
                                                <option value="Condition">{{ __('Condition') }}</option>
                                                <option value="Pre-Order">{{ __('Pre-Order') }}</option>
                                            </select>
                                            <div id="pre_order_notice" style="display:none;" class="mt-2 alert alert-warning py-2 text-xs font-weight-bold">
                                                <i class="feather icon-clock mr-1"></i> {{ __('Pre-Order selected: This order will go to the Pre-Order List without deducting physical stock.') }}
                                            </div>
                                            <div style="display:none;" id="agent_details">
                                                <label class="form-label font-weight-bold">{{ __('Courier Type') }}</label>
                                                <select class="select2" id="courier_type" name="courier_type" required>
                                                    <option value="">{{ __('Select Courier') }}</option>
                                                    <option value="Pathao">Pathao</option>
                                                    @if (env('STEADFAST_API_KEY') != null || get_setting('steadfast_api_key') != null)
                                                        <option value="Stead Fast">Stead Fast</option>
                                                    @endif
                                                </select>

                                                <div style="display:none;" id="pathao_details">
                                                    <label class="form-label font-weight-bold">{{ __('Shop') }} <span
                                                            class="danger">*</span></label>
                                                    {{-- <select class="select2" id="shop_id" name="shop_id">
                                                    @foreach ($shops['data'] as $shop)
                                                        <option value="{{ $shop['store_id'] }}">
                                                            {{ $shop['store_name'] }}</option>
                                                    @endforeach
                                                </select> --}}
                                                    <label for="" class="form-label fw-bold">{{ __('Special Instructions') }}</label>
                                                    <input type="text" class="form-control"
                                                        name="special_instruction" id="">

                                                    <label for="" class="form-label fw-bold"> {{ __('Quantity') }}*</label>
                                                    <input type="number" class="form-control" name="item_quantity"
                                                        min="1" value="1" id="">

                                                    <label for="" class="form-label fw-bold">{{ __('Total Weight') }}*</label>
                                                    <input type="number" class="form-control" name="item_weight"
                                                        id="" value="0.5">
                                                    <label for="" class="form-label fw-bold">{{ __('description') }}</label>
                                                    <input type="text" class="form-control" name="item_description"
                                                        id="">
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <input type="hidden" id="sale_type" name="sale_type" value="Outlet">
                                    @endif
                                    <div class="col-md-12">
                                        {{-- single account payment --}}
                                        <div class="row section" id="pos">
                                            <div class="col-md-12">
                                                <label class="form-label font-weight-bold text-black">{{ __('Bank Account') }}</label>
                                                <select class="select2" name="bank_id"
                                                    style="border-top-left-radius: 25px" required>
                                                    @foreach ($bank_accounts as $bank_account)
                                                        <option value="{{ $bank_account->id }}">
                                                            {{ $bank_account->bank_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="form-group mt-3">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h5 class="text-center text-black "
                                                                style="font-size: 16px;margin-top:5px"> <strong> {{ __('Payment Option') }} </strong></h5>
                                                        </div>
                                                        <div class="col-md-6 text-right">
                                                            <a class="text-white btn full_pay_btn"
                                                                style="padding: 3px 20px; background: #12b76a;border-radius:25px;margin-right: 5px">
                                                                <i class="fa-solid fa-circle-plus"></i>
                                                                {{ __('Paid') }}
                                                            </a>
                                                            <a class="text-white btn btn-danger full_due_btn"
                                                                style="padding: 3px 20px; background: #f04438;border-radius:25px">
                                                                <i class="fa-solid fa-circle-minus"></i>
                                                                {{ __('Due') }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- multiple account payment --}}
                                        <div class="row section" id="checking" style="display: none;">
                                            <div class="col-md-12">
                                                <label class="form-label font-weight-bold text-black">{{ __('Bank Account') }}</label>
                                                <div id="bankAccountList">
                                                    @foreach ($bank_accounts as $bank_account)
                                                        <div class="bank-account-entry"
                                                            data-bank-id="{{ $bank_account->id }}">
                                                            <div class="row align-items-center">
                                                                <div class="col-md-6">
                                                                    <strong
                                                                        class="text-black">{{ $bank_account->bank_name }}</strong>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <input type="number"
                                                                        name="amounts[{{ $bank_account->id }}]"
                                                                        class="form-control bank-amount-input"
                                                                        placeholder="{{ __('Enter amount for') }} {{ $bank_account->bank_name }}"
                                                                        min="0" value="0">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mt-3 input-group input-group-lg">
                                            <span class="input-group-text"
                                                style="border:none;border-right:none;border-top-left-radius: 25px;border-bottom-left-radius: 25px;background:#000ce2;color:white;font-size:14px">{{ __('Pay Amount') }}</span>
                                            <input type="number"
                                                style="border-top-right-radius: 25px;border-bottom-right-radius: 25px;background:#f2f2f2;"
                                                class="form-control pay_amount" name="pay_amount" min="0"
                                                value="">
                                        </div>
                                        <div class="mt-3 input-group input-group-lg pay_amount_div">
                                            <span class="input-group-text"
                                                style="border:none;border-right:none;border-top-left-radius: 25px;border-bottom-left-radius: 25px;background:#000ce2;color:white;font-size:14px">{{ __('Pay Point') }}</span>
                                            <input type="number"
                                                style="border-top-right-radius: 25px;border-bottom-right-radius: 25px;background:#f2f2f2;"
                                                class="form-control pay_point" name="pay_point" min="0"
                                                value="">
                                        </div>
                                        <div class="form-group mt-3 text-left">
                                            <label for="" class="form-label fw-bold"> {{ __('Note') }}</label>
                                            <textarea name="note" class="form-control" style="border-radius: 15px;background:#f2f2f2" rows="2"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    {{-- order details --}}
                                    <h4 class="text-center">{{ __('Payment Details') }}</h4>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <tfoot>
                                                <tr id="point_row">
                                                    <th class="text-left w-60" colspan="2"> <strong
                                                            class="float-left">{{ __('Total Point') }} </strong> </th>
                                                    <td class="w-40 text-right"><strong> <span
                                                                class="total_point">0.00</span></strong></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-left w-60" colspan="2"> <strong
                                                            class="float-left">{{ __('Subtotal') }} </strong> </th>
                                                    <td class="w-40 text-right"><strong> <span
                                                                class="sub_total">0.00</span>
                                                            {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</strong>
                                                    </td>
                                                </tr>
                                                {{-- <tr>
                                                    <th class="text-left w-60" colspan="2"> <strong
                                                            class="float-left">Vat </strong> </th>
                                                    <td class="w-40 text-right"><strong> <span
                                                                class="vat_amount_text">0.00</span>
                                                            {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</strong>
                                                    </td>
                                                </tr> --}}
                                                @if (is_vat_enabled())
                                                <tr>
                                                    <th class="text-left w-60" colspan="2"> <strong
                                                            class="float-left">{{ __('VAT') }} </strong> </th>
                                                    <td class="w-40 text-right"><strong> <span
                                                                class="vat_amount_display">0.00</span>
                                                            {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</strong>
                                                    </td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <th class="text-left w-60" colspan="2"> <strong
                                                            class="float-left">{{ __('Discount') }} </strong> </th>
                                                    <td class="w-40 text-right"><strong> <span
                                                                class="discount_amount_display">0.00</span>
                                                            {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</strong>
                                                    </td>
                                                </tr>
                                                @if (env('APP_ONLINE') == 'yes')
                                                    <tr>
                                                        <th class="text-left w-60" colspan="2"> <strong
                                                                class="float-left">{{ __('Delivery Charge') }} </strong> </th>
                                                        <td class="w-40 text-right"><strong> <span
                                                                    class="delivery_charge">0.00</span>
                                                                TK</strong></td>
                                                    </tr>
                                                @endif
                                                <tr>
                                                    <th class="text-left w-60" colspan="2"> <strong
                                                            class="float-left">{{ __('Previous Due') }}</strong></th>
                                                    <input type="hidden" name="previous_due" id="previous_due">
                                                    <td class="w-40 text-right"><strong> <span
                                                                class="previous_due">0.00
                                                                {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</span>
                                                        </strong></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-left w-60" colspan="2"> <strong
                                                            class="float-left">{{ __('Payable Amount') }} <small>(<span
                                                                    class="total_item"></span> {{ __('items') }})</small></strong>
                                                    </th>
                                                    <input type="hidden" name="payable_amount" id="payable_amount"
                                                        value="">
                                                    <td class="w-40 text-right"><strong> <span
                                                                class="payable_amount">0.00</span>
                                                            {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</strong>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="text-left w-60" colspan="2"> <strong
                                                            class="float-left">{{ __('Paid Amount') }}</strong></th>
                                                    <input type="hidden" name="paid_amount" id="paid_amount"
                                                        value="">
                                                    <td class="w-40 text-right"><strong> <span
                                                                class="paid_amount">0.00</span>
                                                            {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</strong>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="text-left w-60" colspan="2"> <strong
                                                            class="float-left">{{ __('Due Amount') }}</strong></th>
                                                    <input type="hidden" name="due_amount" id="due_amount"
                                                        value="">
                                                    <td class="w-40 text-right"><strong> <span
                                                                class="due_amount">0.00</span>
                                                            {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</strong>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th class="text-left w-60" colspan="2"> <strong
                                                            class="float-left">{{ __('Balance') }}</strong></th>
                                                    <input type="hidden" name="balance" id="balance"
                                                        value="">
                                                    <td class="w-40 text-right"><strong> <span
                                                                class="balance">0.00</span>
                                                            {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}</strong>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn cancel_btn" data-dismiss="modal">{{ __('Close') }}</button>
                <button type="button" class="btn save_btn" id="checkout">{{ __('Checkout') }}</button>
            </div>
        </div>
    </div>
</div>
{{-- @push('js')
    <script>
        $('#sale_type').on('change', function() {
            // Get the selected option's value
            var selectedValue = $(this).val();
            if (selectedValue == 'Online') {
                $('#agent_details').show();
            } else {
                $('#agent_details').hide();
            }
        });
        $('#courier_type').on('change', function() {
            // Get the selected option's value
            var selectedValue = $(this).val();
            if (selectedValue == 'Pathao') {
                $('#pathao_details').show();
            } else {
                $('#pathao_details').hide();
            }
        });
        document.querySelectorAll('input[name="payment_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'pos') {
                    document.getElementById('pos').style.display = 'block';
                    document.getElementById('checking').style.display = 'none';
                } else if (this.value === 'checking') {
                    document.getElementById('pos').style.display = 'none';
                    document.getElementById('checking').style.display = 'block';
                }
            });
        });
    </script>
    <script>
        $('#checkout').on('click', function(e) {
            e.preventDefault(); // prevent default action if inside a form
            var $btn = $(this);

            // যদি button already disabled থাকে, warning দেখাও
            if ($btn.prop('disabled')) {
                iziToast.warning({
                    title: 'Wait',
                    message: 'Please wait 10 seconds before clicking again.'
                });
                return;
            }

            // Disable button
            $btn.prop('disabled', true);

            // Optional: button text পরিবর্তন করে দেখাতে পারো
            var originalText = $btn.text();
            $btn.text('Processing...');

            // 10 seconds পর button enable হবে
            setTimeout(function() {
                $btn.prop('disabled', false);
                $btn.text(originalText);
            }, 10000); // 10 seconds
        });
    </script>
    <script>
        // JavaScript to toggle sections
        document.querySelectorAll('input[name="payment_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'pos') {
                    document.getElementById('pos').style.display = 'block';
                    document.getElementById('checking').style.display = 'none';
                } else if (this.value === 'checking') {
                    document.getElementById('pos').style.display = 'none';
                    document.getElementById('checking').style.display = 'block';
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            function updateAmounts() {
                var payableAmount = parseFloat($('#payable_amount').val()) ||
                    0; // Payable Amount (এটা স্ট্যাটিক থাকবে)
                var payPoint = parseFloat($('.pay_point').val()) || 0; // Pay Point (ইউজার ইনপুট)
                var payAmount = parseFloat($('.pay_amount').val()) || 0; // Pay Amount (ইউজার ইনপুট)

                // Paid Amount = Pay Point + Pay Amount
                var paidAmount = payPoint + payAmount;

                // Due Amount = Payable Amount - Paid Amount
                var dueAmount = payableAmount - paidAmount;
                if (dueAmount < 0) dueAmount = 0; // নেগেটিভ হলে 0 দেখাবে

                // UI তে আপডেট
                $('.paid_amount').text(paidAmount.toFixed(2));
                $('.due_amount').text(dueAmount.toFixed(2));

                // Hidden Input ফিল্ডেও আপডেট
                $('#paid_amount').val(paidAmount);
                $('#due_amount').val(dueAmount);
            }

            // Pay Point অথবা Pay Amount ইনপুটে কিছু টাইপ করলে আপডেট হবে
            $('.pay_point, .pay_amount').on('input', updateAmounts);

            // পেজ লোড হওয়ার পরও ফাংশন রান করাবো
            updateAmounts();
        });
    </script>
    <script>
        function updatePayAmount() {
            let total = 0;

            // Iterate over all bank account input fields and sum their values
            document.querySelectorAll('.bank-amount-input').forEach(input => {
                const value = parseFloat(input.value) || 0; // Use 0 if the value is empty or invalid
                total += value;
            });
            // Update the Pay Amount field
            document.querySelector('.pay_amount').value = total.toFixed();
            document.querySelector('#paid_amount').value = total.toFixed(2);
            document.querySelector('.paid_amount').textContent = total.toFixed(2);

            let total_due = document.querySelector('#payable_amount').value;
            let due = total_due - total;
            document.querySelector('#due_amount').value = due.toFixed(2);
            document.querySelector('.due_amount').textContent = due.toFixed(2);
        }

        // Add event listener to each bank account input field
        document.querySelectorAll('.bank-amount-input').forEach(input => {
            input.addEventListener('input', updatePayAmount);
        });
        // Initialize Pay Amount on page load
        document.addEventListener('DOMContentLoaded', updatePayAmount);







        $('#payment_modal').on('hidden.bs.modal', function() {
            // Clear pay inputs
            $('.pay_amount').val('');
            $('.pay_point').val('');

            // Reset paid amount
            $('#paid_amount').val(0);
            $('.paid_amount').text('0.00');

            // Set due amount = payable amount
            var payableAmount = parseFloat($('#payable_amount').val()) || 0;
            $('#due_amount').val(payableAmount.toFixed(2));
            $('.due_amount').text(payableAmount.toFixed(2));
        });
    </script>
@endpush --}}
@push('js')
<script>
    // Sale type toggle
    $('#sale_type').on('change', function() {
        if ($(this).val() == 'Online') {
            $('#agent_details').show();
        } else {
            $('#agent_details').hide();
        }
    });

    // Courier type toggle
    $('#courier_type').on('change', function() {
        if ($(this).val() == 'Pathao') {
            $('#pathao_details').show();
        } else {
            $('#pathao_details').hide();
        }
    });

    // Payment type toggle
    function togglePaymentType() {
        document.querySelectorAll('input[name="payment_type"]').forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'pos') {
                    document.getElementById('pos').style.display = 'block';
                    document.getElementById('checking').style.display = 'none';
                } else {
                    document.getElementById('pos').style.display = 'none';
                    document.getElementById('checking').style.display = 'block';
                }
            });
        });
    }
    togglePaymentType();

    // Checkout button click cooldown 10 seconds
    $('#checkout').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);

        if ($btn.prop('disabled')) {
            iziToast.warning({
                title: "{{ __('Wait') }}",
                message: "{{ __('Please wait 10 seconds before clicking again.') }}"
            });
            return;
        }

        $btn.prop('disabled', true);
        var originalText = $btn.text();
        $btn.text("{{ __('Processing...') }}");

        setTimeout(function() {
            $btn.prop('disabled', false);
            $btn.text(originalText);
        }, 5000);
    });

    // Update paid and due amounts when user types pay_amount or pay_point
    function updateAmounts() {
        var payableAmount = parseFloat($('#payable_amount').val()) || 0;
        var payPoint = parseFloat($('.pay_point').val()) || 0;
        var payAmount = parseFloat($('.pay_amount').val()) || 0;

        var paidAmount = payPoint + payAmount;
        var dueAmount = payableAmount - paidAmount;
        if (dueAmount < 0) dueAmount = 0;

        $('.paid_amount').text(paidAmount.toFixed(2));
        $('#paid_amount').val(paidAmount);

        $('.due_amount').text(dueAmount.toFixed(2));
        $('#due_amount').val(dueAmount.toFixed(2));
    }

    $(document).ready(function() {
        $('.pay_point, .pay_amount').on('input', updateAmounts);
        updateAmounts();
    });

    // Update pay amount based on multiple bank inputs
    function updatePayAmount() {
        let total = 0;
        document.querySelectorAll('.bank-amount-input').forEach(input => {
            total += parseFloat(input.value) || 0;
        });

        document.querySelector('.pay_amount').value = total.toFixed(2);
        $('#paid_amount').val(total.toFixed(2));
        $('.paid_amount').text(total.toFixed(2));

        let payableAmount = parseFloat($('#payable_amount').val()) || 0;
        let due = payableAmount - total;
        $('#due_amount').val(due.toFixed(2));
        $('.due_amount').text(due.toFixed(2));
    }

    document.querySelectorAll('.bank-amount-input').forEach(input => {
        input.addEventListener('input', updatePayAmount);
    });
    document.addEventListener('DOMContentLoaded', updatePayAmount);

    // Reset modal when closed
    $('#payment_modal').on('hidden.bs.modal', function() {
        // Clear inputs
        $('.pay_amount').val('');
        $('.pay_point').val('');
        $('.bank-amount-input').val('0');

        // Reset paid amount
        $('#paid_amount').val(0);
        $('.paid_amount').text('0.00');

        // Set due = payable amount
        var payableAmount = parseFloat($('#payable_amount').val()) || 0;
        $('#due_amount').val(0);
        $('.due_amount').text(0);

        // Reset payment type view
        $('#pos').show();
        $('#checking').hide();
        $('#oneAccountPayment').prop('checked', true);
    });

    // Prevent Courier orders for Walk-in Customer
    $(document).on('submit', '#payment_form', function(e) {
        var customerId = $('#customer_id').val();
        var saleType = $('#sale_type').val();
        var courierType = $('#courier_type').val();

        if ((customerId == '1' || customerId == 1) && (saleType === 'Online' || (courierType && courierType.trim() !== ''))) {
            e.preventDefault();
            alert("{{ __('Courier orders cannot be placed for Walk-in Customer. Please select or add a registered customer with valid phone and address.') }}");
            return false;
        }
    });
</script>
@endpush
