@extends('backend.layouts.master')
@section('section-title', __('Expense & Asset'))
@section('page-title', __('Add Asset'))

@section('action-button')
    <a href="{{ route('asset.index') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-list"></i>
        {{ __('Asset List') }}
    </a>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <div class="">
                        <form class="row g-3 needs-validation" method="POST" action="{{ route('asset.store') }}">
                            @csrf

                            @if (auth()->user()->branch_id == 1)
                                <div class="col-md-6 mt-2">
                                    <label for="branch_id" class="form-label fw-bold">{{ __('Branch *') }}</label>
                                    <select class="select2" name="branch_id">
                                        @foreach ($allBranch as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="errors">{{ $errors->has('branch_id') ? $errors->first('branch_id') : '' }}</div>
                                </div>
                            @endif

                            {{-- Date --}}
                            <div class="mt-2 {{ auth()->user()->branch_id == 1 ? 'col-md-6' : 'col-md-12' }}">
                                <label class="form-label font-weight-bold">{{ __('Purchase Date *') }}</label>
                                <input type="date" class="form-control" value="{{ date('Y-m-d') }}" name="purchase_date" required>
                                <div class="errors">{{ $errors->has('purchase_date') ? $errors->first('purchase_date') : '' }}</div>
                            </div>

                            {{-- Asset Name --}}
                            <div class="mt-2 col-md-6">
                                <label for="name" class="form-label fw-bold">{{ __('Asset Name *') }}</label>
                                <input type="text" class="form-control" name="name" placeholder="{{ __('Enter Asset Name (e.g. Printer, Laptop, Desk)') }}" required>
                                <div class="errors">{{ $errors->has('name') ? $errors->first('name') : '' }}</div>
                            </div>

                            {{-- Bank Account --}}
                            <div class="mt-2 col-md-6">
                                <label for="bank_id" class="form-label fw-bold">{{ __('Select Bank Account *') }}</label>
                                <select class="select2" name="bank_id" required>
                                    <option selected value="">{{ __('Select Bank Account') }}</option>
                                    @foreach ($bank_accounts as $bank_account)
                                        <option value="{{ $bank_account->id }}">{{ $bank_account->bank_name }}</option>
                                    @endforeach
                                </select>
                                <div class="errors">{{ $errors->has('bank_id') ? $errors->first('bank_id') : '' }}</div>
                            </div>

                            {{-- Cost --}}
                            <div class="mt-2 col-md-6">
                                <label for="purchase_cost" class="form-label fw-bold">{{ __('Unit Purchase Cost *') }}</label>
                                <input type="number" step="0.01" class="form-control" name="purchase_cost" placeholder="{{ __('Enter Unit Cost') }}" required>
                                <div class="errors">{{ $errors->has('purchase_cost') ? $errors->first('purchase_cost') : '' }}</div>
                            </div>

                            {{-- Quantity --}}
                            <div class="mt-2 col-md-6">
                                <label for="quantity" class="form-label fw-bold">{{ __('Quantity *') }}</label>
                                <input type="number" class="form-control" name="quantity" value="1" placeholder="{{ __('Enter Quantity') }}" required>
                                <div class="errors">{{ $errors->has('quantity') ? $errors->first('quantity') : '' }}</div>
                            </div>

                            {{-- Note --}}
                            <div class="mt-2 col-md-12">
                                <label class="form-label fw-bold">{{ __('Notes / Details') }}</label>
                                <textarea class="form-control" name="note" rows="2" placeholder="{{ __('Enter details (e.g. Serial numbers, vendor information)') }}"></textarea>
                                <div class="errors">{{ $errors->has('note') ? $errors->first('note') : '' }}</div>
                            </div>

                            <div class="mt-3 text-center col-12">
                                <button class="btn save_btn" type="submit"> {{ __('Save') }} </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
