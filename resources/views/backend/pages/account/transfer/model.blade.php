<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">{{ __('Add Transfer') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form class="row" method="POST"
                    action="{{ route('bank-transfer-store') }}" enctype="multipart/form-data">
                    @csrf
                    @if (auth()->user()->branch_id == 1)
                        <div class="mb-3 col-md-12">
                            <label class="form-label fw-bold">{{ __('Branch *') }}</label>
                            <select class="select2" name="branch_id" id="branch_id" required>
                                @foreach ($allBranch as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="branch_id" id="branch_id"
                            value="{{ auth()->user()->branch_id }}">
                    @endif
                    {{-- From Account --}}
                    <div class="mb-3 col-md-12">
                        <label class="form-label fw-bold">{{ __('From Account *') }}</label>
                        <select class="select2 from_bank_id" name="from_bank_id" id="from_bank_id"
                            required>
                            <option selected value="">{{ __('Select Account') }}</option>
                            @foreach ($all_accounts as $bank_account)
                                <option value="{{ $bank_account->id }}">
                                    {{ $bank_account->bank_name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="error"><b id="from_amount"></b></div>
                        <input type="hidden" name="from_amount" class="from_amount">
                        <div class="error">
                            {{ $errors->has('from_bank_id') ? $errors->first('from_bank_id') : '' }}
                        </div>
                    </div>

                    {{-- To Account --}}
                    <div class="mb-3 col-md-12">
                        <label class="form-label fw-bold">{{ __('To Account *') }}</label>
                        <select class="select2 to_bank_id" name="to_bank_id" id="to_bank_id"
                            required>
                            <option selected value="">{{ __('Select Account') }}</option>
                        </select>
                        <div class="error"><b id="to_amount"></b></div>
                        <div class="error">
                            {{ $errors->has('to_bank_id') ? $errors->first('to_bank_id') : '' }}
                        </div>
                    </div>

                    {{-- Date --}}
                    <div class="mb-3 col-md-12">
                        <label class="form-label font-weight-bold">{{ __('Date') }}</label>
                        <input type="date" class="form-control" value="{{ date('Y-m-d') }}"
                            name="date" required>
                        <div class="error">{{ $errors->has('date') ? $errors->first('date') : '' }}
                        </div>
                    </div>

                    {{-- Transfer Amount --}}
                    <div class="mb-3 col-md-12">
                        <label for="amount" class="form-label fw-bold">{{ __('Transfer Amount *') }}</label>
                        <input type="number" class="form-control" min="1" step="any"
                            placeholder="{{ __('Enter Transfer Amount') }}" name="transfer_amount">
                        <div class="error">
                            {{ $errors->has('transfer_amount') ? $errors->first('transfer_amount') : '' }}
                        </div>
                    </div>

                    {{-- Details --}}
                    <div class="mb-3 col-md-12">
                        <label for="details" class="form-label fw-bold">{{ __('Note') }}</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="{{ __('Enter Note') }}"></textarea>
                    </div>

                    {{-- Submit Button --}}
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn cancel_btn" data-dismiss="modal">{{ __('Close') }}</button>
                        @if (check_permission('bank-transfer-store'))
                            <button class="btn save_btn" type="submit">{{ __('Save') }}</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
