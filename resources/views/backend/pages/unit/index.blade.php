@extends('backend.layouts.master')
@section('section-title', __('Unit'))
@section('page-title', __('Unit List'))
@if (check_permission('unit.store'))
    @section('action-button')
        <a href="#" data-toggle="modal" data-target="#addModal" class="btn add_list_btn">
            <i class="mr-2 feather icon-plus"></i>
            {{ __('Add Unit') }}
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
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Related To') }}</th>
                                    <th>{{ __('Related Value') }}</th>
                                    <th>{{ __('Final Value') }}</th>
                                    <th class="header_style_right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($units as $data)
                                    @php
                                        $count_ru = App\Models\Unit::where('related_unit_id', $data->id)->count();
                                        $count_pro = App\Models\Product::where('unit_id', $data->id)->count();
                                    @endphp
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>{{ $data->name }}</td>
                                        <td>{{ $data->related_unit ? $data->related_unit->name : '-' }}</td>
                                        <td>{{ $data->related_value ? $data->related_value : '-' }}</td>
                                        <td>
                                            @if ($data->related_unit)
                                                {{ $data->name }} = 1
                                                {{ $data->related_unit ? $data->related_unit->name : '-' }}
                                                {{ $data->related_sign ? $data->related_sign : '-' }}
                                                {{ $data->related_value ? $data->related_value : '-' }}
                                            @endif
                                        </td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    @if (check_permission('unit.update'))
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#editModal-{{ $data->id }}"
                                                            class="dropdown-item text-primary {{ $data->id == 1 ? 'disabled' : '' }}">
                                                            <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                        </a>
                                                    @endif

                                                    {{-- delete --}}
                                                    @if (check_permission('unit.destroy'))
                                                        @if ($count_ru < 1 && $count_pro < 1)
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
                                        <td colspan="100%" class="text-center text-danger">{{ __('No Data Available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{--  Loop through again to render modals outside table for better layout --}}
                    @foreach ($units as $data)
                        {{-- edit modal  --}}
                        <form action="{{ route('unit.update', $data->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <x-edit-modal title="{{ __('Edit Unit') }}" id="{{ $data->id }}">
                                <x-input label="{{ __('Unit Name *') }}" type="text" name="name"
                                    placeholder="{{ __('Enter Unit Name') }}" required md="12"
                                    value="{{ $data->name }}" />
                                <x-select label="{{ __('Related Unit') }}" name="related_unit_id" md="12">
                                    <option value="">{{ __('Select Related Unit') }}</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}"
                                            @if ($unit->id == $data->related_unit_id) selected @endif>
                                            {{ $unit->name }}</option>
                                    @endforeach
                                </x-select>
                                <x-input label="{{ __('Related Sign') }}" type="text" name="related_sign" value="*"
                                    md="12" readonly />
                                <x-input label="{{ __('Related Value *') }}" type="number" name="related_value"
                                    placeholder="{{ __('Enter Related Value') }}" required md="12"
                                    value="{{ $data->related_value }}" />
                            </x-edit-modal>
                        </form>

                        {{-- delete modal --}}
                        <form action="{{ route('unit.destroy', $data->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <x-delete-modal title="{{ __('Unit') }}" id="{{ $data->id }}" />
                        </form>
                    @endforeach

                    <div class="pagination justify-content-center">
                        {{ $units->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <form action="{{ route('unit.store') }}" method="POST">
        @csrf
        <x-add-modal title="{{ __('Add Unit') }}">
            <x-input label="{{ __('Unit Name *') }}" type="text" name="name" placeholder="{{ __('Enter Unit Name') }}" required
                md="12" />
            <x-select label="{{ __('Related Unit') }}" name="related_unit_id" md="12">
                <option value="">{{ __('Select Related Unit') }}</option>
                @foreach ($units as $unit_data)
                    <option value="{{ $unit_data->id }}">{{ $unit_data->name }}</option>
                @endforeach
            </x-select>
            <x-input label="{{ __('Related Sign') }}" type="text" name="related_sign" value="*" md="12" readonly />
            <x-input label="{{ __('Related Value *') }}" type="number" name="related_value" placeholder="{{ __('Enter Related Value') }}" required
                md="12" />
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

