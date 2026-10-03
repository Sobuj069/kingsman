@extends('backend.layouts.master')
@section('section-title', __('Expense & Asset'))
@section('page-title', __('Asset List'))

@section('action-button')
    <a href="{{ route('asset.create') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-plus"></i>
        {{ __('Add Asset') }}
    </a>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <div class="row mb-3 align-items-center">
                        <div class="col-md-6">
                            <h5 class="text-white font-weight-bold mb-0">{{ __('Asset Inventory') }}</h5>
                        </div>
                        <div class="col-md-6 text-md-right">
                            <span class="badge badge-info p-2" style="font-size: 14px;">
                                {{ __('Total Asset Value') }}: {{ number_format($totalCost, 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="datatable-buttons" class="table table-striped text-center">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th>{{ __('Purchase Date') }}</th>
                                    <th>{{ __('Asset Name') }}</th>
                                    <th>{{ __('Cost') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Total Cost') }}</th>
                                    <th>{{ __('Bank Account') }}</th>
                                    <th>{{ __('Notes') }}</th>
                                    <th class="header_style_right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assets as $data)
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>{{ $data->purchase_date }}</td>
                                        <td class="text-left font-weight-bold" style="padding-left: 20px;">{{ $data->name }}</td>
                                        <td>{{ number_format($data->purchase_cost, 2) }}</td>
                                        <td>{{ $data->quantity }}</td>
                                        <td class="font-weight-bold text-orange-400">{{ number_format($data->purchase_cost * $data->quantity, 2) }}</td>
                                        <td>{{ $data->bank_account ? $data->bank_account->bank_name : '-' }}</td>
                                        <td>{{ $data->note ?? '-' }}</td>
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
                                        <td colspan="100%" class="text-center no_data_style text-danger">{{ __('No Assets Registered') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @foreach ($assets as $data)
                        {{-- delete modal --}}
                        <form action="{{ route('asset.destroy', $data->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <x-delete-modal title="{{ __('Asset Record') }}" id="{{ $data->id }}" />
                        </form>
                    @endforeach

                    <div class="pagination justify-content-center mt-3">
                        {{ $assets->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
