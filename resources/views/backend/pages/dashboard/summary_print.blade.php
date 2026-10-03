<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Sales Summary Report ({{ $sdate }} to {{ $edate }})</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Noto+Sans+Bengali:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', 'Noto Sans Bengali', 'Segoe UI', Arial, sans-serif;
            color: #000000 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        body {
            background-color: #ffffff;
            color: #000000;
            padding: 20px;
        }
        .print-container {
            max-width: 920px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border: 2px solid #000000;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000000;
            padding-bottom: 14px;
            margin-bottom: 14px;
        }
        .logo-box {
            margin-bottom: 8px;
        }
        .logo-box img {
            max-height: 60px;
            max-width: 200px;
            object-fit: contain;
        }
        .company-title {
            font-size: 26px;
            font-weight: 900;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .company-info {
            font-size: 13px;
            font-weight: 600;
            line-height: 1.4;
        }
        .report-title-badge {
            display: inline-block;
            border: 2px solid #000000;
            padding: 5px 20px;
            font-size: 14px;
            font-weight: 900;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            background-color: #f8fafc;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            padding: 10px 14px;
            margin-bottom: 14px;
            border: 1.5px solid #000000;
            font-size: 13px;
            background-color: #fafafa;
        }
        .meta-item span {
            font-weight: 600;
        }
        .meta-item strong {
            font-weight: 900;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 16px;
        }
        .stat-card {
            border: 1.5px solid #000000;
            padding: 10px;
            text-align: center;
            background-color: #ffffff;
        }
        .stat-card .val {
            font-size: 20px;
            font-weight: 900;
            line-height: 1.2;
        }
        .stat-card .lbl {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            margin-top: 3px;
            letter-spacing: 0.5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            font-size: 13px;
        }
        th, td {
            padding: 8px 10px;
            border: 1.5px solid #000000;
            text-align: left;
            vertical-align: middle;
        }
        th {
            background-color: #f1f5f9;
            font-weight: 900;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }
        td {
            font-weight: 600;
        }
        .product-name-text {
            font-size: 14px;
            font-weight: 800;
            line-height: 1.2;
        }
        .product-code-text {
            font-size: 11px;
            font-weight: 700;
            margin-top: 1px;
        }
        .num-bold {
            font-size: 13px;
            font-weight: 800;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 45px;
            padding-top: 10px;
            page-break-inside: avoid;
        }
        .sig-block {
            text-align: center;
            width: 230px;
        }
        .sig-line {
            border-top: 1.5px solid #000000;
            margin-bottom: 6px;
        }
        .sig-title {
            font-size: 13px;
            font-weight: 800;
        }
        .print-btn-bar {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            align-items: center;
            margin-bottom: 18px;
            max-width: 920px;
            margin-left: auto;
            margin-right: auto;
        }
        .btn-print {
            background-color: #0f172a !important;
            color: #ffffff !important;
            border: 1.5px solid #000000;
            padding: 11px 22px;
            font-size: 15px;
            font-weight: 800;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.15);
            text-decoration: none;
        }
        .btn-print, .btn-print * {
            color: #ffffff !important;
        }
        .btn-print:hover { background-color: #1e293b !important; }

        .btn-wa {
            background-color: #25D366 !important;
            color: #ffffff !important;
            border: 1.5px solid #1eaa53;
            padding: 11px 22px;
            font-size: 15px;
            font-weight: 800;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            box-shadow: 0 3px 8px rgba(0,0,0,0.15);
        }
        .btn-wa, .btn-wa * {
            color: #ffffff !important;
        }
        .btn-wa:hover { background-color: #1ebe5d !important; }
        
        .btn-pdf {
            background-color: #dc2626 !important;
            color: #ffffff !important;
            border: 1.5px solid #b91c1c;
            padding: 11px 22px;
            font-size: 15px;
            font-weight: 800;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            box-shadow: 0 3px 8px rgba(0,0,0,0.15);
        }
        .btn-pdf, .btn-pdf * {
            color: #ffffff !important;
        }
        .btn-pdf:hover { background-color: #b91c1c !important; }

        @media print {
            body { background: #fff; padding: 0; }
            .print-container { 
                border: none; 
                padding: 0; 
                width: 100%; 
                max-width: 100%; 
            }
            .print-btn-bar { display: none !important; }
            tfoot { display: table-row-group !important; }
        }
    </style>
</head>
<body>

    @php
        $comPhone = get_setting('com_phone') ?: get_setting('phone') ?: '01888714428';
        $cleanPhone = preg_replace('/[^0-9]/', '', $comPhone);
        if (str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '88' . $cleanPhone;
        }
        
        $companyName = empty(get_setting('com_name')) ? (get_setting('site_title') ?: 'Lotus Bloem') : get_setting('com_name');
        $startDateStr = \Carbon\Carbon::parse($sdate)->format('d M, Y');
        $endDateStr = \Carbon\Carbon::parse($edate)->format('d M, Y');
        $pdfUrl = route('dashboard.product-sales-summary.pdf', ['start_date' => $sdate, 'end_date' => $edate]);

        $waMessage = "📊 *PRODUCT SALES " . (!$isSalesman ? "& PROFIT " : "") . "SUMMARY REPORT*\n";
        $waMessage .= "🏢 *" . $companyName . "*\n";
        $waMessage .= "👤 Generated By: " . ($authUser->name ?? auth()->user()->name) . " (" . ($authUser->role->name ?? auth()->user()->role->name ?? 'User') . ")\n";
        $waMessage .= "📅 Date Range: " . $startDateStr . " to " . $endDateStr . "\n\n";
        $waMessage .= "📦 Total Qty Sold: " . number_format($grandTotalQty, 2) . " Pcs\n";
        $waMessage .= "💰 Gross Sale Amount: " . number_format($grandTotalSaleAmount, 2) . "\n";
        if (($grandTotalReturnAmount ?? 0) > 0) {
            $waMessage .= "🔄 Return Amount: " . number_format($grandTotalReturnAmount, 2) . "\n";
        }
        $waMessage .= "💵 Net Sale Amount: " . number_format($grandTotalNetSale ?? ($grandTotalSaleAmount - ($grandTotalReturnAmount ?? 0) - ($grandTotalDiscount ?? 0)), 2) . "\n";
        $waMessage .= "🏷️ Total Discount: " . number_format($grandTotalDiscount ?? 0, 2) . "\n";
        if (!$isSalesman) {
            $waMessage .= "📈 Total Net Profit: " . number_format($grandTotalProfit, 2) . "\n\n";
        }
        $waMessage .= "📄 Direct PDF Download Link:\n" . $pdfUrl;

        $waUrl = "https://api.whatsapp.com/send?phone=" . $cleanPhone . "&text=" . urlencode($waMessage);
    @endphp

    <div class="print-btn-bar">
        <a href="{{ route('dashboard.product-sales-summary.pdf', ['start_date' => $sdate, 'end_date' => $edate, 'download' => 1]) }}" class="btn-pdf">
            <i class="fas fa-file-pdf"></i> {{ __('Download PDF') }}
        </a>
        <a href="{{ $waUrl }}" target="_blank" class="btn-wa">
            <i class="fab fa-whatsapp" style="font-size: 18px;"></i> {{ __('Share PDF Link') }}
        </a>
        <button class="btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> {{ __('Print Report') }}
        </button>
    </div>

    <div class="print-container">
        <div class="content-wrap">
            <!-- Header with Logo, Name & Address -->
            <div class="header">
                @php
                    $sysLogo = get_setting('system_logo');
                    $logoPath = $sysLogo ? public_path('uploads/logo/' . $sysLogo) : null;
                @endphp
                @if($sysLogo && file_exists($logoPath))
                <div class="logo-box">
                    <img src="{{ url('uploads/logo/' . $sysLogo) }}" alt="Logo">
                </div>
                @endif

                <h1 class="company-title">
                    {{ $companyName }}
                </h1>

                <div class="company-info">
                    {{ empty(get_setting('com_address')) ? 'Dhaka, Bangladesh' : get_setting('com_address') }}
                    @if($comPhone)
                        <br>{{ __('Phone:') }} {{ $comPhone }}
                    @endif
                    @if(get_setting('com_email'))
                        | {{ __('Email:') }} {{ get_setting('com_email') }}
                    @endif
                </div>

                <div class="report-title-badge">{{ $isSalesman ? __('PRODUCT SALES SUMMARY REPORT') : __('PRODUCT SALES & PROFIT SUMMARY REPORT') }}</div>
            </div>

            <!-- Meta Info -->
            <div class="meta-grid">
                <div class="meta-item"><span>{{ __('Date Range:') }}</span> <strong>{{ $startDateStr }} {{ __('to') }} {{ $endDateStr }}</strong></div>
                <div class="meta-item"><span>{{ __('Branch:') }}</span> <strong>{{ $branch->name ?? 'Main Branch' }}</strong></div>
                <div class="meta-item"><span>{{ __('Print Time:') }}</span> <strong>{{ date('d M, Y h:i A') }}</strong></div>
                <div class="meta-item"><span>{{ __('Generated By:') }}</span> <strong>{{ $authUser->name ?? auth()->user()->name }} ({{ $authUser->role->name ?? auth()->user()->role->name ?? 'User' }})</strong></div>
                <div class="meta-item" style="grid-column: span 2;"><span>{{ __('Total Item Types Sold:') }}</span> <strong>{{ count($summary) }} {{ __('Items') }}</strong></div>
            </div>

            <!-- Stats Grid (B&W Boxes) -->
            <div class="stats-grid" style="grid-template-columns: repeat({{ $isSalesman ? '4' : '5' }}, 1fr); gap: 8px;">
                <div class="stat-card">
                    <div class="val">{{ number_format($grandTotalQty, 2) }}</div>
                    <div class="lbl">{{ __('Total Qty Sold') }}</div>
                </div>
                <div class="stat-card">
                    <div class="val">{{ number_format($grandTotalSaleAmount, 2) }}</div>
                    <div class="lbl">{{ __('Gross Sale Amount') }}</div>
                </div>
                <div class="stat-card">
                    <div class="val">{{ number_format($grandTotalReturnAmount ?? 0, 2) }}</div>
                    <div class="lbl">{{ __('Return Amount') }}</div>
                </div>
                <div class="stat-card" style="border: 2px solid #000; background-color: #f8fafc;">
                    <div class="val" style="color: #0f172a !important;">{{ number_format($grandTotalNetSale ?? ($grandTotalSaleAmount - ($grandTotalReturnAmount ?? 0) - ($grandTotalDiscount ?? 0)), 2) }}</div>
                    <div class="lbl" style="color: #0f172a !important;">{{ __('Net Sale Amount') }}</div>
                </div>
                <div class="stat-card">
                    <div class="val">{{ number_format($grandTotalDiscount ?? 0, 2) }}</div>
                    <div class="lbl">{{ __('Total Discount') }}</div>
                </div>
                @if(!$isSalesman)
                <div class="stat-card" style="grid-column: span {{ $isSalesman ? '4' : '5' }}; background-color: #f1f5f9;">
                    <div class="val">{{ number_format($grandTotalProfit, 2) }}</div>
                    <div class="lbl">{{ __('Total Net Profit') }}</div>
                </div>
                @endif
            </div>

            <!-- Table (B&W High Contrast) -->
            <table>
                <thead>
                    <tr>
                        <th style="width: 40px;" class="text-center">#</th>
                        <th>{{ __('Product Name & Code') }}</th>
                        <th class="text-center">{{ __('Total Qty Sold') }}</th>
                        <th class="text-right">{{ __('Total Sale') }}</th>
                        <th class="text-center">{{ __('Return Qty') }}</th>
                        <th class="text-right">{{ __('Return Amount') }}</th>
                        <th class="text-right">{{ __('Discount') }}</th>
                        @if(!$isSalesman)
                        <th class="text-right">{{ __('Total Cost') }}</th>
                        <th class="text-right">{{ __('Total Profit') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($summary as $key => $item)
                    <tr>
                        <td class="text-center num-bold">{{ $key + 1 }}</td>
                        <td>
                            <div class="product-name-text">{{ $item['name'] }}</div>
                            <div class="product-code-text">Code: {{ $item['code'] }}</div>
                        </td>
                        <td class="text-center num-bold">{{ number_format($item['total_qty'], 2) }}</td>
                        <td class="text-right num-bold">{{ number_format($item['total_sale'], 2) }}</td>
                        <td class="text-center font-bold">{{ number_format($item['return_qty'], 2) }}</td>
                        <td class="text-right font-bold">{{ number_format($item['return_amount'], 2) }}</td>
                        <td class="text-right font-bold">{{ number_format($item['discount'] ?? 0, 2) }}</td>
                        @if(!$isSalesman)
                        <td class="text-right num-bold">{{ number_format($item['total_cost'], 2) }}</td>
                        <td class="text-right num-bold">{{ number_format($item['total_profit'], 2) }}</td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $isSalesman ? '7' : '9' }}" class="text-center" style="padding: 25px; font-weight: 800; font-size: 15px;">{{ __('No sales found for this date range!') }}</td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($summary) > 0)
                <tfoot>
                    <tr style="font-weight: 900; background-color: #f1f5f9; font-size: 14px;">
                        <td colspan="2" class="text-right" style="font-weight: 900;">{{ __('Grand Total:') }}</td>
                        <td class="text-center" style="font-weight: 900;">{{ number_format($grandTotalQty, 2) }}</td>
                        <td class="text-right" style="font-weight: 900;">{{ number_format($grandTotalSaleAmount, 2) }}</td>
                        <td class="text-center" style="font-weight: 900;">{{ number_format($grandTotalReturnQty, 2) }}</td>
                        <td class="text-right" style="font-weight: 900;">{{ number_format($grandTotalReturnAmount, 2) }}</td>
                        <td class="text-right" style="font-weight: 900;">{{ number_format($grandTotalProductDiscount ?? 0, 2) }}</td>
                        @if(!$isSalesman)
                        <td class="text-right" style="font-weight: 900;">{{ number_format($grandTotalCostAmount, 2) }}</td>
                        <td class="text-right" style="font-weight: 900;">{{ number_format($grandTotalProfit, 2) }}</td>
                        @endif
                    </tr>
                    <tr style="font-weight: 900; background-color: #e2e8f0; font-size: 14px;">
                        <td colspan="{{ $isSalesman ? '7' : '9' }}" class="text-center" style="padding: 10px; font-weight: 900;">
                            {{ __('Net Sale Amount (Gross Sale - Return - Discount):') }} 
                            <span style="font-size: 16px; margin-left: 5px;">Tk {{ number_format($grandTotalNetSale ?? ($grandTotalSaleAmount - ($grandTotalReturnAmount ?? 0) - ($grandTotalDiscount ?? 0)), 2) }}</span>
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-title">{{ __('Prepared By') }}</div>
            </div>
            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-title">{{ __('Manager Signature') }}</div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
