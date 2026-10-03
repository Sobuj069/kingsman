@extends('backend.layouts.master')
@section('section-title', __('Warranty Management'))
@section('page-title', __('Service Center List'))

@section('action-button')
    <a href="{{ route('service-center.create') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-plus"></i>
        {{ __('Add Service Center') }}
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
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Mobile') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Address') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="header_style_right text-center">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($centers as $data)
                                    <tr>
                                        <td class="table_data_style_left">{{ ($centers->currentPage() - 1) * $centers->perPage() + $loop->iteration }}</td>
                                        <td>{{ $data->name }}</td>
                                        <td>{{ $data->mobile }}</td>
                                        <td>{{ $data->email }}</td>
                                        <td>{{ $data->address }}</td>
                                        <td>
                                            <span class="badge {{ $data->status == 1 ? 'badge-success' : 'badge-danger' }}">
                                                {{ $data->status == 1 ? __('Active') : __('Inactive') }}
                                            </span>
                                        </td>
                                        <td class="table_data_style_right text-center">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a href="{{ route('service-center.edit', $data->id) }}" class="dropdown-item text-primary">
                                                        <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                    </a>
                                                    <form action="{{ route('service-center.destroy', $data->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
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
                        {{ $centers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
