@extends('backend.layouts.master')
@section('section-title', __('Installment'))
@section('page-title', __('Installment Details'))

@section('action-button')
    <a href="{{ route('installments.index') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-arrow-left"></i>
        {{ __('Back to List') }}
    </a>
@endsection

@push('css')
    <style>
        .inst-card {
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .inst-header {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 15px 20px;
            font-weight: 700;
            color: #1e293b;
        }
        .detail-label {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .detail-value {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
        }
    </style>
@endpush

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
        <!-- Installment contract summary -->
        <div class="col-md-12">
            <div class="card inst-card">
                <div class="inst-header">
                    <i class="feather icon-file-text mr-2 text-primary"></i>{{ __('Installment Agreement Summary') }}
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="detail-label">{{ __('Customer') }}</div>
                            <div class="detail-value text-primary font-weight-bold">{{ $installment->customer->name }}</div>
                            <div class="text-muted" style="font-size:12px;">{{ $installment->customer->phone }}</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="detail-label">{{ __('Invoice No') }}</div>
                            <div class="detail-value">
                                <a href="{{ route('invoice.show', $installment->invoice_id) }}" class="text-primary font-weight-bold">
                                    {{ $installment->invoice->invoice_no }}
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="detail-label">{{ __('Status') }}</div>
                            <div class="detail-value">
                                @if($installment->status == 'pending')
                                    <span class="badge badge-warning text-white">{{ __('Pending') }}</span>
                                @else
                                    <span class="badge badge-success">{{ __('Completed') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="detail-label">{{ __('Total Interest') }}</div>
                            <div class="detail-value">TK {{ number_format($installment->interest_amount, 2) }} ({{ $installment->interest_percentage }}%)</div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="detail-label">{{ __('Advance Pay') }}</div>
                            <div class="detail-value text-success">TK {{ number_format($installment->advance_pay, 2) }}</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="detail-label">{{ __('Remaining (without Interest)') }}</div>
                            <div class="detail-value">TK {{ number_format($installment->remaining_amount, 2) }}</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="detail-label">{{ __('Total with Interest') }}</div>
                            <div class="detail-value font-weight-bold text-danger">TK {{ number_format($installment->total_with_interest, 2) }}</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="detail-label">{{ __('Per Installment') }}</div>
                            <div class="detail-value text-primary font-weight-bold">TK {{ number_format($installment->per_installment_amount, 2) }}</div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="detail-label">{{ __('Total Installments') }}</div>
                            <div class="detail-value">{{ $installment->total_installments }}</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="detail-label">{{ __('Interval (Days)') }}</div>
                            <div class="detail-value">{{ $installment->interval_days }} {{ __('Days') }}</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="detail-label">{{ __('First Due Date') }}</div>
                            <div class="detail-value">{{ $installment->first_due_date }}</div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="detail-label">{{ __('Last Due Date') }}</div>
                            <div class="detail-value">{{ $installment->last_due_date }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Installment Payment Schedule list -->
        <div class="col-md-12">
            <div class="card inst-card">
                <div class="inst-header">
                    <i class="feather icon-calendar mr-2 text-primary"></i>{{ __('Payment Schedule') }}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center">
                            <thead class="bg-secondary text-white">
                                <tr>
                                    <th>{{ __('Installment #') }}</th>
                                    <th>{{ __('Due Date') }}</th>
                                    <th>{{ __('Scheduled Amount') }}</th>
                                    <th>{{ __('Paid Amount') }}</th>
                                    <th>{{ __('Paid Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($installment->schedules as $sched)
                                    <tr>
                                        <td class="font-weight-bold">#{{ $sched->installment_no }}</td>
                                        <td>{{ $sched->due_date }}</td>
                                        <td>TK {{ number_format($sched->amount, 2) }}</td>
                                        <td>
                                            @if($sched->status == 'paid')
                                                TK {{ number_format($sched->paid_amount, 2) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $sched->paid_date ?? '-' }}</td>
                                        <td>
                                            @if($sched->status == 'paid')
                                                <span class="badge badge-success">{{ __('Paid') }}</span>
                                            @else
                                                <span class="badge badge-warning text-white">{{ __('Pending') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($sched->status == 'pending')
                                                <button type="button" class="btn btn-sm btn-primary collect-btn" 
                                                        data-id="{{ $sched->id }}" 
                                                        data-no="{{ $sched->installment_no }}"
                                                        data-amount="{{ $sched->amount }}">
                                                    <i class="feather icon-check mr-1"></i> {{ __('Collect Payment') }}
                                                </button>
                                            @else
                                                <span class="text-success font-weight-bold"><i class="feather icon-check-circle"></i> {{ __('Received') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
