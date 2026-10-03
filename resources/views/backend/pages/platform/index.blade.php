@extends('backend.layouts.master')
@section('section-title', __('Platform'))
@section('page-title', __('Platform List'))

@section('action-button')
    <a href="#" data-toggle="modal" data-target="#addModal" class="btn add_list_btn">
        <i class="mr-2 feather icon-plus"></i>
        {{ __('Add Platform') }}
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
                                    <th class="header_style_right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($platforms as $data)
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>{{ $data->name }}</td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">

                                                    {{-- edit --}}
                                                    <a href="#" data-toggle="modal"
                                                        data-target="#editModal-{{ $data->id }}"
                                                        class="dropdown-item text-primary">
                                                        <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                    </a>

                                                    {{-- delete --}}
                                                    <a href="#" data-toggle="modal"
                                                        data-target="#deleteModal-{{ $data->id }}"
                                                        class="dropdown-item text-danger">
                                                        <i class="feather icon-trash"></i> {{ __('Delete') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center no_data_style text-danger">{{ __('No Data Available') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Loop through again to render modals outside table for better layout --}}
                    @foreach ($platforms as $data)
                        {{-- edit modal --}}
                        <form action="{{ route('platform.update', $data->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <x-edit-modal title="{{ __('Edit Platform') }}" id="{{ $data->id }}">
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Name *') }}</label>
                                    <input type="text" class="form-control" name="name" placeholder="{{ __('Enter Name') }}"
                                        required value="{{ $data->name }}">
                                </div>
                            </x-edit-modal>
                        </form>

                        {{-- delete modal --}}
                        <form action="{{ route('platform.destroy', $data->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <x-delete-modal title="{{ __('Platform') }}" id="{{ $data->id }}" />
                        </form>
                    @endforeach

                    <div class="pagination justify-content-center">
                        {{ $platforms->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <form action="{{ route('platform.store') }}" method="POST">
        @csrf
        <x-add-modal title="{{ __('Add Platform') }}">
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Platform Name *') }}</label>
                <input type="text" class="form-control" name="name" placeholder="{{ __('Enter Platform Name') }}" required>
            </div>
        </x-add-modal>
    </form>

@endsection
