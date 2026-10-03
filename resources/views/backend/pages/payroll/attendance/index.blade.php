@extends('backend.layouts.master')
@section('section-title', __('Attendance'))
@section('page-title', __('Attendance Record'))
@section('action-button')
    <a href="#" data-toggle="modal" data-target="#addModal" class="btn add_list_btn">
        <i class="mr-2 feather icon-plus"></i>
        {{ __('Add Attendance') }}
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <form action="{{ route('payroll.attendance.index') }}" method="GET">
                        <div class="form-row align-items-end mb-3">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">{{ __('Date') }}</label>
                                <input type="date" name="date" class="form-control" value="{{ request('date') ?? date('Y-m-d') }}">
                            </div>
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">{{ __('Branch') }}</label>
                                <select name="branch_id" class="form-control select2">
                                    <option value="">{{ __('All Branches') }}</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <button type="submit" class="btn add_list_btn">{{ __('Filter') }}</button>
                                <a href="{{ route('payroll.attendance.index') }}" class="btn add_list_btn_reset">{{ __('Reset') }}</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive mt-3">
                        <table id="datatable-buttons" class="table table-striped text-center">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th>{{ __('Employee') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Check In') }}</th>
                                    <th>{{ __('Check Out') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="header_style_right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $data)
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>{{ $data->employee?->name }}</td>
                                        <td>{{ $data->date }}</td>
                                        <td>{{ $data->check_in }}</td>
                                        <td>{{ $data->check_out }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($data->status == 'Present') badge-success 
                                                @elseif($data->status == 'Absent') badge-danger 
                                                @elseif($data->status == 'Late') badge-warning 
                                                @else badge-info @endif">
                                                {{ __($data->status) }}
                                            </span>
                                        </td>
                                        <td class="table_data_style_right">
                                            <a href="#" data-toggle="modal" data-target="#editModal-{{ $data->id }}" class="btn btn-sm add_list_btn">
                                                <i class="feather icon-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
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

    {{-- Add Modal --}}
    <form action="{{ route('payroll.attendance.store') }}" method="POST">
        @csrf
        <x-add-modal title="{{ __('Add Attendance') }}">
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Employee *') }}</label>
                <select name="employee_id" class="form-control select2" required style="width: 100%">
                    <option value="">{{ __('Select Employee') }}</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->branch?->name }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Date *') }}</label>
                <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="row">
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Check In') }}</label>
                    <input type="time" class="form-control" name="check_in">
                </div>
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Check Out') }}</label>
                    <input type="time" class="form-control" name="check_out">
                </div>
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Status *') }}</label>
                <select name="status" class="form-control" required>
                    <option value="Present">{{ __('Present') }}</option>
                    <option value="Absent">{{ __('Absent') }}</option>
                    <option value="Late">{{ __('Late') }}</option>
                    <option value="Leave">{{ __('Leave') }}</option>
                </select>
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Note') }}</label>
                <input type="text" class="form-control" name="note" placeholder="{{ __('Enter Note') }}">
            </div>
        </x-add-modal>
    </form>
@endsection

@push('js')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            dropdownParent: $('#addModal')
        });
    });
</script>
@endpush
