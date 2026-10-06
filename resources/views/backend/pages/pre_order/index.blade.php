@extends('backend.layouts.master')

@section('page-title', __('Pre-Order Management'))

@push('css')
<style>
    .header_bg th { background: #000ce2; color: #fff; font-size: 12px; text-transform: uppercase; letter-spacing: .5px; }
    .dark-theme .header_bg th { background: #1e293b; color: #94a3b8; }
    .pre-order-modal .modal-header { background: linear-gradient(135deg, #f97316, #ea580c); color: #fff; }
    .pre-order-modal .modal-title { font-weight: 700; }
    .dark-theme .modal-content { background: #1e293b; color: #e2e8f0; }
    .dark-theme .modal-header { border-color: #334155; }
    .dark-theme .modal-body { background: #1e293b; }
    .dark-theme .modal-footer { border-color: #334155; }
    .dark-theme .table td, .dark-theme .table th { border-color: #334155; color: #e2e8f0; }
    .table-responsive {
        overflow: visible !important;
    }
    @media (max-width: 768px) {
        .table-responsive {
            overflow-x: auto !important;
            overflow-y: hidden !important;
        }
    }
    .card_style {
        overflow: visible !important;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card m-b-30 card_style">
            <div class="card-body pt-1">

                {{-- Flash Messages --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        <i class="feather icon-check-circle me-2"></i> {!! session('success') !!}
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        <i class="feather icon-alert-circle me-2"></i> {!! session('error') !!}
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                @endif

                {{-- Filter Form --}}
                <form action="{{ route('pre-orders.index') }}" method="GET">
                    <div class="row h-hide mt-2">
                        <div class="col-md col-12 mt-1">
                            <input type="text" class="form-control" name="pre_order_no"
                                value="{{ request('pre_order_no') }}" placeholder="{{ __('Pre-Order No') }}">
                        </div>
                        <div class="col-md col-12 mt-1">
                            <select name="customer_id" class="select2">
                                <option value="">{{ __('All Customers') }}</option>
                                @foreach ($customers as $cust)
                                    <option value="{{ $cust->id }}" {{ request('customer_id') == $cust->id ? 'selected' : '' }}>
                                        {{ $cust->name }} {{ $cust->phone }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md col-12 mt-1">
                            <select name="status" class="form-control">
                                <option value="">{{ __('Pending (Default)') }}</option>
                                <option value="pending"   {{ request('status') == 'pending'   ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>{{ __('Converted') }}</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-2 h-hide">
                        <div class="col-md-12">
                            <button type="submit" class="btn add_list_btn">{{ __('Filter') }}</button>
                            <a href="{{ route('pre-orders.index') }}" class="btn add_list_btn_reset">{{ __('Reset') }}</a>
                        </div>
                    </div>
                </form>

                {{-- Table --}}
                <div class="pre-order-table-wrap">
                <div class="table-responsive mt-2">
                    <table class="table table-striped table-bordered">
                        <thead class="header_bg">
                            <tr class="text-center">
                                <th>#</th>
                                <th>{{ __('Pre-Order No') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Customer') }}</th>
                                <th>{{ __('Items & Qty') }}</th>
                                <th>{{ __('Total Amount') }}</th>
                                <th>{{ __('Branch') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($preOrders as $index => $order)
                                <tr class="text-center">
                                    <td>{{ $preOrders->firstItem() + $index }}</td>
                                    <td>
                                        <strong class="text-warning">{{ $order->pre_order_no }}</strong>
                                    </td>
                                    <td>{{ $order->created_at?->format('d M Y') }}<br>
                                        <small class="text-muted">{{ $order->created_at?->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $order->customer?->name ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">{{ $order->customer?->phone ?? '' }}</small>
                                    </td>
                                    <td class="text-left">
                                        <ul class="mb-0 pl-3" style="font-size:12px;">
                                            @foreach ($order->items as $item)
                                                <li>
                                                    <strong>{{ $item->product?->name ?? 'Product' }}</strong>
                                                    @if(!empty($item->product?->barcode))
                                                        <span class="badge badge-light border text-dark px-1.5 py-0.5 ml-1 font-monospace" style="font-size: 11px;">
                                                            <i class="feather icon-maximize-2 mr-0.5"></i>{{ $item->product?->barcode }}
                                                        </span>
                                                    @endif
                                                    <span class="badge badge-secondary ml-1">x{{ $item->quantity }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td><strong>TK {{ number_format($order->total_amount, 2) }}</strong></td>
                                    <td>{{ $order->branch?->name ?? 'Main' }}</td>
                                    <td>
                                        @if ($order->status == 'pending')
                                            <span class="badge badge-warning">{{ __('Pending') }}</span>
                                        @elseif ($order->status == 'converted')
                                            <span class="badge badge-success">{{ __('Converted') }}</span>
                                        @else
                                            <span class="badge badge-danger">{{ __('Cancelled') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- Eye / Quick View Button --}}
                                        <button type="button" class="btn btn-info btn-sm mr-1"
                                            data-toggle="modal"
                                            data-target="#preOrderModal-{{ $order->id }}"
                                            title="{{ __('Quick View') }}">
                                            <i class="feather icon-eye"></i>
                                        </button>

                                        {{-- Action Dropdown --}}
                                        <div class="dropdown d-inline-block pre-order-action-dropdown">
                                            <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                data-toggle="dropdown" data-boundary="window">
                                                {{ __('Action') }}
                                            </button>
                                            <div class="dropdown-menu">

                                                {{-- View Details --}}
                                                <a class="dropdown-item text-info"
                                                    href="{{ route('pre-orders.show', $order->id) }}">
                                                    <i class="feather icon-eye mr-2"></i>{{ __('View Details') }}
                                                </a>

                                                @if ($order->status == 'pending')

                                                    {{-- Convert to Sale → Go to POS --}}
                                                    <a class="dropdown-item text-success"
                                                        href="{{ route('invoice.create', ['pre_order_id' => $order->id, 'convert_mode' => 1]) }}">
                                                        <i class="feather icon-check-circle mr-2"></i>{{ __('Convert to Sale') }}
                                                    </a>

                                                    {{-- Edit --}}
                                                    <a class="dropdown-item text-primary"
                                                        href="{{ route('pre-orders.edit', $order->id) }}">
                                                        <i class="feather icon-edit mr-2"></i>{{ __('Edit') }}
                                                    </a>

                                                    {{-- Cancel --}}
                                                    <form method="POST" action="{{ route('pre-orders.cancel', $order->id) }}"
                                                        onsubmit="return confirm('{{ __('Cancel this pre-order?') }}')">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="feather icon-x-circle mr-2"></i>{{ __('Cancel') }}
                                                        </button>
                                                    </form>

                                                @elseif ($order->status == 'cancelled')

                                                    {{-- Delete (only for cancelled) --}}
                                                    <form method="POST" action="{{ route('pre-orders.destroy', $order->id) }}"
                                                        onsubmit="return confirm('{{ __('Permanently delete this cancelled pre-order? This cannot be undone.') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="feather icon-trash-2 mr-2"></i>{{ __('Delete') }}
                                                        </button>
                                                    </form>

                                                @endif

                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-danger py-4">
                                        <i class="feather icon-inbox" style="font-size:32px;"></i><br>
                                        {{ __('No pre-orders found.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>{{-- end table-responsive --}}
                </div>{{-- end pre-order-table-wrap --}}

                {{-- Pagination --}}
                @if ($preOrders->hasPages())
                    <div class="mt-3">
                        {{ $preOrders->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

{{-- Quick View Modals --}}
@foreach ($preOrders as $order)
<div class="modal fade pre-order-modal" id="preOrderModal-{{ $order->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="feather icon-eye mr-2"></i>
                    {{ __('Pre-Order Details') }} — {{ $order->pre_order_no }}
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted font-weight-bold" width="130">{{ __('Pre-Order No') }}</td>
                                <td><strong class="text-warning">{{ $order->pre_order_no }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted font-weight-bold">{{ __('Date') }}</td>
                                <td>{{ $order->created_at?->format('d M Y, h:i A') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted font-weight-bold">{{ __('Status') }}</td>
                                <td>
                                    @if ($order->status == 'pending')
                                        <span class="badge badge-warning">{{ __('Pending') }}</span>
                                    @elseif ($order->status == 'converted')
                                        <span class="badge badge-success">{{ __('Converted') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ __('Cancelled') }}</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted font-weight-bold" width="130">{{ __('Customer') }}</td>
                                <td>
                                    <strong>{{ $order->customer?->name ?? 'N/A' }}</strong><br>
                                    <small class="text-muted">{{ $order->customer?->phone ?? '' }}</small>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted font-weight-bold">{{ __('Branch') }}</td>
                                <td>{{ $order->branch?->name ?? 'Main Branch' }}</td>
                            </tr>
                            @if($order->note)
                            <tr>
                                <td class="text-muted font-weight-bold">{{ __('Note') }}</td>
                                <td>{{ $order->note }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
                <hr>
                <h6 class="font-weight-bold mb-2">{{ __('Items') }}</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="header_bg">
                            <tr class="text-center">
                                <th>#</th>
                                <th>{{ __('Product') }}</th>
                                <th>{{ __('Qty') }}</th>
                                <th>{{ __('Unit Price') }}</th>
                                <th>{{ __('Subtotal') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $i => $item)
                                <tr class="text-center">
                                    <td>{{ $i + 1 }}</td>
                                    <td class="text-left">
                                        <strong>{{ $item->product?->name ?? __('Deleted Product') }}</strong>
                                        @if(!empty($item->product?->barcode))
                                            <span class="badge badge-light border text-dark px-1.5 py-0.5 ml-1 font-monospace" style="font-size: 11px;">
                                                <i class="feather icon-maximize-2 mr-0.5"></i>{{ $item->product?->barcode }}
                                            </span>
                                        @endif
                                        @if($item->product?->code)
                                            <br><small class="text-muted">{{ $item->product->code }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>TK {{ number_format($item->unit_price, 2) }}</td>
                                    <td><strong>TK {{ number_format($item->subtotal, 2) }}</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="text-right">
                                <td colspan="4"><strong>{{ __('Grand Total') }}</strong></td>
                                <td><strong class="text-warning">TK {{ number_format($order->total_amount, 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('pre-orders.show', $order->id) }}" class="btn btn-info btn-sm">
                    <i class="feather icon-external-link mr-1"></i>{{ __('Full Details') }}
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection
