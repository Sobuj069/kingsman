@extends('backend.layouts.master')
@section('section-title', __('Purchase'))
@section('page-title', __('Purchase Details'))

@section('action-button')
    <a href="{{ route('purchase.index') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-arrow-left"></i>
        {{ __('Back to List') }}
    </a>
    <a href="{{ route('purchase.print', $purchase->id) }}" class="btn btn-success ml-2">
        <i class="mr-2 feather icon-printer"></i>
        {{ __('Print Purchase') }}
    </a>
@endsection

@push('css')
    <style>
        .invoice-card {
            border-radius: 12px !important;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0 !important;
        }
        .detail-label {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .detail-value {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
        }
        body.dark-theme .detail-label {
            color: #94a3b8;
        }
        body.dark-theme .detail-value {
            color: #f1f5f9;
        }
        .section-header {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        body.dark-theme .section-header {
            color: #f1f5f9;
            border-bottom-color: #334155;
        }
        .summary-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
        }
        body.dark-theme .summary-box {
            background-color: #1e293b;
            border-color: #334155;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <!-- Main Details -->
        <div class="col-lg-8">
            <div class="card m-b-30 invoice-card">
                <div class="card-body">
                    <h5 class="section-header"><i class="feather icon-info mr-2 text-primary"></i>{{ __('Purchase Overview') }}</h5>
                    <div class="row mb-4">
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="detail-label">{{ __('Purchase No') }}</div>
                            <div class="detail-value text-primary font-weight-bold">{{ $purchase->purchase_no }}</div>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="detail-label">{{ __('Date') }}</div>
                            <div class="detail-value">{{ $purchase->date }}</div>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="detail-label">{{ __('Status') }}</div>
                            <div class="detail-value">
                                @if ($purchase->status == 0)
                                    <span class="badge badge-warning">{{ __('Due') }}</span>
                                @elseif($purchase->status == 1)
                                    <span class="badge badge-success">{{ __('Paid') }}</span>
                                @elseif($purchase->status == 2)
                                    <span class="badge badge-danger">{{ __('Returned') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="detail-label">{{ __('Supplier') }}</div>
                            <div class="detail-value">{{ $purchase->supplier?->name }} ({{ $purchase->supplier?->phone ?? 'N/A' }})</div>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="detail-label">{{ __('Branch') }}</div>
                            <div class="detail-value">{{ $purchase->branch?->name ?? __('N/A') }}</div>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="detail-label">{{ __('Creator') }}</div>
                            <div class="detail-value">{{ $purchase->user?->name ?? __('N/A') }}</div>
                        </div>
                    </div>

                    <h5 class="section-header"><i class="feather icon-box mr-2 text-primary"></i>{{ __('Product / Item Details') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Item Name') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Rate') }}</th>
                                    <th>{{ __('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchase_items as $index => $item)
                                    @php
                                        $product = App\Models\Product::where('id', $item->product_id)->with('unit.related_unit')->first();
                                        if ($product->unit->related_unit == null) {
                                            $qty_str = $item->actual_main . ' ' . $product->unit->name;
                                        } else {
                                            $sub_qty = $item->actual_sub == null ? 0 : $item->actual_sub;
                                            $qty_str = $item->actual_main . ' ' . $product->unit->name . ' ' . $sub_qty . ' ' . $product->unit->related_unit->name;
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $item->product?->name }}</strong>
                                            @if ($item->is_return == 1)
                                                <span class="badge badge-danger ml-1">{{ __('Return') }}</span>
                                            @endif
                                            @if ($item->product_variation_id != null)
                                                <br><small class="text-muted">Variation: {{ $item->product_variation?->size?->size }}-{{ $item->product_variation?->color?->color }}</small>
                                            @endif
                                            @if (!empty($item->imei))
                                                <br><small class="text-muted">IMEI: {{ str_replace("\n", ", ", $item->imei) }}</small>
                                            @endif
                                            @if($item->warranty_value)
                                                <br><small class="text-muted">Warranty: {{ $item->warranty_value }} {{ $item->warranty_unit }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $qty_str }}</td>
                                        <td>{{ number_format($item->rate, 2) }}</td>
                                        <td>{{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Summary / Financials -->
        <div class="col-lg-4">
            <!-- Totals Box -->
            <div class="card m-b-30 invoice-card">
                <div class="card-body">
                    <h5 class="section-header"><i class="feather icon-credit-card mr-2 text-primary"></i>{{ __('Financial Summary') }}</h5>
                    <div class="summary-box">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="detail-label">{{ __('Sub Total') }}</span>
                            <span class="detail-value">{{ number_format($purchase->estimated_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="detail-label">
                                {{ __('Discount') }}
                                @if (str_contains($purchase->discount, '%'))
                                    ({{ $purchase->discount }})
                                @endif
                            </span>
                            <span class="detail-value text-danger">- {{ number_format($purchase->discount_amount, 2) }}</span>
                        </div>
                        @if ($purchase->vat_amount > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="detail-label">
                                    {{ __('VAT') }}
                                    @if (str_contains($purchase->vat, '%'))
                                        ({{ $purchase->vat }})
                                    @endif
                                </span>
                                <span class="detail-value">+ {{ number_format($purchase->vat_amount, 2) }}</span>
                            </div>
                        @endif
                        <hr class="my-2" style="border-top: 2px solid #cbd5e1;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="detail-label font-weight-bold text-primary">{{ __('Grand Total') }}</span>
                            <span class="detail-value text-primary font-weight-bold" style="font-size: 16px;">{{ number_format($purchase->total_amount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="detail-label">{{ __('Total Paid') }}</span>
                            <span class="detail-value text-success font-weight-bold">{{ number_format($purchase->total_paid, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="detail-label">{{ __('Total Due') }}</span>
                            <span class="detail-value text-danger font-weight-bold">{{ number_format($purchase->total_due, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Methods & Log Info -->
            <div class="card m-b-30 invoice-card">
                <div class="card-body">
                    <h5 class="section-header"><i class="feather icon-user mr-2 text-primary"></i>{{ __('Activity & Accounts') }}</h5>
                    
                    <div class="mb-3">
                        <div class="detail-label">{{ __('Payment Accounts Used') }}</div>
                        <div class="detail-value">
                            @if ($payments->count() > 0)
                                <ul class="pl-3 mb-0">
                                    @foreach ($payments as $pay)
                                        <li>{{ $pay->bank_account?->bank_name }}: <strong>{{ number_format($pay->amount, 2) }}</strong></li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-muted">{{ __('No Payment transactions registered') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="detail-label">{{ __('Created By') }}</div>
                            <div class="detail-value">{{ $purchase->user?->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="detail-label">{{ __('Created At') }}</div>
                            <div class="detail-value" style="font-size: 12px;">{{ $purchase->created_at }}</div>
                        </div>
                        @if($purchase->updatedBy)
                            <div class="col-6 mb-3">
                                <div class="detail-label">{{ __('Updated By') }}</div>
                                <div class="detail-value">{{ $purchase->updatedBy->name }}</div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="detail-label">{{ __('Updated At') }}</div>
                                <div class="detail-value" style="font-size: 12px;">{{ $purchase->updated_at }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
