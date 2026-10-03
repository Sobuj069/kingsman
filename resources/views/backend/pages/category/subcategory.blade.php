@extends('backend.layouts.master')
@section('section-title', __('Category'))
@section('page-title', __('Sub Category List'))

@if (check_permission('sub-category.store'))
    @section('action-button')
        <a href="#" data-toggle="modal" data-target="#addModal" class="btn add_list_btn">
            <i class="mr-2 feather icon-plus"></i>
            {{ __('Add Sub Category') }}
        </a>
    @endsection
@endif

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable-buttons" class="table table-striped text-center">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Sub Category Name') }}</th>
                                    <th>{{ __('Total Products') }}</th>
                                    <th class="header_style_right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subCategories as $data)
                                    @php
                                        $count_prod = App\Models\Product::where('sub_category_id', $data->id)->count();
                                    @endphp
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>
                                            <span class="badge badge-primary" style="font-size: 13px; padding: 5px 10px;">
                                                {{ $data->category?->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="font-weight-bold">{{ $data->name }}</td>
                                        <td>
                                            <span class="badge badge-info" style="font-size: 12px; padding: 4px 8px;">
                                                {{ $count_prod }} {{ __('Products') }}
                                            </span>
                                        </td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton-{{ $data->id }}" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton-{{ $data->id }}">
                                                    @if (check_permission('sub-category.update'))
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#editModal-{{ $data->id }}"
                                                            class="dropdown-item text-primary">
                                                            <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                        </a>
                                                    @endif

                                                    @if (check_permission('sub-category.destroy'))
                                                        @if ($count_prod < 1)
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
                                        <td colspan="100%" class="text-center no_data_style text-danger">{{ __('No Subcategory Available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Loop for edit/delete modals --}}
                    @foreach ($subCategories as $data)
                        {{-- Edit Modal --}}
                        <form action="{{ route('sub-category.update', $data->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <x-edit-modal title="{{ __('Edit Sub Category') }}" id="{{ $data->id }}">
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Parent Category *') }}</label>
                                    <select name="category_id" class="form-control select2" required style="width: 100%;">
                                        <option value="">{{ __('Select Category') }}</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ $data->category_id == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Sub Category Name *') }}</label>
                                    <input type="text" class="form-control" name="name" placeholder="{{ __('Enter Subcategory Name') }}"
                                        required value="{{ $data->name }}">
                                </div>
                            </x-edit-modal>
                        </form>

                        {{-- Delete Modal --}}
                        <form action="{{ route('sub-category.destroy', $data->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <x-delete-modal title="{{ __('Sub Category') }}" id="{{ $data->id }}" />
                        </form>
                    @endforeach

                    <div class="pagination justify-content-center mt-3">
                        {{ $subCategories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <form action="{{ route('sub-category.store') }}" method="POST">
        @csrf
        <x-add-modal title="{{ __('Add Sub Category') }}">
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Parent Category *') }}</label>
                <select name="category_id" class="form-control select2" required style="width: 100%;">
                    <option value="">{{ __('Select Category') }}</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Sub Category Name *') }}</label>
                <input type="text" class="form-control" name="name" placeholder="{{ __('Enter Subcategory Name') }}" required>
            </div>
        </x-add-modal>
    </form>
@endsection
