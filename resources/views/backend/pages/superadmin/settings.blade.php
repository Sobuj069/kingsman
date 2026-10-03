@extends('backend.layouts.master')

@section('page-title', __('Super Admin Settings'))

@section('content')
<div class="setting-page">
    <div class="setting-tabs">
        <button onclick="switchTab('env')" id="tab-btn-env" class="tab-btn active-tab">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
            {{ __('Feature permissions') }}
        </button>
        <button onclick="switchTab('sidebar')" id="tab-btn-sidebar" class="tab-btn inactive-tab">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M9 3v18"/></svg>
            {{ __('Sidebar modules') }}
        </button>
    </div> 

    <div class="setting-card">
        {{-- Env Toggles Tab --}}
        <div id="tab-env" class="tab-content block">
            <form method="POST" action="{{ route('super-admin.settings.env') }}">
                @csrf
                <p class="section-label">{{ __('Environment Features (.env)') }}</p>
                <span class="text-xs text-slate-500 mb-4 d-block">{{ __('Toggle backend modules and system functions globally') }}</span>

                <div class="space-y-4">
                    {{-- APP_SC --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('SC Mode (APP_SC)') }}</span>
                            <small class="text-slate-500">{{ __('Toggles variation features like Color, Size, and specific variations on products.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="APP_SC" value="yes" {{ env('APP_SC') == 'yes' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- APP_IMEI --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('IMEI Tracking (APP_IMEI)') }}</span>
                            <small class="text-slate-500">{{ __('Track individual product IMEIs or serial numbers during sales and purchases.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="APP_IMEI" value="yes" {{ env('APP_IMEI') == 'yes' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- APP_WARRANTY --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('Warranty Management (APP_WARRANTY)') }}</span>
                            <small class="text-slate-500">{{ __('Enable product warranty assignment, tracking, and claims workflow.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="APP_WARRANTY" value="yes" {{ env('APP_WARRANTY') == 'yes' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- APP_VAT --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('VAT / Tax Include (APP_VAT)') }}</span>
                            <small class="text-slate-500">{{ __('Enable or disable VAT / Tax input fields on POS (sc create) and Purchase creation/edit.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="APP_VAT" value="yes" {{ is_vat_enabled() ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- APP_ONLINE --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('Online Sales Sync (APP_ONLINE)') }}</span>
                            <small class="text-slate-500">{{ __('Synchronize orders with online shop databases and portals.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="APP_ONLINE" value="yes" {{ env('APP_ONLINE') == 'yes' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- APP_SERVICE --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('Service Center Modals (APP_SERVICE)') }}</span>
                            <small class="text-slate-500">{{ __('Enable repair, service requests, and service center ticketing support.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="APP_SERVICE" value="yes" {{ env('APP_SERVICE') == 'yes' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- HIDE_ADMIN_BRANCH --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('Hide Admin Branch (HIDE_ADMIN_BRANCH)') }}</span>
                            <small class="text-slate-500">{{ __('Hides Branch ID 1 (Admin Branch) from lists, only showing default/other branches.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="HIDE_ADMIN_BRANCH" value="yes" {{ env('HIDE_ADMIN_BRANCH') == 'yes' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    {{-- APP_REF_INV --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('Ref Inv Field in POS & Print (APP_REF_INV)') }}</span>
                            <small class="text-slate-500">{{ __('Show Ref Inv input field in POS create page and display Ref Inv on printed invoices.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="APP_REF_INV" value="yes" {{ env('APP_REF_INV') == 'yes' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- APP_INSTALLMENT --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('POS Installment System (APP_INSTALLMENT)') }}</span>
                            <small class="text-slate-500">{{ __('Enable installment sale checkout, collection registers, rolling payments adjustment, and daily due reports.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="APP_INSTALLMENT" value="yes" {{ env('APP_INSTALLMENT') == 'yes' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- APP_AUTOMOBILE --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('AutoMobile Mode (APP_AUTOMOBILE)') }}</span>
                            <small class="text-slate-500">{{ __('Enable AutoMobile specific features including Quotation module, customer vehicle details, and vehicle registration select checklist in checkout.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="APP_AUTOMOBILE" value="yes" {{ env('APP_AUTOMOBILE') == 'yes' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- APP_AUTO_PRINT --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10 mt-3">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('Auto Print Invoices & Receipts (APP_AUTO_PRINT)') }}</span>
                            <small class="text-slate-500">{{ __('Automatically trigger the browser print dialog box when loading any invoice, POS receipt, purchase return, quotation, or warranty delivery print view.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="APP_AUTO_PRINT" value="yes" {{ env('APP_AUTO_PRINT') == 'yes' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- APP_MOBILE_SCANNER --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10 mt-3">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('Mobile Camera Barcode Scanner (APP_MOBILE_SCANNER)') }}</span>
                            <small class="text-slate-500">{{ __('Enable scanning product barcodes using the mobile phone camera on the POS / Invoice generation screen.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="APP_MOBILE_SCANNER" value="yes" {{ env('APP_MOBILE_SCANNER') == 'yes' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- APP_LOYALTY --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10 mt-3">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('Loyalty Points System (APP_LOYALTY)') }}</span>
                            <small class="text-slate-500">{{ __('Enable customer loyalty points accumulation and checkout points adjustment/payment module on POS.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="APP_LOYALTY" value="yes" {{ env('APP_LOYALTY') == 'yes' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- APP_COURIER_FRAUD_CHECK --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10 mt-3">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('Courier Fraud Checker (APP_COURIER_FRAUD_CHECK)') }}</span>
                            <small class="text-slate-500">{{ __('Enable live courier fraud check and customer parcel delivery profile on POS.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="APP_COURIER_FRAUD_CHECK" value="yes" {{ env('APP_COURIER_FRAUD_CHECK') == 'yes' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- APP_ZATCA --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10 mt-3">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('ZATCA E-Invoice QR Code (APP_ZATCA)') }}</span>
                            <small class="text-slate-500">{{ __('Enable Saudi Arabia ZATCA Phase-1 TLV Base64 QR code generation on invoice printouts.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="APP_ZATCA" value="yes" {{ env('APP_ZATCA') == 'yes' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- APP_QR_CODE --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10 mt-3">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('Invoice QR Code (APP_QR_CODE)') }}</span>
                            <small class="text-slate-500">{{ __('Enable or disable QR code display on all invoice and receipt printouts.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="APP_QR_CODE" value="yes" {{ env('APP_QR_CODE', 'yes') == 'yes' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- APP_CUSTOMER_PHONE_ONLY --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10 mt-3">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('Only Phone Number Customer Entry (APP_CUSTOMER_PHONE_ONLY)') }}</span>
                            <small class="text-slate-500">{{ __('Allow creating and updating customers with ONLY phone number. Customer Name and Address become optional. (Note: Only takes effect if Online Sales Sync / APP_ONLINE is turned OFF).') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="APP_CUSTOMER_PHONE_ONLY" value="yes" {{ env('APP_CUSTOMER_PHONE_ONLY') == 'yes' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- HIDE_CUSTOMER_DATES --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10 mt-3">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('Hide Customer Birth & Anniversary Dates (HIDE_CUSTOMER_DATES)') }}</span>
                            <small class="text-slate-500">{{ __('Hides Birth Date and Anniversary Date fields from customer creation, edit, and view modals.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="HIDE_CUSTOMER_DATES" value="yes" {{ env('HIDE_CUSTOMER_DATES') == 'yes' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- SHOW_COST_RATE_IN_POS --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10 mt-3">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('Show Product Cost Rate in POS (SHOW_COST_RATE_IN_POS)') }}</span>
                            <small class="text-slate-500">{{ __('Enable showing the product purchase/cost price alongside the selling rate on the POS / Invoice creation screen.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="SHOW_COST_RATE_IN_POS" value="yes" {{ env('SHOW_COST_RATE_IN_POS') == 'yes' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- APP_POS_AUTO_FULL_VIEW --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10 mt-3">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('Auto Full View Mode in POS (APP_POS_AUTO_FULL_VIEW)') }}</span>
                            <small class="text-slate-500">{{ __('Automatically launch POS / Invoice screen in Full View / Kiosk mode on page load.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="APP_POS_AUTO_FULL_VIEW" value="yes" {{ env('APP_POS_AUTO_FULL_VIEW', 'yes') != 'no' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- APP_LICENSE_CHECK --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10 mt-3">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('Enable License Verification (APP_LICENSE_CHECK)') }}</span>
                            <small class="text-slate-500">{{ __('Enable cryptographic domain and expiration license key validation. If yes, a valid license key must be configured in .env.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="APP_LICENSE_CHECK" value="yes" {{ env('APP_LICENSE_CHECK') == 'yes' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- APP_DISCOUNT_GROUP --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10 mt-3">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('Customer Discount Groups (APP_DISCOUNT_GROUP)') }}</span>
                            <small class="text-slate-500">{{ __('Enable Discount Groups selection for customers and automatic calculations on POS and invoices.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="APP_DISCOUNT_GROUP" value="yes" {{ env('APP_DISCOUNT_GROUP') == 'yes' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- APP_SUB_CATEGORY --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10 mt-3">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('Sub Category Module (APP_SUB_CATEGORY)') }}</span>
                            <small class="text-slate-500">{{ __('Enable Sub Category management linked with Category, Product, Stock Report, and Sale List.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="APP_SUB_CATEGORY" value="yes" {{ env('APP_SUB_CATEGORY') == 'yes' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- APP_INVOICE_NOTE --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10 mt-3">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('POS Invoice Note (APP_INVOICE_NOTE)') }}</span>
                            <small class="text-slate-500">{{ __('Enable visible Note input field in the POS Invoice Creation view and print receipts.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="APP_INVOICE_NOTE" value="yes" {{ env('APP_INVOICE_NOTE') == 'yes' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- APP_BRANCH_SWITCH --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10 mt-3">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('Branch Switching (APP_BRANCH_SWITCH)') }}</span>
                            <small class="text-slate-500">{{ __('Enable or disable the top navbar branch switcher globally. When disabled, branch switching is completely turned off for all users.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="APP_BRANCH_SWITCH" value="yes" {{ env('APP_BRANCH_SWITCH', 'yes') != 'no' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- APP_RACK --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10 mt-3">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('Rack Management System (APP_RACK)') }}</span>
                            <small class="text-slate-500">{{ __('Enable multi-rack management in Sidebar, product purchases, and stock report tracking.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="APP_RACK" value="yes" {{ is_rack_enabled() ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    {{-- APP_UNIT --}}
                    <div class="flex items-center justify-between p-3 bg-slate-800/10 rounded-xl border border-slate-700/10 mt-3">
                        <div>
                            <span class="font-semibold text-sm d-block">{{ __('Unit System (APP_UNIT)') }}</span>
                            <small class="text-slate-500">{{ __('Enable unit & sub-unit management in Sidebar, product creation, and edit pages.') }}</small>
                        </div>
                        <label class="custom-toggle">
                            <input type="checkbox" name="APP_UNIT" value="yes" {{ is_unit_enabled() ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>


                <div class="footer-row mt-6">
                    <button type="submit" class="save-btn flex items-center justify-center gap-2">
                        <span class="btn-text">{{ __('Save environment settings') }}</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Sidebar Customizer Tab --}}
        <div id="tab-sidebar" class="tab-content hidden">
            <form method="POST" action="{{ route('super-admin.settings.sidebar') }}">
                @csrf
                <p class="section-label">{{ __('Sidebar Visibility Control') }}</p>
                <span class="text-xs text-slate-500 mb-4 d-block">{{ __('Select modules you want to HIDE from the main POS sidebar navigation') }}</span>

                @php
                    $modules = [
                        'pos' => __('POS'),
                        'sale_list' => __('Sale List'),
                        'installment' => __('Installment'),
                        'online_sale' => __('Online Sale'),
                        'purchase' => __('Purchase'),
                        'return' => __('Return'),
                        'damage' => __('Damage'),
                        'stock_report' => __('Stock Report'),
                        'ai_stock_auditor' => __('AI Stock Auditor'),
                        'stock_adjust' => __('Stock Adjust'),
                        'stock_audit' => __('Stock Audit'),
                        'transfer' => __('Transfer'),
                        'unit' => __('Unit'),
                        'product' => __('Product'),
                        'rack' => __('Rack Management'),
                        'service' => __('Service'),
                        'warranty_management' => __('Warranty Management'),
                        'category' => __('Category'),
                        'brand' => __('Brand'),
                        'color' => __('Color'),
                        'size' => __('Size'),
                        'expense' => __('Expense'),
                        'payment' => __('Payment'),
                        'sms' => __('SMS'),
                        'finance' => __('Finance Management'),
                        'bank_account' => __('Bank Account'),
                        'deposit' => __('Deposit'),
                        'withdraw' => __('Withdraw'),
                        'bank_transfer' => __('Bank Transfer'),
                        'transaction_history' => __('Transaction History'),
                        'activity_log' => __('Activity Log'),
                        'payroll' => __('Payroll'),
                        'branch' => __('Branch'),
                        'user' => __('User')
                    ];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach ($modules as $key => $label)
                        <div class="flex items-center gap-3 p-3 bg-slate-800/10 rounded-xl border border-slate-700/10 hover:border-slate-500/20 transition-all duration-200">
                            <input type="checkbox" name="hidden_modules[]" value="{{ $key }}" id="module_{{ $key }}"
                                   class="w-4 h-4 rounded border-slate-300 text-orange-500 focus:ring-orange-500"
                                   {{ in_array($key, $hiddenModules) ? 'checked' : '' }}>
                            <label for="module_{{ $key }}" class="text-sm font-medium cursor-pointer select-none">
                                {{ $label }}
                            </label>
                        </div>
                    @endforeach
                </div>

                <div class="footer-row mt-6">
                    <button type="submit" class="save-btn flex items-center justify-center gap-2">
                        <span class="btn-text">{{ __('Save sidebar customizer') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Premium Switch Toggles Style */
    .custom-toggle {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
        cursor: pointer;
    }
    .custom-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .toggle-slider {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: .3s;
        border-radius: 24px;
    }
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px; width: 18px;
        left: 3px; bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.15);
    }
    .custom-toggle input:checked + .toggle-slider {
        background-color: #f97316;
    }
    .custom-toggle input:checked + .toggle-slider:before {
        transform: translateX(20px);
    }
</style>
@endsection

@push('js')
<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(function(c) {
            c.classList.add('hidden');
            c.classList.remove('block');
        });
        document.getElementById('tab-' + tabId).classList.replace('hidden', 'block');

        document.querySelectorAll('.tab-btn').forEach(function(b) {
            b.classList.remove('active-tab');
            b.classList.add('inactive-tab');
        });
        document.getElementById('tab-btn-' + tabId).classList.add('active-tab');
        document.getElementById('tab-btn-' + tabId).classList.remove('inactive-tab');
    }
</script>
@endpush
