@extends('backend.layouts.master')
@section('section-title', __('Marketing'))
@section('page-title', __('Add Ad Cost'))

@section('action-button')
    <a href="{{ route('ad-cost.index') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-list"></i>
        {{ __('Ad Cost List') }}
    </a>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <div class="">
                        <form class="row g-3 needs-validation" method="POST" action="{{ route('ad-cost.store') }}">
                            @csrf

                            {{-- Date --}}
                            <div class="mt-2 col-md-4">
                                <label class="form-label font-weight-bold">{{ __('Date *') }}</label>
                                <input type="date" class="form-control" value="{{ date('Y-m-d') }}" name="date" required>
                                <div class="errors">{{ $errors->has('date') ? $errors->first('date') : '' }}</div>
                            </div>

                            {{-- Platform --}}
                            <div class="mt-2 col-md-4">
                                <label for="platform_id" class="form-label fw-bold">{{ __('Select Platform') }}</label>
                                <select class="select2" name="platform_id">
                                    <option value="">{{ __('Select Platform (or None/General)') }}</option>
                                    @foreach ($platforms as $platform)
                                        <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                                    @endforeach
                                </select>
                                <div class="errors">{{ $errors->has('platform_id') ? $errors->first('platform_id') : '' }}</div>
                            </div>

                            {{-- Product --}}
                            <div class="mt-2 col-md-4">
                                <label for="product_id" class="form-label fw-bold">{{ __('Select Product') }}</label>
                                <select class="select2" name="product_id">
                                    <option value="">{{ __('Select Product (or None/General)') }}</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                                <div class="errors">{{ $errors->has('product_id') ? $errors->first('product_id') : '' }}</div>
                            </div>

                            {{-- Campaign Name --}}
                            <div class="mt-2 col-md-4">
                                <label for="campaign_name" class="form-label fw-bold">{{ __('Campaign Name') }}</label>
                                <input type="text" class="form-control" name="campaign_name" placeholder="{{ __('Enter Campaign Name') }}">
                                <div class="errors">{{ $errors->has('campaign_name') ? $errors->first('campaign_name') : '' }}</div>
                            </div>

                            {{-- Amount --}}
                            <div class="mt-2 col-md-4">
                                <label for="amount" class="form-label fw-bold">{{ __('Amount *') }}</label>
                                <input type="number" step="0.01" class="form-control" name="amount" placeholder="{{ __('Enter Amount') }}" required>
                                <div class="errors">{{ $errors->has('amount') ? $errors->first('amount') : '' }}</div>
                            </div>

                            {{-- Note --}}
                            <div class="mt-2 col-md-4">
                                <label class="form-label fw-bold">{{ __('Notes') }}</label>
                                <textarea class="form-control" name="notes" rows="1" placeholder="{{ __('Enter notes/details') }}"></textarea>
                                <div class="errors">{{ $errors->has('notes') ? $errors->first('notes') : '' }}</div>
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
