@extends('backend.layouts.master')
@section('section-title', __('Account'))
@section('page-title', __('Withdraw'))

@if (check_permission('accounting.transaction-history'))
    @section('action-button')
        <a href="{{ url('transaction-history') }}" class="btn add_list_btn">
            <i class="mr-2 feather icon-list"></i>
            {{ __('Transaction History') }}
        </a>
    @endsection
@endif

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card card_style">
                <div class="card-body">
                    <div class="">
                        <form class="row g-3 needs-validation" method="POST" action="{{ route('withdraw-store') }}" enctype="multipart/form-data">
                            @csrf
                            @if (auth()->user()->branch_id == 1)
                                <div class="mt-2 col-md-6">
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
                            {{-- Date --}}
                            <div class="mt-2 col-md-6">
                                <label class="form-label font-weight-bold">{{ __('Date') }}</label>
                                <input type="date" class="form-control" value="{{ date('Y-m-d') }}" name="date" required>
                                <div class="error">{{ ($errors->has('date'))?$errors->first('date'):''; }}</div>
                            </div>

                            {{-- Account name --}}
                            <div class="mt-2 col-md-6">
                                <label class="form-label fw-bold">{{ __('Bank Account *') }}</label>
                                <select class="select2 bank_id" name="bank_id" required>
                                    <option selected value="">{{ __('Select Bank Account') }}</option>
                                    @foreach ($bank_accounts as $bank_account)
                                        <option value="{{ $bank_account->id }}">
                                            {{ $bank_account->bank_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="error"><b id="amount"></b></div>
                                <input type="hidden" name="from_amount" class="from_amount">
                                <div class="error">{{ ($errors->has('bank_id'))?$errors->first('bank_id'):''; }}</div>
                            </div>

                            {{-- Owner Name --}}
                            <div class="mt-2 col-md-6">
                                <label for="amount" class="form-label fw-bold">{{ __('Owner *') }}</label>
                                <select class="select2 " name="owner_id" required>
                                    <option selected value="">{{ __('Select Owner') }}</option>
                                    @foreach ($owners as $owner)
                                        <option value="{{ $owner->id }}">
                                            {{ $owner->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="error">{{ ($errors->has('owner_id'))?$errors->first('owner_id'):''; }}</div>
                            </div>
                            {{-- Withdraw Amount --}}
                            <div class="mt-2 col-md-6">
                                <label for="amount" class="form-label fw-bold">{{ __('Withdraw Amount *') }}</label>
                                <input type="number" class="form-control" min="1" step="any" placeholder="{{ __('Enter Withdraw Amount') }}" name="withdraw_amount" required>
                                <div class="error">{{ ($errors->has('withdraw_amount'))?$errors->first('withdraw_amount'):''; }}</div>
                            </div>

                            {{-- Details --}}
                            <div class="mt-2 col-md-6">
                                <label for="details" class="form-label fw-bold">{{ __('Note') }}</label>
                                <textarea name="note" class="form-control" rows="2"></textarea>
                            </div>

                            {{-- Submit Button --}}
                            <div class="mt-4 text-right col-md-12">
                                @if (check_permission('withdraw-store'))
                                    <button class="btn save_btn px-5" type="submit">
                                        <i class="feather icon-check-circle mr-1"></i> {{ __('Save') }}
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(function(){
            $(document).on('change', '.bank_id', function(){
            var bank_id = $(this).val();
            if (bank_id != '') {
                $.ajax({
                    url:"{{ route('get-account-balance') }}",
                    type:"GET",
                    data:{bank_id:bank_id},
                    success:function(data){
                        var balance = "{{ __('Current Balance') }} : "+data.balance+" {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}";
                        $('#amount').text(balance);
                        $('.from_amount').val(data.balance);
                    }
                });
            }
            });
        });
    </script>
@endpush
