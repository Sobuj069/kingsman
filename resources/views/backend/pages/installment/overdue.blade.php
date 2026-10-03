@extends('backend.layouts.master')
@section('section-title', __('Installment'))
@section('page-title', __('Overdue Installment Payments'))

@section('content')
    <!-- Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center">
                            <thead class="bg-danger text-white" style="background-color: #ef4444 !important;">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Invoice No') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Installment No') }}</th>
                                    <th>{{ __('Due Date') }}</th>
                                    <th>{{ __('Days Overdue') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schedules as $key => $sched)
                                    @php
                                        $due = \Carbon\Carbon::parse($sched->due_date);
                                        $diff = $due->diffInDays(\Carbon\Carbon::today());
                                    @endphp
                                    <tr>
                                        <td>{{ $schedules->firstItem() + $key }}</td>
                                        <td>
                                            <a href="{{ route('invoice.show', $sched->installment->invoice_id) }}" class="text-primary font-weight-bold">
                                                {{ $sched->installment->invoice->invoice_no }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $sched->installment->customer->name }}
                                            <br><small class="text-muted">{{ $sched->installment->customer->phone }}</small>
                                        </td>
                                        <td class="font-weight-bold">#{{ $sched->installment_no }} / {{ $sched->installment->total_installments }}</td>
                                        <td class="text-danger font-weight-bold">{{ $sched->due_date }}</td>
                                        <td>
                                            <span class="badge badge-danger px-2 py-1">{{ $diff }} {{ __('days overdue') }}</span>
                                        </td>
                                        <td class="text-danger font-weight-bold">TK {{ number_format($sched->amount, 2) }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-success collect-btn" 
                                                    data-id="{{ $sched->id }}" 
                                                    data-no="{{ $sched->installment_no }}"
                                                    data-amount="{{ $sched->amount }}">
                                                <i class="feather icon-check mr-1"></i> {{ __('Collect Payment') }}
                                            </button>
                                            <a href="{{ route('installments.show', $sched->installment_id) }}" class="btn btn-sm btn-info ml-1">
                                                <i class="feather icon-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-success font-weight-bold">{{ __('Hooray! No overdue payments found.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination justify-content-center mt-3">
                        {{ $schedules->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Collection Modal -->
    <div class="modal fade" id="collectModal" tabindex="-1" role="dialog" aria-labelledby="collectModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="collectForm" method="POST" action="">
                    @csrf
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="collectModalLabel"><i class="feather icon-plus-circle mr-2"></i>{{ __('Collect Payment for Installment') }} <span id="modal-inst-no"></span></h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="bank_id">{{ __('Deposit Bank Account') }} <span class="text-danger">*</span></label>
                            <select name="bank_id" id="bank_id" class="form-control select2" style="width:100%;" required>
                                <option value="">{{ __('Select Bank Account') }}</option>
                                @foreach($bankAccounts as $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->bank_name }} - {{ $bank->account_number }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="paid_amount">{{ __('Payment Amount (TK)') }} <span class="text-danger">*</span></label>
                            <input type="number" step="any" name="paid_amount" id="paid_amount" class="form-control" min="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="paid_date">{{ __('Payment Date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="paid_date" id="paid_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-success"><i class="feather icon-save mr-1"></i>{{ __('Submit Payment') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('.collect-btn').on('click', function() {
                var scheduleId = $(this).data('id');
                var instNo = $(this).data('no');
                var amount = $(this).data('amount');

                $('#modal-inst-no').text('#' + instNo);
                $('#paid_amount').val(parseFloat(amount).toFixed(2));
                
                // Construct form action URL dynamically
                var url = "{{ route('installments.collect', ':id') }}";
                url = url.replace(':id', scheduleId);
                $('#collectForm').attr('action', url);

                $('#collectModal').modal('show');
            });
        });
    </script>
@endpush
