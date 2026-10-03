@extends('backend.layouts.master')
@section('section-title', __('Purchase'))
@section('page-title', __('Add Purchase'))
@section('action-button')
    <a href="{{ route('purchase.index') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-list"></i>
        {{ __('Purchase List') }}
    </a>
@endsection

@section('content')
    @if (auth()->user()->branch_id == 1)
        @if ($filterBranchId != null)
            <div class="row width-auto">
                <div class="col-lg-12">
                    <div class="card m-b-30 card_style">
                        <div class="card-body">
                            <form class="needs-validation" novalidate id="purchaseForm" action="{{ route('purchase.store') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-row">
                                    @if (auth()->user()->branch_id == 1)
                                        <input type="hidden" name="branch_id" id="branch_id" value="{{ $filterBranchId }}">
                                    @else
                                        <input type="hidden" name="branch_id" id="branch_id"
                                            value="{{ auth()->user()->branch_id }}">
                                    @endif
                                    {{-- Purchase Date --}}
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label font-weight-bold">
                                            {{ __('Date *') }}
                                        </label>
                                        <input type="date" class="form-control" value="{{ date('Y-m-d') }}"
                                            name="date" required>
                                    </div>
                                    {{-- Purchase No --}}
                                    <div class="mb-3 col-md-6">
                                        <label for="validationCustom02" class="form-label font-weight-bold">{{ __('Purchase No *') }}</label>
                                        <input type="text" class="form-control purchase_no" name="purchase_no"
                                            value="" readonly required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    {{-- Note --}}
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label font-weight-bold">
                                            {{ __('Note') }}</label>
                                        <input type="text" class="form-control" placeholder="{{ __('Enter Note') }}" name="note">
                                    </div>
                                    {{-- Supplier --}}
                                    <div class="mb-3 col-md-6">
                                        <div class="row align-items-end">
                                            <div class="col-md-11 col-10 pr-0">
                                                <label class="form-label font-weight-bold mb-1">{{ __('Supplier *') }}</label>
                                                <select class="select2" id="supplier" name="supplier_id" required>
                                                    @foreach ($suppliers as $supplier)
                                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-1 col-2 pl-2">
                                                <a href="#" data-toggle="modal" data-target="#addModal"
                                                    class="btn extra_btn shadow-sm">
                                                    <i class="feather icon-plus"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="form-row">
                                    {{-- Product --}}
                                    <div class="mb-3 col-md-12 text-center">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text barcod_style" id="basic-addon1"><i
                                                        class="fa fa-barcode"></i></span>
                                            </div>
                                            <input type="text" id="product_search" class="form-control"
                                                placeholder="{{ __('Type & Barcode') }}" aria-label="{{ __('Type & Barcode') }}"
                                                onkeydown="return event.keyCode !== 13" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 col-12 table-responsive">
                                        <table class="table table-bordered text-center table-sm">
                                             <thead>
                                                 <tr class="header_bg container-fluid">
                                                     <th class="header_style_left" width="4%">{{ __('#SL') }}</th>
                                                     <th width="20%">{{ __('Product') }}</th>
                                                     @if (env('APP_IMEI') == 'yes')
                                                         <th width="12%">{{ __('IMEI') }}</th>
                                                     @endif
                                                     @if (env('APP_SC') == 'yes')
                                                         <th width="15%">{{ __('Variation') }}</th>
                                                     @else
                                                         <th width="2%"></th>
                                                     @endif
                                                     @if (is_rack_enabled())
                                                         <th width="15%">{{ __('Rack') }}</th>
                                                     @endif
                                                     <th width="12%">{{ __('Rate') }}</th>
                                                     <th width="14%">{{ __('Qty') }}</th>
                                                     @if (env('APP_WARRANTY') == 'yes')
                                                     <th width="10%">{{ __('Warranty') }}</th>
                                                     @endif
                                                     <th width="8%">{{ __('Sub Total') }}</th>
                                                     <th class="header_style_right" width="5%">
                                                         <i class="fa fa-trash"></i>
                                                     </th>
                                                 </tr>
                                             </thead>
                                             <tbody id="table_body">
                                             </tbody>
                                             @php
                                                 $footerColspan = 4 + (env('APP_IMEI') == 'yes' ? 1 : 0) + (env('APP_WARRANTY') == 'yes' ? 1 : 0) + (is_rack_enabled() ? 1 : 0);
                                             @endphp
                                             <tfoot>
                                                 <tr>
                                                     <td colspan="{{ $footerColspan }}" class="text-right fw-bold">{{ __('Grand Total') }}</td>
                                                     <td colspan="1">
                                                         <input type="number" step="any" style="min-width:100px"
                                                             name="estimated_amount" value="0" class="form-control"
                                                             readonly>
                                                     </td>
                                                     <td class="text-right"></td>
                                                 </tr>
                                                 {{-- Discount --}}
                                                 <tr>
                                                     <td colspan="{{ $footerColspan }}" class="text-right fw-bold">{{ __('Discount') }}</td>
                                                     <td colspan="1">
                                                         <input type="text" name="discount_input"
                                                             class="form-control discount_input" placeholder="10 or 10%">

                                                         <input type="hidden" name="discount" class="discount_percent">
                                                         <input type="hidden" name="discount_amount"
                                                             class="discount_amount">
                                                     </td>
                                                     <td></td>
                                                 </tr>
                                                 {{-- VAT --}}
                                                 @if (is_vat_enabled())
                                                 <tr>
                                                     <td colspan="{{ $footerColspan }}" class="text-right fw-bold">{{ __('VAT') }}</td>
                                                     <td colspan="1">
                                                         <input type="text" name="vat_input"
                                                             class="form-control vat_input" placeholder="5 or 5%">

                                                         <input type="hidden" name="vat_percent" class="vat_percent">
                                                         <input type="hidden" name="vat_amount" class="vat_amount">
                                                     </td>
                                                     <td></td>
                                                 </tr>
                                                 @else
                                                 <input type="hidden" name="vat_input" class="vat_input" value="0">
                                                 <input type="hidden" name="vat_percent" class="vat_percent" value="0">
                                                 <input type="hidden" name="vat_amount" class="vat_amount" value="0">
                                                 @endif
                                                 {{-- Total Amount --}}
                                                 <tr>
                                                     <td colspan="{{ $footerColspan }}" class="text-right fw-bold">{{ __('Payable Amount') }}</td>
                                                     <td colspan="1">
                                                         <input type="number" step="any" name="total_amount"
                                                             min="0" value="0"
                                                             class="form-control total_amount" readonly>
                                                     </td>
                                                     <td class="text-right"></td>
                                                 </tr>
                                                 {{-- Payment Method --}}
                                                 <tr>
                                                     <td colspan="{{ $footerColspan - 1 }}" class="text-right fw-bold text-black">{{ __('Bank Account') }}
                                                     </td>
                                                     <td colspan="1">
                                                         <select class="select2" name="bank_id" required>
                                                             @foreach ($bank_accounts as $bank_account)
                                                                 <option value="{{ $bank_account->id }}">
                                                                     {{ $bank_account->bank_name }}</option>
                                                             @endforeach
                                                         </select>
                                                     </td>
                                                     <td class="text-right"></td>
                                                 </tr>
                                                 {{-- Paid Amount --}}
                                                 <tr>
                                                     <td colspan="{{ $footerColspan }}" class="text-right fw-bold text-black">{{ __('Paid Amount') }}</td>
                                                     <td colspan="1">
                                                         <input type="number" step="any" name="paid_amount"
                                                             min="0" value="0"
                                                             class="form-control paid_amount" required>
                                                     </td>
                                                     <td class="text-right"></td>
                                                 </tr>
                                                 {{-- Due Amount in --}}
                                                 <tr class="bg-light-danger due_amount">
                                                     <td colspan="{{ $footerColspan }}" class="text-right fw-bold">{{ __('Due Amount') }}</td>
                                                     <td colspan="1">
                                                         <input type="number" step="any" name="due_amount"
                                                             min="0" value="0" class="form-control due_amount"
                                                             readonly>
                                                     </td>
                                                     <td class="text-right"></td>
                                                 </tr>
                                             </tfoot>
                                        </table>
                                        {{-- Submit & Reset Button --}}
                                        <div class="row submitAndReset" style="display: none;">
                                            <div class="text-center col-md-12">
                                                <button type="reset" class="btn col-sm-4 reset_button"
                                                    style="padding: 5px 20px; background: #f04438;color:white; border-radius:5px">{{ __('Reset') }}</button>
                                                <button class="btn save_btn col-sm-6 submit_button"
                                                    style="padding: 5px 20px; background: #12b76a;color:white; border-radius:5px;margin-right: 5px">{{ __('Submit') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class=" width-auto">
                <div class="row">
                    <div class="col-md-12 text-center mt-5">
                        <h2 class="text-danger text-center">{{ __('Please Select Branch') }}</h2>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="row width-auto">
            <div class="col-lg-12">
                <div class="card m-b-30 card_style">
                    <div class="card-body">
                        <form class="needs-validation" novalidate id="purchaseForm"
                            action="{{ route('purchase.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-row">
                                @if (auth()->user()->branch_id == 1)
                                    <input type="hidden" name="branch_id" id="branch_id"
                                        value="{{ $filterBranchId }}">
                                @else
                                    <input type="hidden" name="branch_id" id="branch_id"
                                        value="{{ auth()->user()->branch_id }}">
                                @endif
                                {{-- Purchase Date --}}
                                <div class="mb-3 col-md-6">
                                    <label class="form-label font-weight-bold">
                                        {{ __('Date *') }}
                                    </label>
                                    <input type="date" class="form-control" value="{{ date('Y-m-d') }}"
                                        name="date" required>
                                </div>
                                {{-- Purchase No --}}
                                <div class="mb-3 col-md-6">
                                    <label for="validationCustom02" class="form-label font-weight-bold">{{ __('Purchase No *') }}</label>
                                    <input type="text" class="form-control purchase_no" id="purchase_no" name="purchase_no"
                                        value="" readonly required>
                                </div>
                            </div>
                            <div class="form-row">
                                {{-- Note --}}
                                <div class="mb-3 col-md-6">
                                    <label class="form-label font-weight-bold">
                                        {{ __('Note') }}</label>
                                    <input type="text" class="form-control" placeholder="{{ __('Enter Note') }}" name="note">
                                </div>
                                {{-- Supplier --}}
                                <div class="mb-3 col-md-6">
                                    <div class="row">
                                        <div class="col-md-11 col-10" style="margin-right: -7px">
                                            <label class="form-label font-weight-bold">{{ __('Supplier *') }}</label>
                                            <select class="select2" id="supplier" name="supplier_id" required>
                                                @foreach ($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-1 col-2 m-0 p-0">
                                            <label class="form-label font-weight-bold"></label>
                                            <div class="extra_btn_wrapper">
                                                {{-- <button type="button" class="btn btn-primary"></button> --}}
                                                <a href="#" data-toggle="modal" data-target="#addModal"
                                                    class="btn extra_btn">
                                                    <i class="feather icon-plus"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="form-row">
                                {{-- Product --}}
                                <div class="mb-3 col-md-12 text-center">
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text barcod_style" id="basic-addon1"><i
                                                    class="fa fa-barcode"></i></span>
                                        </div>
                                        <input type="text" id="product_search" class="form-control"
                                            placeholder="{{ __('Type & Barcode') }}" aria-label="{{ __('Type & Barcode') }}"
                                            onkeydown="return event.keyCode !== 13" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-12 table-responsive">
                                    <table class="table table-bordered text-center table-sm">
                                        <thead>
                                            <tr class="header_bg container-fluid">
                                                <th class="header_style_left" width="5%">{{ __('#SL') }}</th>
                                                <th width="25%">{{ __('Product') }}</th>
                                                @if (env('APP_IMEI') == 'yes')
                                                    <th width="15%">{{ __('IMEI') }}</th>
                                                @endif
                                                @if (env('APP_SC') == 'yes')
                                                    <th width="20%">{{ __('Variation') }}</th>
                                                @else
                                                    <th width="2%"></th>
                                                @endif
                                                @if (is_rack_enabled())
                                                    <th width="15%">{{ __('Rack') }}</th>
                                                @endif
                                                <th width="20%">{{ __('Rate') }}</th>
                                                <th width="15%">{{ __('Qty') }}</th>
                                                @if (env('APP_WARRANTY') == 'yes')
                                                <th width="15%">{{ __('Warranty') }}</th>
                                                @endif
                                                <th width="10%">{{ __('Sub Total') }}</th>
                                                <th class="header_style_right" width="5%">
                                                    <i class="fa fa-trash"></i>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="table_body">

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="{{ $footerColspan }}" class="text-right fw-bold">{{ __('Grand Total') }}</td>
                                                <td colspan="1">
                                                    <input type="number" step="any" style="min-width:100px"
                                                        name="estimated_amount" value="0" class="form-control"
                                                        readonly>
                                                </td>
                                                <td class="text-right"></td>
                                            </tr>
                                            {{-- Discount --}}
                                            <tr>
                                                <td colspan="{{ $footerColspan }}" class="text-right fw-bold">{{ __('Discount') }}</td>
                                                <td colspan="1">
                                                    <input type="text" name="discount_input"
                                                        class="form-control discount_input" placeholder="10 or 10%">

                                                    <input type="hidden" name="discount" class="discount_percent">
                                                    <input type="hidden" name="discount_amount" class="discount_amount">
                                                </td>
                                                <td></td>
                                            </tr>
                                            {{-- VAT --}}
                                            @if (is_vat_enabled())
                                            <tr>
                                                <td colspan="{{ $footerColspan }}" class="text-right fw-bold">{{ __('VAT') }}</td>
                                                <td colspan="1">
                                                    <input type="text" name="vat_input"
                                                        class="form-control vat_input" placeholder="5 or 5%">

                                                    <input type="hidden" name="vat_percent" class="vat_percent">
                                                    <input type="hidden" name="vat_amount" class="vat_amount">
                                                </td>
                                                <td></td>
                                            </tr>
                                            @else
                                            <input type="hidden" name="vat_input" class="vat_input" value="0">
                                            <input type="hidden" name="vat_percent" class="vat_percent" value="0">
                                            <input type="hidden" name="vat_amount" class="vat_amount" value="0">
                                            @endif
                                            {{-- Total Amount --}}
                                            <tr>
                                                <td colspan="{{ $footerColspan }}" class="text-right fw-bold">{{ __('Payable Amount') }}</td>
                                                <td colspan="1">
                                                    <input type="number" step="any" name="total_amount"
                                                        min="0" value="0" class="form-control total_amount"
                                                        readonly>
                                                </td>
                                                <td class="text-right"></td>
                                            </tr>
                                            {{-- Payment Method --}}
                                            <tr>
                                                <td colspan="{{ $footerColspan - 1 }}" class="text-right fw-bold text-black">{{ __('Bank Account') }}</td>
                                                <td colspan="1">
                                                    <select class="select2" name="bank_id" required>
                                                        @foreach ($bank_accounts as $bank_account)
                                                            <option value="{{ $bank_account->id }}">
                                                                {{ $bank_account->bank_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="text-right"></td>
                                            </tr>
                                            {{-- Paid Amount --}}
                                            <tr>
                                                <td colspan="{{ $footerColspan }}" class="text-right fw-bold text-black">{{ __('Paid Amount') }}</td>
                                                <td colspan="1">
                                                    <input type="number" step="any" name="paid_amount"
                                                        min="0" value="0" class="form-control paid_amount"
                                                        required>
                                                </td>
                                                <td class="text-right"></td>
                                            </tr>
                                            {{-- Due Amount in --}}
                                            <tr class="bg-light-danger due_amount">
                                                <td colspan="{{ $footerColspan }}" class="text-right fw-bold">{{ __('Due Amount') }}</td>
                                                <td colspan="1">
                                                    <input type="number" step="any" name="due_amount"
                                                        min="0" value="0" class="form-control due_amount"
                                                        readonly>
                                                </td>
                                                <td class="text-right"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    {{-- Submit & Reset Button --}}
                                    <div class="row submitAndReset" style="display: none;">
                                        <div class="text-center col-md-12">
                                            <button type="reset" class="btn col-sm-4 reset_button"
                                                style="padding: 5px 20px; background: #f04438;color:white; border-radius:25px">{{ __('Reset') }}</button>
                                            <button class="btn save_btn col-sm-6 submit_button"
                                                style="padding: 5px 20px; background: #12b76a;color:white; border-radius:25px;margin-right: 5px">{{ __('Submit') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Add Modal --}}
    <form action="{{ route('supplier.store') }}" method="POST">
        @csrf
        <x-add-modal title="{{ __('Add Supplier') }}" sizeClass="modal-lg">
            @if (auth()->user()->branch_id == 1)
                <x-select label="{{ __('Branch *') }}" name="branch_id" md="6">
                    @foreach ($allBranch as $data)
                        <option value="{{ $data->id }}">{{ $data->name }}</option>
                    @endforeach
                </x-select>
            @endif
            <x-input label="{{ __('Supplier Name *') }}" type="text" name="name" placeholder="{{ __('Enter Supplier Name') }}" required
                md="6" />
            <x-input label="{{ __('Email') }}" type="email" name="email" placeholder="{{ __('Enter Email') }}" md="6" />
            <x-input label="{{ __('Phone *') }}" type="text" name="phone" placeholder="{{ __('Enter Phone') }}" required md="6" />
            <x-input label="{{ __('Address') }}" type="text" name="address" placeholder="{{ __('Enter Address') }}" md="6" />
            <x-input label="{{ __('Advance Amount') }}" type="text" name="advance_amount" value="0" md="6" />
            <x-input label="{{ __('Due Amount') }}" type="text" name="due_amount" value="0" md="6" />
        </x-add-modal>
    </form>

    <!-- IMEI Input Modal -->
    <div class="modal fade" id="purchaseImeiModal" tabindex="-1" role="dialog" aria-labelledby="purchaseImeiModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content card_style">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="purchaseImeiModalLabel">{{ __('Enter IMEI(s)') }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="modalImeiTextarea" class="font-weight-bold">{{ __('Enter IMEIs (1 item/unit per line. For Dual-SIM, enter 2 IMEIs on 1 line separated by comma e.g. 12, 11):') }}</label>
                        <textarea id="modalImeiTextarea" class="form-control" rows="8" placeholder="12, 11&#10;13, 14&#10;15"></textarea>
                        <div id="modalImeiError" class="alert alert-danger d-none mt-2" style="padding: 8px 12px; font-size: 13px;"></div>
                    </div>
                    <div class="text-right">
                        <small class="text-muted"><span id="modalImeiCount">0</span> {{ __('Unit(s) entered') }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn cancel_btn" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" id="btnConfirmPurchaseImei" class="btn save_btn">{{ __('Confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        // $('body').addClass('toggle-menu');
        $(document).ready(function() {
            function fetchPurchaseNo(branchId) {
                if (branchId) {
                    $.ajax({
                        url: '{{ route('get.purchase.no') }}',
                        type: 'GET',
                        data: {
                            branch_id: branchId
                        },
                        success: function(response) {
                            console.log(response);
                            $('.purchase_no').val(response.purchase_no);
                        },
                        error: function() {
                            $('#purchase_no').val('Error loading');
                        }
                    });
                }
            }

            // Get the initial value on page load and fetch
            var initialBranchId = $('#branch_id').val();
            fetchPurchaseNo(initialBranchId);

            // Listen to future changes (in case you make it a dropdown)
            $('#branch_id').on('change', function() {
                var branchId = $(this).val();
                fetchPurchaseNo(branchId);
            });
        });
    </script>
    <script>
        // Select the input field when the page loads
        window.onload = function() {
            var inputField = document.getElementById('product_search');
            inputField.select();
        };
    </script>

    <script>
        $(document).ready(function() {
            let count = 0;
            let db_pid_array = [];
            var allAvailableRacks = @json($racks ?? []);

            function buildPurchaseRackOptions(selectedIds = []) {
                let html = '';
                if (allAvailableRacks && allAvailableRacks.length > 0) {
                    allAvailableRacks.forEach(function(r) {
                        let isSelected = (selectedIds.map(String).indexOf(String(r.id)) !== -1) ? 'selected' : '';
                        html += `<option value="${r.id}" ${isSelected}>${r.name}</option>`;
                    });
                }
                return html;
            }

            function toggleSubmitAndResetButtons() {
                if ($("#table_body tr").length > 0) {
                    $(".submitAndReset").show();
                } else {
                    $(".submitAndReset").hide();
                }
            }

            // Autocomplete for product search
            $("#product_search").autocomplete({

                source: function(req, res) {
                    let barcode = req.term.trim();

                    let url = "{{ route('purchase.search') }}";
                    let branchId = $('#branch_id').val();
                    $.get(url, {
                        req: barcode,
                        branch_id: branchId
                    }, function(data) {
                        res($.map(data, function(item) {
                            return {
                                id: item.id,
                                label: item.name + " (" + item.selling_price +
                                    " {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}) - " +
                                    item.barcode,
                                value: item.name + " " + item.barcode,
                                price: item.selling_price
                            };
                        }));
                    });
                },
                select: function(event, ui) {
                    if ($("#supplier").val() === "") {
                        iziToast.warning({
                            title: "{{ __('Please Select Supplier First!') }}",
                            position: "topRight",
                        });
                        return false;
                    }

                    if (db_pid_array.some((id) => String(id) === String(ui.item.id))) {
                        iziToast.warning({
                            title: "{{ __('Product already added.') }}",
                            position: "topRight",
                        });
                        return false;
                    }

                    $(this).val(ui.item.value);
                    let url = "{{ route('sc-search-product-id', 'my_id') }}".replace(
                        "my_id",
                        ui.item.id
                    );
                    $.get(url, (data) => {
                        let isImei = data.product.imei == 1 || data.product.imei == '1';
                        let readonly = isImei ? 'readonly' : '';
                        let disabled = isImei ? 'disabled' : '';

                        // Iterate over variations
                        if (data.variations.length > 0) {
                            data.variations.forEach((variation) => {
                                count++;

                                // Create a unique index for each variation
                                let index = `${data.product.id}_${variation.id}`;

                                let quantity_data = '';
                                if (data.product.unit.related_unit == null) {
                                    quantity_data = `
                                        <input type="text" class="has_sub_unit" hidden value="false">
                                        <label class="ml-4 mr-2" style="padding-top: 8px;">${data.product.unit.name}:</label>
                                        <div class="input-group" style="width: 180px;">
                                            <button class="btn btn-outline-secondary btn-decrease" type="button" ${disabled}>-</button>
                                            <input type="number" 
                                                class="form-control main_qty" 
                                                value="" 
                                                min="1"
                                                name="new_main_qty[${index}]"  
                                                data-related="${data.product.unit.related_value}" 
                                                ${readonly}
                                                onkeydown="return event.keyCode !== 190">
                                                <input type="hidden" value="0" class="sub_qty" name="new_sub_qty[${data.product.id}]">
                                            <button class="btn btn-outline-secondary btn-increase" type="button" ${disabled}>+</button>
                                        </div>`;
                                } else {
                                    quantity_data =
                                        `
                                        <input type="text" class="has_sub_unit" hidden value="true">
                                        <input type="text" class="conversion" hidden value="${data.product.unit.related_value}">
                                        <label class="mr-2 ml-4" style="padding-top: 8px;">${data.product.unit.name}:</label>
                                        <div class="input-group" style="width: 180px;">
                                            <button class="btn btn-outline-secondary btn-decrease" type="button" ${disabled}>-</button>
                                            <input type="text" 
                                                class="form-control main_qty" 
                                                value="" 
                                                min="1"
                                                name="new_main_qty[${index}]"  
                                                data-related="${data.product.unit.related_value}" 
                                                ${readonly}
                                                onkeydown="return event.keyCode !== 190">
                                            <button class="btn btn-outline-secondary btn-increase" type="button" ${disabled}>+</button>
                                        </div>
                                        <label class="mr-2" style="padding-top: 8px;">${data.product.unit.related_unit.name}:</label>
                                        <input type="number" value="0" class="form-control sub_qty" style="width: 80px; flex: none;" name="new_sub_qty[${index}]" data-related="${data.product.unit.related_value}"  onkeydown="return event.keyCode !== 190" min="0">`;
                                }

                                let row = `
                                        <tr>
                                            <td>${count}</td>
                                            <td>
                                                ${data.product.name} - ${data.product.barcode}
                                                <input type="hidden" value="${data.product.id}" name="new_product[${index}]" class="product">
                                            </td>
                                            @if (env('APP_IMEI') == 'yes')
                                            <td>
                                                ${data.product.imei == 1 || data.product.imei == '1' ? `
                                                    <button type="button" class="btn btn-sm btn-info btn-enter-imei" data-id="${index}">
                                                        <i class="fa fa-barcode"></i> Add IMEI (<span class="imei-count">0</span>)
                                                    </button>
                                                    <textarea name="new_imei[${index}]" class="imei-hidden-input d-none"></textarea>
                                                ` : ''}
                                            </td>
                                            @endif
                                            @if (env('APP_SC') == 'yes')
                                            <td>
                                                ${variation.size}-${variation.color}
                                                <input type="hidden" value="${variation.id}" name="variation[${index}]">
                                            </td>
                                            @else 
                                            <td></td>
                                            @endif
                                            @if (is_rack_enabled())
                                            <td>
                                                <select name="new_rack_ids[${index}][]" class="form-control select2-rack" multiple data-placeholder="{{ __('Select Rack') }}" style="width: 100%;">
                                                    ${buildPurchaseRackOptions(data.rack_ids || [])}
                                                </select>
                                            </td>
                                            @endif
                                            <td>
                                                <div class="form-row text-center" style="width:100px">
                                                    <input type="number" value="${data.product.purchase_price}" 
                                                        class="form-control rate" name="new_rate[${index}]" required>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center">
                                                    ${quantity_data}
                                                </div>
                                            </td>
                                            @if (env('APP_WARRANTY') == 'yes')
                                            @php
                                                // Dynamic row will evaluate JS w_text
                                            @endphp
                                            <td class="text-center align-middle">
                                                <span class="badge badge-info px-2 py-1 font-weight-bold" style="font-size: 13px;">
                                                    ${(data.product.warranty_value || data.warranty_value) ? ((data.product.warranty_value || data.warranty_value) + ' ' + (data.product.warranty_unit || data.warranty_unit || 'Month')) : '<span class="text-muted">-</span>'}
                                                </span>
                                                <input type="hidden" name="new_warranty_value[${index}]" value="${data.product.warranty_value || data.warranty_value || ''}">
                                                <input type="hidden" name="new_warranty_unit[${index}]" value="${data.product.warranty_unit || data.warranty_unit || ''}">
                                            </td>
                                            @endif
                                            <td>
                                                <strong><span class="sub_total">0</span></strong>
                                                <input type="hidden" name="new_subtotal_input[${index}]" class="subtotal_input" value="0">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm remove">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>`;
                                $("#table_body").append(row);
                            });
                        } else {
                            // Handle product without variations
                            count++;
                            if (data.product.unit.related_unit == null) {
                                quantity_data =
                                    `<input type="text" class="has_sub_unit" hidden value="false">
                                        <label class="ml-4 mr-2" style="padding-top: 8px;">${data.product.unit.name}:</label>
                                        <div class="input-group" style="width: 180px;">
                                            <button class="btn btn-outline-secondary btn-decrease" type="button" ${disabled}>-</button>
                                            <input type="number" value="" class="form-control col main_qty" name="new_main_qty[${data.product.id}]" min="1"  ${readonly} onkeydown="return event.keyCode !== 190" min="1">
                                            <input type="hidden" value="0" class="sub_qty" name="new_sub_qty[${data.product.id}]">
                                            <button class="btn btn-outline-secondary btn-increase" type="button" ${disabled}>+</button>
                                        </div>
                                        `;
                            } else {
                                quantity_data =
                                    `<input type="text" class="has_sub_unit" hidden value="true">
                                        <input type="text" class="conversion" hidden value="${data.product.unit.related_value}">
                                        <label class="mr-2 ml-4" style="padding-top: 8px;">${data.product.unit.name}:</label>
                                        <div class="input-group" style="width: 180px;">
                                                    <button class="btn btn-outline-secondary btn-decrease" type="button" ${disabled}>-</button>
                                                    <input type="number" 
                                                            class="form-control main_qty" 
                                                             value="" 
                                                             min="1"
                                                             name="new_main_qty[${data.product.id}]"  data-related="${data.product.unit.related_value}" 
                                                             ${readonly}
                                                             onkeydown="return event.keyCode !== 190">
                                                     <button class="btn btn-outline-secondary btn-increase" type="button" ${disabled}>+</button>
                                                 </div>
                                         <label class="mr-2 ml-4" style="padding-top: 8px;">${data.product.unit.related_unit.name}:</label>
                                         <input type="number" value="0" class="form-control sub_qty" style="width: 80px; flex: none;" name="new_sub_qty[${data.product.id}]"  onkeydown="return event.keyCode !== 190" min="0">`;
                            }
                            let row = `
                                <tr>
                                    <td>${count}</td>
                                    <td>
                                        ${data.product.name} - ${data.product.barcode}
                                        <input type="hidden" value="${data.product.id}" name="new_product[${data.product.id}]" class="product">
                                    </td>
                                    @if (env('APP_IMEI') == 'yes')
                                    <td>
                                        ${data.product.imei == 1 || data.product.imei == '1' ? `
                                            <button type="button" class="btn btn-sm btn-info btn-enter-imei" data-id="${data.product.id}">
                                                <i class="fa fa-barcode"></i> Add IMEI (<span class="imei-count">0</span>)
                                            </button>
                                            <textarea name="new_imei[${data.product.id}]" class="imei-hidden-input d-none"></textarea>
                                        ` : ''}
                                    </td>
                                    @endif
                                    @if (env('APP_SC') == 'yes')
                                    <td>{{ __('No Variation') }}</td>
                                    @else
                                    <td></td>
                                    @endif
                                    @if (is_rack_enabled())
                                    <td>
                                        <select name="new_rack_ids[${data.product.id}][]" class="form-control select2-rack" multiple data-placeholder="{{ __('Select Rack') }}" style="width: 100%;">
                                            ${buildPurchaseRackOptions(data.rack_ids || [])}
                                        </select>
                                    </td>
                                    @endif
                                    <td>
                                        <div class="form-row text-center" style="width:100px">
                                        <input type="number" value="${data.product.purchase_price}" 
                                            class="form-control rate" name="new_rate[${data.product.id}]" required>
                                        </div>
                                    </td>
                                    <td class="" >
                                        <div class="d-flex align-items-center justify-content-center">
                                            ${quantity_data}
                                        </div>
                                    </td>
                                    @if (env('APP_WARRANTY') == 'yes')
                                    <td class="text-center align-middle">
                                        <span class="badge badge-info px-2 py-1 font-weight-bold" style="font-size: 13px;">
                                            ${(data.product.warranty_value || data.warranty_value) ? ((data.product.warranty_value || data.warranty_value) + ' ' + (data.product.warranty_unit || data.warranty_unit || 'Month')) : '<span class="text-muted">-</span>'}
                                        </span>
                                        <input type="hidden" name="new_warranty_value[${data.product.id}]" value="${data.product.warranty_value || data.warranty_value || ''}">
                                        <input type="hidden" name="new_warranty_unit[${data.product.id}]" value="${data.product.warranty_unit || data.warranty_unit || ''}">
                                    </td>
                                    @endif
                                    <td>
                                        <strong><span class="sub_total">0</span></strong>
                                        <input type="hidden" name="new_subtotal_input[${data.product.id}]" class="subtotal_input" value="0">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm remove">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>`;
                            $("#table_body").append(row);
                        }

                        $('.select2-rack').select2({ placeholder: "{{ __('Select Rack') }}", width: '100%' });
                        db_pid_array.push(String(ui.item.id));
                        toggleSubmitAndResetButtons(); // Show buttons after adding a product
                    });

                    $(this).val("");
                    return false;
                },
                response: function(event, ui) {
                    if (ui.content.length === 1) {
                        ui.item = ui.content[0];
                        $(this).data("ui-autocomplete")._trigger("select", "autocompleteselect", ui);
                        $(this).autocomplete("close");
                    }
                },
                minLength: 0,
            });

            // Prevent decimals in quantity fields
            $(document).on('keydown', '.main_qty, .sub_qty', function(e) {
                if (e.key === '.' || e.key === ',' || e.key === 'e' || e.key === 'E' || e.key === '+' || e.key === '-') {
                    e.preventDefault();
                }
            });

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-increase')) {
                    let input = e.target.closest('.input-group').querySelector('.main_qty');
                    if (input && !input.hasAttribute('readonly')) {
                        input.value = parseInt(input.value || 0) + 1;
                        $(input).trigger('input');
                    }
                }

                if (e.target.classList.contains('btn-decrease')) {
                    let input = e.target.closest('.input-group').querySelector('.main_qty');
                    if (input && !input.hasAttribute('readonly')) {
                        if (parseInt(input.value || 0) > 1) {
                            input.value = parseInt(input.value) - 1;
                            $(input).trigger('input');
                        }
                    }
                }
            });

            // Remove row from the table
            $(document).on("click", ".remove", function() {
                let row = $(this).closest("tr");
                let productId = row.find(".product").val();
                row.remove();

                // Remove product ID from array if no other rows exist for it
                if ($(`.product[value="${productId}"]`).length === 0) {
                    db_pid_array = db_pid_array.filter((id) => String(id) !== String(productId));
                }

                toggleSubmitAndResetButtons(); // Hide buttons if table is empty
                updateGrandTotal(); // Recalculate grand total after removing a row
            });

            // Update subtotal and Grand Total on quantity or rate change
            $(document).on("input", ".main_qty, .rate", function() {
                let row = $(this).closest("tr");
                let qty = parseFloat(row.find(".main_qty").val()) || 0;
                let rate = parseFloat(row.find(".rate").val()) || 0;
                let subtotal = qty * rate;

                row.find(".sub_total").text(subtotal.toFixed(2) +
                    " {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}");
                row.find(".subtotal_input").val(subtotal.toFixed(2));

                updateGrandTotal();
            });

            $(document).on("input", ".discount_input", function() {

                let value = $(this).val().trim();
                let estimated = parseFloat($("input[name='estimated_amount']").val()) || 0;

                let amount = 0;

                if (value.includes("%")) {

                    let percent = parseFloat(value.replace("%", "")) || 0;
                    amount = (estimated * percent) / 100;

                } else {

                    amount = parseFloat(value) || 0;

                }

                $(".discount_percent").val(value);
                $(".discount_amount").val(amount.toFixed(2));

                updatePayableAmount();
            });

            $(document).on("input", ".vat_input", function() {

                let value = $(this).val().trim();
                let estimated = parseFloat($("input[name='estimated_amount']").val()) || 0;

                let amount = 0;

                if (value.includes("%")) {

                    let percent = parseFloat(value.replace("%", "")) || 0;
                    amount = (estimated * percent) / 100;

                } else {

                    amount = parseFloat(value) || 0;

                }

                $(".vat_percent").val(value);
                $(".vat_amount").val(amount.toFixed(2));

                updatePayableAmount();
            });

            $(document).on("input", ".sub_qty", function() {
                let row = $(this).closest("tr");
                let mainQty = parseFloat(row.find(".main_qty").val()) || 0;
                let subQty = parseFloat($(this).val()) || 0;
                let conversion = parseFloat(row.find(".conversion").val()) || 1;
                let maxSubQty = conversion - 1; // max allowed sub unit = mainQty * conversion
                if (subQty > maxSubQty) {
                    $(this).val(maxSubQty);
                    iziToast.warning({
                        title: "{{ __('Sub unit exceeds maximum allowed based on quantity!') }}",
                        position: "topRight",
                    });
                }

                // Recalculate subtotal
                let rate = parseFloat(row.find(".rate").val()) || 0;
                let totalQty = mainQty + subQty / conversion;
                let subtotal = totalQty * rate;

                row.find(".sub_total").text(subtotal.toFixed(2) +
                    " {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}");
                row.find(".subtotal_input").val(subtotal.toFixed(2));

                updateGrandTotal();
            });

            $(document).on("input", " .rate", function() {
                let row = $(this).closest("tr");
                let mainQty = parseFloat(row.find(".main_qty").val()) || 0;
                let subQty = parseFloat(row.find(".sub_qty").val()) || 0;
                let rate = parseFloat(row.find(".rate").val()) || 0;
                let hasSubUnit = row.find(".has_sub_unit").val() === "true";
                let conversion = parseFloat(row.find(".conversion").val()) || 1;

                let totalQty = mainQty;
                if (hasSubUnit) {
                    totalQty += subQty / conversion; // Convert sub_qty to main unit equivalent
                }

                let subtotal = totalQty * rate;

                row.find(".sub_total").text(subtotal.toFixed(2) +
                    " {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}");
                row.find(".subtotal_input").val(subtotal.toFixed(2));

                updateGrandTotal();
            });

            $(document).on("input", ".main_qty", function() {
                let row = $(this).closest("tr");
                let mainQty = parseFloat($(this).val()) || 0;
                let subQtyInput = row.find(".sub_qty");
                let subQty = parseFloat(subQtyInput.val()) || 0;
                let conversion = parseFloat(row.find(".conversion").val()) || 1;

                let maxSubQty = mainQty * conversion;

                if (subQty > maxSubQty) {
                    subQtyInput.val(maxSubQty);
                    iziToast.warning({
                        title: "Sub unit adjusted to max allowed based on quantity!",
                        position: "topRight",
                    });
                }

                // Recalculate subtotal
                let rate = parseFloat(row.find(".rate").val()) || 0;
                let totalQty = mainQty + (parseFloat(subQtyInput.val()) || 0) / conversion;
                let subtotal = totalQty * rate;

                row.find(".sub_total").text(subtotal.toFixed(2) +
                    " {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}");
                row.find(".subtotal_input").val(subtotal.toFixed(2));

                updateGrandTotal();
            });

            // Handle IMEI Modal Opening and Saving
            let currentImeiTarget = null;
            let debounceTimer = null;

            function parseImeiInput(text) {
                let lines = (text || '').split(/\r?\n/).map(l => l.trim()).filter(l => l !== '');
                let tokens = [];
                for (let line of lines) {
                    let items = line.split(/[\s,\/]+/).map(i => i.trim()).filter(i => i !== '');
                    tokens.push(...items);
                }
                return { lines: lines, tokens: tokens };
            }

            function validateImeis() {
                if (!currentImeiTarget) return;

                let text = $('#modalImeiTextarea').val();
                let data = parseImeiInput(text);
                let lines = data.lines;
                let tokens = data.tokens;
                
                // Reset errors and enable button by default
                $('#modalImeiError').addClass('d-none').html('');
                $('#btnConfirmPurchaseImei').prop('disabled', false);

                // 1. Check for duplicate individual IMEIs within the current textarea list itself
                let uniqueTokens = new Set();
                let internalDuplicates = new Set();
                for (let token of tokens) {
                    if (uniqueTokens.has(token)) {
                        internalDuplicates.add(token);
                    }
                    uniqueTokens.add(token);
                }

                if (internalDuplicates.size > 0) {
                    let dupList = Array.from(internalDuplicates).join(', ');
                    showError("Duplicate IMEI(s) in list: " + dupList);
                    return;
                }

                // 2. Check for duplicates across other rows on the same screen (client-side cross-row duplicates)
                let otherTokens = new Set();
                let crossRowDuplicates = new Set();
                $('.imei-hidden-input').not(currentImeiTarget).each(function() {
                    let val = $(this).val() || '';
                    let rData = parseImeiInput(val);
                    for (let rToken of rData.tokens) {
                        otherTokens.add(rToken);
                    }
                });

                for (let token of tokens) {
                    if (otherTokens.has(token)) {
                        crossRowDuplicates.add(token);
                    }
                }

                if (crossRowDuplicates.size > 0) {
                    let dupList = Array.from(crossRowDuplicates).join(', ');
                    showError("IMEIs already entered in other rows: " + dupList);
                    return;
                }

                // 3. Database check (AJAX) - check all tokens against SerialNumber table
                if (tokens.length > 0) {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(function() {
                        let excludePurchaseId = $('input[name="id"]').val() || null;
                        
                        $.ajax({
                            url: "{{ route('check-imei-duplicates') }}",
                            method: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                imeis: tokens,
                                exclude_purchase_id: excludePurchaseId
                            },
                            success: function(response) {
                                if (response.duplicates && response.duplicates.length > 0) {
                                    let dbDups = response.duplicates.join(', ');
                                    showError("IMEIs currently active in stock: " + dbDups);
                                }
                            }
                        });
                    }, 300); // 300ms debounce
                }
            }

            function showError(msg) {
                $('#modalImeiError').removeClass('d-none').text(msg);
                $('#btnConfirmPurchaseImei').prop('disabled', true);
            }

            $(document).on('click', '.btn-enter-imei', function() {
                let btn = $(this);
                currentImeiTarget = btn.siblings('.imei-hidden-input');
                let existingValue = currentImeiTarget.val() || '';
                
                // Set modal textarea value
                $('#modalImeiTextarea').val(existingValue);
                
                // Update count: number of lines = quantity
                let data = parseImeiInput(existingValue);
                $('#modalImeiCount').text(data.lines.length);

                // Reset error state
                $('#modalImeiError').addClass('d-none').html('');
                $('#btnConfirmPurchaseImei').prop('disabled', false);
                
                $('#purchaseImeiModal').modal('show');
                validateImeis();
            });

            $(document).on('input', '#modalImeiTextarea', function() {
                let data = parseImeiInput($(this).val());
                $('#modalImeiCount').text(data.lines.length);
                validateImeis();
            });

            $('#btnConfirmPurchaseImei').on('click', function() {
                if (!currentImeiTarget) return;
                
                let text = $('#modalImeiTextarea').val();
                let data = parseImeiInput(text);
                let count = data.lines.length; // Quantity = Number of lines (1 line = 1 unit)
                
                // Set values back to row
                currentImeiTarget.val(text);
                
                let row = currentImeiTarget.closest('tr');
                row.find('.imei-count').text(count);
                
                let mainQtyInput = row.find('.main_qty');
                if (count > 0) {
                    mainQtyInput.val(count);
                } else {
                    mainQtyInput.val(1);
                }
                
                // Trigger events to update totals
                mainQtyInput.trigger('input');
                mainQtyInput.trigger('change');
                
                $('#purchaseImeiModal').modal('hide');
            });

            // Function to calculate and update the Grand Total, Payable Amount, and Due Amount
            function updateGrandTotal() {
                let grandTotal = 0;
                $(".subtotal_input").each(function() {
                    grandTotal += parseFloat($(this).val()) || 0;
                });

                $("input[name='estimated_amount']").val(grandTotal.toFixed(2));
                updatePayableAmount(); // Update Payable Amount when Grand Total changes
            }

            // Function to calculate and update the Payable Amount
            function updatePayableAmount() {

                let estimatedAmount = parseFloat($("input[name='estimated_amount']").val()) || 0;
                let discountAmount = parseFloat($(".discount_amount").val()) || 0;
                let vatAmount = parseFloat($(".vat_amount").val()) || 0;

                let payableAmount = estimatedAmount - discountAmount + vatAmount;

                if (payableAmount < 0) payableAmount = 0;

                $("input[name='total_amount']").val(payableAmount.toFixed(2));
                $("input[name='paid_amount']").val(payableAmount.toFixed(2));

                updateDueAmount();
            }

            // Function to calculate and update the Due Amount
            function updateDueAmount() {
                let payableAmount = parseFloat($("input[name='total_amount']").val()) || 0;
                let paidAmount = parseFloat($("input[name='paid_amount']").val()) || 0;

                let dueAmount = payableAmount - paidAmount;
                if (dueAmount < 0) dueAmount = 0; // Prevent negative due amounts

                $("input[name='due_amount']").val(dueAmount.toFixed(2));
            }

            // Update Payable Amount on Discount Amount change
            $(document).on("input", "input[name='discount_amount']", function() {
                updatePayableAmount();
            });

            // Update Due Amount on Paid Amount change
            $(document).on("input", "input[name='paid_amount']", function() {
                let payableAmount = parseFloat($("input[name='total_amount']").val()) || 0;
                let paidAmount = parseFloat($(this).val()) || 0;

                if (paidAmount > payableAmount) {
                    alert('Paid amount cannot be greater than Total Amount!');
                    $(this).val(payableAmount.toFixed(2));
                }

                updateDueAmount();
            });

            // Handle Reset button click
            $(".reset_button").on("click", function() {
                $("#table_body").empty();
                db_pid_array = [];
                $("input[name='estimated_amount']").val("0");
                $("input[name='discount_amount']").val("0");
                $("input[name='total_amount']").val("0");
                $("input[name='paid_amount']").val("0");
                $("input[name='due_amount']").val("0");
                toggleSubmitAndResetButtons();
            });

            // Prevent form submit if no product added
            $(document).on('submit', '#purchaseForm', function(e) {
                if ($("#table_body tr").length === 0) {
                    e.preventDefault();
                    iziToast.warning({
                        title: "{{ __('Error') }}",
                        message: "{{ __('Please select at least one product.') }}",
                        position: "topRight"
                    });
                    return false;
                }
            });
        });
    </script>
@endpush
