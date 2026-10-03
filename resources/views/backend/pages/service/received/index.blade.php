@extends('backend.layouts.master')
@section('section-title', __('Service Management'))
@section('page-title', __('Service Received List'))

@section('action-button')
    <a href="{{ route('service-receive.create') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-plus"></i>
        {{ __('Add Service Received') }}
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <form action="{{ route('service-receive.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3 mt-3">
                                <input type="text" class="form-control barcode-filter-input" data-barcode-input name="barcode" placeholder="{{ __('Scan Barcode / Service No') }}" value="{{ $barcode ?? request('service_no') }}" />
                            </div>
                            <div class="col-md-3 mt-3">
                                <select name="customer_id" class="select2">
                                    <option value="">{{ __('Select Customer') }}</option>
                                    @foreach ($allCustomer as $item)
                                        <option value="{{ $item->id }}" {{ request('customer_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mt-3">
                                <select name="pname" class="select2">
                                    <option value="">{{ __('Select Service/Product') }}</option>
                                    @foreach ($allProducts as $item)
                                        <option value="{{ $item->name }}" {{ request('pname') == $item->name ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mt-3">
                                <select name="status" class="form-control">
                                    <option value="">{{ __('Select Status') }}</option>
                                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                    <option value="In Progress" {{ request('status') == 'In Progress' ? 'selected' : '' }}>{{ __('In Progress') }}</option>
                                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                    <option value="Delivered" {{ request('status') == 'Delivered' ? 'selected' : '' }}>{{ __('Delivered') }}</option>
                                </select>
                            </div>
                            <div class="col-md-4 mt-3">
                                <input type="date" class="form-control" name="start_date" title="{{ __('Start Date') }}" value="{{ request('start_date') }}" />
                            </div>
                            <div class="col-md-4 mt-3">
                                <input type="date" class="form-control" name="end_date" title="{{ __('End Date') }}" value="{{ request('end_date') }}" />
                            </div>
                            <div class="col-md-4 mt-3">
                                <button type="submit" class="btn add_list_btn">{{ __('Filter') }}</button>
                                <a href="{{ route('service-receive.index') }}" class="btn add_list_btn_reset">{{ __('Reset') }}</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive mt-3">
                        <table class="table table-striped">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th>{{ __('Service No') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Model') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Received Date') }}</th>
                                    <th class="header_style_right text-center">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($services as $data)
                                    <tr>
                                        <td class="table_data_style_left">{{ ($services->currentPage() - 1) * $services->perPage() + $loop->iteration }}</td>
                                        <td>{{ $data->service_no }}</td>
                                        <td>{{ $data->customer ? $data->customer->name : $data->cname }}</td>
                                        <td>{{ $data->customer ? $data->customer->phone : $data->cphone }}</td>
                                        <td>{{ $data->pname }}</td>
                                        <td>{{ $data->pmodel }}</td>
                                        <td>
                                            @php
                                                $badgeClass = 'badge-warning';
                                                if($data->status == 'Completed') $badgeClass = 'badge-success';
                                                if($data->status == 'Delivered') $badgeClass = 'badge-primary';
                                                if($data->status == 'In Progress') $badgeClass = 'badge-info';
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
                                                    <a href="{{ route('service-receive.edit', $data->id) }}" class="dropdown-item text-primary">
                                                        <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                    </a>
                                                    @if($data->status != 'Delivered')
                                                    <a href="{{ route('service-invoice.create', ['receive_id' => $data->id]) }}" class="dropdown-item text-success">
                                                        <i class="feather icon-file-text"></i> {{ __('Create Invoice') }}
                                                    </a>
                                                    @endif
                                                    <form action="{{ route('service-receive.destroy', $data->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
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
                        {{ $services->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
