@extends('backend.layouts.master')
@section('section-title', __('Category'))
@section('page-title', __('Category List'))

@section('action-button')
    @if (env('APP_SUB_CATEGORY') == 'yes')
        <a href="{{ route('sub-category.index') }}" class="btn add_list_btn mr-2" style="background-color: #6c757d; border-color: #6c757d;">
            <i class="mr-2 feather icon-list"></i>
            {{ __('Sub Categories') }}
        </a>
    @endif
    @if (check_permission('category.store'))
        <a href="#" data-toggle="modal" data-target="#addModal" class="btn add_list_btn">
            <i class="mr-2 feather icon-plus"></i>
            {{ __('Add Category') }}
        </a>
    @endif
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
                                    <th style="width: 70px;">{{ __('Image') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Branch') }}</th>
                                    <th class="header_style_right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $data)
                                    @php
                                        $branch_id = session('branch_filter_id', auth()->user()->branch_id);
                                        if (auth()->user()->branch_id == 1 && !session('branch_filter_id')) {
                                            // Admin & All Branch
                                            $branch_category = App\Models\BranchCategory::where(
                                                'category_id',
                                                $data->id,
                                            )->get();
                                        } else {
                                            $branch_category = App\Models\BranchCategory::where(
                                                'category_id',
                                                $data->id,
                                            )
                                                ->where('branch_id', auth()->user()->branch_id)
                                                ->get();
                                        }
                                        $count_cat = App\Models\Product::where('category_id', $data->id)->count();
                                    @endphp
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>
                                            @if(!empty($data->image) && file_exists(public_path('uploads/category/' . $data->image)))
                                                <img src="{{ asset('uploads/category/' . $data->image) }}" class="rounded shadow-sm" style="width: 44px; height: 44px; object-fit: cover;" alt="{{ $data->name }}">
                                            @elseif(!empty($data->image) && file_exists(public_path('frontend/images/' . $data->image)))
                                                <img src="{{ asset('frontend/images/' . $data->image) }}" class="rounded shadow-sm" style="width: 44px; height: 44px; object-fit: cover;" alt="{{ $data->name }}">
                                            @else
                                                <div class="d-inline-flex align-items-center justify-content-center bg-light rounded text-muted font-weight-bold" style="width: 44px; height: 44px; font-size: 10px; border: 1px dashed #ccc;">
                                                    No Img
                                                </div>
                                            @endif
                                        </td>
                                        <td class="font-weight-bold">{{ $data->name }}</td>
                                        <td>
                                            @foreach ($branch_category as $branch)
                                                <span class="badge badge-primary">{{ $branch->branch?->name }}</span>
                                            @endforeach
                                        </td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    @if (check_permission('category.update'))
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#editModal-{{ $data->id }}"
                                                            class="dropdown-item text-primary{{ $data->id == 1 ? 'disabled' : '' }}">
                                                            <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                        </a>
                                                    @endif

                                                    {{-- delete --}}
                                                    @if (check_permission('category.destroy'))
                                                        @if ($count_cat < 1)
                                                            <a href="#" data-toggle="modal"
                                                                data-target="#deleteModal-{{ $data->id }}"
                                                                class="dropdown-item text-danger {{ $data->id == 1 ? 'disabled' : '' }}">
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
                                        <td colspan="100%" class="text-center no_data_style text-danger">{{ __('No Data Available') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{--  Loop through again to render modals outside table for better layout --}}
                    @foreach ($categories as $data)
                         {{-- edit modal  --}}
                         @php
                            $selectedBranchIds = \App\Models\BranchCategory::where(
                                'category_id',
                                $data->id,
                            )->pluck('branch_id');
                        @endphp
                        <form action="{{ route('category.update', $data->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <x-edit-modal title="{{ __('Edit Category') }}" id="{{ $data->id }}">
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Name *') }}</label>
                                    <input type="text" class="form-control" name="name" placeholder="{{ __('Enter Name') }}"
                                        required value="{{ $data->name }}">
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Category Image') }}</label>
                                    @if(!empty($data->image))
                                        <div class="mb-2">
                                            @if(file_exists(public_path('uploads/category/' . $data->image)))
                                                <img src="{{ asset('uploads/category/' . $data->image) }}" class="rounded shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
                                            @elseif(file_exists(public_path('frontend/images/' . $data->image)))
                                                <img src="{{ asset('frontend/images/' . $data->image) }}" class="rounded shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
                                            @endif
                                        </div>
                                    @endif
                                    <input type="file" class="form-control-file border p-1 rounded" name="image" accept="image/*">
                                    <small class="text-muted">{{ __('Recommended: JPG, PNG, WEBP (Square or Banner format)') }}</small>
                                </div>
                                @if(auth()->user()->branch_id == 1)
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Branch *') }}</label>
                                    <select class="form-control select2" name="branch_id[]" multiple required style="width: 100%" data-placeholder="{{ __('Select Branches') }}">
                                        @foreach ($branchs as $branch_item)
                                            <option
                                                value="{{ $branch_item->id }}"{{ in_array($branch_item->id, $selectedBranchIds->toArray()) ? 'selected' : '' }}>
                                                {{ $branch_item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                            </x-edit-modal>
                        </form>

                        {{-- delete modal --}}
                        <form action="{{ route('category.destroy', $data->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <x-delete-modal title="{{ __('Category') }}" id="{{ $data->id }}" />
                        </form>
                    @endforeach

                    <div class="pagination justify-content-center">
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <form action="{{ route('category.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <x-add-modal title="{{ __('Add Category') }}">
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Category Name *') }}</label>
                <input type="text" class="form-control" name="name" placeholder="{{ __('Enter Category Name') }}" required>
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Category Image') }}</label>
                <input type="file" class="form-control-file border p-1 rounded" name="image" accept="image/*">
                <small class="text-muted">{{ __('Recommended: JPG, PNG, WEBP') }}</small>
            </div>
            @if(auth()->user()->branch_id == 1)
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Branch *') }}</label>
                <select class="form-control select2" name="branch_id[]" multiple required style="width: 100%" data-placeholder="{{ __('Select Branches') }}">
                    @foreach ($branchs as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
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
                        dropdownParent: $(this).closest('.modal'),
                        width: '100%',
                        placeholder: $(this).data('placeholder') || 'Select Option',
                        allowClear: true
                    });
                });
            });
        });
    </script>
@endpush
