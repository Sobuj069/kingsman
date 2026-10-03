@extends('backend.layouts.master')
@section('section-title', __('Warranty Management'))
@section('page-title', __('Supplier Claim List'))

@section('action-button')
    <a href="{{ route('supplier-claim.create') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-plus"></i>
        {{ __('Add Claim To Supplier') }}
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
                                    <th>{{ __('S.Claim No') }}</th>
                                    <th>{{ __('Supplier') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="header_style_right text-center">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($claims as $data)
                                    <tr>
                                        <td class="table_data_style_left">{{ ($claims->currentPage() - 1) * $claims->perPage() + $loop->iteration }}</td>
                                        <td>{{ $data->claim_no }}</td>
                                        <td>{{ $data->supplier ? $data->supplier->name : '' }}</td>
                                        <td>{{ $data->date }}</td>
                                        <td><span class="badge badge-info">{{ $data->status }}</span></td>
                                        <td class="table_data_style_right text-center">
                                            <a href="#" class="btn add_list_btn btn-sm"><i class="feather icon-printer"></i></a>
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
