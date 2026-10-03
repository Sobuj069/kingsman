@extends('backend.layouts.master')
@section('section-title', __('Marketing'))
@section('page-title', __('Ad Cost List'))

@section('action-button')
    <a href="{{ route('ad-cost.create') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-plus"></i>
        {{ __('Add Ad Cost') }}
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
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Platform') }}</th>
                                    <th>{{ __('Product') }}</th>
                                    <th>{{ __('Campaign Name') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Notes') }}</th>
                                    <th class="header_style_right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($adCosts as $data)
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>{{ $data->date }}</td>
                                        <td>{{ $data->platform ? $data->platform->name : __('No Platform') }}</td>
                                        <td>{{ $data->product ? $data->product->name : __('No Product') }}</td>
                                        <td>{{ $data->campaign_name ?? '-' }}</td>
                                        <td>{{ number_format($data->amount, 2) }}</td>
                                        <td>{{ $data->notes ?? '-' }}</td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    {{-- delete --}}
                                                    <a href="#" data-toggle="modal"
                                                        data-target="#deleteModal-{{ $data->id }}"
                                                        class="dropdown-item text-danger">
                                                        <i class="feather icon-trash"></i> {{ __('Delete') }}
                                                    </a>
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

                    @foreach ($adCosts as $data)
                        {{-- delete modal --}}
                        <form action="{{ route('ad-cost.destroy', $data->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <x-delete-modal title="{{ __('Ad Cost Record') }}" id="{{ $data->id }}" />
                        </form>
                    @endforeach

                    <div class="pagination justify-content-center">
                        {{ $adCosts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
