@extends('backend.layouts.master')
@section('section-title', __('Discount Group'))
@section('page-title', __('Discount Group List'))

@section('action-button')
    <a href="#" data-toggle="modal" data-target="#addModal" class="btn add_list_btn">
        <i class="mr-2 feather icon-plus"></i>
        {{ __('Add Discount Group') }}
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
                                    <th>{{ __('Group Name') }}</th>
                                    <th>{{ __('Discount Type') }}</th>
                                    <th>{{ __('Discount Value') }}</th>
                                    <th>{{ __('Assigned Customers') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="header_style_right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($discountGroups as $data)
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->iteration + ($discountGroups->currentPage() - 1) * $discountGroups->perPage() }}</td>
                                        <td class="font-weight-bold text-dark">{{ $data->name }}</td>
                                        <td>
                                            @if($data->type == 'percentage')
                                                <span class="badge badge-info px-2 py-1"><i class="feather icon-percent"></i> {{ __('Percentage') }}</span>
                                            @else
                                                <span class="badge badge-success px-2 py-1"><i class="feather icon-dollar-sign"></i> {{ __('Fixed Amount') }}</span>
                                            @endif
                                        </td>
                                        <td class="font-weight-bold text-primary">
                                            @if($data->type == 'percentage')
                                                {{ number_format($data->value, 2) }} %
                                            @else
                                                Tk {{ number_format($data->value, 2) }}
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary px-2 py-1">{{ $data->customers_count }} {{ __('Customer(s)') }}</span>
                                        </td>
                                        <td>
                                            @if($data->status == 1)
                                                <span class="badge badge-success px-2 py-1">{{ __('Active') }}</span>
                                            @else
                                                <span class="badge badge-danger px-2 py-1">{{ __('Inactive') }}</span>
                                            @endif
                                        </td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton{{ $data->id }}" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $data->id }}">
                                                    <a href="#" data-toggle="modal" data-target="#editModal-{{ $data->id }}"
                                                        class="dropdown-item text-primary">
                                                        <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                    </a>
                                                    <a href="#" data-toggle="modal" data-target="#deleteModal-{{ $data->id }}"
                                                        class="dropdown-item text-danger">
                                                        <i class="feather icon-trash"></i> {{ __('Delete') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center no_data_style text-danger">{{ __('No Discount Group Found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $discountGroups->links() }}
                    </div>

                    {{-- Edit & Delete Modals --}}
                    @foreach ($discountGroups as $data)
                        {{-- Edit Modal --}}
                        <div class="modal fade" id="editModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ __('Edit Discount Group') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('discount-group.update', $data->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body text-left">
                                            <div class="form-group mb-3">
                                                <label class="font-weight-bold">{{ __('Discount Group Name') }} <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" value="{{ $data->name }}" placeholder="e.g. VIP Customer 10%" required>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label class="font-weight-bold">{{ __('Discount Type') }} <span class="text-danger">*</span></label>
                                                <select name="type" class="form-control" required>
                                                    <option value="percentage" {{ $data->type == 'percentage' ? 'selected' : '' }}>{{ __('Percentage (%)') }}</option>
                                                    <option value="fixed" {{ $data->type == 'fixed' ? 'selected' : '' }}>{{ __('Fixed Amount (Tk)') }}</option>
                                                </select>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label class="font-weight-bold">{{ __('Discount Value / Rate') }} <span class="text-danger">*</span></label>
                                                <input type="number" step="any" min="0" name="value" class="form-control" value="{{ $data->value }}" placeholder="e.g. 10 or 100" required>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label class="font-weight-bold">{{ __('Status') }}</label>
                                                <select name="status" class="form-control">
                                                    <option value="1" {{ $data->status == 1 ? 'selected' : '' }}>{{ __('Active') }}</option>
                                                    <option value="0" {{ $data->status == 0 ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                                            <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Delete Modal --}}
                        <div class="modal fade" id="deleteModal-{{ $data->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ __('Delete Discount Group') }}</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-center">
                                        <p class="mb-2">{{ __('Are you sure you want to delete this discount group?') }}</p>
                                        <h6 class="text-danger font-weight-bold">{{ $data->name }}</h6>
                                        <small class="text-muted">{{ __('Customers assigned to this group will no longer receive group discounts.') }}</small>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                                        <form action="{{ route('discount-group.destroy', $data->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Add New Discount Group') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('discount-group.store') }}" method="POST">
                    @csrf
                    <div class="modal-body text-left">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold">{{ __('Discount Group Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Regular VIP (10%)" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">{{ __('Discount Type') }} <span class="text-danger">*</span></label>
                            <select name="type" class="form-control" required>
                                <option value="percentage">{{ __('Percentage (%)') }}</option>
                                <option value="fixed">{{ __('Fixed Amount (Tk)') }}</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">{{ __('Discount Value / Rate') }} <span class="text-danger">*</span></label>
                            <input type="number" step="any" min="0" name="value" class="form-control" placeholder="e.g. 10 for 10% or 100 for 100 Tk" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold">{{ __('Status') }}</label>
                            <select name="status" class="form-control">
                                <option value="1">{{ __('Active') }}</option>
                                <option value="0">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('Save Discount Group') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
