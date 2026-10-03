@extends('backend.layouts.master')
@section('section-title', __('Brand'))
@section('page-title', __('Brand List'))

@section('action-button')
    <a href="#" data-toggle="modal" data-target="#addModal" class="btn add_list_btn">
        <i class="mr-2 feather icon-plus"></i>
        {{ __('Add Brand') }}
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
                                    <th>{{ __('Branch') }}</th>
                                    <th class="header_style_right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($brands as $data)
                                    @php
                                        $branch_id = session('branch_filter_id', auth()->user()->branch_id);
                                        if (auth()->user()->branch_id == 1 && !session('branch_filter_id')) {
                                            $branch_brand = App\Models\BranchBrand::where('brand_id', $data->id)->get();
                                        } else {
                                            $branch_brand = App\Models\BranchBrand::where('brand_id', $data->id)
                                            ->where('branch_id', auth()->user()->branch_id)
                                                ->get();
                                        }
                                        $count_cat = App\Models\Product::where('brand_id', $data->id)->count();
                                    @endphp
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>{{ $data->name }}</td>
                                        <td>
                                            @foreach ($branch_brand as $branch)
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

                                                    {{-- edit --}}
                                                    @if (check_permission('brand.update'))
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#editModal-{{ $data->id }}"
                                                            class="dropdown-item text-primary">
                                                            <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                        </a>
                                                    @endif

                                                    {{-- delete --}}
                                                    @if (check_permission('brand.destroy'))
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
                                        <td colspan="100%" class="text-center no_data_style text-danger">{{ __('No Data Available') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{--  Loop through again to render modals outside table for better layout --}}
                    @foreach ($brands as $data)
                        @php
                            $selectedBranchIds = \App\Models\BranchBrand::where(
                                'brand_id',
                                $data->id,
                            )->pluck('branch_id');
                        @endphp
                        {{-- edit modal  --}}
                        <form action="{{ route('brand.update', $data->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <x-edit-modal title="{{ __('Edit Brand') }}" id="{{ $data->id }}">
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Name *') }}</label>
                                    <input type="text" class="form-control" name="name" placeholder="{{ __('Enter Name') }}"
                                        required value="{{ $data->name }}">
                                </div>
                                @if (auth()->user()->branch_id == 1)
                                    <div class="mb-3 col-md-12 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Branch *') }}</label>
                                        <select class="form-control select2" name="branch_id[]" multiple required style="width: 100%" data-placeholder="{{ __('Select Branches') }}">
                                            @foreach ($branchs as $branch_item)
                                                <option value="{{ $branch_item->id }}"{{ in_array($branch_item->id, $selectedBranchIds->toArray()) ? 'selected' : '' }}>{{ $branch_item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </x-edit-modal>
                        </form>

                        {{-- delete modal --}}
                        <form action="{{ route('brand.destroy', $data->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <x-delete-modal title="{{ __('Brand') }}" id="{{ $data->id }}" />
                        </form>
                    @endforeach

                    <div class="pagination justify-content-center">
                        {{ $brands->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <form action="{{ route('brand.store') }}" method="POST">
        @csrf
        <x-add-modal title="{{ __('Add Brand') }}">
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Brand Name *') }}</label>
                <input type="text" class="form-control" name="name" placeholder="{{ __('Enter Brand Name') }}" required>
            </div>
            @if (auth()->user()->branch_id == 1)
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
