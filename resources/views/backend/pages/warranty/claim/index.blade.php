@extends('backend.layouts.master')
@section('section-title', __('Warranty Management'))
@section('page-title', __('Warranty Claim List'))

@section('action-button')
    <a href="{{ route('warranty-claim.create') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-plus"></i>
        {{ __('Add Warranty Claim') }}
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <form action="{{ route('warranty-claim.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3 mt-3">
                                <input type="text" class="form-control barcode-filter-input" data-barcode-input name="barcode" placeholder="{{ __('Scan Barcode / Claim No') }}" value="{{ $barcode ?? request('claim_no') }}" />
                            </div>
                            <div class="col-md-3 mt-3">
                                <input type="text" class="form-control" name="serial_no" placeholder="{{ __('Serial/IMEI') }}" value="{{ request('serial_no') }}" />
                            </div>
                            <div class="col-md-3 mt-3">
                                <select name="status" class="form-control">
                                    <option value="">{{ __('Select Status') }}</option>
                                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                    <option value="Checked" {{ request('status') == 'Checked' ? 'selected' : '' }}>{{ __('Checked') }}</option>
                                    <option value="Sent to Supplier" {{ request('status') == 'Sent to Supplier' ? 'selected' : '' }}>{{ __('Sent to Supplier') }}</option>
                                    <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3 mt-3">
                                <button type="submit" class="btn add_list_btn">{{ __('Filter') }}</button>
                                <a href="{{ route('warranty-claim.index') }}" class="btn add_list_btn_reset">{{ __('Reset') }}</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive mt-3">
                        <table class="table table-striped">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th>{{ __('Claim No') }}</th>
                                    <th>{{ __('Serial/IMEI') }}</th>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Received Date') }}</th>
                                    <th class="header_style_right text-center">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($claims as $data)
                                    <tr>
                                        <td class="table_data_style_left">{{ ($claims->currentPage() - 1) * $claims->perPage() + $loop->iteration }}</td>
                                        <td>{{ $data->claim_no }}</td>
                                        <td>{{ $data->serial_no }}</td>
                                        <td>{{ $data->product_name }}</td>
                                        <td>{{ $data->customer ? $data->customer->name : __('Walking Customer') }}</td>
                                        <td>
                                            @php
                                                $badgeClass = 'badge-warning';
                                                if($data->status == 'Delivered') $badgeClass = 'badge-success';
                                                if($data->status == 'Checked') $badgeClass = 'badge-info';
                                                if($data->status == 'Sent to Supplier') $badgeClass = 'badge-primary';
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $data->status }}</span>
                                        </td>
                                        <td>{{ $data->received_date }}</td>
                                        <td class="table_data_style_right text-center">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    @if($data->status !== 'Delivered')
                                                        <a href="{{ route('warranty-delivery.create', ['claim_id' => $data->id]) }}" class="dropdown-item text-success">
                                                            <i class="feather icon-navigation"></i> {{ __('Deliver Product') }}
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('warranty-claim.check', $data->id) }}" class="dropdown-item text-info">
                                                        <i class="feather icon-check-square"></i> {{ __('Check Device') }}
                                                    </a>
                                                    <a href="{{ route('warranty-claim.edit', $data->id) }}" class="dropdown-item text-primary">
                                                        <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                    </a>
                                                    <form action="{{ route('warranty-claim.destroy', $data->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="feather icon-trash"></i> {{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center text-danger no_data_style">{{ __('No Data Available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="pagination justify-content-center mt-3">
                        {{ $claims->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
