@extends('backend.layouts.master')

@section('page-title', __('Edit Pre-Order'))

@section('content')
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            {{-- Header --}}
            <div class="row mb-4">
                <div class="col-12 flex justify-between items-center">
                    <div>
                        <h4 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            {{ __('Edit Pre-Order') }} #{{ $preOrder->pre_order_no }}
                        </h4>
                        <p class="text-sm text-slate-500 mt-1">
                            {{ __('Update customer details, items, quantities, or prices for this pending pre-order.') }}
                        </p>
                    </div>
                    <a href="{{ route('pre-orders.index') }}" class="btn btn-light btn-sm px-3 py-2 font-semibold">
                        <i class="feather icon-arrow-left me-1"></i> {{ __('Back to List') }}
                    </a>
                </div>
            </div>

            {{-- Flash Messages --}}
            @if (session('error'))
                <div class="mb-4 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-600 flex items-center gap-3">
                    <i class="feather icon-alert-circle"></i>
                    <span>{!! session('error') !!}</span>
                </div>
            @endif

            {{-- Edit Form --}}
            <form method="POST" action="{{ route('pre-orders.update', $preOrder->id) }}">
                @csrf
                
                <div class="card p-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-xs font-semibold uppercase text-slate-500">{{ __('Customer *') }}</label>
                            <select name="customer_id" class="form-select select2" required>
                                @foreach ($customers as $cust)
                                    <option value="{{ $cust->id }}" {{ $preOrder->customer_id == $cust->id ? 'selected' : '' }}>
                                        {{ $cust->name }} - {{ $cust->phone }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-xs font-semibold uppercase text-slate-500">{{ __('Pre-Order Note') }}</label>
                            <input type="text" name="note" class="form-control" value="{{ old('note', $preOrder->note) }}" placeholder="{{ __('Special instructions or notes') }}">
                        </div>
                    </div>
                </div>

                {{-- Product Items Table --}}
                <div class="card p-4 bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200/80 dark:border-slate-700/80 mb-4">
                    <div class="flex justify-between items-center mb-3">
                        <h5 class="text-md font-bold text-slate-800 dark:text-white mb-0">{{ __('Pre-Order Items') }}</h5>
                        
                        {{-- Add Product Dropdown --}}
                        <div class="flex items-center gap-2" style="width: 300px;">
                            <select id="add_product_select" class="form-select select2 text-sm">
                                <option value="">{{ __('+ Select Product to Add') }}</option>
                                @foreach ($products as $p)
                                    <option value="{{ $p->id }}" data-name="{{ $p->name }}" data-price="{{ $p->selling_price }}">
                                        {{ $p->name }} (TK {{ number_format($p->selling_price, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="items_table">
                            <thead class="bg-slate-50 text-slate-500 uppercase text-xs">
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th width="150">{{ __('Quantity') }}</th>
                                    <th width="180">{{ __('Unit Price (TK)') }}</th>
                                    <th width="180">{{ __('Subtotal (TK)') }}</th>
                                    <th width="60" class="text-center">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody id="items_tbody">
                                @foreach ($preOrder->items as $item)
                                    <tr id="row_prod_{{ $item->product_id }}">
                                        <td>
                                            <input type="hidden" name="product_id[]" value="{{ $item->product_id }}">
                                            <span class="font-bold text-slate-800 dark:text-white">{{ $item->product?->name ?? 'Product' }}</span>
                                        </td>
                                        <td>
                                            <input type="number" min="1" name="quantity[]" value="{{ $item->quantity }}" class="form-control form-control-sm qty-input" onchange="recalcTotals()" onkeyup="recalcTotals()">
                                        </td>
                                        <td>
                                            <input type="number" step="any" min="0" name="unit_price[]" value="{{ $item->unit_price }}" class="form-control form-control-sm price-input" onchange="recalcTotals()" onkeyup="recalcTotals()">
                                        </td>
                                        <td class="font-bold text-slate-900 dark:text-white subtotal-text">
                                            TK {{ number_format($item->subtotal, 2) }}
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger p-1 border-0" onclick="removeRow(this)">
                                                <i class="feather icon-trash-2"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50 font-bold">
                                <tr>
                                    <td colspan="3" class="text-end uppercase text-xs">{{ __('Grand Total Amount:') }}</td>
                                    <td colspan="2" class="text-orange-500 font-bold text-base" id="grand_total_text">
                                        TK {{ number_format($preOrder->total_amount, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center gap-3">
                    <button type="submit" class="btn btn-primary px-4 py-2.5 font-bold flex items-center gap-2">
                        <i class="feather icon-save"></i> {{ __('Save Changes') }}
                    </button>
                    <a href="{{ route('pre-orders.index') }}" class="btn btn-light px-4 py-2.5 font-semibold">
                        {{ __('Cancel') }}
                    </a>
                </div>

            </form>

        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    function recalcTotals() {
        var total = 0;
        $('#items_tbody tr').each(function() {
            var qty = parseFloat($(this).find('.qty-input').val()) || 0;
            var price = parseFloat($(this).find('.price-input').val()) || 0;
            var subtotal = qty * price;
            $(this).find('.subtotal-text').text('TK ' + subtotal.toFixed(2));
            total += subtotal;
        });
        $('#grand_total_text').text('TK ' + total.toFixed(2));
    }

    function removeRow(btn) {
        if ($('#items_tbody tr').length <= 1) {
            alert("{{ __('Pre-order must contain at least one product.') }}");
            return false;
        }
        $(btn).closest('tr').remove();
        recalcTotals();
    }

    $(document).ready(function() {
        $('#add_product_select').on('change', function() {
            var pid = $(this).val();
            if (!pid) return;

            var name = $(this).find(':selected').data('name');
            var price = $(this).find(':selected').data('price') || 0;

            if ($('#row_prod_' + pid).length > 0) {
                var currentQty = parseInt($('#row_prod_' + pid).find('.qty-input').val()) || 1;
                $('#row_prod_' + pid).find('.qty-input').val(currentQty + 1);
            } else {
                var newRow = '<tr id="row_prod_' + pid + '">' +
                    '<td>' +
                        '<input type="hidden" name="product_id[]" value="' + pid + '">' +
                        '<span class="font-bold text-slate-800 dark:text-white">' + name + '</span>' +
                    '</td>' +
                    '<td>' +
                        '<input type="number" min="1" name="quantity[]" value="1" class="form-control form-control-sm qty-input" onchange="recalcTotals()" onkeyup="recalcTotals()">' +
                    '</td>' +
                    '<td>' +
                        '<input type="number" step="any" min="0" name="unit_price[]" value="' + price + '" class="form-control form-control-sm price-input" onchange="recalcTotals()" onkeyup="recalcTotals()">' +
                    '</td>' +
                    '<td class="font-bold text-slate-900 dark:text-white subtotal-text">TK ' + parseFloat(price).toFixed(2) + '</td>' +
                    '<td class="text-center">' +
                        '<button type="button" class="btn btn-sm btn-outline-danger p-1 border-0" onclick="removeRow(this)">' +
                            '<i class="feather icon-trash-2"></i>' +
                        '</button>' +
                    '</td>' +
                '</tr>';
                $('#items_tbody').append(newRow);
            }

            $(this).val('').trigger('change');
            recalcTotals();
        });
    });
</script>
@endpush
