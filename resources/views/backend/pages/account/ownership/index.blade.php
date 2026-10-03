@extends('backend.layouts.master')
@section('section-title', __('Ownership'))
@section('page-title', __('Ownership List'))
@if (check_permission('ownership.store'))
    @section('action-button')
        <a href="#" data-toggle="modal" data-target="#addModal" class="btn add_list_btn">
            <i class="mr-2 feather icon-plus"></i>
            {{ __('Add Owner') }}
        </a>
    @endsection
@endif
@push('css')
<style>
    @media print{
        table,table th,table td {
            color:black !important;
        }

        .h-hide {
            display: none;
        }
    }
</style>
@endpush
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <div class="table-responsive mt-3">
                        <table id="datatable-buttons" class="table table-striped text-center">
                            <thead class="header_bg">
                                <tr>
                                    <th class="header_style_left">{{ __('#SL') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Deposit') }}</th>
                                    <th>{{ __('Withdraw') }}</th>
                                    <th>{{ __('Balance') }}</th>
                                    <th class="header_style_right">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ownerships as $data)
                                    @php
                                        $deposit = App\Models\BankTransaction::where('owner_id',$data->id)->where('pay_type','ownpay')->sum('amount');
                                        $withdraw = App\Models\BankTransaction::where('owner_id',$data->id)->where('pay_type','ownwith')->sum('amount');
                                        $balance = $deposit - $withdraw;
                                    @endphp
                                    <tr>
                                        <td class="table_data_style_left">{{ $loop->index + 1 }}</td>
                                        <td>{{ $data->name }}</td>
                                        <td class="font-weight-bold">{{ $data->phone }}</td>
                                        <td class="font-weight-bold">{{ ($data->email == Null)?'NULL':$data->email; }}</td>
                                        <td class="font-weight-bold">{{ $deposit }} </td>
                                        <td class="font-weight-bold">{{ $withdraw }} </td>
                                        <td class="font-weight-bold">{{ $balance }} </td>
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    {{-- @if (check_permission('ownership.show')) --}}
                                                        <a href="{{ route('ownership.show',$data->id) }}"
                                                            class="dropdown-item text-primary">
                                                            <i class="feather icon-list"></i> {{ __('Ledger') }}
                                                        </a>
                                                    {{-- @endif --}}
                                                    @if (check_permission('ownership.update'))
                                                        <a href="#" data-toggle="modal"
                                                            data-target="#editModal-{{ $data->id }}"
                                                            class="dropdown-item text-primary">
                                                            <i class="feather icon-edit"></i> {{ __('Edit') }}
                                                        </a>
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

                    {{--  Loop through again to render modals outside table for better layout --}}
                    @foreach ($ownerships as $data)
                        {{-- edit modal  --}}
                        <form action="{{ route('ownership.update', $data->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <x-edit-modal title="{{ __('Edit Owner') }}" id="{{ $data->id }}">
                                <x-input label="{{ __('Name *') }}" type="text" name="name" placeholder="{{ __('Enter Name') }}" required md="12" value="{{ $data->name }}" />
                                <x-input label="{{ __('Email') }}" type="email" name="email" placeholder="{{ __('Enter Email') }}" md="12" value="{{ $data->email }}" />
                                <x-input label="{{ __('Phone *') }}" type="text" name="phone" placeholder="{{ __('Enter Phone') }}" required md="12" value="{{ $data->phone }}" />
                                <x-input label="{{ __('Address') }}" type="text" name="address" placeholder="{{ __('Enter Address') }}" md="12" value="{{ $data->address }}" />
                            </x-edit-modal>
                        </form>
                    @endforeach

                </div>
            </div>
        </div>
    </div>

    {{-- Add Modal --}}
    <form action="{{ route('ownership.store') }}" method="POST">
        @csrf
        <x-add-modal title="{{ __('Add Ownership') }}">
            <x-input label="{{ __('Owner Name *') }}" type="text" name="name" placeholder="{{ __('Enter Owner Name') }}" required
                md="12" />
            <x-input label="{{ __('Email') }}" type="email" name="email" placeholder="{{ __('Enter Email') }}" md="12" />
            <x-input label="{{ __('Phone *') }}" type="text" name="phone" placeholder="{{ __('Enter Phone') }}" required md="12" />
            <x-input label="{{ __('Address') }}" type="text" name="address" placeholder="{{ __('Enter Address') }}" md="12" />
            <x-select label="{{ __('Bank Account') }}" name="bank_id" md="12">
                @foreach ($bank_accounts as $bank_account_item)
                    <option value="{{ $bank_account_item->id }}"> {{ $bank_account_item->bank_name }}</option>
                @endforeach
            </x-select>
            <x-input label="{{ __('Deposit') }}" type="text" name="deposit" value="0" md="12" />
        </x-add-modal>
    </form>

@endsection
