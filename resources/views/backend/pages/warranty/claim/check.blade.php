@extends('backend.layouts.master')
@section('section-title', __('Warranty Management'))
@section('page-title', __('Check Warranty Device'))

@section('action-button')
    <a href="{{ route('warranty-claim.index') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-list"></i>
        {{ __('Back to List') }}
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>{{ __('Claim No:') }}</strong> {{ $claim->claim_no }}</p>
                            <p><strong>{{ __('Serial/IMEI:') }}</strong> {{ $claim->serial_no }}</p>
                            <p><strong>{{ __('Product:') }}</strong> {{ $claim->product_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>{{ __('Customer:') }}</strong> {{ $claim->customer ? $claim->customer->name : __('Walking Customer') }}</p>
                            <p><strong>{{ __('Received Condition:') }}</strong> {{ $claim->received_condition }}</p>
                            <p><strong>{{ __('Received Date:') }}</strong> {{ $claim->received_date }}</p>
                        </div>
                    </div>

                    <hr>

                    <form action="{{ route('warranty-claim.update-check', $claim->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __('Technician Checking Note') }}</label>
                                    <textarea name="checking_note" class="form-control" rows="4">{{ $claim->checking_note }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Set Status') }}</label>
                                    <select name="status" class="form-control">
                                        <option value="Checked" {{ $claim->status == 'Checked' ? 'selected' : '' }}>{{ __('Checked / Ready for Supplier') }}</option>
                                        <option value="In progress" {{ $claim->status == 'In progress' ? 'selected' : '' }}>{{ __('In Repair') }}</option>
                                        <option value="Denied" {{ $claim->status == 'Denied' ? 'selected' : '' }}>{{ __('Denied (Out of Warranty)') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn add_list_btn">{{ __('Update Status') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
