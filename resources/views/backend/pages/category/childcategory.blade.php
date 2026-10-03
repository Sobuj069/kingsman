@extends('backend.layouts.master')
@section('section-title', __('Category'))
@section('page-title', __('Child Category List'))

@section('action-button')
    <a href="#" data-toggle="modal" data-target="#addChildModal" class="btn add_list_btn">
        <i class="mr-2 feather icon-plus"></i>
        {{ __('Add Child Category') }}
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
                                    <th class="header_style_left">{{ __('#') }}</th>
                                    <th>{{ __('Category') }}</th>
                                    <th>{{ __('Sub Category') }}</th>
                                    <th>{{ __('Child Category Name') }}</th>
                                    <th>{{ __('Total Products') }}</th>
                                    <th class="header_style_right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($childCategories as $data)
                                    @php
                                        $count_prod = App\Models\Product::where('child_category_id', $data->id)->count();
                                    @endphp
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->iteration }}</td>
                                        <td>
                                            <span class="badge badge-primary" style="font-size: 13px; padding: 5px 10px;">
                                                {{ $data->category?->name ?? ($data->subCategory?->category?->name ?? '-') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary" style="font-size: 13px; padding: 5px 10px;">
                                                {{ $data->subCategory?->name ?? '-' }}
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
                                                    <a href="#" data-toggle="modal"
                                                        data-target="#editModal-{{ $data->id }}"
                                                        class="dropdown-item text-primary">
                                                        <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                    </a>

                                                    @if ($count_prod < 1)
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
                                        <td colspan="100%" class="text-center no_data_style text-danger">{{ __('No Child Category Available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Loop for edit/delete modals --}}
                    @foreach ($childCategories as $data)
                        {{-- Edit Modal --}}
                        <form action="{{ route('child-category.update', $data->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <x-edit-modal title="{{ __('Edit Child Category') }}" id="{{ $data->id }}">
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Parent Sub Category *') }}</label>
                                    <select name="sub_category_id" class="form-control select2" required style="width: 100%;">
                                        <option value="">{{ __('Select Sub Category') }}</option>
                                        @foreach ($subCategories as $sub)
                                            <option value="{{ $sub->id }}" {{ $data->sub_category_id == $sub->id ? 'selected' : '' }}>
                                                {{ $sub->category?->name ? ($sub->category->name . ' -> ') : '' }}{{ $sub->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Child Category Name *') }}</label>
                                    <input type="text" class="form-control" name="name" placeholder="{{ __('Enter Child Category Name') }}"
                                        required value="{{ $data->name }}">
                                </div>
                            </x-edit-modal>
                        </form>

                        {{-- Delete Modal --}}
                        <form action="{{ route('child-category.destroy', $data->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <x-delete-modal title="{{ __('Child Category') }}" id="{{ $data->id }}" />
                        </form>
                    @endforeach

                    <div class="pagination justify-content-center mt-3">
                        {{ $childCategories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <form action="{{ route('child-category.store') }}" method="POST">
        @csrf
        <x-add-modal title="{{ __('Add Child Category') }}" id="addChildModal">
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Parent Sub Category *') }}</label>
                <select name="sub_category_id" class="form-control select2" required style="width: 100%;">
                    <option value="">{{ __('Select Sub Category') }}</option>
                    @foreach ($subCategories as $sub)
                        <option value="{{ $sub->id }}">{{ $sub->category?->name ? ($sub->category->name . ' -> ') : '' }}{{ $sub->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Child Category Name *') }}</label>
                <input type="text" class="form-control" name="name" placeholder="{{ __('Enter Child Category Name') }}" required>
            </div>
        </x-add-modal>
    </form>
@endsection
