@extends('backend.layouts.master')
@section('section-title', __('Department'))
@section('page-title', __('Department List'))
@section('action-button')
    <a href="#" data-toggle="modal" data-target="#addModal" class="btn add_list_btn">
        <i class="mr-2 feather icon-plus"></i>
        {{ __('Add Department') }}
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
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="header_style_right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($departments as $data)
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>{{ $data->name }}</td>
                                        <td>
                                            <span class="badge {{ $data->status == 1 ? 'badge-success' : 'badge-danger' }}">
                                                {{ $data->status == 1 ? __('Active') : __('Inactive') }}
                                            </span>
                                        </td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a href="#" data-toggle="modal"
                                                        data-target="#editModal-{{ $data->id }}"
                                                        class="dropdown-item text-primary">
                                                        <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                    </a>
                                                    <a href="#" data-toggle="modal"
                                                        data-target="#deleteModal-{{ $data->id }}"
                                                        class="dropdown-item text-danger">
                                                        <i class="feather icon-trash"></i> {{ __('Delete') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Edit Modal --}}
                                    <form action="{{ route('payroll.department.update', $data->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <x-edit-modal title="{{ __('Edit Department') }}" id="{{ $data->id }}">
                                            <div class="mb-3 col-md-12 text-left">
                                                <label class="form-label font-weight-bold">{{ __('Department Name *') }}</label>
                                                <input type="text" class="form-control" name="name" value="{{ $data->name }}" required>
                                            </div>
                                            <div class="mb-3 col-md-12 text-left">
                                                <label class="form-label font-weight-bold">{{ __('Status') }}</label>
                                                <select name="status" class="form-control">
                                                    <option value="1" {{ $data->status == 1 ? 'selected' : '' }}>{{ __('Active') }}</option>
                                                    <option value="0" {{ $data->status == 0 ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                                </select>
                                            </div>
                                        </x-edit-modal>
                                    </form>

                                    {{-- Delete Modal --}}
                                    <form action="{{ route('payroll.department.destroy', $data->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-delete-modal title="{{ __('Department') }}" id="{{ $data->id }}" />
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
    <form action="{{ route('payroll.department.store') }}" method="POST">
        @csrf
        <x-add-modal title="{{ __('Add Department') }}">
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Department Name *') }}</label>
                <input type="text" class="form-control" name="name" placeholder="{{ __('Enter Department Name') }}" required>
            </div>
        </x-add-modal>
    </form>
@endsection
