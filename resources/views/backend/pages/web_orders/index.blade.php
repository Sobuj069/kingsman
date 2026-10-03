@extends('backend.layouts.master')
@section('section-title', __('Web Orders'))
@section('page-title', __('Pending Web Order List'))
@section('action-button')
    <a href="{{ route('invoice.online.sale') }}" class="btn btn-outline-primary mr-2" style="border-radius: 8px; font-weight: 600;">
        <i class="feather icon-list mr-1"></i> {{ __('Online Sale List') }}
    </a>
    <a href="{{ route('web-orders.index') }}" class="btn add_list_btn" style="border-radius: 8px;">
        <i class="feather icon-refresh-cw mr-1"></i> {{ __('Refresh') }}
    </a>
@endsection

@push('css')
<style>
    .kpi-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.2s ease;
    }
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    .kpi-label {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }
    .kpi-value {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }
    body.dark-theme .kpi-card {
        background: #121829 !important;
        border-color: #1e293b !important;
    }
    body.dark-theme .kpi-value {
        color: #f8fafc !important;
    }
    body.dark-theme .kpi-label {
        color: #94a3b8 !important;
    }
    .modal-detail-box {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 15px;
    }
    body.dark-theme .modal-detail-box {
        background-color: #1e293b;
        border-color: #334155;
    }
</style>
@endpush

