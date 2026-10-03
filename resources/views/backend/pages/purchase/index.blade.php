@extends('backend.layouts.master')
@section('section-title', __('Purchase'))
@section('page-title', __('Purchase List'))
@if (check_permission('purchase.create'))
    @section('action-button')
        <a href="{{ route('purchase.create') }}" class="btn add_list_btn">
            <i class="mr-2 feather icon-plus"></i>
            {{ __('Add Purchase') }}
        </a>
    @endsection
@endif
@push('css')
    <style>
        @media print {

            table,
            table th,
            table td {
                color: black !important;
            }

            .h-hide {
                display: none;
            }
        }

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

        /* Details Modal Theme Adaptability */
        .summary-box-modal {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            transition: all 0.3s ease;
        }
        .modal-detail-label {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .modal-detail-value {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
        }
        
        /* Dark Mode Overrides for Details Modal */
        .dark-theme .modal-content,
        body.dark-theme .modal-content {
            background-color: #1e293b !important;
            color: #f1f5f9 !important;
            border-color: #334155 !important;
        }
        .dark-theme .summary-box-modal,
        body.dark-theme .summary-box-modal {
            background-color: #0f172a !important;
            border-color: #334155 !important;
        }
        .dark-theme .modal-detail-label,
        body.dark-theme .modal-detail-label {
            color: #94a3b8 !important;
        }
        .dark-theme .modal-detail-value,
        body.dark-theme .modal-detail-value {
            color: #f1f5f9 !important;
        }
        .dark-theme .modal-body .table,
        body.dark-theme .modal-body .table {
            color: #f1f5f9 !important;
        }
        .dark-theme .modal-body .table thead th,
        body.dark-theme .modal-body .table thead th {
            background-color: #0f172a !important;
            color: #f1f5f9 !important;
            border-color: #334155 !important;
        }
        .dark-theme .modal-body .table td,
        body.dark-theme .modal-body .table td {
            border-color: #334155 !important;
            color: #f1f5f9 !important;
        }
        .dark-theme .modal-body .bg-light,
        body.dark-theme .modal-body .bg-light {
            background-color: #0f172a !important;
        }
        .dark-theme .modal-body .text-muted,
        body.dark-theme .modal-body .text-muted {
            color: #94a3b8 !important;
        }
        .dark-theme .modal-body hr,
        body.dark-theme .modal-body hr {
            border-top-color: #334155 !important;
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body pt-1">
                    <form action="{{ route('purchase.index') }}" method="GET">
                        @php
                            $products = App\Models\Product::get();
                        @endphp
                        <div class="row h-hide px-2">
                            <div class="col-md col-12 mt-2 px-1">
                                <input type="date" class="form-control" name="startDate" value="{{ $startDate }}" />
                            </div>
                            <div class="col-md col-12 mt-2 px-1">
                                <input type="date" class="form-control" name="endDate" value="{{ $endDate }}" />
                            </div>
                            <div class="col-md col-12 mt-2 px-1">
                                <input type="text" placeholder="{{ __('Scan Barcode / Purchase No') }}" value="{{ $barcode ?? ($purchase_no ?? '') }}"
                                    name="barcode" class="form-control barcode-filter-input" data-barcode-input>
                            </div>
                            <div class="col-md col-12 mt-2 px-1">
                                <select name="product_id" id="" class="select2">
                                    <option value="">{{ __('Select Product') }}</option>
                                    @foreach ($allProduct as $item)
                                        <option value="{{ $item->id }}"{{ $product_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md col-12 mt-2 px-1">
                                <select name="supplier_id" id="" class="select2">
                                    <option value="">{{ __('Select Supplier') }}</option>
                                    @foreach ($suppliers as $item)
                                        <option
                                            value="{{ $item->id }}"{{ $supplier_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-4 h-hide">
                            <div class="col-md-12 flex flex-wrap items-center justify-between gap-3">
                                <div class="flex flex-wrap gap-2">
                                    <button type="submit" class="btn add_list_btn">{{ __('Filter') }}</button>
                                    <a href="{{ route('purchase.index') }}" class="btn add_list_btn_reset">{{ __('Reset') }}</a>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <a href="" class="btn add_list_btn" onclick="window.print()">{{ __('Print') }}</a>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive mt-3">
                        <table id="datatable-buttons" class="table table-striped table-bordered">
                            <thead class="header_bg">
                                <tr class="text-center">
                                    <th class="header_style_left"> {{ __('#SL') }} </th>
                                    <th> {{ __('Date') }} </th>
                                    <th> {{ __('Purchase No') }} </th>
                                    <th> {{ __('Supplier') }} </th>
                                    <th> {{ __('Total Amount') }} </th>
                                    <th> {{ __('Total Paid') }} </th>
                                    <th> {{ __('Total Due') }} </th>
                                    <th> {{ __('Status') }} </th>
                                    <th class="header_style_right"> {{ __('Action') }} </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_paid = 0;
                                    $total_amt = 0;
                                    $total_due = 0;
                                    $total_stock = 0;
                                @endphp
                                @forelse($purchases as $key => $data)
                                    @php
                                        $display_total = ($data->rtn_total_amount > 0) ? $data->rtn_total_amount : $data->total_amount;
                                        $display_paid = ($data->rtn_total_amount > 0) ? $data->rtn_total_paid : $data->total_paid;
                                        $display_due = ($data->status == 1 || $data->total_due <= 0.009) ? 0 : (($data->rtn_total_amount > 0) ? $data->rtn_total_due : $data->total_due);
                                        if ($display_due <= 0.009) {
                                            $display_due = 0;
                                        }

                                        $total_paid += $display_paid;
                                        $total_amt += $display_total;
                                        $total_due += $display_due;
                                        
                                        $purchase_item = App\Models\PurchaseItem::where(
                                            'purchase_id',
                                            $data->id,
                                        )->first();
                                        $purchase_items = App\Models\PurchaseItem::where(
                                            'purchase_id',
                                            $data->id,
                                        )->get();
                                        $total_stock += App\Models\PurchaseItem::where('purchase_id', $data->id)->sum(
                                            'main_qty',
                                        );
                                        $pur_qty = App\Models\PurchaseItem::where('purchase_id', $data->id)->sum(
                                            'main_qty',
                                        );
                                        $total_qty = 0;
                                        foreach ($purchase_items as $item) {
                                            $item = App\Models\PurchaseItem::where('id', $item->id)->first();
                                            $product = App\Models\Product::where('id', $item->product_id)
                                                ->with('unit.related_unit')
                                                ->first();
                                            $sub_qty = $item->sub_qty ?? 0;
                                            $related_value = ($product->unit->related_value > 0) ? $product->unit->related_value : 1;

                                            // main_qty + sub_qty convert to total units
                                            $total_main = ($item->main_qty * $related_value) + $sub_qty;

                                            $total_qty += $total_main;
                                        }

                                        $sto_qty = App\Models\PurchaseItem::where('purchase_id', $data->id)->sum(
                                            'stock_qty',
                                        );
                                        $stock_in = (float) $total_qty;
                                        $available = (float) $sto_qty;

                                    @endphp
                                    <tr class="text-center">
                                        <td class="table_data_style_left">{{ $key + 1 }}</td>
                                        <td>{{ $data->date }}</td>
                                        <td>{{ $data->purchase_no }}</td>
                                        <td>{{ $data->supplier?->name }}</td>
                                        <td>{{ number_format($display_total, 2) }}</td>
                                        <td>{{ number_format($display_paid, 2) }}</td>
                                        <td>{{ number_format($display_due, 2) }}</td>
                                        <td>
                                            @if ($data->status == 2)
                                                <span class="badge badge-danger">{{ __('Returned') }}</span>
                                            @elseif ($data->status == 1 || $display_due <= 0)
                                                <span class="badge badge-success">{{ __('Paid') }}</span>
                                            @else
                                                <span class="badge badge-warning">{{ __('Due') }}</span>
                                            @endif
                                        </td>
                                        <td class="table_data_style_right">
                                            <button type="button" class="btn btn-info btn-sm mr-1" data-toggle="modal" data-target="#detailsModal-{{ $data->id }}" title="{{ __('Quick View') }}">
                                                <i class="feather icon-eye"></i>
                                            </button>
                                            <div class="dropdown d-inline-block">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton-{{ $data->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton-{{ $data->id }}">
                                                    <a class="dropdown-item text-info" href="{{ route('purchase.show', $data->id) }}">
                                                        <i class="feather icon-eye"></i> {{ __('View Details') }}
                                                    </a>
                                                    {{-- pay button  --}}
                                                    @if (check_permission('purchase.pay'))
                                                        @if ($display_due > 0 && $data->status != 1)
                                                            <a class="dropdown-item text-success"
                                                                href="{{ url('purchase/pay/' . $data->id) }}">
                                                                <i class="feather icon-dollar-sign"></i> {{ __('Due') }}
                                                            </a>
                                                        @endif
                                                    @endif
                                                    @if (check_permission('rtnPurchase.edit'))
                                                        <a class="dropdown-item text-warning"
                                                            href="{{ route('rtnPurchase.edit', $data->id) }}">
                                                            <i class="fa fa-undo"></i> {{ __('Return') }}
                                                        </a>
                                                    @endif
                                                    {{-- @if ($stock_in == $available) --}}
                                                        @if (check_permission('purchase.edit'))
                                                            <a class="dropdown-item text-info"
                                                                href="{{ url('purchase/edit/' . $data->id . '?' . http_build_query(request()->query())) }}">
                                                                <i class="fa fa-edit"></i> {{ __('Edit') }}
                                                            </a>
                                                        @endif
                                                    {{-- @endif --}}
                                                    {{-- print --}}
                                                    <a class="dropdown-item text-success"
                                                        href="{{ route('purchase.print', $data->id) }}">
                                                        <i class="feather icon-printer"></i> {{ __('Print') }}
                                                    </a>
                                                    @if ($data->rtn_total_amount > 0)
                                                        <a class="dropdown-item text-success"
                                                            href="{{ route('purchase.return.print', $data->id) }}">
                                                            <i class="feather icon-printer"></i> {{ __('Return Invoice Print') }}
                                                        </a>
                                                    @endif
                                                    {{-- delete --}}
                                                    @if ($stock_in == $available)
                                                        @if (check_permission('purchase.destroy'))
                                                            <a class="dropdown-item text-danger" href="#"
                                                                data-toggle="modal"
                                                                data-target="#deleteModal-{{ $data->id }}">
                                                                <i class="feather icon-trash"></i> {{ __('Delete') }}
                                                            </a>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-danger no_data_style">{{ __('No Purchase Found') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="text-right header_bg">
                                    <td colspan="4" class="header_style_left text-white "><strong>{{ __('Total') }}: </strong></td>
                                    <td> <strong class="text-white ">{{ number_format($total_amt, 2) }} </strong></td>
                                    <td> <strong class="text-white ">{{ number_format($total_paid, 2) }} </strong></td>
                                    <td> <strong class="text-white ">{{ number_format($total_due, 2) }} </strong></td>
                                    <td colspan="2" class="header_style_right"></td>
                                </tr>
                            </tfoot>
                        </table>

                        {{-- Render modals here outside the table --}}
                        @foreach ($purchases as $data)
                            @php
                                $purchase_items = App\Models\PurchaseItem::where('purchase_id', $data->id)->get();
                                $display_total = ($data->rtn_total_amount > 0) ? $data->rtn_total_amount : $data->total_amount;
                                $display_paid = ($data->rtn_total_amount > 0) ? $data->rtn_total_paid : $data->total_paid;
                                $display_due = ($data->status == 1 || $data->total_due <= 0.009) ? 0 : (($data->rtn_total_amount > 0) ? $data->rtn_total_due : $data->total_due);
                                if ($display_due <= 0.009) {
                                    $display_due = 0;
                                }
                                $sto_qty = App\Models\PurchaseItem::where('purchase_id', $data->id)->sum('stock_qty');
                                $total_qty = 0;
                                foreach ($purchase_items as $item) {
                                    $product = App\Models\Product::where('id', $item->product_id)->with('unit.related_unit')->first();
                                    $sub_qty = $item->sub_qty ?? 0;
                                    $related_value = ($product->unit->related_value > 0) ? $product->unit->related_value : 1;
                                    $total_qty += ($item->main_qty * $related_value) + $sub_qty;
                                }
                                $stock_in = (float) $total_qty;
                                $available = (float) $sto_qty;
                            @endphp

                            {{-- delete modal --}}
                            <form action="{{ route('purchase.destroy', $data->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <x-delete-modal title="{{ __('Purchase') }}" id="{{ $data->id }}" />
                            </form>

                            {{-- Details Modal --}}
                            <div class="modal fade" id="detailsModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel-{{ $data->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-xl" role="document">
                                    <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
                                        <div class="modal-header bg-primary text-white d-flex align-items-center justify-content-between">
                                            <h5 class="modal-title text-white" id="detailsModalLabel-{{ $data->id }}">
                                                <i class="feather icon-eye mr-2"></i>{{ __('Purchase Details') }} - {{ $data->purchase_no }}
                                            </h5>
                                            <button type="button" class="close text-white close_modal_btn" data-dismiss="modal" aria-label="Close" style="border: none; background: transparent; font-size: 24px; line-height: 1;">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body text-left">
                                            <div class="row">
                                                <!-- Overview -->
                                                <div class="col-md-6 mb-3">
                                                    <h6 class="font-weight-bold text-primary border-bottom pb-2 mb-2"><i class="feather icon-info mr-1"></i> {{ __('Overview') }}</h6>
                                                    <div class="row">
                                                        <div class="col-6 mb-2">
                                                            <div class="text-muted small modal-detail-label">{{ __('Purchase No') }}</div>
                                                            <div class="font-weight-bold text-primary modal-detail-value">{{ $data->purchase_no }}</div>
                                                        </div>
                                                        <div class="col-6 mb-2">
                                                            <div class="text-muted small modal-detail-label">{{ __('Date') }}</div>
                                                            <div class="font-weight-bold modal-detail-value">{{ $data->date }}</div>
                                                        </div>
                                                        <div class="col-6 mb-2">
                                                            <div class="text-muted small modal-detail-label">{{ __('Supplier') }}</div>
                                                            <div class="font-weight-bold modal-detail-value">{{ $data->supplier?->name }} ({{ $data->supplier?->phone ?? 'N/A' }})</div>
                                                        </div>
                                                        <div class="col-6 mb-2">
                                                            <div class="text-muted small modal-detail-label">{{ __('Branch') }}</div>
                                                            <div class="font-weight-bold modal-detail-value">{{ $data->branch?->name ?? 'N/A' }}</div>
                                                        </div>
                                                        <div class="col-6 mb-2">
                                                            <div class="text-muted small modal-detail-label">{{ __('Creator') }}</div>
                                                            <div class="font-weight-bold modal-detail-value">{{ $data->user?->name ?? 'N/A' }}</div>
                                                        </div>
                                                        <div class="col-6 mb-2">
                                                            <div class="text-muted small modal-detail-label">{{ __('Status') }}</div>
                                                            <div>
                                                                @if ($data->status == 2)
                                                                    <span class="badge badge-danger">{{ __('Returned') }}</span>
                                                                @elseif ($data->status == 1 || $display_due <= 0)
                                                                    <span class="badge badge-success">{{ __('Paid') }}</span>
                                                                @else
                                                                    <span class="badge badge-warning">{{ __('Due') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Financial Summary -->
                                                <div class="col-md-6 mb-3">
                                                    <h6 class="font-weight-bold text-primary border-bottom pb-2 mb-2"><i class="feather icon-credit-card mr-1"></i> {{ __('Financials') }}</h6>
                                                    <div class="summary-box-modal">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="text-muted small modal-detail-label">{{ __('Sub Total') }}</span>
                                                            <span class="font-weight-bold modal-detail-value">{{ number_format($data->estimated_amount, 2) }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="text-muted small modal-detail-label">{{ __('Discount') }} {{ str_contains($data->discount, '%') ? "({$data->discount})" : '' }}</span>
                                                            <span class="font-weight-bold text-danger">-{{ number_format($data->discount_amount, 2) }}</span>
                                                        </div>
                                                        @if ($data->vat_amount > 0)
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <span class="text-muted small modal-detail-label">{{ __('VAT') }} {{ str_contains($data->vat, '%') ? "({$data->vat})" : '' }}</span>
                                                                <span class="font-weight-bold modal-detail-value">+{{ number_format($data->vat_amount, 2) }}</span>
                                                            </div>
                                                        @endif
                                                        <hr class="my-2">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="font-weight-bold text-primary modal-detail-label">{{ __('Grand Total') }}</span>
                                                            <span class="font-weight-bold text-primary modal-detail-value" style="font-size: 15px;">{{ number_format($display_total, 2) }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="text-muted small modal-detail-label">{{ __('Total Paid') }}</span>
                                                            <span class="font-weight-bold text-success">{{ number_format($display_paid, 2) }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <span class="text-muted small modal-detail-label">{{ __('Total Due') }}</span>
                                                            <span class="font-weight-bold text-danger">{{ number_format($display_due, 2) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Product items table -->
                                            <h6 class="font-weight-bold text-primary border-bottom pb-2 mb-2"><i class="feather icon-box mr-1"></i> {{ __('Items Purchased') }}</h6>
                                            <div class="table-responsive mb-3">
                                                <table class="table table-bordered table-striped table-sm">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>{{ __('Item Name') }}</th>
                                                            <th>{{ __('Qty') }}</th>
                                                            <th>{{ __('Rate') }}</th>
                                                            <th>{{ __('Subtotal') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($purchase_items as $m_idx => $m_item)
                                                            @php
                                                                $product = App\Models\Product::where('id', $m_item->product_id)->with('unit.related_unit')->first();
                                                                if ($product->unit->related_unit == null) {
                                                                    $qty_str = $m_item->actual_main . ' ' . $product->unit->name;
                                                                } else {
                                                                    $sub_qty = $m_item->actual_sub == null ? 0 : $m_item->actual_sub;
                                                                    $qty_str = $m_item->actual_main . ' ' . $product->unit->name . ' ' . $sub_qty . ' ' . $product->unit->related_unit->name;
                                                                }
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $m_idx + 1 }}</td>
                                                                <td>
                                                                    <strong>{{ $m_item->product?->name }}</strong>
                                                                    @if ($m_item->is_return == 1)
                                                                        <span class="badge badge-danger ml-1">{{ __('Return') }}</span>
                                                                    @endif
                                                                    @if ($m_item->product_variation_id != null)
                                                                        <br><small class="text-muted">Variation: {{ $m_item->product_variation?->size?->size }}-{{ $m_item->product_variation?->color?->color }}</small>
                                                                    @endif
                                                                    @if (!empty($m_item->imei))
                                                                        <br><small class="text-muted">IMEI: {{ str_replace("\n", ", ", $m_item->imei) }}</small>
                                                                    @endif
                                                                    @if($m_item->warranty_value)
                                                                        <br><small class="text-muted">Warranty: {{ $m_item->warranty_value }} {{ $m_item->warranty_unit }}</small>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $qty_str }}</td>
                                                                <td>{{ number_format($m_item->rate, 2) }}</td>
                                                                <td>{{ number_format($m_item->subtotal, 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                                            <a href="{{ route('purchase.print', $data->id) }}" class="btn btn-success">
                                                <i class="feather icon-printer"></i> {{ __('Print') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{ $purchases->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
