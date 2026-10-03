@extends('backend.layouts.master')
@section('section-title', __('Leave Application'))
@section('page-title', __('Leave Application List'))
@section('action-button')
    <a href="#" data-toggle="modal" data-target="#addModal" class="btn add_list_btn">
        <i class="mr-2 feather icon-plus"></i>
        {{ __('Add Leave Application') }}
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable-buttons" class="table table-striped text-center">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th>{{ __('Employee') }}</th>
                                    <th>{{ __('Leave Type') }}</th>
                                    <th>{{ __('Start Date') }}</th>
                                    <th>{{ __('End Date') }}</th>
                                    <th>{{ __('Days') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="header_style_right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leave_applications as $data)
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>{{ $data->employee?->name }}</td>
                                        <td>{{ $data->leave_type?->name }}</td>
                                        <td>{{ $data->start_date }}</td>
                                        <td>{{ $data->end_date }}</td>
                                        <td>{{ $data->total_days }}</td>
                                        <td>
                                            @if($data->status == 0)
                                                <span class="badge badge-warning">{{ __('Pending') }}</span>
                                            @elseif($data->status == 1)
                                                <span class="badge badge-success">{{ __('Approved') }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ __('Rejected') }}</span>
                                            @endif
                                        </td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu">
                                                    @if($data->status == 0)
                                                        <form action="{{ route('payroll.leave-application.status', $data->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="status" value="1">
                                                            <button type="submit" class="dropdown-item text-success"><i class="feather icon-check"></i> {{ __('Approve') }}</button>
                                                        </form>
                                                        <form action="{{ route('payroll.leave-application.status', $data->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="status" value="2">
                                                            <button type="submit" class="dropdown-item text-danger"><i class="feather icon-x"></i> {{ __('Reject') }}</button>
                                                        </form>
                                                    @endif
                                                    <a href="#" data-toggle="modal" data-target="#deleteModal-{{ $data->id }}" class="dropdown-item text-danger">
                                                        <i class="feather icon-trash"></i> {{ __('Delete') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Delete Modal --}}
                                    <form action="{{ route('payroll.leave-application.destroy', $data->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-delete-modal title="{{ __('Leave Application') }}" id="{{ $data->id }}" />
                                    </form>
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
    <form action="{{ route('payroll.leave-application.store') }}" method="POST">
        @csrf
        <x-add-modal title="{{ __('Add Leave Application') }}">
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Employee *') }}</label>
                <select name="employee_id" class="form-control select2" required style="width: 100%">
                    <option value="">{{ __('Select Employee') }}</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Leave Type *') }}</label>
                <select name="leave_type_id" class="form-control select2" required style="width: 100%">
                    <option value="">{{ __('Select Leave Type') }}</option>
                    @foreach($leave_types as $lt)
                        <option value="{{ $lt->id }}">{{ $lt->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="row">
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Start Date *') }}</label>
                    <input type="date" class="form-control" name="start_date" required>
                </div>
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('End Date *') }}</label>
                    <input type="date" class="form-control" name="end_date" required>
                </div>
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Reason') }}</label>
                <textarea name="reason" class="form-control" rows="3" placeholder="{{ __('Enter Reason') }}"></textarea>
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
