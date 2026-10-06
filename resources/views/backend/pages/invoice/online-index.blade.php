@extends('backend.layouts.master')
@section('section-title', __('Online Sale'))
@section('page-title', __('Online Sale List'))
@section('action-button')
    <a href="{{ route('web-orders.index') }}" class="btn btn-outline-warning mr-2 font-weight-bold" style="border-radius: 8px;">
        <i class="feather icon-shopping-bag mr-1"></i> {{ __('Web Order List') }}
    </a>
    @if (check_permission('invoice.create'))
        <a href="{{ route('invoice.create') }}" class="btn add_list_btn" style="border-radius: 8px;">
            <i class="mr-2 feather icon-plus"></i>
            {{ __('Add Invoice') }}
        </a>
    @endif
@endsection
@push('css')
    <style>
        @media print {
            body {
                margin: 0;
            }

            #printableArea {
                width: 75mm;
                height: 100mm;
                border: 1px solid black;
                /* Ensure border is visible when printing */
                padding: 10mm;
                box-sizing: border-box;
            }
        }


        .dropdown-menu {
            position: absolute !important;
            z-index: 9999;
        }

        .parent-container {
            overflow: visible !important;
        }
    </style>

    <style>
        @media print {
            body {
                font-size: 14px !important;
            }

            .container {
                width: 100% !important;
            }

            /* Each invoice will be on a new page */
            .print_part2 {
                page-break-before: always;
            }

            /* Hide Modal during printing */
            .modal,
            .modal-backdrop {
                display: none !important;
            }

            /* To prevent table and elements from being cut off */
            .list-item {
                page-break-inside: avoid;
            }

            /* Adjust logo size */
            .logo_part img {
                max-width: 100px !important;
                height: auto !important;
            }
        }

        .table-responsive {
            overflow: visible !important;
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
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong><i class="feather icon-check-circle mr-1"></i> {{ __('Success') }}:</strong> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    @if (session('info'))
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <strong><i class="feather icon-info mr-1"></i> {{ __('Info') }}:</strong> {{ session('info') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    <form action="{{ route('invoice.online.sale') }}" method="GET">
                        @php
                            $customers = App\Models\Customer::get();
                            $products = App\Models\Product::get();
                        @endphp
                        <div class="row h-hide align-items-end mb-2">
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-2">
                                <label class="font-weight-bold text-muted small mb-1">{{ __('Start Date') }}</label>
                                <input type="date" class="form-control" name="startDate" value="{{ $startDate }}" />
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-2">
                                <label class="font-weight-bold text-muted small mb-1">{{ __('End Date') }}</label>
                                <input type="date" class="form-control" name="endDate" value="{{ $endDate }}" />
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-2">
                                <label class="font-weight-bold text-muted small mb-1">{{ __('Barcode / Invoice') }}</label>
                                <input type="text" placeholder="{{ __('Scan Barcode / Invoice No') }}" name="barcode"
                                    value="{{ $barcode ?? ($invoice_no ?? '') }}" class="form-control barcode-filter-input" data-barcode-input>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-12 mb-2">
                                <label class="font-weight-bold text-muted small mb-1">{{ __('Product') }}</label>
                                <select name="product_id" id="" class="select2 form-control">
                                    <option value="">{{ __('Select Product') }}</option>
                                    @foreach ($products as $item)
                                        <option value="{{ $item->id }}"{{ $product_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 col-12 mb-2">
                                <label class="font-weight-bold text-muted small mb-1">{{ __('Customer') }}</label>
                                <select name="customer_id" id="" class="select2 form-control">
                                    <option value="">{{ __('Select Customer') }}</option>
                                    @foreach ($customers as $item)
                                        <option
                                            value="{{ $item->id }}"{{ $customer_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }} {{ $item->phone }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row h-hide mb-3" style="margin-top: 5px !important;">
                            <div class="col-md-12 d-flex flex-wrap align-items-center justify-content-between">
                                <div class="d-flex flex-wrap gap-2 mb-1">
                                    <button type="submit" class="btn add_list_btn mr-1">{{ __('Filter') }}</button>
                                    <a href="{{ route('invoice.online.sale') }}" class="btn add_list_btn_reset mr-1">{{ __('Reset') }}</a>
                                </div>
                                <div class="d-flex flex-wrap align-items-center mb-1">
                                    <button type="button" class="btn btn-success font-weight-bold mr-1" onclick="openClearDuesModal()" title="{{ __('Clear due for selected invoices') }}">
                                        <i class="feather icon-check-circle mr-1"></i> {{ __('Clear Selected Dues') }}
                                    </button>
                                    <button type="button" class="btn add_list_btn mr-1" onclick="printSelectedInvoices()">{{ __('Print Label') }}</button>
                                    <a href="" class="btn add_list_btn" onclick="window.print()">{{ __('Print') }}</a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Modal for Clear Selected Dues -->
                    <div class="modal fade" id="clearDuesModal" tabindex="-1" role="dialog" aria-labelledby="clearDuesModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title text-white" id="clearDuesModalLabel">
                                        <i class="feather icon-dollar-sign mr-1"></i> {{ __('Clear Due for Selected Invoices') }}
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form action="{{ route('invoice.online.clear-selected-dues') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="invoice_ids" id="selectedInvoiceIdsInput">
                                    <div class="modal-body text-left">
                                        <div class="alert alert-info py-2 px-3 mb-3" style="border-radius: 6px;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span><i class="feather icon-check-square mr-1"></i> {{ __('Selected Invoices') }}: <strong id="modalSelectedCount">0</strong></span>
                                                <span>{{ __('Total Due') }}: <strong id="modalTotalDue" class="text-danger font-weight-bold">TK 0.00</strong></span>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="modalBankId" class="font-weight-bold text-dark">{{ __('Select Deposit Bank / Payment Account') }} <span class="text-danger">*</span></label>
                                            <select name="bank_id" id="modalBankId" class="form-control" required style="height: 42px;">
                                                @if(isset($bankAccounts))
                                                    @foreach ($bankAccounts as $account)
                                                        <option value="{{ $account->id }}">
                                                            {{ $account->bank_name }} @if(!empty($account->account_number))({{ $account->account_number }})@endif
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="modalPaymentDate" class="font-weight-bold text-dark">{{ __('Payment Date') }} <span class="text-danger">*</span></label>
                                            <input type="date" name="payment_date" id="modalPaymentDate" class="form-control" value="{{ date('Y-m-d') }}" required style="height: 42px;">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                                        <button type="submit" class="btn btn-success font-weight-bold">
                                            <i class="feather icon-check-circle mr-1"></i> {{ __('Confirm Due Payment') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive mt-2">
                        <table id="datatable-buttons" class="table table-striped table-bordered w-100" style="width: 100% !important;">
                            <thead class="header_bg">
                                <tr class="text-center">
                                    <th class="header_style_left"><input type="checkbox" id="selectAll"></th>
                                    <th> #SL </th>
                                    <th> Date </th>
                                    <th> Invoice No </th>
                                    <th> Customer </th>
                                    <th> Product Item(s) </th>
                                    <th> Total Amount </th>
                                    <th> Total Paid </th>
                                    <th> Total Due </th>
                                    <th> Return Amount </th>
                                    <th> Consignment ID </th>
                                    <th> Courier Status </th>
                                    <th> Create By </th>
                                    <th class="header_style_right"> Action </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_paid = 0;
                                    $total_amt = 0;
                                    $total_due = 0;
                                @endphp
                                @forelse($invoices as $key => $data)
                                    @php
                                        $total_paid += $data->total_paid;
                                        $total_amt += $data->total_amount;
                                        $total_due += $data->total_due;
                                        $inv_items = App\Models\InvoiceItem::where('invoice_id', $data->id)->get();
                                    @endphp
                                    <tr class="text-center">
                                        <td class="table_data_style_left">
                                            <input type="checkbox" class="invoiceCheckbox" value="{{ $data->id }}">
                                        </td>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $data->date }}</td>
                                        <td>
                                            <strong>{{ $data->invoice_no }}</strong>
                                        </td>
                                        <td>{{ $data->customer->name }}</td>
                                        <td>
                                            @foreach ($inv_items as $item)
                                                <div class="mb-1 d-flex align-items-center justify-content-between">
                                                    <span>
                                                        <strong>{{ $item->product?->name }}</strong>
                                                        @if(!empty($item->product?->barcode))
                                                            <span class="badge badge-light border text-dark px-1.5 py-0.5 ml-1 font-monospace" style="font-size: 11px;">
                                                                <i class="feather icon-maximize-2 mr-0.5"></i>{{ $item->product?->barcode }}
                                                            </span>
                                                        @endif
                                                        @if ($data->status == 2)
                                                            @if (env('APP_SC') == 'yes' && $item->product_variation)
                                                                <span class="badge badge-info px-1 py-0.5 ml-1" style="font-size: 10px;">{{ $item->product_variation?->size?->size }}-{{ $item->product_variation?->color?->color }}</span>
                                                            @endif
                                                        @else
                                                            @if ($item->is_return == 1)
                                                                <span class="badge bg-danger ml-1">Return</span>
                                                            @endif
                                                            @if (env('APP_SC') == 'yes' && $item->product_variation)
                                                                <span class="badge badge-info px-1 py-0.5 ml-1" style="font-size: 10px;">{{ $item->product_variation?->size?->size }}-{{ $item->product_variation?->color?->color }}</span>
                                                            @endif
                                                        @endif
                                                    </span>
                                                    <span class="badge badge-secondary ml-2 font-weight-bold">× {{ (int)$item->main_qty }}</span>
                                                </div>
                                            @endforeach
                                        </td>
                                        <td>{{ $data->total_amount }}</td>
                                        <td>{{ $data->total_paid }}</td>
                                        <td class="invoice-due-val">{{ $data->total_due }}</td>
                                        <td>
                                            @if ($data->return_amount == null)
                                                0.00
                                            @else
                                                {{ $data->return_amount }}
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($data->consignment_id))
                                                <span class="badge bg-primary text-white px-2 py-1" style="font-size: 12px; font-family: monospace;">{{ $data->consignment_id }}</span>
                                                <div class="small text-muted mt-1">{{ $data->courier_type ?? 'Steadfast' }}</div>
                                            @else
                                                <span class="badge bg-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($data->consignment_id))
                                                @php
                                                    $cType = strtolower(trim($data->courier_type ?? ''));
                                                @endphp
                                                @if ($cType == 'pathao')
                                                    @php
                                                        $token = getPathaoAccessToken();
                                                        $orderSummary = $token ? getOrderSummary($data->consignment_id, $token) : null;
                                                    @endphp
                                                    <span class="badge bg-info">{{ $orderSummary['data']['order_status'] ?? ($data->order_status ?: 'Submitted') }}</span>
                                                @elseif(in_array($cType, ['stead fast', 'steadfast', 'stead_fast', 'stead-fast']))
                                                    @php
                                                        $orderSummary = status($data->consignment_id);
                                                        $stStatus = $orderSummary['delivery_status'] ?? $data->order_status ?? 'in_review';
                                                    @endphp
                                                    <span class="badge bg-success">{{ ucfirst(str_replace('_', ' ', $stStatus)) }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $data->order_status ?? 'Pending' }}</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">Not Sent</span>
                                            @endif
                                        </td>
                                        <td>{{ $data->user->name }}</td>
                                        <td class="table_data_style_right text-center" style="white-space: nowrap;">
                                            <div class="d-inline-flex align-items-center justify-content-center" style="gap: 4px;">
                                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#detailsModal-{{ $data->id }}" title="{{ __('Quick View') }}" style="padding: 5px 9px;">
                                                    <i class="feather icon-eye"></i>
                                                </button>
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                                                        id="dropdownMenuButton-{{ $data->id }}" data-toggle="dropdown" data-boundary="window" style="padding: 5px 10px;">
                                                        Action
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton-{{ $data->id }}">
                                                    {{-- pay button  --}}
                                                    @if (check_permission('invoice.pay'))
                                                        @if ($data->total_due > 0)
                                                            <a class="dropdown-item"
                                                                href="{{ url('invoice/pay/' . $data->id) }}"
                                                                class="btn btn-success-rgba">
                                                                <i class="feather icon-dollar-sign"></i> Due
                                                            </a>
                                                        @endif
                                                    @endif
                                                    {{-- return amount --}}
                                                    @if ($data->total_due < 0)
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#editModal-{{ $data->id }}"
                                                            class="dropdown-item text-primary">
                                                            <i class="fa fa-undo"></i> Return amount
                                                        </a>
                                                    @endif
                                                    {{-- return --}}
                                                    {{-- @if (check_permission('return.create')) --}}
                                                    {{-- <a href="{{ route('invoice.online.sale.status', $data->consignment_id) }}"
                                                        class="dropdown-item" class="btn btn-danger-rgba">
                                                        <i class="fa fa-undo"></i> View Status
                                                    </a> --}}
                                                    <a href="{{ url('return/sale/' . $data->id) }}" class="dropdown-item"
                                                        class="btn btn-danger-rgba">
                                                        <i class="fa fa-undo"></i> Return
                                                    </a>
                                                    {{-- @endif --}}
                                                     @php
                                                         $hasReturns = App\Models\ReturnTbl::where('invoice_id', $data->id)->exists();
                                                     @endphp
                                                     {{-- exchange --}}
                                                     @if (check_permission('invoice.exchange'))
                                                         @if ($hasReturns)
                                                             <a href="#" class="dropdown-item text-muted disabled" onclick="alert('{{ __('This invoice has returns and cannot be exchanged. Please delete the return first.') }}'); return false;">
                                                                 <i class="fa fa-undo"></i> Exchange <small class="badge badge-warning">{{ __('Returned') }}</small>
                                                             </a>
                                                         @else
                                                             <a href="{{ url('invoice/exchange/' . $data->id) }}"
                                                                 class="dropdown-item" class="btn btn-danger-rgba">
                                                                 <i class="fa fa-undo"></i> Exchange
                                                             </a>
                                                         @endif
                                                     @endif

                                                     {{-- edit --}}
                                                     {{-- @if (check_permission('invoice.edit')) --}}
                                                     @if ($hasReturns)
                                                         <a href="#" class="dropdown-item text-muted disabled" onclick="alert('{{ __('This invoice has returns and cannot be edited. Please delete the return first.') }}'); return false;">
                                                             <i class="fa fa-pencil-square-o"></i> {{ __('Edit & Exchange') }} <small class="badge badge-warning">{{ __('Returned') }}</small>
                                                         </a>
                                                     @else
                                                         <a href="{{ route('inv.edit', $data->id) }}" class="dropdown-item"
                                                             class="btn btn-danger-rgba">
                                                             <i class="fa fa-pencil-square-o"></i> {{ __('Edit & Exchange') }}
                                                         </a>
                                                     @endif
                                                    {{-- @endif --}}
                                                    {{-- print --}}
                                                    <a class="dropdown-item" href="{{ route('invoice.print', $data->id) }}"
                                                        class="btn btn-success-rgba">
                                                        <i class="feather icon-printer"></i> Print
                                                    </a>
                                                    <a class="dropdown-item" href="#" class="btn btn-success-rgba"
                                                        data-toggle="modal"
                                                        data-target="#printModal-{{ $data->id }}">
                                                        <i class="feather icon-printer"></i> Label
                                                    </a>
                                                    @php
                                                        $return_tbl = App\Models\ReturnTbl::where(
                                                            'invoice_id',
                                                            $data->id,
                                                        )->first();
                                                        $del_invoice = App\Models\Invoice::where('id', $data->id)
                                                            ->latest()
                                                            ->first();
                                                        // dd($del_invoice->id);
                                                    @endphp

                                                    {{-- delete --}}
                                                    @if (check_permission('invoice.destroy'))
                                                        @if ($return_tbl?->invoice_id != $data->id)
                                                            <a href="#" class="dropdown-item" data-toggle="modal"
                                                                data-target="#deleteModal-{{ $data->id }}"
                                                                class="btn btn-danger-rgba">
                                                                <i class="feather icon-trash"></i> Delete
                                                            </a>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="14" class="text-center text-danger no_data_style">No Invoice Found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="text-right header_bg">
                                    <td colspan="6" class="header_style_left text-white "><strong>Total: </strong></td>
                                    <td> <strong class="text-white ">{{ number_format($total_amt, 2) }} </strong></td>
                                    <td> <strong class="text-white ">{{ number_format($total_paid, 2) }} </strong></td>
                                    <td> <strong class="text-white ">{{ number_format($total_due, 2) }} </strong></td>
                                    <td colspan="5" class="header_style_right"></td>
                                </tr>
                            </tfoot>
                        </table>

                        {{-- Render modals here outside the table --}}
                        @foreach ($invoices as $data)
                            <div class="modal fade" id="printModal-{{ $data->id }}" tabindex="-1"
                                aria-labelledby="printModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="printModalLabel">Print Label</h5>
                                            <button type="button" class="btn-close" data-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Printable design starts here -->
                                            <div id="printableArea-{{ $data->id }}" class="print_part mt-4">

                                                <div class="logo_part text-center">
                                                    <span class="mt-2 text-black"><strong>
                                                            <h5 class="text-center"
                                                                style="font-size:28px; font-weight:800">
                                                                {{ $data->shop?->shop_name }} </h5>
                                                        </strong></span>
                                                </div>
                                                <div class=" text-right text-black container">
                                                    <strong style="font-size: 12px">Date: {{ $data->date }}
                                                    </strong>
                                                </div>

                                                <div class="container text-black">
                                                    <div class="row">
                                                        <div class="col">
                                                            <strong> Invoice No: {{ $data->invoice_no }} </strong>
                                                            <br>
                                                            <strong> Parcel ID: {{ $data->consignment_id }}
                                                            </strong> </br>
                                                            <strong> NAME: {{ $data->customer->name }} </strong>
                                                            </br>
                                                            <strong> MOBILE: {{ $data->customer->phone }} </strong>
                                                            <br>
                                                            <strong> ADDRESS: {{ $data->customer->address }}
                                                            </strong>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class=" container text-black mt-2">
                                                    <div class="row">
                                                        <div class="col">
                                                            <h5 class="text-center text-black">Product List</h5>
                                                            <ul class="list"
                                                                style="list-style:none; margin-left:-40px">
                                                                <li class="list-item"
                                                                    style="list-style:none;font-size:14px">
                                                                    <div class="">
                                                                        <div class="row text-left product_font">
                                                                            <div class="col-2 text-left">
                                                                                <strong
                                                                                    style="font-size:14px">Sl</strong>
                                                                            </div>
                                                                            <div class="col-7  text-left">
                                                                                <strong
                                                                                    style="font-size:14px">Description</strong>
                                                                            </div>
                                                                            <div class="col-3 text-right">
                                                                                <strong
                                                                                    style="font-size:14px">Qty</strong>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                                @php
                                                                    $products = App\Models\InvoiceItem::where(
                                                                        'invoice_id',
                                                                        $data->id,
                                                                    )->get();
                                                                    // dd($product);
                                                                    $sl = 1;
                                                                @endphp
                                                                @foreach ($products as $item)
                                                                    <li class="list-item" style="font-size:12px">
                                                                        <div class="">
                                                                            <div class="row">
                                                                                <div class="col-2 text-left">
                                                                                    <strong style="font-size:13px">
                                                                                        {{ $sl++ }}
                                                                                    </strong>
                                                                                </div>
                                                                                <div class="col-7  text-left">
                                                                                    <strong style="font-size:13px">
                                                                                        {{ $item->product?->name }}
                                                                                        @if(!empty($item->product?->barcode))
                                                                                            <span style="font-size: 11px; font-weight: normal; color: #555;"> [{{ $item->product?->barcode }}]</span>
                                                                                        @endif
                                                                                    </strong>
                                                                                </div>
                                                                                <div class="col-3 text-right">
                                                                                    <strong style="font-size:13px">
                                                                                        @php
                                                                                            if ($item->product?->unit?->related_unit == null) {
                                                                                                $qty = $item->main_qty . ' ' . ($item->product?->unit?->name ?? '');
                                                                                            } else {
                                                                                                $sub_qty = $item->sub_qty ?? 0;
                                                                                                if ($item->main_qty == 0 && $sub_qty == 0) {
                                                                                                    $qty = '0 ' . $item->product?->unit?->name;
                                                                                                } else {
                                                                                                    $qty = '';
                                                                                                    if ($item->main_qty > 0) $qty .= $item->main_qty . ' ' . $item->product?->unit?->name;
                                                                                                    if ($item->main_qty > 0 && $sub_qty > 0) $qty .= ' - ';
                                                                                                    if ($sub_qty > 0) $qty .= $sub_qty . ' ' . $item->product?->unit?->related_unit?->name;
                                                                                                }
                                                                                            }
                                                                                        @endphp
                                                                                        {{ $qty }}
                                                                                    </strong>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class=" mb-4 mt-2 text-center text-black">
                                                    <span
                                                        style="border:1px solid black;font-size: 16px; padding: 15px 20px;font-weight:800;">COD
                                                        - TK {{ $data->total_due }}</span>
                                                </div>
                                            </div>
                                            <!-- Printable design ends here -->
                                        </div>
                                        <div class="modal-footer">
                                            <!-- Print button to trigger the print function -->
                                            <button type="button" class="btn btn-primary"
                                                onclick="printDiv('printableArea-{{ $data->id }}')">Print</button>
                                            <button type="button" class="btn btn-secondary"
                                                data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- return amount modal  --}}
                            <form action="{{ route('invoice.return.amount', $data->id) }}" method="POST">
                                @csrf
                                <x-edit-modal title="Payment Amount" sizeClass="modal-md"
                                    id="{{ $data->id }}">
                                    <div class="mt-2 col-md-12">
                                        <label class="form-label font-weight-bold">Date</label>
                                        <input type="date" class="form-control" value="{{ date('Y-m-d') }}"
                                            name="date">
                                        <div class="error">
                                            {{ $errors->has('date') ? $errors->first('date') : '' }}</div>
                                    </div>
                                    <div class="mt-2 col-md-12">
                                        <label class="form-label font-weight-bold">Bank Account *</label>
                                        @php
                                            $bank_accounts = App\Models\BankAccount::where('status', 1)
                                                ->orderBy('bank_name', 'asc')
                                                ->get();
                                        @endphp
                                        <select class="select2" name="bank_id">
                                            @foreach ($bank_accounts as $bank_account)
                                                <option value="{{ $bank_account->id }}">
                                                    {{ $bank_account->bank_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mt-2 col-md-12">
                                        <input type="text" name="return_cus_amount" id=""
                                            value="{{ $data->total_paid }}">
                                        <label for="return_paid_amount" class="form-label fw-bold">Amount
                                            *</label>
                                        <input type="number" class="form-control" min="1" step="any"
                                            required placeholder="Enter Amount" name="return_paid_amount"
                                            value="{{ abs($data->total_paid) }}">
                                        <div class="error">
                                            {{ $errors->has('return_paid_amount') ? $errors->first('return_paid_amount') : '' }}
                                        </div>
                                    </div>
                                </x-edit-modal>
                            </form>

                            {{-- delete modal --}}
                            <form action="{{ route('invoice.destroy', $data->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <x-delete-modal title="Invoice" id="{{ $data->id }}" />
                            </form>

                            {{-- Details Modal --}}
                            <div class="modal fade" id="detailsModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel-{{ $data->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-xl" role="document">
                                    <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
                                        <div class="modal-header bg-primary text-white d-flex align-items-center justify-content-between">
                                            <h5 class="modal-title text-white" id="detailsModalLabel-{{ $data->id }}">
                                                <i class="feather icon-eye mr-2"></i>{{ __('Invoice Details') }} - {{ $data->invoice_no }}
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
                                                            <div class="text-muted small modal-detail-label">{{ __('Invoice No') }}</div>
                                                            <div class="font-weight-bold text-primary modal-detail-value">{{ $data->invoice_no }}</div>
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
                                                        @if (env('APP_ONLINE') == 'yes')
                                                        <div class="col-6 mb-2">
                                                            <div class="text-muted small modal-detail-label">{{ __('Sale Type') }}</div>
                                                            <div class="font-weight-bold modal-detail-value">{{ ucfirst($data->sale_type) }}</div>
                                                        </div>
                                                        @if ($data->sale_type == 'Online')
                                                            <div class="col-6 mb-2">
                                                                <div class="text-muted small modal-detail-label">{{ __('Platform') }}</div>
                                                                <div class="font-weight-bold modal-detail-value">{{ $data->platform?->name ?? 'N/A' }}</div>
                                                            </div>
                                                            <div class="col-6 mb-2">
                                                                <div class="text-muted small modal-detail-label">{{ __('Source Link') }}</div>
                                                                <div class="font-weight-bold modal-detail-value">
                                                                    @if ($data->source_link)
                                                                        <a href="{{ $data->source_link }}" target="_blank">{{ $data->source_link }}</a>
                                                                    @else
                                                                        N/A
                                                                    @endif
                                                                </div>
                                                            </div>
                                                             <div class="col-6 mb-2">
                                                                 <div class="text-muted small modal-detail-label">{{ __('Courier Provider') }}</div>
                                                                 <div class="font-weight-bold modal-detail-value">{{ $data->courier_type ?? 'N/A' }}</div>
                                                             </div>
                                                             <div class="col-6 mb-2">
                                                                 <div class="text-muted small modal-detail-label">{{ __('Consignment ID') }}</div>
                                                                 <div class="font-weight-bold text-primary modal-detail-value">
                                                                     @if(!empty($data->consignment_id))
                                                                         <span class="badge bg-primary text-white p-1" style="font-size: 13px; font-family: monospace;">{{ $data->consignment_id }}</span>
                                                                     @else
                                                                         <span class="badge bg-secondary">N/A</span>
                                                                     @endif
                                                                 </div>
                                                             </div>
                                                        @endif
                                                        @endif
                                                        <div class="col-6 mb-2">
                                                            <div class="text-muted small modal-detail-label">{{ __('Status') }}</div>
                                                            <div>
                                                                @if ($data->status == 0)
                                                                    <span class="badge badge-warning">{{ __('Due') }}</span>
                                                                @elseif($data->status == 1)
                                                                    <span class="badge badge-success">{{ __('Paid') }}</span>
                                                                @elseif($data->status == 2)
                                                                    <span class="badge badge-danger">{{ __('Returned') }}</span>
                                                                @endif
                                                                @if ($data->is_edited == 1 || $data->edit_status == 'edited')
                                                                    <span class="badge badge-info">{{ __('Edited') }}</span>
                                                                @elseif($data->is_edited == 2 || $data->edit_status == 'exchange')
                                                                    <span class="badge badge-primary">{{ __('Exchange') }}</span>
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
                                                            <span class="font-weight-bold text-primary modal-detail-value" style="font-size: 15px;">{{ number_format($data->total_amount, 2) }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="text-muted small modal-detail-label">{{ __('Total Paid') }}</span>
                                                            <span class="font-weight-bold text-success">{{ number_format($data->total_paid, 2) }}</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between">
                                                            <span class="text-muted small modal-detail-label">{{ __('Total Due') }}</span>
                                                            <span class="font-weight-bold text-danger">{{ number_format($data->total_due, 2) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                              @php
                                                  $raw_modal_items = App\Models\InvoiceItem::where('invoice_id', $data->id)->with('product.unit.related_unit', 'product_variation.size', 'product_variation.color')->get();
                                                  $modal_items = $raw_modal_items->groupBy(function($item) {
                                                      return $item->product_id . '_' . ($item->product_variation_id ?? 0);
                                                  });
                                                  $modal_total_profit = 0;

                                                  $isEditedInvoice = ($data->is_edited == 1 || $data->is_edited == 2 || $data->edit_status == 'edited' || $data->edit_status == 'exchange' || $data->bankTransactions->count() > 1);
                                                  $firstTx = $data->bankTransactions->where('pay_type', 'invpay')->first();
                                                  $origDate = $firstTx?->date ?? date('Y-m-d', strtotime($data->created_at));
                                                  $origPaid = (float)($firstTx?->amount ?? 0);
                                                  $addedPaid = (float)$data->bankTransactions->where('pay_type', 'invpay_edit')->sum('amount');
                                                  $origAmt = max(0, (float)$data->total_amount - $addedPaid);
                                                  $totalQtyCount = $raw_modal_items->sum('actual_main');
                                                  $origQtyCount = max(1, $totalQtyCount - ($addedPaid > 0 ? 1 : 0));
                                              @endphp

                                              @if ($isEditedInvoice)
                                                  <!-- Edit & Date History Card -->
                                                  <div class="mb-3 p-3 rounded-lg border shadow-sm" style="background: linear-gradient(135deg, #f0fdf4 0%, #eff6ff 100%); border-color: #bfdbfe !important;">
                                                      <div class="d-flex align-items-center justify-content-between mb-2">
                                                          <span class="font-weight-bold text-primary" style="font-size: 13px;">
                                                              <i class="feather icon-calendar mr-1"></i> {{ __('Invoice Edit & Date Breakdown') }}
                                                          </span>
                                                          @if ($data->is_edited == 2 || $data->edit_status == 'exchange')
                                                              <span class="badge badge-primary">{{ __('Exchange') }}</span>
                                                          @else
                                                              <span class="badge badge-info">{{ __('Edited') }}</span>
                                                          @endif
                                                      </div>
                                                      <div class="row">
                                                          <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                                              <div class="p-2.5 bg-white rounded border border-slate-200 h-100 shadow-sm">
                                                                  <div class="d-flex justify-content-between align-items-center">
                                                                      <span class="text-muted small font-weight-bold uppercase" style="letter-spacing: 0.5px;">{{ __('Initial Sale') }}</span>
                                                                      <span class="badge badge-light border text-dark font-weight-bold">{{ $origDate }}</span>
                                                                  </div>
                                                                  <div class="mt-2 text-dark font-weight-bold" style="font-size: 13px;">
                                                                      <span>{{ __('Qty') }}: <span class="text-primary">{{ $origQtyCount }} pcs</span></span>
                                                                      <span class="text-muted mx-1">|</span>
                                                                      <span>{{ __('Amount') }}: <span class="text-primary">৳{{ number_format($origAmt > 0 ? $origAmt : $origPaid, 2) }}</span></span>
                                                                  </div>
                                                                  <div class="small text-muted mt-1">
                                                                      {{ __('Paid on') }} {{ $origDate }}: <strong class="text-success">৳{{ number_format($origPaid, 2) }}</strong>
                                                                  </div>
                                                              </div>
                                                          </div>
                                                          <div class="col-12 col-sm-6">
                                                              <div class="p-2.5 bg-white rounded border border-slate-200 h-100 shadow-sm">
                                                                  <div class="d-flex justify-content-between align-items-center">
                                                                      <span class="text-success small font-weight-bold uppercase" style="letter-spacing: 0.5px;">{{ __('After Edit') }}</span>
                                                                      <span class="badge badge-info font-weight-bold">{{ $data->date }}</span>
                                                                  </div>
                                                                  <div class="mt-2 text-dark font-weight-bold" style="font-size: 13px;">
                                                                      <span>{{ __('Total Qty') }}: <span class="text-success">{{ $totalQtyCount }} pcs</span></span>
                                                                      <span class="text-muted mx-1">|</span>
                                                                      <span>{{ __('Total Amount') }}: <span class="text-success">৳{{ number_format($data->total_amount, 2) }}</span></span>
                                                                  </div>
                                                                  <div class="small text-muted mt-1">
                                                                      {{ __('Added on') }} {{ $data->date }}: <strong class="text-success">৳{{ number_format($addedPaid, 2) }}</strong>
                                                                  </div>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              @endif

                                              <!-- Product items table -->
                                              <h6 class="font-weight-bold text-primary border-bottom pb-2 mb-2"><i class="feather icon-box mr-1"></i> {{ __('Items Ordered') }}</h6>
                                              <div class="table-responsive mb-3">
                                                  <table class="table table-bordered table-striped table-sm">
                                                      <thead class="bg-light">
                                                          <tr>
                                                              <th>#</th>
                                                              <th>{{ __('Item Name') }}</th>
                                                              <th>{{ __('Qty') }}</th>
                                                              <th>{{ __('Sale Rate') }}</th>
                                                              <th>{{ __('Purchase Rate') }}</th>
                                                              <th>{{ __('Discount') }}</th>
                                                              <th>{{ __('Sale Subtotal') }}</th>
                                                              <th>{{ __('Purchase Subtotal') }}</th>
                                                              <th>{{ __('Profit') }}</th>
                                                          </tr>
                                                      </thead>
                                                      <tbody>
                                                          @foreach ($modal_items as $m_idx => $m_group)
                                                              @php
                                                                  $m_item = $m_group->first();
                                                                  $tot_main = $m_group->sum('actual_main');
                                                                  $tot_sub = $m_group->sum('actual_sub');
                                                                  $tot_subtotal = $m_group->sum('subtotal');
                                                                  $tot_pur_subtotal = $m_group->sum('pur_subtotal');
                                                                  $tot_inv_subtotal = $m_group->sum('inv_subtotal');
                                                                  $m_profit = $tot_inv_subtotal - $tot_pur_subtotal;
                                                                  $modal_total_profit += $m_profit;

                                                                  $rel_val = $m_item->product?->unit?->related_value ?? 1;
                                                                  $tot_qty_calc = $tot_main + ($tot_sub / ($rel_val ?: 1));
                                                                  $unit_pur_rate = $tot_qty_calc > 0 ? ($tot_pur_subtotal / $tot_qty_calc) : 0;
                                                              @endphp
                                                              <tr>
                                                                  <td>{{ $loop->iteration }}</td>
                                                                  <td>
                                                                      <strong>{{ $m_item->product?->name }}</strong>
                                                                      @if(!empty($m_item->product?->barcode))
                                                                          <span class="badge badge-light border text-dark px-1.5 py-0.5 ml-1 font-monospace" style="font-size: 11px;">
                                                                              <i class="feather icon-maximize-2 mr-0.5"></i>{{ $m_item->product?->barcode }}
                                                                          </span>
                                                                      @endif
                                                                       @if ($m_item->suppliers->isNotEmpty())
                                                                           <span class="badge badge-secondary ml-1">{{ $m_item->suppliers->pluck('name')->implode(', ') }}</span>
                                                                       @endif
                                                                      @if ($m_item->is_return == 1)
                                                                          <span class="badge badge-danger ml-1">{{ __('Return') }}</span>
                                                                      @endif
                                                                      @if ($m_item->product_variation_id != null)
                                                                          <br><small class="text-muted">Variation: {{ $m_item->product_variation?->size?->size }}-{{ $m_item->product_variation?->color?->color }}</small>
                                                                      @endif
                                                                      @if (!empty($m_item->imei))
                                                                          <br><small class="text-muted">IMEI: {{ str_replace(',', ', ', $m_item->imei) }}</small>
                                                                      @endif
                                                                      @if($m_item->warranty_value)
                                                                          <br><small class="text-muted">Warranty: {{ $m_item->warranty_value }} {{ $m_item->warranty_unit }}</small>
                                                                      @endif
                                                                  </td>
                                                                  <td>
                                                                      @if ($m_item->product?->unit?->related_unit == null)
                                                                          {{ $tot_main }} {{ $m_item->product?->unit?->name ?? 'pcs' }}
                                                                      @else
                                                                          @if($tot_main == 0 && $tot_sub == 0)
                                                                              0 {{ $m_item->product?->unit?->name }}
                                                                          @else
                                                                              @if($tot_main > 0) {{ $tot_main }} {{ $m_item->product?->unit?->name }} @endif
                                                                              @if($tot_main > 0 && $tot_sub > 0) - @endif
                                                                              @if($tot_sub > 0) {{ $tot_sub }} {{ $m_item->product?->unit?->related_unit?->name }} @endif
                                                                          @endif
                                                                      @endif
                                                                  </td>
                                                                  <td>{{ number_format($m_item->rate, 2) }}</td>
                                                                  <td>{{ number_format($unit_pur_rate, 2) }}</td>
                                                                  <td>{{ $m_item->product_discount }}</td>
                                                                  <td>{{ number_format($tot_subtotal, 2) }}</td>
                                                                  <td>{{ number_format($tot_pur_subtotal, 2) }}</td>
                                                                  <td class="text-success font-weight-bold">{{ number_format($m_profit, 2) }}</td>
                                                              </tr>
                                                          @endforeach
                                                      </tbody>
                                                  </table>
                                              </div>

                                            <div class="row">
                                                <!-- Profit summary -->
                                                <div class="col-md-6 mb-2">
                                                    <div class="p-2 rounded bg-light-success d-flex justify-content-between align-items-center" style="border: 1.5px solid #a7f3d0;">
                                                        <span class="font-weight-bold text-success"><i class="feather icon-trending-up mr-1"></i> {{ __('Total Profit') }}</span>
                                                        <span class="font-weight-bold text-success" style="font-size: 15px;">{{ number_format($modal_total_profit - $data->discount_amount, 2) }}</span>
                                                    </div>
                                                </div>

                                                <!-- Auditing/Create info -->
                                                <div class="col-md-6 text-right">
                                                    <div class="text-muted small">{{ __('Created By') }}: {{ $data->user->name }}</div>
                                                    @if($data->updatedBy)
                                                        <div class="text-muted small">{{ __('Updated By') }}: {{ $data->updatedBy->name }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                                            <a href="{{ route('invoice.print', $data->id) }}" class="btn btn-success">
                                                <i class="feather icon-printer"></i> {{ __('Print') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        {{ $invoices->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div id="bulkPrintContainer" class="d-none">
        @foreach ($invoices as $data)
            <div id="printableArea2-{{ $data->id }}" class="print_part2" style="margin-left: -10px">
                <div class="logo_part text-center" style="margin-top: 0px">
                    <span class=" text-black"><strong>
                            <h5 class="text-center mt-2" style="font-size:22px; font-weight:800;">
                                {{ empty(get_setting('com_name')) ? 'Fast IT' : get_setting('com_name') }}</h5>
                        </strong></span>
                </div>
                <div class="mt-1 text-right text-black container" style="margin-top: 0px;">
                    <strong style="font-size: 12px">Date: {{ $data->date }}
                    </strong>
                </div>
                

                <div class="container text-black" style="margin-left: -10px">
                    <div class="row">
                        <div class="col-1"></div>
                        <div class="col-10 text-left" style="line-height: 1.1;">
                            <strong> Invoice No: {{ $data->invoice_no }} </strong> <br>
                            <strong> Parcel ID: {{ $data->consignment_id }}
                            </strong> </br>
                            <strong> NAME: {{ $data->customer->name }} </strong>
                            </br>
                            <strong> MOBILE: {{ $data->customer->phone }} </strong>
                            <br>
                            <strong> ADDRESS: {{ $data->customer->address }}
                            </strong>
                        </div>
                        <div class="col-1"></div>
                    </div>
                </div>

                <div class=" container text-black">
                    <div class="row">
                        {{-- <div class="col-1"></div> --}}
                        <div class="col-12">
                            <h5 class="text-center text-black">Product List</h5>
                            <ul class="list" style="list-style:none;">
                                <li class="list-item" style="list-style:none;font-size:14px">
                                    <div class="">
                                        <div class="row text-left product_font">
                                            <div class="col-2 text-left">
                                                <strong style="font-size:14px">Sl</strong>
                                            </div>
                                            <div class="col-7  text-left">
                                                <strong style="font-size:14px">Description</strong>
                                            </div>
                                            <div class="col-3 text-right">
                                                <strong style="font-size:14px">Qty</strong>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @php
                                    $products = App\Models\InvoiceItem::where('invoice_id', $data->id)->get();
                                    $sl = 1;
                                @endphp
                                @foreach ($products as $item)
                                    <li class="list-item" style="font-size:12px">
                                        <div class="">
                                            <div class="row">
                                                <div class="col-2 text-left">
                                                    <strong style="font-size:13px">
                                                        {{ $sl++ }}
                                                    </strong>
                                                </div>
                                                <div class="col-7  text-left">
                                                    <strong style="font-size:13px">
                                                        {{ $item->product?->name }}
                                                        @if(!empty($item->product?->barcode))
                                                            <span style="font-size: 11px; font-weight: normal; color: #555;"> [{{ $item->product?->barcode }}]</span>
                                                        @endif
                                                        @if (env('APP_IMEI') == 'yes' && !empty($item->imei))
                                                            <br>
                                                            IMEI: {{ str_replace(',', ', ', $item->imei) }}
                                                        @endif
                                                    </strong>
                                                </div>
                                                <div class="col-3 text-right">
                                                    <strong style="font-size:13px">
                                                        @php
                                                            if ($item->product?->unit?->related_unit == null) {
                                                                $qty = $item->main_qty . ' ' . ($item->product?->unit?->name ?? '');
                                                            } else {
                                                                $sub_qty = $item->sub_qty ?? 0;
                                                                if ($item->main_qty == 0 && $sub_qty == 0) {
                                                                    $qty = '0 ' . $item->product?->unit?->name;
                                                                } else {
                                                                    $qty = '';
                                                                    if ($item->main_qty > 0) $qty .= $item->main_qty . ' ' . $item->product?->unit?->name;
                                                                    if ($item->main_qty > 0 && $sub_qty > 0) $qty .= ' - ';
                                                                    if ($sub_qty > 0) $qty .= $sub_qty . ' ' . $item->product?->unit?->related_unit?->name;
                                                                }
                                                            }
                                                        @endphp
                                                        {{ $qty }}
                                                    </strong>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class=" mb-5 mt-2 text-center text-black">
                    <span style="border:1px solid black;font-size: 16px; padding: 15px 20px;font-weight:800;">COD
                        - TK {{ $data->total_due }}</span>
                </div>
                <div class="mb-4 mt-4 text-center text-black">
                    NO Return/NO Exchange Please Check before delivery man leave <br>
                    Thank you for keeping trust on us
                </div>
                
            </div>
        @endforeach
    </div>

@endsection
@push('js')
    <script>
        let selectAllEl = document.getElementById("selectAll");
        if (selectAllEl) {
            selectAllEl.addEventListener("click", function() {
                let checkboxes = document.querySelectorAll(".invoiceCheckbox");
                checkboxes.forEach(checkbox => checkbox.checked = this.checked);
            });
        }

        function openClearDuesModal() {
            let selectedInvoices = document.querySelectorAll(".invoiceCheckbox:checked");

            if (selectedInvoices.length === 0) {
                if (typeof toastMagic !== 'undefined' && toastMagic.error) {
                    toastMagic.error("{{ __('Please select at least one invoice to clear due.') }}");
                } else {
                    alert("{{ __('Please select at least one invoice using checkboxes to clear due.') }}");
                }
                return;
            }

            let ids = [];
            let totalDue = 0;

            selectedInvoices.forEach(cb => {
                ids.push(cb.value);
                let row = cb.closest('tr');
                if (row) {
                    let dueCell = row.querySelector('.invoice-due-val');
                    if (dueCell) {
                        totalDue += parseFloat(dueCell.textContent.replace(/[^0-9.-]+/g, '')) || 0;
                    }
                }
            });

            document.getElementById("selectedInvoiceIdsInput").value = JSON.stringify(ids);
            document.getElementById("modalSelectedCount").textContent = ids.length;
            document.getElementById("modalTotalDue").textContent = "TK " + totalDue.toFixed(2);

            $('#clearDuesModal').modal('show');
        }

        function printSelectedInvoices() {
            let selectedInvoices = document.querySelectorAll(".invoiceCheckbox:checked");

            if (selectedInvoices.length === 0) {
                toastMagic.error("Please select at least one invoice to print.");
                return;
            }

            $('.modal').modal('hide'); // Close modal

            setTimeout(() => {
                let printContents = "";
                selectedInvoices.forEach(invoice => {
                    let invoiceId = invoice.value;
                    let invoiceDiv = document.getElementById("printableArea2-" + invoiceId);
                    if (invoiceDiv) {
                        printContents += invoiceDiv.outerHTML +
                            '<div style="page-break-after: always;"></div>';
                    }
                });

                if (printContents) {
                    let originalContents = document.body.innerHTML;
                    document.body.innerHTML = `
                    <html>
                    <head>
                    <title>Bulk Invoice Print</title>
                    <style>
                        @media print {
                            body { font-size: 14px !important; }
                            .print_part2 { width: 100%; page-break-before: always; }
                            .modal, .modal-backdrop { display: none !important; }
                            .list-item { page-break-inside: avoid; }
                            .logo_part img { max-width: 100px !important; height: auto !important; }
                        }
                    </style>
                    </head>
                    <body>` + printContents + `</body></html>`;

                    window.print(); // Run print command

                    document.body.innerHTML = originalContents; // Restore to original state
                    // location.reload(); // Refresh to restore previous UI
                }
            }, 500);
        }

        function printDiv(divId) {
            var content = document.getElementById(divId).innerHTML;
            var originalContent = document.body.innerHTML;
            document.body.innerHTML = content;
            window.print();
            document.body.innerHTML = originalContent;
            window.location.reload(); // Reload the page to restore the original content
        }
    </script>
@endpush
