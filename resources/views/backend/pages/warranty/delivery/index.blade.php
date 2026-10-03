@extends('backend.layouts.master')
@section('section-title', __('Warranty Management'))
@section('page-title', __('Warranty Delivered List'))

@section('action-button')
    <a href="{{ route('warranty-delivery.create') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-plus"></i>
        {{ __('Add Warranty Delivery') }}
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th>{{ __('Claim No') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Product (New/Fixed)') }}</th>
                                    <th>{{ __('New Serial/IMEI') }}</th>
                                    <th>{{ __('Delivery Date') }}</th>
                                    <th class="header_style_right text-center">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deliveries as $data)
                                    <tr>
                                        <td class="table_data_style_left">{{ ($deliveries->currentPage() - 1) * $deliveries->perPage() + $loop->iteration }}</td>
                                        <td>{{ $data->claim ? $data->claim->claim_no : '' }}</td>
                                        <td>{{ ($data->claim && $data->claim->customer) ? $data->claim->customer->name : __('Walking Customer') }}</td>
                                        <td>{{ $data->product ? $data->product->name : '' }}</td>
                                        <td>{{ $data->delivered_serial }}</td>
                                        <td>{{ $data->delivered_date }}</td>
                                        <td class="table_data_style_right text-center">
                                            <a href="{{ route('warranty-delivery.show', $data->id) }}" target="_blank" class="btn add_list_btn btn-sm"><i class="feather icon-printer"></i></a>
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
                </div>
            </div>
        </div>
    </div>
@endsection
