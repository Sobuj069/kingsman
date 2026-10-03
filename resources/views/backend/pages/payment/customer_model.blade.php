<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="paymentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title font-weight-bold" id="paymentModalLabel">{{ __('Pay Customer') }}</h5>
                <button type="button" class="close_modal_btn" data-dismiss="modal" aria-label="Close">
                    <i class="feather icon-x"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="">
                                    <form class="row g-3 needs-validation" method="POST" action="{{ route('payment.pay-customer-store') }}" enctype="multipart/form-data">
                                        @csrf
                                        @if (auth()->user()->branch_id == 1)
                                            <div class="mt-2 col-md-12">
                                                <label class="form-label fw-bold">Branch *</label>
                                                <select class="select2" name="branch_id" id="branch_id" required>
                                                    <option value="">Select Branch</option>
                                                    @foreach ($allBranch as $branch)
                                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @else
                                            <input type="hidden" name="branch_id" id="branch_id"
                                                value="{{ auth()->user()->branch_id }}">
                                        @endif
                                        {{-- Date --}}
                                        <div class="mt-2 col-md-6">
                                            <label class="form-label font-weight-bold">Date</label>
                                            <input type="date" class="form-control" value="{{ date('Y-m-d') }}" name="date" required>
                                            <div class="error">{{ (isset($errors) && $errors->has('date')) ? $errors->first('date') : '' }}</div>
                                        </div>

                                        {{-- Supplier --}}
                                        <div class="mt-2 col-md-6">
                                            <label class="form-label font-weight-bold">Customer *</label>
                                            <select class="select2 customer_id" id="customer_id" name="customer_id" required>
                                                <option selected value="">Select Customer</option>
                                                {{-- @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                                @endforeach --}}
                                            </select>
                                            <input type="hidden" name="account_type" value="Customer">
                                            <div class="error">{{ (isset($errors) && $errors->has('account_type')) ? $errors->first('account_type') : '' }}</div>
                                            <div class="error">{{ (isset($errors) && $errors->has('customer_id')) ? $errors->first('customer_id') : '' }}</div>
                                        </div>

                                        {{-- Details --}}
                                        <div class="mt-2 col-md-12 text-center" id="details">
                                            <strong>Total (<strong id="due_invoice"></strong>) Invoice Due : <span id="total_invoice_due"></span> {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }} </strong>
                                            <span id="id_hint"></span>
                                            <hr>
                                            <strong><span class="wb_text">Personal Balance : </span><span id="wallet_balance"></span> {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }} </strong>
                                            <span id="wb_hint"></span>
                                            <input type="hidden" name="invoice_due" class="invoice_due" value="">
                                            <input type="hidden" name="wallet_balance" class="wallet_balance" value="">
                                        </div>

                                        {{-- wallet_type --}}
                                        <div class="mt-2 col-md-6">
                                            <label class="form-label fw-bold">Wallet Type *</label>
                                            <select class="select2 wallet_type" name="wallet_type" id="wallet_type" required>
                                                <option selected value="">Select Wallet</option>
                                                    <option value="Due Adjust">Due Adjust</option>
                                                    <option value="Balance Adjust">Balance Adjust</option>
                                            </select>
                                            <div class="error">{{ (isset($errors) && $errors->has('wallet_type')) ? $errors->first('wallet_type') : '' }}</div>
                                        </div>

                                        {{-- wallet_type --}}
                                        <div class="mt-2 col-md-6">
                                            <label class="form-label fw-bold">Pay Type *</label>
                                            <select class="select2 pay_type" name="pay_type" id="pay_type" required>
                                                <option selected value="">Select Pay</option>
                                            </select>
                                            <div class="error">{{ (isset($errors) && $errors->has('pay_type')) ? $errors->first('pay_type') : '' }}</div>
                                        </div>

                                        {{-- Amount --}}
                                        <div class="mt-2 col-md-6">
                                            <label for="amount" class="form-label fw-bold">Amount *</label>
                                            <input type="number" class="form-control" min="1" step="any"
                                                placeholder="Enter Amount" name="amount">
                                            <div class="error">{{ (isset($errors) && $errors->has('amount')) ? $errors->first('amount') : '' }}</div>
                                        </div>
                                        {{-- discount --}}
                                        <div class="mt-2 col-md-6">
                                            <label for="discount" class="form-label fw-bold">Discount</label>
                                            <input type="number" class="form-control" min="1" step="any"
                                                placeholder="Enter Amount" name="discount_amount">
                                            <div class="error">{{ (isset($errors) && $errors->has('discount_amount')) ? $errors->first('discount_amount') : '' }}</div>
                                        </div>

                                        {{-- Supplier --}}
                                        <div class="mt-2 col-md-6">
                                            <label class="form-label font-weight-bold">Bank Account *</label>
                                            <select class="select2" name="bank_id" required>
                                                <option selected value="">Select Account</option>
                                                @foreach ($bank_accounts as $bank_account)
                                                    <option value="{{ $bank_account->id }}">
                                                        {{ $bank_account->bank_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Details --}}
                                        <div class="mt-2 col-md-6">
                                            <label for="details" class="form-label fw-bold">Note</label>
                                            <textarea name="note" class="form-control" rows="3"></textarea>
                                        </div>

                                        {{-- Submit Button --}}
                                        <div class="mt-2 col-md-12">
                                            <button class="btn save_btn float-right" type="submit">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
