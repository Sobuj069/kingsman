@extends('backend.layouts.master')
@section('section-title', __('Designation'))
@section('page-title', __('Designation List'))
@section('action-button')
    <a href="#" data-toggle="modal" data-target="#addModal" class="btn add_list_btn">
        <i class="mr-2 feather icon-plus"></i>
        {{ __('Add Designation') }}
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
                                @forelse($designations as $data)
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
                                    <form action="{{ route('payroll.designation.update', $data->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <x-edit-modal title="{{ __('Edit Designation') }}" id="{{ $data->id }}">
                                            <div class="mb-3 col-md-12 text-left">
                                                <label class="form-label font-weight-bold">{{ __('Designation Name *') }}</label>
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
                                    <form action="{{ route('payroll.designation.destroy', $data->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-delete-modal title="{{ __('Designation') }}" id="{{ $data->id }}" />
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
    <form action="{{ route('payroll.designation.store') }}" method="POST">
        @csrf
        <x-add-modal title="{{ __('Add Designation') }}">
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Designation Name *') }}</label>
                <input type="text" class="form-control" name="name" placeholder="{{ __('Enter Designation Name') }}" required>
            </div>
        </x-add-modal>
    </form>
@endsection
