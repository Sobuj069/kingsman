@extends('backend.layouts.master')
@section('section-title', __('Installment'))
@section('page-title', __('Installment List'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card m-b-30">
                <div class="card-body">
                    <form action="{{ route('installments.index') }}" method="GET" class="mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <label>{{ __('Customer') }}</label>
                                <select name="customer_id" class="form-control select2">
                                    <option value="">{{ __('All Customers') }}</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }} - {{ $customer->phone }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>{{ __('Status') }}</label>
                                <select name="status" class="form-control">
                                    <option value="">{{ __('All Statuses') }}</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fa-solid fa-magnifying-glass mr-1"></i> {{ __('Search') }}</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Invoice No') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Total with Interest') }}</th>
                                    <th>{{ __('Advance Paid') }}</th>
                                    <th>{{ __('Total Paid') }}</th>
                                    <th>{{ __('Total Due') }}</th>
                                    <th>{{ __('Installments') }}</th>
                                    <th>{{ __('Interval') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($installments as $key => $inst)
                                    @php
                                        $paid_sum = $inst->advance_pay + $inst->schedules->where('status', 'paid')->sum('paid_amount');
                                        $due_sum = max(0, $inst->total_with_interest - $inst->schedules->where('status', 'paid')->sum('paid_amount'));
                                    @endphp
                                    <tr>
                                        <td>{{ $installments->firstItem() + $key }}</td>
                                        <td>
                                            <a href="{{ route('invoice.show', $inst->invoice_id) }}" class="text-primary font-weight-bold">
                                                {{ $inst->invoice->invoice_no }}
                                            </a>
                                        </td>
                                        <td>{{ $inst->customer->name }} <br><small class="text-muted">{{ $inst->customer->phone }}</small></td>
                                        <td>TK {{ number_format($inst->total_with_interest + $inst->advance_pay, 2) }}</td>
                                        <td>TK {{ number_format($inst->advance_pay, 2) }}</td>
                                        <td>TK {{ number_format($paid_sum, 2) }}</td>
                                        <td>TK {{ number_format($due_sum, 2) }}</td>
                                        <td>{{ $inst->schedules->where('status', 'paid')->count() }} / {{ $inst->total_installments }}</td>
                                        <td>{{ $inst->interval_days }} {{ __('Days') }}</td>
                                        <td>
                                            @if($inst->status == 'pending')
                                                <span class="badge badge-warning text-white">{{ __('Pending') }}</span>
                                            @else
                                                <span class="badge badge-success">{{ __('Completed') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('installments.show', $inst->id) }}" class="btn btn-sm btn-info">
                                                <i class="feather icon-eye mr-1"></i> {{ __('Details / Collect') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center text-danger">{{ __('No installment transactions found.') }}</td>
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
