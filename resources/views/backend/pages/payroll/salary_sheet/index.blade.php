@extends('backend.layouts.master')
@section('section-title', __('Salary Sheet'))
@section('page-title', __('Salary Sheet List'))
@section('action-button')
    <a href="{{ route('payroll.salary-sheet.create') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-plus"></i>
        {{ __('Generate Salary Sheet') }}
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <form action="{{ route('payroll.salary-sheet.index') }}" method="GET">
                        <div class="form-row align-items-end mb-3">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">{{ __('Month') }}</label>
                                <select name="month" class="form-control select2">
                                    <option value="">{{ __('All Months') }}</option>
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">{{ __('Year') }}</label>
                                <select name="year" class="form-control select2">
                                    <option value="">{{ __('All Years') }}</option>
                                    @foreach(range(date('Y')-1, date('Y')+1) as $y)
                                        <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <button type="submit" class="btn add_list_btn">{{ __('Filter') }}</button>
                                <a href="{{ route('payroll.salary-sheet.index') }}" class="btn add_list_btn_reset">{{ __('Reset') }}</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive mt-3">
                        <table id="datatable-buttons" class="table table-striped text-center">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th>{{ __('Employee') }}</th>
                                    <th>{{ __('Month/Year') }}</th>
                                    <th>{{ __('Gross') }}</th>
                                    <th>{{ __('Net Pay') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="header_style_right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($salary_sheets as $data)
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>{{ $data->employee?->name }}</td>
                                        <td>{{ date('F', mktime(0, 0, 0, $data->month, 1)) }}, {{ $data->year }}</td>
                                        <td>{{ number_format($data->gross_salary, 2) }}</td>
                                        <td>{{ number_format($data->net_pay, 2) }}</td>
                                        <td>
                                            @if($data->status == 1)
                                                <span class="badge badge-success">{{ __('Paid') }}</span>
                                            @else
                                                <span class="badge badge-warning">{{ __('Pending') }}</span>
                                            @endif
                                        </td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu">
                                                    @if($data->status == 0)
                                                        <a href="#" class="dropdown-item text-primary" data-toggle="modal" data-target="#payModal-{{ $data->id }}">
                                                            <i class="feather icon-credit-card"></i> {{ __('Pay Now') }}
                                                        </a>
                                                        <a href="#" class="dropdown-item text-info" data-toggle="modal" data-target="#editModal-{{ $data->id }}">
                                                            <i class="feather icon-edit"></i> {{ __('Adjust') }}
                                                        </a>
                                                    @endif
                                                    <a href="#" class="dropdown-item text-secondary">
                                                        <i class="feather icon-eye"></i> {{ __('View Payslip') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Pay Modal --}}
                                    @if($data->status == 0)
                                    <div class="modal fade" id="payModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('Pay Salary') }} - {{ $data->employee?->name }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                </div>
                                                <form action="{{ route('payroll.salary-sheet.pay', $data->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body text-left">
                                                        <div class="form-group">
                                                            <label>{{ __('Amount to Pay') }}</label>
                                                            <input type="text" class="form-control" value="{{ $data->net_pay }}" readonly>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('Payment Date') }}</label>
                                                            <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>{{ __('Bank Account') }}</label>
                                                            <select name="bank_id" class="form-control select2" required style="width: 100%">
                                                                @foreach(App\Models\BankAccount::all() as $bank)
                                                                    <option value="{{ $bank->id }}">{{ $bank->bank_name }} ({{ $bank->account_no }})</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                                                        <button type="submit" class="btn btn-primary">{{ __('Confirm Payment') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Adjustment Modal --}}
                                    <div class="modal fade" id="editModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ __('Adjust Salary') }} - {{ $data->employee?->name }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                </div>
                                                <form action="{{ route('payroll.salary-sheet.update', $data->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body text-left">
                                                        <div class="row">
                                                            <div class="col-md-3 form-group">
                                                                <label>{{ __('Present Days') }}</label>
                                                                <input type="number" name="present_days" class="form-control" value="{{ $data->present_days }}">
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label>{{ __('Absent Days') }}</label>
                                                                <input type="number" name="absent_days" class="form-control" value="{{ $data->absent_days }}">
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label>{{ __('Late Days') }}</label>
                                                                <input type="number" name="late_days" class="form-control" value="{{ $data->late_days }}">
                                                            </div>
                                                            <div class="col-md-3 form-group">
                                                                <label>{{ __('Leave Days') }}</label>
                                                                <input type="number" name="leave_days" class="form-control" value="{{ $data->leave_days }}">
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-md-4 form-group">
                                                                <label>{{ __('Overtime Hours') }}</label>
                                                                <input type="number" step="0.01" name="overtime_hours" class="form-control" value="{{ $data->overtime_hours }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label>{{ __('Overtime Rate') }}</label>
                                                                <input type="number" step="0.01" name="overtime_rate" class="form-control" value="{{ $data->overtime_rate }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label>{{ __('Overtime Total') }}</label>
                                                                <input type="number" step="0.01" name="overtime_amount" class="form-control" value="{{ $data->overtime_amount }}">
                                                            </div>
                                                        </div>
                                                        <hr>
                                                        <div class="row">
                                                            <div class="col-md-4 form-group">
                                                                <label>{{ __('Bonus') }}</label>
                                                                <input type="number" step="0.01" name="bonus" class="form-control" value="{{ $data->bonus }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label>{{ __('Deduction') }}</label>
                                                                <input type="number" step="0.01" name="deduction" class="form-control" value="{{ $data->deduction }}">
                                                            </div>
                                                            <div class="col-md-4 form-group">
                                                                <label>{{ __('Final Net Pay') }}</label>
                                                                <input type="number" step="0.01" name="net_pay" class="form-control" value="{{ $data->net_pay }}" style="background: #fdf2f2; font-weight: bold;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                                                        <button type="submit" class="btn btn-success">{{ __('Save Adjustments') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center text-danger no_data_style">{{ __('No Data Available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%'
        });
    });
</script>
@endpush
