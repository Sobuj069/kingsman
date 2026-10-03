@extends('backend.layouts.master')
@section('section-title', __('Service Management'))
@section('page-title', __('Service Invoice List'))

@section('action-button')
    <a href="{{ route('service-invoice.create') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-plus"></i>
        {{ __('Add Service Invoice') }}
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <form action="{{ route('service-invoice.index') }}" method="GET">
                        <div class="row">
                            <div class="col-md-2 mt-3">
                                <input type="text" class="form-control" name="invoice_no" placeholder="{{ __('Invoice No') }}" value="{{ request('invoice_no') }}" />
                            </div>
                            <div class="col-md-2 mt-3">
                                <select name="customer_id" class="select2">
                                    <option value="">{{ __('Select Customer') }}</option>
                                    @foreach ($allCustomer as $item)
                                        <option value="{{ $item->id }}" {{ request('customer_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mt-3">
                                <select name="product_id" class="select2">
                                    <option value="">{{ __('Select Service/Product') }}</option>
                                    @foreach ($allProducts as $item)
                                        <option value="{{ $item->id }}" {{ request('product_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mt-3">
                                <input type="date" class="form-control" name="start_date" title="{{ __('Start Date') }}" value="{{ request('start_date') }}" />
                            </div>
                            <div class="col-md-2 mt-3">
                                <input type="date" class="form-control" name="end_date" title="{{ __('End Date') }}" value="{{ request('end_date') }}" />
                            </div>
                            <div class="col-md-2 mt-3">
                                <button type="submit" class="btn add_list_btn">{{ __('Filter') }}</button>
                                <a href="{{ route('service-invoice.index') }}" class="btn add_list_btn_reset">{{ __('Reset') }}</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive mt-3">
                        <table class="table table-striped">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th>{{ __('Invoice No') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Total Amount') }}</th>
                                    <th>{{ __('Paid') }}</th>
                                    <th>{{ __('Due') }}</th>
                                    <th class="header_style_right text-center">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $data)
                                    <tr>
                                        <td class="table_data_style_left">{{ ($invoices->currentPage() - 1) * $invoices->perPage() + $loop->iteration }}</td>
                                        <td>{{ $data->invoice_no }}</td>
                                        <td>{{ $data->date }}</td>
                                        <td>{{ $data->customer ? $data->customer->name : __('Walking Customer') }}</td>
                                        <td>{{ number_format($data->net_amount, 2) }}</td>
                                        <td>{{ number_format($data->paid_amount, 2) }}</td>
                                        <td>{{ number_format($data->due_amount, 2) }}</td>
                                        <td class="table_data_style_right text-center">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a href="{{ route('service-invoice.show', $data->id) }}" class="dropdown-item text-primary">
                                                        <i class="feather icon-eye"></i> {{ __('View/Print') }}
                                                    </a>
                                                    @if(check_permission('service-invoice.destroy'))
                                                    <form action="{{ route('service-invoice.destroy', $data->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this service invoice?') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" style="border: none; background: none; width: 100%; text-align: left;">
                                                            <i class="feather icon-trash"></i> {{ __('Delete') }}
                                                        </button>
                                                    </form>
                                                    @endif
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
                        {{ $invoices->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
