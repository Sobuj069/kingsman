@extends('backend.layouts.master')
@section('section-title', __('Warranty Management'))
@section('page-title', __('Add Warranty Claim'))

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
                    <div class="mb-4">
                        <div class="row align-items-end">
                            <div class="col-md-10">
                                <label>{{ __('Search (IMEI / Barcode / Invoice ID / Product Name)') }}</label>
                                <input type="text" id="warranty_search" class="form-control" placeholder="{{ __('Type to search and fetch warranty info...') }}" autocomplete="off">
                                <small class="text-info">{{ __('Enter IMEI, Barcode, Invoice ID (e.g. SINV-0000001) or Product Name') }}</small>
                            </div>
                            <div class="col-md-2">
                                <div id="search_spinner" class="spinner-border spinner-border-sm text-primary" role="status" style="display: none;">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <form action="{{ route('warranty-claim.store') }}" method="POST" id="claim_form">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="serial_no" id="serial_no">
                            <input type="hidden" name="product_id" id="product_id">
                            <input type="hidden" name="customer_id" id="customer_id">
                            <input type="hidden" name="invoice_id" id="invoice_id">

                            <div class="col-md-12 mb-3" id="warranty_alert_div" style="display: none;">
                                <div class="alert alert-info">
                                    {{-- Will be populated dynamically via JavaScript --}}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Product Name') }} <span class="text-danger">*</span></label>
                                    <select name="product_name" id="product_name_select" class="form-control select2" required>
                                        <option value="">{{ __('Select Product') }}</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->name }}" data-id="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Customer Name') }}</label>
                                    <input type="text" id="customer_name_display" class="form-control" value="{{ __('Walking Customer') }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('IMEI / Serial No') }}</label>
                                    <input type="text" id="serial_no_display" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Invoice No') }}</label>
                                    <input type="text" id="invoice_no_display" class="form-control" placeholder="{{ __('Auto-filled from sale') }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Received Condition') }}</label>
                                    <input type="text" name="received_condition" class="form-control" placeholder="{{ __('e.g. Broken, Power Problem') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Place For') }}</label>
                                    <select name="place_for" class="form-control">
                                        <option value="Internal">{{ __('Internal Repair') }}</option>
                                        @foreach($service_centers as $center)
                                            <option value="{{ $center->name }}">{{ $center->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Received Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="received_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            @if(auth()->user()->branch_id == 1)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('Branch') }} <span class="text-danger">*</span></label>
                                    <select name="branch_id" class="form-control select2" required>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn add_list_btn">{{ __('Record Warranty Claim') }}</button>
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
        $('#product_name_select').on('change', function() {
            var productId = $(this).find(':selected').data('id');
            $('#product_id').val(productId);
        });

        $('#warranty_search').on('keypress', function(e) {
            if (e.which == 13) { // Enter key
                var search = $(this).val();
                if (search.length > 2) {
                    fetchWarrantyInfo(search);
                }
            }
        });

        // Optional: Search on blur or after delay
        var timeout = null;
        $('#warranty_search').on('keyup', function() {
            clearTimeout(timeout);
            var search = $(this).val();
            timeout = setTimeout(function() {
                if (search.length > 3) {
                    fetchWarrantyInfo(search);
                }
            }, 800);
        });

        function fetchWarrantyInfo(search) {
            $('#search_spinner').show();
            $.ajax({
                url: "{{ route('warranty-claim.ajax-search') }}",
                type: "GET",
                data: { search: search },
                success: function(response) {
                    $('#search_spinner').hide();
                    if (response.success) {
                        var data = response.data;
                        
                        // Populate hidden fields
                        $('#serial_no').val(data.serial_no);
                        $('#product_id').val(data.product_id);
                        $('#customer_id').val(data.customer_id);
                        $('#invoice_id').val(data.invoice_id);

                        // Populate visible fields
                        $('#serial_no_display').val(data.serial_no);
                        $('#customer_name_display').val(data.customer_name);
                        $('#invoice_no_display').val(data.invoice_no);
                        
                        // Update Select2
                        $('#product_name_select').val(data.product_name).trigger('change');

                        // Show Info Alert with Green ("sobuj") styling for valid, Red for expired, Warning for none
                        var alertDiv = $('#warranty_alert_div').find('.alert');
                        alertDiv.removeClass('alert-info alert-success alert-danger alert-warning');
                        
                        var alertHtml = '';
                        if (!data.has_warranty) {
                            alertDiv.addClass('alert-warning');
                            alertHtml = '<strong><i class="feather icon-alert-triangle mr-2"></i>' + "{{ __('No Warranty Info!') }}" + '</strong><br>' +
                                        '<strong>' + "{{ __('Customer Name:') }}" + '</strong> ' + data.customer_name + '<br>' +
                                        '<strong>' + "{{ __('Invoice No:') }}" + '</strong> ' + data.invoice_no + '<br>' +
                                        '<strong>' + "{{ __('Sale Date:') }}" + '</strong> ' + data.sale_date + '<br>' +
                                        '<strong>' + "{{ __('Warranty Status:') }}" + '</strong> <span class="badge badge-warning text-dark px-2 py-1">' + data.warranty_info + '</span>';
                        } else if (data.is_expired) {
                            alertDiv.addClass('alert-danger');
                            alertHtml = '<strong><i class="feather icon-x-circle mr-2"></i>' + "{{ __('Warranty Expired!') }}" + '</strong><br>' +
                                        '<strong>' + "{{ __('Customer Name:') }}" + '</strong> ' + data.customer_name + '<br>' +
                                        '<strong>' + "{{ __('Invoice No:') }}" + '</strong> ' + data.invoice_no + '<br>' +
                                        '<strong>' + "{{ __('Sale Date:') }}" + '</strong> ' + data.sale_date + '<br>' +
                                        '<strong>' + "{{ __('Warranty Status:') }}" + '</strong> <span class="badge badge-danger text-white px-2 py-1">' + data.warranty_info + '</span>';
                        } else {
                            alertDiv.addClass('alert-success'); // "sobuj" (green) alert
                            alertHtml = '<strong><i class="feather icon-check-circle mr-2"></i>' + "{{ __('Warranty Active & Valid!') }}" + '</strong><br>' +
                                        '<strong>' + "{{ __('Customer Name:') }}" + '</strong> ' + data.customer_name + '<br>' +
                                        '<strong>' + "{{ __('Invoice No:') }}" + '</strong> ' + data.invoice_no + '<br>' +
                                        '<strong>' + "{{ __('Sale Date:') }}" + '</strong> ' + data.sale_date + '<br>' +
                                        '<strong>' + "{{ __('Warranty Status:') }}" + '</strong> <span class="badge badge-success text-white px-2 py-1">' + data.warranty_info + '</span>';
                        }
                        
                        alertDiv.html(alertHtml);
                        $('#warranty_alert_div').show();

                        iziToast.success({
                            title: "{{ __('Info Fetched Successfully') }}",
                            position: "topRight"
                        });
                    } else {
                        // Reset fields
                        $('#serial_no_display').val('');
                        $('#customer_name_display').val('Walking Customer');
                        $('#invoice_no_display').val('');
                        $('#product_name_select').val('').trigger('change');
                        $('#warranty_alert_div').hide();
                        
                        iziToast.warning({
                            title: "{{ __('No Records Found') }}",
                            message: response.message,
                            position: "topRight"
                        });
                    }
                },
                error: function() {
                    $('#search_spinner').hide();
                    iziToast.error({
                        title: "{{ __('Error searching') }}",
                        position: "topRight"
                    });
                }
            });
        }
    });
</script>
@endpush
