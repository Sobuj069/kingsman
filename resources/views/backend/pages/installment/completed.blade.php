@extends('backend.layouts.master')
@section('section-title', __('Installment'))
@section('page-title', __('Completed Installments'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center">
                            <thead class="bg-info text-white" style="background-color: #0ea5e9 !important;">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Invoice No') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Installments Paid') }}</th>
                                    <th>{{ __('First Due Date') }}</th>
                                    <th>{{ __('Last Due Date') }}</th>
                                    <th>{{ __('Total Paid') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($installments as $key => $inst)
                                    @php
                                        $paid_sum = $inst->advance_pay + $inst->schedules->sum('paid_amount');
                                    @endphp
                                    <tr>
                                        <td>{{ $installments->firstItem() + $key }}</td>
                                        <td>
                                            <a href="{{ route('invoice.show', $inst->invoice_id) }}" class="text-primary font-weight-bold">
                                                {{ $inst->invoice->invoice_no }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $inst->customer->name }}
                                            <br><small class="text-muted">{{ $inst->customer->phone }}</small>
                                        </td>
                                        <td class="font-weight-bold">{{ $inst->total_installments }} / {{ $inst->total_installments }}</td>
                                        <td>{{ $inst->first_due_date }}</td>
                                        <td>{{ $inst->last_due_date }}</td>
                                        <td class="text-success font-weight-bold">TK {{ number_format($paid_sum, 2) }}</td>
                                        <td>
                                            <span class="badge badge-success">{{ __('Completed') }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('installments.show', $inst->id) }}" class="btn btn-sm btn-info">
                                                <i class="feather icon-eye mr-1"></i> {{ __('View Details') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-danger">{{ __('No completed installment contracts found.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination justify-content-center mt-3">
                        {{ $installments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
