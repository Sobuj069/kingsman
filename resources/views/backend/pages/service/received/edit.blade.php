@extends('backend.layouts.master')
@section('section-title', __('Service Management'))
@section('page-title', __('Edit Service Received'))

@section('action-button')
    <a href="{{ route('service-receive.index') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-list"></i>
        {{ __('Back to List') }}
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <form action="{{ route('service-receive.update', $service->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Select Customer') }}</label>
                                    <select name="customer_id" id="customer_id" class="select2">
                                        <option value="">{{ __('Select Customer') }}</option>
                                        @foreach ($customers as $item)
                                            <option value="{{ $item->id }}" {{ $service->customer_id == $item->id ? 'selected' : '' }} data-phone="{{ $item->phone }}" data-address="{{ $item->address }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Customer Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="cname" id="cname" class="form-control" value="{{ $service->cname }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Customer Phone') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="cphone" id="cphone" class="form-control" value="{{ $service->cphone }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Customer Address') }}</label>
                                    <input type="text" name="caddress" id="caddress" class="form-control" value="{{ $service->caddress }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Product Name / Service Name') }} <span class="text-danger">*</span></label>
                                    <select name="pname" class="form-control select2" required>
                                        <option value="">{{ __('Select Product') }}</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->name }}" {{ $service->pname == $product->name ? 'selected' : '' }}>{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Product Model') }}</label>
                                    <input type="text" name="pmodel" class="form-control" value="{{ $service->pmodel }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __('Problem Description') }}</label>
                                    <textarea name="pdescription" class="form-control" rows="3">{{ $service->pdescription }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Received Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="received_date" class="form-control" value="{{ $service->received_date }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Expected Delivery Date') }}</label>
                                    <input type="date" name="deli_date" class="form-control" value="{{ $service->deli_date }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Status') }}</label>
                                    <select name="status" class="form-control">
                                        <option value="Pending" {{ $service->status == 'Pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                        <option value="In Progress" {{ $service->status == 'In Progress' ? 'selected' : '' }}>{{ __('In Progress') }}</option>
                                        <option value="Completed" {{ $service->status == 'Completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                        <option value="Delivered" {{ $service->status == 'Delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn add_list_btn">{{ __('Update Received Item') }}</button>
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
        $('#customer_id').on('change', function() {
            var selected = $(this).find(':selected');
            var name = selected.text();
            var phone = selected.data('phone');
            var address = selected.data('address');
            
            if($(this).val() != '') {
                $('#cname').val(name);
                $('#cphone').val(phone);
                $('#caddress').val(address);
            }
        });
    });
</script>
@endpush
