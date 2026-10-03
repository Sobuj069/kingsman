@extends('backend.layouts.master')
@section('section-title', __('Used Product'))
@section('page-title', __('Buy List'))
@if (check_permission('usedPurchase.create'))
    @section('action-button')
        <a href="{{ route('usedPurchase.create') }}" class="btn add_list_btn">
            <i class="mr-2 feather icon-plus"></i>
            {{ __('New Buy') }}
        </a>
    @endsection
@endif
@push('css')
    <style>
        @media print {

            table,
            table th,
            table td {
                color: black !important;
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
                    <form action="{{ route('usedPurchase.index') }}" method="GET">
                        @php
                            $suppliers = App\Models\Supplier::get();
                            $products = App\Models\UsedProduct::get();
                        @endphp
                        <div class="row h-hide">
                            <div class="col-md-3 mt-2">
                                <input type="date" class="form-control" name="startDate" value="{{ $startDate }}" />
                            </div>
                            <div class="col-md-3 mt-2">
                                <input type="date" class="form-control" name="endDate" value="{{ $endDate }}" />
                            </div>
                             <div class="col-md-3 mt-2">
                                <input type="text" placeholder="{{ __('Scan Barcode / Buy No') }}" value="{{ $barcode ?? ($purchase_no ?? '') }}"
                                    name="barcode" class="form-control barcode-filter-input" data-barcode-input>
                            </div>
                            <div class="col-md-3 col-12 mt-2">
                                <select name="product_id" id="" class="select2">
                                    <option value="">{{ __('Select Product') }}</option>
                                    @foreach ($products as $item)
                                        <option
                                            value="{{ $item->id }}"{{ $product_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mt-2">
                                <select name="supplier_id" id="" class="select2">
                                    <option value="">{{ __('Select Supplier') }}</option>
                                    @foreach ($suppliers as $item)
                                        <option
                                            value="{{ $item->id }}"{{ $supplier_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3 h-hide d-flex justify-content-between">
                            <div class="col-md-12 col-12">
                                <button type="submit" class="btn add_list_btn">{{ __('Filter') }}</button>
                                <a href="{{ route('usedPurchase.index') }}" class="btn add_list_btn_reset">{{ __('Reset') }}</a>
                                <a href="" class="btn add_list_btn float-right" onclick="window.print()">{{ __('Print') }}</a>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive mt-3">
                        <table id="datatable-buttons" class="table table-striped">
                            <thead class="header_bg">
                                <tr class="text-center">
                                    <th class="header_style_left"> {{ __('Buy Date') }} </th>
                                    <th> {{ __('Buy No') }} </th>
                                    <th> {{ __('Product') }} </th>
                                    <th> {{ __('Supplier') }} </th>
                                    <th> {{ __('Creator') }} </th>
                                    <th> {{ __('Total Amount') }} </th>
                                    <th class="header_style_right"> {{ __('Action') }} </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total_paid = 0;
                                    $total_amt = 0;
                                    $total_due = 0;
                                @endphp
                                @forelse($purchases as $data)
                                    @php
                                        $total_paid += $data->total_paid;
                                        $total_amt += $data->total_amount;
                                        $total_due += $data->total_due;
                                        $purchase_item = App\Models\UsedPurchaseItem::where(
                                            'purchase_id',
                                            $data->id,
                                        )->get();
                                    @endphp
                                    <tr class="text-center">
                                        <td class="table_data_style_left">{{ $data->date }}</td>
                                        <td>{{ $data->purchase_no }}</td>
                                        {{-- <td>{{ $purchase_item?->product?->name }}</td> --}}
                                        <td>
                                            @foreach ($purchase_item as $item)
                                                <ul>
                                                    <li>
                                                        {{ $item->product?->name }}
                                                    </li>
                                                </ul>
                                            @endforeach
                                        </td>
                                        <td>{{ $data->supplier->name }}</td>
                                        <td>{{ $data->user->name }}</td>
                                        <td>{{ $data->total_amount }}</td>
                                        {{-- <td>{{ $data->total_paid }}</td>
                                        <td>{{ $data->total_due }}</td>
                                        <td>
                                            @if ($data->status == 0)
                                                <span class="badge badge-warning">Due</span>
                                            @elseif($data->status == 1)
                                                <span class="badge badge-success">Paid</span>
                                            @elseif($data->status == 2)
                                                <span class="badge badge-danger">Returned</span>
                                            @endif
                                        </td> --}}
                                        <td class="table_data_style_right">
                                            <div class="dropdown">
                                                <button class="btn add_list_btn btn-sm dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown">
                                                    {{ __('Action') }}
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    {{-- pay button  --}}
                                                    @if (check_permission('purchase.pay'))
                                                        @if ($data->total_due > 0)
                                                            <a class="dropdown-item text-success" href="{{ url('purchase/pay/' . $data->id) }}">
                                                                <i class="feather icon-dollar-sign"></i> {{ __('Due') }}
                                                            </a>
                                                        @endif
                                                    @endif
                                                    @if (check_permission('purchase.edit'))
                                                        <a class="dropdown-item text-info" href="{{ url('purchase/edit/' . $data->id) }}">
                                                            <i class="fa fa-edit"></i> {{ __('Edit') }}
                                                        </a>
                                                    @endif
                                                    {{-- print --}}
                                                    <a class="dropdown-item text-success" href="{{ route('purchase.print', $data->id) }}">
                                                        <i class="feather icon-printer"></i> {{ __('Print') }}
                                                    </a>

                                                    {{-- delete --}}
                                                    @if (check_permission('purchase.destroy'))
                                                        <a class="dropdown-item text-danger" href="#" data-toggle="modal"
                                                            data-target="#deleteModal-{{ $data->id }}">
                                                            <i class="feather icon-trash"></i> {{ __('Delete') }}
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- delete modal --}}
                                    <form action="{{ route('purchase.destroy', $data->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-delete-modal title="{{ __('Purchase') }}" id="{{ $data->id }}" />
                                    </form>
                                @empty
                                    <tr>
                                        <td colspan="12" class="text-center no_data_style">{{ __('No Purchase Found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="text-dark">
                                <tr class="text-dark text-right">
                                    <td colspan="5"><strong>{{ __('Total') }}: </strong></td>
                                    <td> <strong>{{ number_format($total_amt,2) }} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</strong></td>
                                    {{-- <td> <strong>{{ number_format($total_paid,2) }} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</strong></td>
                                    <td> <strong>{{ number_format($total_due,2) }} {{ empty(get_setting('com_currency')) ? : get_setting('com_currency') }}</strong></td> --}}
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                        {{ $purchases->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
