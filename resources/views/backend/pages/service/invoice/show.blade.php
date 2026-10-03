@extends('backend.layouts.master')
@section('section-title', __('Service Management'))
@section('page-title', __('Service Invoice Details'))

@section('action-button')
    <a href="{{ route('service-invoice.index') }}" class="btn add_list_btn">
        <i class="mr-2 feather icon-list"></i>
        {{ __('Back to List') }}
    </a>
    <button onclick="window.print()" class="btn add_list_btn">
        <i class="mr-2 feather icon-printer"></i>
        {{ __('Print') }}
    </button>
@endsection

@section('content')
    <div class="row print_area">
        <div class="col-lg-12">
            <div class="card card_style">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h5>{{ __('Invoice To:') }}</h5>
                            <p>
                                <strong>{{ $invoice->customer ? $invoice->customer->name : __('Walking Customer') }}</strong><br>
                                {{ $invoice->customer ? $invoice->customer->phone : '' }}<br>
                                {{ $invoice->customer ? $invoice->customer->address : '' }}
                            </p>
                        </div>
                        <div class="col-6 text-right">
                            <h3>{{ __('INVOICE') }}</h3>
                            <p>
                                <strong>{{ __('Invoice No:') }}</strong> {{ $invoice->invoice_no }}<br>
                                <strong>{{ __('Date:') }}</strong> {{ $invoice->date }}<br>
                                @if($invoice->receive)
                                    <strong>{{ __('Service No:') }}</strong> {{ $invoice->receive->service_no }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="table-responsive mt-4">
                        <table class="table table-bordered">
                            <thead class="header_bg">
                                <tr>
                                    <th>{{ __('Service/Product') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Price') }}</th>
                                    <th>{{ __('Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->items as $item)
                                    <tr>
                                        <td>{{ $item->product ? $item->product->name : __('Deleted Product') }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ number_format($item->price, 2) }}</td>
                                        <td>{{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right">{{ __('Total Amount') }}</th>
                                    <th>{{ number_format($invoice->total_amount, 2) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-right">{{ __('Discount') }}</th>
                                    <th>{{ number_format($invoice->discount, 2) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-right">{{ __('Net Amount') }}</th>
                                    <th>{{ number_format($invoice->net_amount, 2) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-right">{{ __('Paid Amount') }}</th>
                                    <th>{{ number_format($invoice->paid_amount, 2) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="3" class="text-right">{{ __('Due Amount') }}</th>
                                    <th>{{ number_format($invoice->due_amount, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
<style>
    @media print {
        .navbar-header, .modern-sidebar, .action-button, .breadcrumb, footer {
            display: none !important;
        }
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .rightbar {
            margin-left: 0 !important;
        }
    }
</style>
@endpush
