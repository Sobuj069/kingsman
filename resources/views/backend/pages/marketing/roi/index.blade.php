@extends('backend.layouts.master')
@section('section-title', __('Marketing'))
@section('page-title', __('ROI Tracking'))

@section('content')
    <!-- Product Filter Selection Card -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body">
                    <form method="GET" action="{{ route('roi.tracking') }}" class="row align-items-center">
                        <div class="col-md-8">
                            <label class="form-label font-weight-bold text-white mb-2">{{ __('Select Product to View Performance & ROI *') }}</label>
                            <select class="select2 form-control" name="product_id" onchange="this.form.submit()">
                                <option value="">{{ __('--- Select a Product ---') }}</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" {{ isset($selectedProduct) && $selectedProduct->id == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mt-4 text-md-right">
                            <button type="submit" class="btn add_list_btn px-4">
                                <i class="feather icon-filter mr-2"></i>{{ __('Filter') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(isset($selectedProduct))
        <div class="row mt-2">
            <!-- Summary Cards -->
            <div class="col-md-4">
                <div class="card card_style m-b-30" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border: 1px solid rgba(249, 115, 22, 0.2);">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 1px;">{{ __('Total Sales via Platforms') }}</span>
                                <h3 class="mt-2 text-white font-weight-bold">{{ number_format(collect($roiData)->sum('total_sales'), 2) }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <span class="p-3 bg-orange-500 rounded-circle text-white" style="display: inline-flex; background: rgba(249, 115, 22, 0.1); border: 1px solid rgba(249, 115, 22, 0.4);">
                                    <i class="feather icon-shopping-bag text-orange-400" style="font-size: 24px; color: #f97316;"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card card_style m-b-30" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border: 1px solid rgba(239, 68, 68, 0.2);">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 1px;">{{ __('Total Ad Cost') }}</span>
                                <h3 class="mt-2 text-white font-weight-bold">{{ number_format(collect($roiData)->sum('total_ad_cost') + $overallData->total_ad_cost, 2) }}</h3>
                            </div>
                            <div class="col-4 text-right">
                                <span class="p-3 bg-red-500 rounded-circle text-white" style="display: inline-flex; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.4);">
                                    <i class="feather icon-minus-circle text-danger" style="font-size: 24px;"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                @php
                    $overallAdCost = collect($roiData)->sum('total_ad_cost') + $overallData->total_ad_cost;
                    $overallSales = collect($roiData)->sum('total_sales') + $overallData->total_sales;
                    $overallProfit = $overallSales - $overallAdCost;
                    $overallRoi = $overallAdCost > 0 ? ($overallProfit / $overallAdCost) * 100 : 0;
                @endphp
                <div class="card card_style m-b-30" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border: 1px solid rgba(34, 197, 94, 0.2);">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <span class="text-muted text-uppercase font-weight-bold" style="font-size: 11px; letter-spacing: 1px;">{{ __('Overall Product ROI') }}</span>
                                <h3 class="mt-2 text-white font-weight-bold">{{ number_format($overallRoi, 2) }}%</h3>
                            </div>
                            <div class="col-4 text-right">
                                <span class="p-3 bg-green-500 rounded-circle text-white" style="display: inline-flex; background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.4);">
                                    <i class="feather icon-trending-up text-success" style="font-size: 24px;"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Platform Breakdown Table -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card m-b-30 card_style">
                    <div class="card-header bg-transparent border-0">
                        <h5 class="text-white font-weight-bold mb-0">
                            {{ __('Platform Performance Breakdown for:') }} <span class="text-orange-400">{{ $selectedProduct->name }}</span>
                        </h5>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table table-striped text-center">
                                <thead class="header_bg">
                                    <tr>
                                        <th class="header_style_left">{{ __('Platform') }}</th>
                                        <th>{{ __('Ad Cost') }}</th>
                                        <th>{{ __('Generated Sales') }}</th>
                                        <th>{{ __('Net Income') }}</th>
                                        <th class="header_style_right">{{ __('ROI') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($roiData as $data)
                                        <tr>
                                            <td class="table_data_style_left font-weight-bold">{{ $data->platform->name }}</td>
                                            <td>{{ number_format($data->total_ad_cost, 2) }}</td>
                                            <td>{{ number_format($data->total_sales, 2) }}</td>
                                            <td class="{{ $data->net_profit >= 0 ? 'text-success' : 'text-danger' }} font-weight-bold">
                                                {{ number_format($data->net_profit, 2) }}
                                            </td>
                                            <td class="table_data_style_right font-weight-bold">
                                                <span class="badge {{ $data->roi_percentage >= 0 ? 'badge-success' : 'badge-danger' }} p-2">
                                                    {{ number_format($data->roi_percentage, 2) }}%
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%" class="text-center no_data_style text-danger">{{ __('No Platforms Configured') }}</td>
                                        </tr>
                                    @endforelse
                                    <!-- General/Offline Sales row -->
                                    <tr style="border-top: 2px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.02);">
                                        <td class="table_data_style_left font-weight-bold text-muted">{{ __('Direct / Offline (No Platform)') }}</td>
                                        <td>{{ number_format($overallData->total_ad_cost, 2) }}</td>
                                        <td>{{ number_format($overallData->total_sales, 2) }}</td>
                                        <td class="{{ $overallData->net_profit >= 0 ? 'text-success' : 'text-danger' }} font-weight-bold">
                                            {{ number_format($overallData->net_profit, 2) }}
                                        </td>
                                        <td class="table_data_style_right font-weight-bold">
                                            <span class="badge {{ $overallData->roi_percentage >= 0 ? 'badge-success' : 'badge-danger' }} p-2">
                                                {{ number_format($overallData->roi_percentage, 2) }}%
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Welcome / Prompt to select product -->
        <div class="row mt-2">
            <div class="col-lg-12">
                <div class="card m-b-30 card_style text-center py-5">
                    <div class="card-body">
                        <i class="feather icon-bar-chart-2 text-muted mb-3" style="font-size: 50px;"></i>
                        <h5 class="text-white font-weight-bold">{{ __('Please Select a Product') }}</h5>
                        <p class="text-muted">{{ __('Select a product from the dropdown above to view the platform-wise ad expense and ROI statistics.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
