@extends('backend.layouts.master')
@section('section-title', __('Account'))
@section('page-title', __('Bank Account'))
@if (check_permission('bank-account.create'))
    @section('action-button')
        <a href="#" data-toggle="modal" data-target="#addModal" class="btn add_list_btn">
            <i class="mr-2 feather icon-plus"></i>
            {{ __('Add Bank Account') }}
        </a>
    @endsection
@endif


@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable-buttons" class="table table-striped">
                            <thead class="header_bg">
                                <tr class="text-center">
                                    <th class="header_style_left"> {{ __('Bank Name') }} </th>
                                    <th> {{ __('Account Number') }} </th>
                                    {{-- <th> Opening Balance </th> --}}
                                    <th> {{ __('Current Balance') }} </th>
                                    <th> {{ __('Status') }} </th>
                                    <th class="header_style_right"> {{ __('Action') }} </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bank_accounts as $data)
                                    @php
                                        
                                        $count_bank = App\Models\BankTransaction::where('bank_id', $data->id)->count();
                                        $count_tra = App\Models\Transaction::where('bank_id', $data->id)->count();
                                        $current = 0;
                                        $current += current_balance($data->id);
                                        
                                    @endphp
                                    <tr class="text-center">
                                        <td class="table_data_style_left">{{ $data->bank_name }}</td>
                                        <td>{{ $data->account_number }}</td>
                                        {{-- <td>{{ number_format($data->opening_balance,2) }}</td> --}}
                                        <td>{{ current_balance($data->id) > 0 ? number_format(current_balance($data->id),2) : number_format(current_balance($data->id),2) }}</td>
                                        <td>
                                            <input type="checkbox" data-toggle="toggle" data-on="{{ __('Active') }}"
                                                class="status-update" {{ $data->status == 1 ? 'checked' : '' }}
                                                data-off="{{ __('Inactive') }}" data-onstyle="success" data-offstyle="danger"
                                                {{ $data->id == 2 ? 'disabled' : '' }}
                                                data-id="{{ $data->id }}" data-model="BankAccount">
                                        </td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    @if (check_permission('bank-account.edit'))
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#editModal-{{ $data->id }}"
                                                            class="dropdown-item text-primary">
                                                            <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                        </a>
                                                    @endif

                                                    @if (check_permission('bank-account.destroy'))
                                                        @if ($count_bank<1 && $count_tra<1)
                                                            @if ($data->id != 1)
                                                            <a href="#" data-toggle="modal"
                                                                data-target="#deleteModal-{{ $data->id }}"
                                                                class="dropdown-item text-danger">
                                                                <i class="feather icon-trash"></i> {{ __('Delete') }}
                                                            </a>
                                                            @endif
                                                        @endif
                                                    @endif

                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center no_data_style text-danger">{{ __('No Data Available') }}</td>
                                    </tr>
                                @endforelse
                                
                            </tbody>
                        </table>
                    </div>

                    {{--  Loop through again to render modals  outside table for better layout --}}
                    @foreach ($bank_accounts as $data)
                        {{-- edit modal  --}}
                        <form action="{{ route('bank-account.update', $data->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <x-edit-modal title="{{ __('Edit Bank Account') }}" id="{{ $data->id }}">
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Bank Name *') }}</label>
                                    <input type="text" class="form-control" name="bank_name" value="{{ $data->bank_name }}" required placeholder="{{ __('Enter Bank Name') }}">
                                </div>
                                <div class="mb-3 col-md-12 text-left">
                                    <label class="form-label font-weight-bold">{{ __('Account Number *') }}</label>
                                    <input type="text" class="form-control" name="account_number" value="{{ $data->account_number }}" required placeholder="{{ __('Enter Account Number') }}">
                                </div>
                            </x-edit-modal>
                        </form>

                        {{-- delete modal --}}
                        <form action="{{ route('bank-account.destroy', $data->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <x-delete-modal title="{{ __('Bank Account') }}" id="{{ $data->id }}" />
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('bank-account.store') }}" method="POST">
        @csrf
        <x-add-modal title="{{ __('Add Bank Account') }}">
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Bank Name *') }}</label>
                <input type="text" class="form-control" name="bank_name" placeholder="{{ __('Enter Bank Name') }}" required>
            </div>
            <div class="mb-3 col-md-12 text-left">
                <label class="form-label font-weight-bold">{{ __('Account Number *') }}</label>
                <input type="text" class="form-control" name="account_number" placeholder="{{ __('Enter Account Number') }}" required>
            </div>
        </x-add-modal>
    </form>
@endsection
