@extends('backend.layouts.master')
@section('section-title', __('Warranty Management'))
@section('page-title', __('Deliver Warranty Product'))

@section('action-button')
    <a href="{{ route('warranty-delivery.index') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-list"></i>
        {{ __('Back to List') }}
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    @if($claim)
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">{{ __('Claim Information') }}</h5>
                                <p><strong>{{ __('Claim No:') }}</strong> <span class="badge badge-info px-2 py-1">{{ $claim->claim_no }}</span></p>
                                <p><strong>{{ __('Customer:') }}</strong> {{ $claim->customer ? $claim->customer->name : __('Walking Customer') }}</p>
                                <p><strong>{{ __('Received Product:') }}</strong> {{ $claim->product_name }}</p>
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">&nbsp;</h5>
                                <p><strong>{{ __('Received Serial/IMEI:') }}</strong> {{ $claim->serial_no }}</p>
                                <p><strong>{{ __('Received Date:') }}</strong> {{ $claim->received_date }}</p>
                                <p><strong>{{ __('Received Condition:') }}</strong> {{ $claim->received_condition }}</p>
                            </div>
                        </div>
                        <hr>
                    @endif

                    <form action="{{ route('warranty-delivery.store') }}" method="POST">
                        @csrf
                        @if($claim)
                            <input type="hidden" name="warranty_claim_id" value="{{ $claim->id }}">
                        @else
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-group">
                                        <label>{{ __('Select Warranty Claim') }} <span class="text-danger">*</span></label>
                                        @php
                                            $pendingClaims = \App\Models\WarrantyClaim::where('status', '!=', 'Delivered')->get();
                                        @endphp
                                        <select name="warranty_claim_id" class="form-control select2" required>
                                            <option value="">{{ __('Select Claim') }}</option>
                                            @foreach($pendingClaims as $pc)
                                                <option value="{{ $pc->id }}">
                                                    {{ $pc->claim_no }} - {{ $pc->product_name }} ({{ $pc->customer ? $pc->customer->name : 'Walking Customer' }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Delivered Product (New or Repaired)') }} <span class="text-danger">*</span></label>
                                    <select name="delivered_product_id" id="delivered_product_id" class="form-control select2" required>
                                        <option value="">{{ __('Select Product') }}</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-imei="{{ $product->imei }}" {{ ($claim && $claim->product_id == $product->id) ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('New Serial / IMEI') }} <span id="serial_label_asterisk" class="text-danger">*</span></label>
                                    <input type="text" name="delivered_serial" id="delivered_serial" class="form-control" 
                                           value="{{ $claim ? $claim->serial_no : '' }}" 
                                           placeholder="{{ __('Enter IMEI or Serial of delivered item') }}">
                                    <small class="text-muted">{{ __('Keep the same if the device is repaired, or write new serial if replaced.') }}</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Delivery Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="delivered_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Delivery Note / Remarks') }}</label>
                                    <textarea name="delivery_note" class="form-control" rows="2" placeholder="{{ __('e.g. Replaced with new unit, Repaired motherboard') }}"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn add_list_btn">{{ __('Confirm Delivery') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        function toggleSerialRequirement() {
            var selectedOption = $('#delivered_product_id').find(':selected');
            var isImei = selectedOption.data('imei');
            
            if (isImei == 1) {
                $('#delivered_serial').attr('required', true);
                $('#serial_label_asterisk').show();
            } else {
                $('#delivered_serial').removeAttr('required');
                $('#serial_label_asterisk').hide();
            }
        }

        // On change
        $('#delivered_product_id').on('change', function() {
            toggleSerialRequirement();
        });

        // On load
        toggleSerialRequirement();
    });
</script>
@endpush
