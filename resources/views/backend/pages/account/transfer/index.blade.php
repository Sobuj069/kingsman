@extends('backend.layouts.master')
@section('section-title', __('Account'))
@section('page-title', __('Transfer'))

@if (check_permission('bank-transfer-create'))
    @section('action-button')
        <a href="#" id="modal_btn" class="btn add_list_btn">
            <i class="feather icon-plus mr-2"></i>
            {{ __('Add Transfer') }}
        </a>
    @endsection
@endif

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <div class="table-responsive mt-3">
                        <table id="datatable-buttons" class="table table-striped text-center">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left"> {{ __('Date') }} </th>
                                    <th> {{ __('Branch') }}</th>
                                    <th> {{ __('From Account') }} </th>
                                    <th> {{ __('To Account') }} </th>
                                    <th> {{ __('Note') }} </th>
                                    <th> {{ __('Amount') }} </th>
                                    <th class="header_style_right"> {{ __('Created By') }} </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $total = 0; @endphp
                                @forelse ($bank_transfers as $transfer)
                                    <tr>
                                        <td class="table_data_style_left"> {{ date('d-M-Y', strtotime($transfer->date)) }} </td>
                                        <td> {{ $transfer->branch?->name }} </td>
                                        <td>
                                            <span class="badge badge-danger">
                                                {{ $transfer->from_bank_account->bank_name }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-success">
                                                {{ $transfer->to_bank_account->bank_name }}
                                            </span>
                                        </td>
                                        <td> {{ $transfer->note ?: '--' }} </td>
                                        <td class="font-weight-bold"> 
                                            {{ number_format($transfer->amount, 2) }} {{ get_setting('com_currency') }}
                                        </td>
                                        <td class="table_data_style_right">
                                            {{ $transfer->user->name }}
                                        </td>
                                    </tr>
                                    @php $total += $transfer->amount; @endphp
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center no_data_style text-danger">
                                            {{ __('No Data Available') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($bank_transfers->count() > 0)
                            <tfoot>
                                <tr class="header_bg text-white">
                                    <th colspan="5" class="header_style_left text-right"> {{ __('Total Amount') }} </th>
                                    <th class="header_style_right text-left"> 
                                        {{ number_format($total, 2) }} {{ get_setting('com_currency') }}
                                    </th>
                                    <th></th>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
                @if($bank_transfers->hasPages())
                <div class="card-footer bg-transparent border-0 py-4">
                    <div class="pagination justify-content-center">
                        {{ $bank_transfers->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @include('backend.pages.account.transfer.model')
@endsection

@push('js')
    <script type="text/javascript">
        $("#modal_btn").on("click", function () {
            $("#modal").modal("show");
        });

        // Toggle Balance Logic
        function formatBalance(amount) {
            return parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2 });
        }

        $(document).on('change', '.from_bank_id', function(){
            var bank_id = $(this).val();
            if (bank_id != '') {
                $.ajax({
                    url: "{{ route('get-account-balance') }}",
                    type: "GET",
                    data: { bank_id: bank_id },
                    success: function(data){
                        var currency = "{{ get_setting('com_currency') }}";
                        var balanceText = "{{ __('Current Balance') }}: " + formatBalance(data.balance) + " " + currency;
                        $('#from_amount').html('<span class="text-danger font-bold uppercase text-[11px]"><i class="feather icon-info mr-1"></i> ' + balanceText + '</span>');
                        $('.from_amount').val(data.balance);
                    }
                });
            }
        });

        $(document).on('change', '.to_bank_id', function(){
            var bank_id = $(this).val();
            if (bank_id != '') {
                $.ajax({
                    url: "{{ route('get-account-balance') }}",
                    type: "GET",
                    data: { bank_id: bank_id },
                    success: function(data){
                        var currency = "{{ get_setting('com_currency') }}";
                        var balanceText = "{{ __('Current Balance') }}: " + formatBalance(data.balance) + " " + currency;
                        $('#to_amount').html('<span class="text-emerald-500 font-bold uppercase text-[11px]"><i class="feather icon-check-circle mr-1"></i> ' + balanceText + '</span>');
                    }
                });
            }
        });

        $(document).on('change', '#from_bank_id', function(){
            var from_bank_id = $(this).val();
            if (from_bank_id != '') {
                $.ajax({
                    url:"{{ route('get-to-account') }}",
                    type:"GET",
                    data:{from_bank_id:from_bank_id},
                    success:function(data){
                        var html = '<option value="">{{ __('Select Account') }}</option>';
                        $.each(data, function(key, v){
                            html +='<option value="'+v.id+'">'+v.bank_name+'</option>';
                        });
                        $('#to_bank_id').html(html);
                        $('.to_bank_id').trigger('change');
                    }
                });
            }
        });
    </script>
@endpush