@section('content')
<div class="row mb-4">
    <!-- KPI Summary Cards -->
    <div class="col-md-4 col-12 mb-3 mb-md-0">
        <div class="kpi-card">
            <div class="kpi-icon bg-orange-100 text-orange-600" style="background-color: #ffedd5; color: #ea580c;">
                <i class="feather icon-shopping-bag"></i>
            </div>
            <div>
                <div class="kpi-label">{{ __('Pending Web Orders') }}</div>
                <div class="kpi-value">{{ $totalPendingCount }} <span class="text-xs font-normal text-muted">{{ __('Orders') }}</span></div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-12 mb-3 mb-md-0">
        <div class="kpi-card">
            <div class="kpi-icon bg-blue-100 text-blue-600" style="background-color: #dbeafe; color: #2563eb;">
                <i class="feather icon-clock"></i>
            </div>
            <div>
                <div class="kpi-label">{{ __('Today\'s New Orders') }}</div>
                <div class="kpi-value">{{ $todayPendingCount }} <span class="text-xs font-normal text-muted">{{ __('Today') }}</span></div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-12">
        <div class="kpi-card">
            <div class="kpi-icon bg-emerald-100 text-emerald-600" style="background-color: #dcfce7; color: #16a34a;">
                <i class="feather icon-dollar-sign"></i>
            </div>
            <div>
                <div class="kpi-label">{{ __('Total Pending Value') }}</div>
                <div class="kpi-value">TK {{ number_format($totalPendingAmount, 2) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card m-b-30 card_style">
            <div class="card-header">
                <!-- Search & Filter Form -->
                <form action="{{ route('web-orders.index') }}" method="GET">
                    <div class="form-row align-items-end">
                        <div class="col-md-2 col-12 mb-3">
                            <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Search Keyword') }}</label>
                            <input type="text" class="form-control" name="barcode" value="{{ $barcode }}" placeholder="{{ __('Order #, Phone, Name') }}" style="height: 38px !important;">
                        </div>
                        <div class="col-md-2 col-12 mb-3">
                            <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Customer') }}</label>
                            <select class="select2 form-control" name="customer_id">
                                <option value="">{{ __('All Customers') }}</option>
                                @foreach ($customers as $c)
                                    <option value="{{ $c->id }}" {{ $customer_id == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }} ({{ $c->phone }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 col-12 mb-3">
                            <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Product') }}</label>
                            <select class="select2 form-control" name="product_id">
                                <option value="">{{ __('All Products') }}</option>
                                @foreach ($products as $p)
                                    <option value="{{ $p->id }}" {{ $product_id == $p->id ? 'selected' : '' }}>
                                        {{ $p->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 col-12 mb-3">
                            <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Payment') }}</label>
                            <select class="form-control" name="payment_status" style="height: 38px !important;">
                                <option value="">{{ __('All Statuses') }}</option>
                                <option value="due" {{ $payment_status == 'due' ? 'selected' : '' }}>{{ __('Cash on Delivery (Due)') }}</option>
                                <option value="paid" {{ $payment_status == 'paid' ? 'selected' : '' }}>{{ __('Paid Online') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2 col-12 mb-3">
                            <label class="font-weight-bold text-muted small uppercase mb-1">{{ __('Date Range') }}</label>
                            <div class="input-group">
                                <input type="date" class="form-control" name="startDate" value="{{ $startDate }}" style="height: 38px !important;">
                                <input type="date" class="form-control" name="endDate" value="{{ $endDate }}" style="height: 38px !important;">
                            </div>
                        </div>
                        <div class="col-md-2 col-12 mb-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill" style="height: 38px; border-radius: 6px;">
                                <i class="feather icon-filter"></i> {{ __('Filter') }}
                            </button>
                            <a href="{{ route('web-orders.index') }}" class="btn btn-secondary" style="height: 38px; border-radius: 6px;">
                                <i class="feather icon-rotate-ccw"></i>
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Bulk Actions Bar -->
                <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-success font-weight-bold px-3" id="btnBulkCourier" style="border-radius: 6px; display: none;">
                            <i class="feather icon-truck mr-1"></i> {{ __('Send Selected to Courier') }} (<span id="selectedCount">0</span>)
                        </button>
                    </div>
                    <div class="text-muted small">
                        {{ __('Showing pending orders waiting for courier fulfillment.') }}
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered w-100" style="width: 100% !important;">
                        <thead class="header_bg">
                            <tr class="text-center">
                                <th class="header_style_left" style="width: 40px;"><input type="checkbox" id="selectAllOrders"></th>
                                <th> #SL </th>
                                <th> Date </th>
                                <th> Order No </th>
                                <th> Customer </th>
                                <th> Delivery Address </th>
                                <th> Ordered Items </th>
                                <th> Total (TK) </th>
                                <th> COD Due (TK) </th>
                                <th> Status </th>
                                <th class="header_style_right" style="width: 130px;"> Action </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($webOrders as $key => $order)
                                @php
                                    $invItems = $order->invoiceItems;
                                    $cleanAddress = $order->customer?->address ?? '-';
                                @endphp
                                <tr class="text-center align-middle">
                                    <td class="table_data_style_left">
                                        <input type="checkbox" class="orderCheckbox" value="{{ $order->id }}">
                                    </td>
                                    <td>{{ $webOrders->firstItem() + $key }}</td>
                                    <td>
                                        <span class="font-weight-bold">{{ date('d M Y', strtotime($order->date)) }}</span>
                                        <div class="text-muted small">{{ date('h:i A', strtotime($order->created_at)) }}</div>
                                    </td>
                                    <td>
                                        <strong class="text-primary font-monospace">{{ $order->invoice_no }}</strong>
                                        @if($order->discount_amount > 0)
                                            <div class="badge badge-success text-white small mt-1">
                                                -TK {{ number_format($order->discount_amount) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-left">
                                        <strong class="text-dark">{{ $order->customer?->name ?? 'Web Patron' }}</strong>
                                        <div class="small text-muted">
                                            <i class="feather icon-phone text-success mr-1"></i>
                                            <a href="tel:{{ $order->customer?->phone }}" class="text-primary font-weight-bold">{{ $order->customer?->phone }}</a>
                                        </div>
                                    </td>
                                    <td class="text-left" style="max-width: 200px; font-size: 12px;">
                                        <span class="text-muted">{{ $cleanAddress }}</span>
                                    </td>
                                    <td class="text-left" style="font-size: 12px;">
                                        @foreach ($invItems as $item)
                                            <div class="mb-1 d-flex align-items-center justify-content-between">
                                                <span>
                                                    <strong>{{ $item->product?->name }}</strong>
                                                    @if ($item->product_variation)
                                                        <span class="badge badge-info px-1.5 py-0.5 ml-1" style="font-size: 10px;">
                                                            {{ $item->product_variation->size?->size }} / {{ $item->product_variation->color?->color }}
                                                        </span>
                                                    @endif
                                                </span>
                                                <span class="badge badge-secondary ml-2 font-weight-bold">× {{ (int)$item->main_qty }}</span>
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>
                                        <strong class="text-dark">TK {{ number_format($order->total_amount, 2) }}</strong>
                                        @if($order->delivery_charge > 0)
                                            <div class="text-muted small">+TK {{ number_format($order->delivery_charge) }} Ship</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->total_due > 0)
                                            <strong class="text-danger">TK {{ number_format($order->total_due, 2) }}</strong>
                                            <div class="badge badge-warning text-dark font-weight-bold small">COD</div>
                                        @else
                                            <span class="badge badge-success text-white font-weight-bold">PAID</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-warning font-weight-bold px-2 py-1 text-dark" style="background-color: #fef08a; border: 1px solid #fde047;">
                                            <i class="feather icon-clock mr-1"></i> {{ __('Pending') }}
                                        </span>
                                    </td>
                                    <td class="table_data_style_right text-center" style="white-space: nowrap;">
                                        <div class="d-inline-flex align-items-center justify-content-center" style="gap: 4px;">
                                            <!-- Dispatch to Courier Trigger -->
                                            <button type="button" class="btn btn-success btn-sm font-weight-bold" 
                                                    data-toggle="modal" data-target="#courierModal-{{ $order->id }}"
                                                    title="{{ __('Send to Courier') }}" style="padding: 4px 8px; border-radius: 6px;">
                                                <i class="feather icon-truck"></i> {{ __('Dispatch') }}
                                            </button>

                                            <!-- View Details Modal Trigger -->
                                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#orderModal-{{ $order->id }}" title="{{ __('View Details') }}" style="padding: 4px 8px; border-radius: 6px;">
                                                <i class="feather icon-eye"></i>
                                            </button>

                                            <!-- Print Invoice -->
                                            <a href="{{ route('invoice.print', $order->id) }}" target="_blank" class="btn btn-secondary btn-sm" title="{{ __('Print Invoice') }}" style="padding: 4px 8px; border-radius: 6px;">
                                                <i class="feather icon-printer"></i>
                                            </a>

                                            <!-- Cancel Order -->
                                            <form action="{{ route('web-orders.cancel', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to cancel this web order? Inventory will be restored to stock.') }}')">
                                                @csrf
                                                <button type="submit" class="btn btn-danger btn-sm" title="{{ __('Cancel Order & Restore Stock') }}" style="padding: 4px 8px; border-radius: 6px;">
                                                    <i class="feather icon-x-circle"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="feather icon-check-circle text-success" style="font-size: 40px;"></i>
                                            <h6 class="mt-3 font-weight-bold">{{ __('No Pending Web Orders Found') }}</h6>
                                            <p class="small text-muted">{{ __('All incoming website orders have been fulfilled or dispatched to couriers.') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $webOrders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PER-ORDER MODALS (OUTSIDE TABLE) -->
@foreach($webOrders as $order)
    @php
        $invItems = $order->invoiceItems;
        $cleanAddress = $order->customer?->address ?? '-';
    @endphp
    <!-- COURIER DISPATCH MODAL -->
    <div class="modal fade" id="courierModal-{{ $order->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content text-left" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-success text-white py-3">
                    <h5 class="modal-title font-weight-bold text-white d-flex align-items-center">
                        <i class="feather icon-truck mr-2"></i> {{ __('Dispatch Web Order to Courier') }} ({{ $order->invoice_no }})
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('web-orders.send-to-courier', $order->id) }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">{{ __('Select Courier Service') }} <span class="text-danger">*</span></label>
                            <select name="courier_type" class="form-control font-weight-bold" id="courierSelect-{{ $order->id }}" required style="height: 42px;">
                                <option value="steadfast">Steadfast Courier (Automated API)</option>
                                <option value="pathao">Pathao Courier (Automated API)</option>
                                <option value="manual">Manual / Own Concierge Delivery</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">{{ __('Recipient Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="recipient_name" class="form-control" value="{{ $order->customer?->name ?? 'Customer' }}" required style="height: 40px;">
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">{{ __('Recipient 11-Digit Phone Number') }} <span class="text-danger">*</span></label>
                            <input type="text" name="recipient_phone" class="form-control font-weight-bold text-primary" value="{{ $order->customer?->phone }}" required style="height: 40px;">
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-dark">{{ __('Full Delivery Address') }} <span class="text-danger">*</span></label>
                            <textarea name="recipient_address" class="form-control" rows="2" required>{{ $cleanAddress }}</textarea>
                        </div>

                        <div class="form-row mb-3">
                            <div class="col-6">
                                <label class="font-weight-bold text-dark">{{ __('COD Collection Amount (TK)') }} <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="cod_amount" class="form-control font-weight-bold text-danger" value="{{ (int)$order->total_due }}" required style="height: 40px;">
                                <small class="text-muted">{{ __('0 for already paid orders') }}</small>
                            </div>
                            <div class="col-6">
                                <label class="font-weight-bold text-dark">{{ __('Delivery Note / Instructions') }}</label>
                                <input type="text" name="note" class="form-control" value="{{ $order->note ?? 'Handle with care' }}" style="height: 40px;">
                            </div>
                        </div>

                        <div class="alert alert-info py-2 px-3 mb-0 small" style="border-radius: 8px;">
                            <i class="feather icon-info mr-1"></i> {{ __('Submitting this will generate tracking and move this order to the Online Sale List.') }}
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2">
                        <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-success font-weight-bold px-4">
                            <i class="feather icon-check-circle mr-1"></i> {{ __('Confirm & Dispatch Order') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ORDER DETAILS MODAL -->
    <div class="modal fade" id="orderModal-{{ $order->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content text-left" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-dark text-white py-3">
                    <h5 class="modal-title font-weight-bold text-white d-flex align-items-center">
                        <i class="feather icon-file-text mr-2 text-orange-400"></i> {{ __('Web Order Details') }} - {{ $order->invoice_no }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="modal-detail-box">
                                <div class="small font-weight-bold text-muted uppercase">{{ __('Customer Profile') }}</div>
                                <h6 class="font-weight-bold text-dark mt-1 mb-1">{{ $order->customer?->name }}</h6>
                                <div class="text-primary font-weight-bold"><i class="feather icon-phone mr-1"></i> {{ $order->customer?->phone }}</div>
                                <div class="text-muted small mt-1"><i class="feather icon-map-pin mr-1"></i> {{ $cleanAddress }}</div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3 mt-md-0">
                            <div class="modal-detail-box">
                                <div class="small font-weight-bold text-muted uppercase">{{ __('Order Summary') }}</div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span>{{ __('Subtotal') }}:</span>
                                    <strong>TK {{ number_format($order->total_amount - $order->delivery_charge + $order->discount_amount, 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>{{ __('Delivery Charge') }}:</span>
                                    <strong>TK {{ number_format($order->delivery_charge, 2) }}</strong>
                                </div>
                                @if($order->discount_amount > 0)
                                    <div class="d-flex justify-content-between text-success">
                                        <span>{{ __('Coupon / Discount') }}:</span>
                                        <strong>-TK {{ number_format($order->discount_amount, 2) }}</strong>
                                    </div>
                                @endif
                                <hr class="my-1">
                                <div class="d-flex justify-content-between font-weight-bold text-dark">
                                    <span>{{ __('Grand Total') }}:</span>
                                    <span class="text-primary">TK {{ number_format($order->total_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="font-weight-bold text-dark mb-2">{{ __('Ordered Line Items') }}</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Variation (Size/Color)') }}</th>
                                    <th class="text-center">{{ __('Qty') }}</th>
                                    <th class="text-right">{{ __('Unit Price') }}</th>
                                    <th class="text-right">{{ __('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invItems as $item)
                                    <tr>
                                        <td><strong>{{ $item->product?->name }}</strong></td>
                                        <td>
                                            @if ($item->product_variation)
                                                <span class="badge badge-info">{{ $item->product_variation->size?->size }}</span>
                                                <span class="badge badge-dark">{{ $item->product_variation->color?->color }}</span>
                                            @else
                                                <span class="text-muted">Standard</span>
                                            @endif
                                        </td>
                                        <td class="text-center font-weight-bold">{{ (int)$item->main_qty }}</td>
                                        <td class="text-right">TK {{ number_format($item->rate, 2) }}</td>
                                        <td class="text-right font-weight-bold">TK {{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-success font-weight-bold" data-dismiss="modal" data-toggle="modal" data-target="#courierModal-{{ $order->id }}">
                        <i class="feather icon-truck mr-1"></i> {{ __('Dispatch to Courier') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach

<!-- BULK DISPATCH MODAL -->
<div class="modal fade" id="bulkCourierModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title font-weight-bold text-white">
                    <i class="feather icon-truck mr-1"></i> {{ __('Bulk Courier Dispatch') }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('web-orders.bulk-courier') }}" method="POST" id="bulkCourierForm">
                @csrf
                <input type="hidden" name="order_ids" id="bulkOrderIds">
                <div class="modal-body p-4">
                    <p class="font-weight-bold text-dark mb-3">
                        {{ __('You have selected') }} <span id="modalBulkCount" class="badge badge-primary px-2 py-1">0</span> {{ __('web order(s) for bulk dispatch.') }}
                    </p>
                    <div class="form-group mb-3">
                        <label class="font-weight-bold text-dark">{{ __('Choose Courier Service') }}</label>
                        <select name="bulk_courier_type" class="form-control font-weight-bold" style="height: 42px;">
                            <option value="steadfast">Steadfast Courier (Automated API Batch)</option>
                            <option value="manual">Manual Courier / Own Rider Delivery</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary font-weight-bold" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary font-weight-bold px-4">
                        <i class="feather icon-check-circle mr-1"></i> {{ __('Dispatch All Selected') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
<script>
    $(document).ready(function() {
        function updateBulkButton() {
            var selected = $('.orderCheckbox:checked').length;
            $('#selectedCount').text(selected);
            $('#modalBulkCount').text(selected);
            if (selected > 0) {
                $('#btnBulkCourier').fadeIn();
            } else {
                $('#btnBulkCourier').fadeOut();
            }
        }

        $('#selectAllOrders').on('change', function() {
            $('.orderCheckbox').prop('checked', $(this).is(':checked'));
            updateBulkButton();
        });

        $(document).on('change', '.orderCheckbox', function() {
            updateBulkButton();
        });

        $('#btnBulkCourier').on('click', function() {
            var ids = [];
            $('.orderCheckbox:checked').each(function() {
                ids.push($(this).val());
            });
            $('#bulkOrderIds').val(JSON.stringify(ids));
            $('#bulkCourierModal').modal('show');
        });
    });
</script>
@endpush
@endsection
