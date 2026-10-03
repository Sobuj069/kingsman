@extends('backend.layouts.master')
@section('section-title', __('Warranty Management'))
@section('page-title', __('Warranty List / Types'))

@section('action-button')
    <button type="button" class="btn add_list_btn" data-toggle="modal" data-target="#addWarrantyModal">
        <i class="feather icon-plus mr-1"></i> {{ __('Add Warranty') }}
    </button>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card card_style m-b-30">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ __('Warranty Presets & Types') }}</h5>
                </div>
                <div class="card-body">
                    <!-- Search Filter -->
                    <form action="{{ route('warranty.index') }}" method="GET" class="mb-3">
                        <div class="form-row align-items-end">
                            <div class="col-md-4 mb-2">
                                <label class="small text-muted font-weight-bold">{{ __('Search Warranty') }}</label>
                                <input type="text" name="search" class="form-control" placeholder="{{ __('Search by name, period or description...') }}" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="small text-muted font-weight-bold">{{ __('Filter Period') }}</label>
                                <select name="period" class="form-control select2">
                                    <option value="">{{ __('All Periods') }}</option>
                                    <option value="Day" {{ request('period') == 'Day' ? 'selected' : '' }}>{{ __('Day') }}</option>
                                    <option value="Month" {{ request('period') == 'Month' ? 'selected' : '' }}>{{ __('Month') }}</option>
                                    <option value="Year" {{ request('period') == 'Year' ? 'selected' : '' }}>{{ __('Year') }}</option>
                                    <option value="Lifetime" {{ request('period') == 'Lifetime' ? 'selected' : '' }}>{{ __('Lifetime') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <button type="submit" class="btn add_list_btn mr-1"><i class="fa fa-filter mr-1"></i> {{ __('Filter') }}</button>
                                <a href="{{ route('warranty.index') }}" class="btn add_list_btn_reset"><i class="feather icon-refresh-cw"></i></a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped text-center">
                            <thead>
                                <tr style="background: #1e293b; color: white;">
                                    <th style="width: 50px;">#SL</th>
                                    <th>{{ __('Warranty Name') }}</th>
                                    <th>{{ __('Duration') }}</th>
                                    <th>{{ __('Period / Unit') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th style="width: 120px;">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($warranties as $key => $warranty)
                                    <tr>
                                        <td>{{ $warranties->firstItem() + $key }}</td>
                                        <td class="font-weight-bold text-primary">{{ $warranty->name }}</td>
                                        <td>
                                            @if($warranty->period === 'Lifetime')
                                                <span class="badge badge-info px-2 py-1">{{ __('Lifetime') }}</span>
                                            @else
                                                <strong>{{ $warranty->duration }}</strong>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary px-2 py-1">{{ $warranty->period }}</span>
                                        </td>
                                        <td class="text-left">{{ $warranty->description ?? '-' }}</td>
                                        <td>
                                            @if($warranty->status == 1)
                                                <span class="badge badge-success px-2 py-1">{{ __('Active') }}</span>
                                            @else
                                                <span class="badge badge-danger px-2 py-1">{{ __('Inactive') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info edit-warranty-btn" 
                                                data-id="{{ $warranty->id }}"
                                                data-name="{{ $warranty->name }}"
                                                data-duration="{{ $warranty->duration }}"
                                                data-period="{{ $warranty->period }}"
                                                data-description="{{ $warranty->description }}"
                                                data-status="{{ $warranty->status }}"
                                                title="{{ __('Edit') }}">
                                                <i class="feather icon-edit"></i>
                                            </button>
                                            <form action="{{ route('warranty.destroy', $warranty->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this warranty?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="{{ __('Delete') }}">
                                                    <i class="feather icon-trash-2"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="feather icon-shield" style="font-size: 36px; opacity: 0.5; display: block; margin-bottom: 8px;"></i>
                                            <h5>{{ __('No Warranty presets found') }}</h5>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end mt-2">
                            {{ $warranties->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Warranty Modal -->
    <div class="modal fade" id="addWarrantyModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{ route('warranty.store') }}" method="POST">
                    @csrf
                    <div class="modal-header" style="background: #1e293b; color: white;">
                        <h5 class="modal-title text-white"><i class="feather icon-plus mr-1"></i> {{ __('Add New Warranty') }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('Warranty Name / Title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. 1 Year Warranty, 6 Months Warranty" required>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">{{ __('Duration (Value)') }} <span class="text-danger">*</span></label>
                                <input type="number" name="duration" class="form-control" placeholder="e.g. 1, 6, 12, 24" min="0" value="1" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">{{ __('Period / Unit') }} <span class="text-danger">*</span></label>
                                <select name="period" class="form-control" required>
                                    <option value="Day">{{ __('Day') }}</option>
                                    <option value="Month" selected>{{ __('Month') }}</option>
                                    <option value="Year">{{ __('Year') }}</option>
                                    <option value="Lifetime">{{ __('Lifetime') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('Description / Terms') }}</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Optional notes or warranty conditions"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('Status') }}</label>
                            <select name="status" class="form-control">
                                <option value="1">{{ __('Active') }}</option>
                                <option value="0">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn add_list_btn"><i class="feather icon-check mr-1"></i> {{ __('Save Warranty') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Warranty Modal -->
    <div class="modal fade" id="editWarrantyModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editWarrantyForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header" style="background: #1e293b; color: white;">
                        <h5 class="modal-title text-white"><i class="feather icon-edit mr-1"></i> {{ __('Edit Warranty') }}</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('Warranty Name / Title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="form-row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">{{ __('Duration (Value)') }} <span class="text-danger">*</span></label>
                                <input type="number" name="duration" id="edit_duration" class="form-control" min="0" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">{{ __('Period / Unit') }} <span class="text-danger">*</span></label>
                                <select name="period" id="edit_period" class="form-control" required>
                                    <option value="Day">{{ __('Day') }}</option>
                                    <option value="Month">{{ __('Month') }}</option>
                                    <option value="Year">{{ __('Year') }}</option>
                                    <option value="Lifetime">{{ __('Lifetime') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('Description / Terms') }}</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">{{ __('Status') }}</label>
                            <select name="status" id="edit_status" class="form-control">
                                <option value="1">{{ __('Active') }}</option>
                                <option value="0">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn add_list_btn"><i class="feather icon-check mr-1"></i> {{ __('Update Warranty') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $('.edit-warranty-btn').on('click', function() {
                let id = $(this).data('id');
                let name = $(this).data('name');
                let duration = $(this).data('duration');
                let period = $(this).data('period');
                let description = $(this).data('description');
                let status = $(this).data('status');

                let actionUrl = "{{ route('warranty.update', ':id') }}".replace(':id', id);
                $('#editWarrantyForm').attr('action', actionUrl);

                $('#edit_name').val(name);
                $('#edit_duration').val(duration);
                $('#edit_period').val(period);
                $('#edit_description').val(description);
                $('#edit_status').val(status);

                $('#editWarrantyModal').modal('show');
            });
        });
    </script>
@endpush
