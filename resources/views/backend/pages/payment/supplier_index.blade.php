@extends('backend.layouts.master')
@section('section-title', __('Supplier Payment'))
@section('page-title', __('Pay Supplier'))
@if (check_permission('payment.pay-supplier-store'))
    @section('action-button')
    <a href="#" id="modal_btn" class="btn add_list_btn">
        <i class="mr-2 feather icon-plus"></i>
        {{ __('Pay Supplier') }}
    </a>
    @endsection
@endif
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-body mb-2 card_style" style="margin-top: -5px" id="h-hide">
                <form action="{{ route('payment.pay-supplier')}}">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <select class="select2" name="supplier_id">
                                <option selected value="">{{ __('Select Supplier') }}</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <input type="date" name="start_date" class="form-control" value="{{ (isset($sdate))?date('Y-m-d', strtotime($sdate)):''; }}">
                        </div>
                        <div class="form-group col-md-4">
                            <input type="date" name="end_date" class="form-control" value="{{ (isset($edate))?date('Y-m-d', strtotime($edate)):''; }}">
                        </div>
                    </div>
                    <div class="form-row mt-2">
                        <div class="form-group col-12">
                            <button class="btn add_list_btn" type="submit">
                                    <i class="fa fa-sliders"></i> {{ __('Filter') }}
                            </button>
                            <a href="{{ route('payment.pay-supplier') }}" class="btn cancel_btn">{{ __('Reset') }}</a>
                            <a href="" class="btn add_list_btn float-right" onclick="window.print()">{{ __('Print') }}</a>
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
                                    <th>{{ __('Branch') }}</th>
                                    <th>{{ __('Wallet Type') }}</th>
                                    <th>{{ __('Pay Type') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Discount Amount') }}</th>
                                    <th class="header_style_right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payment as $data)
                                @php
                                    // dd($data->transaction->id);
                                @endphp
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>{{ date('d-m-Y',strtotime($data->date)) }}</td>
                                        <td>{{ $data->branch?->name }}</td>
                                        <td>{{ $data->wallet_type }}</td>
                                        <td>{{ $data->pay_type }} ({{ $data->transaction?->supplier?->name }})</td>
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
                                                    @if (check_permission('payment.supplier-destroy'))
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
                                    <form action="{{ route('payment.supplier-destroy', $data->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-delete-modal title="{{ __('Supplier Payment') }}" id="{{ $data->id }}" />
                                    </form>

                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center no_data_style text-danger">{{ __('No Data Available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination justify-content-center">
                        {{-- {{ $payment->links() }} --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('backend.pages.payment.supplier_model')
@endsection

@push('js')


<script>
    $(document).ready(function() {
        function loadSuppliers(branchId) {
            $.ajax({
                url: "{{ route('get.suppliers.by.branch') }}",
                type: "GET",
                data: { branch_id: branchId },
                success: function(res) {
                    let options = `<option value="">{{ __('Select Supplier') }}</option>`;
                    res.forEach(function(supplier) {
                        options += `<option value="${supplier.id}">${supplier.name}</option>`;
                    });
                    $('#supplier_id').html(options).trigger('change');
                }
            });
        }

        // Initial load for non-admin user
        @if (auth()->user()->branch_id != 1)
            loadSuppliers("{{ auth()->user()->branch_id }}");
        @endif

        // If branch selector exists (admin)
        $('#branch_id').on('change', function() {
            let branchId = $(this).val();
            if (branchId) {
                loadSuppliers(branchId);
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
            $('#modal .select2').each(function() {
                $(this).select2({
                    dropdownParent: $(this).parent(),
                    width: '100%'
                });
            });
        });

        $(document).on('change', '.supplier_id', function () {
            let supplier_id = $(this).val(); console.log(supplier_id);
            if (!supplier_id) {
                $("#details").hide(500);
                return;
            }
            let url = "{{ route('supplier-account-balance', 'my_id') }}".replace('my_id', supplier_id);
            $.get(url, data => {
                $("#details").show(500);
                $("#account_name").text(data.supplier_name);
                $("#due_purchase").text(data.due_purchase);
                $("#total_purchase_due").text(data.purchase_due);
                $("#wallet_balance").text(data.walletBalance);
                $("#id_hint").html('*** ক্রয় বাবদ দেনা আছে '+Math.abs(data.purchase_due)+' {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }} ***');

                if(data.walletBalance>=0){
                        $("#wb_hint").html('**** আপনার থেকে সাপ্লাইয়ার পাবেঃ '+Math.abs(data.walletBalance)+'{{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }} ****');
                }else{
                        $("#wb_hint").html('**** সাপ্লাইয়ারের কাছে জমা আছেঃ '+Math.abs(data.walletBalance)+'{{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }} ****');
                }

                //for input
                $(".purchase_due").val(data.purchase_due);
                $(".wallet_balance").val(data.walletBalance);
            });
        });

        $("#supplier_id").change(function(){
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
            let data = ['Money Payment'];
            $.each(data,function(key,v){
                html +='<option value="'+v+'">'+v+'</option>';
            });
            $('#pay_type').html(html);
            $('#pay_type').trigger('change');
          } else if(wallet_type == 'Balance Adjust'){
            let data = ['Money Payment', 'Money Received'];
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
