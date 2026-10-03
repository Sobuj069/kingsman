@extends('backend.layouts.master')
@section('section-title', __('Branch'))
@section('page-title', __('Branch List'))
@if (check_permission('branch.store'))
    @section('action-button')
        <a href="#" data-toggle="modal" data-target="#addModal" class="btn add_list_btn">
            <i class="mr-2 feather icon-plus"></i>
            {{ __('Add Branch') }}
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
                    <div class="table-responsive mt-3">
                        <table id="datatable-buttons" class="table table-striped text-center">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Phone ') }}</th>
                                    <th>{{ __('Address') }}</th>
                                    <th class="header_style_right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($branchs as $data)
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>{{ $data->name }}</td>
                                        <td>{{ $data->phone }}</td>
                                        <td>{{ $data->address }}</td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    @if (check_permission('branch.update'))
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#editModal-{{ $data->id }}"
                                                            class="dropdown-item text-primary">
                                                            <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                        </a>
                                                    @endif

                                                    @if (check_permission('branch.destroy'))
                                                        @if ($data->id != 1)
                                                            <a href="#" data-toggle="modal"
                                                                data-target="#deleteModal-{{ $data->id }}"
                                                                class="dropdown-item text-danger">
                                                                <i class="feather icon-trash"></i> {{ __('Delete') }}
                                                            </a>
                                                        @endif
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
                    @foreach ($branchs as $data)
                        <form action="{{ route('branch.update', $data->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <x-edit-modal title="{{ __('Edit Branch') }}" id="{{ $data->id }}">
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Branch Name *') }}</label>
                                    <input type="text" class="form-control" name="name" value="{{ $data->name }}" required placeholder="{{ __('Enter Name') }}">
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Phone *') }}</label>
                                    <input type="text" class="form-control" name="phone" value="{{ $data->phone }}" required placeholder="{{ __('Enter Phone') }}">
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Email') }}</label>
                                    <input type="email" class="form-control" name="email" value="{{ $data->email }}" placeholder="{{ __('Enter Email') }}">
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Shop Name') }}</label>
                                    <input type="text" class="form-control" name="shop_name" value="{{ $data->shop_name }}" placeholder="{{ __('Enter Shop Name') }}">
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Address') }}</label>
                                    <input type="text" class="form-control" name="address" value="{{ $data->address }}" placeholder="{{ __('Enter Address') }}">
                                </div>
                            </x-edit-modal>
                        </form>

                        {{-- delete modal --}}
                        <form action="{{ route('branch.destroy', $data->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <x-delete-modal title="{{ __('Branch') }}" id="{{ $data->id }}" />
                        </form>
                    @endforeach

                </div>
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <form action="{{ route('branch.store') }}" method="POST">
        @csrf
        <x-add-modal title="{{ __('Add Branch') }}">
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Branch Name *') }}</label>
                <input type="text" class="form-control" name="name" placeholder="{{ __('Enter Branch Name') }}" required>
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Phone *') }}</label>
                <input type="text" class="form-control" name="phone" placeholder="{{ __('Enter Phone') }}" required>
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Email') }}</label>
                <input type="email" class="form-control" name="email" placeholder="{{ __('Enter Email') }}">
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Shop Name') }}</label>
                <input type="text" class="form-control" name="shop_name" placeholder="{{ __('Enter Shop Name') }}">
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Address') }}</label>
                <input type="text" class="form-control" name="address" placeholder="{{ __('Enter Address') }}">
            </div>
        </x-add-modal>
    </form>

@endsection
