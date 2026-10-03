@extends('backend.layouts.master')
@section('section-title', __('Marketing'))
@section('page-title', __('Search Invoice by Courier ID'))

@section('content')
    <div class="row">
        <!-- Search Card -->
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <form method="GET" action="{{ route('courier.tracking') }}" class="row align-items-center">
                        <div class="col-md-9">
                            <label class="form-label font-weight-bold text-white mb-2">{{ __('Enter Courier / Consignment ID *') }}</label>
                            <input type="text" class="form-control" name="consignment_id" value="{{ $consignmentId ?? '' }}" placeholder="{{ __('Enter Steadfast Courier ID...') }}" required>
                        </div>
                        <div class="col-md-3 mt-4 text-md-right">
                            <button type="submit" class="btn add_list_btn px-4 w-100">
                                <i class="feather icon-search mr-2"></i>{{ __('Search & Track') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(isset($consignmentId))
        <div class="row mt-2">
            @if($invoice)
                <!-- Invoice Info Card -->
                <div class="col-md-6">
                    <div class="card m-b-30 card_style h-100">
                        <div class="card-header bg-transparent border-b border-slate-700/50">
                            <h5 class="text-white font-weight-bold mb-0"><i class="feather icon-file-text mr-2 text-orange-400"></i>{{ __('Invoice Details') }}</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless text-white">
                                <tr>
                                    <td class="font-weight-bold text-muted" style="width: 40%;">{{ __('Invoice No') }}:</td>
                                    <td>{{ $invoice->invoice_no }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-muted">{{ __('Invoice Date') }}:</td>
                                    <td>{{ $invoice->date }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-muted">{{ __('Customer Name') }}:</td>
                                    <td>{{ $invoice->customer ? $invoice->customer->name : __('Walk-in Customer') }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-muted">{{ __('Phone') }}:</td>
                                    <td>{{ $invoice->customer ? $invoice->customer->phone : '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-muted">{{ __('Sale Type') }}:</td>
                                    <td><span class="badge badge-info p-2">{{ $invoice->sale_type ?? __('Courier') }}</span></td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold text-muted">{{ __('Total Amount') }}:</td>
                                    <td class="font-weight-bold text-orange-400">{{ number_format($invoice->total_amount, 2) }}</td>
                                </tr>
                            </table>

                            <h6 class="text-white font-weight-bold mt-4 mb-2">{{ __('Products Ordered') }}:</h6>
                            <div class="table-responsive">
                                <table class="table table-striped table-sm text-center">
                                    <thead class="header_bg">
                                        <tr>
                                            <th>{{ __('Product') }}</th>
                                            <th>{{ __('Qty') }}</th>
                                            <th>{{ __('Subtotal') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoice->invoice_items as $item)
                                            <tr>
                                                <td class="text-left" style="padding-left: 10px;">{{ $item->product->name }}</td>
                                                <td>{{ number_format($item->main_qty, 0) }}</td>
                                                <td>{{ number_format($item->subtotal, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Courier Tracking Status Card -->
                <div class="col-md-6">
                    <div class="card m-b-30 card_style h-100">
                        <div class="card-header bg-transparent border-b border-slate-700/50">
                            <h5 class="text-white font-weight-bold mb-0"><i class="feather icon-truck mr-2 text-success"></i>{{ __('Steadfast Courier Status') }}</h5>
                        </div>
                        <div class="card-body">
                            @if(!empty($courierStatus))
                                <div class="text-center mb-4">
                                    <span class="text-muted d-block uppercase font-weight-bold" style="font-size: 11px;">{{ __('Current Status') }}</span>
                                    <h3 class="text-success font-weight-bold mt-1">
                                        {{ $courierStatus['status'] ?? $courierStatus['delivery_status'] ?? __('Unknown') }}
                                    </h3>
                                </div>

                                <table class="table table-borderless text-white">
                                    @if(isset($courierStatus['tracking_code']))
                                        <tr>
                                            <td class="font-weight-bold text-muted" style="width: 40%;">{{ __('Tracking Code') }}:</td>
                                            <td>{{ $courierStatus['tracking_code'] }}</td>
                                        </tr>
                                    @endif
                                    @if(isset($courierStatus['recipient_name']))
                                        <tr>
                                            <td class="font-weight-bold text-muted">{{ __('Recipient Name') }}:</td>
                                            <td>{{ $courierStatus['recipient_name'] }}</td>
                                        </tr>
                                    @endif
                                    @if(isset($courierStatus['recipient_phone']))
                                        <tr>
                                            <td class="font-weight-bold text-muted">{{ __('Recipient Phone') }}:</td>
                                            <td>{{ $courierStatus['recipient_phone'] }}</td>
                                        </tr>
                                    @endif
                                    @if(isset($courierStatus['recipient_address']))
                                        <tr>
                                            <td class="font-weight-bold text-muted">{{ __('Address') }}:</td>
                                            <td>{{ $courierStatus['recipient_address'] }}</td>
                                        </tr>
                                    @endif
                                    @if(isset($courierStatus['cod_amount']))
                                        <tr>
                                            <td class="font-weight-bold text-muted">{{ __('COD Amount') }}:</td>
                                            <td>{{ number_format($courierStatus['cod_amount'], 2) }}</td>
                                        </tr>
                                    @endif
                                </table>
                            @else
                                <div class="text-center py-5">
                                    <i class="feather icon-alert-triangle text-warning mb-3" style="font-size: 40px;"></i>
                                    <h6 class="text-white font-weight-bold">{{ __('No API Response') }}</h6>
                                    <p class="text-muted">{{ __('Could not retrieve live tracking data from Steadfast API. Please verify API keys in Super Admin settings.') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <!-- Courier ID entered but no matching Invoice in DB -->
                <div class="col-lg-12">
                    <div class="card m-b-30 card_style text-center py-5">
                        <div class="card-body">
                            <i class="feather icon-slash text-danger mb-3" style="font-size: 50px;"></i>
                            <h5 class="text-white font-weight-bold">{{ __('Invoice Not Found') }}</h5>
                            <p class="text-muted">{{ __('No local invoice matches the Courier ID:') }} <strong class="text-orange-400">{{ $consignmentId }}</strong></p>
                            
                            @if(!empty($courierStatus))
                                <div class="mt-4 p-4 rounded bg-slate-800 text-left border border-slate-700/50" style="max-width: 500px; margin: 0 auto;">
                                    <h6 class="text-success font-weight-bold"><i class="feather icon-info mr-2"></i>{{ __('Steadfast API Data') }}:</h6>
                                    <p class="text-white mb-1"><strong>{{ __('Status') }}:</strong> {{ $courierStatus['status'] ?? $courierStatus['delivery_status'] ?? __('Unknown') }}</p>
                                    <p class="text-white mb-1"><strong>{{ __('Recipient') }}:</strong> {{ $courierStatus['recipient_name'] ?? '-' }}</p>
                                    <p class="text-white mb-0"><strong>{{ __('Phone') }}:</strong> {{ $courierStatus['recipient_phone'] ?? '-' }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @else
        <!-- Welcome Prompt -->
        <div class="row mt-2">
            <div class="col-lg-12">
                <div class="card m-b-30 card_style text-center py-5">
                    <div class="card-body">
                        <i class="feather icon-search text-muted mb-3" style="font-size: 50px;"></i>
                        <h5 class="text-white font-weight-bold">{{ __('Track Courier Parcel') }}</h5>
                        <p class="text-muted">{{ __('Enter a Steadfast Courier Consignment ID to fetch its invoice details and tracking status.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
