@extends('backend.layouts.master')
@section('section-title', __('Quotation'))
@section('page-title', __('Quotation List'))

@section('action-button')
    <a href="{{ route('quotation.create') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-plus"></i>
        {{ __('Add Quotation') }}
    </a>
@endsection

@push('css')
    <style>
        @media print {
            table, table th, table td {
                color: black !important;
            }
            .h-hide {
                display: none;
            }
        }
        .table-responsive {
            overflow: visible !important;
        }
        .card_style {
            overflow: visible !important;
        }
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
                    <form action="{{ route('quotation.index') }}" method="GET">
                        <div class="row h-hide">
                            <div class="col-md col-12 mt-1">
                                <input type="date" class="form-control" name="startDate" value="{{ request('startDate') }}" />
                            </div>
                            <div class="col-md col-12 mt-1">
                                <input type="date" class="form-control" name="endDate" value="{{ request('endDate') }}" />
                            </div>
                            <div class="col-md col-12 mt-1">
                                <input type="text" placeholder="{{ __('Scan Barcode / Quotation No') }}" name="barcode"
                                    value="{{ $barcode ?? (request('quotation_no') ?? '') }}" class="form-control barcode-filter-input" data-barcode-input>
                            </div>
                            <div class="col-md col-12 mt-1">
                                <select name="product_id" id="" class="select2">
                                    <option value="">{{ __('Select Product') }}</option>
                                    @foreach ($allProduct as $item)
                                        <option value="{{ $item->id }}"{{ request('product_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md col-12 mt-1">
                                <select name="customer_id" id="" class="select2">
                                    <option value="">{{ __('Select Customer') }}</option>
                                    @foreach ($allCustomer as $item)
                                        <option value="{{ $item->id }}"{{ request('customer_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }} {{ $item->phone }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2 h-hide d-flex justify-content-between">
                            <div class="col-md-12">
                                <button type="submit" class="btn add_list_btn">{{ __('Filter') }}</button>
                                <a href="{{ route('quotation.index') }}" class="btn add_list_btn_reset">{{ __('Reset') }}</a>
                                <a href="" class="btn add_list_btn float-right" onclick="window.print()">{{ __('Print') }}</a>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive mt-2">
                        <table id="datatable-buttons" class="table table-striped table-bordered">
                            <thead class="header_bg">
                                <tr class="text-center">
                                    <th class="header_style_left"> {{ __('#SL') }} </th>
                                    <th> {{ __('Date') }} </th>
                                    <th> {{ __('Quotation No') }} </th>
                                    <th> {{ __('Customer') }} </th>
                                    <th> {{ __('Subtotal') }} </th>
                                    <th> {{ __('Discount') }} </th>
                                    <th> {{ __('Total Amount') }} </th>
                                    <th> {{ __('Created By') }} </th>
                                    <th class="header_style_right"> {{ __('Action') }} </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_sub = 0;
                                    $total_discount = 0;
                                    $total_amt = 0;
                                @endphp
                                @forelse($quotations as $key => $data)
                                    @php
                                        $total_sub += $data->estimated_amount;
                                        $total_discount += $data->discount_amount;
                                        $total_amt += $data->total_amount;
                                    @endphp
                                    <tr class="text-center">
                                        <td class="table_data_style_left">{{ $key + 1 }}</td>
                                        <td>{{ $data->date }}</td>
                                        <td>{{ $data->quotation_no }}</td>
                                        <td>{{ $data->customer->name }}</td>
                                        <td>{{ number_format($data->estimated_amount, 2) }}</td>
                                        <td>{{ number_format($data->discount_amount, 2) }}</td>
                                        <td>{{ number_format($data->total_amount, 2) }}</td>
                                        <td>{{ $data->user->name }}</td>
                                        <td class="table_data_style_right">
                                            <button type="button" class="btn btn-info btn-sm mr-1" data-toggle="modal" data-target="#detailsModal-{{ $data->id }}" title="{{ __('Quick View') }}">
                                                <i class="feather icon-eye"></i>
                                            </button>
                                            <div class="dropdown d-inline-block">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown" data-boundary="window">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item text-success" href="{{ route('quotation.print', $data->id) }}">
                                                        <i class="feather icon-printer"></i> {{ __('Print') }}
                                                    </a>
                                                    @if($data->status == 1)
                                                        <a class="dropdown-item text-muted disabled" href="javascript:void(0)">
                                                            <i class="feather icon-check-circle text-success"></i> {{ __('Converted') }}
                                                        </a>
                                                    @else
                                                        <a class="dropdown-item text-warning" href="{{ route('invoice.create', ['quotation_id' => $data->id]) }}">
                                                            <i class="feather icon-shopping-cart"></i> {{ __('Add to Sale') }}
                                                        </a>
                                                    @endif
                                                    @if($data->status != 1)
                                                    <a class="dropdown-item text-primary" href="{{ route('quotation.edit', $data->id) }}">
                                                        <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                    </a>
                                                    @endif
                                                    <a href="#" class="dropdown-item text-danger" data-toggle="modal"
                                                        data-target="#deleteModal-{{ $data->id }}">
                                                        <i class="feather icon-trash"></i> {{ __('Delete') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-danger no_data_style">{{ __('No Quotation Found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="text-right header_bg">
                                    <td colspan="4" class="header_style_left text-white "><strong>{{ __('Total') }}: </strong></td>
                                    <td> <strong class="text-white ">{{ number_format($total_sub, 2) }} </strong></td>
                                    <td> <strong class="text-white ">{{ number_format($total_discount, 2) }} </strong></td>
                                    <td> <strong class="text-white ">{{ number_format($total_amt, 2) }} </strong></td>
                                    <td colspan="2" class="header_style_right"></td>
                                </tr>
                            </tfoot>
                        </table>

                        {{-- Render modals here outside the table --}}
                        @foreach ($quotations as $data)
                            {{-- delete modal --}}
                            <form action="{{ route('quotation.destroy', $data->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <x-delete-modal title="{{ __('Quotation') }}" id="{{ $data->id }}" />
                            </form>

                            {{-- Details Modal --}}
                            <div class="modal fade" id="detailsModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel-{{ $data->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-xl" role="document">
                                    <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
                                        <div class="modal-header bg-primary text-white d-flex align-items-center justify-content-between">
                                            <h5 class="modal-title text-white" id="detailsModalLabel-{{ $data->id }}">
                                                <i class="feather icon-eye mr-2"></i>{{ __('Quotation Details') }} - {{ $data->quotation_no }}
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
                                                            <div class="text-muted small modal-detail-label">{{ __('Quotation No') }}</div>
                                                            <div class="font-weight-bold text-primary modal-detail-value">{{ $data->quotation_no }}</div>
                                                        </div>
                                                        <div class="col-6 mb-2">
                                                            <div class="text-muted small modal-detail-label">{{ __('Date') }}</div>
                                                            <div class="font-weight-bold modal-detail-value">{{ $data->date }}</div>
                                                        </div>
                                                        <div class="col-6 mb-2">
                                                            <div class="text-muted small modal-detail-label">{{ __('Customer') }}</div>
                                                            <div class="font-weight-bold modal-detail-value">{{ $data->customer->name }} ({{ $data->customer->phone }})</div>
                                                        </div>
                                                        <div class="col-6 mb-2">
                                                            <div class="text-muted small modal-detail-label">{{ __('Branch') }}</div>
                                                            <div class="font-weight-bold modal-detail-value">{{ $data->branch?->name ?? 'N/A' }}</div>
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
                                                        @if ($data->delivery_charge > 0)
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <span class="text-muted small modal-detail-label">{{ __('Delivery Charge') }}</span>
                                                                <span class="font-weight-bold modal-detail-value">+{{ number_format($data->delivery_charge, 2) }}</span>
                                                            </div>
                                                        @endif
                                                        <hr class="my-2">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="font-weight-bold text-primary modal-detail-label">{{ __('Grand Total') }}</span>
                                                            <span class="font-weight-bold text-primary modal-detail-value" style="font-size: 15px;">{{ number_format($data->total_amount, 2) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Product items table -->
                                            <h6 class="font-weight-bold text-primary border-bottom pb-2 mb-2"><i class="feather icon-box mr-1"></i> {{ __('Items Quoted') }}</h6>
                                            <div class="table-responsive mb-3">
                                                <table class="table table-bordered table-striped table-sm">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>{{ __('Description') }}</th>
                                                            <th>{{ __('Qty') }}</th>
                                                            <th>{{ __('Unit') }}</th>
                                                            <th>{{ __('Rate') }}</th>
                                                            <th>{{ __('Discount') }}</th>
                                                            <th>{{ __('Subtotal') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($data->quotationItems as $m_idx => $m_item)
                                                            <tr>
                                                                <td>{{ $m_idx + 1 }}</td>
                                                                <td>
                                                                    <strong>{{ $m_item->product?->name }}</strong>
                                                                    @if ($m_item->product_variation_id != null)
                                                                        <br><small class="text-muted">Variation: {{ $m_item->product_variation?->size?->size ?? '' }}-{{ $m_item->product_variation?->color?->color ?? '' }}</small>
                                                                    @endif
                                                                    @if (!empty($m_item->imei))
                                                                        <br><small class="text-muted">IMEI: {{ $m_item->imei }}</small>
                                                                    @endif
                                                                    @if($m_item->warranty_value)
                                                                        <br><small class="text-muted">Warranty: {{ $m_item->warranty_value }} {{ $m_item->warranty_unit }}</small>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $m_item->main_qty }}</td>
                                                                <td>{{ $m_item->product_unit }}</td>
                                                                <td>{{ number_format($m_item->rate, 2) }}</td>
                                                                <td>{{ $m_item->product_discount }}</td>
                                                                <td>{{ number_format($m_item->subtotal, 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    @if($data->note)
                                                        <div class="p-2 rounded bg-light border">
                                                            <strong>Subject:</strong> {{ $data->note }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                                            <a href="{{ route('quotation.print', $data->id) }}" class="btn btn-success">
                                                <i class="feather icon-printer"></i> {{ __('Print') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{ $quotations->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
