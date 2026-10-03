@extends('backend.layouts.master')
@section('page-title', __('POS'))
@push('css')
    <style>
        #category-sidebar.sidebar-pos-hidden,
        #category-sidebar-alt.sidebar-pos-hidden,
        .sidebar-pos-hidden {
            display: none !important;
        }
        .invoice-contentbar {
            margin-top: 0px;
            padding: 10px;
            margin-bottom: 10px;
            width: 100%;
            padding-bottom: 185px !important;
        }

        /* Fixed POS bottom footer style */
        .pos-fixed-footer {
            position: fixed;
            bottom: 0;
            left: 270px;
            right: 0;
            background: #f1f5f9;
            border-top: 1.5px solid #cbd5e1;
            padding: 12px 24px;
            z-index: 1090;
            transition: left 0.3s ease, background 0.3s ease, border-color 0.3s ease;
            box-shadow: 0 -8px 24px rgba(0, 0, 0, 0.12);
        }
        
        .dark-theme .pos-fixed-footer {
            background: #0f172a !important;
            border-color: #1e293b !important;
            box-shadow: 0 -8px 24px rgba(0, 0, 0, 0.3);
        }

        .rightbar.mini .pos-fixed-footer,
        #modernSidebar.mini ~ .rightbar .pos-fixed-footer {
            left: 80px;
        }
        
        body.kiosk-mode .pos-fixed-footer,
        .sidebar-none .pos-fixed-footer,
        #containerbar.sidebar-none .pos-fixed-footer {
            left: 0 !important;
        }

        /* Fixed POS summary design */
        .toast-container {
            z-index: 99999999 !important;
        }

        .pos-footer-summary {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 12px;
            margin-bottom: 12px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.02);
            transition: all 0.3s ease;
        }

        .dark-theme .pos-footer-summary {
            background: #1e293b;
            border-color: #334155;
            box-shadow: none;
        }

        .pos-summary-item {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 6px 10px;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            transition: all 0.2s ease;
        }

        .dark-theme .pos-summary-item {
            background: #0f172a;
            border-color: #1e293b;
        }

        .pos-summary-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .pos-summary-item .summary-label {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            text-align: center;
        }

        .dark-theme .pos-summary-item .summary-label {
            color: #94a3b8;
        }

        .pos-summary-item .summary-value {
            font-size: 13px;
            font-weight: 800;
            color: #0f172a;
            text-align: center;
        }

        .dark-theme .pos-summary-item .summary-value {
            color: #f1f5f9;
        }

        .pos-summary-item.highlight-payable {
            background: #eff6ff;
            border-color: #bfdbfe;
        }

        .dark-theme .pos-summary-item.highlight-payable {
            background: #172554;
            border-color: #1e3a8a;
        }

        .pos-summary-item.highlight-payable .summary-value {
            color: #1d4ed8;
        }
        .dark-theme .pos-summary-item.highlight-payable .summary-value {
            color: #60a5fa;
        }

        .pos-footer-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            width: 100%;
            max-width: 1600px;
            margin: 0 auto;
        }

        .btn-pos-footer {
            height: 55px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: none;
            width: 100%;
        }

        .btn-pos-footer.btn-full-paid {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #ffffff !important;
        }
        .btn-pos-footer.btn-full-paid:hover {
            background: linear-gradient(135deg, #34d399, #10b981);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
        }

        .btn-pos-footer.btn-full-due {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: #ffffff !important;
        }
        .btn-pos-footer.btn-full-due:hover {
            background: linear-gradient(135deg, #f87171, #ef4444);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.35);
        }

        .btn-pos-footer.btn-checkout {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: #ffffff !important;
        }
        .btn-pos-footer.btn-checkout:hover {
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.35);
        }

        @media (max-width: 1200px) {
            .pos-footer-summary {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 768px) {
            .pos-fixed-footer {
                left: 0 !important;
                padding: 10px 15px;
            }
            .pos-footer-grid {
                gap: 10px;
            }
            .btn-pos-footer {
                height: 48px;
                font-size: 13px;
            }
            .pos-footer-summary {
                grid-template-columns: repeat(2, 1fr);
                gap: 8px;
                padding: 8px;
            }
            .pos-summary-item {
                padding: 4px 6px;
            }
            .pos-summary-item .summary-label {
                font-size: 10px;
            }
            .pos-summary-item .summary-value {
                font-size: 12px;
            }
        }

        /* Global Footer Hide for POS Page */
        footer, .footer {
            display: none !important;
        }


        /* Full Screen POS Mode Styles - Only applies in kiosk-mode */
        body.kiosk-mode header, 
        body.kiosk-mode footer,
        body.kiosk-mode .footer,
        body.kiosk-mode .breadcrumbbar {
            display: none !important;
        }

        /* If sidebar is explicitly hidden via eye toggle in kiosk mode */
        body.kiosk-mode #containerbar.sidebar-none .modern-sidebar {
            display: none !important;
        }
        body.kiosk-mode #containerbar.sidebar-none .rightbar {
            margin-left: 0 !important;
            padding: 0 !important;
            min-height: 100vh;
        }

        /* If sidebar is visible in kiosk-mode */
        body.kiosk-mode #containerbar:not(.sidebar-none) .modern-sidebar {
            display: flex !important;
        }
        body.kiosk-mode #containerbar:not(.sidebar-none) .rightbar {
            margin-left: 270px !important;
            padding: 0 !important;
            min-height: 100vh;
        }
        body.kiosk-mode #containerbar:not(.sidebar-none) #modernSidebar.mini ~ .rightbar {
            margin-left: 80px !important;
        }

        /* Navigation Toggles in Kiosk Mode */
        .kiosk-nav-toggles {
            display: none !important;
            align-items: center;
            gap: 4px;
            background: #ffffff;
            padding: 4px;
            border-radius: 50px;
            border: 1px solid #e2e8f0;
            margin-right: auto;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        body.kiosk-mode .kiosk-nav-toggles {
            display: inline-flex !important;
        }
        body.dark-theme .kiosk-nav-toggles {
            background: #1e293b;
            border-color: #334155;
        }
        .kiosk-nav-btn {
            width: 32px !important;
            height: 32px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: 50% !important;
            border: none !important;
            background: transparent !important;
            color: #64748b !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
            padding: 0 !important;
            box-shadow: none !important;
        }
        .kiosk-nav-btn:hover {
            background: #f1f5f9 !important;
            color: #3b82f6 !important;
        }
        body.dark-theme .kiosk-nav-btn:hover {
            background: #334155 !important;
            color: #60a5fa !important;
        }

        /* POS Qty Plus/Minus Buttons Styling */
        .qty-minus-btn, .qty-plus-btn {
            border: 1px solid #cbd5e1 !important;
            background-color: #f8fafc !important;
            color: #475569 !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
        }
        .qty-minus-btn:hover, .qty-plus-btn:hover {
            background-color: #e2e8f0 !important;
            color: #0f172a !important;
        }
        body.dark-theme .qty-minus-btn, body.dark-theme .qty-plus-btn {
            border: 1px solid #334155 !important;
            background-color: #1e293b !important;
            color: #94a3b8 !important;
        }
        body.dark-theme .qty-minus-btn:hover, body.dark-theme .qty-plus-btn:hover {
            background-color: #334155 !important;
            color: #f1f5f9 !important;
        }

        body.kiosk-mode #containerbar {
            padding: 0 !important;
            overflow-y: auto;
            overflow-x: hidden;
            height: 100vh;
        }

        /* Toggle Button Styling */
        .kiosk-toggle-btn {
            background: #f1f5f9;
            color: #475569;
            border: 1.5px solid #e2e8f0;
            width: 34px;
            height: 34px; 
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        .kiosk-toggle-btn:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
        body.dark-theme .kiosk-toggle-btn {
            background: #1e293b;
            border-color: #334155;
            color: #94a3b8;
        }
        body.dark-theme .kiosk-toggle-btn:hover {
            background: #334155;
            color: #f1f5f9;
        }


        /* Ensure the main container takes full height */
        .flex-grow.w-full {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Initializer Overlay */
        #pos-init-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.9);
            backdrop-blur: 8px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #fff;
            cursor: pointer;
            transition: opacity 0.4s ease;
        }
        #pos-init-overlay .init-content {
            text-align: center;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.05); opacity: 1; }
            100% { transform: scale(1); opacity: 0.8; }
        }

        /* pos footer section start */

        .footerpos {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 280px;
            z-index: 1000;
        }

        .footerpos .footerpos_left {
            background-color: #000ce2 !important;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 8px 10px;
            border-top-left-radius: 25px;
            border-bottom-left-radius: 25px;
        }

        .footerpos .footerpos_left div {
            font-size: 20px;
            color: #fff;
            padding: 0 !important;
            font-weight: bold;
        }

        .footerpos_right {
            background-color: #00a65a !important;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 8px 10px;
            border-top-right-radius: 25px;
            border-bottom-right-radius: 25px;
        }

        .footerpos_right div {
            font-size: 18px;
            color: #fff;
            cursor: pointer;
            padding: 0 !important;
            font-weight: bold;
        }

        .productcss {
            cursor: pointer;
            height: 100% !important;
            border-radius: 12px;
            background: #fff;
            border: 1.2px solid #eaedf1;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 5px !important;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin-bottom: 0px; /* Removed fixed bottom margin */
        }
        /* Uniform Grid Spacing */
        #products .row > [class*="col-"] {
            padding: 6px !important;
        }
        .productcss:hover {
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
            border-color: #000ce2;
            transform: translateY(-4px);
        }
        .productcss .product-head {
            margin-bottom: 4px;
            width: 100%;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .productcss .product-head img {
            max-height: 55px !important;
            width: auto !important;
            max-width: 90%;
            object-fit: contain;
        }
        .productcss .product-body {
            padding: 0 !important;
            width: 100%;
        }
        .productcss .product-name {
            font-size: 11px !important;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0px !important;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.1;
            height: 24px;
        }
        .productcss .product-code {
            font-size: 10px !important;
            color: #718096;
            margin-bottom: 2px !important;
        }
        .productcss .stock-status {
            font-size: 9.5px !important;
            color: #4a5568;
            font-weight: 500;
        }

        /* Modern Pagination Styling */
        .pagination {
            gap: 3px !important;
            margin-top: 10px !important;
            margin-bottom: 0px !important;
            flex-wrap: nowrap;
            overflow-x: auto;
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
            padding-bottom: 5px;
        }
        .pagination::-webkit-scrollbar {
            display: none;
        }
        .pagination .page-item .page-link {
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            color: #4a5568 !important;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 8px;
            transition: all 0.2s ease;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .pagination .page-item.active .page-link {
            background: #000ce2 !important;
            border-color: #000ce2 !important;
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(0, 12, 226, 0.25);
        }
        .pagination .page-item .page-link:hover:not(.active) {
            background: #f8f9fa !important;
            border-color: #cbd5e0 !important;
            color: #000ce2 !important;
            transform: translateY(-1px);
        }
        .pagination .page-item.disabled .page-link {
            background: #f1f5f9;
            color: #94a3b8 !important;
            border-color: #e2e8f0 !important;
        }

        /* Dark Mode Pagination */
        body.dark-theme .pagination .page-item .page-link {
            background: #2d3748;
            border-color: #4a5568 !important;
            color: #cbd5e0 !important;
        }
        body.dark-theme .pagination .page-item.active .page-link {
            background: #4f6ef7 !important;
            border-color: #4f6ef7 !important;
            color: #fff !important;
        }
        body.dark-theme .pagination .page-item.disabled .page-link {
            background: #1a202c;
            color: #4a5568 !important;
        }
        body.dark-theme .productcss {
            background: #1e2533 !important;
            border-color: #2d3748 !important;
        }
        body.dark-theme .productcss .product-name {
            color: #e2e8f0 !important;
        }
        body.dark-theme .productcss .product-code {
            color: #a0aec0 !important;
        }
        body.dark-theme .productcss .stock-status {
            color: #cbd5e0 !important;
        }

        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        .table-responsive {
            overflow-x: auto;
        }

        /* Sidebar Toggle CSS */
        .pos-toggle-container {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }
        
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
            margin-left: 10px;
        }
        
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: #007bff;
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .extra_btn, .btn, button, .footerpos_right div {
            cursor: pointer !important;
        }

        /* Sidebar Toggle CSS */
        #category-sidebar, #category-sidebar-alt {
            transition: all 0.3s ease;
        }
        #main-pos-area, #main-pos-area-alt {
            transition: all 0.3s ease;
        }
        .sidebar-hidden #category-sidebar, 
        .sidebar-hidden #category-sidebar-alt {
            display: none !important;
        }
        .sidebar-hidden #main-pos-area, 
        .sidebar-hidden #main-pos-area-alt {
            flex: 0 0 100% !important;
            max-width: 100% !important;
        }
        .category-toggle-btn {
            background: #6c757d;
            color: #fff;
            border: none;
            padding: 5px 15px;
            border-radius: 5px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
            font-weight: bold;
            font-size: 13px;
        }
        .ecommerce-sortby {
            margin-bottom: 5px !important;
        }
        .ecommerce-sortby label {
            margin-bottom: 3px !important;
        }
        #products {
            margin-top: 5px !important;
        }
        .category-toggle-btn:hover {
            background: #5a6268;
            color: #fff;
        }

        /* Global POS Input Height Reduction */
        .ecommerce-sortby .form-control,
        .ecommerce-sortby .select2-container--default .select2-selection--single,        .ecommerce-sortby .extra_btn, 
        .ecommerce-sortby .category-toggle-btn,
        .product_search,
        #product_search_scale,
        .barcod_style,
        .ifield .form-control,
        .bank-amount-input,
        .table .form-control {
            height: 34px !important;
            font-size: 13px !important;
            padding-top: 4px !important;
            padding-bottom: 4px !important;
        }
        
        .barcod_style {
            display: flex !important;
            align-items: center;
            justify-content: center;
        }
        
        /* Adjusting Select2 text and arrow for smaller height */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 32px !important;
            padding-left: 10px !important;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 32px !important;
        }
        
        .ecommerce-sortby .extra_btn, 
        .ecommerce-sortby .category-toggle-btn {
            width: 36px !important;
            display: flex !important;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
        }

        /* Modern Summary Panel - Compact Container overrides */
        .pos-summary-panel {
            background: transparent !important;
            border: none !important;
            margin-top: 10px;
            padding: 0 !important;
            box-shadow: none !important;
        }

        .pos-compact-container {
            display: flex;
            gap: 15px;
            width: 100%;
        }

        .pos-compact-card {
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 14px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: all 0.3s ease;
        }

        body.dark-theme .pos-compact-card {
            background: #1e293b;
            border-color: #334155;
            box-shadow: none;
        }

        .card-adjustments {
            flex: 0.95;
        }

        .card-transaction {
            flex: 1.05;
        }

        .card-title-compact {
            font-size: 11px;
            font-weight: 800;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            display: flex;
            align-items: center;
            border-bottom: 1.5px solid #f1f5f9;
            padding-bottom: 6px;
            margin-bottom: 2px;
        }

        body.dark-theme .card-title-compact {
            color: #94a3b8;
            border-bottom-color: #334155;
        }

        .compact-grid-2x2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .transaction-fields-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .transaction-note-field {
            grid-column: span 2;
        }

        .compact-pay-type {
            margin-bottom: 2px !important;
            gap: 8px !important;
        }

        .compact-pay-type .pay-type-opt {
            padding: 6px 12px !important;
            font-size: 12px !important;
            border-radius: 6px !important;
        }

        .compact-ifield-inline {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 6px;
        }

        .compact-ifield-inline .form-control {
            max-width: 140px;
        }

        .pos-summary-field {
            display: flex;
            flex-direction: column;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 8px 12px;
            transition: all 0.2s ease;
        }

        body.dark-theme .pos-summary-field {
            background: #0f172a;
            border-color: #1e293b;
        }

        .pos-summary-field:focus-within {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .pos-summary-field label {
            font-size: 10px !important;
            font-weight: 700 !important;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px !important;
            height: 14px;
            display: flex;
            align-items: center;
        }

        body.dark-theme .pos-summary-field label {
            color: #94a3b8;
        }

        .pos-summary-field .form-control,
        .pos-summary-field input {
            border: none !important;
            background: transparent !important;
            padding: 0 !important;
            height: 24px !important;
            font-weight: 700 !important;
            font-size: 15px !important;
            color: #0f172a !important;
            box-shadow: none !important;
        }

        body.dark-theme .pos-summary-field .form-control,
        body.dark-theme .pos-summary-field input {
            color: #f1f5f9 !important;
            background: transparent !important;
        }

        /* Prevent autocomplete white background */
        .pos-summary-field input:-webkit-autofill,
        .pos-summary-field input:-webkit-autofill:hover, 
        .pos-summary-field input:-webkit-autofill:focus, 
        .pos-summary-field input:-webkit-autofill:active {
            -webkit-text-fill-color: #0f172a !important;
            transition: background-color 5000s ease-in-out 0s;
        }
        body.dark-theme .pos-summary-field input:-webkit-autofill,
        body.dark-theme .pos-summary-field input:-webkit-autofill:hover, 
        body.dark-theme .pos-summary-field input:-webkit-autofill:focus, 
        body.dark-theme .pos-summary-field input:-webkit-autofill:active {
            -webkit-text-fill-color: #f1f5f9 !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        /* Highlight grand total in adjustments */
        .pos-summary-field.highlight-grand {
            background: #eff6ff;
            border-color: #bfdbfe;
        }

        body.dark-theme .pos-summary-field.highlight-grand {
            background: #172554;
            border-color: #1e3a8a;
        }

        .pos-summary-field.highlight-grand .form-control {
            color: #2563eb !important;
        }

        body.dark-theme .pos-summary-field.highlight-grand .form-control {
            color: #60a5fa !important;
        }

        /* Payment Type Radio Pills styling */
        .pay-type-row {
            display: flex;
            gap: 12px;
            margin-bottom: 12px;
            border-bottom: none;
            padding: 0;
            background: transparent !important;
        }
        .pay-type-opt {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            background: #f1f5f9;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
        }
        body.dark-theme .pay-type-opt {
            background: #1e293b;
            border-color: #334155;
            color: #cbd5e1;
        }
        .pay-type-opt input[type="radio"] {
            display: none !important;
        }
        .pay-type-opt:has(input:checked) {
            background: #2563eb !important;
            border-color: #1d4ed8 !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }
        body.dark-theme .pay-type-opt:has(input:checked) {
            background: #3b82f6 !important;
            border-color: #2563eb !important;
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        @media (max-width: 768px) {
            .pos-compact-container {
                flex-direction: column;
                gap: 12px;
            }
        }

        /* Inline unit styles inside quantity textbox */
        .quantity-input-wrapper {
            position: relative;
            display: inline-flex;
            align-items: center;
            width: 100%;
            min-width: 90px;
        }
        .quantity-input-wrapper .quantity-input {
            padding-right: 32px !important;
            width: 100%;
            text-align: center;
            font-weight: 700;
        }
        .quantity-input-wrapper .quantity-unit {
            position: absolute;
            right: 8px;
            font-size: 10px;
            font-weight: 800;
            color: #64748b;
            pointer-events: none;
            text-transform: uppercase;
        }
        body.dark-theme .quantity-input-wrapper .quantity-unit {
            color: #94a3b8;
        }
        .pos-grand-total-value {
            font-size: 22px;
            font-weight: 800;
            color: #000ce2;
            padding: 4px 12px;
            background: #eef1ff;
            border-radius: 8px;
            border: 1.5px solid #c7d7ff;
            min-width: 130px;
            text-align: right;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }
        .pos-action-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-pay-now {
            height: 38px;
            padding: 0 20px;
            background: linear-gradient(135deg, #12b76a, #0d9f5a);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(18,183,106,0.3);
            white-space: nowrap;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            line-height: 1;
        }
        .btn-pay-now:hover {
            background: linear-gradient(135deg, #0d9f5a, #0a8a4d);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(18,183,106,0.45);
        }
        .pos-action-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* General Font Size Reductions */
        .table thead th {
            font-size: 14px !important;
            padding: 8px 4px !important;
        }
        .table tbody td {
            font-size: 13px !important;
            padding: 6px 4px !important;
            vertical-align: middle !important;
        }
        /* Hide spin buttons for input number */
        .table input::-webkit-outer-spin-button,
        .table input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .table input[type=number] {
            -moz-appearance: textfield;
        }

        .table tbody td input.form-control,
        .table tbody td select.form-control {
            font-size: 13px !important;
            height: 34px !important;
            color: #0f172a !important;
            font-weight: 600 !important;
            border: 1px solid #cbd5e1 !important;
            background-color: #ffffff !important;
            border-radius: 4px !important;
            text-align: center !important;
            padding: 4px 6px !important;
            transition: all 0.2s ease !important;
            width: 100% !important;
        }
        .table tbody td input.form-control:focus,
        .table tbody td select.form-control:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2) !important;
            background-color: #ffffff !important;
        }
        /* Style for readonly/disabled fields like subtotal to be clear and prominent */
        .table tbody td input.form-control[readonly],
        .table tbody td input.form-control[disabled] {
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #1e3a8a !important;
            font-weight: 700 !important;
            cursor: not-allowed !important;
        }
        /* Style for input group containing quantity and unit badge */
        .table tbody td .input-group {
            flex-wrap: nowrap !important;
        }
        .table tbody td .input-group-text {
            font-size: 11px !important;
            padding: 0 8px !important;
            background-color: #e2e8f0 !important;
            color: #334155 !important;
            border: 1px solid #cbd5e1 !important;
            border-left: none !important;
            font-weight: 600 !important;
            display: flex;
            align-items: center;
            height: 34px !important;
            border-radius: 0 4px 4px 0 !important;
        }
        .table tbody td .input-group .quantity-input {
            border-radius: 4px 0 0 4px !important;
        }

        /* ======= DARK MODE OVERRIDES ======= */
        body.dark-theme .table tbody td input.form-control,
        body.dark-theme .table tbody td select.form-control {
            color: #f8fafc !important;
            background-color: #1e293b !important;
            border-color: #475569 !important;
        }
        body.dark-theme .table tbody td input.form-control[readonly],
        body.dark-theme .table tbody td input.form-control[disabled] {
            background-color: #0f172a !important;
            border-color: #334155 !important;
            color: #38bdf8 !important;
        }
        body.dark-theme .table tbody td .input-group-text {
            background-color: #334155 !important;
            color: #cbd5e1 !important;
            border-color: #475569 !important;
        }
        .ecommerce-sortby label {
            font-size: 13px !important;
        }
        .ecommerce-sortby .form-control, 
        .ecommerce-sortby .select2-container--default .select2-selection--single {
            font-size: 14px !important;
        }
        .input-group-text {
            font-size: 13px !important;
            padding: 0 8px !important;
        }

        /* ======= DARK MODE OVERRIDES ======= */
        body.dark-theme .pos-summary-panel {
            background: #1e2533 !important;
            border-color: #2d3748 !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3) !important;
        }
        body.dark-theme .pos-summary-field label {
            color: #a0aec0 !important;
        }
        body.dark-theme .pos-summary-field .form-control {
            background: #2d3748 !important;
            border-color: #4a5568 !important;
            color: #e2e8f0 !important;
        }
        body.dark-theme .pos-summary-field .form-control[readonly] {
            background: #1a2340 !important;
            border-color: #3b5bdb !important;
            color: #90cdf4 !important;
        }
        body.dark-theme .pos-summary-field .form-control:focus {
            background: #2d3748 !important;
            border-color: #5c7cfa !important;
            box-shadow: 0 0 0 3px rgba(92,124,250,0.2) !important;
        }
        body.dark-theme .grand-total-label {
            color: #90cdf4 !important;
        }
        body.dark-theme .category-toggle-btn {
            background: #4a5568 !important;
            color: #e2e8f0 !important;
        }
        body.dark-theme .category-toggle-btn:hover {
            background: #5a6578 !important;
        }
        /* Dark mode table rows */
        body.dark-theme tbody tr {
            background: #2d3748 !important;
            color: #e2e8f0 !important;
        }
        body.dark-theme tbody tr td {
            color: #e2e8f0 !important;
            border-color: #4a5568 !important;
        }
        body.dark-theme .table-striped tbody tr:nth-of-type(odd) {
            background: #263044 !important;
        }
        body.dark-theme .form-control {
            background: #2d3748 !important;
            border-color: #4a5568 !important;
            color: #e2e8f0 !important;
        }
        body.dark-theme .cart-search-header,
        body.dark-theme .cart-head,
        body.dark-theme .cart-container {
            background: transparent !important;
        }
        /* Light mode explicit — ensure white mode stays clean */
        body:not(.dark-theme) .pos-summary-panel {
            background: #ffffff;
            border-color: #e9ecef;
        }
        body:not(.dark-theme) .pos-summary-field .form-control {
            background: #f8f9fa;
            color: #1a1a2e;
        }

        /* Responsive Fixes */
        @media (max-width: 768px) {
            .invoice-contentbar {
                padding: 10px !important;
                margin-top: 5px;
                height: auto !important;
                overflow: visible !important;
                padding-bottom: 240px !important;
            }
            #pos-row-container, #pos-row-container-alt {
                flex-direction: column !important;
                height: auto !important;
                overflow: visible !important;
            }
            .main-pos-col, .sidebar-pos-col {
                flex: 0 0 100% !important;
                max-width: 100% !important;
                height: auto !important;
                margin-bottom: 20px !important;
            }
            body:not(.kiosk-mode) .main-pos-col,
            body:not(.kiosk-mode) .sidebar-pos-col {
                height: auto !important;
            }
            .main-pos-col .card.card_top,
            .sidebar-pos-col .card.card_top {
                height: auto !important;
                overflow: visible !important;
            }
            .table-responsive {
                height: auto !important;
                max-height: 400px !important;
                min-height: auto !important;
                overflow-y: auto !important;
            }
            .sidebar-pos-col #products {
                height: auto !important;
                max-height: 500px !important;
                overflow-y: auto !important;
            }
            .checkout-actions-row {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 12px !important;
            }
            .payment-button-group {
                justify-content: space-between !important;
                max-width: 100% !important;
                width: 100% !important;
                flex-wrap: nowrap !important;
                gap: 6px !important;
            }
            .btn-pos-action {
                flex: 1 1 0% !important;
                min-width: 0 !important;
                justify-content: center !important;
                padding: 0 4px !important;
                font-size: 10px !important;
                height: 38px !important;
                white-space: nowrap !important;
            }
            .btn-pos-action i {
                margin-right: 3px !important;
                font-size: 10px !important;
            }
            .pos-summary-banner {
                grid-template-columns: repeat(2, 1fr) !important;
            }
            .pos-summary-banner-item::after {
                display: none !important;
            }
            .cart-container {
                padding: 10px !important;
            }
            .pos-summary-panel {
                padding: 15px !important;
                margin-top: 15px;
            }
            .pos-summary-section {
                padding: 0 !important;
            }
            .pos-summary-field {
                margin-bottom: 15px !important;
            }
            .btn-pay-now {
                width: 100% !important;
                margin-top: 10px;
            }
            .grand-total-section {
                padding: 15px !important;
            }
            .grand-total-label {
                font-size: 16px !important;
            }
            .estimated_amount {
                font-size: 24px !important;
            }
            .ecommerce-sortby .col-md-3, 
            .ecommerce-sortby .col-md-8, 
            .ecommerce-sortby .col-md-1 {
                margin-bottom: 10px;
            }
            #category-sidebar, #category-sidebar-alt {
                margin-top: 20px;
            }
            .category-toggle-btn {
                display: none !important;
            }
            .sidebar-hidden #category-sidebar,
            .sidebar-hidden #category-sidebar-alt {
                display: block !important;
            }
            .table-responsive .table th, 
            .table-responsive .table td {
                white-space: nowrap !important;
                min-width: 80px;
            }
            .header_style_left {
                min-width: 150px !important;
            }
            .hide-on-mobile {
                display: none !important;
            }

            /* Summary Panel Responsiveness */
            .pos-summary-panel .summary-row {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 12px !important;
            }
            .pos-summary-field {
                width: 100% !important;
                flex: 1 1 auto !important;
                min-width: 0 !important;
                margin-bottom: 5px !important;
            }
            .pos-action-section {
                width: 100% !important;
            }
            .btn-pay-now {
                width: 100% !important;
                justify-content: center !important;
            }
            .pos-grand-total-value {
                width: 100% !important;
                justify-content: center !important;
                min-width: 0 !important;
            }
        }
        @media (max-width: 576px) {
            .ecommerce-sortby {
                flex-wrap: wrap !important;
                gap: 5px !important;
            }
            .ecommerce-sortby .col-auto {
                flex: 0 0 auto !important;
                max-width: none !important;
                margin-bottom: 5px !important;
            }
            .ecommerce-sortby .col {
                flex: 0 0 100% !important;
                max-width: 100% !important;
                margin-bottom: 5px !important;
            }
            .ecommerce-sortby div[style*="width: 115px"] {
                width: auto !important;
            }
            .select2-container {
                width: 100% !important;
            }
            .ecommerce-sortby .d-flex {
                width: 100%;
            }
        }

        /* ===== Inline Payment Panel ===== */
        #inline-payment-panel {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            margin-top: 10px;
            overflow: hidden;
            animation: slideDown 0.25s ease;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .inline-payment-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 16px;
            background: linear-gradient(135deg, #0f172a, #1e3a8a);
            color: #fff;
            font-size: 14px;
            font-weight: 600;
        }
        .inline-payment-close {
            background: rgba(255,255,255,0.15);
            border: none;
            color: #fff;
            border-radius: 6px;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s;
        }
        .inline-payment-close:hover { background: rgba(255,255,255,0.25); }

        .pay-type-row {
            display: flex;
            gap: 16px;
            padding: 10px 16px;
            background: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
        }
        .pay-type-opt {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            cursor: pointer;
        }

        .inline-pay-body {
            display: flex;
            gap: 0;
        }
        .inline-pay-inputs {
            flex: 1;
            padding: 12px 14px;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .inline-pay-summary {
            flex: 1;
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .ifield { display: flex; flex-direction: column; }
        .ifield label, .ifield-label {
            font-size: 10px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 3px;
        }
        .ifield .form-control, .ifield textarea {
            font-size: 13px;
            border-radius: 7px;
            border: 1.5px solid #e2e8f0;
            background: #fff;
            padding: 5px 10px;
            height: 34px;
        }
        .ifield textarea { height: auto; resize: none; }
        .ifield-inline {
            flex-direction: row;
            align-items: center;
            gap: 8px;
        }
        .ifield-bank-name {
            flex: 1;
            font-size: 12px;
            font-weight: 600;
            color: #334155;
        }
        .ifield-inline .form-control { max-width: 110px; }

        .pay-summary-title {
            font-size: 12px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            padding-bottom: 6px;
            border-bottom: 2px solid #e2e8f0;
        }
        .pay-detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 3px 0;
            font-size: 12px;
            color: #475569;
            border-bottom: 1px dashed #f1f5f9;
        }
        .pay-detail-row strong { font-size: 12px; }
        .pay-detail-highlight {
            background: #eff6ff;
            border-radius: 6px;
            padding: 5px 8px !important;
            margin: 4px 0;
            border-bottom: none !important;
        }
        .pay-action-row {
            display: flex;
            gap: 8px;
            margin-top: 8px;
        }
        .btn-full-paid {
            flex: 1;
            padding: 6px 10px;
            background: #ecfdf5;
            color: #059669;
            border: 1.5px solid #a7f3d0;
            border-radius: 7px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-full-paid:hover { background: #d1fae5; }
        .btn-full-due {
            flex: 1;
            padding: 6px 10px;
            background: #fef2f2;
            color: #dc2626;
            border: 1.5px solid #fecaca;
            border-radius: 7px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-full-due:hover { background: #fee2e2; }
        .btn-checkout {
            width: 100%;
            margin-top: 10px;
            padding: 10px;
            background: linear-gradient(135deg, #0f172a, #1d4ed8);
            color: #fff;
            border: none;
            border-radius: 9px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(29,78,216,0.3);
            transition: all 0.2s;
        }
        .btn-checkout:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(29,78,216,0.4);
        }

        /* ===== Dark Mode Fixes for Unified Payment Panel ===== */
        body.dark-theme .pos-summary-panel {
            background: #1e293b;
            border-color: #334155;
            box-shadow: none;
        }
        body.dark-theme .pos-summary-field label,
        body.dark-theme .ifield label, 
        body.dark-theme .ifield-label {
            color: #94a3b8;
        }
        body.dark-theme .pos-summary-field .form-control,
        body.dark-theme .ifield .form-control, 
        body.dark-theme .ifield textarea {
            background: #0f172a;
            border-color: #334155;
            color: #f1f5f9;
        }
        body.dark-theme .pos-summary-field .form-control:focus,
        body.dark-theme .ifield .form-control:focus, 
        body.dark-theme .ifield textarea:focus {
            background: #1e293b;
            color: #fff;
        }
        body.dark-theme .unified-checkout-top {
            border-bottom-color: #334155 !important;
        }
        body.dark-theme .pay-type-opt {
            color: #cbd5e1;
        }
        body.dark-theme .inline-pay-inputs {
            border-right-color: #334155 !important;
        }
        body.dark-theme .ifield-bank-name {
            color: #cbd5e1;
        }
        body.dark-theme .pay-summary-title {
            color: #f1f5f9;
            border-bottom-color: #334155;
        }
        body.dark-theme .pay-detail-row {
            color: #94a3b8;
            border-bottom-color: #334155;
        }
        body.dark-theme .pay-detail-row strong {
            color: #e2e8f0;
        }
        body.dark-theme .pay-detail-highlight {
            background: #0f172a;
            border: 1px solid #334155 !important;
        }
        body.dark-theme .btn-full-paid {
            background: #064e3b;
            color: #34d399;
            border-color: #065f46;
        }
        body.dark-theme .btn-full-paid:hover {
            background: #065f46;
        }
        body.dark-theme .btn-full-due {
            background: #7f1d1d;
            color: #f87171;
            border-color: #991b1b;
        }
        body.dark-theme .btn-full-due:hover {
            background: #991b1b;
        }

        /* POS Toggle Container Styling (Light & Dark Mode visibility) */
        .pos-toggle-container span.text-white {
            color: #334155 !important;
        }
        body.dark-theme .pos-toggle-container span.text-white {
            color: #f1f5f9 !important;
        }

        /* Light Mode Buttons */
        body:not(.dark-theme) .pos-toggle-container .btn-warning {
            background-color: #ffa800 !important;
            border-color: #ffa800 !important;
            color: #ffffff !important;
        }
        body:not(.dark-theme) .pos-toggle-container .btn-info {
            background-color: #17a2b8 !important;
            border-color: #17a2b8 !important;
            color: #ffffff !important;
        }
        body:not(.dark-theme) .pos-toggle-container .btn-success {
            background-color: #28a745 !important;
            border-color: #28a745 !important;
            color: #ffffff !important;
        }
        body:not(.dark-theme) .pos-toggle-container .btn-dark {
            background-color: #343a40 !important;
            border-color: #343a40 !important;
            color: #ffffff !important;
        }

        /* Hover States for Light Mode Buttons */
        body:not(.dark-theme) .pos-toggle-container .btn-warning:hover {
            background-color: #e69700 !important;
            border-color: #e69700 !important;
        }
        body:not(.dark-theme) .pos-toggle-container .btn-info:hover {
            background-color: #138496 !important;
            border-color: #117a8b !important;
        }
        body:not(.dark-theme) .pos-toggle-container .btn-success:hover {
            background-color: #218838 !important;
            border-color: #1e7e34 !important;
        }
        body:not(.dark-theme) .pos-toggle-container .btn-dark:hover {
            background-color: #23272b !important;
            border-color: #1d2124 !important;
        }

        /* Dark Mode Buttons */
        body.dark-theme .pos-toggle-container .btn-warning {
            background-color: #e69700 !important;
            border-color: #e69700 !important;
            color: #ffffff !important;
        }
        body.dark-theme .pos-toggle-container .btn-info {
            background-color: #117a8b !important;
            border-color: #117a8b !important;
            color: #ffffff !important;
        }
        body.dark-theme .pos-toggle-container .btn-success {
            background-color: #1e7e34 !important;
            border-color: #1e7e34 !important;
            color: #ffffff !important;
        }
        body.dark-theme .pos-toggle-container .btn-dark {
            background-color: #1d2124 !important;
            border-color: #1d2124 !important;
            color: #ffffff !important;
        }

        /* ==================== POS PREMIUM DARK THEME OVERRIDES ==================== */

        /* Page Background & Workspace */
        body, .rightbar, #containerbar, .invoice-contentbar {
            background-color: #0d1220 !important;
            color: #f1f5f9 !important;
        }

        /* Sidebar override for main app */
        body.kiosk-mode .rightbar {
            background-color: #0d1220 !important;
        }

        /* Lock layout to viewport height & hide browser scrollbars (high specificity) */
        html, body, body.bg-slate-50, body.dark-theme, body.kiosk-mode {
            height: 100vh !important;
            overflow: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        #containerbar, body #containerbar {
            height: 100vh !important;
            overflow: hidden !important;
        }
        .rightbar, body .rightbar {
            height: 100vh !important;
            overflow: hidden !important;
            display: flex !important;
            flex-direction: column !important;
        }
        .rightbar > .flex-grow.w-full, body .rightbar > .flex-grow.w-full {
            flex: 1 !important;
            min-height: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            height: auto !important;
            overflow: hidden !important;
        }
        body.kiosk-mode .rightbar > .flex-grow.w-full {
            height: 100vh !important;
        }

        /* Card Overrides & Full Height Layout Locking */
        .invoice-contentbar .card.card_top, 
        .invoice-contentbar .card {
            border-radius: 12px !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15) !important;
        }
        .invoice-contentbar {
            display: flex !important;
            flex-direction: column !important;
            height: 100% !important;
            overflow: hidden !important;
            padding: 10px 10px 0 10px !important;
            margin: 0 !important;
            flex: 1 !important;
            min-height: 0 !important;
        }
        body:not(.kiosk-mode) .invoice-contentbar {
            height: 100% !important;
        }
        #pos-row-container, #pos-row-container-alt {
            display: flex !important;
            flex: 1 1 0% !important;
            min-height: 0 !important;
            margin: 0 -10px !important;
        }
        .main-pos-col {
            height: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            min-height: 0 !important;
        }
        body:not(.kiosk-mode) .main-pos-col {
            height: 100% !important;
        }
        .main-pos-col .card.card_top {
            height: 100% !important;
            flex: 1 1 0% !important;
            min-height: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            overflow-y: auto !important;
            margin-bottom: 0 !important;
        }
        #payment_form {
            height: 100% !important;
            flex: 1 1 0% !important;
            min-height: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            overflow: visible !important;
        }
        .cart-container {
            height: 100% !important;
            flex: 1 1 0% !important;
            min-height: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            overflow: visible !important;
        }
        .cart-search-header {
            display: flex !important;
            flex-direction: column !important;
            flex: 0 0 auto !important;
            overflow: visible !important;
        }
        .cart-head {
            display: flex !important;
            flex-direction: column !important;
            flex: 1 1 0% !important;
            min-height: 0 !important;
            overflow: visible !important;
        }
        .table-responsive {
            flex: 1 1 0% !important;
            min-height: 150px !important;
            overflow-y: auto !important;
            margin-bottom: 15px !important;
            border-radius: 8px !important;
        }
        .pos-summary-panel {
            margin-top: auto !important;
            z-index: 10 !important;
            padding-top: 10px !important;
            flex: 0 0 auto !important;
        }
        
        .sidebar-pos-col {
            height: 100% !important;
            display: flex;
            flex-direction: column !important;
            min-height: 0 !important;
        }
        body:not(.kiosk-mode) .sidebar-pos-col {
            height: 100% !important;
        }
        .sidebar-pos-col .card.card_top {
            flex: 1 1 0% !important;
            min-height: 0 !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .card-body {
            padding: 15px !important;
            display: flex !important;
            flex-direction: column !important;
            flex: 1 1 0% !important;
            min-height: 0 !important;
        }

        /* Make only products area scroll internally on right sidebar */
        .sidebar-pos-col #products {
            flex: 1 1 0% !important;
            min-height: 0 !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding-right: 5px;
        }

        /* Responsive CSS Grid for Products to prevent squishing at 100% zoom */
        #products .row {
            display: grid !important;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)) !important;
            gap: 10px !important;
            margin: 0 !important;
            width: 100% !important;
        }
        #products .row > [class*="col-"] {
            width: 100% !important;
            max-width: 100% !important;
            flex: none !important;
            padding: 0 !important;
        }

        /* Custom scrollbar for products list */
        .sidebar-pos-col #products::-webkit-scrollbar {
            width: 6px !important;
        }
        .sidebar-pos-col #products::-webkit-scrollbar-track {
            background: transparent !important;
        }
        .sidebar-pos-col #products::-webkit-scrollbar-thumb {
            border-radius: 3px !important;
        }
        body.dark-theme .sidebar-pos-col #products::-webkit-scrollbar-thumb {
            background: #242f49 !important;
        }
        body:not(.dark-theme) .sidebar-pos-col #products::-webkit-scrollbar-thumb {
            background: #cbd5e1 !important;
        }

        /* Top Status/Toggles Bar */
        .pos-toggle-container {
            display: flex !important;
            align-items: center !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
            margin-bottom: 15px !important;
            justify-content: center !important;
        }

        #online_status {
            background-color: #10b981 !important;
            border: 1px solid #10b981 !important;
            color: #ffffff !important;
            border-radius: 6px !important;
            padding: 6px 12px !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            margin: 0 !important;
            text-transform: uppercase !important;
        }

        #online_status i {
            animation: blink-animation 1.5s infinite;
        }

        @keyframes blink-animation {
            0% { opacity: 0.3; }
            50% { opacity: 1; }
            100% { opacity: 0.3; }
        }

        .pos-toggle-container .btn {
            height: 34px !important;
            padding: 0 14px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            transition: all 0.2s ease !important;
            text-transform: capitalize !important;
        }

        .pos-toggle-container .btn-warning {
            background: transparent !important;
            border: 1.5px solid #f59e0b !important;
            color: #f59e0b !important;
        }
        .pos-toggle-container .btn-warning:hover {
            background: #f59e0b !important;
            color: #ffffff !important;
        }

        .pos-toggle-container .btn-info {
            background: transparent !important;
            border: 1.5px solid #06b6d4 !important;
            color: #06b6d4 !important;
        }
        .pos-toggle-container .btn-info:hover {
            background: #06b6d4 !important;
            color: #ffffff !important;
        }

        .pos-toggle-container .btn-success {
            background: transparent !important;
            border: 1.5px solid #10b981 !important;
            color: #10b981 !important;
        }
        .pos-toggle-container .btn-success:hover {
            background: #10b981 !important;
            color: #ffffff !important;
        }

        .pos-toggle-container .btn-dark {
            background: transparent !important;
            border: 1.5px solid #ef4444 !important;
            color: #ef4444 !important;
        }
        .pos-toggle-container .btn-dark:hover {
            background: #ef4444 !important;
            color: #ffffff !important;
        }

        .pos-toggle-container span.text-white {
            color: #94a3b8 !important;
            font-size: 12px !important;
        }

        .switch {
            margin-bottom: 0 !important;
        }

        /* Date and Customer Row */
        .ecommerce-sortby {
            margin-bottom: 15px !important;
        }

        #date, .ecommerce-sortby input[type="date"] {
            border-radius: 6px !important;
            height: 38px !important;
            font-size: 13px !important;
            padding: 6px 10px !important;
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            border-radius: 6px !important;
            height: 38px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px !important;
            padding-left: 12px !important;
            font-size: 13px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }

        /* Add Customer Plus Button */
        .extra_btn {
            background-color: #2563eb !important;
            border: none !important;
            color: #ffffff !important;
            border-radius: 6px !important;
            height: 38px !important;
            width: 38px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s ease !important;
        }

        .extra_btn:hover {
            background-color: #1d4ed8 !important;
        }

        /* Barcode Scanner Input Group */
        .input-group-prepend .barcod_style {
            border-right: none !important;
            border-radius: 6px 0 0 6px !important;
            height: 38px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        .product_search {
            border-radius: 0 6px 6px 0 !important;
            height: 38px !important;
            font-size: 14px !important;
        }

        .product_search:focus {
            border-color: #3b82f6 !important;
            box-shadow: none !important;
        }

        /* Cart Table Layout */
        .table {
            border: none !important;
            margin-bottom: 0 !important;
            width: 100% !important;
        }

        .table thead th {
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.6px !important;
            border-top: none !important;
            font-size: 12px !important;
            padding: 12px 10px !important;
        }

        .table tbody tr {
            transition: background-color 0.15s ease !important;
        }

        .table tbody tr td,
        .table tbody tr td:first-child,
        .table tbody tr td:last-child,
        .table_data_style_left,
        .table_data_style_right {
            border-radius: 0px !important;
        }

        .table tbody td {
            border-top: none !important;
            font-size: 13px !important;
            padding: 10px 8px !important;
            vertical-align: middle !important;
        }

        .table tbody td input.form-control,
        .table tbody td select.form-control {
            font-size: 13px !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
            text-align: center !important;
            padding: 4px 6px !important;
            height: 32px !important;
        }

        .table tbody td input.form-control[readonly] {
            cursor: not-allowed !important;
        }

        .table tbody td .btn-danger {
            background-color: #ef4444 !important;
            border: none !important;
            color: #ffffff !important;
            padding: 6px 10px !important;
            border-radius: 6px !important;
            font-size: 12px !important;
            transition: background-color 0.2s !important;
        }

        .table tbody td .btn-danger:hover {
            background-color: #dc2626 !important;
        }

        /* Checkout inputs single horizontal row */
        .checkout-input-row {
            display: grid !important;
            grid-template-columns: repeat(8, 1fr) !important;
            gap: 10px !important;
            margin-bottom: 15px !important;
            align-items: end !important;
        }

        @media (max-width: 1024px) {
            .checkout-input-row {
                grid-template-columns: repeat(4, 1fr) !important;
            }
        }

        @media (max-width: 576px) {
            .checkout-input-row {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }

        .checkout-col {
            display: flex !important;
            flex-direction: column !important;
        }

        .checkout-col label {
            font-size: 10px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.6px !important;
            margin-bottom: 4px !important;
        }

        .checkout-col .form-control,
        .checkout-col select {
            border-radius: 6px !important;
            height: 38px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            padding: 6px 10px !important;
            width: 100% !important;
        }

        .checkout-col .form-control:focus {
            border-color: #3b82f6 !important;
            box-shadow: none !important;
        }

        .checkout-col .input-group {
            display: flex !important;
            width: 100% !important;
            flex-wrap: nowrap !important;
        }

        .checkout-col .input-group .discount_val_input {
            border-top-right-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
            flex: 1 1 auto !important;
            min-width: 0 !important;
            width: auto !important;
        }

        .checkout-col .input-group .discount_type_select {
            border-top-left-radius: 0 !important;
            border-bottom-left-radius: 0 !important;
            border-left: 0 !important;
            width: auto !important;
            flex: 0 0 52px !important;
            max-width: 56px !important;
            padding: 0 2px !important;
            text-align-last: center !important;
            background: #f8f9fa !important;
            cursor: pointer !important;
        }

        /* Grand Total display column in row */
        .checkout-col.grand-total-col {
            display: flex !important;
            flex-direction: column !important;
        }

        .grand-total-box {
            border-radius: 6px !important;
            height: 38px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            padding: 0 12px !important;
            font-size: 16px !important;
            font-weight: 800 !important;
            text-align: right !important;
        }

        /* Payment actions row (Options on left, actions on right) */
        .checkout-actions-row {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            margin-bottom: 20px !important;
            flex-wrap: wrap !important;
            gap: 15px !important;
            width: 100% !important;
        }

        /* Single/Multiple account selectors */
        .payment-type-selectors {
            display: flex !important;
            gap: 15px !important;
            align-items: center !important;
        }

        .payment-type-selectors label {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            margin-bottom: 0 !important;
            cursor: pointer !important;
        }

        .payment-type-selectors input[type="radio"] {
            appearance: none !important;
            -webkit-appearance: none !important;
            width: 16px !important;
            height: 16px !important;
            border-radius: 50% !important;
            display: inline-block !important;
            position: relative !important;
            cursor: pointer !important;
            vertical-align: middle !important;
            outline: none !important;
        }

        .payment-type-selectors input[type="radio"]:checked::after {
            content: "" !important;
            position: absolute !important;
            top: 3px !important;
            left: 3px !important;
            width: 6px !important;
            height: 6px !important;
            border-radius: 50% !important;
        }

        /* Checkout Button Row */
        .payment-button-group {
            display: flex !important;
            gap: 12px !important;
            align-items: center !important;
            flex: 1 !important;
            justify-content: flex-end !important;
            max-width: 650px !important;
        }

        .btn-pos-action {
            height: 48px !important;
            padding: 0 24px !important;
            border-radius: 8px !important;
            font-size: 15px !important;
            font-weight: 900 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 8px !important;
            border: none !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
            color: #ffffff !important;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12) !important;
            flex: 1 !important;
            min-width: 120px !important;
            max-width: 220px !important;
        }

        .btn-pos-action.btn-full-paid {
            background-color: #10b981 !important;
        }
        .btn-pos-action.btn-full-paid:hover {
            background-color: #059669 !important;
            transform: translateY(-1px) !important;
        }

        .btn-pos-action.btn-full-due {
            background-color: #ef4444 !important;
        }
        .btn-pos-action.btn-full-due:hover {
            background-color: #dc2626 !important;
            transform: translateY(-1px) !important;
        }

        .btn-pos-action.btn-checkout {
            background-color: #2563eb !important;
        }
        .btn-pos-action.btn-checkout:hover {
            background-color: #1d4ed8 !important;
            transform: translateY(-1px) !important;
        }

        /* Summary Banner Block */
        .pos-summary-banner {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            border-radius: 8px !important;
            padding: 0 !important;
            margin-top: 15px !important;
            overflow: hidden !important;
        }

        .pos-summary-banner-item {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 12px !important;
            text-align: center !important;
            position: relative !important;
        }

        .pos-summary-banner-item:not(:last-child)::after {
            content: "" !important;
            position: absolute !important;
            right: 0 !important;
            top: 15% !important;
            height: 70% !important;
        }

        .banner-label {
            font-size: 13px !important;
            font-weight: 900 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.6px !important;
            margin-bottom: 4px !important;
        }

        .banner-value {
            font-size: 24px !important;
            font-weight: 900 !important;
        }

        /* FAST IT Brand Highlight colors for POS Summary Banner (Alternating Orange and Blue) */
        .pos-summary-banner-item:nth-child(odd) {
            background-color: #FF6700 !important;
        }
        .pos-summary-banner-item:nth-child(odd) .banner-label {
            color: rgba(255, 255, 255, 0.9) !important;
        }
        .pos-summary-banner-item:nth-child(odd) .banner-value {
            color: #ffffff !important;
        }

        .pos-summary-banner-item:nth-child(even) {
            background-color: #000CE2 !important;
        }
        .pos-summary-banner-item:nth-child(even) .banner-label {
            color: rgba(255, 255, 255, 0.9) !important;
        }
        .pos-summary-banner-item:nth-child(even) .banner-value {
            color: #ffffff !important;
        }

        /* Hide vertical dividers to keep highlighted blocks solid and clean */
        .pos-summary-banner-item::after {
            display: none !important;
        }

        /* Right Side Category & Product Cards */
        #category-sidebar, #category-sidebar-alt {
            border-radius: 12px !important;
        }

        #getProductsByCat, .sidebar-pos-col select {
            border-radius: 6px !important;
            height: 38px !important;
        }

        /* Product Cards */
        .productcss {
            border-radius: 8px !important;
            padding: 8px !important;
            position: relative !important;
            overflow: hidden !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            text-align: center !important;
        }

        .productcss:hover {
            border-color: #3b82f6 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.15) !important;
        }

        .productcss .product-head {
            background-color: #ffffff !important;
            border-radius: 6px !important;
            width: 100% !important;
            height: 80px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 6px !important;
            margin-bottom: 6px !important;
            position: relative !important;
        }

        .productcss .product-head img {
            max-height: 100% !important;
            max-width: 100% !important;
            object-fit: contain !important;
        }

        .productcss .stock-badge {
            position: absolute !important;
            top: 0 !important;
            right: 0 !important;
            z-index: 10 !important;
        }

        .productcss .stock-badge span.badge-danger {
            background-color: #ef4444 !important;
            color: #ffffff !important;
            font-size: 9px !important;
            font-weight: 700 !important;
            padding: 3px 6px !important;
            border-radius: 0 6px 0 6px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.4px !important;
        }

        .productcss .product-body {
            padding: 0 !important;
            width: 100% !important;
        }

        .productcss .product-name {
            font-size: 12px !important;
            font-weight: 600 !important;
            margin-bottom: 2px !important;
            height: 24px !important;
            line-height: 1.1 !important;
        }

        .productcss .product-code {
            font-size: 10px !important;
            margin-bottom: 2px !important;
        }

        .productcss .stock-status {
            font-size: 10px !important;
            font-weight: 500 !important;
        }

        /* Scrollbar Styling for Table */
        .table-responsive::-webkit-scrollbar {
            width: 8px !important;
            height: 8px !important;
        }
        .table-responsive::-webkit-scrollbar-track {
            border-radius: 4px !important;
        }
        .table-responsive::-webkit-scrollbar-thumb {
            border-radius: 4px !important;
        }

        /* ==================== THEME MODE STYLING ==================== */

        /* 1. DARK THEME SPECIFIC STYLES */
        body.dark-theme,
        body.dark-theme .rightbar,
        body.dark-theme #containerbar,
        body.dark-theme .invoice-contentbar {
            background-color: #0d1220 !important;
            color: #f1f5f9 !important;
        }
        
        body.dark-theme .invoice-contentbar .card.card_top, 
        body.dark-theme .invoice-contentbar .card {
            background-color: #121829 !important;
            border: 1px solid #1e293b !important;
        }

        body.dark-theme .pos-summary-panel {
            background-color: #121829 !important;
            border-top: 1.5px solid #1c233a !important;
        }

        body.dark-theme #date, 
        body.dark-theme .ecommerce-sortby input[type="date"] {
            background-color: #151b2e !important;
            border: 1.5px solid #242f49 !important;
            color: #ffffff !important;
        }

        body.dark-theme .select2-container--default .select2-selection--single {
            background-color: #151b2e !important;
            border: 1.5px solid #242f49 !important;
        }

        body.dark-theme .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #ffffff !important;
        }

        body.dark-theme .input-group-prepend .barcod_style {
            background-color: #151b2e !important;
            border: 1.5px solid #242f49 !important;
            color: #94a3b8 !important;
        }

        body.dark-theme .product_search {
            background-color: #151b2e !important;
            border: 1.5px solid #242f49 !important;
            color: #ffffff !important;
        }

        body.dark-theme .product_search:focus {
            background-color: #151b2e !important;
            color: #ffffff !important;
        }

        body.dark-theme .table-responsive {
            border: 1px solid #1c233a !important;
        }

        body.dark-theme .table {
            background-color: #0c0f1d !important;
        }

        body.dark-theme .table thead th {
            background-color: #151c30 !important;
            color: #94a3b8 !important;
            border-bottom: 2px solid #242f49 !important;
        }

        body.dark-theme .table tbody {
            background-color: #0c0f1d !important;
        }

        body.dark-theme .table tbody tr {
            background-color: #0c0f1d !important;
        }

        body.dark-theme .table tbody tr:hover {
            background-color: #111628 !important;
        }

        body.dark-theme .table tbody td {
            color: #cbd5e1 !important;
            border-bottom: 1px solid #1c233a !important;
        }

        body.dark-theme .table tbody td input.form-control,
        body.dark-theme .table tbody td select.form-control {
            background-color: #151b2e !important;
            border: 1px solid #242f49 !important;
            color: #ffffff !important;
        }

        body.dark-theme .table tbody td input.form-control[readonly] {
            background-color: #1c233a !important;
            color: #38bdf8 !important;
            border-color: #242f49 !important;
        }

        body.dark-theme .checkout-col label {
            color: #94a3b8 !important;
        }

        body.dark-theme .checkout-col .form-control,
        body.dark-theme .checkout-col select {
            background-color: #151b2e !important;
            border: 1.5px solid #242f49 !important;
            color: #ffffff !important;
        }

        body.dark-theme .grand-total-box {
            background-color: #0f1423 !important;
            border: 1.5px solid #1d4ed8 !important;
            color: #3b82f6 !important;
        }

        body.dark-theme .payment-type-selectors label {
            color: #cbd5e1 !important;
        }

        body.dark-theme .payment-type-selectors input[type="radio"] {
            border: 2px solid #475569 !important;
            background: transparent !important;
        }

        body.dark-theme .payment-type-selectors input[type="radio"]:checked {
            border-color: #ffffff !important;
        }

        body.dark-theme .payment-type-selectors input[type="radio"]:checked::after {
            background-color: #ffffff !important;
        }

        body.dark-theme .pos-summary-banner {
            background-color: transparent !important;
            border: none !important;
        }

        body.dark-theme .pos-summary-banner-item:not(:last-child)::after {
            border-right: 1.5px dashed #242f49 !important;
        }

        body.dark-theme .banner-label {
            color: #94a3b8 !important;
        }

        body.dark-theme .banner-value {
            color: #ffffff !important;
        }

        body.dark-theme #category-sidebar, 
        body.dark-theme #category-sidebar-alt {
            background-color: #121829 !important;
            border: 1px solid #1e293b !important;
        }

        body.dark-theme #getProductsByCat, 
        body.dark-theme .sidebar-pos-col select {
            background-color: #151b2e !important;
            border: 1.5px solid #242f49 !important;
            color: #ffffff !important;
        }

        body.dark-theme .productcss {
            background-color: #151b2e !important;
            border: 1px solid #242f49 !important;
        }

        body.dark-theme .productcss .product-name {
            color: #ffffff !important;
        }

        body.dark-theme .productcss .product-code {
            color: #94a3b8 !important;
        }

        body.dark-theme .productcss .stock-status {
            color: #cbd5e1 !important;
        }

        body.dark-theme .table-responsive::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.3) !important;
        }
        body.dark-theme .table-responsive::-webkit-scrollbar-thumb {
            background: #242f49 !important;
            border: 1px solid #1c233a !important;
        }
        body.dark-theme .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #3b82f6 !important;
        }

        /* Multiple Account Dark Theme overrides */
        body.dark-theme .inline-checking-section {
            background-color: #151b2e !important;
            border: 1.5px solid #242f49 !important;
        }
        body.dark-theme .inline-checking-title {
            color: #94a3b8 !important;
        }
        body.dark-theme .bank-name-label {
            color: #cbd5e1 !important;
        }
        body.dark-theme .bank-amount-input {
            background-color: #0c0f1d !important;
            border: 1.5px solid #242f49 !important;
            color: #ffffff !important;
        }
        body.dark-theme .bank-amount-input:focus {
            border-color: #3b82f6 !important;
            background-color: #0c0f1d !important;
            color: #ffffff !important;
        }

        /* Modal Dark Theme overrides */
        body.dark-theme .modal-content {
            background-color: #121829 !important;
            border-color: #1e293b !important;
            color: #f1f5f9 !important;
        }
        body.dark-theme .modal-header {
            border-bottom: 1.5px solid #1e293b !important;
            background-color: #151c30 !important;
        }
        body.dark-theme .modal-footer {
            border-top: 1.5px solid #1e293b !important;
            background-color: #151c30 !important;
        }
        body.dark-theme .modal-body {
            background-color: #121829 !important;
        }
        body.dark-theme .modal-body .table th {
            background-color: #151c30 !important;
            border-bottom: 2px solid #242f49 !important;
            color: #94a3b8 !important;
        }
        body.dark-theme .modal-body .table td {
            border-bottom: 1px solid #1c233a !important;
            color: #cbd5e1 !important;
        }
        body.dark-theme .modal-body input.form-control,
        body.dark-theme .modal-body select.form-control {
            background-color: #151b2e !important;
            border: 1px solid #242f49 !important;
            color: #ffffff !important;
        }
        body.dark-theme .header_bg {
            background: #151c30 !important;
            color: #94a3b8 !important;
        }

        /* 2. LIGHT THEME SPECIFIC STYLES */
        body:not(.dark-theme),
        body:not(.dark-theme) .rightbar,
        body:not(.dark-theme) #containerbar,
        body:not(.dark-theme) .invoice-contentbar {
            background-color: #f1f5f9 !important;
            color: #1e293b !important;
        }

        body:not(.dark-theme) .invoice-contentbar .card.card_top, 
        body:not(.dark-theme) .invoice-contentbar .card {
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
        }

        body:not(.dark-theme) .pos-summary-panel {
            background-color: #ffffff !important;
            border-top: 1.5px solid #e2e8f0 !important;
        }

        body:not(.dark-theme) #date, 
        body:not(.dark-theme) .ecommerce-sortby input[type="date"] {
            background-color: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #0f172a !important;
        }

        body:not(.dark-theme) .select2-container--default .select2-selection--single {
            background-color: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
        }

        body:not(.dark-theme) .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #0f172a !important;
        }

        body:not(.dark-theme) .input-group-prepend .barcod_style {
            background-color: #f1f5f9 !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #475569 !important;
        }

        body:not(.dark-theme) .product_search {
            background-color: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #0f172a !important;
        }

        body:not(.dark-theme) .product_search:focus {
            background-color: #ffffff !important;
            color: #0f172a !important;
        }

        body:not(.dark-theme) .table-responsive {
            border: 1px solid #e2e8f0 !important;
        }

        body:not(.dark-theme) .table {
            background-color: #ffffff !important;
        }

        body:not(.dark-theme) .table thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            border-bottom: 2px solid #e2e8f0 !important;
        }

        body:not(.dark-theme) .table tbody {
            background-color: #ffffff !important;
        }

        body:not(.dark-theme) .table tbody tr {
            background-color: #ffffff !important;
        }

        body:not(.dark-theme) .table tbody tr:hover {
            background-color: #f1f5f9 !important;
        }

        body:not(.dark-theme) .table tbody td {
            color: #334155 !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }

        body:not(.dark-theme) .table tbody td input.form-control,
        body:not(.dark-theme) .table tbody td select.form-control {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #0f172a !important;
        }

        body:not(.dark-theme) .table tbody td input.form-control[readonly] {
            background-color: #f1f5f9 !important;
            color: #1d4ed8 !important;
            border-color: #cbd5e1 !important;
        }

        body:not(.dark-theme) .checkout-col label {
            color: #475569 !important;
        }

        body:not(.dark-theme) .checkout-col .form-control,
        body:not(.dark-theme) .checkout-col select {
            background-color: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #0f172a !important;
        }

        body:not(.dark-theme) .grand-total-box {
            background-color: #eff6ff !important;
            border: 1.5px solid #2563eb !important;
            color: #1d4ed8 !important;
        }

        body:not(.dark-theme) .payment-type-selectors label {
            color: #334155 !important;
        }

        body:not(.dark-theme) .payment-type-selectors input[type="radio"] {
            border: 2px solid #94a3b8 !important;
            background: #ffffff !important;
        }

        body:not(.dark-theme) .payment-type-selectors input[type="radio"]:checked {
            border-color: #2563eb !important;
        }

        body:not(.dark-theme) .payment-type-selectors input[type="radio"]:checked::after {
            background-color: #2563eb !important;
        }

        body:not(.dark-theme) .pos-summary-banner {
            background-color: transparent !important;
            border: none !important;
        }

        body:not(.dark-theme) .pos-summary-banner-item:not(:last-child)::after {
            border-right: 1.5px dashed #cbd5e1 !important;
        }

        body:not(.dark-theme) .banner-label {
            color: #64748b !important;
        }

        body:not(.dark-theme) .banner-value {
            color: #0f172a !important;
        }

        body:not(.dark-theme) #category-sidebar, 
        body:not(.dark-theme) #category-sidebar-alt {
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
        }

        body:not(.dark-theme) #getProductsByCat, 
        body:not(.dark-theme) .sidebar-pos-col select {
            background-color: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #0f172a !important;
        }

        body:not(.dark-theme) .productcss {
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
        }

        body:not(.dark-theme) .productcss .product-name {
            color: #0f172a !important;
        }

        body:not(.dark-theme) .productcss .product-code {
            color: #64748b !important;
        }

        body:not(.dark-theme) .productcss .stock-status {
            color: #334155 !important;
        }

        body:not(.dark-theme) .table-responsive::-webkit-scrollbar-track {
            background: rgba(241, 245, 249, 0.5) !important;
        }
        body:not(.dark-theme) .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e1 !important;
            border: 1px solid #e2e8f0 !important;
        }
        body:not(.dark-theme) .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #94a3b8 !important;
        }

        /* Multiple Account Light Theme overrides */
        body:not(.dark-theme) .inline-checking-section {
            background-color: #f8fafc !important;
            border: 1.5px solid #cbd5e1 !important;
        }
        body:not(.dark-theme) .inline-checking-title {
            color: #475569 !important;
        }
        body:not(.dark-theme) .bank-name-label {
            color: #334155 !important;
        }
        body:not(.dark-theme) .bank-amount-input {
            background-color: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #0f172a !important;
        }
        body:not(.dark-theme) .bank-amount-input:focus {
            border-color: #2563eb !important;
            background-color: #ffffff !important;
            color: #0f172a !important;
        }
        body:not(.dark-theme) .header_bg {
            background: #f8fafc !important;
            color: #475569 !important;
        }
        body:not(.dark-theme) .table thead th {
            background-color: #f8fafc !important;
            color: #475569 !important;
            border-bottom: 2px solid #e2e8f0 !important;
        }

        /* Multiple Accounts Modal custom styles */
        body.dark-theme .grand-total-banner {
            background-color: rgba(59, 130, 246, 0.15) !important;
            border: 1.5px solid rgba(59, 130, 246, 0.3) !important;
            color: #60a5fa !important;
        }
        body.dark-theme .bank-badge {
            background-color: #1e293b !important;
            color: #cbd5e1 !important;
        }
        body.dark-theme .bank-account-item {
            background-color: #151b2e !important;
            border-color: #242f49 !important;
        }
        body.dark-theme .bank-modal-amount-input {
            background-color: #0c0f1d !important;
            border-color: #242f49 !important;
            color: #ffffff !important;
        }
        body.dark-theme .bank-modal-amount-input:focus {
            border-color: #3b82f6 !important;
            background-color: #0c0f1d !important;
            color: #ffffff !important;
        }

        body:not(.dark-theme) .grand-total-banner {
            background-color: rgba(59, 130, 246, 0.08) !important;
            border: 1.5px solid rgba(59, 130, 246, 0.2) !important;
            color: #2563eb !important;
        }
        body:not(.dark-theme) .bank-badge {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
        }
        body:not(.dark-theme) .bank-account-item {
            background-color: #f8fafc !important;
            border-color: #e2e8f0 !important;
        }
        body:not(.dark-theme) .bank-modal-amount-input {
            background-color: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #0f172a !important;
        }
        body:not(.dark-theme) .bank-modal-amount-input:focus {
            border-color: #2563eb !important;
            background-color: #ffffff !important;
            color: #0f172a !important;
        }

        /* Online sale form inputs styling */
        body.dark-theme .courier-select,
        body.dark-theme .platform-select,
        body.dark-theme .source-link-input {
            background-color: #151b2e !important;
            border: 1.5px solid #242f49 !important;
            color: #ffffff !important;
            border-radius: 6px !important;
            height: 32px !important;
            padding: 4px 8px !important;
            outline: none !important;
            font-size: 13px !important;
        }
        body.dark-theme .courier-select:focus,
        body.dark-theme .platform-select:focus,
        body.dark-theme .source-link-input:focus {
            border-color: #3b82f6 !important;
            background-color: #151b2e !important;
            color: #ffffff !important;
        }
        body.dark-theme .source-link-input::placeholder {
            color: #64748b !important;
            opacity: 1 !important;
        }

        body:not(.dark-theme) .courier-select,
        body:not(.dark-theme) .platform-select,
        body:not(.dark-theme) .source-link-input {
            background-color: #ffffff !important;
            border: 1.5px solid #cbd5e1 !important;
            color: #0f172a !important;
            border-radius: 6px !important;
            height: 32px !important;
            padding: 4px 8px !important;
            outline: none !important;
            font-size: 13px !important;
        }
        body:not(.dark-theme) .courier-select:focus,
        body:not(.dark-theme) .platform-select:focus,
        body:not(.dark-theme) .source-link-input:focus {
            border-color: #2563eb !important;
            background-color: #ffffff !important;
            color: #0f172a !important;
        }
        body:not(.dark-theme) .source-link-input::placeholder {
            color: #94a3b8 !important;
            opacity: 1 !important;
        }

        /* Final overrides to ensure button styling applies on mobile */
        @media (max-width: 768px) {
            body .payment-button-group .btn-pos-action,
            body.dark-theme .payment-button-group .btn-pos-action,
            body:not(.dark-theme) .payment-button-group .btn-pos-action {
                min-width: 0 !important;
                max-width: none !important;
                font-size: 8.5px !important;
                font-weight: 700 !important;
                letter-spacing: 0px !important;
                padding: 0 4px !important;
                height: 38px !important;
                white-space: nowrap !important;
                gap: 3px !important;
            }
            body .payment-button-group .btn-pos-action i,
            body.dark-theme .payment-button-group .btn-pos-action i {
                margin-right: 1px !important;
                font-size: 9px !important;
            }
        }

        /* =============================================
           COMPREHENSIVE MOBILE RESPONSIVE OVERRIDES
           ============================================= */

        @media (max-width: 991.98px) {
            /* Unlock height/overflow so mobile can scroll */
            html, body,
            body.bg-slate-50, body.dark-theme, body.kiosk-mode,
            #containerbar, body #containerbar,
            .rightbar, body .rightbar {
                height: auto !important;
                overflow: auto !important;
                overflow-x: hidden !important;
                margin-left: 0 !important;
                width: 100% !important;
            }
            body.kiosk-mode #containerbar:not(.sidebar-none) .rightbar {
                margin-left: 0 !important;
                width: 100% !important;
            }

            .rightbar > .flex-grow.w-full {
                height: auto !important;
                overflow: visible !important;
            }

            .invoice-contentbar {
                display: block !important;
                height: auto !important;
                overflow: visible !important;
                padding: 8px !important;
                padding-bottom: 20px !important;
                margin: 0 !important;
                flex: none !important;
                min-height: 0 !important;
            }

            #pos-row-container, #pos-row-container-alt {
                display: block !important;
                flex: none !important;
                height: auto !important;
                min-height: 0 !important;
                margin: 0 !important;
            }

            .main-pos-col, .sidebar-pos-col {
                flex: 0 0 100% !important;
                max-width: 100% !important;
                width: 100% !important;
                height: auto !important;
                min-height: 0 !important;
                display: block !important;
                margin-bottom: 12px !important;
            }

            .main-pos-col .card.card_top,
            .sidebar-pos-col .card.card_top {
                height: auto !important;
                overflow: visible !important;
                flex: none !important;
                min-height: 0 !important;
            }

            #payment_form, .cart-container {
                height: auto !important;
                overflow: visible !important;
                flex: none !important;
                min-height: 0 !important;
            }

            .cart-search-header, .cart-head {
                flex: none !important;
                min-height: 0 !important;
                overflow: visible !important;
            }

            .table-responsive {
                height: auto !important;
                max-height: 380px !important;
                min-height: 120px !important;
                overflow-y: auto !important;
                flex: none !important;
            }

            .pos-summary-panel {
                margin-top: 12px !important;
                flex: none !important;
            }

            .sidebar-pos-col #products {
                flex: none !important;
                min-height: 0 !important;
                overflow-y: auto !important;
                max-height: 420px !important;
            }

            .card-body {
                display: block !important;
                flex: none !important;
                min-height: 0 !important;
                overflow: visible !important;
            }

            /* Top Toggle Bar */
            .pos-toggle-container {
                flex-wrap: wrap !important;
                gap: 5px !important;
                margin-bottom: 10px !important;
                justify-content: center !important;
            }

            .pos-toggle-container .btn {
                height: 30px !important;
                padding: 0 8px !important;
                font-size: 11px !important;
            }

            #online_status {
                font-size: 11px !important;
                padding: 4px 8px !important;
            }

            /* Date / Customer Row */
            .ecommerce-sortby {
                flex-wrap: wrap !important;
                gap: 4px !important;
                margin-bottom: 8px !important;
            }

            .ecommerce-sortby .col-auto {
                flex: 0 0 auto !important;
            }

            .ecommerce-sortby div[style*="width: 115px"] {
                width: 100px !important;
            }

            #date {
                height: 34px !important;
                font-size: 12px !important;
                padding: 4px 6px !important;
            }

            /* checkout input grid - 2 columns on mobile */
            .checkout-input-row {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 6px !important;
                margin-bottom: 8px !important;
            }

            .checkout-col label {
                font-size: 9px !important;
            }

            .checkout-col .form-control,
            .checkout-col select {
                height: 32px !important;
                font-size: 12px !important;
                padding: 4px 6px !important;
            }

            .grand-total-box {
                height: 32px !important;
                font-size: 14px !important;
                padding: 0 8px !important;
            }

            /* Payment type selector row */
            .checkout-actions-row {
                flex-direction: column !important;
                gap: 8px !important;
                margin-bottom: 8px !important;
            }

            .payment-type-selectors {
                flex-wrap: wrap !important;
                gap: 8px !important;
            }

            .payment-type-selectors label {
                font-size: 12px !important;
            }

            /* Payment buttons */
            .payment-button-group {
                justify-content: stretch !important;
                max-width: 100% !important;
                width: 100% !important;
                flex-wrap: nowrap !important;
                gap: 6px !important;
            }

            body .payment-button-group .btn-pos-action,
            body.dark-theme .payment-button-group .btn-pos-action,
            body:not(.dark-theme) .payment-button-group .btn-pos-action {
                flex: 1 !important;
                min-width: 0 !important;
                max-width: none !important;
                font-size: 10px !important;
                font-weight: 700 !important;
                padding: 0 6px !important;
                height: 40px !important;
                white-space: nowrap !important;
                gap: 3px !important;
            }

            /* Summary Banner - 2 columns on mobile */
            .pos-summary-banner {
                grid-template-columns: repeat(2, 1fr) !important;
            }

            .banner-label {
                font-size: 10px !important;
            }

            .banner-value {
                font-size: 16px !important;
            }

            .pos-summary-banner-item {
                padding: 8px !important;
            }

            /* Table font reduction */
            .table thead th {
                font-size: 10px !important;
                padding: 6px 3px !important;
                white-space: nowrap;
            }

            .table tbody td {
                font-size: 11px !important;
                padding: 5px 3px !important;
            }

            .table tbody td input.form-control,
            .table tbody td select.form-control {
                font-size: 11px !important;
                height: 28px !important;
                padding: 2px 4px !important;
            }

            /* Product grid override for mobile */
            #products .row {
                grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)) !important;
                gap: 6px !important;
            }

            .productcss .product-head {
                height: 55px !important;
            }

            .productcss .product-head img {
                max-height: 48px !important;
            }

            .productcss .product-name {
                font-size: 10px !important;
                height: 20px !important;
            }

            .productcss .product-code,
            .productcss .stock-status {
                font-size: 9px !important;
            }

            /* Online sale inline selectors */
            .courier-select-wrapper,
            .platform-select-wrapper,
            .source-link-wrapper {
                margin-top: 4px !important;
                margin-left: 0 !important;
                width: 100% !important;
            }

            .courier-select,
            .platform-select,
            .source-link-input {
                min-width: 100px !important;
                width: 100% !important;
                height: 30px !important;
                font-size: 11px !important;
            }

            /* Multiple accounts bank row */
            #inline-checking-section .col-md-4 {
                flex: 0 0 50% !important;
                max-width: 50% !important;
            }

            .bank-amount-input {
                max-width: 90px !important;
                font-size: 11px !important;
            }
        }

        /* Extra-small screens (< 576px) */
        @media (max-width: 575.98px) {
            .invoice-contentbar {
                padding: 5px !important;
            }

            .pos-toggle-container .btn {
                height: 26px !important;
                font-size: 10px !important;
                padding: 0 6px !important;
            }

            .pos-toggle-container {
                gap: 4px !important;
            }

            /* Stack checkout inputs 1 column */
            .checkout-input-row {
                grid-template-columns: 1fr 1fr !important;
                gap: 5px !important;
            }

            .banner-value {
                font-size: 13px !important;
            }

            .banner-label {
                font-size: 9px !important;
                letter-spacing: 0 !important;
            }

            .payment-button-group {
                gap: 4px !important;
            }

            body .payment-button-group .btn-pos-action {
                font-size: 9px !important;
                height: 36px !important;
                padding: 0 4px !important;
            }

            /* product grid very small */
            #products .row {
                grid-template-columns: repeat(auto-fill, minmax(85px, 1fr)) !important;
                gap: 4px !important;
            }

            .productcss {
                padding: 4px !important;
            }

            .productcss .product-head {
                height: 45px !important;
            }

            /* Switch label */
            .pos-toggle-container > span {
                font-size: 10px !important;
            }

            #inline-checking-section .col-md-4 {
                flex: 0 0 100% !important;
                max-width: 100% !important;
            }

            .table thead th {
                font-size: 9px !important;
                padding: 4px 2px !important;
            }

            .table tbody td {
                font-size: 10px !important;
                padding: 4px 2px !important;
            }
        }
    </style>
@endpush

@section('invoice')
    @php
        $show_imei = trim(strtolower(env('APP_IMEI'))) == 'yes';
        $show_warranty = trim(strtolower(env('APP_WARRANTY'))) == 'yes';
        $extra_cols = ($show_imei ? 1 : 0) + ($show_warranty ? 1 : 0);
    @endphp

    @if ($userBranchId == 1)
        @if ($filterBranchId != null)
            <div class="invoice-contentbar">
                <div class="pos-toggle-container">
                    <!-- Navigation Toggles for Kiosk/Full Mode -->
                    <div class="kiosk-nav-toggles mr-auto">
                        <button type="button" class="kiosk-nav-btn pos-kiosk-hamburger-btn" title="Toggle Sidebar">
                            <i class="fa-solid fa-bars-staggered"></i>
                        </button>
                        <button type="button" class="kiosk-nav-btn pos-full-hide-toggle-btn" title="Full Width View">
                            <i class="fa-solid fa-eye-slash"></i>
                        </button>
                    </div>
                    <div id="online_status" class="mr-3 text-white font-weight-bold">
                        <span class="badge badge-success"><i class="fa fa-circle"></i> Online</span>
                    </div>
                    <button type="button" class="btn btn-warning mr-2 hold_invoice_btn" id="hold_invoice_btn">
                        <i class="fa fa-pause-circle"></i> {{ __('Hold') }}
                    </button>
                    <button type="button" class="btn btn-info mr-2 hold_list_btn" id="hold_list_btn">
                        <i class="fa fa-list"></i> {{ __('Hold List') }} (<span class="hold_count" id="hold_count">{{ $hold_count ?? 0 }}</span>)
                    </button>
                    <button type="button" class="btn btn-success mr-2 offline_sync_btn" id="offline_sync_btn" title="Sync Data">
                        <i class="fa fa-refresh"></i>
                    </button>
                    <button type="button" class="btn btn-dark mr-2 offline_sales_btn" id="offline_sales_btn" data-toggle="modal" data-target="#offlineSalesModal">
                        <i class="fa fa-shopping-cart"></i> {{ __('Offline') }} (<span class="offline_sale_count" id="offline_sale_count">0</span>)
                    </button>
                    <span class="font-weight-bold text-white">{{ __('Show Products/Categories') }}</span>
                    <label class="switch">
                        <input type="checkbox" id="sidebar_toggle" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="row" id="pos-row-container">
                    <!-- Start col -->
                    <div class="col-md-7 main-pos-col" id="main-pos-area">
                        <div class="card card_top">
                            <form action="{{ route('invoice.store') }}" id="payment_form" method="POST" onsubmit="return checkExplicitSubmit(event);">
                                @csrf

        @if(isset($quotation))
                                    <input type="hidden" name="quotation_id" value="{{ $quotation->id }}">
                                @endif
                                <div class="cart-container">
                                    <div class="cart-search-header">
                                        @if (auth()->user()->branch_id == 1)
                                            <input type="hidden" name="branch_id" id="branch_id"
                                                value="{{ $filterBranchId }}">
                                        @else
                                            <input type="hidden" name="branch_id" id="branch_id"
                                                value="{{ auth()->user()->branch_id }}">
                                        @endif
                                        <div class="row align-items-center ecommerce-sortby mb-3 mx-n1">
                                            <div class="col-auto px-1">
                                                <button type="button" onclick="toggleKioskMode()" class="kiosk-toggle-btn" title="{{ __('Toggle View Mode') }}">
                                                    <i id="kiosk-btn-icon" class="fa-solid fa-expand"></i>
                                                </button>
                                            </div>
                                            <div class="col-auto px-1" style="width: 115px;">
                                                <input type="date" class="form-control" id="date"
                                                    value="{{ date('Y-m-d') }}" name="date" max="{{ date('Y-m-d') }}" required style="padding: 5px;">
                                            </div>
                                            <div class="col px-1">
                                                <select class="select2" name="customer_id" id="customer_id">
                                                    @foreach ($customers as $customer)
                                                        <option value="{{ $customer->id }}"
                                                            @if(env('APP_DISCOUNT_GROUP') == 'yes')
                                                            data-discount-type="{{ $customer->discountGroup?->type }}"
                                                            data-discount-value="{{ $customer->discountGroup?->value }}"
                                                            @endif>
                                                            {{ env('APP_COMPACT_CUSTOMER') == 'yes' ? $customer->phone : ($customer->name . ' - ' . $customer->phone) }} @if(env('APP_DISCOUNT_GROUP') == 'yes' && $customer->discountGroup) (Discount: {{ $customer->discountGroup->type == 'percentage' ? number_format($customer->discountGroup->value, 0).'%' : 'Tk '.number_format($customer->discountGroup->value, 0) }}) @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @if(env('APP_AUTOMOBILE') == 'yes')
                                            {{-- Vehicle Reg No (hidden until customer has vehicles) --}}
                                            <div class="col-auto px-1" id="vehicle_reg_wrapper" style="display:none; min-width:160px;">
                                                <select class="select2" name="vehicle_reg_no" id="vehicle_reg_no" style="width:100%;">
                                                    <option value="">-- Reg No --</option>
                                                </select>
                                            </div>
                                            @endif
                                            @if(env('APP_COURIER_FRAUD_CHECK') == 'yes')
                                            <div class="col-auto px-1">
                                                <button type="button" onclick="checkCurrentCustomerFraudModal()" class="btn btn-warning shadow-sm text-white" style="margin-bottom: 0; height: 38px; width: 40px; display: flex; align-items: center; justify-content: center; background-color: #f59e0b; border-color: #d97706;" title="{{ __('Check Courier Fraud Profile') }}">
                                                    <i class="fa fa-shield-alt" style="font-size: 16px;"></i>
                                                </button>
                                            </div>
                                            @endif
                                            <div class="col-auto px-1">
                                                <a href="#" data-toggle="modal" data-target="#addModal" onclick="$('#addModal').modal('show'); return false;"
                                                    class="btn extra_btn shadow-sm" style="margin-bottom: 0; height: 38px; width: 40px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="feather icon-plus"></i>
                                                </a>
                                            </div>
                                            <div class="col-auto px-1">
                                                <button type="button" class="category-toggle-btn" style="margin-bottom: 0; height: 38px; width: 40px; display: flex; align-items: center; justify-content: center;" onclick="toggleCategorySidebar()">
                                                    <i class="fa fa-th-large"></i>
                                                </button>
                                            </div>
                                        </div>
                                        {{-- ===== Customer Quick History Panel ===== --}}
                                        <div class="customer-history-panel" style="display:none; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 1px solid #bae6fd; border-radius: 10px; padding: 10px 12px; margin-bottom: 8px; font-size: 12px; position: relative;">
                                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                                                <span class="chp-name" style="font-weight:700; color:#0369a1; font-size:13px;"></span>
                                                <button type="button" onclick="$(this).closest('.customer-history-panel').slideUp(200)" style="background:none;border:none;color:#94a3b8;font-size:16px;padding:0;line-height:1;cursor:pointer;">&times;</button>
                                            </div>
                                            <div style="display:grid; grid-template-columns: repeat({{ trim(strtolower(env('APP_WARRANTY'))) == 'yes' ? 4 : 3 }}, 1fr); gap:6px; margin-bottom:10px;">
                                                <div style="background:#fff; border-radius:8px; padding:6px 4px; text-align:center; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                                                    <div class="chp-purchases" style="font-size:16px; font-weight:800; color:#0ea5e9;">-</div>
                                                    <div style="color:#64748b; font-size:10px;">{{ __('Purchases') }}</div>
                                                </div>
                                                <div style="background:#fff; border-radius:8px; padding:6px 4px; text-align:center; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                                                    <div class="chp-spent" style="font-size:16px; font-weight:800; color:#10b981;">-</div>
                                                    <div style="color:#64748b; font-size:10px;">{{ __('Total Spent') }}</div>
                                                </div>
                                                <div style="background:#fff; border-radius:8px; padding:6px 4px; text-align:center; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                                                    <div class="chp-due" style="font-size:16px; font-weight:800; color:#ef4444;">-</div>
                                                    <div style="color:#64748b; font-size:10px;">{{ __('Total Due') }}</div>
                                                </div>
                                                @if (trim(strtolower(env('APP_WARRANTY'))) == 'yes')
                                                <div style="background:#fff; border-radius:8px; padding:6px 4px; text-align:center; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                                                    <div class="chp-claims" style="font-size:16px; font-weight:800; color:#f59e0b;">-</div>
                                                    <div style="color:#64748b; font-size:10px;">{{ __('Warranty') }}</div>
                                                </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div style="font-weight:600; color:#475569; font-size:11px; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.5px;">{{ __('Recent Purchases') }}</div>
                                                <div class="chp-invoices-list"></div>
                                            </div>
                                            <div style="margin-top:8px; padding-top:6px; border-top:1px dashed #bae6fd; display:flex; justify-content:space-between; color:#64748b; font-size:11px;">
                                                <span>📞 <span class="chp-phone"></span></span>
                                                @if(env('APP_LOYALTY') == 'yes')
                                                    <span>⭐ <span class="chp-points"></span> {{ __('Points') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="input-group mb-3">
                                             <div class="input-group-prepend">
                                                 <span class="input-group-text barcod_style" id="basic-addon1"><i
                                                         class="fa fa-barcode"></i></span>
                                                 @if(env('APP_MOBILE_SCANNER') == 'yes')
                                                     <span class="input-group-text bg-primary text-white cursor-pointer btn-scan-camera" style="cursor: pointer;" title="{{ __('Scan with Camera') }}"><i class="fa fa-camera"></i></span>
                                                 @endif
                                             </div>
                                             <input type="text" class="form-control product_search"
                                                 placeholder="{{ __('Type & Barcode') }}" aria-label="{{ __('Type & Barcode') }}"
                                                 autocomplete="off">
                                         </div>
                                        <div class="input-group mb-3" hidden>
                                            <div class="input-group-prepend ">
                                                <span class="input-group-text barcod_style" id="basic-addon1"><i
                                                        class="fa fa-barcode"></i></span>
                                            </div>
                                            <input type="text" id="product_search_scale" class="form-control"
                                                placeholder="{{ __('Scan Barcode for weight product') }}" aria-label="{{ __('Type & Barcode') }}"
                                                onkeydown="return event.keyCode !== 13" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="cart-head">
                                        <div class="table-responsive">
                                            @php
                                                $show_imei = trim(strtolower(env('APP_IMEI'))) == 'yes';
                                                $show_warranty = trim(strtolower(env('APP_WARRANTY'))) == 'yes';
                                                $extra_cols = ($show_imei ? 1 : 0) + ($show_warranty ? 1 : 0);
                                            @endphp
                                            <table class="table table-striped text-center">
                                               <thead>
                                                    <tr class="header_bg text-white">
                                                        <th class="header_style_left" width="42%">{{ __('Product') }}</th>
                                                        <th width="10%">{{ __('Rate') }}</th>
                                                        @if ($show_imei)
                                                            <th width="">{{ __('IMEI') }}</th>
                                                        @endif
                                                        @if ($show_warranty)
                                                            <th width="5%">{{ __('Warranty') }}</th>
                                                        @endif
                                                        <th width="18%">{{ __('Quantity') }}</th>
                                                        <th width="10%">{{ __('Discount') }}</th>
                                                        <th width="12%">{{ __('Total') }}</th>
                                                        <th class="header_style_right" width="2%">{{ __('Action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tbody">

                                                </tbody>
                                            </table>
                                    </div>

                                    {{-- Unified Checkout Panel --}}
                                    <div class="pos-summary-panel">
                                        <!-- Inputs Row -->
                                        <div class="checkout-input-row">
                                            <!-- DISCOUNT -->
                                            <div class="checkout-col">
                                                <label>{{ __('DISCOUNT') }}</label>
                                                <div class="input-group" style="display: flex; width: 10%;">
                                                    <input type="number" step="any" min="0" class="form-control discount_val_input"
                                                        name="discount_val_input" placeholder="0" autocomplete="off" value=""
                                                        style="border-top-right-radius: 0 !important;width: 40px !important; border-bottom-right-radius: 0 !important; flex: 1 1 auto; min-width: 0;">
                                                    <select class="form-control discount_type_select" name="discount_type"
                                                        style="max-width: 56px !important; min-width: 48px !important; padding: 0 2px !important; border-top-left-radius: 0 !important; border-bottom-left-radius: 0 !important; border-left: 0 !important; font-size: 12px; font-weight: bold; background: #f8f9fa; cursor: pointer; text-align-last: center;">
                                                        <option value="fixed">{{ empty(get_setting('com_currency')) ? 'Tk' : get_setting('com_currency') }}</option>
                                                        <option value="percent">%</option>
                                                    </select>
                                                </div>
                                                <input type="hidden" class="form-control discount_amount"
                                                    name="discount_amount" placeholder="0%">
                                                <input type="hidden" class="form-control discount"
                                                    name="discount" placeholder="0%">
                                            </div>
                                            <!-- VAT -->
                                            @if (is_vat_enabled())
                                            <div class="checkout-col">
                                                <label>{{ __('VAT') }}</label>
                                                <input type="text" class="form-control vat"
                                                    name="vat" placeholder="0%" value="0" autocomplete="off">
                                                <input type="hidden" class="form-control vat_amount"
                                                    name="vat_amount" placeholder="0">
                                            </div>
                                            @else
                                            <input type="hidden" class="form-control vat" name="vat" value="0">
                                            <input type="hidden" class="form-control vat_amount" name="vat_amount" value="0">
                                            @endif
                                            <!-- DELIVERY -->
                                            <div class="checkout-col">
                                                <label>{{ __('DELIVERY') }}</label>
                                                <input type="text" class="form-control delivery_charge"
                                                    name="delivery_charge" placeholder="0" value="0" autocomplete="off">
                                            </div>
                                            <!-- BANK ACCOUNT -->
                                            <div class="checkout-col">
                                                <label>{{ __('BANK ACCOUNT') }}</label>
                                                <div id="inline-pos-section">
                                                    <select class="form-control select2" name="bank_id" required>
                                                        @foreach ($bank_accounts as $bank_account)
                                                            <option value="{{ $bank_account->id }}">{{ $bank_account->bank_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- PAY AMOUNT -->
                                            <div class="checkout-col">
                                                <label>{{ __('PAY AMOUNT') }}</label>
                                                <input type="number" step="any" class="form-control pay_amount" name="pay_amount" min="0" placeholder="0.00">
                                            </div>
                                            @if(env('APP_LOYALTY') == 'yes')
                                            <!-- PAY WITH POINTS -->
                                            <div class="checkout-col" id="pay_point_col" style="display: none;">
                                                <label style="color: #ea580c; font-weight: 700;">
                                                    <i class="fa fa-star text-warning"></i>  {{ __('POINTS (1pt=৳0.75)') }} 
                                                </label>
                                                <div class="input-group">
                                                    <input type="number" step="1" min="0" class="form-control pay_point" name="pay_point" value="0" placeholder="0 pts" style="border-color: #f97316; font-weight: bold; color: #ea580c;">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-sm btn-warning font-weight-bold text-white btn-use-max-points" title="{{ __('Use Max Points') }}">
                                                            <i class="fa fa-bolt"></i> MAX
                                                        </button>
                                                    </div>
                                                </div>
                                                <small class="form-text" id="point_discount_text" style="font-size: 11px; font-weight: 600; color: #16a34a;">
                                                    Discount: ৳0.00
                                                </small>
                                            </div>
                                            @else
                                                <input type="hidden" class="pay_point" name="pay_point" value="0">
                                            @endif
                                            <!-- DUE AMOUNT -->
                                            <div class="checkout-col">
                                                <label>{{ __('DUE AMOUNT') }}</label>
                                                <input type="text" class="form-control inline_due_amount" readonly value="0.00" style="background-color: #f8d7da; color: #721c24; font-weight: bold;">
                                            </div>
                                            <!-- CHANGE AMOUNT -->
                                            <div class="checkout-col">
                                                <label>{{ __('CHANGE') }}</label>
                                                <input type="text" class="form-control inline_change_amount" readonly value="0.00" style="background-color: #d4edda; color: #155724; font-weight: bold;">
                                            </div>
                                            <!-- NOTE (moved to actions row) -->
                                            <!-- GRAND TOTAL -->
                                            <div class="checkout-col grand-total-col">
                                                <label>{{ __('GRAND TOTAL') }}</label>
                                                <div class="grand-total-box">
                                                    <span class="payable_amount">0.00</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Multiple Accounts inputs - displayed only when multiple account is active -->
                                        <div id="inline-checking-section" class="inline-checking-section" style="display:none; margin-bottom: 15px; padding: 12px; border-radius: 8px;">
                                            <label class="checkout-col label inline-checking-title" style="font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 8px;">{{ __('Bank Accounts') }}</label>
                                            <div class="row">
                                                @foreach ($bank_accounts as $bank_account)
                                                    <div class="col-md-4 mb-2">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <span class="bank-name-label" style="font-size: 12px; font-weight: 600;">{{ $bank_account->bank_name }}</span>
                                                            <input type="number" name="amounts[{{ $bank_account->id }}]"
                                                                class="form-control bank-amount-input" placeholder="0" min="0" value="0" style="max-width: 120px; height: 32px; border-radius: 6px; padding: 4px 8px; text-align: center;">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- Installment payment fields - displayed only when installment check is active -->
                                        <div id="installment-fields-container" class="installment-fields-container" style="display:none; margin-bottom: 20px; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; background-color: #ffffff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                                            <input type="hidden" name="inst_interest_amount" class="inst_interest_amount_val" id="inst_interest_amount_val">
                                            <input type="hidden" name="inst_remaining" class="inst_remaining_val" id="inst_remaining_val">
                                            <input type="hidden" name="inst_total_with_interest" class="inst_total_with_interest_val" id="inst_total_with_interest_val">
                                            <input type="hidden" name="inst_per_installment" class="inst_per_installment_val" id="inst_per_installment_val">
                                            <style>
                                                .inst-label {
                                                    font-size: 13px;
                                                    font-weight: 700;
                                                    color: #1e293b;
                                                    margin-bottom: 6px;
                                                    display: block;
                                                }
                                                .inst-input {
                                                    border: 1px solid #cbd5e1;
                                                    border-radius: 20px;
                                                    height: 42px;
                                                    padding: 8px 16px;
                                                    font-size: 14px;
                                                    width: 100%;
                                                    outline: none;
                                                    transition: border-color 0.2s;
                                                }
                                                .inst-input:focus {
                                                    border-color: #f97316;
                                                }
                                                .inst-display-box {
                                                    background-color: #e2e8f0;
                                                    border: none;
                                                    border-radius: 20px;
                                                    height: 42px;
                                                    padding: 8px 16px;
                                                    font-size: 14px;
                                                    font-weight: 600;
                                                    color: #334155;
                                                    display: flex;
                                                    align-items: center;
                                                    width: 100%;
                                                }
                                                .inst-divider {
                                                    border-top: 1px solid #f1f5f9;
                                                    margin: 20px 0;
                                                    grid-column: span 2;
                                                }
                                            </style>
                                            <h6 style="color: #f97316; font-weight: 800; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 15px;">
                                                 <i class="fa-solid fa-calculator mr-2"></i>{{ __('Installment Calculator') }}
                                             </h6>
                                             <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px 25px;">
                                                 <!-- Row 1 -->
                                                 <div>
                                                     <label class="inst-label">{{ __('Advance Pay') }}</label>
                                                     <input type="number" step="any" id="inst_advance_pay" name="inst_advance_pay" class="inst-input inst_advance_pay" value="0.00" min="0">
                                                 </div>
                                                 <div>
                                                     <label class="inst-label">{{ __('Remaining') }}</label>
                                                     <div id="inst_remaining" class="inst-display-box inst_remaining">0.00</div>
                                                 </div>

                                                 <!-- Row 2: Duration & Interval -->
                                                 <div>
                                                     <label class="inst-label">{{ __('Total Duration (Days)') }}</label>
                                                     <input type="number" id="inst_total_duration" name="inst_total_duration" class="inst-input inst_total_duration" placeholder="e.g. 120" min="1">
                                                 </div>
                                                 <div>
                                                     <label class="inst-label">{{ __('Interval (Days)') }}</label>
                                                     <input type="number" id="inst_interval_days" name="inst_interval_days" class="inst-input inst_interval_days" placeholder="e.g. 30" value="30" min="1">
                                                     <div class="d-flex gap-1 mt-1 flex-wrap inst-preset-container" style="gap: 4px;">
                                                         <button type="button" class="btn btn-xs btn-outline-secondary inst-interval-preset" data-days="30" style="font-size: 10px; padding: 1px 6px; border-radius: 10px;">{{ __('Monthly (30d)') }}</button>
                                                         <button type="button" class="btn btn-xs btn-outline-secondary inst-interval-preset" data-days="15" style="font-size: 10px; padding: 1px 6px; border-radius: 10px;">{{ __('15 Days') }}</button>
                                                         <button type="button" class="btn btn-xs btn-outline-secondary inst-interval-preset" data-days="7" style="font-size: 10px; padding: 1px 6px; border-radius: 10px;">{{ __('Weekly (7d)') }}</button>
                                                         <button type="button" class="btn btn-xs btn-outline-secondary inst-interval-preset" data-days="1" style="font-size: 10px; padding: 1px 6px; border-radius: 10px;">{{ __('Daily (1d)') }}</button>
                                                     </div>
                                                 </div>

                                                 <!-- Row 3: Total Installments & Interest (%) -->
                                                 <div>
                                                     <label class="inst-label">{{ __('Total Installments') }}</label>
                                                     <input type="number" id="inst_total_installments" name="inst_total_installments" class="inst-input inst_total_installments" placeholder="e.g. 4" min="1" value="1">
                                                 </div>
                                                 <div>
                                                     <label class="inst-label">{{ __('Interest (%)') }}</label>
                                                     <input type="number" step="any" id="inst_interest_percent" name="inst_interest_percent" class="inst-input inst_interest_percent" value="0.00" min="0">
                                                 </div>

                                                 <!-- Row 4: Interest Amount & Total with Interest -->
                                                 <div>
                                                     <label class="inst-label">{{ __('Interest Amount') }}</label>
                                                     <div id="inst_interest_amount" class="inst-display-box inst_interest_amount">0.00</div>
                                                 </div>
                                                 <div>
                                                     <label class="inst-label">{{ __('Total with Interest') }}</label>
                                                     <div id="inst_total_with_interest" class="inst-display-box inst_total_with_interest">0.00</div>
                                                 </div>

                                                 <!-- Divider -->
                                                 <div class="inst-divider"></div>

                                                 <!-- Row 5: Per Installment & First Due Date -->
                                                 <div>
                                                     <label class="inst-label">{{ __('Per Installment') }}</label>
                                                     <input type="number" step="any" id="inst_per_installment" name="inst_per_installment" class="inst-input inst_per_installment" placeholder="0.00" min="0">
                                                 </div>
                                                 <div>
                                                     <label class="inst-label">{{ __('First Due Date') }}</label>
                                                     <input type="date" id="inst_first_due_date" name="inst_first_due_date" class="inst-input inst_first_due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}">
                                                 </div>

                                                 <!-- Row 6: Last Due Date -->
                                                 <div style="grid-column: span 2;">
                                                     <label class="inst-label">{{ __('Last Due Date') }}</label>
                                                     <input type="date" id="inst_last_due_date" name="inst_last_due_date" class="inst-input inst_last_due_date">
                                                 </div>
                                             </div>
                                        </div>

<!-- Actions Row: Selector Left, Buttons Right -->
                                        <div class="checkout-actions-row">
                                            <!-- Single/Multiple selector on left -->
                                            <div class="payment-type-selectors d-flex align-items-center">
                                                <label class="mr-2 mb-0" style="display: none !important;">
                                                    <input type="radio" id="inlineOneAccountCompact" name="payment_type" value="pos" checked>
                                                    <span>{{ __('Single Account') }}</span>
                                                </label>
                                                <label class="mb-0">
                                                    <input type="radio" id="inlineMultiAccountCompact" name="payment_type" value="checking">
                                                    <span>{{ __('Multiple Account') }}</span>
                                                </label>
                                                @if(env('APP_INSTALLMENT') == 'yes')
                                                <label class="mb-0 ml-4 cursor-pointer d-flex align-items-center" style="gap: 5px;">
                                                    <input type="checkbox" id="is_installment" class="is_installment_toggle" name="is_installment" value="1" style="width: 16px; height: 16px;">
                                                    <span class="font-weight-bold" style="font-size: 13px; color: #f97316;">{{ __('Installment Sale') }}</span>
                                                </label>
                                                @endif
                                                <input type="hidden" name="sale_type" class="sale-type-hidden-val" value="Outlet">
                                                @if (env('APP_ONLINE') == 'yes')
                                                <div class="ml-4 d-flex align-items-center gap-2 flex-wrap">
                                                    <label class="mb-0 cursor-pointer d-flex align-items-center" style="gap: 5px;">
                                                        <input type="checkbox" class="online-sale-toggle-chk" style="width: 16px; height: 16px;">
                                                        <span class="font-weight-bold" style="font-size: 13px; color: #94a3b8;">{{ __('Online Sale') }}</span>
                                                    </label>
                                                     <div class="pre-order-chk-wrapper ml-2">
                                                         <label class="mb-0 cursor-pointer d-flex align-items-center bg-warning text-dark px-2 py-1" style="gap: 5px; font-size: 12.5px; font-weight: bold; border-radius: 6px !important; box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);">
                                                             <input type="checkbox" class="is-pre-order-chk" style="width: 15px; height: 15px; cursor: pointer;">
                                                             <span><i class="fa-solid fa-clock mr-1"></i>{{ __('Pre-Order') }}</span>
                                                         </label>
                                                     </div>
                                                    <div class="courier-select-wrapper ml-2" style="display: none;">
                                                        <select class="form-control form-control-sm courier-select" name="courier_type" style="min-width: 120px;">
                                                            <option value="">{{ __('Select Courier') }}</option>
                                                            <option value="Stead Fast">Stead Fast</option>
                                                            <option value="Pathao">Pathao</option>
                                                        </select>
                                                    </div>
                                                    <div class="platform-select-wrapper ml-2" style="display: none;">
                                                        <select class="form-control form-control-sm platform-select" name="platform_id" style="min-width: 120px;">
                                                            <option value="">{{ __('Select Platform') }}</option>
                                                            @foreach($platforms as $platform)
                                                                <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="source-link-wrapper ml-2" style="display: none;">
                                                        <input type="text" class="form-control form-control-sm source-link-input" name="source_link" placeholder="{{ __('Source Link') }}" style="min-width: 150px;">
                                                    </div>
                                                     <div class="cod-amount-wrapper ml-2" style="display: none;">
                                                         <input type="number" step="any" class="form-control form-control-sm cod-amount-input" name="cod_amount" placeholder="{{ __('COD Amount') }}" style="min-width: 100px;">
                                                     </div>
                                                </div>
                                                @endif
                                                @if(env('APP_INVOICE_NOTE') == 'yes')
                                                <div class="note-btn-wrapper ml-2 d-flex align-items-center">
                                                    <button type="button" class="btn btn-sm btn-pos-note-trigger d-flex align-items-center" title="{{ __('Invoice Note') }}" style="height: 32px; border-radius: 6px; padding: 0 10px; font-size: 12px; font-weight: 600; border: 1px solid #cbd5e1; background: #ffffff; color: #334155; box-shadow: 0 1px 2px rgba(0,0,0,0.05); cursor: pointer; transition: all 0.2s;">
                                                        <i class="fa-solid fa-note-sticky text-primary mr-1" style="font-size: 13px;"></i>
                                                        <span>{{ __('Note') }}</span>
                                                        <span class="badge badge-primary pos-note-badge ml-1" style="display: {{ (!empty($preOrder->note) || !empty($quotation->note)) ? 'inline-block' : 'none' }}; font-size: 10px; border-radius: 10px; padding: 2px 6px; background-color: #2563eb; color: #ffffff;">✓</span>
                                                    </button>
                                                    <input type="hidden" name="note" class="note-input pos-invoice-note-field" value="{{ $preOrder->note ?? $quotation->note ?? '' }}">
                                                </div>
                                                @else
                                                <input type="hidden" name="note" class="note-input pos-invoice-note-field" value="{{ $preOrder->note ?? $quotation->note ?? '' }}">
                                                @endif
                                                @if(env('APP_REF_INV') == 'yes')
                                                <div class="ml-3 d-flex align-items-center">
                                                    <input type="text" class="form-control form-control-sm" name="ref_no" id="ref_no" placeholder="{{ __('Ref Inv') }}" value="{{ $preOrder->ref_no ?? $quotation->ref_no ?? '' }}" style="min-width: 130px; max-width: 160px; height: 32px; border-radius: 4px; font-size: 12px;">
                                                </div>
                                                @endif
                                            </div>

                                            <!-- Checkout Action Buttons on right -->
                                            <div class="payment-button-group">
                                                @if(isset($preOrder) && !request('convert_mode'))
                                                <button type="button" class="btn-pos-action btn-warning text-white font-weight-bold btn-pre-order-submit" style="background: #f59e0b; border: none;" title="{{ __('Update Pre-Order') }}">
                                                    <i class="fa-solid fa-save mr-1"></i> {{ __('Update Pre-Order') }}
                                                </button>
                                                @else
                                                @if(env('APP_ONLINE') == 'yes' && !request('convert_mode'))
                                                <button type="button" class="btn-pos-action btn-warning text-white font-weight-bold btn-pre-order-submit" style="background: #f59e0b; border: none;" title="{{ __('Place Pre-Order without stock deduction') }}">
                                                    <i class="fa-solid fa-clock mr-1"></i> {{ __('Pre-Order') }}
                                                </button>
                                                @endif
                                                <button type="button" class="btn-pos-action btn-full-paid full_pay_btn d-none" title="Shortcut: F8" style="display: none !important;">
                                                    <i class="fa-solid fa-circle-check mr-1"></i> {{ __('Full Paid') }} (F8)
                                                </button>
                                                <button type="button" class="btn-pos-action btn-full-due full_due_btn" title="Shortcut: F9">
                                                    <i class="fa-solid fa-circle-minus mr-1"></i> {{ __('Full Due') }} (F9)
                                                </button>
                                                <button type="button" class="btn-pos-action btn-checkout" id="checkout" title="Shortcut: F12">
                                                    <i class="fa-solid fa-check-double mr-1"></i> {{ __('Checkout') }} (F12)
                                                </button>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Hidden/Dummy inputs for calculation compatibility -->
                                        <input type="hidden" name="payable_amount" id="payable_amount" value="">
                                        <input type="hidden" name="paid_amount" id="paid_amount" value="">
                                        <input type="hidden" name="due_amount" id="due_amount" value="">
                                        <input type="hidden" name="balance" id="balance" value="">
                                        <input type="hidden" name="previous_due" id="previous_due">
                                        <input type="number" step="any" name="estimated_amount" value="0" class="estimated_amount" style="display:none;">

                                        <!-- Bottom summary banner (4 blue cells) -->
                                        <div class="pos-summary-banner">
                                            <div class="pos-summary-banner-item">
                                                <span class="banner-label">{{ __('TOTAL QTY') }}</span>
                                                <span class="banner-value"># <span class="total_item">0</span></span>
                                            </div>
                                            <div class="pos-summary-banner-item">
                                                <span class="banner-label">{{ __('TOTAL GROSS') }}</span>
                                                <span class="banner-value"><span class="sub_total">0.00</span></span>
                                            </div>
                                            <div class="pos-summary-banner-item">
                                                <span class="banner-label">{{ __('TOTAL DISC') }}</span>
                                                <span class="banner-value"><span class="discount_amount_display">0.00</span></span>
                                            </div>
                                            <div class="pos-summary-banner-item">
                                                <span class="banner-label">{{ __('NET TOTAL') }}</span>
                                                <span class="banner-value"><span class="payable_amount">0.00</span></span>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
    
                                <div class="modal fade" id="imeiModal" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-md" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title">{{ __('Select IMEI(s)') }}</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <input type="text" id="imeiSearch" class="form-control mb-3" placeholder="{{ __('Search IMEI...') }}">
                                                </div>
                                                <div id="imeiList" style="max-height: 400px; overflow-y: auto;"></div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="mr-auto">
                                                    {{ __('Selected') }}: <span id="selectedImeiCount" class="font-weight-bold">0</span>
                                                </div>
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                                                <button type="button" id="confirmImei" class="btn btn-primary">{{ __('Confirm Selection') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                    <!-- Modal was here, moved to end of file -->
                    <div class="col-md-5 sidebar-pos-col" id="category-sidebar">
                        <div class="card card_top">
                            <div class="card-body">
                                <!-- Start row -->
                                <div class="row align-items-center ecommerce-sortby">
                                    <!-- Start col -->
                                    <div class="col-md-12 col-lg-12 col-xl-12">

                                        <select class="select2" name="category_id" id="getProductsByCat">
                                            <option selected value="">{{ __('Select Category') }}</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <!-- End col -->
                                </div>
                                <div id="products">
                                    <div class="row">
                                        @forelse($products as $product)
                                            @if(env('APP_IMEI') == 'no' && $product->imei == 1)
                                                @continue
                                            @endif

                                            @if ($product->is_service == 0)
                                                @php
                                                    $stock_qty = product_stock_check($product);
                                                    $pure_stock = (float) product_fake_stock_val($product);
                                                    $is_out_of_stock = ($product->is_service == 0 && $pure_stock <= 0);
                                                    $rtn = App\Models\ReturnItem::where('product_id', $product->id)->sum(
                                                        'main_qty',
                                                    );
                                                @endphp
                                                <!-- Start col -->
                                                <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">
                                                    <div class="product-bar productcss product {{ $is_out_of_stock ? 'out-of-stock' : '' }}"
                                                        data-value="{{ $product->id }}">
                                                        @if ($is_out_of_stock)
                                                            <div class="stock-badge" style="position: absolute; top: 10px; right: 10px; z-index: 1;">
                                                                <span class="badge badge-danger">{{ __('Out of Stock') }}</span>
                                                            </div>
                                                        @endif
                                                        <div class="product-head">
                                                            <img src="{{ !empty($product->images) ? url('uploads/products/' . $product->images) : url('backend/images/no_images.png') }}"
                                                                class="img-fluid" alt="product">
                                                        </div>
                                                        <div class="product-body">
                                                            <div class="product-name">{{ $product->name }}</div>
                                                            <div class="product-code">({{ $product->barcode }})</div>
                                                            <div class="stock-status">
                                                                @if($product->is_service == 1)
                                                                    <span class="badge badge-info" style="font-size: 10px; padding: 2px 6px;">{{ __('Service Item') }}</span>
                                                                @else
                                                                    {{ $stock_qty }} {{ __('in stock') }}
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End col -->
                                            @endif
                                        @empty
                                            <div class="col-md-12" style="padding-bottom: 30px;">
                                                <div class="alert alert-danger text-center" role="alert"> {{ __('Products not available!') }}</div>
                                            </div>
                                        @endforelse
                                    </div>
                                    @if (env('APP_SERVICE') == 'yes')
                                        <p class="text-dark">{{ __('Services') }}</p>
                                        <div class="row">
                                            @forelse($services ?? [] as $product)
                                                <!-- Start col -->
                                                <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">
                                                    <div class="product-bar productcss product"
                                                        data-value="{{ $product->id }}">
                                                        <div class="product-head">
                                                            <img src="{{ !empty($product->images) ? url('uploads/products/' . $product->images) : url('backend/images/no_images.png') }}"
                                                                class="img-fluid" alt="product">
                                                        </div>
                                                        <div class="product-body">
                                                            <div class="product-name">{{ $product->name }}</div>
                                                            <div class="product-code">({{ $product->barcode }})</div>
                                                            <div class="stock-status">
                                                                <span class="badge badge-info" style="font-size: 10px; padding: 2px 6px;">{{ __('Service Item') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End col -->
                                            @empty
                                                <div class="col-md-12" style="padding-bottom: 30px;">
                                                    <div class="alert alert-info text-center" role="alert"> {{ __('No services available!') }}</div>
                                                </div>
                                            @endforelse
                                        </div>
                                    @endif
                                    <div class="pagination justify-content-center">
                                        {{ $products->onEachSide(0)->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End col -->
                </div>
            </div>
        @else
            <div class="invoice-contentbar">
                <div class="row">
                    <div class="col-md-12 text-center mt-5">
                        <h2 class="text-danger">{{ __('Please Select Branch') }}</h2>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="invoice-contentbar">
                <div class="pos-toggle-container">
                    <!-- Navigation Toggles for Kiosk/Full Mode -->
                    <div class="kiosk-nav-toggles mr-auto">
                        <button type="button" class="kiosk-nav-btn pos-kiosk-hamburger-btn" title="Toggle Sidebar">
                            <i class="fa-solid fa-bars-staggered"></i>
                        </button>
                        <button type="button" class="kiosk-nav-btn pos-full-hide-toggle-btn" title="Full Width View">
                            <i class="fa-solid fa-eye-slash"></i>
                        </button>
                    </div>
                    <div id="online_status" class="mr-3 text-white font-weight-bold">
                        <span class="badge badge-success"><i class="fa fa-circle"></i> Online</span>
                    </div>
                    <button type="button" class="btn btn-warning mr-2 hold_invoice_btn" id="hold_invoice_btn_alt">
                        <i class="fa fa-pause-circle"></i> {{ __('Hold') }}
                    </button>
                    <button type="button" class="btn btn-info mr-2 hold_list_btn" id="hold_list_btn_alt">
                        <i class="fa fa-list"></i> {{ __('Hold List') }} (<span class="hold_count" id="hold_count_alt">{{ $hold_count ?? 0 }}</span>)
                    </button>
                    <button type="button" class="btn btn-success mr-2 offline_sync_btn" id="offline_sync_btn_alt" title="Sync Data">
                        <i class="fa fa-refresh"></i>
                    </button>
                    <button type="button" class="btn btn-dark mr-2 offline_sales_btn" id="offline_sales_btn_alt" data-toggle="modal" data-target="#offlineSalesModal">
                        <i class="fa fa-shopping-cart"></i> {{ __('Offline') }} (<span class="offline_sale_count" id="offline_sale_count_alt">0</span>)
                    </button>
                    <span class="font-weight-bold text-white">{{ __('Show Products/Categories') }}</span>
                    <label class="switch">
                        <input type="checkbox" id="sidebar_toggle" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="row" id="pos-row-container-alt">
                    <!-- Start col -->
                    <div class="col-md-7 main-pos-col" id="main-pos-area-alt">
                    <div class="card card_top">
                        <form action="{{ route('invoice.store') }}" id="payment_form" method="POST" onsubmit="return checkExplicitSubmit(event);">
                            @csrf
                            <div class="cart-container">
                                <div class="cart-search-header">
                                    @if (auth()->user()->branch_id == 1)
                                        <input type="hidden" name="branch_id" id="branch_id"
                                            value="{{ $filterBranchId }}">
                                    @else
                                        <input type="hidden" name="branch_id" id="branch_id"
                                            value="{{ auth()->user()->branch_id }}">
                                    @endif
                                    <div class="row align-items-center ecommerce-sortby mb-3 mx-n1">
                                        <div class="col-auto px-1">
                                            <button type="button" onclick="toggleKioskMode()" class="kiosk-toggle-btn" title="{{ __('Toggle View Mode') }}">
                                                <i id="kiosk-btn-icon" class="fa-solid fa-expand"></i>
                                            </button>
                                        </div>
                                        <div class="col-auto px-1" style="width: 115px;">
                                            <input type="date" class="form-control" id="date"
                                                value="{{ date('Y-m-d') }}" name="date" max="{{ date('Y-m-d') }}" required style="padding: 5px;">
                                        </div>
                                        <div class="col px-1">
                                            <select class="select2" name="customer_id" id="customer_id">
                                                @foreach ($customers as $customer)
                                                    <option value="{{ $customer->id }}"
                                                        @if(env('APP_DISCOUNT_GROUP') == 'yes')
                                                        data-discount-type="{{ $customer->discountGroup?->type }}"
                                                        data-discount-value="{{ $customer->discountGroup?->value }}"
                                                        @endif>
                                                        {{ env('APP_COMPACT_CUSTOMER') == 'yes' ? $customer->phone : ($customer->name . ' - ' . $customer->phone) }} @if(env('APP_DISCOUNT_GROUP') == 'yes' && $customer->discountGroup) (Discount: {{ $customer->discountGroup->type == 'percentage' ? number_format($customer->discountGroup->value, 0).'%' : 'Tk '.number_format($customer->discountGroup->value, 0) }}) @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if(env('APP_AUTOMOBILE') == 'yes')
                                        {{-- Vehicle Reg No (hidden until customer has vehicles) --}}
                                        <div class="col-auto px-1" id="vehicle_reg_wrapper" style="display:none; min-width:160px;">
                                            <select class="select2" name="vehicle_reg_no" id="vehicle_reg_no" style="width:100%;">
                                                <option value="">-- Reg No --</option>
                                            </select>
                                        </div>
                                        @endif
                                        <div class="col-auto px-1">
                                            <a href="#" data-toggle="modal" data-target="#addModal" onclick="$('#addModal').modal('show'); return false;"
                                                class="btn extra_btn shadow-sm" style="margin-bottom: 0; height: 38px; width: 40px; display: flex; align-items: center; justify-content: center;">
                                                <i class="feather icon-plus"></i>
                                            </a>
                                        </div>
                                        <div class="col-auto px-1">
                                            <button type="button" class="category-toggle-btn" style="margin-bottom: 0; height: 38px; width: 40px; display: flex; align-items: center; justify-content: center;" onclick="toggleCategorySidebarAlt()">
                                                <i class="fa fa-th-large"></i>
                                            </button>
                                        </div>
                                    </div>
                                    {{-- ===== Customer Quick History Panel (Alt Layout) ===== --}}
                                    <div class="customer-history-panel" style="display:none; background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%); border: 1px solid #bae6fd; border-radius: 10px; padding: 10px 12px; margin-bottom: 8px; font-size: 12px; position: relative;">
                                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                                            <span class="chp-name" style="font-weight:700; color:#0369a1; font-size:13px;"></span>
                                            <button type="button" onclick="$(this).closest('.customer-history-panel').slideUp(200)" style="background:none;border:none;color:#94a3b8;font-size:16px;padding:0;line-height:1;cursor:pointer;">&times;</button>
                                        </div>
                                        <div style="display:grid; grid-template-columns: repeat({{ trim(strtolower(env('APP_WARRANTY'))) == 'yes' ? 4 : 3 }}, 1fr); gap:6px; margin-bottom:10px;">
                                            <div style="background:#fff; border-radius:8px; padding:6px 4px; text-align:center; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                                                <div class="chp-purchases" style="font-size:16px; font-weight:800; color:#0ea5e9;">-</div>
                                                <div style="color:#64748b; font-size:10px;">{{ __('Purchases') }}</div>
                                            </div>
                                            <div style="background:#fff; border-radius:8px; padding:6px 4px; text-align:center; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                                                <div class="chp-spent" style="font-size:16px; font-weight:800; color:#10b981;">-</div>
                                                <div style="color:#64748b; font-size:10px;">{{ __('Total Spent') }}</div>
                                            </div>
                                            <div style="background:#fff; border-radius:8px; padding:6px 4px; text-align:center; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                                                <div class="chp-due" style="font-size:16px; font-weight:800; color:#ef4444;">-</div>
                                                <div style="color:#64748b; font-size:10px;">{{ __('Total Due') }}</div>
                                            </div>
                                            @if (trim(strtolower(env('APP_WARRANTY'))) == 'yes')
                                            <div style="background:#fff; border-radius:8px; padding:6px 4px; text-align:center; box-shadow:0 1px 3px rgba(0,0,0,0.06);">
                                                <div class="chp-claims" style="font-size:16px; font-weight:800; color:#f59e0b;">-</div>
                                                <div style="color:#64748b; font-size:10px;">{{ __('Warranty') }}</div>
                                            </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div style="font-weight:600; color:#475569; font-size:11px; margin-bottom:4px; text-transform:uppercase; letter-spacing:0.5px;">{{ __('Recent Purchases') }}</div>
                                            <div class="chp-invoices-list"></div>
                                        </div>
                                        <div style="margin-top:8px; padding-top:6px; border-top:1px dashed #bae6fd; display:flex; justify-content:space-between; color:#64748b; font-size:11px;">
                                            <span>📞 <span class="chp-phone"></span></span>
                                            @if(env('APP_LOYALTY') == 'yes')
                                                <span>⭐ <span class="chp-points"></span> {{ __('Points') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text barcod_style" id="basic-addon1"><i
                                                    class="fa fa-barcode"></i></span>
                                            @if(env('APP_MOBILE_SCANNER') == 'yes')
                                                <span class="input-group-text bg-primary text-white cursor-pointer btn-scan-camera" style="cursor: pointer;" title="{{ __('Scan with Camera') }}"><i class="fa fa-camera"></i></span>
                                            @endif
                                        </div>
                                        <input type="text" class="form-control product_search"
                                            placeholder="{{ __('Type & Barcode') }}" aria-label="{{ __('Type & Barcode') }}"
                                            autocomplete="off">
                                    </div>
                                    <div class="input-group mb-3" hidden>
                                        <div class="input-group-prepend ">
                                            <span class="input-group-text barcod_style" id="basic-addon1"><i
                                                    class="fa fa-barcode"></i></span>
                                        </div>
                                        <input type="text" id="product_search_scale" class="form-control"
                                            placeholder="{{ __('Scan Barcode for weight product') }}" aria-label="{{ __('Type & Barcode') }}"
                                            onkeydown="return event.keyCode !== 13" autocomplete="off">
                                    </div>
                                </div>
                                <div class="cart-head">
                                    <div class="table-responsive">
                                        <table class="table table-striped text-center">
                                            <thead>
                                                @php
                                                    $show_imei = trim(strtolower(env('APP_IMEI'))) == 'yes';
                                                    $show_warranty = trim(strtolower(env('APP_WARRANTY'))) == 'yes';
                                                    $extra_cols = ($show_imei ? 1 : 0) + ($show_warranty ? 1 : 0);
                                                @endphp<table class="table table-bordered text-center"><thead><tr class="header_bg text-white">
                                                    <th class="header_style_left" width="32%">{{ __('Product') }}</th>
                                                    <th width="10%">{{ __('Rate') }}</th>
                                                    @if ($show_imei)
                                                        <th width="">{{ __('IMEI') }}</th>
                                                    @endif
                                                    @if ($show_warranty)
                                                        <th width="">{{ __('Warranty') }}</th>
                                                    @endif
                                                    <th width="18%">{{ __('Quantity') }}</th>
                                                    <th width="10%">{{ __('Discount') }}</th>
                                                    <th width="12%">{{ __('Total') }}</th>
                                                    <th class="header_style_right" width="2%">{{ __('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbody">

                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Unified Checkout Panel --}}
                                    <div class="pos-summary-panel">
                                        <!-- Inputs Row -->
                                        <div class="checkout-input-row">
                                            <!-- DISCOUNT -->
                                            <div class="checkout-col">
                                                <label>{{ __('DISCOUNT') }}</label>
                                                <div class="input-group" style="display: flex; width: 10%;">
                                                    <input type="number" step="any" min="0" class="form-control discount_val_input"
                                                        name="discount_val_input" placeholder="0" autocomplete="off" value=""
                                                        style="border-top-right-radius: 0 !important; border-bottom-right-radius: 0 !important; flex: 1 1 auto; width: 40px !important;">
                                                    <select class="form-control discount_type_select" name="discount_type"
                                                        style="max-width: 40px !important; min-width: 40px !important; padding: 0 2px !important; border-top-left-radius: 0 !important; border-bottom-left-radius: 0 !important; border-left: 0 !important; font-size: 12px; font-weight: bold; background: #f8f9fa; cursor: pointer; text-align-last: center;">
                                                        <option value="fixed">{{ empty(get_setting('com_currency')) ? 'Tk' : get_setting('com_currency') }}</option>
                                                        <option value="percent">%</option>
                                                    </select>
                                                </div>
                                                <input type="hidden" class="form-control discount_amount"
                                                    name="discount_amount" placeholder="0%">
                                                <input type="hidden" class="form-control discount"
                                                    name="discount" placeholder="0%">
                                            </div>
                                            <!-- VAT -->
                                            @if (is_vat_enabled())
                                            <div class="checkout-col">
                                                <label>{{ __('VAT') }}</label>
                                                <input type="text" class="form-control vat"
                                                    name="vat" placeholder="0%" value="0" autocomplete="off">
                                                <input type="hidden" class="form-control vat_amount"
                                                    name="vat_amount" placeholder="0">
                                            </div>
                                            @else
                                            <input type="hidden" class="form-control vat" name="vat" value="0">
                                            <input type="hidden" class="form-control vat_amount" name="vat_amount" value="0">
                                            @endif
                                            <!-- DELIVERY -->
                                            <div class="checkout-col">
                                                <label>{{ __('DELIVERY') }}</label>
                                                <input type="text" class="form-control delivery_charge"
                                                    name="delivery_charge" placeholder="0" value="0" autocomplete="off">
                                            </div>
                                            <!-- BANK ACCOUNT -->
                                            <div class="checkout-col">
                                                <label>{{ __('BANK ACCOUNT') }}</label>
                                                <div id="inline-pos-section">
                                                    <select class="form-control select2" name="bank_id" required>
                                                        @foreach ($bank_accounts as $bank_account)
                                                            <option value="{{ $bank_account->id }}">{{ $bank_account->bank_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <!-- PAY AMOUNT -->
                                            <div class="checkout-col">
                                                <label>{{ __('PAY AMOUNT') }}</label>
                                                <input type="number" step="any" class="form-control pay_amount" name="pay_amount" min="0" placeholder="0.00">
                                            </div>
                                            @if(env('APP_LOYALTY') == 'yes')
                                            <!-- PAY WITH POINTS -->
                                            <div class="checkout-col" id="pay_point_col" style="display: none;">
                                                <label style="color: #ea580c; font-weight: 700;">
                                                    <i class="fa fa-star text-warning"></i>  {{ __('POINTS (1pt=৳0.75)') }} 
                                                </label>
                                                <div class="input-group">
                                                    <input type="number" step="1" min="0" class="form-control pay_point" name="pay_point" value="0" placeholder="0 pts" style="border-color: #f97316; font-weight: bold; color: #ea580c;">
                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-sm btn-warning font-weight-bold text-white btn-use-max-points" title="{{ __('Use Max Points') }}">
                                                            <i class="fa fa-bolt"></i> MAX
                                                        </button>
                                                    </div>
                                                </div>
                                                <small class="form-text" id="point_discount_text" style="font-size: 11px; font-weight: 600; color: #16a34a;">
                                                    Discount: ৳0.00
                                                </small>
                                            </div>
                                            @else
                                                <input type="hidden" class="pay_point" name="pay_point" value="0">
                                            @endif
                                            <!-- DUE AMOUNT -->
                                            <div class="checkout-col">
                                                <label>{{ __('DUE AMOUNT') }}</label>
                                                <input type="text" class="form-control inline_due_amount" readonly value="0.00" style="background-color: #f8d7da; color: #721c24; font-weight: bold;">
                                            </div>
                                            <!-- CHANGE AMOUNT -->
                                            <div class="checkout-col">
                                                <label>{{ __('CHANGE') }}</label>
                                                <input type="text" class="form-control inline_change_amount" readonly value="0.00" style="background-color: #d4edda; color: #155724; font-weight: bold;">
                                            </div>
                                            <!-- NOTE (moved to actions row) -->
                                            <!-- GRAND TOTAL -->
                                            <div class="checkout-col grand-total-col">
                                                <label>{{ __('GRAND TOTAL') }}</label>
                                                <div class="grand-total-box">
                                                    <span class="payable_amount">0.00</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Multiple Accounts inputs - displayed only when multiple account is active -->
                                        <div id="inline-checking-section" class="inline-checking-section" style="display:none; margin-bottom: 15px; padding: 12px; border-radius: 8px;">
                                            <label class="checkout-col label inline-checking-title" style="font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 8px;">{{ __('Bank Accounts') }}</label>
                                            <div class="row">
                                                @foreach ($bank_accounts as $bank_account)
                                                    <div class="col-md-4 mb-2">
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <span class="bank-name-label" style="font-size: 12px; font-weight: 600;">{{ $bank_account->bank_name }}</span>
                                                            <input type="number" name="amounts[{{ $bank_account->id }}]"
                                                                class="form-control bank-amount-input" placeholder="0" min="0" value="0" style="max-width: 120px; height: 32px; border-radius: 6px; padding: 4px 8px; text-align: center;">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <!-- Installment payment fields - displayed only when installment check is active -->
                                        <div id="installment-fields-container" class="installment-fields-container" style="display:none; margin-bottom: 20px; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; background-color: #ffffff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                                            <input type="hidden" name="inst_interest_amount" class="inst_interest_amount_val" id="inst_interest_amount_val">
                                            <input type="hidden" name="inst_remaining" class="inst_remaining_val" id="inst_remaining_val">
                                            <input type="hidden" name="inst_total_with_interest" class="inst_total_with_interest_val" id="inst_total_with_interest_val">
                                            <input type="hidden" name="inst_per_installment" class="inst_per_installment_val" id="inst_per_installment_val">
                                            <style>
                                                .inst-label {
                                                    font-size: 13px;
                                                    font-weight: 700;
                                                    color: #1e293b;
                                                    margin-bottom: 6px;
                                                    display: block;
                                                }
                                                .inst-input {
                                                    border: 1px solid #cbd5e1;
                                                    border-radius: 20px;
                                                    height: 42px;
                                                    padding: 8px 16px;
                                                    font-size: 14px;
                                                    width: 100%;
                                                    outline: none;
                                                    transition: border-color 0.2s;
                                                }
                                                .inst-input:focus {
                                                    border-color: #f97316;
                                                }
                                                .inst-display-box {
                                                    background-color: #e2e8f0;
                                                    border: none;
                                                    border-radius: 20px;
                                                    height: 42px;
                                                    padding: 8px 16px;
                                                    font-size: 14px;
                                                    font-weight: 600;
                                                    color: #334155;
                                                    display: flex;
                                                    align-items: center;
                                                    width: 100%;
                                                }
                                                .inst-divider {
                                                    border-top: 1px solid #f1f5f9;
                                                    margin: 20px 0;
                                                    grid-column: span 2;
                                                }
                                            </style>
                                            <h6 style="color: #f97316; font-weight: 800; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 15px;">
                                                <i class="fa-solid fa-calculator mr-2"></i>{{ __('Installment Calculator') }}
                                            </h6>
                                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px 25px;">
                                                <!-- Row 1 -->
                                                <div>
                                                    <label class="inst-label">{{ __('Advance Pay') }}</label>
                                                    <input type="number" step="any" id="inst_advance_pay" name="inst_advance_pay" class="inst-input inst_advance_pay" value="0.00" min="0">
                                                </div>
                                                <div>
                                                    <label class="inst-label">{{ __('Remaining') }}</label>
                                                    <div id="inst_remaining" class="inst-display-box inst_remaining">0.00</div>
                                                </div>

                                                <!-- Row 2: Duration & Interval -->
                                                <div>
                                                    <label class="inst-label">{{ __('Total Duration (Days)') }}</label>
                                                    <input type="number" id="inst_total_duration" name="inst_total_duration" class="inst-input inst_total_duration" placeholder="e.g. 120" min="1">
                                                </div>
                                                <div>
                                                    <label class="inst-label">{{ __('Interval (Days)') }}</label>
                                                    <input type="number" id="inst_interval_days" name="inst_interval_days" class="inst-input inst_interval_days" placeholder="e.g. 30" value="30" min="1">
                                                    <div class="d-flex gap-1 mt-1 flex-wrap inst-preset-container" style="gap: 4px;">
                                                         <button type="button" class="btn btn-xs btn-outline-secondary inst-interval-preset" data-days="30" style="font-size: 10px; padding: 1px 6px; border-radius: 10px;">{{ __('Monthly (30d)') }}</button>
                                                         <button type="button" class="btn btn-xs btn-outline-secondary inst-interval-preset" data-days="15" style="font-size: 10px; padding: 1px 6px; border-radius: 10px;">{{ __('15 Days') }}</button>
                                                         <button type="button" class="btn btn-xs btn-outline-secondary inst-interval-preset" data-days="7" style="font-size: 10px; padding: 1px 6px; border-radius: 10px;">{{ __('Weekly (7d)') }}</button>
                                                         <button type="button" class="btn btn-xs btn-outline-secondary inst-interval-preset" data-days="1" style="font-size: 10px; padding: 1px 6px; border-radius: 10px;">{{ __('Daily (1d)') }}</button>
                                                     </div>
                                                </div>

                                                <!-- Row 3: Total Installments & Interest (%) -->
                                                <div>
                                                    <label class="inst-label">{{ __('Total Installments') }}</label>
                                                    <input type="number" id="inst_total_installments" name="inst_total_installments" class="inst-input inst_total_installments" placeholder="e.g. 4" min="1" value="1">
                                                </div>
                                                <div>
                                                    <label class="inst-label">{{ __('Interest (%)') }}</label>
                                                    <input type="number" step="any" id="inst_interest_percent" name="inst_interest_percent" class="inst-input inst_interest_percent" value="0.00" min="0">
                                                </div>

                                                <!-- Row 4: Interest Amount & Total with Interest -->
                                                <div>
                                                    <label class="inst-label">{{ __('Interest Amount') }}</label>
                                                    <div id="inst_interest_amount" class="inst-display-box inst_interest_amount">0.00</div>
                                                </div>
                                                <div>
                                                    <label class="inst-label">{{ __('Total with Interest') }}</label>
                                                    <div id="inst_total_with_interest" class="inst-display-box inst_total_with_interest">0.00</div>
                                                </div>

                                                <!-- Divider -->
                                                <div class="inst-divider"></div>

                                                <!-- Row 5: Per Installment & First Due Date -->
                                                <div>
                                                    <label class="inst-label">{{ __('Per Installment') }}</label>
                                                    <input type="number" step="any" id="inst_per_installment" name="inst_per_installment" class="inst-input inst_per_installment" placeholder="0.00" min="0">
                                                </div>
                                                <div>
                                                    <label class="inst-label">{{ __('First Due Date') }}</label>
                                                    <input type="date" id="inst_first_due_date" name="inst_first_due_date" class="inst-input inst_first_due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}">
                                                </div>

                                                <!-- Row 6: Last Due Date -->
                                                <div style="grid-column: span 2;">
                                                    <label class="inst-label">{{ __('Last Due Date') }}</label>
                                                    <input type="date" id="inst_last_due_date" name="inst_last_due_date" class="inst-input inst_last_due_date">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Actions Row: Selector Left, Buttons Right -->
                                        <div class="checkout-actions-row">
                                            <!-- Single/Multiple selector on left -->
                                            <div class="payment-type-selectors d-flex align-items-center">
                                                <label class="mr-2 mb-0" style="display: none !important;">
                                                    <input type="radio" id="inlineOneAccountCompact" name="payment_type" value="pos" checked>
                                                    <span>{{ __('Single Account') }}</span>
                                                </label>
                                                <label class="mb-0">
                                                    <input type="radio" id="inlineMultiAccountCompact" name="payment_type" value="checking">
                                                    <span>{{ __('Multiple Account') }}</span>
                                                </label>
                                                @if(env('APP_INSTALLMENT') == 'yes')
                                                <label class="mb-0 ml-4 cursor-pointer d-flex align-items-center" style="gap: 5px;">
                                                    <input type="checkbox" id="is_installment" class="is_installment_toggle" name="is_installment" value="1" style="width: 16px; height: 16px;">
                                                    <span class="font-weight-bold" style="font-size: 13px; color: #f97316;">{{ __('Installment Sale') }}</span>
                                                </label>
                                                @endif
                                                @if (env('APP_ONLINE') == 'yes')
                                                <div class="ml-4 d-flex align-items-center gap-2 flex-wrap">
                                                    <label class="mb-0 cursor-pointer d-flex align-items-center" style="gap: 5px;">
                                                        <input type="checkbox" class="online-sale-toggle-chk" style="width: 16px; height: 16px;">
                                                        <span class="font-weight-bold" style="font-size: 13px; color: #94a3b8;">{{ __('Online Sale') }}</span>
                                                    </label>
                                                    <div class="pre-order-chk-wrapper ml-2">
                                                        <label class="mb-0 cursor-pointer d-flex align-items-center bg-warning text-dark px-2 py-1" style="gap: 5px; font-size: 12.5px; font-weight: bold; border-radius: 6px !important; box-shadow: 0 2px 4px rgba(245, 158, 11, 0.3);">
                                                            <input type="checkbox" id="is_pre_order_chk" class="is-pre-order-chk" style="width: 15px; height: 15px; cursor: pointer;">
                                                            <span><i class="fa-solid fa-clock mr-1"></i>{{ __('Pre-Order') }}</span>
                                                        </label>
                                                    </div>
                                                    <div class="courier-select-wrapper ml-2" style="display: none;">
                                                        <select class="form-control form-control-sm courier-select" name="courier_type" style="min-width: 120px;">
                                                            <option value="">{{ __('Select Courier') }}</option>
                                                            <option value="Stead Fast">Stead Fast</option>
                                                            <option value="Pathao">Pathao</option>
                                                        </select>
                                                    </div>
                                                    <div class="platform-select-wrapper ml-2" style="display: none;">
                                                        <select class="form-control form-control-sm platform-select" name="platform_id" style="min-width: 120px;">
                                                            <option value="">{{ __('Select Platform') }}</option>
                                                            @foreach($platforms as $platform)
                                                                <option value="{{ $platform->id }}">{{ $platform->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="source-link-wrapper ml-2" style="display: none;">
                                                        <input type="text" class="form-control form-control-sm source-link-input" name="source_link" placeholder="{{ __('Source Link') }}" style="min-width: 150px;">
                                                    </div>
                                                     <div class="cod-amount-wrapper ml-2" style="display: none;">
                                                         <input type="number" step="any" class="form-control form-control-sm cod-amount-input" name="cod_amount" placeholder="{{ __('COD Amount') }}" style="min-width: 100px;">
                                                     </div>
                                                </div>
                                                @endif
                                                @if(env('APP_INVOICE_NOTE') == 'yes')
                                                <div class="note-btn-wrapper ml-2 d-flex align-items-center">
                                                    <button type="button" class="btn btn-sm btn-pos-note-trigger d-flex align-items-center" title="{{ __('Invoice Note') }}" style="height: 32px; border-radius: 6px; padding: 0 10px; font-size: 12px; font-weight: 600; border: 1px solid #cbd5e1; background: #ffffff; color: #334155; box-shadow: 0 1px 2px rgba(0,0,0,0.05); cursor: pointer; transition: all 0.2s;">
                                                        <i class="fa-solid fa-note-sticky text-primary mr-1" style="font-size: 13px;"></i>
                                                        <span>{{ __('Note') }}</span>
                                                        <span class="badge badge-primary pos-note-badge ml-1" style="display: {{ (!empty($preOrder->note) || !empty($quotation->note)) ? 'inline-block' : 'none' }}; font-size: 10px; border-radius: 10px; padding: 2px 6px; background-color: #2563eb; color: #ffffff;">✓</span>
                                                    </button>
                                                    <input type="hidden" name="note" class="note-input pos-invoice-note-field" value="{{ $preOrder->note ?? $quotation->note ?? '' }}">
                                                </div>
                                                @else
                                                <input type="hidden" name="note" class="note-input pos-invoice-note-field" value="{{ $preOrder->note ?? $quotation->note ?? '' }}">
                                                @endif
                                                @if(env('APP_REF_INV') == 'yes')
                                                <div class="ml-3 d-flex align-items-center">
                                                    <input type="text" class="form-control form-control-sm" name="ref_no" id="ref_no" placeholder="{{ __('Ref Inv') }}" value="{{ $preOrder->ref_no ?? $quotation->ref_no ?? '' }}" style="min-width: 130px; max-width: 160px; height: 32px; border-radius: 4px; font-size: 12px;">
                                                </div>
                                                @endif
                                            </div>

                                            <!-- Checkout Action Buttons on right -->
                                             <div class="payment-button-group">
                                                 @if(isset($preOrder) && !request('convert_mode'))
                                                 <button type="button" class="btn-pos-action btn-warning text-white font-weight-bold btn-pre-order-submit" style="background: #f59e0b; border: none;" title="{{ __('Update Pre-Order') }}">
                                                     <i class="fa-solid fa-save mr-1"></i> {{ __('Update Pre-Order') }}
                                                 </button>
                                                 @else
                                                 @if(env('APP_ONLINE') == 'yes' && !request('convert_mode'))
                                                 <button type="button" class="btn-pos-action btn-warning text-white font-weight-bold btn-pre-order-submit" style="background: #f59e0b; border: none;" title="{{ __('Place Pre-Order without stock deduction') }}">
                                                     <i class="fa-solid fa-clock mr-1"></i> {{ __('Pre-Order') }}
                                                 </button>
                                                 @endif
                                                 <button type="button" class="btn-pos-action btn-full-paid full_pay_btn d-none" title="Shortcut: F8" style="display: none !important;">
                                                     <i class="fa-solid fa-circle-check mr-1"></i> {{ __('Full Paid') }} (F8)
                                                 </button>
                                                 <button type="button" class="btn-pos-action btn-full-due full_due_btn" title="Shortcut: F9">
                                                     <i class="fa-solid fa-circle-minus mr-1"></i> {{ __('Full Due') }} (F9)
                                                 </button>
                                                 <button type="button" class="btn-pos-action btn-checkout" id="checkout" title="Shortcut: F12">
                                                     <i class="fa-solid fa-check-double mr-1"></i> {{ __('Checkout') }} (F12)
                                                 </button>
                                                 @endif
                                             </div>
                                        </div>

                                        <!-- Hidden/Dummy inputs for calculation compatibility -->
                                        <input type="hidden" name="payable_amount" id="payable_amount" value="">
                                        <input type="hidden" name="paid_amount" id="paid_amount" value="">
                                        <input type="hidden" name="due_amount" id="due_amount" value="">
                                        <input type="hidden" name="balance" id="balance" value="">
                                        <input type="hidden" name="previous_due" id="previous_due">
                                        <input type="number" step="any" name="estimated_amount" value="0" class="estimated_amount" style="display:none;">

                                        <!-- Bottom summary banner (4 blue cells) -->
                                        <div class="pos-summary-banner">
                                            <div class="pos-summary-banner-item">
                                                <span class="banner-label">{{ __('TOTAL QTY') }}</span>
                                                <span class="banner-value"># <span class="total_item">0</span></span>
                                            </div>
                                            <div class="pos-summary-banner-item">
                                                <span class="banner-label">{{ __('TOTAL GROSS') }}</span>
                                                <span class="banner-value"><span class="sub_total">0.00</span></span>
                                            </div>
                                            <div class="pos-summary-banner-item">
                                                <span class="banner-label">{{ __('TOTAL DISC') }}</span>
                                                <span class="banner-value"><span class="discount_amount_display">0.00</span></span>
                                            </div>
                                            <div class="pos-summary-banner-item">
                                                <span class="banner-label">{{ __('NET TOTAL') }}</span>
                                                <span class="banner-value"><span class="payable_amount">0.00</span></span>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>

                            <!-- IMEI Selection Modal -->
                            <div class="modal fade" id="imeiModal" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-md" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">{{ __('Select IMEI(s)') }}</h5>
                                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <input type="text" id="imeiSearch" class="form-control mb-3" placeholder="{{ __('Search IMEI...') }}">
                                            </div>
                                            <div id="imeiList" style="max-height: 400px; overflow-y: auto;">
                                                <!-- IMEIs will be loaded here -->
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="mr-auto">
                                                {{ __('Selected') }}: <span id="selectedImeiCount" class="font-weight-bold">0</span>
                                            </div>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                                            <button type="button" id="confirmImei" class="btn btn-primary">{{ __('Confirm Selection') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-5 sidebar-pos-col" id="category-sidebar-alt">
                    <div class="card card_top">
                        <div class="card-body">
                            <!-- Start row -->
                            <div class="row align-items-center ecommerce-sortby">
                                <!-- Start col -->
                                <div class="col-md-12 col-lg-12 col-xl-12">

                                    <select class="select2" name="category_id" id="getProductsByCat">
                                        <option selected value="">{{ __('Select Category') }}</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- End col -->
                            </div>
                            <div id="products">
                                <div class="row">
                                    @forelse($products as $product)
                                        @if(env('APP_IMEI') == 'no' && $product->imei == 1)
                                            @continue
                                        @endif
                                        @if ($product->is_service == 0)
                                            @php
                                                $stock_qty = product_stock_check($product);
                                                $pure_stock = (float) product_fake_stock_val($product);
                                                $is_out_of_stock = ($product->is_service == 0 && $pure_stock <= 0);
                                                $rtn = App\Models\ReturnItem::where('product_id', $product->id)->sum(
                                                    'main_qty',
                                                );
                                            @endphp
                                            <!-- Start col -->
                                            <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">
                                                <div class="product-bar productcss product {{ $is_out_of_stock ? 'out-of-stock' : '' }}"
                                                    data-value="{{ $product->id }}">
                                                    @if ($is_out_of_stock)
                                                        <div class="stock-badge" style="position: absolute; top: 10px; right: 10px; z-index: 1;">
                                                            <span class="badge badge-danger">{{ __('Out of Stock') }}</span>
                                                        </div>
                                                    @endif
                                                    <div class="product-head">
                                                        <img src="{{ !empty($product->images) ? url('uploads/products/' . $product->images) : url('backend/images/no_images.png') }}"
                                                            class="img-fluid" alt="product">
                                                    </div>
                                                    <div class="product-body">
                                                        <div class="product-name">{{ $product->name }}</div>
                                                        <div class="product-code">({{ $product->barcode }})</div>
                                                        <div class="stock-status">{{ $stock_qty }} in stock</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End col -->
                                        @endif
                                    @empty
                                        <div class="col-md-12" style="padding-bottom: 30px;">
                                            <div class="alert alert-danger text-center" role="alert"> {{ __('Products not available!') }}</div>
                                        </div>
                                    @endforelse
                                </div>
                                @if (env('APP_SERVICE') == 'yes')
                                    <p class="text-dark">{{ __('Services') }}</p>
                                    <div class="row">
                                        @forelse($services ?? [] as $product)
                                            <!-- Start col -->
                                            <div class="col-3 col-sm-3 col-md-3 col-lg-3 col-xl-3">
                                                <div class="product-bar productcss product"
                                                    data-value="{{ $product->id }}">
                                                    <div class="product-head">
                                                        <img src="{{ !empty($product->images) ? url('uploads/products/' . $product->images) : url('backend/images/no_images.png') }}"
                                                            class="img-fluid" alt="product">
                                                    </div>
                                                    <div class="product-body">
                                                        <div class="product-name">{{ $product->name }}</div>
                                                        <div class="product-code">({{ $product->barcode }})</div>
                                                        <div class="stock-status">
                                                            <span class="badge badge-info" style="font-size: 10px; padding: 2px 6px;">{{ __('Service Item') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End col -->
                                        @empty
                                            <div class="col-md-12" style="padding-bottom: 30px;">
                                                <div class="alert alert-info text-center" role="alert"> {{ __('No services available!') }}</div>
                                            </div>
                                        @endforelse
                                    </div>
                                @endif
                                <div class="pagination justify-content-center">
                                    {{ $products->onEachSide(0)->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End col -->
    @endif
    {{-- Add Modal --}}
    <form id="customerForm" action="{{ route('customer.store') }}" method="POST">
        @csrf
        <x-add-modal title="{{ __('Add Customer') }}" sizeClass="modal-xl">
            <div class="row">
                @if (auth()->user()->branch_id == 1)
                    <x-select label="{{ __('Branch *') }}" name="branch_id" md="6">
                        @foreach ($allBranch as $data)
                            <option value="{{ $data->id }}">{{ $data->name }}</option>
                        @endforeach
                    </x-select>
                @endif
                @php $phoneOnlyAllowed = (env('APP_CUSTOMER_PHONE_ONLY') == 'yes' && env('APP_ONLINE') != 'yes'); @endphp
                @if(env('APP_COMPACT_CUSTOMER') != 'yes')
                @if ($phoneOnlyAllowed)
                    <x-input label="{{ __('Customer Name') }}" type="text" name="name" placeholder="{{ __('Enter Customer Name') }}" md="6" />
                @else
                    <x-input label="{{ __('Customer Name *') }}" type="text" name="name" placeholder="{{ __('Enter Customer Name') }}" required md="6" />
                @endif
                @endif
                <x-input label="{{ __('Phone *') }}" type="text" name="phone" placeholder="{{ __('Enter Phone') }}" required md="6" />
                @if(env('APP_COMPACT_CUSTOMER') != 'yes')
                <x-input label="{{ __('Email') }}" type="email" name="email" placeholder="{{ __('Enter Email') }}" md="6" />
                <x-input label="{{ __('Address') }}" type="text" name="address" placeholder="{{ __('Enter Address') }}" md="6" />
                @if(!is_hide_customer_dates())
                <x-input label="{{ __('Birth Date') }}" type="date" name="birth_date" md="6" />
                @endif
                <x-input label="{{ __('Due Amount') }}" type="text" name="due_amount" value="0" md="6" />
                @if (env('APP_DISCOUNT_GROUP') == 'yes')
                <div class="form-group col-md-6 text-left">
                    <label class="font-weight-bold">{{ __('Discount Group') }}</label>
                    <select name="discount_group_id" class="form-control">
                        <option value="">{{ __('Select Discount Group') }}</option>
                        @foreach ($discountGroups as $dg)
                            <option value="{{ $dg->id }}">{{ $dg->name }} ({{ $dg->type == 'percentage' ? number_format($dg->value, 0).'%' : 'Tk '.number_format($dg->value, 0) }})</option>
                        @endforeach
                    </select>
                </div>
                @endif
                @endif

                @if(env('APP_AUTOMOBILE') == 'yes')
                <div class="col-md-12 text-left">
                    <h6 class="font-weight-bold text-primary mt-3">{{ __('Vehicle Details') }}</h6>
                </div>
                <div class="col-md-12">
                    <div class="vehicle-container">
                        <div class="row col-md-12 vehicle-block position-relative border p-2 mb-3 rounded" style="border-style: dashed !important; border-width: 1.5px !important; border-color: #cbd5e1 !important; margin-left: 0; margin-right: 0;">
                            <button type="button" class="btn btn-sm btn-danger remove-vehicle-btn position-absolute" style="top: -10px; right: -10px; z-index: 10; border-radius: 50%; width: 24px; height: 24px; padding: 0; display: none;"><i class="fa-solid fa-xmark"></i></button>
                            <x-input label="{{ __('Vehicle Name') }}" type="text" name="vehicle_name[]" placeholder="{{ __('Vehicle Name') }}" md="3" />
                            <x-input label="{{ __('Reg No') }}" type="text" name="reg_no[]" placeholder="{{ __('Reg No') }}" md="3" />
                            <x-input label="{{ __('Model') }}" type="text" name="model[]" placeholder="{{ __('Model') }}" md="3" />
                            <x-input label="{{ __('Made In') }}" type="text" name="made_in[]" placeholder="{{ __('Made In') }}" md="3" />
                            <x-input label="{{ __('Engine No') }}" type="text" name="engine_no[]" placeholder="{{ __('Engine No') }}" md="3" />
                            <x-input label="{{ __('Chassis No') }}" type="text" name="chassis_no[]" placeholder="{{ __('Chassis No') }}" md="3" />
                            <x-input label="{{ __('Milage') }}" type="text" name="milage[]" placeholder="{{ __('Milage') }}" md="3" />
                            <x-input label="{{ __('Driver Name') }}" type="text" name="driver_name[]" placeholder="{{ __('Driver Name') }}" md="3" />
                            <x-input label="{{ __('Driver Phone') }}" type="text" name="driver_phone[]" placeholder="{{ __('Driver Phone') }}" md="3" />
                        </div>
                    </div>
                </div>
                
                <div class="col-md-12 mt-2 text-left">
                    <button type="button" class="btn btn-info btn-sm add-vehicle-btn" style="border-radius: 6px;"><i class="fa fa-plus mr-1"></i>{{ __('Add Another Vehicle') }}</button>
                </div>
                @endif
            </div>
        </x-add-modal>
    </form>

    <!-- Hold List Modal -->
    <div class="modal fade" id="holdListModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fa fa-pause-circle mr-2"></i>{{ __('Hold List') }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Items') }}</th>
                                    <th>{{ __('Total') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody id="hold_list_body">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- POS Invoice Note Modal -->
    <div class="modal fade" id="posInvoiceNoteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 480px;">
            <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none; box-shadow: 0 15px 35px rgba(0,0,0,0.25);">
                <div class="modal-header bg-primary text-white" style="border-bottom: none; padding: 14px 20px;">
                    <h5 class="modal-title text-white font-weight-bold d-flex align-items-center mb-0" style="color: #ffffff !important; font-size: 16px;">
                        <i class="fa fa-sticky-note mr-2 text-warning"></i>
                        <span style="color: #ffffff !important;">{{ __('Invoice Note / Remark') }}</span>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.9; text-shadow: none; outline: none; color: #ffffff !important;">
                        <span aria-hidden="true" style="font-size: 24px; color: #ffffff !important;">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 20px; background: #f8fafc;">
                    <label class="font-weight-bold text-dark mb-2 d-flex align-items-center" style="font-size: 13px;">
                        <i class="fa fa-pencil-alt mr-2 text-primary"></i>{{ __('Note for this Invoice') }}
                    </label>
                    <textarea id="modal_pos_invoice_note_textarea" class="form-control" rows="4" placeholder="{{ __('Type any note, instructions, delivery instructions, or remarks for this invoice...') }}" style="border-radius: 8px; border: 1.5px solid #cbd5e1; font-size: 13px; padding: 10px; resize: vertical; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05); background: #ffffff;"></textarea>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <small class="text-muted" style="font-size: 11px;">
                            <i class="fa fa-info-circle text-info mr-1"></i>{{ __('This note will appear on printed invoices and receipts.') }}
                        </small>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="btn_clear_pos_invoice_note" style="font-size: 11px; padding: 2px 8px; border-radius: 5px;">
                            <i class="fa fa-trash mr-1"></i>{{ __('Clear') }}
                        </button>
                    </div>
                </div>
                <div class="modal-footer" style="padding: 12px 20px; background: #ffffff; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 8px;">
                    <button type="button" class="btn btn-secondary btn-sm font-weight-bold" data-dismiss="modal" style="border-radius: 6px; padding: 6px 16px; font-size: 13px;">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" class="btn btn-primary btn-sm font-weight-bold" id="btn_save_pos_invoice_note" data-dismiss="modal" style="border-radius: 6px; padding: 6px 20px; font-size: 13px; background: #2563eb; border: none; box-shadow: 0 2px 5px rgba(37,99,235,0.3);">
                        <i class="fa fa-check mr-1"></i> {{ __('Save Note') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Multiple Account Payments Modal -->
    <div class="modal fade" id="multipleAccountModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                <div class="modal-header bg-primary text-white" style="border-bottom: none; padding: 16px 20px;">
                    <h5 class="modal-title font-weight-bold" style="font-size: 16px;"><i class="fa-solid fa-building-columns mr-2"></i>{{ __('Multiple Account Payments') }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8; outline: none;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    <div class="grand-total-banner mb-3 text-center py-3 rounded" style="font-size: 15px; font-weight: 800;">
                        {{ __('Grand Total') }}: <span class="modal_payable_amount">0.00</span>
                    </div>
                    <div class="bank-accounts-list" style="max-height: 300px; overflow-y: auto; padding-right: 5px;">
                        @foreach ($bank_accounts as $bank_account)
                            @php
                                $firstLetter = substr($bank_account->bank_name, 0, 1);
                                $badgeColor = '#3b82f6';
                                if ($firstLetter === 'C') $badgeColor = '#10b981';
                                if ($firstLetter === 'B') $badgeColor = '#3b82f6';
                                if ($firstLetter === 'S') $badgeColor = '#8b5cf6';
                            @endphp
                            <div class="bank-account-item d-flex align-items-center justify-content-between p-2 mb-2 rounded border" style="border-radius: 8px !important;">
                                <div class="d-flex align-items-center">
                                    <span class="bank-badge rounded-circle d-flex align-items-center justify-content-center mr-3 font-weight-bold text-uppercase text-white" style="width: 34px; height: 34px; background: {{ $badgeColor }}; font-size: 13px;">
                                        {{ $firstLetter }}
                                    </span>
                                    <span class="bank-name font-weight-bold" style="font-size: 13px;">{{ $bank_account->bank_name }}</span>
                                </div>
                                <input type="number" step="any" class="form-control bank-modal-amount-input" data-id="{{ $bank_account->id }}" placeholder="0" min="0" value="0" style="max-width: 100px; height: 32px; border-radius: 6px; padding: 4px 8px; text-align: center;">
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top" style="border-top: 1.5px solid #e2e8f0;">
                        <span class="font-weight-bold" style="font-size: 12px; color: #64748b;">{{ __('Total Allocated') }}:</span>
                        <span class="total_allocated_text font-weight-bold" style="font-size: 16px; color: #10b981;">0.00</span>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: none; padding: 12px 20px 20px 20px; gap: 8px;">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="height: 38px; border-radius: 8px; font-weight: 700; font-size: 13px; padding: 0 20px;">{{ __('Cancel') }}</button>
                    <button type="button" id="confirmMultipleAccounts" class="btn btn-primary" style="height: 38px; border-radius: 8px; font-weight: 700; font-size: 13px; padding: 0 24px;">{{ __('Confirm') }}</button>
                </div>
            </div>
    @if(env('APP_MOBILE_SCANNER') == 'yes')
    <!-- Camera Barcode Scanner Modal -->
    <div class="modal fade" id="cameraScannerModal" tabindex="-1" role="dialog" aria-labelledby="cameraScannerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cameraScannerModalLabel"><i class="fa fa-camera mr-2"></i>{{ __('Scan Barcode') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="btn-close-scanner">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div id="reader" style="width: 100%; min-height: 250px; background: #f8f9fa; border: 1px dashed #ccc; border-radius: 4px;"></div>
                    <div id="scanner-result" class="mt-2 text-success font-weight-bold"></div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection
@push('js')
    <script>
        // Toggle Sidebar with Persistence

        $(document).ready(function() {
            $('#cityDropdown').on('change', function() {
                let city_id = $(this).val();
                console.log(city_id);
                if (city_id) {
                    $.ajax({
                        url: "{{ route('pathao.zones') }}",
                        type: "GET",
                        data: {
                            city_id: city_id
                        },
                        success: function(response) {
                            if (response.code === 200) {
                                let options = '<option value="">Select Zone</option>';
                                $.each(response.data.data, function(index, zone) {
                                    options +=
                                        `<option value="${zone.zone_id}">${zone.zone_name}</option>`;
                                });
                                $("#zoneDropdown").html(options);
                            }
                        }
                    });
                } else {
                    $("#zoneDropdown").html('<option value="">Select Zone</option>');
                }
            });
            $('#zoneDropdown').on('change', function() {
                let zone_id = $(this).val();
                if (zone_id) {
                    $.ajax({
                        url: "{{ route('pathao.areas') }}",
                        type: "GET",
                        data: {
                            zone_id: zone_id
                        },
                        success: function(response) {
                            if (response.code === 200) {
                                let options = '<option value="">Select Area</option>';
                                $.each(response.data.data, function(index, area) {
                                    options +=
                                        `<option value="${area.area_id}">${area.area_name}</option>`;
                                });
                                $("#areaDropdown").html(options);
                            }
                        }
                    });
                } else {
                    $("#areaDropdown").html('<option value="">Select Area</option>');
                }
            });
            $('#editCityDropdown').on('change', function() {
                let city_id = $(this).val();
                if (city_id) {
                    $.ajax({
                        url: "{{ route('pathao.zones') }}",
                        type: "GET",
                        data: {
                            city_id: city_id
                        },
                        success: function(response) {
                            if (response.code === 200) {
                                let options = '<option value="">Select Zone</option>';
                                $.each(response.data.data, function(index, zone) {
                                    options +=
                                        `<option value="${zone.zone_id}">${zone.zone_name}</option>`;
                                });
                                $("#editZoneDropdown").html(options);
                            }
                        }
                    });
                } else {
                    $("#editZoneDropdown").html('<option value="">Select Zone</option>');
                }
            });
            $('#editZoneDropdown').on('change', function() {
                let zone_id = $(this).val();
                if (zone_id) {
                    $.ajax({
                        url: "{{ route('pathao.areas') }}",
                        type: "GET",
                        data: {
                            zone_id: zone_id
                        },
                        success: function(response) {
                            if (response.code === 200) {
                                let options = '<option value="">Select Area</option>';
                                $.each(response.data.data, function(index, area) {
                                    options +=
                                        `<option value="${area.area_id}">${area.area_name}</option>`;
                                });
                                $("#editAreaDropdown").html(options);
                            }
                        }
                    });
                } else {
                    $("#editAreaDropdown").html('<option value="">Select Area</option>');
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $("#customerForm").submit(function(e) {
                e.preventDefault(); // Stopped page reload

                if (!navigator.onLine) {
                    let name = $(this).find('input[name="name"]').val() || '';
                    let phone = $(this).find('input[name="phone"]').val();
                    let id = 'OFF-CUST-' + Date.now();
                    
                    let newCust = { id: id, name: name, phone: phone };
                    let offlineCusts = JSON.parse(localStorage.getItem('pos-offline-customers-new') || '[]');
                    offlineCusts.push(newCust);
                    localStorage.setItem('pos-offline-customers-new', JSON.stringify(offlineCusts));
                    
                    // Add to dropdown
                    let isCompact = "{{ env('APP_COMPACT_CUSTOMER') == 'yes' }}";
                    let displayName = isCompact ? phone : ((name ? name + " - " : "") + phone);
                    let option = `<option value="${id}" selected>${displayName}</option>`;
                    $("#customer_id").append(option).val(id).trigger("change");
                    
                    $("#addModal").modal("hide");
                    $("#customerForm")[0].reset();
                    iziToast.success({ title: "{{ __('Customer saved offline!') }}", position: "topRight" });
                    return;
                }

                let formData = $(this).serialize();

                $.ajax({
                    url: $(this).attr("action"),
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success) {
                            // Closing the Modal
                            $("#addModal").modal("hide");
                            $("#customerForm")[0].reset();

                            // Check if option already exists
                            if ($("#customer_id option[value='" + res.customer.id + "']").length > 0) {
                                $("#customer_id").val(res.customer.id).trigger("change");
                            } else {
                                // Adding to Customer dropdown
                                let isCompact = "{{ env('APP_COMPACT_CUSTOMER') == 'yes' }}";
                                let dg = res.customer.discount_group || {};
                                let dgType = dg.type || '';
                                let dgVal = dg.value || '';
                                let dgText = dg.name ? ' (Discount: ' + (dgType === 'percentage' ? parseInt(dgVal) + '%' : 'Tk ' + parseInt(dgVal)) + ')' : '';
                                let displayName = (isCompact ? res.customer.phone : (res.customer.name + " - " + res.customer.phone)) + dgText;
                                let newCustomer =
                                    `<option value="${res.customer.id}" data-discount-type="${dgType}" data-discount-value="${dgVal}" selected>${displayName}</option>`;
                                $("#customer_id").append(newCustomer).val(res.customer.id).trigger(
                                    "change");
                            }

                            iziToast.success({
                                title: "{{ __('Customer Added Successfully!') }}",
                                position: "topRight",
                            });
                        } else {
                            iziToast.error({
                                title: "{{ __('Something went wrong!') }}",
                                position: "topRight",
                            });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            var errMsgs = [];
                            $.each(errors, function(key, val) {
                                errMsgs.push(val.join(' '));
                            });
                            iziToast.error({
                                title: "{{ __('Validation Error') }}",
                                message: errMsgs.join("<br>"),
                                position: "topRight",
                            });
                        } else {
                            iziToast.error({
                                title: "{{ __('Failed to add customer!') }}",
                                position: "topRight",
                            });
                        }
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            var appLoyaltyEnabled = "{{ env('APP_LOYALTY') == 'yes' ? 'yes' : 'no' }}";

            function updateTotalPoint() {
                if (appLoyaltyEnabled !== 'yes') {
                    $('#point_row').hide();
                    $('#pay_point_col').hide();
                    $('.pay_point').val(0);
                    return;
                }
                var customerId = $('#customer_id').val();

                if (customerId && customerId != "1") {
                    $('#point_row').show();
                    $.ajax({
                        url: '/customer/points/' + customerId,
                        type: 'GET',
                        success: function(response) {
                            var totalPoint = response.total_point ? parseFloat(response.total_point) : 0;
                            var pointRate = response.point_rate ? parseFloat(response.point_rate) : 0.75;
                            var pointValue = (totalPoint * pointRate).toFixed(2);

                            $('.total_point').text(totalPoint);
                            $('#customer_available_points').text(totalPoint);
                            $('#customer_points_val').text(pointValue);
                            $('.chp-points').html(totalPoint + ' pts (<span style="color:#059669;font-weight:700;">৳' + pointValue + '</span>)');

                            if (totalPoint > 0) {
                                $('#pay_point_col').slideDown(150);
                                $('.pay_point').attr('max', totalPoint);
                            } else {
                                $('#pay_point_col').slideUp(150);
                                $('.pay_point').val(0);
                                $('#point_discount_text').html('Discount: ৳0.00');
                            }
                            updateInlineAmounts();
                        }
                    });
                } else {
                    $('#point_row').hide();
                    $('.total_point').text('0');
                    $('#customer_available_points').text('0');
                    $('#customer_points_val').text('0.00');
                    $('#pay_point_col').slideUp(150);
                    $('.pay_point').val(0);
                    $('#point_discount_text').html('Discount: ৳0.00');
                    updateInlineAmounts();
                }
            }
            $('#customer_id').change(updateTotalPoint);
            updateTotalPoint();

            $(document).on('input', '.pay_point', function() {
                var maxPoint = parseFloat($(this).attr('max')) || 0;
                var val = parseFloat($(this).val()) || 0;
                if (val > maxPoint) {
                    $(this).val(maxPoint);
                } else if (val < 0) {
                    $(this).val(0);
                }
                updateInlineAmounts();
            });

            $(document).on('click', '.btn-use-max-points, .btn-quick-pay-point', function(e) {
                e.preventDefault();
                var payableAmount = parseFloat($('#payable_amount').val()) || 0;
                var availablePoints = parseFloat($('.pay_point').attr('max')) || 0;
                var pointRate = 0.75;

                var neededPoints = Math.ceil(payableAmount / pointRate);
                var pointsToUse = Math.min(availablePoints, neededPoints);

                $('.pay_point').val(pointsToUse);
                
                var pointDiscount = pointsToUse * pointRate;
                var remainingPayable = Math.max(0, payableAmount - pointDiscount);
                $('.pay_amount').val(remainingPayable.toFixed(2));
                updateInlineAmounts();
            });
        });
    </script>

    <script>
        // ===== Customer Quick History Panel (Both Layouts) =====
        var quickHistoryUrl = "{{ route('customer_quick_history', '__ID__') }}";

        function buildInvoiceHtml(res) {
            var c = res.customer, s = res.stats;
            var invoiceHtml = '';
            if (!res.recent_invoices || res.recent_invoices.length === 0) {
                invoiceHtml = '<span style="color:#94a3b8; font-style:italic;">{{ __("No purchases yet") }}</span>';
            } else {
                res.recent_invoices.forEach(function(inv) {
                    var due = parseFloat(String(inv.due_amount).replace(/,/g,''));
                    var dueText = due > 0
                        ? '<span style="color:#ef4444; font-weight:600;">&#9888; {{ __("Due") }}: ' + inv.due_amount + '</span>'
                        : '<span style="color:#10b981;">&#10003; {{ __("Paid") }}</span>';
                    var itemsText = (inv.items && inv.items.length > 0)
                        ? inv.items.join(', ') + (inv.items_count > 3 ? ' +' + (inv.items_count - 3) + ' {{ __("more") }}' : '')
                        : '-';
                    invoiceHtml += '<div style="background:#fff;border-radius:6px;padding:5px 8px;margin-bottom:4px;border-left:3px solid #0ea5e9;box-shadow:0 1px 2px rgba(0,0,0,0.04);">' +
                        '<div style="display:flex;justify-content:space-between;">' +
                        '<span style="font-weight:700;color:#0369a1;">' + inv.invoice_no + '</span>' +
                        '<span style="color:#94a3b8;font-size:11px;">' + inv.date + '</span></div>' +
                        '<div style="color:#475569;margin-top:2px;font-size:11px;">' + itemsText + '</div>' +
                        '<div style="display:flex;justify-content:space-between;margin-top:2px;">' +
                        '<span style="color:#334155;font-weight:600;">&#2547; ' + inv.grand_total + '</span>' + dueText + '</div></div>';
                });
            }
            return invoiceHtml;
        }

        window.currentFraudData = null;

        function applyPanelData(panel, res) {
            var c = res.customer, s = res.stats;
            panel.find('.chp-name').text(c.name);
            panel.find('.chp-phone').text(c.phone || '-');
            panel.find('.chp-points').text(c.points);
            panel.find('.chp-purchases').text(s.total_purchases);
            panel.find('.chp-spent').text(s.total_spent);
            panel.find('.chp-due').text(s.total_due);
            panel.find('.chp-claims').text(s.active_claims);
            var dueVal = parseFloat(String(s.total_due).replace(/,/g,''));
            panel.find('.chp-due').css('color', dueVal > 0 ? '#ef4444' : '#10b981');
            panel.find('.chp-invoices-list').html(buildInvoiceHtml(res));
            if (res.fraud_check) {
                window.currentFraudData = res.fraud_check;
            }
            panel.slideDown(250);
        }

        function openFraudDetailsModal() {
            var fc = window.currentFraudData;
            if (!fc) {
                iziToast.info({ title: "{{ __('No courier fraud data available') }}", position: "topRight" });
                return;
            }
            $('#cfm_cust_name').text(fc.customer_name || 'Customer');
            $('#cfm_cust_phone').text(fc.phone || '-');
            $('#cfm_risk_badge').text(fc.risk_label).removeClass().addClass('badge badge-pill px-3 py-2 ' + fc.badge_class);
            if (fc.badge_bg) $('#cfm_risk_badge').css('background-color', fc.badge_bg).css('color', '#fff');

            $('#cfm_success_rate').text(fc.overall_success_rate + '%');

            if (fc.warning_message) {
                $('#cfm_warning_text').text(fc.warning_message);
                $('#cfm_warning_alert').show();
            } else {
                $('#cfm_warning_alert').hide();
            }

            // Populate Breakdown Table
            var tbody = '';
            var sources = fc.sources || {};
            
            var sourceNames = {
                'internal': '{{ __("Internal POS Orders") }}',
                'steadfast': '{{ __("Steadfast Courier") }}',
                'pathao': '{{ __("Pathao Courier") }}',
                'courier_api': '{{ __("Courier Fraud API") }}'
            };

            $.each(sources, function(key, s) {
                var total = s.total_orders || s.total_parcels || 0;
                var delivered = s.delivered || 0;
                var cancelled = s.returned || s.cancelled || 0;
                var rate = s.success_rate !== undefined ? s.success_rate + '%' : '-';
                var name = sourceNames[key] || key.toUpperCase();

                var badgeClass = s.success_rate >= 80 ? 'text-success' : (s.success_rate >= 50 ? 'text-warning' : 'text-danger');

                tbody += '<tr>' +
                    '<td class="text-left font-weight-bold">' + name + '</td>' +
                    '<td><span class="badge badge-light px-2 py-1">' + total + '</span></td>' +
                    '<td><span class="badge badge-success px-2 py-1">' + delivered + '</span></td>' +
                    '<td><span class="badge badge-danger px-2 py-1">' + cancelled + '</span></td>' +
                    '<td class="font-weight-bold ' + badgeClass + '">' + rate + '</td>' +
                '</tr>';
            });

            $('#cfm_sources_tbody').html(tbody);
            $('#customerFraudModal').modal('show');
        }

        function checkCurrentCustomerFraudModal() {
            var customerId = $('#customer_id').val();
            if (!customerId || customerId == 1) {
                iziToast.info({
                    title: "{{ __('Please select a customer first') }}",
                    position: "topRight"
                });
                return;
            }

            var selectedText = $('#customer_id option:selected').text();
            $('#cfm_cust_name').text(selectedText);
            $('#cfm_cust_phone').text('...');
            $('#cfm_risk_badge').text('Loading...').removeClass().addClass('badge badge-pill badge-secondary px-3 py-2');
            $('#cfm_warning_alert').hide();
            $('#cfm_sources_tbody').html('<tr><td colspan="5" class="text-center py-4"><i class="fa fa-spinner fa-spin fa-2x text-primary"></i><br><span class="text-muted small mt-2 d-inline-block">{{ __("Checking Courier Fraud Profile...") }}</span></td></tr>');
            $('#customerFraudModal').modal('show');

            var url = "{{ route('customer.fraud-check', ':id') }}".replace(':id', customerId);
            $.get(url, function(fc) {
                if (!fc || !fc.success) {
                    $('#cfm_sources_tbody').html('<tr><td colspan="5" class="text-danger py-3">{{ __("Failed to fetch fraud profile") }}</td></tr>');
                    return;
                }

                window.currentFraudData = fc;
                $('#cfm_cust_name').text(fc.customer_name || selectedText);
                $('#cfm_cust_phone').text(fc.phone || '-');
                $('#cfm_risk_badge').text(fc.risk_label).removeClass().addClass('badge badge-pill px-3 py-2 ' + fc.badge_class);
                if (fc.badge_bg) $('#cfm_risk_badge').css('background-color', fc.badge_bg).css('color', '#fff');

                $('#cfm_success_rate').text(fc.overall_success_rate + '%');

                if (fc.warning_message) {
                    $('#cfm_warning_text').text(fc.warning_message);
                    $('#cfm_warning_alert').show();
                } else {
                    $('#cfm_warning_alert').hide();
                }

                var tbody = '';
                var sources = fc.sources || {};
                var sourceNames = {
                    'internal': '{{ __("Internal POS Orders") }}',
                    'steadfast': '{{ __("Steadfast Courier") }}',
                    'pathao': '{{ __("Pathao Courier") }}',
                    'courier_api': '{{ __("Courier Fraud API") }}'
                };

                $.each(sources, function(key, s) {
                    var total = s.total_orders || s.total_parcels || 0;
                    var delivered = s.delivered || 0;
                    var cancelled = s.returned || s.cancelled || 0;
                    var rate = (s.success_rate !== null && s.success_rate !== undefined) ? s.success_rate + '%' : '-';
                    var name = sourceNames[key] || key.toUpperCase();

                    var badgeClass = s.success_rate >= 80 ? 'text-success' : (s.success_rate >= 50 ? 'text-warning' : (s.success_rate !== null ? 'text-danger' : 'text-muted'));

                    tbody += '<tr>' +
                        '<td class="text-left font-weight-bold">' + name + '</td>' +
                        '<td><span class="badge badge-light px-2 py-1">' + total + '</span></td>' +
                        '<td><span class="badge badge-success px-2 py-1">' + delivered + '</span></td>' +
                        '<td><span class="badge badge-danger px-2 py-1">' + cancelled + '</span></td>' +
                        '<td class="font-weight-bold ' + badgeClass + '">' + rate + '</td>' +
                    '</tr>';
                });

                $('#cfm_sources_tbody').html(tbody);
            }).fail(function() {
                $('#cfm_sources_tbody').html('<tr><td colspan="5" class="text-danger py-3">{{ __("Error checking courier fraud profile") }}</td></tr>');
            });
        }

        function loadCustomerHistory(customerId) {
            $('.customer-history-panel').slideUp(150);
            if (!customerId || customerId == 1) return;
            var url = quickHistoryUrl.replace('__ID__', customerId);
            $.get(url, function(res) {
                if (!res.success) return;
                // Fill all panels that are in a visible container
                $('.customer-history-panel').each(function() {
                    var panel = $(this);
                    var container = panel.closest('.cart-search-header');
                    if (container.is(':visible') || container.length === 0) {
                        applyPanelData(panel, res);
                    }
                });
            });
        }

        function applyCustomerDiscountGroup(customerId) {
            var selectedOption = $('select[name="customer_id"]').find('option:selected');
            var discountType = selectedOption.attr('data-discount-type');
            var discountValue = selectedOption.attr('data-discount-value');

            if (discountType && discountValue !== undefined && discountValue !== null && discountValue !== '') {
                if (discountType === 'percentage') {
                    $('.discount_type_select').val('percent');
                    $('.discount_val_input').val(parseFloat(discountValue));
                    $('.discount_amount').val(parseFloat(discountValue) + '%');
                } else if (discountType === 'fixed') {
                    $('.discount_type_select').val('fixed');
                    $('.discount_val_input').val(parseFloat(discountValue));
                    $('.discount_amount').val(parseFloat(discountValue));
                }
            } else {
                $('.discount_val_input').val('');
                $('.discount_amount').val('');
            }
            if (typeof totalCalculate === 'function') {
                totalCalculate();
            }
        }

        $(document).ready(function() {
            $(document).on('change', 'select[name="customer_id"]', function() {
                loadCustomerHistory($(this).val());
                applyCustomerDiscountGroup($(this).val());
            });
            // Initial load check
            var initialVal = $('select[name="customer_id"]').val();
            if (initialVal && initialVal != 1) {
                loadCustomerHistory(initialVal);
            }
            applyCustomerDiscountGroup(initialVal);
        });
    </script>

    <!-- Offline Sales Modal -->
    <div class="modal fade" id="offlineSalesModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="z-index: 9999;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Offline Pending Sales') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Total') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody id="offline_sales_body">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-primary" id="sync_all_offline">{{ __('Sync All Now') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Courier Fraud Details Modal -->
    <div class="modal fade" id="customerFraudModal" tabindex="-1" role="dialog" aria-labelledby="customerFraudModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="z-index: 10050;">
            <div class="modal-content" style="border-radius: 14px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                <div class="modal-header text-white" id="cfm_modal_header" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 16px 20px;">
                    <h5 class="modal-title text-white font-weight-bold d-flex align-items-center gap-2" id="customerFraudModalLabel">
                        <i class="fa fa-shield-alt"></i> {{ __('Customer Courier & Fraud Profile') }}
                    </h5>
                    <button type="button" class="close text-white opacity-8" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 20px; background: #f8fafc;">
                    <!-- Top Customer Summary Card -->
                    <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded-lg mb-3 shadow-sm border" style="border-radius: 10px;">
                        <div>
                            <h6 class="font-weight-bold mb-1 text-primary" id="cfm_cust_name">-</h6>
                            <div class="text-muted small"><i class="fa fa-phone"></i> <span id="cfm_cust_phone">-</span></div>
                        </div>
                        <div class="text-right">
                            <span id="cfm_risk_badge" class="badge badge-pill px-3 py-2" style="font-size: 13px;">-</span>
                            <div class="small font-weight-bold mt-1 text-dark">{{ __('Success Rate') }}: <span id="cfm_success_rate" class="text-success">100%</span></div>
                        </div>
                    </div>

                    <!-- Warning Alert Box -->
                    <div id="cfm_warning_alert" class="alert alert-danger shadow-sm mb-3" style="display: none; border-left: 5px solid #dc3545; border-radius: 8px;">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-exclamation-triangle fa-2x mr-3 text-danger"></i>
                            <div id="cfm_warning_text" class="font-weight-bold"></div>
                        </div>
                    </div>

                    <!-- Courier Source Breakdown Table -->
                    <div class="card border-0 shadow-sm rounded-lg overflow-hidden mb-3" style="border-radius: 10px;">
                        <div class="card-header bg-white font-weight-bold text-dark border-bottom py-2">
                            <i class="fa fa-truck text-info"></i> {{ __('Courier & Order History Source Breakdown') }}
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0 text-center" style="font-size: 13px;">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-left">{{ __('Source') }}</th>
                                        <th>{{ __('Total Parcels/Orders') }}</th>
                                        <th>{{ __('Delivered') }}</th>
                                        <th>{{ __('Cancelled/Returned') }}</th>
                                        <th>{{ __('Success Rate') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="cfm_sources_tbody">
                                    <tr><td colspan="5" class="text-muted py-3">{{ __('Loading fraud data...') }}</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-white border-top">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" data-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Description Edit Modal -->
    <div class="modal fade" id="editProductDescModal" tabindex="-1" role="dialog" aria-labelledby="editProductDescModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="z-index: 9999;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductDescModalLabel">{{ __('Edit Product Description') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_desc_product_id">
                    <div class="form-group">
                        <label for="edit_desc_textarea" class="fw-bold">{{ __('Product Description') }}</label>
                        <textarea id="edit_desc_textarea" class="form-control" rows="4" placeholder="{{ __('Enter product description') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-primary" id="save_product_desc_btn">{{ __('Save Changes') }}</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Select the input field when the page loads
        window.onload = function() {
            var inputFields = document.getElementsByClassName('product_search');
            if (inputFields.length > 0) {
                inputFields[0].focus();
                inputFields[0].select();
            }
        };
        // Clear previous draft cart on page entry
        @if(!isset($quotation))
            localStorage.removeItem("pos-items");
        @endif


    </script>

    <script>
        // Explicit submit guard to prevent accidental scanner/enter-key page refreshes
        window.isExplicitSubmitAllowed = false;
        window.checkExplicitSubmit = function(e) {
            if (!window.isExplicitSubmitAllowed) {
                if (e) {
                    if (typeof e.preventDefault === 'function') e.preventDefault();
                    if (typeof e.stopPropagation === 'function') e.stopPropagation();
                }
                return false;
            }
            return true;
        };

        // iziToast compatibility layer mapping to ToastMagic
        if (typeof iziToast === 'undefined') {
            window.iziToast = {
                show: function(opts) {
                    if (window.toastMagic) {
                        if (opts.class === 'iziToast-success') window.toastMagic.success(opts.message || opts.title);
                        else if (opts.class === 'iziToast-error') window.toastMagic.error(opts.message || opts.title);
                        else window.toastMagic.info(opts.message || opts.title);
                    } else {
                        console.log(opts.message || opts.title);
                    }
                },
                success: function(opts) {
                    if (window.toastMagic) window.toastMagic.success(opts.message || opts.title);
                    else console.log(opts.message || opts.title);
                },
                error: function(opts) {
                    if (window.toastMagic) window.toastMagic.error(opts.message || opts.title);
                    else console.log(opts.message || opts.title);
                },
                warning: function(opts) {
                    if (window.toastMagic) window.toastMagic.warning(opts.message || opts.title);
                    else console.log(opts.message || opts.title);
                },
                info: function(opts) {
                    if (window.toastMagic) window.toastMagic.info(opts.message || opts.title);
                    else console.log(opts.message || opts.title);
                }
            };
        }

        // Page Load
        var empty = '';
        // $('body').addClass('toggle-menu');
        setTimeout(function() {
            var $search = $('.product_search:visible').first();
            if ($search.length) $search.focus();
        }, 100);

        var localData = localStorage.getItem('pos-items') ? JSON.parse(localStorage.getItem('pos-items')) : [];
        var env_warranty = "{{ env('APP_WARRANTY') }}";

        function showList() {
            if (localData.length <= 0) {
                $("#tbody").html(empty);
            } else {
                localData.forEach((item, index) => {
                    domPrepend(item, index, item.variation_code);
                });
            }
        }

        showList();
        estimatedAmount();

        var cartList = [];

        // Helper Functions
        function empty_field_check(placeholder) {
            if (typeof placeholder == NaN) {
                placeholder = 0;
            } else if (placeholder == null) {
                placeholder = 0;
            } else if (placeholder.trim() == "") {
                placeholder = 0;
            } else if (placeholder == 'null') {
                placeholder = 0;
            }
            return placeholder;
        }

        function parseQuantityInput(input, related_by) {
            input = input.toString().trim();

            if (input === '' || isNaN(input)) {
                return { main_qty: 0, sub_qty: 0 };
            }

            let main_qty = 0;
            let sub_qty = 0;

            if (related_by > 1) {
                // Split by dot (.)
                let parts = input.split('.');
                main_qty = parseInt(parts[0]) || 0;

                if (parts.length > 1) {
                    // Take the decimal digits directly as subunit (e.g. .2 => 2 pcs)
                    let decimalPart = parts[1].replace(/[^0-9]/g, ''); // remove non-numeric
                    sub_qty = parseInt(decimalPart) || 0;

                    // Prevent overflow — if subunit >= related_by, convert extra to main_qty
                    if (sub_qty >= related_by) {
                        main_qty += Math.floor(sub_qty / related_by);
                        sub_qty = sub_qty % related_by;
                    }
                }
            } else {
                let val = parseFloat(input);
                if (val < 0) val = 0;
                main_qty = val;
                sub_qty = 0;
            }

            return {
                main_qty: main_qty,
                sub_qty: sub_qty
            };
        }


        function to_sub_unit(main_val, sub_val, related_by, has_sub_unit) {
            if (has_sub_unit == 'true') {
                return (main_val * related_by) + sub_val;
            }
            return main_val;
        }

        function convert_to_main_and_sub(quantity, has_sub_unit, related_by) {
            var main_qty = 0;
            var main_qty_as_sub = 0;
            var sub_qty = 0;

            main_qty = parseFloat(quantity);

            if (has_sub_unit == "true" && quantity != 0 && related_by != 0) {
                main_qty = parseInt(quantity / related_by);
                main_qty_as_sub = main_qty * related_by;
                sub_qty = quantity - main_qty_as_sub;
            }

            return {
                'main_qty': main_qty,
                'sub_qty': sub_qty
            };
        }

        function update_row_discount_and_subtotal(row) {
            let main_qty = parseFloat(row.find('.main_qty').val() || row.find('.quantity-input').val()) || 0;
            let sub_qty = parseFloat(row.find('.sub_qty').val()) || 0;
            let unit_price = parseFloat(row.find('.rate').val()) || 0;
            let related_by = parseInt(row.find('.quantity-input').attr('data-related')) || 1;
            let has_sub_unit = row.find('.has_sub_unit').val();

            let sub_unit_price = 0;
            if (has_sub_unit == "true" && related_by != 0) {
                sub_unit_price = parseFloat(unit_price / related_by);
            }
            let lineGross = (main_qty * unit_price) + (sub_qty * sub_unit_price);

            let discVal = parseFloat(row.find('.product_discount_val').val()) || 0;
            let discType = row.find('.product_discount_type').val() || 'fixed';
            let calculatedDiscountAmount = 0;

            if (discType === 'percent') {
                if (discVal > 100) {
                    discVal = 100;
                    row.find('.product_discount_val').val(100);
                    iziToast.warning({
                        title: "Item discount cannot exceed 100%",
                        position: "topRight",
                    });
                }
                calculatedDiscountAmount = lineGross * (discVal / 100);
            } else {
                if (discVal > lineGross && lineGross > 0) {
                    discVal = lineGross;
                    row.find('.product_discount_val').val(lineGross.toFixed(2));
                    iziToast.warning({
                        title: "Item discount cannot exceed item total (" + lineGross.toFixed(2) + ")",
                        position: "topRight",
                    });
                }
                calculatedDiscountAmount = discVal;
            }

            let subTotal = Math.max(0, lineGross - calculatedDiscountAmount);
            row.find('.product_discount').val(calculatedDiscountAmount.toFixed(2));
            row.find('.sub_total').val(subTotal.toFixed(2));
            row.find('.sub_total_text').text(subTotal.toFixed(2));

            return subTotal;
        }

        function calculate_sub_total(main_qty, sub_qty, unit_price, related_by, has_sub_unit, discount = 0, discount_type = 'fixed') {
            var sub_unit_price = 0;
            if (has_sub_unit == "true" && related_by != 0) {
                sub_unit_price = parseFloat(unit_price / related_by);
            }
            var main_price = main_qty * unit_price;
            var sub_price = sub_qty * sub_unit_price;
            var lineGross = main_price + sub_price;
            
            var discountAmount = 0;
            if (discount_type === 'percent' || (typeof discount === 'string' && discount.includes("%"))) {
                let percent = parseFloat(discount.toString().replace('%', '')) || 0;
                discountAmount = lineGross * (percent / 100);
            } else {
                discountAmount = parseFloat(discount) || 0;
            }
            var subtotal = Math.max(0, lineGross - discountAmount);
            return parseFloat(subtotal).toFixed(2);
        }

        $(document).on('input keyup change', '.product_discount_val, .product_discount_type', function(e) {
            let row = $(this).closest('tr');
            update_row_discount_and_subtotal(row);
            estimatedAmount();
        });

        function parseStockQty(stockText, has_sub_unit, related_by = 1) {
            if (!stockText) return 0;

            // API object response
            if (typeof stockText === 'object' && stockText.available_stock !== undefined) {
                return parseFloat(stockText.available_stock) || 0;
            }

            if (typeof stockText === 'string') {
                let matches = stockText.match(/\d+(?:\.\d+)?/g);
                if (matches && matches.length >= 2) {
                    let main = parseFloat(matches[0]) || 0;
                    let sub = parseFloat(matches[1]) || 0;
                    if (has_sub_unit && related_by > 0) {
                        return main + (sub / related_by);
                    }
                    return main + sub;
                } else if (matches && matches.length === 1) {
                    return parseFloat(matches[0]) || 0;
                }
            }

            return parseFloat(stockText) || 0;
        }
        window.parseStockText = parseStockQty;

        function addProductToCard(data, weight = null, variation_code = null, scanned_imei = null, skipStockCheck = false) {
            // Check if product with the same variation already exists (Skip for IMEI products unless scanned_imei is passed)
            let existingRow = $();
            if (data.product.imei != 1) {
                existingRow = $("#tbody tr").filter(function() {
                    let rowVal = $(this).find("[name='variation_id[]']").val() || "";
                    let searchVal = variation_code || "";
                    return $(this).find("input[name='product_id[]']").val() == data.product.id && rowVal == searchVal;
                });
            } else {
                existingRow = $("#tbody tr").filter(function() {
                    return $(this).find("input[name='product_id[]']").val() == data.product.id;
                });
            }

            if (existingRow.length > 0) {
                if (data.product.imei == 1) {
                    let currentInput = existingRow.find('.imei_input');
                    let currentImeis = (currentInput.val() || '').split(/\r?\n/).map(x => x.trim()).filter(Boolean);
                    if (scanned_imei) {
                        if (currentImeis.includes(scanned_imei)) {
                            iziToast.warning({
                                title: "{{ __('IMEI already added') }}",
                                message: scanned_imei + " {{ __('is already in the cart.') }}",
                                position: "topRight",
                            });
                            return false;
                        }
                        currentImeis.push(scanned_imei);
                    } else {
                        iziToast.warning({
                            title: "{{ __('Duplicate Product') }}",
                            message: "{{ __('Please select IMEI(s) from the list.') }}",
                            position: "topRight",
                        });
                        return false;
                    }
                    
                    let newQty = currentImeis.length;
                    currentInput.val(currentImeis.join('\n'));
                    existingRow.find('.selected_imeis_display').text(currentImeis.join(', '));
                    
                    existingRow.find(".quantity-input").val(newQty);
                    existingRow.find(".main_qty").val(newQty);
                    existingRow.find(".sub_qty").val(0);
                    
                    update_row_discount_and_subtotal(existingRow);
                    estimatedAmount();

                    iziToast.success({
                        title: "{{ __('IMEI added to cart') }}",
                        message: scanned_imei,
                        position: "topRight",
                    });
                    return;
                }

                // Extract numeric stock value for checking
                let stockText = data.stock_qty;
                let has_sub_unit = (data.product.unit && data.product.unit.related_unit != null);
                let related_by = (data.product.unit && data.product.unit.related_value) ? data.product.unit.related_value : 1;
                let stockQty = parseStockQty(stockText, has_sub_unit, related_by);

                // Get variation stock if applicable
                let varStock = null;
                if (variation_code && data.variations) {
                    let selectedVar = data.variations.find(v => v.id == variation_code);
                    if (selectedVar) {
                        varStock = parseFloat(selectedVar.stock) || 0;
                    }
                }

                // ===== Existing product quantity increase =====
                let qtyInput = existingRow.find(".quantity-input"); // visible input box
                let mainQtyHidden = existingRow.find(".main_qty"); // hidden main_qty
                let subQtyHidden = existingRow.find(".sub_qty"); // hidden sub_qty

                let currentQty = parseFloat(mainQtyHidden.val()) || 1;
                let newQty = currentQty + 1;

                // stock check
                let maxAvailable = (varStock !== null) ? varStock : stockQty;
                if (data.product.is_service == 0 && newQty > maxAvailable) {
                    iziToast.error({
                        title: "{{ __('Out of Stock!') }}",
                        message: "{{ __('Available: ') }}" + maxAvailable,
                        position: "topRight",
                    });

                    // Show field validation error on stock label
                    qtyInput.css('border', '2px solid red');
                    existingRow.find('.stock-info-label').removeClass('text-muted').addClass('text-danger').css('font-weight', '600')
                        .html('⚠ Out of Stock! Available: ' + maxAvailable);
                    return false;
                }

                // visible input update
                qtyInput.val(newQty);

                // hidden main_qty update
                mainQtyHidden.val(newQty);

                // sub_qty always 0
                subQtyHidden.val(0);

                // ===== Subtotal Update =====
                update_row_discount_and_subtotal(existingRow);
                estimatedAmount();

                iziToast.success({
                    title: "{{ __('Quantity increased') }}",
                    position: "topRight",
                });
            } else {
                // Extract numeric stock value for checking
                let stockText = data.stock_qty;

                // check if product has sub unit
                let has_sub_unit = (data.product.unit && data.product.unit.related_unit != null);

                // conversion value (kg=1000gm / box=12pcs etc)
                let related_by = (data.product.unit && data.product.unit.related_value) ? data.product.unit.related_value : 1;

                // parse stock quantity
                let stockQty = parseStockQty(stockText, has_sub_unit, related_by);

                // stock check (skipped in Pre-Order edit mode)
                if (!skipStockCheck && data.product.is_service == 0 && stockQty <= 0) {
                    iziToast.error({
                        title: "{{ __('Out of Stock!') }}",
                        message: "{{ __('This product is out of stock. Please purchase more stock.') }}",
                        position: "topRight",
                    });
                    return false;
                }

                // ===== New product add =====
                let index = localData.length;
                localData.push({
                    product: data.product,
                    stock_qty: data.stock_qty,
                    variations: data.variations || [],
                    variation_code: variation_code // store variation_code
                });

                localStorage.setItem('pos-items', JSON.stringify(localData));

                // Adding a new row to DOM
                domPrepend(data, index, variation_code, skipStockCheck);

                // set quantity, subtotal etc...
                let tr = $("#tbody tr:first"); // Since it was prepended, I'm taking the first tr

                if (data.product.imei == 1 && scanned_imei) {
                    tr.find('.imei_input').val(scanned_imei);
                    tr.find('.selected_imeis_display').text(scanned_imei);
                }

                let main_qty = 1;
                let sub_qty = 0;

                if (weight !== null && weight !== '') {
                    let has_sub_unit = tr.find('.has_sub_unit').val();
                    let related_by = parseInt(tr.find('.quantity-input').attr('data-related')) || 1000;
                    let gram = parseFloat(weight);

                    if (gram < 10 && related_by == 1000) {
                        gram = gram / 1000;
                    }

                    if (has_sub_unit === "true" && related_by > 0) {
                        main_qty = gram / related_by;
                        sub_qty = 0;
                    } else {
                        main_qty = gram / related_by;
                        sub_qty = 0;
                    }
                }

                tr.find('.main_qty').val(main_qty);
                tr.find('.sub_qty').val(sub_qty);
                tr.find('.quantity-input').val(main_qty);

                update_row_discount_and_subtotal(tr);
                estimatedAmount();
            }
        }

        // Manage Addition and Removal from LocalStorage
        function pExist(pid) {
            let ldata = localStorage.getItem('pos-items') ? JSON.parse(localStorage.getItem('pos-items')) : [];
            return ldata.some(function(el) {
                return el.product.id === pid
            });
        }

        function storedata(data) {
            if (localStorage.getItem('pos-items') != null) {
                cartList = JSON.parse(localStorage.getItem('pos-items'))
                cartList.push(data);
            } else {
                cartList.push(data);
            }
            localStorage.setItem('pos-items', JSON.stringify(cartList));
        }

        var weight = null;
        $("#product_search_scale").autocomplete({
            source: function(req, res) {
                let barcode = req.term.substring(0, 6);
                weight = req.term.substring(6, 11);
                let url = "{{ route('product-search') }}";
                $.get(url, {
                    req: barcode
                }, (data) => {
                    res($.map(data, function(item) {
                        return {
                            id: item.id,
                            value: item.name + " " + item.barcode,
                            price: item.selling_price
                        }
                    })); // end res
                });
            },
            select: function(event, ui) {
                $(this).val(ui.item.value);

                $("#search_product_id").val(ui.item.id);
                let url = "{{ route('search-product-id', 'my_id') }}".replace('my_id', ui.item.id);
                $.get(url, (data) => {
                    // console.log(data);
                    let stockText = data.stock_qty;
                    let stockQty = 0;

                    if (typeof stockText === 'object' && stockText.available_stock) {
                        stockQty = parseFloat(stockText.available_stock) || 0;
                    } else if (typeof stockText === 'string') {
                        let kgMatch = stockText.match(/(\d+(?:\.\d+)?)\s*kg/i);
                        let gmMatch = stockText.match(/(\d+(?:\.\d+)?)\s*gm/i);
                        let kg = kgMatch ? parseFloat(kgMatch[1]) : 0;
                        let gm = gmMatch ? parseFloat(gmMatch[1]) : 0;
                        stockQty = kg + (gm / 1000);
                    } else {
                        stockQty = parseFloat(stockText) || 0;
                    }

                    if (data.variations && data.variations.length > 0) {
                        addProductToCard(data, weight);
                    } else if (pExist(data.product.id) == true) {
                        iziToast.warning({
                            title: "{{ __('Please Increase the quantity.') }}",
                            position: "topRight",
                        });
                    } else {
                        addProductToCard(data, weight);
                    }
                });
                $(this).val('');
                return false;
            },
            response: function(event, ui) {
                if (ui.content.length == 1) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');

                }
            },
            minLength: 0
        });

        var variation_code = '';
        var scanned_term = '';

        // Function to handle instant barcode scan or Enter key press on search input
        function handleDirectBarcodeScan(term, $input) {
            term = (term || '').trim();
            if (!term) return;

            // Close any open autocomplete dropdown
            if ($input && $input.data('ui-autocomplete')) {
                try { $input.autocomplete('close'); } catch(e) {}
            }
            if ($input && $input.length) {
                $input.val('');
            }

            // 1. Check local offline cache if offline or available
            let cachedProducts = [];
            try {
                cachedProducts = JSON.parse(localStorage.getItem('pos-offline-products') || '[]');
            } catch(e) {
                cachedProducts = [];
            }

            if (!navigator.onLine) {
                let exactLocal = cachedProducts.find(p => p.barcode && p.barcode.toString().trim().toLowerCase() === term.toLowerCase());
                if (exactLocal) {
                    let outStock = (exactLocal.stock_qty <= 0 && exactLocal.is_service == 0);
                    if (outStock) {
                        iziToast.error({
                            title: "{{ __('Out of Stock!') }}",
                            message: "{{ __('This product is out of stock.') }} (" + (exactLocal.name || '') + ")",
                            position: "topRight"
                        });
                        return;
                    }
                    addProductToCard({ product: exactLocal, stock_qty: exactLocal.stock_qty, variations: exactLocal.variations || [] });
                    return;
                } else {
                    iziToast.error({
                        title: "{{ __('Product Not Found') }}",
                        message: "{{ __('No product found for barcode: ') }}" + term,
                        position: "topRight"
                    });
                    return;
                }
            }

            // 2. Query server
            let url = "{{ route('sc-product-search') }}";
            $.get(url, { req: term }, function(data) {
                if (!data || data.length === 0) {
                    iziToast.error({
                        title: "{{ __('Product Not Found') }}",
                        message: "{{ __('No product found for barcode: ') }}" + term,
                        position: "topRight"
                    });
                    return;
                }

                // Match exact barcode first, then variation, then exact name, else fallback to first match
                let targetProduct = data.find(p => p.barcode && p.barcode.toString().trim().toLowerCase() === term.toLowerCase())
                                 || data.find(p => p.matched_variation_id)
                                 || data.find(p => p.name && p.name.toString().trim().toLowerCase() === term.toLowerCase())
                                 || data[0];

                let targetVariationId = targetProduct.matched_variation_id || null;

                // Quick stock check
                let quickStock = targetProduct.stock_qty !== undefined ? (parseFloat(targetProduct.stock_qty) || 0) : null;
                if (targetProduct.is_service == 0 && quickStock !== null && quickStock <= 0) {
                    iziToast.error({
                        title: "{{ __('Out of Stock!') }}",
                        message: "{{ __('This product is out of stock. Please purchase more stock.') }} (" + (targetProduct.name || '') + ")",
                        position: "topRight"
                    });
                    return;
                }

                // Fetch full details
                let detailsUrl = "{{ route('sc-search-product-id', 'my_id') }}".replace('my_id', targetProduct.id);
                $.get(detailsUrl, function(fullData) {
                    let stockText = fullData.stock_qty;
                    let has_sub_unit = (fullData.product.unit && fullData.product.unit.related_unit != null);
                    let related_by = (fullData.product.unit && fullData.product.unit.related_value) ? fullData.product.unit.related_value : 1;
                    let stockQty = parseStockQty(stockText, has_sub_unit, related_by);

                    if (fullData.product.is_service == 0 && stockQty <= 0) {
                        iziToast.error({
                            title: "{{ __('Out of Stock!') }}",
                            message: "{{ __('This product is out of stock. Please purchase more stock.') }}",
                            position: "topRight"
                        });
                        return;
                    }

                    // Check IMEI match if applicable
                    let matchedImei = null;
                    if (fullData.product.imei == 1 && fullData.imeis) {
                        let cleanTerm = term.toLowerCase();
                        let matched = fullData.imeis.find(function(i) {
                            let s = ((typeof i === 'object') ? i.serial : i).toString().trim().toLowerCase();
                            return s === cleanTerm;
                        }) || fullData.imeis.find(function(i) {
                            let s = ((typeof i === 'object') ? i.serial : i).toString().trim().toLowerCase();
                            return s.includes(cleanTerm);
                        });
                        if (matched) {
                            matchedImei = (typeof matched === 'object') ? matched.serial : matched;
                        }
                    }

                    addProductToCard(fullData, null, targetVariationId, matchedImei);

                    // Re-focus search input
                    if ($input && $input.length) {
                        $input.focus();
                    } else {
                        $('.product_search:visible').first().focus();
                    }
                }).fail(function() {
                    iziToast.error({
                        title: "{{ __('Error') }}",
                        message: "{{ __('Failed to load product details') }}",
                        position: "topRight"
                    });
                });
            }).fail(function() {
                iziToast.error({
                    title: "{{ __('Error') }}",
                    message: "{{ __('Network error during barcode search') }}",
                    position: "topRight"
                });
            });
        }
        window.handleDirectBarcodeScan = handleDirectBarcodeScan;

        // Enter key handler on search box (from scanner or keyboard)
        $(document).on('keydown keypress keyup', '.product_search, #product_search_scale', function(e) {
            if (e.keyCode === 13 || e.key === 'Enter' || e.which === 13) {
                e.preventDefault();
                e.stopPropagation();
                if (typeof e.stopImmediatePropagation === 'function') {
                    e.stopImmediatePropagation();
                }
                if (e.type === 'keydown') {
                    let term = $(this).val().trim();
                    if (term) {
                        handleDirectBarcodeScan(term, $(this));
                    }
                }
                return false;
            }
        });

        // Global barcode scanner listener (when focus is outside search box)
        let barcodeBuffer = '';
        let barcodeLastKeyTime = 0;
        $(document).on('keydown keypress', function(e) {
            // Ignore if modal or select2 is open
            if ($('.modal.show, .modal.in, .select2-container--open').length > 0) return;
            // Ignore if active in another input/textarea/select
            let target = $(e.target);
            if (target.is('input:not(.product_search), textarea, select') && !target.hasClass('product_search')) {
                return;
            }
            if (target.hasClass('product_search')) {
                // Handled directly by .product_search listener
                return;
            }

            let currentTime = new Date().getTime();
            if (e.key === 'Enter' || e.keyCode === 13 || e.which === 13) {
                if (barcodeBuffer.length >= 2 && (currentTime - barcodeLastKeyTime < 300)) {
                    e.preventDefault();
                    e.stopPropagation();
                    let scannedCode = barcodeBuffer.trim();
                    barcodeBuffer = '';
                    handleDirectBarcodeScan(scannedCode, $('.product_search:visible').first());
                    return false;
                }
                barcodeBuffer = '';
            } else if (e.type === 'keydown' && e.key && e.key.length === 1) {
                if (currentTime - barcodeLastKeyTime > 150) {
                    barcodeBuffer = '';
                }
                barcodeBuffer += e.key;
                barcodeLastKeyTime = currentTime;
            }
        });

        // Autocomplete search for manual typing
        $(".product_search").autocomplete({
            source: function(req, res) {
                let rawTerm = req.term || '';
                let term = rawTerm.trim();
                if (!term) {
                    res([]);
                    return;
                }
                scanned_term = term;
                variation_code = '';

                // 🚀 Layer 1: Instant In-Memory Filter (0-1ms)
                let termLower = term.toLowerCase();
                let cachedProducts = [];
                try {
                    cachedProducts = JSON.parse(localStorage.getItem('pos-offline-products') || '[]');
                } catch(e) {
                    cachedProducts = [];
                }

                let localMatches = [];
                if (cachedProducts.length > 0) {
                    localMatches = cachedProducts.filter(p => {
                        let nameMatch = p.name && p.name.toLowerCase().includes(termLower);
                        let barcodeMatch = p.barcode && p.barcode.toString().toLowerCase().includes(termLower);
                        return nameMatch || barcodeMatch;
                    });
                }

                // If exact single barcode match or exact name match locally, return immediately
                if (localMatches.length === 1 && (localMatches[0].barcode === term || (localMatches[0].name && localMatches[0].name.toLowerCase() === termLower))) {
                    let p = localMatches[0];
                    let outOfStock = (p.stock_qty <= 0 && p.is_service == 0) ? " (Out of Stock)" : "";
                    res([{
                        id: p.id,
                        label: p.name + " (" + p.selling_price +
                            " {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}) - " +
                            (p.barcode || '') + outOfStock,
                        value: p.name + " " + (p.barcode || '') + outOfStock,
                        price: p.selling_price,
                        stock_qty: p.stock_qty,
                        barcode: p.barcode || '',
                        offline_data: { product: p, stock_qty: p.stock_qty, variations: p.variations || [] }
                    }]);
                    return;
                }

                if (!navigator.onLine) {
                    if (localMatches.length > 0) {
                        let formatted = localMatches.map(p => {
                            let outOfStock = (p.stock_qty <= 0 && p.is_service == 0) ? " (Out of Stock)" : "";
                            return {
                                id: p.id,
                                label: p.name + " (" + p.selling_price +
                                    " {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}) - " +
                                    (p.barcode || '') + outOfStock,
                                value: p.name + " " + (p.barcode || '') + outOfStock,
                                price: p.selling_price,
                                stock_qty: p.stock_qty,
                                barcode: p.barcode || '',
                                offline_data: { product: p, stock_qty: p.stock_qty, variations: p.variations || [] }
                            };
                        });
                        res(formatted);
                    } else {
                        res([]);
                    }
                    return;
                }

                // 🚀 Layer 2: Server Search
                let url = "{{ route('sc-product-search') }}";
                $.get(url, { req: term }, function(data) {
                    if (data && data.length === 1 && data[0].matched_variation_id) {
                        variation_code = data[0].matched_variation_id;
                    }
                    if (data && data.length > 0) {
                        res($.map(data, function(item) {
                            let outOfStock = item.stock_qty <= 0 ? " (Out of Stock)" : "";
                            return {
                                id: item.id,
                                label: item.name + " (" + item.selling_price +
                                    " {{ empty(get_setting('com_currency')) ?: get_setting('com_currency') }}) - " +
                                    item.barcode + outOfStock,
                                value: item.name + " " + item.barcode + outOfStock,
                                price: item.selling_price,
                                stock_qty: item.stock_qty,
                                barcode: item.barcode || '',
                                matched_variation_id: item.matched_variation_id || null
                            };
                        }));
                    } else {
                        res([]);
                    }
                }).fail(function() {
                    res([]);
                });
            },
            select: function(event, ui) {
                let $input = $(this);
                $input.val(ui.item.value);
                $("#search_product_id").val(ui.item.id);

                // Use matched_variation_id from the autocomplete item if available
                if (ui.item.matched_variation_id) {
                    variation_code = ui.item.matched_variation_id;
                }

                let quickStock = ui.item.stock_qty !== undefined ? (parseFloat(ui.item.stock_qty) || 0) : null;
                if (quickStock !== null && quickStock <= 0) {
                    iziToast.error({
                        title: "{{ __('Out of Stock!') }}",
                        message: "{{ __('This product is out of stock. Please purchase more stock.') }}",
                        position: "topRight",
                    });
                    $input.val('');
                    return false;
                }

                if (!navigator.onLine && ui.item.offline_data) {
                    addProductToCard(ui.item.offline_data);
                    $input.val('');
                    return false;
                }

                let url = "{{ route('sc-search-product-id', 'my_id') }}".replace('my_id', ui.item.id);
                $.get(url, function(data) {
                    let stockText = data.stock_qty;

                    // check if product has sub unit
                    let has_sub_unit = (data.product.unit && data.product.unit.related_unit != null);

                    // conversion value (kg=1000gm / box=12pcs etc)
                    let related_by = (data.product.unit && data.product.unit.related_value) ? data.product.unit.related_value : 1;

                    // parse stock quantity
                    let stockQty = parseStockQty(stockText, has_sub_unit, related_by);

                    // stock check
                    if (data.product.is_service == 0 && stockQty <= 0) {
                        iziToast.error({
                            title: "{{ __('Out of Stock!') }}",
                            message: "{{ __('This product is out of stock. Please purchase more stock.') }}",
                            position: "topRight",
                        });
                        $input.val('');
                        return false;
                    }

                    // Check if the scanned term matches any of the available IMEIs of this product
                    let matchedImei = null;
                    if (data.product.imei == 1 && data.imeis && scanned_term) {
                        let cleanTerm = scanned_term.trim().toLowerCase();
                        let matched = data.imeis.find(function(i) {
                            let s = ((typeof i === 'object') ? i.serial : i).toString().trim().toLowerCase();
                            return s === cleanTerm;
                        }) || data.imeis.find(function(i) {
                            let s = ((typeof i === 'object') ? i.serial : i).toString().trim().toLowerCase();
                            return s.includes(cleanTerm);
                        });
                        if (matched) {
                            matchedImei = (typeof matched === 'object') ? matched.serial : matched;
                        }
                    }

                    // Pass the variation_code (which may be matched_variation_id from backend) and matchedImei
                    addProductToCard(data, null, variation_code, matchedImei);
                    if ($input && $input.length) {
                        $input.focus();
                    }
                });

                $input.val('');
                return false;
            },
            response: function(event, ui) {
                if (!ui.content || ui.content.length === 0) return;

                if (ui.content.length === 1) {
                    ui.item = ui.content[0];
                    $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                    $(this).autocomplete('close');
                    return;
                }

                if (scanned_term) {
                    let exactMatch = ui.content.find(item => item.barcode && item.barcode.toString().trim().toLowerCase() === scanned_term.trim().toLowerCase());
                    if (exactMatch) {
                        ui.item = exactMatch;
                        $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                        $(this).autocomplete('close');
                        return;
                    }
                }
            },
            minLength: 1,
            delay: 150
        });

        $(document).on('click', '.remove-btn', function() {
            let itemIndex = $(this).attr('data-value');
            localData.splice(itemIndex, 1);
            localStorage.removeItem('pos-items');
            localStorage.setItem('pos-items', JSON.stringify(localData))
            $(this).parents('tr').remove();
            estimatedAmount();
        });

        // Plus/Minus Quantity Button Click Handlers
        $(document).on('click', '.qty-plus-btn', function(e) {
            e.preventDefault();
            let row = $(this).closest('tr');
            let inputEl = row.find('.quantity-input');
            let currentVal = parseFloat(inputEl.val()) || 0;
            let newVal = currentVal + 1;
            inputEl.val(newVal).trigger('change');
        });

        $(document).on('click', '.qty-minus-btn', function(e) {
            e.preventDefault();
            let row = $(this).closest('tr');
            let inputEl = row.find('.quantity-input');
            let currentVal = parseFloat(inputEl.val()) || 0;
            let newVal = currentVal - 1;
            if (newVal < 0) newVal = 0;
            inputEl.val(newVal).trigger('change');
        });

        // Manage Cart items
        $(document).on('click', '.product', function() {
            let productId = $(this).attr('data-value');
            let url = "{{ route('sc-pos-product-id', 'my_id') }}".replace('my_id', productId);
            $.get(url, data => {
                try {
                    let stockText = data.stock_qty;

                    // check if product has sub unit
                    let has_sub_unit = (data.product.unit && data.product.unit.related_unit != null) ? true : false;

                    // conversion value (kg=1000gm / box=12pcs etc)
                    let related_by = (data.product.unit && data.product.unit.related_value) ? data.product.unit.related_value : 1;

                    // parse stock quantity
                    let stockQty = parseStockQty(stockText, has_sub_unit, related_by);

                    // stock check
                    if (data.product.is_service == 0 && stockQty <= 0) {
                        iziToast.error({
                            title: "{{ __('Out of Stock!') }}",
                            message: "{{ __('This product is out of stock. Please purchase more stock.') }} (" + stockText + ")",
                            position: "topRight",
                        });
                        return false;
                    }
                    addProductToCard(data);
                } catch (err) {
                    console.error("Click handler callback error:", err);
                    iziToast.error({
                        title: "JS Error",
                        message: "Error processing product click: " + err.message,
                        position: "topRight"
                    });
                }
            }).fail((xhr, status, error) => {
                console.error("AJAX request failed:", xhr.responseText);
                iziToast.error({
                    title: "Server Error",
                    message: "Failed to load product details: " + error,
                    position: "topRight"
                });
            }); // Load Data to cart
        });

        function clearCart() {
            localStorage.removeItem('pos-items');
            localData = [];
            window.isFullDueManual = false;
            $("#tbody").html('');
            estimatedAmount();
        }

        $("#clearList").on('click', function() {
            clearCart();
        });

        function domPrepend(data = null, index = null, variation_code = null, skipStockCheck = false) {
            var name = data.product.name;
            var quantity_data = '';
            var variation_data = ``;
            let main_qty_val = 1; // Default to 1 for new products
            let sub_qty_val = 0;
            let quantity_readonly = data.product.imei == 1 ? 'readonly' : '';

            // Format stock_qty for display
            let displayStock = '';
            if (typeof data.stock_qty === 'object' && data.stock_qty.available_stock) {
                displayStock = data.stock_qty.available_stock;
            } else {
                displayStock = data.stock_qty;
            }

            if (data.variations && data.variations.length > 0) {
                variation_data += `<input type="text" class="has_size" data-has-size="true" hidden>
                <select name="variation_id[]" class="form-control size" required>
                <option value="">{{ __('Select Variation') }}</option>`;

                $.each(data.variations, function(idx, value) {
                    let isOutOfStock = parseFloat(value.stock) <= 0;
                    let isMatch = variation_code && (String(variation_code) === String(value.id));
                    let selected = isMatch ? "selected" : "";
                    let disableAttr = (isOutOfStock && !isMatch && !skipStockCheck) ? 'disabled' : '';
                    variation_data += `<option stock='${value.stock}' value='${value.id}' ${selected} ${disableAttr}>
                    ${value.size || ''} - ${value.color || ''} - ${value.stock}${isOutOfStock ? ' (Out of Stock)' : ''}</option>`;
                });

                variation_data += '</select>';
            } else {
                variation_data = `<input type="text" class="has_size" data-has-size="false" hidden>
                <input type="hidden" name="variation_id[]">
                `;
            }

            let imei_data = '';
            if (data.product.imei == 1) {
                imei_data = `
                    <button type="button" class="btn btn-sm btn-info select_imei_btn" 
                        data-product-id="${data.product.id}">
                        {{ __('Select IMEI(s)') }}
                    </button>
                    <div class="selected_imeis_display mt-1" style="font-size: 11px; color: #555;"></div>
                    <input type="hidden" name="imei[]" class="imei_input" required>
                `;
            } else {
                imei_data = `<input type="hidden" name="imei[]" value="">`;
            }

            let warranty_data = '';
            if (env_warranty == 'yes') {
                let w_val = data.warranty_value || data.product.warranty_value || '';
                let w_unit = data.warranty_unit || data.product.warranty_unit || 'Month';
                let w_text = w_val ? `${w_val} ${w_unit}` : '<span class="text-muted">-</span>';
                warranty_data = `
                    <div class="d-flex align-items-center justify-content-center" style="min-width: 100px;">
                        <span class="badge badge-info px-2 py-1 font-weight-bold warranty-display-badge" style="font-size: 13px;">${w_text}</span>
                        <input type="hidden" name="warranty_value[]" class="warranty_value_input" value="${w_val}">
                        <input type="hidden" name="warranty_unit[]" class="warranty_unit_input" value="${w_unit}">
                    </div>
                `;
            } else {
                warranty_data = `
                    <input type="hidden" name="warranty_value[]" value="">
                    <input type="hidden" name="warranty_unit[]" value="">
                `;
            }

            let initialSubtotal = calculate_sub_total(
                1,
                0,
                data.product.selling_price || 0,
                (data.product.unit && data.product.unit.related_value) ? data.product.unit.related_value : 1,
                (data.product.unit && data.product.unit.related_unit != null) ? "true" : "false"
            );

            if (data.product.is_service == 0) {
                if (typeof weight !== "undefined" && weight !== null && weight !== "" && !isNaN(weight)) {
                    let gram = parseInt(weight, 10); // Always comes in grams
                    if (data.product.unit.related_unit != null) {
                        // If there is a related unit (e.g., kg = 1000 gm)
                        let related_by = parseInt(data.product.unit.related_value) || 1000;
                        main_qty_val = (gram / related_by).toFixed(3); // convert to decimal
                    } else {
                        // Only main unit
                        main_qty_val = gram;
                    }
                }
                if (!data.product.unit || data.product.unit.related_unit == null) {
                    quantity_data =
                        `
                            <input type="text" class="has_sub_unit" hidden value="false">
                            <label class="ml-2 mr-2" style="padding-top: 5px;">${(data.product.unit ? data.product.unit.name : 'pcs')}:</label>
                            <input type="text" class="form-control quantity-input" value="${main_qty_val}" 
                                placeholder="{{ __('e.g., 5 kg, 500 gm, 5.5') }}" 
                                data-related="${(data.product.unit ? data.product.unit.related_value : 1) || 1}" 
                                data-stock="${displayStock}" ${quantity_readonly}>
                            <input type="hidden" class="main_qty" name="main_qty[]" value="1">
                            <input type="hidden" class="sub_qty" name="sub_qty[]" value="0">`;
                } else {
                    quantity_data =
                        `<input type="text" class="has_sub_unit" hidden value="true">
                                <input type="text" class="conversion" hidden value="${data.product.unit.related_value}">
                                <label class="mr-4 ml-1" style="padding-top: 5px;">${data.product.unit.name}:</label>
                                <input type="text" class="form-control quantity-input" value="${main_qty_val}" 
                                    placeholder="{{ __('e.g., 5 kg, 500 gm, 5.5') }}" 
                                    data-related="${data.product.unit.related_value}" 
                                    data-stock="${displayStock}" ${quantity_readonly}>
                                <input type="hidden" class="main_qty" name="main_qty[]" value="1">
                                <input type="hidden" class="sub_qty" name="sub_qty[]" value="0">`;
                }
            } else {
                quantity_data =
                    `
                        <label class="ml-2 mr-2" style="padding-top: 5px;">pcs:</label>
                        <input type="number" value="1" class="form-control col main_qty" 
                            name="main_qty[]" onkeydown="return event.keyCode !== 190" min="0">`;
            }

            let dom = `
                    <tr id="tbody_tr" class="item-row" data-is-service="${data.product.is_service || 0}" data-product-id="${data.product.id}">
                        <td class="table_data_style_left text-left" style="width: 30%; min-width: 130px;">
                            <span class="font-weight-bold">${data.product.name}</span>
                            ${data.product.is_service == 1 ? `<div class="text-info small mt-1 fw-bold">Cost: ${data.product.purchase_price || '0.00'} | Sale: ${data.product.selling_price || '0.00'}</div>` : ''}
                            <div class="mt-1">${variation_data}</div>
                            <div class="product-description-container mt-1 d-flex align-items-center">
                                <span class="product-desc-text text-muted small" style="max-width: 180px; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${data.product.description || ''}</span>
                                <a href="javascript:void(0)" class="edit-description-btn ml-2" data-id="${data.product.id}" data-name="${data.product.name.replace(/"/g, '&quot;')}" style="cursor: pointer;">
                                    <i class="fa fa-edit text-primary" style="font-size: 13px;"></i>
                                </a>
                            </div>
                            <input type="hidden" class="name" 
                                value="${name.replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, '')}" name="name[]" />
                            <input type="hidden" value="${data.product.id}" name="product_id[]" />
                        </td>
                        <td style="width: 12%; min-width: 80px;">
                            <input type="text" style="width: 100%;" 
                                class="form-control rate" 
                                name="rate[]" value="${data.product.selling_price || 0}" placeholder="Rate" />
                            @if (env('SHOW_COST_RATE_IN_POS') == 'yes')
                                <div class="text-danger small mt-1 font-weight-bold text-center cost-rate-label" style="font-size: 11px;">
                                    Cost: ${parseFloat(data.product.purchase_price || 0).toFixed(2)}
                                </div>
                            @endif
                        </td>
                        @if (env('APP_IMEI') == 'yes')
                            <td style="min-width: 150px;">
                                ${imei_data}
                            </td>
                        @endif
                        @if (env('APP_WARRANTY') == 'yes')
                            <td style="min-width: 130px;">
                                ${warranty_data}
                            </td>
                        @endif
                        <td style="width: 18%; min-width: 160px;">
                            <input type="hidden" class="has_sub_unit" value="${(data.product.unit && data.product.unit.related_unit != null) ? 'true' : 'false'}">
                            <div class="input-group flex-nowrap" style="width: 100%;">
                                <div class="input-group-prepend">
                                    <button type="button" class="btn btn-sm qty-minus-btn px-2" ${data.product.imei == 1 ? 'disabled' : ''} style="border-radius: 4px 0 0 4px; height: 31px; display: flex; align-items: center; justify-content: center; z-index: 3;">
                                        <i class="fa fa-minus" style="font-size: 8px;"></i>
                                    </button>
                                </div>
                                <input type="text" style="width: 100%; height: 31px; text-align: center;"
                                    class="form-control quantity-input px-1" 
                                    data-related="${(data.product.unit && data.product.unit.related_value) ? data.product.unit.related_value : 1}" 
                                    data-stock="${displayStock}" ${quantity_readonly}
                                    value="${main_qty_val}" placeholder="Qty" />
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-sm qty-plus-btn px-2" ${data.product.imei == 1 ? 'disabled' : ''} style="border-radius: 0; height: 31px; display: flex; align-items: center; justify-content: center; z-index: 3;">
                                        <i class="fa fa-plus" style="font-size: 8px;"></i>
                                    </button>
                                    <span class="input-group-text p-1" style="font-size: 10px; border-radius: 0 4px 4px 0; display: flex; align-items: center; justify-content: center; height: 31px;">${(data.product.unit ? data.product.unit.name : 'pcs')}</span>
                                </div>
                            </div>
                            <small class="stock-info-label text-muted d-block mt-1" style="font-size:10px; ${data.product.is_service == 1 ? 'display: none !important;' : ''}">📦 Stock: ${displayStock || '0'}</small>
                            <input type="hidden" class="main_qty" name="main_qty[]" value="${Math.floor(main_qty_val)}">
                            <input type="hidden" class="sub_qty" name="sub_qty[]" value="${sub_qty_val}">
                        </td>
                        <td style="width: 13%; min-width: 110px;">
                            <div class="input-group input-group-sm d-flex flex-nowrap align-items-center" style="width: 100%;">
                                <input type="number" step="any" min="0" style="width: 58%; min-width: 45px; text-align: center; border-top-right-radius: 0; border-bottom-right-radius: 0; padding: 2px 4px; height: 31px;" 
                                    class="form-control product_discount_val" 
                                    name="product_discount_val[]"
                                    value="0" placeholder="0" />
                                <select class="form-control product_discount_type" name="product_discount_type[]" style="width: 42%; max-width: 52px; min-width: 44px; padding: 0 2px; border-top-left-radius: 0; border-bottom-left-radius: 0; border-left: 0; height: 31px; font-size: 11px; font-weight: 600; cursor: pointer; text-align-last: center; background: #f8f9fa;">
                                    <option value="fixed">{{ empty(get_setting('com_currency')) ? 'Tk' : get_setting('com_currency') }}</option>
                                    <option value="percent">%</option>
                                </select>
                                <input type="hidden" class="product_discount" name="product_discount[]" value="0" />
                            </div>
                        </td>
                        <td style="width: 12%; min-width: 85px;">
                            <input type="text" style="width: 100%;" readonly 
                                name="sub_total[]" class="form-control sub_total" 
                                value="${initialSubtotal}"/>
                        </td>
                        <td class="table_data_style_right" style="width: 3%; min-width: 40px;">
                            <a href="#" class="remove-btn item-index text-danger" data-value="${index}">
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    `;

            $("#tbody").prepend(dom);
        }

        function parseStockText(stockText, has_sub_unit, related_by = 1) {

            if (!stockText) return 0;

            // PC / Piece product
            if (!has_sub_unit || has_sub_unit === "false") {
                return parseFloat(stockText) || 0;
            }

            // API object response
            if (typeof stockText === 'object' && stockText.available_stock !== undefined) {
                return parseFloat(stockText.available_stock) || 0;
            }

            // If string like "2 unit 5 sub"
            if (typeof stockText === 'string') {

                let numbers = stockText.match(/\d+(\.\d+)?/g);

                let main = numbers && numbers[0] ? parseFloat(numbers[0]) : 0;
                let sub = numbers && numbers[1] ? parseFloat(numbers[1]) : 0;

                return main + (sub / related_by);
            }

            return parseFloat(stockText) || 0;
        }

        // $(document).on('keyup change', '.quantity-input', function(e) {
        //     let row = $(this).closest('tr');

        //     let input = $(this).val();
        //     let related_by = parseInt($(this).attr('data-related')) || 1;
        //     let has_sub_unit = row.find('.has_sub_unit').val();
        //     let stockText = $(this).attr('data-stock');

        //     // parse stock
        //     let stock_qty = parseStockText(stockText, has_sub_unit, related_by);
        //     let stock = stock_qty * related_by;

        //     // parse input quantity
        //     let quantities = parseQuantityInput(input, related_by);
        //     let total_quantity = to_sub_unit(
        //         quantities.main_qty,
        //         quantities.sub_qty,
        //         related_by,
        //         has_sub_unit
        //     );

        //     if (stock < total_quantity) {
        //         iziToast.warning({
        //             title: "Not Enough Stock.",
        //             position: "topRight",
        //         });

        //         // Set input to max available stock
        //         let converted = convert_to_main_and_sub(stock, has_sub_unit, related_by);
        //         quantities.main_qty = converted.main_qty;
        //         quantities.sub_qty = converted.sub_qty;

        //         $(this).val(
        //             quantities.main_qty +
        //             (quantities.sub_qty > 0 ?
        //                 '.' + quantities.sub_qty.toString().padStart(3, '0').substring(0, 3) :
        //                 '')
        //         );
        //     }

        //     // Update hidden inputs
        //     row.find('.main_qty').val(quantities.main_qty);
        //     row.find('.sub_qty').val(quantities.sub_qty);

        //     // Call handle_change to recalc subtotal
        //     handle_change($(this));
        // });
        $(document).on('keyup change input', '.quantity-input', function(e) {
            let row = $(this).closest('tr');
            let isService = row.attr('data-is-service') === '1' || row.data('is-service') == 1;

            if (isService) {
                let rawInput = $(this).val();
                let cleaned = rawInput.replace(/[^0-9]/g, '');
                if (cleaned !== rawInput) {
                    $(this).val(cleaned);
                }
                $(this).css('border', '');
                row.find('.stock-info-label').removeClass('text-danger').addClass('text-muted').css('font-weight', 'normal').html('');
                let val = parseFloat(cleaned) || 0;
                row.find('.main_qty').val(val);
                row.find('.sub_qty').val(0);
                handle_change($(this));
                return;
            }

            let rawInput = $(this).val();
            let has_sub_unit = row.find('.has_sub_unit').val();

            // ✅ Clean/validate input based on unit type
            let cleaned;
            let validationMsg = '';
            if (has_sub_unit === 'true') {
                cleaned = rawInput.replace(/[^0-9.]/g, '');
                // Prevent multiple dots
                let dotCount = (cleaned.match(/\./g) || []).length;
                if (dotCount > 1) {
                    cleaned = cleaned.substring(0, cleaned.lastIndexOf('.'));
                }
                if (cleaned !== rawInput) {
                    validationMsg = '⚠ Only numbers allowed!';
                }
            } else {
                // For piece products, truncate at the first dot if typed, then keep only digits
                let base = rawInput;
                if (rawInput.includes('.')) {
                    base = rawInput.split('.')[0];
                }
                cleaned = base.replace(/[^0-9]/g, '');
                if (cleaned !== rawInput) {
                    validationMsg = '⚠ Only whole numbers allowed!';
                }
            }

            if (cleaned !== rawInput) {
                $(this).val(cleaned);
                $(this).css('border', '2px solid red');
                row.find('.stock-info-label').removeClass('text-muted').addClass('text-danger').css('font-weight', '600')
                    .html(validationMsg);
                
                // Recalculate subtotal for the cleaned value
                let input = cleaned;
                let related_by = parseInt($(this).attr('data-related')) || 1;
                let quantities = parseQuantityInput(input, related_by);
                row.find('.main_qty').val(quantities.main_qty);
                row.find('.sub_qty').val(quantities.sub_qty);
                handle_change($(this));
                return;
            }

            let input = cleaned;
            let related_by = parseInt($(this).attr('data-related')) || 1;

            // ✅ Get stock from selected variation if exists
            let variation_select = row.find('select[name="variation_id[]"]');
            let stock = 0;

            if (variation_select.length > 0) {
                let selectedOption = variation_select.find('option:selected');
                let variationStock = parseFloat(selectedOption.attr('stock')) || 0;
                stock = has_sub_unit === "true" ? variationStock * related_by : variationStock;
            } else {
                // fallback to product stock
                let stockText = $(this).attr('data-stock');
                let stock_qty = parseStockText(stockText, has_sub_unit, related_by);
                stock = stock_qty * related_by;
            }

            // parse input quantity
            let quantities = parseQuantityInput(input, related_by);
            let total_quantity = to_sub_unit(
                quantities.main_qty,
                quantities.sub_qty,
                related_by,
                has_sub_unit
            );



            if (stock < total_quantity) {
                // Convert available stock to display format
                let availDisplay = (has_sub_unit === 'true') ? (stock / related_by).toFixed(3) : stock;

                // Show field validation error on stock label
                $(this).css('border', '2px solid red');
                row.find('.stock-info-label').removeClass('text-muted').addClass('text-danger').css('font-weight', '600')
                    .html('⚠ Not enough! Stock: ' + ($(this).attr('data-stock') || '0'));

                // Set input to max available stock
                let converted = convert_to_main_and_sub(stock, has_sub_unit, related_by);
                quantities.main_qty = converted.main_qty;
                quantities.sub_qty = converted.sub_qty;

                // Correct input value instantly
                $(this).val(availDisplay);

                // Show error message
                if (typeof window.toastMagic !== 'undefined') {
                    window.toastMagic.error("{{ __('Not Enough Stock!') }} (" + "{{ __('Available: ') }}" + availDisplay + ")");
                } else if (typeof iziToast !== 'undefined') {
                    iziToast.error({
                        title: "{{ __('Not Enough Stock!') }}",
                        message: "Available: " + availDisplay,
                        position: "topRight",
                    });
                }
            } else {
                // Valid quantity — clear error styling, restore stock info
                $(this).css('border', '');
                row.find('.stock-info-label').removeClass('text-danger').addClass('text-muted').css('font-weight', 'normal')
                    .html('📦 Stock: ' + ($(this).attr('data-stock') || '0'));
            }

            // Update hidden inputs
            row.find('.main_qty').val(quantities.main_qty);
            row.find('.sub_qty').val(quantities.sub_qty);

            // Recalc subtotal
            handle_change($(this));
        });

        // ========================================
        // ✅ Live Variation Select Change Handler (Strict Stock Check)
        // ========================================
        $(document).on('change', 'select[name="variation_id[]"]', function() {
            let row = $(this).closest('tr');
            let selectedOption = $(this).find('option:selected');
            let variationStock = parseFloat(selectedOption.attr('stock'));
            let stockLabel = row.find('.stock-info-label');
            let qtyInput = row.find('.quantity-input');

            if (isNaN(variationStock)) {
                return;
            }

            let isImeiProduct = row.find('.select_imei_btn').length > 0;

            if (variationStock <= 0 && !isImeiProduct) {
                $(this).css('border', '2px solid red');
                stockLabel.removeClass('text-muted').addClass('text-danger').css('font-weight', '600')
                    .html('⚠ Stock Out! Available: 0');
                qtyInput.val(0).css('border', '2px solid red');
                row.find('.main_qty').val(0);
                row.find('.sub_qty').val(0);
                handle_change(qtyInput);
                iziToast.error({
                    title: "{{ __('Stock Out!') }}",
                    message: "{{ __('Selected variation is out of stock.') }}",
                    position: "topRight"
                });
            } else {
                $(this).css('border', '');
                qtyInput.attr('data-stock', variationStock);
                stockLabel.removeClass('text-danger').addClass('text-muted').css('font-weight', 'normal')
                    .html('📦 Stock: ' + variationStock);
                // Trigger quantity input re-validation if not 0
                if (parseFloat(qtyInput.val()) > 0) {
                    qtyInput.trigger('input');
                }
            }
        });

        // ========================================
        // ✅ Invoice Form Submit Strict Stock Validation
        // ========================================
        $(document).on('submit', '#sc_invoice_form, form[action*="invoice"]', function(e) {
            if (!window.isExplicitSubmitAllowed) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
            let hasError = false;
            let errorMsg = '';

            $("#tbody tr").each(function() {
                let row = $(this);
                let isService = row.attr('data-is-service') === '1' || row.data('is-service') == 1;
                if (isService) return;

                let name = row.find('.name').val() || 'Product';
                let varSelect = row.find('select[name="variation_id[]"]');
                
                if (varSelect.length > 0) {
                    let varId = varSelect.val();
                    let selectedOpt = varSelect.find('option:selected');
                    
                    if (!varId) {
                        hasError = true;
                        errorMsg = "{{ __('Please select a variation for product:') }} " + name;
                        varSelect.css('border', '2px solid red').focus();
                        return false;
                    }
                    
                    let varStock = parseFloat(selectedOpt.attr('stock')) || 0;
                    let mainQty = parseFloat(row.find('.main_qty').val()) || 0;
                    let subQty = parseFloat(row.find('.sub_qty').val()) || 0;
                    let reqQty = mainQty + subQty;
                    
                    if (varStock <= 0 || reqQty > varStock) {
                        hasError = true;
                        errorMsg = "{{ __('Stock Out Error: Selected variation for ') }}" + name + " {{ __('is out of stock!') }} (" + "{{ __('Available: ') }}" + varStock + ")";
                        varSelect.css('border', '2px solid red');
                        row.find('.quantity-input').css('border', '2px solid red').focus();
                        return false;
                    }
                } else {
                    let qtyInput = row.find('.quantity-input');
                    let has_sub_unit = row.find('.has_sub_unit').val() === 'true' || parseFloat(qtyInput.attr('data-related')) > 1;
                    let related_by = parseFloat(qtyInput.attr('data-related')) || 1;
                    let stockText = qtyInput.attr('data-stock');
                    
                    let stockQtyInMain = parseStockQty(stockText, has_sub_unit, related_by);
                    let mainQty = parseFloat(row.find('.main_qty').val()) || 0;
                    let subQty = parseFloat(row.find('.sub_qty').val()) || 0;
                    let reqQtyInMain = (has_sub_unit && related_by > 0) ? (mainQty + (subQty / related_by)) : (mainQty + subQty);
                    
                    if (stockQtyInMain <= 0 || reqQtyInMain > (stockQtyInMain + 0.0001)) {
                        hasError = true;
                        errorMsg = "{{ __('Stock Out Error: Product ') }}" + name + " {{ __('is out of stock!') }} (" + "{{ __('Available: ') }}" + (stockText || stockQtyInMain) + ")";
                        qtyInput.css('border', '2px solid red').focus();
                        return false;
                    }
                }
            });

            if (hasError) {
                window.isExplicitSubmitAllowed = false;
                e.preventDefault();
                e.stopPropagation();
                iziToast.error({
                    title: "{{ __('Out of Stock!') }}",
                    message: errorMsg,
                    position: "topRight"
                });
                return false;
            }
        });

        // ========================================
        // ✅ Numeric-only validation for all monetary/numeric fields
        // ========================================
        $(document).on('input keyup', '.rate, .product_discount, .product_discount_val, .discount_amount, .discount_val_input, .vat, .delivery_charge, .pay_amount', function() {
            let rawVal = $(this).val();
            // Allow only numbers, dot, and % (for discount percentage)
            let cleaned = rawVal.replace(/[^0-9.%]/g, '');
            // Prevent multiple dots
            let parts = cleaned.split('.');
            if (parts.length > 2) {
                cleaned = parts[0] + '.' + parts.slice(1).join('');
            }
            if (cleaned !== rawVal) {
                $(this).val(cleaned);
                $(this).css('border', '2px solid red');
                // Show inline error
                let parentCol = $(this).closest('.checkout-col, td');
                parentCol.find('.numeric-error-msg').remove();
                parentCol.append('<small class="numeric-error-msg text-danger d-block" style="font-size:10px; font-weight:600;">⚠ Numbers only!</small>');
            } else {
                $(this).css('border', '');
                let parentCol = $(this).closest('.checkout-col, td');
                parentCol.find('.numeric-error-msg').remove();
            }
        });

        // function handle_change(obj) {
        //     let row = obj.closest('tr');

        //     let main_val = parseInt(empty_field_check(row.find('.main_qty').val())) || 0;
        //     let sub_val = parseInt(empty_field_check(row.find('.sub_qty').val())) || 0;

        //     let related_by = parseInt(row.find('.quantity-input').attr('data-related')) || 1;
        //     let has_sub_unit = row.find('.has_sub_unit').val();

        //     // stock calculate
        //     let stockText = row.find('.quantity-input').attr('data-stock');
        //     let stock_qty = parseStockText(stockText, has_sub_unit, related_by);
        //     let stock = stock_qty * related_by;

        //     let converted_sub = to_sub_unit(main_val, sub_val, related_by, has_sub_unit);

        //     if (stock < converted_sub) {
        //         iziToast.warning({
        //             title: "Not Enough Stock.",
        //             position: "topRight",
        //         });

        //         // Set max available qty
        //         let converted = convert_to_main_and_sub(stock, has_sub_unit, related_by);
        //         main_val = converted.main_qty;
        //         sub_val = converted.sub_qty;
        //         row.find('.main_qty').val(main_val);
        //         row.find('.sub_qty').val(sub_val);
        //     }

        //     let price = parseFloat(row.find('.rate').val()) || 0;
        //     let subTotal = calculate_sub_total(main_val, sub_val, price, related_by, has_sub_unit);

        //     row.find('.sub_total').val(subTotal);

        //     // Recalculate total amount
        //     estimatedAmount();
        // }

        function handle_change(obj) {
            let row = obj.closest('tr');

            let main_val = parseFloat(empty_field_check(row.find('.main_qty').val())) || 0;
            let sub_val = parseFloat(empty_field_check(row.find('.sub_qty').val())) || 0;

            let related_by = parseInt(row.find('.quantity-input').attr('data-related')) || 1;
            let has_sub_unit = row.find('.has_sub_unit').val();

            let isService = parseInt(row.attr('data-is-service')) || 0;

            if (isService !== 1) {
                // ✅ Check if variation exists
                let variation_select = row.find('select[name="variation_id[]"]');
                let stock = 0;

                if (variation_select.length > 0) {
                    // Get stock from selected variation
                    let selectedOption = variation_select.find('option:selected');
                    let variationStock = parseFloat(selectedOption.attr('stock')) || 0;

                    // Convert to smallest unit if sub-unit exists
                    stock = has_sub_unit === "true" ? variationStock * related_by : variationStock;
                } else {
                    // Fallback: use product stock
                    let stockText = row.find('.quantity-input').attr('data-stock');
                    let stock_qty = parseStockText(stockText, has_sub_unit, related_by);
                    stock = stock_qty * related_by;
                }

                let converted_sub = to_sub_unit(main_val, sub_val, related_by, has_sub_unit);

                if (stock < converted_sub) {
                    iziToast.error({
                        title: "{{ __('Not Enough Stock for selected variation.') }}",
                        position: "topRight",
                    });

                    // Set max available qty
                    let converted = convert_to_main_and_sub(stock, has_sub_unit, related_by);
                    main_val = converted.main_qty;
                    sub_val = converted.sub_qty;
                    row.find('.main_qty').val(main_val);
                    row.find('.sub_qty').val(sub_val);
                }
            }

            update_row_discount_and_subtotal(row);
            estimatedAmount();
        }
        // Qty increase
        $(document).on("click", ".btn-increase", function() {
            let input = $(this).closest(".input-group").find(".main_qty");
            let val = parseFloat(input.val()) || 0;
            input.val(val + 1).trigger("change");
        });

        // Qty decrease
        $(document).on("click", ".btn-decrease", function() {
            let input = $(this).closest(".input-group").find(".main_qty");
            let val = parseFloat(input.val()) || 0;
            if (val > 1) {
                input.val(val - 1).trigger("change");
            } else if (val === 1) {
                // If it's the last item, ask if they want to remove it
                if (confirm("Remove this item from cart?")) {
                    $(this).closest('tr').find('.remove-btn').trigger('click');
                }
            }
        });

        // rate change
        $(document).on('keyup change', '.rate', function(e) {
            handle_change($(this));
            return;
        });
        // main_qty change
        $(document).on('keyup change', '.main_qty', function(e) {
            handle_change($(this));
            return;
        });

        //estimatedAmount function
        function estimatedAmount() {
            var sum = 0;

            $(".sub_total").each(function() {
                if ($(this).is('input')) {
                    var value = $(this).val();
                    if (value !== undefined && !isNaN(value) && value.length != 0) {
                        sum += parseFloat(value);
                    }
                }
            });
            $("input[name='estimated_amount']").val(sum);
            totalCalculate();
        }

        // Other Calculations - discount
        $(document).on(
            "input keyup change",
            ".discount_val_input, .discount_type_select",
            function() {
                let parentCol = $(this).closest('.checkout-col');
                let val = parentCol.find('.discount_val_input').val();
                let type = parentCol.find('.discount_type_select').val();

                $('.discount_val_input').not(parentCol.find('.discount_val_input')).val(val);
                $('.discount_type_select').not(parentCol.find('.discount_type_select')).val(type);

                let numVal = parseFloat(val) || 0;
                if (type === 'percent') {
                    if (numVal > 100) {
                        numVal = 100;
                        $('.discount_val_input').val(100);
                        iziToast.warning({
                            title: "Discount cannot exceed 100%",
                            position: "topRight",
                        });
                    }
                    $('.discount_amount').val(numVal > 0 ? (numVal + '%') : '');
                } else {
                    $('.discount_amount').val(numVal > 0 ? numVal : '');
                }

                totalCalculate();
            }
        );

        $(document).on(
            "keyup change",
            "input[name='discount_amount']",
            function() {
                totalCalculate();
            }
        );

        $(document).on(
            "keyup change",
            "input[name='vat']",
            function() {
                let val = $(this).val();
                $("input[name='vat']").not(this).val(val);
                totalCalculate();
            }
        );

        $(document).on(
            "keyup change",
            "input[name='pay_point']",
            function() {
                totalCalculate();
            }
        );

        $(document).on(
            "keyup change",
            "input[name='delivery_charge']",
            function() {
                totalCalculate();
            });

        function totalCalculate() {
            // Calculate total quantity (sum of all item quantities in the cart)
            let totalQty = 0;
            $("#tbody tr.item-row").each(function() {
                let row = $(this);
                let qtyInput = row.find('.quantity-input');
                let qtyVal = 0;
                if (qtyInput.length > 0) {
                    qtyVal = parseFloat(qtyInput.val()) || 0;
                } else {
                    qtyVal = parseFloat(row.find('.main_qty').val()) || 0;
                }
                totalQty += qtyVal;
            });
            let displayQty = Number.isInteger(totalQty) ? totalQty : parseFloat(totalQty.toFixed(3));
            $(".total_item").text(displayQty);

            let discountType = $(".discount_type_select:visible").first().val() || $(".discount_type_select").val() || 'fixed';
            let discountVal = parseFloat($(".discount_val_input:visible").first().val() || $(".discount_val_input").val()) || 0;
            let estimated_amount = parseFloat(
                $("input[name='estimated_amount']").val()
            ) || 0;

            let delivery_amount = parseFloat(
                $("input[name='delivery_charge']").val()
            ) || 0;

            let discountAmount = 0;
            if (discountType === 'percent') {
                if (discountVal > 100) {
                    discountVal = 100;
                    $(".discount_val_input").val(100);
                    iziToast.warning({
                        title: "Discount cannot exceed 100%",
                        position: "topRight",
                    });
                }
                discountAmount = Math.round(estimated_amount * (discountVal / 100));
                $(".discount_amount").val(discountVal > 0 ? (discountVal + '%') : '');
            } else {
                discountAmount = discountVal;
                $(".discount_amount").val(discountVal > 0 ? discountVal : '');
            }

            // Cap discount: cannot exceed the gross total amount
            if (estimated_amount > 0 && discountAmount > estimated_amount) {
                discountAmount = estimated_amount;
                if (discountType === 'fixed') {
                    $(".discount_val_input").val(estimated_amount.toFixed(2));
                    $(".discount_amount").val(estimated_amount.toFixed(2));
                }
                iziToast.warning({
                    title: "Discount cannot exceed the total amount (" + estimated_amount.toFixed(2) + ")",
                    position: "topRight",
                });
            }

            let vat_input = $(".vat").val();
            vat_input = empty_field_check(vat_input);
            let vatAmount = 0;
            if ((typeof vat_input === 'string' || vat_input instanceof String) && vat_input.includes("%")) {
                let removed_percent_vat = vat_input.replace('%', '');
                vat_input = parseFloat(removed_percent_vat);
                vatAmount = Math.round($(".estimated_amount").val() * (vat_input / 100));
            } else {
                vatAmount = parseFloat(vat_input) || 0;
            }

            let total_amount = estimated_amount - discountAmount + vatAmount + delivery_amount;
            $("#grand_total").text(total_amount.toFixed(2));
            $(".sub_total").text(estimated_amount.toFixed(2));
            $(".discount_amount_display").text(discountAmount.toFixed(2));
            $(".discount").val(discountAmount.toFixed(2));
            $(".vat_amount_display").text(vatAmount.toFixed(2));
            $(".vat_amount").val(vatAmount.toFixed(2));
            $(".payable_amount").text(total_amount.toFixed(2));
            $("#payable_amount").val(total_amount.toFixed(2));
            $(".delivery_charge").text(delivery_amount.toFixed(2));
            $("#delivery_charge").val(delivery_amount.toFixed(2));

            // Update COD amount field if online sale is checked
            if ($('.online-sale-toggle-chk').is(':checked')) {
                $('.cod-amount-input').val(total_amount.toFixed(2));
            }

            // Direct Paid Amount: By default, automatically populate pay_amount with the net payable amount
            if (!window.isFullDueManual && !$('#is_installment').is(':checked')) {
                let payPoint = parseFloat($('.pay_point').val()) || 0;
                let pointTkValue = Math.min(total_amount, payPoint * 0.75);
                let needed = total_amount - pointTkValue;
                let finalNeeded = needed > 0 ? needed : 0;
                $('.pay_amount').val(finalNeeded > 0 ? finalNeeded.toFixed(2) : (total_amount > 0 ? total_amount.toFixed(2) : '0.00'));
            }

            if (typeof updateInlineAmounts === 'function') {
                updateInlineAmounts();
            }
            if ($('#is_installment').is(':checked')) {
                if (typeof calculateInstallments === 'function') {
                    calculateInstallments();
                }
            }
        }

        // ===================order modal===================
        //payment_modal_btn
        $("#payment_modal_btn").on("click", function() {
            //date
            var date = $("#date").val();
            var variation = $("#variation_id").val();
            if (date == '') {
                //Walking Customer Can't to Create a Due
                iziToast.warning({
                    title: "Please select a date.",
                    position: "topRight",
                });
                return false;
            }
            if (variation == '') {
                //Walking Customer Can't to Create a Due
                iziToast.warning({
                    title: "Please select a product variation.",
                    position: "topRight",
                });
                return false;
            }
            //customer_id
            var customer_id = $("#customer_id").val();
            if (customer_id == '') {
                //Walking Customer Can't to Create a Due
                iziToast.warning({
                    title: "Please select a customer.",
                    position: "topRight",
                });
                return false;
            }
            //order_modal_obj
            if ($.trim($('.name').val()) == '') {
                iziToast.warning({
                    title: "Please select at least one product",
                    position: "topRight",
                });
                return false;
            }
            // count of tr in cart_list table
            var count = $("#tbody").find("tr#tbody_tr").length;
            $(".total_item").text(count);
            //get customer name from id="customer_id"
            var customer_name = $("#customer_id").find("option:selected").text();
            $("#payment_modal").find("#customer_name").text(customer_name);
            var customer_id = $("#customer_id").val();
            $("#payment_modal").find("input[name=customer_id]").val(customer_id);
            //show payment_modal
            $("#payment_modal").modal("show");
        });


        //id="checkout"
        $("#checkout").on("click", function() {
            if (!navigator.onLine) {
                saveOfflineInvoice();
                return false;
            }
            var customer_id = $("#customer_id").val();
            var payable_amount = $("#payable_amount").val();
            var pay_amount = $(".pay_amount").val();
            var paid_amount = $("#paid_amount").val();
            var due_amount = $("#due_amount").val();
            // if (parseFloat(pay_amount) > parseFloat(payable_amount)) {
            //     iziToast.warning({
            //         title: "Sorry Over Payment Not Allowed.",
            //         position: "topRight",
            //     });
            //     return false;
            // }
            if (parseFloat(pay_amount) < 0) {
                iziToast.warning({
                    title: "Sorry Below Payment Not Allowed.",
                    position: "topRight",
                });
                return false;
            }
            if (paid_amount == 0.00 && due_amount == 0.00) {
                iziToast.warning({
                    title: "Please Enter Pay Amount.",
                    position: "topRight",
                });
                return false;
            }
            if (customer_id == 1 && due_amount != 0.00) {
                iziToast.warning({
                    title: "Walking Customer Can't to Create a Due",
                    position: "topRight",
                });
                return false;
            }

            let imei_error = false;
            $("#tbody tr").each(function() {
                let row = $(this);
                let imeiInput = row.find('.imei_input');
                if (!imeiInput.length || !imeiInput.prop('required')) return;

                let imeiValue = (imeiInput.val() || '').trim();
                if (!imeiValue) {
                    imei_error = true;
                    return false;
                }

                let selectedCount = imeiValue.split(/\r?\n/).map(x => x.trim()).filter(Boolean).length;
                let qty = parseInt(row.find('.main_qty').val() || row.find('.quantity-input').val() || "0", 10) || 0;
                if (qty > 0 && selectedCount !== qty) {
                    imei_error = true;
                    return false;
                }
            });

            if (imei_error) {
                iziToast.warning({
                    title: "Please select IMEI for all products that require it.",
                    position: "topRight",
                });
                return false;
            }
            
            // Clear cart items from localStorage on successful checkout
            localStorage.removeItem('pos-items');
            if (typeof localData !== 'undefined') {
                localData = [];
            }
            
            $("#payment_form").submit();
        });

        // Toggle Sidebar with Persistence
        function toggleCategorySidebar() {
            const sidebar = $('#category-sidebar');
            const mainArea = $('#main-pos-area');
            
            if (sidebar.hasClass('sidebar-pos-hidden')) {
                sidebar.removeClass('sidebar-pos-hidden');
                mainArea.removeClass('col-md-12').addClass('col-md-7');
                localStorage.setItem('pos_sidebar_hidden', 'false');
                $('#sidebar_toggle').prop('checked', true);
            } else {
                sidebar.addClass('sidebar-pos-hidden');
                mainArea.removeClass('col-md-7').addClass('col-md-12');
                localStorage.setItem('pos_sidebar_hidden', 'true');
                $('#sidebar_toggle').prop('checked', false);
            }
        }

        function toggleCategorySidebarAlt() {
            const sidebar = $('#category-sidebar-alt');
            const mainArea = $('#main-pos-area-alt');
            
            if (sidebar.hasClass('sidebar-pos-hidden')) {
                sidebar.removeClass('sidebar-pos-hidden');
                mainArea.removeClass('col-md-12').addClass('col-md-7');
                localStorage.setItem('pos_sidebar_hidden_alt', 'false');
                $('#sidebar_toggle').prop('checked', true);
            } else {
                sidebar.addClass('sidebar-pos-hidden');
                mainArea.removeClass('col-md-7').addClass('col-md-12');
                localStorage.setItem('pos_sidebar_hidden_alt', 'true');
                $('#sidebar_toggle').prop('checked', false);
            }
        }

        // Full Screen Mode Logic
        function enterFullScreen() {
            if (!document.fullscreenElement && $('body').hasClass('kiosk-mode')) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.log(`Error attempting to enable full-screen mode: ${err.message}`);
                });
            }
        }

        function startPOS() {
            if ($('body').hasClass('kiosk-mode')) {
                enterFullScreen();
            }
            $('#pos-init-overlay').css('opacity', '0');
            setTimeout(function() {
                $('#pos-init-overlay').remove();
            }, 400);
        }

        function toggleKioskMode() {
            const body = $('body');
            const icon = $('#kiosk-btn-icon');
            
            if (body.hasClass('kiosk-mode')) {
                // Switch to Admin View
                body.removeClass('kiosk-mode');
                if (document.fullscreenElement) {
                    document.exitFullscreen();
                }
                icon.removeClass('fa-compress').addClass('fa-expand');
                localStorage.setItem('pos_view_mode', 'admin');
            } else {
                // Switch to Kiosk View
                body.addClass('kiosk-mode');
                enterFullScreen();
                icon.removeClass('fa-expand').addClass('fa-compress');
                localStorage.setItem('pos_view_mode', 'kiosk');
            }
        }

        $(document).ready(function() {
            @if(env('APP_POS_AUTO_FULL_VIEW', 'yes') != 'no')
                // Auto start in kiosk (full view) mode on page load when enabled in settings
                localStorage.setItem('pos_view_mode', 'kiosk');
                $('body').addClass('kiosk-mode');
                $('#kiosk-btn-text').text('{{ __("Admin View") }}');
                enterFullScreen();

                // Enforce sidebar hidden by default on load for the biggest view in kiosk mode
                $('#containerbar').addClass('sidebar-none');
                $('.pos-full-hide-toggle-btn i').removeClass('fa-eye-slash').addClass('fa-eye');
                if (document.getElementById('full-hide-toggle')) {
                    $('#full-hide-toggle i').removeClass('fa-eye-slash').addClass('fa-eye');
                }
            @else
                // Start in standard mode (not auto full view)
                $('body').removeClass('kiosk-mode');
                $('#containerbar').removeClass('sidebar-none');
                $('#kiosk-btn-text').text('{{ __("Full View") }}');
                $('.pos-full-hide-toggle-btn i').removeClass('fa-eye').addClass('fa-eye-slash');
                if (document.getElementById('full-hide-toggle')) {
                    $('#full-hide-toggle i').removeClass('fa-eye').addClass('fa-eye-slash');
                }
            @endif

            // Click handler for POS full hide toggle buttons
            $(document).on('click', '.pos-full-hide-toggle-btn', function() {
                const container = $('#containerbar');
                container.toggleClass('sidebar-none');
                
                const isHidden = container.hasClass('sidebar-none');
                localStorage.setItem('sidebar_hidden', isHidden ? 'true' : 'false');
                
                // Sync icons for all eye buttons on the page
                if (isHidden) {
                    $('.pos-full-hide-toggle-btn i, #full-hide-toggle i').removeClass('fa-eye-slash').addClass('fa-eye');
                } else {
                    $('.pos-full-hide-toggle-btn i, #full-hide-toggle i').removeClass('fa-eye').addClass('fa-eye-slash');
                }
            });

            // Click handler for POS Kiosk Hamburger button
            $(document).on('click', '.pos-kiosk-hamburger-btn', function() {
                const container = $('#containerbar');
                if (container.hasClass('sidebar-none')) {
                    // If the sidebar is hidden, show it first
                    container.removeClass('sidebar-none');
                    localStorage.setItem('sidebar_hidden', 'false');
                    $('.pos-full-hide-toggle-btn i, #full-hide-toggle i').removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    // If visible, toggle mini/full using the global toggleSidebar function
                    if (typeof toggleSidebar === 'function') {
                        toggleSidebar();
                    }
                }
            });

            const isHidden = localStorage.getItem('pos_sidebar_hidden');
            if (isHidden === 'true') {
                $('#category-sidebar').addClass('sidebar-pos-hidden');
                $('#main-pos-area').removeClass('col-md-7').addClass('col-md-12');
                $('#sidebar_toggle').prop('checked', false);
            } else {
                $('#category-sidebar').removeClass('sidebar-pos-hidden');
                $('#main-pos-area').removeClass('col-md-12').addClass('col-md-7');
                $('#sidebar_toggle').prop('checked', true);
            }

            const isHiddenAlt = localStorage.getItem('pos_sidebar_hidden_alt');
            if (isHiddenAlt === 'true') {
                $('#category-sidebar-alt').addClass('sidebar-pos-hidden');
                $('#main-pos-area-alt').removeClass('col-md-7').addClass('col-md-12');
                $('#sidebar_toggle').prop('checked', false);
            } else {
                $('#category-sidebar-alt').removeClass('sidebar-pos-hidden');
                $('#main-pos-area-alt').removeClass('col-md-12').addClass('col-md-7');
                $('#sidebar_toggle').prop('checked', true);
            }
        });

        // AJAX Pagination for Products
        $(document).on('click', '#products .pagination a', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            var cat_id = $('#getProductsByCat').val();
            
            // If we have a category filter, append it to pagination URL
            if (cat_id) {
                url += (url.includes('?') ? '&' : '?') + 'cat_id=' + cat_id;
            }

            $.ajax({
                url: url,
                type: "GET",
                success: function(data) {
                    // Check if the response contains the full page or just the partial
                    // If it's a full page, we extract the #products content
                    var html = $(data).find('#products').length > 0 ? $(data).find('#products').html() : data;
                    $("#products").html(html);
                },
                error: function() {
                    console.log('Pagination error');
                }
            });
        });

        // IMEI Modal Logic
        let currentImeiBtn = null;

        $(document).on('click', '.select_imei_btn', function() {
            currentImeiBtn = $(this);
            let productId = $(this).data('product-id');
            let currentRow = $(this).closest('tr');
            let currentInput = currentRow.find('.imei_input');
            let selectedImeis = (currentInput.val() || '').split(/\r?\n/).map(x => x.trim()).filter(Boolean);
            let usedImeis = new Set();

            $('.imei_input').each(function() {
                if (this === currentInput.get(0)) return;
                let vals = ($(this).val() || '').split(/\r?\n/).map(x => x.trim()).filter(Boolean);
                vals.forEach(v => usedImeis.add(v));
            });
            
            console.log("Opening IMEI modal for product:", productId);
            
            let url = "{{ route('sc-pos-product-id', 'my_id') }}".replace('my_id', productId);
            $.get(url, data => {
                let imeis = data.imeis || [];
                let html = '';
                
                if (imeis.length === 0) {
                    html = '<div class="alert alert-warning">No available IMEIs found for this product.</div>';
                } else {
                    imeis.forEach(imei => {
                        let serial = (typeof imei === 'object') ? imei.serial : imei;
                        let w_val = (typeof imei === 'object') ? (imei.warranty_value || '') : '';
                        let w_unit = (typeof imei === 'object') ? (imei.warranty_unit || '') : '';
                        
                        let checked = selectedImeis.includes(serial.toString()) ? 'checked' : '';
                        let disabled = (!checked && usedImeis.has(serial.toString())) ? 'disabled' : '';
                        html += `
                            <div class="custom-control custom-checkbox mb-2 imei-item">
                                <input type="checkbox" class="custom-control-input imei-checkbox" 
                                    id="imei_${productId}_${serial}" value="${serial}" 
                                    data-warranty-value="${w_val}" data-warranty-unit="${w_unit}"
                                    ${checked} ${disabled}>
                                <label class="custom-control-label" for="imei_${productId}_${serial}">${serial}</label>
                            </div>
                        `;
                    });
                }
                
                $('#imeiList').html(html);
                $('#selectedImeiCount').text(selectedImeis.length);
                $('#imeiModal').modal('show');
            }).fail(function(xhr) {
                console.error("Failed to fetch IMEIs:", xhr);
                alert("Error loading IMEI data. Please check connection.");
            });
        });

        $(document).on('change', '.imei-checkbox', function() {
            let count = $('.imei-checkbox:checked').length;
            $('#selectedImeiCount').text(count);
        });

        $('#imeiSearch').on('input', function() {
            let query = $(this).val().toLowerCase();
            $('.imei-item').each(function() {
                let text = $(this).find('label').text().toLowerCase();
                $(this).toggle(text.includes(query));
            });
        });

        $('#confirmImei').on('click', function() {
            if (!currentImeiBtn) return;
            
            let selected = [];
            $('.imei-checkbox:checked').each(function() {
                selected.push($(this).val());
            });

            if (selected.length === 0) {
                iziToast.warning({
                    title: "Please select at least one IMEI.",
                    position: "topRight",
                });
                return;
            }
            
            let row = currentImeiBtn.closest('tr');
            row.find('.imei_input').val(selected.join('\n'));
            row.find('.selected_imeis_display').text(selected.join(', '));

            // Update warranty from first selected IMEI if it has warranty info
            let firstSelected = $('.imei-checkbox:checked').first();
            let imei_w_val = firstSelected.data('warranty-value');
            let imei_w_unit = firstSelected.data('warranty-unit');

            if (imei_w_val) {
                row.find('.warranty_value_input').val(imei_w_val);
            }
            if (imei_w_unit) {
                row.find('.warranty_unit_input').val(imei_w_unit);
            }
            if (imei_w_val && imei_w_unit) {
                row.find('.warranty-display-badge').text(imei_w_val + ' ' + imei_w_unit);
            }
            
            let count = selected.length;
            row.find('.main_qty').val(count);
            let qtyInput = row.find('.quantity-input');
            if (qtyInput.length > 0) {
                qtyInput.val(count).css('border', '');
            }
            row.find('.stock-info-label').removeClass('text-danger').addClass('text-muted').css('font-weight', 'normal').html('');
            
            handle_change(qtyInput.length > 0 ? qtyInput : row.find('.main_qty'));
            
            $('#imeiModal').modal('hide');
        });

        // Product Search
        $(document).on("change", "#getProductsByCat", function() {
            var cat_id = $(this).val();
            $.ajax({
                url: "{{ route('posProducts') }}",
                type: "GET",
                data: {
                    cat_id: cat_id
                },
                success: function(data) {
                    $("#products").html(data);
                },
                error: function() {
                    alert('Error !');
                }
            });
        });
        // Sidebar Toggle Logic
        $(document).on('change', '#sidebar_toggle', function() {
            if ($('#pos-row-container').length) {
                toggleCategorySidebar();
            } else if ($('#pos-row-container-alt').length) {
                toggleCategorySidebarAlt();
            }
        });

        // Hold System Functions (Database-Backed with Instant Persistence)
        function updateHoldCount() {
            $.get("{{ route('invoice.hold.list') }}", function(res) {
                if (res && res.success) {
                    $('.hold_count, #hold_count, #hold_count_alt').text(res.hold_count || 0);
                }
            }).fail(function() {
                let holds = JSON.parse(localStorage.getItem('pos-holds') || '[]');
                $('.hold_count, #hold_count, #hold_count_alt').text(holds.length);
            });
        }

        $(document).ready(function() {
            updateHoldCount();
        });

        $(document).on('click', '#hold_invoice_btn, #hold_invoice_btn_alt, .hold_invoice_btn', function() {
            if (typeof localData === 'undefined' || localData.length === 0) {
                iziToast.warning({ title: "{{ __('Cart is empty') }}", position: "topRight" });
                return;
            }

            let items_data = [];
            // Products are prepended, so rows in DOM are reverse order of localData
            $("#tbody tr, #pos-row-container-alt #tbody tr").get().reverse().forEach(function(el, index) {
                let row = $(el);
                if (row.hasClass('item-row') || row.find('.main_qty').length > 0) {
                    items_data.push({
                        main_qty: row.find(".main_qty").val() || 1,
                        sub_qty: row.find(".sub_qty").val() || 0,
                        quantity_input: row.find(".quantity-input").val() || 1,
                        rate: row.find(".rate").val() || 0,
                        product_discount: row.find(".product_discount").val() || 0,
                        product_discount_val: row.find(".product_discount_val").val() || 0,
                        product_discount_type: row.find(".product_discount_type").val() || 'fixed',
                        sub_total: row.find(".sub_total").val() || 0,
                        variation_id: row.find("select[name='variation_id[]']").val() || row.find("input[name='variation_id[]']").val() || '',
                        imei: row.find(".imei_input").val() || '',
                        imei_display: row.find(".selected_imeis_display").text() || '',
                        warranty_value: row.find(".warranty_value_input").val() || '',
                        warranty_unit: row.find(".warranty_unit_input").val() || 'Month'
                    });
                }
            });

            let custId = $("#customer_id").val() || $("select[name='customer_id']").val() || 1;
            let custName = $("#customer_id option:selected").text() || $("select[name='customer_id'] option:selected").text() || 'Walking Customer';
            let totalAmt = parseFloat($(".estimated_amount").val()) || 0;

            let payload = {
                _token: "{{ csrf_token() }}",
                customer_id: custId,
                customer_name: custName,
                total_amount: totalAmt,
                total_items: localData.length,
                localData: JSON.parse(JSON.stringify(localData)),
                items_data: items_data
            };

            let $btn = $(this);
            $btn.prop('disabled', true);

            $.ajax({
                url: "{{ route('invoice.hold.store') }}",
                type: "POST",
                data: payload,
                success: function(res) {
                    $btn.prop('disabled', false);
                    if (res.success) {
                        clearCart();
                        $("#customer_id, select[name='customer_id']").val(1).trigger('change');
                        $('.hold_count, #hold_count, #hold_count_alt').text(res.hold_count);
                        iziToast.success({ title: "{{ __('Invoice held successfully') }}", position: "topRight" });
                    } else {
                        iziToast.error({ title: res.message || "{{ __('Failed to hold invoice') }}", position: "topRight" });
                    }
                },
                error: function() {
                    $btn.prop('disabled', false);
                    // Fallback to offline localStorage
                    let holds = JSON.parse(localStorage.getItem('pos-holds') || '[]');
                    payload.id = 'OFFLINE-' + Date.now();
                    payload.date = new Date().toLocaleString();
                    holds.push(payload);
                    localStorage.setItem('pos-holds', JSON.stringify(holds));
                    clearCart();
                    $("#customer_id, select[name='customer_id']").val(1).trigger('change');
                    updateHoldCount();
                    iziToast.warning({ title: "{{ __('Invoice held offline') }}", position: "topRight" });
                }
            });
        });

        $(document).on('click', '#hold_list_btn, #hold_list_btn_alt, .hold_list_btn', function() {
            $('#hold_list_body').html('<tr><td colspan="5" class="text-center py-3"><i class="fa fa-spinner fa-spin mr-2"></i>{{ __('Loading held invoices...') }}</td></tr>');
            $('#holdListModal').modal('show');

            $.get("{{ route('invoice.hold.list') }}", function(res) {
                if (res && res.success) {
                    $('.hold_count, #hold_count, #hold_count_alt').text(res.hold_count);
                    let html = '';
                    if (res.holds && res.holds.length > 0) {
                        res.holds.forEach((hold) => {
                            let formattedDate = new Date(hold.created_at).toLocaleString();
                            let formattedTotal = parseFloat(hold.total_amount).toFixed(2);
                            html += `
                                <tr>
                                    <td><span class="badge badge-secondary mr-1">${hold.hold_no || 'HOLD'}</span><br><small class="text-muted">${formattedDate}</small></td>
                                    <td><b>${hold.customer_name || 'N/A'}</b></td>
                                    <td><span class="badge badge-info">${hold.total_items} items</span></td>
                                    <td class="font-weight-bold text-success">৳ ${formattedTotal}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary apply-hold-db mr-1" data-id="${hold.id}" title="{{ __('Recall / Apply to Cart') }}">
                                            <i class="fa fa-check mr-1"></i> {{ __('Apply') }}
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-hold-db" data-id="${hold.id}" title="{{ __('Delete') }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        html = '<tr><td colspan="5" class="text-center text-muted py-4"><i class="fa fa-info-circle mr-1"></i> {{ __('No held invoices found') }}</td></tr>';
                    }
                    $('#hold_list_body').html(html);
                }
            }).fail(function() {
                // Fallback to localStorage if offline
                let holds = JSON.parse(localStorage.getItem('pos-holds') || '[]');
                let html = '';
                if (holds.length > 0) {
                    holds.forEach((hold, index) => {
                        let itemCount = hold.localData ? hold.localData.length : 0;
                        html += `
                            <tr>
                                <td>${hold.date || 'Offline'}</td>
                                <td>${hold.customer_name || 'N/A'}</td>
                                <td>${itemCount}</td>
                                <td>৳ ${(parseFloat(hold.total_amount || hold.total) || 0).toFixed(2)}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-primary apply-hold-local mr-1" data-index="${index}" title="{{ __('Apply') }}"><i class="fa fa-check"></i></button>
                                    <button type="button" class="btn btn-sm btn-danger delete-hold-local" data-index="${index}" title="{{ __('Delete') }}"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    html = '<tr><td colspan="5" class="text-center text-muted py-4">{{ __('No held invoices') }}</td></tr>';
                }
                $('#hold_list_body').html(html);
            });
        });

        // Delete from DB
        $(document).on('click', '.delete-hold-db', function() {
            let holdId = $(this).data('id');
            if (!confirm("{{ __('Are you sure you want to delete this held invoice?') }}")) return;
            
            let $row = $(this).closest('tr');
            $row.css('opacity', '0.5');

            $.ajax({
                url: "{{ url('/invoice/hold/delete') }}/" + holdId,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    if (res && res.success) {
                        $('.hold_count, #hold_count, #hold_count_alt').text(res.hold_count);
                        $row.fadeOut(300, function() {
                            $(this).remove();
                            if ($('#hold_list_body tr').length === 0) {
                                $('#hold_list_body').html('<tr><td colspan="5" class="text-center text-muted py-4">{{ __('No held invoices found') }}</td></tr>');
                            }
                        });
                        iziToast.info({ title: "{{ __('Held invoice removed') }}", position: "topRight" });
                    }
                }
            });
        });

        // Delete from LocalStorage (Offline fallback)
        $(document).on('click', '.delete-hold-local', function() {
            let index = $(this).data('index');
            let holds = JSON.parse(localStorage.getItem('pos-holds') || '[]');
            holds.splice(index, 1);
            localStorage.setItem('pos-holds', JSON.stringify(holds));
            updateHoldCount();
            $('#hold_list_btn, #hold_list_btn_alt, .hold_list_btn').first().trigger('click');
        });

        // Apply / Recall from DB
        $(document).on('click', '.apply-hold-db', function() {
            let holdId = $(this).data('id');
            if (typeof localData !== 'undefined' && localData.length > 0) {
                if (!confirm("{{ __('Current cart will be cleared and replaced with this held invoice. Continue?') }}")) return;
            }

            let $btn = $(this);
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

            $.get("{{ url('/invoice/hold/get') }}/" + holdId, function(res) {
                if (res && res.success && res.hold) {
                    let hold = res.hold;
                    let itemsData = hold.items_data || {};
                    let savedLocalData = itemsData.localData || [];
                    let savedRowItems = itemsData.items_data || [];

                    // Load Customer
                    if (hold.customer_id) {
                        $("#customer_id, select[name='customer_id']").val(hold.customer_id).trigger('change');
                    }

                    // Populate Cart
                    localData = JSON.parse(JSON.stringify(savedLocalData));
                    localStorage.setItem('pos-items', JSON.stringify(localData));

                    $("#tbody").html('');
                    localData.forEach((item, idx) => {
                        let itemData = savedRowItems[idx] || {};
                        domPrepend(item, idx, itemData.variation_id || item.variation_code);
                        let row = $("#tbody tr:first");
                        if (itemData.main_qty !== undefined && itemData.main_qty !== null) row.find(".main_qty").val(itemData.main_qty);
                        if (itemData.sub_qty !== undefined && itemData.sub_qty !== null) row.find(".sub_qty").val(itemData.sub_qty);
                        if (itemData.quantity_input !== undefined && itemData.quantity_input !== null) row.find(".quantity-input").val(itemData.quantity_input);
                        if (itemData.rate !== undefined && itemData.rate !== null) row.find(".rate").val(itemData.rate);
                        if (itemData.product_discount_val !== undefined && itemData.product_discount_val !== null) row.find(".product_discount_val").val(itemData.product_discount_val);
                        if (itemData.product_discount_type !== undefined && itemData.product_discount_type !== null) row.find(".product_discount_type").val(itemData.product_discount_type);
                        if (itemData.product_discount !== undefined && itemData.product_discount !== null) row.find(".product_discount").val(itemData.product_discount);
                        if (itemData.sub_total !== undefined && itemData.sub_total !== null) row.find(".sub_total").val(itemData.sub_total);
                        if (itemData.imei !== undefined && itemData.imei !== null) row.find(".imei_input").val(itemData.imei);
                        if (itemData.imei_display !== undefined && itemData.imei_display !== null) row.find(".selected_imeis_display").text(itemData.imei_display);
                        if (itemData.warranty_value !== undefined && itemData.warranty_value !== null) {
                            row.find(".warranty_value_input").val(itemData.warranty_value);
                            let u = itemData.warranty_unit || 'Month';
                            row.find(".warranty_unit_input").val(u);
                            row.find(".warranty-display-badge").text(itemData.warranty_value ? (itemData.warranty_value + ' ' + u) : '-');
                        }
                    });

                    estimatedAmount();
                    $('#holdListModal').modal('hide');

                    // Delete the recalled hold from DB
                    $.ajax({
                        url: "{{ url('/invoice/hold/delete') }}/" + holdId,
                        type: "POST",
                        data: { _token: "{{ csrf_token() }}" },
                        success: function(delRes) {
                            if (delRes && delRes.success) {
                                $('.hold_count, #hold_count, #hold_count_alt').text(delRes.hold_count);
                            }
                        }
                    });

                    iziToast.success({ title: "{{ __('Held invoice recalled to cart!') }}", position: "topRight" });
                }
            }).fail(function() {
                $btn.prop('disabled', false).html('<i class="fa fa-check mr-1"></i> {{ __("Apply") }}');
                iziToast.error({ title: "{{ __('Failed to load held invoice') }}", position: "topRight" });
            });
        });

        // Apply from LocalStorage (Offline fallback)
        $(document).on('click', '.apply-hold-local', function() {
            let index = $(this).data('index');
            let holds = JSON.parse(localStorage.getItem('pos-holds') || '[]');
            let hold = holds[index];

            if (!hold) return;

            if (localData.length > 0) {
                if (!confirm("{{ __('Current cart will be cleared. Continue?') }}")) return;
            }

            if (hold.customer_id) {
                $("#customer_id, select[name='customer_id']").val(hold.customer_id).trigger('change');
            }
            
            localData = JSON.parse(JSON.stringify(hold.localData || []));
            localStorage.setItem('pos-items', JSON.stringify(localData));
            
            $("#tbody").html('');
            localData.forEach((item, idx) => {
                let itemData = (hold.items_data && hold.items_data[idx]) ? hold.items_data[idx] : {};
                domPrepend(item, idx, itemData.variation_id || item.variation_code);
                let row = $("#tbody tr:first");
                if (itemData.main_qty !== undefined && itemData.main_qty !== null) row.find(".main_qty").val(itemData.main_qty);
                if (itemData.sub_qty !== undefined && itemData.sub_qty !== null) row.find(".sub_qty").val(itemData.sub_qty);
                if (itemData.quantity_input !== undefined && itemData.quantity_input !== null) row.find(".quantity-input").val(itemData.quantity_input);
                if (itemData.rate !== undefined && itemData.rate !== null) row.find(".rate").val(itemData.rate);
                if (itemData.product_discount_val !== undefined && itemData.product_discount_val !== null) row.find(".product_discount_val").val(itemData.product_discount_val);
                if (itemData.product_discount_type !== undefined && itemData.product_discount_type !== null) row.find(".product_discount_type").val(itemData.product_discount_type);
                if (itemData.product_discount !== undefined && itemData.product_discount !== null) row.find(".product_discount").val(itemData.product_discount);
                if (itemData.sub_total !== undefined && itemData.sub_total !== null) row.find(".sub_total").val(itemData.sub_total);
                if (itemData.imei !== undefined && itemData.imei !== null) row.find(".imei_input").val(itemData.imei);
                if (itemData.imei_display !== undefined && itemData.imei_display !== null) row.find(".selected_imeis_display").text(itemData.imei_display);
                if (itemData.warranty_value !== undefined && itemData.warranty_value !== null) {
                    row.find(".warranty_value_input").val(itemData.warranty_value);
                    let u = itemData.warranty_unit || 'Month';
                    row.find(".warranty_unit_input").val(u);
                    row.find(".warranty-display-badge").text(itemData.warranty_value ? (itemData.warranty_value + ' ' + u) : '-');
                }
            });

            holds.splice(index, 1);
            localStorage.setItem('pos-holds', JSON.stringify(holds));
            updateHoldCount();

            estimatedAmount();
            $('#holdListModal').modal('hide');
            iziToast.success({ title: "{{ __('Hold invoice applied') }}", position: "topRight" });
        });

        // Offline Data Sync
        // Offline Data Sync
        function syncOfflineData() {
            if (!navigator.onLine) return;
            $('.offline_sync_btn, #offline_sync_btn, #offline_sync_btn_alt').find('i').addClass('fa-spin');
            $.get("{{ route('invoice.offline-data') }}", function(data) {
                if (data) {
                    localStorage.setItem('pos-offline-products', JSON.stringify(data.products || []));
                    localStorage.setItem('pos-offline-customers', JSON.stringify(data.customers || []));
                    $('.offline_sync_btn, #offline_sync_btn, #offline_sync_btn_alt').find('i').removeClass('fa-spin');
                    updateOfflineCustomerDropdown();
                }
            }).fail(function() {
                $('.offline_sync_btn, #offline_sync_btn, #offline_sync_btn_alt').find('i').removeClass('fa-spin');
            });
        }

        function updateOfflineCustomerDropdown() {
            if (navigator.onLine) return;
            let customers = JSON.parse(localStorage.getItem('pos-offline-customers') || '[]');
            let offlineCusts = JSON.parse(localStorage.getItem('pos-offline-customers-new') || '[]');
            let all = [{id: 1, name: 'Walking Customer', phone: '00000000000'}].concat(customers, offlineCusts);
            let html = '';
            let seen = {};
            all.forEach(c => {
                if (c && c.id && !seen[c.id]) {
                    seen[c.id] = true;
                    html += `<option value="${c.id}">${c.name}${c.phone ? ' - ' + c.phone : ''}</option>`;
                }
            });
            $('#customer_id, select[name="customer_id"]').html(html).trigger('change');
        }

        function saveOfflineInvoice() {
            let items = [];
            $("#tbody tr, #pos-row-container-alt #tbody tr").each(function() {
                let row = $(this);
                if (row.hasClass('item-row') || row.find('.main_qty').length > 0) {
                    let prodId = row.find("input[name='product_id[]']").val() || row.attr('data-product-id');
                    if (prodId) {
                        items.push({
                            product_id: prodId,
                            variation_id: row.find("select[name='variation_id[]']").val() || row.find("input[name='variation_id[]']").val() || '',
                            main_qty: parseFloat(row.find(".main_qty").val() || row.find(".quantity-input").val()) || 1,
                            sub_qty: parseFloat(row.find(".sub_qty").val()) || 0,
                            rate: parseFloat(row.find(".rate").val()) || 0,
                            product_discount: parseFloat(row.find(".product_discount").val()) || 0,
                            product_discount_val: parseFloat(row.find(".product_discount_val").val()) || 0,
                            product_discount_type: row.find(".product_discount_type").val() || 'fixed',
                            sub_total: parseFloat(row.find(".sub_total").val()) || 0,
                            imei: row.find(".imei_input").val() || '',
                            product_name: row.find(".item-name, td:first").text().trim()
                        });
                    }
                }
            });

            if (items.length === 0) {
                iziToast.warning({ title: "{{ __('Cart is empty') }}", position: "topRight" });
                return;
            }

            let custId = $("#customer_id").val() || $("select[name='customer_id']").val() || 1;
            let custName = $("#customer_id option:selected").text() || $("select[name='customer_id'] option:selected").text() || 'Walking Customer';
            let totalAmount = parseFloat($("#payable_amount").val() || $(".estimated_amount").val()) || 0;
            let paidAmount = parseFloat($(".pay_amount").val()) || totalAmount;
            let dueAmount = parseFloat($("#due_amount").val()) || 0;

            let offline_inv = {
                unique_id: 'OFF-' + Date.now(),
                customer_id: custId,
                customer_name: custName,
                date: $("#date").val() || new Date().toISOString().slice(0, 10),
                branch_id: $('#branch_id').val() || 2,
                items: items,
                total: totalAmount,
                discount: parseFloat($('.discount').val()) || 0,
                discount_amount: parseFloat($('.discount_amount').val()) || 0,
                vat: parseFloat($('.vat').val()) || 0,
                vat_amount: parseFloat($('.vat_amount').val()) || 0,
                paid_amount: paidAmount,
                due_amount: dueAmount
            };

            let offline_list = JSON.parse(localStorage.getItem('pos-offline-invoices') || '[]');
            offline_list.push(offline_inv);
            localStorage.setItem('pos-offline-invoices', JSON.stringify(offline_list));
            
            clearCart();
            $('#payment_modal').modal('hide');
            iziToast.success({ title: "{{ __('Invoice saved offline. Sync when online.') }}", position: "topRight" });
            updateOfflineSaleCount();
        }

        function updateOfflineSaleCount() {
            let count = JSON.parse(localStorage.getItem('pos-offline-invoices') || '[]').length;
            $('.offline_sale_count, #offline_sale_count, #offline_sale_count_alt').text(count);
        }

        $(document).on('click', '#offline_sales_btn, #offline_sales_btn_alt, .offline_sales_btn', function() {
            let sales = JSON.parse(localStorage.getItem('pos-offline-invoices') || '[]');
            let html = '';
            if (sales.length > 0) {
                sales.forEach((s, idx) => {
                    let itemCount = s.items ? s.items.length : 0;
                    let totalAmt = (parseFloat(s.total) || 0).toFixed(2);
                    html += `
                        <tr>
                            <td><span class="badge badge-warning">${s.unique_id}</span><br><small class="text-muted">${s.date}</small></td>
                            <td><b>${s.customer_name || 'N/A'}</b></td>
                            <td><span class="badge badge-info">${itemCount} items</span></td>
                            <td class="font-weight-bold text-success">৳ ${totalAmt}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-primary apply-offline mr-1" data-index="${idx}">
                                    <i class="fa fa-cloud-upload mr-1"></i> {{ __('Sync') }}
                                </button>
                                <button type="button" class="btn btn-sm btn-danger delete-offline" data-index="${idx}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                html = '<tr><td colspan="5" class="text-center text-muted py-4"><i class="fa fa-check-circle mr-1"></i> {{ __('No pending offline invoices') }}</td></tr>';
            }
            $('#offline_sales_body').html(html);
        });

        $(document).on('click', '.apply-offline', function() {
            let idx = $(this).data('index');
            let sales = JSON.parse(localStorage.getItem('pos-offline-invoices') || '[]');
            let sale = sales[idx];
            
            if (!navigator.onLine) {
                iziToast.warning({ title: "{{ __('You are offline! Cannot sync.') }}", position: "topRight" });
                return;
            }
            
            $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
            submitOfflineInvoice(sale, idx);
        });

        function submitOfflineInvoice(sale, index, callback = null) {
            let formData = {
                _token: "{{ csrf_token() }}",
                customer_id: sale.customer_id || 1,
                date: sale.date || new Date().toISOString().slice(0, 10),
                branch_id: sale.branch_id || $('#branch_id').val() || 2,
                estimated_amount: sale.total || 0,
                discount_amount: sale.discount_amount || 0,
                discount: sale.discount || 0,
                vat_amount: sale.vat_amount || 0,
                vat: sale.vat || 0,
                delivery_charge: 0,
                courier_type: '',
                previous_due: 0,
                note: 'Offline Synced Invoice (' + (sale.unique_id || '') + ')',
                balance: 0,
                sale_type: 'Outlet',
                payable_amount: sale.total,
                paid_amount: sale.paid_amount || sale.total,
                due_amount: sale.due_amount || 0,
                pay_amount: sale.paid_amount || sale.total,
                product_id: sale.items.map(i => i.product_id),
                variation_id: sale.items.map(i => i.variation_id || ''),
                main_qty: sale.items.map(i => i.main_qty || 1),
                sub_qty: sale.items.map(i => i.sub_qty || 0),
                rate: sale.items.map(i => i.rate || 0),
                imei: sale.items.map(i => i.imei || ''),
                product_discount: sale.items.map(i => i.product_discount || 0),
                sub_total: sale.items.map(i => i.sub_total || (i.main_qty * i.rate))
            };

            $.ajax({
                url: "{{ route('invoice.store') }}",
                type: "POST",
                data: formData,
                timeout: 30000,
                success: function(res) {
                    let sales = JSON.parse(localStorage.getItem('pos-offline-invoices') || '[]');
                    let newSales = sales.filter(s => s.unique_id !== sale.unique_id);
                    localStorage.setItem('pos-offline-invoices', JSON.stringify(newSales));
                    updateOfflineSaleCount();
                    
                    if ($('#offlineSalesModal').is(':visible')) {
                        $('#offline_sales_btn, #offline_sales_btn_alt, .offline_sales_btn').first().trigger('click');
                    }
                    
                    if (callback) callback(true);
                    else iziToast.success({ title: "{{ __('Invoice synced successfully!') }}", position: "topRight" });
                },
                error: function(xhr) {
                    if (callback) callback(false);
                    else {
                        let msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "{{ __('Failed to sync invoice') }}";
                        iziToast.error({ title: msg, position: "topRight" });
                        $('.apply-offline').prop('disabled', false).html('<i class="fa fa-cloud-upload mr-1"></i> {{ __("Sync") }}');
                    }
                }
            });
        }

        $(document).on('click', '.delete-offline', function() {
            let idx = $(this).data('index');
            let sales = JSON.parse(localStorage.getItem('pos-offline-invoices') || '[]');
            sales.splice(idx, 1);
            localStorage.setItem('pos-offline-invoices', JSON.stringify(sales));
            $('#offline_sales_btn, #offline_sales_btn_alt, .offline_sales_btn').first().trigger('click');
            updateOfflineSaleCount();
        });

        $('#sync_all_offline').on('click', async function() {
            if (!navigator.onLine) {
                iziToast.warning({ title: "{{ __('Still offline!') }}", position: "topRight" });
                return;
            }
            
            let sales = JSON.parse(localStorage.getItem('pos-offline-invoices') || '[]');
            if (sales.length === 0) return;

            let syncBtn = $(this);
            syncBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> {{ __("Syncing...") }}');

            let toSync = [...sales];
            let successCount = 0;

            try {
                for (let sale of toSync) {
                    if (!sale) continue;

                    let result = await new Promise(resolve => {
                        let done = false;
                        let timer = setTimeout(() => {
                            if (done) return;
                            done = true;
                            resolve(false);
                        }, 25000);

                        try {
                            submitOfflineInvoice(sale, null, function(success) {
                                if (done) return;
                                done = true;
                                clearTimeout(timer);
                                resolve(!!success);
                            });
                        } catch (e) {
                            if (done) return;
                            done = true;
                            clearTimeout(timer);
                            resolve(false);
                        }
                    });

                    if (result) successCount++;
                }
            } finally {
                syncBtn.prop('disabled', false).text("{{ __('Sync All Now') }}");
            }

            iziToast.success({ title: successCount + " {{ __('Invoices synced successfully!') }}", position: "topRight" });
            if (successCount > 0) setTimeout(() => location.reload(), 1500);
        });

        $(document).on('click', '#offline_sync_btn, #offline_sync_btn_alt, .offline_sync_btn', syncOfflineData);
        syncOfflineData();

        window.addEventListener('online', function() {
            $('#online_status').html('<span class="badge badge-success"><i class="fa fa-circle"></i> Online</span>');
            syncOfflineData();
        });
        window.addEventListener('offline', function() {
            $('#online_status').html('<span class="badge badge-danger"><i class="fa fa-wifi"></i> Offline</span>');
            updateOfflineCustomerDropdown();
        });

        $(document).ready(function() {
            updateOfflineSaleCount();
            if (!navigator.onLine) {
                $('#online_status').html('<span class="badge badge-danger"><i class="fa fa-wifi"></i> Offline</span>');
                updateOfflineCustomerDropdown();
            }
        });

        // =============================================
        // UNIFIED PAYMENT PANEL JAVASCRIPT
        // =============================================

        // 1. Sync values continuously
        function syncPaymentPanel() {
            // Sync discount display
            var discountVal = parseFloat($('.discount').val()) || 0;
            $('.discount_amount_display').text(discountVal.toFixed(2));

            var vatVal = parseFloat($('.vat_amount').val()) || 0;
            $('.vat_amount_display').text(vatVal.toFixed(2));

            // We shouldn't sync payable_amount from estimated_amount because estimated_amount is subtotal.
            // totalCalculate() already updates payable_amount correctly.
            // Just recalculate due/paid.
            updateInlineAmounts();

            // Sync subtotal
            var subTotal = parseFloat($('.estimated_amount').val()) || 0;
            $('.sub_total').text(subTotal.toFixed(2));
        }

        setInterval(syncPaymentPanel, 500);

        // 2. Payment type toggle (Single vs Multiple Account)
        $(document).on('change', 'input[name="payment_type"]', function() {
            if ($(this).val() === 'pos') {
                $('#inline-pos-section').show();
                // Clear multiple account inputs
                $('.bank-amount-input').val('0');
                $('.pay_amount').val('');
                updateInlineAmounts();
            } else {
                $('#inline-pos-section').hide();
                // Open the Multiple Account Payments modal!
                openMultipleAccountModal();
            }
        });

        // Re-open Multiple Account modal if clicking the radio button when already active
        $(document).on('click', '#inlineMultiAccountCompact', function() {
            if ($(this).is(':checked')) {
                openMultipleAccountModal();
            }
        });

        function openMultipleAccountModal() {
            // Update Grand Total inside modal
            var payableAmount = parseFloat($('#payable_amount').val()) || 0;
            $('.modal_payable_amount').text(payableAmount.toFixed(2));

            // Load values from hidden bank-amount-inputs to modal inputs
            $('.bank-modal-amount-input').each(function() {
                var bankId = $(this).attr('data-id');
                // Find matching hidden input
                var hiddenInput = $('.bank-amount-input[name="amounts[' + bankId + ']"]');
                var val = parseFloat(hiddenInput.val()) || 0;
                $(this).val(val > 0 ? val : '');
            });

            recalculateModalAllocated();
            $('#multipleAccountModal').modal('show');
        }

        function recalculateModalAllocated() {
            var sum = 0;
            $('.bank-modal-amount-input').each(function() {
                sum += parseFloat($(this).val()) || 0;
            });
            $('.total_allocated_text').text(sum.toFixed(2));
        }

        $(document).on('input', '.bank-modal-amount-input', recalculateModalAllocated);

        $(document).on('click', '#confirmMultipleAccounts', function() {
            var total = 0;
            // Copy values from modal to hidden bank-amount-inputs
            $('.bank-modal-amount-input').each(function() {
                var bankId = $(this).attr('data-id');
                var val = parseFloat($(this).val()) || 0;
                $('.bank-amount-input[name="amounts[' + bankId + ']"]').val(val);
                total += val;
            });

            // Set main screen pay_amount
            $('.pay_amount').val(total > 0 ? total.toFixed(2) : '');
            updateInlineAmounts();

            $('#multipleAccountModal').modal('hide');
        });

        // 3. Real-time amount calculation
        function updateInlineAmounts() {
            var payableAmount = parseFloat($('#payable_amount').val()) || 0;
            var payPoint     = parseFloat($('.pay_point').val()) || 0;
            var pointRate    = 0.75;
            var pointTkValue = Math.min(payableAmount, payPoint * pointRate);

            if (payPoint > 0) {
                $('#point_discount_text').html('Discount: <strong>৳' + pointTkValue.toFixed(2) + '</strong> (' + payPoint + ' pts)');
            } else {
                $('#point_discount_text').html('Discount: ৳0.00');
            }

            var payAmount    = parseFloat($('.pay_amount').val()) || 0;
            var paidAmount   = pointTkValue + payAmount;
            var dueAmount    = Math.max(0, payableAmount - paidAmount);
            var balance      = paidAmount > payableAmount ? (paidAmount - payableAmount) : 0;

            // Update display spans
            $('.paid_amount').text(paidAmount.toFixed(2));
            $('.due_amount').text(dueAmount.toFixed(2));
            $('.balance').text(balance.toFixed(2));

            // Update new inline DUE and CHANGE inputs
            $('.inline_due_amount').val(dueAmount.toFixed(2));
            $('.inline_change_amount').val(balance.toFixed(2));

            // Update hidden inputs
            $('#paid_amount').val(paidAmount.toFixed(2));
            $('#due_amount').val(dueAmount.toFixed(2));
            $('#balance').val(balance.toFixed(2));
        }

        // Trigger update on pay_amount / pay_point change
        $(document).on('input', '.pay_amount', function() {
            window.isFullDueManual = true;
            updateInlineAmounts();
        });
        $(document).on('input', '.pay_point', updateInlineAmounts);

        // If Multiple Account payment mode is active, clicking pay_amount input re-opens modal for breakdown
        $(document).on('focus click', '.pay_amount', function(e) {
            if ($('#inlineMultiAccountCompact').is(':checked')) {
                e.preventDefault();
                $(this).blur();
                openMultipleAccountModal();
            }
        });

        // 4. Multiple bank account input sum
        $(document).on('input', '.bank-amount-input', function() {
            var total = 0;
            $('.bank-amount-input').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('.pay_amount').val(total.toFixed(2));
            window.isFullDueManual = true;
            updateInlineAmounts();
        });

        // 5. Full Paid shortcut
        $(document).on('click', '.full_pay_btn', function() {
            window.isFullDueManual = false;
            var payable = parseFloat($('#payable_amount').val()) || 0;
            var payPoint = parseFloat($('.pay_point').val()) || 0;
            var pointTkValue = Math.min(payable, payPoint * 0.75);
            var needed = payable - pointTkValue;
            var finalNeeded = needed > 0 ? needed : 0;
            $('.pay_amount').val(finalNeeded.toFixed(2));

            if ($('#inlineMultiAccountCompact').is(':checked')) {
                // Clear all multi inputs, set full amount to first bank
                $('.bank-amount-input, .bank-modal-amount-input').val('0');
                var firstInput = $('.bank-modal-amount-input').first();
                if (firstInput.length) {
                    firstInput.val(finalNeeded.toFixed(2));
                    var bankId = firstInput.attr('data-id');
                    $('.bank-amount-input[name="amounts[' + bankId + ']"]').val(finalNeeded.toFixed(2));
                }
            }
            updateInlineAmounts();
        });

        // 6. Full Due shortcut (pay nothing, all due)
        $(document).on('click', '.full_due_btn', function() {
            window.isFullDueManual = true;
            $('.pay_amount').val('0.00');
            $('.pay_point').val('0');
            if ($('#inlineMultiAccountCompact').is(':checked')) {
                $('.bank-amount-input, .bank-modal-amount-input').val('0');
            }
            updateInlineAmounts();
        });

        // 6.1 Installment System Handlers
        $(document).on('change', '.is_installment_toggle', function() {
            let panel = $(this).closest('.pos-summary-panel');
            let container = panel.find('.installment-fields-container');
            let payAmount = panel.find('.pay_amount');
            let inlineOneAccount = panel.find('#inlineOneAccountCompact');
            let inlineMultiAccount = panel.find('#inlineMultiAccountCompact');
            let fullPayBtn = panel.find('.full_pay_btn');
            let fullDueBtn = panel.find('.full_due_btn');
            
            if ($(this).is(':checked')) {
                container.slideDown();
                payAmount.prop('readonly', true);
                
                // Switch payment type to single account ('pos')
                inlineOneAccount.prop('checked', true).trigger('change');
                inlineMultiAccount.prop('disabled', true);
                
                // Lock and fade Full Paid and Full Due buttons
                fullPayBtn.prop('disabled', true).css('opacity', '0.5');
                fullDueBtn.prop('disabled', true).css('opacity', '0.5');

                // Set default first due date if not set (30 days from now)
                let firstDueDateInput = container.find('.inst_first_due_date');
                if (!firstDueDateInput.val()) {
                    let defaultDate = new Date();
                    defaultDate.setDate(defaultDate.getDate() + 30);
                    let yyyy = defaultDate.getFullYear();
                    let mm = String(defaultDate.getMonth() + 1).padStart(2, '0');
                    let dd = String(defaultDate.getDate()).padStart(2, '0');
                    firstDueDateInput.val(`${yyyy}-${mm}-${dd}`);
                }
                
                calculateInstallmentsForPanel(panel);
            } else {
                container.slideUp();
                payAmount.prop('readonly', false);
                inlineMultiAccount.prop('disabled', false);
                
                // Unlock and restore Full Paid and Full Due buttons
                fullPayBtn.prop('disabled', false).css('opacity', '1');
                fullDueBtn.prop('disabled', false).css('opacity', '1');

                totalCalculate();
            }
        });

function calculateInstallments() {
    let checked = false;
    let activePanel = null;
    
    // Check if any installment toggle is checked
    $('.is_installment_toggle').each(function() {
        if ($(this).is(':checked')) {
            checked = true;
            activePanel = $(this).closest('.pos-summary-panel');
        }
    });
    
    if (checked && activePanel) {
        calculateInstallmentsForPanel(activePanel);
    }
}

function calculateInstallmentsForPanel(panel, source) {
    let container = panel.find('.installment-fields-container');
    
    let estimatedVal = parseFloat(panel.find('input[name="estimated_amount"]').val()) || 0;

    let discountVal = panel.find('.discount_amount').val() || '0';
    let discountAmount = 0;
    if (discountVal.includes("%")) {
        let percent = parseFloat(discountVal.replace('%', '')) || 0;
        discountAmount = Math.round(estimatedVal * (percent / 100));
    } else {
        discountAmount = parseFloat(discountVal) || 0;
    }
    if (discountAmount > estimatedVal) discountAmount = estimatedVal;

    let vatVal = panel.find('.vat').val() || '0';
    let vatAmount = 0;
    if (vatVal.includes("%")) {
        let percent = parseFloat(vatVal.replace('%', '')) || 0;
        vatAmount = Math.round(estimatedVal * (percent / 100));
    } else {
        vatAmount = parseFloat(vatVal) || 0;
    }

    let delivery = parseFloat(panel.find('input[name="delivery_charge"]').val()) || 0;
    let cartTotal = estimatedVal - discountAmount + vatAmount + delivery;

    let advancePayInput = container.find('.inst_advance_pay');
    let advancePay = parseFloat(advancePayInput.val()) || 0;
    if (advancePay > cartTotal) {
        advancePay = cartTotal;
        advancePayInput.val(advancePay.toFixed(2));
    }

    let remaining = cartTotal - advancePay;
    container.find('.inst_remaining').text(remaining.toFixed(2));

    let interestPercent = parseFloat(container.find('.inst_interest_percent').val()) || 0;
    let interestAmount = remaining * (interestPercent / 100);
    container.find('.inst_interest_amount').text(interestAmount.toFixed(2));

    let totalWithInterest = remaining + interestAmount;
    container.find('.inst_total_with_interest').text(totalWithInterest.toFixed(2));

    let intervalDaysInput = container.find('.inst_interval_days');
    let intervalDays = parseInt(intervalDaysInput.val()) || 30;
    if (intervalDays < 1) {
        intervalDays = 1;
        intervalDaysInput.val(1);
    }

    let totalDurationInput = container.find('.inst_total_duration');
    let totalInstallmentsInput = container.find('.inst_total_installments');
    let perInstallmentInput = container.find('.inst_per_installment');
    let firstDueDateInput = container.find('.inst_first_due_date');
    let lastDueDateInput = container.find('.inst_last_due_date');

    let firstDueVal = firstDueDateInput.val();
    let firstDueDate = firstDueVal ? new Date(firstDueVal) : new Date();

    let totalInstallments = parseInt(totalInstallmentsInput.val()) || 1;

    // Trigger calculation based on source
    if (source === 'duration') {
        let totalDuration = parseInt(totalDurationInput.val()) || 0;
        if (totalDuration > 0) {
            totalInstallments = Math.max(1, Math.round(totalDuration / intervalDays));
            totalInstallmentsInput.val(totalInstallments);
        }
    } else if (source === 'per_installment') {
        let perInstVal = parseFloat(perInstallmentInput.val()) || 0;
        if (perInstVal > 0 && totalWithInterest > 0) {
            totalInstallments = Math.max(1, Math.ceil(totalWithInterest / perInstVal));
            totalInstallmentsInput.val(totalInstallments);
            let totalDuration = (totalInstallments - 1) * intervalDays;
            totalDurationInput.val(totalDuration > 0 ? totalDuration : intervalDays);
        }
    } else if (source === 'last_due_date') {
        let lastDueVal = lastDueDateInput.val();
        if (lastDueVal && firstDueVal) {
            let lastDate = new Date(lastDueVal);
            let diffTime = lastDate.getTime() - firstDueDate.getTime();
            let diffDays = Math.round(diffTime / (1000 * 3600 * 24));
            if (diffDays >= 0) {
                totalInstallments = Math.max(1, Math.round(diffDays / intervalDays) + 1);
                totalInstallmentsInput.val(totalInstallments);
                totalDurationInput.val(diffDays);
            }
        }
    } else if (source === 'interval') {
        let totalDuration = parseInt(totalDurationInput.val()) || 0;
        if (totalDuration > 0) {
            totalInstallments = Math.max(1, Math.round(totalDuration / intervalDays));
            totalInstallmentsInput.val(totalInstallments);
        } else {
            totalDuration = (totalInstallments - 1) * intervalDays;
            totalDurationInput.val(totalDuration > 0 ? totalDuration : intervalDays);
        }
    } else {
        // source === 'installments' or default
        if (totalInstallments < 1) {
            totalInstallments = 1;
            totalInstallmentsInput.val(1);
        }
        let totalDuration = (totalInstallments - 1) * intervalDays;
        totalDurationInput.val(totalDuration > 0 ? totalDuration : intervalDays);
    }

    if (totalInstallments < 1) {
        totalInstallments = 1;
        totalInstallmentsInput.val(1);
    }

    let perInstallment = totalWithInterest / totalInstallments;
    if (source !== 'per_installment') {
        perInstallmentInput.val(perInstallment.toFixed(2));
    }

    // Calculate Last Due Date
    if (firstDueVal && source !== 'last_due_date') {
        let lastDate = new Date(firstDueVal);
        let daysToAdd = (totalInstallments - 1) * intervalDays;
        lastDate.setDate(lastDate.getDate() + daysToAdd);

        let yyyy = lastDate.getFullYear();
        let mm = String(lastDate.getMonth() + 1).padStart(2, '0');
        let dd = String(lastDate.getDate()).padStart(2, '0');
        lastDueDateInput.val(`${yyyy}-${mm}-${dd}`);
    }

    let actualPayable = cartTotal + interestAmount;
    
    // Update hidden form inputs for backend
    container.find('.inst_interest_amount_val').val(interestAmount.toFixed(2));
    container.find('.inst_remaining_val').val(remaining.toFixed(2));
    container.find('.inst_total_with_interest_val').val(totalWithInterest.toFixed(2));
    container.find('.inst_per_installment_val').val(perInstallment.toFixed(2));

    panel.find('.payable_amount').text(actualPayable.toFixed(2));
    panel.find('#payable_amount').val(actualPayable.toFixed(2));
    panel.find('.pay_amount').val(advancePay.toFixed(2));
    
    updateInlineAmounts();
}

$(document).on('input change', '.inst_advance_pay', function() {
    let panel = $(this).closest('.pos-summary-panel');
    calculateInstallmentsForPanel(panel, 'advance_pay');
});

$(document).on('input change', '.inst_total_duration', function() {
    let panel = $(this).closest('.pos-summary-panel');
    calculateInstallmentsForPanel(panel, 'duration');
});

$(document).on('input change', '.inst_interval_days', function() {
    let panel = $(this).closest('.pos-summary-panel');
    calculateInstallmentsForPanel(panel, 'interval');
});

$(document).on('click', '.inst-interval-preset', function(e) {
    e.preventDefault();
    let days = $(this).data('days');
    let panel = $(this).closest('.pos-summary-panel');
    panel.find('.inst_interval_days').val(days);
    calculateInstallmentsForPanel(panel, 'interval');
});

$(document).on('input change', '.inst_total_installments', function() {
    let panel = $(this).closest('.pos-summary-panel');
    calculateInstallmentsForPanel(panel, 'installments');
});

$(document).on('input change', '.inst_interest_percent', function() {
    let panel = $(this).closest('.pos-summary-panel');
    calculateInstallmentsForPanel(panel, 'interest');
});

$(document).on('input change', '.inst_per_installment', function() {
    let panel = $(this).closest('.pos-summary-panel');
    calculateInstallmentsForPanel(panel, 'per_installment');
});

$(document).on('input change', '.inst_first_due_date', function() {
    let panel = $(this).closest('.pos-summary-panel');
    calculateInstallmentsForPanel(panel, 'first_due_date');
});

$(document).on('input change', '.inst_last_due_date', function() {
    let panel = $(this).closest('.pos-summary-panel');
    calculateInstallmentsForPanel(panel, 'last_due_date');
});


        // Vehicle database mapped by customer ID
        var customerVehiclesMap = {
            @foreach($customers as $cust)
                "{{ $cust->id }}": [
                    @foreach($cust->vehicles as $v)
                        @if(!empty($v->reg_no))
                        {
                            reg_no: @json($v->reg_no),
                            vehicle_name: @json($v->vehicle_name),
                            model: @json($v->model)
                        },
                        @endif
                    @endforeach
                ],
            @endforeach
        };

        $(document).on('change', '#customer_id', function() {
            var customerId = $(this).val();
            if (customerId) {
                $.ajax({
                    url: "{{ route('customer.previous.due') }}",
                    type: "GET",
                    data: { customer_id: customerId },
                    success: function(data) {
                        var due = parseFloat(data.total_due !== undefined ? data.total_due : data.due) || 0;
                        $('#previous_due').val(due.toFixed(2));
                        $('.previous_due').text(due.toFixed(2));
                        updateInlineAmounts();
                    },
                    error: function() {
                        $('#previous_due').val('0');
                        $('.previous_due').text('0.00');
                    }
                });

                @if(env('APP_AUTOMOBILE') == 'yes')
                // Load customer vehicles instantly from local map
                var vehicles = customerVehiclesMap[customerId] || [];
                var $sel = $('#vehicle_reg_no');
                var $wrapper = $('#vehicle_reg_wrapper');
                $sel.html('<option value="">-- Reg No --</option>');
                if (vehicles.length > 0) {
                    $.each(vehicles, function(i, v) {
                        var label = v.reg_no;
                        if (v.vehicle_name) label = v.vehicle_name + ' — ' + v.reg_no;
                        if (v.model) label += ' (' + v.model + ')';
                        $sel.append('<option value="' + v.reg_no + '">' + label + '</option>');
                    });
                    $wrapper.show();
                    // Re-init select2 if needed
                    if ($sel.hasClass('select2-hidden-accessible')) {
                        $sel.select2('destroy');
                    }
                    $sel.select2({ width: '100%', placeholder: '-- Reg No --', allowClear: true });
                } else {
                    $wrapper.hide();
                    $sel.val('');
                }
                @endif
            } else {
                @if(env('APP_AUTOMOBILE') == 'yes')
                $('#vehicle_reg_wrapper').hide();
                $('#vehicle_reg_no').html('<option value="">-- Reg No --</option>').val('');
                @endif
            }
        });


        // Explicit trigger for checkout buttons
        $(document).on('click', '#checkout, .full_pay_btn, .full_due_btn, .btn-pre-order-submit, .btn-checkout', function() {
            window.isExplicitSubmitAllowed = true;
        });

        // 8. Checkout button — submit form
        $(document).on('click', '#checkout', function() {
            var $btn = $(this);

            // Validation: must have at least one product
            if ($('#tbody tr').length === 0) {
                window.isExplicitSubmitAllowed = false;
                iziToast.warning({
                    title: '{{ __("Warning") }}',
                    message: '{{ __("Please add at least one product to the cart.") }}'
                });
                return;
            }

            // Debounce protection
            if ($btn.prop('disabled')) {
                window.isExplicitSubmitAllowed = false;
                iziToast.warning({
                    title: '{{ __("Wait") }}',
                    message: '{{ __("Please wait before clicking again.") }}'
                });
                return;
            }

            $btn.prop('disabled', true).html('<i class="feather icon-loader mr-2"></i> {{ __("Processing...") }}');

            setTimeout(function() {
                $btn.prop('disabled', false).html('<i class="feather icon-check-circle mr-2"></i> {{ __("Checkout") }}');
            }, 5000);

            // Submit the form
            window.isExplicitSubmitAllowed = true;
            $('#payment_form').submit();
        });

        // Keyboard Shortcuts
        document.addEventListener('keydown', function(event) {
            // Check if installment mode is active
            let isInstallmentActive = $('.is_installment_toggle:checked').length > 0;

            // F8 for Full Paid
            if (event.key === 'F8') {
                event.preventDefault();
                if (!isInstallmentActive) {
                    window.isExplicitSubmitAllowed = true;
                    $('.full_pay_btn:visible').first().click();
                }
            }
            // F9 for Full Due
            if (event.key === 'F9') {
                event.preventDefault();
                if (!isInstallmentActive) {
                    window.isExplicitSubmitAllowed = true;
                    $('.full_due_btn:visible').first().click();
                }
            }
            // F12 for Checkout
            if (event.key === 'F12') {
                event.preventDefault();
                window.isExplicitSubmitAllowed = true;
                $('#checkout').click();
            }
        });

        // Prevent unwanted form submission on Enter keypress in form inputs
        $(document).on('keydown keypress', '#payment_form input:not([type="submit"]):not([type="button"]), #payment_form select', function(e) {
            if (e.keyCode === 13 || e.key === 'Enter' || e.which === 13) {
                if ($(this).hasClass('product_search')) {
                    return; // Handled by .product_search keydown handler
                }
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });

        // Form Submit Validation: Block checkout if any product exceeds available stock
        $(document).on('submit', '#payment_form', function(e) {
            if (!window.isExplicitSubmitAllowed) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
            let hasError = false;

            $("#tbody tr.item-row").each(function() {
                let row = $(this);
                let isService = row.attr('data-is-service') === '1' || row.data('is-service') == 1;
                if (isService) return; // Skip service products

                let name = row.find('span.font-weight-bold').text().trim();
                let has_sub_unit = row.find('.has_sub_unit').val();
                let qtyInputEl = row.find('.quantity-input');
                if (qtyInputEl.length === 0) return;

                let input = qtyInputEl.val();
                let related_by = parseInt(qtyInputEl.attr('data-related')) || 1;

                // Get stock from selected variation if exists
                let variation_select = row.find('select[name="variation_id[]"]');
                let stock = 0;
                let displayStock = "";

                if (variation_select.length > 0) {
                    let selectedOption = variation_select.find('option:selected');
                    let variationStock = parseFloat(selectedOption.attr('stock')) || 0;
                    stock = has_sub_unit === "true" ? variationStock * related_by : variationStock;
                    displayStock = variationStock + " (Variation)";
                } else {
                    // fallback to product stock
                    let stockText = qtyInputEl.attr('data-stock');
                    displayStock = stockText || '0';
                    let stock_qty = parseStockText(stockText, has_sub_unit, related_by);
                    stock = stock_qty * related_by;
                }

                // parse input quantity
                let quantities = parseQuantityInput(input, related_by);
                let total_quantity = to_sub_unit(
                    quantities.main_qty,
                    quantities.sub_qty,
                    related_by,
                    has_sub_unit
                );

                // ✅ Block checkout if piece product has decimals
                if (has_sub_unit !== 'true' && input.toString().includes('.')) {
                    if (typeof window.toastMagic !== 'undefined') {
                        window.toastMagic.error(name + ": " + "{{ __('Only whole numbers allowed!') }}");
                    } else if (typeof iziToast !== 'undefined') {
                        iziToast.error({
                            title: "{{ __('Invalid Quantity!') }}",
                            message: name + ": " + "{{ __('Only whole numbers allowed!') }}",
                            position: "topRight",
                        });
                    } else {
                        alert(name + ": " + "{{ __('Only whole numbers allowed!') }}");
                    }
                    qtyInputEl.css('border', '2px solid red');
                    row.find('.stock-info-label').removeClass('text-muted').addClass('text-danger').css('font-weight', '600')
                        .html('⚠ Only whole numbers allowed!');
                    hasError = true;
                }

                // Block checkout if quantity is 0 or less
                if (total_quantity <= 0) {
                    if (typeof iziToast !== 'undefined') {
                        iziToast.error({
                            title: "{{ __('Invalid Quantity!') }}",
                            message: name + ": " + "{{ __('Quantity cannot be zero or empty!') }}",
                            position: "topRight",
                        });
                    } else {
                        alert(name + ": " + "{{ __('Quantity cannot be zero or empty!') }}");
                    }
                    qtyInputEl.css('border', '2px solid red');
                    row.find('.stock-info-label').removeClass('text-muted').addClass('text-danger').css('font-weight', '600')
                        .html('⚠ Quantity cannot be zero!');
                    hasError = true;
                }

                if (stock < total_quantity) {
                    let availDisplay = (has_sub_unit === 'true') ? (stock / related_by).toFixed(3) : stock;
                    
                    if (typeof window.toastMagic !== 'undefined') {
                        window.toastMagic.error(name + ": " + "{{ __('Not Enough Stock!') }} (" + "{{ __('Available: ') }}" + displayStock + ")");
                    } else if (typeof iziToast !== 'undefined') {
                        iziToast.error({
                            title: "{{ __('Not Enough Stock!') }}",
                            message: name + ": Available: " + displayStock,
                            position: "topRight",
                        });
                    } else {
                        alert("{{ __('Not Enough Stock!') }} " + name + ": Available: " + displayStock);
                    }
                    
                    // Highlight the input box with red border
                    qtyInputEl.css('border', '2px solid red');
                    row.find('.stock-info-label').removeClass('text-muted').addClass('text-danger').css('font-weight', '600')
                        .html('⚠ Not enough! Stock: ' + displayStock);

                    hasError = true;
                }
            });

            if (hasError) {
                window.isExplicitSubmitAllowed = false;
                e.preventDefault();
                e.stopPropagation();
                // Restore Checkout button state if it was disabled/processing
                let checkoutBtn = $('#checkout');
                checkoutBtn.prop('disabled', false).html('<i class="fa-solid fa-check-double mr-1"></i> {{ __("Checkout") }} (F12)');
                return false;
            }
        });

        // Toggle Courier and Platform dropdowns on Online Sale Checkbox change
        $(document).on('change', '.online-sale-toggle-chk', function() {
            let isChecked = $(this).is(':checked');
            let parent = $(this).closest('.payment-type-selectors');
            let hiddenValEl = parent.find('.sale-type-hidden-val');
            
            if (isChecked) {
                hiddenValEl.val('Online');
                parent.find('.courier-select-wrapper').show();
                parent.find('.platform-select-wrapper').show();
                parent.find('.source-link-wrapper').show();
                parent.find('.cod-amount-wrapper').show();
                // Set default COD amount to grand total
                let totalAmount = parseFloat($('#payable_amount').val()) || 0;
                parent.find('.cod-amount-input').val(totalAmount.toFixed(2));
            } else {
                hiddenValEl.val('Outlet');
                parent.find('.courier-select-wrapper').hide().find('select').val('');
                parent.find('.platform-select-wrapper').hide().find('select').val('');
                parent.find('.source-link-wrapper').hide().find('input').val('');
                parent.find('.cod-amount-wrapper').hide().find('input').val('');
            }
        });

        // Toggle Source Link text input on Platform change
        $(document).on('change', '.platform-select', function() {
            let val = $(this).val();
            let parent = $(this).closest('.d-flex');
            if (val) {
                parent.find('.source-link-wrapper').show();
            } else {
                parent.find('.source-link-wrapper').hide().find('input').val('');
            }
        });

        // Initialize Summernote on description textarea
        $(document).ready(function() {
            if ($.fn.summernote) {
                $('#edit_desc_textarea').summernote({
                    height: 180,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'clear']],
                        ['fontname', ['fontname']],
                        ['fontsize', ['fontsize']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['insert', ['link', 'picture', 'table']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ]
                });
            }
        });

        // Product description editing in POS
        $(document).on('click', '.edit-description-btn', function(e) {
            e.preventDefault();
            let productId = $(this).data('id');
            let productName = $(this).data('name');
            let currentRow = $(this).closest('tr');
            let currentDesc = currentRow.find('.product-desc-text').html() || '';

            $('#edit_desc_product_id').val(productId);
            if ($.fn.summernote && $('#edit_desc_textarea').data('summernote')) {
                $('#edit_desc_textarea').summernote('code', currentDesc);
            } else {
                $('#edit_desc_textarea').val(currentDesc);
            }
            $('#editProductDescModalLabel').text('{{ __("Edit Description of") }} ' + productName);
            $('#editProductDescModal').modal('show');
        });

        $('#save_product_desc_btn').on('click', function() {
            let productId = $('#edit_desc_product_id').val();
            let newDesc = ($.fn.summernote && $('#edit_desc_textarea').data('summernote')) ? $('#edit_desc_textarea').summernote('code') : $('#edit_desc_textarea').val();
            let url = "{{ route('product.update-description', 'my_id') }}".replace('my_id', productId);

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    description: newDesc
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Update DOM row(s)
                        $('tr[data-product-id="' + productId + '"]').each(function() {
                            $(this).find('.product-desc-text').html(newDesc);
                        });

                        // Update localStorage pos-items
                        let posItems = localStorage.getItem('pos-items') ? JSON.parse(localStorage.getItem('pos-items')) : [];
                        posItems.forEach(function(item) {
                            if (item.product && item.product.id == productId) {
                                item.product.description = newDesc;
                            }
                        });
                        localStorage.setItem('pos-items', JSON.stringify(posItems));
                        
                        // Update localData array in memory
                        if (typeof localData !== 'undefined') {
                            localData.forEach(function(item) {
                                if (item.product && item.product.id == productId) {
                                    item.product.description = newDesc;
                                }
                            });
                        }

                        if (typeof window.toastMagic !== 'undefined') {
                            window.toastMagic.success(response.message);
                        } else if (typeof iziToast !== 'undefined') {
                            iziToast.success({
                                title: "{{ __('Success') }}",
                                message: response.message,
                                position: "topRight"
                            });
                        }

                        $('#editProductDescModal').modal('hide');
                    }
                },
                error: function(xhr) {
                    let errMsg = "{{ __('Something went wrong!') }}";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    if (typeof window.toastMagic !== 'undefined') {
                        window.toastMagic.error(errMsg);
                    } else if (typeof iziToast !== 'undefined') {
                        iziToast.error({
                            title: "{{ __('Error') }}",
                            message: errMsg,
                            position: "topRight"
                        });
                    }
                }
            });
        });

        @if(isset($preOrder))
        $(document).ready(function() {
            clearCart();
            $("#customer_id").val({{ $preOrder->customer_id }}).trigger('change');

            @if(request('convert_mode'))
                // ─── CONVERT TO SALE MODE ───
                // Stay in normal Sale mode (no pre-order checkbox)
                // Inject convert_mode + pre_order_id so controller marks pre_order as converted after sale
                if ($('#pre_order_convert_input').length === 0) {
                    $('#payment_form').append('<input type="hidden" name="pre_order_id" id="pre_order_convert_input" value="{{ $preOrder->id }}">');
                    $('#payment_form').append('<input type="hidden" name="convert_mode" value="1">');
                }

                // Show a notice banner at the top
                if ($('#convert-mode-banner').length === 0) {
                    $('body').prepend('<div id="convert-mode-banner" style="position:fixed;top:56px;left:0;right:0;z-index:9998;background:linear-gradient(90deg,#16a34a,#15803d);color:#fff;text-align:center;padding:8px 16px;font-weight:700;font-size:13px;letter-spacing:.3px;box-shadow:0 2px 8px rgba(0,0,0,.15);">🔄 {{ __("Converting Pre-Order") }} <strong>{{ $preOrder->pre_order_no }}</strong> {{ __("to Sale — Complete sale then submit") }}</div>');
                }
            @else
                // ─── EDIT PRE-ORDER MODE ───
                $('.online-sale-toggle-chk').prop('checked', true).trigger('change');
                $('.is-pre-order-chk').prop('checked', true).trigger('change');
                $('.sale-type-hidden-val, #sale_type').val('Pre-Order');

                @if(isset($preOrder->note) && $preOrder->note)
                    $('.note-input').val("{{ addslashes($preOrder->note) }}");
                @endif

                if ($('#pre_order_id_input').length === 0) {
                    $('#payment_form').append('<input type="hidden" name="pre_order_id" id="pre_order_id_input" value="{{ $preOrder->id }}">');
                }
            @endif

            @foreach($preOrder->items as $item)
                @php
                    $productWithStock = \App\Models\Product::with('unit', 'variations.size', 'variations.color')->find($item->product_id);
                    $varId = $item->product_variation_id ?? null;
                    $stock = 0;
                    if ($productWithStock && $productWithStock->is_service == 0) {
                        $stock = product_stock($productWithStock);
                    }
                    $variationsMapped = $productWithStock ? $productWithStock->variations->map(function($v) {
                        return [
                            'id'    => $v->id,
                            'size'  => $v->size->size ?? '',
                            'color' => $v->color->color ?? '',
                            'stock' => variation_stock($v->id),
                        ];
                    })->values()->toArray() : [];
                    $dataObj = [
                        'product'    => $productWithStock,
                        'stock_qty'  => $stock,
                        'variations' => $variationsMapped,
                    ];
                @endphp
                @if($productWithStock)
                (function() {
                    var itemData = {!! json_encode($dataObj) !!};
                    itemData.product.selling_price = {{ $item->unit_price }};
                    var varId = {{ $varId ?? 'null' }};

                    // Pass varId so variation is pre-selected in dropdown
                    // skipStockCheck=true so out-of-stock doesn't block edit mode
                    addProductToCard(itemData, null, varId, null, true);

                    let lastRow = $("#tbody tr").first();
                    let qtyInput = lastRow.find('.quantity-input');
                    if (qtyInput.length === 0) { qtyInput = lastRow.find('.main_qty'); }
                    qtyInput.val({{ $item->quantity }}).trigger('change');

                    // Also update main_qty hidden to correct quantity
                    lastRow.find('.main_qty').val({{ $item->quantity }});

                    // Recalculate subtotal
                    let rate = parseFloat(lastRow.find('.rate').val()) || {{ $item->unit_price }};
                    let qty  = {{ $item->quantity }};
                    let sub  = rate * qty;
                    lastRow.find('.sub_total').val(sub.toFixed(2));
                    lastRow.find('.sub_total_text').text(sub.toFixed(2));
                })();
                @endif
            @endforeach
        });
        @endif

        @if(isset($quotation))
        $(document).ready(function() {
            clearCart();
            $("#customer_id").val({{ $quotation->customer_id }}).trigger('change');
            
            @foreach($quotation->quotationItems as $item)
                @php
                    $productWithStock = \App\Models\Product::with('unit', 'variations.size', 'variations.color')->find($item->product_id);
                    $stock = 0;
                    if ($productWithStock && $productWithStock->is_service == 0) {
                        $stock = product_stock($productWithStock);
                    }
                    $variationsMapped = $productWithStock ? $productWithStock->variations->map(function($v) {
                        return [
                            'id'    => $v->id,
                            'size'  => $v->size->size ?? '',
                            'color' => $v->color->color ?? '',
                            'stock' => variation_stock($v->id),
                        ];
                    })->values()->toArray() : [];
                    $dataObj = [
                        'product'    => $productWithStock,
                        'stock_qty'  => $stock,
                        'variations' => $variationsMapped,
                    ];
                @endphp
                
                @if($productWithStock)
                (function() {
                    var itemData = {!! json_encode($dataObj) !!};
                    itemData.product.selling_price = {{ $item->rate }};
                    
                    addProductToCard(itemData, null, "{{ $item->product_variation_id }}");
                    
                    let lastRow = $("#tbody tr").first();
                    let qtyInput = lastRow.find('.quantity-input');
                    if (qtyInput.length === 0) {
                        qtyInput = lastRow.find('.main_qty');
                    }
                    qtyInput.val({{ $item->main_qty }}).trigger('change');
                    lastRow.find('.product_discount_val').val({{ $item->product_discount ?? 0 }});
                    lastRow.find('.product_discount_type').val('fixed');
                    lastRow.find('.product_discount').val({{ $item->product_discount ?? 0 }});
                    update_row_discount_and_subtotal(lastRow);
                    estimatedAmount();
                })();
                @endif
            @endforeach
        });
        @endif

        $(document).on('click', '.add-vehicle-btn', function() {
            var modal = $(this).closest('.modal');
            var container = modal.find('.vehicle-container');
            var firstBlock = container.find('.vehicle-block').first();
            var clone = firstBlock.clone();
            
            clone.find('input').val('');
            clone.find('.remove-vehicle-btn').show();
            container.append(clone);
        });

        $(document).on('click', '.btn-pre-order-submit', function(e) {
            e.preventDefault();

            var custId = $('#customer_id').val();
            if (!custId || custId == 1) {
                alert("Walk-in Customer cannot place a Pre-Order. Please select a registered customer.");
                return false;
            }

            // ── Edit Mode: pre_order_id_input exists → UPDATE existing pre-order ──
            var preOrderId = $('#pre_order_id_input').val();
            if (preOrderId) {
                // Gather items from cart
                var items = [];
                $('#tbody tr').each(function() {
                    var row = $(this);
                    var productId = row.find('[name^="product_id"]').val();
                    if (!productId) return;
                    var variationId = row.find('[name^="product_variation_id"]').val() || null;
                    var qty = row.find('.quantity-input, .main_qty').val() || 1;
                    var price = row.find('[name^="selling_price"], .unit_price_input').val() || 0;
                    var discount = row.find('[name^="discount"], .discount_input').val() || 0;
                    var imei = row.find('.imei_input').val() || '';
                    items.push({
                        product_id: productId,
                        product_variation_id: variationId,
                        quantity: qty,
                        unit_price: price,
                        discount: discount,
                        imei: imei
                    });
                });

                if (items.length === 0) {
                    alert("No items in cart. Please add products before updating.");
                    return false;
                }

                var note = $('.note-input').val() || '';
                var customerId = custId;

                // Create and submit a form to pre-orders.update
                var $form = $('<form method="POST" action="/pre-order/update/' + preOrderId + '"></form>');
                $form.append($('<input type="hidden" name="_token">').val($('meta[name="csrf-token"]').attr('content')));
                $form.append($('<input type="hidden" name="_method" value="POST">'));
                $form.append($('<input type="hidden" name="customer_id">').val(customerId));
                $form.append($('<input type="hidden" name="note">').val(note));

                $.each(items, function(i, item) {
                    $form.append($('<input type="hidden">').attr('name', 'items[' + i + '][product_id]').val(item.product_id));
                    $form.append($('<input type="hidden">').attr('name', 'items[' + i + '][product_variation_id]').val(item.product_variation_id || ''));
                    $form.append($('<input type="hidden">').attr('name', 'items[' + i + '][quantity]').val(item.quantity));
                    $form.append($('<input type="hidden">').attr('name', 'items[' + i + '][unit_price]').val(item.unit_price));
                    $form.append($('<input type="hidden">').attr('name', 'items[' + i + '][discount]').val(item.discount));
                    $form.append($('<input type="hidden">').attr('name', 'items[' + i + '][imei]').val(item.imei));
                });

                $('body').append($form);
                $form.submit();
                return;
            }

            // ── New Pre-Order: open payment modal ──
            $('.online-sale-toggle-chk').prop('checked', true).trigger('change');
            $('.is-pre-order-chk').prop('checked', true).trigger('change');
            $('#payment_modal').modal('show');
        });


        $(document).on('change', '.online-sale-toggle-chk', function() {
            if ($(this).is(':checked')) {
                $('#main_sale_type_input, #sale_type').val('Online');
                $('.courier-select-wrapper, .platform-select-wrapper, .source-link-wrapper, .cod-amount-wrapper, .pre-order-chk-wrapper').slideDown();
            } else {
                $('#main_sale_type_input, #sale_type').val('Outlet');
                $('#is_pre_order_chk').prop('checked', false);
                $('.courier-select-wrapper, .platform-select-wrapper, .source-link-wrapper, .cod-amount-wrapper, .pre-order-chk-wrapper').slideUp();
            }
        });

        $(document).on('change', '.is-pre-order-chk', function() {
            var isChecked = $(this).is(':checked');
            if (isChecked) {
                var custId = $('#customer_id').val();
                if (!custId || custId == 1) {
                    alert("Walk-in Customer cannot place a Pre-Order. Please select a registered customer.");
                    $('.is-pre-order-chk').prop('checked', false);
                    $('.sale-type-hidden-val, #sale_type').val('Outlet');
                    return false;
                }
                $('.is-pre-order-chk').prop('checked', true);
                $('.sale-type-hidden-val, #sale_type').val('Pre-Order');
            } else {
                $('.is-pre-order-chk').prop('checked', false);
                var isOnlineChecked = $('.online-sale-toggle-chk').is(':checked');
                $('.sale-type-hidden-val, #sale_type').val(isOnlineChecked ? 'Online' : 'Outlet');
            }
        });

        var isPreOrderSubmitting = false;

        $(document).on('submit', '#payment_form', function(e) {
            var saleType = $('.sale-type-hidden-val').val() || $('#sale_type').val();
            var isPreOrderChecked = $('.is-pre-order-chk:checked').length > 0;

            if (saleType === 'Pre-Order' || isPreOrderChecked) {
                e.preventDefault();

                if (isPreOrderSubmitting) {
                    return false;
                }

                var customerId = $('#customer_id').val();
                if (!customerId || customerId == 1) {
                    alert("Walk-in Customer cannot place a Pre-Order. Please select a registered customer.");
                    return false;
                }

                var rowCount = $('#tbody tr').length;
                if (rowCount < 1) {
                    alert("Please select at least one product for Pre-Order.");
                    return false;
                }

                isPreOrderSubmitting = true;
                var submitBtns = $('#checkout, .full_pay_btn, .full_due_btn, .btn-pre-order-submit');
                submitBtns.prop('disabled', true);

                var formData = $(this).serializeArray();
                formData = formData.filter(function(i) { return i.name !== 'sale_type'; });
                formData.push({ name: 'sale_type', value: 'Pre-Order' });

                $.ajax({
                    url: "{{ route('pre-orders.store') }}",
                    method: "POST",
                    data: $.param(formData),
                    success: function(res) {
                        if (res.status == 'success') {
                            // Direct 1-click redirect to Pre-Order List!
                            window.location.href = "{{ route('pre-orders.index') }}";
                        } else {
                            isPreOrderSubmitting = false;
                            submitBtns.prop('disabled', false);
                            alert(res.message || "Failed to place Pre-Order.");
                        }
                    },
                    error: function(xhr) {
                        isPreOrderSubmitting = false;
                        submitBtns.prop('disabled', false);
                        var msg = "Failed to create Pre-Order.";
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        alert(msg);
                    }
                });
                return false;
            }
        });

        // Helper to reliably trigger toast notifications in POS
        function triggerPosToast(msg, type) {
            type = type || 'success';
            if (typeof window.toastMagic !== 'undefined' && window.toastMagic) {
                if (type === 'success') window.toastMagic.success(msg);
                else if (type === 'error') window.toastMagic.error(msg);
                else if (type === 'warning') window.toastMagic.warning(msg);
                else window.toastMagic.info(msg);
            } else if (typeof ToastMagic !== 'undefined') {
                var tm = new ToastMagic();
                if (type === 'success') tm.success(msg);
                else if (type === 'error') tm.error(msg);
                else if (type === 'warning') tm.warning(msg);
                else tm.info(msg);
            } else if (typeof iziToast !== 'undefined' && iziToast) {
                if (type === 'success') iziToast.success({ title: msg, position: "topRight", timeout: 2500 });
                else if (type === 'error') iziToast.error({ title: msg, position: "topRight", timeout: 2500 });
                else iziToast.info({ title: msg, position: "topRight", timeout: 2500 });
            } else if (typeof toastr !== 'undefined' && toastr) {
                if (type === 'success') toastr.success(msg);
                else if (type === 'error') toastr.error(msg);
                else toastr.info(msg);
            }
        }

        // POS Invoice Note Popup Handlers
        $(document).on('click', '.btn-pos-note-trigger', function(e) {
            e.preventDefault();
            let currentNote = $('.pos-invoice-note-field').first().val() || '';
            $('#modal_pos_invoice_note_textarea').val(currentNote);
            $('#posInvoiceNoteModal').modal('show');
        });

        $('#posInvoiceNoteModal').on('shown.bs.modal', function () {
            $('#modal_pos_invoice_note_textarea').focus();
        });

        $(document).on('click', '#btn_save_pos_invoice_note', function(e) {
            var noteVal = $('#modal_pos_invoice_note_textarea').val();
            var noteText = (typeof noteVal === 'string') ? noteVal.trim() : '';
            $('.pos-invoice-note-field, .note-input').val(noteText);
            
            if (noteText.length > 0) {
                $('.pos-note-badge').show();
                $('.btn-pos-note-trigger').css({
                    'background': '#eff6ff',
                    'border-color': '#93c5fd',
                    'color': '#1d4ed8'
                }).attr('title', 'Note: ' + noteText);

                triggerPosToast("{{ __('Invoice Note saved successfully!') }}", 'success');
            } else {
                $('.pos-note-badge').hide();
                $('.btn-pos-note-trigger').css({
                    'background': '#ffffff',
                    'border-color': '#cbd5e1',
                    'color': '#334155'
                }).attr('title', '{{ __("Invoice Note") }}');

                triggerPosToast("{{ __('Invoice Note cleared.') }}", 'info');
            }
            
            $('#posInvoiceNoteModal').modal('hide');
        });

        $(document).on('click', '#btn_clear_pos_invoice_note', function(e) {
            e.preventDefault();
            $('#modal_pos_invoice_note_textarea').val('').focus();
            triggerPosToast("{{ __('Note text cleared') }}", 'info');
        });

        // Initialize button badge on page load if note exists
        $(document).ready(function() {
            let initialNote = $('.pos-invoice-note-field').first().val() || '';
            if (initialNote.trim().length > 0) {
                $('.pos-note-badge').show();
                $('.btn-pos-note-trigger').css({
                    'background': '#eff6ff',
                    'border-color': '#93c5fd',
                    'color': '#1d4ed8'
                }).attr('title', 'Note: ' + initialNote);
            }
        });

        $(document).on('click', '.remove-vehicle-btn', function() {
            $(this).closest('.vehicle-block').remove();
        });
    </script>

    @if(env('APP_MOBILE_SCANNER') == 'yes')
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        $(document).ready(function() {
            let html5QrcodeScanner = null;

            $(document).on('click', '.btn-scan-camera', function() {
                $('#cameraScannerModal').modal('show');
                
                setTimeout(() => {
                    if (!html5QrcodeScanner) {
                        html5QrcodeScanner = new Html5Qrcode("reader");
                    }
                    
                    const config = { 
                        fps: 10, 
                        qrbox: function(width, height) {
                            let minSize = Math.min(width, height);
                            let size = Math.floor(minSize * 0.7);
                            return { width: size, height: Math.floor(size * 0.6) };
                        },
                        aspectRatio: 1.0
                    };
                    
                    html5QrcodeScanner.start(
                        { facingMode: "environment" },
                        config,
                        onScanSuccess,
                        onScanFailure
                    ).catch(err => {
                        console.error("Error starting camera scanner: ", err);
                        iziToast.error({
                            title: "{{ __('Camera Error') }}",
                            message: "{{ __('Could not access camera. Please allow camera permissions.') }}",
                            position: "topRight"
                        });
                        $('#cameraScannerModal').modal('hide');
                    });
                }, 400);
            });

            function onScanSuccess(decodedText, decodedResult) {
                iziToast.success({
                    title: "{{ __('Scanned successfully') }}",
                    message: decodedText,
                    position: "topRight",
                    timeout: 1000
                });
                
                stopScanner();
                $('#cameraScannerModal').modal('hide');
                
                let $searchBox = $(".product_search:visible").first();
                handleDirectBarcodeScan(decodedText, $searchBox);
            }

            function onScanFailure(error) {
                // Ignore silent failures for QR detection
            }

            function stopScanner() {
                if (html5QrcodeScanner && html5QrcodeScanner.isScanning) {
                    html5QrcodeScanner.stop().then(() => {
                        console.log("Camera scanner stopped.");
                    }).catch(err => {
                        console.error("Error stopping scanner: ", err);
                    });
                }
            }

            $('#cameraScannerModal').on('hidden.bs.modal', function () {
                stopScanner();
            });
        });
    </script>
    @endif
@endpush
