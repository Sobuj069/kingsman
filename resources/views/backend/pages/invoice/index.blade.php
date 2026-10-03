@extends('backend.layouts.master')
@section('section-title', __('Invoice'))
@section('page-title', __('Invoice List'))
@if (check_permission('invoice.create'))
    @section('action-button')
        <a href="{{ route('invoice.create') }}" class="btn add_list_btn">
            <i class="mr-2 feather icon-plus"></i>
            {{ __('Add Invoice') }}
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
                    <form action="{{ route('invoice.index') }}" method="GET">
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
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-2">
                                <label class="font-weight-bold text-muted small mb-1">{{ __('Product') }}</label>
                                <select name="product_id" id="" class="select2 form-control">
                                    <option value="">{{ __('Select Product') }}</option>
                                    @foreach ($allProduct as $item)
                                        <option value="{{ $item->id }}"{{ $product_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-2">
                                <label class="font-weight-bold text-muted small mb-1">{{ __('Category') }}</label>
                                <select name="category_id" id="" class="select2 form-control">
                                    <option value="">{{ __('Select Category') }}</option>
                                    @foreach ($allCategory ?? [] as $item)
                                        <option value="{{ $item->id }}"{{ ($category_id ?? '') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if (env('APP_SUB_CATEGORY') == 'yes')
                                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-2">
                                    <label class="font-weight-bold text-muted small mb-1">{{ __('Sub Category') }}</label>
                                    <select name="sub_category_id" id="" class="select2 form-control">
                                        <option value="">{{ __('Select Sub Category') }}</option>
                                        @foreach ($allSubCategory ?? [] as $subItem)
                                            <option value="{{ $subItem->id }}"{{ ($sub_category_id ?? '') == $subItem->id ? 'selected' : '' }}>
                                                {{ $subItem->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-2">
                                <label class="font-weight-bold text-muted small mb-1">{{ __('Customer') }}</label>
                                <select name="customer_id" id="" class="select2 form-control">
                                    <option value="">{{ __('Select Customer') }}</option>
                                    @foreach ($allCustomer as $item)
                                        <option
                                            value="{{ $item->id }}"{{ $customer_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }} {{ $item->phone }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-2">
                                <label class="font-weight-bold text-muted small mb-1">{{ __('Supplier') }}</label>
                                <select name="supplier_id" id="" class="select2 form-control">
                                    <option value="">{{ __('Select Supplier') }}</option>
                                    @foreach ($allSupplier as $item)
                                        <option
                                            value="{{ $item->id }}"{{ $supplier_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if (env('APP_IMEI') == 'yes')
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-2">
                                <label class="font-weight-bold text-muted small mb-1">{{ __('IMEI') }}</label>
                                <input type="text" placeholder="{{ __('Enter IMEI') }}" name="imei"
                                    value="{{ $imei }}" class="form-control">
                            </div>
                            @endif
                            <div class="col-xl-3 col-lg-4 col-md-6 col-12 mb-2 d-flex align-items-center">
                                <button type="submit" class="btn add_list_btn mr-1">{{ __('Filter') }}</button>
                                <a href="{{ route('invoice.index') }}" class="btn add_list_btn_reset mr-1">{{ __('Reset') }}</a>
                                <a href="" class="btn add_list_btn ml-auto" onclick="window.print()">{{ __('Print') }}</a>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive mt-2">
                        <table id="datatable-buttons" class="table table-striped table-bordered w-100" style="width: 100% !important;">
                            <thead class="header_bg">
                                <tr class="text-center">
                                    <th class="header_style_left"> {{ __('#SL') }} </th>
                                    <th> {{ __('Date') }} </th>
                                    <th> {{ __('Invoice No') }} </th>
                                    <th> {{ __('Customer') }} </th>
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
                                    $total_profit = 0;
                                @endphp
                                @forelse($invoices as $key => $data)
                                    @php
                                        $total_paid += $data->total_paid;
                                        $total_amt += $data->total_amount;
                                        $total_due += $data->total_due;
                                        $inv_items = App\Models\InvoiceItem::where('invoice_id', $data->id)->get();
                                        $payments = App\Models\BankTransaction::where('invoice_id', $data->id)->get();
                                        $pay_count = $payments->count();
                                        $return_tbl = App\Models\ReturnTbl::where('invoice_id', $data->id)->first();
                                        $del_invoice = App\Models\Invoice::where('id', $data->id)->latest()->first();
                                        // dd($del_invoice->id);
                                    @endphp
                                    <tr class="text-center">
                                        <td class="table_data_style_left">{{ $key + 1 }}</td>
                                        <td>{{ $data->date }}</td>
                                        <td>
                                            <strong>{{ $data->invoice_no }}</strong>
                                            @if(!empty($data->consignment_id))
                                                <div><span class="badge bg-primary text-white mt-1" style="font-size: 11px; font-family: monospace;">CID: {{ $data->consignment_id }}</span></div>
                                            @endif
                                        </td>
                                        <td>{{ $data->customer->name }}</td>
                                        @php
                                            $purchaseCost = 0;
                                            $saleCost = 0;
                                            foreach($inv_items as $item) {
                                                $purchaseCost += $item->pur_subtotal;
                                                $saleCost += $item->inv_subtotal;
                                            }
                                            $profit = $saleCost - $purchaseCost - $data->discount_amount;
                                            $total_profit += $profit;
                                        @endphp
                                        <td>{{ $data->total_amount }}</td>
                                        <td>{{ $data->total_paid }}</td>
                                        <td>{{ $data->total_due }}</td>
                                        <td>
                                            @if ($data->status == 0)
                                                <span class="badge badge-warning">{{ __('Due') }}</span>
                                            @elseif($data->status == 1)
                                                <span class="badge badge-success">{{ __('Paid') }}</span>
                                            @elseif($data->status == 2)
                                                <span class="badge badge-danger">{{ __('Returned') }}</span>
                                            @endif
                                            @if ($data->is_edited == 1 || $data->edit_status == 'edited')
                                                <span class="badge badge-info" title="{{ __('Invoice was edited') }}">{{ __('Edited') }}</span>
                                            @elseif($data->is_edited == 2 || $data->edit_status == 'exchange')
                                                <span class="badge badge-primary" title="{{ __('Invoice was exchanged') }}">{{ __('Exchange') }}</span>
                                            @endif
                                        </td>
                                        <td class="table_data_style_right text-center" style="white-space: nowrap;">
                                            <div class="d-inline-flex align-items-center justify-content-center" style="gap: 4px;">
                                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#detailsModal-{{ $data->id }}" title="{{ __('Quick View') }}" style="padding: 5px 9px;">
                                                    <i class="feather icon-eye"></i>
                                                </button>
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                        id="dropdownMenuButton-{{ $data->id }}" data-toggle="dropdown" data-boundary="window" style="padding: 5px 10px;">
                                                        {{ __('Action') }}
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton-{{ $data->id }}">
                                                    <a class="dropdown-item text-info" href="{{ route('invoice.print', $data->id) }}">
                                                        <i class="feather icon-eye"></i> {{ __('View Details') }}
                                                    </a>

                                                    <a class="dropdown-item text-success" href="{{ route('invoice.print', $data->id) }}">
                                                        <i class="feather icon-printer"></i> {{ __('Print') }}
                                                    </a>
                                                     @if (check_permission('invoice.edit'))
                                                         @if ($return_tbl?->invoice_id == $data->id)
                                                             <a class="dropdown-item text-muted disabled" href="#" onclick="alert('{{ __('This invoice has returns and cannot be edited. Please delete the return first.') }}'); return false;">
                                                                 <i class="feather icon-edit"></i> {{ __('Edit & Exchange') }} <small class="badge badge-warning">{{ __('Returned') }}</small>
                                                             </a>
                                                         @else
                                                             <a class="dropdown-item text-primary" href="{{ route('inv.edit', $data->id) }}">
                                                                 <i class="feather icon-edit"></i> {{ __('Edit & Exchange') }}
                                                             </a>
                                                         @endif
                                                     @endif
                                                     {{-- pay button  --}}
                                                     @if (check_permission('invoice.pay'))
                                                         @if ($data->total_due > 0)
                                                             <a class="dropdown-item text-success"
                                                                 href="{{ url('invoice/pay/' . $data->id) }}">
                                                                 <i class="feather icon-dollar-sign"></i> {{ __('Due') }}
                                                             </a>
                                                         @endif
                                                     @endif
                                                     {{-- return --}}
                                                     @if (check_permission('return.create'))
                                                         <a href="{{ url('return/sale/' . $data->id) }}"
                                                             class="dropdown-item text-danger">
                                                             <i class="fa fa-undo"></i> {{ __('Return') }}
                                                         </a>
                                                     @endif
                                                

                                                     {{-- delete --}}
                                                     @if (check_permission('invoice.destroy'))
                                                         <a href="#" class="dropdown-item text-danger" data-toggle="modal"
                                                             data-target="#deleteModal-{{ $data->id }}">
                                                             <i class="feather icon-trash"></i> Delete
                                                             @if ($return_tbl?->invoice_id == $data->id)
                                                                 <small class="badge badge-warning">Returned</small>
                                                             @endif
                                                         </a>
                                                     @endif
                                                 </div>
                                             </div>
                                         </td>
                                     </tr>
                                @empty
                                     <tr>
                                         <td colspan="9" class="text-center text-danger no_data_style">{{ __('No Invoice Found') }}
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
                        @foreach ($invoices as $data)
                             @php
                                 $return_tbl = App\Models\ReturnTbl::where('invoice_id', $data->id)->first();
                             @endphp

                             {{-- return amount modal  --}}
                             <form action="{{ route('invoice.return.amount', $data->id) }}" method="POST">
                                 @csrf
                                 <x-edit-modal title="{{ __('Payment Amount') }}" sizeClass="modal-md" id="{{ $data->id }}">
                                     <div class="mt-2 col-md-12">
                                         <label class="form-label font-weight-bold">{{ __('Date') }}</label>
                                         <input type="date" class="form-control" value="{{ date('Y-m-d') }}"
                                             name="date">
                                         <div class="error">
                                             {{ (isset($errors) && $errors->has('date')) ? $errors->first('date') : '' }}</div>
                                     </div>
                                     <div class="mt-2 col-md-12">
                                         <label class="form-label font-weight-bold">{{ __('Bank Account *') }}</label>
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
                                         <input type="hidden" name="return_cus_amount" id=""
                                             value="{{ $data->total_paid }}">
                                         <label for="return_paid_amount" class="form-label fw-bold">{{ __('Amount *') }}</label>
                                         <input type="number" class="form-control" min="1" step="any"
                                             required placeholder="{{ __('Enter Amount') }}" name="return_paid_amount"
                                             value="{{ abs($data->total_paid) }}">
                                         <div class="error">
                                             {{ (isset($errors) && $errors->has('return_paid_amount')) ? $errors->first('return_paid_amount') : '' }}
                                         </div>
                                     </div>
                                 </x-edit-modal>
                             </form>

                             {{-- delete modal --}}
                             <form action="{{ route('invoice.destroy', $data->id) }}" method="POST">
                                 @csrf
                                 @method('DELETE')
                                 <x-delete-modal title="{{ __('Invoice') }}" id="{{ $data->id }}" />
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

@endsection
