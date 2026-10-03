@extends('backend.layouts.master')
@section('section-title', __('Service Management'))
@section('page-title', __('Create Service Invoice'))

@section('action-button')
    <a href="{{ route('service-invoice.index') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-list"></i>
        {{ __('Back to List') }}
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <form action="{{ route('service-invoice.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('Select Service Received') }}</label>
                                    <select name="service_receive_id" id="service_receive_id" class="select2">
                                        <option value="">{{ __('Direct Invoice (No Receipt)') }}</option>
                                        @foreach ($service_receives as $item)
                                            <option value="{{ $item->id }}" 
                                                data-customer="{{ $item->customer_id }}"
                                                data-pname="{{ $item->pname }}"
                                                {{ (request('receive_id') == $item->id) ? 'selected' : '' }}>
                                                {{ $item->service_no }} - {{ $item->pname }} ({{ $item->cname }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('Customer') }} <span class="text-danger">*</span></label>
                                    <select name="customer_id" id="customer_id" class="select2" required>
                                        <option value="">{{ __('Select Customer') }}</option>
                                        @foreach ($customers as $item)
                                            <option value="{{ $item->id }}" {{ (isset($selected_receive) && $selected_receive->customer_id == $item->id) ? 'selected' : '' }}>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ __('Invoice Date') }} <span class="text-danger">*</span></label>
                                    <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            @if(auth()->user()->branch_id == 1)
                            <div class="col-md-4">
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

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __('Add Services/Parts') }}</label>
                                    <select id="product_search" class="select2">
                                        <option value="">{{ __('Search Service/Product...') }}</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}" data-cost="{{ $product->purchase_price ?? 0 }}">{{ $product->name }} ({{ $product->selling_price }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive mt-3">
                            <table class="table table-bordered" id="invoice_items_table">
                                <thead class="header_bg">
                                    <tr>
                                        <th>{{ __('Service/Product') }}</th>
                                        <th width="150">{{ __('Quantity') }}</th>
                                        <th width="180">{{ __('Cost Price') }}</th>
                                        <th width="180">{{ __('Price') }}</th>
                                        <th width="180">{{ __('Subtotal') }}</th>
                                        <th width="50">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Dynamic Rows --}}
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-right">{{ __('Total Amount') }}</th>
                                        <th><input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control" readonly value="0"></th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-right">{{ __('Discount') }}</th>
                                        <th><input type="number" step="0.01" name="discount" id="discount" class="form-control" value="0"></th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-right">{{ __('Net Amount') }}</th>
                                        <th><input type="number" step="0.01" name="net_amount" id="net_amount" class="form-control" readonly value="0"></th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-right">{{ __('Paid Amount') }}</th>
                                        <th><input type="number" step="0.01" name="paid_amount" id="paid_amount" class="form-control" value="0"></th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="text-right">{{ __('Due Amount') }}</th>
                                        <th><input type="number" step="0.01" name="due_amount" id="due_amount" class="form-control" readonly value="0"></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn add_list_btn btn-lg btn-block">{{ __('Create Service Invoice') }}</button>
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
        $('#product_search').on('change', function() {
            var productId = $(this).val();
            if(!productId) return;
            
            var productName = $(this).find(':selected').text();
            var price = $(this).find(':selected').data('price');
            var cost = $(this).find(':selected').data('cost');
            
            var row = `
                <tr>
                    <td>
                        <input type="hidden" name="product_id[]" value="${productId}">
                        ${productName}
                    </td>
                    <td>
                        <input type="number" name="quantity[]" class="form-control quantity" value="1" min="1" step="0.01">
                    </td>
                    <td>
                        <input type="number" name="cost_price[]" class="form-control cost_price" value="${cost || 0}" step="0.01">
                    </td>
                    <td>
                        <input type="number" name="price[]" class="form-control price" value="${price}" step="0.01">
                    </td>
                    <td>
                        <input type="number" name="subtotal[]" class="form-control subtotal" value="${price}" readonly>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm remove-row"><i class="feather icon-trash"></i></button>
                    </td>
                </tr>
            `;
            
            $('#invoice_items_table tbody').append(row);
            $(this).val('').trigger('change');
            calculateTotals();
        });

        $(document).on('input', '.quantity, .price', function() {
            var row = $(this).closest('tr');
            var qty = parseFloat(row.find('.quantity').val()) || 0;
            var price = parseFloat(row.find('.price').val()) || 0;
            var subtotal = qty * price;
            row.find('.subtotal').val(subtotal.toFixed(2));
            calculateTotals();
        });

        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
            calculateTotals();
        });

        $('#discount, #paid_amount').on('input', function() {
            calculateTotals();
        });

        function calculateTotals() {
            var total = 0;
            $('.subtotal').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            
            $('#total_amount').val(total.toFixed(2));
            
            var discount = parseFloat($('#discount').val()) || 0;
            var netAmount = total - discount;
            $('#net_amount').val(netAmount.toFixed(2));
            
            var paid = parseFloat($('#paid_amount').val()) || 0;
            var due = netAmount - paid;
            $('#due_amount').val(due.toFixed(2));
        }

        $('#service_receive_id').on('change', function() {
            var customerId = $(this).find(':selected').data('customer');
            if(customerId) {
                $('#customer_id').val(customerId).trigger('change');
            }
        });
    });
</script>
@endpush
