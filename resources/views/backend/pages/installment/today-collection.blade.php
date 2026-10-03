@extends('backend.layouts.master')
@section('section-title', __('Installment'))
@section('page-title', __('Today\'s Collections'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center">
                            <thead class="bg-success text-white" style="background-color: #10b981 !important;">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Invoice No') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Installment No') }}</th>
                                    <th>{{ __('Received Amount') }}</th>
                                    <th>{{ __('Collection Date') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schedules as $key => $sched)
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
                                        <td class="text-success font-weight-bold">TK {{ number_format($sched->paid_amount, 2) }}</td>
                                        <td>{{ $sched->paid_date }}</td>
                                        <td>
                                            <a href="{{ route('installments.show', $sched->installment_id) }}" class="btn btn-sm btn-info">
                                                <i class="feather icon-eye mr-1"></i> {{ __('View Details') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-danger">{{ __('No payments collected today.') }}</td>
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
@endsection
