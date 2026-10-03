<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Audit Report - {{ $audit->audit_no }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Hind Siliguri', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-color: #ffffff;
            color: #000000;
            padding: 20px;
        }
        .print-container {
            max-width: 850px;
            margin: 0 auto;
            background: #fff;
            color: #000000;
            padding: 25px;
            border: 1px solid #000;
            min-height: calc(100vh - 80px);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .content-wrap {
            flex-grow: 1;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .logo-box img {
            max-height: 50px;
            margin-bottom: 8px;
        }
        .company-title {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .company-info {
            font-size: 12px;
            margin-bottom: 10px;
        }
        .report-title-badge, .report-title-badge * {
            display: inline-block;
            background: #000000 !important;
            color: #ffffff !important;
            padding: 5px 18px;
            font-weight: 700;
            font-size: 13px;
            border-radius: 4px;
            letter-spacing: 0.5px;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            border: 1px solid #000;
            padding: 10px 15px;
            border-radius: 4px;
            background: #f8fafc;
        }
        .meta-item {
            font-size: 13px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .stat-card {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
            border-radius: 4px;
            background: #f8fafc;
        }
        .stat-card .val {
            font-size: 18px;
            font-weight: 700;
        }
        .stat-card .lbl {
            font-size: 11px;
            font-weight: 600;
            color: #333 !important;
            margin-top: 2px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }
        th, td {
            border: 1px solid #000;
            padding: 7px 10px;
            text-align: left;
            vertical-align: middle;
        }
        th {
            background-color: #e2e8f0;
            font-weight: 700;
            font-size: 12px;
        }
        .text-center {
            text-align: center;
        }
        .font-bold {
            font-weight: 700;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: auto;
            padding-top: 30px;
            margin-bottom: 10px;
        }
        .sig-block {
            text-align: center;
            width: 220px;
        }
        .sig-line {
            border-top: 1px solid #000;
            margin-bottom: 5px;
        }
        .sig-title {
            font-size: 12px;
            font-weight: 700;
        }
        .print-btn-bar {
            text-align: right;
            margin-bottom: 15px;
            max-width: 850px;
            margin-left: auto;
            margin-right: auto;
        }
        .btn-print {
            background-color: #0f172a !important;
            color: #ffffff !important;
            border: 1px solid #000000;
            padding: 10px 22px;
            font-size: 15px;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }
        .btn-print, .btn-print * {
            color: #ffffff !important;
        }
        .btn-print:hover { background-color: #1e293b !important; }

        @media print {
            body { background: #fff; padding: 0; }
            .print-container { 
                border: none; 
                padding: 0; 
                width: 100%; 
                max-width: 100%; 
                min-height: 96vh;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }
            .print-btn-bar { display: none; }
        }
    </style>
</head>
<body>

    <div class="print-btn-bar">
        <button class="btn-print" onclick="window.print()">🖨️ {{ __('Print Report') }}</button>
    </div>

    <div class="print-container">
        <div class="content-wrap">
            <!-- Header with Logo, Name & Address -->
            <div class="header">
                @if(!empty(get_setting('system_logo')))
                <div class="logo-box">
                    <img src="{{ url('uploads/logo/' . get_setting('system_logo')) }}" alt="Company Logo">
                </div>
                @endif

                <h1 class="company-title">
                    {{ empty(get_setting('com_name')) ? (get_setting('site_title') ?: 'Lotus Bloem') : get_setting('com_name') }}
                </h1>

                <div class="company-info">
                    {{ empty(get_setting('com_address')) ? 'Dhaka, Bangladesh' : get_setting('com_address') }}
                    @if(get_setting('com_phone'))
                        <br>{{ __('Phone:') }} {{ get_setting('com_phone') }}
                    @endif
                    @if(get_setting('com_email'))
                        | {{ __('Email:') }} {{ get_setting('com_email') }}
                    @endif
                </div>

                <div class="report-title-badge">{{ __('STOCK AUDIT REPORT') }}</div>
            </div>

            <!-- Meta Info -->
            <div class="meta-grid">
                <div class="meta-item"><span>{{ __('Audit No:') }}</span> <strong>{{ $audit->audit_no }}</strong></div>
                <div class="meta-item"><span>{{ __('Date:') }}</span> <strong>{{ \Carbon\Carbon::parse($audit->date)->format('d M, Y') }}</strong></div>
                <div class="meta-item"><span>{{ __('Branch:') }}</span> <strong>{{ $audit->branch->name ?? 'Main Branch' }}</strong></div>
                <div class="meta-item"><span>{{ __('Auditor:') }}</span> <strong>{{ $audit->auditor->name ?? 'N/A' }}</strong></div>
            </div>

            <!-- Stats Grid (B&W Boxes) -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="val">{{ $audit->total_items }}</div>
                    <div class="lbl">{{ __('Total Items') }}</div>
                </div>
                <div class="stat-card">
                    <div class="val">{{ $audit->matched_items }}</div>
                    <div class="lbl">{{ __('Matched Items') }}</div>
                </div>
                <div class="stat-card">
                    <div class="val">{{ $audit->discrepancy_items }}</div>
                    <div class="lbl">{{ __('Discrepancy Items') }}</div>
                </div>
                <div class="stat-card">
                    <div class="val">{{ number_format($audit->total_deficit_qty, 2) }}</div>
                    <div class="lbl">{{ __('Total Deficit Qty') }}</div>
                </div>
            </div>

            <!-- Table (B&W) -->
            <table>
                <thead>
                    <tr>
                        <th style="width: 35px;" class="text-center">#</th>
                        <th>{{ __('Product Name & Code') }}</th>
                        <th class="text-center">{{ __('Unit') }}</th>
                        <th class="text-center">{{ __('System Stock') }}</th>
                        <th class="text-center">{{ __('Scanned Qty') }}</th>
                        <th class="text-center">{{ __('Physical Stock') }}</th>
                        <th class="text-center">{{ __('Diff') }}</th>
                        <th class="text-center">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($audit->items as $key => $item)
                    @php
                        $diff = $item->diff_qty;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $key + 1 }}</td>
                        <td>
                            <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                            <div style="font-size: 11px;">Code/Barcode: {{ $item->product->barcode ?? $item->product->id }}</div>
                        </td>
                        <td class="text-center">{{ $item->product->unit->name ?? '' }}</td>
                        <td class="text-center">{{ number_format($item->system_qty, 2) }}</td>
                        <td class="text-center">{{ number_format($item->scanned_qty, 2) }}</td>
                        <td class="text-center"><strong>{{ number_format($item->physical_qty, 2) }}</strong></td>
                        <td class="text-center" style="font-weight: 800;">
                            {{ $diff > 0 ? '+'.number_format($diff, 2) : number_format($diff, 2) }}
                        </td>
                        <td class="text-center font-bold">
                            @if(abs($diff) < 0.001)
                                Match
                            @elseif($diff < 0)
                                Short ({{ abs($diff) }})
                            @else
                                Surplus (+{{ $diff }})
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if($audit->note)
            <div style="margin-bottom: 25px; padding: 10px; border: 1px solid #000; font-size: 12px;">
                <strong>{{ __('Audit Note:') }}</strong> {{ $audit->note }}
            </div>
            @endif
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-title">{{ __('Auditor Signature') }}</div>
            </div>
            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-title">{{ __('Store Incharge / Manager') }}</div>
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
