@extends('backend.layouts.master')
@section('section-title', __('Customer'))
@section('page-title', __('Upcoming Events (Birthday / Anniversary)'))

@push('css')
    <style>
        .filter-select {
            border-radius: 20px !important;
            padding: 6px 20px !important;
            height: 42px !important;
            border: 1px solid #cbd5e1;
            font-size: 14px;
            background-color: #fff;
        }
        .filter-input {
            border-radius: 20px !important;
            padding: 6px 16px !important;
            height: 42px !important;
            border: 1px solid #cbd5e1;
            font-size: 14px;
            background-color: #fff;
        }
        .btn-filter {
            border-radius: 20px !important;
            padding: 8px 22px !important;
            background-color: #0022d6 !important;
            border-color: #0022d6 !important;
            color: #fff !important;
            font-weight: 600;
            height: 42px !important;
        }
        .btn-reset {
            border-radius: 20px !important;
            padding: 8px 22px !important;
            background-color: #ef4444 !important;
            border-color: #ef4444 !important;
            color: #fff !important;
            font-weight: 600;
            height: 42px !important;
        }
        .preset-btn {
            border-radius: 16px !important;
            padding: 4px 14px !important;
            font-size: 13px !important;
            font-weight: 600;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            color: #475569;
            transition: all 0.2s ease;
        }
        .preset-btn:hover, .preset-btn.active-preset {
            background-color: #0022d6;
            color: #ffffff;
            border-color: #0022d6;
        }
        .events-table-head {
            background-color: #0022d6 !important;
            color: #ffffff !important;
            border-radius: 20px;
        }
        .events-table-head th {
            color: #ffffff !important;
            font-weight: 700;
            border: none !important;
            padding: 12px 16px !important;
        }
        .events-table-head th:first-child {
            border-top-left-radius: 20px;
            border-bottom-left-radius: 20px;
        }
        .events-table-head th:last-child {
            border-top-right-radius: 20px;
            border-bottom-right-radius: 20px;
        }
        .no-events-box {
            background-color: #cbd5e1 !important;
            border-radius: 20px !important;
            color: #ef4444 !important;
            font-weight: 600;
            font-size: 15px;
            padding: 15px !important;
            text-align: center;
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card m-b-30 card_style">
                <div class="card-body pt-3">
                    
                    <!-- Filter Form -->
                    <form action="{{ route('customer.upcoming-wishlist') }}" method="GET" class="mb-3" id="filterForm">
                        <div class="row align-items-end">
                            <div class="col-lg-3 col-md-4 mb-2">
                                <label class="font-weight-bold text-dark mb-1" style="font-size: 13px;">{{ __('Event Type') }}</label>
                                <select name="type" class="form-control filter-select">
                                    <option value="all" {{ $filterType == 'all' ? 'selected' : '' }}>{{ __('All (Birthday / Anniversary)') }}</option>
                                    <option value="birthday" {{ $filterType == 'birthday' ? 'selected' : '' }}>{{ __('Birthday') }}</option>
                                    <option value="anniversary" {{ $filterType == 'anniversary' ? 'selected' : '' }}>{{ __('Anniversary') }}</option>
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-4 mb-2">
                                <label class="font-weight-bold text-dark mb-1" style="font-size: 13px;">{{ __('Start Date') }}</label>
                                <input type="date" name="start_date" id="start_date" value="{{ $startDate->format('Y-m-d') }}" class="form-control filter-input">
                            </div>
                            <div class="col-lg-3 col-md-4 mb-2">
                                <label class="font-weight-bold text-dark mb-1" style="font-size: 13px;">{{ __('End Date') }}</label>
                                <input type="date" name="end_date" id="end_date" value="{{ $endDate->format('Y-m-d') }}" class="form-control filter-input">
                            </div>
                            <div class="col-lg-3 col-md-12 mb-2 d-flex gap-2">
                                <button type="submit" class="btn btn-filter flex-fill"><i class="fa fa-filter mr-1"></i>{{ __('Filter') }}</button>
                                <a href="{{ route('customer.upcoming-wishlist') }}" class="btn btn-reset flex-fill">{{ __('Reset') }}</a>
                            </div>
                        </div>

                        <!-- Quick Presets -->
                        <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                            <span class="text-muted font-weight-bold mr-1" style="font-size: 13px;">{{ __('Quick Range:') }}</span>
                            <button type="button" class="btn preset-btn" onclick="setQuickDate(0, 3)">{{ __('Next 3 Days') }}</button>
                            <button type="button" class="btn preset-btn" onclick="setQuickDate(0, 7)">{{ __('Next 7 Days') }}</button>
                            <button type="button" class="btn preset-btn" onclick="setQuickDate(0, 15)">{{ __('Next 15 Days') }}</button>
                            <button type="button" class="btn preset-btn" onclick="setQuickDate(0, 30)">{{ __('Next 30 Days') }}</button>
                            <button type="button" class="btn preset-btn" onclick="setMonthRange()">{{ __('This Month') }}</button>
                        </div>
                    </form>

                    <!-- Title & Date Subheader -->
                    <div class="text-center my-4">
                        <h2 class="font-weight-bold" style="font-size: 26px; color: #1e293b;">
                            {{ __('Upcoming Events') }}
                            @if($totalDays == 4)
                                ({{ __('Next 3 Days') }})
                            @else
                                ({{ $totalDays }} {{ __('Days Range') }})
                            @endif
                        </h2>
                        <p class="font-weight-bold" style="font-size: 15px; color: #64748b;">
                            {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }} 
                            <span style="color: #0022d6;">({{ ucfirst($filterType == 'all' ? 'Birthday / Anniversary' : $filterType) }})</span>
                        </p>
                    </div>

                    <!-- Table List -->
                    <div class="table-responsive">
                        <table class="table text-center align-middle" style="border-collapse: separate; border-spacing: 0 8px;">
                            <thead class="events-table-head">
                                <tr>
                                    <th>{{ __('#SL') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Birth Date') }}</th>
                                    <th>{{ __('Anniversary Date') }}</th>
                                    <th>{{ __('Upcoming Event(S)') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $index => $customer)
                                    <tr style="background-color: #f8fafc; border-radius: 12px;">
                                        <td class="font-weight-bold">{{ $index + 1 }}</td>
                                        <td class="font-weight-bold text-dark">{{ $customer->name }}</td>
                                        <td>
                                            <a href="tel:{{ $customer->phone }}" class="text-primary font-weight-bold">
                                                <i class="fa fa-phone mr-1"></i>{{ $customer->phone }}
                                            </a>
                                        </td>
                                        <td>{{ !empty($customer->birth_date) ? date('d M Y', strtotime($customer->birth_date)) : '—' }}</td>
                                        <td>{{ !empty($customer->anni_date) ? date('d M Y', strtotime($customer->anni_date)) : '—' }}</td>
                                        <td>
                                            @foreach($customer->upcoming_events as $event)
                                                <span class="badge {{ $event['type'] == 'Birthday' ? 'badge-primary' : 'badge-success' }} px-3 py-2 m-1" style="font-size: 13px; border-radius: 12px;">
                                                    <i class="fa {{ $event['type'] == 'Birthday' ? 'fa-birthday-cake' : 'fa-heart' }} mr-1"></i>
                                                    {{ $event['label'] }}
                                                </span>
                                            @endforeach
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-0 border-0">
                                            <div class="no-events-box mt-3">
                                                {{ __('No upcoming events found for the selected date range.') }}
                                            </div>
                                        </td>
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

@push('js')
<script>
    function formatDate(d) {
        let month = '' + (d.getMonth() + 1);
        let day = '' + d.getDate();
        let year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [year, month, day].join('-');
    }

    function setQuickDate(startAdd, endAdd) {
        let today = new Date();
        
        let startDate = new Date();
        startDate.setDate(today.getDate() + startAdd);
        
        let endDate = new Date();
        endDate.setDate(today.getDate() + endAdd);

        document.getElementById('start_date').value = formatDate(startDate);
        document.getElementById('end_date').value = formatDate(endDate);
        document.getElementById('filterForm').submit();
    }

    function setMonthRange() {
        let date = new Date();
        let firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
        let lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);

        document.getElementById('start_date').value = formatDate(firstDay);
        document.getElementById('end_date').value = formatDate(lastDay);
        document.getElementById('filterForm').submit();
    }
</script>
@endpush
