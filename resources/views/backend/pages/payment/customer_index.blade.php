@extends('backend.layouts.master')
@section('section-title', __('Customer Payment'))
@section('page-title', __('Pay Customer'))
@if (check_permission('payment.pay-customer-store'))
    @section('action-button')
    <a href="#" id="modal_btn" class="btn add_list_btn">
        <i class="mr-2 feather icon-plus"></i>
        {{ __('Pay Customer') }}
    </a>
    @endsection
@endif
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-body card_style mb-2" style="margin-top: -5px" id="h-hide">
                <form action="{{ route('payment.pay-customer')}}">
                    <div class="form-row">
                        <div class="form-group col-md-3">
                            <label class="font-weight-bold">{{ __('Select Customer') }}</label>
                            <select class="form-control select2" name="customer_id">
                                <option value="">{{ __('Select Customer') }}</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ isset($customer_id) && $customer_id == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label class="font-weight-bold">{{ __('Start Date') }}</label>
                            <input type="date" name="start_date" class="form-control" style="height: 38px !important;" value="{{ (isset($sdate))?date('Y-m-d', strtotime($sdate)):''; }}">
                        </div>
                        <div class="form-group col-md-2">
                            <label class="font-weight-bold">{{ __('End Date') }}</label>
                            <input type="date" name="end_date" class="form-control" style="height: 38px !important;" value="{{ (isset($edate))?date('Y-m-d', strtotime($edate)):''; }}">
                        </div>
                        <div class="form-group col-md-5">
                            <label>&nbsp;</label>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <button class="btn add_list_btn" style="padding-top: 6px !important; padding-bottom: 6px !important;" type="submit">
                                        <i class="fa fa-sliders mr-1"></i> {{ __('Filter') }}
                                    </button>
                                    <a href="{{ route('payment.pay-customer') }}" class="btn cancel_btn ml-1" style="padding-top: 6px !important; padding-bottom: 6px !important;">{{ __('Reset') }}</a>
                                </div>
                                <div>
                                    <a href="" class="btn add_list_btn" style="padding-top: 6px !important; padding-bottom: 6px !important;" onclick="window.print()">
                                        <i class="feather icon-printer mr-1"></i> {{ __('Print') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable-buttons" class="table table-striped">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Wallet Type') }}</th>
                                    <th>{{ __('Pay Type') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Discount Amount') }}</th>
                                    <th class="header_style_right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payment as $data)
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>{{ date('d-m-Y',strtotime($data->date)) }}</td>
                                        <td>{{ $data->wallet_type }}</td>
                                        <td>{{ $data->pay_type }} ({{ $data->transaction->customer->name }})</td>
                                        <td>{{ $data->amount }}</td>
                                        <td>
                                            @if($data->discount_amount == null )
                                                0.00
                                            @endif
                                            @if($data->discount_amount != null)
                                            {{ $data->discount_amount }}
                                            @endif
                                        </td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                                                    {{-- delete --}}
                                                    @if (check_permission('payment.customer-destroy'))
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#deleteModal-{{ $data->id }}"
                                                            class="dropdown-item text-danger">
                                                            <i class="feather icon-trash"></i> {{ __('Delete') }}
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- delete modal --}}
                                    <form action="{{ route('payment.customer-destroy', $data->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-delete-modal title="{{ __('Customer Payment') }}" id="{{ $data->id }}" />
                                    </form>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center no_data_style text-danger">{{ __('No Data Available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- <div class="pagination justify-content-center">
                        {{ $payment->links() }}
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
    @include('backend.pages.payment.customer_model')
@endsection

@push('js')


<script>
    $(document).ready(function() {
        function loadCustomers(branchId) {
            $.ajax({
                url: "{{ route('get.customers.by.branch') }}",
                type: "GET",
                data: { branch_id: branchId },
                success: function(res) {
                    let options = `<option value="">{{ __('Select Customer') }}</option>`;
                    res.forEach(function(customers) {
                        options += `<option value="${customers.id}">${customers.name}</option>`;
                    });
                    $('#customer_id').html(options).trigger('change');
                }
            });
        }

        // Initial load for non-admin user
        @if (auth()->user()->branch_id != 1)
            loadCustomers("{{ auth()->user()->branch_id }}");
        @endif

        // If branch selector exists (admin)
        $('#branch_id').on('change', function() {
            let branchId = $(this).val();
            if (branchId) {
                loadCustomers(branchId);
            }
        });
    });
</script>





    <script type="text/javascript">
        $("#modal_btn").on("click", function () {
            //show payment_modal
            $("#modal").modal("show");
        });

        $('#modal').on('shown.bs.modal', function () {
            $('#modal .select2').select2({
                dropdownParent: $('#modal'),
                width: '100%'
            });
        });

        $(document).on('change', '.customer_id', function () {
            let customer_id = $(this).val();
            if (!customer_id) {
                $("#details").hide(500);
                return;
            }
            let url = "{{ route('customer-account-balance', 'my_id') }}".replace('my_id', customer_id);
            $.get(url, data => {
                $("#details").show(500);
                $("#account_name").text(data.customer_name);
                $("#due_invoice").text(data.due_invoice);
                $("#total_invoice_due").text(data.invoice_due);
                $("#wallet_balance").text(data.walletBalance);
                $("#id_hint").html('*** বিক্রয় বাবদ পাওনা আছে '+Math.abs(data.invoice_due)+' {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }} ***');

                if(data.walletBalance>=0){
                        $("#wb_hint").html('**** কাস্টমারের কাছে পাওনা আছেঃ '+Math.abs(data.walletBalance)+'{{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }} ****');
                }else{
                        $("#wb_hint").html('**** কাস্টমারের ওয়ালেটে জমা আছেঃ '+Math.abs(data.walletBalance)+'{{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }} ****');
                }

                //for input
                $(".invoice_due").val(data.invoice_due);
                $(".wallet_balance").val(data.walletBalance);
            });
        });

        $("#customer_id").change(function(){
            if($(this).val() == '') {
                $("#details").hide(500);
            }
        });

        $("#details").hide();
    </script>

    {{-- Payment Options --}}
    <script type="text/javascript">
        $(document).on('change', '#wallet_type', function(){
          // alert('ok');
          var wallet_type = $(this).val();
          let html = '<option value="">{{ __('Select Pay Type') }}</option>';
          if(wallet_type == 'Due Adjust'){
            let data = ['Money Received'];
            $.each(data,function(key,v){
                html +='<option value="'+v+'">'+v+'</option>';
            });
            $('#pay_type').html(html);
            $('#pay_type').trigger('change');
          } else if(wallet_type == 'Balance Adjust'){
            let data = ['Money Received', 'Money Payment'];
            $.each(data,function(key,v){
                html +='<option value="'+v+'">'+v+'</option>';
            });
            $('#pay_type').html(html);
            $('#pay_type').trigger('change');
          } else {
            $('#pay_type').html(html);
            $('#pay_type').trigger('change');
          }
        });
      </script>
@endpush
