@extends('backend.layouts.master')
@section('section-title', __('Employee'))
@section('page-title', __('Employee List'))
@if (check_permission('employee.store'))
    @section('action-button')
        <a href="#" data-toggle="modal" data-target="#addModal" class="btn add_list_btn">
            <i class="mr-2 feather icon-plus"></i>
            {{ __('Add Employee') }}
        </a>
    @endsection
@endif
@push('css')
    <style>
        @media print {

            table,
            table th,
            table td {
                color: black !important;
            }

            .h-hide {
                display: none;
            }
        }
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <form action="{{ route('employee.index') }}" method="GET">
                        <div class="form-row align-items-end mb-3">
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">{{ __('Employee Name') }}</label>
                                <input type="text" placeholder="{{ __('Enter Name') }}" name="name"
                                    value="{{ $name ?? '' }}" class="form-control" style="height: 38px !important;">
                            </div>
                            <div class="form-group col-md-3">
                                <label class="font-weight-bold">{{ __('Phone No') }}</label>
                                <input type="text" placeholder="{{ __('Enter Phone No') }}" name="phone"
                                    value="{{ $phone ?? '' }}" class="form-control" style="height: 38px !important;">
                            </div>
                            <div class="form-group col-md-6">
                                <label>&nbsp;</label>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <button type="submit" class="btn add_list_btn" style="padding-top: 8px !important; padding-bottom: 8px !important;">
                                            <i class="fa fa-sliders mr-1"></i> {{ __('Filter') }}
                                        </button>
                                        <a href="{{ route('employee.index') }}" class="btn add_list_btn_reset ml-1" style="padding-top: 8px !important; padding-bottom: 8px !important;">
                                            {{ __('Reset') }}
                                        </a>
                                    </div>
                                    <div>
                                        <a href="" class="btn add_list_btn" style="padding-top: 8px !important; padding-bottom: 8px !important;" onclick="window.print()">
                                            <i class="feather icon-printer mr-1"></i> {{ __('Print') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive mt-3">
                        <table id="datatable-buttons" class="table table-striped text-center">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th>{{ __('Branch') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Joining Date') }}</th>
                                    <th>{{ __('Salary') }}</th>
                                    <th>{{ __('Payment Date') }}</th>
                                    <th>{{ __('Advance Payment') }}</th>
                                    <th class="header_style_right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $data)
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>{{ $data->branch?->name }}</td>
                                        <td>{{ $data->name }}</td>
                                        <td>{{ $data->phone }}</td>
                                        <td>{{ $data->joining_date }}</td>
                                        <td>{{ number_format($data->salary, 2) }}
                                            {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }} </td>
                                        <td>{{ $data->payment_date }}</td>
                                        <td>{{ number_format($data->advance_payment, 2) }}
                                            {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }} </td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a href="#"class="btn dropdown-item text-primary"
                                                        data-toggle="modal"
                                                        data-target="#exampleModal-{{ $data->id }}">
                                                        <i class="feather icon-edit"></i> {{ __('Add Payment') }}
                                                    </a>
                                                    @if (check_permission('employee.update'))
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#editModal-{{ $data->id }}"
                                                            class="dropdown-item text-primary">
                                                            <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('employee.salary.details',$data->id) }}"class="btn dropdown-item text-primary">
                                                        <i class="feather icon-printer"></i> {{ __('Salary Details') }}
                                                    </a>
                                                    @if (check_permission('employee.destroy'))
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
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center text-danger no_data_style">{{ __('No Data Available') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{--  Loop through again to render modals outside table for better layout --}}
                    @foreach ($employees as $data)
                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal-{{ $data->id }}" tabindex="-1"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">{{ __('Add Payment ( ') }}
                                            {{ $data->name }})</h5>
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('employee.payment.store',$data->id) }}"
                                            method="POST">
                                            @csrf
                                            <input type="hidden" name="staf_id" value="{{ $data->id }}">
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="">{{ __('Payment Amount *') }}</label>
                                                    <input type="number" name="payment_amount" id=""
                                                        class="form-control" placeholder="{{ __('Enter Amount') }}"
                                                        required>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label for="">{{ __('Payment Date *') }}</label>
                                                    <input type="date" name="payment_date" id=""
                                                        class="form-control" placeholder="{{ __('Enter Amount') }}"
                                                        required>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label for="">{{ __('Payment Type *') }}</label>
                                                    <select name="payment_type" id="" class="select2"
                                                        required>
                                                        <option value="">{{ __('Select Payment Type') }}</option>
                                                        <option value="Salary">{{ __('Salary') }}</option>
                                                        <option value="Advance">{{ __('Advance Payment') }}</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label for="">{{ __('Payment Method *') }}</label>
                                                    <select name="payment_method" id="" class="select2"
                                                        required>
                                                        @foreach ($bankAccounts as $account)
                                                            <option value="{{ $account->id }}">
                                                                {{ $account->bank_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-12 mb-3">
                                                    <label for="">{{ __('Payment Note') }}</label>
                                                    <textarea name="note" id="" placeholder="{{ __('Enter Note') }}" rows="2" class="form-control"></textarea>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12 text-right">
                                                    <button type="button" class="btn cancel_btn"
                                                        data-dismiss="modal">{{ __('Close') }}</button>
                                                    <button type="submit" class="btn save_btn">{{ __('Save') }}</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- edit modal  --}}
                        <form action="{{ route('employee.update', $data->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <x-edit-modal title="{{ __('Edit Employee') }}" id="{{ $data->id }}">
                                <div class="row">
                                    <div class="mb-3 col-md-6 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Employee Name *') }}</label>
                                        <input type="text" class="form-control" name="name" placeholder="{{ __('Enter Name') }}"
                                            required value="{{ $data->name }}">
                                    </div>
                                    <div class="mb-3 col-md-6 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Phone *') }}</label>
                                        <input type="number" class="form-control" name="phone" value="{{ $data->phone }}" required placeholder="{{ __('Enter Phone') }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-6 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Father Name') }}</label>
                                        <input type="text" class="form-control" name="father_name" value="{{ $data->father_name }}" placeholder="{{ __('Enter Father Name') }}">
                                    </div>
                                    <div class="mb-3 col-md-6 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Mother Name') }}</label>
                                        <input type="text" class="form-control" name="mother_name" value="{{ $data->mother_name }}" placeholder="{{ __('Enter Mother Name') }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-6 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Department') }}</label>
                                        <select class="form-control select2" name="department_id" style="width: 100%" data-placeholder="{{ __('Select Department') }}">
                                            <option value=""></option>
                                            @foreach (App\Models\Department::all() as $dept)
                                                <option value="{{ $dept->id }}" {{ $data->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-6 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Designation') }}</label>
                                        <select class="form-control select2" name="designation_id" style="width: 100%" data-placeholder="{{ __('Select Designation') }}">
                                            <option value=""></option>
                                            @foreach (App\Models\Designation::all() as $desg)
                                                <option value="{{ $desg->id }}" {{ $data->designation_id == $desg->id ? 'selected' : '' }}>{{ $desg->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @if (auth()->user()->branch_id == 1)
                                    <div class="mb-3 col-md-12 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Branch *') }}</label>
                                        <select class="form-control select2" name="branch_id" required style="width: 100%" data-placeholder="{{ __('Select Branch') }}">
                                            @foreach ($allBranch as $branch_item)
                                                <option value="{{ $branch_item->id }}" {{ $data->branch_id == $branch_item->id ? 'selected' : '' }}>
                                                    {{ $branch_item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="mb-3 col-md-6 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Email') }}</label>
                                        <input type="email" class="form-control" name="email" value="{{ $data->email }}" placeholder="{{ __('Enter Email') }}">
                                    </div>
                                    <div class="mb-3 col-md-6 text-left">
                                        <label class="form-label font-weight-bold">{{ __('NID') }}</label>
                                        <input type="text" class="form-control" name="nid" value="{{ $data->nid }}" placeholder="{{ __('Enter NID') }}">
                                    </div>
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Address') }}</label>
                                    <input type="text" class="form-control" name="address" value="{{ $data->address }}" placeholder="{{ __('Enter Address') }}">
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-md-6 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Gender *') }}</label>
                                        <select class="form-control select2" name="gender" required style="width: 100%" data-placeholder="{{ __('Select Gender') }}">
                                            <option value="Male" {{ $data->gender == 'Male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                                            <option value="Female" {{ $data->gender == 'Female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                                            <option value="Others" {{ $data->gender == 'Others' ? 'selected' : '' }}>{{ __('Others') }}</option>
                                        </select>
                                    </div>
                                    <div class="mb-3 col-md-6 text-left">
                                        <label class="form-label font-weight-bold">{{ __('DOB') }}</label>
                                        <input type="date" class="form-control" name="dob" value="{{ $data->dob }}">
                                    </div>
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Salary') }}</label>
                                    <input type="number" class="form-control" name="salary" value="{{ $data->salary }}" placeholder="{{ __('Enter Salary') }}">
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Service Commission') }}</label>
                                    <input type="number" class="form-control" name="commission" value="{{ $data->commission }}" placeholder="{{ __('Enter % Amount') }}">
                                </div>
                            </x-edit-modal>
                        </form>

                        {{-- delete modal --}}
                        <form action="{{ route('employee.destroy', $data->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <x-delete-modal title="{{ __('Employee') }}" id="{{ $data->id }}" />
                        </form>
                    @endforeach

                    <div class="pagination justify-content-center">
                        {{ $employees->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <form action="{{ route('employee.store') }}" method="POST">
        @csrf
        <x-add-modal title="{{ __('Add Employee') }}">
            <div class="row">
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Employee Name *') }}</label>
                    <input type="text" class="form-control" name="name" placeholder="{{ __('Enter Employee Name') }}" required>
                </div>
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Phone *') }}</label>
                    <input type="number" class="form-control" name="phone" placeholder="{{ __('Enter Phone') }}" required>
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Father Name') }}</label>
                    <input type="text" class="form-control" name="father_name" placeholder="{{ __('Enter Father Name') }}">
                </div>
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Mother Name') }}</label>
                    <input type="text" class="form-control" name="mother_name" placeholder="{{ __('Enter Mother Name') }}">
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Department') }}</label>
                    <select class="form-control select2" name="department_id" style="width: 100%" data-placeholder="{{ __('Select Department') }}">
                        <option value=""></option>
                        @foreach (App\Models\Department::all() as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Designation') }}</label>
                    <select class="form-control select2" name="designation_id" style="width: 100%" data-placeholder="{{ __('Select Designation') }}">
                        <option value=""></option>
                        @foreach (App\Models\Designation::all() as $desg)
                            <option value="{{ $desg->id }}">{{ $desg->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @if (auth()->user()->branch_id == 1)
                <div class="mb-3 col-md-12 text-left">
                    <label class="form-label font-weight-bold">{{ __('Branch *') }}</label>
                    <select class="form-control select2" name="branch_id" required style="width: 100%" data-placeholder="{{ __('Select Branch') }}">
                        <option value=""></option>
                        @foreach ($allBranch as $branch_item)
                            <option value="{{ $branch_item->id }}">{{ $branch_item->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="row">
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Email') }}</label>
                    <input type="email" class="form-control" name="email" placeholder="{{ __('Enter Email') }}">
                </div>
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('NID') }}</label>
                    <input type="text" class="form-control" name="nid" placeholder="{{ __('Enter NID Number') }}">
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Joining Date *') }}</label>
                    <input type="date" class="form-control" name="joining_date" required>
                </div>
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Salary') }}</label>
                    <input type="number" class="form-control" name="salary" placeholder="{{ __('Enter Salary') }}">
                </div>
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Address') }}</label>
                <input type="text" class="form-control" name="address" placeholder="{{ __('Enter Address') }}">
            </div>
            <div class="row">
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('Gender *') }}</label>
                    <select class="form-control select2" name="gender" required style="width: 100%" data-placeholder="{{ __('Select Gender') }}">
                        <option value=""></option>
                        <option value="Male">{{ __('Male') }}</option>
                        <option value="Female">{{ __('Female') }}</option>
                        <option value="Others">{{ __('Others') }}</option>
                    </select>
                </div>
                <div class="mb-3 col-md-6 text-left">
                    <label class="form-label font-weight-bold">{{ __('DOB') }}</label>
                    <input type="date" class="form-control" name="dob">
                </div>
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Commission') }}</label>
                <input type="number" class="form-control" name="commission" placeholder="{{ __('Enter % Amount') }}">
            </div>
        </x-add-modal>
    </form>

@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Initialize Select2 for ALL Bootstrap modals on this page
            $('.modal').on('shown.bs.modal', function () {
                $(this).find('.select2').each(function() {
                    $(this).select2({
                        // Attach to the immediate parent to prevent "floating" offset issues
                        dropdownParent: $(this).parent(), 
                        width: '100%',
                        placeholder: $(this).data('placeholder') || 'Select Option',
                        allowClear: true,
                        minimumResultsForSearch: $(this).find('option').length > 10 ? 0 : Infinity
                    });
                });
            });
        });
    </script>
    <style>
        /* Ensure the parent is the reference point for the dropdown */
        .mb-3.col-md-12 {
            position: relative !important;
        }
        .select2-container--open {
            z-index: 9999 !important;
        }
        /* Style the selection box to match dashboard */
        .select2-container .select2-selection--single {
            height: 38px !important;
            padding: 5px !important;
            background-color: #f8f9fa !important;
            border: 1px solid #ced4da !important;
        }
        body.dark-theme .select2-container .select2-selection--single {
            background-color: #2b3035 !important;
            border-color: #495057 !important;
            color: #fff !important;
        }
    </style>
@endpush
