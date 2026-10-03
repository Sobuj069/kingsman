@extends('backend.layouts.master')
@section('section-title', __('Service Management'))
@section('page-title', __('Add Service Received'))

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
                    <form action="{{ route('service-receive.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Select Customer') }}</label>
                                    <select name="customer_id" id="customer_id" class="select2">
                                        <option value="">{{ __('Select Customer') }}</option>
                                        @foreach ($customers as $item)
                                            <option value="{{ $item->id }}" data-phone="{{ $item->phone }}" data-address="{{ $item->address }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Customer Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="cname" id="cname" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Customer Phone') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="cphone" id="cphone" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Customer Address') }}</label>
                                    <input type="text" name="caddress" id="caddress" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Product Name / Service Name') }} <span class="text-danger">*</span></label>
                                    <select name="pname" class="form-control select2" required>
                                        <option value="">{{ __('Select Product') }}</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->name }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Product Model') }}</label>
                                    <input type="text" name="pmodel" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __('Problem Description') }}</label>
                                    <textarea name="pdescription" class="form-control" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Received Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="received_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Expected Delivery Date') }}</label>
                                    <input type="date" name="deli_date" class="form-control">
                                </div>
                            </div>
                            @if(auth()->user()->branch_id == 1)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Branch') }}</label>
                                    <select name="branch_id" class="form-control">
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn add_list_btn">{{ __('Save Received Item') }}</button>
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
            } else {
                $('#cname').val('');
                $('#cphone').val('');
                $('#caddress').val('');
            }
        });
    });
</script>
@endpush
