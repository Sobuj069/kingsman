@extends('backend.layouts.master')
@section('page-title', 'Dashboard')
@push('css')
    <style>
        .order-card {
            color: #fff;
        }

        .bg-c-blue {
            background: linear-gradient(45deg, #4099ff, #73b4ff);
        }

        .bg-c-green {
            background: linear-gradient(45deg, #2ed8b6, #59e0c5);
        }

        .bg-c-yellow {
            background: linear-gradient(45deg, #FFB64D, #ffcb80);
        }

        .bg-c-pink {
            background: linear-gradient(45deg, #FF5370, #ff869a);
        }


        .card {
            border-radius: 25px;
            -webkit-box-shadow: 0 1px 2.94px 0.06px rgba(4, 26, 55, 0.16);
            box-shadow: 0 1px 2.94px 0.06px rgba(4, 26, 55, 0.16);
            border: none;
            margin-bottom: 30px;
            -webkit-transition: all 0.3s ease-in-out;
            transition: all 0.3s ease-in-out;
        }

        .card .card-block {
            padding: 25px;
        }

        .order-card i {
            font-size: 26px;
        }

        .f-left {
            float: left;
        }

        .f-right {
            float: right;
        }
    </style>
@endpush

@section('content')
    @php

        $total_invoice = App\Models\Invoice::sum('total_amount');
        $total_return = App\Models\ReturnTbl::sum('total_return');
        $total_purchase = App\Models\Purchase::sum('total_amount');
        $return_purchase = App\Models\ReturnPurchase::sum('total_return');

    @endphp
    <div class=" ms-3" style="margin-left:20px;margin-top:90px;margin-bottom: 50px;">

        @php
    use App\Models\Invoice;
    use App\Models\Purchase;
    use App\Models\ReturnTbl;
    use App\Models\ReturnPurchase;

    $userBranchId = auth()->user()->branch_id;
    $filterBranchId = session('branch_filter_id', auth()->user()->branch_id);

    // ব্রাঞ্চ আইডি নির্ধারণ
    if ($userBranchId == 1 && $filterBranchId) {
        $branchIdForQuery = $filterBranchId;
    } else {
        $branchIdForQuery = $userBranchId;
    }

    // শুধু লগিন ইউজারের ব্রাঞ্চ (বা ফিল্টারকৃত ব্রাঞ্চ) এর সেল বের করা
    $query = Invoice::where('branch_id', $branchIdForQuery);
    if (\request()->has('startDate') && \request()->has('endDate')) {
        $query->whereBetween('date', [\request()->startDate, \request()->endDate]);
    }
    $total_invoice = Invoice::getFakeSum($query, 'total_amount');

    // একই ব্রাঞ্চের রিটার্ন
    $total_return = ReturnTbl::where('branch_id', $branchIdForQuery)->sum('total_return');

    // পণ্য ক্রয় (ব্রাঞ্চ ভিত্তিক)
    $total_purchase = Purchase::where('branch_id', $branchIdForQuery)->sum('total_amount');
    $return_purchase = ReturnPurchase::where('branch_id', $branchIdForQuery)->sum('total_return');

    // আজকের সেল (ব্রাঞ্চ ভিত্তিক + ফেক ফিল্টার)
    $todayQuery = Invoice::where('branch_id', $branchIdForQuery)->whereDate('date', today());
    $user_sale = Invoice::getFakeSum($todayQuery, 'total_amount');
@endphp

        <div class="row mt-5 ml-4" style="margin-top: 150px !important;">
            <div class="col-md-4 col-xl-5">
                <div class="card order-card before_work_height" style="background: #fcfcfd; border: 1px solid #e2e8f0; border-radius: 12px;">
                    <div class="card-block">
                        <h6 class="text-left text-slate-500" style="font-size: 18px; color: #64748b; font-weight: 600;">Available Stock Amount (Sale Rate)</h6>
                        <h4 class="text-left text-slate-800" style="font-size: 26px; color: #1e293b; font-weight: 800; margin-top: 8px;">
                            <span>{{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                {{ number_format($available_stock_sale_val, 0) }} </span>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xl-5">
                <div class="card order-card before_work_height" style="background: #fcfcfd; border: 1px solid #e2e8f0; border-radius: 12px;">
                    <div class="card-block">
                        <h6 class="text-left text-slate-500" style="font-size: 18px; color: #64748b; font-weight: 600;"><span class="filterName"></span> Sale</h6>
                        <h4 class="text-left text-emerald-600" id="saleAmount" style="font-size: 26px; color: #059669; font-weight: 800; margin-top: 8px;">
                            <span>{{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}
                                {{ number_format($user_sale, 0) }} </span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        {{-- @endif --}}
    </div>

@endsection
@push('js')
    <script>
        $(document).ready(function() {
            $('#filterSelect').change(function() {
                $('#filterForm').submit();
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Handle filter link clicks
            $('#filterNav .nav-link').click(function(e) {
                e.preventDefault(); // Prevent default anchor behavior

                // Remove 'active' class from all links
                $('#filterNav .nav-link').removeClass('active');

                // Add 'active' class to the clicked link
                $(this).addClass('active');

                // Get the selected filter from data-filter attribute
                let selectedFilter = $(this).data('filter');
                console.log('Selected Filter:', selectedFilter);

                // Update the filter name dynamically in the UI
                $('.filterName').text(selectedFilter.charAt(0).toUpperCase() + selectedFilter.slice(1));

                // Perform an AJAX request to apply the filter
                $.ajax({
                    url: "{{ route('dashboard.filter') }}", 
                    type: "GET",
                    data: {
                        filter: selectedFilter
                    },
                    success: function(response) {
                        // Handle the response (update the UI dynamically if needed)
                        // console.log('Filter applied:', response);
                        $('#saleAmount').text('৳ ' + Number(response.sale || 0).toLocaleString());
                        $('#purchaseAmount').text('৳ ' + Number(response.purchase || 0).toLocaleString());
                        $('#expenseAmount').text('৳ ' + Number(response.expense || 0).toLocaleString());
                        $('#profitAmount').text('৳ ' + Number(response.profit || 0).toLocaleString());
                    },
                    error: function(xhr) {
                        console.log('Error:', xhr);
                    }
                });
            });
        });
    </script>
@endpush
