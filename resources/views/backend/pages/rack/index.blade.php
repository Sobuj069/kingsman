@extends('backend.layouts.master')
@section('section-title', __('Rack'))
@section('page-title', __('Rack List'))

@section('action-button')
    @if (check_permission('rack.store'))
        <a href="#" data-toggle="modal" data-target="#addModal" class="btn add_list_btn">
            <i class="mr-2 feather icon-plus"></i>
            {{ __('Add Rack') }}
        </a>
    @endif
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <form action="{{ route('rack.index') }}" method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="barcode" class="form-control barcode-filter-input" data-barcode-input
                                    placeholder="{{ __('Scan Barcode / Search Rack / Shelf') }}" value="{{ request('barcode') ?? request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn add_list_btn">{{ __('Filter') }}</button>
                                <a href="{{ route('rack.index') }}" class="btn add_list_btn_reset">{{ __('Reset') }}</a>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table id="datatable-buttons" class="table table-striped text-center">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th>{{ __('Rack Name') }}</th>
                                    <th>{{ __('Code / Shelf') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Branch') }}</th>
                                    <th>{{ __('Products in Rack') }}</th>
                                    <th class="header_style_right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($racks as $data)
                                    @php
                                        $branch_id = session('branch_filter_id', auth()->user()->branch_id);
                                        if (auth()->user()->branch_id == 1 && !session('branch_filter_id')) {
                                            $branch_racks = App\Models\BranchRack::where('rack_id', $data->id)->get();
                                        } else {
                                            $branch_racks = App\Models\BranchRack::where('rack_id', $data->id)
                                                ->where('branch_id', auth()->user()->branch_id)
                                                ->get();
                                        }
                                        $count_prod = $data->products_count ?? $data->products()->count();
                                    @endphp
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td><strong>{{ $data->name }}</strong></td>
                                        <td>{{ $data->code ?? '-' }}</td>
                                        <td>{{ $data->description ?? '-' }}</td>
                                        <td>
                                            @foreach ($branch_racks as $br)
                                                <span class="badge badge-primary">{{ $br->branch?->name }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @if ($count_prod > 0)
                                                <span class="badge badge-success px-2 py-1" style="font-size: 12px;">
                                                    <i class="feather icon-box"></i> {{ $count_prod }} {{ __('Products') }}
                                                </span>
                                            @else
                                                <span class="badge badge-secondary px-2 py-1" style="font-size: 12px;">
                                                    0 {{ __('Products') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton-{{ $data->id }}" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton-{{ $data->id }}">
                                                    {{-- edit --}}
                                                    @if (check_permission('rack.update'))
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#editModal-{{ $data->id }}"
                                                            class="dropdown-item text-primary">
                                                            <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                        </a>
                                                    @endif

                                                    {{-- delete --}}
                                                    @if (check_permission('rack.destroy'))
                                                        @if ($count_prod > 0)
                                                            <a href="javascript:void(0);" 
                                                               onclick="iziToast.warning({title: '{{ __('Cannot Delete') }}', message: '{{ __('Cannot delete rack \":name\" because products are currently assigned to it. Remove products first.', ['name' => $data->name]) }}', position: 'topRight'});" 
                                                               class="dropdown-item text-muted" 
                                                               title="{{ __('Cannot delete rack with products') }}">
                                                                <i class="feather icon-trash-2"></i> {{ __('Delete (In Use)') }}
                                                            </a>
                                                        @else
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
                                        <td colspan="100%" class="text-center no_data_style text-danger">{{ __('No Data Available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $racks->links() }}
                    </div>

                    {{-- Add Modal --}}
                    <form action="{{ route('rack.store') }}" method="POST">
                        @csrf
                        <x-add-modal title="{{ __('Add Rack') }}">
                            <div class="mb-3 col-md-12 text-left">
                                <label class="form-label font-weight-bold">{{ __('Rack Name *') }}</label>
                                <input type="text" class="form-control" name="name" placeholder="{{ __('e.g. Rack A1, Shelf 3') }}" required>
                            </div>
                            <div class="mb-3 col-md-12 text-left">
                                <label class="form-label font-weight-bold">{{ __('Code / Shelf No') }}</label>
                                <input type="text" class="form-control" name="code" placeholder="{{ __('e.g. R-A1') }}">
                            </div>
                            <div class="mb-3 col-md-12 text-left">
                                <label class="form-label font-weight-bold">{{ __('Description / Location Note') }}</label>
                                <textarea class="form-control" name="description" rows="2" placeholder="{{ __('e.g. Left corner 2nd row') }}"></textarea>
                            </div>
                            @if (auth()->user()->branch_id == 1)
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Branch *') }}</label>
                                    <select class="form-control select2" name="branch_id[]" multiple required style="width: 100%" data-placeholder="{{ __('Select Branches') }}">
                                        @foreach ($branchs as $branch_item)
                                            <option value="{{ $branch_item->id }}" selected>{{ $branch_item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </x-add-modal>
                    </form>

                    {{-- Edit & Delete Modals for each rack --}}
                    @foreach ($racks as $data)
                        @php
                            $selectedBranchIds = \App\Models\BranchRack::where('rack_id', $data->id)->pluck('branch_id');
                        @endphp
                        {{-- edit modal --}}
                        <form action="{{ route('rack.update', $data->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <x-edit-modal title="{{ __('Edit Rack') }}" id="{{ $data->id }}">
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Rack Name *') }}</label>
                                    <input type="text" class="form-control" name="name" placeholder="{{ __('Enter Name') }}"
                                        required value="{{ $data->name }}">
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Code / Shelf No') }}</label>
                                    <input type="text" class="form-control" name="code" placeholder="{{ __('e.g. R-A1') }}" value="{{ $data->code }}">
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Description / Location Note') }}</label>
                                    <textarea class="form-control" name="description" rows="2" placeholder="{{ __('e.g. Left corner 2nd row') }}">{{ $data->description }}</textarea>
                                </div>
                                @if (auth()->user()->branch_id == 1)
                                    <div class="mb-3 col-md-12 text-left">
                                        <label class="form-label font-weight-bold">{{ __('Branch *') }}</label>
                                        <select class="form-control select2" name="branch_id[]" multiple required style="width: 100%" data-placeholder="{{ __('Select Branches') }}">
                                            @foreach ($branchs as $branch_item)
                                                <option value="{{ $branch_item->id }}" {{ in_array($branch_item->id, $selectedBranchIds->toArray()) ? 'selected' : '' }}>{{ $branch_item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </x-edit-modal>
                        </form>

                        {{-- delete modal --}}
                        @if (($data->products_count ?? $data->products()->count()) == 0)
                            <form action="{{ route('rack.destroy', $data->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <x-delete-modal title="{{ __('Delete Rack') }}" id="{{ $data->id }}" />
                            </form>
                        @endif
                    @endforeach

                </div>
            </div>
        </div>
    </div>
@endsection
